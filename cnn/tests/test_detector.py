import sys
import os
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from detector import AIDetector


def test_detector_loads():
    d = AIDetector()
    d.load_model()
    assert d.is_loaded


def test_analyze_returns_prediction():
    d = AIDetector()
    d.load_model()

    fixture = os.path.join(os.path.dirname(__file__), "fixtures", "sample.jpg")
    if os.path.exists(fixture):
        with open(fixture, "rb") as f:
            result = d.analyze(f.read(), "sample.jpg")
        assert "prediction" in result
        assert "label" in result["prediction"]
        assert "confidence" in result["prediction"]
        assert "is_ai_generated" in result["prediction"]
