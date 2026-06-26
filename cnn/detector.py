import time
import io
import torch
import torch.nn as nn
from PIL import Image
from transformers import AutoModelForImageClassification, AutoImageProcessor
from huggingface_hub import hf_hub_download
from config import MODEL_NAME, CONFIDENCE_THRESHOLD


class AIDetector:
    def __init__(self):
        self.model = None
        self.processor = None
        self.binary_head = None
        self.is_loaded = False
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

    def load_model(self):
        self.model = AutoModelForImageClassification.from_pretrained(MODEL_NAME)
        self.processor = AutoImageProcessor.from_pretrained(MODEL_NAME)

        hidden_size = self.model.config.hidden_size
        binary_head_path = hf_hub_download(repo_id=MODEL_NAME, filename="binary_head.pt")
        self.binary_head = nn.Sequential(nn.Dropout(0.1), nn.Linear(hidden_size, 2))
        missing, unexpected = self.binary_head.load_state_dict(
            torch.load(binary_head_path, map_location=self.device), strict=True
        )
        assert not missing and not unexpected, f"Binary head load mismatch: missing={missing}, unexpected={unexpected}"

        self.model.to(self.device).eval()
        self.binary_head.to(self.device).eval()
        self.is_loaded = True

    def analyze(self, image_bytes: bytes, filename: str = "unknown") -> dict:
        if not self.is_loaded:
            self.load_model()

        start = time.time()

        img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
        inputs = self.processor(img, return_tensors="pt").to(self.device)

        with torch.no_grad():
            features = self.model.beit(inputs["pixel_values"]).last_hidden_state[:, 0]
            binary_logits = self.binary_head(features)
            binary_probs = torch.softmax(binary_logits, dim=-1)[0]

            human_prob = binary_probs[0].item()
            ai_prob = binary_probs[1].item()

        elapsed_ms = round((time.time() - start) * 1000, 1)

        is_ai = ai_prob > human_prob and ai_prob >= CONFIDENCE_THRESHOLD
        confidence = max(ai_prob, human_prob)
        label = "AI-generated" if is_ai else "human"

        return {
            "prediction": {
                "label": label,
                "confidence": round(confidence, 4),
                "is_ai_generated": is_ai
            },
            "signals": {
                "ai_probability": round(ai_prob, 4),
                "human_probability": round(human_prob, 4)
            },
            "model": MODEL_NAME,
            "processing_time_ms": elapsed_ms
        }
