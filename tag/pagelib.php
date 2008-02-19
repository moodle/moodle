<?php

require_once($CFG->libdir.'/pagelib.php');
require_once('lib.php');

define('PAGE_TAG_INDEX', 'tag-index');
define('TAG_FORMAT', 'tag');

class page_tag extends page_base {

    var $tag_object = NULL;
    
    function get_type() {
        return PAGE_TAG_INDEX;
    }

    function user_allowed_editing() {
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        return has_capability('moodle/tag:editblocks', $systemcontext);        
    }

    function user_is_editing() {
        global $USER;

        return (!empty($USER->editing));
    }

    
    function url_get_path() {
        global $CFG;
        return $CFG->wwwroot.'/tag/index.php';

    }

    function url_get_parameters() {
        $param = array();
        $param['id'] = $this->id;
        return $param;
    }
       
    function blocks_default_position() {
        return BLOCK_POS_LEFT;
    }

    function blocks_get_positions() {
        return array(BLOCK_POS_LEFT, BLOCK_POS_RIGHT);
    }

    function blocks_move_position(&$instance, $move) {
        if($instance->position == BLOCK_POS_LEFT && $move == BLOCK_MOVE_RIGHT) {
            return BLOCK_POS_RIGHT;
        } else if ($instance->position == BLOCK_POS_RIGHT && $move == BLOCK_MOVE_LEFT) {
            return BLOCK_POS_LEFT;
        }
        return $instance->position;
    }

    function get_format_name() {
        return TAG_FORMAT;
    }
    
    //-----------  printing funtions -----------
    
    function print_header() {

        global $USER, $CFG;

        $tagname = tag_display_name($this->tag_object);

        $navlinks = array();
        $navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
        $navlinks[] = array('name' => $tagname, 'link' => '', 'type' => '');

        $navigation = build_navigation($navlinks);
        $title = get_string('tag', 'tag') .' - '. $tagname;
        
        $button = '';
        if( $this->user_allowed_editing() ) {
            $button = update_tag_button($this->id);
        }
        print_header_simple($title, '', $navigation, '', '', '', $button);
    }    
    
    function print_footer() {
        print_footer();
    }
}

page_map_class(PAGE_TAG_INDEX, 'page_tag');

?>
