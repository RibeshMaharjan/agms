import time
import torch
from transformers import AutoImageProcessor, SiglipForImageClassification
from config import MODEL_NAME
from utils.image_utils import preprocess_image


class AIDetector:
    def __init__(self):
        self.processor = None
        self.model = None
        self.is_loaded = False
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

    def load_model(self):
        self.processor = AutoImageProcessor.from_pretrained(MODEL_NAME)
        self.model = SiglipForImageClassification.from_pretrained(MODEL_NAME)
        self.model.to(self.device)
        self.model.eval()
        self.is_loaded = True

    def analyze(self, image_bytes: bytes, filename: str = "unknown") -> dict:
        if not self.is_loaded:
            self.load_model()

        start = time.time()

        img = preprocess_image(image_bytes)
        inputs = self.processor(images=img, return_tensors="pt").to(self.device)

        with torch.no_grad():
            outputs = self.model(**inputs)
            logits = outputs.logits
            probs = torch.softmax(logits, dim=-1)

        elapsed_ms = round((time.time() - start) * 1000, 1)

        label_map = self.model.config.id2label
        scores = {label_map[i]: round(probs[0, i].item(), 4) for i in range(len(label_map))}

        ai_score = scores.get("ai", 0.0)
        human_score = scores.get("hum", 0.0)

        is_ai = ai_score > human_score
        confidence = max(ai_score, human_score)
        label = "AI-generated" if is_ai else "human"

        return {
            "prediction": {
                "label": label,
                "confidence": round(confidence, 4),
                "is_ai_generated": is_ai
            },
            "all_scores": scores,
            "model": MODEL_NAME,
            "processing_time_ms": elapsed_ms
        }
