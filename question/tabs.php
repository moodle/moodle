<?php  // $Id$
/**
 * Sets up the tabs used by the question bank editing page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

/// This file to be included so we can assume config.php has already been included.

    if (!isset($currenttab)) {
        $currenttab = '';
    }
    if (!isset($course)) {
        error('No course specified');
    }

    $tabs = array();
    $row  = array();
    $inactive = array();

    $row[] = new tabobject('questions', "$CFG->wwwroot/question/edit.php?courseid=$course->id", get_string('questions', 'quiz'), get_string('editquizquestions', 'quiz'));
    $row[] = new tabobject('categories', "$CFG->wwwroot/question/category.php?id=$course->id", get_string('categories', 'quiz'), get_string('editqcats', 'quiz'));
    $row[] = new tabobject('import', "$CFG->wwwroot/question/import.php?course=$course->id", get_string('import', 'quiz'), get_string('importquestions', 'quiz'));
    $row[] = new tabobject('export', "$CFG->wwwroot/question/export.php?courseid=$course->id", get_string('export', 'quiz'), get_string('exportquestions', 'quiz'));

    $tabs[] = $row;

    print_tabs($tabs, $currenttab, $inactive);

?>
