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
 * Library of functions and constants for module game
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Define CONSTANTS.

/*
 * Options determining how the grades from individual attempts are combined to give
 * the overall grade for a user
 */
define('GAME_GRADEHIGHEST', 1);
define('GAME_GRADEAVERAGE', 2);
define('GAME_ATTEMPTFIRST', 3);
define('GAME_ATTEMPTLAST', 4);

// The different review options are stored in the bits of $game->review.
// These constants help to extract the options.

define('GAME_REVIEW_IMMEDIATELY', 0x3f);    // The first 6 bits refer to the time immediately after the attempt.
define('GAME_REVIEW_OPEN', 0xfc0);          // The next 6 bits refer to the time after the attempt but while the game is open.
define('GAME_REVIEW_CLOSED', 0x3f000);      // The final 6 bits refer to the time after the game closes.

// Within each group of 6 bits we determine what should be shown.
define('GAME_REVIEW_RESPONSES',   1 * 0x1041); // Show responses.
define('GAME_REVIEW_SCORES',      2 * 0x1041); // Show scores.
define('GAME_REVIEW_FEEDBACK',    4 * 0x1041); // Show feedback.
define('GAME_REVIEW_ANSWERS',     8 * 0x1041); // Show correct answers.

// Some handling of worked solutions is already in the code but not yet fully supported.
// and not switched on in the user interface.
define('GAME_REVIEW_SOLUTIONS',  16 * 0x1041);      // Show solutions.
define('GAME_REVIEW_GENERALFEEDBACK', 32 * 0x1041); // Show general feedback.

/**
 * Given an object containing all the necessary data, will create a new instance and return the id number of the new instance.
 *
 * @param object $game An object from the form in mod.html
 *
 * @return int The id of the newly inserted game record
 **/
function game_add_instance($game) {
    global $DB;

    $game->timemodified = time();
    game_before_add_or_update( $game);

    // May have to add extra stuff in here.

    $id = $DB->insert_record("game", $game);

    $game = $DB->get_record_select( 'game', "id=$id");

    // Do the processing required after an add or an update.
    game_grade_item_update( $game);

    return $id;
}

/**
 * Given an object containing all the necessary data, this function will update an existing instance with new data.
 *
 * @param object $game An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function game_update_instance($game) {
    global $DB;

    $game->timemodified = time();
    $game->id = $game->instance;

    if (!isset( $game->glossarycategoryid)) {
        $game->glossarycategoryid = 0;
    }

    if (!isset( $game->glossarycategoryid2)) {
        $game->glossarycategoryid2 = 0;
    }

    if ($game->grade == '') {
        $game->grade = 0;
    }

    if (!isset( $game->param1)) {
        $game->param1 = 0;
    }

    if ($game->param1 == '') {
        $game->param1 = 0;
    }

    if (!isset( $game->param2)) {
        $game->param2 = 0;
    }

    if ($game->param2 == '') {
        $game->param2 = 0;
    }

    if (!isset( $game->questioncategoryid)) {
        $game->questioncategoryid = 0;
    }

    game_before_add_or_update( $game);

    if (!$DB->update_record("game", $game)) {
        return false;
    }

    // Do the processing required after an add or an update.
    game_grade_item_update( $game);

    return true;
}

/**
 * Updates some fields before writing to database.
 *
 * @param stdClass $game
 */
function game_before_add_or_update(&$game) {
    if (isset( $game->questioncategoryid)) {
        $pos = strpos( $game->questioncategoryid, ',');
        if ($pos != false) {
            $game->questioncategoryid = substr( $game->questioncategoryid, 0, $pos);
        }
    }

    if ($game->gamekind == 'millionaire') {
        $pos = strpos( '-'.$game->param8, '#');
        if ($pos > 0) {
            $game->param8 = hexdec(substr( $game->param8, $pos));
        }
    } else if ($game->gamekind == 'snakes') {
        $s = '';
        if ($game->param3 == 0) {
            // Means user defined.
            $draftitemid = $game->param4;
            if (isset( $game->id)) {
                $cmg = get_coursemodule_from_instance('game', $game->id, $game->course);
                $modcontext = game_get_context_module_instance( $cmg->id);
                $attachmentoptions = array('subdirs' => 0, 'maxbytes' => 9999999, 'maxfiles' => 1);
                file_save_draft_area_files($draftitemid, $modcontext->id, 'mod_game', 'snakes_file', $game->id,
                    array('subdirs' => 0, 'maxbytes' => 9999999, 'maxfiles' => 1));
                $game->param5 = 1;
            }

            if (isset( $_POST[ 'snakes_cols'])) {
                $fields = array( 'snakes_data', 'snakes_cols', 'snakes_rows', 'snakes_headerx', 'snakes_headery',
                    'snakes_footerx', 'snakes_footery', 'snakes_width', 'snakes_height');
                foreach ($fields as $f) {
                    $s .= '#'.$f.':'.$_POST[ $f];
                }
                $s = substr( $s, 1);
            }
        }
        $game->param9 = $s;
    }
}

/**
 * Given an ID of an instance of this module, this function will permanently delete the instance and any data that depends on it.
 *
 * @param int $gameid Id of the module instance
 * @return boolean Success/Failure
 **/
function game_delete_instance($gameid) {
    global $DB;

    // Delete any dependent records here.
    $aids = array();
    if (($recs = $DB->get_records( 'game_attempts', array( 'gameid' => $gameid))) != false) {
        $ids = '';

        $count = 0;
        foreach ($recs as $rec) {
            $ids .= ( $ids == '' ? $rec->id : ','.$rec->id);
            if (++$count > 10) {
                $aids[] = $ids;
                $count = 0;
                $ids = '';
            }
        }
        if ($ids != '') {
            $aids[] = $ids;
        }
    }

    foreach ($aids as $ids) {
        $tables = array( 'game_hangman', 'game_cross', 'game_cryptex', 'game_millionaire',
            'game_bookquiz', 'game_sudoku', 'game_snakes');

        foreach ($tables as $t) {
            $sql = "DELETE FROM {".$t."} WHERE id IN (".$ids.')';
            if (!$DB->execute( $sql)) {
                return false;
            }
        }
    }

    $tables = array( 'game_attempts', 'game_grades', 'game_bookquiz_questions', 'game_queries', 'game_repetitions');
    foreach ($tables as $t) {
        if (!$DB->delete_records( $t, array( 'gameid' => $gameid))) {
            return false;
        }
    }

    $tables = array( 'game_export_javame', 'game_export_html', 'game');
    foreach ($tables as $table) {
        if (!$DB->delete_records( $table, array( 'id' => $gameid))) {
            return false;
        }
    }

    return true;
}

/**
 * Return a small object with summary information about what a user has done
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param string $mod
 * @param stdClass $game
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 **/
function game_user_outline($course, $user, $mod, $game) {
    global $DB;

    if ($grade = $DB->get_record_select('game_grades', "userid=$user->id AND gameid = $game->id", null, 'id,score,timemodified')) {

        $result = new stdClass;
        if ((float)$grade->score) {
            $result->info = get_string('grade').':&nbsp;'.round($grade->score * $game->grade, $game->decimalpoints).' '.
                            get_string('percent', 'game').':&nbsp;'.round(100 * $grade->score, $game->decimalpoints).' %';
        }
        $result->time = $grade->timemodified;
        return $result;
    }

    return null;
}

/**
 * Print a detailed representation of what a user has done with a given particular game,(user activity reports).

 * @param stdClass $course
 * @param stdClass $user
 * @param string $mod
 * @param stdClass $game
 */
function game_user_complete($course, $user, $mod, $game) {
    global $DB;

    if ($attempts = $DB->get_records_select('game_attempts', "userid='$user->id' AND gameid='$game->id'", null, 'attempt ASC')) {
        if ($game->grade && $grade = $DB->get_record('game_grades', array( 'userid' => $user->id, 'gameid' => $game->id))) {
            echo get_string('grade').': '.game_format_score( $game, $grade->score).'/'.$game->grade.'<br />';
        }
        foreach ($attempts as $attempt) {
            echo get_string('attempt', 'game').' '.$attempt->attempt.': ';
            if ($attempt->timefinish == 0) {
                print_string( 'unfinished');
            } else {
                echo game_format_score( $game, $attempt->score).'/'.$game->grade;
            }
            echo ' - '.userdate($attempt->timelastattempt).'<br />';
        }
    } else {
        print_string('noattempts', 'game');
    }

    return true;
}

/**
 * Given a course and a time, this module should find recent activity that has occurred in game activities and print it out.
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 *
 * @param stdClass $course
 * @param int $isteacher
 * @param int $timestart
 *
 * @return True if anything was printed, otherwise false.
 */
function game_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function game_cron() {
    global $CFG;

    return true;
}

/**
 * Must return an array of grades for a given instance of this module, indexed by user.
 *
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $gameid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function game_grades($gameid) {
    // Must return an array of grades, indexed by user, and a max grade.

    global $DB;

    $game = $DB->get_record( 'game', array( 'id' => intval($gameid)));
    if (empty($game) || empty($game->grade)) {
        return null;
    }

    $return = new stdClass;
    $return->grades = $DB->get_records_menu('game_grades', 'gameid', $game->id, '', "userid, score * {$game->grade}");
    $return->maxgrade = $game->grade;

    return $return;
}

/**
 * Return grade for given user or all users.
 *
 * @param stdClass $game
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function game_get_user_grades($game, $userid=0) {
    global $DB;

    $user = $userid ? "AND u.id = $userid" : "";

    $sql = 'SELECT u.id, u.id AS userid, '.$game->grade.
            ' * g.score AS rawgrade, g.timemodified AS dategraded, MAX(a.timefinish) AS datesubmitted
            FROM {user} u, {game_grades} g, {game_attempts} a
            WHERE u.id = g.userid AND g.gameid = '.$game->id.' AND a.gameid = g.gameid AND u.id = a.userid';
    if ($userid != 0) {
        $sql .= ' AND u.id='.$userid;
    }
    $sql .= ' GROUP BY u.id, g.score, g.timemodified';

    return $DB->get_records_sql( $sql);
}

/**
 * Must return an array of user records (all data) who are participants for a given instance of game.
 *
 * @param int $gameid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function game_get_participants($gameid) {
    return false;
}

/**
 * This function returns if a scale is being used by one game it it has support for grading and scales.
 *
 * @param int $gameid ID of an instance of this module
 * @param int $scaleid
 * @return mixed
 * @todo Finish documenting this function
 **/
function game_scale_used ($gameid, $scaleid) {
    $return = false;

    return $return;
}

/**
 * Update grades in central gradebook
 *
 * @param object $game null means all games
 * @param int $userid specific user only, 0 mean all
 * @param boolean $nullifnone
 */
function game_update_grades($game=null, $userid=0, $nullifnone=true) {
    global $CFG;

    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        if (file_exists( $CFG->libdir.'/gradelib.php')) {
            require_once($CFG->libdir.'/gradelib.php');
        } else {
            return;
        }
    }

    if ($game != null) {
        if ($grades = game_get_user_grades($game, $userid)) {
            game_grade_item_update($game, $grades);

        } else if ($userid and $nullifnone) {
            $grade = new stdClass;
            $grade->userid   = $userid;
            $grade->rawgrade = null;
            game_grade_item_update( $game, $grade);

        } else {
            game_grade_item_update( $game);
        }

    } else {
        $sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
                  FROM {game} a, {course_modules} cm, {modules} m
                 WHERE m.name='game' AND m.id=cm.module AND cm.instance=a.id";
        if ($rs = $DB->get_recordset_sql( $sql)) {
            while ($game = $DB->rs_fetch_next_record( $rs)) {
                if ($game->grade != 0) {
                    game_update_grades( $game, 0, false);
                } else {
                    game_grade_item_update( $game);
                }
            }
            $DB->rs_close( $rs);
        }
    }
}

/**
 * Create grade item for given game
 *
 * @param object $game object with extra cmidnumber
 * @param stdClass $grades
 * @return int 0 if ok, error code otherwise
 */
function game_grade_item_update($game, $grades=null) {
    global $CFG;

    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        if (file_exists( $CFG->libdir.'/gradelib.php')) {
            require_once($CFG->libdir.'/gradelib.php');
        } else {
            return;
        }
    }

    if (array_key_exists('cmidnumber', $game)) { // Tt may not be always present.
        $params = array('itemname' => $game->name, 'idnumber' => $game->cmidnumber);
    } else {
        $params = array('itemname' => $game->name);
    }

    if ($game->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $game->grade;
        $params['grademin']  = 0;

    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/game', $game->course, 'mod', 'game', $game->id, 0, $grades, $params);
}


/**
 * Delete grade item for given game
 *
 * @param object $game object
 * @return object game
 */
function game_grade_item_delete( $game) {
    global $CFG;

    if (file_exists( $CFG->libdir.'/gradelib.php')) {
        require_once($CFG->libdir.'/gradelib.php');
    } else {
        return;
    }

    return grade_update('mod/game', $game->course, 'mod', 'game', $game->id, 0, null, array('deleted' => 1));
}

/**
 * Returns all game graded users since a given time for specified game
 *
 * @param stdClass $activities
 * @param int $index
 * @param int $timestart
 * @param int $courseid
 * @param int $cmid
 * @param int $userid
 * @param int $groupid
 */
function game_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
    global $DB, $COURSE, $USER;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array( 'id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];

    if ($userid) {
        $userselect = "AND u.id = $userid";
    } else {
        $userselect = "";
    }

    if ($groupid) {
        $groupselect = "AND gm.groupid = $groupid";
        $groupjoin   = "JOIN {groups_members} gm ON  gm.userid=u.id";
    } else {
        $groupselect = "";
        $groupjoin   = "";
    }

    $sql = "SELECT qa.*, qa.gameid, q.grade, u.lastname,u.firstname,u.picture ".
    "FROM {game_attempts} qa JOIN {game} q ON q.id = qa.gameid JOIN {user} u ON u.id = qa.userid $groupjoin ".
    "WHERE qa.timefinish > $timestart AND q.id = $cm->instance $userselect $groupselect ".
    "ORDER BY qa.timefinish ASC";
    if (!$attempts = $DB->get_records_sql( $sql)) {
         return;
    }

    $cmcontext      = game_get_context_module_instance( $cm->id);
    $grader          = has_capability('moodle/grade:viewall', $cmcontext);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cmcontext);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $cmcontext);
    $groupmode       = groups_get_activity_groupmode($cm, $course);

    if (is_null($modinfo->groups)) {
        $modinfo->groups = groups_get_user_groups($course->id); // Load all my groups and cache it in modinfo.
    }

    $aname = format_string($cm->name, true);
    foreach ($attempts as $attempt) {
        if ($attempt->userid != $USER->id) {
            if (!$grader) {
                // Grade permission required.
                continue;
            }

            if ($groupmode == SEPARATEGROUPS and !$accessallgroups) {
                $usersgroups = groups_get_all_groups($course->id, $attempt->userid, $cm->groupingid);
                if (!is_array($usersgroups)) {
                    continue;
                }
                $usersgroups = array_keys($usersgroups);
                $interset = array_intersect($usersgroups, $modinfo->groups[$cm->id]);
                if (empty($intersect)) {
                    continue;
                }
            }
        }

        $tmpactivity = new stdClass;

        $tmpactivity->type      = 'game';
        $tmpactivity->gameid    = $attempt->gameid;
        $tmpactivity->cmid      = $cm->id;
        $tmpactivity->name      = $aname;
        $tmpactivity->sectionnum = $cm->sectionnum;
        $tmpactivity->timestamp = $attempt->timefinish;

        $tmpactivity->content = new stdClass;
        $tmpactivity->content->attemptid = $attempt->id;
        $tmpactivity->content->sumgrades = $attempt->score * $attempt->grade;
        $tmpactivity->content->maxgrade  = $attempt->grade;
        $tmpactivity->content->attempt   = $attempt->attempt;

        $tmpactivity->user = new stdClass;
        $tmpactivity->user->userid   = $tmpactivity->user->id = $attempt->userid;
        $tmpactivity->user->fullname = fullname($attempt, $viewfullnames);
        $tmpactivity->user->firstname = $attempt->firstname;
        $tmpactivity->user->lastname = $attempt->lastname;
        $tmpactivity->user->picture  = $attempt->picture;
        $tmpactivity->user->imagealt  = $attempt->imagealt;
        $tmpactivity->user->email  = $attempt->email;

        $activities[$index++] = $tmpactivity;
    }
}

/**
 * Prints recent activity.
 *
 * @param stdClass $activity
 * @param int $courseid
 * @param stdClass $detail
 * @param array $modnames
 */
function game_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $CFG, $OUTPUT;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="forum-recent">';

    echo "<tr><td class=\"userpicture\" valign=\"top\">";
    echo $OUTPUT->user_picture($activity->user, array('courseid' => $courseid));
    echo "</td><td>";

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo "<img src=\"$CFG->modpixpath/{$activity->type}/icon.gif\" ".
             "class=\"icon\" alt=\"$modname\" />";
        echo "<a href=\"{$CFG->wwwroot}/mod/game/view.php?id={$activity->cmid}\">{$activity->name}</a>";
        echo '</div>';
    }

    echo '<div class="grade">';
    echo  get_string("attempt", "game")." {$activity->content->attempt}: ";
    $grades = "({$activity->content->sumgrades} / {$activity->content->maxgrade})";

    echo "<a href=\"{$CFG->wwwroot}/mod/game/review.php".
        "?attempt={$activity->content->attemptid}&q={$activity->gameid}\">$grades</a>";
    echo '</div>';

    echo '<div class="user">';
    echo "<a href=\"{$CFG->wwwroot}/user/view.php?id={$activity->user->userid}&amp;course=$courseid\">"
         ."{$activity->user->fullname}</a> - ".userdate($activity->timestamp);
    echo '</div>';

    echo "</td></tr></table>";
}


/**
 * Removes all grades from gradebook
 *
 * @param int $courseid
 * @param string $type
 **/
function game_reset_gradebook($courseid, $type='') {
    global $DB;

    $sql = "SELECT q.*, cm.idnumber as cmidnumber, q.course as courseid
              FROM {game} q, {course_modules} cm, {modules} m
             WHERE m.name='game' AND m.id=cm.module AND cm.instance=q.id AND q.course=$courseid";

    if ($games = $DB->get_records_sql( $sql)) {
        foreach ($games as $game) {
            game_grade_item_update( $game, 'reset');
        }
    }
}

/**
 * What supports.
 *
 * @uses FEATURE_GRADE_HAS_GRADE
 * @param string $feature
 * @return bool True if quiz supports feature
 */
function game_supports($feature) {
    switch($feature) {
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_RATE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;

        default:
            return null;
    }
}

/**
 * get extra capabilities
 *
 * @return array all other caps used in module
 */
function game_get_extra_capabilities() {
    global $DB, $CFG;

    require_once($CFG->libdir.'/questionlib.php');
    $caps = question_get_all_capabilities();
    $reportcaps = $DB->get_records_select_menu('capabilities', 'name LIKE ?', array('quizreport/%'), 'id,name');
    $caps = array_merge($caps, $reportcaps);
    $caps[] = 'moodle/site:accessallgroups';

    return $caps;
}

/**
 * Return a textual summary of the number of attemtps that have been made at a particular game,
 *
 * @param object $game the game object. Only $game->id is used at the moment.
 * @param object $cm the cm object. Only $cm->course, $cm->groupmode and $cm->groupingid fields are used at the moment.
 * @param boolean $returnzero if false (default), when no attempts have been made '' is returned instead of 'Attempts: 0'.
 * @param int $currentgroup if there is a concept of current group where this method is being called
 *         (e.g. a report) pass it in here. Default 0 which means no current group.
 * @return string a string like "Attempts: 123", "Attemtps 123 (45 from your groups)" or
 *          "Attemtps 123 (45 from this group)".
 */
function game_num_attempt_summary($game, $cm, $returnzero = false, $currentgroup = 0) {
    global $CFG, $USER, $DB;

    $numattempts = $DB->count_records('game_attempts', array('gameid' => $game->id, 'preview' => 0));

    if ($numattempts || $returnzero) {
        if (groups_get_activity_groupmode($cm)) {
            $a = new stdClass();
            $a->total = $numattempts;
            if ($currentgroup) {
                $a->group = $DB->count_records_sql('SELECT count(1) FROM ' .
                        '{game_attempts} qa JOIN ' .
                        '{groups_members} gm ON qa.userid = gm.userid ' .
                        'WHERE gameid = ? AND preview = 0 AND groupid = ?', array($game->id, $currentgroup));
                return get_string('attemptsnumthisgroup', 'quiz', $a);
            } else if ($groups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid)) {
                list($usql, $params) = $DB->get_in_or_equal(array_keys($groups));
                $a->group = $DB->count_records_sql('SELECT count(1) FROM ' .
                        '{game_attempts} qa JOIN ' .
                        '{groups_members} gm ON qa.userid = gm.userid ' .
                        'WHERE gameid = ? AND preview = 0 AND ' .
                        "groupid $usql", array_merge(array($game->id), $params));
                return get_string('attemptsnumyourgroups', 'quiz', $a);
            }
        }
        return get_string('attemptsnum', 'quiz', $numattempts);
    }
    return '';
}

/**
 * Converts score of game to grade.
 *
 * @param stdClass $game
 * @param float $score
 *
 * @return float  the score
 */
function game_format_score($game, $score) {
    return format_float($game->grade * $score / 100, $game->decimalpoints);
}

/**
 * Converts grade to score.
 *
 * @param stdClass $game
 * @param float $grade
 *
 * @return foat score
 */
function game_format_grade($game, $grade) {
    return format_float($grade, $game->decimalpoints);
}

/**
 * get grading options
 *
 * @return the options for calculating the quiz grade from the individual attempt grades.
 */
function game_get_grading_options() {
    return array (
            GAME_GRADEHIGHEST => get_string('gradehighest', 'quiz'),
            GAME_GRADEAVERAGE => get_string('gradeaverage', 'quiz'),
            GAME_ATTEMPTFIRST => get_string('attemptfirst', 'quiz'),
            GAME_ATTEMPTLAST  => get_string('attemptlast', 'quiz'));
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $gamenode
 * @return void
 */
function game_extend_settings_navigation($settings, $gamenode) {
    global $PAGE, $CFG, $DB;

    $context = $PAGE->cm->context;

    if (!has_capability('mod/game:viewreports', $context)) {
        return;
    }

    if (has_capability('mod/game:view', $context)) {
        $url = new moodle_url('/mod/game/view.php', array('id' => $PAGE->cm->id));
        $gamenode->add(get_string('info', 'game'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/info', ''));
    }

    if (has_capability('mod/game:manage', $context)) {
        $url = new moodle_url('/course/modedit.php', array('update' => $PAGE->cm->id, 'return' => true, 'sesskey' => sesskey()));
        $gamenode->add(get_string('edit', 'moodle', ''), $url, navigation_node::TYPE_SETTING,
            null, null, new pix_icon('t/edit', ''));
    }

    if (has_capability('mod/game:viewreports', $context)) {
        $url = new moodle_url('/mod/game/showanswers.php', array('q' => $PAGE->cm->instance));
        $reportnode = $gamenode->add(get_string('showanswers', 'game'), $url, navigation_node::TYPE_SETTING,
            null, null, new pix_icon('i/item', ''));
    }

    if (has_capability('mod/game:viewreports', $context)) {
        $url = new moodle_url('/mod/game/showattempts.php', array('q' => $PAGE->cm->instance));
        $reportnode = $gamenode->add(get_string('showattempts', 'game'), $url, navigation_node::TYPE_SETTING,
            null, null, new pix_icon('f/explore', ''));
    }

    if (has_capability('mod/game:viewreports', $context)) {
        $game = $DB->get_record('game', array("id" => $PAGE->cm->instance));
        $courseid = $game->course;

        switch( $game->gamekind) {
            case 'bookquiz':
                $url = new moodle_url('/mod/game/bookquiz/questions.php',  array('q' => $PAGE->cm->instance));
                $exportnode = $gamenode->add( get_string('bookquiz_questions', 'game'), $url, navigation_node::TYPE_SETTING,
                    null, null, new pix_icon('i/item', ''));
                break;
            case 'hangman':
                $url = new moodle_url('/mod/game/export.php', array( 'id' => $PAGE->cm->id,
                    'courseid' => $courseid, 'target' => 'html'));
                $gamenode->add( get_string('export_to_html', 'game'), $url, navigation_node::TYPE_SETTING,
                    null, null, new pix_icon('i/item', ''));

                $url = new moodle_url('/mod/game/export.php', array( 'id' => $PAGE->cm->id,
                    'courseid' => $courseid, 'target' => 'javame'));
                $gamenode->add( get_string('export_to_javame', 'game'), $url, navigation_node::TYPE_SETTING,
                    null, null, new pix_icon('i/item', ''));
                break;
            case 'snakes':
            case 'cross':
            case 'millionaire':
                $url = new moodle_url('/mod/game/export.php', array( 'q' => $game->id,
                    'courseid' => $courseid, 'target' => 'html'));
                $gamenode->add(get_string('export_to_html', 'game'), $url, navigation_node::TYPE_SETTING,
                    null, null, new pix_icon('i/item', ''));
                break;
        }
    }

    $gamenode->make_active();
}

/* Returns an array of game type objects to construct menu list when adding new game  */
require($CFG->dirroot.'/version.php');
if ($branch >= '31') {
    define('USE_GET_SHORTCUTS', '1');
}

if (!defined('USE_GET_SHORTCUTS')) {
    /**
     * Shows kind of games
     */
    function game_get_types() {
        global $DB;

        $config = get_config('game');

        $types = array();

        $type = new stdClass;
        $type->modclass = MOD_CLASS_ACTIVITY;
        $type->type = "game_group_start";
        $type->typestr = '--'.get_string( 'modulenameplural', 'game');
        $types[] = $type;

        $hide = ( isset( $config->hidehangman) ? ($config->hidehangman != 0) : false);

        if ($hide == false) {
            $type = new stdClass;
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "game&amp;type=hangman";
            $type->typestr = get_string('game_hangman', 'game');
            $types[] = $type;
        }

        if (isset( $config->hidecross)) {
            $hide = ($config->hidecross != 0);
        } else {
            $hide = false;
        }

        if ($hide == false) {
            $type = new stdClass;
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "game&amp;type=cross";
            $type->typestr = get_string('game_cross', 'game');
            $types[] = $type;
        }

        if (isset( $config->hidecryptex)) {
            $hide = ($config->hidecryptex != 0);
        } else {
            $hide = false;
        }

        if ($hide == false) {
            $type = new stdClass;
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "game&amp;type=cryptex";
            $type->typestr = get_string('game_cryptex', 'game');
            $types[] = $type;
        }

        $hide = (isset( $config->hidemillionaire) ? ($config->hidemillionaire != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "game&amp;type=millionaire";
            $type->typestr = get_string('game_millionaire', 'game');
            $types[] = $type;
        }

        $hide = (isset( $config->hidesudoku) ? ($config->hidesudoku != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "game&amp;type=sudoku";
            $type->typestr = get_string('game_sudoku', 'game');
            $types[] = $type;
        }

        $hide = (isset( $config->hidesnakes) ? ($config->hidesnakes != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "game&amp;type=snakes";
            $type->typestr = get_string('game_snakes', 'game');
            $types[] = $type;
        }

        $hide = (isset( $config->hidehiddenpicture) ? ($config->hidehiddenpicture != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "game&amp;type=hiddenpicture";
            $type->typestr = get_string('game_hiddenpicture', 'game');
            $types[] = $type;
        }

        $hide = (isset( $config->hidebookquiz) ? ($config->hidebookquiz != 0) : false);
        if ($hide == false) {
            if ($DB->get_record( 'modules', array( 'name' => 'book'), 'id,id')) {
                $type = new stdClass;
                $type->modclass = MOD_CLASS_ACTIVITY;
                $type->type = "game&amp;type=bookquiz";
                $type->typestr = get_string('game_bookquiz', 'game');
                $types[] = $type;
            }
        }

        $type = new stdClass;
        $type->modclass = MOD_CLASS_ACTIVITY;
        $type->type = "game_group_end";
        $type->typestr = '--';
        $types[] = $type;

        return $types;
    }
}

if (defined('USE_GET_SHORTCUTS')) {
    /**
     * Returns an array of game type objects to construct menu list when adding new game
     *
     * @param stdClass $defaultitem
     */
    function game_get_shortcuts($defaultitem) {
        global $DB, $CFG;
        $config = get_config('game');
        $types = array();
        $hide = ( isset( $config->hidehangman) ? ($config->hidehangman != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->archetype = MOD_CLASS_ACTIVITY;
            $type->type = "game&type=hangman";
            $type->name = preg_replace('/.*type=/', '', $type->type);
            $type->title = get_string('pluginname', 'game').' - '.get_string('game_hangman', 'game');
            $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
            if (empty($type->help) && !empty($type->name) &&
                get_string_manager()->string_exists('help' . $type->name, 'game')) {
                    $type->help = get_string('help' . $type->name, 'game');
            }
            $types[] = $type;
        }
        if (isset( $config->hidecross)) {
            $hide = ($config->hidecross != 0);
        } else {
            $hide = false;
        }
        if ($hide == false) {
            $type = new stdClass;
            $type->archetype = MOD_CLASS_ACTIVITY;
            $type->type = "game&type=cross";
            $type->name = preg_replace('/.*type=/', '', $type->type);
            $type->title = get_string('pluginname', 'game').' - '.get_string('game_cross', 'game');
            $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
            if (empty($type->help) && !empty($type->name) &&
                get_string_manager()->string_exists('help' . $type->name, 'game')) {
                    $type->help = get_string('help' . $type->name, 'game');
            }
            $types[] = $type;
        }
        if (isset( $config->hidecryptex)) {
            $hide = ($config->hidecryptex != 0);
        } else {
            $hide = false;
        }
        if ($hide == false) {
            $type = new stdClass;
            $type->archetype = MOD_CLASS_ACTIVITY;
            $type->type = "game&type=cryptex";
            $type->title = get_string('pluginname', 'game').' - '.get_string('game_cryptex', 'game');
            $type->name = preg_replace('/.*type=/', '', $type->type);
            $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
            if (empty($type->help) && !empty($type->name) &&
                get_string_manager()->string_exists('help' . $type->name, 'game')) {
                    $type->help = get_string('help' . $type->name, 'game');
            }
            $types[] = $type;
        }
        $hide = (isset( $config->hidemillionaire) ? ($config->hidemillionaire != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->archetype = MOD_CLASS_ACTIVITY;
            $type->type = "game&type=millionaire";
            $type->title = get_string('pluginname', 'game').' - '.get_string('game_millionaire', 'game');
            $type->name = preg_replace('/.*type=/', '', $type->type);
            $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
            if (empty($type->help) && !empty($type->name) &&
                get_string_manager()->string_exists('help' . $type->name, 'game')) {
                    $type->help = get_string('help' . $type->name, 'game');
            }
            $types[] = $type;
        }
        $hide = (isset( $config->hidesudoku) ? ($config->hidesudoku != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->archetype = MOD_CLASS_ACTIVITY;
            $type->type = "game&type=sudoku";
            $type->title = get_string('pluginname', 'game').' - '.get_string('game_sudoku', 'game');
            $type->name = preg_replace('/.*type=/', '', $type->type);
            $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
            if (empty($type->help) && !empty($type->name) &&
                get_string_manager()->string_exists('help' . $type->name, 'game')) {
                    $type->help = get_string('help' . $type->name, 'game');
            }
            $types[] = $type;
        }
        $hide = (isset( $config->hidesnakes) ? ($config->hidesnakes != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->archetype = MOD_CLASS_ACTIVITY;
            $type->type = "game&type=snakes";
            $type->title = get_string('pluginname', 'game').' - '.get_string('game_snakes', 'game');
            $type->name = preg_replace('/.*type=/', '', $type->type);
            $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
            if (empty($type->help) && !empty($type->name) &&
                get_string_manager()->string_exists('help' . $type->name, 'game')) {
                    $type->help = get_string('help' . $type->name, 'game');
            }
            $types[] = $type;
        }
        $hide = (isset( $config->hidehiddenpicture) ? ($config->hidehiddenpicture != 0) : false);
        if ($hide == false) {
            $type = new stdClass;
            $type->archetype = MOD_CLASS_ACTIVITY;
            $type->type = "game&type=hiddenpicture";
            $type->title = get_string('pluginname', 'game').' - '.get_string('game_hiddenpicture', 'game');
            $type->name = preg_replace('/.*type=/', '', $type->type);
            $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
            if (empty($type->help) && !empty($type->name) &&
                get_string_manager()->string_exists('help' . $type->name, 'game')) {
                    $type->help = get_string('help' . $type->name, 'game');
            }
            $types[] = $type;
        }
        $hide = (isset( $config->hidebookquiz) ? ($config->hidebookquiz != 0) : false);
        if ($hide == false) {
            if ($DB->get_record( 'modules', array( 'name' => 'book'), 'id,id')) {
                $type = new stdClass;
                $type->archetype = MOD_CLASS_ACTIVITY;
                $type->type = "game&type=bookquiz";
                $type->title = get_string('pluginname', 'game').' - '.get_string('game_bookquiz', 'game');
                $type->name = preg_replace('/.*type=/', '', $type->type);
                $type->link = new moodle_url($defaultitem->link, array('type' => $type->name));
                if (empty($type->help) && !empty($type->name) &&
                    get_string_manager()->string_exists('help' . $type->name, 'game')) {
                        $type->help = get_string('help' . $type->name, 'game');
                }
                $types[] = $type;
            }
        }
        return $types;
    }
}

/**
 * Serves the game attachents.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param boolean $forcedownload
 *
 * @return boolean false if not exists file
 */
function mod_game_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea == 'questiontext') {
        $questionid = $args[ 0];
        $file = $args[ 1];
        $a = explode( '/', $context->path);
        if (!$contextcourse = game_get_context_course_instance( $course->id)) {
            print_error('nocontext');
        }
        $a = array( 'component' => 'question', 'filearea' => 'questiontext',
            'itemid' => $questionid, 'filename' => $file, 'contextid' => $contextcourse->id);
        $rec = $DB->get_record( 'files', $a);

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash($rec->pathnamehash) or $file->is_directory()) {
            return false;
        }

        // Finally send the file.
        send_stored_file($file, 0, 0, true); // download MUST be forced - security!
    } else if ($filearea == 'answer') {
        $answerid = $args[ 0];
        $file = $args[ 1];

        if (!$contextcourse = game_get_context_course_instance( $course->id)) {
            print_error('nocontext');
        }
        $rec = $DB->get_record( 'files', array( 'component' => 'question', 'filearea' => 'answer',
            'itemid' => $answerid, 'filename' => $file, 'contextid' => $contextcourse->id));

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash($rec->pathnamehash) or $file->is_directory()) {
            return false;
        }

        // Finally send the file.
        send_stored_file($file, 0, 0, true); // Download MUST be forced - security!
    }

    $filearea = $args[ 0];
    $filename = $args[ 1];

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_game/$filearea/$cm->instance/$filename";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, true); // download MUST be forced - security!
}

/**
 * Add reset buttons to form.
 *
 * @param object $mform form passed by reference
 */
function game_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'gameheader', get_string('modulenameplural', 'game'));
    $mform->addElement('checkbox', 'reset_game_all', get_string('reset_game_all', 'game'));
    $mform->addElement('checkbox', 'reset_game_deleted_course', get_string('reset_game_deleted_course', 'game'));
}

/**
 * Course reset form defaults.
 *
 * @param stdClass $course
 *
 * @return array
 */
function game_reset_course_form_defaults($course) {
    return array('reset_game_all' => 0, 'reset_game_deleted_course' => 0);
}

/**
 * Actual implementation of the reset course functionality, delete all the Game responses for course $data->courseid.
 *
 * @param stdClass $data the data submitted from the reset course.
 *
 * @return array status array
 */
function game_reset_userdata($data) {
    global $DB;

    $componentstr = get_string('modulenameplural', 'game');
    $status = array();
    $fs = get_file_storage();

    for ($i = 1; $i <= 2; $i++) {
        if ($i == 1) {
            if (empty($data->reset_game_all)) {
                continue;
            }
            $allgamessql = 'SELECT g.id FROM {game} g WHERE g.course = '.$data->courseid;
            $allattemptssql = 'SELECT ga.id FROM {game} g LEFT JOIN {game_attempts} ga ON g.id = ga.gameid WHERE g.course = '.
                $data->courseid;
            $newstatus = array('component' => $componentstr, 'item' => get_string('reset_game_all', 'game'), 'error' => false);
        } else if ($i == 2) {
            if (empty($data->reset_game_deleted_course)) {
                continue;
            }

            $allgamessql = 'SELECT g.id FROM {game} g WHERE NOT EXISTS( SELECT * FROM {course} c WHERE c.id = g.course)';
            $allattemptssql = 'SELECT ga.id FROM {game_attempts} ga '.
                'WHERE NOT EXISTS( SELECT * FROM {game} g WHERE ga.gameid = g.id)';
            $newstatus = array('component' => $componentstr, 'item' => get_string('reset_game_deleted_course', 'game'),
                'error' => false);
        }

        $recs = $DB->get_recordset_sql($allgamessql);
        if ($recs->valid()) {
            foreach ($recs as $rec) {
                if (!$cm = get_coursemodule_from_instance('game', $rec->id)) {
                    continue;
                }
                $context = game_get_context_module_instance( $cm->id);
                $fs->delete_area_files($context->id, 'mod_game', 'gnakes_file');
                $fs->delete_area_files($context->id, 'mod_game', 'gnakes_board');

                // Reset grades.
                $game = $DB->get_record_select( 'game', 'id='.$rec->id, null, 'id,name,course ');
                $grades = null;
                $params = array('itemname' => $game->name, 'idnumber' => 0);
                $params['reset'] = true;
                grade_update('mod/game', $game->course, 'mod', 'game', $game->id, 0, $grades, $params);
            }
        }

        $DB->delete_records_select('game_bookquiz', "id IN ($allgamessql)");
        $DB->delete_records_select('game_bookquiz_chapters', "attemptid IN ($allattemptssql)");
        $DB->delete_records_select('game_bookquiz_questions', "gameid IN ($allgamessql)");
        $DB->delete_records_select('game_cross', "id IN ($allgamessql)");
        $DB->delete_records_select('game_cryptex', "id IN ($allgamessql)");
        $DB->delete_records_select('game_export_html', "id IN ($allgamessql)");
        $DB->delete_records_select('game_export_javame', "id IN ($allgamessql)");
        $DB->delete_records_select('game_grades', "gameid IN ($allgamessql)");
        $DB->delete_records_select('game_hangman', "id IN ($allgamessql)");
        $DB->delete_records_select('game_hiddenpicture', "id IN ($allgamessql)");
        $DB->delete_records_select('game_millionaire', "id IN ($allgamessql)");
        $DB->delete_records_select('game_queries', "gameid IN ($allgamessql)");
        $DB->delete_records_select('game_repetitions', "gameid IN ($allgamessql)");
        $DB->delete_records_select('game_snakes', "id IN ($allgamessql)");
        $DB->delete_records_select('game_sudoku', "id IN ($allgamessql)");

        if ($i == 2) {
            $DB->delete_records_select('game_attempts', "NOT EXISTS (SELECT * FROM {game} g WHERE {game_attempts}.gameid=g.id)");
        } else {
            $DB->delete_records_select('game_attempts', "gameid IN ($allgamessql)");
        }

        $status[] = $newstatus;
    }

    if (empty($data->reset_game_deleted_course)) {
        return $status;
    }

    // Delete data from deleted games.
    $a = array( 'bookquiz', 'cross', 'cryptex', 'grades', 'bookquiz_questions', 'export_html', 'export_javame', 'hangman',
            'hiddenpicture', 'millionaire', 'snakes', 'sudoku');
    foreach ($a as $table) {
        $DB->delete_records_select( 'game_'.$table, "NOT EXISTS( SELECT * FROM {game} g WHERE {game_$table}.id=g.id)");
    }

    $a = array( 'grades', 'queries', 'repetitions');
    foreach ($a as $table) {
        $DB->delete_records_select( 'game_'.$table, "NOT EXISTS( SELECT * FROM {game} g WHERE {game_$table}.gameid=g.id)");
    }

    $a = array( 'bookquiz_chapters');
    foreach ($a as $table) {
        $DB->delete_records_select( 'game_'.$table,
            "NOT EXISTS( SELECT * FROM {game_attempts} ga WHERE {game_$table}.attemptid=ga.id)");
    }

    return $status;
}

/**
 * Obtains the automatic completion state for this module based on any conditions in game settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 *
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function game_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;

    if (($cm->completion == 0) or ($cm->completion == 1)) {
        // Completion option is not enabled so just return $type.
        return $type;
    }

    if ($cm->completionview) {
        // Just want to view it. Not needed it.
        return true;
    }

    if (! $game = $DB->get_record('game', array('id' => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

    // Check for passing grade.
    if ($game->completionpass) {
        require_once($CFG->libdir . '/gradelib.php');
        $item = grade_item::fetch(array('courseid' => $course->id, 'itemtype' => 'mod',
                'itemmodule' => 'game', 'iteminstance' => $cm->instance, 'outcomeid' => null));
        if ($item) {
            $grades = grade_grade::fetch_users_grades($item, array($userid), false);
            if (!empty($grades[$userid])) {
                return $grades[$userid]->is_passed($item);
            }
        }
    }

    return false;
}

/**
 * Checks if scale is being used by any instance of Game
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $scaleid
 * @return boolean True if the scale is used by any Game
 */
function game_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('game', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns the context instance of a Module. Is the same for all version of Moodle.
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $moduleid
 * @return stdClass context
 */
function game_get_context_module_instance( $moduleid) {
    if (class_exists( 'context_module')) {
        return context_module::instance( $moduleid);
    }

    return get_context_instance( CONTEXT_MODULE, $moduleid);
}

/**
 * Returns the context instance of a course. Is the same for all version of Moodle.
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $courseid
 *
 * @return stdClass context
 */
function game_get_context_course_instance( $courseid) {
    if (class_exists( 'context_course')) {
        return context_course::instance( $courseid);
    }

    return get_context_instance( CONTEXT_COURSE, $courseid);
}

/**
 * Returns the url of a pix file. Is the same for all versions of Moodle.
 *
 * @param string $filename
 * @param string $module
 *
 * @return stdClass url
 */
function game_pix_url( $filename, $module='') {
    global $OUTPUT;

    if (game_get_moodle_version() >= '03.03') {
        return $OUTPUT->image_url($filename, $module);
    } else {
        return $OUTPUT->pix_url($filename, $module);
    }
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_game_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules'])
        || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completionattemptsexhausted':
                if (empty($val)) {
                    continue;
                }
                $descriptions[] = get_string('completionattemptsexhausteddesc', 'quiz');
                break;
            case 'completionpass':
                if (empty($val)) {
                    continue;
                }
                $descriptions[] = get_string('completionpassdesc', 'quiz', format_time($val));
                break;
            default:
                break;
        }
    }
    return $descriptions;
}
