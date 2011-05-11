<?php

/**
 * Message outputs configuration page
 *
 * @package    message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/message/lib.php');
require_once($CFG->libdir.'/adminlib.php');

// This is an admin page
admin_externalpage_setup('managemessageoutputs');

// Require site configuration capability
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

// Get the submitted params
$disable    = optional_param('disable', 0, PARAM_INT);
$enable     = optional_param('enable', 0, PARAM_INT);

if (!empty($disable) && confirm_sesskey()) {
    if (!$processor = $DB->get_record('message_processors', array('id'=>$disable))) {
        print_error('outputdoesnotexist', 'message');
    }
    $DB->set_field('message_processors', 'enabled', '0', array('id'=>$processor->id));      // Disable output
}

if (!empty($enable) && confirm_sesskey() ) {
    if (!$processor = $DB->get_record('message_processors', array('id'=>$enable))) {
        print_error('outputdoesnotexist', 'message');
    }
    $DB->set_field('message_processors', 'enabled', '1', array('id'=>$processor->id));      // Enable output
}

if ($disable || $enable) {
    $url = new moodle_url('message.php');
    redirect($url);
}
// Page settings
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));

// Grab the renderer
$renderer = $PAGE->get_renderer('core', 'message');

// Display the manage message outputs interface
$processors = get_message_processors();
$messageoutputs = $renderer->manage_messageoutputs($processors);

// Display the page
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managemessageoutputs', 'message'));
echo $messageoutputs;
echo $OUTPUT->footer();