<?php
return [
    'allowed_classes' => [
        'App\\Jobs\\TestJob',
        'App\\Jobs\\TestJobRunning'
    ],
    'retry_delay' => 5,
    'retry_attempts' => 3
];