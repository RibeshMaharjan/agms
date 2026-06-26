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


def organize_binary(src_dir: Path, out_dir: Path, val_split=0.15):
    real_dirs = [
        "artbench-10-cartoon", "artbench-10-photo", "artbench-10-pixel",
        "artbench-10-pop_art", "artbench-10-impressionism",
        "artbench-10-renaissance", "artbench-10-sketch",
        "artbench-10-ugly", "artbench-10-realism", "artbench-10-japanese"
    ]
    ai_dirs = ["latent_diffusion", "standard_diffusion"]

    for split in ["train", "val"]:
        (out_dir / split / "real").mkdir(parents=True, exist_ok=True)
        (out_dir / split / "ai").mkdir(parents=True, exist_ok=True)

    all_real = []
    for d in real_dirs:
        p = src_dir / d
        if p.exists():
            all_real.extend(list(p.glob("*.png")) + list(p.glob("*.jpg")) + list(p.glob("*.jpeg")))
    print(f"Found {len(all_real)} real art images")

    all_ai = []
    for d in ai_dirs:
        p = src_dir / d
        if p.exists():
            all_ai.extend(list(p.glob("*.png")) + list(p.glob("*.jpg")) + list(p.glob("*.jpeg")))
    print(f"Found {len(all_ai)} AI-generated images")

    if len(all_real) == 0 or len(all_ai) == 0:
        print("ERROR: No images found. Check dataset structure.")
        print(f"Source directory contents: {list(src_dir.iterdir())}")
        sys.exit(1)

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
    out = Path(sys.argv[1]) if len(sys.argv) > 1 else Path("data")

    src = download_dataset()

    possible_dirs = [src, src / "real-ai-art", src / "real_ai_art"]
    dataset_dir = None
    for d in possible_dirs:
        if d.exists():
            contents = list(d.iterdir())
            if any("artbench" in str(c).lower() or "diffusion" in str(c).lower() for c in contents):
                dataset_dir = d
                break

    if dataset_dir is None:
        print(f"ERROR: Could not find dataset subdirectories.")
        print(f"Source directory: {src}")
        print(f"Contents: {list(src.iterdir())}")
        sys.exit(1)

    print(f"\nUsing dataset from: {dataset_dir}")
    organize_binary(dataset_dir, out)

    print(f"\nDataset ready at: {out}/")
    print("Directory structure:")
    for p in sorted(out.rglob("*")):
        if p.is_dir():
            count = len(list(p.glob("*.*")))
            if count > 0:
                print(f"  {p} ({count} images)")

    print(f"\nTo train the model:")
    print(f"  python train/train.py {out}")


if __name__ == "__main__":
    main()
