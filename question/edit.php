<?php // $Id$
/**
* Page to edit the question bank
*
* TODO: add logging
*
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by Gustav Delius and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package questionbank
*/

    require_once("../config.php");
    require_once("editlib.php");

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('questions');

    question_showbank_actions($thispageurl, $cm);

    $context = $contexts->lowest();
    $streditingquestions = get_string('editquestions', "quiz");
    if ($cm!==null) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $contexts->lowest())
            ? update_module_button($cm->id, $COURSE->id, get_string('modulename', $cm->modname))
            : "";
        $navlinks = array();
        $navlinks[] = array('name' => get_string('modulenameplural', $cm->modname), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$COURSE->id", 'type' => 'activity');
        $navlinks[] = array('name' => format_string($module->name), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?id={$cm->id}", 'type' => 'title');
        $navlinks[] = array('name' => $streditingquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);
        print_header_simple($streditingquestions, '', $navigation, "", "", true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'questions';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/$cm->modname/tabs.php");
    } else {
        // Print basic page layout.
        $navlinks = array();
        $navlinks[] = array('name' => $streditingquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);

        print_header_simple($streditingquestions, '', $navigation);

        // print tabs
        $currenttab = 'questions';
        include('tabs.php');
    }


    echo '<table class="boxaligncenter" border="0" cellpadding="2" cellspacing="0">';
    echo '<tr><td valign="top">';

    question_showbank('questions', $contexts, $thispageurl, $cm, $pagevars['qpage'], $pagevars['qperpage'], $pagevars['qsortorder'], $pagevars['qsortorderdecoded'],
                    $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'], $pagevars['showquestiontext']);

    echo '</td></tr>';
    echo '</table>';

    print_footer($COURSE);
?>
