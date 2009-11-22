<?php  // $Id$

/// Library of functions and constants for module glossary
/// (replace glossary with the name of your module and delete this line)

require_once($CFG->libdir.'/filelib.php');

define("GLOSSARY_SHOW_ALL_CATEGORIES", 0);
define("GLOSSARY_SHOW_NOT_CATEGORISED", -1);

define("GLOSSARY_NO_VIEW", -1);
define("GLOSSARY_STANDARD_VIEW", 0);
define("GLOSSARY_CATEGORY_VIEW", 1);
define("GLOSSARY_DATE_VIEW", 2);
define("GLOSSARY_AUTHOR_VIEW", 3);
define("GLOSSARY_ADDENTRY_VIEW", 4);
define("GLOSSARY_IMPORT_VIEW", 5);
define("GLOSSARY_EXPORT_VIEW", 6);
define("GLOSSARY_APPROVAL_VIEW", 7);

/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////

function glossary_add_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    if (empty($glossary->userating)) {
        $glossary->assessed = 0;
    }

    if (empty($glossary->ratingtime) or empty($glossary->assessed)) {
        $glossary->assesstimestart  = 0;
        $glossary->assesstimefinish = 0;
    }

    if (empty($glossary->globalglossary) ) {
        $glossary->globalglossary = 0;
    }

    if (!has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
        $glossary->globalglossary = 0;
    }

    $glossary->timecreated  = time();
    $glossary->timemodified = $glossary->timecreated;

    //Check displayformat is a valid one
    $formats = get_list_of_plugins('mod/glossary/formats','TEMPLATE');
    if (!in_array($glossary->displayformat, $formats)) {
        error("This format doesn't exist!");
    }

    if ($returnid = insert_record("glossary", $glossary)) {
        $glossary->id = $returnid;
        $glossary = stripslashes_recursive($glossary);
        glossary_grade_item_update($glossary);
    }

    return $returnid;
}


function glossary_update_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.
    global $CFG;

    if (empty($glossary->globalglossary)) {
        $glossary->globalglossary = 0;
    }

    if (!has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
        // keep previous
        unset($glossary->globalglossary);
    }

    $glossary->timemodified = time();
    $glossary->id           = $glossary->instance;

    if (empty($glossary->userating)) {
        $glossary->assessed = 0;
    }

    if (empty($glossary->ratingtime) or empty($glossary->assessed)) {
        $glossary->assesstimestart  = 0;
        $glossary->assesstimefinish = 0;
    }

    //Check displayformat is a valid one
    $formats = get_list_of_plugins('mod/glossary/formats','TEMPLATE');
    if (!in_array($glossary->displayformat, $formats)) {
        error("This format doesn't exist!");
    }

    if ($return = update_record("glossary", $glossary)) {
        if ($glossary->defaultapproval) {
            execute_sql("update {$CFG->prefix}glossary_entries SET approved = 1 where approved != 1 and glossaryid = " . $glossary->id,false);
        }
        $glossary = stripslashes_recursive($glossary);
        glossary_grade_item_update($glossary);
    }

    return $return;
}


function glossary_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $glossary = get_record("glossary", "id", "$id")) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records("glossary", "id", "$glossary->id")) {
        $result = false;
    } else {
        if ($categories = get_records("glossary_categories","glossaryid",$glossary->id)) {
            $cats = "";
            foreach ( $categories as $cat ) {
                $cats .= "$cat->id,";
            }
            $cats = substr($cats,0,-1);
            if ($cats) {
                delete_records_select("glossary_entries_categories", "categoryid in ($cats)");
                delete_records("glossary_categories", "glossaryid", $glossary->id);
            }
        }
        if ( $entries = get_records("glossary_entries", "glossaryid", $glossary->id) ) {
            $ents = "";
            foreach ( $entries as $entry ) {
                if ( $entry->sourceglossaryid ) {
                    $entry->glossaryid = $entry->sourceglossaryid;
                    $entry->sourceglossaryid = 0;
                    update_record("glossary_entries",$entry);
                } else {
                    $ents .= "$entry->id,";
                }
            }
            $ents = substr($ents,0,-1);
            if ($ents) {
                delete_records_select("glossary_comments", "entryid in ($ents)");
                delete_records_select("glossary_alias", "entryid in ($ents)");
                delete_records_select("glossary_ratings", "entryid in ($ents)");
            }
        }
        glossary_delete_attachments($glossary);
        delete_records("glossary_entries", "glossaryid", "$glossary->id");
    }
    glossary_grade_item_delete($glossary);

    return $result;
}

function glossary_user_outline($course, $user, $mod, $glossary) {
    global $CFG;
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'glossary', $glossary->id, $user->id);
    if (empty($grades->items[0]->grades)) {
        $grade = false;
    } else {
        $grade = reset($grades->items[0]->grades);
    }

    if ($entries = glossary_get_user_entries($glossary->id, $user->id)) {
        $result = new object();
        $result->info = count($entries) . ' ' . get_string("entries", "glossary");

        $lastentry = array_pop($entries);
        $result->time = $lastentry->timemodified;

        if ($grade) {
            $result->info .= ', ' . get_string('grade') . ': ' . $grade->str_long_grade;
        }
        return $result;
    } else if ($grade) {
        $result = new object();
        $result->info = get_string('grade') . ': ' . $grade->str_long_grade;
        $result->time = $grade->dategraded;
        return $result;
    }

    return NULL;
}

function glossary_get_user_entries($glossaryid, $userid) {
/// Get all the entries for a user in a glossary
    global $CFG;

    return get_records_sql("SELECT e.*, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}glossary g,
                                   {$CFG->prefix}glossary_entries e,
                                   {$CFG->prefix}user u
                             WHERE g.id = '$glossaryid'
                               AND e.glossaryid = g.id
                               AND e.userid = '$userid'
                               AND e.userid = u.id
                          ORDER BY e.timemodified ASC");
}

function glossary_user_complete($course, $user, $mod, $glossary) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.
    global $CFG;
    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'glossary', $glossary->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        echo '<p>'.get_string('grade').': '.$grade->str_long_grade.'</p>';
        if ($grade->str_feedback) {
            echo '<p>'.get_string('feedback').': '.$grade->str_feedback.'</p>';
        }
    }
    if ($entries = glossary_get_user_entries($glossary->id, $user->id)) {
        echo '<table width="95%" border="0"><tr><td>';
        foreach ($entries as $entry) {
            $cm = get_coursemodule_from_instance("glossary", $glossary->id, $course->id);
            glossary_print_entry($course, $cm, $glossary, $entry,"","",0);
            echo '<p>';
        }
        echo '</td></tr></table>';
    }
}

function glossary_print_recent_activity($course, $viewfullnames, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in glossary activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG, $USER;

    //TODO: use timestamp in approved field instead of changing timemodified when approving in 2.0

    $modinfo = get_fast_modinfo($course);
    $ids = array();
    foreach ($modinfo->cms as $cm) {
        if ($cm->modname != 'glossary') {
            continue;
        }
        if (!$cm->uservisible) {
            continue;
        }
        $ids[$cm->instance] = $cm->instance;
    }

    if (!$ids) {
        return false;
    }

    $glist = implode(',', $ids); // there should not be hundreds of glossaries in one course, right?

    if (!$entries = get_records_sql("SELECT ge.id, ge.concept, ge.approved, ge.timemodified, ge.glossaryid,
                                            ge.userid, u.firstname, u.lastname, u.email, u.picture
                                       FROM {$CFG->prefix}glossary_entries ge
                                            JOIN {$CFG->prefix}user u ON u.id = ge.userid
                                      WHERE ge.glossaryid IN ($glist) AND ge.timemodified > $timestart
                                   ORDER BY ge.timemodified ASC")) {
        return false;
    }

    $editor  = array();

    foreach ($entries as $entryid=>$entry) {
        if ($entry->approved) {
            continue;
        }

        if (!isset($editor[$entry->glossaryid])) {
            $editor[$entry->glossaryid] = has_capability('mod/glossary:approve', get_context_instance(CONTEXT_MODULE, $modinfo->instances['glossary'][$entry->glossaryid]->id));
        }

        if (!$editor[$entry->glossaryid]) {
            unset($entries[$entryid]);
        }
    }

    if (!$entries) {
        return false;
    }
    print_headline(get_string('newentries', 'glossary').':');

    $strftimerecent = get_string('strftimerecent');
    foreach ($entries as $entry) {
        $link = $CFG->wwwroot.'/mod/glossary/view.php?g='.$entry->glossaryid.'&amp;mode=entry&amp;hook='.$entry->id;
        if ($entry->approved) {
            $dimmed = '';
        } else {
            $dimmed = ' dimmed_text';
        }
        echo '<div class="head'.$dimmed.'">';
        echo '<div class="date">'.userdate($entry->timemodified, $strftimerecent).'</div>';
        echo '<div class="name">'.fullname($entry, $viewfullnames).'</div>';
        echo '</div>';
        echo '<div class="info"><a href="'.$link.'">'.format_text($entry->concept, true).'</a></div>';
    }

    return true;
}


function glossary_log_info($log) {
    global $CFG;

    return get_record_sql("SELECT e.*, u.firstname, u.lastname
                             FROM {$CFG->prefix}glossary_entries e,
                                  {$CFG->prefix}user u
                            WHERE e.id = '$log->info'
                              AND u.id = '$log->userid'");
}

function glossary_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

/**
 * Return grade for given user or all users.
 *
 * @param int $glossaryid id of glossary
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function glossary_get_user_grades($glossary, $userid=0) {
    global $CFG;

    $user = $userid ? "AND u.id = $userid" : "";

    $sql = "SELECT u.id, u.id AS userid, avg(gr.rating) AS rawgrade
              FROM {$CFG->prefix}user u, {$CFG->prefix}glossary_entries ge,
                   {$CFG->prefix}glossary_ratings gr
             WHERE u.id = ge.userid AND ge.id = gr.entryid
                   AND gr.userid != u.id AND ge.glossaryid = $glossary->id
                   $user
          GROUP BY u.id";

    return get_records_sql($sql);
}

/**
 * Update grades by firing grade_updated event
 *
 * @param object $glossary null means all glossaries (with extra cmidnumber property)
 * @param int $userid specific user only, 0 mean all
 */
function glossary_update_grades($glossary=null, $userid=0, $nullifnone=true) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if ($glossary != null) {
        if ($grades = glossary_get_user_grades($glossary, $userid)) {
            glossary_grade_item_update($glossary, $grades);

        } else if ($userid and $nullifnone) {
            $grade = new object();
            $grade->userid   = $userid;
            $grade->rawgrade = NULL;
            glossary_grade_item_update($glossary, $grade);

        } else {
            glossary_grade_item_update($glossary);
        }

    } else {
        $sql = "SELECT g.*, cm.idnumber as cmidnumber
                  FROM {$CFG->prefix}glossary g, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                 WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id";
        if ($rs = get_recordset_sql($sql)) {
            while ($glossary = rs_fetch_next_record($rs)) {
                if ($glossary->assessed) {
                    glossary_update_grades($glossary, 0, false);
                } else {
                    glossary_grade_item_update($glossary);
                }
            }
            rs_close($rs);
        }
    }
}

/**
 * Create/update grade item for given glossary
 *
 * @param object $glossary object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int, 0 if ok, error code otherwise
 */
function glossary_grade_item_update($glossary, $grades=NULL) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    $params = array('itemname'=>$glossary->name, 'idnumber'=>$glossary->cmidnumber);

    if (!$glossary->assessed or $glossary->scale == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;

    } else if ($glossary->scale > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $glossary->scale;
        $params['grademin']  = 0;

    } else if ($glossary->scale < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$glossary->scale;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/glossary', $glossary->course, 'mod', 'glossary', $glossary->id, 0, $grades, $params);
}

/**
 * Delete grade item for given glossary
 *
 * @param object $glossary object
 */
function glossary_grade_item_delete($glossary) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/glossary', $glossary->course, 'mod', 'glossary', $glossary->id, 0, NULL, array('deleted'=>1));
}

function glossary_get_participants($glossaryid) {
//Returns the users with data in one glossary
//(users with records in glossary_entries, students)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}glossary_entries g
                                 WHERE g.glossaryid = '$glossaryid' and
                                       u.id = g.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}

function glossary_scale_used ($glossaryid,$scaleid) {
//This function returns if a scale is being used by one glossary

    $return = false;

    $rec = get_record("glossary","id","$glossaryid","scale","-$scaleid");

    if (!empty($rec)  && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of glossary
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any glossary
 */
function glossary_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('glossary', 'scale', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other glossary functions go here.  Each of them must have a name that
/// starts with glossary_

//This function return an array of valid glossary_formats records
//Everytime it's called, every existing format is checked, new formats
//are included if detected and old formats are deleted and any glossary
//using an invalid format is updated to the default (dictionary).
function glossary_get_available_formats() {

    global $CFG;

    //Get available formats (plugin) and insert (if necessary) them into glossary_formats
    $formats = get_list_of_plugins('mod/glossary/formats', 'TEMPLATE');
    $pluginformats = array();
    foreach ($formats as $format) {
        //If the format file exists
        if (file_exists($CFG->dirroot.'/mod/glossary/formats/'.$format.'/'.$format.'_format.php')) {
            include_once($CFG->dirroot.'/mod/glossary/formats/'.$format.'/'.$format.'_format.php');
            //If the function exists
            if (function_exists('glossary_show_entry_'.$format)) {
                //Acummulate it as a valid format
                $pluginformats[] = $format;
                //If the format doesn't exist in the table
                if (!$rec = get_record('glossary_formats','name',$format)) {
                    //Insert the record in glossary_formats
                    $gf = new object();
                    $gf->name = $format;
                    $gf->popupformatname = $format;
                    $gf->visible = 1;
                    insert_record("glossary_formats",$gf);
                }
            }
        }
    }

    //Delete non_existent formats from glossary_formats table
    $formats = get_records("glossary_formats");
    foreach ($formats as $format) {
        $todelete = false;
        //If the format in DB isn't a valid previously detected format then delete the record
        if (!in_array($format->name,$pluginformats)) {
            $todelete = true;
        }

        if ($todelete) {
            //Delete the format
            delete_records('glossary_formats','name',$format->name);
            //Reasign existing glossaries to default (dictionary) format
            if ($glossaries = get_records('glossary','displayformat',$format->name)) {
                foreach($glossaries as $glossary) {
                    set_field('glossary','displayformat','dictionary','id',$glossary->id);
                }
            }
        }
    }

    //Now everything is ready in glossary_formats table
    $formats = get_records("glossary_formats");

    return $formats;
}

function glossary_debug($debug,$text,$br=1) {
    if ( $debug ) {
        echo '<font color="red">' . $text . '</font>';
        if ( $br ) {
            echo '<br />';
        }
    }
}

function glossary_get_entries($glossaryid, $entrylist, $pivot = "") {
    global $CFG;
    if ($pivot) {
       $pivot .= ",";
    }

    return get_records_sql("SELECT $pivot id,userid,concept,definition,format
                            FROM {$CFG->prefix}glossary_entries
                            WHERE glossaryid = '$glossaryid'
                            AND id IN ($entrylist)");
}

function glossary_get_entries_search($concept, $courseid) {

    global $CFG;

    //Check if the user is an admin
    $bypassadmin = 1; //This means NO (by default)
    if (has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_SYSTEM))) {
        $bypassadmin = 0; //This means YES
    }

    //Check if the user is a teacher
    $bypassteacher = 1; //This means NO (by default)
    if (has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_COURSE, $courseid))) {
        $bypassteacher = 0; //This means YES
    }

    $conceptlower = moodle_strtolower(trim($concept));

    return get_records_sql("SELECT e.*, g.name as glossaryname, cm.id as cmid, cm.course as courseid
                            FROM {$CFG->prefix}glossary_entries e,
                                 {$CFG->prefix}glossary g,
                                 {$CFG->prefix}course_modules cm,
                                 {$CFG->prefix}modules m
                            WHERE m.name = 'glossary' AND
                                  cm.module = m.id AND
                                  (cm.visible = 1 OR  cm.visible = $bypassadmin OR
                                    (cm.course = '$courseid' AND cm.visible = $bypassteacher)) AND
                                  g.id = cm.instance AND
                                  e.glossaryid = g.id  AND
                                  ( (e.casesensitive != 0 AND LOWER(concept) = '$conceptlower') OR
                                    (e.casesensitive = 0 and concept = '$concept')) AND
                                  (g.course = '$courseid' OR g.globalglossary = 1) AND
                                  e.usedynalink != 0 AND
                                  g.usedynalink != 0");
}

function glossary_get_entries_sorted($glossary, $where="", $orderby="", $pivot = "") {
global $CFG;
    if ($where) {
       $where = " and $where";
    }
    if ($orderby) {
       $orderby = " ORDER BY $orderby";
    }
    if ($pivot) {
       $pivot .= ",";
    }
    return      get_records_sql("SELECT $pivot *
                                 FROM {$CFG->prefix}glossary_entries
                                 WHERE (glossaryid = $glossary->id or sourceglossaryid = $glossary->id) $where $orderby");
}

function glossary_get_entries_by_category($glossary, $hook, $where="", $orderby="", $pivot = "") {
global $CFG;
    if ($where) {
       $where = " and $where";
    }
    if ($orderby) {
       $orderby = " ORDER BY $orderby";
    }
    if ($pivot) {
       $pivot .= ",";
    }
    return      get_records_sql("SELECT $pivot ge.*
                                 FROM {$CFG->prefix}glossary_entries ge, {$CFG->prefix}glossary_entries_categories c
                                 WHERE (ge.id = c.entryidid and c.categoryid = $hook) and
                                             (ge.glossaryid = $glossary->id or ge.sourceglossaryid = $glossary->id) $where $orderby");
}

function glossary_print_entry($course, $cm, $glossary, $entry, $mode='',$hook='',$printicons = 1, $displayformat  = -1, $ratings = NULL, $printview = false) {
    global $USER, $CFG;
    $return = false;
    if ( $displayformat < 0 ) {
        $displayformat = $glossary->displayformat;
    }
    if ($entry->approved or ($USER->id == $entry->userid) or ($mode == 'approval' and !$entry->approved) ) {
        $formatfile = $CFG->dirroot.'/mod/glossary/formats/'.$displayformat.'/'.$displayformat.'_format.php';
        if ($printview) {
            $functionname = 'glossary_print_entry_'.$displayformat;
        } else {
            $functionname = 'glossary_show_entry_'.$displayformat;
        }

        if (file_exists($formatfile)) {
            include_once($formatfile);
            if (function_exists($functionname)) {
                $return = $functionname($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings);
            } else if ($printview) {
                //If the glossary_print_entry_XXXX function doesn't exist, print default (old) print format
                $return = glossary_print_entry_default($entry);
            }
        }
    }
    return $return;
}

 //Default (old) print format used if custom function doesn't exist in format
function glossary_print_entry_default ($entry) {
    echo '<h3>'. strip_tags($entry->concept) . ': </h3>';

    $definition = $entry->definition;

    // always detect and strip TRUSTTEXT marker before processing and add+strip it afterwards!
    if (trusttext_present($definition)) {
        $ttpresent = true;
        $definition = trusttext_strip($definition);
    } else {
        $ttpresent = false;
    }

    $definition = '<span class="nolink">' . strip_tags($definition) . '</span>';

    // reconstruct the TRUSTTEXT properly after processing
    if ($ttpresent) {
        $definition = trusttext_mark($definition);
    } else {
        $definition = trusttext_strip($definition); //make 100% sure TRUSTTEXT marker was not created
    }

    $options = new object();
    $options->para = false;
    $options->trusttext = true;
    $definition = format_text($definition, $entry->format, $options);
    echo ($definition);
    echo '<br /><br />';
}

/**
 * Print glossary concept/term as a heading &lt;h3>
 */
function  glossary_print_entry_concept($entry) {
    $options = new object();
    $options->para = false;
    $text = format_text(print_heading('<span class="nolink">' . $entry->concept . '</span>', '', 3, 'nolink', true), FORMAT_MOODLE, $options);
    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }
    echo $text;
}

function glossary_print_entry_definition($entry) {

    $definition = $entry->definition;

    // always detect and strip TRUSTTEXT marker before processing and add+strip it afterwards!
    if (trusttext_present($definition)) {
        $ttpresent = true;
        $definition = trusttext_strip($definition);
    } else {
        $ttpresent = false;
    }

    global $GLOSSARY_EXCLUDECONCEPTS;

    //Calculate all the strings to be no-linked
    //First, the concept
    $GLOSSARY_EXCLUDECONCEPTS=array($entry->concept);
    //Now the aliases
    if ( $aliases = get_records('glossary_alias','entryid',$entry->id) ) {
        foreach ($aliases as $alias) {
            $GLOSSARY_EXCLUDECONCEPTS[]=trim($alias->alias);
        }
    }

    $options = new object();
    $options->para = false;
    $options->trusttext = true;

    // reconstruct the TRUSTTEXT properly after processing
    if ($ttpresent) {
        $definition = trusttext_mark($definition);
    } else {
        $definition = trusttext_strip($definition); //make 100% sure TRUSTTEXT marker was not created
    }

    $text = format_text($definition, $entry->format, $options);
    
    // Stop excluding concepts from autolinking
    unset($GLOSSARY_EXCLUDECONCEPTS);
    
    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }
    if (isset($entry->footer)) {   // Unparsed footer info
        $text .= $entry->footer;
    }
    echo $text;
}

function  glossary_print_entry_aliases($course, $cm, $glossary, $entry,$mode='',$hook='', $type = 'print') {
    $return = '';
    if ( $aliases = get_records('glossary_alias','entryid',$entry->id) ) {
        foreach ($aliases as $alias) {
            if (trim($alias->alias)) {
                if ($return == '') {
                    $return = '<select style="font-size:8pt">';
                }
                $return .= "<option>$alias->alias</option>";
            }
        }
        if ($return != '') {
            $return .= '</select>';
        }
    }
    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

function glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode='',$hook='', $type = 'print') {
    global $USER, $CFG;

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $output = false;   //To decide if we must really return text in "return". Activate when needed only!
    $importedentry = ($entry->sourceglossaryid == $glossary->id);
    $ismainglossary = $glossary->mainglossary;


    $return = '<span class="commands">';
    // Differentiate links for each entry.
    $altsuffix = ': '.strip_tags(format_text($entry->concept));

    if (!$entry->approved) {
        $output = true;
        $return .= get_string('entryishidden','glossary');
    }
    $return .= glossary_print_entry_commentslink($course, $cm, $glossary, $entry,$mode,$hook,'html');

    if (has_capability('mod/glossary:comment', $context) and $glossary->allowcomments) {
        $output = true;
        $return .= ' <a title="' . get_string('addcomment','glossary') . '" href="comment.php?action=add&amp;eid='.$entry->id.'"><img src="comment.gif" class="iconsmall" alt="'.get_string('addcomment','glossary').$altsuffix.'" /></a>';
    }


    if (has_capability('mod/glossary:manageentries', $context) or (!empty($USER->id) and has_capability('mod/glossary:write', $context) and $entry->userid == $USER->id)) {
        // only teachers can export entries so check it out
        if (has_capability('mod/glossary:export', $context) and !$ismainglossary and !$importedentry) {
            $mainglossary = get_record('glossary','mainglossary',1,'course',$course->id);
            if ( $mainglossary ) {  // if there is a main glossary defined, allow to export the current entry
                $output = true;
                $return .= ' <a title="'.get_string('exporttomainglossary','glossary') . '" href="exportentry.php?id='.$cm->id.'&amp;entry='.$entry->id.'&amp;mode='.$mode.'&amp;hook='.$hook.'"><img src="export.gif" class="iconsmall" alt="'.get_string('exporttomainglossary','glossary').$altsuffix.'" /></a>';
            }
        }

        if ( $entry->sourceglossaryid ) {
            $icon = "minus.gif";   // graphical metaphor (minus) for deleting an imported entry
        } else {
            $icon = "$CFG->pixpath/t/delete.gif";
        }

        //Decide if an entry is editable:
        // -It isn't a imported entry (so nobody can edit a imported (from secondary to main) entry)) and
        // -The user is teacher or he is a student with time permissions (edit period or editalways defined).
        $ineditperiod = ((time() - $entry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
        if ( !$importedentry and (has_capability('mod/glossary:manageentries', $context) or ($entry->userid == $USER->id and ($ineditperiod and has_capability('mod/glossary:write', $context))))) {
            $output = true;
            $return .= " <a title=\"" . get_string("delete") . "\" href=\"deleteentry.php?id=$cm->id&amp;mode=delete&amp;entry=$entry->id&amp;prevmode=$mode&amp;hook=$hook\"><img src=\"";
            $return .= $icon;
            $return .= "\" class=\"iconsmall\" alt=\"" . get_string("delete") .$altsuffix."\" /></a> ";

            $return .= " <a title=\"" . get_string("edit") . "\" href=\"edit.php?id=$cm->id&amp;e=$entry->id&amp;mode=$mode&amp;hook=$hook\"><img src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"" . get_string("edit") .$altsuffix. "\" /></a>";
        } elseif ( $importedentry ) {
            $return .= " <font size=\"-1\">" . get_string("exportedentry","glossary") . "</font>";
        }
    }
    $return .= "&nbsp;&nbsp;"; // just to make up a little the output in Mozilla ;)

    $return .= '</span>';

    //If we haven't calculated any REAL thing, delete result ($return)
    if (!$output) {
        $return = '';
    }
    //Print or get
    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

function glossary_print_entry_commentslink($course, $cm, $glossary, $entry,$mode,$hook, $type = 'print') {
    $return = '';

    $count = count_records('glossary_comments','entryid',$entry->id);
    if ($count) {
        $return = '';
        $return .= "<a href=\"comments.php?id=$cm->id&amp;eid=$entry->id\">$count ";
        if ($count == 1) {
            $return .= get_string('comment', 'glossary');
        } else {
            $return .= get_string('comments', 'glossary');
        }
        $return .= '</a>';
    }

    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

function  glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook,$printicons,$ratings,$aliases=true) {

    if ($aliases) {
        $aliases = glossary_print_entry_aliases($course, $cm, $glossary, $entry, $mode, $hook,'html');
    }
    $icons   = '';
    $return   = '';
    if ( $printicons ) {
        $icons   = glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,'html');
    }
    if ($aliases || $icons || $ratings) {
        echo '<table>';
        if ( $aliases ) {
            echo '<tr valign="top"><td class="aliases">' .
                  get_string('aliases','glossary').': '.$aliases . '</td></tr>';
        }
        if ($icons) {
            echo '<tr valign="top"><td class="icons">'.$icons.'</td></tr>';
        }
        if ($ratings) {
            echo '<tr valign="top"><td class="ratings">';
            $return = glossary_print_entry_ratings($course, $entry, $ratings);
            echo '</td></tr>';
        }
        echo '</table>';
    }
    return $return;
}

function glossary_print_entry_attachment($entry,$format=NULL,$align="right",$insidetable=true) {
///   valid format values: html  : Return the HTML link for the attachment as an icon
///                        text  : Return the HTML link for tha attachment as text
///                        blank : Print the output to the screen
    if ($entry->attachment) {
          $glossary = get_record("glossary","id",$entry->glossaryid);
          $entry->course = $glossary->course; //used inside print_attachment
          if ($insidetable) {
              echo "<table border=\"0\" width=\"100%\" align=\"$align\"><tr><td align=\"$align\" nowrap=\"nowrap\">\n";
          }
          echo glossary_print_attachments($entry,$format,$align);
          if ($insidetable) {
              echo "</td></tr></table>\n";
          }
    }
}

function  glossary_print_entry_approval($cm, $entry, $mode,$align="right",$insidetable=true) {
    global $CFG;

    if ( $mode == 'approval' and !$entry->approved ) {
        if ($insidetable) {
            echo '<table class="glossaryapproval" align="'.$align.'"><tr><td align="'.$align.'">';
        }
        echo '<a title="'.get_string('approve','glossary').'" href="approve.php?eid='.$entry->id.'&amp;mode='.$mode.'&amp;sesskey='.sesskey().'"><img align="'.$align.'" src="'.$CFG->pixpath.'/i/approve.gif" style="border:0px; width:34px; height:34px" alt="'.get_string('approve','glossary').'" /></a>';
        if ($insidetable) {
            echo '</td></tr></table>';
        }
    }
}

function glossary_search($course, $searchterms, $extended = 0, $glossary = NULL) {
// It returns all entries from all glossaries that matches the specified criteria
//    within a given $course. It performs an $extended search if necessary.
// It restrict the search to only one $glossary if the $glossary parameter is set.

    global $CFG;
    if ( !$glossary ) {
        if ( $glossaries = get_records("glossary", "course", $course->id) ) {
            $glos = "";
            foreach ( $glossaries as $glossary ) {
                $glos .= "$glossary->id,";
            }
            $glos = substr($glos,0,-1);
        }
    } else {
        $glos = $glossary->id;
    }

    if (!has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_COURSE, $glossary->course))) {
        $glossarymodule = get_record("modules", "name", "glossary");
        $onlyvisible = " AND g.id = cm.instance AND cm.visible = 1 AND cm.module = $glossarymodule->id";
        $onlyvisibletable = ", {$CFG->prefix}course_modules cm";
    } else {

        $onlyvisible = "";
        $onlyvisibletable = "";
    }

    /// Some differences in syntax for entrygreSQL
    switch ($CFG->dbfamily) {
    case 'postgres':
        $LIKE = "ILIKE";   // case-insensitive
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    break;
    case 'mysql':
    default:
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    break;
    }

    $conceptsearch = "";
    $definitionsearch = "";


    foreach ($searchterms as $searchterm) {
        if ($conceptsearch) {
            $conceptsearch.= " OR ";
        }
        if ($definitionsearch) {
            $definitionsearch.= " OR ";
        }

    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE search
        if ($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql') {
            $searchterm = trim($searchterm, '+-');
        }

        if (substr($searchterm,0,1) == "+") {
            $searchterm = substr($searchterm,1);
            $conceptsearch.= " e.concept $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $definitionsearch .= " e.definition $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $conceptsearch .= " e.concept $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $definitionsearch .= " e.definition $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $conceptsearch .= " e.concept $LIKE '%$searchterm%' ";
            $definitionsearch .= " e.definition $LIKE '%$searchterm%' ";
        }
    }

    $definitionsearch = !empty($extended) ? "OR $definitionsearch" : '';

    $selectsql = "{$CFG->prefix}glossary_entries e,
                  {$CFG->prefix}glossary g $onlyvisibletable
             WHERE ($conceptsearch $definitionsearch)
               AND (e.glossaryid = g.id or e.sourceglossaryid = g.id) $onlyvisible
               AND g.id IN ($glos) AND e.approved != 0";

    return get_records_sql("SELECT e.*
                            FROM $selectsql ORDER BY e.concept ASC");
}

function glossary_search_entries($searchterms, $glossary, $extended) {
    $course = get_record("course","id",$glossary->course);
    return glossary_search($course,$searchterms,$extended,$glossary);
}

function glossary_file_area_name($entry) {
    global $CFG;
//  Creates a directory file name, suitable for make_upload_directory()

    // I'm doing this workaround for make it works for delete_instance also
    //  (when called from delete_instance, glossary is already deleted so
    //   getting the course from mdl_glossary does not work)
    $module = get_record("modules","name","glossary");
    $cm = get_record("course_modules","module",$module->id,"instance",$entry->glossaryid);
    return "$cm->course/$CFG->moddata/glossary/$entry->glossaryid/$entry->id";
}

function glossary_file_area($entry) {
    return make_upload_directory( glossary_file_area_name($entry) );
}

function glossary_main_file_area($glossary) {
    $modarea = glossary_mod_file_area($glossary);
    return "$modarea/$glossary->id";
}

function glossary_mod_file_area($glossary) {
    global $CFG;

    return make_upload_directory( "$glossary->course/$CFG->moddata/glossary" );
}

function glossary_delete_old_attachments($entry, $exception="") {
// Deletes all the user files in the attachments area for a entry
// EXCEPT for any file named $exception

    if ($basedir = glossary_file_area($entry)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                if ($file != $exception) {
                    unlink("$basedir/$file");
//                    notify("Existing file '$file' has been deleted!");
                }
            }
        }
        if (!$exception) {  // Delete directory as well, if empty
            rmdir("$basedir");
        }
    }
}
function glossary_delete_attachments($glossary) {
// Deletes all the user files in the attachments area for the glossary
    if ( $entries = get_records("glossary_entries","glossaryid",$glossary->id) ) {
        $deleted = 0;
        foreach ($entries as $entry) {
            if ( $entry->attachment ) {
                if ($basedir = glossary_file_area($entry)) {
                    if ($files = get_directory_list($basedir)) {
                        foreach ($files as $file) {
                            unlink("$basedir/$file");
                        }
                    }
                    rmdir("$basedir");
                    $deleted++;
                }
            }
        }
        if ( $deleted ) {
            $attachmentdir = glossary_main_file_area($glossary);
            $glossarydir = glossary_mod_file_area($glossary);

            rmdir("$attachmentdir");
            if (!$files = get_directory_list($glossarydir) ) {
                rmdir( "$glossarydir" );
            }
        }
    }
}

function glossary_copy_attachments($entry, $newentry) {
/// Given a entry object that is being copied to glossaryid,
/// this function checks that entry
/// for attachments, and if any are found, these are
/// copied to the new glossary directory.

    global $CFG;

    $return = true;

    if ($entries = get_records_select("glossary_entries", "id = '$entry->id' AND attachment <> ''")) {
        foreach ($entries as $curentry) {
            $oldentry = new object();
            $oldentry->id = $entry->id;
            $oldentry->course = $entry->course;
            $oldentry->glossaryid = $curentry->glossaryid;
            $oldentrydir = "$CFG->dataroot/".glossary_file_area_name($oldentry);
            if (is_dir($oldentrydir)) {

                $newentrydir = glossary_file_area($newentry);
                if (! copy("$oldentrydir/$newentry->attachment", "$newentrydir/$newentry->attachment")) {
                    $return = false;
                }
            }
        }
     }
    return $return;
}

function glossary_move_attachments($entry, $glossaryid) {
/// Given a entry object that is being moved to glossaryid,
/// this function checks that entry
/// for attachments, and if any are found, these are
/// moved to the new glossary directory.

    global $CFG;

    require_once($CFG->dirroot.'/lib/uploadlib.php');

    $return = true;

    if ($entries = get_records_select("glossary_entries", "glossaryid = '$entry->id' AND attachment <> ''")) {
        foreach ($entries as $entry) {
            $oldentry = new object();
            $oldentry->course = $entry->course;
            $oldentry->glossaryid = $entry->glossaryid;
            $oldentrydir = "$CFG->dataroot/".glossary_file_area_name($oldentry);
            if (is_dir($oldentrydir)) {
                $newentry = $oldentry;
                $newentry->glossaryid = $glossaryid;
                $newentrydir = "$CFG->dataroot/".glossary_file_area_name($newentry);
                $files = get_directory_list($oldentrydir); // get it before we rename it.
                if (! @rename($oldentrydir, $newentrydir)) {
                    $return = false;
                }
                foreach ($files as $file) {
                    // this is not tested as I can't find anywhere that calls this function, grepping the source.
                    clam_change_log($oldentrydir.'/'.$file,$newentrydir.'/'.$file);
                }
            }
        }
    }
    return $return;
}

function glossary_print_attachments($entry, $return=NULL, $align="left") {
// if return=html, then return a html string.
// if return=text, then return a text-only string.
// otherwise, print HTML for non-images, and return image HTML
//     if attachment is an image, $align set its aligment.
    global $CFG;

    $newentry = $entry;
    if ( $newentry->sourceglossaryid ) {
        $newentry->glossaryid = $newentry->sourceglossaryid;
    }

    $filearea = glossary_file_area_name($newentry);

    $imagereturn = "";
    $output = "";

    if ($basedir = glossary_file_area($newentry)) {
        if ($files = get_directory_list($basedir)) {
            $strattachment = get_string("attachment", "glossary");
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                $ffurl = get_file_url("$filearea/$file");
                $image = "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"\" />";

                if ($return == "html") {
                    $output .= "<a href=\"$ffurl\">$image</a> ";
                    $output .= "<a href=\"$ffurl\">$file</a><br />";

                } else if ($return == "text") {
                    $output .= "$strattachment $file:\n$ffurl\n";

                } else {
                    if ($icon == "image.gif") {    // Image attachments don't get printed as links
                        $imagereturn .= "<img src=\"$ffurl\" align=\"$align\" alt=\"\" />";
                    } else {
                        echo "<a href=\"$ffurl\">$image</a> ";
                        echo "<a href=\"$ffurl\">$file</a><br />";
                    }
                }
            }
        }
    }

    if ($return) {
        return $output;
    }

    return $imagereturn;
}

function glossary_print_tabbed_table_end() {
     echo "</div></div>";
}

function glossary_print_approval_menu($cm, $glossary,$mode, $hook, $sortkey = '', $sortorder = '') {
    if ($glossary->showalphabet) {
        echo '<div class="glossaryexplain">' . get_string("explainalphabet","glossary") . '</div><br />';
    }
    glossary_print_special_links($cm, $glossary, $mode, $hook);

    glossary_print_alphabet_links($cm, $glossary, $mode, $hook,$sortkey, $sortorder);

    glossary_print_all_links($cm, $glossary, $mode, $hook);

    glossary_print_sorting_links($cm, $mode, 'CREATION', 'asc');
}

function glossary_print_import_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    echo '<div class="glossaryexplain">' . get_string("explainimport","glossary") . '</div>';
}

function glossary_print_export_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    echo '<div class="glossaryexplain">' . get_string("explainexport","glossary") . '</div>';
}

function glossary_print_alphabet_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    if ( $mode != 'date' ) {
        if ($glossary->showalphabet) {
            echo '<div class="glossaryexplain">' . get_string("explainalphabet","glossary") . '</div><br />';
        }

        glossary_print_special_links($cm, $glossary, $mode, $hook);

        glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder);

        glossary_print_all_links($cm, $glossary, $mode, $hook);
    } else {
        glossary_print_sorting_links($cm, $mode, $sortkey,$sortorder);
    }
}

function glossary_print_author_menu($cm, $glossary,$mode, $hook, $sortkey = '', $sortorder = '') {
    if ($glossary->showalphabet) {
        echo '<div class="glossaryexplain">' . get_string("explainalphabet","glossary") . '</div><br />';
    }

    glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder);
    glossary_print_all_links($cm, $glossary, $mode, $hook);
    glossary_print_sorting_links($cm, $mode, $sortkey,$sortorder);
}

function glossary_print_categories_menu($cm, $glossary, $hook, $category) {

     global $CFG;

     $context = get_context_instance(CONTEXT_MODULE, $cm->id);

     echo '<table border="0" width="100%">';
     echo '<tr>';

     echo '<td align="center" style="width:20%">';
     if (has_capability('mod/glossary:managecategories', $context)) {
             $options['id'] = $cm->id;
             $options['mode'] = 'cat';
             $options['hook'] = $hook;
             echo print_single_button("editcategories.php", $options, get_string("editcategories","glossary"), "get");
     }
     echo '</td>';

     echo '<td align="center" style="width:60%">';
     echo '<b>';

     $menu[GLOSSARY_SHOW_ALL_CATEGORIES] = get_string("allcategories","glossary");
     $menu[GLOSSARY_SHOW_NOT_CATEGORISED] = get_string("notcategorised","glossary");

     $categories = get_records("glossary_categories", "glossaryid", $glossary->id, "name ASC");
     $selected = '';
     if ( $categories ) {
          foreach ($categories as $currentcategory) {
                 $url = $currentcategory->id;
                 if ( $category ) {
                     if ($currentcategory->id == $category->id) {
                         $selected = $url;
                     }
                 }
                 $menu[$url] = clean_text($currentcategory->name); //Only clean, not filters
          }
     }
     if ( !$selected ) {
         $selected = GLOSSARY_SHOW_NOT_CATEGORISED;
     }

     if ( $category ) {
        echo format_text($category->name, FORMAT_PLAIN);
     } else {
        if ( $hook == GLOSSARY_SHOW_NOT_CATEGORISED ) {

            echo get_string("entrieswithoutcategory","glossary");
            $selected = GLOSSARY_SHOW_NOT_CATEGORISED;

        } elseif ( $hook == GLOSSARY_SHOW_ALL_CATEGORIES ) {

            echo get_string("allcategories","glossary");
            $selected = GLOSSARY_SHOW_ALL_CATEGORIES;

        }
     }
     echo '</b></td>';
     echo '<td align="center" style="width:20%">';

     echo popup_form("$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=cat&amp;hook=", $menu, "catmenu", $selected, "",
                      "", "", false);

     echo '</td>';
     echo '</tr>';

     echo '</table>';
}

function glossary_print_all_links($cm, $glossary, $mode, $hook) {
global $CFG;
     if ( $glossary->showall) {
         $strallentries       = get_string("allentries", "glossary");
         if ( $hook == 'ALL' ) {
              echo "<b>$strallentries</b>";
         } else {
              $strexplainall = strip_tags(get_string("explainall","glossary"));
              echo "<a title=\"$strexplainall\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=$mode&amp;hook=ALL\">$strallentries</a>";
         }
     }
}

function glossary_print_special_links($cm, $glossary, $mode, $hook) {
global $CFG;
     if ( $glossary->showspecial) {
         $strspecial          = get_string("special", "glossary");
         if ( $hook == 'SPECIAL' ) {
              echo "<b>$strspecial</b> | ";
         } else {
              $strexplainspecial = strip_tags(get_string("explainspecial","glossary"));
              echo "<a title=\"$strexplainspecial\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=$mode&amp;hook=SPECIAL\">$strspecial</a> | ";
         }
     }
}

function glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder) {
global $CFG;
     if ( $glossary->showalphabet) {
          $alphabet = explode(",", get_string("alphabet"));
          $letters_by_line = 14;
          for ($i = 0; $i < count($alphabet); $i++) {
              if ( $hook == $alphabet[$i] and $hook) {
                   echo "<b>$alphabet[$i]</b>";
              } else {
                   echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=$mode&amp;hook=".urlencode($alphabet[$i])."&amp;sortkey=$sortkey&amp;sortorder=$sortorder\">$alphabet[$i]</a>";
              }
              if ((int) ($i % $letters_by_line) != 0 or $i == 0) {
                   echo ' | ';
              } else {
                   echo '<br />';
              }
          }
     }
}

function glossary_print_sorting_links($cm, $mode, $sortkey = '',$sortorder = '') {
global $CFG;

    $asc    = get_string("ascending","glossary");
    $desc   = get_string("descending","glossary");
    $bopen  = '<b>';
    $bclose = '</b>';

     $neworder = '';
     $currentorder = '';
     $currentsort = '';
     if ( $sortorder ) {
         if ( $sortorder == 'asc' ) {
             $currentorder = $asc;
             $neworder = '&amp;sortorder=desc';
             $newordertitle = get_string('changeto', 'glossary', $desc);
         } else {
             $currentorder = $desc;
             $neworder = '&amp;sortorder=asc';
             $newordertitle = get_string('changeto', 'glossary', $asc);
         }
         $icon = " <img src=\"$sortorder.gif\" class=\"icon\" alt=\"$newordertitle\" />";
     } else {
         if ( $sortkey != 'CREATION' and $sortkey != 'UPDATE' and
               $sortkey != 'FIRSTNAME' and $sortkey != 'LASTNAME' ) {
             $icon = "";
             $newordertitle = $asc;
         } else {
             $newordertitle = $desc;
             $neworder = '&amp;sortorder=desc';
             $icon = ' <img src="asc.gif" class="icon" alt="'.$newordertitle.'" />';
         }
     }
     $ficon     = '';
     $fneworder = '';
     $fbtag     = '';
     $fendbtag  = '';

     $sicon     = '';
     $sneworder = '';

     $sbtag      = '';
     $fbtag      = '';
     $fendbtag      = '';
     $sendbtag      = '';

     $sendbtag  = '';

     if ( $sortkey == 'CREATION' or $sortkey == 'FIRSTNAME' ) {
         $ficon       = $icon;
         $fneworder   = $neworder;
         $fordertitle = $newordertitle;
         $sordertitle = $asc;
         $fbtag       = $bopen;
         $fendbtag    = $bclose;
     } elseif ($sortkey == 'UPDATE' or $sortkey == 'LASTNAME') {
         $sicon = $icon;
         $sneworder   = $neworder;
         $fordertitle = $asc;
         $sordertitle = $newordertitle;
         $sbtag       = $bopen;
         $sendbtag    = $bclose;
     } else {
         $fordertitle = $asc;
         $sordertitle = $asc;
     }

     if ( $sortkey == 'CREATION' or $sortkey == 'UPDATE' ) {
         $forder = 'CREATION';
         $sorder =  'UPDATE';
         $fsort  = get_string("sortbycreation", "glossary");
         $ssort  = get_string("sortbylastupdate", "glossary");

         $currentsort = $fsort;
         if ($sortkey == 'UPDATE') {
             $currentsort = $ssort;
         }
         $sort        = get_string("sortchronogically", "glossary");
     } elseif ( $sortkey == 'FIRSTNAME' or $sortkey == 'LASTNAME') {
         $forder = 'FIRSTNAME';
         $sorder =  'LASTNAME';
         $fsort  = get_string("firstname");
         $ssort  = get_string("lastname");

         $currentsort = $fsort;
         if ($sortkey == 'LASTNAME') {
             $currentsort = $ssort;
         }
         $sort        = get_string("sortby", "glossary");
     }
     $current = '<span class="accesshide">'.get_string('current', 'glossary', "$currentsort $currentorder").'</span>';
     echo "<br />$current $sort: $sbtag<a title=\"$ssort $sordertitle\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;sortkey=$sorder$sneworder&amp;mode=$mode\">$ssort$sicon</a>$sendbtag | ".
                          "$fbtag<a title=\"$fsort $fordertitle\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;sortkey=$forder$fneworder&amp;mode=$mode\">$fsort$ficon</a>$fendbtag<br />";
}

function glossary_sort_entries ( $entry0, $entry1 ) {

    if ( moodle_strtolower(ltrim($entry0->concept)) < moodle_strtolower(ltrim($entry1->concept)) ) {
        return -1;
    } elseif ( moodle_strtolower(ltrim($entry0->concept)) > moodle_strtolower(ltrim($entry1->concept)) ) {
        return 1;
    } else {
        return 0;
    }
}

function glossary_print_comment($course, $cm, $glossary, $entry, $comment) {
    global $CFG, $USER;

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $user = get_record('user', 'id', $comment->userid);
    $strby = get_string('writtenby','glossary');
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));

    echo '<div class="boxaligncenter">';
    echo '<table class="glossarycomment" cellspacing="0">';
    echo '<tr valign="top">';
    echo '<td class="left picture">';
    print_user_picture($user, $course->id, $user->picture);
    echo '</td>';
    echo '<td class="entryheader">';

    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
    $by = new object();
    $by->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id.'">'.$fullname.'</a>';
    $by->date = userdate($comment->timemodified);
    echo '<span class="author">'.get_string('bynameondate', 'forum', $by).'</span>';

    echo '</td></tr>';

    echo '<tr valign="top"><td class="left side">';
    echo '&nbsp;';
    echo '</td><td class="entry">';

    $options = new object();
    $options->trusttext = true;
    echo format_text($comment->entrycomment, $comment->format, $options);

    echo '<div class="icons commands">';

    $ineditperiod = ((time() - $comment->timemodified <  $CFG->maxeditingtime) || $glossary->editalways);
    if ( ($glossary->allowcomments &&  $ineditperiod && $USER->id == $comment->userid)  || has_capability('mod/glossary:managecomments', $context)) {
        echo "<a href=\"comment.php?cid=$comment->id&amp;action=edit\"><img
               alt=\"" . get_string("edit") . "\" src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" /></a> ";
    }
    if ( ($glossary->allowcomments && $USER->id == $comment->userid) || has_capability('mod/glossary:managecomments', $context) ) {
        echo "<a href=\"comment.php?cid=$comment->id&amp;action=delete\"><img
               alt=\"" . get_string("delete") . "\" src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" /></a>";
    }

    echo '</div></td></tr>';
    echo '</table></div>';

}

function  glossary_print_entry_ratings($course, $entry, $ratings = NULL) {

    global $USER, $CFG;

    $glossary = get_record('glossary', 'id', $entry->glossaryid);
    $glossarymod = get_record('modules','name','glossary');
    $cm = get_record_sql("select * from {$CFG->prefix}course_modules where course = $course->id
                          and module = $glossarymod->id and instance = $glossary->id");

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $ratingsmenuused = false;
    if (!empty($ratings) and !empty($USER->id)) {
        $useratings = true;
        if ($ratings->assesstimestart and $ratings->assesstimefinish) {
            if ($entry->timecreated < $ratings->assesstimestart or $entry->timecreated > $ratings->assesstimefinish) {
                $useratings = false;
            }
        }
        if ($useratings) {
            if (has_capability('mod/glossary:viewrating', $context)) {
                glossary_print_ratings_mean($entry->id, $ratings->scale);
                if ($USER->id != $entry->userid) {
                     glossary_print_rating_menu($entry->id, $USER->id, $ratings->scale);
                     $ratingsmenuused = true;
                }
            } else if ($USER->id == $entry->userid) {
                glossary_print_ratings_mean($entry->id, $ratings->scale);
            } else if (!empty($ratings->allow) ) {
                glossary_print_rating_menu($entry->id, $USER->id, $ratings->scale);
                $ratingsmenuused = true;
            }
        }
    }
    return $ratingsmenuused;
}

function glossary_print_dynaentry($courseid, $entries, $displayformat = -1) {
    global $USER,$CFG;

    echo '<div class="boxaligncenter">';
    echo '<table class="glossarypopup" cellspacing="0"><tr>';
    echo '<td>';
    if ( $entries ) {
        foreach ( $entries as $entry ) {
            if (! $glossary = get_record('glossary', 'id', $entry->glossaryid)) {
                error('Glossary ID was incorrect or no longer exists');
            }
            if (! $course = get_record('course', 'id', $glossary->course)) {
                error('Glossary is misconfigured - don\'t know what course it\'s from');
            }
            if (!$cm = get_coursemodule_from_instance('glossary', $entry->glossaryid, $glossary->course) ) {
                error('Glossary is misconfigured - don\'t know what course module it is');
            }

            //If displayformat is present, override glossary->displayformat
            if ($displayformat < 0) {
                $dp = $glossary->displayformat;
            } else {
                $dp = $displayformat;
            }

            //Get popupformatname
            $format = get_record('glossary_formats','name',$dp);
            $displayformat = $format->popupformatname;

            //Check displayformat variable and set to default if necessary
            if (!$displayformat) {
                $displayformat = 'dictionary';
            }

            $formatfile = $CFG->dirroot.'/mod/glossary/formats/'.$displayformat.'/'.$displayformat.'_format.php';
            $functionname = 'glossary_show_entry_'.$displayformat;

            if (file_exists($formatfile)) {
                include_once($formatfile);
                if (function_exists($functionname)) {
                    $functionname($course, $cm, $glossary, $entry,'','','','');
                }
            }
        }
    }
    echo '</td>';
    echo '</tr></table></div>';
}

function glossary_generate_export_file($glossary, $hook = "", $hook = 0) {
    global $CFG;

    $co  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

    $co .= glossary_start_tag("GLOSSARY",0,true);
    $co .= glossary_start_tag("INFO",1,true);
        $co .= glossary_full_tag("NAME",2,false,$glossary->name);
        $co .= glossary_full_tag("INTRO",2,false,$glossary->intro);
        $co .= glossary_full_tag("ALLOWDUPLICATEDENTRIES",2,false,$glossary->allowduplicatedentries);
        $co .= glossary_full_tag("DISPLAYFORMAT",2,false,$glossary->displayformat);
        $co .= glossary_full_tag("SHOWSPECIAL",2,false,$glossary->showspecial);
        $co .= glossary_full_tag("SHOWALPHABET",2,false,$glossary->showalphabet);
        $co .= glossary_full_tag("SHOWALL",2,false,$glossary->showall);
        $co .= glossary_full_tag("ALLOWCOMMENTS",2,false,$glossary->allowcomments);
        $co .= glossary_full_tag("USEDYNALINK",2,false,$glossary->usedynalink);
        $co .= glossary_full_tag("DEFAULTAPPROVAL",2,false,$glossary->defaultapproval);
        $co .= glossary_full_tag("GLOBALGLOSSARY",2,false,$glossary->globalglossary);
        $co .= glossary_full_tag("ENTBYPAGE",2,false,$glossary->entbypage);

        if ( $entries = get_records("glossary_entries","glossaryid",$glossary->id) ) {
            $co .= glossary_start_tag("ENTRIES",2,true);
            foreach ($entries as $entry) {
                $permissiongranted = 1;
                if ( $hook ) {
                    switch ( $hook ) {
                    case "ALL":
                    case "SPECIAL":
                    break;
                    default:
                        $permissiongranted = ($entry->concept[ strlen($hook)-1 ] == $hook);
                    break;
                    }
                }
                if ( $hook ) {
                    switch ( $hook ) {
                    case GLOSSARY_SHOW_ALL_CATEGORIES:
                    break;
                    case GLOSSARY_SHOW_NOT_CATEGORISED:
                        $permissiongranted = !record_exists("glossary_entries_categories","entryid",$entry->id);
                    break;
                    default:
                        $permissiongranted = record_exists("glossary_entries_categories","entryid",$entry->id, "categoryid",$hook);
                    break;
                    }
                }
                if ( $entry->approved and $permissiongranted ) {
                    $co .= glossary_start_tag("ENTRY",3,true);
                    $co .= glossary_full_tag("CONCEPT",4,false,trim($entry->concept));
                    $co .= glossary_full_tag("DEFINITION",4,false,trusttext_strip($entry->definition));
                    $co .= glossary_full_tag("FORMAT",4,false,$entry->format);
                    $co .= glossary_full_tag("USEDYNALINK",4,false,$entry->usedynalink);
                    $co .= glossary_full_tag("CASESENSITIVE",4,false,$entry->casesensitive);
                    $co .= glossary_full_tag("FULLMATCH",4,false,$entry->fullmatch);
                    $co .= glossary_full_tag("TEACHERENTRY",4,false,$entry->teacherentry);

                    if ( $aliases = get_records("glossary_alias","entryid",$entry->id) ) {
                        $co .= glossary_start_tag("ALIASES",4,true);
                        foreach ($aliases as $alias) {
                            $co .= glossary_start_tag("ALIAS",5,true);
                                $co .= glossary_full_tag("NAME",6,false,trim($alias->alias));
                            $co .= glossary_end_tag("ALIAS",5,true);
                        }
                        $co .= glossary_end_tag("ALIASES",4,true);
                    }
                    if ( $catentries = get_records("glossary_entries_categories","entryid",$entry->id) ) {
                        $co .= glossary_start_tag("CATEGORIES",4,true);
                        foreach ($catentries as $catentry) {
                            $category = get_record("glossary_categories","id",$catentry->categoryid);

                            $co .= glossary_start_tag("CATEGORY",5,true);
                                $co .= glossary_full_tag("NAME",6,false,$category->name);
                                $co .= glossary_full_tag("USEDYNALINK",6,false,$category->usedynalink);
                            $co .= glossary_end_tag("CATEGORY",5,true);
                        }
                        $co .= glossary_end_tag("CATEGORIES",4,true);
                    }

                    $co .= glossary_end_tag("ENTRY",3,true);
                }
            }
            $co .= glossary_end_tag("ENTRIES",2,true);

        }


    $co .= glossary_end_tag("INFO",1,true);
    $co .= glossary_end_tag("GLOSSARY",0,true);

    return $co;
}
/// Functions designed by Eloy Lafuente
/// Functions to create, open and write header of the xml file

// Read import file and convert to current charset
function glossary_read_imported_file($file) {
    require_once "../../lib/xmlize.php";
    global $CFG;

    $h = fopen($file,"r");
    $line = '';
    if ($h) {
        while ( !feof($h) ) {
           $char = fread($h,1024);
           $line .= $char;
        }
        fclose($h);
    }
    return xmlize($line, 0);
}

//Return the xml start tag
function glossary_start_tag($tag,$level=0,$endline=false) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."<".strtoupper($tag).">".$endchar;
}

//Return the xml end tag
function glossary_end_tag($tag,$level=0,$endline=true) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."</".strtoupper($tag).">".$endchar;
}

//Return the start tag, the contents and the end tag
function glossary_full_tag($tag,$level=0,$endline=true,$content) {
        global $CFG;

        $st = glossary_start_tag($tag,$level,$endline);
        $co = preg_replace("/\r\n|\r/", "\n", s($content));
        $et = glossary_end_tag($tag,0,true);
        return $st.$co.$et;
}

/*
* Adding grading functions
*/

function glossary_get_ratings($entryid, $sort="u.firstname ASC") {
/// Returns a list of ratings for a particular entry - sorted.
    global $CFG;
    return get_records_sql("SELECT u.*, r.rating, r.time
                              FROM {$CFG->prefix}glossary_ratings r,
                                   {$CFG->prefix}user u
                             WHERE r.entryid = '$entryid'
                               AND r.userid = u.id
                             ORDER BY $sort");
}

function glossary_count_unrated_entries($glossaryid, $userid) {
// How many unrated entries are in the given glossary for a given user?
    global $CFG;
    if ($entries = get_record_sql("SELECT count(*) as num
                                   FROM {$CFG->prefix}glossary_entries
                                  WHERE glossaryid = '$glossaryid'
                                    AND userid <> '$userid' ")) {

        if ($rated = get_record_sql("SELECT count(*) as num
                                       FROM {$CFG->prefix}glossary_entries e,
                                            {$CFG->prefix}glossary_ratings r
                                      WHERE e.glossaryid = '$glossaryid'
                                        AND e.id = r.entryid
                                        AND r.userid = '$userid'")) {
            $difference = $entries->num - $rated->num;
            if ($difference > 0) {
                return $difference;
            } else {
                return 0;    // Just in case there was a counting error
            }
        } else {
            return $entries->num;
        }
    } else {
        return 0;
    }
}

function glossary_print_ratings_mean($entryid, $scale) {
/// Print the multiple ratings on a entry given to the current user by others.
/// Scale is an array of ratings

    static $strrate;

    $mean = glossary_get_ratings_mean($entryid, $scale);

    if ($mean !== "") {

        if (empty($strratings)) {
            $strratings = get_string("ratings", "glossary");
        }

        echo "$strratings: ";
        link_to_popup_window ("/mod/glossary/report.php?id=$entryid", "ratings", $mean, 400, 600);
    }
}


function glossary_get_ratings_mean($entryid, $scale, $ratings=NULL) {
/// Return the mean rating of a entry given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("glossary_ratings", "entryid", $entryid)) {
            foreach ($rates as $rate) {
                $ratings[] = $rate->rating;
            }
        }
    }

    $count = count($ratings);

    if ($count == 0) {
        return "";

    } else if ($count == 1) {
        return $scale[$ratings[0]];

    } else {
        $total = 0;
        foreach ($ratings as $rating) {
            $total += $rating;
        }
        $mean = round( ((float)$total/(float)$count) + 0.001);  // Little fudge factor so that 0.5 goes UP

        if (isset($scale[$mean])) {
            return $scale[$mean]." ($count)";
        } else {
            return "$mean ($count)";    // Should never happen, hopefully
        }
    }
}

function glossary_get_ratings_summary($entryid, $scale, $ratings=NULL) {
/// Return a summary of entry ratings given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("glossary_ratings", "entryid", $entryid)) {
            foreach ($rates as $rate) {
                $rating[] = $rate->rating;
            }
        }
    }


    if (!$count = count($ratings)) {
        return "";
    }


    foreach ($scale as $key => $scaleitem) {
        $sumrating[$key] = 0;
    }

    foreach ($ratings as $rating) {
        $sumrating[$rating]++;
    }

    $summary = "";
    foreach ($scale as $key => $scaleitem) {
        $summary = $sumrating[$key].$summary;
        if ($key > 1) {
            $summary = "/$summary";
        }
    }
    return $summary;
}

function glossary_print_rating_menu($entryid, $userid, $scale) {
/// Print the menu of ratings as part of a larger form.
/// If the entry has already been - set that value.
/// Scale is an array of ratings

    static $strrate;

    if (!$rating = get_record("glossary_ratings", "userid", $userid, "entryid", $entryid)) {
        $rating->rating = -999;
    }

    if (empty($strrate)) {
        $strrate = get_string("rate", "glossary");
    }

    choose_from_menu($scale, $entryid, $rating->rating, "$strrate...",'',-999);
}


function glossary_get_paging_bar($totalcount, $page, $perpage, $baseurl, $maxpageallowed=99999, $maxdisplay=20, $separator="&nbsp;", $specialtext="", $specialvalue=-1, $previousandnext = true) {
// Returns the html code to represent any pagging bar. Paramenters are:
//
//  Mandatory:
//     $totalcount: total number of records to be displayed
//     $page: page currently selected (0 based)
//     $perpage: number of records per page
//     $baseurl: url to link in each page, the string 'page=XX' will be added automatically.
//  Optional:
//     $maxpageallowed: maximum number of page allowed.
//     $maxdisplay: maximum number of page links to show in the bar
//     $separator: string to be used between pages in the bar
//     $specialtext: string to be showed as an special link
//     $specialvalue: value (page) to be used in the special link
//     $previousandnext: to decide if we want the previous and next links
//
// The function dinamically show the first and last pages, and "scroll" over pages.
// Fully compatible with Moodle's print_paging_bar() function. Perhaps some day this
// could replace the general one. ;-)

    $code = '';

    $showspecial = false;
    $specialselected = false;

    //Check if we have to show the special link
    if (!empty($specialtext)) {
        $showspecial = true;
    }
    //Check if we are with the special link selected
    if ($showspecial && $page == $specialvalue) {
        $specialselected = true;
    }

    //If there are results (more than 1 page)
    if ($totalcount > $perpage) {
        $code .= "<div style=\"text-align:center\">";
        $code .= "<p>".get_string("page").":";

        $maxpage = (int)(($totalcount-1)/$perpage);

        //Lower and upper limit of page
        if ($page < 0) {
            $page = 0;
        }
        if ($page > $maxpageallowed) {
            $page = $maxpageallowed;
        }
        if ($page > $maxpage) {
            $page = $maxpage;
        }

        //Calculate the window of pages
        $pagefrom = $page - ((int)($maxdisplay / 2));
        if ($pagefrom < 0) {
            $pagefrom = 0;
        }
        $pageto = $pagefrom + $maxdisplay - 1;
        if ($pageto > $maxpageallowed) {
            $pageto = $maxpageallowed;
        }
        if ($pageto > $maxpage) {
            $pageto = $maxpage;
        }

        //Some movements can be necessary if don't see enought pages
        if ($pageto - $pagefrom < $maxdisplay - 1) {
            if ($pageto - $maxdisplay + 1 > 0) {
                $pagefrom = $pageto - $maxdisplay + 1;
            }
        }

        //Calculate first and last if necessary
        $firstpagecode = '';
        $lastpagecode = '';
        if ($pagefrom > 0) {
            $firstpagecode = "$separator<a href=\"{$baseurl}page=0\">1</a>";
            if ($pagefrom > 1) {
                $firstpagecode .= "$separator...";
            }
        }
        if ($pageto < $maxpage) {
            if ($pageto < $maxpage -1) {
                $lastpagecode = "$separator...";
            }
            $lastpagecode .= "$separator<a href=\"{$baseurl}page=$maxpage\">".($maxpage+1)."</a>";
        }

        //Previous
        if ($page > 0 && $previousandnext) {
            $pagenum = $page - 1;
            $code .= "&nbsp;(<a  href=\"{$baseurl}page=$pagenum\">".get_string("previous")."</a>)&nbsp;";
        }

        //Add first
        $code .= $firstpagecode;

        $pagenum = $pagefrom;

        //List of maxdisplay pages
        while ($pagenum <= $pageto) {
            $pagetoshow = $pagenum +1;
            if ($pagenum == $page && !$specialselected) {
                $code .= "$separator<b>$pagetoshow</b>";
            } else {
                $code .= "$separator<a href=\"{$baseurl}page=$pagenum\">$pagetoshow</a>";
            }
            $pagenum++;
        }

        //Add last
        $code .= $lastpagecode;

        //Next
        if ($page < $maxpage && $page < $maxpageallowed && $previousandnext) {
            $pagenum = $page + 1;
            $code .= "$separator(<a href=\"{$baseurl}page=$pagenum\">".get_string("next")."</a>)";
        }

        //Add special
        if ($showspecial) {
            $code .= '<br />';
            if ($specialselected) {
                $code .= "<b>$specialtext</b>";
            } else {
                $code .= "$separator<a href=\"{$baseurl}page=$specialvalue\">$specialtext</a>";
            }
        }

        //End html
        $code .= "</p>";
        $code .= "</div>";
    }

    return $code;
}

function glossary_get_view_actions() {
    return array('view','view all','view entry');
}

function glossary_get_post_actions() {
    return array('add category','add comment','add entry','approve entry','delete category','delete comment','delete entry','edit category','update comment','update entry');
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the glossary.
 * @param $mform form passed by reference
 */
function glossary_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'glossaryheader', get_string('modulenameplural', 'glossary'));
    $mform->addElement('checkbox', 'reset_glossary_all', get_string('resetglossariesall','glossary'));

    $mform->addElement('select', 'reset_glossary_types', get_string('resetglossaries', 'glossary'),
                       array('main'=>get_string('mainglossary', 'glossary'), 'secondary'=>get_string('secondaryglossary', 'glossary')), array('multiple' => 'multiple'));
    $mform->setAdvanced('reset_glossary_types');
    $mform->disabledIf('reset_glossary_types', 'reset_glossary_all', 'checked');

    $mform->addElement('checkbox', 'reset_glossary_notenrolled', get_string('deletenotenrolled', 'glossary'));
    $mform->disabledIf('reset_glossary_notenrolled', 'reset_glossary_all', 'checked');

    $mform->addElement('checkbox', 'reset_glossary_ratings', get_string('deleteallratings'));
    $mform->disabledIf('reset_glossary_ratings', 'reset_glossary_all', 'checked');

    $mform->addElement('checkbox', 'reset_glossary_comments', get_string('deleteallcomments'));
    $mform->disabledIf('reset_glossary_comments', 'reset_glossary_all', 'checked');
}

/**
 * Course reset form defaults.
 */
function glossary_reset_course_form_defaults($course) {
    return array('reset_glossary_all'=>0, 'reset_glossary_ratings'=>1, 'reset_glossary_comments'=>1, 'reset_glossary_notenrolled'=>0);
}

/**
 * Removes all grades from gradebook
 * @param int $courseid
 * @param string optional type
 */
function glossary_reset_gradebook($courseid, $type='') {
    global $CFG;

    switch ($type) {
        case 'main'      : $type = "AND g.mainglossary=1"; break;
        case 'secondary' : $type = "AND g.mainglossary=0"; break;
        default          : $type = ""; //all
    }

    $sql = "SELECT g.*, cm.idnumber as cmidnumber, g.course as courseid
              FROM {$CFG->prefix}glossary g, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
             WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id AND g.course=$courseid $type";

    if ($glossarys = get_records_sql($sql)) {
        foreach ($glossarys as $glossary) {
            glossary_grade_item_update($glossary, 'reset');
        }
    }
}
/**
 * Actual implementation of the rest coures functionality, delete all the
 * glossary responses for course $data->courseid.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function glossary_reset_userdata($data) {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');

    $componentstr = get_string('modulenameplural', 'glossary');
    $status = array();

    $allentriessql = "SELECT e.id
                        FROM {$CFG->prefix}glossary_entries e
                             INNER JOIN {$CFG->prefix}glossary g ON e.glossaryid = g.id
                       WHERE g.course = {$data->courseid}";

    $allglossariessql = "SELECT g.id
                            FROM {$CFG->prefix}glossary g
                           WHERE g.course={$data->courseid}";

    // delete entries if requested
    if (!empty($data->reset_glossary_all)
         or (!empty($data->reset_glossary_types) and in_array('main', $data->reset_glossary_types) and in_array('secondary', $data->reset_glossary_types))) {

        delete_records_select('glossary_ratings', "entryid IN ($allentriessql)");
        delete_records_select('glossary_comments', "entryid IN ($allentriessql)");
        delete_records_select('glossary_entries', "glossaryid IN ($allglossariessql)");

        if ($glossaries = get_records_sql($allglossariessql)) {
            foreach ($glossaries as $glossaryid=>$unused) {
                fulldelete($CFG->dataroot."/$data->courseid/moddata/glossary/$glossaryid");
            }
        }

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            glossary_reset_gradebook($data->courseid);
        }

        $status[] = array('component'=>$componentstr, 'item'=>get_string('resetglossariesall', 'glossary'), 'error'=>false);

    } else if (!empty($data->reset_glossary_types)) {
        $mainentriessql         = "$allentries AND g.mainglossary=1";
        $secondaryentriessql    = "$allentries AND g.mainglossary=0";

        $mainglossariessql      = "$allglossariessql AND g.mainglossary=1";
        $secondaryglossariessql = "$allglossariessql AND g.mainglossary=0";

        if (in_array('main', $data->reset_glossary_types)) {
            delete_records_select('glossary_ratings', "entryid IN ($mainentriessql)");
            delete_records_select('glossary_comments', "entryid IN ($mainentriessql)");
            delete_records_select('glossary_entries', "glossaryid IN ($mainglossariessql)");

            if ($glossaries = get_records_sql($mainglossariessql)) {
                foreach ($glossaries as $glossaryid=>$unused) {
                    fulldelete("$CFG->dataroot/$data->courseid/moddata/glossary/$glossaryid");
                }
            }

            // remove all grades from gradebook
            if (empty($data->reset_gradebook_grades)) {
                glossary_reset_gradebook($data->courseid, 'main');
            }

            $status[] = array('component'=>$componentstr, 'item'=>get_string('resetglossaries', 'glossary'), 'error'=>false);

        } else if (in_array('secondary', $data->reset_glossary_types)) {
            delete_records_select('glossary_ratings', "entryid IN ($secondaryentriessql)");
            delete_records_select('glossary_comments', "entryid IN ($secondaryentriessql)");
            delete_records_select('glossary_entries', "glossaryid IN ($secondaryglossariessql)");
            // remove exported source flag from entries in main glossary
            execute_sql("UPDATE {$CFG->prefix}glossary_entries
                            SET sourceglossaryid=0
                          WHERE glossaryid IN ($mainglossariessql)", false);

            if ($glossaries = get_records_sql($secondaryglossariessql)) {
                foreach ($glossaries as $glossaryid=>$unused) {
                    fulldelete("$CFG->dataroot/$data->courseid/moddata/glossary/$glossaryid");
                }
            }

            // remove all grades from gradebook
            if (empty($data->reset_gradebook_grades)) {
                glossary_reset_gradebook($data->courseid, 'secondary');
            }

            $status[] = array('component'=>$componentstr, 'item'=>get_string('resetglossaries', 'glossary').': '.get_string('secondaryglossary', 'glossary'), 'error'=>false);
        }
    }

    // remove entries by users not enrolled into course
    if (!empty($data->reset_glossary_notenrolled)) {
        $entriessql = "SELECT e.id, e.userid, e.glossaryid, u.id AS userexists, u.deleted AS userdeleted
                         FROM {$CFG->prefix}glossary_entries e
                              INNER JOIN {$CFG->prefix}glossary g ON e.glossaryid = g.id
                              LEFT OUTER JOIN {$CFG->prefix}user u ON e.userid = u.id
                        WHERE g.course = {$data->courseid} AND e.userid > 0";

        $course_context = get_context_instance(CONTEXT_COURSE, $data->courseid);
        $notenrolled = array();
        if ($rs = get_recordset_sql($entriessql)) {
            while ($entry = rs_fetch_next_record($rs)) {
                if (array_key_exists($entry->userid, $notenrolled) or !$entry->userexists or $entry->userdeleted
                  or !has_capability('moodle/course:view', $course_context , $entry->userid)) {
                    delete_records('glossary_ratings', 'entryid', $entry->id);
                    delete_records('glossary_comments', 'entryid', $entry->id);
                    delete_records('glossary_entries', 'id', $entry->id);
                    fulldelete("$CFG->dataroot/$data->courseid/moddata/glossary/$entry->glossaryid");
                    $notenrolled[$entry->userid] = true;
                }
            }
            rs_close($rs);
            $status[] = array('component'=>$componentstr, 'item'=>get_string('deletenotenrolled', 'glossary'), 'error'=>false);
        }
    }

    // remove all ratings
    if (!empty($data->reset_glossary_ratings)) {
        delete_records_select('glossary_ratings', "entryid IN ($allentriessql)");
        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            glossary_reset_gradebook($data->courseid);
        }
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallratings'), 'error'=>false);
    }

    // remove all comments
    if (!empty($data->reset_glossary_comments)) {
        delete_records_select('glossary_comments', "entryid IN ($allentriessql)");
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallcomments'), 'error'=>false);
    }

    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('glossary', array('assesstimestart', 'assesstimefinish'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;
}

/**
 * Returns all other caps used in module
 */
function glossary_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames', 'moodle/site:trustcontent');
}

?>
