<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 exabis internet solutions <info@exabis.at>
*  All rights reserved
*
*  You can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  This module is based on the Collaborative Moodle Modules from
*  NCSA Education Division (http://www.ncsa.uiuc.edu)
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class block_exabis_student_review extends block_list {
    function init() {
        $this->title = get_string('blocktitle', 'block_exabis_student_review');
        $this->version = 2008031000;
    }

    function instance_allow_multiple() {
        return false;
    }
    
	function has_config() {
	    return true;
	}
    
    function instance_allow_config() {
        return false;
    }

	function config_save($data) {
		print_r($data);
		die();
	    // Default behavior: save all variables as $CFG properties
	    foreach ($data as $name => $value) {
	        set_config($name, $value);
	    }
	    return true;
	}
    
    function get_content() {
    	global $CFG, $COURSE, $USER;
    	
    	if ($this->content !== NULL) {
            return $this->content;
        }
        
        $this->content = '';
    	
    	$context = get_context_instance(CONTEXT_SYSTEM);
        if (!has_capability('block/exabis_student_review:use', $context)) {
        	return $this->content;
        }
        
        if (empty($this->instance)) {
            return $this->content;
        }
        
        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        
        if(has_capability('block/exabis_student_review:head', $context)) {
			$this->content->icons[]='<img src="' . $CFG->wwwroot . '/blocks/exabis_student_review/pix/klassenzuteilung.png" height="12" width="11" alt="" />';
			$this->content->items[]='<a title="' . get_string('configuration', 'block_exabis_student_review') . '" href="' . $CFG->wwwroot . '/blocks/exabis_student_review/configuration.php?courseid=' . $COURSE->id . '">' . get_string('configuration', 'block_exabis_student_review') . '</a>';
			
			$this->content->icons[]='<img src="' . $CFG->wwwroot . '/blocks/exabis_student_review/pix/zeugnisse.png" height="12" width="11" alt="" />';
			$this->content->items[]='<a title="' . get_string('report', 'block_exabis_student_review') . '" href="' . $CFG->wwwroot . '/blocks/exabis_student_review/report.php?courseid=' . $COURSE->id . '">' . get_string('report', 'block_exabis_student_review') . '</a>';
        }
        
		
		if(has_capability('block/exabis_student_review:editperiods', $context)) {
			$this->content->icons[]='<img src="' . $CFG->wwwroot . '/blocks/exabis_student_review/pix/eingabezeitraum.png" height="12" width="11" alt="" />';
			$this->content->items[]='<a title="' . get_string('periods', 'block_exabis_student_review') . '" href="' . $CFG->wwwroot . '/blocks/exabis_student_review/periods.php?courseid=' . $COURSE->id . '">' . get_string('periods', 'block_exabis_student_review') . '</a>';
		}
		
    	if(count_records('block_exabstudreviteactoclas', 'teacherid', $USER->id) > 0) {
			$this->content->icons[]='<img src="' . $CFG->wwwroot . '/blocks/exabis_student_review/pix/beurteilung.png" height="12" width="11" alt="" />';
			$this->content->items[]='<a title="' . get_string('review', 'block_exabis_student_review') . '" href="' . $CFG->wwwroot . '/blocks/exabis_student_review/review.php?courseid=' . $COURSE->id . '">' . get_string('review', 'block_exabis_student_review') . '</a>';
    	}
        
		return $this->content;
    }
}
