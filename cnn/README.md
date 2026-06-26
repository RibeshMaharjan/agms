# CNN AI-Generated Image Detection Service

Detects whether uploaded artwork images are AI-generated or human-created using a pre-trained Vision Transformer (ViT-Base) model.

## How It Works

1. Admin uploads an artwork image via the GalleryNest admin panel
2. PHP sends the image to this FastAPI service via HTTP POST
3. The ViT-Base model classifies the image as "AI-generated" or "human"
4. PHP receives the result and stores the flag in the database
5. If flagged, the admin sees a warning; the image is still saved (soft-block)

## Model

- **Architecture:** ViT-Base (Vision Transformer)
- **Pre-trained on:** `dima806/ai_vs_human_generated_image_detection`
- **Accuracy:** ~98%
- **License:** Apache 2.0
- **Size:** ~340MB (downloaded on first run)

## Prerequisites

- Python 3.10+
- pip
- ~500MB RAM for model inference

## Quick Setup

```bash
cd cnn
chmod +x scripts/setup.sh
./scripts/setup.sh
```

This will:
1. Create a Python virtual environment
2. Install all dependencies (PyTorch, Transformers, FastAPI)
3. Pre-download the model weights (~340MB)
4. Verify the installation

## Starting the Service

```bash
# Option 1: Using the script
./scripts/start.sh

# Option 2: Manual
cd cnn
source venv/bin/activate
python app.py
```

The service starts on `http://127.0.0.1:8000`.

## API Endpoints

### POST /detect

Classify an image as AI-generated or human-created.

**Request:** `multipart/form-data` with field `image`

**Response:**
```json
{
  "success": true,
  "filename": "artwork.jpg",
  "prediction": {
    "label": "AI-generated",
    "confidence": 0.9423,
    "is_ai_generated": true
  },
  "all_scores": {
    "AI": 0.9423,
    "human": 0.0577
  },
  "model": "dima806/ai_vs_human_generated_image_detection",
  "processing_time_ms": 127.5
}
```

**Example (curl):**
```bash
curl -X POST http://127.0.0.1:8000/detect \
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

Environment variables (set in `.env` or export before starting):

| Variable | Default | Description |
|----------|---------|-------------|
| `CNN_SERVICE_HOST` | `127.0.0.1` | Bind address |
| `CNN_SERVICE_PORT` | `8000` | Port number |
| `CNN_MODEL_NAME` | `dima806/ai_vs_human_generated_image_detection` | HuggingFace model ID |
| `CNN_CONFIDENCE_THRESHOLD` | `0.7` | Confidence threshold for AI flagging |
| `CNN_MAX_FILE_SIZE_MB` | `10` | Maximum upload file size |

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

**Model download fails:**
- Check internet connection
- Ensure ~1GB free disk space for model cache

**Service won't start:**
- Verify Python 3.10+: `python3 --version`
- Check port 8000 isn't in use: `lsof -i :8000`

**PHP connection refused:**
- Ensure the CNN service is running: `curl http://127.0.0.1:8000/health`
- Check firewall isn't blocking localhost:8000
