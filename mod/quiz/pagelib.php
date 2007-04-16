<?php // $Id$

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot.'/course/lib.php'); // needed for some blocks

define('PAGE_QUIZ_VIEW',   'mod-quiz-view');

page_map_class(PAGE_QUIZ_VIEW, 'page_quiz');

$DEFINEDPAGES = array(PAGE_QUIZ_VIEW);

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
  
    function get_type() {
        return PAGE_QUIZ_VIEW;
    }
}

?>
