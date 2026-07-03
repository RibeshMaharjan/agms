import time
import io
import os
import json
import torch
import torch.nn as nn
from PIL import Image
from torchvision import transforms
from model.cnn_architecture import AIDetectionCNN
from config import (
    MODEL_TYPE, MODEL_SAVE_PATH, HUGGINGFACE_MODEL_ID,
    IMG_SIZE, NUM_CLASSES, CONFIDENCE_THRESHOLD,
)

DEVICE = torch.device("cuda" if torch.cuda.is_available() else "cpu")

inference_transform = transforms.Compose([
    transforms.Resize((IMG_SIZE, IMG_SIZE)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
])


class CustomDetector:
    """Custom CNN from scratch (AIDetectionCNN)."""

    def __init__(self):
        self.model = None
        self.is_loaded = False

    def load_model(self):
        self.model = AIDetectionCNN(num_classes=NUM_CLASSES)

        if os.path.exists(MODEL_SAVE_PATH):
            print(f"Loading trained CNN from: {MODEL_SAVE_PATH}")
            self.model.load_state_dict(torch.load(MODEL_SAVE_PATH, map_location=DEVICE))
        else:
            print(f"WARNING: No trained model found at {MODEL_SAVE_PATH}")
            print("Run 'python train/train.py' to train the model first.")
            print("Using randomly initialized weights (predictions will be meaningless).")

        self.model.to(DEVICE).eval()
        self.is_loaded = True

    def analyze(self, image_bytes: bytes, filename: str = "unknown") -> dict:
        if not self.is_loaded:
            self.load_model()

        start = time.time()

        img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
        tensor = inference_transform(img).unsqueeze(0).to(DEVICE)

        with torch.no_grad():
            outputs = self.model(tensor)
            probs = torch.softmax(outputs, dim=-1)[0]

        elapsed_ms = round((time.time() - start) * 1000, 1)

        human_prob = probs[0].item()
        ai_prob = probs[1].item()

        is_ai = ai_prob > human_prob and ai_prob >= CONFIDENCE_THRESHOLD
        confidence = max(ai_prob, human_prob)
        label = "AI-generated" if is_ai else "human"

        return {
            "prediction": {
                "label": label,
                "confidence": round(confidence, 4),
                "is_ai_generated": is_ai,
            },
            "signals": {
                "ai_probability": round(ai_prob, 4),
                "human_probability": round(human_prob, 4),
            },
            "model": "Custom CNN (from scratch)",
            "processing_time_ms": elapsed_ms,
        }


class HuggingFaceDetector:
    """Pre-trained BEiT-Large model from HuggingFace (boluobobo/ItsNotAI-ai-detector-v2)."""

    def __init__(self):
        self.model = None
        self.processor = None
        self.binary_head = None
        self.source_is_real = {}
        self.is_loaded = False

    def load_model(self):
        from transformers import AutoModelForImageClassification, AutoImageProcessor
        from huggingface_hub import hf_hub_download

        print(f"Loading HuggingFace model: {HUGGINGFACE_MODEL_ID}")
        self.processor = AutoImageProcessor.from_pretrained(HUGGINGFACE_MODEL_ID)
        self.model = AutoModelForImageClassification.from_pretrained(HUGGINGFACE_MODEL_ID)
        self.model.to(DEVICE).eval()

        binary_path = hf_hub_download(repo_id=HUGGINGFACE_MODEL_ID, filename="binary_head.pt")
        self.binary_head = nn.Sequential(
            nn.Dropout(0.1), nn.Linear(self.model.config.hidden_size, 2)
        )
        self.binary_head.load_state_dict(torch.load(binary_path, map_location=DEVICE))
        self.binary_head.to(DEVICE).eval()

        meta_path = hf_hub_download(repo_id=HUGGINGFACE_MODEL_ID, filename="source_meta.json")
        with open(meta_path) as f:
            meta = json.load(f)
        self.source_is_real = meta["source_is_real"]

        n_params = sum(p.numel() for p in self.model.parameters())
        print(f"Model loaded: {HUGGINGFACE_MODEL_ID} ({n_params:,} params)")
        self.is_loaded = True

    def _backbone_features(self, pixel_values):
        if hasattr(self.model, "beit"):
            outputs = self.model.beit(pixel_values)
        elif hasattr(self.model, "vit"):
            outputs = self.model.vit(pixel_values)
        else:
            outputs = self.model(pixel_values, output_hidden_states=True)
            return outputs.hidden_states[-1][:, 0]
        return outputs.last_hidden_state[:, 0]

    def analyze(self, image_bytes: bytes, filename: str = "unknown") -> dict:
        if not self.is_loaded:
            self.load_model()

        start = time.time()

        img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
        inputs = self.processor(img, return_tensors="pt")
        inputs = {k: v.to(DEVICE) for k, v in inputs.items()}

        with torch.no_grad():
            outputs = self.model(**inputs)
            features = self._backbone_features(inputs["pixel_values"])
            binary_logits = self.binary_head(features)
            binary_probs = torch.softmax(binary_logits, dim=-1)[0]

        elapsed_ms = round((time.time() - start) * 1000, 1)

        human_prob = binary_probs[0].item()
        ai_prob = binary_probs[1].item()

        is_ai = ai_prob > human_prob and ai_prob >= CONFIDENCE_THRESHOLD
        confidence = max(ai_prob, human_prob)
        label = "AI-generated" if is_ai else "human"

        return {
            "prediction": {
                "label": label,
                "confidence": round(confidence, 4),
                "is_ai_generated": is_ai,
            },
            "signals": {
                "ai_probability": round(ai_prob, 4),
                "human_probability": round(human_prob, 4),
            },
            "model": f"HuggingFace ({HUGGINGFACE_MODEL_ID})",
            "processing_time_ms": elapsed_ms,
        }


# Unified interface — pick implementation based on config
if MODEL_TYPE == "huggingface":
    AIDetector = HuggingFaceDetector
else:
    AIDetector = CustomDetector
