<?php

/**
 * * Pearson MyLab & Mastering block library code.
 *
 * @package    blocks_mylabmastering
 * @copyright  
 * @license    
 */


defined('MOODLE_INTERNAL') || die;

function mylabmastering_is_global_configured() {
	global $CFG;
	$configured = true;
	$mylabmasteringUrl = $CFG->mylabmastering_url;
	$mylabmasteringKey = $CFG->mylabmastering_key;
	$mylabmasteringSecret = $CFG->mylabmastering_secret;

	if ((!isset($mylabmasteringUrl) || trim($mylabmasteringUrl)==='')
			|| (!isset($mylabmasteringKey) || trim($mylabmasteringKey)==='')
			|| (!isset($mylabmasteringSecret) || trim($mylabmasteringSecret)==='')) {
		$configured = false;
	}

	return $configured;
}

function mylabmastering_create_highlander_link($id, $url, $title) {
	global $DB, $CFG, $COURSE;
	require_once($CFG->dirroot.'/course/lib.php');
	$courseid = $COURSE->id;
	
	$lti = new stdClass;
	$lti->course = $courseid;
	$lti->name = $title;
	$lti->timecreated = time();
	$lti->timemodified = $lti->timecreated;
	$lti->typeid = 0;
	$lti->toolurl = $url;
	if (strpos($lti->toolurl, 'https') === 0) {
		$lti->instructorchoicesendname = 1;
		$lti->instructorchoicesendemailaddr = 1;
	}
	else {
		$lti->instructorchoicesendname = 0;
		$lti->instructorchoicesendemailaddr = 0;
	}
	$lti->launchcontainer = 2;
	$lti->resourcekey = $CFG->mylabmastering_key;
	$lti->password = $CFG->mylabmastering_secret;
	$lti->debuglaunch = 0;
	$lti->showtitlelaunch = 0;
	$lti->showdescriptionlaunch = 0;	
	
	$use_icons = $CFG->mylabmastering_use_icons;
	if ($use_icons) {
		$lti->icon = $CFG->wwwroot.'/blocks/mylabmastering/pix/icon.jpg';
	}	

	$lti->id = $DB->insert_record('lti', $lti);

	$cm = new stdClass;
	$cm->course = $courseid;
	$cm->module = mylabmastering_get_lti_module();
	$cm->instance = $lti->id;
	$cm->section = 0;
	$cm->idnumber = $id;
	$cm->added = time();
	$cm->score = 0;
	$cm->indent = 0;
	$cm->visible = 1;
	$cm->visibleold = 1;
	$cm->groupmode = 0;
	$cm->groupingid = 0;
	$cm->groupmembersonly = 0;
	$cm->completion = 0;
	$cm->completionview = 0;
	$cm->completionexpected = 0;
	$cm->showavailability = 0;
	$cm->showdescription = 0;
	$cm->coursemodule = add_course_module($cm);
	$sectionid = course_add_cm_to_section($cm->course, $cm->coursemodule, $cm->section, null);
	$DB->set_field("course_modules", "section", $sectionid, array("id" => $cm->coursemodule));

	return $lti->id;
}

function mylabmastering_get_lti_module() {
	global $DB;
	$module = $DB->get_record('modules', array('name' => 'lti'), '*', MUST_EXIST);
	return $module->id;
}

function mylabmastering_course_has_config($courseid) {
	global $DB;
	return $DB->get_record('mylabmastering', array('course' => $courseid), '*', IGNORE_MISSING);
}

function mylabmastering_create_course_config($config) {
	global $DB;
	$DB->insert_record('mylabmastering',$config);
}

function mylabmastering_update_course_config($config) {
	global $DB;
	$DB->update_record('mylabmastering',$config);
}


/**
 * Returns the mapping data from Highlander.
 *
 * @param int $courseid
 * @return product standard class with 2 members: code & platform
 */

// JAM 03/2015 - Updated to use Moodle CURL class. Does not use cache as that could cause an invalid UI experience.
function mylabmastering_get_mapping($courseid) {
	global $CFG, $DB;

	$retval = NULL;

	$url = $CFG->mylabmastering_url;
	$url .= "/highlander/api/v1/mappings/" . $courseid;
	$url .= '?consumerkey=' . $CFG->mylabmastering_key;

	$c = new curl();

	$c_opts = array(
		'url' => $url,
		'CURLOPT_HEADER' => 0,
		'CURLOPT_RETURNTRANSFER' => 1,
		'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
		'siteid' => $CFG->siteidentifier
	);

	$response = $c->get($url, $c_opts);

	if ($response && strpos($response, 'ext_course_material') > 0) {
		$json = json_decode($response);
		$product = new stdClass;
		$product->code = $json->ext_course_material;
		if (isset($json->platform)) {
			$product->platform = $json->platform;
		}
		$retval = $product;
	}

	return $retval;
}

function mylabmastering_delete_highlander_link($id) {
	global $DB,$CFG;
	require_once($CFG->dirroot.'/mod/lti/locallib.php');
	require_once($CFG->dirroot.'/course/lib.php');

	$coursemodules = $DB->get_records('course_modules', array('course' => $courseconfig->course, 'module' => mylabmastering_get_lti_module()));
	if($coursemodules) {
		foreach ($coursemodules as $cm) {
			if ($cm->idnumber === $id) {
				delete_mod_from_section($cm->id, $cm->section);
				course_delete_module($cm->id);
			}
		}
	}
}

function mylabmastering_get_content_links($code) {
	global $CFG;

	$url = $CFG->mylabmastering_url;
	$url .= "/highlander/api/v2/bundles/".$code;
	$url .= '?restrict=content&consumer=moodleblti';

	$c = new curl(array('cache' => false));

	$c_opts = array(
		'url' => $url,
		'CURLOPT_HEADER' => 0,
		'CURLOPT_RETURNTRANSFER' => 1,
		'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
		'siteid' => $CFG->siteidentifier
	);

	$response = $c->get($url, $c_opts);

	$retval = NULL;

	if ($response && strpos($response, 'basicLtiLinkBundle') > 0) {
		$json = json_decode($response);
		$product = new stdClass;
		$product->bundle = $json->basicLtiLinkBundle;
		$product->platform = $json->platform;
		$product->description = $json->basicLtiLinkBundle->description;
		$retval = $product;
	}

	return $retval;
}

// JAM 03/2015 -- This method does not appear to be called from anywhere in the block
function mylabmastering_get_tools_links($code) {
	global $CFG;

	$url = $CFG->mylabmastering_url;
	$url .= "/highlander/api/v2/bundles/".$code;
	$url .= '?restrict=tools&consumer=moodleblti';

	$c = new curl(array('cache' => false));

	$c_opts = array(
		'url' => $url,
		'CURLOPT_HEADER' => 0,
		'CURLOPT_RETURNTRANSFER' => 1,
		'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
		'siteid' => $CFG->siteidentifier
	);

	$response = $c->get($url, $c_opts);
	die;

	$retval = NULL;

	if ($response && strpos($response, 'ext_course_material') > 0) {
		$json = json_decode($response);
		$product = new stdClass;
		$product->bundle = $json->basicLtiLinkBundle;
		$product->platform = $json->platform;
		$product->description = $json->basicLtiLinkBundle->description;
		$retval = $product;
	}

	return $retval;
}

function mylabmastering_is_instructor_link($link) {
	$instructorLink = true;
	$exts = $link->extensions;
	$params = $exts[0]->parameters;
	foreach ($params as $param) {
		$n = $param->name;
		if (strcasecmp($n, 'isinstructoronly') == 0) {
			$v = $param->value;
			if (strcasecmp($v, 'false') == 0) {
				$instructorLink = false;
			}
		}
	}
	return $instructorLink;
}

function mylabmastering_create_lti_type($link, $courseid, $userid) {
	global $DB, $CFG;
	$ltitype = new stdClass;
	$ltitype->name = $link->title;
	$ltitype->baseurl = $link->launchUrl;
	$ltitype->tooldomain = mylabmastering_construct_id_for_ltitypes($link->id,$courseid);
	$ltitype->state = 1;
	$ltitype->course = $courseid;
	$ltitype->coursevisible = 1;
	$ltitype->createdby = $userid;
	$ltitype->timecreated = time();
	$ltitype->timemodified = $ltitype->timecreated;

	$ltitype->id = $DB->insert_record('lti_types', $ltitype);

	$ltitypeconfig = new stdClass;
	$ltitypeconfig->resourcekey = $CFG->mylabmastering_key;
	$ltitypeconfig->password = $CFG->mylabmastering_secret;

	$strCustomParams = '';

	$cps = $link->customParameters;

	foreach ($cps as $cp) {
		$n =  strtolower($cp->name);

		$v = $cp->value;

		if ($strCustomParams == NULL) {
			$strCustomParams = '';
		}
		$strCustomParams .= $n.'='.$v."\n";
	}

	if ($strCustomParams != null) {
		$ltitypeconfig->customparameters = $strCustomParams;
	}

	$ltitypeconfig->launchcontainer = 4;
	if (strpos($ltitype->baseurl, 'https') === 0) {
		$ltitypeconfig->sendname = 1;
		$ltitypeconfig->sendemailaddr = 1;
	}
	else {
		$ltitypeconfig->sendname = 0;
		$ltitypeconfig->sendemailaddr = 0;
	}
	
	$ltitypeconfig->acceptgrades = 2;
	//$ltitypeconfig->organizationid = ;
	//$ltitypeconfig->organizationurl = ;
	$ltitypeconfig->coursevisible = 1;
	$ltitypeconfig->forcessl = 0;
	$ltitypeconfig->servicesalt = 'mm.salt';
	
	$use_icons = $CFG->mylabmastering_use_icons;
	if ($use_icons) {
		$ltitypeconfig->icon = $CFG->wwwroot.'/blocks/mylabmastering/pix/icon.jpg';
	}
	
	if ($ltitype->id) {
		foreach ($ltitypeconfig as $key => $value) {
			$record = new StdClass();
			$record->typeid = $ltitype->id;
			$record->name = $key;
			$record->value = $value;

			$DB->insert_record('lti_types_config', $record);
		}
	}
}


function mylabmastering_construct_id_for_ltitypes($highlanderid,$courseid) {
	return 'mm:'.$highlanderid.':'.$courseid;
}

function mylabmastering_construct_id_for_lti($highlanderid,$courseid,$localid,$placement) {
	return 'mm:'.$highlanderid.':'.$courseid.':'.$localid.':'.$placement;
}

function mylabmastering_get_lti_id($courseid,$idnumber) {
	global $DB;
	$retval = NULL;
	$coursemodules = $DB->get_records('course_modules', array('course' => $courseid, 'module' => mylabmastering_get_lti_module()));
	if ($coursemodules) {
		foreach($coursemodules as $coursemodule) {
			if (strpos($coursemodule->idnumber, $idnumber) === 0) {
				$retval = $coursemodule->instance;
			}
		}
	}
	return $retval;
	
}

function mylabmastering_handle_code_change($courseid) {
	global $DB,$CFG;
	require_once($CFG->dirroot.'/mod/lti/locallib.php');
	require_once($CFG->dirroot.'/course/lib.php');

	$types = $DB->get_records('lti_types', array('course' => $courseid));
	if($types) {
		foreach ($types as $type) {
			if (!strncmp($type->tooldomain, 'mm:', strlen('mm:'))) {
				lti_delete_type($type->id);
			}
		}
	}

	$coursemodules = $DB->get_records('course_modules', array('course' => $courseid, 'module' => mylabmastering_get_lti_module()));
	if($coursemodules) {
		foreach ($coursemodules as $cm) {
			if (!strncmp($cm->idnumber, 'mm:', strlen('mm:'))) {
				delete_mod_from_section($cm->id, $cm->section);
				course_delete_module($cm->id);
			}
		}
	}
}

function mylabmastering_is_student($courseid) {
	global $USER,$CFG;
	$student = false;
	require_once($CFG->dirroot.'/mod/lti/locallib.php');

	//TPLMS-2242
	$role = lti_get_ims_role($USER, '' ,$courseid, false);

	if (strcasecmp($role, 'learner') == 0) {
		$student = true;
	}

	return $student;
}

function mylabmastering_reset_local_mapping($local_config) {
	// Reset the basic data
	$local_config->code = 'unmapped';
	$local_config->platform = '';
	$local_config->description = format_text('<p>Pearson MyLab & Mastering course pairing: None</p>', FORMAT_HTML) .
		format_text('<p>Use the Pearson MyLab & Mastering Tools link to get started.</p>', FORMAT_HTML);

	mylabmastering_update_course_config($local_config);

}



