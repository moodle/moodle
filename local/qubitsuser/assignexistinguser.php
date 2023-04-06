<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require($CFG->dirroot."/local/qubitsuser/lib/user.php");
require($CFG->dirroot."/local/qubitsuser/classes/qubits_assign_existing_user_form.php");

$returnto = optional_param('returnto', 0, PARAM_TEXT); // Generic navigation return page switch.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL); // A return URL. returnto must also be set to 'url'.
$siteid = optional_param('siteid', 0, PARAM_INT);    // Site id.

$context = context_system::instance();
require_login();

$pageparams = array('siteid' => $siteid);
if ($returnurl) {
    $pageparams['returnurl'] = $returnurl;
}

if ($returnto === 'url' && confirm_sesskey() && $returnurl) {
    // If returnto is 'url' then $returnurl may be used as the destination to return to after saving or cancelling.
    // Sesskey must be specified, and would be set by the form anyway.
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitsuser/index.php', array("siteid" => $siteid));
    if ($returnto !== 0) {
        switch ($returnto) {
            case 'sitelisting':
                $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php');
                break;
            case 'userslisting':
                $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitsuser/index.php', array("siteid" => $siteid));
                break;
        }
    }
}
$qubitssite = $DB->get_record("local_qubits_sites", array('id' => $siteid));
if (empty($qubitssite))
    throw new \moodle_exception('invalidqubitssiteid');


$linktext = get_string('assignexistinguser', 'local_qubitsuser').' - '.$qubitssite->name;
if ($returnto !== 0) {
    $pageparams['returnto'] = $returnto;
    if ($returnto === 'url' && $returnurl) {
        $pageparams['returnurl'] = $returnurl;
    }
}

$args = array(
    'returnto' => $returnto,
    'returnurl' => $returnurl,
    'context' => $context,
    'siteid' => $siteid,
    'qubitssite' => $qubitssite
);
$currenturl = new moodle_url('/local/qubitsuser/assignexistinguser.php', $pageparams);
$formurl = $CFG->wwwroot.'/local/qubitsuser/assignexistinguser.php?' .http_build_query($pageparams, '', '&');
$userform = new qubits_assign_existing_user_form($formurl, $args);

if ($userform->is_cancelled()) {
    redirect($returnurl);
    die;
} else if ($data = $userform->get_data()) {
    $data->cohortid = $qubitssite->cohortid;
    if (!$userid = qubits_user::assign($data)) {
        $this->verbose("Error assigning a existing user in the database!");
        if (!$this->get('ignore_errors')) {
            die();
        }
    }
    redirect($returnurl, get_string('userassigned', 'local_qubitsuser'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Print the page header.
$PAGE->set_url($currenturl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

echo $OUTPUT->header();

$userform->display();

echo $OUTPUT->footer();