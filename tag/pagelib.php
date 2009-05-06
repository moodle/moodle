<?php

require_once($CFG->libdir.'/pagelib.php');
require_once('lib.php');

define('PAGE_TAG_INDEX', 'tag-index');
define('TAG_FORMAT', 'tag');

class page_tag extends page_base {

    var $tag_object = NULL;

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
}

page_map_class(PAGE_TAG_INDEX, 'page_tag');

?>
