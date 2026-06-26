import os

SERVICE_HOST = os.getenv("CNN_SERVICE_HOST", "127.0.0.1")
SERVICE_PORT = int(os.getenv("CNN_SERVICE_PORT", "8000"))
MODEL_NAME = os.getenv("CNN_MODEL_NAME", "dima806/ai_vs_human_generated_image_detection")
CONFIDENCE_THRESHOLD = float(os.getenv("CNN_CONFIDENCE_THRESHOLD", "0.7"))
MAX_FILE_SIZE_MB = int(os.getenv("CNN_MAX_FILE_SIZE_MB", "10"))
