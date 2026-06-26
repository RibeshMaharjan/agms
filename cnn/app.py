import time
from contextlib import asynccontextmanager
from fastapi import FastAPI, File, UploadFile, HTTPException
from schemas import DetectionResult, HealthResponse
from detector import AIDetector
from config import SERVICE_HOST, SERVICE_PORT, MAX_FILE_SIZE_MB

detector = AIDetector()


@asynccontextmanager
async def lifespan(app):
    detector.load_model()
    yield


app = FastAPI(title="GalleryNest AI Image Detection", lifespan=lifespan)


@app.get("/health", response_model=HealthResponse)
async def health():
    return HealthResponse(status="ok", model_loaded=detector.is_loaded)


@app.post("/detect", response_model=DetectionResult)
async def detect(image: UploadFile = File(...)):
    start = time.time()

    contents = await image.read()

    if len(contents) > MAX_FILE_SIZE_MB * 1024 * 1024:
        raise HTTPException(status_code=413, detail=f"File exceeds {MAX_FILE_SIZE_MB}MB limit")

    allowed = {"image/jpeg", "image/png", "image/gif", "image/webp"}
    if image.content_type not in allowed:
        raise HTTPException(status_code=400, detail=f"Unsupported image type: {image.content_type}")

    try:
        result = detector.analyze(contents, image.filename)
    except Exception as e:
        return DetectionResult(success=False, error=str(e))

    elapsed_ms = round((time.time() - start) * 1000, 1)

    return DetectionResult(
        success=True,
        filename=image.filename,
        prediction=result["prediction"],
        signals=result["signals"],
        model=result["model"],
        processing_time_ms=elapsed_ms
    )


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host=SERVICE_HOST, port=SERVICE_PORT)
