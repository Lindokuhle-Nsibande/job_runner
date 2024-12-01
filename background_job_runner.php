<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobLog;
use Illuminate\Support\Facades\Config;

if ($argc < 4) {
    echo "Usage: php background_job_runner.php <ClassName> <MethodName> <Params>\n";
    return false;
}

$className = $argv[1];
$methodName = $argv[2];
$params = array_slice($argv, 3);

$processID = getmypid();
if ($processID === false || $processID === null) {
    echo "Failed to get process ID.\n";
    return false;
}

$jobLog = JobLog::create([
    'class_name' => $className,
    'method_name' => $methodName,
    'parameters' => json_encode($params),
    'status' => 'pending',
    'retry_count' => 0,
    'priority' => 0,
    'process_id' => $processID
]);

$allowedClasses = Config::get('background_jobs.allowed_classes', []);
if (!in_array($className, $allowedClasses)) {
    updateJobLog($jobLog, 'failure', 'Unauthorized class.');
    echo "Unauthorized class: " . $className;
    return false;
}

if (!class_exists($className)) {
    updateJobLog($jobLog, 'failure', "Class $className not found.");
    echo "Class $className not found.";
    return false;
}

$classInstance = app($className);
if (!method_exists($classInstance, $methodName)) {
    updateJobLog($jobLog, 'failure', "Method $methodName not found in class $className.");
    echo "Method $methodName not found in class $className.";
    return false;
}

$retryAttempts = Config::get('background_jobs.retry_attempts');
$retryDelay = Config::get('background_jobs.retry_delay'); // seconds

for ($attempt = 1; $attempt < $retryAttempts; $attempt++) {
    try {

        // echo "Attempt $attempt: Executing $className::$methodName\n";
        updateJobLog($jobLog, 'running', "Executing $className::$methodName on attempt $attempt", $attempt);


        call_user_func_array([$classInstance, $methodName], $params);

        // error_log("We are in");
        // echo "Method executed successfully.\n";
        updateJobLog($jobLog, 'success', "Job succeeded on attempt $attempt", $attempt);
        error_log("We are in");
        break;
    } catch (\Exception $e) {
        updateJobLog($jobLog, 'failure', $e->getMessage(), $attempt);
        error_log("Job failed on attempt $attempt: " . $e->getMessage() . "\n");
        if ($attempt < $retryAttempts - 1) {
            sleep($retryDelay);
        }
    }
}

function updateJobLog($jobLog, $status, $message, $retryCount = 0)
{
    $jobLog->update([
        'status' => $status,
        'retry_count' => $retryCount,
        'error_message' => $message
    ]);
    logJobToFile($jobLog->class_name, $jobLog->method_name, $status, $message);
}

function logJobToFile($class, $method, $status, $error = null)
{
    $logDirectory = storage_path('logs');
    $logFile = $logDirectory . '/background_jobs.log';
    $errorLogFile = $logDirectory . '/background_jobs_errors.log';

    if (!is_dir($logDirectory)) {
        mkdir($logDirectory, 0755, true);
    }

    $logMessage = sprintf("[%s] %s::%s - %s", date('Y-m-d H:i:s'), $class, $method, $status);
    if ($error) {
        $logMessage .= " - Error: $error";
        file_put_contents($errorLogFile, $logMessage . PHP_EOL, FILE_APPEND);
    } else {
        file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
    }
}