# Quick Start Guide - Interactive Mode

## 🚀 In 60 Seconds

```bash
# 1. Navigate to automation directory
cd .local/automation

# 2. Run orchestrator with your prompt
./run.sh --input=example_prompt.txt

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

| Step | Creates | You Do | Time |
|------|---------|--------|------|
| **1. PLAN** | `plan.md` | Copy prompt → Paste in Cursor | 5-10 min |
| **2. QUALITY** | `checklist.md` | Copy prompt → Paste in Cursor | 3-5 min |
| **3. DECOMPOSITION** | `tasks/*.md` | Copy prompt → Paste in Cursor | 5-10 min |
| **4. STANDARDIZATION** | `INDEX.md`, etc. | Copy prompt → Paste in Cursor | 3-5 min |

## 💡 Key Points

✅ **Interactive** - You trigger Cursor agent at each step  
✅ **Automatic detection** - Script monitors and continues when ready  
✅ **Retry logic** - Up to 3 attempts per step  
✅ **Full logging** - Everything saved for debugging  

## 🎯 What You'll See

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
# Basic usage
./run.sh --input=prompt.txt

# Custom output location
./run.sh --input=prompt.txt --output=/path/to/output/

# Test without execution
./run.sh --input=prompt.txt --dry-run

# Get help
./run.sh --help
```

## 📚 More Information

- 📖 **Full documentation**: [USAGE_WITH_CURSOR.md](./USAGE_WITH_CURSOR.md)
- 📊 **Visual workflow**: [WORKFLOW_DIAGRAM.md](./WORKFLOW_DIAGRAM.md)
- 📝 **What changed**: [CHANGELOG_INTERACTIVE.md](./CHANGELOG_INTERACTIVE.md)
- 📋 **Main README**: [README.md](./README.md)

## ⚠️ Important Notes

1. **English Only** - All content must be in English (project policy)
2. **Cursor Required** - Must have Cursor IDE installed and running
3. **Interactive** - Not fully automated, requires your input at each step
4. **Directory** - Ensure correct output directory before starting
5. **Files** - Don't delete files between steps

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| Timeout | Check if files created with different names |
| Wrong directory | Verify output path in Cursor Composer |
| Missing files | Run Cursor agent again with same prompt |
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

1. Keep **both terminal and Cursor IDE visible** side-by-side
2. **Read the prompt first** to understand what will be created
3. **Let Cursor finish** before checking files manually
4. **Check the log** if something seems wrong: `orchestrator.log`
5. **Prompts are saved** - you can reuse them if needed

---

**Ready? Run `./run.sh --input=example_prompt.txt` to start!**

