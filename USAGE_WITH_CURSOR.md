# Using Test Plan Orchestrator with Cursor Agent

## Overview

This orchestrator works **interactively** with Cursor IDE agent. It's a semi-automated workflow where:
1. The script generates detailed prompts for each step
2. You execute those prompts in Cursor Composer
3. The script monitors for completion and proceeds automatically

## Prerequisites

- Cursor IDE installed and running
- PHP 7.4+ installed
- Access to this codebase in Cursor

## Quick Start

### Step 1: Prepare Your Prompt

Create a prompt file describing what you want to test:

```bash
# Create prompt file
cat > .local/automation/my_prompt.txt << 'EOF'
Create comprehensive unit tests for the class:
Bigcommerce\Modules\Checkout\Services\PaymentService

Include all public methods, mocking, and edge cases.
EOF
```

### Step 2: Run the Orchestrator

```bash
cd .local/automation
./run.sh --input=my_prompt.txt --output=../test-plan/
```

### Step 3: Interactive Workflow

The orchestrator will guide you through 4 steps:

#### For Each Step:

1. **Script pauses** and displays instructions
2. **Open the prompt file** (e.g., `step_1_prompt.txt`)
3. **Copy the entire content**
4. **Open Cursor Composer** (Cmd+I / Ctrl+I)
5. **Paste and submit** the prompt
6. **Wait for Cursor agent** to create the files
7. **Script auto-detects** completion and continues

## The 4 Steps

### Step 1: PLAN
- Creates: `plan.md`
- Detailed specifications for all test methods
- ~1000+ lines of comprehensive planning

### Step 2: QUALITY
- Creates: `checklist.md`
- Quality checklist for test writing
- ~500+ lines of guidelines

### Step 3: DECOMPOSITION
- Creates: `tasks/task_00.md`, `task_01.md`, etc.
- Atomic task files (minimum 5)
- Each task is independent and executable

### Step 4: STANDARDIZATION
- Creates: `INDEX.md`, `QUICKSTART.md`, `tasks/README.md`
- Navigation structure
- Cross-references between all files

## Example Workflow

```bash
# Terminal 1: Run orchestrator
cd .local/automation
./run.sh --input=example_prompt.txt

# When script pauses:
# 1. Note the prompt file path shown
# 2. Open that file in Cursor
# 3. Copy the content
# 4. Open Cursor Composer (Cmd+I)
# 5. Paste and submit

# Script will automatically detect when files are created
# and proceed to the next step
```

## Monitoring Progress

The script shows:
- ✅ Files created
- ⏳ Time remaining
- 📊 Progress (X/Y files ready)
- Every 10 seconds: status update

## Timeout

- Default: 300 seconds (5 minutes) per step
- If Cursor agent is slow, files will be detected automatically
- You can increase timeout in `orchestrator.php`:
  ```php
  private const TIMEOUT_SECONDS = 600; // 10 minutes
  ```

## Troubleshooting

### "Timeout waiting for agent"
- Cursor agent didn't create expected files
- Check if files were created with wrong names
- Check the output directory matches expectations

### "File not found"
- Ensure Cursor is working in the correct directory
- Check that file paths in Cursor composer are correct
- Verify output directory setting

### Script exits early
- Check `orchestrator.log` for details
- Verify all required files were created
- Ensure files meet minimum size requirements

## Tips for Best Results

1. **Keep Cursor IDE open** during the entire process
2. **Use Cursor Composer** (not Chat) for better file creation
3. **Wait for completion** before proceeding manually
4. **Check logs** if something fails: `orchestrator.log`
5. **Review generated files** before continuing to next step

## File Structure After Completion

```
output-directory/
├── plan.md                      # Detailed test plan
├── checklist.md                 # Quality checklist
├── INDEX.md                     # Main navigation
├── QUICKSTART.md               # Quick start guide
├── tasks/
│   ├── README.md               # Task overview
│   ├── task_00.md              # Task 1
│   ├── task_01.md              # Task 2
│   └── ...                     # More tasks
├── step_1_prompt.txt           # Saved prompts (for reference)
├── step_2_prompt.txt
├── step_3_prompt.txt
├── step_4_prompt.txt
├── orchestrator.log            # Execution log
└── orchestrator_report.md      # Final report
```

## Advanced Usage

### Custom Output Directory

```bash
./run.sh --input=prompt.txt --output=/custom/path/
```

### Dry Run (see what would execute)

```bash
./run.sh --input=prompt.txt --dry-run
```

### Resume from Failure

If a step fails:
1. Check which files are missing
2. Manually create them using Cursor
3. Re-run the script (it will validate and continue)

## Benefits of This Approach

✅ **Semi-automated**: Combines automation with human oversight  
✅ **Quality control**: You review each step before proceeding  
✅ **Flexible**: Works with Cursor's actual capabilities  
✅ **Traceable**: All prompts and outputs are saved  
✅ **Reliable**: File-based detection is simple and robust  

## Notes

- This is NOT fully automated (Cursor has no public API)
- Human intervention required at each step
- Designed for quality over speed
- Perfect for important test planning work

