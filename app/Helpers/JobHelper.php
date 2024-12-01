<?php

namespace App\Helpers;

class JobHelper
{
    public $timeout = 300;

    public static function runBackgroundJob($className, $methodName, $params = [])
    {
        try {
            $paramsJson = implode(' ', array_map('escapeshellarg', $params));
            $command = sprintf(
                'php %s %s %s %s',
                escapeshellarg(base_path('background_job_runner.php')),
                escapeshellarg($className),
                escapeshellarg($methodName),
                $paramsJson
            );

            if (stripos(PHP_OS, 'WIN') === 0) {
                // Windows
                $command = "start /B " . $command;
            } else {
                // Unix-based systems
                $command .= " > /dev/null 2>&1 &";
            }

            // Log the command for debugging purposes
            error_log("Executing command: $command");

            // Execute the command
            exec($command);
        } catch (\Exception $e) {
            error_log("Failed to execute command: " . $e->getMessage());
        }
    }
}