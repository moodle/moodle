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
 * mod_journal lib file
 *
 * @package    mod_journal
 * @copyright  2014 David Monllao <david.monllao@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
    $journal->id = $DB->insert_record('journal', $journal);

    journal_grade_item_update($journal);

    $completiontimeexpected = !empty($journal->completionexpected) ? $journal->completionexpected : null;
    \core_completion\api::update_completion_date_event($journal->coursemodule, 'journal', $journal->id, $completiontimeexpected);

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

    $result = $DB->update_record('journal', $journal);

    journal_grade_item_update($journal);

    journal_update_grades($journal, 0, false);

    $completiontimeexpected = !empty($journal->completionexpected) ? $journal->completionexpected : null;
    \core_completion\api::update_completion_date_event($journal->coursemodule, 'journal', $journal->id, $completiontimeexpected);

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

    $cm = get_coursemodule_from_instance('journal', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'journal', $id, null);

    if (! $journal = $DB->get_record('journal', array('id' => $id))) {
        return false;
    }

    if (! $DB->delete_records('journal_entries', array('journal' => $journal->id))) {
        $result = false;
    }

    if (! $DB->delete_records('journal', array('id' => $journal->id))) {
        $result = false;
    }

    return $result;
}

/**
 * List of feature supported
 *
 * @param int $feature Feature constant
 * @return bool|null True if feature is supported, falsy if it is not
 */
function journal_supports($feature) {
    if (defined('FEATURE_MOD_PURPOSE')
        && defined('MOD_PURPOSE_COLLABORATION')
        && $feature === FEATURE_MOD_PURPOSE) {
        return MOD_PURPOSE_COLLABORATION;
    }
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
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Return list of actions for the view
 *
 * @return array Array of actions
 */
function journal_get_view_actions() {
    return array('view', 'view all', 'view responses');
}

/**
 * Return list of actions for the post method
 *
 * @return array Array of actions
 */
function journal_get_post_actions() {
    return array('add entry', 'update entry', 'update feedback');
}


/**
 * User outline
 *
 * @param stdClass $course Course object
 * @param stdClass $user User object
 * @param stdClass $mod Mod object
 * @param stdClass $journal Journal object
 * @return stdClass|null User outline object or null
 */
function journal_user_outline($course, $user, $mod, $journal) {

    global $DB;

    if ($entry = $DB->get_record('journal_entries', array('userid' => $user->id, 'journal' => $journal->id))) {

        $numwords = count(preg_split('/\w\b/', $entry->text)) - 1;

        $result = new \stdClass();
        $result->info = get_string('numwords', '', $numwords);
        $result->time = $entry->modified;
        return $result;
    }
    return null;
}

/**
 * User complete check
 *
 * @param stdClass $course Course object
 * @param stdClass $user User object
 * @param stdClass $mod Mod object
 * @param stdClass $journal Journal object
 * @return void
 */
function journal_user_complete($course, $user, $mod, $journal) {

    global $DB, $OUTPUT;

    if ($entry = $DB->get_record('journal_entries', array('userid' => $user->id, 'journal' => $journal->id))) {

        echo $OUTPUT->box_start();

        if ($entry->modified) {
            echo '<p><font size="1">'.get_string('lastedited').': '.userdate($entry->modified).'</font></p>';
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
        print_string('noentry', 'journal');
    }
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in journal activities and print it out.
 * Return true if there was output, or false if there was none.
 *
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
        $context = \context_module::instance($anentry->cmid);

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
        $context = \context_module::instance($submission->cmid);
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
    $students = $DB->get_records_sql('SELECT DISTINCT u.id
                                      FROM {user} u,
                                      {journal_entries} j
                                      WHERE j.journal=? and
                                      u.id = j.userid', array($journalid));
    // Get teachers.
    $teachers = $DB->get_records_sql('SELECT DISTINCT u.id
                                      FROM {user} u,
                                      {journal_entries} j
                                      WHERE j.journal=? and
                                      u.id = j.teacher', array($journalid));

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

    $rec = $DB->get_record('journal', array('id' => $journalid, 'grade' => -$scaleid));

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of journal
 * This is used to find out if scale used anywhere
 * @param int $scaleid Scale id
 * @return boolean True if the scale is used by any journal
 */
function journal_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid && $DB->get_records('journal', array('grade' => -$scaleid))) {
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
 * @param object $course Course object
 * @return array Array with defaults
 */
function journal_reset_course_form_defaults($course) {
    return array('reset_journal' => 1);
}

/**
 * Removes all entries
 *
 * @param object $data Data array
 */
function journal_reset_userdata($data) {

    global $CFG, $DB;

    $status = array();
    if (!empty($data->reset_journal)) {

        $sql = 'SELECT j.id
                FROM {journal} j
                WHERE j.course = ?';
        $params = array($data->courseid);

        $DB->delete_records_select('journal_entries', "journal IN ($sql)", $params);

        $status[] = array('component' => get_string('modulenameplural', 'journal'),
                          'item' => get_string('removeentries', 'journal'),
                          'error' => false);
    }

    return $status;
}

/**
 * Print journal overview
 *
 * @param array $courses Courses array
 * @param array $htmlarray HTML array
 * @return void
 */
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

        if ($courses[$journal->course]->format == 'weeks' && $journal->days) {

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

/**
 * Get user grade
 *
 * @param stdClass $journal Journal object
 * @param integer $userid User id
 * @return array Array of grades
 */
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

        $sql = 'SELECT userid, modified as datesubmitted, format as feedbackformat,
                rating as rawgrade, entrycomment as feedback, teacher as usermodifier, timemarked as dategraded
                FROM {journal_entries}
                WHERE journal = :jid '.$userstr;
        $params['jid'] = $journal->id;

        $grades = $DB->get_records_sql($sql, $params);

        if ($grades) {
            foreach ($grades as $key => $grade) {
                $grades[$key]->id = $grade->userid;
                if ($grade->rawgrade == -1) {
                    $grades[$key]->rawgrade = null;
                }
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
            $grade = new \stdClass();
            $grade->userid   = $userid;
            $grade->rawgrade = null;
            journal_grade_item_update($journal, $grade);
        } else {
            journal_grade_item_update($journal);
        }
    } else {
        $sql = 'SELECT j.*, cm.idnumber as cmidnumber
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                JOIN {journal} j ON cm.instance = j.id
                WHERE m.name = \'journal\'';
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
 * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function journal_grade_item_update($journal, $grades=null) {
    global $CFG;
    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    if (property_exists($journal, 'cmidnumber')) {
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

/**
 * Return array of users who completed journal
 *
 * @param stdClass $journal Journal object
 * @param int $currentgroup Group id
 * @return array Array of users
 */
function journal_get_users_done($journal, $currentgroup) {
    global $DB;

    $params = array();

    $sql = 'SELECT u.* FROM {journal_entries} j
            JOIN {user} u ON j.userid = u.id ';

    // Group users.
    if ($currentgroup != 0) {
        $sql .= 'JOIN {groups_members} gm ON gm.userid = u.id AND gm.groupid = ?';
        $params[] = $currentgroup;
    }

    $sql .= ' WHERE j.journal=? ORDER BY j.modified DESC';
    $params[] = $journal->id;
    $journals = $DB->get_records_sql($sql, $params);

    $cm = journal_get_coursemodule($journal->id);
    if (!$journals || !$cm) {
        return null;
    }

    // Remove unenrolled participants.
    foreach ($journals as $key => $user) {

        $context = \context_module::instance($cm->id);

        $canadd = has_capability('mod/journal:addentries', $context, $user);
        $entriesmanager = has_capability('mod/journal:manageentries', $context, $user);

        if (!$entriesmanager && !$canadd) {
            unset($journals[$key]);
        }
    }

    return $journals;
}

/**
 * Counts all the journal entries (optionally in a given group)
 * @param stdClass $journal Journal object
 * @param boolean|int|array $groupids Group id or array of ids. 0 or false = see all.
 * @return int Number of entries
 */
function journal_count_entries($journal, $groupids = 0) {
    global $DB;

    $cm = journal_get_coursemodule($journal->id);
    $context = \context_module::instance($cm->id);
    $journals = null;

    // Convert single group id to an array containing the group id to
    // process it later in the function.
    if (!is_array($groupids) && $groupids) {
        $groupids = [$groupids];
    }

    if (is_array($groupids) && !empty($groupids)) {     // How many in a particular group?
        $params = array($journal->id);
        $sqlin = $DB->get_in_or_equal($groupids);

        $sql = "SELECT DISTINCT u.id FROM {journal_entries} j
                JOIN {groups_members} g ON g.userid = j.userid
                JOIN {user} u ON u.id = g.userid
                WHERE j.journal = ? AND g.groupid $sqlin[0]";
        $journals = $DB->get_records_sql($sql, array_merge($params, $sqlin[1]));

    } else if ($groupids === 0 || $groupids === false) { // Count all the entries from the whole course.
        $sql = 'SELECT DISTINCT u.id FROM {journal_entries} j
                JOIN {user} u ON u.id = j.userid
                WHERE j.journal = ?';
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

/**
 * Get list of graded unmailed users
 *
 * @param int $cutofftime Timestamp
 * @return array Array of users
 */
function journal_get_unmailed_graded($cutofftime) {
    global $DB;

    $sql = 'SELECT je.*, j.course, j.name FROM {journal_entries} je
            JOIN {journal} j ON je.journal = j.id
            WHERE je.mailed = 0 AND je.timemarked < ? AND je.timemarked > 0
            AND (je.rating <> -1 OR (je.entrycomment IS NOT NULL AND trim(je.entrycomment) <> ?))';
    return $DB->get_records_sql($sql, array($cutofftime, ''));
}

/**
 * Log info
 *
 * @param stdClass $log Log object
 * @return stdClass|null Log object
 */
function journal_log_info($log) {
    global $DB;

    $sql = 'SELECT j.*, u.firstname, u.lastname
            FROM {journal} j
            JOIN {journal_entries} je ON je.journal = j.id
            JOIN {user} u ON u.id = je.userid
            WHERE je.id = ?';
    return $DB->get_record_sql($sql, array($log->info));
}

/**
 * Returns the journal instance course_module id
 *
 * @param integer $journalid Journal id
 * @return object Course module object
 */
function journal_get_coursemodule($journalid) {

    global $DB;

    return $DB->get_record_sql('SELECT cm.id FROM {course_modules} cm
                                JOIN {modules} m ON m.id = cm.module
                                WHERE cm.instance = ? AND m.name = \'journal\'', array($journalid));
}


/**
 * Print user entry
 *
 * @param object $course Course object
 * @param object $user User object
 * @param object $entry Entry object
 * @param array $teachers Teachers array
 * @param array $grades Grades array
 * @param array $cmid Course module id for the specific journal
 * @return void
 */
function journal_print_user_entry($course, $user, $entry, $teachers, $grades, $cmid) {
    global $USER, $OUTPUT, $DB, $CFG;

    require_once($CFG->dirroot.'/lib/gradelib.php');

    echo '<div class="journaluserentrywrapper">';
    echo '<table class="journaluserentry m-b-1" id="entry-' . $user->id . '">';

    echo '<tr>';
    echo '<td class="userpix" style="border-bottom: 1px solid #dedede;">';
    echo $OUTPUT->user_picture($user, array('courseid' => $course->id, 'alttext' => true));
    echo '</td>';
    echo '<td class="userfullname"><strong>'.fullname($user).'</strong>';
    if ($entry) {
        echo ' <span class="lastedit">'.get_string('lastedited').': '.userdate($entry->modified).'</span>';
    }
    echo '</td>';
    echo '</tr>';

    echo '<tr><td colspan="2">';
    if ($entry) {
        echo journal_format_entry_text($entry, $course);
    } else {
        print_string('noentry', 'journal');
    }
    echo '</td></tr>';

    if ($entry) {
        echo '<tr>';
        echo '<td class="userpix" style="border-top: 1px solid #dedede;">';
        if (!$entry->teacher) {
            $entry->teacher = $USER->id;
        }
        if (empty($teachers[$entry->teacher])) {
            $teachers[$entry->teacher] = $DB->get_record('user', array('id' => $entry->teacher));
        }
        echo $OUTPUT->user_picture($teachers[$entry->teacher], array('courseid' => $course->id, 'alttext' => true));
        echo '</td>';
        echo '<td style="border-top: 1px solid #dedede;">'.get_string('feedback').':';

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
        $gradestring = get_string_manager()->string_exists('gradenoun', 'moodle') ? get_string('gradenoun') : get_string('grade');
        echo html_writer::label(fullname($user).' '.$gradestring, 'r'.$entry->id, true, array('class' => 'accesshide'));
        echo html_writer::select($grades, 'r'.$entry->id, $entry->rating, get_string('nograde').'...', $attrs);
        echo $hiddengradestr;
        // Rewrote next three lines to show entry needs to be regraded due to resubmission.
        if (!empty($entry->timemarked) && $entry->modified > $entry->timemarked) {
            echo ' <span class="lastedit">'.get_string('needsregrade', 'journal'). '</span>';
        } else if ($entry->timemarked) {
            echo ' <span class="lastedit">'.userdate($entry->timemarked).'</span>';
        }
        echo $gradebookgradestr;

        // Feedback text.
        echo html_writer::label(fullname($user).' '.get_string('feedback'), 'c'.$entry->id, true, array('class' => 'accesshide'));
        echo "<p><textarea id=\"c$entry->id\" name=\"c$entry->id\" rows=\"7\" $feedbackdisabledstr>";
        p($feedbacktext);
        echo '</textarea></p>';

        if ($feedbackdisabledstr != '') {
            echo '<input type="hidden" name="c'.$entry->id.'" value="'.$feedbacktext.'"/>';
        }
        echo '</td></tr>';
    }
    echo '</table>';

    if ($entry) {
        echo '<p class="feedbacksave" style="margin-top: -16px;">';
        echo '<input type="button" data-cmid="'.$cmid.'" data-entryid="'.$entry->id.'" data-userid="'.$user->id.'"';
        echo 'value="'.get_string('savefeedback', 'journal').'" class="saveindividualfeedback btn btn-secondary m-t-1"/>';
        echo '</p>';
    }

    echo '</div>';

}

/**
 * Print feedback
 *
 * @param object $course Course object
 * @param object $entry Entry object
 * @param array $grades Grades array
 * @return void
 */
function journal_print_feedback($course, $entry, $grades) {

    global $CFG, $DB, $OUTPUT;

    require_once($CFG->dirroot.'/lib/gradelib.php');

    if (! $teacher = $DB->get_record('user', array('id' => $entry->teacher))) {
        throw new \moodle_exception(get_string('Weird journal error'));
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
    echo '<td class="side">&nbsp;</td>';
    echo '<td class="entrycontent">';

    echo '<div class="grade">';

    // Gradebook preference.
    $gradinginfo = grade_get_grades($course->id, 'mod', 'journal', $entry->journal, array($entry->userid));
    if (!empty($gradinginfo->items[0]->grades[$entry->userid]->str_long_grade)) {
        $gradestring = get_string_manager()->string_exists('gradenoun', 'moodle') ? get_string('gradenoun') : get_string('grade');
        echo $gradestring.': ';
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

/**
 * Format entry text
 *
 * @param object $entry Entry object
 * @param object $course Course object
 * @param object $cm Course module object
 * @return string Formatted text
 */
function journal_format_entry_text($entry, $course = false, $cm = false) {

    if (!$cm) {
        if ($course) {
            $courseid = $course->id;
        } else {
            $courseid = 0;
        }
        $cm = get_coursemodule_from_instance('journal', $entry->journal, $courseid);
    }

    $context = \context_module::instance($cm->id);
    $entrytext = file_rewrite_pluginfile_urls($entry->text, 'pluginfile.php', $context->id, 'mod_journal', 'entry', $entry->id);

    $formatoptions = array(
        'context' => $context,
        'noclean' => false,
        'trusted' => false
    );
    return format_text($entrytext, $entry->format, $formatoptions);
}


/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_journal_core_calendar_provide_event_action(calendar_event $event,
                                                     \core_calendar\action_factory $factory,
                                                     int $userid = 0) {
    global $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['journal'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $context = \context_module::instance($cm->id);

    if (!has_capability('mod/journal:addentries', $context, $userid)) {
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/journal/view.php', ['id' => $cm->id]),
        1,
        true
    );
}

/**
 * Sort the users in regards to the sort criterion
 *
 * @param array $users The user's array
 * @param string $sortby The sort criterion
 * @param array $entrybyuser The sorted array
 */
function mod_journal_sort_users(array &$users, $sortby, array $entrybyuser) {
    uasort($users, function($a, $b) use ($sortby, $entrybyuser) {
        switch ($sortby){
            case 'firstnamedesc':
                return $a->firstname < $b->firstname ? 1 :
                    ($a->firstname > $b->firstname ? -1 : 0);
            case 'firstnameasc':
                return $a->firstname < $b->firstname ? -1 :
                    ($a->firstname > $b->firstname ? 1 : 0);
            case 'lastnamedesc':
                return $a->lastname < $b->lastname ? 1 :
                    ($a->lastname > $b->lastname ? -1 : 0);
            case 'lastnameasc':
                return $a->lastname < $b->lastname ? -1 :
                    ($a->lastname > $b->lastname ? 1 : 0);
            case 'datedesc':
                if (!isset($entrybyuser[$a->id]->modified) || !isset($entrybyuser[$b->id]->modified)) {
                    return 1;
                }
                return $entrybyuser[$a->id]->modified < $entrybyuser[$b->id]->modified ? 1 :
                    ($entrybyuser[$a->id]->modified > $entrybyuser[$b->id]->modified ? -1 : 0);
            case 'dateasc':
            default:
                if (!isset($entrybyuser[$a->id]->modified) || !isset($entrybyuser[$b->id]->modified)) {
                    return -1;
                }
                return $entrybyuser[$a->id]->modified < $entrybyuser[$b->id]->modified ? -1 :
                    ($entrybyuser[$a->id]->modified > $entrybyuser[$b->id]->modified ? 1 : 0);
        }
    });
}
