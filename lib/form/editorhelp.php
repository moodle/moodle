<?php
require_once('../../config.php');

$PAGE->set_url('/lib/form/editorhelp.php');

$topics = array();
$titles = array();
for ($i=1; ; $i++){
    $button = optional_param("button$i", '', PARAM_ALPHANUMEXT);
    if ($button){
        switch ($button){
            case 'reading' :
                $topics[$i] = helplink('reading', get_string('helpreading'), 'moodle', false, true);
                break;
            case 'writing' :
                $topics[$i] = helplink('writing', get_string('helpwriting'));
                break;
            case 'questions' :
                $topics[$i] = helplink('questions', get_string('helpquestions'));
                break;
            case 'emoticons' :
                debugging("You are referring to the old help file 'emoticons'. " .
                        "This was renamed to 'emoticons2' becuase of MDL-13233. " .
                        "Please update your code.", DEBUG_DEVELOPER);
                // Fall through.
            case 'emoticons2' :
                $topics[$i] = helplink('emoticons2', get_string('helpemoticons'));
                break;
            case 'richtext' :
                debugging("You are referring to the old help file 'richtext'. " .
                        "This was renamed to 'richtext2' becuase of MDL-13233. " .
                        "Please update your code.", DEBUG_DEVELOPER);
                // Fall through.
            case 'richtext2' :
                $topics[$i] = helplink('richtext2', get_string('helprichtext'));
                break;
            case 'text' :
                debugging("You are referring to the old help file 'text'. " .
                        "This was renamed to 'text2' becuase of MDL-13233. " .
                        "Please update your code.", DEBUG_DEVELOPER);
                // Fall through.
            case 'text2' :
                $topics[$i] = helplink('text2', get_string('helptext'));
                break;
            default :
                print_error('unknownhelp', '', '', $item);
        }
    } else {
        $keyword = optional_param("keyword$i", '', PARAM_ALPHAEXT);
        if ('' == $keyword) {
            break;//exit for loop -  no more help items
        }
        $title = optional_param("title$i", '', PARAM_NOTAGS);
        $module = optional_param("module$i", 'moodle', PARAM_ALPHAEXT);
        $func[$i] = 'helpbutton';
        $topics[$i] = helplink($keyword, $title, $module);
    }

}
echo $OUTPUT->header();
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('editorhelptopics'));


echo '<ul>';
foreach ($topics as $i => $topic){
    echo('<li>'.$topics[$i].'</li>');
}
echo '</ul>';
echo $OUTPUT->box_end();
// End of page.
echo $OUTPUT->close_window_button();
global $CFG;
echo '<p align="center"><a href="'.$CFG->wwwroot.'/help.php?file=index.html">'. get_string('helpindex') .'</a></p>';

$CFG->docroot = '';   // We don't want a doc link here
echo $OUTPUT->footer();
die;
/**
 * A function to return a simple link to a help page. We don't want a popup here
 * since these links are displayed in a pop up already.
 *
 * @param string $page
 * @param string $linktext
 * @param string $module
 * @return string
 */
function helplink($page, $linktext='', $module='moodle'){
    global $CFG;
    return "<a href=\"$CFG->wwwroot/help.php?module=$module&amp;file=$page.html\">$linktext</a>";
}
