import sys
import os
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from fastapi.testclient import TestClient
from app import app

client = TestClient(app)


def test_health():
    response = client.get("/health")
    assert response.status_code == 200
    data = response.json()
    assert data["status"] == "ok"


def test_detect_no_file():
    response = client.post("/detect")
    assert response.status_code == 422


def test_detect_with_file():
    fixture = os.path.join(os.path.dirname(__file__), "fixtures", "sample.jpg")
    if os.path.exists(fixture):
        with open(fixture, "rb") as f:
            response = client.post("/detect", files={"image": ("test.jpg", f, "image/jpeg")})
        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
