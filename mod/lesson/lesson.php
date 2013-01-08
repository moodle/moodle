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
 * Handles lesson actions
 *
 * ACTIONS handled are:
 *    confirmdelete
 *    delete
 *    move
 *    moveit
 * @package    mod
 * @subpackage lesson
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/lesson/locallib.php');

$id     = required_param('id', PARAM_INT);         // Course Module ID
$action = required_param('action', PARAM_ALPHA);   // Action
$pageid = required_param('pageid', PARAM_INT);

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST));

require_login($course, false, $cm);

$url = new moodle_url('/mod/lesson/lesson.php', array('id'=>$id,'action'=>$action));
$PAGE->set_url($url);

$context = context_module::instance($cm->id);
require_capability('mod/lesson:edit', $context);
require_sesskey();

$lessonoutput = $PAGE->get_renderer('mod_lesson');

/// Process the action
switch ($action) {
    case 'confirmdelete':
        $PAGE->navbar->add(get_string($action, 'lesson'));

        $thispage = $lesson->load_page($pageid);

        echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('deletingpage', 'lesson', format_string($thispage->title)));
        echo $OUTPUT->heading(get_string("deletingpage", "lesson", format_string($thispage->title)));
        // print the jumps to this page
        $params = array("lessonid" => $lesson->id, "pageid" => $pageid);
        if ($answers = $DB->get_records_select("lesson_answers", "lessonid = :lessonid AND jumpto = :pageid + 1", $params)) {
            echo $OUTPUT->heading(get_string("thefollowingpagesjumptothispage", "lesson"));
            echo "<p align=\"center\">\n";
            foreach ($answers as $answer) {
                if (!$title = $DB->get_field("lesson_pages", "title", array("id" => $answer->pageid))) {
                    print_error('cannotfindpagetitle', 'lesson');
                }
                echo $title."<br />\n";
            }
        }
        echo $OUTPUT->confirm(get_string("confirmdeletionofthispage","lesson"),"lesson.php?action=delete&id=$cm->id&pageid=$pageid","view.php?id=$cm->id");

        break;
    case 'move':
        $PAGE->navbar->add(get_string($action, 'lesson'));

        $title = $DB->get_field("lesson_pages", "title", array("id" => $pageid));

        echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('moving', 'lesson', format_String($title)));
        echo $OUTPUT->heading(get_string("moving", "lesson", format_string($title)));

        $params = array ("lessonid" => $lesson->id, "prevpageid" => 0);
        if (!$page = $DB->get_record_select("lesson_pages", "lessonid = :lessonid AND prevpageid = :prevpageid", $params)) {
            print_error('cannotfindfirstpage', 'lesson');
        }

        echo "<center><table cellpadding=\"5\" border=\"1\">\n";
        echo "<tr><td><a href=\"lesson.php?id=$cm->id&amp;sesskey=".sesskey()."&amp;action=moveit&amp;pageid=$pageid&amp;after=0\"><small>".
            get_string("movepagehere", "lesson")."</small></a></td></tr>\n";
        while (true) {
            if ($page->id != $pageid) {
                if (!$title = trim(format_string($page->title))) {
                    $title = "<< ".get_string("notitle", "lesson")."  >>";
                }
                echo "<tr><td><b>$title</b></td></tr>\n";
                echo "<tr><td><a href=\"lesson.php?id=$cm->id&amp;sesskey=".sesskey()."&amp;action=moveit&amp;pageid=$pageid&amp;after={$page->id}\"><small>".
                    get_string("movepagehere", "lesson")."</small></a></td></tr>\n";
            }
            if ($page->nextpageid) {
                if (!$page = $DB->get_record("lesson_pages", array("id" => $page->nextpageid))) {
                    print_error('cannotfindnextpage', 'lesson');
                }
            } else {
                // last page reached
                break;
            }
        }
        echo "</table>\n";

        break;
    case 'delete':
        $thispage = $lesson->load_page($pageid);
        $thispage->delete();
        redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
        break;
    case 'moveit':
        $after = (int)required_param('after', PARAM_INT); // target page

        $pages = $lesson->load_all_pages();

        if (!array_key_exists($pageid, $pages) || ($after!=0 && !array_key_exists($after, $pages))) {
            print_error('cannotfindpages', 'lesson', "$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
        }
        $pagetomove = clone($pages[$pageid]);
        unset($pages[$pageid]);

        $pageids = array();
        if ($after === 0) {
            $pageids['p0'] = $pageid;
        }
        foreach ($pages as $page) {
            $pageids[] = $page->id;
            if ($page->id == $after) {
                $pageids[] = $pageid;
            }
        }

        $pageidsref = $pageids;
        reset($pageidsref);
        $prev = 0;
        $next = next($pageidsref);
        foreach ($pageids as $pid) {
            if ($pid === $pageid) {
                $page = $pagetomove;
            } else {
                $page = $pages[$pid];
            }
            if ($page->prevpageid != $prev || $page->nextpageid != $next) {
                $page->move($next, $prev);
            }
            $prev = $page->id;
            $next = next($pageidsref);
            if (!$next) {
                $next = 0;
            }
        }

        redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
        break;
    default:
        print_error('unknowaction');
        break;
}

echo $lessonoutput->footer();
