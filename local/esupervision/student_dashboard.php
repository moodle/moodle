<?php
require_once('../../config.php');
require_once('lib.php');
require_login();

// Set up the page context and layout
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Student Dashboard');
$PAGE->set_heading('Student Dashboard');

// Include the Moodle header
echo $OUTPUT->header();

// Get the current user ID
$userId = $USER->id;

// Retrieve student-specific data (replace with your own logic)
// For example, get projects assigned to the student
$projects = get_student_projects($userId);

// Display student dashboard
echo '<h1>Hello, ' . fullname($USER) . '!</h1>';
echo '<h2>Your Projects:</h2>';

if (!empty($projects)) {
    echo '<ul>';
    foreach ($projects as $project) {
        echo '<li>';
        echo '<strong>' . $project->name . '</strong>';
        echo '<br>Description: ' . $project->description . '<br>';
        echo 'Assigned Supervisor: ' . $project->supervisor . '<br>';
        echo 'Status: ' . $project->status;
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No projects assigned.</p>';
}

// Include the Moodle footer
echo $OUTPUT->footer();
?>
