# Test Plan Orchestrator - Interactive Workflow

## Visual Workflow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                     START: Run orchestrator                         │
│                  ./run.sh --input=prompt.txt                        │
└────────────────────────────────┬────────────────────────────────────┘
                                 │
                                 ▼
                    ┌────────────────────────┐
                    │   Read Input Prompt    │
                    │   Validate Parameters  │
                    └────────────┬───────────┘
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║              STEP 1: PLAN                      ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │ Build detailed prompt  │
                    │ Save: step_1_prompt.txt│
                    └────────────┬───────────┘
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║          🛑 HUMAN INTERACTION REQUIRED         ║
        ╠════════════════════════════════════════════════╣
        ║  ┌──────────────────────────────────────┐     ║
        ║  │ 1. Open: step_1_prompt.txt           │     ║
        ║  │ 2. Copy entire content               │     ║
        ║  │ 3. Press: Cmd+I (Cursor Composer)    │     ║
        ║  │ 4. Paste and submit                  │     ║
        ║  │ 5. Wait for Cursor agent             │     ║
        ║  └──────────────────────────────────────┘     ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │  Monitor for files:    │
                    │  - plan.md             │
                    │                        │
                    │  Check every 2s        │
                    │  Timeout: 5 min        │
                    └────────────┬───────────┘
                                 │
                        ┌────────▼─────────┐
                        │ Files detected?  │
                        └────┬─────────┬───┘
                             │ NO      │ YES
                    ┌────────▼──┐      │
                    │  Timeout? │      │
                    └────┬──┬───┘      │
                         │  │          │
                    YES  │  │ NO       │
                         │  └──────────┘
                         │             │
                         ▼             ▼
                    ┌────────┐   ┌────────┐
                    │  FAIL  │   │VALIDATE│
                    │  EXIT  │   │ FILES  │
                    └────────┘   └────┬───┘
                                      │
                                      ▼
        ╔════════════════════════════════════════════════╗
        ║              STEP 2: QUALITY                   ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │ Build checklist prompt │
                    │ Save: step_2_prompt.txt│
                    └────────────┬───────────┘
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║          🛑 HUMAN INTERACTION REQUIRED         ║
        ╠════════════════════════════════════════════════╣
        ║  Same process: Copy → Paste → Submit          ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │  Monitor for files:    │
                    │  - checklist.md        │
                    └────────────┬───────────┘
                                 │
                                 ▼
                            [VALIDATE]
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║           STEP 3: DECOMPOSITION                ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │ Build tasks prompt     │
                    │ Save: step_3_prompt.txt│
                    └────────────┬───────────┘
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║          🛑 HUMAN INTERACTION REQUIRED         ║
        ╠════════════════════════════════════════════════╣
        ║  Same process: Copy → Paste → Submit          ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │  Monitor for files:    │
                    │  - tasks/task_00.md    │
                    │  - tasks/task_01.md    │
                    │  - tasks/task_02.md    │
                    │  - ... (min 5 tasks)   │
                    │  - tasks/README.md     │
                    └────────────┬───────────┘
                                 │
                                 ▼
                            [VALIDATE]
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║          STEP 4: STANDARDIZATION               ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │ Build structure prompt │
                    │ Save: step_4_prompt.txt│
                    └────────────┬───────────┘
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║          🛑 HUMAN INTERACTION REQUIRED         ║
        ╠════════════════════════════════════════════════╣
        ║  Same process: Copy → Paste → Submit          ║
        ╚════════════════════════════════════════════════╝
                                 │
                    ┌────────────▼───────────┐
                    │  Monitor for files:    │
                    │  - INDEX.md            │
                    │  - QUICKSTART.md       │
                    │  - tasks/README.md     │
                    └────────────┬───────────┘
                                 │
                                 ▼
                            [VALIDATE]
                                 │
                                 ▼
                    ┌────────────────────────┐
                    │  Generate Final Report │
                    │  - orchestrator_report.md
                    │  - orchestrator.log    │
                    └────────────┬───────────┘
                                 │
                                 ▼
        ╔════════════════════════════════════════════════╗
        ║                ✅ SUCCESS                       ║
        ║                                                ║
        ║  All files created and validated               ║
        ║  Test plan ready for implementation            ║
        ╚════════════════════════════════════════════════╝
```

## Detailed Step Breakdown

### Step 1: PLAN (5-10 minutes)
**Input:** Original prompt  
**Output:** `plan.md` (~1000+ lines)  
**Content:** Detailed test specifications for all methods

### Step 2: QUALITY (3-5 minutes)
**Input:** `plan.md`  
**Output:** `checklist.md` (~500+ lines)  
**Content:** Quality checklist and test templates

### Step 3: DECOMPOSITION (5-10 minutes)
**Input:** `plan.md`, `checklist.md`  
**Output:** Multiple `tasks/task_XX.md` files (min 5)  
**Content:** Atomic, independent task descriptions

### Step 4: STANDARDIZATION (3-5 minutes)
**Input:** All previous files  
**Output:** `INDEX.md`, `QUICKSTART.md`, updated `tasks/README.md`  
**Content:** Navigation structure and cross-references

## Timeline

```
Total estimated time: 20-30 minutes (including Cursor agent execution)

┌─────────┬─────────┬─────────┬─────────┬──────────┐
│ Step 1  │ Step 2  │ Step 3  │ Step 4  │  Report  │
│         │         │         │         │          │
│ 5-10min │ 3-5min  │ 5-10min │ 3-5min  │  <1min   │
└─────────┴─────────┴─────────┴─────────┴──────────┘
```

## Retry Logic

Each step has automatic retry with exponential backoff:

```
Attempt 1 → Fail → Wait 2s  → Attempt 2 → Fail → Wait 4s  → Attempt 3 → Fail → EXIT
         ↓                              ↓                              ↓
       Success                        Success                        Success
         ↓                              ↓                              ↓
    Next Step                      Next Step                      Next Step
```

## File Structure After Completion

```
output-directory/
├── plan.md                      ← Step 1 output
├── checklist.md                 ← Step 2 output
├── tasks/                       ← Step 3 outputs
│   ├── README.md
│   ├── task_00.md
│   ├── task_01.md
│   ├── task_02.md
│   └── ...
├── INDEX.md                     ← Step 4 outputs
├── QUICKSTART.md                ←
├── step_1_prompt.txt           ← Saved prompts (for reference)
├── step_2_prompt.txt           ←
├── step_3_prompt.txt           ←
├── step_4_prompt.txt           ←
├── orchestrator.log            ← Execution log
└── orchestrator_report.md      ← Final summary
```

## Progress Indicators

### During Waiting
```
⏳ Still waiting... (290s remaining, 0/3 files ready)
⏳ Still waiting... (280s remaining, 1/3 files ready)
⏳ Still waiting... (270s remaining, 2/3 files ready)
✅ All expected files detected!
```

### On Success
```
✅ Step 1 completed in 45.2s (attempt 1)
✅ Step 2 completed in 32.1s (attempt 1)
✅ Step 3 completed in 67.8s (attempt 1)
✅ Step 4 completed in 28.4s (attempt 1)
```

## Error Scenarios

### Timeout
```
❌ Timeout reached. Missing files:
   ✗ plan.md
⚠️  Step 1 failed: Timeout waiting for agent to complete step 1
🔄 Retry attempt 2/3
```

### Validation Failure
```
❌ File too small: 234 < 1000 lines
⚠️  Step 1 failed: Validation failed for step 1
🔄 Retry attempt 2/3
```

## Key Features

✅ **Semi-automated** - Human guides, machine validates  
✅ **Traceable** - All prompts and logs preserved  
✅ **Resilient** - Retry logic with backoff  
✅ **Transparent** - Clear progress indicators  
✅ **Validated** - Each step checked before proceeding  
✅ **Interactive** - Works with Cursor's actual capabilities  

## Usage Tips

1. **Keep Cursor IDE visible** - Switch between terminal and Cursor
2. **Wait for completion** - Don't interrupt Cursor agent mid-task
3. **Check validation** - Ensure files meet requirements
4. **Read prompts** - Understand what agent should do
5. **Monitor progress** - Watch terminal for updates

---

**This workflow leverages the strengths of both automation (validation, monitoring) and human intelligence (Cursor agent execution).**

