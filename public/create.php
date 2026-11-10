<?php

/* The following two lines include the validation and storage functions needed for
task creation. */
require_once __DIR__ . '/../src/validation.php';
require_once __DIR__ . '/../src/storage.php';

// The following section initializes variables used to display the form information.
$errors = [];
$formData = [
    'title' => '',
    'description' => '',
    'priority' => 'Medium',
    'due' => ''
];

// The following section checks whether the user submitted the form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // This validates the information users submit through the form.
    $result = validateCreateTask($_POST);

    // These commands retrieve validation results.
    $errors = $result['errors'];
    $formData = $result['data'];

    // The following section is executed only if the information users submit is valid.
    if ($result['isValid']) {

        // This retrieves all existing tasks from the JSON file.
        $tasks = load_tasks();

        // The following section creates a new task using the validated information.
        $newTask = [
            'id' => uniqid('task_', true),
            'title' => $result['data']['title'],
            'description' => $result['data']['description'],
            'priority' => $result['data']['priority'],
            'due' => $result['data']['due'],
            'completed' => false,
            'created_at' => date('c')
        ];

        // The following command adds the new task to the existing task list.
        $tasks[] = $newTask;

        // The following command saves the updated task list to the JSON file.
        save_tasks($tasks);

        /* The following command redirects the page back to the task list after
        successful creation. */
        header('Location: index.php?created=1');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <!-- The following defines the character encoding so the
    browser displays text correctly. -->
    <meta charset="UTF-8">

    <!-- The following ensures the page adjusts to different screens
    as instructed. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Task | TaskPad</title>

    <!-- The following line connects this page to the CSS stylesheet. -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <main class="container">

        <h1>Create a Task</h1>
        <p>Enter the details below to add a task to your list.</p>

        <!-- The following section ensures create.php receives the users'
        responses via POST. -->

        <form action="create.php" method="POST">

            <!-- The following section implements the required 120
            character limit. -->
            <div class="form-group">
                <label for="title">Task Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="120"
                    required
                    value="<?= htmlspecialchars($formData['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <!-- The following section displays the title validation
                error when a user submits an invalid value. -->

                <?php if (isset($errors['title'])): ?>
                    <p class="error">
                        <?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Description (Optional)</label>

                <textarea
                    id="description"
                    name="description"
                    maxlength="1000"
                    rows="4"
                ><?= htmlspecialchars($formData['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                <!-- The following displays a validation error if present. -->

                <?php if (isset($errors['description'])): ?>
                    <p class="error">
                        <?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- The following section highlights the different options users can
            select when creating tasks. -->

            <div class="form-group">
                <label for="priority">Priority</label>

                <select id="priority" name="priority" required>
                    <option value="Low" <?= $formData['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
                    <option value="Medium" <?= $formData['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="High" <?= $formData['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                </select>

                <!-- The following section displays a validation error if present. -->

                <?php if (isset($errors['priority'])): ?>
                    <p class="error">
                        <?= htmlspecialchars($errors['priority'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- The following section implements the due date field of the Task Tracker (optional). -->

            <div class="form-group">
                <label for="due">Due Date (Optional)</label>

                <input
                    type="date"
                    id="due"
                    name="due"
                    value="<?= htmlspecialchars($formData['due'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <!-- The following section displays a validation error if present. -->

                <?php if (isset($errors['due'])): ?>
                    <p class="error">
                        <?= htmlspecialchars($errors['due'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- The following line submits the task information to be processed by the PHP implementation. -->

            <button type="submit">Create Task</button>

        </form>

        <!-- The following line returns the user to the main task list. -->

        <a href="index.php">Back to Task List</a>

    </main>

</body>
</html>
