<?php
require_once('../../config.php');
$topics = array();
$titles = array();
for ($i=1; ; $i++){
    $button = optional_param("button$i", '', PARAM_ALPHAEXT);
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
                $topics[$i] = helplink('emoticons', get_string('helpemoticons'));
                break;
            case 'richtext' :
                $topics[$i] = helplink('richtext', get_string('helprichtext'));
                break;
            case 'text' :
                $topics[$i] = helplink('text', get_string('helptext'));
                break;
            default :
                error('Unknown help topic '.$item);
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
print_header();
print_simple_box_start('center', '96%');
print_heading(get_string('editorhelptopics'));


echo '<ul>';
foreach ($topics as $i => $topic){
    echo('<li>'.$topics[$i].'</li>');
}
echo '</ul>';
print_simple_box_end();
// End of page.
close_window_button();
global $CFG;
echo '<p align="center"><a href="'.$CFG->wwwroot.'/help.php?file=index.html">'. get_string('helpindex') .'</a></p>';

$CFG->docroot = '';   // We don't want a doc link here
print_footer('none');
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

?>