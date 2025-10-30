# Test Plan Automation Orchestrator

> **🎯 Interactive Mode**: This orchestrator works **semi-automatically** with Cursor IDE agent.  
> **See [USAGE_WITH_CURSOR.md](./USAGE_WITH_CURSOR.md) for step-by-step instructions.**

## 📋 Description

Automates the test plan creation process through 4 logical steps:

```
[PLAN] → Detailed specifications (plan.md)
    ↓
[QUALITY] → Checklist (checklist.md)
    ↓
[DECOMPOSITION] → N atomic tasks (tasks/*.md)
    ↓
[STANDARDIZATION] → Uniform structure (INDEX.md, QUICKSTART.md, etc.)
```

## 🎯 Features

- ✅ Automatic execution of all 4 steps
- ✅ Retry logic with exponential backoff (up to 3 attempts per step)
- ✅ Validation of results for each step
- ✅ Detailed logging of the entire process
- ✅ Final report generation
- ✅ Timeout protection (5 minutes per step)
- ✅ Integrity checking of created files

## 📁 Structure

```
.local/automation/
├── orchestrator.php       # Main orchestrator script
├── example_prompt.txt     # Example input prompt
└── README.md              # This documentation
```

## 🚀 Usage

### Step 1: Prepare Prompt

Create a file with the initial prompt, e.g., `my_prompt.txt`:

```text
Create a detailed test coverage plan for the code in modules/some/module.php

Requirements:
- Analyze all class methods
- Create detailed specifications
- Describe test scenarios
...
```

See [example_prompt.txt](example_prompt.txt) for an example.

### Step 2: Run Orchestrator

```bash
cd .local/automation
php orchestrator.php --input=my_prompt.txt --output-dir=../
```

### Step 3: Working with Agent

The script will sequentially execute 4 steps. For each step:

1. **Script generates specialized prompt** for AI agent
2. **Saves prompt** to `step_N_prompt.txt`
3. **Waits for file creation** by agent
4. **Validates results**
5. **On error** - retries step (up to 3 times)

### Operation Modes

#### Mode A: Automatic (requires API)
Full automation requires Cursor API or similar AI service.

**TODO**: Add support for:
- Cursor AI API
- OpenAI API
- Anthropic Claude API

#### Mode B: Semi-automatic (current)
1. Script creates prompt for each step
2. You copy prompt and pass to AI agent manually
3. Agent creates files
4. Script validates results

```bash
# Run script
php orchestrator.php --input=prompt.txt --output-dir=../

# Script will create step_1_prompt.txt and wait for files
# You copy contents of step_1_prompt.txt
# Give to AI agent (Cursor, ChatGPT, Claude)
# Agent creates files in .local/
# Script automatically detects files and continues
```

## 📊 Parameters

### Required
- `--input=FILE` - Path to file with initial prompt

### Optional
- `--output-dir=DIR` - Directory for output files (default: `../`)
- `--help, -h` - Show help

## 🔍 Step Details

### Step 1: PLAN (plan.md)
**Goal**: Create detailed specifications of all methods

**Output file**: `plan.md`

**Validation**:
- ✅ File exists
- ✅ Minimum 1000 lines
- ✅ Contains sections: Overview, Methods, Test Scenarios, Mock Requirements

**Time**: ~2-5 minutes

### Step 2: QUALITY (checklist.md)
**Goal**: Create universal checklist for writing tests

**Output file**: `checklist.md`

**Validation**:
- ✅ File exists
- ✅ Minimum 500 lines
- ✅ Contains key elements: checklist, phase, arrange, act, assert, mock

**Time**: ~1-3 minutes

### Step 3: DECOMPOSITION (tasks/*.md)
**Goal**: Break down large plan into atomic tasks

**Output files**: 
- `tasks/task_00.md`, `task_01.md`, ..., `task_NN.md`
- `tasks/README.md`

**Validation**:
- ✅ Directory `tasks/` exists
- ✅ Minimum 5 task files
- ✅ Correct name format: `task_XX.md` (with zero-padding)
- ✅ File `tasks/README.md` exists

**Time**: ~3-10 minutes

### Step 4: STANDARDIZATION (INDEX.md, QUICKSTART.md)
**Goal**: Create navigation structure

**Output files**:
- `INDEX.md` - main navigation hub
- `QUICKSTART.md` - quick start
- `tasks/README.md` - tasks overview

**Validation**:
- ✅ All required files exist
- ✅ Correct names: `plan.md`, `checklist.md` (not `-plan.md`)
- ✅ Cross-references between files

**Time**: ~2-5 minutes

## 📝 Logging

All actions are logged to:
- **Console** - real-time output
- **orchestrator.log** - complete log of all operations

Log format:
```
[2025-10-30 15:00:00] [INFO] 🚀 Starting Test Plan Orchestration
[2025-10-30 15:00:01] [INFO] 📋 Step 1/4: PLAN
[2025-10-30 15:00:05] [INFO] ✅ Step 1 completed in 4.2s (attempt 1)
```

## 📄 Reports

After successful execution, generates:

### orchestrator_report.md
Contains:
- Summary of all steps
- Execution statuses
- Number of attempts
- Execution time
- List of created files
- File structure tree

## 🔄 Retry Logic

On error at any step:
1. **Attempt 1** - immediate
2. **Attempt 2** - after 2 seconds
3. **Attempt 3** - after 4 seconds
4. **Failure** - if all 3 attempts failed

Retry reasons:
- Files not created
- Validation failed
- Timeout (5 minutes)
- Incomplete data

## ⚠️ Error Handling

### Common Issues

#### "Timeout waiting for agent"
**Cause**: Agent didn't create files within 5 minutes

**Solution**: 
- Check prompt in `step_N_prompt.txt`
- Ensure agent is working
- Increase timeout in code (constant `TIMEOUT_SECONDS`)

#### "Validation failed"
**Cause**: Created files don't meet requirements

**Solution**:
- Check logs for details
- Ensure all required sections are present
- Check file sizes

#### "File not found"
**Cause**: Agent didn't create expected file

**Solution**:
- Check path in `--output-dir`
- Ensure agent creates files in correct directory
- Check file names

## 🛠 Extension

### Adding New Step

1. Add step to `STEPS` constant:
```php
5 => [
    'name' => 'NEW_STEP',
    'description' => 'Description',
    'output_file' => 'output.md',
    'validator' => 'validateNewStep',
],
```

2. Create validation method:
```php
private function validateNewStep(array $result, array $config): bool
{
    // Your validation logic
    return true;
}
```

3. Add instructions to `getStepInstructions()`:
```php
5 => <<<EOT
Instructions for new step...
EOT,
```

### API Integration

For full automation, modify the `invokeCursorAgent()` method:

```php
private function invokeCursorAgent(string $prompt, int $stepNumber): array
{
    // API call
    $apiKey = getenv('CURSOR_API_KEY');
    $response = $this->callCursorAPI($apiKey, $prompt);
    
    // Process response
    return $this->processAPIResponse($response);
}
```

## 📋 Pre-Launch Checklist

- [ ] PHP 8.0+ installed
- [ ] Prompt file prepared
- [ ] Output directory exists and is writable
- [ ] AI agent ready to process prompts (if semi-automatic mode)
- [ ] Sufficient disk space (~1-5 MB)

## 📊 Usage Example

```bash
# 1. Create prompt
cat > my_test_plan_prompt.txt << 'EOF'
Create test plan for Payment Gateway module
- Analyze all methods
- Create specifications
- Break down into tasks
EOF

# 2. Run orchestrator
php orchestrator.php --input=my_test_plan_prompt.txt --output-dir=../

# 3. Follow console instructions
# Script will wait for file creation at each step

# 4. Check results
ls -la ../
cat ../orchestrator_report.md
```

## 🎯 Expected Result

After successful execution, output directory will contain:

```
../
├── plan.md                    # Detailed plan
├── checklist.md               # Checklist
├── INDEX.md                   # Navigation
├── QUICKSTART.md              # Quick start
├── orchestrator.log           # Execution log
├── orchestrator_report.md     # Final report
├── step_1_prompt.txt          # Prompts for each step
├── step_2_prompt.txt
├── step_3_prompt.txt
├── step_4_prompt.txt
└── tasks/
    ├── README.md              # Tasks overview
    ├── task_00.md             # Task 0
    ├── task_01.md             # Task 1
    └── ...                    # Other tasks
```

## 🔮 Future Improvements

- [ ] Cursor AI API integration
- [ ] OpenAI API integration
- [ ] Claude API integration
- [ ] Parallel execution of independent steps
- [ ] Web UI for progress monitoring
- [ ] Metrics export to Prometheus/Grafana
- [ ] CI/CD integration
- [ ] Custom validator support via config
- [ ] Resume mechanism (continue from last successful step)

## 📚 See Also

- [example_prompt.txt](example_prompt.txt) - Example prompt
- [orchestrator.php](orchestrator.php) - Script source code
- [../INDEX.md](../INDEX.md) - Main project navigation
- [../QUICKSTART.md](../QUICKSTART.md) - Quick start

## 📞 Support

If issues occur:
1. Check `orchestrator.log` for details
2. Ensure all parameters are specified correctly
3. Check file access permissions
4. Ensure PHP version >= 8.0

---

**Version**: 1.0.0  
**Last Updated**: 2025-10-30  
**Author**: Test Automation Team

