# CNN Implementation for AI Image Detection

## 1. Overview

This document describes the custom Convolutional Neural Network (CNN) architecture implemented from scratch for detecting AI-generated images in the GalleryNest art platform. The model classifies images as either "human-created" or "AI-generated" using binary classification.

## 2. Architecture

### 2.1 Network Structure

```
Input Image (3, 224, 224)
         │
         ▼
┌─────────────────────────┐
│   Conv Block 1          │
│   Conv2d(3→32, 3×3)     │
│   BatchNorm2d(32)       │
│   ReLU                  │
│   MaxPool2d(2×2)        │
└─────────────────────────┘
         │ (32, 112, 112)
         ▼
┌─────────────────────────┐
│   Conv Block 2          │
│   Conv2d(32→64, 3×3)    │
│   BatchNorm2d(64)       │
│   ReLU                  │
│   MaxPool2d(2×2)        │
└─────────────────────────┘
         │ (64, 56, 56)
         ▼
┌─────────────────────────┐
│   Conv Block 3          │
│   Conv2d(64→128, 3×3)   │
│   BatchNorm2d(128)      │
│   ReLU                  │
│   MaxPool2d(2×2)        │
└─────────────────────────┘
         │ (128, 28, 28)
         ▼
┌─────────────────────────┐
│   Conv Block 4          │
│   Conv2d(128→256, 3×3)  │
│   BatchNorm2d(256)      │
│   ReLU                  │
│   MaxPool2d(2×2)        │
└─────────────────────────┘
         │ (256, 14, 14)
         ▼
┌─────────────────────────┐
│   Conv Block 5          │
│   Conv2d(256→512, 3×3)  │
│   BatchNorm2d(512)      │
│   ReLU                  │
│   MaxPool2d(2×2)        │
└─────────────────────────┘
         │ (512, 7, 7)
         ▼
┌─────────────────────────┐
│   Global Average Pool   │
│   AdaptiveAvgPool2d(1)  │
└─────────────────────────┘
         │ (512, 1, 1)
         ▼
┌─────────────────────────┐
│   Flatten               │
│   → (512)               │
└─────────────────────────┘
         │
         ▼
┌─────────────────────────┐
│   FC Layer 1            │
│   Linear(512→256)       │
│   ReLU                  │
│   Dropout(0.5)          │
└─────────────────────────┘
         │ (256)
         ▼
┌─────────────────────────┐
│   FC Layer 2 (Output)   │
│   Linear(256→2)         │
└─────────────────────────┘
         │ (2)
         ▼
    [human, ai] logits
```

### 2.2 Total Parameters

| Layer | Parameters |
|-------|-----------|
| Conv Block 1 | 3×32×3×3 + 32 = 896 |
| Conv Block 2 | 32×64×3×3 + 64 = 18,496 |
| Conv Block 3 | 64×128×3×3 + 128 = 73,856 |
| Conv Block 4 | 128×256×3×3 + 256 = 295,168 |
| Conv Block 5 | 256×512×3×3 + 512 = 1,180,160 |
| FC Layer 1 | 512×256 + 256 = 131,328 |
| FC Layer 2 | 256×2 + 2 = 514 |
| BatchNorm (5 layers) | 5×(32+64+128+256+512) = 4,960 |
| **Total** | **~1.7M parameters** |

## 3. Layer-by-Layer Explanation

### 3.1 Convolutional Layer (Conv2d)

The convolution operation extracts features from the input image. For a single output pixel:

```
Output(i,j) = Σ Σ Input(i+m, j+n) × Kernel(m, n) + Bias
              m n
```

- **Kernel size**: 3×3 (captures local patterns)
- **Padding**: 1 (preserves spatial dimensions)
- **Stride**: 1 (slides one pixel at a time)

**What each block learns:**
- Block 1: Edges, colors, textures
- Block 2: Simple shapes, patterns
- Block 3: Complex textures, regions
- Block 4: Object parts, structures
- Block 5: High-level semantic features

### 3.2 Batch Normalization

Normalizes activations across the batch to stabilize training:

```
y = (x - μ_batch) / √(σ²_batch + ε) × γ + β
```

Where:
- μ_batch, σ²_batch: batch mean and variance
- γ, β: learnable scale and shift parameters
- ε: small constant for numerical stability

**Benefits:**
- Faster convergence
- Reduces internal covariate shift
- Allows higher learning rates

### 3.3 ReLU Activation

Applies non-linearity: `f(x) = max(0, x)`

- Introduces non-linearity to learn complex patterns
- Computationally efficient
- Avoids vanishing gradient problem

### 3.4 Max Pooling

Downsamples feature maps by taking maximum value in each 2×2 window:

```
Output(i,j) = max(Input(2i:2i+2, 2j:2j+2))
```

**Purpose:**
- Reduces spatial dimensions by half
- Provides translation invariance
- Reduces computation for subsequent layers

### 3.5 Global Average Pooling

Converts (512, 7, 7) feature map to (512) vector by averaging:

```
Output(k) = (1/H×W) Σᵢ Σⱼ FeatureMap(k, i, j)
```

**Advantages over Flatten:**
- No parameters to learn
- Reduces overfitting
- Handles variable input sizes

### 3.6 Fully Connected Layers

**FC1 (512→256):**
```
Output = ReLU(Weight × Input + Bias)
```
- Weight matrix: 256×512
- Learns combinations of high-level features

**Dropout (0.5):**
- Randomly sets 50% of activations to zero during training
- Prevents co-adaptation of neurons
- Acts as regularization

**FC2 (256→2):**
```
Output = Weight × Input + Bias
```
- Produces raw logits for [human, ai] classes

## 4. Training Process

### 4.1 Dataset

- **Source**: AI-ArtBench (Kaggle: ravidussilva/real-ai-art)
- **Real images**: 90,000 art pieces (10 styles: Renaissance, Impressionism, etc.)
- **AI images**: 90,000 (Stable Diffusion + Latent Diffusion)
- **Split**: 85% training, 15% validation

### 4.2 Data Augmentation

Applied during training to increase diversity:
- Random horizontal flip (50% probability)
- Random rotation (±15 degrees)
- Color jitter (brightness, contrast, saturation ±20%)
- Normalize to ImageNet statistics

### 4.3 Loss Function

**Cross-Entropy Loss:**
```
L = -Σᵢ yᵢ log(ŷᵢ)
```
Where:
- yᵢ: true label (one-hot)
- ŷᵢ: predicted probability

For binary classification:
```
L = -[y × log(ŷ) + (1-y) × log(1-ŷ)]
```

### 4.4 Optimizer

**Adam (Adaptive Moment Estimation):**
```
m_t = β₁ × m_{t-1} + (1 - β₁) × g_t          (first moment)
v_t = β₂ × v_{t-1} + (1 - β₂) × g_t²          (second moment)
m̂_t = m_t / (1 - β₁ᵗ)                          (bias-corrected)
v̂_t = v_t / (1 - β₂ᵗ)                          (bias-corrected)
θ_t = θ_{t-1} - lr × m̂_t / (√v̂_t + ε)         (update)
```

Parameters:
- Learning rate: 0.001
- Weight decay: 1e-4 (L2 regularization)
- β₁ = 0.9, β₂ = 0.999

### 4.5 Learning Rate Scheduler

**ReduceLROnPlateau:**
- Monitors validation loss
- Reduces LR by factor of 0.5 if no improvement for 2 epochs
- Prevents getting stuck in local minima

### 4.6 Training Loop

```python
for epoch in range(num_epochs):
    # Training phase
    for images, labels in train_loader:
        outputs = model(images)           # Forward pass
        loss = criterion(outputs, labels) # Compute loss
        loss.backward()                   # Backward pass
        optimizer.step()                  # Update weights

    # Validation phase
    for images, labels in val_loader:
        outputs = model(images)
        # Compute metrics (accuracy, precision, recall)

    # Save best model
    if val_acc > best_acc:
        save_model(model)
```

## 5. Inference Pipeline

### 5.1 Preprocessing

```python
transform = transforms.Compose([
    transforms.Resize((224, 224)),
    transforms.ToTensor(),                    # [0, 255] → [0, 1]
    transforms.Normalize(                     # ImageNet normalization
        mean=[0.485, 0.456, 0.406],
        std=[0.229, 0.224, 0.225]
    ),
])
```

### 5.2 Prediction

```python
with torch.no_grad():
    logits = model(image_tensor)        # Raw output [human, ai]
    probs = softmax(logits)             # Probabilities [0, 1]
    prediction = argmax(probs)          # Class label
    confidence = max(probs)             # Confidence score
```

### 5.3 Decision Logic

```python
is_ai = (ai_probability > human_probability) and (ai_probability >= threshold)
```

Default threshold: 0.85 (configurable via environment variable)

## 6. Algorithm Comparison

| Aspect | Custom CNN | Pre-trained (BEiT-Large) |
|--------|-----------|-------------------------|
| Parameters | ~1.7M | ~304M |
| Input size | 224×224 | 224×224 |
| Training data | AI-ArtBench | Large-scale datasets |
| Training time | ~2-3 hours | N/A (pre-trained) |
| Accuracy (expected) | 85-92% | 95%+ |
| Model size | ~7MB | ~1.2GB |
| Dependencies | PyTorch only | Transformers, HuggingFace |

## 7. Implementation Files

```
cnn/
├── model/
│   ├── cnn_architecture.py    # CNN model definition
│   └── weights/
│       └── cnn_best.pth       # Trained weights
├── train/
│   ├── prepare_dataset.py     # Dataset download/organize
│   └── train.py               # Training script
├── detector.py                # Inference pipeline
├── app.py                     # FastAPI server
├── config.py                  # Configuration
└── requirements.txt           # Dependencies
```

## 8. References

1. LeCun, Y., et al. (1998). Gradient-based learning applied to document recognition. Proceedings of the IEEE.
2. Krizhevsky, A., et al. (2012). ImageNet classification with deep convolutional neural networks. NeurIPS.
3. Ioffe, S., & Szegedy, C. (2015). Batch normalization: Accelerating deep network training. ICML.
4. Srivastava, N., et al. (2014). Dropout: A simple way to prevent neural networks from overfitting. JMLR.
5. Kingma, D. P., & Ba, J. (2015). Adam: A method for stochastic optimization. ICLR.
