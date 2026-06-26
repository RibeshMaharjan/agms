import time
from transformers import pipeline
from config import MODEL_NAME
from utils.image_utils import preprocess_image


class AIDetector:
    def __init__(self):
        self.pipe = None
        self.is_loaded = False

    def load_model(self):
        self.pipe = pipeline(
            "image-classification",
            model=MODEL_NAME
        )
        self.is_loaded = True

    def analyze(self, image_bytes: bytes, filename: str = "unknown") -> dict:
        if not self.is_loaded:
            self.load_model()

        start = time.time()

        img = preprocess_image(image_bytes)
        results = self.pipe(img)

        elapsed_ms = round((time.time() - start) * 1000, 1)

        ai_score = 0.0
        human_score = 0.0
        for r in results:
            label_lower = r["label"].lower()
            if "ai" in label_lower or "generated" in label_lower:
                ai_score = r["score"]
            elif "human" in label_lower or "real" in label_lower:
                human_score = r["score"]

        if ai_score == 0.0 and human_score == 0.0 and len(results) >= 2:
            ai_score = results[0]["score"]
            human_score = results[1]["score"]

        is_ai = ai_score > human_score
        confidence = max(ai_score, human_score)
        label = "AI-generated" if is_ai else "human"

        return {
            "prediction": {
                "label": label,
                "confidence": round(confidence, 4),
                "is_ai_generated": is_ai
            },
            "all_scores": {r["label"]: round(r["score"], 4) for r in results},
            "model": MODEL_NAME,
            "processing_time_ms": elapsed_ms
        }
