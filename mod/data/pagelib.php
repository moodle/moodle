<?php // $Id$

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot.'/course/lib.php'); // needed for some blocks

define('PAGE_DATA_VIEW',   'mod-data-view');

page_map_class(PAGE_DATA_VIEW, 'page_data');

$DEFINEDPAGES = array(PAGE_DATA_VIEW);
/*
*/

/**
 * Class that models the behavior of a data
 *
 * @author Jon Papaioannou
 * @package pages
 */

class page_data extends page_generic_activity {

    function init_quick($data) {
        if(empty($data->pageid)) {
            error('Cannot quickly initialize page: empty course id');
        }
        $this->activityname = 'data';
        parent::init_quick($data);
    }

    function print_header($title, $morenavlinks = NULL, $meta) {
        parent::print_header($title, $morenavlinks, '', $meta);
    }

    function get_type() {
        return PAGE_DATA_VIEW;
    }
}

?>
