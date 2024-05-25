<?php

namespace app\tools;

require 'Api.php';

class Route
{
    private Api $timeLoggerApi;

    public function __construct()
    {
        $this->timeLoggerApi = new Api();
    }

    public function route(string $type, string $action, ?string $argument): void
    {
        switch ($type) {
            case '--help':
                $this->showHelp();
                break;
            case '--status':
                $this->showStatus();
                break;
            case '-p':
                $this->handleProject($action, $argument);
                break;
            case '-t':
                $this->handleTask($action, $argument);
                break;
            case '-b':
                $this->handleBooking($action, $argument);
                break;
            default:
                echo("Invalid arguments\n");
        }
    }

    private function handleProject(string $action, ?string $argument): void
    {
        switch ($action) {
            case 'list':
                $this->timeLoggerApi->getProjectList();
                break;
            case 'use':
                $this->timeLoggerApi->useProject((int)$argument);
                break;
            case 'create':
                $this->timeLoggerApi->createProject($argument);
                break;
            default:
                echo('Invalid arguments');
        }
    }

    private function handleTask(string $action, ?string $argument): void
    {
        switch ($action) {
            case 'list':
                $this->timeLoggerApi->getTaskList();
                break;
            case 'use':
                $this->timeLoggerApi->useTask((int)$argument);
                break;
            case 'create':
                $this->timeLoggerApi->createTask($argument);
                break;
            default:
                echo('Invalid arguments');
        }
    }

    private function handleBooking(string $action, ?string $argument): void
    {
        switch ($action) {
            case 'start':
                $this->timeLoggerApi->startBooking();
                break;
            case 'stop':
                $this->timeLoggerApi->endBooking($argument);
                break;
            default:
                echo('Invalid arguments');
        }
    }

    private function showStatus(): void
    {
        $this->timeLoggerApi->showStatus();
    }

    private function showHelp(): void
    {
        $text = <<<'EOF'
Usage:
  time [type] [action] [arguments]

Options:
  --help            Display this help

Available commands:
  --status                      Show project status
  -p list                       List projects
  -p use <project_id>           Switch to a project
  -p create <project_name>      Create a new project

  -t list                       List tasks on the current project  
  -t use <task_id>              Switch to a task
  -t create <task_name>         Create a new task
  -b start                      Start booking task
  -b stop <description>         Stop booking task
EOF;

        echo($text . "\n");
    }
}
