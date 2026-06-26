import time
import io
import json
import warnings
import torch
import torch.nn as nn
from PIL import Image
from transformers import AutoModelForImageClassification, AutoImageProcessor
from huggingface_hub import hf_hub_download
from config import MODEL_NAME, CONFIDENCE_THRESHOLD

warnings.filterwarnings("ignore", message=".*UNEXPECTED.*")
warnings.filterwarnings("ignore", message=".*MISSING.*")
warnings.filterwarnings("ignore", message=".*newly initialized.*")


class AIDetector:
    def __init__(self):
        self.model = None
        self.processor = None
        self.binary_head = None
        self.source_names = None
        self.source_is_real = None
        self.is_loaded = False
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

    def load_model(self):
        self.model = AutoModelForImageClassification.from_pretrained(MODEL_NAME)
        self.processor = AutoImageProcessor.from_pretrained(MODEL_NAME)

        meta_path = hf_hub_download(repo_id=MODEL_NAME, filename="source_meta.json")
        with open(meta_path) as f:
            meta = json.load(f)
        self.source_names = meta["source_names"]
        self.source_is_real = meta["source_is_real"]
        hidden_size = meta.get("hidden_size", self.model.config.hidden_size)

        binary_head_path = hf_hub_download(repo_id=MODEL_NAME, filename="binary_head.pt")
        self.binary_head = nn.Sequential(nn.Dropout(0.1), nn.Linear(hidden_size, 2))
        self.binary_head.load_state_dict(torch.load(binary_head_path, map_location=self.device))

        self.model.to(self.device).eval()
        self.binary_head.to(self.device).eval()
        self.is_loaded = True

    def _get_features(self, pixel_values):
        if hasattr(self.model, 'beit'):
            outputs = self.model.beit(pixel_values)
        elif hasattr(self.model, 'vit'):
            outputs = self.model.vit(pixel_values)
        else:
            outputs = self.model(pixel_values)
        return outputs.last_hidden_state[:, 0]

    def analyze(self, image_bytes: bytes, filename: str = "unknown") -> dict:
        if not self.is_loaded:
            self.load_model()

        start = time.time()

        img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
        inputs = self.processor(img, return_tensors="pt").to(self.device)

        with torch.no_grad():
            outputs = self.model(**inputs)
            probs = torch.softmax(outputs.logits, dim=-1)[0]

            features = self._get_features(inputs["pixel_values"])
            binary_logits = self.binary_head(features)
            binary_probs = torch.softmax(binary_logits, dim=-1)[0]

            human_prob = binary_probs[0].item()
            ai_prob = binary_probs[1].item()

        elapsed_ms = round((time.time() - start) * 1000, 1)

        pred_idx = probs.argmax().item()
        predicted_source = self.source_names[pred_idx]

        is_ai = ai_prob > human_prob and ai_prob >= CONFIDENCE_THRESHOLD
        confidence = max(ai_prob, human_prob)
        label = "AI-generated" if is_ai else "human"

        top_sources = []
        for i, name in enumerate(self.source_names):
            if not self.source_is_real.get(name, False):
                top_sources.append({"label": name, "score": round(probs[i].item(), 4)})
        top_sources.sort(key=lambda x: x["score"], reverse=True)

        return {
            "prediction": {
                "label": label,
                "confidence": round(confidence, 4),
                "is_ai_generated": is_ai
            },
            "signals": {
                "ai_probability": round(ai_prob, 4),
                "human_probability": round(human_prob, 4),
                "predicted_source": predicted_source,
                "top_sources": top_sources[:5]
            },
            "model": MODEL_NAME,
            "processing_time_ms": elapsed_ms
        }
