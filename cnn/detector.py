import time
import io
import os
import torch
from PIL import Image
from torchvision import transforms
from model.cnn_architecture import AIDetectionCNN
from config import MODEL_SAVE_PATH, IMG_SIZE, NUM_CLASSES, CONFIDENCE_THRESHOLD

DEVICE = torch.device("cuda" if torch.cuda.is_available() else "cpu")

inference_transform = transforms.Compose([
    transforms.Resize((IMG_SIZE, IMG_SIZE)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
])


class AIDetector:
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
                "is_ai_generated": is_ai
            },
            "signals": {
                "ai_probability": round(ai_prob, 4),
                "human_probability": round(human_prob, 4)
            },
            "model": "Custom CNN (from scratch)",
            "processing_time_ms": elapsed_ms
        }
