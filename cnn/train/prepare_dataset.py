import os
import sys
import shutil
import random
from pathlib import Path

try:
    import kagglehub
except ImportError:
    print("Installing kagglehub...")
    os.system(f"{sys.executable} -m pip install kagglehub")
    import kagglehub


def download_dataset():
    print("Downloading AI-ArtBench from Kaggle...")
    path = kagglehub.dataset_download("ravidussilva/real-ai-art")
    print(f"Downloaded to: {path}")
    return Path(path)


def find_dataset_dir(src: Path) -> Path:
    candidates = [
        src / "Real_AI_SD_LD_Dataset",
        src / "real-ai-art" / "Real_AI_SD_LD_Dataset",
    ]
    for c in candidates:
        if c.exists():
            return c

    for p in [src] + list(src.rglob("*")):
        if p.is_dir() and any(d.name.startswith("AI_") for d in p.iterdir() if d.is_dir()):
            return p

    return src


def collect_images(dataset_dir: Path):
    all_real = []
    all_ai = []

    for split in ["train", "test"]:
        split_dir = dataset_dir / split
        if not split_dir.exists():
            continue

        for folder in split_dir.iterdir():
            if not folder.is_dir():
                continue

            images = list(folder.glob("*.png")) + list(folder.glob("*.jpg")) + list(folder.glob("*.jpeg"))

            if folder.name.startswith("AI_LD_") or folder.name.startswith("AI_SD_"):
                all_ai.extend(images)
            elif not folder.name.startswith("AI_"):
                all_real.extend(images)

    return all_real, all_ai


def main():
    out = Path(sys.argv[1]) if len(sys.argv) > 1 else Path("data")

    src = download_dataset()
    dataset_dir = find_dataset_dir(src)
    print(f"Dataset directory: {dataset_dir}")

    all_real, all_ai = collect_images(dataset_dir)
    print(f"Found {len(all_real)} real art images, {len(all_ai)} AI-generated images")

    if len(all_real) == 0 or len(all_ai) == 0:
        print("ERROR: No images found. Check dataset structure.")
        sys.exit(1)

    random.seed(42)
    random.shuffle(all_real)
    random.shuffle(all_ai)

    val_split = 0.15
    n_real_val = int(len(all_real) * val_split)
    n_ai_val = int(len(all_ai) * val_split)

    splits = {
        "train": {"real": all_real[n_real_val:], "ai": all_ai[n_ai_val:]},
        "val": {"real": all_real[:n_real_val], "ai": all_ai[:n_ai_val]},
    }

    for split_name, classes in splits.items():
        for label, files in classes.items():
            (out / split_name / label).mkdir(parents=True, exist_ok=True)
            for i, src_file in enumerate(files):
                dst = out / split_name / label / f"{label}_{i:06d}{src_file.suffix}"
                shutil.copy2(src_file, dst)
            print(f"  {split_name}/{label}: {len(files)} images")

    print(f"\nDataset ready at: {out}/")
    print(f"\nTo train the model:")
    print(f"  python train/train.py {out}")


if __name__ == "__main__":
    main()
