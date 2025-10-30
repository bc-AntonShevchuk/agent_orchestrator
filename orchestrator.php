#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Test Plan Automation Orchestrator
 * 
 * Automates test plan creation through 4 logical steps:
 * 1. PLAN → Detailed specifications
 * 2. QUALITY → Checklist
 * 3. DECOMPOSITION → N atomic tasks
 * 4. STANDARDIZATION → Uniform structure
 * 
 * Usage: php orchestrator.php --input="prompt.txt" --output-dir="../"
 */

class TestPlanOrchestrator
{
    private const MAX_RETRIES = 3;
    private const TIMEOUT_SECONDS = 300; // 5 minutes per step
    
    private string $inputPrompt;
    private string $outputDir;
    private string $logFile;
    private array $stepResults = [];
    private int $currentStep = 0;
    
    // Logical steps
    private const STEPS = [
        1 => [
            'name' => 'PLAN',
            'description' => 'Create detailed specifications',
            'output_file' => 'plan.md',
            'validator' => 'validatePlan',
            'min_lines' => 1000, // minimum lines for validation
        ],
        2 => [
            'name' => 'QUALITY',
            'description' => 'Create checklist',
            'output_file' => 'checklist.md',
            'validator' => 'validateChecklist',
            'min_lines' => 500,
        ],
        3 => [
            'name' => 'DECOMPOSITION',
            'description' => 'Create atomic tasks',
            'output_dir' => 'tasks/',
            'validator' => 'validateTasks',
            'min_tasks' => 5,
        ],
        4 => [
            'name' => 'STANDARDIZATION',
            'description' => 'Create uniform structure',
            'validator' => 'validateStandardization',
            'required_files' => ['INDEX.md', 'QUICKSTART.md', 'tasks/README.md'],
        ],
    ];
    
    public function __construct(string $inputPrompt, string $outputDir)
    {
        $this->inputPrompt = $inputPrompt;
        $this->outputDir = rtrim($outputDir, '/') . '/';
        $this->logFile = $this->outputDir . 'orchestrator.log';
        
        $this->ensureOutputDirectory();
    }
    
    /**
     * Main orchestration method
     */
    public function execute(): bool
    {
        $this->log("🚀 Starting Test Plan Orchestration");
        $this->log("📝 Input prompt length: " . strlen($this->inputPrompt) . " characters");
        $this->log("📁 Output directory: " . $this->outputDir);
        $this->log("");
        
        $startTime = microtime(true);
        
        try {
            foreach (self::STEPS as $stepNumber => $stepConfig) {
                $this->currentStep = $stepNumber;
                $this->executeStep($stepNumber, $stepConfig);
            }
            
            $duration = round(microtime(true) - $startTime, 2);
            $this->log("");
            $this->log("✅ All steps completed successfully!");
            $this->log("⏱️  Total time: {$duration} seconds");
            
            $this->generateSummaryReport();
            
            return true;
            
        } catch (Exception $e) {
            $this->log("❌ Orchestration failed: " . $e->getMessage(), 'ERROR');
            $this->log("Stack trace: " . $e->getTraceAsString(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Execute one logical step with retry logic
     */
    private function executeStep(int $stepNumber, array $config): void
    {
        $this->log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->log("📋 Step {$stepNumber}/4: {$config['name']}");
        $this->log("📝 {$config['description']}");
        $this->log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        $attempt = 0;
        $success = false;
        
        while ($attempt < self::MAX_RETRIES && !$success) {
            $attempt++;
            
            if ($attempt > 1) {
                $this->log("🔄 Retry attempt {$attempt}/" . self::MAX_RETRIES);
            }
            
            try {
                $startTime = microtime(true);
                
                // Execute step
                $result = $this->runStepAgent($stepNumber, $config);
                
                $duration = round(microtime(true) - $startTime, 2);
                
                // Validate result
                $validator = $config['validator'];
                if ($this->$validator($result, $config)) {
                    $this->stepResults[$stepNumber] = [
                        'status' => 'success',
                        'attempt' => $attempt,
                        'duration' => $duration,
                        'result' => $result,
                    ];
                    
                    $this->log("✅ Step {$stepNumber} completed in {$duration}s (attempt {$attempt})");
                    $success = true;
                } else {
                    throw new Exception("Validation failed for step {$stepNumber}");
                }
                
            } catch (Exception $e) {
                $this->log("⚠️  Step {$stepNumber} failed: " . $e->getMessage(), 'WARNING');
                
                if ($attempt >= self::MAX_RETRIES) {
                    throw new Exception("Step {$stepNumber} failed after {$attempt} attempts");
                }
                
                // Exponential backoff
                $waitTime = pow(2, $attempt);
                $this->log("⏳ Waiting {$waitTime} seconds before retry...");
                sleep($waitTime);
            }
        }
        
        $this->log("");
    }
    
    /**
     * Run agent to execute step
     */
    private function runStepAgent(int $stepNumber, array $config): array
    {
        $this->log("🤖 Invoking AI agent for step {$stepNumber}...");
        
        // Prepare prompt for agent
        $prompt = $this->buildPromptForStep($stepNumber, $config);
        
        // Save prompt for debugging
        $promptFile = $this->outputDir . "step_{$stepNumber}_prompt.txt";
        file_put_contents($promptFile, $prompt);
        $this->log("📄 Prompt saved to: " . basename($promptFile));
        
        // Invoke agent (using Cursor AI via command)
        $result = $this->invokeCursorAgent($prompt, $stepNumber);
        
        return $result;
    }
    
    /**
     * Invoke Cursor AI agent (Interactive Mode)
     */
    private function invokeCursorAgent(string $prompt, int $stepNumber): array
    {
        // Create prompt file that user can reference
        $promptFile = $this->outputDir . "step_{$stepNumber}_prompt.txt";
        file_put_contents($promptFile, $prompt);
        
        $this->log("📝 Prompt ready for Cursor agent");
        
        $result = [
            'status' => 'pending',
            'files_created' => [],
            'step' => $stepNumber,
        ];
        
        // Interactive mode - guide user to use Cursor agent
        $this->displayCursorInstructions($stepNumber, $promptFile);
        
        // Wait for user confirmation or file creation
        $expectedFiles = $this->getExpectedFiles($stepNumber);
        
        $this->log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->log("⏸️  Paused - Waiting for Cursor agent to complete the task");
        $this->log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->log("");
        $this->log("Expected files:");
        foreach ($expectedFiles as $file) {
            $this->log("  - {$file}");
        }
        $this->log("");
        $this->log("Monitoring for file creation (timeout: " . self::TIMEOUT_SECONDS . "s)");
        $this->log("Press Ctrl+C to cancel");
        $this->log("");
        
        // Wait for file creation with progress indicators
        $timeout = time() + self::TIMEOUT_SECONDS;
        $checkInterval = 2; // Check every 2 seconds
        $lastCheck = 0;
        
        while (time() < $timeout) {
            $currentTime = time();
            
            // Check files
            $allFilesExist = true;
            $createdFiles = [];
            $missingFiles = [];
            
            foreach ($expectedFiles as $file) {
                $filePath = $this->outputDir . $file;
                if (file_exists($filePath)) {
                    $createdFiles[] = $file;
                } else {
                    $allFilesExist = false;
                    $missingFiles[] = $file;
                }
            }
            
            // Show progress every 10 seconds
            if ($currentTime - $lastCheck >= 10) {
                $remaining = $timeout - $currentTime;
                $this->log("⏳ Still waiting... ({$remaining}s remaining, " . count($createdFiles) . "/" . count($expectedFiles) . " files ready)");
                $lastCheck = $currentTime;
            }
            
            if ($allFilesExist) {
                $result['status'] = 'completed';
                $result['files_created'] = $createdFiles;
                $this->log("");
                $this->log("✅ All expected files detected!");
                foreach ($createdFiles as $file) {
                    $this->log("   ✓ {$file}");
                }
                break;
            }
            
            // Short sleep
            sleep($checkInterval);
        }
        
        if ($result['status'] === 'pending') {
            $this->log("");
            $this->log("❌ Timeout reached. Missing files:");
            foreach ($missingFiles as $file) {
                $this->log("   ✗ {$file}");
            }
            throw new Exception("Timeout waiting for agent to complete step {$stepNumber}");
        }
        
        return $result;
    }
    
    /**
     * Display instructions for using Cursor agent
     */
    private function displayCursorInstructions(int $stepNumber, string $promptFile): void
    {
        $stepName = self::STEPS[$stepNumber]['name'];
        
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════════╗\n";
        echo "║  ACTION REQUIRED: Use Cursor Agent                              ║\n";
        echo "╚══════════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📋 Step {$stepNumber}: {$stepName}\n";
        echo "\n";
        echo "🎯 Instructions:\n";
        echo "   1. Open Cursor IDE (if not already open)\n";
        echo "   2. Open the prompt file: {$promptFile}\n";
        echo "   3. Copy the ENTIRE prompt content\n";
        echo "   4. Open Cursor Composer (Cmd+I or Ctrl+I)\n";
        echo "   5. Paste the prompt and press Enter\n";
        echo "   6. Wait for Cursor agent to complete the task\n";
        echo "   7. Verify all required files are created\n";
        echo "\n";
        echo "💡 Tips:\n";
        echo "   - Make sure you're in the correct directory: {$this->outputDir}\n";
        echo "   - The agent will create files automatically\n";
        echo "   - This script will detect when files are ready\n";
        echo "\n";
        echo "📂 This script will automatically continue when files are detected\n";
        echo "\n";
    }
    
    /**
     * Get list of expected files for step
     */
    private function getExpectedFiles(int $stepNumber): array
    {
        $config = self::STEPS[$stepNumber];
        $files = [];
        
        if (isset($config['output_file'])) {
            $files[] = $config['output_file'];
        }
        
        if (isset($config['output_dir'])) {
            // For decomposition step, wait for minimum tasks
            $minTasks = $config['min_tasks'] ?? 5;
            for ($i = 0; $i < $minTasks; $i++) {
                $files[] = $config['output_dir'] . "task_" . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . ".md";
            }
        }
        
        if (isset($config['required_files'])) {
            $files = array_merge($files, $config['required_files']);
        }
        
        return $files;
    }
    
    /**
     * Build prompt for specific step
     */
    private function buildPromptForStep(int $stepNumber, array $config): string
    {
        $context = $this->gatherContext($stepNumber);
        
        $prompt = "# Test Plan Automation - Step {$stepNumber}: {$config['name']}\n\n";
        $prompt .= "## Objective\n";
        $prompt .= "{$config['description']}\n\n";
        
        $prompt .= "## Context\n";
        $prompt .= "This is step {$stepNumber} of 4 in the test plan automation process.\n\n";
        
        if ($stepNumber > 1) {
            $prompt .= "### Previous Steps Completed:\n";
            for ($i = 1; $i < $stepNumber; $i++) {
                $prompt .= "- Step {$i}: " . self::STEPS[$i]['name'] . " ✅\n";
            }
            $prompt .= "\n";
        }
        
        $prompt .= "## Input\n";
        if ($stepNumber === 1) {
            $prompt .= "Original prompt:\n```\n{$this->inputPrompt}\n```\n\n";
        } else {
            $prompt .= $context;
        }
        
        $prompt .= "## Task\n";
        $prompt .= $this->getStepInstructions($stepNumber, $config);
        
        $prompt .= "\n## Output Requirements\n";
        $prompt .= $this->getOutputRequirements($stepNumber, $config);
        
        $prompt .= "\n## Validation Criteria\n";
        $prompt .= $this->getValidationCriteria($stepNumber, $config);
        
        return $prompt;
    }
    
    /**
     * Gather context from previous steps
     */
    private function gatherContext(int $stepNumber): string
    {
        $context = "";
        
        if ($stepNumber > 1) {
            $context .= "### Results from previous steps:\n\n";
            
            for ($i = 1; $i < $stepNumber; $i++) {
                if (isset($this->stepResults[$i])) {
                    $stepConfig = self::STEPS[$i];
                    $context .= "#### Step {$i}: {$stepConfig['name']}\n";
                    
                    if (isset($stepConfig['output_file'])) {
                        $file = $this->outputDir . $stepConfig['output_file'];
                        if (file_exists($file)) {
                            $lines = count(file($file));
                            $context .= "- File: `{$stepConfig['output_file']}` ({$lines} lines)\n";
                        }
                    }
                    
                    $context .= "\n";
                }
            }
        }
        
        return $context;
    }
    
    /**
     * Get instructions for step
     */
    private function getStepInstructions(int $stepNumber, array $config): string
    {
        $instructions = match($stepNumber) {
            1 => <<<EOT
Create a comprehensive test coverage plan (plan.md) that includes:
1. Overview of the class under test
2. List of ALL methods (public, protected, private)
3. For EACH method document:
   - Method signature and parameters
   - Dependencies used
   - Test scenarios (happy path, error cases, edge cases)
   - Mock requirements with code examples
   - Expected number of tests
4. Coverage goals and priorities

The plan should be detailed enough that any developer can implement tests without additional information.
EOT,
            
            2 => <<<EOT
Create a testing checklist (checklist.md) that includes:
1. Pre-test phase (understanding, planning)
2. Test writing phase (AAA pattern, mocking, assertions)
3. Post-test phase (verification, refactoring)
4. Quality checks (code quality, mock quality, coverage)
5. Negative testing guidelines
6. Common anti-patterns to avoid
7. Test templates with code examples
8. Progress tracking mechanism

The checklist should be usable for EVERY single test method written.
EOT,
            
            3 => <<<EOT
Create atomic task files by decomposing the plan:
1. Group related methods into logical tasks
2. Create task files: tasks/task_00.md, task_01.md, etc.
3. Each task file must include:
   - Priority (P0/P1/P2)
   - Status
   - Dependencies on other tasks
   - Methods to test
   - Test count estimate
   - Detailed test scenarios
   - Mock requirements with examples
   - Acceptance criteria
   - Time estimate
4. Ensure tasks are independent and can be executed in parallel where possible
5. Create tasks/README.md with overview and progress tracking

Minimum {$config['min_tasks']} tasks required.
EOT,
            
            4 => <<<EOT
Create standardized navigation and infrastructure:
1. Create INDEX.md - main navigation hub with:
   - Project overview
   - File structure explanation
   - "Where to find..." reference table
   - Workflow guides
   - Quick links
2. Create QUICKSTART.md - quick start guide for agents
3. Create tasks/README.md if not exists - task overview with progress tracking
4. Ensure all files follow naming conventions:
   - plan.md (not plan-*.md)
   - checklist.md (not checklist-*.md)
   - task_XX.md (with zero-padding)
5. Add cross-references between all files
6. Verify all required files exist: {$config['required_files']}

The structure should be automation-ready with machine-readable metadata.
EOT,
            
            default => "Execute step {$stepNumber}"
        };
        
        return $instructions;
    }
    
    /**
     * Get output requirements
     */
    private function getOutputRequirements(int $stepNumber, array $config): string
    {
        $requirements = "Files must be created in: `{$this->outputDir}`\n\n";
        
        if (isset($config['output_file'])) {
            $requirements .= "- Create file: `{$config['output_file']}`\n";
            if (isset($config['min_lines'])) {
                $requirements .= "  - Minimum {$config['min_lines']} lines\n";
            }
        }
        
        if (isset($config['output_dir'])) {
            $requirements .= "- Create directory: `{$config['output_dir']}`\n";
            if (isset($config['min_tasks'])) {
                $requirements .= "  - Minimum {$config['min_tasks']} task files\n";
            }
        }
        
        if (isset($config['required_files'])) {
            $requirements .= "- Required files:\n";
            foreach ($config['required_files'] as $file) {
                $requirements .= "  - `{$file}`\n";
            }
        }
        
        return $requirements;
    }
    
    /**
     * Get validation criteria
     */
    private function getValidationCriteria(int $stepNumber, array $config): string
    {
        $criteria = match($stepNumber) {
            1 => <<<EOT
- File `plan.md` exists
- Contains at least {$config['min_lines']} lines
- Includes overview section
- Documents all methods with signatures
- Has test scenarios for each method
- Includes mock requirements
EOT,
            
            2 => <<<EOT
- File `checklist.md` exists
- Contains at least {$config['min_lines']} lines
- Has multiple phases (pre, during, post)
- Includes code examples
- Has anti-patterns section
- Provides test templates
EOT,
            
            3 => <<<EOT
- Directory `tasks/` exists
- Contains at least {$config['min_tasks']} task files
- Each task file has standard structure
- All tasks have priority assigned
- Dependencies are documented
- tasks/README.md exists with overview
EOT,
            
            4 => <<<EOT
- All required files exist: {$config['required_files']}
- INDEX.md has navigation structure
- QUICKSTART.md has quick start guide
- All files follow naming conventions
- Cross-references exist between files
EOT,
            
            default => "Standard validation"
        };
        
        return $criteria;
    }
    
    // ==================== VALIDATORS ====================
    
    /**
     * Validator for PLAN step
     */
    private function validatePlan(array $result, array $config): bool
    {
        $this->log("🔍 Validating PLAN step...");
        
        $file = $this->outputDir . $config['output_file'];
        
        if (!file_exists($file)) {
            $this->log("❌ File not found: {$config['output_file']}", 'ERROR');
            return false;
        }
        
        $content = file_get_contents($file);
        $lines = count(file($file));
        
        $this->log("📊 File size: {$lines} lines");
        
        if ($lines < $config['min_lines']) {
            $this->log("❌ File too small: {$lines} < {$config['min_lines']} lines", 'ERROR');
            return false;
        }
        
        // Check required sections
        $requiredSections = [
            'Overview',
            'Methods',
            'Test Scenarios',
            'Mock Requirements',
        ];
        
        foreach ($requiredSections as $section) {
            if (stripos($content, $section) === false) {
                $this->log("❌ Missing section: {$section}", 'ERROR');
                return false;
            }
        }
        
        $this->log("✅ Plan validation passed");
        return true;
    }
    
    /**
     * Validator for QUALITY step
     */
    private function validateChecklist(array $result, array $config): bool
    {
        $this->log("🔍 Validating QUALITY step...");
        
        $file = $this->outputDir . $config['output_file'];
        
        if (!file_exists($file)) {
            $this->log("❌ File not found: {$config['output_file']}", 'ERROR');
            return false;
        }
        
        $content = file_get_contents($file);
        $lines = count(file($file));
        
        $this->log("📊 File size: {$lines} lines");
        
        if ($lines < $config['min_lines']) {
            $this->log("❌ File too small: {$lines} < {$config['min_lines']} lines", 'ERROR');
            return false;
        }
        
        // Check key checklist elements
        $requiredElements = [
            'checklist',
            'phase',
            'arrange',
            'act',
            'assert',
            'mock',
        ];
        
        $foundElements = 0;
        foreach ($requiredElements as $element) {
            if (stripos($content, $element) !== false) {
                $foundElements++;
            }
        }
        
        if ($foundElements < count($requiredElements) * 0.8) { // 80% must be present
            $this->log("❌ Missing key elements in checklist", 'ERROR');
            return false;
        }
        
        $this->log("✅ Checklist validation passed");
        return true;
    }
    
    /**
     * Validator for DECOMPOSITION step
     */
    private function validateTasks(array $result, array $config): bool
    {
        $this->log("🔍 Validating DECOMPOSITION step...");
        
        $tasksDir = $this->outputDir . $config['output_dir'];
        
        if (!is_dir($tasksDir)) {
            $this->log("❌ Tasks directory not found: {$config['output_dir']}", 'ERROR');
            return false;
        }
        
        // Count task files
        $taskFiles = glob($tasksDir . 'task_*.md');
        $taskCount = count($taskFiles);
        
        $this->log("📊 Found {$taskCount} task files");
        
        if ($taskCount < $config['min_tasks']) {
            $this->log("❌ Not enough tasks: {$taskCount} < {$config['min_tasks']}", 'ERROR');
            return false;
        }
        
        // Check filename format
        foreach ($taskFiles as $file) {
            $basename = basename($file);
            if (!preg_match('/^task_\d{2}\.md$/', $basename)) {
                $this->log("❌ Invalid task filename format: {$basename}", 'ERROR');
                return false;
            }
        }
        
        // Check for README
        if (!file_exists($tasksDir . 'README.md')) {
            $this->log("❌ tasks/README.md not found", 'ERROR');
            return false;
        }
        
        $this->log("✅ Tasks validation passed");
        return true;
    }
    
    /**
     * Validator for STANDARDIZATION step
     */
    private function validateStandardization(array $result, array $config): bool
    {
        $this->log("🔍 Validating STANDARDIZATION step...");
        
        // Check all required files exist
        foreach ($config['required_files'] as $file) {
            $filePath = $this->outputDir . $file;
            if (!file_exists($filePath)) {
                $this->log("❌ Required file missing: {$file}", 'ERROR');
                return false;
            }
            $this->log("✅ Found: {$file}");
        }
        
        // Check naming conventions
        $correctNames = ['plan.md', 'checklist.md'];
        foreach ($correctNames as $name) {
            if (!file_exists($this->outputDir . $name)) {
                $this->log("❌ File not standardized: {$name}", 'ERROR');
                return false;
            }
        }
        
        $this->log("✅ Standardization validation passed");
        return true;
    }
    
    // ==================== UTILITIES ====================
    
    /**
     * Ensure output directory exists
     */
    private function ensureOutputDirectory(): void
    {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Logging with timestamp
     */
    private function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}\n";
        
        // Output to console
        echo $logMessage;
        
        // Write to log file
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Generate summary report
     */
    private function generateSummaryReport(): void
    {
        $reportFile = $this->outputDir . 'orchestrator_report.md';
        
        $report = "# Test Plan Orchestration Report\n\n";
        $report .= "**Generated**: " . date('Y-m-d H:i:s') . "\n\n";
        
        $report .= "## Steps Summary\n\n";
        
        foreach (self::STEPS as $stepNumber => $config) {
            $result = $this->stepResults[$stepNumber] ?? null;
            
            $report .= "### Step {$stepNumber}: {$config['name']}\n";
            $report .= "- **Description**: {$config['description']}\n";
            
            if ($result) {
                $report .= "- **Status**: ✅ {$result['status']}\n";
                $report .= "- **Attempts**: {$result['attempt']}\n";
                $report .= "- **Duration**: {$result['duration']}s\n";
                
                if (!empty($result['result']['files_created'])) {
                    $report .= "- **Files Created**:\n";
                    foreach ($result['result']['files_created'] as $file) {
                        $report .= "  - `{$file}`\n";
                    }
                }
            }
            
            $report .= "\n";
        }
        
        $report .= "## Output Files\n\n";
        $report .= "```\n";
        $report .= $this->generateFileTree($this->outputDir);
        $report .= "```\n";
        
        file_put_contents($reportFile, $report);
        
        $this->log("📄 Report generated: " . basename($reportFile));
    }
    
    /**
     * Generate file tree
     */
    private function generateFileTree(string $dir, string $prefix = ''): string
    {
        $tree = '';
        $items = scandir($dir);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . $item;
            $isLast = ($item === end($items));
            $connector = $isLast ? '└── ' : '├── ';
            
            $tree .= $prefix . $connector . $item;
            
            if (is_dir($path)) {
                $tree .= "/\n";
                $newPrefix = $prefix . ($isLast ? '    ' : '│   ');
                $tree .= $this->generateFileTree($path . '/', $newPrefix);
            } else {
                $size = filesize($path);
                $tree .= " (" . $this->formatBytes($size) . ")\n";
            }
        }
        
        return $tree;
    }
    
    /**
     * Format file size
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . 'B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1) . 'KB';
        } else {
            return round($bytes / 1048576, 1) . 'MB';
        }
    }
}

// ==================== CLI INTERFACE ====================

/**
 * Parse command line arguments
 */
function parseArgs(array $argv): array
{
    $args = [
        'input' => null,
        'output-dir' => null,
        'help' => false,
    ];
    
    for ($i = 1; $i < count($argv); $i++) {
        if (preg_match('/^--([^=]+)=(.+)$/', $argv[$i], $matches)) {
            $args[$matches[1]] = $matches[2];
        } elseif ($argv[$i] === '--help' || $argv[$i] === '-h') {
            $args['help'] = true;
        }
    }
    
    return $args;
}

/**
 * Show help
 */
function showHelp(): void
{
    echo <<<HELP
Test Plan Automation Orchestrator

Usage:
  php orchestrator.php --input=<prompt_file> --output-dir=<output_directory>
  
Options:
  --input=FILE         Path to file containing the initial prompt
  --output-dir=DIR     Directory where files will be created (default: ../)
  --help, -h           Show this help message
  
Example:
  php orchestrator.php --input=prompt.txt --output-dir=../
  
Steps executed:
  1. PLAN → Create detailed specifications (plan.md)
  2. QUALITY → Create quality checklist (checklist.md)
  3. DECOMPOSITION → Create atomic tasks (tasks/*.md)
  4. STANDARDIZATION → Create standardized structure (INDEX.md, etc.)

HELP;
}

// ==================== MAIN ====================

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

$args = parseArgs($argv);

if ($args['help']) {
    showHelp();
    exit(0);
}

if (!$args['input']) {
    echo "Error: --input parameter is required\n\n";
    showHelp();
    exit(1);
}

if (!file_exists($args['input'])) {
    echo "Error: Input file not found: {$args['input']}\n";
    exit(1);
}

$inputPrompt = file_get_contents($args['input']);
$outputDir = $args['output-dir'] ?? '../';

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   Test Plan Automation Orchestrator                   ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$orchestrator = new TestPlanOrchestrator($inputPrompt, $outputDir);
$success = $orchestrator->execute();

exit($success ? 0 : 1);

