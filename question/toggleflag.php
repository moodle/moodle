<?php
/**
 * Used by ajax calls to toggle the flagged state of a question in an attempt.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

require_once('../config.php');
require_once($CFG->libdir.'/questionlib.php');

// Parameters
$sessionid = required_param('qsid', PARAM_INT);
$attemptid = required_param('aid', PARAM_INT);
$questionid = required_param('qid', PARAM_INT);
$newstate = required_param('newstate', PARAM_BOOL);
$checksum = required_param('checksum', PARAM_ALPHANUM);

// Check user is logged in.
require_login();

// Check the sesskey.
if (!confirm_sesskey()) {
    echo 'sesskey failure';
}

// Check the checksum - it is very hard to know who a question session belongs
// to, so we require that checksum parameter is matches an md5 hash of the
// three ids and the users username. Since we are only updating a flag, that
// probably makes it sufficiently difficult for malicious users to toggle
// other users flags.
if ($checksum != md5($attemptid . "_" . $USER->secret . "_" . $questionid . "_" . $sessionid)) {
    echo 'checksum failure';
}

// Check that the requested session really exists
$questionsession = $DB->get_record('question_sessions', array('id' => $sessionid,
        'attemptid' => $attemptid, 'questionid' => $questionid));
if (!$questionsession) {
    echo 'invalid ids';
}

// Now change state
if (!question_update_flag($sessionid, $newstate)) {
    echo 'update failed';
}

echo 'OK';
