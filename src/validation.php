<?php

/* The following function validates the information submitted through 
the task creation form.*/

function validateCreateTask(array $input): array
{
    // The following line stores validation errors by field.
    $errors = [];

    /* The following four commands retrieve the values it recieves, 
    and if a field is missing, it uses an empty string.*/ 
    $title = $input['title'] ?? '';
    $description = $input['description'] ?? '';
    $priority = $input['priority'] ?? '';
    $due = $input['due'] ?? '';

    // The following section checks that all submitted values are strings.
    foreach ([
        'title' => &$title,
        'description' => &$description,
        'priority' => &$priority,
        'due' => &$due
    ] as $field => &$value) {
        if (!is_string($value)) {
            $errors[$field] = 'Invalid input.';
            $value = '';
        }
    }
    unset($value);

    // The following four commands remove unneeded whitespace from submitted values.
    $title = trim($title);
    $description = trim($description);
    $priority = trim($priority);
    $due = trim($due);

    // The following section validates the required task title.
    if ($title === '') {
        $errors['title'] = 'Please enter a task title.';
    } elseif (strlen($title) > 120) {
        $errors['title'] = 'The title cannot exceed 120 characters.';
    }

    // The following section validates the optional task description.
    if (strlen($description) > 1000) {
        $errors['description'] = 'The description cannot exceed 1,000 characters.';
    }

    // The following section validates priority against the three allowed options.
    $allowedPriorities = ['Low', 'Medium', 'High'];

    if (!in_array($priority, $allowedPriorities, true)) {
        $errors['priority'] = 'Please select a valid priority.';
    }

    // The following section validates the optional due date.
    if ($due !== '') {
        // This checks whether the date follows the YYYY-MM-DD format.
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due)) {
            $errors['due'] = 'Please enter a date in YYYY-MM-DD format.';
        } else {
            // This extracts the year, month, and day of the date that users submit.
            $year = (int) substr($due, 0, 4);
            $month = (int) substr($due, 5, 2);
            $day = (int) substr($due, 8, 2);

            // This checks whether the submitted date exists on the calendar.
            if (!checkdate($month, $day, $year)) {
                $errors['due'] = 'Please enter a valid calendar date.';
            }
        }
    }

    /* The following section organizes the sanitized information before returning 
    the results.*/ 
    $data = [
        'title' => $title,
        'description' => $description,
        'priority' => $priority,
        'due' => $due === '' ? null : $due
    ];

    // The following section returns the validation status, errors, and cleaned task information.
    return [
        'isValid' => empty($errors),
        'errors' => $errors,
        'data' => $data
    ];
}
