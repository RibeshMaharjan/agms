#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
CNN_DIR="$(dirname "$SCRIPT_DIR")"

echo "=== GalleryNest CNN Setup ==="

cd "$CNN_DIR"

echo "[1/4] Creating virtual environment..."
python3 -m venv venv
source venv/bin/activate

echo "[2/4] Installing Python dependencies..."
pip install --upgrade pip
pip install -r requirements.txt

echo "[3/4] Pre-downloading AI detection models..."
python -c "from huggingface_hub import hf_hub_download; hf_hub_download(repo_id='Dafilab/ai-image-detector', filename='pytorch_model.pth'); print('Dafilab model OK')"
python -c "from transformers import AutoImageProcessor, SiglipForImageClassification; AutoImageProcessor.from_pretrained('Krishn25/AI-vs-Human-image-detection'); SiglipForImageClassification.from_pretrained('Krishn25/AI-vs-Human-image-detection'); print('SigLIP2 model OK')"

echo "[4/4] Verifying installation..."
python -c "import fastapi, uvicorn, torch, timm, huggingface_hub; print('All dependencies OK')"

echo ""
echo "=== Setup complete ==="
echo "Start the service with: scripts/start.sh"
