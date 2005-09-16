<?PHP  // $Id$

//////////////////////////////////
/// CONFIGURATION settings 

if (!isset($CFG->hotpot_showtimes)) {
    set_config("hotpot_showtimes", 0);
}  
if (!isset($CFG->hotpot_excelencodings)) {
    set_config("hotpot_excelencodings", "");
}  


//////////////////////////////////
/// CONSTANTS and GLOBAL VARIABLES

$ds = DIRECTORY_SEPARATOR;
$CFG->hotpotroot = "$CFG->dirroot{$ds}mod{$ds}hotpot";
$CFG->hotpottemplate = "$CFG->hotpotroot{$ds}template";

define("HOTPOT_JS", "$CFG->wwwroot/mod/hotpot/hotpot-full.js");

define("HOTPOT_NO",  "0");
define("HOTPOT_YES", "1");

define ("HOTPOT_TEXTSOURCE_QUIZ", "0");
define ("HOTPOT_TEXTSOURCE_FILENAME", "1");
define ("HOTPOT_TEXTSOURCE_FILEPATH", "2");
define ("HOTPOT_TEXTSOURCE_SPECIFIC", "3");

define("HOTPOT_LOCATION_COURSEFILES", "0");
define("HOTPOT_LOCATION_SITEFILES",   "1");

$HOTPOT_LOCATION = array (
	HOTPOT_LOCATION_COURSEFILES => get_string("coursefiles"),
	HOTPOT_LOCATION_SITEFILES   => get_string("sitefiles"),
);

define("HOTPOT_OUTPUTFORMAT_BEST",     "1");
define("HOTPOT_OUTPUTFORMAT_V3",      "10");
define("HOTPOT_OUTPUTFORMAT_V4",      "11");
define("HOTPOT_OUTPUTFORMAT_V5",      "12");
define("HOTPOT_OUTPUTFORMAT_V5_PLUS", "13");
define("HOTPOT_OUTPUTFORMAT_V6",      "14");
define("HOTPOT_OUTPUTFORMAT_V6_PLUS", "15");
define("HOTPOT_OUTPUTFORMAT_FLASH",   "20");
define("HOTPOT_OUTPUTFORMAT_MOBILE",  "30");

$HOTPOT_OUTPUTFORMAT = array (
	HOTPOT_OUTPUTFORMAT_BEST    => get_string("outputformat_best", "hotpot"),
	HOTPOT_OUTPUTFORMAT_V6_PLUS => get_string("outputformat_v6_plus", "hotpot"),
	HOTPOT_OUTPUTFORMAT_V6      => get_string("outputformat_v6", "hotpot"),
	HOTPOT_OUTPUTFORMAT_V5_PLUS => get_string("outputformat_v5_plus", "hotpot"),
	 HOTPOT_OUTPUTFORMAT_V5      => get_string("outputformat_v5", "hotpot"),
	 HOTPOT_OUTPUTFORMAT_V4      => get_string("outputformat_v4", "hotpot"),
	 HOTPOT_OUTPUTFORMAT_V3      => get_string("outputformat_v3", "hotpot"),
	// HOTPOT_OUTPUTFORMAT_FLASH   => get_string("outputformat_flash", "hotpot"),
	// HOTPOT_OUTPUTFORMAT_MOBILE  => get_string("outputformat_mobile", "hotpot"),
);

$HOTPOT_OUTPUTFORMAT_DIR = array (
	HOTPOT_OUTPUTFORMAT_V6_PLUS => 'v6',
	HOTPOT_OUTPUTFORMAT_V6      => 'v6',
	// HOTPOT_OUTPUTFORMAT_V5      => 'v5',
	// HOTPOT_OUTPUTFORMAT_V4      => 'v4',
	// HOTPOT_OUTPUTFORMAT_V3      => 'v3',
	// HOTPOT_OUTPUTFORMAT_FLASH   => 'flash',
	// HOTPOT_OUTPUTFORMAT_MOBILE  => 'mobile',
);

define("HOTPOT_NAVIGATION_BAR",     "1");
define("HOTPOT_NAVIGATION_FRAME",   "2");
define("HOTPOT_NAVIGATION_IFRAME",  "3");
define("HOTPOT_NAVIGATION_BUTTONS", "4");
define("HOTPOT_NAVIGATION_GIVEUP", "5");
define("HOTPOT_NAVIGATION_NONE",    "6");

$HOTPOT_NAVIGATION = array (
	HOTPOT_NAVIGATION_BAR     => get_string("navigation_bar", "hotpot"),
	HOTPOT_NAVIGATION_FRAME   => get_string("navigation_frame", "hotpot"),
	HOTPOT_NAVIGATION_IFRAME  => get_string("navigation_iframe", "hotpot"),
	HOTPOT_NAVIGATION_BUTTONS => get_string("navigation_buttons", "hotpot"),
	HOTPOT_NAVIGATION_GIVEUP  => get_string("navigation_give_up", "hotpot"),
	HOTPOT_NAVIGATION_NONE    => get_string("navigation_none", "hotpot"),
);

define("HOTPOT_JCB",    "1");
define("HOTPOT_JCLOZE", "2");
define("HOTPOT_JCROSS", "3");
define("HOTPOT_JMATCH", "4");
define("HOTPOT_JMIX",   "5");
define("HOTPOT_JQUIZ",  "6");
define("HOTPOT_TEXTOYS_RHUBARB",   "7");
define("HOTPOT_TEXTOYS_SEQUITUR",  "8");

define("HOTPOT_JQUIZ_MULTICHOICE", "1");
define("HOTPOT_JQUIZ_SHORTANSWER", "2");
define("HOTPOT_JQUIZ_HYBRID",      "3");
define("HOTPOT_JQUIZ_MULTISELECT", "4");

define("HOTPOT_GRADEMETHOD_HIGHEST", "1");
define("HOTPOT_GRADEMETHOD_AVERAGE", "2");
define("HOTPOT_GRADEMETHOD_FIRST",   "3");
define("HOTPOT_GRADEMETHOD_LAST",    "4");

$HOTPOT_GRADEMETHOD = array (
	HOTPOT_GRADEMETHOD_HIGHEST => get_string("gradehighest", "quiz"),
	HOTPOT_GRADEMETHOD_AVERAGE => get_string("gradeaverage", "quiz"),
	HOTPOT_GRADEMETHOD_FIRST   => get_string("attemptfirst", "quiz"),
	HOTPOT_GRADEMETHOD_LAST    => get_string("attemptlast",  "quiz"),
);

define("HOTPOT_STATUS_INPROGRESS", "1");
define("HOTPOT_STATUS_TIMEDOUT",   "2");
define("HOTPOT_STATUS_ABANDONED",  "3");
define("HOTPOT_STATUS_COMPLETED",  "4");

$HOTPOT_STATUS = array (
	HOTPOT_STATUS_INPROGRESS => get_string("inprogress", "hotpot"),
	HOTPOT_STATUS_TIMEDOUT   => get_string("timedout",   "hotpot"),
	HOTPOT_STATUS_ABANDONED  => get_string("abandoned",  "hotpot"),
	HOTPOT_STATUS_COMPLETED  => get_string("completed",  "hotpot"),
);

define("HOTPOT_FEEDBACK_NONE", "0");
define("HOTPOT_FEEDBACK_WEBPAGE", "1");
define("HOTPOT_FEEDBACK_FORMMAIL", "2");
define("HOTPOT_FEEDBACK_MOODLEFORUM", "3");
define("HOTPOT_FEEDBACK_MOODLEMESSAGING", "4");

$HOTPOT_FEEDBACK = array (
	HOTPOT_FEEDBACK_NONE => get_string("feedbacknone", "hotpot"),
	HOTPOT_FEEDBACK_WEBPAGE => get_string("feedbackwebpage",  "hotpot"),
	HOTPOT_FEEDBACK_FORMMAIL => get_string("feedbackformmail", "hotpot"),
	HOTPOT_FEEDBACK_MOODLEFORUM => get_string("feedbackmoodleforum", "hotpot"),
);
if (!empty($CFG->messaging)) { // Moodle 1.5+
	$HOTPOT_FEEDBACK[HOTPOT_FEEDBACK_MOODLEMESSAGING] = get_string("feedbackmoodlemessaging", "hotpot");
}

define("HOTPOT_DISPLAYNEXT_QUIZ",   "0");
define("HOTPOT_DISPLAYNEXT_COURSE", "1");
define("HOTPOT_DISPLAYNEXT_INDEX",  "2");

//////////////////////////////////
/// CORE FUNCTIONS


// possible return values:
//	false: 
//		display moderr.html (if exists) OR "Could not update" and return to couse view
//	string: 
//		display as error message and return to course view
//  true (or non-zero number):
//		continue to $hp->redirect (if set) OR hotpot/view.php (to displsay quiz)

function hotpot_add_instance(&$hp) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.
	
	if (hotpot_set_form_values($hp)) {
		$result = insert_record("hotpot", $hp);
	} else {
		$result=  false;
	}
	return $result;
}


function hotpot_update_instance(&$hp) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

	if (hotpot_set_form_values($hp)) {
		$hp->id = $hp->instance;
		$result = update_record("hotpot", $hp);
	} else {
		$result=  false;
	}
	return $result;
}

function hotpot_set_form_values(&$hp) {
	$ok = true;
	$hp->errors = array(); // these will be reported by moderr.html

	if (empty($hp->reference)) {
		$ok = false;
		$hp->errors['reference']= get_string('error_nofilename', 'hotpot');
	}

	if ($hp->studentfeedbackurl=='http://') {
		$hp->studentfeedbackurl = '';
	}

	if (empty($hp->studentfeedbackurl)) {
		switch ($hp->studentfeedback) {
			case HOTPOT_FEEDBACK_WEBPAGE:
				$ok = false;
				$hp->errors['studentfeedbackurl']= get_string('error_nofeedbackurlwebpage', 'hotpot');
			break;
			case HOTPOT_FEEDBACK_FORMMAIL:
				$ok = false;
				$hp->errors['studentfeedbackurl']= get_string('error_nofeedbackurlformmail', 'hotpot');
			break;
		}
	}

	$time = time();
	$hp->timecreated = $time;
	$hp->timemodified = $time;

	if (empty($hp->enabletimeopen)) {
		$hp->timeopen = 0;
	} else {
		$hp->timeopen = make_timestamp(
			$hp->openyear, $hp->openmonth, $hp->openday, 
			$hp->openhour, $hp->openminute, 0
		);
	}

	if (empty($hp->enabletimeclose)) {
		$hp->timeclose = 0;
	} else {
		$hp->timeclose = make_timestamp(
			$hp->closeyear, $hp->closemonth, $hp->closeday, 
			$hp->closehour, $hp->closeminute, 0
		);
	}

	if ($hp->quizchain==HOTPOT_YES) {
		switch ($hp->mode) {
			case 'add':
				$ok = hotpot_add_chain($hp);
			break;
			case 'update':
				$ok = hotpot_update_chain($hp);
			break;
		}
	} else {
		$xml_quiz = NULL;
		
		$textfields = array('name', 'summary');
		foreach ($textfields as $textfield) {

			$textsource = $textfield.'source';
			if ($hp->$textsource==HOTPOT_TEXTSOURCE_QUIZ) {
				if (empty($xml_quiz)) {
					$xml_quiz = new hotpot_xml_quiz($hp, false, false, false, false, false);
					hotpot_get_titles_and_next_ex($hp, $xml_quiz->filepath);
				}
				if ($textfield=='name') {
					$hp->$textfield = $hp->exercisetitle;
				} else if ($textfield=='summary') {
					$hp->$textfield = $hp->exercisesubtitle;
				}
			}
			switch ($hp->$textsource) {
				case HOTPOT_TEXTSOURCE_FILENAME:
					$hp->$textfield = basename($hp->reference);
					break;
				case HOTPOT_TEXTSOURCE_FILEPATH:
					$hp->$textfield = '';
					// continue to next lines
				default:
					if (empty($hp->$textfield)) {
						$hp->$textfield = str_replace('/', ' ', $hp->reference);
					}
			} // end switch
		} // end foreach
	}

	switch ($hp->displaynext) {
		// N.B. redirection only works for Moodle 1.5+
		case HOTPOT_DISPLAYNEXT_COURSE:
			$hp->redirect = true;
			$hp->redirecturl = "view.php?id=$hp->course";
			break;
		case HOTPOT_DISPLAYNEXT_INDEX:
			$hp->redirect = true;
			$hp->redirecturl = "../mod/hotpot/index.php?id=$hp->course";
			break;
		// otherwise go on to display quiz
	}

	// if ($ok && $hp->setdefaults) {
	if ($ok) {
		set_user_preference('hotpot_timeopen', $hp->timeopen);
		set_user_preference('hotpot_timeclose', $hp->timeclose);
		set_user_preference('hotpot_navigation', $hp->navigation);
		set_user_preference('hotpot_outputformat', $hp->outputformat);
		set_user_preference('hotpot_studentfeedback', $hp->studentfeedback);
		set_user_preference('hotpot_studentfeedbackurl', $hp->studentfeedbackurl);
		set_user_preference('hotpot_forceplugins', $hp->forceplugins);
		set_user_preference('hotpot_shownextquiz', $hp->shownextquiz);
		set_user_preference('hotpot_review', $hp->review);
		set_user_preference('hotpot_grade', $hp->grade);
		set_user_preference('hotpot_grademethod', $hp->grademethod);
		set_user_preference('hotpot_attempts', $hp->attempts);
		get_user_preference('hotpot_subnet', $hp->subnet);
		set_user_preference('hotpot_displaynext', $hp->displaynext);
		if ($hp->mode=='add') {
			set_user_preference('hotpot_quizchain', $hp->quizchain);
			set_user_preference('hotpot_namesource', $hp->namesource);
			set_user_preference('hotpot_summarysource', $hp->summarysource);
		}
	}

	return $ok;
}
function hotpot_get_chain(&$cm) {

	// get details of course_modules in this section
	$course_module_ids = get_field('course_sections', 'sequence', 'id', $cm->section);
	if (empty($course_module_ids)) {
		$hotpot_modules = array();
	} else {
		$hotpot_modules = get_records_select('course_modules', "id IN ($course_module_ids) AND module=$cm->module");
	}

	// get ids of hotpot modules in this section
	$ids = array();
	foreach ($hotpot_modules as $hotpot_module) {
		$ids[] = $hotpot_module->instance;
	}

	// get details of hotpots in this section
	if (empty($ids)) {
		$hotpots = array();
	} else {
		$hotpots = get_records_list('hotpot', 'id', implode(',', $ids));
	}

	$found = false;
	$chain = array();

	// loop through course_modules in this section
	$ids = explode(',', $course_module_ids);
	foreach ($ids as $id) {
	
		// check this course_module is a hotpot activity
		if (isset($hotpot_modules[$id])) {

			// store details of this course module and hotpot activity
			$hotpot_id = $hotpot_modules[$id]->instance;
			$chain[$id] = &$hotpot_modules[$id];
			$chain[$id]->hotpot = &$hotpots[$hotpot_id];

			// set $found, if this is the course module we're looking for
			if ($id==$cm->coursemodule) {
				$found = true;
			}

			// is this the end of a chain
			if (empty($hotpots[$hotpot_id]->shownextquiz)) {
				if ($found) {
					break; // out of loop
				} else {
					// restart chain (target cm has not been found yet)
					$chain = array();
				}
			}
		}
	} // end foreach $ids

	return $found ? $chain : false;
}
function hotpot_is_visible(&$cm) {
	$visible = HOTPOT_YES;
	if (empty($cm->visible)) {
		if ($chain = hotpot_get_chain($cm)) {
			$visible = $chain[0]->visible;
		}
	}
	return $visible;
}
function hotpot_add_chain(&$hp) {
/// add a chain of hotpot actiivities
	
	global $CFG, $course;

	$ok = true;
	$hp->files = array();
	$hp->titles = array();

	$xml_quiz = new hotpot_xml_quiz($hp, false, false, false, false, false);

	if (isset($xml_quiz->error)) {
		$hp->errors['reference'] = $xml_quiz->error;
		$ok = false;

	} else if (is_dir($xml_quiz->filepath)) {
	
		// get list of hotpot files in this folder
		if ($dh = @opendir($xml_quiz->filepath)) {
			while ($file = @readdir($dh)) {
				if (preg_match('/\.(jbc|jcl|jcw|jmt|jmx|jqz|htm|html)$/', $file)) {
					$hp->files[] = "$xml_quiz->reference/$file";
				}
			}
			closedir($dh);

			// get titles
			foreach ($hp->files as $i=>$file) {
				$filepath = $xml_quiz->fileroot.DIRECTORY_SEPARATOR.$xml_quiz->filesubdir.$file;
				hotpot_get_titles_and_next_ex($hp, $filepath);
				$hp->titles[$i] = $hp->exercisetitle;
			}
			
		} else {
			$ok = false;
			$hp->errors['reference'] = get_string('error_couldnotopenfolder', 'hotpot', $hp->reference);
		}
	
	} else if (is_file($xml_quiz->filepath)) {

		$filerootlength = strlen($xml_quiz->fileroot) + 1;

		while ($xml_quiz->filepath) {
			hotpot_get_titles_and_next_ex($hp, $xml_quiz->filepath, true);

			$hp->files[] = substr($xml_quiz->filepath, $filerootlength);
			$hp->titles[] = $hp->exercisetitle;

			if ($hp->nextexercise) {
				$filepath = $xml_quiz->fileroot.DIRECTORY_SEPARATOR.$xml_quiz->filesubdir.$hp->nextexercise;
			} else {
				$filepath = '';
			}
			if ($filepath && file_exists($filepath) && is_file($filepath) && is_readable($filepath)) {
				$xml_quiz->filepath = $filepath;
			} else {
				$xml_quiz->filepath = false; // finish while loop
			}
		} // end while
	
	} else {
		$ok = false;
		$hp->errors['reference'] = get_string('error_notfileorfolder', 'hotpot', $hp->reference);
	}

	if (empty($hp->files) && empty($hp->errors['reference'])) {
		$ok = false;
		$hp->errors['reference'] = get_string('error_noquizzesfound', 'hotpot', $hp->reference);
	}

	if ($ok) {
		$hp->visible = HOTPOT_YES;
		$hp->shownextquiz = HOTPOT_YES;

		if (trim($hp->name)=='') {
			$hp->name = get_string("modulename", $hp->modulename);
		}
		$hp->basename = $hp->name;

		// add all except last activity in chain

		$i_max = count($hp->files)-1;
		for ($i=0; $i<$i_max; $i++) {

			$hp->name = addslashes($hp->titles[$i]);
			$hp->reference = addslashes($hp->files[$i]);

			if (!$hp->instance = insert_record("hotpot", $hp)) {
				error("Could not add a new instance of $hp->modulename", "view.php?id=$hp->course");
			}

			// store (hotpot table) id of start of chain
			if ($i==0) {
				$hp->startofchain = $hp->instance;
			}

			if (isset($course->groupmode)) {
				$hp->groupmode = $course->groupmode;
			}

			if (! $hp->coursemodule = add_course_module($hp)) {
				error("Could not add a new course module");
			}
			if (! $sectionid = add_mod_to_section($hp) ) {
				error("Could not add the new course module to that section");
			}
			
			if (! set_field("course_modules", "section", $sectionid, "id", $hp->coursemodule)) {
				error("Could not update the course module with the correct section");
			}   
			
			add_to_log($hp->course, "course", "add mod", 
				"../mod/$hp->modulename/view.php?id=$hp->coursemodule", 
				"$hp->modulename $hp->instance"
			); 
			add_to_log($hp->course, $hp->modulename, "add", 
				"view.php?id=$hp->coursemodule", 
				"$hp->instance", $hp->coursemodule
			);

			// hide tail of chain
			$hp->visible = HOTPOT_NO;
			
		} // end for ($hp->files)

		// settings for final activity in chain
		$hp->name = addslashes($hp->titles[$i]);
		$hp->reference = addslashes($hp->files[$i]);
		$hp->shownextquiz = HOTPOT_NO;

		if (isset($hp->startofchain)) {
			// redirection only works for Moodle 1.5+
			$hp->redirect = true;
			$hp->redirecturl = "$CFG->wwwroot/mod/hotpot/view.php?hp=$hp->startofchain";
		}
	} // end if $ok

	return $ok;
}
function hotpot_get_titles_and_next_ex(&$hp, $filepath, $get_next=false) {

	$hp->exercisetitle = '';
	$hp->exercisesubtitle = '';
	$hp->nextexercise = '';

	// open the quiz file
	if ($fp = @fopen($filepath, 'r')) {

		$source = fread($fp, filesize($filepath));
		fclose($fp);

		$xml_tree = new hotpot_xml_tree($source);
		$xml_tree->filetype = '';

		$keys = array_keys($xml_tree->xml);
		foreach ($keys as $key) {

			if ($key=='html' || $key=='HTML') {
				$xml_tree->filetype = 'html';
				$xml_tree->xml_root = "['$key']['#']";
				break;
			} else if (preg_match('/^(hotpot|textoys)-(\w+)-file$/i', $key, $matches)) {
				$xml_tree->filetype = 'xml';
				$xml_tree->xml_root = "['$key']['#']";
				$xml_tree->quiztype = strtolower($matches[2]);
				break;
			}
		}

		$title = '';
		if ($xml_tree->filetype=='html') {
			$title = strip_tags($xml_tree->xml_value('head,title'));
		} else if ($xml_tree->filetype=='xml') {
			$title = strip_tags($xml_tree->xml_value('data,title'));
		}
		$hp->exercisetitle = (empty($title) || is_array($title)) ? basename($filepath) : $title;

		$subtitle = '';
		if ($xml_tree->filetype=='html') {
			$tags = 'body,div';

			$i = 0;
			while (empty($subtitle) && ($div="[$i]") && $xml_tree->xml_value($tags, $div)) {
			
				$class = $xml_tree->xml_value($tags, $div."['@']['class']");
				if (isset($class) && $class=='Titles') {

					$ii = 0;
					while (empty($subtitle) && ($h3=$div."['#']['h3'][$ii]") && $xml_tree->xml_value($tags, $h3)) {

						$class = $xml_tree->xml_value($tags, $h3."['@']['class']");
						if (isset($class) && $class=='ExerciseSubtitle') {
							$subtitle = $xml_tree->xml_value($tags, $h3."['#']");
						}

						$ii++; // increment H3 index
					}
				}
				$i++; // increment DIV index
			}
		} else if ($xml_tree->filetype=='xml') {
			$subtitle = $xml_tree->xml_value('hotpot-config-file,'.$xml_tree->quiztype.',exercise-subtitle');
		}
		$hp->exercisesubtitle = (empty($subtitle) || is_array($subtitle)) ? $hp->exercisetitle : $subtitle;

		$next = '';
		if ($get_next) {

			if ($xml_tree->filetype=='html') {
				$tags = 'body,div';

				$i = 0;
				while (($div="[$i]") && $xml_tree->xml_value($tags, $div)) {
				
					$id = $xml_tree->xml_value($tags, $div."['@']['id']");
					if (isset($id) && $id=='TopNavBar') {

						$ii = 0;
						while (($button=$div."['#']['button'][$ii]") && $xml_tree->xml_value($tags, $button)) {

							$onclick = $xml_tree->xml_value($tags, $button."['@']['onclick']");
							if (isset($onclick) && preg_match("|location='(.*)'|", $onclick, $matches)) {
								$next = $matches[1];
							}

							$ii++; // increment BUTTON index
						}
					}
					$i++; // increment DIV index
				}

			} else if ($xml_tree->filetype=='xml') {

				$include = $xml_tree->xml_value('hotpot-config-file,global,include-next-ex');
				if (!empty($include)) {

					$next = $xml_tree->xml_value("hotpot-config-file,$xml_tree->quiztype,next-ex-url");
					if (is_array($next)) {
						// workaround for the 'next-ex-url' tag being repeated (as it sometimes seems to be)
						$next = $next[0];
					}
				}
			}
		}
		$hp->nextexercise = $next;
	}
}
function hotpot_get_all_instances_in_course($modulename, $course) {

	global $CFG;
	$instances = array();
	
	if ($modinfo = unserialize($course->modinfo)) {
	
		if (isset($CFG->release) && substr($CFG->release, 0, 3)>=1.2) {
			$groupmode = 'cm.groupmode,';
		} else {
			$groupmode = '';
		}
		$query = "
			SELECT 
				cm.id AS coursemodule, 
				cm.visible AS visible,
				$groupmode
				cs.section,
				m.*
			FROM 
				{$CFG->prefix}course_modules AS cm,
				{$CFG->prefix}course_sections AS cs,
				{$CFG->prefix}modules AS md,
				{$CFG->prefix}$modulename AS m
			WHERE 
				cm.course = '$course->id' AND
				cm.instance = m.id AND
				cm.section = cs.id AND
				md.name = '$modulename' AND
				md.id = cm.module
		";
		if ($rawmods = get_records_sql($query)) {

			// cache $isteacher setting
			$isteacher = isteacher($course->id);
			
			foreach ($modinfo as $mod) {

				$visible = false;
				if ($mod->mod == $modulename) {
					if ($isteacher) {
						$visible = true;
					} else if ($mod->mod=='hotpot') {
						$visible = hotpot_is_visible($mod);
					} else {
						$visible = $mod->visible;
					}
				}
				if ($visible) {
					$instance = $rawmods[$mod->cm];
					if (!empty($mod->extra)) {
						$instance->extra = $mod->extra;
					}
					$instances[] = $instance;
				}
			} // end foreach $modinfo
		}
	}
	return $instances;
}

function hotpot_update_chain(&$hp) {
/// update a chain of hotpot actiivities

	$ok = true;
	if ($hotpot_modules = hotpot_get_chain($hp)) {

		// skip updating of these fields
		$skipfields = array('id', 'course', 'name', 'reference', 'summary', 'shownextquiz');
		$fields = array();

		foreach ($hotpot_modules as $hotpot_module) {

			if ($hp->coursemodule==$hotpot_module->id) {
				// don't need to update this hotpot

			} else {	
				// shortcut to hotpot record
				$hotpot = &$hotpot_module->hotpot;
	
				// get a list of fields to update
				if (empty($fields)) {
					$fields = array_keys(get_object_vars($hotpot));
				}

				// assume update is NOT required
				$require_update = false;

				// update field values (except $skipfields)
				foreach($fields as $field) {
					if (in_array($field, $skipfields) || $hotpot->$field==$hp->$field) {
						// update not required for this field
					} else {
						$require_update = true;
						$hotpot->$field = $hp->$field;
					}
				}
	
				// update this $hotpot (if required)
				if ($require_update && !update_record("hotpot", $hotpot)) {
					error("Could not update the $hp->modulename", "view.php?id=$hp->course");
				}
			}
		} // end foreach $ids
	}
	return $ok;
}
function hotpot_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

	$result = false;
	if (delete_records("hotpot", "id", "$id")) {
		$result = true;
		delete_records("hotpot_questions", "hotpot", "$id");
		if ($attempts = get_records_select("hotpot_attempts", "hotpot='$id'")) {
			$ids = implode(',', array_keys($attempts));
			delete_records_select("hotpot_attempts",  "id IN ($ids)");
			delete_records_select("hotpot_details",   "attempt IN ($ids)");
			delete_records_select("hotpot_responses", "attempt IN ($ids)");
		}
	}
	return $result;
}
function hotpot_delete_and_notify($table, $select, $strtable) {
	$count = max(0, count_records_select($table, $select));
	if ($count) {
		delete_records_select($table, $select);
		$count -= max(0, count_records_select($table, $select));
		if ($count) {
			notify(get_string('deleted')." $count x $strtable");
		}
	}
}

function hotpot_user_complete($course, $user, $mod, $hp) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.

	$report = hotpot_user_outline($course, $user, $mod, $hp);
	if (empty($report)) {
		print get_string("noactivity", "hotpot");
	} else {
		$date = userdate($report->time, get_string('strftimerecentfull'));
		print $report->info.' '.get_string('mostrecently').': '.$date;
	}
	return true;
}

function hotpot_user_outline($course, $user, $mod, $hp) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $report->time = the time they did it
/// $report->info = a short text description

	$report = NULL;
	if ($records = get_records_select("hotpot_attempts", "hotpot='$hp->id' AND userid='$user->id'", "timestart ASC", "*")) {
		$scores = array();
		foreach ($records as $record){
			if (empty($report->time)) {
				$report->time = $record->timestart;
			}
			$scores[] = hotpot_format_score($record);
		}
		if (empty($scores)) {
			$report->time = 0;
			$report->info = get_string('noactivity', 'hotpot');
		} else {
			$report->info = get_string('score', 'quiz').': '.implode(', ', $scores);
		}
	}
	return $report;
}

function hotpot_format_score($record, $undefined='&nbsp;') {
	if (isset($record->score)) {
		$score = $record->score;
	} else {
		$score = $undefined;
	}
	return $score;
}

function hotpot_format_status($record, $undefined='&nbsp;') {
	global $HOTPOT_STATUS;

	if (isset($record->status) || isset($HOTPOT_STATUS[$record->status])) {
		$status = $HOTPOT_STATUS[$record->status];
	} else {
		$status = $undefined;
	}
	return $status;
}

function hotpot_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity 
/// that has occurred in hotpot activities and print it out. 
/// Return true if there was output, or false is there was none.

	global $CFG;

	$result = false;
	if($isteacher){

		$records = get_records_sql("
			SELECT
				h.id AS id, 
				h.name AS name,
				COUNT(*) AS count_attempts
			FROM 
				{$CFG->prefix}hotpot AS h, 
				{$CFG->prefix}hotpot_attempts AS a
			WHERE 
				h.course = $course->id 
				AND h.id = a.hotpot 
				AND a.id = a.clickreportid
				AND a.starttime > $timestart
			GROUP  BY 
				h.id, h.name
		");	
		// note that PostGreSQL requires h.name in the GROUP BY clause

		if($records) {

			$names = array();
			foreach ($records as $id => $record){
				$href = "$CFG->wwwroot/mod/hotpot/view.php?hp=$id";
				$name = '&nbsp;<a href="'.$href.'">'.$record->name.'</a>';
				if ($record->count_attempts > 1) {
					$name .= " ($record->count_attempts)";
				}
				$names[] = $name;
			}

			print_headline(get_string('modulenameplural', 'hotpot').':');

			if ($CFG->version >= 2005050500) { // Moodle 1.5+
				echo '<div class="head"><div class="name">'.implode('<br />', $names).'</div></div>';
			} else { // Moodle 1.4.x (or less)
				echo '<font size="1">'.implode('<br />', $names).'</font>';
			}

			$result = true;
		}
	}
	return $result;  //  True if anything was printed, otherwise false 
}

function hotpot_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid, $cmid="", $userid="", $groupid="") {
// Returns all quizzes since a given time. 

	global $CFG;

	// If $cmid or $userid are specified, then this restricts the results
	$cm_select = empty($cmid) ? "" : " AND cm.id = '$cmid'";
	$user_select = empty($userid) ? "" : " AND u.id = '$userid'";

	$records = get_records_sql("
		SELECT
			a.*, 
			h.name, h.course, 
			cm.instance, cm.section,
			u.firstname, u.lastname, u.picture
		FROM 
			{$CFG->prefix}hotpot_attempts AS a,
			{$CFG->prefix}hotpot AS h,
			{$CFG->prefix}course_modules AS cm,
			{$CFG->prefix}user AS u
		WHERE 
			a.timefinish > '$sincetime'
			AND a.id = a.clickreportid
			AND a.userid = u.id $user_select
			AND a.hotpot = h.id $cm_select
			AND cm.instance = h.id
			AND cm.course = '$courseid'
			AND h.course = cm.course
		ORDER BY 
			a.timefinish ASC
	");

	if (!empty($records)) {
		foreach ($records as $record) {
			if (empty($groupid) || ismember($groupid, $record->userid)) {

				unset($activity);

				$activity->type = "hotpot";
				$activity->defaultindex = $index;
				$activity->instance = $record->hotpot;

				$activity->name = $record->name;
				$activity->section = $record->section;

				$activity->content->attemptid = $record->id;
				$activity->content->attempt = $record->attempt;
				$activity->content->score = $record->score;
				$activity->content->timestart = $record->timestart;
				$activity->content->timefinish = $record->timefinish;

				$activity->user->userid = $record->userid;
				$activity->user->fullname = fullname($record);
				$activity->user->picture = $record->picture;

				$activity->timestamp = $record->timefinish;

				$activities[] = $activity;

				$index++;
			}
		} // end foreach
	}
}

function hotpot_print_recent_mod_activity($activity, $course, $detail=false) {
/// Basically, this function prints the results of "hotpot_get_recent_activity"

	global $CFG, $THEME, $USER;

	print '<table border="0" cellpadding="3" cellspacing="0">';

	print '<tr><td bgcolor="'.$THEME->cellcontent2.'" class="forumpostpicture" width="35" valign="top">';
	print_user_picture($activity->user->userid, $course, $activity->user->picture);
	print '</td><td width="100%"><font size="2">';

	if ($detail) {
		// activity icon
		$src = "$CFG->modpixpath/$activity->type/icon.gif";
		print '<img src="'.$src.'" height="16" width="16" alt="'.$activity->type.'" /> ';

		// link to activity
		$href = "$CFG->wwwroot/mod/hotpot/view.php?hp=$activity->instance";
		print '<a href="'.$href.'">'.$activity->name.'</a> - ';
	}
	if (isteacher($course)) {
		// score (with link to attempt details)
		$href = "$CFG->wwwroot/mod/hotpot/review.php?hp=$activity->instance&attempt=".$activity->content->attemptid;
		print '<a href="'.$href.'">('.hotpot_format_score($activity->content).')</a> ';

		// attempt number
		print get_string('attempt', 'quiz').' - '.$activity->content->attempt.'<br />';
	}

	// link to user
	$href = "$CFG->wwwroot/user/view.php?id=$activity->user->userid&course=$course";
	print '<a href="'.$href.'">'.$activity->user->fullname.'</a> ';

	// time and date
	print ' - ' . userdate($activity->timestamp);

	// duration
	$duration = format_time($activity->content->timestart - $activity->content->timefinish);
	print " &nbsp; ($duration)";

	print "</font></td></tr>";
	print "</table>";
}

function hotpot_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

	global $CFG;

	return true;
}

function hotpot_grades($hotpotid) {
/// Must return an array of grades for a given instance of this module, 
/// indexed by user.  It also returns a maximum allowed grade.

	$hotpot = get_record('hotpot', 'id', $hotpotid);
	$return->grades = hotpot_get_grades($hotpot);
	$return->maxgrade = $hotpot->grade;

	return $return;
}
function hotpot_get_grades($hotpot, $user_ids='') {
	global $CFG;

	$grades = array();

	$weighting = $hotpot->grade / 100;
	$precision = hotpot_get_precision($hotpot);

	// set the SQL string to determine the $grade
	$grade = "";
	switch ($hotpot->grademethod) {
		case HOTPOT_GRADEMETHOD_HIGHEST:
			$grade = "ROUND(MAX(score) * $weighting, $precision) AS grade";
			break;
		case HOTPOT_GRADEMETHOD_AVERAGE:
			// the 'AVG' function skips abandoned quizzes, so use SUM(score)/COUNT(id)
			$grade = "ROUND(SUM(score)/COUNT(id) * $weighting, $precision) AS grade";
			break;
		case HOTPOT_GRADEMETHOD_FIRST:
			if ($CFG->dbtype=='postgres7') {
				$grade = "MIN(timestart||'_'||(CASE WHEN (score IS NULL) THEN '' ELSE TRIM(ROUND(score * $weighting, $precision)) END)) AS grade";
			} else {
				$grade = "MIN(CONCAT(timestart, '_', IF(score IS NULL, '', ROUND(score * $weighting, $precision)))) AS grade";
			}
			break;
		case HOTPOT_GRADEMETHOD_LAST:
			if ($CFG->dbtype=='postgres7') {
				$grade = "MAX(timestart||'_'||(CASE WHEN (score IS NULL) THEN '' ELSE TRIM(ROUND(score * $weighting, $precision)) END)) AS grade";
			} else {
				$grade = "MAX(CONCAT(timestart, '_', IF(score IS NULL, '', ROUND(score * $weighting, $precision)))) AS grade";
			}
			break;
	}

	if ($grade) {
		$userid_condition = empty($user_ids) ? '' : "AND userid IN ($user_ids) ";
		$grades = get_records_sql_menu("
			SELECT userid, $grade
			FROM {$CFG->prefix}hotpot_attempts
			WHERE timefinish>0 AND hotpot='$hotpot->id' $userid_condition
			GROUP BY userid
		");
		if ($grades) {
			if ($hotpot->grademethod==HOTPOT_GRADEMETHOD_FIRST || $hotpot->grademethod==HOTPOT_GRADEMETHOD_LAST) {
				// remove left hand characters in $grade (up to and including the underscore)
				foreach ($grades as $userid=>$grade) {
					$grades[$userid] = substr($grades[$userid], strpos($grades[$userid], '_')+1);
				}
			}
		}
	}

	return $grades;
}
function hotpot_get_precision(&$hotpot) {
	return ($hotpot->grademethod==HOTPOT_GRADEMETHOD_AVERAGE || $hotpot->grade<100) ? 1 : 0;
}

function hotpot_get_participants($hotpotid) {
//Must return an array of user records (all data) who are participants
//for a given instance of hotpot. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)
//See other modules as example.
	global $CFG;

	return get_records_sql("
		SELECT DISTINCT 
			u.*
		FROM 
			{$CFG->prefix}user u,
			{$CFG->prefix}hotpot_attempts a
		WHERE 
			u.id = a.userid
			AND a.hotpot = '$hotpotid' 
	");
}

function hotpot_scale_used ($hotpotid, $scaleid) {
//This function returns if a scale is being used by one hotpot
//it it has support for grading and scales. Commented code should be
//modified if necessary. See forum, glossary or journal modules
//as reference.
   
	$report = false;

	//$rec = get_record("hotpot","id","$hotpotid","scale","-$scaleid");
	//
	//if (!empty($rec)  && !empty($scaleid)) {
	//	$report = true;
	//}
   
	return $report;
}

//////////////////////////////////////////////////////////
/// Any other hotpot functions go here.  
/// Each of them must have a name that starts with hotpot


function hotpot_add_attempt($hotpotid) {
	global $db, $CFG, $USER;
	$time = time();
	switch (strtolower($CFG->dbtype)) {
		case 'mysql':
			$timefinish = "IF(a.timefinish IS NULL, '$time', a.timefinish)";
			$clickreportid = "IF(a.clickreportid IS NULL, a.id, a.clickreportid)";
			break;
		case 'postgres7':
			$timefinish = "WHEN(a.timefinish IS NULL) THEN '$time' ELSE a.timefinish";
			$clickreportid = "WHEN(a.clickreportid IS NULL) THEN a.id ELSE a.clickreportid";
			break;
	}

	$db->Execute("
		UPDATE 
			{$CFG->prefix}hotpot_attempts as a
		SET
			a.timefinish = $timefinish,
			a.status = '".HOTPOT_STATUS_ABANDONED."',
			a.clickreportid = $clickreportid
		WHERE
			a.hotpot='$hotpotid'
			AND a.userid='$USER->id'
			AND a.status='".HOTPOT_STATUS_INPROGRESS."'
	");

	// create and add new attempt record
	$attempt->hotpot = $hotpotid;
	$attempt->userid = $USER->id;
	$attempt->attempt = hotpot_get_next_attempt($hotpotid);
	$attempt->timestart = time();

	return insert_record("hotpot_attempts", $attempt);
}
function hotpot_get_next_attempt($hotpotid) {
	global $USER;

	// get max attempt so far
	$i = count_records_select('hotpot_attempts', "hotpot='$hotpotid' AND userid='$USER->id'", 'MAX(attempt)');

	return empty($i) ? 1 : ($i+1);
}
function hotpot_get_question_name($question) {
	$name = '';
	if (isset($question->text)) {
		$name = hotpot_strings($question->text);
	}
	if (empty($name)) {
		$name = $question->name;
	}
	return $name;
}
function hotpot_strings($ids) {

	// array of ids of empty strings
	static $HOTPOT_EMPTYSTRINGS;

	if (!isset($HOTPOT_EMPTYSTRINGS)) { // first time only
		// get ids of empty strings
		$emptystrings = get_records_select('hotpot_strings', 'LENGTH(TRIM(string))=0');
		$HOTPOT_EMPTYSTRINGS = empty($emptystrings) ? array() : array_keys($emptystrings);
	}

	$strings = array();
	if (!empty($ids)) {
		$ids = explode(',', $ids);
		foreach ($ids as $id) {
			if (!in_array($id, $HOTPOT_EMPTYSTRINGS)) {
				$strings[] = hotpot_string($id);
			}
		}
	}
	return implode(',', $strings);
}
function hotpot_string($id) {
	return get_field('hotpot_strings', 'string', 'id', $id);
}

//////////////////////////////////////////////////////////////////////////////////////
/// the class definitions to handle XML trees

// get the standard XML parser supplied with Moodle
require_once($CFG->libdir.DIRECTORY_SEPARATOR.'xmlize.php');

// get the default class for hotpot quiz templates
require_once($CFG->hotpottemplate.DIRECTORY_SEPARATOR.'default.php');

class hotpot_xml_tree {
	function hotpot_xml_tree($str, $xml_root='') {
		if (empty($str)) {
			$this->xml =  array();
		} else {
			$str = utf8_encode($str);
			$this->xml =  xmlize($str, 0);
		}
		$this->xml_root = $xml_root;
	}
	function xml_value($tags, $more_tags="[0]['#']") {

		$tags = empty($tags) ? '' : "['".str_replace(",", "'][0]['#']['", $tags)."']";
		eval('$value = &$this->xml'.$this->xml_root.$tags.$more_tags.';');

		if (is_string($value)) {
			$value = utf8_decode($value);

			// decode angle brackets
			$value = strtr($value, array('&#x003C;'=>'<', '&#x003E;'=>'>'));

			// remove white space between <TABLE>, <UL|OL|DL> and <OBJECT|EMBED> parts 
			// (so it doesn't get converted to <BR>)
			$htmltags = '('
			.	'TABLE|/?CAPTION|/?COL|/?COLGROUP|/?TBODY|/?TFOOT|/?THEAD|/?TD|/?TH|/?TR'
			.	'|OL|UL|/?LI'
			.	'|DL|/?DT|/?DD'
			.	'|EMBED|OBJECT|APPLET|/?PARAM'
			//.	'|SELECT|/?OPTION'
			//.	'|FIELDSET|/?LEGEND'
			//.	'|FRAMESET|/?FRAME'
			.	')'
			;
			$search = '#(<'.$htmltags.'[^>]*'.'>)\s+'.'(?='.'<'.')#is';
			$value = preg_replace($search, '\\1', $value);

			// replace remaining newlines with <BR>
			$value = str_replace("\n", '<br />', $value);

			// encode unicode characters as HTML entities
			// (in particular, accented charaters that have not been encoded by HP)

			// unicode characetsr can be detected by checking the hex value of a character
			//	00 - 7F : ascii char (roman alphabet + punctuation)
			//	80 - BF : byte 2, 3 or 4 of a unicode char
			//	C0 - DF : 1st byte of 2-byte char
			//	E0 - EF : 1st byte of 3-byte char
			//	F0 - FF : 1st byte of 4-byte char
			// if the string doesn't match the above, it might be
			//	80 - FF : single-byte, non-ascii char
			$search = '#('.'[\xc0-\xdf][\x80-\xbf]'.'|'.'[\xe0-\xef][\x80-\xbf]{2}'.'|'.'[\xf0-\xff][\x80-\xbf]{3}'.'|'.'[\x80-\xff]'.')#se';
			$value = preg_replace($search, "hotpot_utf8_to_html_entity('\\1')", $value);

			// NOTICE
			// ======
			// the following lines have been removed because 
			// the final "preg_replace" takes several SECONDS to run

			// encode any orphaned angle brackets back to html entities
			//if (empty($this->tag_pattern)) {
			//	$q   = "'"; // single quote
			//	$qq  = '"'; // double quote
			//	$this->tag_pattern = '<(([^>'.$q.$qq.']*)|('."{$q}[^$q]*$q".')|('."{$qq}[^$qq]*$qq".'))*>';
			//}
			//$value = preg_replace('/<([^>]*'.$this->tag_pattern.')/', '&lt;$1', $value);
			//$value = preg_replace('/('.$this->tag_pattern.'[^<]*)>/', '$1&gt;', $value);
		}
		return $value;
	}
	function xml_values($tags) {
		$i = 0;
		$values = array();
		while ($value = $this->xml_value($tags, "[$i]['#']")) {
			$values[$i++] = $value;
		}
		return $values;
	}
	function obj_value(&$obj, $name) {
		return is_object($obj) ? @$obj->$name : (is_array($obj) ? @$obj[$name] : NULL);
	}
	function encode_cdata(&$str, $tag) {

		// conversion tables
		static $HTML_ENTITIES = array(
			'&apos;' => "'",
			'&quot;' => '"',
			'&lt;'   => '<',
			'&gt;'   => '>',
			'&amp;'  => '&',
		);
		static $ILLEGAL_STRINGS = array(
			"\r"  => '',
			"\n"  => '&lt;br /&gt;',
			']]>' => '&#93;&#93;&#62;',
		);

		// extract the $tag from the $str(ing), if possible
		$pattern = '|(^.*<'.$tag.'[^>]*)(>.*<)(/'.$tag.'>.*$)|is';
		if (preg_match($pattern, $str, $matches)) {

			// encode problematic CDATA chars and strings
			$matches[2] = strtr($matches[2], $ILLEGAL_STRINGS);

			// if there are any ampersands in "open text"
			// surround them by CDATA start and end markers
			// (and convert HTML entities to plain text)
			$search = '/>([^<]*&[^<]*)</e';
			$replace = '"><![CDATA[".strtr("$1", $HTML_ENTITIES)."]]><"';
			$matches[2] = preg_replace($search, $replace, $matches[2]);

			$str = $matches[1].$matches[2].$matches[3];
		}
	}
}

class hotpot_xml_quiz extends hotpot_xml_tree {

	// constructor function
	function hotpot_xml_quiz(&$obj, $read_file=true, $parse_xml=true, $convert_urls=true, $report_errors=true, $create_html=true) {
		// obj can be the $_GET array or a form object/array

		global $CFG, $HOTPOT_OUTPUTFORMAT, $HOTPOT_OUTPUTFORMAT_DIR;

		// check xmlize functions are available
		if (! function_exists("xmlize")) {
			error('xmlize functions are not available');
		}

		$this->read_file = $read_file;
		$this->parse_xml = $parse_xml;
		$this->convert_urls = $convert_urls;
		$this->report_errors = $report_errors;
		$this->create_html = $create_html;

		// extract fields from $obj
		//	course       : the course id
		// 	reference    : the filename within the files folder
		//	location     : "site" files folder or "course" files folder
		//	navigation   : type of navigation required in quiz
		//	forceplugins : force Moodle compatible media players
		$this->course = $this->obj_value($obj, 'course');
		$this->reference = $this->obj_value($obj, 'reference');
		$this->location = $this->obj_value($obj, 'location');
		$this->navigation = $this->obj_value($obj, 'navigation');
		$this->forceplugins = $this->obj_value($obj, 'forceplugins');

		// can't continue if there is no course or reference
		if (empty($this->course) || empty($this->reference)) {
			$this->error = get_string('error_nocourseorfilename', 'hotpot');
			if ($this->report_errors) {
				error($this->error);
			}
			return;
		}

		$this->course_homeurl = "$CFG->wwwroot/course/view.php?id=$this->course";

		// set filedir, filename and filepath
		switch ($this->location) {
			case HOTPOT_LOCATION_SITEFILES:
				$site = get_site();
				$this->filedir = $site->id;
				break;

			case HOTPOT_LOCATION_COURSEFILES:
			default:
				$this->filedir = $this->course;
				break;
		}
		$this->filesubdir = dirname($this->reference);
		if ($this->filesubdir=='.') {
			$this->filesubdir = '';
		}
		if ($this->filesubdir) {
			$this->filesubdir .= DIRECTORY_SEPARATOR;
		}
		$this->filename = basename($this->reference);
		$this->fileroot = $CFG->dataroot.DIRECTORY_SEPARATOR.$this->filedir;
		$this->filepath = $this->fileroot.DIRECTORY_SEPARATOR.$this->reference;

		// read the file, if required
		if ($this->read_file) {
		
			if (!file_exists($this->filepath) || !$fp = fopen($this->filepath, 'r')) {
				$this->error = get_string('error_couldnotopensourcefile', 'hotpot', $this->filepath);
				if ($this->report_errors) {
					error($this->error, $this->course_homeurl);
				}
				return;
			}
	
			// read in the XML source and close the file
			$this->source = fread($fp, filesize($this->filepath));
			fclose($fp);
	
			// convert relative URLs to absolute URLs
			if ($this->convert_urls) {
				$this->hotpot_convert_relative_urls($this->source);
			}

			if ($this->parse_xml) {
			
				// prepend initial <html> tag if required (JCloze HP5)
				if (preg_match('|\.html?$|', $this->filename)) {
					if (preg_match('|</html>\s*$|i', $this->source) && !preg_match('|^\s*<html>|i', $this->source)) {
						$this->source = '<html>'.$this->source;
					}
				}
		
				// encode "gap fill" text in JCloze exercise
				$this->encode_cdata($this->source, 'gap-fill');
		
				// convert source to xml tree
				$this->hotpot_xml_tree($this->source);

				// initialize file type, quiz type and output format
				$this->html = '';
				$this->filetype = '';
				$this->quiztype = '';
				$this->outputformat = 0; // undefined
		
				// link <HTML> tag to <html>, if necessary
				if (isset($this->xml['HTML'])) {
					$this->xml['html'] = &$this->xml['HTML'];
				}
		
				if (isset($this->xml['html'])) {
		
					$this->filetype = 'html';
					$this->quiztype = '';
		
					// relative URLs in "PreloadImages(...);"
					$search = '%'.'(?<='.'PreloadImages'.'\('.')'."([^)]+?)".'(?='.'\);'.')'.'%se';
					$replace = "hotpot_convert_preloadimages_urls('".$this->get_baseurl()."','".$this->reference."','\\1')";
					$this->source = preg_replace($search, $replace, $this->source);

				} else {
					$this->filetype = 'xml';
		
					$keys = array_keys($this->xml);
					foreach ($keys as $key) {
						if (preg_match('/^(hotpot|textoys)-(\w+)-file$/i', $key, $matches)) {
							$this->quiztype = strtolower($matches[2]);
							$this->xml_root = "['$key']['#']";
							break;
						}
					}
				}

				if ($this->create_html) {

					// set the real output format from the requested output format
					$this->real_outputformat = $this->obj_value($obj, 'outputformat');
					$this->draganddrop = '';
					if (
						empty($this->real_outputformat) ||
						$this->real_outputformat==HOTPOT_OUTPUTFORMAT_BEST || 
						empty($HOTPOT_OUTPUTFORMAT_DIR[$this->real_outputformat])
					) {
						// set the best output format for this browser
						// see http://jp2.php.net/function.get-browser
						if (function_exists('get_browser') && ini_get('browscap')) {
							$b = get_browser();
							// apparently get_browser is a slow 
							// so we should store the results in $this->browser
						} else {
							$ua = $_SERVER['HTTP_USER_AGENT'];
							$b = NULL;
							// store the results in $this->browser
							// [parent] => Firefox 0.9
							// [platform] => WinXP
							// [browser] => Firefox
							// [version] => 0.9
							// [majorver] => 0
							// [minorver] => 9
						}
						if ($this->quiztype=='jmatch' || $this->quiztype=='jmix') {
							$this->real_outputformat = HOTPOT_OUTPUTFORMAT_V6_PLUS;
						} else {
							$this->real_outputformat = HOTPOT_OUTPUTFORMAT_V6;
						}
					}
			
					if ($this->real_outputformat==HOTPOT_OUTPUTFORMAT_V6_PLUS) {
						if ($this->quiztype=='jmatch' || $this->quiztype=='jmix') {
							$this->draganddrop = 'd'; // prefix for templates (can also be "f" ?)
						}
						$this->real_outputformat = HOTPOT_OUTPUTFORMAT_V6;
					}
			
					// set the output html
					$this->html = '';
					if ($this->filetype=='html') {
						$this->html = &$this->source;
			
					} else {
						// set path(s) to template
						$this->template_dir = $HOTPOT_OUTPUTFORMAT_DIR[$this->real_outputformat];
						$this->template_dirpath = $CFG->hotpottemplate.DIRECTORY_SEPARATOR.$this->template_dir;
						$this->template_filepath = $CFG->hotpottemplate.DIRECTORY_SEPARATOR.$this->template_dir.'.php';
			
						// check template class exists
						if (!file_exists($this->template_filepath) || !is_readable($this->template_filepath)) {
							$this->error = get_string('error_couldnotopentemplate', 'hotpot', $this->template_dir);
							if ($this->report_errors) {
								error($this->error, $this->course_homeurl);
							}
							return;
						}
			
						// get default and output-specfic template classes
						include($this->template_filepath);
			
						// create html (using the template for the specified output format)
						$this->template = new hotpot_xml_quiz_template($this);
						$this->html = &$this->template->html;
					}

				} // end $this->create_html	
			} // end if $this->parse_xml
		} // end if $this->read_file
	} // end constructor function

	function hotpot_convert_relative_urls(&$str) {
		$tagopen = '(?:(<)|(&lt;)|(&amp;#x003C;))'; // left angle bracket
		$tagclose = '(?(2)>|(?(3)&gt;|(?(4)&amp;#x003E;)))'; //  right angle bracket (to match left angle bracket)

		$space = '\s+'; // at least one space
 		$anychar = '(?:[^>]*?)'; // any character

		$quoteopen = '("|&quot;|&amp;quot;)'; // open quote
		$quoteclose = '\\5'; //  close quote (to match open quote)

		$url = '\S+?\.\S+?';
		$replace = "hotpot_convert_relative_url('".$this->get_baseurl()."', '".$this->reference."', '\\1', '\\6', '\\7')";

		$tags = array('script'=>'src', 'link'=>'href', 'a'=>'href','img'=>'src','param'=>'value');
		foreach ($tags as $tag=>$attribute) {

			$search = "%($tagopen$tag$space$anychar$attribute=$quoteopen)($url)($quoteclose$anychar$tagclose)%ise";
			$str = preg_replace($search, $replace, $str);
		}
	}

	function get_baseurl() {
		// set the url base (first time only)
		if (!isset($this->baseurl)) {
			global $CFG;
			if ($CFG->slasharguments) {
				$this->baseurl = "$CFG->wwwroot/file.php/$this->filedir/";
			} else {
				$this->baseurl = "$CFG->wwwroot/file.php?file=/$this->filedir/";
			}
		}
		return $this->baseurl;
	}


	// insert forms and messages

	function remove_nav_buttons() {
		$search = '#<!-- Begin(Top|Bottom)NavButtons -->(.*?)<!-- End(Top|Bottom)NavButtons -->#s';
		$this->html = preg_replace($search, '', $this->html);
	}
	function insert_script($src=HOTPOT_JS) {
		$script = '<script src="'.$src.'" type="text/javascript" language="javascript"></script>'."\n";
		$this->html = preg_replace('|</head>|i', $script.'</head>', $this->html, 1);
	}
	function insert_submission_form($attemptid, $startblock, $endblock) {
		$form_name = 'store';
		$form_fields = ''
		.	'<input type="hidden" name="attemptid" value="'.$attemptid.'" />'
		.	'<input type="hidden" name="starttime" value="" />'
		.	'<input type="hidden" name="endtime" value="" />'
		.	'<input type="hidden" name="mark" value="" />'
		.	'<input type="hidden" name="detail" value="" />'
		.	'<input type="hidden" name="status" value="" />'
		;
		$this->insert_form($startblock, $endblock, $form_name, $form_fields);
	}
	function insert_giveup_form($attemptid, $startblock, $endblock) {
		$form_name = 'giveup';
		$form_fields = ''
		.	'<input type="hidden" name="status" value="'.HOTPOT_STATUS_ABANDONED.'" />'
		.	'<input type="hidden" name="attemptid" value="'.$attemptid.'" />'
		.	'<input type="submit" value="'.get_string('giveup', 'hotpot').'" class="FuncButton" onfocus="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseover="FuncBtnOver(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" />'
		;
		$this->insert_form($startblock, $endblock, $form_name, $form_fields, true);
	}
	function insert_form($startblock, $endblock, $form_name, $form_fields, $center=false) {
		global $CFG;
		$char = substr($endblock, 0, 1);
		$search = '#(?<='.preg_quote($startblock).')'."[^$char]*".'(?='.preg_quote($endblock).')#';
		$replace = '<form action="'.$CFG->wwwroot.'/mod/hotpot/attempt.php" method="POST" name="'.$form_name.'" target="'.$CFG->framename.'">'.$form_fields.'</form>';
		if ($center) {
			$replace = '<div style="margin-left:auto; margin-right:auto; text-align: center;">'.$replace.'</div>';
		}
		$this->html = preg_replace($search, $replace, $this->html, 1);
	}
	function insert_message($start_str, $message, $color='red', $align='center') {
		$message = '<p align="'.$align.'" style="text-align:'.$align.'"><b><font color="'.$color.'">'.$message."</font></b></p>\n";
		$this->html = preg_replace('|'.preg_quote($start_str).'|', $start_str.$message, $this->html, 1);
	}

	function adjust_media_urls() {

		if ($this->forceplugins) {

			// make sure the Moodle media plugin is available
			global $CFG;
			include_once "$CFG->dirroot/filter/mediaplugin/filter.php";
	
			// exclude swf files from the filter
			$CFG->filter_mediaplugin_ignore_swf = true;
	
			$s = '\s+'; // at least one space
			$n = '[^>]*'; // any character inside a tag
			$q = '["'."']?"; // single, double, or no quote
			$Q = '[^"'."' >]*"; // any charater inside a quoted string
	
			// patterns to media files types and paths
			$filetype = "avi|mpeg|mpg|mp3|mov|wmv";
			$filepath = "$Q\.($filetype)";
	
			// pattern to match <param> tags which contain the file path
			//	wmp        : url
			//	quicktime  : src
			//	realplayer : src
			//	flash      : movie (doesn't need replacing)
			$url_param = "/<param$s{$n}name=$q(src|url)$q$s{$n}value=$q($filepath)$q$n>/is";
	
			// pattern to match <a> tags which link to multimedia files (not swf)
			$link = "/<a$s{$n}href=$q($filepath)$q$n>(.*?)<\/a>/is";
			
			// extract <object> tags
			preg_match_all("|<object$n>(.*?)</object>|is", $this->html, $objects);
	
			$i_max = count($objects[0]);
			for ($i=0; $i<$i_max; $i++) {
	
				$url = '';
				if (preg_match($url_param, $objects[1][$i], $matches)) {
					$url = $matches[2];
				} else if (preg_match($link, $objects[1][$i], $matches)) {
					$url = $matches[1];
				}
	
				if ($url) {
					$txt = trim(strip_tags($objects[1][$i]));
	
					// if url is in the query string, remove the leading characters
					$url = preg_replace('/^[^?]*\?([^=]+=[^&]*&)*[^=]+=([^&]*)$/', '$2', $url, 1);

					$new_object = mediaplugin_filter($this->filedir, '<a href="'.$url.'">'.$txt.'</a>');
					$new_object = preg_replace("|(<a$n>.*<\/a>)(.*<object$n>.*<embed$n>.*)(</embed>.*</object>.*)$|is", '$2$1$3', $new_object);
	
					$this->html = str_replace($objects[0][$i], $new_object, $this->html);
				}
			}
		}
	}

} // end class

function hotpot_convert_preloadimages_urls($baseurl, $reference, $urls) {
	$urls = explode(',', $urls);
	foreach ($urls as $i=>$url) {
		$url = substr($url, 1, strlen($url)-2); // strip quotes
		$urls[$i] = "'".hotpot_convert_url($baseurl, $reference, $url)."'";
	}
	return implode(',',$urls);
}

function hotpot_convert_relative_url($baseurl, $reference, $opentag, $url, $closetag) {

	// match a series of "name=value" pairs in a <PARAM ...> tag
	if (preg_match('|^'.'\w+=[^&]+'.'([&]\w+=[^&]+)*'.'$|', $url)) {
		$query = $url;
		$url = '';
		$fragment = '';

	// parse the $url into $matches
	//	[1] path
	//	[2] query string, if any
	//	[3] anchor fragment, if any
	} else if (preg_match('|^'.'([^?]*)'.'((?:\\?[^#]*)?)'.'((?:#.*)?)'.'$|', $url, $matches)) {
		$url = $matches[1];
		$query = $matches[2];
		$fragment = $matches[3];

	// these appears to be no query or fragment in this url
	} else {
		$query = '';
		$fragment = '';
	}

	if ($url) {
		$url = hotpot_convert_url($baseurl, $reference, $url);
	}

	// try and parse the query string arguments into $matches
	//	[1] names
	//	[2] values
	if ($query && preg_match_all('|([^=]+)=([^&]*)|', substr($query, empty($url) ? 0 : 1), $matches)) {

		$query = array();

		// the values of the following arguments are considered to be URLs
		$url_names = array('src','thesound'); // lowercase

		// loop through the arguments in the query string
		$i_max = count($matches[0]);
		for ($i=0; $i<$i_max; $i++) {
		
			$name = $matches[1][$i];
			$value = $matches[2][$i];
			// convert $value if it is a URL
			if (in_array(strtolower($name), $url_names)) {
				$value = hotpot_convert_url($baseurl, $reference, $value);
			} else {
			}

			$query[] = "$name=$value";
		}
		$query = (empty($url) ? '' : '?').implode('&', $query);
	}

	// remove the slashes that were added by the "e" modifier of preg_replace
	$url = stripslashes($opentag.$url.$query.$fragment.$closetag);
	// N.B. 'e' does not appear to add slashes to single quotes, 
	// so javascript backslashes may get messed up at this point

	return $url;
}

function hotpot_convert_url($baseurl, $reference, $url) {

	// maintain a cache of converted urls
	static $HOTPOT_RELATIVE_URLS = array();

	// is this an absolute url? (or javascript pseudo url)
	if (preg_match('%^(http://|/|javascript:)%i', $url)) {
		// do nothing

	// has this url already been converted?
	} else if (isset($HOTPOT_CONVERTED_URLS[$url])) {
		$url = $HOTPOT_CONVERTED_URLS[$url];

	} else {
		$relativeurl = $url;

		// get the subdirectory, $dir, of the quiz $reference
		$dir = dirname($reference);
	
		// allow for leading "./" and "../"
		while (preg_match('|^(\.{1,2})/(.*)$|', $url, $matches)) {
			if ($matches[1]=='..') {
				$dir = dirname($dir);
			}
			$url = $matches[2];
		}

		// add subdirectory, $dir, to $baseurl, if necessary
		if ($dir && $dir!='.') {
			$baseurl .= "$dir/";
		}

		// prefix $url with $baseurl
		$url = "$baseurl$url";

		// add url to cache
		$HOTPOT_CONVERTED_URLS[$relativeurl] = $url;
	}
	return $url;
}

// ===================================================
// function for adding attempt questions and responses
// ===================================================

function hotpot_add_attempt_details(&$attempt) {

	// encode ampersands so that HTML entities are preserved in the XML parser
	// N.B. ampersands inside <![CDATA[ ]]> blocks do NOT need to be encoded

	$old = &$attempt->details; // shortcut to "old" details
	$new = '';
	$i = 0;
	while (($ii = strpos($old, '<![CDATA[', $i)) && ($iii = strpos($old, ']]>', $ii))) {
		$iii += 3;
		$new .= str_replace('&', '&amp;', substr($old, $i, $ii-$i)).substr($old, $ii, $iii-$ii);
		$i = $iii;
	}
	$new .= str_replace('&', '&amp;', substr($old, $i));
	unset($old);

	// parse the attempt details as xml
	$details = new hotpot_xml_tree($new, "['hpjsresult']['#']");

	$num = -1;
	$q_num = -1;
	$question = NULL;
	$reponse = NULL;

	$i = 0;
	$tags = 'fields,field';

	while (($field="[$i]['#']") && $details->xml_value($tags, $field)) {

		$name = $details->xml_value($tags, $field."['fieldname'][0]['#']");
		$data = $details->xml_value($tags, $field."['fielddata'][0]['#']");

		// parse the field name into $matches
		//	[1] quiz type
		//	[2] attempt detail name
		if (preg_match('|^(\w+?)_(\w+)$|', $name, $matches)) {
			$quiztype = strtolower($matches[1]);
			$name = strtolower($matches[2]);

			// parse the attempt detail $name into $matches
			//	[1] question number
			//	[2] question detail name

			if (preg_match('|^q(\d+)_(\w+)$|', $name, $matches)) {
				// question number and detail name
				$num = $matches[1];
				$name = strtolower($matches[2]);
				$data = addslashes($data);

				// is this a new question (or the first one)?
				if ($q_num != $num) {

					// add previous question and response, if any
					hotpot_add_response($attempt, $question, $response);

					// initialize question object
					$question = NULL;
					$question->name = '';
					$question->text = '';
					$question->hotpot = $attempt->hotpot;

					// initialize response object
					$response = NULL;
					$response->attempt = $attempt->id;

					// update question number
					$q_num = $num;
				}

				// adjust field name and value, and set question type
				// (may not be necessary one day)
				hotpot_adjust_response_field($quiztype, $question, $num, $name, $data);

				// add $data to the question/response details
				switch ($name) {
					case 'name':
					case 'type':
						$question->$name = $data;
						break;
					case 'text':
						$question->$name = hotpot_string_id($data);
						break;
	
					case 'correct':
					case 'ignored':
					case 'wrong':
						$response->$name = hotpot_string_ids($data);
						break;
	
					case 'score':
					case 'weighting':
					case 'hints':
					case 'clues':
					case 'checks':
						$response->$name = intval($data);
						break;
				}

			} else { // attempt details

				// adjust field name and value
				hotpot_adjust_response_field($quiztype, $question, $num='', $name, $data);

				// add $data to the attempt details
				if ($name=='penalties') {
					$attempt->$name = intval($data);
				}
			}
		}

		$i++;
	} // end while

	// add the final question and response, if any
	hotpot_add_response($attempt, $question, $response);
}
function hotpot_add_response(&$attempt, &$question, &$response) {
	global $db, $next_url;

	$loopcount = 1;

	$looping = isset($question) && isset($question->name) && isset($response);
	while ($looping) {
	
		if ($loopcount==1) {
			$questionname = $question->name;
		}

		if (!$question->id = get_field('hotpot_questions', 'id', 'name', $question->name, 'hotpot', $attempt->hotpot)) {
			// add question record
			if (!$question->id = insert_record('hotpot_questions', $question)) {
				error("Could not add question record (attempt_id=$attempt->id): ".$db->ErrorMsg(), $next_url);
			}
		}

		if (record_exists('hotpot_responses', 'attempt', $attempt->id, 'question', $question->id)) {
			// there is already a response to this question for this attempt
			// probably because this quiz has two questions with the same text
			//	e.g. Which one of these answers is correct?
	
			// To workaround this, we create new question names
			//	e.g. Which one of these answers is correct? (2)
			// until we get a question name for which there is no response yet on this attempt
	
			$loopcount++;
			$question->name = "$questionname ($loopcount)";
			
			// This method fails to correctly identify questions in 
			// quizzes which allow questions to be shuffled or omitted. 
			// As yet, there is no workaround for such cases.

		} else {
			$response->question = $question->id;
	
			// add response record
			if(!$response->id = insert_record('hotpot_responses', $response)) {
				error("Could not add response record (attempt_id=$attempt->id, question_id=$question->id): ".$db->ErrorMsg(), $next_url);
			}
			
			// we can stop looping now
			$looping = false;
		}
	} // end while
}
function hotpot_adjust_response_field($quiztype, &$question, &$num, &$name, &$data) {
	switch ($quiztype) {
		case 'jbc':
			$question->type = HOTPOT_JCB;
			switch ($name) {
				case 'right':
					$name = 'correct';
				break;
			}
			break;
		case 'jcloze':
			$question->type = HOTPOT_JCLOZE;
			$question->name = $num;
			switch ($name) {
				case 'penalties':
					$name = 'checks';
					if (is_numeric($data)) {
						$data++;
					}
					break;
				case 'clue_shown':
					$name = 'clues';
					$data = ($data=='YES' ? 1 : 0);
					break;
				case 'clue_text':
					$name = 'text';
					break;
			}
			break;
		case 'jcross':
			$question->type = HOTPOT_JCROSS;
			switch ($name) {
				case 'across':
				case 'down':
					$question->name = $num.'_'.$name;
					$name = 'correct';
					break;
				case 'across_clue':
				case 'down_clue':
					$name = 'text';
					break;
				default:
					if (preg_match('/^(across|down)_(\w+)$/i', $name, $matches)) {
						$name = $matches[2];
					}
					break;

			}
			break;
		case 'jmatch':
			$question->type = HOTPOT_JMATCH;
			switch ($name) {
				case 'attempts':
					$name = 'penalties';
					if (is_numeric($data) && $data>0) {
						$data--;
					}
				break;
				case 'lhs':
					$name = 'name';
				break;
				case 'rhs':
					$name = 'correct';
				break;
			}
			break;
		case 'jmix':
			$question->type = HOTPOT_JMIX;
			$question->name = $num;
			switch ($name) {
				// keep these in for "restore" of courses
				// which were backed up with HotPot v2.0.x
				case 'wrongguesses':
					$name = 'checks';
					if (is_numeric($data)) {
						$data++;
					}
				break;
				case 'right':
					$name = 'correct';
				break;
			}
			break;
			break;
		case 'jquiz':
			switch ($name) {
				case 'type':
					$data = HOTPOT_JQUIZ;
					switch ($data) {
						case 'multiple-choice':
							$data .= '.'.HOTPOT_JQUIZ_MULTICHOICE;
						break;
						case 'short-answer':
							$data .= '.'.HOTPOT_JQUIZ_SHORTANSWER;
						break;
						case 'hybrid':
							$data .= '.'.HOTPOT_JQUIZ_HYBRID;
						break;
						case 'multi-select':
							$data .= '.'.HOTPOT_JQUIZ_MULTISELECT;
						case 'n/a':
						default:
							// do nothing more
						break;
					}
				break;
				case 'question':
					$name = 'name';
				break;
			}
			break;

		case 'rhubarb':
			$question->type = HOTPOT_TEXTOYS_RHUBARB;
			if (empty($question->name)) {
				$question->name = $num;
			}
			break;

		case 'sequitur':
			$question->type = HOTPOT_TEXTOYS_SEQUITUR;
			break;
	}
}
function hotpot_string_ids($field_value) {
	$ids = array();
	$strings = explode(',', $field_value);
	foreach($strings as $str) {
		$ids[] = hotpot_string_id($str);
	}
	return implode(',', $ids);
}
function hotpot_string_id($str) {
	$id = '';
	if ($str) {

		// get the id from the table if it is already there
		if (!$id = get_field('hotpot_strings', 'id', 'string', $str)) {

			// create a string "object"
			$string = NULL;
			$string->string = $str;

			// try and add the new string to the table
			if (!$id = insert_record('hotpot_strings', $string)) {
				global $db;
				error("Could not add string record for '".htmlspecialchars($str)."': ".$db->ErrorMsg());
			}
		}
	}
	return $id;
}

function hotpot_flush($n=0, $time=false) {
	if ($time) {
		$t = strftime("%X",time());
	} else {
		$t = "";
	}
	echo str_repeat(" ", $n) . $t . "\n";
	flush();
}

if (!function_exists('html_entity_decode')) {
	// add this function for php version<4.3
	function html_entity_decode($str) {
		$t = get_html_translation_table(HTML_ENTITIES);
		$t = array_flip($t);
		return strtr($str, $t);
	}

}

if (!defined('PARAM_RAW')) define('PARAM_RAW', 0x0000);
if (!defined('PARAM_INT'))  define('PARAM_INT', 0x0002);
if (!defined('PARAM_ALPHA')) define('PARAM_ALPHA', 0x0004);

if (!function_exists('required_param')) {
	// add this function for Moodle version<1.4.2
	function required_param($varname, $options=PARAM_RAW) {
		if (isset($_POST[$varname])) {
			$param = $_POST[$varname];
		} else if (isset($_GET[$varname])) {
			$param = $_GET[$varname];
		} else { // missing
			error('A required parameter ('.$varname.') was missing');
		}
		return $param;
	}
}
if (!function_exists('optional_param')) {
	// add this function for Moodle version<1.4.2
	function optional_param($varname, $default=NULL, $options=PARAM_RAW) {
		if (isset($_POST[$varname])) {
			$param = $_POST[$varname];
		} else if (isset($_GET[$varname])) {
			$param = $_GET[$varname];
		} else {
			$param = $default;
		}
		return $param;
	}
}
if (!function_exists('set_user_preference')) {
	// add this function for Moodle 1.x
	function set_user_preference($name, $value) {
		return false;
	}
}
if (!function_exists('get_user_preference')) {
	// add this function for Moodle 1.x
	function get_user_preference($name=NULL, $default=NULL) {
		return $default;
	}
}
if (!function_exists('fullname')) {
	// add this function for Moodle 1.x
	function fullname($user) {
		return "$user->firstname $user->lastname";
	}
}

function hotpot_utf8_to_html_entity($char) {
	// http://www.zend.com/codex.php?id=835&single=1

	// array used to figure what number to decrement from character order value 
	// according to number of characters used to map unicode to ascii by utf-8 
	static $HOTPOT_UTF8_DECREMENT = array(
		1=>0, 2=>192, 3=>224, 4=>240
	);

	// the number of bits to shift each character by 
	static $HOTPOT_UTF8_SHIFT = array(
		1=>array(0=>0),
		2=>array(0=>6,  1=>0),
		3=>array(0=>12, 1=>6,  2=>0),
		4=>array(0=>18, 1=>12, 2=>6, 3=>0)
	);
	 
	$dec = 0; 
	$len = strlen($char);
	for ($pos=0; $pos<$len; $pos++) {
		$ord = ord ($char{$pos});
		$ord -= ($pos ? 128 : $HOTPOT_UTF8_DECREMENT[$len]); 
		$dec += ($ord << $HOTPOT_UTF8_SHIFT[$len][$pos]); 
	} 
	return '&#x'.sprintf('%04X', $dec).';'; 
}

function hotpot_print_show_links($course, $location, $reference, $actions='', $spacer=' &nbsp; ', $new_window=false) {
	global $CFG;
	if (is_string($actions)) {
		if (empty($actions)) {
			$actions = 'showxmlsource,showxmltree,showhtmlsource';
		}
		$actions = explode(',', $actions);
	}
	$strenterafilename = get_string('enterafilename', 'hotpot');
	$html = <<<END_OF_SCRIPT
<script type="text/javascript" language="javascript">
<!--
	function setLink(lnk) {
		var form = document.forms['form'];
		return setLinkAttribute(lnk, 'reference', form) && setLinkAttribute(lnk, 'location', form);
	}
	function setLinkAttribute(lnk, name, form) {
		// set link attribute value using
		// f(orm) name and e(lement) name

		var r = true; // result

		var obj = (form) ? form.elements[name] : null;
		if (obj) {
			r = false;
			var v = getObjValue(obj);
			if (v=='') {
				alert('$strenterafilename');
			} else {
				var s = lnk.href;
				var i = s.indexOf('?');
				if (i>=0) {
					i = s.indexOf(name+'=', i+1);
					if (i>=0) {
						i += name.length+1;
						var ii = s.indexOf('&', i);
						if (ii<0) {
							ii = s.length;
						}
						lnk.href = s.substring(0, i) + v + s.substring(ii);
						r = true;
					}
				}
			}
		}
		return r;
	}
	function getObjValue(obj) {
		var v = ''; // the value
		var t = (obj && obj.type) ? obj.type : "";
		if (t=="text" || t=="textarea" || t=="hidden") {
			v = obj.value;
		} else if (t=="select-one" || t=="select-multiple") {
			var l = obj.options.length;
			for (var i=0; i<l; i++) {
				if (obj.options[i].selected) {
					v += (v=="" ? "" : ",") + obj.options[i].value;
				}
			}
		}
		return v;
	}
	function getDir(s) {
		if (s.charAt(0)!='/') {
			s = '/' + s;
		}
		var i = s.lastIndexOf('/');
		return s.substring(0, i);
	}
//-->
</script>
END_OF_SCRIPT;

	foreach ($actions as $action) {
		$html .= $spacer
		.	'<a href="'
		.			$CFG->wwwroot.'/mod/hotpot/show.php'
		.			'?course='.$course.'&location='.$location.'&reference='.urlencode($reference).'&action='.$action
		.		'"'
		.		' onclick="return setLink(this);"'
		.		($new_window ? ' target="_blank"' : '')
		.	'>'.get_string($action, 'hotpot').'</a>'
		;
	}
	print '<span class="helplink">'.$html.'</span>';
}

?>
