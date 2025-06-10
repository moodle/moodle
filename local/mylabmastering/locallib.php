<?php

/**
 * Pearson MyLab & Mastering library code.
 *
 * @package    local_mylabmastering
 * @copyright  
 * @license    
 */

defined('MOODLE_INTERNAL') || die;

function mylabmastering_getkeymasterlink() {
	global $CFG;
	$regUrl = NULL;
	
	if (isset($CFG->mylabmastering_regurl)) {
		$regUrl = $CFG->mylabmastering_regurl;
	}
	
	if (!isset($regUrl) || trim($regUrl)==='') { // default to production
		$regUrl = 'https://tpi.bb.pearsoncmg.com/keymaster/ui/u/index?consumer=moodleblti';
	}	   
 	
	return $regUrl;
}

function mylabmastering_showkeymasterlink() {
	$regUrl = mylabmastering_getkeymasterlink();
	
	if (isset($regUrl) && trim($regUrl)!=='') {
		return true;
	}
	else {
		return false;
	}
}
