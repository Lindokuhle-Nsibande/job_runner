# Background Job Runner System

This documentation provides instructions on how to set up and use the background job runner system in a Laravel environment. The system allows you to run background jobs by executing specified methods in a separate process.

## Table of Contents

-   Installation
-   Usage
    -   Using `runBackgroundJob`
    -   Examples
-   Configuration
    -   Retry Attempts and Delays
    -   Job Priorities
    -   Security Settings
-   Advanced Features
-   Assumptions, Limitations, and Improvements

## Installation

1. **Clone the repository**:

    ```sh
    git clone <repository-url>
    ```

2. **Install dependencies**:

    ```sh
    composer install
    ```

3. **Set up your environment**:
   Copy the `.env.example` file to `.env` and configure your database and other settings.

4. **Run migrations**:
    ```sh
    php artisan migrate
    ```

## Usage

### Using `runBackgroundJob`

The `runBackgroundJob` function is used to execute a method in a background process. It is defined in `App\Helpers\JobHelper.php`.

#### Function Signature

```php
public static function runBackgroundJob($className, $methodName, $params = [])
```

#### Parameters

-   $className (string): The name of the class containing the method to be executed.
-   $methodName (string): The name of the method to be executed.
-   $params (array): An array of parameters to be passed to the method.

#### Examples

#### Example 1: Running a background job

```php
use App\Helpers\JobHelper;

JobHelper::runBackgroundJob('App\\Services\\ExampleService', 'exampleMethod', ['param1', 'param2']);

```

### Configuration

### Retry Attempts and Delays

You can configure the number of retry attempts and the delay between retries in your config/background_jobs.php file.

```php
return [
    'retry_attempts' => 3,
    'retry_delay' => 5, // in seconds
];

```

#### Security Settings

To ensure security, only allow specific classes to be executed as background jobs. Configure the allowed classes in `config/background_jobs.php.`

```php
return [
    'allowed_classes' => [
        'App\\Services\\AllowedService',
    ],
];
```

## Advanced Features

-   Job Dashboard: Implement a dashboard to monitor job statuses, retry counts, and logs.
-   Priority Handling: Enhance the system to process higher priority jobs first.

The end point to the dashboad is `/job-logs`
