from pydantic import BaseModel
from typing import Dict, Any


class Prediction(BaseModel):
    label: str
    confidence: float
    is_ai_generated: bool


class DetectionResult(BaseModel):
    success: bool
    filename: str | None = None
    prediction: Prediction | None = None
    signals: Dict[str, Any] | None = None
    model: str | None = None
    processing_time_ms: float | None = None
    error: str | None = None


class HealthResponse(BaseModel):
    status: str
    model_loaded: bool
