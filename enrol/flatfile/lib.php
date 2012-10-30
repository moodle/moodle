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
 * Flatfile enrolment plugin.
 *
 * This plugin lets the user specify a "flatfile" (CSV) containing enrolment information.
 * On a regular cron cycle, the specified file is parsed and then deleted.
 *
 * @package    enrol
 * @subpackage flatfile
 * @copyright  2010 Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Flatfile enrolment plugin implementation.
 * @author  Eugene Venter - based on code by Petr Skoda, Martin Dougiamas, Martin Langhoff and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_flatfile_plugin extends enrol_plugin {

    /**
     * Override the base cron() function to read in a file
     *
     * Comma separated file assumed to have four or six fields per line:
     *   operation, role, idnumber(user), idnumber(course) [, starttime, endtime]
     * where:
     *   operation        = add | del
     *   role             = student | teacher | teacheredit
     *   idnumber(user)   = idnumber in the user table NB not id
     *   idnumber(course) = idnumber in the course table NB not id
     *   starttime        = start time (in seconds since epoch) - optional
     *   endtime          = end time (in seconds since epoch) - optional
     */
    private $log;

    public function cron() {
        $this->process_file();

        $this->process_buffer();

        echo $this->log;
    } // end of function

    protected function process_file() {
        global $CFG, $DB;

        $filelocation = $this->get_config('location');
        $mailadmins   = $this->get_config('mailadmins');
        if (empty($filelocation)) {
            $filename = "$CFG->dataroot/1/enrolments.txt";  // Default location
        } else {
            $filename = $filelocation;
        }

        if ( file_exists($filename) ) {
            $this->log  = userdate(time()) . "\n";
            $this->log .= "Flatfile enrol cron found file: $filename\n\n";

            if (($fh = fopen($filename, "r")) != false) {

                list($roles, $rolemap) = $this->get_roles();

                $line = 0;
                while (!feof($fh)) {

                    $line++;
                    $fields = explode( ",", str_replace( "\r", "", fgets($fh) ) );

                /// If a line is incorrectly formatted ie does not have 4 comma separated fields then ignore it
                    if (count($fields) != 4 and count($fields) !=6) {
                        if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
                            $this->log .= "$line: Line incorrectly formatted - ignoring\n";
                        }
                        continue;
                    }

                    $fields[0] = trim(strtolower($fields[0]));
                    $fields[1] = trim(strtolower($fields[1]));
                    $fields[2] = trim($fields[2]);
                    $fields[3] = trim($fields[3]);

                    $this->log .= "$line: $fields[0] $fields[1] $fields[2] $fields[3] ";

                    if (!empty($fields[5])) {
                        $fields[4] = (int)trim($fields[4]);
                        $fields[5] = (int)trim($fields[5]);
                        $this->log .= "$fields[4] $fields[5]";
                    } else {
                        $fields[4] = 0;
                        $fields[5] = 0;
                    }

                    $this->log .= ":";

                /// check correct formatting of operation field
                    if ($fields[0] != "add" and $fields[0] != "del") {
                        $this->log .= "Unknown operation in field 1 - ignoring line\n";
                        continue;
                    }

                /// check correct formatting of role field
                    if (!isset($rolemap[$fields[1]]) && !isset($roles[$fields[1]])) {
                        $this->log .= "Unknown role in field2 - ignoring line\n";
                        continue;
                    }

                    if (! $user = $DB->get_record("user", array("idnumber"=>$fields[2]))) {
                        $this->log .= "Unknown user idnumber in field 3 - ignoring line\n";
                        continue;
                    }

                    if (! $course = $DB->get_record("course", array("idnumber"=>$fields[3]))) {
                        $this->log .= "Unknown course idnumber in field 4 - ignoring line\n";
                        continue;
                    }

                    // Either field[1] is a name that appears in the mapping,
                    // or it's an actual short name. It has to be one or the
                    // other, or we don't get to this point.
                    $roleid = isset($rolemap[$fields[1]]) ? $roles[$rolemap[$fields[1]]] : $roles[$fields[1]];

                    if ($fields[4] > $fields[5]) {
                        $this->log .= "Start time was later than end time - ignoring line\n";
                        continue;
                    }

                    $this->process_records($fields[0],$roleid,$user,$course,$fields[4],$fields[5]);

                 } // end of while loop

            fclose($fh);
            } // end of if(file_open)

            if(! @unlink($filename)) {
                $eventdata = new stdClass();
                $eventdata->modulename        = 'moodle';
                $eventdata->component         = 'enrol_flatfile';
                $eventdata->name              = 'flatfile_enrolment';
                $eventdata->userfrom          = get_admin();
                $eventdata->userto            = get_admin();
                $eventdata->subject           = get_string("filelockedmailsubject", "enrol_flatfile");
                $eventdata->fullmessage       = get_string("filelockedmail", "enrol_flatfile", $filename);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
                $this->log .= "Error unlinking file $filename\n";
            }

            if (!empty($mailadmins)) {

                // Send mail to admin
                $eventdata = new stdClass();
                $eventdata->modulename        = 'moodle';
                $eventdata->component         = 'enrol_flatfile';
                $eventdata->name              = 'flatfile_enrolment';
                $eventdata->userfrom          = get_admin();
                $eventdata->userto            = get_admin();
                $eventdata->subject           = "Flatfile Enrolment Log";
                $eventdata->fullmessage       = $this->log;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }

        } // end of if(file_exists)

    } // end of function

    protected function process_buffer() {
        global $DB;
        // get records from enrol_flatfile table and process any records that are due.
        if ($future_enrols = $DB->get_records('enrol_flatfile', null, '')) {
            foreach($future_enrols as $id => $future_en) {
                    $this->log .= "Processing buffered enrolments.\n";
                    $user = $DB->get_record("user", array("id"=>$future_en->userid));
                    $course = $DB->get_record("course", array("id"=>$future_en->courseid));
                    // enrol the person.
                    if($this->process_records($future_en->action, $future_en->roleid,
                            $user, $course, $future_en->timestart, $future_en->timeend, false)) {
                        //ok record went thru, get rid of the record.
                        $DB->delete_records('enrol_flatfile', array('id'=>$future_en->id));
                    }
            }
        }
    }

    private function process_records($action, $roleid, $user, $course, $timestart, $timeend, $store_to_buffer = true) {
        global $CFG, $DB;

        $mailstudents = $this->get_config('mailstudents');
        $mailteachers = $this->get_config('mailteachers');

        // check if timestart is for future processing.
        if ($timestart > time()) {
            if ($store_to_buffer) {
                // populate into enrol_flatfile table as a future role to be assigned by cron.
                $future_en = new stdClass();
                $future_en->action = $action;
                $future_en->roleid = $roleid;
                $future_en->userid = $user->id;
                $future_en->courseid = $course->id;
                $future_en->timestart = $timestart;
                $future_en->timeend     = $timeend;
                $future_en->timemodified  = time();
                $future_en->id = $DB->insert_record('enrol_flatfile', $future_en);
            }
            return false;
        }

        unset($elog);

        // Create/resurrect a context object
        $context = context_course::instance($course->id);

        if ($action == 'add') {
            $instance = $DB->get_record('enrol',
                            array('courseid' => $course->id, 'enrol' => 'flatfile'));
            if (empty($instance)) {
                // Only add an enrol instance to the course if non-existent
                $enrolid = $this->add_instance($course);
                $instance = $DB->get_record('enrol', array('id' => $enrolid));
            }
            // Enrol the user with this plugin instance
            $this->enrol_user($instance, $user->id, $roleid, $timestart, $timeend);
        } else {
            $instances = $DB->get_records('enrol',
                            array('enrol' => 'flatfile', 'courseid' => $course->id));
            foreach ($instances as $instance) {
                // Unenrol the user from all flatfile enrolment instances
                $this->unenrol_user($instance, $user->id);
            }
        }


        if ( empty($elog) and ($action== "add") ) {
            $role = $DB->get_record("role", array("id"=>$roleid));

            if ($role->archetype == "student") {

                // TODO: replace this with check for $CFG->couremanager, 'moodle/course:update' is definitely wrong
                if ($teachers = get_users_by_capability($context, 'moodle/course:update', 'u.*')) {
                    foreach ($teachers as $u) {
                        $teacher = $u;
                    }
                }

                if (!isset($teacher)) {
                    $teacher = get_admin();
                }
            } else {
                $teacher = get_admin();
            }


            if (!empty($mailstudents)) {
                // Send mail to students
                $a = new stdClass();
                $a->coursename = format_string($course->fullname, true, array('context' => $context));
                $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id";
                $subject = get_string("enrolmentnew", 'enrol', format_string($course->shortname, true, array('context' => $context)));

                $eventdata = new stdClass();
                $eventdata->modulename        = 'moodle';
                $eventdata->component         = 'enrol_flatfile';
                $eventdata->name              = 'flatfile_enrolment';
                $eventdata->userfrom          = $teacher;
                $eventdata->userto            = $user;
                $eventdata->subject           = $subject;
                $eventdata->fullmessage       = get_string('welcometocoursetext', '', $a);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }

            if (!empty($mailteachers) && $teachers) {

                // Send mail to teachers
                foreach($teachers as $teacher) {
                    $a = new stdClass();
                    $a->course = format_string($course->fullname, true, array('context' => $context));
                    $a->user = fullname($user);
                    $subject = get_string("enrolmentnew", 'enrol', format_string($course->shortname, true, array('context' => $context)));

                    $eventdata = new stdClass();
                    $eventdata->modulename        = 'moodle';
                    $eventdata->component         = 'enrol_flatfile';
                    $eventdata->name              = 'flatfile_enrolment';
                    $eventdata->userfrom          = $user;
                    $eventdata->userto            = $teacher;
                    $eventdata->subject           = $subject;
                    $eventdata->fullmessage       = get_string('enrolmentnewuser', 'enrol', $a);
                    $eventdata->fullmessageformat = FORMAT_PLAIN;
                    $eventdata->fullmessagehtml   = '';
                    $eventdata->smallmessage      = '';
                    message_send($eventdata);
                }
            }
        }


        if (empty($elog)) {
            $elog = "OK\n";
        }
        $this->log .= $elog;

        return true;
    }

    /**
     * Returns a pair of arrays.  The first is the set of roleids, indexed by
     * their shortnames.  The second is the set of shortnames that have
     * mappings, indexed by those mappings.
     *
     * @return array ($roles, $rolemap)
     */
    function get_roles() {
        global $DB;

        // Get all roles
        $roles = $DB->get_records('role', null, '', 'id, name, shortname');

        $config = get_config('enrol_flatfile');

        // Set some usable mapping configs for later
        foreach($roles as $id => $role) {
            if (isset($config->{"map_{$id}"})) {
                set_config('map_'.$role->shortname, $config->{"map_{$id}"}, 'enrol_flatfile');
            } else {
                set_config('map_'.$role->shortname, $role->shortname, 'enrol_flatfile');
            }
        }
        // Get the updated config
        $config = get_config('enrol_flatfile');
        // Get a list of all the roles in the database, indexed by their short names.
        $roles = $DB->get_records('role', null, '', 'shortname, id');

        // Get any name mappings. These will be of the form 'map_shortname' => 'flatfilename'.
        array_walk($roles, create_function('&$value', '$value = $value->id;'));
        $rolemap = array();
        foreach($config as $name => $value) {
            if (strpos($name, 'map_') === 0 && isset($roles[$key = substr($name, 4)])) {
                $rolemap[$value] = $key;
            }
        }

        return array($roles, $rolemap);
    }

} // end of class
