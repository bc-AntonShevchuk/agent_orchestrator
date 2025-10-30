# Quick Reference - Test Plan Automation

## ⚡ Quick Start

### 1-Minute Launch

```bash
cd .local/automation
./run.sh --input=example_prompt.txt
```

That's it! The script will do the rest.

---

## 📝 Basic Commands

### Run with example
```bash
./run.sh --input=example_prompt.txt
```

### Run with your own prompt
```bash
./run.sh --input=my_prompt.txt
```

### Run with custom output directory
```bash
./run.sh --input=prompt.txt --output=/tmp/output/
```

### Dry run (see what will be executed)
```bash
./run.sh --input=prompt.txt --dry-run
```

### Show help
```bash
./run.sh --help
```

---

## 🎯 What Happens

### 4 Automatic Steps:

```
Step 1: PLAN
├─ Creates: plan.md
├─ Size: ~1000+ lines
└─ Time: 2-5 min

Step 2: QUALITY
├─ Creates: checklist.md
├─ Size: ~500+ lines
└─ Time: 1-3 min

Step 3: DECOMPOSITION
├─ Creates: tasks/task_00.md, task_01.md, ...
├─ Minimum: 5+ tasks
└─ Time: 3-10 min

Step 4: STANDARDIZATION
├─ Creates: INDEX.md, QUICKSTART.md, tasks/README.md
├─ Validates: correct naming
└─ Time: 2-5 min
```

**Total**: ~10-25 minutes

---

## 📁 What Gets Created

After successful execution:

```
../
├── plan.md                # Detailed plan
├── checklist.md           # Checklist
├── INDEX.md               # Navigation
├── QUICKSTART.md          # Quick start
├── orchestrator.log       # Log
├── orchestrator_report.md # Report
└── tasks/
    ├── README.md
    ├── task_00.md
    ├── task_01.md
    └── ...
```

---

## 🔍 Checking Results

### Watch log in real-time
```bash
tail -f ../orchestrator.log
```

### View report
```bash
cat ../orchestrator_report.md
```

### Check created files
```bash
ls -lh ../
ls -lh ../tasks/
```

---

## ⚠️ Troubleshooting

### "Input file not found"
```bash
# Make sure the file exists
ls -l my_prompt.txt

# Use full path
./run.sh --input=/full/path/to/prompt.txt
```

### "Timeout waiting for agent"
```bash
# Check logs
tail -50 ../orchestrator.log

# View generated prompt
cat ../step_1_prompt.txt
```

### "Validation failed"
```bash
# Check details in log
grep ERROR ../orchestrator.log

# Check created files
ls -lh ../plan.md ../checklist.md
```

---

## 🛠 Operation Modes

### Automatic (future)
```bash
# When API integration is available
export CURSOR_API_KEY="your-key"
./run.sh --input=prompt.txt --auto
```

### Semi-automatic (current)
```bash
# Script creates prompts, you give them to AI
./run.sh --input=prompt.txt

# Script waits for file creation
# You copy step_N_prompt.txt
# Give to Cursor/ChatGPT/Claude
# Agent creates files
# Script continues automatically
```

---

## 📊 Progress Monitoring

### Console output
```
[2025-10-30 15:00:00] [INFO] 🚀 Starting Test Plan Orchestration
[2025-10-30 15:00:01] [INFO] 📋 Step 1/4: PLAN
[2025-10-30 15:00:05] [INFO] ✅ Step 1 completed
[2025-10-30 15:00:06] [INFO] 📋 Step 2/4: QUALITY
...
```

### Log file
```bash
# Follow log
tail -f ../orchestrator.log

# Search for errors
grep ERROR ../orchestrator.log
```

---

## 🎯 Prompt Examples

### Minimal
```text
Create test plan for module.php
```

### Detailed
```text
Create test plan for modules/payment/processor/processor.php

Requirements:
- 45 methods to test
- Critical payment operations
- OAuth integration
- Webhook handling
- Coverage target: 85%
```

### With Context
```text
Create test plan for PAYMENT_PROVIDER class

Context:
- File: modules/payment/provider/module.provider.php
- Methods: 67
- Dependencies: 15 in constructor
- Interfaces: PaymentProviderInterface, RefundableInterface
- Features: Express Checkout, Recurring Payments

Plan should include integration tests for Payment Provider API.
```

---

## ✅ Success Checklist

After execution, verify:

- [ ] File `plan.md` created and > 1000 lines
- [ ] File `checklist.md` created and > 500 lines
- [ ] Directory `tasks/` created
- [ ] Minimum 5 files `task_XX.md` exist
- [ ] Files `INDEX.md` and `QUICKSTART.md` created
- [ ] File `orchestrator_report.md` created
- [ ] No ERROR entries in `orchestrator.log`

---

## 🔗 Links

- [README.md](README.md) - Full documentation
- [orchestrator.php](orchestrator.php) - Source code
- [example_prompt.txt](example_prompt.txt) - Example prompt

---

## 💡 Pro Tips

1. **Use detailed prompts** - more details lead to better results
2. **Check logs** - they show what's happening
3. **Save prompts** - for reuse
4. **Make backups** - before rerunning with the same files

---

**Version**: 1.0.0  
**Date**: 2025-10-30

