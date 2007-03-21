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
    $inactive = array();
    $row  = array();
    questionbank_navigation_tabs($row, $context, $course->id);
    $tabs[] = $row;

    print_tabs($tabs, $currenttab, array());

?>
