<?php

namespace App\Jobs;

class TestJob
{
    public function execute($param1, $param2)
    {
        // Simulate job processing
        sleep(10);
        echo "Job execution completed.\n";
    }
}