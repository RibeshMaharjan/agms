# CNN AI-Generated Image Detection Service

Detects whether uploaded artwork images are AI-generated or human-created.
Supports **two switchable backends**: a lightweight custom CNN (from scratch) or a pre-trained BEiT-Large Vision Transformer model from HuggingFace.

## How It Works

1. Admin uploads an artwork image via the GalleryNest admin panel
2. PHP sends the image to this FastAPI service via HTTP POST
3. The selected model classifies the image as "AI-generated" or "human"
4. PHP receives the result and stores the flag in the database
5. If flagged, the admin sees a warning; the image is still saved (soft-block)

## Model Selection

The backend is controlled by the `CNN_MODEL_TYPE` environment variable:

| Mode | Env Value | Architecture | Params | Accuracy | Model Size |
|------|-----------|-------------|--------|----------|-----------|
| **HuggingFace** (default) | `huggingface` | BEiT-Large (ViT) | ~304M | 95%+ (binary) | ~1.2 GB |
| **Custom CNN** | `custom` | 5 Conv Blocks + 2 FC | ~1.7M | 85-92% (benchmark) | ~7 MB |

### Custom CNN (`CNN_MODEL_TYPE=custom`)

- Architecture: `AIDetectionCNN` (defined in `model/cnn_architecture.py`)
- 5 convolutional blocks (32→64→128→256→512 channels) + global average pooling + 2 fully-connected layers
- Trained from scratch on the AI-ArtBench dataset (90K real + 90K AI images)
- 50 training epochs with Adam optimizer, ReduceLROnPlateau scheduler
- Input: 224×224 RGB, ImageNet-normalized
- Weights saved to `model/weights/cnn_best.pth`

### HuggingFace (`CNN_MODEL_TYPE=huggingface`)

- Model: [`boluobobo/ItsNotAI-ai-detector-v2`](https://huggingface.co/boluobobo/ItsNotAI-ai-detector-v2)
- Base: `microsoft/beit-large-patch16-224` fine-tuned with a dual-head architecture (binary Real/AI head + 33-class source identification head)
- Detects: Stable Diffusion, Midjourney, DALL-E, FLUX, GANs, and more
- Weights auto-downloaded on first use (~1.2 GB, cached in HuggingFace cache)
- Apache 2.0 license

## Prerequisites

- Python 3.10+
- pip
- ~500MB RAM for custom CNN, ~2GB RAM for HuggingFace model
- ~1.2 GB free disk (only if using HuggingFace model)

## Quick Setup

```bash
cd cnn
chmod +x scripts/setup.sh
./scripts/setup.sh
```

This will:
1. Create a Python virtual environment
2. Install all dependencies (PyTorch, Transformers, FastAPI, etc.)
3. Verify the installation

To pre-download the HuggingFace model weights (~1.2GB):
```bash
source venv/bin/activate
python -c "from transformers import AutoModelForImageClassification, AutoImageProcessor; AutoModelForImageClassification.from_pretrained('boluobobo/ItsNotAI-ai-detector-v2'); AutoImageProcessor.from_pretrained('boluobobo/ItsNotAI-ai-detector-v2')"
```

## Starting the Service

```bash
# Option 1: Using the script
./scripts/start.sh

# Option 2: Manual
cd cnn
source venv/bin/activate

# Default: HuggingFace
python app.py

# Or use the custom CNN:
CNN_MODEL_TYPE=custom python app.py
```

The service starts on `http://127.0.0.1:7070`.

### Quantitative evaluation

After training, keep the held-out images in `data/val/real` and `data/val/ai` and run:

```bash
python3 train/evaluate.py data --weights model/weights/cnn_best.pth \
  --output evaluation_results.json
```

The evaluator writes the confusion matrix, accuracy, weighted precision,
weighted recall, weighted F1-score, and per-class metrics to the JSON output.

## API Endpoints

### POST /detect

Classify an image as AI-generated or human-created.

**Request:** `multipart/form-data` with field `image`

**Response (HuggingFace model):**
```json
{
  "success": true,
  "filename": "artwork.jpg",
  "prediction": {
    "label": "AI-generated",
    "confidence": 0.9638,
    "is_ai_generated": true
  },
  "signals": {
    "ai_probability": 0.9638,
    "human_probability": 0.0362
  },
  "model": "HuggingFace (boluobobo/ItsNotAI-ai-detector-v2)",
  "processing_time_ms": 127.5
}
```

**Response (Custom CNN):**
```json
{
  "success": true,
  "filename": "artwork.jpg",
  "prediction": {
    "label": "human",
    "confidence": 0.8921,
    "is_ai_generated": false
  },
  "signals": {
    "ai_probability": 0.1079,
    "human_probability": 0.8921
  },
  "model": "Custom CNN (from scratch)",
  "processing_time_ms": 45.3
}
```

**Example (curl):**
```bash
curl -X POST http://127.0.0.1:7070/detect \
  -F "image=@/path/to/artwork.jpg"
```

### GET /health

Check service status and model loading state.

**Response:**
```json
{
  "status": "ok",
  "model_loaded": true
}
```

## Configuration

Environment variables (set before starting):

| Variable | Default | Description |
|----------|---------|-------------|
| `CNN_SERVICE_HOST` | `0.0.0.0` | Bind address |
| `CNN_SERVICE_PORT` | `7070` | Port number |
| `CNN_MODEL_TYPE` | `huggingface` | Model backend: `huggingface` or `custom` |
| `CNN_HF_MODEL_ID` | `boluobobo/ItsNotAI-ai-detector-v2` | HuggingFace model ID (only used when `CNN_MODEL_TYPE=huggingface`) |
| `CNN_CONFIDENCE_THRESHOLD` | `0.85` | Minimum AI probability to flag as AI-generated |
| `CNN_MAX_FILE_SIZE_MB` | `10` | Maximum upload file size |
| `CNN_MODEL_SAVE_PATH` | `model/weights/cnn_best.pth` | Custom CNN weights path |
| `CNN_BATCH_SIZE` | `64` | Training batch size (custom CNN only) |

## PHP Integration

The file `includes/cnn_helper.php` provides two functions:

```php
// Full result with confidence score
$result = detectAIGeneratedImage("/path/to/image.jpg");
// Returns: ['is_ai_generated' => bool, 'confidence' => float, 'label' => string]

// Quick boolean check
$flagged = isAIGenerated("/path/to/image.jpg");
// Returns: true if AI confidence >= threshold
```

## Testing

```bash
cd cnn
source venv/bin/activate

# Unit tests
python -m pytest tests/test_detector.py -v

# API tests
python -m pytest tests/test_api.py -v

# All tests
python -m pytest tests/ -v
```

Place test images in `tests/fixtures/` (add `sample.jpg` for full test coverage).

## Troubleshooting

**HuggingFace model download fails:**
- Check internet connection
- Ensure ~2GB free disk space for model cache
- Set `CNN_MODEL_TYPE=custom` to fall back to the lightweight CNN

**Custom CNN gives meaningless predictions:**
- Train the model first: `python train/train.py data`
- Or switch to the HuggingFace model: `CNN_MODEL_TYPE=huggingface python app.py`

**Service won't start:**
- Verify Python 3.10+: `python3 --version`
- Check port 7070 isn't in use: `lsof -i :7070`

**PHP connection refused:**
- Ensure the CNN service is running: `curl http://127.0.0.1:7070/health`
- Check firewall isn't blocking localhost:7070
