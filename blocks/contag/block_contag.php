<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


require_once(dirname(__FILE__) . "/lib.php"); // with just lib.php, this was failing depending on where the block was being seen!

class block_contag extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_contag');        
    }//function init
    
    function get_content() {
        
        global $CFG, $COURSE;
        $courseid = $COURSE->id;
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        if (has_capability('block/contag:view', $context)) { // can they view it?
            
            // Set up content components
            $this->content = new stdClass;
            $this->content->footer = ''; // suppresses an error?
            
            // START BLOCK MAIN LINKS
            $res = '<ul>';
            $link = $CFG->wwwroot.'/blocks/contag/';
            
            // display "Navigate" link
            $res .= '<li>'.'<a href="'.$link.'view.php?id='.$courseid.'">'.get_string('navigate_by_concept_tags', 'block_contag').'</a>'.'</li>'; // TODO: Is it safe to pass in the courseid like this?
            
            
            // for teachers only - display "Edit" link
            if (has_capability('block/contag:edit', $context)){
                $res .= '<li>'.'<a href="'.$link.'edit.php?id='.$courseid.'">'.get_string('edit_concept_tags', 'block_contag').'</a>'.'</li>';
            }
            
            $this->content->text = $res.'</ul>';
            // END BLOCK MAIN LINKS

            // add random tags
            $randtags = contag_get_random_tags($courseid,5);
            if (!empty($randtags)){
                $this->content->text.="Some tags: ".implode(", ", array_map("contag_format_random_tag_link", $randtags));
            }
            
            return $this->content;
            
        } // end 'has_capability->view'
        
    }//function get_content
    
    function cron(){
        contag_purge_stale_resolutions();
    }//function cron

}//class block_contag
?>
