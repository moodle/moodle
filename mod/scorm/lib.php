<?php  // $Id$

/// Library of functions and constants for module scorm
/// (replace scorm with the name of your module and delete this line)

define('VALUESCOES', '0');
define('VALUEHIGHEST', '1');
define('VALUEAVERAGE', '2');
define('VALUESUM', '3');
$SCORM_GRADE_METHOD = array (VALUESCOES => get_string("gradescoes", "scorm"), 
			     VALUEHIGHEST => get_string("gradehighest", "scorm"),
                             VALUEAVERAGE => get_string("gradeaverage", "scorm"),
                             VALUESUM => get_string("gradesum", "scorm"));
                             
$SCORM_WINDOW_OPTIONS = array('resizable', 'scrollbars', 'status', 'height', 'width');

if (!isset($CFG->scorm_popup)) {
    set_config('scorm_popup', '');
}
if (!isset($CFG->scorm_validate)) {
    $scorm_validate = 'none';
    if (extension_loaded('domxml')) {
        $scorm_validate = 'domxml';
    }
    if (version_compare(phpversion(),'5.0.0','>=')) {
        $scorm_validate = 'php5';
    }
    set_config('scorm_validate', $scorm_validate);
}

foreach ($SCORM_WINDOW_OPTIONS as $popupoption) {
    $popupoption = "scorm_popup$popupoption";
    if (!isset($CFG->$popupoption)) {
        if ($popupoption == 'scorm_popupheight') {
            set_config($popupoption, 450);
        } else if ($popupoption == 'scorm_popupwidth') {
            set_config($popupoption, 620);
        } else {
            set_config($popupoption, 'checked');
        }
    }  
}

if (!isset($CFG->scorm_framesize)) {
    set_config('scorm_framesize', 140);
}

function scorm_add_instance($scorm) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $scorm->timemodified = time();

    # May have to add extra stuff in here #
    global $SCORM_WINDOW_OPTIONS;
    
    $scorm->popup = '';
    
    $optionlist = array();
    foreach ($SCORM_WINDOW_OPTIONS as $option) {
        if (isset($scorm->$option)) {
            $optionlist[] = $option.'='.$scorm->$option;
        }
    }
    $scorm->popup = implode(',', $optionlist);
    

    if ($scorm->popup != '') {
    	$scorm->popup .= ',location=0,menubar=0,toolbar=0';
    	$scorm->auto = '0';
    }
    
    return insert_record('scorm', $scorm);
}


function scorm_update_instance($scorm) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.
    
    $scorm->timemodified = time();
    $scorm->id = $scorm->instance;

    # May have to add extra stuff in here #
    global $SCORM_WINDOW_OPTIONS;
    
    $scorm->popup = '';
    
    $optionlist = array();
    foreach ($SCORM_WINDOW_OPTIONS as $option) {
        if (isset($scorm->$option)) {
            $optionlist[] = $option.'='.$scorm->$option;
        }
    }
    $scorm->popup = implode(',', $optionlist);

    if ($scorm->popup != '') {
    	$scorm->popup .= ',location=0,menubar=0,toolbar=0';
    	$scorm->auto = '0';
    }
    return update_record('scorm', $scorm);
}


function scorm_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  
    
    require('../config.php');

    if (! $scorm = get_record('scorm', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent files #
    scorm_delete_files($CFG->dataroot.'/'.$scorm->course.'/moddata/scorm'.$scorm->datadir);

    # Delete any dependent records here #
    if (! delete_records('scorm_sco_users', 'scormid', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm_scoes', 'scorm', $scorm->id)) {
        $result = false;
    }
    if (! delete_records('scorm', 'id', $scorm->id)) {
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
    
    if (!$scorm = get_record("scorm", "id", $scormid)) {
        return NULL;
    }
    
    if ($scorm->grademethod == VALUESCOES) {
    	if (!$return->maxgrade = count_records_select('scorm_scoes',"scorm='$scormid' AND launch<>''")) {
            return NULL;
    	}
    
    	$return->grades = NULL;
    	if ($sco_users=get_records_select('scorm_sco_users', "scormid='$scormid' GROUP BY userid")) {
            foreach ($sco_users as $sco_user) {
        	$user_data=get_records_select('scorm_sco_users',"scormid='$scormid' AND userid='$sco_user->userid'");
            	$scores->completed=0;
            	$scores->browsed=0;
            	$scores->incomplete=0;
            	$scores->failed=0;
            	$scores->notattempted=0;
            	$result='';
            	$data = current($user_data);
            	foreach ($user_data as $data) {
                    if ($data->cmi_core_lesson_status=='passed')
                    	$scores->completed++;
                    else
                    	$scores->{scorm_remove_spaces($data->cmi_core_lesson_status)}++;
            	}
            	if ($scores->completed)
                    $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/completed.gif\" alt=\"".get_string('completed','scorm')."\" title=\"".get_string('completed','scorm')."\" /> $scores->completed ";
            	if ($scores->incomplete)
                    $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/incomplete.gif\" alt=\"".get_string('incomplete','scorm')."\" title=\"".get_string('incomplete','scorm')."\" /> $scores->incomplete ";
            	if ($scores->failed)
                    $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/failed.gif\" alt=\"".get_string('failed','scorm')."\" title=\"".get_string('failed','scorm')."\" /> $scores->failed ";
            	if ($scores->browsed)
                    $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/browsed.gif\" alt=\"".get_string('browsed','scorm')."\" title=\"".get_string('browsed','scorm')."\" /> $scores->browsed ";
            	if ($scores->notattempted)
                    $result.="<img src=\"$CFG->wwwroot/mod/scorm/pix/notattempted.gif\" alt=\"".get_string('notattempted','scorm')."\" title=\"".get_string('notattempted','scorm')."\" /> $scores->notattempted ";
            
            	$return->grades[$sco_user->userid]=$result;
            }
        
   	}
    } else {
        $grades = get_records_select("scorm_sco_users", "scormid=$scormid AND cmi_core_score_raw>0","","id,userid,cmi_core_score_raw");
        //$grades = get_records_menu("scorm_sco_users", "scormid",$scormid,"","userid,cmi_core_score_raw");
        $valutations = array();
        foreach ($grades as $grade) {
            if (!isset($valutations[$grade->userid])) {
            	if ($scorm->grademethod == VALUEAVERAGE) {
            	    $values = array();
            	    $values[$grade->userid]->grade = 0;
            	    $values[$grade->userid]->values = 0;
            	}
            	$valutations[$grade->userid] = 0;
            }
            switch ($scorm->grademethod) {
            	case VALUEHIGHEST:
            	    if ($grade->cmi_core_score_raw > $valutations[$grade->userid]) {
            	    	$valutations[$grade->userid] = $grade->cmi_core_score_raw;
            	    }
            	break;
            	case VALUEAVERAGE:
            	    $values[$grade->userid]->grade += $grade->cmi_core_score_raw;
            	    $values[$grade->userid]->values++;
            	break;
            	case VALUESUM:
            	    $valutations[$grade->userid] += $grade->cmi_core_score_raw;
            	break;
            }
        }
        if ($scorm->grademethod == VALUEAVERAGE) {
            foreach($values as $userid => $value) {
            	$valutations[$userid] = $value->grade/$value->values;
            }
        }
	//print_r($grades);
	$return->grades = $valutations;
	$return->maxgrade = $scorm->maxgrade;
    }
    return $return;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other scorm functions go here.  Each of them must have a name that 
/// starts with scorm_


function scorm_randstring($len = '8')
{
        $rstring = NULL;
        $lchar = '';
        for($i=0; $i<$len; $i++) {
                $char = chr(rand(48,122));
                while (!ereg('[a-zA-Z0-9]', $char)){
                        if($char == $lchar) continue;
                        $char = chr(rand(48,90));
                }
                $rstring .= $char;
                $lchar = $char;
        }
        return $rstring;
}

 
function scorm_datadir($strPath, $existingdir='', $prefix = 'SCORM')
{
    global $CFG;

    if (($existingdir!='') && (is_dir($strPath.$existingdir)))
        return $strPath.$existingdir;
        
    if (is_dir($strPath)) {
        do {
            $datadir='/'.$prefix.scorm_randstring();
        } while (file_exists($strPath.$datadir));
        mkdir($strPath.$datadir, $CFG->directorypermissions);
        @chmod($strPath.$datadir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        return $strPath.$datadir;
    } else {
        return false;
    }
} 

if ($CFG->scorm_validate == 'domxml') {
    require_once('validatordomxml.php');
}

function scorm_validate($manifest)
{
    global $CFG;
    
    global $item_idref_array;
    global $idres_array;
    global $def_org_array;
    global $id_org_array;
    
    if (is_file ($manifest)) {
	if (file_exists($manifest)) {
	    if ($CFG->scorm_validate == 'domxml') {
    	    	$manifest_string = file_get_contents($manifest);

	    	/* Elimino i caratteri speciali di spaziatura e ritorno a capo dal file xml */

	    	$spec = array('\n', '\r', '\t', '\0', '\x0B');
	    	$content = str_replace($spec, '', $manifest_string);

	    	if ($xmldoc = domxml_open_mem($content)) {
		    $root = $xmldoc->document_element();
		    if (!testRoot($root)) {
		        return 'syntax';
	    	    }
		    if (testNode($root)) {	
		        // Nel corpo di questo if si controllano le corrispondenze fra gli attributi
		        // Nello Standard SCORM ad ogni attributo idRef di <item> deve corrispondere
                        // un attributo ID di <resource>
		        // Gli array degli attributi sono stati dichiarati globali in validator.php
		        // pertanto possono essere utilizzati direttamente all'interno di main.php

		        foreach($item_idref_array as $elem_it) {  
		    	    if (array_search($elem_it, $idres_array) === false) {
				return 'mismatch';
		    	    }
		    	}
  
                    	foreach($def_org_array as $elem_def) {  
		    	    if (array_search($elem_it, $id_org_array) === false) {
				return 'mismatch';
		   	    }
		    	}
                   
		    } else {
		    	return 'badmanifest';
		    }
	    	}
	    	return 'regular';
	    } else {
	        return 'found';
            }
    	}
    } else {
        return 'nomanifest';
    }
}

function scorm_delete_files($directory)
{
    if (is_dir($directory))
    {
        $handle=opendir($directory);
        while (($file = readdir($handle)) != '')
        {
            if ($file != '.' && $file != '..')
            {
            	if (!is_dir($directory.'/'.$file)) {
            	    //chmod($directory.'/'.$file,0777);
                    unlink($directory.'/'.$file);
            	} else {
            	    scorm_delete_files($directory.'/'.$file);
            	}
            }
        }
        rmdir($directory);
    }
}

function scorm_startElement($parser, $name, $attrs) {

    global $scoes,$i,$resources,$parent,$level,$organization,$manifest,$defaultorg;

    if ($name == 'ITEM') {
        $i++;
        $scoes[$i]['manifest'] = $manifest;
        $scoes[$i]['organization'] = $organization;
        $scoes[$i]['identifier'] = $attrs['IDENTIFIER'];
        if (empty($attrs['IDENTIFIERREF']))
            $attrs['IDENTIFIERREF'] = '';
        $scoes[$i]['identifierref'] = $attrs['IDENTIFIERREF'];
        if (empty($attrs['ISVISIBLE']))
            $attrs['ISVISIBLE'] = '';
        $scoes[$i]['isvisible'] = $attrs['ISVISIBLE'];
        $scoes[$i]['parent'] = $parent[$level];
        $level++;
        $parent[$level] = $attrs['IDENTIFIER'];
    }
    if ($name == 'RESOURCE') {
        if (!isset($attrs['HREF'])) {
            $attrs['HREF'] = '';
        }
        $resources[$attrs['IDENTIFIER']]['href']=$attrs['HREF'];
        if (!isset($attrs['ADLCP:SCORMTYPE'])) {
            $attrs['ADLCP:SCORMTYPE'] = '';
        }
        $resources[$attrs['IDENTIFIER']]['type']=$attrs['ADLCP:SCORMTYPE'];
    }
    if ($name == 'ORGANIZATION') {
        $i++;
        $scoes[$i]['manifest'] = $manifest;
        $scoes[$i]['organization'] = '';
        $scoes[$i]['identifier'] = $attrs['IDENTIFIER'];
        $scoes[$i]['identifierref'] = '';
        $scoes[$i]['isvisible'] = '';
        $scoes[$i]['parent'] = $parent[$level];
        $level++;
        $parent[$level] = $attrs['IDENTIFIER'];
    	$organization = $attrs['IDENTIFIER'];
    }
    if ($name == 'MANIFEST') {
    	$manifest = $attrs['IDENTIFIER'];
    }
    if ($name == 'ORGANIZATIONS') {
    	if (!isset($attrs['DEFAULT'])) {
    	    $attrs['DEFAULT'] = '';
    	}
    	$defaultorg = $attrs['DEFAULT'];
    }
}

function scorm_endElement($parser, $name) {
    global $scoes,$i,$level,$datacontent,$navigation;
    if ($name == 'ITEM') {
        $level--;
    }
    //if ($name == 'TITLE' && $level>0) {
    if ($name == 'TITLE') {
        $scoes[$i]['title'] = $datacontent;
    }
    if ($name == 'ADLCP:HIDERTSUI') {
        $scoes[$i][$datacontent] = 1;
    }
    if ($name == 'ADLCP:DATAFROMLMS') {
    	$scoes[$i]['datafromlms'] = $datacontent;
    }
    if ($name == 'ORGANIZATION') {
    	$organization = '';
	$level--;
    }
    if ($name == 'MANIFEST') {
    	$manifest = '';
    }
}

function scorm_characterData($parser, $data) {
    global $datacontent;
    $datacontent = $data;
}

function scorm_parse($basedir,$file,$scorm_id) {
    global $scoes,$i,$resources,$parent,$level,$defaultorg;
    $datacontent = '';
    $scoes[][] = '';
    $resources[] = '';
    $organization = '';
    $defaultorg = '';
    $i = 0;
    $level = 0;
    $parent[$level] = '/';

    $xml_parser = xml_parser_create('UTF-8');
    // use case-folding so we are sure to find the tag in $map_array
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
    xml_set_element_handler($xml_parser, 'scorm_startElement', 'scorm_endElement');
    xml_set_character_data_handler($xml_parser, 'scorm_characterData');
    if (!($fp = fopen($basedir.$file, 'r'))) {
       die('could not open XML input');
    }

    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
            die(sprintf('XML error: %s at line %d',
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
        }
    }
    xml_parser_free($xml_parser);
    $launch = 0;

    $sco->scorm = $scorm_id;
    delete_records('scorm_scoes','scorm',$scorm_id);
    delete_records('scorm_sco_users','scormid',$scorm_id);
    
    if (isset($scoes[1])) {
    	for ($j=1; $j<=$i; $j++) {
            $sco->identifier = $scoes[$j]['identifier'];
            $sco->parent = $scoes[$j]['parent'];
            $sco->title = $scoes[$j]['title'];
            $sco->organization = $scoes[$j]['organization'];
            if (!isset($scoes[$j]['datafromlms'])) {
        	$scoes[$j]['datafromlms'] = '';
            } 
            $sco->datafromlms = $scoes[$j]['datafromlms'];
        
            if (!isset($resources[($scoes[$j]['identifierref'])]['href'])) {
        	$resources[($scoes[$j]['identifierref'])]['href'] = '';
            }
            $sco->launch = $resources[($scoes[$j]['identifierref'])]['href'];
        
            if (!isset($resources[($scoes[$j]['identifierref'])]['type'])) {
        	$resources[($scoes[$j]['identifierref'])]['type'] = '';
            }
    	    $sco->type = $resources[($scoes[$j]['identifierref'])]['type'];
    	
    	    if (!isset($scoes[$j]['previous'])) {
        	$scoes[$j]['previous'] = 0;
            }
    	    $sco->previous = $scoes[$j]['previous'];
    	
    	    if (!isset($scoes[$j]['continue'])) {
        	$scoes[$j]['continue'] = 0;
            }
    	    $sco->next = $scoes[$j]['continue'];
    	
    	    if (scorm_remove_spaces($scoes[$j]['isvisible']) != 'false') {
        	$id = insert_record('scorm_scoes',$sco);
            }
    	    //if (($launch==0) && (isset($sco->launch)) && ($defaultorg==$sco->organization)) {
    	    if (($launch==0) && ($defaultorg==$sco->identifier)) {
        	$launch = $id;
            }
	}
    } else {
    	foreach ($resources as $label => $resource) {
    	    if (!empty($resource['href'])) {
    	    	$sco->identifier = $label;
    	    	$sco->title = $label;
    	    	$sco->parent = '/';
    	    	$sco->launch = $resource['href'];
    	    	$sco->type = $resource['type'];
    	    	$id = insert_record('scorm_scoes',$sco);
    	    	
    	    	if ($launch == 0) {
        	    $launch = $id;
            	}
            }
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
    $newstr='';
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
    return "<a name=\"\" title=\"$stringa\">".substr($stringa,0,$len-4).'...'.substr($stringa,strlen($stringa)-1,1).'</a>';
    } else
    return $stringa;
}

function scorm_external_link($link) {
// check if a link is external
    $result = false;
    $link = strtolower($link);
    if (substr($link,0,7) == 'http://')
        $result = true;
    else if (substr($link,0,8) == 'https://')
        $result = true;
    else if (substr($link,0,4) == 'www.')
        $result = true;
    /*else if (substr($link,0,7) == 'rstp://')
        $result = true;
    else if (substr($link,0,6) == 'rtp://')
        $result = true;
    else if (substr($link,0,6) == 'ftp://')
        $result = true;
    else if (substr($link,0,9) == 'gopher://')
        $result = true; */
    return $result;
}    
?>
