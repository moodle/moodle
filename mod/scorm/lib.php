<?PHP  // $Id$

/// Library of functions and constants for module scorm
/// (replace scorm with the name of your module and delete this line)

$SCORM_WINDOW_OPTIONS = array("resizable", "scrollbars", "status", "height", "width");

if (!isset($CFG->scorm_popup)) {
    set_config("scorm_popup", "");
}  

foreach ($SCORM_WINDOW_OPTIONS as $popupoption) {
    $popupoption = "scorm_popup$popupoption";
    if (!isset($CFG->$popupoption)) {
        if ($popupoption == "scorm_popupheight") {
            set_config($popupoption, 450);
        } else if ($popupoption == "scorm_popupwidth") {
            set_config($popupoption, 620);
        } else {
            set_config($popupoption, "checked");
        }
    }  
}

if (!isset($CFG->scorm_framesize)) {
    set_config("scorm_framesize", 140);
}

function scorm_add_instance($scorm) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $scorm->timemodified = time();

    # May have to add extra stuff in here #
    global $SCORM_WINDOW_OPTIONS;
    
    $scorm->popup = "";
    
    $optionlist = array();
    foreach ($SCORM_WINDOW_OPTIONS as $option) {
        if (isset($scorm->$option)) {
            $optionlist[] = $option."=".$scorm->$option;
        }
    }
    $scorm->popup = implode(',', $optionlist);
    

    if ($scorm->popup != "") {
    	$scorm->popup .= ',location=0,menubar=0,toolbar=0';
    	$scorm->auto = '0';
    }
    
    return insert_record("scorm", $scorm);
}


function scorm_update_instance($scorm) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.
    
    $scorm->timemodified = time();
    $scorm->id = $scorm->instance;

    # May have to add extra stuff in here #
    global $SCORM_WINDOW_OPTIONS;
    
    $scorm->popup = "";
    
    $optionlist = array();
    foreach ($SCORM_WINDOW_OPTIONS as $option) {
        if (isset($scorm->$option)) {
            $optionlist[] = $option."=".$scorm->$option;
        }
    }
    $scorm->popup = implode(',', $optionlist);

    if ($scorm->popup != "") {
    	$scorm->popup .= ',location=0,menubar=0,toolbar=0';
    	$scorm->auto = '0';
    }
    return update_record("scorm", $scorm);
}


function scorm_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  
    
    require('../config.php');

    if (! $scorm = get_record("scorm", "id", "$id")) {
        return false;
    }

    $result = true;

    # Delete any dependent files #
    scorm_delete_files($CFG->dataroot."/".$scorm->course."/moddata/scorm".$scorm->datadir);

    # Delete any dependent records here #
    if (! delete_records("scorm_sco_users", "scormid", "$scorm->id")) {
        $result = false;
    }
    if (! delete_records("scorm_scoes", "scorm", "$scorm->id")) {
        $result = false;
    }
    if (! delete_records("scorm", "id", "$scorm->id")) {
        $result = false;
    }
    

    return $result;
}

function scorm_user_outline($course, $user, $mod, $scorm) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    return $return;
}

function scorm_user_complete($course, $user, $mod, $scorm) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.

    return true;
}

function scorm_print_recent_activity(&$logs, $isteacher=false) {
/// Given a list of logs, assumed to be those since the last login 
/// this function prints a short list of changes related to this module
/// If isteacher is true then perhaps additional information is printed.
/// This function is called from course/lib.php: print_recent_activity()

    global $CFG, $COURSE_TEACHER_COLOR;

    $content = NULL;

    return $content;  // True if anything was printed, otherwise false
}

function scorm_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

    global $CFG;

    return true;
}

function scorm_grades($scormid) {
/// Must return an array of grades for a given instance of this module, 
/// indexed by user.  It also returns a maximum allowed grade.

    global $CFG;
    
    if (!$return->maxgrade = count_records_select("scorm_scoes","scorm='$scormid' AND launch<>''")) {
        return NULL;
    }
    
    $return->grades = NULL;
    if ($sco_users=get_records_select("scorm_sco_users", "scormid='$scormid' GROUP BY userid")) {
        foreach ($sco_users as $sco_user) {
            $user_data=get_records_select("scorm_sco_users","scormid='$scormid' AND userid='$sco_user->userid'");
            $scores->completed=0;
            $scores->browsed=0;
            $scores->incomplete=0;
            $scores->failed=0;
            $scores->notattempted=0;
            $result="";
            $data = current($user_data);
            foreach ($user_data as $data) {
                if ($data->cmi_core_lesson_status=="passed")
                    $scores->completed++;
                else
                    $scores->{scorm_remove_spaces($data->cmi_core_lesson_status)}++;
                
            }
            if ($scores->completed)
                $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/completed.gif\" alt=\"".get_string("completed","scorm")."\" title=\"".get_string("completed","scorm")."\"> $scores->completed ";
            if ($scores->incomplete)
                $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/incomplete.gif\" alt=\"".get_string("incomplete","scorm")."\" title=\"".get_string("incomplete","scorm")."\"> $scores->incomplete ";
            if ($scores->failed)
                $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/failed.gif\" alt=\"".get_string("failed","scorm")."\" title=\"".get_string("failed","scorm")."\"> $scores->failed ";
            if ($scores->browsed)
                $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/browsed.gif\" alt=\"".get_string("browsed","scorm")."\" title=\"".get_string("browsed","scorm")."\"> $scores->browsed ";
            if ($scores->notattempted)
                $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/notattempted.gif\" alt=\"".get_string("notattempted","scorm")."\" title=\"".get_string("notattempted","scorm")."\"> $scores->notattempted ";
            
            $return->grades[$sco_user->userid]=$result;
        }
        
    }

    return $return;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other scorm functions go here.  Each of them must have a name that 
/// starts with scorm_


function scorm_randstring($len = "8")
{
        $rstring = NULL;
        for($i=0; $i<$len; $i++) {
                $char = chr(rand(48,122));
                while (!ereg("[a-zA-Z0-9]", $char)){
                        if($char == $lchar) continue;
                        $char = chr(rand(48,90));
                }
                $rstring .= $char;
                $lchar = $char;
        }
        return $rstring;
}

 
function scorm_datadir($strPath, $existingdir="", $prefix = "SCORM")
{
    global $CFG;

    if (($existingdir!="") && (is_dir($strPath.$existingdir)))
        return $strPath.$existingdir;
        
    if (is_dir($strPath)) {
        do {
            $datadir="/".$prefix.scorm_randstring();
        } while (file_exists($strPath.$datadir));
        mkdir($strPath.$datadir, $CFG->directorypermissions);
        @chmod($strPath.$datadir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        return $strPath.$datadir;
    } else {
        return false;
    }
} 

function scorm_validate($manifest)
{
    if (is_file ($manifest)) {
    if (file_exists($manifest))
    {
        return "regular";
    }
    } else {
        return "nomanifest";
    }
}

function scorm_delete_files($directory)
{
    if (is_dir($directory))
    {
        $handle=opendir($directory);
        while (($file = readdir($handle)) != '')
        {
            if ($file != "." && $file != "..")
        {
            if (!is_dir($directory."/".$file))
                    unlink($directory."/".$file);
            else
            scorm_delete_files($directory."/".$file);
        }
        }
        rmdir($directory);
    }
}

function scorm_startElement($parser, $name, $attrs) {
    global $manifest,$i,$resources,$parent,$level;
    if ($name == "ITEM") {
        $i++;
        $manifest[$i]["identifier"] = $attrs["IDENTIFIER"];
        if (empty($attrs["IDENTIFIERREF"]))
            $attrs["IDENTIFIERREF"] = "";
        $manifest[$i]["identifierref"] = $attrs["IDENTIFIERREF"];
        if (empty($attrs["ISVISIBLE"]))
            $attrs["ISVISIBLE"] = "";
        $manifest[$i]["isvisible"] = $attrs["ISVISIBLE"];
        $manifest[$i]["parent"] = $parent[$level];
        $level++;
        $parent[$level] = $attrs["IDENTIFIER"];
    }
    if ($name == "RESOURCE") {
        $resources[$attrs["IDENTIFIER"]]["href"]=$attrs["HREF"];
        $resources[$attrs["IDENTIFIER"]]["type"]=$attrs["ADLCP:SCORMTYPE"];
    }
}

function scorm_endElement($parser, $name) {
    global $manifest,$i,$level,$datacontent,$navigation;
    if ($name == "ITEM") {
        $level--;
    }
    if ($name == "TITLE" && $level>0) {
    	$manifest[$i]["title"] = $datacontent;
    }
    if ($name == "ADLCP:HIDERTSUI") {
    	$manifest[$i][$datacontent] = 1;
    }
    if ($name == "ORGANIZATION") {
    	$level = 0;
    }
}

function scorm_characterData($parser, $data) {
    global $datacontent;
    $datacontent = $data;
}

function scorm_parse($basedir,$file,$scorm_id) {
    global $manifest,$i,$resources,$parent,$level;
    $datacontent = "";
    $manifest[][] = "";
    $resources[] = "";
    $i = 0;
    $level = 0;
    $parent[$level] = "/";

    $xml_parser = xml_parser_create();
    // use case-folding so we are sure to find the tag in $map_array
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
    xml_set_element_handler($xml_parser, "scorm_startElement", "scorm_endElement");
    xml_set_character_data_handler($xml_parser, "scorm_characterData");
    if (!($fp = fopen($basedir.$file, "r"))) {
       die("could not open XML input");
    }

    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
            die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
        }
    }
    xml_parser_free($xml_parser);
    $launch = 0;

    $sco->scorm = $scorm_id;
    delete_records("scorm_scoes","scorm",$scorm_id);
    delete_records("scorm_sco_users","scormid",$scorm_id);
    
    for ($j=1; $j<=$i; $j++) {
        $sco->identifier = $manifest[$j]["identifier"];
        $sco->parent = $manifest[$j]["parent"];
        $sco->title = $manifest[$j]["title"];
        if (!isset($manifest[$j]["datafromlms"])) {
            $manifest[$j]["datafromlms"] = "";
        } 
        $sco->datafromlms = $manifest[$j]["datafromlms"];
        
        if (!isset($resources[($manifest[$j]["identifierref"])]["href"])) {
            $resources[($manifest[$j]["identifierref"])]["href"] = "";
        }
        $sco->launch = $resources[($manifest[$j]["identifierref"])]["href"];
        
        if (!isset($resources[($manifest[$j]["identifierref"])]["type"])) {
            $resources[($manifest[$j]["identifierref"])]["type"] = "";
        }
    	$sco->type = $resources[($manifest[$j]["identifierref"])]["type"];
    	
    	if (!isset($manifest[$j]["previous"])) {
            $manifest[$j]["previous"] = 0;
        }
    	$sco->previous = $manifest[$j]["previous"];
    	
    	if (!isset($manifest[$j]["continue"])) {
            $manifest[$j]["continue"] = 0;
        }
    	$sco->next = $manifest[$j]["continue"];
    	
    	if (scorm_remove_spaces($manifest[$j]["isvisible"]) != "false") {
            $id = insert_record("scorm_scoes",$sco);
        }
        
    	if ($launch==0 && $sco->launch) {
            $launch = $id;
        }
    }
    return $launch;
}

function scorm_get_scoes_records($sco_user) {
/// Gets all info required to display the table of scorm results
/// for report.php
    global $CFG;

    return get_records_sql("SELECT su.*, u.firstname, u.lastname, u.picture 
                            FROM {$CFG->prefix}scorm_sco_users su, 
                                 {$CFG->prefix}user u
                            WHERE su.scormid = '$sco_user->scormid'
                              AND su.userid = u.id
                              AND su.userid = $sco_user->userid
                              ORDER BY scoid");
}

function scorm_remove_spaces($sourcestr) {
// Remove blank space from a string
    $newstr="";
    for( $i=0; $i<strlen($sourcestr); $i++) {
        if ($sourcestr[$i]!=' ')
            $newstr .=$sourcestr[$i];
    }
    return $newstr;
}

function scorm_string_round($stringa) {
// Crop a string to $len character and set an anchor title to the full string
    $len=11;
    if ( strlen($stringa)>$len ) {
    return "<A name=\"\" title=\"$stringa\">".substr($stringa,0,$len-4)."...".substr($stringa,strlen($stringa)-1,1)."</A>";
    } else
    return $stringa;
}

function scorm_external_link($link) {
// check if a link is external
    $result = false;
    $link = strtolower($link);
    if (substr($link,0,7) == "http://")
        $result = true;
    else if (substr($link,0,8) == "https://")
        $result = true;
    else if (substr($link,0,4) == "www.")
        $result = true;
    /*else if (substr($link,0,7) == "rstp://")
        $result = true;
    else if (substr($link,0,6) == "rtp://")
        $result = true;
    else if (substr($link,0,6) == "ftp://")
        $result = true;
    else if (substr($link,0,9) == "gopher://")
        $result = true; */
    return $result;
}    
?>
