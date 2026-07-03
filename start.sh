#!/bin/bash
set -e

# ============================================================
# GalleryNest - One-Click Start (Docker + AI Detection Model)
# ============================================================
# This script starts both:
#   1. Docker containers (web + MariaDB) via docker compose
#   2. CNN AI Detection service (FastAPI on port 7070)
# ============================================================

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

# ── Colors ──────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; NC='\033[0m' # No Color

echo -e "${CYAN}"
echo "  ╔═══════════════════════════════════════════════╗"
echo "  ║         GalleryNest - Starting Up             ║"
echo "  ╚═══════════════════════════════════════════════╝"
echo -e "${NC}"

# ── Phase 1: Docker ─────────────────────────────────────────
echo -e "${YELLOW}[1/2] Starting Docker containers...${NC}"
echo "       web  → http://localhost:6767"
echo "       db   → MariaDB (container)"

# Check if docker is available
if ! command -v docker &>/dev/null; then
    echo -e "${RED}Error: docker not found. Install Docker first.${NC}" >&2
    exit 1
fi

docker compose up -d

if [ $? -eq 0 ]; then
    echo -e "${GREEN}  ✓ Docker containers started${NC}"
else
    echo -e "${RED}  ✗ Docker failed to start. Check docker-compose.yml${NC}" >&2
    exit 1
fi

# ── Phase 2: CNN Model Service ──────────────────────────────
echo ""
echo -e "${YELLOW}[2/2] Starting CNN AI Detection Model...${NC}"
echo "       service → http://127.0.0.1:7070"
echo "       health  → http://127.0.0.1:7070/health"
echo "       model   → huggingface (BEiT-Large) / custom CNN fallback"
echo ""

CNN_DIR="$PROJECT_DIR/cnn"

if [ ! -d "$CNN_DIR/venv" ]; then
    echo -e "${YELLOW}  ⚠ CNN virtual env not found. Running setup first...${NC}"
    (cd "$CNN_DIR" && bash scripts/setup.sh)
fi

# Start the CNN service in foreground
echo -e "${GREEN}  ✓ Starting CNN service (Ctrl+C to stop all)...${NC}"
echo ""
echo -e "${CYAN}  ─────────────────────────────────────────────${NC}"
echo -e "${CYAN}  App:      http://localhost:6767${NC}"
echo -e "${CYAN}  Admin:    http://localhost:6767/admin${NC}"
echo -e "${CYAN}  AI API:   http://127.0.0.1:7070/docs${NC}"
echo -e "${CYAN}  ─────────────────────────────────────────────${NC}"
echo ""

cd "$CNN_DIR"
source venv/bin/activate
exec python app.py
