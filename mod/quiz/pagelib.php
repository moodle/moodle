<?php // $Id$

require_once($CFG->libdir.'/pagelib.php');

define('PAGE_QUIZ_VIEW',   'mod-quiz-view');

page_map_class(PAGE_QUIZ_VIEW, 'page_quiz');

/**
 * Class that models the behavior of a quiz
 *
 * @author Jon Papaioannou
 * @package pages
 */

class page_quiz extends page_generic_activity {

    function init_quick($data) {
        if(empty($data->pageid)) {
            error('Cannot quickly initialize page: empty course id');
        }
        $this->activityname = 'quiz';
        parent::init_quick($data);
    }

    function print_header($title, $morebreadcrumbs = NULL) {
        global $USER, $CFG;

        $this->init_full();
        $replacements = array(
            '%fullname%' => $this->activityrecord->name
        );
        foreach($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }

        $breadcrumbs = array(
            $this->courserecord->shortname => $CFG->wwwroot.'/course/view.php?id='.$this->courserecord->id,
            get_string('modulenameplural', 'quiz') => $CFG->wwwroot.'/mod/quiz/index.php?id='.$this->courserecord->id,
            $this->activityrecord->name => $CFG->wwwroot.'/mod/quiz/view.php?id='.$this->modulerecord->id,
        );

        if(!empty($morebreadcrumbs)) {
            $breadcrumbs = array_merge($breadcrumbs, $morebreadcrumbs);
        }

        $total     = count($breadcrumbs);
        $current   = 1;
        $crumbtext = '';
        foreach($breadcrumbs as $text => $href) {
            if($current++ == $total) {
                $crumbtext .= ' '.$text;
            }
            else {
                $crumbtext .= ' <a href="'.$href.'">'.$text.'</a> ->';
            }
        }

        if(empty($morebreadcrumbs) && $this->user_allowed_editing()) {
            $buttons = '<table><tr><td><form target="'.$CFG->framename.'" method="get" action="edit.php">'.
               '<input type="hidden" name="quizid" value="'.$this->activityrecord->id.'" />'.
               '<input type="submit" value="'.get_string('editquestions', 'quiz').'" /></form></td><td>'.
               update_module_button($this->modulerecord->id, $this->courserecord->id, get_string('modulename', 'quiz')).
               '</td>'.
               '<td><form target="'.$CFG->framename.'" method="get" action="view.php">'.
               '<input type="hidden" name="id" value="'.$this->modulerecord->id.'" />'.
               '<input type="hidden" name="edit" value="'.($this->user_is_editing()?'off':'on').'" />'.
               '<input type="submit" value="'.get_string($this->user_is_editing()?'turneditingoff':'blocksaddedit').'" /></form></td></tr></table>';
        }
        else {
            $buttons = '&nbsp;';
        }
        print_header($title, $this->courserecord->fullname, $crumbtext, '', '', true, $buttons, navmenu($this->courserecord, $this->modulerecord));

    }

    function get_type() {
        return PAGE_QUIZ_VIEW;
    }
}

?>