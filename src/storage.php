<?php
function get_data_file_path() {
    return __DIR__ . '/../data/tasks.json';
}

function load_tasks() {
    $path = get_data_file_path();
    if (!file_exists($path)) {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($path, json_encode([], JSON_PRETTY_PRINT));
        return [];
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function save_tasks(array $tasks) {
    $path = get_data_file_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($path, json_encode($tasks, JSON_PRETTY_PRINT));
}