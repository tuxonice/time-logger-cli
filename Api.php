<?php

namespace app\tools;

class Api
{
    public function showStatus(): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $token = $config['token'];
        $method = 'GET';
        $baseUrl = $config['baseUrl'];
        $url =  $baseUrl . '/projects/';

        $currentProjectId = $config['currentProjectId'];
        $currentTaskId = $config['currentTaskId'];

        $response = $this->makeRequest($method, $url, $token);
        $response = json_decode($response, true);

        foreach ($response as $project) {
            if ($currentProjectId == $project['id']) {
                echo("Project: " . $project['name'] . " [" . $project['id'] . "]\n");
                foreach ($project['tasks'] as $task) {
                    if ($task['id'] == $currentTaskId) {
                        echo("Task: " . $task['name'] . " [" . $task['id'] . "]\n");
                    }
                }
                break;
            }
        }
    }

    public function getProjectList(): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $token = $config['token'];
        $method = 'GET';
        $baseUrl = $config['baseUrl'];
        $url =  $baseUrl . '/projects/';

        $response = $this->makeRequest($method, $url, $token);
        $response = json_decode($response, true);

        echo("Projects list:\n\n");
        foreach ($response as $project) {
            echo("[" . $project['id'] . "] " . $project['name'] . "\n");
        }
    }

    public function createProject(string $name): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $token = $config['token'];
        $method = 'POST';
        $baseUrl = $config['baseUrl'];
        $url =  $baseUrl . '/projects/create';

        $data = [
            'name' => $name,
        ];

        $response = $this->makeRequest($method, $url, $token, json_encode($data));
    }

    public function useProject(int $projectId): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $config['currentProjectId'] = $projectId;
        $config['currentTaskId'] = null;

        file_put_contents('.config.json', json_encode($config, JSON_PRETTY_PRINT));
    }

    public function useTask(int $taskId): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $config['currentTaskId'] = $taskId;

        file_put_contents('.config.json', json_encode($config, JSON_PRETTY_PRINT));
    }

    public function getTaskList(): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $token = $config['token'];
        $method = 'GET';
        $baseUrl = $config['baseUrl'];
        $projectId = $config['currentProjectId'];
        $url =  $baseUrl . '/projects/' . $projectId . '/tasks';


        $response = $this->makeRequest($method, $url, $token);
        $response = json_decode($response, true);

        echo("Task list:\n\n");
        foreach ($response as $task) {
            echo("[" . $task['id'] . "] " . $task['name'] . "\n");
        }
    }

    public function createTask(string $name): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $token = $config['token'];
        $method = 'POST';
        $baseUrl = $config['baseUrl'];
        $projectId = $config['currentProjectId'];
        $url =  $baseUrl . '/projects/' . $projectId . '/tasks/create';

        $data = [
            'name' => $name,
        ];

        $this->makeRequest($method, $url, $token, json_encode($data));
    }

    public function startBooking(): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $token = $config['token'];
        $method = 'POST';
        $baseUrl = $config['baseUrl'];
        $projectId = $config['currentProjectId'];
        $taskId = $config['currentTaskId'];
        $url =  $baseUrl . '/projects/' . $projectId . '/tasks/' . $taskId . '/bookings/start';

        $response = $this->makeRequest($method, $url, $token);
        $response = json_decode($response, true);

        $config['currentKey'] = $response['key'];

        file_put_contents('.config.json', json_encode($config, JSON_PRETTY_PRINT));
    }

    public function endBooking(string $description): void
    {
        $config = json_decode(file_get_contents('.config.json'), true);
        $token = $config['token'];
        $method = 'PUT';
        $baseUrl = $config['baseUrl'];
        $projectId = $config['currentProjectId'];
        $taskId = $config['currentTaskId'];
        $key = $config['currentKey'];

        $url =  $baseUrl . '/projects/' . $projectId . '/tasks/' . $taskId . '/bookings/end';

        $data = [
            'key' => $key,
            'description' => $description,
        ];

        $response = $this->makeRequest($method, $url, $token, json_encode($data));
        $response = json_decode($response, true);

        $config['currentKey'] = null;

        file_put_contents('.config.json', json_encode($config, JSON_PRETTY_PRINT));
    }

    private function makeRequest(string $method, string $url, string $token, string $data = ''): string
    {
        ob_start();
        $command = "curl -s --request $method '$url' --header 'X-Bearer-Token: $token' --header 'Content-Type: application/json' ";
        if (!empty($data)) {
            $command .= " --data-raw '$data'";
        }

        $result = system($command);
        ob_clean();
        return $result;
    }
}
