<?PHP  // $Id$

/// Library of functions and constants for module hotpot

$CFG->hotpotroot = $CFG->dirroot.DIRECTORY_SEPARATOR.'mod'.DIRECTORY_SEPARATOR.'hotpot';

define("HOTPOT_JS", "$CFG->wwwroot/mod/hotpot/hotpot-full.js");

define("HOTPOT_NO",  "0");
define("HOTPOT_YES", "1");

define("HOTPOT_LOCATION_COURSEFILES", "0");
define("HOTPOT_LOCATION_SITEFILES",   "1");

$HOTPOT_LOCATION = array (
	HOTPOT_LOCATION_COURSEFILES => get_string("coursefiles"),
	HOTPOT_LOCATION_SITEFILES   => get_string("sitefiles"),
);

define("HOTPOT_OUTPUTFORMAT_BEST",     "1");
define("HOTPOT_OUTPUTFORMAT_V3",      "11");
define("HOTPOT_OUTPUTFORMAT_V4",      "12");
define("HOTPOT_OUTPUTFORMAT_V5",      "13");
define("HOTPOT_OUTPUTFORMAT_V6",      "14");
define("HOTPOT_OUTPUTFORMAT_V6_PLUS", "15");
define("HOTPOT_OUTPUTFORMAT_FLASH",   "20");
define("HOTPOT_OUTPUTFORMAT_MOBILE",  "30");

$HOTPOT_OUTPUTFORMAT = array (
	HOTPOT_OUTPUTFORMAT_BEST    => get_string("outputformat_best", "hotpot"),
	HOTPOT_OUTPUTFORMAT_V6_PLUS => get_string("outputformat_v6_plus", "hotpot"),
	HOTPOT_OUTPUTFORMAT_V6      => get_string("outputformat_v6", "hotpot"),
	// HOTPOT_OUTPUTFORMAT_V5      => get_string("outputformat_v5", "hotpot"),
	// HOTPOT_OUTPUTFORMAT_V4      => get_string("outputformat_v4", "hotpot"),
	// HOTPOT_OUTPUTFORMAT_V3      => get_string("outputformat_v3", "hotpot"),
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
	HOTPOT_NAVIGATION_GIVEUP => get_string("navigation_give_up", "hotpot"),
	HOTPOT_NAVIGATION_NONE    => get_string("navigation_none", "hotpot"),
);

define("HOTPOT_JCB",    "1");
define("HOTPOT_JCLOZE", "2");
define("HOTPOT_JCROSS", "3");
define("HOTPOT_JMATCH", "4");
define("HOTPOT_JMIX",   "5");
define("HOTPOT_JQUIZ",  "6");
define("HOTPOT_TEXTOYS_RHUBARB",    "7");
define("HOTPOT_TEXTOYS_SEQUITUR",   "8");

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
	HOTPOT_GRADEMETHOD_LAST    => get_string("attemptlast", "quiz"),
);

function hotpot_add_instance($hp) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.
	
	hotpot_set_times($hp);
	return insert_record("hotpot", $hp);
}


function hotpot_update_instance($hp) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

	hotpot_set_times($hp);
	$hp->id = $hp->instance;

	return update_record("hotpot", $hp);
}

function hotpot_set_times(&$hp) {
	$time = time();

	$hp->timecreated = $time;
	$hp->timemodified = $time;

	$hp->timeopen = make_timestamp(
		$hp->openyear, $hp->openmonth, $hp->openday, 
		$hp->openhour, $hp->openminute, 0
	);
	$hp->timeclose = make_timestamp(
		$hp->closeyear, $hp->closemonth, $hp->closeday, 
		$hp->closehour, $hp->closeminute, 0
	);
}

function hotpot_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

	$result = false;
	if (delete_records("hotpot", "id", "$id")) {
		$result = true;
		if ($attempts = get_records_select("hotpot_attempts", "hotpot='$id'")) {
			$ids = implode(',', array_keys($attempts));
			delete_records_select("hotpot_attempts", "id IN ($ids)");
			delete_records_select("hotpot_questions", "attempt IN ($ids)");
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
			notify(get_string("deleted")." $count x $strtable");
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

function hotpot_format_score($record) {
	if (isset($record->score)) {
		$score = $record->score;
	} else {
		$str = empty($record->timefinish) ? 'inprogress' : 'abandoned';
		$score = '<i>'.get_string($str, 'hotpot').'</i>';
	}
	return $score;
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
				AND a.starttime > $timestart
			GROUP  BY 
				h.id, h.name
		");	
		// note that PostGreSQL requires h.name in the GROUP BY clause

		if($records) {

			$names = array();
			foreach ($records as $id => $record){
				$href = $CFG->wwwroot.'/mod/hotpot/view.php?id='.$id;
				$name = "&nbsp;<a href=\"$href\">$record->name</a>";
				if ($record->count_attempts > 1) {
					$name .= " ($record->count_attempts)";
				}
				$names[] = $name;
			}

            print_headline(get_string('modulenameplural', 'hotpot').':');
			echo '<div class="head"><div class="name">'.implode('<br />', $names).'</div></div>';

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
				$activity->content->details = $record->details;
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
		$href = "$CFG->wwwroot/mod/hotpot/view.php?id=$activity->instance";
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
	$precision = ($hotpot->grademethod==HOTPOT_GRADEMETHOD_AVERAGE || $hotpot->grade<100) ? 1 : 0;

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

//////////////////////////////////////////////////////////////////////////////////////
/// Any other hotpot functions go here.  Each of them must have a name that 
/// starts with hotpot


function hotpot_add_attempt($hotpotid, $userid=NULL) {
	$userid = hotpot_get_userid($userid);

	hotpot_close_previous_attempts($hotpotid, $userid);

	$attempt->hotpot = $hotpotid;
	$attempt->userid = $userid;
	$attempt->attempt = hotpot_get_next_attempt($hotpotid, $userid);
	$attempt->timestart = time();

	return insert_record("hotpot_attempts", $attempt);
}
function hotpot_close_previous_attempts($hotpotid, $userid=NULL, $time=NULL) {
/// set previously unfinished attempts of this quiz by this user to "finished"
	if (empty($time)) {
		$time = time();
	}
	$userid = hotpot_get_userid($userid);
	set_field("hotpot_attempts", "timefinish", $time, "hotpot", $hotpotid, "userid", $userid, "timefinish", 0);
}
function hotpot_get_next_attempt($hotpotid, $userid=NULL) {
	// get max attempt so far
	$i = count_records_select(
		'hotpot_attempts', 
		"hotpot='$hotpotid' AND userid='".hotpot_get_userid($userid)."'", 
		'MAX(attempt)'
	);
	return empty($i) ? 1 : ($i+1);
}
function hotpot_get_userid($userid=NULL) {
	global $USER;
	return isset($userid) ? $userid : $USER->id;
}
function hotpot_get_question_name($question) {
	$name = '';
	if (isset($question->text)) {
		$name =hotpot_strings($question->text);
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
		$emptystringids = get_records_select('hotpot_strings', 'LENGTH(TRIM(string))=0');
		$HOTPOT_EMPTYSTRINGS = empty($emptystringids) ? array() : array_keys($emptystringids);
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

			// decode angle brackets and replace newlines
			$value = strtr($value, array('&#x003C;'=>'<', '&#x003E;'=>'>', "\n"=>'<br />'));

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
		$HTML_ENTITIES = array(
			'&apos;' => "'",
			'&quot;' => '"',
			'&lt;'   => '<',
			'&gt;'   => '>',
			'&amp;'  => '&',
		);
		$ILLEGAL_STRINGS = array(
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
	function hotpot_xml_quiz(&$obj) {
		// obj can be the $_GET array or a form object/array

		global $CFG, $HOTPOT_OUTPUTFORMAT, $HOTPOT_OUTPUTFORMAT_DIR;

		// check xmlize functions are available
		if (! function_exists("xmlize")) {
			error('xmlize functions are not available');
		}		

		// extract fields from $obj
		//	course       : the course id
		// 	reference    : the filename within the files folder for this course
		//	navigation   : type of navigation required in quiz
		//	forceplugins : force Moodle compatible media players
		$this->course = $this->obj_value($obj, 'course');
		$this->reference = $this->obj_value($obj, 'reference');
		$this->location = $this->obj_value($obj, 'location');
		$this->navigation = $this->obj_value($obj, 'navigation');
		$this->forceplugins = $this->obj_value($obj, 'forceplugins');

		// can't continue if there is no course or reference
		if (empty($this->course) || empty($this->reference)) {
			error('Could not create XML tree: missing course or reference');
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
		$this->filename = basename($this->reference);
		$this->filepath = $CFG->dataroot.DIRECTORY_SEPARATOR.$this->filedir.DIRECTORY_SEPARATOR.$this->reference;

		// try and open the file
		if (!file_exists($this->filepath) || !$fp = fopen($this->filepath, 'r')) {
			error('Could not open the XML source file &quot;'.$this->filename.'&quot;', $this->course_homeurl);
		}

		// read in the XML source and close the file
		$this->source = fread($fp, filesize($this->filepath));
		fclose($fp);

		// convert relative URLs to absolute URLs
		$this->hotpot_convert_relative_urls($this->source);

		// encode "gap fill" text in JCloze exercise
		$this->encode_cdata($this->source, 'gap-fill');

		// convert source to xml tree
		$this->hotpot_xml_tree($this->source);

		// initialize file type, quiz type and output format
		$this->html = '';
		$this->filetype = '';
		$this->quiztype = '';
		$this->outputformat = 0;

		// link <HTML> tag to <html>, if necessary
		if (isset($this->xml['HTML'])) {
			$this->xml['html'] = &$this->xml['HTML'];
		}

		if (isset($this->xml['html'])) {

			$this->filetype = 'html';

			// shortcut to source
			$s = &$this->source;

			// try to set the quiz type from phrases in the source

			if (strpos($s, 'QuizForm') && strpos($s, 'CheckForm') && strpos($s, 'CorrectAnswers')) {
				$this->outputformat = HOTPOT_OUTPUTFORMAT_V3;
				$this->quiztype = 'jmatch';

			} else if (strpos($s, 'name="FeedbackFrame"') && strpos($s, 'name="CodeFrame"')) {
				$this->outputformat = HOTPOT_OUTPUTFORMAT_V3;
				$this->quiztype = strpos($s, 'QuizForm') ? 'jcb' : strpos($s, 'Cloze') ? 'jcloze' : strpos($s, 'Crossword') ? 'jcross' : strpos($s, 'QForm1') ? 'jquiz' : '';

			} else if (strpos($s, 'function DynLayer')) {
				$this->outputformat = HOTPOT_OUTPUTFORMAT_V4;
				$this->quiztype = (strpos($s, 'QForm') && strpos($s, 'QForm.FB[QNum]')) ? 'jcb' : strpos($s, 'Cloze') ? 'jcloze' : strpos($s, 'Crossword') ? 'jcross' : strpos($s, 'ExCheck') ? 'jmatch' : (strpos($s, 'QForm') && strpos($s, 'QForm.Answer')) ? 'jquiz' : '';

			} else if (strpos($s, 'name="TopFrame"') && strpos($s, 'name="BottomFrame"')) {
				$this->outputformat = HOTPOT_OUTPUTFORMAT_V5;
				$this->quiztype = (strpos($s, 'QForm') && strpos($s, 'FB_[QNum]_[ANum]')) ? 'jcb' : strpos($s, 'form name="Cloze"') ? 'jcloze' : strpos($s, 'AnswerForm') ? 'jcross' : (strpos($s, 'QForm') && strpos($s, 'sel[INum]')) ? 'jmatch' : strpos($s, 'ButtonForm') ? 'jmix' : (strpos($s, 'QForm[QNum]') && strpos($s, 'Buttons[QNum]')) ? 'jquiz' : '';

			} else if (strpos($s, '<div id="MainDiv" class="StdDiv">')) {
				$this->outputformat = HOTPOT_OUTPUTFORMAT_V6;
				$this->quiztype = strpos($s, 'jcb test') ? 'jcb' : strpos($s, '<div id="ClozeDiv">') ? 'jcloze' : (strpos($s, 'GridDiv') || strpos($s, 'Clues')) ? 'jcross' : strpos($s, 'MatchDiv') ? 'jmatch' : strpos($s, 'SegmentDiv') ? 'jmix' : ((strpos($s, 'QForm') && strpos($s, 'QForm.Guess')) || strpos($s, 'Questions')) ? 'jquiz' : '';

			} else if (strpos($s, '<div class="StdDiv">')) { // TexToys
				$this->outputformat = HOTPOT_OUTPUTFORMAT_V6;
				$this->quiztype = strpos($s, 'var Words = new Array()') ? 'rhubarb' : strpos($s, 'var Segments = new Array()') ? 'sequitur' : '';

			} else if (strpos($s, 'D = new Array')) {
				$this->outputformat = HOTPOT_OUTPUTFORMAT_V6_PLUS; // drag and drop (HP5 and HP6)
				$this->quiztype = (strpos($s, 'F = new Array')) ? 'jmatch' : (strpos($s, 'Drop = new Array')) ? 'jmix' : 0;
			}

			unset($s);

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

		// set template source directory
		$this->template_dir = $HOTPOT_OUTPUTFORMAT_DIR[$this->real_outputformat];
		$this->template_dir_path = $CFG->hotpotroot.DIRECTORY_SEPARATOR.$this->template_dir.DIRECTORY_SEPARATOR.'source';

		// set the output html
		$this->html = '';
		if ($this->filetype=='html') {
			$this->html = &$this->source;

		} else {
			$method = $this->template_dir.'_create_html';
			if (method_exists($this, $method)) {
				eval('$this->'.$method.'();');
			} else {
				error(
					$method.'Could not create quiz in &quot;'.$this->template_dir.'&quot; format',
					$this->course_homeurl
				);
			}
		}

	} // end constructor function

	function read_template($filename, $tag='temporary') {
		// create the file path to the template
		$filepath = $this->template_dir_path.DIRECTORY_SEPARATOR.$filename;

		// try and open the template file
		if (!file_exists($filepath) || !$fp = fopen($filepath, "r")) {
			error(
				'Could not open the '.$this->template_dir.' template file &quot;'.$filename.'&quot;',
				$this->course_homeurl
			);
		}

		// read in the template and close the file
		$this->$tag = fread($fp, filesize($filepath));
		fclose($fp);

		// expand the blocks and strings in the template
		$this->expand_blocks($tag);
		$this->expand_strings($tag);

		if ($tag=='temporary') {
			$template = $this->$tag;
			$this->$tag = '';
			return $template;
		}

	}
	function expand_blocks($tag) {
		// get block $names
		//	[1] the full block name (including optional leading 'str' or 'incl')
		//	[2] leading 'incl' or 'str'
		//	[3] the real block name ([1] without [2])
		$search = '/\[\/((incl|str)?(\w+))\]/';
		preg_match_all($search, $this->$tag, $names);

		$i_max = count($names[0]);
		for ($i=0; $i<$i_max; $i++) {

			$method = $this->template_dir.'_expand_'.$names[3][$i];
			if (method_exists($this, $method)) {

				eval('$value=$this->'.$method.'();');

				$search = '/\['.$names[1][$i].'\](.*?)\[\/'.$names[1][$i].'\]/s';
				preg_match_all($search, $this->$tag, $blocks);

				$ii_max = count($blocks[0]);
				for ($ii=0; $ii<$ii_max; $ii++) {

					$replace = empty($value) ? '' : $blocks[1][$ii];	
					$this->$tag = str_replace($blocks[0][$ii], $replace, $this->$tag);
				}
			} else {
				error('Could not expand template block &quot;'.$matches[4][$i].'&quot;', $this->course_homeurl);
				//print 'Could not expand template block &quot;'.$blockname.'&quot; for '.$tag."<br />\n";
			}
		}
	}
	function expand_strings($tag, $search='') {
		if (empty($search)) {
			// default $search $pattern
			$search = '/\[(?:bool|int|str)(\\w+)\]/';
		}
		preg_match_all($search, $this->$tag, $matches);

		$i_max = count($matches[0]);
		for ($i=0; $i<$i_max; $i++) {

			$method = $this->template_dir.'_expand_'.$matches[1][$i];
			if (method_exists($this, $method)) {

				eval('$replace=$this->'.$method.'();');
				$this->$tag = str_replace($matches[0][$i], $replace, $this->$tag);
			}			
		}
	}

	function bool_value($tags, $more_tags="[0]['#']") {
		$value = $this->xml_value($tags, $more_tags);
		return empty($value) ? 'false' : 'true';
	}
	function int_value($tags, $more_tags="[0]['#']") {
		return intval($this->xml_value($tags, $more_tags));
	}
	function js_value($tags, $more_tags="[0]['#']", $convert_to_unicode=false) {
		return $this->js_safe($this->xml_value($tags, $more_tags), $convert_to_unicode);
	}
	function js_safe($str, $convert_to_unicode=false) {
		// encode a string for javascript

		// decode "<" and ">" - not necesary as it was done by xml_value()
		// $str  = strtr($str, array('&#x003C;' => '<', '&#x003E;' => '>'));

		// escape single quotes and backslashes
		$str = strtr($str, array("'"=>"\\'", '\\'=>'\\\\'));

		// convert newlines (win = "\r\n", mac="\r", linix/unix="\n")
		$nl = '\\n'; // javascript newline
		$str = strtr($str, array("\r\n"=>$nl, "\r"=>$nl, "\n"=>$nl));

		// convert (hex and decimal) html entities to unicode, if required
		if ($convert_to_unicode) {
			$str = preg_replace('|&#x([0-9A-F]+);|i', '\\u\\1', $str);
			$str = preg_replace('|&#(\d+);|e', "'\\u'.sprintf('%04X', '\\1')", $str);
		}

		return $str;
	}

	// =================================
	//    functions for v6 quizzes
	// =================================

	function v6_create_html() {

		if (isset($_GET['css'])) {
			$this->css = '';
			$this->read_template('hp6.cs_', 'css');

		} else if (isset($_GET['js'])) {
			$this->js = '';
			$this->read_template($this->draganddrop.$this->quiztype.'6.js_', 'js');

		} else {
			$this->html = '';
			$this->read_template($this->draganddrop.$this->quiztype.'6.ht_', 'html');
		}

		// expand special strings, if any
		$pattern = '';
		switch ($this->quiztype) {
			case 'jcloze':
				$pattern = '/\[(PreloadImageList)\]/';
				break;
			case 'jcross':
				$pattern = '/\[(PreloadImageList|ShowHideClueList)\]/';
				break;
			case 'jmatch':
				$pattern = '/\[(PreloadImageList|QsToShow|FixedArray|DragArray)\]/';
				break;
			case 'jmix':
				$pattern = '/\[(PreloadImageList|SegmentArray|AnswerArray)\]/';
				break;
			case 'jquiz':
				$pattern = '/\[(PreloadImageList|QsToShow)\]/';
				break;
		}
		if (!empty($pattern)) {
			$this->expand_strings('html', $pattern);
		}
	}

	// captions and messages

	function v6_expand_AlsoCorrect() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,also-correct");
	}
	function v6_expand_CapitalizeFirst() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,capitalize-first-letter");
	}
	function v6_expand_CheckCaption() {
		return $this->xml_value('hotpot-config-file,global,check-caption');
	}
	function v6_expand_CorrectIndicator() {
		return $this->xml_value('hotpot-config-file,global,correct-indicator');
	}
	function v6_expand_Back() {
		return $this->xml_value('hotpot-config-file,global,include-back');
	}
	function v6_expand_BackCaption() {
		return $this->xml_value('hotpot-config-file,global,back-caption');
	}
	function v6_expand_ClickToAdd() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,click-to-add");
	}
	function v6_expand_Contents() {
		return $this->xml_value('hotpot-config-file,global,include-contents');
	}
	function v6_expand_ContentsCaption() {
		return $this->xml_value('hotpot-config-file,global,contents-caption');
	}
	function v6_expand_GuessCorrect() {
		return $this->js_value("hotpot-config-file,$this->quiztype,guess-correct");
	}
	function v6_expand_GuessIncorrect() {
		return $this->js_value("hotpot-config-file,$this->quiztype,guess-incorrect");
	}
	function v6_expand_Hint() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,include-hint");
	}
	function v6_expand_HintCaption() {
		return $this->xml_value('hotpot-config-file,global,hint-caption');
	}
	function v6_expand_IncorrectIndicator() {
		return $this->xml_value('hotpot-config-file,global,incorrect-indicator');
	}
	function v6_expand_LastQCaption() {
		return $this->xml_value('hotpot-config-file,global,last-q-caption');
	}
	function v6_expand_NextCorrect() {
		$value = $this->xml_value("hotpot-config-file,$this->quiztype,next-correct-part");
		if (empty($value)) { // jquiz
			$value = $this->xml_value("hotpot-config-file,$this->quiztype,next-correct-letter");
		}
		return $value;
	}
	function v6_expand_NextEx() {
		return $this->xml_value('hotpot-config-file,global,include-next-ex');
	}
	function v6_expand_NextExCaption() {
		return $this->xml_value('hotpot-config-file,global,next-ex-caption');
	}
	function v6_expand_NextQCaption() {
		return $this->xml_value('hotpot-config-file,global,next-q-caption');
	}
	function v6_expand_OKCaption() {
		return $this->xml_value('hotpot-config-file,global,ok-caption');
	}
	function v6_expand_Restart() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,include-restart");
	}
	function v6_expand_RestartCaption() {
		return $this->xml_value('hotpot-config-file,global,restart-caption');
	}
	function v6_expand_ShowAllQuestionsCaption() {
		return $this->xml_value('hotpot-config-file,global,show-all-questions-caption');
	}
	function v6_expand_ShowOneByOneCaption() {
		return $this->xml_value('hotpot-config-file,global,show-one-by-one-caption');
	}
	function v6_expand_TheseAnswersToo() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,also-correct");
	}
	function v6_expand_ThisMuch() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,this-much-correct");
	}
	function v6_expand_Undo() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,include-undo");
	}
	function v6_expand_UndoCaption() {
		return $this->xml_value('hotpot-config-file,global,undo-caption');
	}
	function v6_expand_YourScoreIs() {
		return $this->xml_value('hotpot-config-file,global,your-score-is');
	}

	// reading

	function v6_expand_Reading() {
		return $this->xml_value('data,reading,include-reading');
	}
	function v6_expand_ReadingText() {
		$title = $this->v6_expand_ReadingTitle();
		$value = $this->xml_value('data,reading,reading-text');
		$value = empty($value) ? '' : ('<div class="ReadingText">'.$value.'</div>');
		return $title.$value;
	}
	function v6_expand_ReadingTitle() {
		$value = $this->xml_value('data,reading,reading-title');
		return empty($value) ? '' : ('<h3 class="ExerciseSubtitle">'.$value.'</h3>');
	}

	// timer 

	function v6_expand_Timer() {
		return $this->xml_value('data,timer,include-timer');
	}
	function v6_expand_JSTimer() {
		return $this->read_template('hp6timer.js_');
	}
	function v6_expand_Seconds() {
		return $this->xml_value('data,timer,seconds');
	}

	// send results 

	function v6_expand_SendResults() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,send-email");
	}
	function v6_expand_JSSendResults() {
		return $this->read_template('hp6sendresults.js_');
	}
	function v6_expand_FormMailURL() {
		return $this->xml_value('hotpot-config-file,global,formmail-url');
	}
	function v6_expand_EMail() {
		return $this->xml_value('hotpot-config-file,global,email');
	}
	function v6_expand_NamePlease() {
		return $this->js_value('hotpot-config-file,global,name-please');
	}

	// preload images

	function v6_expand_PreloadImages() {
		$value = $this->v6_expand_PreloadImageList();
		return empty($value) ? false : true;
	}
	function v6_expand_PreloadImageList() {

		// check it has not been set already
		if (!isset($this->PreloadImageList)) {

			// the list of image urls
			$list = array();

			// extract <img> tags
			$img_tag = htmlspecialchars('|&#x003C;img.*?src="(.*?)".*?&#x003E;|is');
			if (preg_match_all($img_tag, $this->source, $matches)) {
				$list = $matches[1];

				// remove duplicates
				$list = array_unique($list);
			}

			// convert to comma delimited string
			$this->PreloadImageList = empty($list) ? '' : "'".implode(',', $list)."'";
		}
		return $this->PreloadImageList;
	}

	// html files (all quiz types)

	function v6_expand_PlainTitle() {
		return $this->xml_value('data,title');
	}
	function v6_expand_ExerciseSubtitle() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,exercise-subtitle");
	}
	function v6_expand_Instructions() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,instructions");
	}
	function v6_expand_DublinCoreMetadata() {
		return ''
		.	'<link rel="schema.DC" href="'.$this->xml_value('', "['rdf:RDF'][0]['@']['xmlns:dc']").'" />'."\n"
		.	'<meta name="DC:Creator" content="'.$this->xml_value('rdf:RDF,rdf:Description,dc:creator').'" />'."\n"
		.	'<meta name="DC:Title" content="'.strip_tags($this->xml_value('rdf:RDF,rdf:Description,dc:title')).'" />'."\n"
		;
	}
	function v6_expand_FullVersionInfo() {
		global $CFG;
		require_once($CFG->hotpotroot.DIRECTORY_SEPARATOR.'version.php'); // set $module
		return $this->xml_value('version').'.x (Moodle '.$CFG->release.', hotpot-module '.$this->obj_value($module, 'release').')';
	}
	function v6_expand_HeaderCode() {
		return $this->xml_value('hotpot-config-file,global,header-code');
	}
	function v6_expand_StyleSheet() {
		$this->read_template('hp6.cs_', 'css');
		return $this->css;
	}

	// stylesheet (hp6.cs_)

	function v6_expand_PageBGColor() {
		return $this->xml_value('hotpot-config-file,global,page-bg-color');
	}
	function v6_expand_GraphicURL() {
		return $this->xml_value('hotpot-config-file,global,graphic-url');
	}
	function v6_expand_ExBGColor() {
		return $this->xml_value('hotpot-config-file,global,ex-bg-color');
	}

	function v6_expand_FontFace() {
		return $this->xml_value('hotpot-config-file,global,font-face');
	}
	function v6_expand_FontSize() {
		return $this->xml_value('hotpot-config-file,global,font-size');
	}
	function v6_expand_TextColor() {
		return $this->xml_value('hotpot-config-file,global,text-color');
	}
	function v6_expand_TitleColor() {
		return $this->xml_value('hotpot-config-file,global,title-color');
	}
	function v6_expand_LinkColor() {
		return $this->xml_value('hotpot-config-file,global,link-color');
	}
	function v6_expand_VLinkColor() {
		return $this->xml_value('hotpot-config-file,global,vlink-color');
	}

	function v6_expand_NavTextColor() {
		return $this->xml_value('hotpot-config-file,global,page-bg-color');
	}
	function v6_expand_NavBarColor() {
		return $this->xml_value('hotpot-config-file,global,nav-bar-color');
	}
	function v6_expand_NavLightColor() {
		$color = $this->xml_value('hotpot-config-file,global,nav-bar-color');
		return $this->get_halfway_color($color, '#ffffff'); 
	}
	function v6_expand_NavShadeColor() {
		$color = $this->xml_value('hotpot-config-file,global,nav-bar-color');
		return $this->get_halfway_color($color, '#000000');
	}

	function v6_expand_FuncLightColor() { // top-left of buttons
		$color = $this->xml_value('hotpot-config-file,global,ex-bg-color');
		return $this->get_halfway_color($color, '#ffffff');
	}
	function v6_expand_FuncShadeColor() { // bottom right of buttons
		$color = $this->xml_value('hotpot-config-file,global,ex-bg-color');
		return $this->get_halfway_color($color, '#000000');
	}

	function get_halfway_color($x, $y) {
		// returns the $color that is half way between $x and $y
		$color = $x; // default
		$rgb = '/^\#?([0-9a-f])([0-9a-f])([0-9a-f])$/i';
		$rrggbb = '/^\#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i';
		if ((
			preg_match($rgb, $x, $x_matches) ||
			preg_match($rrggbb, $x, $x_matches) 
		) && (
			preg_match($rgb, $y, $y_matches) ||
			preg_match($rrggbb, $y, $y_matches)
		)) {
			$color = '#';
			for ($i=1; $i<=3; $i++) {
				$x_dec = hexdec($x_matches[$i]);
				$y_dec = hexdec($y_matches[$i]);
				$color .= sprintf('%02x', min($x_dec, $y_dec) + abs($x_dec-$y_dec)/2);
			}
		}
		return $color;
	}

	// navigation buttons

	function v6_expand_NavButtons() {
		$back = $this->v6_expand_Back();
		$next_ex = $this->v6_expand_NextEx();
		$contents = $this->v6_expand_Contents();
		return (empty($back) && empty($next_ex) && empty($contents) ? false : true);
	}
	function v6_expand_NavBarJS() {
		return $this->v6_expand_NavButtons();
	}

	// js files (all quiz types)

	function v6_expand_JSBrowserCheck() {
		return $this->read_template('hp6browsercheck.js_');
	}
	function v6_expand_JSButtons() {
		return $this->read_template('hp6buttons.js_');
	}
	function v6_expand_JSCard() {
		return $this->read_template('hp6card.js_');
	}
	function v6_expand_JSCheckShortAnswer() {
		return $this->read_template('hp6checkshortanswer.js_');
	}
	function v6_expand_JSHotPotNet() {
		return $this->read_template('hp6hotpotnet.js_');
	}
	function v6_expand_JSShowMessage() {
		return $this->read_template('hp6showmessage.js_');
	}
	function v6_expand_JSUtilities() {
		return $this->read_template('hp6utilities.js_');
	}

	// js files

	function v6_expand_JSJCloze6() {
		return $this->read_template('jcloze6.js_');
	}
	function v6_expand_JSJCross6() {
		return $this->read_template('jcross6.js_');
	}
	function v6_expand_JSJMatch6() {
		return $this->read_template('jmatch6.js_');
	}
	function v6_expand_JSJMix6() {
		return $this->read_template('jmix6.js_');
	}
	function v6_expand_JSJQuiz6() {
		return $this->read_template('jquiz6.js_');
	}

	// drag and drop

	function v6_expand_JSDJMatch6() {
		return $this->read_template('djmatch6.js_');
	}
	function v6_expand_JSDJMix6() {
		return $this->read_template('djmix6.js_');
	}

	// what are these for?

	function v6_expand_JSFJMatch6() {
		return $this->read_template('fjmatch6.js_');
	}
	function v6_expand_JSFJMix6() {
		return $this->read_template('fjmix6.js_');
	}

	// jmatch6.js_

	function v6_expand_ShuffleQs() {
		return $this->bool_value("hotpot-config-file,$this->quiztype,shuffle-questions");
	}
	function v6_expand_QsToShow() {
		$i = $this->xml_value("hotpot-config-file,$this->quiztype,show-limited-questions");
     		if ($i) {
			$i = $this->xml_value("hotpot-config-file,$this->quiztype,questions-to-show");
		}
		if (empty($i)) {
			$i = 0;
			switch ($this->quiztype) {
				case 'jmatch':
					$values = $this->xml_values('data,matching-exercise,pair');
					$i = count($values);
					break;
				case 'jquiz':
					while ($this->xml_value('data,questions,question-record', "[$i]['#']['question'][0]['#']")) {
						$i++;
					}
					break;
			} // end switch
		}
		return $i;
	}
	function v6_expand_MatchDivItems() {
		$str = '';

		$this->get_jmatch_items($l_items=array(), $r_items = array());

		$l_keys = $this->shuffle_jmatch_items($l_items);
		$r_keys = $this->shuffle_jmatch_items($r_items);

		$options = '<option value="x">'.$this->xml_value('data,matching-exercise,default-right-item').'</option>';
		foreach ($r_keys as $key) {
			$options .= '<option value="'.$key.'">'.$r_items[$key]['text'][0]['#'].'</option>'."\n";
		}
		foreach ($l_keys as $key) {
			$str .= '<tr><td class="LeftItem">'.$l_items[$key]['text'][0]['#'].'</td>';
			$str .= '<td class="RightItem"><select id="s'.$key.'_'.$key.'">'.$options.'</select></td>';
			$str .= '<td></td></tr>';
		}
		return $str;
	}

	// jmix6.js_

	function v6_expand_Punctuation() {
		$tags = 'data,jumbled-order-exercise';
		$chars = array_merge(
			$this->jmix_Punctuation("$tags,main-order,segment"),
			$this->jmix_Punctuation("$tags,alternate")
		);
		$chars = array_unique($chars);
		$chars = implode('', $chars);
		$chars = $this->js_safe($chars, true);
		return $chars;
	}
	function jmix_Punctuation($tags) {
		$chars = array();

		// all punctutation except '&#;' (because they are used in html entities)
		$ENTITIES = $this->jmix_encode_punctuation('!"$%'."'".'()*+,-./:<=>?@[\]^_`{|}~');
		$pattern = "/&#x([0-9A-F]+);/i";
		$i = 0;

		// get next segment (or alternate answer)
		while ($value = $this->xml_value($tags, "[$i]['#']")) {

			// convert low-ascii punctuation to entities
			$value = strtr($value, $ENTITIES);

			// extract all hex HTML entities
			if (preg_match_all($pattern, $value, $matches)) {

				// loop through hex entities
				$m_max = count($matches[0]);
				for ($m=0; $m<$m_max; $m++) {

					// convert to hex number
					eval('$hex=0x'.$matches[1][$m].';');

					// is this a punctuation character?
					if (
						($hex>=0x0020 && $hex<=0x00BF) || // ascii punctuation
						($hex>=0x2000 && $hex<=0x206F) || // general punctuation
						($hex>=0x3000 && $hex<=0x303F) || // CJK punctuation
						($hex>=0xFE30 && $hex<=0xFE4F) || // CJK compatability
						($hex>=0xFE50 && $hex<=0xFE6F) || // small form variants
						($hex>=0xFF00 && $hex<=0xFF40) || // halfwidth and fullwidth forms (1)
						($hex>=0xFF5B && $hex<=0xFF65) || // halfwidth and fullwidth forms (2)
						($hex>=0xFFE0 && $hex<=0xFFEE)    // halfwidth and fullwidth forms (3)
					) {
						// add this character
						$chars[] = $matches[0][$m];
					}
				}
			}
			$i++;
		}

		return $chars;
	}
	function v6_expand_OpenPunctuation() {
		$tags = 'data,jumbled-order-exercise';
		$chars = array_merge(
			$this->jmix_OpenPunctuation("$tags,main-order,segment"),
			$this->jmix_OpenPunctuation("$tags,alternate")
		);
		$chars = array_unique($chars);
		$chars = implode('', $chars);
		$chars = $this->js_safe($chars, true);
		return $chars;
	}
	function jmix_OpenPunctuation($tags) {
		$chars = array();

		// unicode punctuation designations (pi="initial quote", ps="open")
		//	http://www.sql-und-xml.de/unicode-database/pi.html
		//	http://www.sql-und-xml.de/unicode-database/ps.html
		$pi = '0022|0027|00AB|2018|201B|201C|201F|2039';
		$ps = '0028|005B|007B|0F3A|0F3C|169B|201A|201E|2045|207D|208D|2329|23B4|2768|276A|276C|276E|2770|2772|2774|27E6|27E8|27EA|2983|2985|2987|2989|298B|298D|298F|2991|2993|2995|2997|29D8|29DA|29FC|3008|300A|300C|300E|3010|3014|3016|3018|301A|301D|FD3E|FE35|FE37|FE39|FE3B|FE3D|FE3F|FE41|FE43|FE47|FE59|FE5B|FE5D|FF08|FF3B|FF5B|FF5F|FF62';
		$pattern = "/(&#x($pi|$ps);)/i";

		$ENTITIES = $this->jmix_encode_punctuation('"'."'".'(<[{');

		$i = 0;
		while ($value = $this->xml_value($tags, "[$i]['#']")) {
			$value = strtr($value, $ENTITIES);
			if (preg_match_all($pattern, $value, $matches)) {
				$chars = array_merge($chars, $matches[0]);
			} 
			$i++;
		}

		return $chars;
	}
	function jmix_encode_punctuation($str) {
		$ENTITIES = array();
		$i_max = strlen($str);
		for ($i=0; $i<$i_max; $i++) {
			$ENTITIES[$str{$i}] = '&#x'.sprintf('%04X', ord($str{$i})).';';
		}
		return $ENTITIES;
	}
	function v6_expand_ExerciseTitle() {
		return $this->xml_value('data,title');
	}

	// Jmix specials

	function v6_expand_SegmentArray() {
		$segments = $this->xml_values('data,jumbled-order-exercise,main-order,segment');

		$this->seed_random_number_generator();
		$keys = array_keys($segments);
		shuffle($keys);

		$str = '';
		for($i=0; $i<count($keys); $i++) {
			$str .= "Segments[$i] = new Array();\n";
			$str .= "Segments[$i][0] = '".$this->js_safe($segments[$keys[$i]])."';\n";
			$str .= "Segments[$i][1] = ".($keys[$i]+1).";\n";
			$str .= "Segments[$i][2] = 0;\n";
		}
		return $str;
	}
	function v6_expand_AnswerArray() {

		$segments = $this->xml_values('data,jumbled-order-exercise,main-order,segment');
		$alternates = $this->xml_values('data,jumbled-order-exercise,alternate');

		$i = 0;
		$pattern = '';
		$str = 'Answers['.$i++.'] = new Array(';
		for($ii=0; $ii<count($segments); $ii++) {
			$str .= ($ii==0 ? '' : ',').($ii+1);
			$pattern .= (empty($pattern) ? '' : '|').preg_quote($segments[$ii], '/');
		}
		$str .= ");\n";
		$pattern = '/^('.$pattern.')\\s*/';

		foreach ($alternates as $alternate) {
			$ii = 0;
			$str .= 'Answers['.$i++.'] = new Array(';
			while (!empty($alternate) && preg_match($pattern, $alternate, $matches)) {
				$iii = array_search($matches[1], $segments);
				if (is_int($iii)) {
					$str .= ($ii==0 ? '' : ',').($iii+1);
					$alternate = substr($alternate, strlen($matches[0]));
					$ii++;
				} else {
					// $matches[1] was not found in $segments!
					// something is very wrong, so abort the loop
					break; 
				}
			}
			$str .= ");\n";
		}
		return $str;
	}

	// ===============================================================

	// JMix (jmix6.js_)

	function v6_expand_RemainingWords() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,remaining-words");
	}
	function v6_expand_TimesUp() {
		return $this->xml_value('hotpot-config-file,global,times-up');
	}

	// nav bar

	function v6_expand_NavBar() {
		$tag = 'navbar';
		$this->read_template('hp6navbar.ht_', $tag);
		return $this->$tag;
	}
	function v6_expand_TopNavBar() {
		return $this->v6_expand_NavBar();
	}
	function v6_expand_BottomNavBar() {
		return $this->v6_expand_NavBar();
	}
	function v6_expand_NextExURL() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,next-ex-url");
	}

	// hp6navbar.ht_

	function v6_expand_NavBarID() {
		return ''; // what's this?;
	}
	function v6_expand_ContentsURL() {
		return $this->xml_value('hotpot-config-file,global,contents-url');
	}

	// conditional blocks

	function v6_expand_ShowAnswer() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,include-show-answer");
	}
	function v6_expand_Slide() {
		return true; // whats's this (JMatch drag and drop)
	}

	// specials (JMatch)

	function v6_expand_FixedArray() {
		$str = '';
		$this->get_jmatch_items($l_items=array(), $r_items = array());
		foreach ($l_items as $i=>$item) {
			$str .= "F[$i] = new Array();\n";
			$str .= "F[$i][0] = '".$this->js_safe($item['text'][0]['#'], true)."';\n";
			$str .= "F[$i][1] = ".($i+1).";\n";
		}
		return $str;
	}
	function v6_expand_DragArray() {
		$str = '';
		$this->get_jmatch_items($l_items=array(), $r_items = array());
		foreach ($r_items as $i=>$item) {
			$str .= "D[$i] = new Array();\n";
			$str .= "D[$i][0] = '".$this->js_safe($item['text'][0]['#'], true)."';\n";
			$str .= "D[$i][1] = ".($i+1).";\n";
			$str .= "D[$i][2] = 0;\n";
 		}
		return $str;
	}

	function get_jmatch_items(&$l_items, &$r_items) {
		$i = 0;
		while(
			($l_item = $this->xml_value('data,matching-exercise,pair',"[$i]['#']['left-item'][0]['#']")) &&
			($r_item = $this->xml_value('data,matching-exercise,pair',"[$i]['#']['right-item'][0]['#']"))
		) {
			$l_items[] = $l_item;
			$r_items[] = $r_item;
			$i++;
		}
	}
	function shuffle_jmatch_items(&$items) {
		// get moveable items
		$moveable_keys = array();
		for($i=0; $i<count($items); $i++) {
			if(empty($items[$i]['fixed'][0]['#'])) {
				$moveable_keys[] = $i;
			}
		}
		// shuffle moveable items
		$this->seed_random_number_generator();
		shuffle($moveable_keys);

		$keys = array();
		for($i=0, $ii=0; $i<count($items); $i++) {
			if(empty($items[$i]['fixed'][0]['#'])) {
				// 	moveable items are inserted in a shuffled order
				$keys[] = $moveable_keys[$ii++];
			} else {
				//	fixed items stay where they are
				$keys[] = $i;
			}
		}
		return $keys;
	}
	function seed_random_number_generator() {
		static $seeded_RNG = FALSE;
		if (!$seeded_RNG) {
			srand((double) microtime() * 1000000);
			$seeded_RNG = TRUE;
		}
	}

	// specials (JMix)


	// specials (JCloze)

	function v6_expand_ItemArray() {
		$q = 0;
		$str = '';
		switch ($this->quiztype) {
			case 'jcloze':
				$str .= "I = new Array();\n";
				$tags = 'data,gap-fill,question-record';
				while (($question="[$q]['#']") && $this->xml_value($tags, $question)) {
					$a = 0;
					$aa = 0;
					while (($answer=$question."['answer'][$a]['#']") && $this->xml_value($tags, $answer)) {
						$text = $this->js_value($tags,  $answer."['text'][0]['#']", true);
						if ($text) {
							if ($aa==0) { // first time only
								$str .= "I[$q] = new Array();\n";
								$str .= "I[$q][1] = new Array();\n";
							}
							$str .= "I[$q][1][$aa] = new Array();\n";
							$str .= "I[$q][1][$aa][0] = '$text';\n";
							$aa++;
						}
						$a++;
					}
					// add clue, if any answers were found
					if ($aa) {
						$clue = $this->js_value($tags, $question."['clue'][0]['#']", true);
						$str .= "I[$q][2]='$clue';\n";
					}
					$q++;
				}
				break;
			case 'jquiz':
				$str .= "I=new Array();\n";
				$tags = 'data,questions,question-record';
				while (($question="[$q]['#']") && $this->xml_value($tags, $question)) {

					$question_type = $this->int_value($tags, $question."['question-type'][0]['#']");
					$weighting = $this->int_value($tags, $question."['weighting'][0]['#']");
					$clue = $this->js_value($tags, $question."['clue'][0]['#']", true);

					$answers = $question."['answers'][0]['#']";

					$a = 0;
					$aa = 0;
					while (($answer = $answers."['answer'][$a]['#']") && $this->xml_value($tags, $answer)) {
						$text =     $this->js_value($tags,  $answer."['text'][0]['#']", true);
						$feedback = $this->js_value($tags,  $answer."['feedback'][0]['#']", true);
						$correct =  $this->int_value($tags, $answer."['correct'][0]['#']", true);
						$percent =  $this->int_value($tags, $answer."['percent-correct'][0]['#']", true);
						$include =  $this->int_value($tags, $answer."['include-in-mc-options'][0]['#']", true);
						if ($text) {
							if ($aa==0) { // first time only
								$str .= "I[$q]=new Array();\n";
								$str .= "I[$q][0]=$weighting;\n";
								$str .= "I[$q][1]='$clue';\n";
								$str .= "I[$q][2]='".($question_type-1)."';\n";
								$str .= "I[$q][3]=new Array();\n";
							}
							$str .= "I[$q][3][$aa]=new Array('$text','$feedback',$correct,$percent,$include);\n";
							$aa++;
						}
						$a++;
					}
					$q++;
				}
				break;
		}
		return $str;
	}

	function v6_expand_ClozeBody() {
		$str = '';
		$q = 0;
		$tags = 'data,gap-fill';
		while ($text = $this->xml_value($tags, "[0]['#'][$q]")) {
			$str .= $text;
			if (($question="[$q]['#']") && $this->xml_value("$tags,question-record", $question)) {
				$str .= '<span class="GapSpan" id="GapSpan'.$q.'"><INPUT type="text" id="Gap'.$q.'" onfocus="TrackFocus('.$q.')" onblur="LeaveGap()" class="GapBox" size="6"></input><button style="line-height: 1.0" class="FuncButton" onfocus="FuncBtnOver(this)" onmouseover="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" onclick="ShowClue('.$q.')">[?]</button></span>';
			}
			$q++;
		}


		return $str;
	}

	// JCloze quiztype

	function v6_expand_WordList() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,include-word-list");
	}
	function v6_expand_Keypad() {
		$str = '';
		if ($this->bool_value("hotpot-config-file,$this->quiztype,include-keypad")) {

			// these characters must always be in the keypad
			$chars = array();
			$this->add_keypad_chars($chars, $this->xml_value('hotpot-config-file,global,keypad-characters'));
			
			// append other characters used in the answers
			$tags = '';
			switch ($this->quiztype) {
				case 'jcloze':
					$tags = 'data,gap-fill,question-record';
					break;
				case 'jquiz':
					$tags = 'data,questions,question-record';
					break;
			}
			if ($tags) {
				$q = 0;
				while (($question="[$q]['#']") && $this->xml_value($tags, $question)) {

					if ($this->quiztype=='jquiz') {
						$answers = $question."['answers'][0]['#']";
					} else {
						$answers = $question;
					}
					
					$a = 0;
					while (($answer=$answers."['answer'][$a]['#']") && $this->xml_value($tags, $answer)) {
						$this->add_keypad_chars($chars, $this->xml_value($tags,  $answer."['text'][0]['#']"));
						$a++;
					}
					$q++;
				}
			}

			// remove duplicate characters and sort
			$chars = array_unique($chars);
			usort($chars, "hotpot_sort_keypad_chars");

			// create keypad buttons for each character
			$str .= '<div class="Keypad">';
			foreach ($chars as $char) {
				$str .= "<button onclick=\"TypeChars('".$this->js_safe($char, true)."'); return false;\">$char</button>";
			}
			$str .= '</div>';
		}
		return $str;
	}
	function add_keypad_chars(&$chars, $text) {
		if (preg_match_all('|&[^;]+;|i', $text, $more_chars)) {
			$chars = array_merge($chars, $more_chars[0]);
		}
	}
	function v6_expand_Correct() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,guesses-correct");
	}
	function v6_expand_Incorrect() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,guesses-incorrect");
	}
	function v6_expand_GiveHint() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,next-correct-letter");
	}
	function v6_expand_CaseSensitive() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,case-sensitive");
	}

	// JCross quiztype

	function v6_expand_CluesAcrossLabel() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,clues-across");
	}
	function v6_expand_CluesDownLabel() {
		$this->xml_value("hotpot-config-file,$this->quiztype,clues-down");
		return '';
	}
	function v6_expand_EnterCaption() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,enter-caption");
	}
	function v6_expand_ShowHideClueList() {
		$value = $this->xml_value("hotpot-config-file,$this->quiztype,include-clue-list");
		return empty($value) ? ' style="display: none;"' : '';
	}

	// JCross specials

	function v6_expand_CluesDown() {
		return $this->v6_expand_jcross_clues('D');
	}
	function v6_expand_CluesAcross() {
		return $this->v6_expand_jcross_clues('A');
	}
	function v6_expand_jcross_clues($direction) {
		// $direction: A(cross) or D(own)
		$this->v6_get_jcross_grid($row=NULL, $r_max=0, $c_max=0);
		$i = 0; // clue index;
		$str = '';
		for($r=0; $r<=$r_max; $r++) {
			for($c=0; $c<=$c_max; $c++) {
				$aword = $this->get_jcross_aword($row, $r, $r_max, $c, $c_max);
				$dword = $this->get_jcross_dword($row, $r, $r_max, $c, $c_max);
				if ($aword || $dword) {
					$i++; // increment clue index

					// get the definition for this word
					$def = '';
					$word = ($direction=='A') ? $aword : $dword;
					$clues = $this->xml_values('data,crossword,clues,item');
					foreach ($clues as $clue) {
						if ($clue['word'][0]['#']==$word) {
							$def = $clue['def'][0]['#'];
							$def = strtr($def, array('&#x003C;'=>'<', '&#x003E;'=>'>', "\n"=>'<br />'));
							break;
						}
					}

					if (!empty($def)) {
						$str .= '<tr><td class="ClueNum">'.$i.'. </td><td id="Clue_'.$direction.'_'.$i.'" class="Clue">'.$def.'</td></tr>';
					}
				}
			}
		}
		return $str;
	}

	// jcross6.js_

	function v6_expand_LetterArray() {
		$this->v6_get_jcross_grid($row=NULL, $r_max=0, $c_max=0);
		$str = '';
		for($r=0; $r<=$r_max; $r++) {
			$str .= "L[$r] = new Array(";
			for($c=0; $c<=$c_max; $c++) {
				$str .= ($c>0 ? ',' : '')."'".$this->js_safe($row[$r]['cell'][$c]['#'], true)."'";
			}
			$str .= ");\n";
		}
		return $str;
	}
	function v6_expand_GuessArray() {
		$this->v6_get_jcross_grid($row=NULL, $r_max=0, $c_max=0);
		$str = '';
		for($r=0; $r<=$r_max; $r++) {
			$str .= "G[$r] = new Array('".str_repeat("','", $c_max)."');\n";
		}
		return $str;
	}
	function v6_expand_ClueNumArray() {
		$this->v6_get_jcross_grid($row=NULL, $r_max=0, $c_max=0);
		$i = 0; // clue index
		$str = '';
		for($r=0; $r<=$r_max; $r++) {
			$str .= "CL[$r] = new Array(";
			for($c=0; $c<=$c_max; $c++) {
				if ($c>0) {
					$str .= ',';
				}
				$aword = $this->get_jcross_aword($row, $r, $r_max, $c, $c_max);
				$dword = $this->get_jcross_dword($row, $r, $r_max, $c, $c_max);
				if (empty($aword) && empty($dword)) {
					$str .= 0;
				} else {
					$i++; // increment the clue index
					$str .= $i;
				}
			}
			$str .= ");\n";
		}
		return $str;
	}
	function v6_expand_GridBody() {
		$this->v6_get_jcross_grid($row=NULL, $r_max=0, $c_max=0);
		$i = 0; // clue index;
		$str = '';
		for($r=0; $r<=$r_max; $r++) {
			$str .= '<tr id="Row_'.$r.'">';
			for($c=0; $c<=$c_max; $c++) {
				if (empty($row[$r]['cell'][$c]['#'])) {
					$str .= '<td class="BlankCell">&nbsp;</td>';
				} else {
					$aword = $this->get_jcross_aword($row, $r, $r_max, $c, $c_max);
					$dword = $this->get_jcross_dword($row, $r, $r_max, $c, $c_max);
					if (empty($aword) && empty($dword)) {
						$str .= '<td class="LetterOnlyCell"><span id="L_'.$r.'_'.$c.'">&nbsp;</span></td>';
					} else {
						$i++; // increment clue index
						$str .= '<td class="NumLetterCell"><a href="javascript:void(0);" class="GridNum" onclick="ShowClue('.$i.','.$r.','.$c.')">'.$i.'</a><span class="NumLetterCellText" id="L_'.$r.'_'.$c.'" onclick="ShowClue('.$i.','.$r.','.$c.')">&nbsp;&nbsp;&nbsp;</span></td>';
					}
				}
			}
			$str .= '</tr>';
		}
		return $str;
	}
	function v6_get_jcross_grid(&$row, &$r_max, &$c_max) {
		$row = $this->xml_values('data,crossword,grid,row');
		$r_max = 0;
		$c_max = 0;
		if (isset($row) && is_array($row)) {
			for($r=0; $r<count($row); $r++) {
				if (isset($row[$r]['cell']) && is_array($row[$r]['cell'])) {
					for($c=0; $c<count($row[$r]['cell']); $c++) {
						if (!empty($row[$r]['cell'][$c]['#'])) {
							$r_max = max($r, $r_max);
							$c_max = max($c, $c_max);
						}
					} // end for $c
				}
			} // end for $r
		}
	}
	function get_jcross_dword(&$row, $r, $r_max, $c, $c_max) {
		$str = '';
		if (($r==0 || empty($row[$r-1]['cell'][$c]['#'])) && $r<$r_max && !empty($row[$r+1]['cell'][$c]['#'])) {
			$str = $this->get_jcross_word($row, $r, $r_max, $c, $c_max, true);
		}
		return $str;
	}
	function get_jcross_aword(&$row, $r, $r_max, $c, $c_max) {
		$str = '';
		if (($c==0 || empty($row[$r]['cell'][$c-1]['#'])) && $c<$c_max && !empty($row[$r]['cell'][$c+1]['#'])) {
			$str = $this->get_jcross_word($row, $r, $r_max, $c, $c_max, false);
		}
		return $str;
	}
	function get_jcross_word(&$row, $r, $r_max, $c, $c_max, $go_down=false) {
		$str = '';
		while ($r<=$r_max && $c<=$c_max && !empty($row[$r]['cell'][$c]['#'])) {
			$str .= $row[$r]['cell'][$c]['#'];
			if ($go_down) {
				$r++;
			} else {
				$c++;
			}
		}
		return $str;
	}

	// specials (JQuiz)

	function v6_expand_QuestionOutput() {
		$str = '';
		$str .= '<ol class="QuizQuestions" id="Questions">'."\n";

		$q = 0;
		$tags = 'data,questions,question-record';
		while (($question="[$q]['#']") && $this->xml_value($tags, $question)) {

			// get question
			$question_text = $this->xml_value($tags, $question."['question'][0]['#']");
			$question_type = $this->xml_value($tags, $question."['question-type'][0]['#']");

			// check we have a question
			if ($question_text && $question_type) {

				$str .= '<li class="QuizQuestion" id="Q_'.$q.'" style="display: none;">';
				$str .= '<p class="QuestionText">'.$question_text.'</p>';

				if (
					$question_type==HOTPOT_JQUIZ_SHORTANSWER || 
					$question_type==HOTPOT_JQUIZ_HYBRID
				) {
					$str .= '<div class="ShortAnswer" id="Q_'.$q.'_SA"><form method="post" action="" onsubmit="return false;"><div>';
					$str .= '<INPUT type="text" id="Q_'.$q.'_Guess" onfocus="TrackFocus('."'".'Q_'.$q.'_Guess'."'".')" onblur="LeaveGap()" class="ShortAnswerBox" size="9"></input><br /><br />';

					$text = $this->v6_expand_CheckCaption();
					$str .= $this->v6_expand_jquiz_button($text, "CheckShortAnswer($q)");

					if ($this->v6_expand_hint()) {
						$text = $this->v6_expand_HintCaption();
						$str .= $this->v6_expand_jquiz_button($text, "ShowHint($q)");
					}

					if ($this->v6_expand_ShowAnswer()) {
						$text = $this->v6_expand_ShowAnswerCaption();
						$str .= $this->v6_expand_jquiz_button($text, "ShowAnswers($q)");
					}

					$str .= '</div></form></div>';
				}

				if (
					$question_type==HOTPOT_JQUIZ_MULTICHOICE || 
					$question_type==HOTPOT_JQUIZ_HYBRID ||
					$question_type==HOTPOT_JQUIZ_MULTISELECT
				) {
		
					switch ($question_type) {
						case HOTPOT_JQUIZ_MULTICHOICE: 
							$str .= '<ol class="MCAnswers">'."\n";
						break;
						case HOTPOT_JQUIZ_HYBRID:
							$str .= '<ol class="MCAnswers" id="Q_'.$q.'_Hybrid_MC" style="display: none;">'."\n";
						break;	
						case HOTPOT_JQUIZ_MULTISELECT:
							$str .= '<ol class="MSelAnswers">'."\n";
						break;
					}

					$a = 0;
					$aa = 0;
					$answers = $question."['answers'][0]['#']";
					while (($answer = $answers."['answer'][$a]['#']") && $this->xml_value($tags, $answer)) {
						$text = $this->xml_value($tags, $answer."['text'][0]['#']");
						if ($text) {
							switch ($question_type) {
								case HOTPOT_JQUIZ_MULTICHOICE:
								case HOTPOT_JQUIZ_HYBRID:
									$include = $this->int_value($tags, $answer."['include-in-mc-options'][0]['#']");
									if ($include) {
										$str .= '<li id="Q_'.$q.'_'.$aa.'"><button class="FuncButton" onfocus="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseover="FuncBtnOver(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" id="Q_'.$q.'_'.$aa.'_Btn" onclick="CheckMCAnswer('.$q.','.$aa.',this)">&nbsp;&nbsp;?&nbsp;&nbsp;</button>&nbsp;&nbsp;'.$text.'</li>'."\n";
									}
								break;
								case HOTPOT_JQUIZ_MULTISELECT:
									$str .= '<li id="Q_'.$q.'_'.$aa.'"><form method="post" action="" onsubmit="return false;"><div><INPUT type="checkbox" id="Q_'.$q.'_'.$aa.'_Chk" class="MSelCheckbox" />'.$text.'</div></form></li>'."\n";
								break;	
							}
							$aa++;
						}
						$a++;
					}

					$str .= '</ol>';

					if ($question_type==HOTPOT_JQUIZ_MULTISELECT) {
						$text = $this->v6_expand_CheckCaption();
						$str .= $this->v6_expand_jquiz_button($text, "CheckMultiSelAnswer($q)");
					}
				}

				$str .= "</li>\n";
			}
			$q++;

		} // end while $question

		$str .= "</ol>\n";
		return $str;
	}

	function v6_expand_jquiz_button($text, $onclick) {
		return '<button class="FuncButton" onfocus="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseover="FuncBtnOver(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" onclick="'.$onclick.'">'.$text.'</button>';
	}

	// jquiz.js_

	function v6_expand_MultiChoice() {
		return $this->v6_jquiz_question_type(HOTPOT_JQUIZ_MULTICHOICE);
	}
	function v6_expand_ShortAnswer() {
		return $this->v6_jquiz_question_type(HOTPOT_JQUIZ_SHORTANSWER);
	}
	function v6_expand_MultiSelect() {
		return $this->v6_jquiz_question_type(HOTPOT_JQUIZ_MULTISELECT);
	}
	function v6_jquiz_question_type($type) {
		// does this quiz have any questions of the given $type?
		$flag = false;

		$q = 0;
		$tags = 'data,questions,question-record';
		while (($question = "[$q]['#']") && $this->xml_value($tags, $question)) {
			$question_type = $this->xml_value($tags, $question."['question-type'][0]['#']");
			if ($question_type==$type || ($question_type==HOTPOT_JQUIZ_HYBRID && ($type==HOTPOT_JQUIZ_MULTICHOICE || $type==HOTPOT_JQUIZ_SHORTANSWER))) {
				$flag = true;
				break;
			}
			$q++;
		}
		return $flag;
	}
	function v6_expand_CorrectFirstTime() {
		return $this->js_value('hotpot-config-file,global,correct-first-time');
	}
	function v6_expand_ContinuousScoring() {
		return $this->bool_value("hotpot-config-file,$this->quiztype,continuous-scoring");
	}
	function v6_expand_ShowCorrectFirstTime() {
		return $this->bool_value("hotpot-config-file,$this->quiztype,show-correct-first-time");
	}
	function v6_expand_ShuffleAs() {
		return $this->bool_value("hotpot-config-file,$this->quiztype,shuffle-answers");
	}

	function v6_expand_DefaultRight() {
		return $this->v6_expand_GuessCorrect();
	}
	function v6_expand_DefaultWrong() {
		return $this->v6_expand_GuessIncorrect();
	}
	function v6_expand_ShowAllQuestionsCaptionJS() {
		return $this->v6_expand_ShowAllQuestionsCaption();
	}
	function v6_expand_ShowOneByOneCaptionJS() {
		return $this->v6_expand_ShowOneByOneCaption();
	}

	// hp6checkshortanswers.js_ (JQuiz)

	function v6_expand_CorrectList() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,correct-answers");
	}
	function v6_expand_HybridTries() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,short-answer-tries-on-hybrid-q");
	}
	function v6_expand_PleaseEnter() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,enter-a-guess");
	}
	function v6_expand_PartlyIncorrect() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,partly-incorrect");
	}
	function v6_expand_ShowAnswerCaption() {
		return $this->xml_value("hotpot-config-file,$this->quiztype,show-answer-caption");
	}
	function v6_expand_ShowAlsoCorrect() {
		return $this->bool_value('hotpot-config-file,global,show-also-correct');
	}

	// insert forms and messages

	function insert_script($src='') {
		if (empty($src)) {
			$src = HOTPOT_JS;
		}
		$url = '<SCRIPT src="'.$src.'" type="text/javascript" language="javascript"></SCRIPT>'."\n";
		$this->html = preg_replace('|</head>|', $url.'</head>', $this->html, 1);
	}
	function insert_submission_form($attemptid) {
		$form_name = 'store';
		$form_fields = '';
		$form_fields .= '<INPUT type="hidden" name="attemptid" value="'.$attemptid.'">'."\n";
		$form_fields .= '<INPUT type="hidden" name="starttime" value="">'."\n";
		$form_fields .= '<INPUT type="hidden" name="endtime" value="">'."\n";
		$form_fields .= '<INPUT type="hidden" name="mark" value="">'."\n";
		$form_fields .= '<INPUT type="hidden" name="detail" value="">'."\n";
		$this->insert_form(
			'<!-- BeginSubmissionForm -->', '<!-- EndSubmissionForm -->', $form_name, $form_fields
		);
	}
	function insert_giveup_form($attemptid) {
		$form_name = 'giveup';
		$form_fields = '';
		$form_fields .= '<INPUT type="hidden" name="attemptid" value="'.$attemptid.'">'."\n";
		$form_fields .= '<INPUT type="submit" value="'.get_string('giveup', 'hotpot').'" class="FuncButton" onfocus="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseover="FuncBtnOver(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)">';
		$this->insert_form(
			'<!-- BeginTopNavButtons -->', '<!-- EndTopNavButtons -->', $form_name, $form_fields, true
		);
	}
	function remove_nav_buttons() {
		$this->insert_form(
			'<!-- BeginTopNavButtons -->', '<!-- EndTopNavButtons -->'
		);
		$this->insert_form(
			'<!-- BeginBottomNavButtons -->', '<!-- EndBottomNavButtons -->'
		);
	}
	function insert_form($startblock, $endblock, $form_name=NULL, $form_fields='', $center=false) {
		global $CFG;
		$form = '';
		if (isset($form_name)) {
			$form .= '<FORM action="'.$CFG->wwwroot.'/mod/hotpot/attempt.php" method="POST" name="'.$form_name.'" target="'.$CFG->framename.'">'."\n";
			$form .= $form_fields;
			$form .= '</FORM>'."\n";
			if ($center) {
				$form = '<DIV style="margin-left: auto; margin-right: auto; text-align: center">'."\n".$form.'</DIV>'."\n";
			}
		}
		if (
			is_int($start = strpos($this->html, $startblock)) &&
			is_int($end = strpos($this->html, $endblock, $start))
		) {
			$this->html = substr($this->html, 0, $start).$startblock.$form.substr($this->html, $end);
		}
	}
	function insert_message($start_str, $message, $color="red", $align="center") {
		$message = '<p align="'.$align.'" style="color:'.$color.';text-align:'.$align.';"><b>'.$message."</b></p>\n";
		if (is_int($start = strpos($this->html, $start_str))) {
			$start += strlen($start_str);
			$this->html = substr($this->html, 0, $start).$message.substr($this->html, $start);
		}
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



	function hotpot_convert_relative_urls(&$str) {
		$tagopen = '(?:(<)|(&lt;)|(&amp;#x003C;))'; // left angle bracket
		$tagclose = '(?(2)>|(?(3)&gt;|(?(4)&amp;#x003E;)))'; //  right angle bracket (to match left angle bracket)

		$space = '\s+'; // at least one space
 		$anychar = '(?:.*?)'; // any character

		$quoteopen = '("|&quot;|&amp;quot;)'; // open quote
		$quoteclose = '\\5'; //  close quote (to match open quote)

		$url = '\S+?\.\S+?';
		$replace = "hotpot_convert_relative_url('".$this->get_baseurl()."', '".$this->reference."', '\\1', '\\6', '\\7')";

		$tags = array('a'=>'href','img'=>'src','param'=>'value');
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
} // end class

function hotpot_convert_relative_url($baseurl, $reference, $opentag, $url, $closetag) {

	// try and parse the $url into $matches
	//	[1] path
	//	[2] query string, if any
	//	[3] anchor fragment, if any
	if (preg_match('|^([^?]*)((?:\\?[^#]*)?)((?:#.*)?)$|', $url, $matches)) {
		$url = $matches[1];
		$query = $matches[2];
		$fragment = $matches[3];
	} else {
		$query = '';
		$fragment = '';
	}

	$url = hotpot_convert_url($baseurl, $reference, $url);

	// try and parse the query string arguments into $matches
	//	[1] names
	//	[2] values
	if ($query && preg_match_all('|([^=]+)=([^&]*)|', substr($query, 1), $matches)) {

		$query = array();

		// the values of the following arguments are considered to be URLs
		$url_names = array('src');

		// loop through the arguments in the query string
		$i_max = count($matches[0]);
		for ($i=0; $i<$i_max; $i++) {
		
			$name = $matches[1][$i];
			$value = $matches[2][$i];

			// convert $value if it is a URL
			if (in_array(strtolower($name), $url_names)) {
				$value = hotpot_convert_url($baseurl, $reference, $value);
			}

			$query[] = "$name=$value";
		}

		$query = '?'.implode('&', $query);
	}

	// remove the slashes that were added by the "e" modifier of preg_replace
	$url = stripslashes("$opentag$url$query$fragment$closetag");

	return $url;
}

function hotpot_convert_url($baseurl, $reference, $url) {
	// exclude absolute urls
	if (!preg_match('%^(http://|/)%i', $url)) {

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
	}
	return $url;
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
<SCRIPT>
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
</SCRIPT>
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
function hotpot_sort_keypad_chars($a, $b) {
	$a =  hotpot_keypad_sort_value($a);
	$b =  hotpot_keypad_sort_value($b);
	return ($a<$b) ? -1 : ($a==$b ? 0 : 1);
}
function hotpot_keypad_sort_value($char) {

	// hexadecimal
	if (preg_match('|&#x([0-9A-F]+);|ie', $char, $matches)) {
		$ord = hexdec($matches[1]);

	// decimal
	} else if (preg_match('|&#(\d+);|i', $char, $matches)) {
		$ord = intval($matches[1]);

	// other html entity
	} else if (preg_match('|&[^;]+;|', $char, $matches)) {
   		$char = html_entity_decode($matches[0]);
		$ord = empty($char) ? 0 : ord($char);

	// not an html entity
	} else {
		$char = trim($char);
		$ord = empty($char) ? 0 : ord($char);
	}

	// lowercase letters (plain or accented)
	if (($ord>=97 && $ord<=122) || ($ord>=224 && $ord<=255)) {
		$sort_value = ($ord-31).'.'.sprintf('%04d', $ord);

	// all other characters
	} else {
		$sort_value = $ord;
	}

	return $sort_value;
} 


// ===================================================
// function for adding attempt questions and responses
// ===================================================

function hotpot_add_attempt_details(&$attempt) {

	// if you have trouble with recursively encoded ampersands, you can correct them with this:
	//$attempt->details = preg_replace('|&(amp;)+#|i', '&#', $attempt->details);
	
	// encode the ampersands, so that HTML entities are preserved in the XML parser
	$details = str_replace('&', '&amp;', $attempt->details);

	// parse the attempt details as xml
	$details = new hotpot_xml_tree($details, "['hpjsresult']['#']");

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

	// 
}
function hotpot_add_response(&$attempt, &$question, &$response) {

	global $db, $next_url;

	if (isset($question) && isset($question->name)) {
		if (!$question->id = get_field('hotpot_questions', 'id', 'name', $question->name, 'hotpot', $attempt->hotpot)) {
			if(!$question->id = insert_record('hotpot_questions', $question)) {
				error("Could not add question record for attempt '$attempt->id': ".$db->ErrorMsg(), $next_url);
			}
		}
		if (isset($response)) {
			$response->question = $question->id;
			if(!$response->id = insert_record('hotpot_responses', $response)) {
				error("Could not add response record for attempt '$attempt->id': ".$db->ErrorMsg(), $next_url);
			}
		}
	}
}
function hotpot_adjust_response_field($quiztype, &$question, &$num, &$name, &$data) {
	switch ($quiztype) {
		case 'jcb':
			$question->type = HOTPOT_JCB;
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
					$data = ($data=='HOTPOT_YES' ? 1 : 0);
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

if (!function_exists('html_entity_decode')) {
	// add this function for php version<4.3
	function html_entity_decode($str) {
		$t = get_html_translation_table(HTML_ENTITIES);
		$t = array_flip($t);
		return strtr($str, $t);
	}

}

if (!function_exists('required_param')) {
	// add this function for Moodle version<1.4.2
	function required_param($varname, $options="") {
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
	function optional_param($varname, $default=NULL, $options="") {
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

?>
