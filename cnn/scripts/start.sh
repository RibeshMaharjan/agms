#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
CNN_DIR="$(dirname "$SCRIPT_DIR")"

cd "$CNN_DIR"

if [ ! -d "venv" ]; then
    echo "Virtual environment not found. Run scripts/setup.sh first."
    exit 1
fi

source venv/bin/activate

echo "Starting GalleryNest CNN Detection Service on port 8000..."
python app.py
