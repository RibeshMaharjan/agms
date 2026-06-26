# Art Gallery Management System

A full-stack PHP web application for managing and selling art online, with AI-generated image detection.

## Quick Start

### 1. Database Setup

**Linux / macOS:**
```bash
mysql -u chandan -pchandan -e "CREATE DATABASE IF NOT EXISTS agms;"
mysql -u chandan -pchandan agms < database/newagms.sql
mysql -u chandan -pchandan agms < database/migration_add_ai_column.sql
```

**Windows (XAMPP):**
```cmd
mysql -u chandan -pchandan -e "CREATE DATABASE IF NOT EXISTS agms;"
mysql -u chandan -pchandan agms < database\newagms.sql
mysql -u chandan -pchandan agms < database\migration_add_ai_column.sql
```

Database credentials: `chandan` / `chandan` (configured in `includes/dbconnection.php`)

### 2. Start the Web Server

Using PHP's built-in server:
```bash
php -S localhost:8080
```

Or use Laragon/XAMPP and place the project in the `www`/`htdocs` directory.

### 3. Start the CNN Detection Service

**Linux / macOS:**
```bash
cd cnn
chmod +x scripts/setup.sh
./scripts/setup.sh          # One-time setup (installs Python deps + model)
./scripts/start.sh           # Start the FastAPI service on port 8000
```

**Windows:**
```cmd
cd cnn
scripts\setup.bat            # One-time setup (installs Python deps + model)
scripts\start.bat            # Start the FastAPI service on port 8000
```

Or manually (any OS):
```bash
cd cnn
python -m venv venv
# Linux/macOS: source venv/bin/activate
# Windows:     venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

### 4. Access the Application

- **Storefront:** http://localhost:8080
- **Admin Panel:** http://localhost:8080/admin
- **CNN Health Check:** http://127.0.0.1:8000/health

## Default Login Credentials

### Admin Panel
- URL: `/admin`
- Username: `admin`
- Password: `admin`

### User Registration
- Users can register through the registration form
- After registration, users can log in to make purchases

## Architecture

```
gallerynest/
├── index.php                  # Homepage
├── product.php                # Product listing
├── single-product.php         # Product detail + recommendations
├── art-enquiry.php            # Purchase form
├── login.php / register.php   # Auth
├── includes/
│   ├── dbconnection.php       # MySQL connection (chandan/chandan)
│   ├── header.php / footer.php
│   ├── cnn_helper.php         # CNN service integration
│   └── recommendation_functions.php
├── admin/                     # Admin panel (CRUD)
│   ├── add-art-product.php    # Upload with CNN check
│   ├── dashboard.php
│   └── ...
├── cnn/                       # Python AI detection service
│   ├── app.py                 # FastAPI server
│   ├── detector.py            # ItsNotAI v2 dual-head detection
│   ├── config.py              # Configuration
│   ├── requirements.txt       # Python dependencies
│   ├── scripts/               # Setup & start scripts
│   └── tests/                 # Unit & API tests
├── database/
│   ├── newagms.sql            # Full schema + seed data
│   └── migration_add_ai_column.sql
└── esewa/                     # Payment gateway callbacks
```

## CNN AI Image Detection

When an admin uploads an artwork, the image is automatically checked by a CNN model:

1. Image uploaded via admin panel
2. PHP sends image to FastAPI service (`localhost:8000/detect`)
3. ItsNotAI v2 (BEiT-Large) classifies as "AI-generated" or "human"
4. Result stored in `tblartproduct.IsAIGenerated` column
5. Admin sees warning if image is flagged

### API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/detect` | POST | Classify image (multipart/form-data) |
| `/health` | GET | Service status |

### Configuration

Environment variables for the CNN service:

| Variable | Default | Description |
|----------|---------|-------------|
| `CNN_SERVICE_PORT` | `8000` | Service port |
| `CNN_MODEL_NAME` | `boluobobo/ItsNotAI-ai-detector-v2` | HuggingFace model |
| `CNN_CONFIDENCE_THRESHOLD` | `0.7` | AI flagging threshold |

## Tech Stack

- **Backend:** PHP (no framework), MySQLi
- **Database:** MariaDB/MySQL (`agms`)
- **Frontend:** Bootstrap 4, jQuery
- **AI Detection:** Python FastAPI + PyTorch + HuggingFace Transformers
- **Payment:** eSewa (Nepal)
- **Server:** PHP built-in / Laragon / XAMPP
