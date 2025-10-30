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
Test Plan Automation - Interactive Launcher with Cursor Agent

🎯 INTERACTIVE MODE: This script works WITH Cursor IDE agent
   You will be guided step-by-step to use Cursor Composer for each task

Usage:
  ./run.sh [OPTIONS]
  ./run.sh --input=prompt.txt
  ./run.sh --input=prompt.txt --output=../test-output/

Options:
  --input=FILE         Path to prompt file (required)
  --output=DIR         Output directory (default: ../)
  --dry-run            Show what would be executed without running
  --help               Show this help

Examples:
  # Basic usage (RECOMMENDED: read USAGE_WITH_CURSOR.md first)
  ./run.sh --input=example_prompt.txt

  # Custom output directory
  ./run.sh --input=my_prompt.txt --output=/tmp/test-plan/

  # Dry run
  ./run.sh --input=prompt.txt --dry-run

How It Works:
  1. Script generates detailed prompt for each step
  2. You copy prompt and paste into Cursor Composer (Cmd+I)
  3. Cursor agent creates the required files
  4. Script detects completion and proceeds automatically
  5. Repeat for all 4 steps

📖 Full Documentation: ./USAGE_WITH_CURSOR.md

EOF
}

# Parse arguments
INPUT_FILE=""
OUTPUT_DIR="$DEFAULT_OUTPUT_DIR"
DRY_RUN=false

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

# Show configuration
echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║   Test Plan Automation Launcher (Interactive Mode)    ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
log_warning "INTERACTIVE MODE: You will need to use Cursor Composer at each step"
log_info "Read USAGE_WITH_CURSOR.md for detailed instructions"
echo ""
log_info "Configuration:"
echo "  Input file:    $INPUT_FILE"
echo "  Output dir:    $OUTPUT_DIR"
echo "  Orchestrator:  $ORCHESTRATOR"
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
    --output-dir="$OUTPUT_DIR"

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

