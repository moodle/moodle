<?php

/**
 * Pearson MyLab & Mastering block code.
 *
 * @package    block_mylabmastering
 * @copyright  2012-2013 Pearson Education
 * @license    
 */

defined('MOODLE_INTERNAL') || die;

class block_mylabmastering extends block_base {
    public function init() {
        $this->title = get_string('mylabmastering', 'block_mylabmastering');
    }
    
    public function get_content() {
	    global $CFG, $COURSE, $USER, $PAGE, $DB;
	    require_once($CFG->dirroot.'/blocks/mylabmastering/locallib.php');
	    require_once($CFG->dirroot.'/lib/modinfolib.php');
	   	$this->content = new stdClass;
	   	$this->content->text = '';
	   	$this->content->footer = '';
	   	$strHTML = '';
	   	
	   	// check capabilities and throw error if needed
	   	require_capability('block/mylabmastering:view', context_course::instance($COURSE->id));
	   	
	   	if (mylabmastering_is_student($COURSE->id)) {
	   		return $this->content;
	   	}
    		 
	    if (mylabmastering_is_global_configured()) {
			// Get the JavaScript
			$PAGE->requires->js('/blocks/mylabmastering/mylabmastering_async.js');

	    	// this means that the base url, key and secret are configured correctly
	    	// check for local course configuration
	    	$mm_local_config = mylabmastering_course_has_config($COURSE->id);

	    	if (!$mm_local_config) {
				// The local course configuration does not exist.
	    		$mm_tools_id = mylabmastering_create_highlander_link('mm_tools', $CFG->mylabmastering_url.'/highlander/api/o/lti/tools', 'MyLab & Mastering Tools');
	    		
	    		$mm_local_config = new stdClass;
				$mm_local_config->course = $COURSE->id;
				$mm_local_config->code = 'unmapped';
				$mm_local_config->platform = '';
				$mm_local_config->description = '<p>Pearson MyLab & Mastering course pairing: None</p>';
				$mm_local_config->description .= '<p>Use the <a href="'.$CFG->wwwroot."/mod/lti/view.php?l=".$mm_tools_id.'" >Pearson MyLab & Mastering Tools link</a> to get started.</p>';
	    		mylabmastering_create_course_config($mm_local_config);
	    		
	    		rebuild_course_cache($COURSE->id);
	    	} else {
				// Call the JavaScript function to validate and update the page as needed.
				$PAGE->requires->js_init_call('M.block_mylabmastering.init', array($COURSE->id, $USER->id, $mm_local_config->code), true);
			}

			// Build the output to the screen
	    	$strHTML .= '<div id="block_mylabmastering_tree" >';
	    	$strHTML .= $mm_local_config->description;	    	
	    	$strHTML .= '<br/>';
	    	$strHTML .= '</div>';
	    }
	    else {
	    	// this means that the base url, key and secret are not configured correctly
	    	$strHTML .= get_string('mylabmastering_notconfigured', 'block_mylabmastering');
	    }	    	   
	    
	    //$this->content->text = format_text($strHTML, FORMAT_HTML);
		$this->content->text = $strHTML;

	    return $this->content;
	}
  
	public function specialization() {
	  if (!empty($this->config->title)) {
		$this->title = $this->config->title;
	  } else {
		$this->title = get_string('mylabmastering', 'block_mylabmastering');
	  }
	}
  
	public function applicable_formats() {
	  return array('course-view' => true);
	}
}   // Here's the closing bracket for the class definition
