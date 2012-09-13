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
 * Provides the interface for viewing and adding high scores
 *
 * @package    mod
 * @subpackage lesson
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/** include required files */
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');

$id      = required_param('id', PARAM_INT);             // Course Module ID
$mode    = optional_param('mode', '', PARAM_ALPHA);
$link = optional_param('link', 0, PARAM_INT);

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);;
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST));

require_login($course, false, $cm);

$url = new moodle_url('/mod/lesson/highscores.php', array('id'=>$id));
if ($mode !== '') {
    $url->param('mode', $mode);
}
if ($link !== 0) {
    $url->param('link', $link);
}
$PAGE->set_url($url);

$context = context_module::instance($cm->id);

switch ($mode) {
    case 'add':
        // Ensure that we came from view.php
        if (!confirm_sesskey() or !data_submitted()) {
            print_error('invalidformdata');
        }
        break;

    case 'save':
        if (confirm_sesskey() and $form = data_submitted($CFG->wwwroot.'/mod/lesson/view.php')) {
            $name = trim(optional_param('name', '', PARAM_CLEAN));

            // Make sure it is not empty
            if (empty($name)) {
                $lesson->add_message(get_string('missingname', 'lesson'));
                $mode = 'add';
                break;
            }
            // Check for censored words
            $filterwords = explode(',', get_string('censorbadwords'));
            foreach ($filterwords as $filterword) {
                if (strstr($name, $filterword)) {
                    $lesson->add_message(get_string('namereject', 'lesson'));
                    $mode = 'add';
                    break;
                }
            }
            // Bad word was found
            if ($mode == 'add') {
                break;
            }
            $params = array ("lessonid" => $lesson->id, "userid" => $USER->id);
            if (!$grades = $DB->get_records_select('lesson_grades', "lessonid = :lessonid", $params, 'completed')) {
                print_error('cannotfindfirstgrade', 'lesson');
            }

            if (!$newgrade = $DB->get_record_sql("SELECT *
                                               FROM {lesson_grades}
                                              WHERE lessonid = :lessonid
                                                AND userid = :userid
                                           ORDER BY completed DESC", $params, true)) {
                print_error('cannotfindnewestgrade', 'lesson');
            }

            // Check for multiple submissions
            if ($DB->record_exists('lesson_high_scores', array('gradeid' => $newgrade->id))) {
                print_error('onpostperpage', 'lesson');
            }

            // Find out if we need to delete any records
            if ($highscores = $DB->get_records_sql("SELECT h.*, g.grade
                                                 FROM {lesson_grades} g, {lesson_high_scores} h
                                                WHERE h.gradeid = g.id
                                                AND h.lessonid = :lessonid
                                                ORDER BY g.grade DESC", $params)) {
                // Only count unique scores in our total for max high scores
                $uniquescores = array();
                foreach ($highscores as $highscore) {
                    $uniquescores[$highscore->grade] = 1;
                }
                if (count($uniquescores) >= $lesson->maxhighscores) {
                    // Top scores list is full, might need to delete a score
                    $flag = true;
                    // See if the new score is already listed in the top scores list
                    // if it is listed, then dont need to delete any records
                    foreach ($highscores as $highscore) {
                        if ($newgrade->grade == $highscore->grade) {
                            $flag = false;
                        }
                    }
                    if ($flag) {
                        // Pushing out the lowest score (could be multiple records)
                        $lowscore = 0;
                        foreach ($highscores as $highscore) {
                            if (empty($lowscore) or $lowscore > $highscore->grade) {
                                $lowscore = $highscore->grade;
                            }
                        }
                        // Now, delete all high scores with the low score
                        foreach ($highscores as $highscore) {
                            if ($highscore->grade == $lowscore) {
                                $DB->delete_records('lesson_high_scores', array('id' => $highscore->id));
                            }
                        }
                    }
                }
            }

            $newhighscore = new stdClass;
            $newhighscore->lessonid = $lesson->id;
            $newhighscore->userid = $USER->id;
            $newhighscore->gradeid = $newgrade->id;
            $newhighscore->nickname = $name;

            $DB->insert_record('lesson_high_scores', $newhighscore);

            // Log it
            add_to_log($course->id, 'lesson', 'update highscores', "highscores.php?id=$cm->id", $name, $cm->id);

            $lesson->add_message(get_string('postsuccess', 'lesson'), 'notifysuccess');
            redirect("$CFG->wwwroot/mod/lesson/highscores.php?id=$cm->id&amp;link=1");
        } else {
            print_error('invalidformdata');
        }
        break;
}

// Log it
add_to_log($course->id, 'lesson', 'view highscores', "highscores.php?id=$cm->id", $lesson->name, $cm->id);

$lessonoutput = $PAGE->get_renderer('mod_lesson');
echo $lessonoutput->header($lesson, $cm, 'highscores', false, null, get_string('viewhighscores', 'lesson'));

switch ($mode) {
    case 'add':
        echo $lessonoutput->add_highscores_form($lesson);
        break;
    default:
        $params = array ("lessonid" => $lesson->id);
        if (!$grades = $DB->get_records_select("lesson_grades", "lessonid = :lessonid", $params, "completed")) {
            $grades = array();
        }

        echo $OUTPUT->heading(get_string("topscorestitle", "lesson", $lesson->maxhighscores), 4);

        if (!$highscores = $DB->get_records_select("lesson_high_scores", "lessonid = :lessonid", $params)) {
            echo $OUTPUT->heading(get_string("nohighscores", "lesson"), 3);
        } else {
            foreach ($highscores as $highscore) {
                $grade = $grades[$highscore->gradeid]->grade;
                $topscores[$grade][] = $highscore->nickname;
            }
            krsort($topscores);

            $table = new html_table();
            $table->align = array('center', 'left', 'right');
            $table->wrap = array();
            $table->width = "30%";
            $table->cellspacing = '10px';
            $table->size = array('*', '*', '*');

            $table->head = array(get_string("rank", "lesson"), get_string('name'), get_string("scores", "lesson"));

            $printed = 0;
            while (true) {
                $temp = current($topscores);
                $score = key($topscores);
                $rank = $printed + 1;
                sort($temp);
                foreach ($temp as $student) {
                    $table->data[] = array($rank, $student, $score.'%');
                }
                $printed++;
                if (!next($topscores) || !($printed < $lesson->maxhighscores)) {
                    break;
                }
            }
            echo html_writer::table($table);
        }

        if (!has_capability('mod/lesson:manage', $context)) {  // teachers don't need the links
            echo $OUTPUT->box_start('mdl-align');
            echo $OUTPUT->box_start('lessonbutton standardbutton');
            if ($link) {
                echo html_writer::link(new moodle_url('/course/view.php', array('id'=>$course->id)), get_string("returntocourse", "lesson"));
            } else {
                echo html_writer::link(new moodle_url('/course/view.php', array('id'=>$course->id)), get_string("cancel", "lesson")). ' ';
                echo html_writer::link(new moodle_url('/mod/lesson/view.php', array('id'=>$cm->id, 'viewed'=>'1')), get_string("startlesson", "lesson"));
            }
            echo $OUTPUT->box_end();
            echo $OUTPUT->box_end();
        }
        break;
}

echo $lessonoutput->footer();