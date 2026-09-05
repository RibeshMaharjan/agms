"""Evaluate the custom CNN on a labelled held-out split.

Expected layout::

    data/val/real/*.jpg
    data/val/ai/*.jpg

The output JSON is suitable for copying the measured values into the report.
"""

import argparse
import json
import os
import sys
from pathlib import Path

import torch
from sklearn.metrics import (accuracy_score, confusion_matrix,
                             precision_recall_fscore_support)
from torch.utils.data import DataLoader
from torchvision import datasets, transforms

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from config import BATCH_SIZE, IMG_SIZE, MODEL_SAVE_PATH, NUM_CLASSES
from model.cnn_architecture import AIDetectionCNN


def evaluate(data_dir: Path, weights: Path, batch_size: int):
    split_dir = data_dir / "val"
    if not split_dir.is_dir():
        raise FileNotFoundError(f"Validation directory not found: {split_dir}")

    transform = transforms.Compose([
        transforms.Resize((IMG_SIZE, IMG_SIZE)),
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406],
                             std=[0.229, 0.224, 0.225]),
    ])
    dataset = datasets.ImageFolder(split_dir, transform=transform)
    expected = ["real", "ai"]
    if dataset.classes != expected:
        raise ValueError(
            f"Expected class folders in order {expected}, found {dataset.classes}."
        )
    if len(dataset) == 0:
        raise ValueError(f"No validation images found in {split_dir}")
    if len(set(dataset.targets)) != NUM_CLASSES:
        raise ValueError("The validation split must contain both real and ai images")

    loader = DataLoader(dataset, batch_size=batch_size, shuffle=False, num_workers=0)
    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    model = AIDetectionCNN(num_classes=NUM_CLASSES).to(device)
    if not weights.is_file():
        raise FileNotFoundError(f"CNN weights not found: {weights}")
    checkpoint = torch.load(weights, map_location=device)
    state_dict = checkpoint.get("state_dict", checkpoint) if isinstance(checkpoint, dict) else checkpoint
    model.load_state_dict(state_dict)
    model.eval()

    labels, predictions = [], []
    with torch.no_grad():
        for images, batch_labels in loader:
            outputs = model(images.to(device))
            predictions.extend(outputs.argmax(dim=1).cpu().tolist())
            labels.extend(batch_labels.tolist())

    matrix = confusion_matrix(labels, predictions, labels=[0, 1])
    per_precision, per_recall, per_f1, per_support = precision_recall_fscore_support(
        labels, predictions, labels=[0, 1], zero_division=0
    )
    weighted = precision_recall_fscore_support(
        labels, predictions, average="weighted", zero_division=0
    )
    result = {
        "model": "AIDetectionCNN (custom CNN)",
        "dataset": str(split_dir),
        "weights": str(weights),
        "class_order": expected,
        "samples": len(labels),
        "confusion_matrix": matrix.tolist(),
        "confusion_matrix_labels": {"rows": "actual", "columns": "predicted"},
        "accuracy": accuracy_score(labels, predictions),
        "weighted_precision": weighted[0],
        "weighted_recall": weighted[1],
        "weighted_f1": weighted[2],
        "per_class": {
            name: {
                "precision": float(per_precision[i]),
                "recall": float(per_recall[i]),
                "f1": float(per_f1[i]),
                "support": int(per_support[i]),
            }
            for i, name in enumerate(expected)
        },
    }
    return result


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("data", type=Path, help="Dataset root containing val/real and val/ai")
    parser.add_argument("--weights", type=Path, default=Path(MODEL_SAVE_PATH))
    parser.add_argument("--output", type=Path, default=Path("evaluation_results.json"))
    parser.add_argument("--batch-size", type=int, default=BATCH_SIZE)
    args = parser.parse_args()

    result = evaluate(args.data, args.weights, args.batch_size)
    args.output.write_text(json.dumps(result, indent=2) + "\n", encoding="utf-8")
    tn, fp, fn, tp = result["confusion_matrix"][0] + result["confusion_matrix"][1]
    print(f"Samples: {result['samples']}")
    print(f"Confusion matrix (actual rows, predicted columns): [[{tn}, {fp}], [{fn}, {tp}]]")
    print(f"Accuracy: {result['accuracy']:.4f}")
    print(f"Weighted precision: {result['weighted_precision']:.4f}")
    print(f"Weighted recall: {result['weighted_recall']:.4f}")
    print(f"Weighted F1-score: {result['weighted_f1']:.4f}")
    print(f"Saved: {args.output}")


if __name__ == "__main__":
    main()
