# Quick Start Guide

## 🚀 Two Options: Pick Your Mode

### Option A: Automated Mode (Fastest! 🤖)

```bash
# 1. Install Cursor CLI (one-time setup)
./run.sh --install-cli

# 2. Set API key
export CURSOR_API_KEY="your-api-key"

# 3. Run - completely automatic!
./run.sh --input=example_prompt.txt --automated

# 4. Done! No manual steps needed.
```

### Option B: Interactive Mode (Manual 👤)

```bash
# 1. Navigate to automation directory
cd .local/automation

# 2. Run orchestrator with your prompt
./run.sh --input=example_prompt.txt --interactive

# 3. When script pauses, for each step:
#    → Open the displayed prompt file
#    → Copy ALL content
#    → Press Cmd+I (or Ctrl+I) in Cursor
#    → Paste and submit
#    → Wait for files to appear
#    → Script continues automatically

# 4. Done! Check output directory for results
```

## 📋 The 4 Steps

### Automated Mode 🤖
| Step | Creates | Agent Does | Time |
|------|---------|------------|------|
| **1. PLAN** | `plan.md` | Automatic via cursor-agent | 2-5 min |
| **2. QUALITY** | `checklist.md` | Automatic via cursor-agent | 1-3 min |
| **3. DECOMPOSITION** | `tasks/*.md` | Automatic via cursor-agent | 3-7 min |
| **4. STANDARDIZATION** | `INDEX.md`, etc. | Automatic via cursor-agent | 1-3 min |

**Total**: ~10-20 minutes (fully automatic)

### Interactive Mode 👤
| Step | Creates | You Do | Time |
|------|---------|--------|------|
| **1. PLAN** | `plan.md` | Copy prompt → Paste in Cursor | 5-10 min |
| **2. QUALITY** | `checklist.md` | Copy prompt → Paste in Cursor | 3-5 min |
| **3. DECOMPOSITION** | `tasks/*.md` | Copy prompt → Paste in Cursor | 5-10 min |
| **4. STANDARDIZATION** | `INDEX.md`, etc. | Copy prompt → Paste in Cursor | 3-5 min |

**Total**: ~20-30 minutes (with manual steps)

## 💡 Key Points

✅ **Two Modes** - Automated (fully automatic) or Interactive (manual)  
✅ **Smart Detection** - Auto-selects best mode if no flag specified  
✅ **Automatic Validation** - Script verifies files at each step  
✅ **Retry Logic** - Up to 3 attempts per step  
✅ **Full Logging** - Everything saved for debugging  
✅ **CLI Installation** - Built-in installer for cursor-agent  

## 🎯 What You'll See

### Automated Mode 🤖
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
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🤖 Executing Cursor Agent (Automated)
🚀 Executing cursor-agent...
⏱️  Agent execution took 45s

✅ All expected files created successfully!
✅ Step 1 completed in 47.2s (attempt 1)
```

### Interactive Mode 👤
```
╔══════════════════════════════════════════════════════════════════╗
║  ACTION REQUIRED: Use Cursor Agent                              ║
╚══════════════════════════════════════════════════════════════════╝

📋 Step 1: PLAN

🎯 Instructions:
   1. Open Cursor IDE (if not already open)
   2. Open the prompt file: step_1_prompt.txt
   3. Copy the ENTIRE prompt content
   4. Open Cursor Composer (Cmd+I or Ctrl+I)
   5. Paste the prompt and press Enter
   6. Wait for Cursor agent to complete the task
   7. Verify all required files are created

⏸️  Paused - Waiting for Cursor agent to complete the task

Expected files:
  - plan.md

Monitoring for file creation (timeout: 300s)
```

## 🔧 Command Options

```bash
# Automated mode (fully automatic)
./run.sh --input=prompt.txt --automated

# Interactive mode (manual steps)
./run.sh --input=prompt.txt --interactive

# Auto-detect (smart mode selection)
./run.sh --input=prompt.txt

# Install Cursor CLI
./run.sh --install-cli

# Custom output location
./run.sh --input=prompt.txt --output=/path/to/output/ --automated

# Test without execution
./run.sh --input=prompt.txt --dry-run

# Get help
./run.sh --help
```

## 📚 More Information

- 🤖 **Automated mode guide**: [USAGE_AUTOMATED.md](./USAGE_AUTOMATED.md) ⭐ **NEW!**
- 👤 **Interactive mode guide**: [USAGE_WITH_CURSOR.md](./USAGE_WITH_CURSOR.md)
- 📊 **Visual workflow**: [WORKFLOW_DIAGRAM.md](./WORKFLOW_DIAGRAM.md)
- 📋 **Main README**: [README.md](./README.md)
- 📝 **Changelog**: [CHANGELOG_AUTOMATED.md](./CHANGELOG_AUTOMATED.md)

## ⚠️ Important Notes

1. **English Only** - All content must be in English (project policy)
2. **Automated Mode** - Requires cursor-agent CLI + CURSOR_API_KEY
3. **Interactive Mode** - Requires Cursor IDE (manual interaction)
4. **Directory** - Ensure correct output directory before starting
5. **Files** - Don't delete files between steps
6. **API Key** - Keep your CURSOR_API_KEY secure (don't commit to git)

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| cursor-agent not found | Run: `./run.sh --install-cli` |
| CURSOR_API_KEY not set | Run: `export CURSOR_API_KEY="your-key"` |
| Timeout | Check if files created with different names |
| Wrong directory | Verify output path |
| Missing files | Check logs in orchestrator.log |
| Validation fails | Check file size and content requirements |

## ✅ Success Looks Like

```
✅ Step 1 completed in 45.2s (attempt 1)
✅ Step 2 completed in 32.1s (attempt 1)
✅ Step 3 completed in 67.8s (attempt 1)
✅ Step 4 completed in 28.4s (attempt 1)
✅ All steps completed successfully!
⏱️  Total time: 173.7 seconds

📄 Report generated: orchestrator_report.md
```

## 🎓 Pro Tips

### For Automated Mode 🤖
1. **Set API key permanently** - Add to ~/.bashrc or ~/.zshrc
2. **Use detailed prompts** - Better prompts = better results
3. **Check logs** - orchestrator.log has full execution details
4. **Run in background** - For long operations: `nohup ./run.sh ... &`
5. **CI/CD ready** - Perfect for automation pipelines

### For Interactive Mode 👤
1. Keep **both terminal and Cursor IDE visible** side-by-side
2. **Read the prompt first** to understand what will be created
3. **Let Cursor finish** before checking files manually
4. **Check the log** if something seems wrong: `orchestrator.log`
5. **Prompts are saved** - you can reuse them if needed

---

**Ready?**
- **Automated**: `./run.sh --input=example_prompt.txt --automated`
- **Interactive**: `./run.sh --input=example_prompt.txt --interactive`

