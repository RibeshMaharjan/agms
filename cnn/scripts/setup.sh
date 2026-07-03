#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
CNN_DIR="$(dirname "$SCRIPT_DIR")"

echo "=== GalleryNest CNN Setup ==="

cd "$CNN_DIR"

echo "[1/3] Creating virtual environment..."
python3 -m venv venv
source venv/bin/activate

echo "[2/3] Installing Python dependencies..."
pip install --upgrade pip
pip install -r requirements.txt

echo "[3/3] Verifying installation..."
python -c "
import fastapi, uvicorn, torch, torchvision, transformers, huggingface_hub
print('All dependencies OK')
"

echo ""
echo "=== Setup complete ==="
echo ""
echo "To pre-download the HuggingFace model (optional, ~1.2GB):"
echo "  python -c \"from transformers import AutoModelForImageClassification, AutoImageProcessor; AutoModelForImageClassification.from_pretrained('boluobobo/ItsNotAI-ai-detector-v2'); AutoImageProcessor.from_pretrained('boluobobo/ItsNotAI-ai-detector-v2')\""
echo ""
echo "To train the custom CNN model:"
echo "  python train/prepare_dataset.py    # Downloads AI-ArtBench from Kaggle"
echo "  python train/train.py data         # Trains the CNN (50 epochs)"
echo ""
echo "To start the service:"
echo "  scripts/start.sh"
echo ""
echo "Model selection (set env var before starting):"
echo "  CNN_MODEL_TYPE=huggingface  # boluobobo/ItsNotAI-ai-detector-v2 (default)"
echo "  CNN_MODEL_TYPE=custom       # Custom CNN from scratch"
