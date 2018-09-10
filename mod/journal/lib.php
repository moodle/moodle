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


defined('MOODLE_INTERNAL') || die();


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 * @param object $journal Object containing required journal properties
 * @return int Journal ID
 */
function journal_add_instance($journal) {
    global $DB;

    $journal->timemodified = time();
    $journal->id = $DB->insert_record("journal", $journal);

    journal_grade_item_update($journal);

    return $journal->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 * @param object $journal Object containing required journal properties
 * @return boolean True if successful
 */
function journal_update_instance($journal) {
    global $DB;

    $journal->timemodified = time();
    $journal->id = $journal->instance;

    $result = $DB->update_record("journal", $journal);

    journal_grade_item_update($journal);

    journal_update_grades($journal, 0, false);

    return $result;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * nd any data that depends on it.
 * @param int $id Journal ID
 * @return boolean True if successful
 */
function journal_delete_instance($id) {
    global $DB;

    $result = true;

    if (! $journal = $DB->get_record("journal", array("id" => $id))) {
        return false;
    }

    if (! $DB->delete_records("journal_entries", array("journal" => $journal->id))) {
        $result = false;
    }

    if (! $DB->delete_records("journal", array("id" => $journal->id))) {
        $result = false;
    }

    return $result;
}


function journal_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_RATE:
            return false;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}


function journal_get_view_actions() {
    return array('view', 'view all', 'view responses');
}


function journal_get_post_actions() {
    return array('add entry', 'update entry', 'update feedback');
}


function journal_user_outline($course, $user, $mod, $journal) {

    global $DB;

    if ($entry = $DB->get_record("journal_entries", array("userid" => $user->id, "journal" => $journal->id))) {

        $numwords = count(preg_split("/\w\b/", $entry->text)) - 1;

        $result = new stdClass();
        $result->info = get_string("numwords", "", $numwords);
        $result->time = $entry->modified;
        return $result;
    }
    return null;
}


function journal_user_complete($course, $user, $mod, $journal) {

    global $DB, $OUTPUT;

    if ($entry = $DB->get_record("journal_entries", array("userid" => $user->id, "journal" => $journal->id))) {

        echo $OUTPUT->box_start();

        if ($entry->modified) {
            echo "<p><font size=\"1\">".get_string("lastedited").": ".userdate($entry->modified)."</font></p>";
        }
        if ($entry->text) {
            echo journal_format_entry_text($entry, $course, $mod);
        }
        if ($entry->teacher) {
            $grades = make_grades_menu($journal->grade);
            journal_print_feedback($course, $entry, $grades);
        }

        echo $OUTPUT->box_end();

    } else {
        print_string("noentry", "journal");
    }
}

/**
 * Function to be run periodically according to the moodle cron.
 * Finds all journal notifications that have yet to be mailed out, and mails them.
 */
function journal_cron () {
    global $CFG, $USER, $DB;

    $cutofftime = time() - $CFG->maxeditingtime;

    if ($entries = journal_get_unmailed_graded($cutofftime)) {
        $timenow = time();

        $usernamefields = get_all_user_name_fields();
        $requireduserfields = 'id, auth, mnethostid, email, mailformat, maildisplay, lang, deleted, suspended, '
                .implode(', ', $usernamefields);

        // To save some db queries.
        $users = array();
        $courses = array();

        foreach ($entries as $entry) {

            echo "Processing journal entry $entry->id\n";

            if (!empty($users[$entry->userid])) {
                $user = $users[$entry->userid];
            } else {
                if (!$user = $DB->get_record("user", array("id" => $entry->userid), $requireduserfields)) {
                    echo "Could not find user $entry->userid\n";
                    continue;
                }
                $users[$entry->userid] = $user;
            }

            $USER->lang = $user->lang;

            if (!empty($courses[$entry->course])) {
                $course = $courses[$entry->course];
            } else {
                if (!$course = $DB->get_record('course', array('id' => $entry->course), 'id, shortname')) {
                    echo "Could not find course $entry->course\n";
                    continue;
                }
                $courses[$entry->course] = $course;
            }

            if (!empty($users[$entry->teacher])) {
                $teacher = $users[$entry->teacher];
            } else {
                if (!$teacher = $DB->get_record("user", array("id" => $entry->teacher), $requireduserfields)) {
                    echo "Could not find teacher $entry->teacher\n";
                    continue;
                }
                $users[$entry->teacher] = $teacher;
            }

            // All cached.
            $coursejournals = get_fast_modinfo($course)->get_instances_of('journal');
            if (empty($coursejournals) || empty($coursejournals[$entry->journal])) {
                echo "Could not find course module for journal id $entry->journal\n";
                continue;
            }
            $mod = $coursejournals[$entry->journal];

            // This is already cached internally.
            $context = context_module::instance($mod->id);
            $canadd = has_capability('mod/journal:addentries', $context, $user);
            $entriesmanager = has_capability('mod/journal:manageentries', $context, $user);

            if (!$canadd and $entriesmanager) {
                continue;  // Not an active participant.
            }

            $journalinfo = new stdClass();
            $journalinfo->teacher = fullname($teacher);
            $journalinfo->journal = format_string($entry->name, true);
            $journalinfo->url = "$CFG->wwwroot/mod/journal/view.php?id=$mod->id";
            $modnamepl = get_string( 'modulenameplural', 'journal' );
            $msubject = get_string( 'mailsubject', 'journal' );

            $postsubject = "$course->shortname: $msubject: ".format_string($entry->name, true);
            $posttext  = "$course->shortname -> $modnamepl -> ".format_string($entry->name, true)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= get_string("journalmail", "journal", $journalinfo)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($user->mailformat == 1) {  // HTML.
                $posthtml = "<p><font face=\"sans-serif\">".
                "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                "<a href=\"$CFG->wwwroot/mod/journal/index.php?id=$course->id\">journals</a> ->".
                "<a href=\"$CFG->wwwroot/mod/journal/view.php?id=$mod->id\">".format_string($entry->name, true)."</a></font></p>";
                $posthtml .= "<hr /><font face=\"sans-serif\">";
                $posthtml .= "<p>".get_string("journalmailhtml", "journal", $journalinfo)."</p>";
                $posthtml .= "</font><hr />";
            } else {
                $posthtml = "";
            }

            if (! email_to_user($user, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: Journal cron: Could not send out mail for id $entry->id to user $user->id ($user->email)\n";
            }
            if (!$DB->set_field("journal_entries", "mailed", "1", array("id" => $entry->id))) {
                echo "Could not update the mailed field for id $entry->id\n";
            }
        }
    }

    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in journal activities and print it out.
 * Return true if there was output, or false if there was none.
 *
 * @global stdClass $DB
 * @global stdClass $OUTPUT
 * @param stdClass $course
 * @param bool $viewfullnames
 * @param int $timestart
 * @return bool
 */
function journal_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;

    if (!get_config('journal', 'showrecentactivity')) {
        return false;
    }

    $dbparams = array($timestart, $course->id, 'journal');
    $namefields = user_picture::fields('u', null, 'userid');
    $sql = "SELECT je.id, je.modified, cm.id AS cmid, $namefields
         FROM {journal_entries} je
              JOIN {journal} j         ON j.id = je.journal
              JOIN {course_modules} cm ON cm.instance = j.id
              JOIN {modules} md        ON md.id = cm.module
              JOIN {user} u            ON u.id = je.userid
         WHERE je.modified > ? AND
               j.course = ? AND
               md.name = ?
         ORDER BY je.modified ASC
    ";

    $newentries = $DB->get_records_sql($sql, $dbparams);

    $modinfo = get_fast_modinfo($course);
    $show    = array();

    foreach ($newentries as $anentry) {

        if (!array_key_exists($anentry->cmid, $modinfo->get_cms())) {
            continue;
        }
        $cm = $modinfo->get_cm($anentry->cmid);

        if (!$cm->uservisible) {
            continue;
        }
        if ($anentry->userid == $USER->id) {
            $show[] = $anentry;
            continue;
        }
        $context = context_module::instance($anentry->cmid);

        // Only teachers can see other students entries.
        if (!has_capability('mod/journal:manageentries', $context)) {
            continue;
        }

        $groupmode = groups_get_activity_groupmode($cm, $course);

        if ($groupmode == SEPARATEGROUPS &&
                !has_capability('moodle/site:accessallgroups',  $context)) {
            if (isguestuser()) {
                // Shortcut - guest user does not belong into any group.
                continue;
            }

            // This will be slow - show only users that share group with me in this cm.
            if (!$modinfo->get_groups($cm->groupingid)) {
                continue;
            }
            $usersgroups = groups_get_all_groups($course->id, $anentry->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $anentry;
    }

    if (empty($show)) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newjournalentries', 'journal').':', 3);

    foreach ($show as $submission) {
        $cm = $modinfo->get_cm($submission->cmid);
        $context = context_module::instance($submission->cmid);
        if (has_capability('mod/journal:manageentries', $context)) {
            $link = $CFG->wwwroot.'/mod/journal/report.php?id='.$cm->id;
        } else {
            $link = $CFG->wwwroot.'/mod/journal/view.php?id='.$cm->id;
        }
        print_recent_activity_note($submission->modified,
                                   $submission,
                                   $cm->name,
                                   $link,
                                   false,
                                   $viewfullnames);
    }
    return true;
}

/**
 * Returns the users with data in one journal
 * (users with records in journal_entries, students and teachers)
 * @param int $journalid Journal ID
 * @return array Array of user ids
 */
function journal_get_participants($journalid) {
    global $DB;

    // Get students.
    $students = $DB->get_records_sql("SELECT DISTINCT u.id
                                      FROM {user} u,
                                      {journal_entries} j
                                      WHERE j.journal=? and
                                      u.id = j.userid", array($journalid));
    // Get teachers.
    $teachers = $DB->get_records_sql("SELECT DISTINCT u.id
                                      FROM {user} u,
                                      {journal_entries} j
                                      WHERE j.journal=? and
                                      u.id = j.teacher", array($journalid));

    // Add teachers to students.
    if ($teachers) {
        foreach ($teachers as $teacher) {
            $students[$teacher->id] = $teacher;
        }
    }
    // Return students array (it contains an array of unique users).
    return $students;
}

/**
 * This function returns true if a scale is being used by one journal
 * @param int $journalid Journal ID
 * @param int $scaleid Scale ID
 * @return boolean True if a scale is being used by one journal
 */
function journal_scale_used ($journalid, $scaleid) {

    global $DB;
    $return = false;

    $rec = $DB->get_record("journal", array("id" => $journalid, "grade" => -$scaleid));

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of journal
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any journal
 */
function journal_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->get_records('journal', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the journal.
 *
 * @param object $mform form passed by reference
 */
function journal_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'journalheader', get_string('modulenameplural', 'journal'));
    $mform->addElement('advcheckbox', 'reset_journal', get_string('removemessages', 'journal'));
}

/**
 * Course reset form defaults.
 *
 * @param object $course
 * @return array
 */
function journal_reset_course_form_defaults($course) {
    return array('reset_journal' => 1);
}

/**
 * Removes all entries
 *
 * @param object $data
 */
function journal_reset_userdata($data) {

    global $CFG, $DB;

    $status = array();
    if (!empty($data->reset_journal)) {

        $sql = "SELECT j.id
                FROM {journal} j
                WHERE j.course = ?";
        $params = array($data->courseid);

        $DB->delete_records_select('journal_entries', "journal IN ($sql)", $params);

        $status[] = array('component' => get_string('modulenameplural', 'journal'),
                          'item' => get_string('removeentries', 'journal'),
                          'error' => false);
    }

    return $status;
}

function journal_print_overview($courses, &$htmlarray) {

    global $USER, $CFG, $DB;

    if (!get_config('journal', 'overview')) {
        return array();
    }

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$journals = get_all_instances_in_courses('journal', $courses)) {
        return array();
    }

    $strjournal = get_string('modulename', 'journal');

    $timenow = time();
    foreach ($journals as $journal) {

        if (empty($courses[$journal->course]->format)) {
            $courses[$journal->course]->format = $DB->get_field('course', 'format', array('id' => $journal->course));
        }

        if ($courses[$journal->course]->format == 'weeks' AND $journal->days) {

            $coursestartdate = $courses[$journal->course]->startdate;

            $journal->timestart  = $coursestartdate + (($journal->section - 1) * 608400);
            if (!empty($journal->days)) {
                $journal->timefinish = $journal->timestart + (3600 * 24 * $journal->days);
            } else {
                $journal->timefinish = 9999999999;
            }
            $journalopen = ($journal->timestart < $timenow && $timenow < $journal->timefinish);

        } else {
            $journalopen = true;
        }

        if ($journalopen) {
            $str = '<div class="journal overview"><div class="name">'.
                   $strjournal.': <a '.($journal->visible ? '' : ' class="dimmed"').
                   ' href="'.$CFG->wwwroot.'/mod/journal/view.php?id='.$journal->coursemodule.'">'.
                   $journal->name.'</a></div></div>';

            if (empty($htmlarray[$journal->course]['journal'])) {
                $htmlarray[$journal->course]['journal'] = $str;
            } else {
                $htmlarray[$journal->course]['journal'] .= $str;
            }
        }
    }
}

function journal_get_user_grades($journal, $userid=0) {
    global $DB;

    $params = array();

    if ($userid) {
        $userstr = 'AND userid = :uid';
        $params['uid'] = $userid;
    } else {
        $userstr = '';
    }

    if (!$journal) {
        return false;

    } else {

        $sql = "SELECT userid, modified as datesubmitted, format as feedbackformat,
                rating as rawgrade, entrycomment as feedback, teacher as usermodifier, timemarked as dategraded
                FROM {journal_entries}
                WHERE journal = :jid ".$userstr;
        $params['jid'] = $journal->id;

        $grades = $DB->get_records_sql($sql, $params);

        if ($grades) {
            foreach ($grades as $key => $grade) {
                $grades[$key]->id = $grade->userid;
            }
        } else {
            return false;
        }

        return $grades;
    }

}


/**
 * Update journal grades in 1.9 gradebook
 *
 * @param object   $journal      if is null, all journals
 * @param int      $userid       if is false al users
 * @param boolean  $nullifnone   return null if grade does not exist
 */
function journal_update_grades($journal=null, $userid=0, $nullifnone=true) {

    global $CFG, $DB;

    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    if ($journal != null) {
        if ($grades = journal_get_user_grades($journal, $userid)) {
            journal_grade_item_update($journal, $grades);
        } else if ($userid && $nullifnone) {
            $grade = new stdClass();
            $grade->userid   = $userid;
            $grade->rawgrade = null;
            journal_grade_item_update($journal, $grade);
        } else {
            journal_grade_item_update($journal);
        }
    } else {
        $sql = "SELECT j.*, cm.idnumber as cmidnumber
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                JOIN {journal} j ON cm.instance = j.id
                WHERE m.name = 'journal'";
        if ($recordset = $DB->get_records_sql($sql)) {
            foreach ($recordset as $journal) {
                if ($journal->grade != false) {
                    journal_update_grades($journal);
                } else {
                    journal_grade_item_update($journal);
                }
            }
        }
    }
}


/**
 * Create grade item for given journal
 *
 * @param object $journal object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function journal_grade_item_update($journal, $grades=null) {
    global $CFG;
    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    if (array_key_exists('cmidnumber', $journal)) {
        $params = array('itemname' => $journal->name, 'idnumber' => $journal->cmidnumber);
    } else {
        $params = array('itemname' => $journal->name);
    }

    if ($journal->grade > 0) {
        $params['gradetype']  = GRADE_TYPE_VALUE;
        $params['grademax']   = $journal->grade;
        $params['grademin']   = 0;
        $params['multfactor'] = 1.0;

    } else if ($journal->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$journal->grade;

    } else {
        $params['gradetype']  = GRADE_TYPE_NONE;
        $params['multfactor'] = 1.0;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/journal', $journal->course, 'mod', 'journal', $journal->id, 0, $grades, $params);
}


/**
 * Delete grade item for given journal
 *
 * @param   object   $journal
 * @return  object   grade_item
 */
function journal_grade_item_delete($journal) {
    global $CFG;

    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/journal', $journal->course, 'mod', 'journal', $journal->id, 0, null, array('deleted' => 1));
}



function journal_get_users_done($journal, $currentgroup) {
    global $DB;

    $params = array();

    $sql = "SELECT u.* FROM {journal_entries} j
            JOIN {user} u ON j.userid = u.id ";

    // Group users.
    if ($currentgroup != 0) {
        $sql .= "JOIN {groups_members} gm ON gm.userid = u.id AND gm.groupid = ?";
        $params[] = $currentgroup;
    }

    $sql .= " WHERE j.journal=? ORDER BY j.modified DESC";
    $params[] = $journal->id;
    $journals = $DB->get_records_sql($sql, $params);

    $cm = journal_get_coursemodule($journal->id);
    if (!$journals || !$cm) {
        return null;
    }

    // Remove unenrolled participants.
    foreach ($journals as $key => $user) {

        $context = context_module::instance($cm->id);

        $canadd = has_capability('mod/journal:addentries', $context, $user);
        $entriesmanager = has_capability('mod/journal:manageentries', $context, $user);

        if (!$entriesmanager and !$canadd) {
            unset($journals[$key]);
        }
    }

    return $journals;
}

/**
 * Counts all the journal entries (optionally in a given group)
 */
function journal_count_entries($journal, $groupid = 0) {
    global $DB;

    $cm = journal_get_coursemodule($journal->id);
    $context = context_module::instance($cm->id);

    if ($groupid) {     // How many in a particular group?

        $sql = "SELECT DISTINCT u.id FROM {journal_entries} j
                JOIN {groups_members} g ON g.userid = j.userid
                JOIN {user} u ON u.id = g.userid
                WHERE j.journal = ? AND g.groupid = ?";
        $journals = $DB->get_records_sql($sql, array($journal->id, $groupid));

    } else { // Count all the entries from the whole course.

        $sql = "SELECT DISTINCT u.id FROM {journal_entries} j
                JOIN {user} u ON u.id = j.userid
                WHERE j.journal = ?";
        $journals = $DB->get_records_sql($sql, array($journal->id));
    }

    if (!$journals) {
        return 0;
    }

    $canadd = get_users_by_capability($context, 'mod/journal:addentries', 'u.id');
    $entriesmanager = get_users_by_capability($context, 'mod/journal:manageentries', 'u.id');

    // Remove unenrolled participants.
    foreach ($journals as $userid => $notused) {

        if (!isset($entriesmanager[$userid]) && !isset($canadd[$userid])) {
            unset($journals[$userid]);
        }
    }

    return count($journals);
}

function journal_get_unmailed_graded($cutofftime) {
    global $DB;

    $sql = "SELECT je.*, j.course, j.name FROM {journal_entries} je
            JOIN {journal} j ON je.journal = j.id
            WHERE je.mailed = '0' AND je.timemarked < ? AND je.timemarked > 0";
    return $DB->get_records_sql($sql, array($cutofftime));
}

function journal_log_info($log) {
    global $DB;

    $sql = "SELECT j.*, u.firstname, u.lastname
            FROM {journal} j
            JOIN {journal_entries} je ON je.journal = j.id
            JOIN {user} u ON u.id = je.userid
            WHERE je.id = ?";
    return $DB->get_record_sql($sql, array($log->info));
}

/**
 * Returns the journal instance course_module id
 *
 * @param integer $journal
 * @return object
 */
function journal_get_coursemodule($journalid) {

    global $DB;

    return $DB->get_record_sql("SELECT cm.id FROM {course_modules} cm
                                JOIN {modules} m ON m.id = cm.module
                                WHERE cm.instance = ? AND m.name = 'journal'", array($journalid));
}



function journal_print_user_entry($course, $user, $entry, $teachers, $grades) {

    global $USER, $OUTPUT, $DB, $CFG;

    require_once($CFG->dirroot.'/lib/gradelib.php');

    echo "\n<table class=\"journaluserentry\" id=\"entry-" . $user->id . "\">";

    echo "\n<tr>";
    echo "\n<td class=\"userpix\" rowspan=\"2\">";
    echo $OUTPUT->user_picture($user, array('courseid' => $course->id, 'alttext' => true));
    echo "</td>";
    echo "<td class=\"userfullname\">".fullname($user);
    if ($entry) {
        echo " <span class=\"lastedit\">".get_string("lastedited").": ".userdate($entry->modified)."</span>";
    }
    echo "</td>";
    echo "</tr>";

    echo "\n<tr><td>";
    if ($entry) {
        echo journal_format_entry_text($entry, $course);
    } else {
        print_string("noentry", "journal");
    }
    echo "</td></tr>";

    if ($entry) {
        echo "\n<tr>";
        echo "<td class=\"userpix\">";
        if (!$entry->teacher) {
            $entry->teacher = $USER->id;
        }
        if (empty($teachers[$entry->teacher])) {
            $teachers[$entry->teacher] = $DB->get_record('user', array('id' => $entry->teacher));
        }
        echo $OUTPUT->user_picture($teachers[$entry->teacher], array('courseid' => $course->id, 'alttext' => true));
        echo "</td>";
        echo "<td>".get_string("feedback").":";

        $attrs = array();
        $hiddengradestr = '';
        $gradebookgradestr = '';
        $feedbackdisabledstr = '';
        $feedbacktext = $entry->entrycomment;

        // If the grade was modified from the gradebook disable edition also skip if journal is not graded.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'journal', $entry->journal, array($user->id));
        if (!empty($gradinginfo->items[0]->grades[$entry->userid]->str_long_grade)) {
            if ($gradingdisabled = $gradinginfo->items[0]->grades[$user->id]->locked
                    || $gradinginfo->items[0]->grades[$user->id]->overridden) {
                $attrs['disabled'] = 'disabled';
                $hiddengradestr = '<input type="hidden" name="r'.$entry->id.'" value="'.$entry->rating.'"/>';
                $gradebooklink = '<a href="'.$CFG->wwwroot.'/grade/report/grader/index.php?id='.$course->id.'">';
                $gradebooklink .= $gradinginfo->items[0]->grades[$user->id]->str_long_grade.'</a>';
                $gradebookgradestr = '<br/>'.get_string("gradeingradebook", "journal").':&nbsp;'.$gradebooklink;

                $feedbackdisabledstr = 'disabled="disabled"';
                $feedbacktext = $gradinginfo->items[0]->grades[$user->id]->str_feedback;
            }
        }

        // Grade selector.
        $attrs['id'] = 'r' . $entry->id;
        echo html_writer::label(fullname($user)." ".get_string('grade'), 'r'.$entry->id, true, array('class' => 'accesshide'));
        echo html_writer::select($grades, 'r'.$entry->id, $entry->rating, get_string("nograde").'...', $attrs);
        echo $hiddengradestr;
        // Rewrote next three lines to show entry needs to be regraded due to resubmission.
        if (!empty($entry->timemarked) && $entry->modified > $entry->timemarked) {
            echo " <span class=\"lastedit\">".get_string("needsregrade", "journal"). "</span>";
        } else if ($entry->timemarked) {
            echo " <span class=\"lastedit\">".userdate($entry->timemarked)."</span>";
        }
        echo $gradebookgradestr;

        // Feedback text.
        echo html_writer::label(fullname($user)." ".get_string('feedback'), 'c'.$entry->id, true, array('class' => 'accesshide'));
        echo "<p><textarea id=\"c$entry->id\" name=\"c$entry->id\" rows=\"12\" cols=\"60\" $feedbackdisabledstr>";
        p($feedbacktext);
        echo "</textarea></p>";

        if ($feedbackdisabledstr != '') {
            echo '<input type="hidden" name="c'.$entry->id.'" value="'.$feedbacktext.'"/>';
        }
        echo "</td></tr>";
    }
    echo "</table>\n";

}

function journal_print_feedback($course, $entry, $grades) {

    global $CFG, $DB, $OUTPUT;

    require_once($CFG->dirroot.'/lib/gradelib.php');

    if (! $teacher = $DB->get_record('user', array('id' => $entry->teacher))) {
        print_error('Weird journal error');
    }

    echo '<table class="feedbackbox">';

    echo '<tr>';
    echo '<td class="left picture">';
    echo $OUTPUT->user_picture($teacher, array('courseid' => $course->id, 'alttext' => true));
    echo '</td>';
    echo '<td class="entryheader">';
    echo '<span class="author">'.fullname($teacher).'</span>';
    echo '&nbsp;&nbsp;<span class="time">'.userdate($entry->timemarked).'</span>';
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="left side">&nbsp;</td>';
    echo '<td class="entrycontent">';

    echo '<div class="grade">';

    // Gradebook preference.
    $gradinginfo = grade_get_grades($course->id, 'mod', 'journal', $entry->journal, array($entry->userid));
    if (!empty($gradinginfo->items[0]->grades[$entry->userid]->str_long_grade)) {
        echo get_string('grade').': ';
        echo $gradinginfo->items[0]->grades[$entry->userid]->str_long_grade;
    } else {
        print_string('nograde');
    }
    echo '</div>';

    // Feedback text.
    echo format_text($entry->entrycomment, FORMAT_PLAIN);
    echo '</td></tr></table>';
}

/**
 * Serves the journal files.
 *
 * @package  mod_journal
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function journal_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $USER;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $context)) {
        return false;
    }

    // Args[0] should be the entry id.
    $entryid = intval(array_shift($args));
    $entry = $DB->get_record('journal_entries', array('id' => $entryid), 'id, userid', MUST_EXIST);

    $canmanage = has_capability('mod/journal:manageentries', $context);
    if (!$canmanage && !has_capability('mod/journal:addentries', $context)) {
        // Even if it is your own entry.
        return false;
    }

    // Students can only see their own entry.
    if (!$canmanage && $USER->id !== $entry->userid) {
        return false;
    }

    if ($filearea !== 'entry') {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_journal/$filearea/$entryid/$relativepath";
    $file = $fs->get_file_by_hash(sha1($fullpath));

    // Finally send the file.
    send_stored_file($file, null, 0, $forcedownload, $options);
}

function journal_format_entry_text($entry, $course = false, $cm = false) {

    if (!$cm) {
        if ($course) {
            $courseid = $course->id;
        } else {
            $courseid = 0;
        }
        $cm = get_coursemodule_from_instance('journal', $entry->journal, $courseid);
    }

    $context = context_module::instance($cm->id);
    $entrytext = file_rewrite_pluginfile_urls($entry->text, 'pluginfile.php', $context->id, 'mod_journal', 'entry', $entry->id);

    $formatoptions = array(
        'context' => $context,
        'noclean' => false,
        'trusted' => false
    );
    return format_text($entrytext, $entry->format, $formatoptions);
}

