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
 * @copyright  2021 Sergio Comerón <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/moodlelib.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");
global $DB;

$token = required_param('t', PARAM_TEXT);

$sql = "select * from {jitsi} where token = '".$token."'";
$jitsi = $DB->get_record_sql($sql);
$module = $DB->get_record ('modules', array('name' => 'jitsi'));
$cm = $DB->get_record ('course_modules', array('instance' => $jitsi->id, 'module' => $module->id));
$id = $cm->id;

$sessionid = $cm->instance;

/**
 * Access form for name.
 */
class name_form extends moodleform {
    /**
     * Define the form
     */
    public function definition() {
        global $CFG, $USER;

        $mform = $this->_form;
        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', '', 'client', false, false);

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('continue'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    /**
     * Validate the form data
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        return array();
    }
}

$PAGE->set_url($CFG->wwwroot.'/mod/jitsi/formuniversal.php');
$sesion = $DB->get_record('jitsi', array('id' => $sessionid));
$PAGE->set_cm($cm);
$PAGE->set_context(context_module::instance($id));

$PAGE->set_title(get_string('accesstotitle', 'jitsi', $sesion->name));
$PAGE->set_heading(get_string('accesstotitle', 'jitsi', $sesion->name));

echo $OUTPUT->header();

if ($jitsi->intro) {
    echo $jitsi->intro;
}

$event = \mod_jitsi\event\jitsi_session_guest_form::create(array(
  'objectid' => $PAGE->cm->instance,
  'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $sesion);

$event->trigger();
if (!istimedout($sesion)) {
    if ($CFG->jitsi_invitebuttons == 1) {
        if (!isloggedin()) {
            echo get_string('accessto', 'jitsi', $sesion->name);
            $today = getdate();
            if ($today[0] < $sesion->timeclose || $sesion->timeclose == 0) {
                if ($today[0] > (($sesion->timeopen) - ($sesion->minpretime * 60))) {
                    $urlparamsform = array('ses' => $sessionid, 'id' => $id);
                    $urlform = new moodle_url('/mod/jitsi/universal.php', $urlparamsform);
                    $mform = new name_form($urlform);
                    if ($mform->is_cancelled()) {
                        echo "";
                    } else if ($fromform = $mform->get_data()) {
                        echo "";
                    } else {
                        $mform->display();
                    }
                } else {
                    echo $OUTPUT->box(get_string('nostart', 'jitsi',
                        date("d-m-Y H:i", ($sesion->timeopen - ($sesion->minpretime * 60)))));
                }
            } else {
                echo $OUTPUT->box(get_string('finish', 'jitsi'));
            }
        } else {
            echo get_string('accesstowithlogin', 'jitsi', $sesion->name);
            $today = getdate();
            if ($today[0] > (($sesion->timeopen) - ($sesion->minpretime * 60))) {
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
                $avatar = $CFG->wwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';
                $mail = '';
                $urlparams = array('avatar' => $avatar, 'name' => $nom, 'ses' => $sessionid,
                                    'mail' => $mail, 'id' => $id);
                echo $OUTPUT->box(get_string('instruction', 'jitsi'));
                echo $OUTPUT->single_button(new moodle_url('/mod/jitsi/universal.php', $urlparams),
                    get_string('access', 'jitsi'), 'post');
            } else {
                echo $OUTPUT->box(get_string('nostart', 'jitsi',
                        date("d-m-Y H:i", ($sesion->timeopen - ($sesion->minpretime * 60)))));
            }
        }
    } else {
        echo get_string('noinviteaccess', 'jitsi');
    }
} else {
    echo "<div class=\"alert alert-danger\" role=\"alert\">";
    echo generateerrortime($sesion);
    echo "</div>";
}
echo '<p></p>';
echo $CFG->jitsi_help;

echo $OUTPUT->footer();
