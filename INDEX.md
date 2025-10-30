# Test Plan Automation - Documentation Index

## 📚 Documentation

### 🚀 Getting Started

1. **[QUICKREF.md](QUICKREF.md)** ⭐ - **START HERE**
   - Quick start in 1 minute
   - Basic commands
   - Troubleshooting

2. **[README.md](README.md)** - Complete documentation
   - Detailed description of all functions
   - System architecture
   - Advanced usage

### 📁 Files

```
automation/
├── INDEX.md              # This file - navigation
├── QUICKREF.md          # ⚡ Quick reference
├── README.md            # 📖 Complete documentation
├── orchestrator.php     # 🤖 Main script
├── run.sh               # 🚀 Wrapper for launching
├── example_prompt.txt   # 📝 Example prompt
└── .gitignore           # Git ignore rules
```

---

## ⚡ Quick Start

```bash
# 1. Navigate to directory
cd .local/automation

# 2. Run with example
./run.sh --input=example_prompt.txt

# 3. Done!
```

---

## 📖 What is this?

**Test Plan Automation Orchestrator** - automates test plan creation through 4 logical steps:

```
Step 1: PLAN            → plan.md (detailed specifications)
Step 2: QUALITY         → checklist.md (checklist)
Step 3: DECOMPOSITION   → tasks/*.md (atomic tasks)
Step 4: STANDARDIZATION → INDEX.md, QUICKSTART.md, etc.
```

---

## 🎯 Key Features

- ✅ Automatic execution of all steps
- ✅ Retry logic (up to 3 attempts)
- ✅ Result validation
- ✅ Detailed logging
- ✅ Report generation
- ✅ Timeout protection

---

## 📋 Files and Their Purpose

### Executable Scripts

#### `orchestrator.php`
**Main orchestrator** - executes all 4 steps sequentially

**Main classes:**
- `TestPlanOrchestrator` - main class
- Methods for each step: `executeStep()`, `runStepAgent()`
- Validators: `validatePlan()`, `validateChecklist()`, `validateTasks()`, `validateStandardization()`

**Direct execution:**
```bash
php orchestrator.php --input=prompt.txt --output-dir=../
```

#### `run.sh`
**Wrapper script** - simplified interface for orchestrator.php

**Features:**
- Beautiful colored output
- Parameter validation
- Dry-run mode
- Automatic environment check

**Execution:**
```bash
./run.sh --input=prompt.txt [--output=../] [--dry-run]
```

### Documentation

#### `QUICKREF.md`
**Quick reference** - for those who want to start in a minute

**Contains:**
- Copy-paste commands
- Brief step descriptions
- Quick troubleshooting
- Prompt examples

#### `README.md`
**Complete documentation** - detailed description of the entire system

**Contains:**
- System architecture
- Description of all 4 steps
- Detailed validation
- Operation modes
- Functionality extension
- Code examples

#### `INDEX.md`
**This file** - navigation hub

### Examples

#### `example_prompt.txt`
**Example input prompt** for system testing

**Contains:**
- Typical prompt for test plan creation
- Requirements description
- Class context

---

## 🔄 Workflow

### For New Users

```
1. Open QUICKREF.md
   ↓
2. Copy launch command
   ↓
3. Run ./run.sh --input=example_prompt.txt
   ↓
4. Observe execution
   ↓
5. Check results
```

### For Advanced Users

```
1. Create your own prompt
   ↓
2. Run directly:
   php orchestrator.php --input=my_prompt.txt
   ↓
3. Check orchestrator_report.md
```

---

## 📊 What Gets Created

After successful execution in the parent directory (`../`):

```
../
├── plan.md                    # Step 1 output
├── checklist.md               # Step 2 output
├── INDEX.md                   # Step 4 output
├── QUICKSTART.md              # Step 4 output
├── orchestrator.log           # Execution log
├── orchestrator_report.md     # Final report
├── step_1_prompt.txt          # Prompts used
├── step_2_prompt.txt
├── step_3_prompt.txt
├── step_4_prompt.txt
└── tasks/                     # Step 3 output
    ├── README.md
    ├── task_00.md
    ├── task_01.md
    └── ...
```

---

## 🛠 Usage

### Basic

```bash
# With example
./run.sh --input=example_prompt.txt

# With your own prompt
./run.sh --input=my_prompt.txt

# Custom output
./run.sh --input=prompt.txt --output=/tmp/output/
```

### Advanced

```bash
# Direct orchestrator call
php orchestrator.php --input=prompt.txt --output-dir=../

# Dry run
./run.sh --input=prompt.txt --dry-run

# Help
./run.sh --help
php orchestrator.php --help
```

---

## 🔍 Monitoring

### Real-time

```bash
# Follow log
tail -f ../orchestrator.log

# Search for errors
grep ERROR ../orchestrator.log

# Check progress
grep "Step [0-9]" ../orchestrator.log
```

### After Execution

```bash
# Report
cat ../orchestrator_report.md

# Created files
ls -lh ../

# Tasks
ls -lh ../tasks/
```

---

## ⚠️ Troubleshooting

### Common Issues

| Issue | Solution | File |
|-------|----------|------|
| "Input file not found" | Check file path | - |
| "Timeout waiting" | Increase `TIMEOUT_SECONDS` | orchestrator.php:17 |
| "Validation failed" | Check logs | orchestrator.log |
| "Permission denied" | `chmod +x run.sh` | - |

### Where to Find Information

| What to Look For | Where to Check |
|------------------|----------------|
| Quick help | [QUICKREF.md](QUICKREF.md) |
| Error details | `../orchestrator.log` |
| Generated prompts | `../step_N_prompt.txt` |
| Final report | `../orchestrator_report.md` |
| Detailed documentation | [README.md](README.md) |

---

## 🎓 Usage Examples

### Example 1: Testing Payment Module

```bash
# Create prompt
cat > payment_prompt.txt << 'EOF'
Create test plan for modules/payment/processor/processor.php
- 45 methods
- Payment operations (charge, refund, void)
- Webhook handling
- Coverage: 85%+
EOF

# Run
./run.sh --input=payment_prompt.txt
```

### Example 2: Testing API Controller

```bash
# Create prompt
cat > api_prompt.txt << 'EOF'
Create test plan for API controller
File: api/v2/OrdersController.php
Methods: 23
Endpoints: GET, POST, PUT, DELETE /v2/orders
EOF

# Run
./run.sh --input=api_prompt.txt --output=../api-tests/
```

### Example 3: Dry Run Before Execution

```bash
# See what will be executed
./run.sh --input=my_prompt.txt --dry-run

# If everything is OK, run
./run.sh --input=my_prompt.txt
```

---

## 📈 Metrics

Typical execution:
- **Step 1 (PLAN)**: 2-5 minutes, ~1000-2000 lines
- **Step 2 (QUALITY)**: 1-3 minutes, ~500-800 lines
- **Step 3 (DECOMPOSITION)**: 3-10 minutes, 5-20 tasks
- **Step 4 (STANDARDIZATION)**: 2-5 minutes, 3-4 files

**Total**: 10-25 minutes for complete plan

---

## 🔮 Roadmap

### Version 1.1
- [ ] Cursor AI API integration
- [ ] OpenAI API integration
- [ ] Claude API integration

### Version 1.2
- [ ] Parallel execution of independent steps
- [ ] Resume mechanism
- [ ] Web UI

### Version 2.0
- [ ] CI/CD integration
- [ ] Metrics export (Prometheus)
- [ ] Custom validators via config

---

## 📞 Contact

- **Documentation**: [README.md](README.md)
- **Quick Start**: [QUICKREF.md](QUICKREF.md)
- **Code**: [orchestrator.php](orchestrator.php)

---

## 📜 License

Internal tool for BigCommerce test automation.

---

**Version**: 1.0.0  
**Date**: 2025-10-30  
**Author**: Test Automation Team

