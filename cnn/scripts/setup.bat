@echo off
setlocal

set "SCRIPT_DIR=%~dp0"
set "CNN_DIR=%SCRIPT_DIR%.."

echo === GalleryNest CNN Setup ===

cd /d "%CNN_DIR%"

echo [1/4] Creating virtual environment...
python -m venv venv
call venv\Scripts\activate.bat

echo [2/4] Installing Python dependencies...
python -m pip install --upgrade pip
pip install -r requirements.txt

echo [3/4] Pre-downloading AI detection model...
python -c "from transformers import pipeline; pipeline('image-classification', model='dima806/ai_vs_human_generated_image_detection')"

echo [4/4] Verifying installation...
python -c "import fastapi, uvicorn, transformers, torch; print('All dependencies OK')"

echo.
echo === Setup complete ===
echo Start the service with: scripts\start.bat
