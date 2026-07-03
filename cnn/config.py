import os

SERVICE_HOST = os.getenv("CNN_SERVICE_HOST", "0.0.0.0")
SERVICE_PORT = int(os.getenv("CNN_SERVICE_PORT", "7070"))
CONFIDENCE_THRESHOLD = float(os.getenv("CNN_CONFIDENCE_THRESHOLD", "0.85"))
MAX_FILE_SIZE_MB = int(os.getenv("CNN_MAX_FILE_SIZE_MB", "10"))

# Model selection: "huggingface" (pre-trained BEiT) or "custom" (from-scratch CNN)
MODEL_TYPE = os.getenv("CNN_MODEL_TYPE", "huggingface").lower()
# MODEL_TYPE = os.getenv("CNN_MODEL_TYPE", "custom").lower()  # lightweight fallback
HUGGINGFACE_MODEL_ID = os.getenv("CNN_HF_MODEL_ID", "boluobobo/ItsNotAI-ai-detector-v2")

# Custom CNN settings
MODEL_SAVE_PATH = os.getenv("CNN_MODEL_SAVE_PATH", "model/weights/cnn_best.pth")
IMG_SIZE = 224
NUM_CLASSES = 2
NUM_EPOCHS = 50
BATCH_SIZE = int(os.getenv("CNN_BATCH_SIZE", "64"))
LEARNING_RATE = 0.001
