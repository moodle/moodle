<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of jitsi
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_jitsi
 * @copyright  2019 Sergio Comerón <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");

/**
 * Form users to call a meeting
 */
class userstocall_form extends moodleform {
    /**
     * Define the form
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!.

        $options = array(
            'ajax' => 'core_user/form_user_selector',
            'multiple' => true
        );
        $mform->addElement('autocomplete', 'userstocall', 'Users', array(), $options);
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', 'Call');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    /**
     * Validate the form data
     * @param array $data - array
     * @param array $files - array files
     */
    public function validation($data, $files) {
        return array();
    }
}

global $USER, $DB, $PAGE, $CFG;

$userid = required_param('user', PARAM_INT);
$fromuserid = optional_param('fromuser', null, PARAM_INT);
$userstocall = optional_param_array('userstocall', null, PARAM_RAW);

$user = $DB->get_record('user', array('id' => $userid));

$PAGE->set_context(context_system::instance());
require_login();
$PAGE->set_url('/mod/jitsi/viewpriv.php', array('user' => $user->id));
require_login();
$PAGE->set_title(format_string($user->firstname));
$PAGE->set_heading(format_string($user->firstname));

if ($userstocall != null) {
    foreach ($userstocall as $usertocall) {
        $usertocallob = $DB->get_record('user', array('id' => $usertocall));
        sendcallprivatesession($user, $usertocallob);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('privatesession', 'jitsi', $user->firstname));

if ($CFG->jitsi_privatesessions) {

    if ($USER->id == $user->id) {
        $moderation = 1;
    } else {
        $moderation = 0;
    }

    $nom = null;
    switch ($CFG->jitsi_id) {
        case 'username':
            $nom = $USER->username;
            break;
        case 'nameandsurname':
            $nom = $USER->firstname.' '.$USER->lastname;
            break;
        case 'alias':
            break;
    }
    $sessionoptionsparam = ['$course->shortname', '$jitsi->id', '$jitsi->name'];
    $fieldssessionname = $CFG->jitsi_sesionname;

    $allowed = explode(',', $fieldssessionname);
    $max = count($allowed);

    if ($fromuserid) {
        $sesparam = $SITE->shortname.'-'.$user->username.'-'.$fromuserid;
    } else {
        $sesparam = $SITE->shortname.'-'.$user->username.'-'.$USER->id;
    }
    $avatar = $CFG->wwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';

    $urlparams = array('avatar' => $avatar, 'nom' => $nom, 'ses' => $sesparam,
        't' => $moderation, 'u' => $userid);

    if ($USER->id != $user->id) {
        echo "<div class=\"alert alert-warning\" role=\"alert\">";
        echo get_string('warningprivate', 'jitsi', $user->firstname);
        echo "</div>";
    }

    echo $OUTPUT->box(get_string('instruction', 'jitsi'));
    echo $OUTPUT->single_button(new moodle_url('/mod/jitsi/sessionpriv.php', $urlparams), get_string('access', 'jitsi'), 'post');

    echo "<p></p>";
    echo $CFG->jitsi_help;

} else {
    echo get_string('privatesessiondisabled', 'jitsi');
}

echo $OUTPUT->footer();
