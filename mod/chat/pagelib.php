<?php // $Id$

require_once($CFG->libdir.'/pagelib.php');

define('PAGE_CHAT_VIEW',   'mod-chat-view');

page_map_class(PAGE_CHAT_VIEW, 'page_chat');

$DEFINEDPAGES = array(PAGE_CHAT_VIEW);

/**
 * Class that models the behavior of a chat
 *
 * @author Jon Papaioannou
 * @package pages
 */

class page_chat extends page_generic_activity {

    function init_quick($data) {
        if(empty($data->pageid)) {
            error('Cannot quickly initialize page: empty course id');
        }
        $this->activityname = 'chat';
        parent::init_quick($data);
    }

    function get_type() {
        return PAGE_CHAT_VIEW;
    }
}

?>
