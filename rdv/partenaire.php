
<?php
require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

redirect_if_major_upgrade_required();

// TODO Add sesskey check to edit
$edit   = optional_param('edit', null, PARAM_BOOL);    // Turn editing on and off
$reset  = optional_param('reset', null, PARAM_BOOL);

require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}


// Start setting up the page
$params = array();

$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($header);
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_url('/rdv/partenaire.php');
echo $OUTPUT->header();
$PAGE->set_heading("Visioconférence partenaires");
?>
<body>
<iframe src="https://www.smartagenda.fr/pro/infans-partenaires/rendez-vous/"></iframe>
<?php 
   echo $OUTPUT->footer(); 
?>

<style>
    section {
    position: relative;
    height: 100vh; /* Sets the height of the section to the height of the viewport */
    overflow: hidden;
}

iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    scrollbar-width: none; /* For Firefox */
    -ms-overflow-style: none; /* For Internet Explorer and Edge */
}

iframe::-webkit-scrollbar {
    display: none; /* For Chrome, Safari, and Opera */
}  
</style>
