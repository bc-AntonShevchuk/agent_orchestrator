# Using Test Plan Orchestrator - Automated Mode

## Overview

The orchestrator now supports **fully automated execution** using Cursor's agent CLI. No manual intervention required - the entire 4-step process runs automatically!

## 🚀 Quick Start

### 1. Install Cursor CLI

```bash
# Option A: Use built-in installer
./run.sh --install-cli

# Option B: Install directly
curl https://cursor.com/install -fsS | bash
```

After installation, restart your terminal or run:
```bash
source ~/.bashrc  # or ~/.zshrc
```

### 2. Get API Key

1. Open Cursor IDE
2. Go to Settings → Account → API Keys
3. Generate a new API key
4. Copy the key

### 3. Set Environment Variable

```bash
# Set for current session
export CURSOR_API_KEY="your-api-key-here"

# Or add to your shell profile for persistence
echo 'export CURSOR_API_KEY="your-api-key-here"' >> ~/.bashrc  # or ~/.zshrc
source ~/.bashrc  # or ~/.zshrc
```

### 4. Run Automated Mode

```bash
cd .local/automation
./run.sh --input=example_prompt.txt --automated
```

That's it! The orchestrator will:
- Generate prompts for all 4 steps
- Execute cursor-agent CLI automatically
- Create all required files
- Validate results
- Generate final report

## 🎯 Operation Modes

### Automated Mode (🤖)
- **Requires**: `cursor-agent` CLI + `CURSOR_API_KEY`
- **Usage**: `./run.sh --input=prompt.txt --automated`
- **Behavior**: Fully automatic, no manual intervention
- **Speed**: Fastest execution
- **Best for**: Batch processing, CI/CD, automation

### Interactive Mode (👤)
- **Requires**: Cursor IDE (manual interaction)
- **Usage**: `./run.sh --input=prompt.txt --interactive`
- **Behavior**: Manual copy-paste to Cursor Composer
- **Best for**: Step-by-step review, learning, customization

### Auto-Detect Mode (🔍)
- **Usage**: `./run.sh --input=prompt.txt` (no mode flag)
- **Behavior**: Automatically uses automated mode if available, falls back to interactive
- **Best for**: Flexibility, compatibility

## 📋 Command Reference

### Basic Commands

```bash
# Automated mode
./run.sh --input=prompt.txt --automated

# Interactive mode
./run.sh --input=prompt.txt --interactive

# Auto-detect (smart mode)
./run.sh --input=prompt.txt

# Custom output directory
./run.sh --input=prompt.txt --output=/tmp/test-plan/ --automated

# Dry run (preview)
./run.sh --input=prompt.txt --automated --dry-run

# Install Cursor CLI
./run.sh --install-cli

# Help
./run.sh --help
```

### Direct PHP Execution

```bash
# Automated mode
export CURSOR_API_KEY="your-key"
php orchestrator.php --input=prompt.txt --automated

# Interactive mode
php orchestrator.php --input=prompt.txt --interactive

# Auto-detect
php orchestrator.php --input=prompt.txt
```

## 🔧 Configuration

### Environment Variables

```bash
# Required for automated mode
export CURSOR_API_KEY="your-api-key"

# Optional: Specify model (default: claude-sonnet-4.5)
# Note: Model selection is configured in orchestrator.php
```

### Customizing Model

Edit `orchestrator.php` to change the AI model:

```php
private string $cursorModel = 'claude-sonnet-4.5'; // Change this
```

Available models (check Cursor documentation for current list):
- `claude-sonnet-4.5`
- `gpt-4o`
- `gpt-5`

## 📊 What Happens in Automated Mode

### Execution Flow

```
┌─────────────────────────────────────────┐
│  START: ./run.sh --automated            │
└────────────────┬────────────────────────┘
                 │
                 ▼
    ┌────────────────────────┐
    │  Detect cursor-agent   │
    │  Check CURSOR_API_KEY  │
    └────────────┬───────────┘
                 │
                 ▼
    ╔════════════════════════╗
    ║   STEP 1: PLAN        ║
    ╚════════════════════════╝
                 │
    ┌────────────▼───────────┐
    │ Generate prompt        │
    │ Execute: cursor-agent  │
    │ Verify: plan.md        │
    │ Validate content       │
    └────────────┬───────────┘
                 │
                 ▼
    ╔════════════════════════╗
    ║   STEP 2: QUALITY     ║
    ╚════════════════════════╝
                 │
    ┌────────────▼───────────┐
    │ Execute: cursor-agent  │
    │ Verify: checklist.md   │
    │ Validate content       │
    └────────────┬───────────┘
                 │
                 ▼
    ╔════════════════════════╗
    ║   STEP 3: DECOMPOSE   ║
    ╚════════════════════════╝
                 │
    ┌────────────▼───────────┐
    │ Execute: cursor-agent  │
    │ Verify: tasks/*.md     │
    │ Validate structure     │
    └────────────┬───────────┘
                 │
                 ▼
    ╔════════════════════════╗
    ║   STEP 4: STANDARDIZE ║
    ╚════════════════════════╝
                 │
    ┌────────────▼───────────┐
    │ Execute: cursor-agent  │
    │ Verify: INDEX.md, etc  │
    │ Validate structure     │
    └────────────┬───────────┘
                 │
                 ▼
    ┌────────────────────────┐
    │  Generate Report       │
    │  orchestrator_report.md│
    └────────────┬───────────┘
                 │
                 ▼
    ╔════════════════════════╗
    ║     ✅ SUCCESS         ║
    ╚════════════════════════╝
```

### Console Output Example

```
╔════════════════════════════════════════════════════════╗
║   Test Plan Automation Orchestrator                   ║
║   🤖 Automated Mode                                    ║
╚════════════════════════════════════════════════════════╝

🎯 Operation mode: 🤖 AUTOMATED
✅ cursor-agent CLI detected
✅ CURSOR_API_KEY configured
🤖 Model: claude-sonnet-4.5

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📋 Step 1/4: PLAN
📝 Create detailed specifications
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🤖 Invoking Cursor agent via CLI (Automated Mode)
🚀 Executing cursor-agent...
⏱️  Agent execution took 45s

🔍 Verifying file creation...
   ✓ plan.md

✅ All expected files created successfully!
✅ Step 1 completed in 47.2s (attempt 1)

[... Steps 2-4 ...]

✅ All steps completed successfully!
⏱️  Total time: 173.7 seconds

📄 Report generated: orchestrator_report.md
```

## 🔍 Monitoring and Logging

### Log Files

All execution is logged to:
- **Console**: Real-time output with emojis
- **orchestrator.log**: Detailed log file with timestamps

```bash
# Watch logs in real-time
tail -f /path/to/output/orchestrator.log

# Search for errors
grep ERROR /path/to/output/orchestrator.log

# View agent output
grep "Agent output" /path/to/output/orchestrator.log -A 10
```

### Generated Files

After successful execution:

```
output-directory/
├── plan.md                      # Step 1 output
├── checklist.md                 # Step 2 output
├── tasks/                       # Step 3 outputs
│   ├── README.md
│   ├── task_00.md
│   ├── task_01.md
│   └── ...
├── INDEX.md                     # Step 4 outputs
├── QUICKSTART.md                #
├── step_1_prompt.txt           # Generated prompts
├── step_2_prompt.txt
├── step_3_prompt.txt
├── step_4_prompt.txt
├── step_1_full_prompt.txt      # Full prompts with context (automated mode only)
├── step_2_full_prompt.txt
├── step_3_full_prompt.txt
├── step_4_full_prompt.txt
├── orchestrator.log            # Execution log
└── orchestrator_report.md      # Final summary
```

## ⚠️ Troubleshooting

### cursor-agent not found

```bash
Error: cursor-agent CLI not found!

To install Cursor CLI, run:
  ./run.sh --install-cli
```

**Solution**: Install Cursor CLI using `./run.sh --install-cli` or manually

### CURSOR_API_KEY not set

```bash
Error: CURSOR_API_KEY environment variable not set!

To set API key:
  export CURSOR_API_KEY='your-api-key-here'
```

**Solution**: Set the environment variable with your API key

### cursor-agent failed with exit code

```bash
❌ cursor-agent failed with exit code 1
```

**Solutions**:
1. Check API key is valid
2. Verify network connection
3. Check cursor-agent logs
4. Try with a simpler prompt first

### Files not created

```bash
❌ Not all expected files were created. Missing:
   ✗ plan.md
```

**Solutions**:
1. Check agent output in logs
2. Verify output directory is writable
3. Check disk space
4. Try running the step manually

### Validation failed

```bash
❌ File too small: 234 < 1000 lines
```

**Solutions**:
1. Review the generated file
2. Check if prompt is detailed enough
3. Try different model or adjust prompt
4. Re-run with retry logic (automatic)

## 🎓 Advanced Usage

### Batch Processing

```bash
#!/bin/bash
# Process multiple prompts

for prompt in prompts/*.txt; do
    echo "Processing: $prompt"
    ./run.sh --input="$prompt" \
             --output="output/$(basename $prompt .txt)/" \
             --automated
done
```

### CI/CD Integration

```yaml
# .github/workflows/test-plan.yml
name: Generate Test Plan

on:
  workflow_dispatch:
    inputs:
      prompt_file:
        description: 'Prompt file path'
        required: true

jobs:
  generate:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Install Cursor CLI
        run: curl https://cursor.com/install -fsS | bash
      
      - name: Generate Test Plan
        env:
          CURSOR_API_KEY: ${{ secrets.CURSOR_API_KEY }}
        run: |
          cd .local/automation
          ./run.sh --input="${{ github.event.inputs.prompt_file }}" --automated
      
      - name: Upload Results
        uses: actions/upload-artifact@v3
        with:
          name: test-plan
          path: .local/*.md
```

### Custom Scripts

```bash
#!/bin/bash
# custom-test-plan.sh

# Set API key from vault/secrets manager
export CURSOR_API_KEY=$(vault read -field=api_key secret/cursor)

# Run orchestrator
cd .local/automation
./run.sh \
    --input=custom_prompt.txt \
    --output=/var/test-plans/$(date +%Y%m%d)/ \
    --automated

# Post-process results
if [ $? -eq 0 ]; then
    echo "Success! Test plan generated."
    # Send notification, commit to git, etc.
fi
```

## 📈 Performance Tips

1. **Use SSD storage**: File I/O is intensive during generation
2. **Stable network**: Required for cursor-agent API calls
3. **Detailed prompts**: Better prompts = better results = fewer retries
4. **Appropriate timeout**: Adjust `TIMEOUT_SECONDS` in orchestrator.php if needed

## 🔐 Security Considerations

1. **API Key Protection**
   - Never commit API keys to git
   - Use environment variables
   - Consider secrets managers (AWS Secrets, HashiCorp Vault)

2. **File Permissions**
   - Generated files may contain sensitive info
   - Set appropriate permissions on output directory

3. **Network Security**
   - cursor-agent communicates with Cursor API
   - Ensure secure network connection
   - Consider firewall rules for production

## 📚 See Also

- [USAGE_WITH_CURSOR.md](./USAGE_WITH_CURSOR.md) - Interactive mode guide
- [README.md](./README.md) - Complete documentation
- [WORKFLOW_DIAGRAM.md](./WORKFLOW_DIAGRAM.md) - Visual workflow
- [QUICK_START.md](./QUICK_START.md) - Quick reference

## 🎯 Benefits of Automated Mode

✅ **Fully Automatic** - No manual steps  
✅ **Consistent** - Same quality every time  
✅ **Fast** - No waiting for human interaction  
✅ **Scalable** - Process multiple prompts in parallel  
✅ **CI/CD Ready** - Integrate into pipelines  
✅ **Reproducible** - Same input = same output  
✅ **Traceable** - Complete logs and audit trail  

---

**Version**: 2.0.0  
**Last Updated**: 2025-10-30  
**Status**: ✅ Production Ready

