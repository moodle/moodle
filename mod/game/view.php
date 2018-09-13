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
 * This file is the entry point to the game module. All pages are rendered from here
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/mod/game/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID.

if (! $cm = get_coursemodule_from_id('game', $id)) {
    print_error('invalidcoursemodule');
}
if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}
if (! $game = $DB->get_record('game', array('id' => $cm->instance))) {
    print_error('invalidcoursemodule');
}

// Check login and get context.
require_login($course->id, false, $cm);
$context = game_get_context_module_instance( $cm->id);
require_capability('mod/game:view', $context);

$timenow = time();

// Cache some other capabilites we use several times.
$canattempt = true;
$strtimeopenclose = '';
if ($timenow < $game->timeopen) {
    $canattempt = false;
    $strtimeopenclose = get_string('gamenotavailable', 'game', userdate($game->timeopen));
} else if ($game->timeclose && $timenow > $game->timeclose) {
    $strtimeopenclose = get_string("gameclosed", "game", userdate($game->timeclose));
    $canattempt = false;
} else {
    if ($game->timeopen) {
        $strtimeopenclose = get_string('gameopenedon', 'game', userdate($game->timeopen));
    }
    if ($game->timeclose) {
        $strtimeopenclose = get_string('gamecloseson', 'game', userdate($game->timeclose));
    }
}
if (has_capability('mod/game:manage', $context)) {
    $canattempt = true;
}

// Log this request.
if (game_use_events()) {
    require( 'classes/event/course_module_viewed.php');
        \mod_game\event\course_module_viewed::viewed($game, $context)->trigger();
} else {
    add_to_log($course->id, 'game', 'view', "view.php?id=$cm->id", $game->id, $cm->id);
}

// Mark as viewed.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Here have to check if not need summarize.
if ($game->disablesummarize) {
    if (game_can_start_new_attempt( $game)) {
        require_once( 'attempt.php');
        die;
    }
}

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/game/view.php', array('id' => $cm->id));

$edit = optional_param('edit', -1, PARAM_BOOL);
if ($edit != -1 && $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

$title = $course->shortname . ': ' . format_string($game->name);

if ($PAGE->user_allowed_editing() && !empty($CFG->showblocksonmodpages)) {
    $buttons = '<table><tr><td><form method="get" action="view.php"><div>'.
        '<input type="hidden" name="id" value="'.$cm->id.'" />'.
        '<input type="hidden" name="edit" value="'.($PAGE->user_is_editing() ? 'off' : 'on').'" />'.
        '<input type="submit" value="'.
        get_string($PAGE->user_is_editing() ? 'blockseditoff' : 'blocksediton').
        '" /></div></form></td></tr></table>';
    $PAGE->set_button($buttons);
}

$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// Print game name and description.
echo $OUTPUT->heading(format_string($game->name));

// Display information about this game.
echo $OUTPUT->box_start('quizinfo');
if ($game->attempts != 1) {
    echo get_string('gradingmethod', 'quiz', game_get_grading_option_name($game->grademethod));
}
echo $OUTPUT->box_end();

// Show number of attempts summary to those who can view reports.
if (has_capability('mod/game:viewreports', $context)) {
    if ($strattemptnum = game_get_user_attempts($game->id, $USER->id)) {
        echo get_string( 'attempts', 'game').': '.count( $strattemptnum);
        if ($game->maxattempts) {
            echo ' ('.get_string( 'max', 'quiz').': '.$game->maxattempts.')';
        }
    }
}

// Get this user's attempts.
$attempts = game_get_user_attempts($game->id, $USER->id);
$lastfinishedattempt = end($attempts);
$unfinished = false;
if ($unfinishedattempt = game_get_user_attempt_unfinished($game->id, $USER->id)) {
    $attempts[] = $unfinishedattempt;
    $unfinished = true;
}
$numattempts = count($attempts);

// Work out the final grade, checking whether it was overridden in the gradebook.
$mygrade = game_get_best_grade($game, $USER->id);
$mygradeoverridden = false;
$gradebookfeedback = '';

$gradinginfo = grade_get_grades($course->id, 'mod', 'game', $game->id, $USER->id);
if (!empty($gradinginfo->items)) {
    $item = $gradinginfo->items[0];
    if (isset($item->grades[$USER->id])) {
        $grade = $item->grades[$USER->id];

        if ($grade->overridden) {
            $mygrade = $grade->grade + 0; // Convert to number.
            $mygradeoverridden = true;
        }
        if (!empty($grade->str_feedback)) {
            $gradebookfeedback = $grade->str_feedback;
        }
    }
}

// Print table with existing attempts.
if ($attempts) {
    echo $OUTPUT->heading(get_string('summaryofattempts', 'quiz'));

    // Work out which columns we need, taking account what data is available in each attempt.
    list($someoptions, $alloptions) = game_get_combined_reviewoptions($game, $attempts, $context);

    $attemptcolumn = $game->attempts != 1;

    $gradecolumn = $someoptions->scores && ($game->grade > 0);
    $overallstats = $alloptions->scores;

    // Prepare table header.
    $table = new html_table();
    $table->attributes['class'] = 'generaltable gameattemptsummary';
    $table->head = array();
    $table->align = array();
    $table->size = array();
    if ($attemptcolumn) {
        $table->head[] = get_string('attempt', 'game');
        $table->align[] = 'center';
        $table->size[] = '';
    }
    $table->head[] = get_string('timecompleted', 'game');
    $table->align[] = 'left';
    $table->size[] = '';

    if ($gradecolumn) {
        $table->head[] = get_string('grade') . ' / ' . game_format_grade( $game, $game->grade);
        $table->align[] = 'center';
        $table->size[] = '';
    }

    $table->head[] = get_string('timetaken', 'game');
    $table->align[] = 'left';
    $table->size[] = '';

    // One row for each attempt.
    foreach ($attempts as $attempt) {
        $attemptoptions = game_get_reviewoptions($game, $attempt, $context);
        $row = array();

        // Add the attempt number, making it a link, if appropriate.
        if ($attemptcolumn) {
            if ($attempt->preview) {
                $row[] = get_string('preview', 'game');
            } else {
                $row[] = $attempt->attempt;
            }
        }

        // Prepare strings for time taken and date completed.
        $timetaken = '';
        $datecompleted = '';
        if ($attempt->timefinish > 0) {
            // Attempt has finished.
            $timetaken = format_time($attempt->timefinish - $attempt->timestart);
            $datecompleted = userdate($attempt->timefinish);
        } else {
            // The a is still in progress.
            $timetaken = format_time($timenow - $attempt->timestart);
            $datecompleted = '';
        }
        $row[] = $datecompleted;

        // Ouside the if because we may be showing feedback but not grades.
        $attemptgrade = game_score_to_grade($attempt->score, $game);

        if ($gradecolumn) {
            if ($attemptoptions->scores) {
                $formattedgrade = game_format_grade($game, $attemptgrade);
                // Highlight the highest grade if appropriate.
                if ($overallstats && !$attempt->preview && $numattempts > 1 && !is_null($mygrade) &&
                    $attemptgrade == $mygrade && $game->grademethod == QUIZ_GRADEHIGHEST) {
                        $table->rowclasses[$attempt->attempt] = 'bestrow';
                }

                $row[] = $formattedgrade;
            } else {
                $row[] = '';
            }
        }

        $row[] = $timetaken;

        if ($attempt->preview) {
            $table->data['preview'] = $row;
        } else {
            $table->data[$attempt->attempt] = $row;
        }
    } // End of loop over attempts.
    echo html_writer::table($table);
}

// Print information about the student's best score for this game if possible.
if ($numattempts && $gradecolumn && !is_null($mygrade)) {
    $resultinfo = '';

    if ($overallstats) {
        $a = new stdClass;
        $a->grade = game_format_grade($game, $mygrade);
        $a->maxgrade = game_format_grade($game, $game->grade);
        $a = get_string('outofshort', 'quiz', $a);
        $resultinfo .= $OUTPUT->heading(get_string('yourfinalgradeis', 'game', $a), 2, 'main');
    }

    if ($mygradeoverridden) {
        $resultinfo .= '<p class="overriddennotice">'.get_string('overriddennotice', 'grades')."</p>\n";
    }

    if ($gradebookfeedback) {
        $resultinfo .= $OUTPUT->heading(get_string('comment', 'game'), 3, 'main');
        $resultinfo .= '<p class="gameteacherfeedback">'.$gradebookfeedback."</p>\n";
    }

    if ($resultinfo) {
        echo $OUTPUT->box($resultinfo, 'generalbox', 'feedback');
    }
}

// Determine if we should be showing a start/continue attempt button or a button to go back to the course page.
echo $OUTPUT->box_start('gameattempt');
$buttontext = ''; // This will be set something if as start/continue attempt button should appear.

if ($unfinished) {
    if ($canattempt) {
        $buttontext = get_string('continueattemptgame', 'game');
    }
} else {
    // Game is finished. Check if max number of attempts is reached.
    if (!game_can_start_new_attempt( $game)) {
        $canattempt = false;
    }

    if ($canattempt) {
        echo '<br>';

        if ($numattempts == 0) {
            $buttontext = get_string('attemptgamenow', 'game');
        } else {
            $buttontext = get_string('reattemptgame', 'game');
        }
    }
}

// Now actually print the appropriate button.
echo $strtimeopenclose;

if ($buttontext) {
    global $OUTPUT;

    $strconfirmstartattempt = '';

    // Show the start button, in a div that is initially hidden.
    echo '<div id="gamestartbuttondiv">';
    $url = new moodle_url($CFG->wwwroot.'/mod/game/attempt.php', array('id' => $id));
    $button = new single_button($url, $buttontext);
    echo $OUTPUT->render($button);
    echo "</div>\n";
} else {
    echo $OUTPUT->continue_button($CFG->wwwroot . '/course/view.php?id=' . $course->id);
}
echo $OUTPUT->box_end();

if ($game->highscore > 0) {
    // Display high score.
    game_highscore( $game);
}

if (has_capability('mod/game:manage', $context)) {
    require( 'check.php');
    $s = game_check_common_problems( $context, $game);
    if ($s != '') {
        echo '<br>'.$s;
    }
}

echo $OUTPUT->footer();

/**
 * Computes high score for this game. Shows the names of $game->highscore students.
 *
 * @param stdClass $game
 */
function game_highscore( $game) {
    global $CFG, $DB, $OUTPUT;

    $sql = "SELECT userid, MAX(score) as maxscore".
    " FROM {$CFG->prefix}game_attempts ".
    " WHERE gameid={$game->id} AND score > 0".
    " GROUP BY userid".
    " ORDER BY score DESC";
    $score = 0;
    $recs = $DB->get_records_sql( $sql);
    foreach ($recs as $rec) {
        $score = $rec->maxscore;
    }
    if ($score == 0) {
        return;
    }

    $sql = "SELECT u.id, u.lastname, u.firstname, MAX(ga.score) as maxscore".
    " FROM {$CFG->prefix}user u, {$CFG->prefix}game_attempts ga ".
    " WHERE ga.gameid={$game->id} AND ga.userid = u.id".
    " GROUP BY u.id,u.lastname,u.firstname".
    " HAVING MAX(ga.score) >= $score".
    " ORDER BY MAX(ga.score) DESC";

    $recs = $DB->get_records_sql( $sql, null, 0, $game->highscore);
    if (count( $recs) == 0) {
        return false;
    }

    // Prepare table header.
    $table = new html_table();
    $table->attributes['class'] = 'generaltable gameattemptsummary';
    $table->head = array();
    $table->align = array();
    $table->size = array();

    $table->head[] = get_string('students');
    $table->align[] = 'left';
    $table->size[] = '';

    $table->head[] = get_string('percent', 'grades');
    $table->align[] = 'center';
    $table->size[] = '';

    foreach ($recs as $rec) {
        echo "<tr>";
        $row = array();
        $row[] = $rec->firstname.' '.$rec->lastname;
        $row[] = round( $rec->maxscore * 100).' %';

        $table->data[$rec->id] = $row;
    }

    echo '<br>'.$OUTPUT->heading(get_string('col_highscores', 'game'));

    echo html_writer::table($table);
}
