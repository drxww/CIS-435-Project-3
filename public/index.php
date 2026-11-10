<?php
session_start();

require_once __DIR__ . '/../src/storage.php';

// Load all tasks
$tasks = load_tasks();

// Get search filters from URL
$search = trim($_GET['search'] ?? '');
$priority = $_GET['priority'] ?? '';

// Only allow valid priority filters
if (!in_array($priority, ['', 'Low', 'Medium', 'High'], true)) {
    $priority = '';
}

// Filter tasks
$filteredTasks = [];

foreach ($tasks as $task) {
    $matchesText = true;
    $matchesPriority = true;

    if ($search !== '') {
        $matchesText = stripos($task['title'], $search) !== false;
    }

    if ($priority !== '') {
        $matchesPriority = $task['priority'] === $priority;
    }

    if ($matchesText && $matchesPriority) {
        $filteredTasks[] = $task;
    }
}

// Count tasks
$total = count($tasks);
$shown = count($filteredTasks);
$completed = 0;

foreach ($tasks as $task) {
    if ($task['completed']) {
        $completed++;
    }
}

$open = $total - $completed;

// Flash message
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Escape HTML output, prevent text from being misinterpreted
function escapeHTML($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

header('Content-Type: text/html; charset=UTF-8');
?>

<!DOCTYPE html>

<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TaskPad PHP</title>
    <link rel="stylesheet" href="assets/style.css">

</head>
<body>

<main class="container">

    <header class="page-header">
        <h1>TaskPad PHP🗒️ </h1>
        <a href="create.php" class="button">+ Add Task</a>
    </header>

    <?php if ($flash !== ''): ?>
        <p class="flash">
        <?= escapeHTML($flash) ?></p>
    <?php endif; ?>

    <section class="stats">
        <p>Total: <?= $total ?></p>
        <p>Open: <?= $open ?></p>
        <p>Completed: <?= $completed ?></p>
        <p>Showing: <?= $shown ?></p>
    </section>

    <form method="GET" action="index.php" class="filters">

        <label for="search">Search Tasks</label>
        <input
            type="text"
            id="search"
            name="search"
            value="<?= escapeHTML($search) ?>"
            placeholder="Search Task Name"
        >

        <label for="priority">Priority</label>
        <select id="priority" name="priority">
            <option value="">All Priorities</option>

            <?php foreach (['Low', 'Medium', 'High'] as $p): ?>
                <option value="<?= escapeHTML($p) ?>"
                    <?= $priority === $p ? 'selected' : '' ?>>
                    <?= escapeHTML($p) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="button">Filter</button>
        <a href="index.php" class="button">Clear</a>

    </form>

    <section class="task-list">
        <h2>Tasks</h2>

        <?php if ($total === 0): ?>

            <p>No tasks yet. Add your first task!</p>

        <?php elseif ($shown === 0): ?>

            <p>No matching tasks found.</p>

        <?php else: ?>

            <?php foreach ($filteredTasks as $task): ?>

                <article class="task-card">

                    <h3><?= escapeHTML($task['title']) ?></h3>

                    <p><?= escapeHTML($task['description']) ?></p>

                    <p>
                        Priority:
                        <span class="indicator">
                            <?= escapeHTML($task['priority']) ?>
                        </span>
                    </p>

                    <p>
                        Status:
                        <span class="indicator">
                            <?= $task['completed'] ? 'Completed' : 'Open' ?>
                        </span>
                    </p>

                    <?php if (!empty($task['due'])): ?>
                        <p>Due: <?= escapeHTML($task['due']) ?></p>
                    <?php endif; ?>

                    <div class="task-actions">

                        <?php if (!$task['completed']): ?>
                            <form method="POST" action="actions.php">
                                <input type="hidden" name="id"
                                    value="<?= escapeHTML($task['id']) ?>">
                                <input type="hidden" name="action"
                                    value="complete">
                                <button type="submit" class="button">
                                    Complete
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" action="actions.php">
                            <input type="hidden" name="id"
                                value="<?= escapeHTML($task['id']) ?>">
                            <input type="hidden" name="action"
                                value="delete">
                            <button type="submit" class="button button-delete">
                                Delete
                            </button>
                        </form>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

</main>

</body>
</html>
