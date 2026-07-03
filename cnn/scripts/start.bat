@echo off
setlocal

set "SCRIPT_DIR=%~dp0"
set "CNN_DIR=%SCRIPT_DIR%.."

cd /d "%CNN_DIR%"

if not exist "venv" (
    echo Virtual environment not found. Run scripts\setup.bat first.
    exit /b 1
)

call venv\Scripts\activate.bat

echo Starting GalleryNest CNN Detection Service on port 7070...
python app.py
