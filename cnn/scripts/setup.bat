@echo off
setlocal

set "SCRIPT_DIR=%~dp0"
set "CNN_DIR=%SCRIPT_DIR%.."

echo === GalleryNest CNN Setup ===

cd /d "%CNN_DIR%"

echo [1/3] Creating virtual environment...
python -m venv venv
call venv\Scripts\activate.bat

echo [2/3] Installing Python dependencies...
python -m pip install --upgrade pip
pip install -r requirements.txt

echo [3/3] Verifying installation...
python -c "import fastapi, uvicorn, torch, torchvision; print('All dependencies OK')"

echo.
echo === Setup complete ===
echo.
echo To download dataset and train the model:
echo   python train\prepare_dataset.py    # Downloads AI-ArtBench from Kaggle
echo   python train\train.py data         # Trains the CNN
echo.
echo To start the service:
echo   scripts\start.bat
