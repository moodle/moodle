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
 * Local plugin "bulkenrol" - Local Library
 *
 * @package   local_bulkenrol
 * @copyright 2017 Soon Systems GmbH on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('LOCALBULKENROL_HINT', 'hint');
define('LOCALBULKENROL_ENROLUSERS', 'enrolusers');
define('LOCALBULKENROL_GROUPINFOS', 'groupinfos');

/**
 * Check list of submitted user mails and creates a data structure for displaying information on the confirm page and
 * for performing the bulkenrol.
 *
 * @param string $emailstextfield Text field value to be checked for emails and course groups.
 * @param int $courseid ID of the course, used to determine the context for checking whether a user is already enroled.
 *
 * @return stdClass Object containing information to be displayed on confirm page and being used for bulkenrol.
 */
function local_bulkenrol_check_user_mails($emailstextfield, $courseid) {

    $checkedemails = new stdClass();
    $checkedemails->emails_to_ignore = [];
    $checkedemails->error_messages = [];
    $checkedemails->moodleusers_for_email = [];
    $checkedemails->course_groups = [];
    $checkedemails->user_groups = [];
    $checkedemails->user_enroled = [];
    $checkedemails->validemailfound = 0;

    $emaildelimiters = [', ', ' ', ','];

    if (!empty($emailstextfield)) {

        $emailslines = local_bulkenrol_parse_emails($emailstextfield);

        $linecnt = 0;

        $currentgroup = '';

        $context = null;

        if (!empty($courseid)) {
            $context = context_course::instance($courseid, MUST_EXIST);
        }

        // Process emails from textfield.
        foreach ($emailslines as $emailline) {
            $linecnt++;

            $error = '';

            $emailline = trim($emailline);

            // Check for course group.
            $grouppos = strpos($emailline , '#');
            if ($grouppos !== false) {

                $groupname = substr($emailline, $grouppos + 1);
                $currentgroup = trim($groupname);
                $checkedemails->course_groups[$currentgroup] = [];
                continue;
            }

            // Check number of emails in current row/line.
            $emailsinlinecnt = substr_count($emailline , '@');

            // No email in row/line.
            if ($emailsinlinecnt == 0) {

                $a = new stdClass();
                $a->line = $linecnt;
                $a->content = $emailline;
                if (trim($a->content != "")) {
                    $error = get_string('error_no_email', 'local_bulkenrol', $a);
                } else {
                    $error = get_string('error_empty_line', 'local_bulkenrol', $a);
                }
                $checkedemails->error_messages[$linecnt] = $error;

                // One email in row/line.
            } else if ($emailsinlinecnt == 1) {
                $email = $emailline;
                local_bulkenrol_check_email($email, $linecnt, $courseid, $context, $currentgroup, $checkedemails);
            }
            // More than one email in row/line.
            if ($emailsinlinecnt > 1) {
                $delimiter = '';

                // Check delimiters.
                foreach ($emaildelimiters as $emaildelimiter) {
                    $pos = strpos($emailline, $emaildelimiter);
                    if ($pos) {
                        $delimiter = $emaildelimiter;
                        break;
                    }
                }
                if (!empty($delimiter)) {
                    $emailsinline = explode($delimiter, $emailline);

                    // Iterate emails in row/line.
                    foreach ($emailsinline as $emailinline) {

                        $email = trim($emailinline);
                        local_bulkenrol_check_email($email, $linecnt, $courseid, $context, $currentgroup, $checkedemails);
                    }
                }
            }
        }
    }

    return $checkedemails;
}

/**
 *
 * Check submitted email, working on the $checkedemails array
 *
 * @param string $email e-mail of the user that should be enroled
 * @param int $linecnt line counter used for error messages
 * @param int $courseid course id
 * @param context_course $context context instance of the course the user should be enroled into
 * @param string $currentgroup name of the group a user should be added to as member
 * @param object $checkedemails Object containing information to be displayed on confirm page and being used for bulkenrol.
 */
function local_bulkenrol_check_email($email, $linecnt, $courseid, $context, $currentgroup, &$checkedemails) {
    // Check for valid email.
    $emailisvalid = validate_email($email);
    // Email is not valid.
    if (!$emailisvalid) {
        $checkedemails->emails_to_ignore[] = $email;
        $a = new stdClass();
        $a->row = $linecnt;
        $a->email = $email;
        $error = get_string('error_invalid_email', 'local_bulkenrol', $a);
        if (array_key_exists($linecnt, $checkedemails->error_messages)) {
            $errors = $checkedemails->error_messages[$linecnt];
            $errors .= "<br>".$error;
            $checkedemails->error_messages[$linecnt] = $errors;
        } else {
            $checkedemails->error_messages[$linecnt] = $error;
        }

        // Email is valid.
    } else {
        // Check for moodle user with email.
        list($error, $userrecord) = local_bulkenrol_get_user($email);
        if (!empty($error)) {
            $checkedemails->emails_to_ignore[] = $email;
            if (array_key_exists($linecnt, $checkedemails->error_messages)) {
                $errors = $checkedemails->error_messages[$linecnt];
                $errors .= "<br>".$error;
                $checkedemails->error_messages[$linecnt] = $errors;
            } else {
                $checkedemails->error_messages[$linecnt] = $error;
            }
        } else if (!empty($userrecord) && !empty($userrecord->id)) {
            $checkedemails->validemailfound += 1;

            $useralreadyenroled = false;
            if (!empty($context) && !empty($userrecord)) {
                $useralreadyenroled = is_enrolled($context, $userrecord->id);
            }
            $checkedemails->moodleusers_for_email[$email] = $userrecord;
            if (empty($useralreadyenroled)) {
                $checkedemails->user_enroled[$email] = $userrecord;
            }
            if (!empty($currentgroup) && array_key_exists($currentgroup, $checkedemails->course_groups)) {
                $checkedemails->course_groups[$currentgroup][] = $userrecord;
            }
            if (!array_key_exists($email, $checkedemails->user_groups)) {
                $checkedemails->user_groups[$email] = [];
            }
            if (!empty($currentgroup) && !array_key_exists($currentgroup, $checkedemails->user_groups[$email])) {
                // Check if user is already member of the group.
                $result = local_bulkenrol_is_already_member($courseid, $currentgroup, $userrecord->id);
                if (!empty($result->error)) {
                    $a = new stdClass();
                    $a->row = $linecnt;
                    $a->email = $email;
                    $a->groupname = $currentgroup;
                    $a->error = $result->error;
                    $error = get_string('error_check_is_already_member', 'local_bulkenrol', $a);
                    $checkedemails->error_messages[$linecnt] = $error;
                }
                $alreadymember = $result->already_member;
                // Compose group information.
                if (empty($alreadymember)) {
                    $groupinfo = html_writer::tag('span',
                            get_string('user_groups_yes', 'local_bulkenrol'),
                            ['class' => 'badge badge-secondary']);
                } else {
                    $groupinfo = html_writer::tag('span',
                            get_string('user_groups_already', 'local_bulkenrol'),
                            ['class' => 'badge badge-success']);
                }
                $checkedemails->user_groups[$email][] = $currentgroup .': '. $groupinfo;
            }
        }
    }
}

/**
 * Takes input from text area containing a list of e-mail adresses (optionally group names starting with '#').
 * Returns an array representation of the input.
 *
 * @param mixed $emails input value of the text area.
 * @return string[] of e-emails and optional group names
 */
function local_bulkenrol_parse_emails($emails) {
    if (empty($emails)) {
        return [];
    } else {
        $rawlines = explode(PHP_EOL, $emails);
        $result = [];
        foreach ($rawlines as $rawline) {
            $result[] = trim($rawline);
        }
        return $result;
    }
}

/**
 * Takes an e-mail and returns a moodle user record and error string (if occured).
 *
 * @param string $email E-mail used to search for a user
 * @return string,object[]
 */
function local_bulkenrol_get_user($email) {
    global $DB;

    $error = null;
    $userrecord = null;

    if (empty($email)) {
        return [$error, $userrecord];
    } else {
        // Get user records for email.
        try {
            $userrecords = $DB->get_records('user', ['email' => $email]);
            $count = count($userrecords);
            if (!empty($count)) {
                // More than one user with email -> ignore email and don't enrol users later!
                if ($count > 1) {
                    $error = get_string('error_more_than_one_record_for_email', 'local_bulkenrol', $email);
                } else {
                    $userrecord = current($userrecords);
                }
            } else {
                $error = get_string('error_no_record_found_for_email', 'local_bulkenrol', $email);
            }
        } catch (Exception $e) {
            $error = get_string('error_getting_user_for_email', 'local_bulkenrol', $email).local_bulkenrol_get_exception_info($e);
        }

        return [$error, $userrecord];
    }
}

/**
 * Get an understandable reason from an exception which happened during bulkenrol.
 *
 * @param object $e should be of instanceof Exception
 * @return string readable form of an exception
 */
function local_bulkenrol_get_exception_info($e) {
    if (empty($e) || !($e instanceof Exception) ) {
        return '';
    }

    return " ".get_string('error_exception_info', 'local_bulkenrol').": ".$e->getMessage()." -> ".$e->getTraceAsString();
}

/**
 * Perform user enrolment into the course and optionally add users as member into course groups. Groups are created if necessary.
 *
 * @param string $localbulkenrolkey
 * @return object
 */
function local_bulkenrol_users($localbulkenrolkey) {
    global $CFG, $DB, $SESSION;

    $error = '';
    $exceptionsmsg = [];

    if (!empty($localbulkenrolkey)) {
        if (!empty($localbulkenrolkey) && !empty($SESSION->local_bulkenrol) &&
                array_key_exists($localbulkenrolkey, $SESSION->local_bulkenrol)) {
            $localbulkenroldata = $SESSION->local_bulkenrol[$localbulkenrolkey];
            if (!empty($localbulkenroldata)) {
                $error = '';

                $courseid = 0;

                $tmpdata = explode('_', $localbulkenrolkey);
                if (!empty($tmpdata)) {
                    $courseid = $tmpdata[0];
                }

                $userstoenrol = $localbulkenroldata->moodleusers_for_email;

                if (!empty($courseid) && !empty($userstoenrol)) {
                    try {
                        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

                        $enrolinstances = enrol_get_instances($course->id, false);

                        // Get enrolment for bulkenrol.
                        $bulkenrolplugin = get_config('local_bulkenrol', 'enrolplugin');

                        // Check if string contains "enrol_".
                        if (strpos($bulkenrolplugin, 'enrol_') === 0) {
                            // This is needed because enrol_get_plugin needs the string without the "enrol_".
                            $bulkenrolplugin = substr($bulkenrolplugin, 6);
                        }

                        $plugin = enrol_get_plugin($bulkenrolplugin);

                        $enrolinstance = null;

                        foreach ($enrolinstances as $instance) {
                            // Check enrolment.
                            if ($bulkenrolplugin == $instance->enrol) {
                                if ($instance->status != ENROL_INSTANCE_ENABLED) {
                                    $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
                                }
                                $enrolinstance = $instance;
                                break;
                            }
                        }

                        if (empty($enrolinstance)) {
                            $fields = $plugin->get_instance_defaults();
                            $id = $plugin->add_instance($course, $fields);

                            $enrolinstance = $DB->get_record('enrol', ['id' => $id]);
                            $enrolinstance->expirynotify = $plugin->get_config('expirynotify');
                            $enrolinstance->expirythreshold = $plugin->get_config('expirythreshold');
                            $enrolinstance->roleid = $plugin->get_config('roleid');
                            $enrolinstance->timemodified = time();
                            $DB->update_record('enrol', $enrolinstance);
                        }

                        if (!empty($enrolinstance)) {
                            // Enrol users in course.
                            $roleid = get_config('local_bulkenrol', 'role');

                            // Get the course context.
                            $coursecontext = context_course::instance($courseid);

                            foreach ($userstoenrol as $user) {
                                try {
                                    // Check if user is already enrolled with another enrolment method.
                                    $userisenrolled = is_enrolled($coursecontext, $user->id, '', false);

                                    // If the user is already enrolled, continue to avoid a second enrolment for the user.
                                    if ($userisenrolled) {
                                        continue;

                                        // Otherwise.
                                    } else {
                                        $plugin->enrol_user($enrolinstance, $user->id, $roleid);
                                    }
                                } catch (Exception $e) {
                                    $a = new stdClass();
                                    $a->email = $user->email;

                                    $msg = get_string('error_enrol_user', 'local_bulkenrol', $a).
                                            local_bulkenrol_get_exception_info($e);
                                    $exceptionsmsg[] = $msg;
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $msg = get_string('error_enrol_users', 'local_bulkenrol').local_bulkenrol_get_exception_info($e);
                        $exceptionsmsg[] = $msg;
                    }

                    // Check for course groups to create.
                    $groups = $localbulkenroldata->course_groups;

                    if (!empty($groups)) {

                        try {
                            require_once($CFG->dirroot . '/group/lib.php');

                            $existingcoursegroups = groups_get_all_groups($courseid);

                            foreach ($groups as $name => $members) {
                                $groupname = trim($name);

                                // Check if group already exists.
                                $groupid = null;
                                foreach ($existingcoursegroups as $key => $existingcoursegroup) {
                                    if ($groupname == $existingcoursegroup->name) {
                                        $groupid = $existingcoursegroup->id;
                                        break;
                                    }
                                }
                                // Group not found in course -> create new course group.
                                if (empty($groupid)) {
                                    $groupdata = new stdClass();
                                    $groupdata->courseid = $courseid;
                                    $groupdata->name = $groupname;
                                    $groupid = groups_create_group($groupdata, false, false);
                                }
                                if (!empty($groupid) && !empty($members)) {
                                    foreach ($members as $key => $member) {
                                        try {
                                            $useradded = groups_add_member($groupid, $member->id);

                                            if (empty($useradded)) {
                                                $a = new stdClass();
                                                $a->email = $member->email;
                                                $a->group = $groupname;
                                                $msg = get_string('error_group_add_member', 'local_bulkenrol', $a);
                                                $exceptionsmsg[] = $msg;
                                            }
                                        } catch (Exception $e) {
                                            $a = new stdClass();
                                            $a->email = $member->email;
                                            $a->group = $groupname;
                                            $msg = get_string('error_group_add_member', 'local_bulkenrol', $a).
                                                    local_bulkenrol_get_exception_info($e);
                                            $exceptionsmsg[] = $msg;
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            $msg = get_string('error_group_add_members', 'local_bulkenrol').local_bulkenrol_get_exception_info($e);
                            $exceptionsmsg[] = $msg;
                        }
                    }
                }
            }
        }
    }

    $retval = new stdClass();
    $retval->status = '';
    $retval->text = '';

    if (!empty($error) || !empty($exceptionsmsg)) {
        $retval->status = 'error';

        if (!empty($error)) {
            $msg = get_string($error, 'local_bulkenrol');
            $retval->text = $msg;
        }

        if (!empty($exceptionsmsg)) {
            if (!empty($error)) {
                $retval->text .= '<br>';
            }
            $retval->text .= implode('<br>', $exceptionsmsg);
        }
    } else {
        $retval->status = 'success';
        $msg = get_string('enrol_users_successful', 'local_bulkenrol');
        $retval->text = $msg;
    }

    return $retval;
}

/**
 * According to the parameter, either a table with hints is displayed or a table with users to be written is displayed.
 *
 * @param object $localbulkenroldata
 * @param string $key
 */
function local_bulkenrol_display_table($localbulkenroldata, $key) {
    global $OUTPUT;

    if (!empty($localbulkenroldata) && !empty($key)) {

        switch ($key) {
            case LOCALBULKENROL_HINT:

                $data = [];

                if (!empty($localbulkenroldata->error_messages)) {
                    foreach ($localbulkenroldata->error_messages as $line => $errormessages) {
                        $row = [];

                        $cell = new html_table_cell();
                        $cell->text = $line;
                        $row[] = $cell;

                        $cell = new html_table_cell();
                        $cell->text = $errormessages;
                        $row[] = $cell;

                        $data[] = $row;
                    }
                }

                $table = new html_table();
                $table->id = "localbulkenrol_hints";
                $table->attributes['class'] = 'generaltable';
                $table->summary = get_string('hints', 'local_bulkenrol');
                $table->size = ['10%', '90%'];
                $table->head = [];
                $table->head[] = get_string('row', 'local_bulkenrol');
                $table->head[] = get_string('hints', 'local_bulkenrol');
                $table->data = $data;

                if (!empty($data)) {
                    echo $OUTPUT->heading(get_string('hints', 'local_bulkenrol'), 3);
                    echo html_writer::tag('div', html_writer::table($table), ['class' => 'flexible-wrap']);
                }

                break;

            case LOCALBULKENROL_ENROLUSERS:
                $data = [];

                if (!empty($localbulkenroldata->moodleusers_for_email)) {
                    foreach ($localbulkenroldata->moodleusers_for_email as $email => $user) {
                        $row = [];

                        $cell = new html_table_cell();
                        $cell->text = $user->email;
                        $row[] = $cell;

                        $cell = new html_table_cell();
                        $cell->text = $user->firstname;
                        $row[] = $cell;

                        $cell = new html_table_cell();
                        $cell->text = $user->lastname;
                        $row[] = $cell;

                        $cell = new html_table_cell();
                        $cell->text = '';
                        if (!empty($localbulkenroldata->user_enroled[$email])) {
                            $cell->text = html_writer::tag('span',
                                get_string('user_enroled_yes', 'local_bulkenrol'),
                                ['class' => 'badge badge-secondary']);
                        } else {
                            $cell->text = html_writer::tag('span',
                                get_string('user_enroled_already', 'local_bulkenrol'),
                                ['class' => 'badge badge-secondary']);
                        }
                        $row[] = $cell;

                        $cell = new html_table_cell();
                        $cell->text = '';
                        if (!empty($localbulkenroldata->user_groups[$email])) {
                            $cell->text = implode(',<br />', $localbulkenroldata->user_groups[$email]);
                        }
                        $row[] = $cell;

                        $data[] = $row;
                    }
                }

                $table = new html_table();
                $table->id = "localbulkenrol_enrolusers";
                $table->attributes['class'] = 'generaltable';
                $table->summary = get_string('users_to_enrol_in_course', 'local_bulkenrol');
                $table->size = ['20%', '17%', '17%', '20%', '26%'];
                $table->head = [];
                $table->head[] = get_string('email');
                $table->head[] = get_string('firstname');
                $table->head[] = get_string('lastname');
                $table->head[] = get_string('user_enroled', 'local_bulkenrol');
                $table->head[] = get_string('user_groups', 'local_bulkenrol');
                $table->data = $data;

                if (!empty($data)) {
                    echo $OUTPUT->heading(get_string('users_to_enrol_in_course', 'local_bulkenrol'), 3);
                    echo html_writer::tag('div', html_writer::table($table), ['class' => 'flexible-wrap']);
                }
                break;

            case LOCALBULKENROL_GROUPINFOS:
                $data = [];

                if (!empty($localbulkenroldata->course_groups)) {
                    $courseid = required_param('id', PARAM_INT);
                    $existingcoursegroups = groups_get_all_groups($courseid, 0, 0, 'id, name');

                    foreach ($localbulkenroldata->course_groups as $name => $members) {
                        $groupname = trim($name);

                        // Check if group already exists.
                        $groupexists = false;
                        foreach ($existingcoursegroups as $key => $existingcoursegroup) {
                            if ($groupname == $existingcoursegroup->name) {
                                $groupexists = true;
                                break;
                            }
                        }

                        $row = [];

                        $cell = new html_table_cell();
                        $cell->text = $groupname;
                        $row[] = $cell;

                        $cell = new html_table_cell();
                        if (empty($groupexists)) {
                            $cell->text = html_writer::tag('span',
                                get_string('group_status_create', 'local_bulkenrol'),
                                ['class' => 'badge badge-secondary']);
                        } else {
                            $cell->text = html_writer::tag('span',
                                get_string('group_status_exists', 'local_bulkenrol'),
                                ['class' => 'badge badge-success']);
                        }

                        $row[] = $cell;

                        $data[] = $row;
                    }
                }

                $table = new html_table();
                $table->id = "localbulkenrol_groupinfos";
                $table->attributes['class'] = 'generaltable';
                $table->size = ['50%', '50%'];
                $table->head = [];
                $table->head[] = get_string('group_name_headline', 'local_bulkenrol');
                $table->head[] = get_string('group_status_headline', 'local_bulkenrol');
                $table->data = $data;

                if (!empty($data)) {
                    echo $OUTPUT->heading(get_string('groupinfos_headline', 'local_bulkenrol'), 3);
                    echo html_writer::tag('div', html_writer::table($table), ['class' => 'flexible-wrap']);
                }
                break;

            default:
            break;
        }
    }
}


/**
 * Checks whether a user with id $userid can be found in members list of the course group with name $groupname.
 *
 * @param int $courseid id of the course
 * @param string $groupname
 * @param int $userid id of the user
 * @return mixed Data structure containing flag 'already_member' and 'error'.
 */
function local_bulkenrol_is_already_member($courseid, $groupname, $userid) {
    global $CFG;

    $result = new stdClass();
    $result->already_member = null;
    $result->error = '';

    if (empty($courseid) || empty($groupname) || empty($userid)) {
        $result->error = get_string('parameter_empty', 'local_bulkenrol');
        return $result;
    }

    // Get group by groupname.
    try {
        require_once($CFG->dirroot . '/group/lib.php');

        $existingcoursegroups = groups_get_all_groups($courseid);

        $groupname = trim($groupname);

        // Check if group already exists.
        $groupid = null;
        foreach ($existingcoursegroups as $key => $existingcoursegroup) {
            if ($groupname == $existingcoursegroup->name) {
                $groupid = $existingcoursegroup->id;
                break;
            }
        }
        // Group not found in course -> group has to be created, user is not a member.
        if (empty($groupid)) {
            return $result;
        }

        if (!empty($groupid)) {
            $ismember = groups_is_member($groupid, $userid);
            if ($ismember) {
                $result->already_member = $ismember;
            }
        }
    } catch (Exception $e) {
        $msg = get_string('error_group_add_members', 'local_bulkenrol').local_bulkenrol_get_exception_info($e);
        $exceptionsmsg[] = $msg;
    }
    return $result;
}

/**
 * Helper function to show the enrolment details about the upcoming enrolments.
 */
function local_bulkenrol_display_enroldetails() {
    global $DB, $OUTPUT;

    // Get enrolment configuration.
    $enrolpluginshortname = get_config('local_bulkenrol', 'enrolplugin');
    $enrolpluginname = get_string('pluginname', 'enrol_' . $enrolpluginshortname);

    // Get role configuration.
    $roleid = get_config('local_bulkenrol', 'role');
    $role = $DB->get_record('role', ['id' => $roleid]);
    $systemcontext = context_system::instance();
    $roles = role_fix_names([$roleid => $role], $systemcontext, ROLENAME_ORIGINAL);
    $rolename = $roles[$roleid]->localname;

    // Build enrolment details table.
    $data = [];
    $row = [];
    $cell = new html_table_cell();
    $cell->text = $enrolpluginname;
    $row[] = $cell;

    $cell = new html_table_cell();
    $cell->text = $rolename;
    $row[] = $cell;

    $data[] = $row;

    $table = new html_table();
    $table->id = "localbulkenrol_enrolinfo";
    $table->attributes['class'] = 'generaltable';
    $table->size = ['50%', '50%'];
    $table->head = [];
    $table->head[] = get_string('type_enrol', 'local_bulkenrol');
    $table->head[] = get_string('role_assigned', 'local_bulkenrol');
    $table->data = $data;

    echo $OUTPUT->heading(get_string('enrolinfo_headline', 'local_bulkenrol'), 3);
    echo html_writer::tag('div', html_writer::table($table), ['class' => 'flexible-wrap']);
}
