<?php

// Require Composer's autoload file
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get the Todoist API token from environment variables
$TODOIST_API_TOKEN = $_ENV['TODOIST_API_TOKEN'];

if (!$TODOIST_API_TOKEN) {
    throw new Exception("Todoist API token not found. Please set it in the .env file.");
}

// Initialize Guzzle client
$client = new Client([
    'base_uri' => 'https://api.todoist.com/rest/v2/',
    'headers' => [
        'Authorization' => 'Bearer ' . $TODOIST_API_TOKEN
    ]
]);

// Function to fetch projects from Todoist
function fetch_todoist_projects($client) {
    try {
        $response = $client->get('projects');
        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        throw new Exception("Failed to fetch projects: " . $e->getMessage());
    }
}

// Function to fetch tasks from Todoist
function fetch_todoist_tasks($client, $project_id) {
    try {
        $response = $client->get('tasks', [
            'query' => ['project_id' => $project_id]
        ]);
        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        throw new Exception("Failed to fetch tasks: " . $e->getMessage());
    }
}

// Function to delete a task from Todoist
function delete_todoist_task($client, $task_id) {
    try {
        $client->delete('tasks/' . $task_id);
    } catch (RequestException $e) {
        throw new Exception("Failed to delete task: " . $e->getMessage());
    }
}

// Function to convert Todoist tasks to Things3 JSON format
function convert_to_things_json($tasks) {
    $things_tasks = [];
    foreach ($tasks as $task) {
        $things_task = [
            "type" => "to-do",
            "attributes" => [
                "title" => $task['content'],
                "notes" => $task['description'] ?? ''
            ]
        ];
        $things_tasks[] = $things_task;
    }
    return $things_tasks;
}

// Function to generate Things3 import URL
function generate_things_url($things_tasks) {
    $json_data = json_encode($things_tasks);
    $encoded_data = rawurlencode($json_data);
    return 'things:///json?data=' . $encoded_data;
}

// Main function
function main($client, $delete = false) {
    // Fetch projects from Todoist
    $projects = fetch_todoist_projects($client);

    // Find the inbox project ID
    $inbox_project_id = null;
    foreach ($projects as $project) {
        if ($project['is_inbox_project']) {
            $inbox_project_id = $project['id'];
            break;
        }
    }

    if (!$inbox_project_id) {
        throw new Exception("Inbox project not found in Todoist.");
    }

    // Fetch tasks from the inbox project
    $tasks = fetch_todoist_tasks($client, $inbox_project_id);

    // Convert tasks to Things3 JSON format
    $things_tasks = convert_to_things_json($tasks);

    // Generate Things3 import URL
    $things_url = generate_things_url($things_tasks);

    // Print the Things3 import URL
    echo 'Click the link to import tasks into Things3: ' . $things_url . PHP_EOL;

    // Delete tasks from Todoist if the delete option is specified
    if ($delete) {
        foreach ($tasks as $task) {
            delete_todoist_task($client, $task['id']);
        }
        echo 'Tasks deleted from Todoist.' . PHP_EOL;
    }
}

// Check if the delete option is specified
$delete = isset($argv[1]) && $argv[1] === '--delete';

// Run the main function
main($client, $delete);

?>
