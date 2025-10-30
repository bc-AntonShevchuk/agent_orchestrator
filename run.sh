#!/bin/bash

##
# Wrapper script for Test Plan Automation Orchestrator
# Simplifies orchestrator launching
##

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ORCHESTRATOR="${SCRIPT_DIR}/orchestrator.php"
DEFAULT_OUTPUT_DIR="${SCRIPT_DIR}/../"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper functions
log_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

log_success() {
    echo -e "${GREEN}✓${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

log_error() {
    echo -e "${RED}✗${NC} $1"
}

show_help() {
    cat << EOF
Test Plan Automation - Cursor Agent Launcher

🎯 OPERATION MODES:
   🤖 AUTOMATED: Fully automatic via cursor-agent CLI
   👤 INTERACTIVE: Manual workflow with Cursor Composer

Usage:
  ./run.sh [OPTIONS]
  ./run.sh --input=prompt.txt
  ./run.sh --input=prompt.txt --automated

Options:
  --input=FILE         Path to prompt file (required)
  --output=DIR         Output directory (default: ../)
  --automated, --auto  Use automated mode (requires cursor-agent CLI)
  --interactive        Force interactive mode
  --install-cli        Install Cursor CLI for automated mode
  --dry-run            Show what would be executed without running
  --help               Show this help

Examples:
  # Automated mode (fully automatic - RECOMMENDED)
  export CURSOR_API_KEY="your-api-key"
  ./run.sh --input=example_prompt.txt --automated

  # Interactive mode (manual copy-paste)
  ./run.sh --input=example_prompt.txt --interactive

  # Auto-detect mode (uses automated if available)
  ./run.sh --input=example_prompt.txt

  # Install Cursor CLI
  ./run.sh --install-cli

  # Custom output directory
  ./run.sh --input=my_prompt.txt --output=/tmp/test-plan/ --automated

Automated Mode Setup:
  1. Install Cursor CLI:
     ./run.sh --install-cli
     OR
     curl https://cursor.com/install -fsS | bash
     
  2. Get API key from Cursor dashboard
  
  3. Set environment variable:
     export CURSOR_API_KEY="your-api-key-here"
     
  4. Run with --automated flag

Interactive Mode Workflow:
  1. Script generates detailed prompt for each step
  2. You copy prompt and paste into Cursor Composer (Cmd+I)
  3. Cursor agent creates the required files
  4. Script detects completion and proceeds automatically
  5. Repeat for all 4 steps

📖 Documentation:
   - ./USAGE_WITH_CURSOR.md - Interactive mode guide
   - ./USAGE_AUTOMATED.md - Automated mode guide
   - ./README.md - Complete reference

EOF
}

# Parse arguments
INPUT_FILE=""
OUTPUT_DIR="$DEFAULT_OUTPUT_DIR"
DRY_RUN=false
AUTOMATED=false
INTERACTIVE=false
INSTALL_CLI=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --input=*)
            INPUT_FILE="${1#*=}"
            shift
            ;;
        --output=*)
            OUTPUT_DIR="${1#*=}"
            shift
            ;;
        --automated|--auto)
            AUTOMATED=true
            shift
            ;;
        --interactive)
            INTERACTIVE=true
            shift
            ;;
        --install-cli)
            INSTALL_CLI=true
            shift
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --help|-h)
            show_help
            exit 0
            ;;
        *)
            log_error "Unknown option: $1"
            show_help
            exit 1
            ;;
    esac
done

# Handle CLI installation
if [ "$INSTALL_CLI" = true ]; then
    log_info "Installing Cursor CLI..."
    echo ""
    if curl https://cursor.com/install -fsS | bash; then
        log_success "Cursor CLI installed successfully!"
        echo ""
        log_info "Next steps:"
        echo "  1. Restart your terminal or run: source ~/.bashrc (or ~/.zshrc)"
        echo "  2. Get your API key from Cursor dashboard"
        echo "  3. Set environment variable: export CURSOR_API_KEY='your-key'"
        echo "  4. Run: ./run.sh --input=prompt.txt --automated"
    else
        log_error "Failed to install Cursor CLI"
        exit 1
    fi
    exit 0
fi

# Validation
if [ -z "$INPUT_FILE" ]; then
    log_error "Missing required parameter: --input"
    echo ""
    show_help
    exit 1
fi

if [ ! -f "$INPUT_FILE" ]; then
    log_error "Input file not found: $INPUT_FILE"
    exit 1
fi

if [ ! -f "$ORCHESTRATOR" ]; then
    log_error "Orchestrator script not found: $ORCHESTRATOR"
    exit 1
fi

# Check PHP
if ! command -v php &> /dev/null; then
    log_error "PHP is not installed or not in PATH"
    exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
log_info "PHP version: $PHP_VERSION"

# Check operation mode
MODE_FLAG=""
MODE_NAME="Auto-Detect"
MODE_ICON="🔍"

if [ "$AUTOMATED" = true ]; then
    # Check for cursor-agent
    if ! command -v cursor-agent &> /dev/null; then
        log_error "cursor-agent CLI not found!"
        echo ""
        log_info "To install Cursor CLI, run:"
        echo "  ./run.sh --install-cli"
        echo ""
        exit 1
    fi
    
    # Check for API key
    if [ -z "$CURSOR_API_KEY" ]; then
        log_error "CURSOR_API_KEY environment variable not set!"
        echo ""
        log_info "To set API key:"
        echo "  export CURSOR_API_KEY='your-api-key-here'"
        echo ""
        exit 1
    fi
    
    MODE_FLAG="--automated"
    MODE_NAME="Automated (cursor-agent CLI)"
    MODE_ICON="🤖"
    log_success "cursor-agent CLI detected"
    log_success "CURSOR_API_KEY configured"
elif [ "$INTERACTIVE" = true ]; then
    MODE_FLAG="--interactive"
    MODE_NAME="Interactive (Manual)"
    MODE_ICON="👤"
else
    # Auto-detect
    if command -v cursor-agent &> /dev/null && [ -n "$CURSOR_API_KEY" ]; then
        MODE_FLAG="--automated"
        MODE_NAME="Auto-Detected: Automated"
        MODE_ICON="🤖"
        log_info "cursor-agent detected, using automated mode"
    else
        MODE_FLAG="--interactive"
        MODE_NAME="Auto-Detected: Interactive"
        MODE_ICON="👤"
        log_info "cursor-agent not available, using interactive mode"
    fi
fi

# Show configuration
echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║   Test Plan Automation Launcher                       ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
log_info "Operation Mode: ${MODE_ICON} ${MODE_NAME}"
if [ "$MODE_NAME" = "Interactive (Manual)" ] || [[ "$MODE_NAME" == *"Interactive"* ]]; then
    log_warning "INTERACTIVE MODE: You will use Cursor Composer at each step"
    log_info "Read USAGE_WITH_CURSOR.md for instructions"
else
    log_success "AUTOMATED MODE: Fully automatic execution"
    log_info "Read USAGE_AUTOMATED.md for details"
fi
echo ""
log_info "Configuration:"
echo "  Input file:    $INPUT_FILE"
echo "  Output dir:    $OUTPUT_DIR"
echo "  Orchestrator:  $ORCHESTRATOR"
echo "  Mode:          $MODE_NAME"
echo "  Dry run:       $DRY_RUN"
echo ""

# Dry run mode
if [ "$DRY_RUN" = true ]; then
    log_warning "DRY RUN MODE - No actual execution"
    echo ""
    echo "Would execute:"
    echo "  php $ORCHESTRATOR \\"
    echo "    --input=\"$INPUT_FILE\" \\"
    echo "    --output-dir=\"$OUTPUT_DIR\""
    echo ""
    exit 0
fi

# Create output directory if needed
if [ ! -d "$OUTPUT_DIR" ]; then
    log_info "Creating output directory: $OUTPUT_DIR"
    mkdir -p "$OUTPUT_DIR"
fi

# Execute orchestrator
log_info "Starting orchestration..."
echo ""

php "$ORCHESTRATOR" \
    --input="$INPUT_FILE" \
    --output-dir="$OUTPUT_DIR" \
    $MODE_FLAG

RESULT=$?

echo ""
if [ $RESULT -eq 0 ]; then
    log_success "Orchestration completed successfully!"
    echo ""
    log_info "Output files:"
    ls -lh "$OUTPUT_DIR"*.md 2>/dev/null || true
    ls -lh "$OUTPUT_DIR"tasks/*.md 2>/dev/null || true
    echo ""
    log_info "Check the report: ${OUTPUT_DIR}orchestrator_report.md"
    log_info "Check the log: ${OUTPUT_DIR}orchestrator.log"
else
    log_error "Orchestration failed with exit code: $RESULT"
    log_info "Check the log for details: ${OUTPUT_DIR}orchestrator.log"
fi

exit $RESULT

