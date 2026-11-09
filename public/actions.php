<?php
require_once __DIR__ . '/../src/storage.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    if ($id !== '') {
        $tasks = load_tasks();
        $found = false;

        foreach ($tasks as &$task) {
            if ($task['id'] === $id) {
                $found = true;
                if ($action === 'complete') {
                    $task['completed'] = !$task['completed'];
                } elseif ($action === 'delete') {
                    $tasks = array_values(array_filter($tasks, function($t) use ($id) {
                        return $t['id'] !== $id;
                    }));
                }
                break;
            }
        }
        unset($task);

        if ($found) {
            save_tasks($tasks);
        }
    }
}

header('Location: index.php');
exit;