import os
import sys
import time
import torch
import torch.nn as nn
import torch.optim as optim
from torch.amp import autocast, GradScaler
from torch.utils.data import DataLoader, random_split
from torchvision import datasets, transforms
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score
from tqdm import tqdm

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from model.cnn_architecture import AIDetectionCNN
from config import IMG_SIZE, NUM_CLASSES, NUM_EPOCHS, BATCH_SIZE, LEARNING_RATE, MODEL_SAVE_PATH

DEVICE = torch.device("cuda" if torch.cuda.is_available() else "cpu")
SCALER = GradScaler("cuda") if DEVICE.type == "cuda" else None


def get_transforms():
    train_tf = transforms.Compose([
        transforms.Resize((IMG_SIZE, IMG_SIZE)),
        transforms.RandomHorizontalFlip(),
        transforms.RandomRotation(15),
        transforms.ColorJitter(brightness=0.2, contrast=0.2, saturation=0.2),
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])
    val_tf = transforms.Compose([
        transforms.Resize((IMG_SIZE, IMG_SIZE)),
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])
    return train_tf, val_tf


def load_data(data_dir):
    train_tf, val_tf = get_transforms()

    train_dir = os.path.join(data_dir, "train")
    val_dir = os.path.join(data_dir, "val")

    if os.path.exists(train_dir) and os.path.exists(val_dir):
        train_dataset = datasets.ImageFolder(train_dir, transform=train_tf)
        val_dataset = datasets.ImageFolder(val_dir, transform=val_tf)
        classes = train_dataset.classes
    else:
        full_dataset = datasets.ImageFolder(data_dir)
        total = len(full_dataset)
        val_size = int(total * 0.15)
        train_size = total - val_size
        train_dataset, val_dataset = random_split(full_dataset, [train_size, val_size])
        train_dataset.dataset.transform = train_tf
        val_dataset.dataset.transform = val_tf
        classes = full_dataset.classes

    train_loader = DataLoader(train_dataset, batch_size=BATCH_SIZE, shuffle=True,
                              num_workers=8, pin_memory=True, persistent_workers=True, prefetch_factor=2)
    val_loader = DataLoader(val_dataset, batch_size=BATCH_SIZE, shuffle=False,
                            num_workers=8, pin_memory=True, persistent_workers=True, prefetch_factor=2)

    return train_loader, val_loader, classes


def train_one_epoch(model, loader, criterion, optimizer, epoch):
    model.train()
    running_loss = 0.0
    all_preds = []
    all_labels = []

    pbar = tqdm(loader, desc=f"Train Epoch {epoch}", leave=False)
    for images, labels in pbar:
        images, labels = images.to(DEVICE), labels.to(DEVICE)

        optimizer.zero_grad()

        if SCALER:
            with autocast("cuda"):
                outputs = model(images)
                loss = criterion(outputs, labels)
            SCALER.scale(loss).backward()
            SCALER.step(optimizer)
            SCALER.update()
        else:
            outputs = model(images)
            loss = criterion(outputs, labels)
            loss.backward()
            optimizer.step()

        running_loss += loss.item() * images.size(0)
        preds = outputs.argmax(1)
        all_preds.extend(preds.cpu().numpy())
        all_labels.extend(labels.cpu().numpy())

        pbar.set_postfix(loss=f"{loss.item():.4f}")

    epoch_loss = running_loss / len(loader.dataset)
    epoch_acc = accuracy_score(all_labels, all_preds)
    return epoch_loss, epoch_acc


def evaluate(model, loader, criterion, epoch):
    model.eval()
    running_loss = 0.0
    all_preds = []
    all_labels = []

    pbar = tqdm(loader, desc=f"Val Epoch   {epoch}", leave=False)
    with torch.no_grad():
        for images, labels in pbar:
            images, labels = images.to(DEVICE), labels.to(DEVICE)

            if SCALER:
                with autocast("cuda"):
                    outputs = model(images)
                    loss = criterion(outputs, labels)
            else:
                outputs = model(images)
                loss = criterion(outputs, labels)

            running_loss += loss.item() * images.size(0)
            preds = outputs.argmax(1)
            all_preds.extend(preds.cpu().numpy())
            all_labels.extend(labels.cpu().numpy())

            pbar.set_postfix(loss=f"{loss.item():.4f}")

    epoch_loss = running_loss / len(loader.dataset)
    acc = accuracy_score(all_labels, all_preds)
    prec = precision_score(all_labels, all_preds, average='weighted', zero_division=0)
    rec = recall_score(all_labels, all_preds, average='weighted', zero_division=0)
    f1 = f1_score(all_labels, all_preds, average='weighted', zero_division=0)
    return epoch_loss, acc, prec, rec, f1


def main():
    data_dir = sys.argv[1] if len(sys.argv) > 1 else "data"

    print(f"Device: {DEVICE}")
    print(f"Data directory: {data_dir}")

    train_loader, val_loader, classes = load_data(data_dir)
    print(f"Classes: {classes}")
    print(f"Train batches: {len(train_loader)}, Val batches: {len(val_loader)}")

    model = AIDetectionCNN(num_classes=NUM_CLASSES).to(DEVICE)
    criterion = nn.CrossEntropyLoss()
    optimizer = optim.Adam(model.parameters(), lr=LEARNING_RATE, weight_decay=1e-4)
    scheduler = optim.lr_scheduler.ReduceLROnPlateau(optimizer, mode='min', factor=0.5, patience=2)

    print(f"\nModel architecture:")
    print(model)
    print(f"\nTotal parameters: {sum(p.numel() for p in model.parameters()):,}")

    best_acc = 0
    total_time = 0
    for epoch in range(NUM_EPOCHS):
        start = time.time()

        train_loss, train_acc = train_one_epoch(model, train_loader, criterion, optimizer, epoch+1)
        val_loss, val_acc, val_prec, val_rec, val_f1 = evaluate(model, val_loader, criterion, epoch+1)
        scheduler.step(val_loss)

        elapsed = time.time() - start
        total_time += elapsed
        avg_epoch_time = total_time / (epoch + 1)
        remaining = avg_epoch_time * (NUM_EPOCHS - epoch - 1)

        def fmt_time(s):
            h, m = int(s // 3600), int((s % 3600) // 60)
            return f"{h}h {m}m" if h > 0 else f"{m}m"

        print(f"Epoch {epoch+1}/{NUM_EPOCHS} ({elapsed:.1f}s) - ETA: {fmt_time(remaining)} - "
              f"train_loss={train_loss:.4f} train_acc={train_acc:.4f} - "
              f"val_loss={val_loss:.4f} val_acc={val_acc:.4f} val_prec={val_prec:.4f} val_rec={val_rec:.4f} val_f1={val_f1:.4f}")

        if val_acc > best_acc:
            best_acc = val_acc
            os.makedirs(os.path.dirname(MODEL_SAVE_PATH), exist_ok=True)
            torch.save(model.state_dict(), MODEL_SAVE_PATH)
            print(f"  -> Saved best model (val_acc={val_acc:.4f})")

    print(f"\nTraining complete. Best validation accuracy: {best_acc:.4f}")
    print(f"Model saved to: {MODEL_SAVE_PATH}")


if __name__ == "__main__":
    main()
