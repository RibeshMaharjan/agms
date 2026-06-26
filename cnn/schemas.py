from pydantic import BaseModel
from typing import Dict


class Prediction(BaseModel):
    label: str
    confidence: float
    is_ai_generated: bool


class DetectionResult(BaseModel):
    success: bool
    filename: str | None = None
    prediction: Prediction | None = None
    all_scores: Dict[str, float] | None = None
    model: str | None = None
    processing_time_ms: float | None = None
    error: str | None = None


class HealthResponse(BaseModel):
    status: str
    model_loaded: bool
