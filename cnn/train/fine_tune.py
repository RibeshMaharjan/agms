import os
import sys
import torch
import torch.nn as nn
from torch.utils.data import DataLoader
from torchvision import datasets, transforms
from timm import create_model
from huggingface_hub import hf_hub_download

IMG_SIZE = 380
DEVICE = "cuda" if torch.cuda.is_available() else "cpu"
EPOCHS = 10
LR = 2e-5
BATCH_SIZE = 32
PATIENCE = 3

train_tf = transforms.Compose([
    transforms.Resize(IMG_SIZE + 20),
    transforms.RandomCrop(IMG_SIZE),
    transforms.RandomHorizontalFlip(),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
])
val_tf = transforms.Compose([
    transforms.Resize(IMG_SIZE + 20),
    transforms.CenterCrop(IMG_SIZE),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
])


def load_model():
    model = create_model('efficientnet_b4', pretrained=False, num_classes=2)
    ckpt = hf_hub_download(repo_id="Dafilab/ai-image-detector", filename="pytorch_model.pth")
    model.load_state_dict(torch.load(ckpt, map_location="cpu"))
    return model


def train_one_epoch(model, loader, optimizer, criterion):
    model.train()
    total_loss = correct = total = 0
    for imgs, labels in loader:
        imgs, labels = imgs.to(DEVICE), labels.to(DEVICE)
        optimizer.zero_grad()
        outputs = model(imgs)
        loss = criterion(outputs, labels)
        loss.backward()
        optimizer.step()
        total_loss += loss.item() * imgs.size(0)
        correct += (outputs.argmax(1) == labels).sum().item()
        total += labels.size(0)
    return total_loss / total, correct / total


def evaluate(model, loader):
    model.eval()
    correct = total = 0
    with torch.no_grad():
        for imgs, labels in loader:
            imgs, labels = imgs.to(DEVICE), labels.to(DEVICE)
            preds = model(imgs).argmax(1)
            correct += (preds == labels).sum().item()
            total += labels.size(0)
    return correct / total


def main():
    data_dir = sys.argv[1] if len(sys.argv) > 1 else "data"
    output_path = sys.argv[2] if len(sys.argv) > 2 else "gallery_detector_best.pth"

    print(f"Device: {DEVICE}")
    print(f"Data: {data_dir}")

    train_ds = datasets.ImageFolder(f"{data_dir}/train", transform=train_tf)
    val_ds = datasets.ImageFolder(f"{data_dir}/val", transform=val_tf)
    print(f"Train: {len(train_ds)} images ({train_ds.classes})")
    print(f"Val: {len(val_ds)} images ({val_ds.classes})")

    train_loader = DataLoader(train_ds, batch_size=BATCH_SIZE, shuffle=True, num_workers=4, pin_memory=True)
    val_loader = DataLoader(val_ds, batch_size=BATCH_SIZE, num_workers=4, pin_memory=True)

    model = load_model()
    model.to(DEVICE)

    optimizer = torch.optim.AdamW(model.parameters(), lr=LR, weight_decay=1e-4)
    scheduler = torch.optim.lr_scheduler.CosineAnnealingLR(optimizer, T_max=EPOCHS)
    criterion = nn.CrossEntropyLoss()

    best_acc = 0
    no_improve = 0

    for epoch in range(EPOCHS):
        train_loss, train_acc = train_one_epoch(model, train_loader, optimizer, criterion)
        scheduler.step()
        val_acc = evaluate(model, val_loader)

        print(f"Epoch {epoch+1}/{EPOCHS}: "
              f"train_loss={train_loss:.4f} train_acc={train_acc:.4f} val_acc={val_acc:.4f}")

        if val_acc > best_acc:
            best_acc = val_acc
            no_improve = 0
            torch.save(model.state_dict(), output_path)
            print(f"  -> Saved best model (val_acc={val_acc:.4f})")
        else:
            no_improve += 1
            if no_improve >= PATIENCE:
                print(f"  -> Early stopping (no improvement for {PATIENCE} epochs)")
                break

    print(f"\nBest validation accuracy: {best_acc:.4f}")
    print(f"Model saved to: {output_path}")


if __name__ == "__main__":
    main()
