import time
import io
import os
import warnings
import torch
from PIL import Image
from torchvision import transforms
from timm import create_model
from huggingface_hub import hf_hub_download
from transformers import AutoImageProcessor, SiglipForImageClassification
from config import MODEL_NAME, FINE_TUNED_MODEL, SECONDARY_MODEL, CONFIDENCE_THRESHOLD

warnings.filterwarnings("ignore", message=".*bos_token_id.*")
warnings.filterwarnings("ignore", message=".*eos_token_id.*")


IMG_SIZE = 380
LABEL_MAPPING = {1: "human", 0: "ai"}

dafilab_transform = transforms.Compose([
    transforms.Resize(IMG_SIZE + 20),
    transforms.CenterCrop(IMG_SIZE),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
])


class AIDetector:
    def __init__(self):
        self.primary_model = None
        self.secondary_processor = None
        self.secondary_model = None
        self.is_loaded = False
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

    def load_model(self):
        self.primary_model = create_model('efficientnet_b4', pretrained=False, num_classes=2)

        if os.path.exists(FINE_TUNED_MODEL):
            print(f"Loading fine-tuned model: {FINE_TUNED_MODEL}")
            self.primary_model.load_state_dict(torch.load(FINE_TUNED_MODEL, map_location=self.device))
            self.model_source = "fine-tuned"
        else:
            print(f"Loading base model from HuggingFace: {MODEL_NAME}")
            model_path = hf_hub_download(repo_id=MODEL_NAME, filename="pytorch_model.pth")
            self.primary_model.load_state_dict(torch.load(model_path, map_location=self.device))
            self.model_source = "base"

        self.primary_model.to(self.device).eval()

        self.secondary_processor = AutoImageProcessor.from_pretrained(SECONDARY_MODEL)
        self.secondary_model = SiglipForImageClassification.from_pretrained(SECONDARY_MODEL)
        self.secondary_model.to(self.device).eval()
        self.is_loaded = True

    def _check_exif(self, image_bytes: bytes) -> dict:
        try:
            img = Image.open(io.BytesIO(image_bytes))
            exif_data = img.getexif()
            has_camera = any(
                tag in exif_data
                for tag in [0x010F, 0x0110, 0xA434, 0xA431, 0xA432, 0xA433]
            )
            has_datetime = 0x9003 in exif_data or 0x9004 in exif_data
            return {"has_camera_info": has_camera, "has_datetime": has_datetime}
        except Exception:
            return {"has_camera_info": False, "has_datetime": False}

    def _predict_dafilab(self, img: Image.Image) -> float:
        tensor = dafilab_transform(img).unsqueeze(0).to(self.device)
        with torch.no_grad():
            logits = self.primary_model(tensor)
            probs = torch.nn.functional.softmax(logits, dim=1)
        return probs[0, 0].item()

    def _predict_secondary(self, img: Image.Image) -> float:
        inputs = self.secondary_processor(images=img, return_tensors="pt").to(self.device)
        with torch.no_grad():
            outputs = self.secondary_model(**inputs)
            probs = torch.softmax(outputs.logits, dim=-1)
        label_map = self.secondary_model.config.id2label
        scores = {label_map[i]: probs[0, i].item() for i in range(len(label_map))}
        return scores.get("ai", 0.0)

    def analyze(self, image_bytes: bytes, filename: str = "unknown") -> dict:
        if not self.is_loaded:
            self.load_model()

        start = time.time()

        img = Image.open(io.BytesIO(image_bytes)).convert("RGB")

        primary_ai = self._predict_dafilab(img)
        secondary_ai = self._predict_secondary(img)
        exif = self._check_exif(image_bytes)

        elapsed_ms = round((time.time() - start) * 1000, 1)

        votes_for_ai = 0
        total_signals = 3

        if primary_ai >= CONFIDENCE_THRESHOLD:
            votes_for_ai += 1
        if secondary_ai >= CONFIDENCE_THRESHOLD:
            votes_for_ai += 1
        if not exif["has_camera_info"] and not exif["has_datetime"]:
            votes_for_ai += 1

        is_ai = votes_for_ai >= 2
        avg_confidence = round((primary_ai + secondary_ai) / 2, 4)
        label = "AI-generated" if is_ai else "human"

        return {
            "prediction": {
                "label": label,
                "confidence": avg_confidence,
                "is_ai_generated": is_ai
            },
            "signals": {
                "primary_model": {"name": MODEL_NAME, "ai_score": round(primary_ai, 4)},
                "secondary_model": {"name": SECONDARY_MODEL, "ai_score": round(secondary_ai, 4)},
                "exif_metadata": exif,
                "votes_for_ai": votes_for_ai,
                "total_signals": total_signals
            },
            "model": f"{MODEL_NAME} ({self.model_source})",
            "processing_time_ms": elapsed_ms
        }
