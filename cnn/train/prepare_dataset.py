import os
import sys
import shutil
import random
from pathlib import Path

try:
    from kagglehub import dataset_download
except ImportError:
    dataset_download = None


def download_dataset():
    if dataset_download is None:
        print("Install kagglehub: pip install kagglehub")
        print("Then set KAGGLE_USERNAME and KAGGLE_KEY environment variables")
        sys.exit(1)

    print("Downloading AI-ArtBench from Kaggle...")
    path = dataset_download("ravidussilva/real-ai-art")
    return Path(path)


def prepare_binary(src_dir: Path, out_dir: Path, val_split=0.15):
    real_dirs = ["artbench-10-cartoon", "artbench-10-photo", "artbench-10-pixel",
                 "artbench-10-pop_art", "artbench-10-impressionism",
                 "artbench-10-renaissance", "artbench-10-sketch",
                 "artbench-10-ugly", "artbench-10-realism", "artbench-10-japanese"]
    ai_dirs = ["latent_diffusion", "standard_diffusion"]

    for split in ["train", "val"]:
        (out_dir / split / "real").mkdir(parents=True, exist_ok=True)
        (out_dir / split / "ai").mkdir(parents=True, exist_ok=True)

    all_real = []
    for d in real_dirs:
        p = src_dir / d
        if p.exists():
            all_real.extend(list(p.glob("*.png")) + list(p.glob("*.jpg")) + list(p.glob("*.jpeg")))

    all_ai = []
    for d in ai_dirs:
        p = src_dir / d
        if p.exists():
            all_ai.extend(list(p.glob("*.png")) + list(p.glob("*.jpg")) + list(p.glob("*.jpeg")))

    print(f"Found {len(all_real)} real images, {len(all_ai)} AI images")

    random.seed(42)
    random.shuffle(all_real)
    random.shuffle(all_ai)

    n_real_val = int(len(all_real) * val_split)
    n_ai_val = int(len(all_ai) * val_split)

    splits = {
        "train": {"real": all_real[n_real_val:], "ai": all_ai[n_ai_val:]},
        "val": {"real": all_real[:n_real_val], "ai": all_ai[:n_ai_val]},
    }

    for split_name, classes in splits.items():
        for label, files in classes.items():
            for i, src in enumerate(files):
                dst = out_dir / split_name / label / f"{label}_{i:06d}{src.suffix}"
                shutil.copy2(src, dst)
            print(f"  {split_name}/{label}: {len(files)} images")


def main():
    src = download_dataset()
    out = Path("data")
    prepare_binary(src, out)
    print(f"\nDataset ready at {out}/")
    print("Directory structure:")
    for p in sorted(out.rglob("*")):
        if p.is_dir():
            count = len(list(p.glob("*.*")))
            if count > 0:
                print(f"  {p} ({count} images)")


if __name__ == "__main__":
    main()
