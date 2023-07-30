<?php
require_once('../../config.php');
require_once('lib.php');
require_login();

// Check if the current user is a supervisor (you can customize the role name)
if (!is_siteadmin() && !has_capability('local/esupervision:supervisor', context_system::instance())) {
    // If not a supervisor, redirect to the home page or show an error message
    redirect(new moodle_url('/'));
}

// Set up the page context and layout
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Supervisor Dashboard');
$PAGE->set_heading('Supervisor Dashboard');

echo $OUTPUT->header();

// Get the current user ID (supervisor ID)
$supervisorId = $USER->id;

// Retrieve projects assigned to the supervisor
$projects = get_supervisor_projects($supervisorId);

// Display supervisor dashboard
echo '<h1>Hello, ' . fullname($USER) . '!</h1>';
echo '<h2>Your Assigned Students:</h2>';

if (!empty($projects)) {
    echo '<ul>';
    foreach ($projects as $project) {
        echo '<li>';
        echo '<strong>' . $project->student_name . '</strong>';
        echo '<br>Project Description: ' . $project->description . '<br>';
        echo 'Project Status: ' . $project->status;
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No students assigned to your projects.</p>';
}

// Include the Moodle footer
echo $OUTPUT->footer();
?>