<?php  // $Id$
/**
* Sets up the tabs used by the lesson pages for teachers.
*
* This file was adapted from the mod/quiz/tabs.php
*
* @version $Id$
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package lesson
*/

/// This file to be included so we can assume config.php has already been included.

    if (empty($lesson)) {
        error('You cannot call this script in that way');
    }
    if (!isset($currenttab)) {
        $currenttab = '';
    }
    if (!isset($cm)) {
        $cm = get_coursemodule_from_instance('lesson', $lesson->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    }
    if (!isset($course)) {
        $course = get_record('course', 'id', $lesson->course);
    }

    $tabs = $row = $inactive = $activated = array();

/// user attempt count for reports link hover (completed attempts - much faster)
    $counts           = new stdClass;
    $counts->attempts = count_records('lesson_grades', 'lessonid', $lesson->id);
    $counts->student  = $course->student;
    
    $row[] = new tabobject('view', "$CFG->wwwroot/mod/lesson/view.php?id=$cm->id", get_string('preview', 'lesson'), get_string('previewlesson', 'lesson', format_string($lesson->name)));
    $row[] = new tabobject('edit', "$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id", get_string('edit', 'lesson'), get_string('edit', 'moodle', format_string($lesson->name)));
    $row[] = new tabobject('reports', "$CFG->wwwroot/mod/lesson/report.php?id=$cm->id", get_string('reports', 'lesson'), get_string('viewreports', 'lesson', $counts));
    if (has_capability('mod/lesson:edit', $context)) {
        $row[] = new tabobject('essay', "$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id", get_string('manualgrading', 'lesson'));
    }
    if ($lesson->highscores) {
        $row[] = new tabobject('highscores', "$CFG->wwwroot/mod/lesson/highscores.php?id=$cm->id", get_string('highscores', 'lesson'));
    }

    $tabs[] = $row;


    switch ($currenttab) {
        case 'reportoverview':
        case 'reportdetail':
        /// sub tabs for reports (overview and detail)
            $inactive[] = 'reports';
            $activated[] = 'reports';

            $row    = array();
            $row[]  = new tabobject('reportoverview', "$CFG->wwwroot/mod/lesson/report.php?id=$cm->id&amp;action=reportoverview", get_string('overview', 'lesson'));
            $row[]  = new tabobject('reportdetail', "$CFG->wwwroot/mod/lesson/report.php?id=$cm->id&amp;action=reportdetail", get_string('detailedstats', 'lesson'));
            $tabs[] = $row;
            break;
        case 'collapsed':
        case 'full':
        case 'single':
        /// sub tabs for edit view (collapsed and expanded aka full)
            $inactive[] = 'edit';
            $activated[] = 'edit';
            
            $row    = array();
            $row[]  = new tabobject('collapsed', "$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id&amp;mode=collapsed", get_string('collapsed', 'lesson'));
            $row[]  = new tabobject('full', "$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id&amp;mode=full", get_string('full', 'lesson'));
            $tabs[] = $row;
            break;
    }

    print_tabs($tabs, $currenttab, $inactive, $activated);

?>
