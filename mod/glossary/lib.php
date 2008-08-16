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
    global $DB;
/// Given an object containing all the necessary data,
/// (defined by the form in mod_form.php) this function
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
        print_error('unknowformat', '', '', $glossary->displayformat);
    }

    if ($returnid = $DB->insert_record("glossary", $glossary)) {
        $glossary->id = $returnid;
        glossary_grade_item_update($glossary);
    }

    return $returnid;
}


function glossary_update_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod_form.php) this function
/// will update an existing instance with new data.
    global $CFG, $DB;

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
        print_error('unknowformat', '', '', $glossary->displayformat);
    }

    if ($return = $DB->update_record("glossary", $glossary)) {
        if ($glossary->defaultapproval) {
            $DB->execute("UPDATE {glossary_entries} SET approved = 1 where approved <> 1 and glossaryid = ?", array($glossary->id));
        }
        glossary_grade_item_update($glossary);
    }

    return $return;
}


function glossary_delete_instance($id) {
    global $DB;
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $glossary = $DB->get_record("glossary", array("id"=>"$id"))) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! $DB->delete_records("glossary", array("id"=>$glossary->id))) {
        $result = false;
    } else {
        if ($categories = $DB->get_records("glossary_categories", array("glossaryid"=>$glossary->id))) {
            $cats = "";
            foreach ( $categories as $cat ) {
                $cats .= "$cat->id,";
            }
            $cats = substr($cats,0,-1);
            if ($cats) {
                $DB->delete_records_select("glossary_entries_categories", "categoryid in ($cats)");
                $DB->delete_records("glossary_categories", array("glossaryid"=>$glossary->id));
            }
        }
        if ( $entries = $DB->get_records("glossary_entries", array("glossaryid"=>$glossary->id))) {
            $ents = "";
            foreach ( $entries as $entry ) {
                if ( $entry->sourceglossaryid ) {
                    $entry->glossaryid = $entry->sourceglossaryid;
                    $entry->sourceglossaryid = 0;
                    $DB->update_record("glossary_entries",$entry);
                } else {
                    $ents .= "$entry->id,";
                }
            }
            $ents = substr($ents,0,-1);
            if ($ents) {
                $DB->delete_records_select("glossary_comments", "entryid in ($ents)");
                $DB->delete_records_select("glossary_alias", "entryid in ($ents)");
                $DB->delete_records_select("glossary_ratings", "entryid in ($ents)");
            }
        }
        glossary_delete_attachments($glossary);
        $DB->delete_records("glossary_entries", array("glossaryid"=>$glossary->id));
    }
    glossary_grade_item_delete($glossary);

    return $result;
}

function glossary_user_outline($course, $user, $mod, $glossary) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    if ($entries = glossary_get_user_entries($glossary->id, $user->id)) {
        $result = new object();
        $result->info = count($entries) . ' ' . get_string("entries", "glossary");

        $lastentry = array_pop($entries);
        $result->time = $lastentry->timemodified;
        return $result;
    }
    return NULL;
}

function glossary_get_user_entries($glossaryid, $userid) {
/// Get all the entries for a user in a glossary
    global $DB;

    return $DB->get_records_sql("SELECT e.*, u.firstname, u.lastname, u.email, u.picture
                                   FROM {glossary} g, {glossary_entries} e, {user} u
                             WHERE g.id = ?
                               AND e.glossaryid = g.id
                               AND e.userid = ?
                               AND e.userid = u.id
                          ORDER BY e.timemodified ASC", array($glossaryid, $userid));
}

function glossary_user_complete($course, $user, $mod, $glossary) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.
    global $CFG;

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

    global $CFG, $USER, $DB;

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

    if (!$entries = $DB->get_records_sql("SELECT ge.id, ge.concept, ge.approved, ge.timemodified, ge.glossaryid,
                                                 ge.userid, u.firstname, u.lastname, u.email, u.picture
                                            FROM {glossary_entries} ge
                                            JOIN {user} u ON u.id = ge.userid
                                           WHERE ge.glossaryid IN ($glist) AND ge.timemodified > ?
                                        ORDER BY ge.timemodified ASC", array($timestart))) {
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
    global $DB;

    return $DB->get_record_sql("SELECT e.*, u.firstname, u.lastname
                                  FROM {glossary_entries} e, {user} u
                                 WHERE e.id = ? AND u.id = ?", array($log->info, $log->userid));
}

function glossary_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...
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
    global $DB;

    $params = array('userid'=>$userid, 'gid'=>$glossary->id);

    $user = $userid ? "AND u.id = :userid" : "";

    $sql = "SELECT u.id, u.id AS userid, avg(gr.rating) AS rawgrade
              FROM {user} u, {glossary_entries} ge, {glossary_ratings} gr
             WHERE u.id = ge.userid AND ge.id = gr.entryid
                   AND gr.userid != u.id AND ge.glossaryid = :gid
                   $user
          GROUP BY u.id";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Update activity grades
 *
 * @param object $glossary null means all glossaries
 * @param int $userid specific user only, 0 means all
 */
function glossary_update_grades($glossary=null, $userid=0, $nullifnone=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if (!$glossary->assessed) {
        glossary_grade_item_update($glossary);

    } else if ($grades = glossary_get_user_grades($glossary, $userid)) {
        glossary_grade_item_update($glossary, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new object();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        glossary_grade_item_update($glossary, $grade);

    } else {
        glossary_grade_item_update($glossary);
    }
}

/**
 * Update all grades in gradebook.
 */
function glossary_upgrade_grades() {
    global $DB;

    $sql = "SELECT COUNT('x')
              FROM {glossary} g, {course_modules} cm, {modules} m
             WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id";
    $count = $DB->count_records_sql($sql);

    $sql = "SELECT g.*, cm.idnumber AS cmidnumber, g.course AS courseid
              FROM {glossary} g, {course_modules} cm, {modules} m
             WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id";
    if ($rs = $DB->get_recordset_sql($sql)) {
        $prevdebug = $DB->get_debug();
        $DB->set_debug(false);
        $pbar = new progress_bar('glossaryupgradegrades', 500, true);
        $i=0;
        foreach ($rs as $glossary) {
            $i++;
            upgrade_set_timeout(60*5); // set up timeout, may also abort execution
            glossary_update_grades($glossary, 0, false);
            $pbar->update($i, $count, "Updating Glossary grades ($i/$count).");
        }
        $DB->set_debug($prevdebug);
        $rs->close();
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
    require_once($CFG->libdir.'/gradelib.php');

    if(!empty($glossary->cmidnumber)){
        $params = array('itemname'=>$glossary->name, 'idnumber'=>$glossary->cmidnumber);
    }else{
        // MDL-14303
        $cm = get_coursemodule_from_instance('glossary', $glossary->id);
        $params = array('itemname'=>$glossary->name, 'idnumber'=>$cm->id);
    }

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
    global $DB;

    //Get students
    $students = $DB->get_records_sql("SELECT DISTINCT u.id, u.id
                                        FROM {user} u, {glossary_entries} g
                                 WHERE g.glossaryid = ? AND u.id = g.userid", array($glossaryid));

    //Return students array (it contains an array of unique users)
    return $students;
}

function glossary_scale_used ($glossaryid,$scaleid) {
//This function returns if a scale is being used by one glossary
    global $DB;

    $return = false;

    $rec = $DB->get_record("glossary", array("id"=>$glossaryid, "scale"=>-$scaleid));

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
    global $DB;

    if ($scaleid and $DB->record_exists('glossary', array('scale'=>-$scaleid))) {
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
    global $CFG, $DB;

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
                if (!$rec = $DB->get_record('glossary_formats', array('name'=>$format))) {
                    //Insert the record in glossary_formats
                    $gf = new object();
                    $gf->name = $format;
                    $gf->popupformatname = $format;
                    $gf->visible = 1;
                    $DB->insert_record("glossary_formats",$gf);
                }
            }
        }
    }

    //Delete non_existent formats from glossary_formats table
    $formats = $DB->get_records("glossary_formats");
    foreach ($formats as $format) {
        $todelete = false;
        //If the format in DB isn't a valid previously detected format then delete the record
        if (!in_array($format->name,$pluginformats)) {
            $todelete = true;
        }

        if ($todelete) {
            //Delete the format
            $DB->delete_records('glossary_formats', array('name'=>$format->name));
            //Reasign existing glossaries to default (dictionary) format
            if ($glossaries = $DB->get_records('glossary', array('displayformat'=>$format->name))) {
                foreach($glossaries as $glossary) {
                    $DB->set_field('glossary','displayformat','dictionary', array('id'=>$glossary->id));
                }
            }
        }
    }

    //Now everything is ready in glossary_formats table
    $formats = $DB->get_records("glossary_formats");

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
    global $DB;
    if ($pivot) {
       $pivot .= ",";
    }

    return $DB->get_records_sql("SELECT $pivot id,userid,concept,definition,format
                                   FROM {glossary_entries}
                                  WHERE glossaryid = ?
                                        AND id IN ($entrylist)", array($glossaryid));
}

function glossary_get_entries_search($concept, $courseid) {
    global $CFG, $DB;

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

    $params = array('courseid1'=>$courseid, 'courseid2'=>$courseid, 'conceptlower'=>$conceptlower, 'concept'=>$concept);

    return $DB->get_records_sql("SELECT e.*, g.name as glossaryname, cm.id as cmid, cm.course as courseid
                                   FROM {glossary_entries} e, {glossary} g,
                                        {course_modules} cm, {modules} m
                                  WHERE m.name = 'glossary' AND
                                        cm.module = m.id AND
                                        (cm.visible = 1 OR  cm.visible = $bypassadmin OR
                                            (cm.course = :courseid1 AND cm.visible = $bypassteacher)) AND
                                        g.id = cm.instance AND
                                        e.glossaryid = g.id  AND
                                        ( (e.casesensitive != 0 AND LOWER(concept) = :conceptlower) OR
                                          (e.casesensitive = 0 and concept = :concept)) AND
                                        (g.course = courseid2 OR g.globalglossary = 1) AND
                                         e.usedynalink != 0 AND
                                         g.usedynalink != 0", $params);
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
    $text = format_text(print_heading($entry->concept, '', 3, 'nolink', true), FORMAT_MOODLE, $options);
    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }
    echo $text;
}

function glossary_print_entry_definition($entry) {
    global $DB;

    $definition = $entry->definition;

    // always detect and strip TRUSTTEXT marker before processing and add+strip it afterwards!
    if (trusttext_present($definition)) {
        $ttpresent = true;
        $definition = trusttext_strip($definition);
    } else {
        $ttpresent = false;
    }

    $links = array();
    $tags = array();
    $urls = array();
    $addrs = array();

    //Calculate all the strings to be no-linked
    //First, the concept
    $term = preg_quote(trim($entry->concept),'/');
    $pat = '/('.$term.')/is';
    $doNolinks[] = $pat;
    //Now the aliases
    if ( $aliases = $DB->get_records('glossary_alias', array('entryid'=>$entry->id))) {
        foreach ($aliases as $alias) {
            $term = preg_quote(trim($alias->alias),'/');
            $pat = '/('.$term.')/is';
            $doNolinks[] = $pat;
        }
    }


    //Extract <a>..><a> tags from definition
    preg_match_all('/<a\s[^>]+?>(.*?)<\/a>/is',$definition,$list_of_a);

    //Save them into links array to use them later
    foreach (array_unique($list_of_a[0]) as $key=>$value) {
        $links['<#'.$key.'#>'] = $value;
    }
    //Take off every link from definition
    if ( $links ) {
        $definition = str_replace($links,array_keys($links),$definition);
    }


    //Extract all tags from definition
    preg_match_all('/(<.*?>)/is',$definition,$list_of_tags);

    //Save them into tags array to use them later
    foreach (array_unique($list_of_tags[0]) as $key=>$value) {
        $tags['<@'.$key.'@>'] = $value;
    }
    //Take off every tag from definition
    if ( $tags ) {
        $definition = str_replace($tags,array_keys($tags),$definition);
    }


    //Extract all URLS with protocol (http://domain.com) from definition
    preg_match_all('/([[:space:]]|^|\(|\[)([[:alnum:]]+):\/\/([^[:space:]]*)([[:alnum:]#?\/&=])/is',$definition,$list_of_urls);

    //Save them into urls array to use them later
    foreach (array_unique($list_of_urls[0]) as $key=>$value) {
        $urls['<*'.$key.'*>'] = $value;
    }
    //Take off every url from definition
    if ( $urls ) {
        $definition = str_replace($urls,array_keys($urls),$definition);
    }


    //Extract all WEB ADDRESSES (www.domain.com) from definition
    preg_match_all('/([[:space:]]|^|\(|\[)www\.([^[:space:]]*)([[:alnum:]#?\/&=])/is',$definition,$list_of_addresses);

    //Save them into addrs array to use them later
    foreach (array_unique($list_of_addresses[0]) as $key=>$value) {
        $addrs['<+'.$key.'+>'] = $value;
    }
    //Take off every addr from definition
    if ( $addrs ) {
        $definition = str_replace($addrs,array_keys($addrs),$definition);
    }


    //Put doNolinks (concept + aliases) enclosed by <nolink> tag
    $definition= preg_replace($doNolinks,'<span class="nolink">$1</span>',$definition);

    //Restore addrs
    if ( $addrs ) {
        $definition = str_replace(array_keys($addrs),$addrs,$definition);
    }

    //Restore urls
    if ( $urls ) {
        $definition = str_replace(array_keys($urls),$urls,$definition);
    }

    //Restore tags
    if ( $tags ) {
        $definition = str_replace(array_keys($tags),$tags,$definition);
    }

    //Restore links
    if ( $links ) {
        $definition = str_replace(array_keys($links),$links,$definition);
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
    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }
    if (isset($entry->footer)) {   // Unparsed footer info
        $text .= $entry->footer;
    }
    echo $text;
}

function  glossary_print_entry_aliases($course, $cm, $glossary, $entry,$mode='',$hook='', $type = 'print') {
    global $DB;

    $return = '';
    if ( $aliases = $DB->get_records('glossary_alias', array('entryid'=>$entry->id))) {
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
    global $USER, $CFG, $DB;

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
            $mainglossary = $DB->get_record('glossary', array('mainglossary'=>1,'course'=>$course->id));
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
    if (true) { // @todo penny add capability check
        require_once($CFG->libdir . '/portfoliolib.php');
        $p = array(
            'id' => $cm->id,
            'entryid' => $entry->id,
        );
        $return .= portfolio_add_button('glossary_entry_portfolio_caller', $p, null, PORTFOLIO_ADD_ICON_LINK, null, true);
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
    global $DB;

    $return = '';

    $count = $DB->count_records('glossary_comments', array('entryid'=>$entry->id));
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
    global $DB;

    if ($entry->attachment) {
          $glossary = $DB->get_record("glossary", array("id"=>$entry->glossaryid));
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
        echo '<a title="'.get_string('approve','glossary').'" href="approve.php?id='.$cm->id.'&amp;eid='.$entry->id.'&amp;mode='.$mode.'"><img align="'.$align.'" src="'.$CFG->pixpath.'/i/approve.gif" style="border:0px; width:34px; height:34px" alt="'.get_string('approve','glossary').'" /></a>';
        if ($insidetable) {
            echo '</td></tr></table>';
        }
    }
}

function glossary_search($course, $searchterms, $extended = 0, $glossary = NULL) {
// It returns all entries from all glossaries that matches the specified criteria
//    within a given $course. It performs an $extended search if necessary.
// It restrict the search to only one $glossary if the $glossary parameter is set.
    global $CFG, $DB;

    if ( !$glossary ) {
        if ( $glossaries = $DB->get_records("glossary", array("course"=>$course->id)) ) {
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
        $glossarymodule = $DB->get_record("modules", array("name"=>"glossary"));
        $onlyvisible = " AND g.id = cm.instance AND cm.visible = 1 AND cm.module = $glossarymodule->id";
        $onlyvisibletable = ", {course_modules} cm";
    } else {

        $onlyvisible = "";
        $onlyvisibletable = "";
    }

    if ($DB->sql_regex_supported()) {
        $REGEXP    = $DB->sql_regex(true);
        $NOTREGEXP = $DB->sql_regex(false);
    }
    $LIKE = $DB->sql_ilike(); // case-insensitive

    $searchcond = array();
    $params     = array();
    $i = 0;

    $concat = $DB->sql_concat('e.concept', "' '", 'e.definition');


    foreach ($searchterms as $searchterm) {
        $i++;

        $NOT = ''; /// Initially we aren't going to perform NOT LIKE searches, only MSSQL and Oracle
                   /// will use it to simulate the "-" operator with LIKE clause

    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE (or NOT LIKE) queries
        if (!$DB->sql_regex_supported()) {
            if (substr($searchterm, 0, 1) == '-') {
                $NOT = ' NOT ';
            }
            $searchterm = trim($searchterm, '+-');
        }

        // TODO: +- may not work for non latin languages

        if (substr($searchterm,0,1) == '+') {
            $searchterm = trim($searchterm, '+-');
            $searchterm = preg_quote($searchterm, '|');
            $searchcond[] = "$concat $REGEXP :ss$i";
            $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = trim($searchterm, '+-');
            $searchterm = preg_quote($searchterm, '|');
            $searchcond[] = "$concat $NOTREGEXP :ss$i";
            $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

        } else {
            $searchcond[] = "$concat $NOT $LIKE :ss$i";
            $params['ss'.$i] = "%$searchterm%";
        }
    }

    if (empty($searchcond)) {
        $totalcount = 0;
        return array();
    }

    $searchcond = implode(" AND ", $searchcond);

    $sql = "SELECT e.*
              FROM {glossary_entries} e, {glossary} g $onlyvisibletable
             WHERE $searchcond
               AND (e.glossaryid = g.id or e.sourceglossaryid = g.id) $onlyvisible
               AND g.id IN ($glos) AND e.approved <> 0";

    return $DB->get_records_sql($sql, $params);
}

function glossary_search_entries($searchterms, $glossary, $extended) {
    global $DB;

    $course = $DB->get_record("course", array("id"=>$glossary->course));
    return glossary_search($course,$searchterms,$extended,$glossary);
}

function glossary_file_area_name($entry) {
    global $CFG, $DB;
//  Creates a directory file name, suitable for make_upload_directory()

    // I'm doing this workaround for make it works for delete_instance also
    //  (when called from delete_instance, glossary is already deleted so
    //   getting the course from mdl_glossary does not work)
    $module = $DB->get_record("modules", array("name"=>"glossary"));
    $cm = $DB->get_record("course_modules", array("module"=>$module->id, "instance"=>$entry->glossaryid));
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
    global $DB;
// Deletes all the user files in the attachments area for the glossary
    if ( $entries = $DB->get_records("glossary_entries", array("glossaryid"=>$glossary->id))) {
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
    global $CFG, $DB;

    $return = true;

    if ($entries = $DB->get_records_select("glossary_entries", "id = ? AND attachment <> ''", array($entry->id))) {
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

    global $CFG, $DB;

    require_once($CFG->dirroot.'/lib/uploadlib.php');

    $return = true;

    if ($entries = $DB->get_records_select("glossary_entries", "glossaryid = ? AND attachment <> ''", array($entry->id))) {
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
     global $CFG, $DB;

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

     $categories = $DB->get_records("glossary_categories", array("glossaryid"=>$glossary->id), "name ASC");
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
    global $CFG, $USER, $DB;

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $user = $DB->get_record('user', array('id'=>$comment->userid));
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
    global $USER, $CFG, $DB;

    $glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid));
    $glossarymod = $DB->get_record('modules', array('name'=>'glossary'));
    $cm = $DB->get_record_sql("SELECT *
                                 FROM {course_modules}
                                WHERE course = ? AND module = ? and instance = ?", array($course->id, $glossarymod->id, $glossary->id));

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
    global $USER,$CFG, $DB;

    echo '<div class="boxaligncenter">';
    echo '<table class="glossarypopup" cellspacing="0"><tr>';
    echo '<td>';
    if ( $entries ) {
        foreach ( $entries as $entry ) {
            if (! $glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid))) {
                print_error('invalidid', 'glossary');
            }
            if (! $course = $DB->get_record('course', array('id'=>$glossary->course))) {
                print_error('coursemisconf');
            }
            if (!$cm = get_coursemodule_from_instance('glossary', $entry->glossaryid, $glossary->course) ) {
                print_error('invalidid', 'glossary');
            }

            //If displayformat is present, override glossary->displayformat
            if ($displayformat < 0) {
                $dp = $glossary->displayformat;
            } else {
                $dp = $displayformat;
            }

            //Get popupformatname
            $format = $DB->get_record('glossary_formats', array('name'=>$dp));
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

function glossary_generate_export_csv($entries, $aliases, $categories) {
    global $CFG;
    $csv = '';
    $delimiter = '';
    require_once($CFG->libdir . '/csvlib.class.php');
    $delimiter = csv_import_reader::get_delimiter('comma');
    $csventries = array(0 => array(get_string('concept', 'glossary'), get_string('definition', 'glossary')));
    $csvaliases = array(0 => array());
    $csvcategories = array(0 => array());
    $aliascount = 0;
    $categorycount = 0;

    foreach ($entries as $entry) {
        $thisaliasesentry = array();
        $thiscategoriesentry = array();
        $thiscsventry = array($entry->concept, nl2br(trusttext_strip($entry->definition)));

        if (array_key_exists($entry->id, $aliases) && is_array($aliases[$entry->id])) {
            $thiscount = count($aliases[$entry->id]);
            if ($thiscount > $aliascount) {
                $aliascount = $thiscount;
            }
            foreach ($aliases[$entry->id] as $alias) {
                $thisaliasesentry[] = trim($alias);
            }
        }
        if (array_key_exists($entry->id, $categories) && is_array($categories[$entry->id])) {
            $thiscount = count($categories[$entry->id]);
            if ($thiscount > $categorycount) {
                $categorycount = $thiscount;
            }
            foreach ($categories[$entry->id] as $catentry) {
                $thiscategoriesentry[] = trim($catentry);
            }
        }
        $csventries[$entry->id] = $thiscsventry;
        $csvaliases[$entry->id] = $thisaliasesentry;
        $csvcategories[$entry->id] = $thiscategoriesentry;

    }
    $returnstr = '';
    foreach ($csventries as $id => $row) {
        $aliasstr = '';
        $categorystr = '';
        if ($id == 0) {
            $aliasstr = get_string('alias', 'glossary');
            $categorystr = get_string('category', 'glossary');
        }
        $row = array_merge($row, array_pad($csvaliases[$id], $aliascount, $aliasstr), array_pad($csvcategories[$id], $categorycount, $categorystr));
        $returnstr .= '"' . implode('"' . $delimiter . '"', $row) . '"' . "\n";
    }
    return $returnstr;
}

function glossary_generate_export_file($glossary, $hook = "", $hook = 0) {
    global $CFG, $DB;

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

        if ( $entries = $DB->get_records("glossary_entries", array("glossaryid"=>$glossary->id))) {
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
                        $permissiongranted = !$DB->record_exists("glossary_entries_categories", array("entryid"=>$entry->id));
                    break;
                    default:
                        $permissiongranted = $DB->record_exists("glossary_entries_categories", array("entryid"=>$entry->id, "categoryid"=>$hook));
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

                    if ( $aliases = $DB->get_records("glossary_alias", array("entryid"=>$entry->id))) {
                        $co .= glossary_start_tag("ALIASES",4,true);
                        foreach ($aliases as $alias) {
                            $co .= glossary_start_tag("ALIAS",5,true);
                                $co .= glossary_full_tag("NAME",6,false,trim($alias->alias));
                            $co .= glossary_end_tag("ALIAS",5,true);
                        }
                        $co .= glossary_end_tag("ALIASES",4,true);
                    }
                    if ( $catentries = $DB->get_records("glossary_entries_categories", array("entryid"=>$entry->id))) {
                        $co .= glossary_start_tag("CATEGORIES",4,true);
                        foreach ($catentries as $catentry) {
                            $category = $DB->get_record("glossary_categories", array("id"=>$catentry->categoryid));

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
    global $DB;
    return $DB->get_records_sql("SELECT u.*, r.rating, r.time
                                   FROM {glossary_ratings} r, {user} u
                                  WHERE r.entryid = ? AND r.userid = u.id
                               ORDER BY $sort", array($entryid));
}

function glossary_count_unrated_entries($glossaryid, $userid) {
// How many unrated entries are in the given glossary for a given user?
    global $DB;
    if ($entries = $DB->get_record_sql("SELECT count('x') as num
                                          FROM {glossary_entries}
                                         WHERE glossaryid = ? AND userid <> ?", array($glossaryid, $userid))) {

        if ($rated = $DB->get_record_sql("SELECT count(*) as num
                                            FROM {glossary_entries} e, {glossary_ratings} r
                                           WHERE e.glossaryid = ? AND e.id = r.entryid
                                                 AND r.userid = ?", array($glossaryid, $userid))) {
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
    global $DB;

    if (!$ratings) {
        $ratings = array();
        if ($rates = $DB->get_records("glossary_ratings", array("entryid"=>$entryid))) {
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
    global $DB;

    if (!$ratings) {
        $ratings = array();
        if ($rates = $DB->get_records("glossary_ratings", array("entryid"=>$entryid))) {
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
    global $DB;

    static $strrate;

    if (!$rating = $DB->get_record("glossary_ratings", array("userid"=>$userid, "entryid"=>$entryid))) {
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
                $code .= "$separator$pagetoshow";
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
                $code .= $specialtext;
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
    global $DB;

    switch ($type) {
        case 'main'      : $type = "AND g.mainglossary=1"; break;
        case 'secondary' : $type = "AND g.mainglossary=0"; break;
        default          : $type = ""; //all
    }

    $sql = "SELECT g.*, cm.idnumber as cmidnumber, g.course as courseid
              FROM {glossary} g, {course_modules} cm, {modules} m
             WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id AND g.course=? $type";

    if ($glossarys = $DB->get_records_sql($sql, array($courseid))) {
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
    global $CFG, $DB;
    require_once($CFG->libdir.'/filelib.php');

    $componentstr = get_string('modulenameplural', 'glossary');
    $status = array();

    $allentriessql = "SELECT e.id
                        FROM {glossary_entries} e
                             JOIN {glossary} g ON e.glossaryid = g.id
                       WHERE g.course = ?";

    $allglossariessql = "SELECT g.id
                           FROM {glossary} g
                          WHERE g.course = ?";

    $params = array($data->courseid);

    // delete entries if requested
    if (!empty($data->reset_glossary_all)
         or (!empty($data->reset_glossary_types) and in_array('main', $data->reset_glossary_types) and in_array('secondary', $data->reset_glossary_types))) {

        $DB->delete_records_select('glossary_ratings', "entryid IN ($allentriessql)", $params);
        $DB->delete_records_select('glossary_comments', "entryid IN ($allentriessql)", $params);
        $DB->delete_records_select('glossary_entries', "glossaryid IN ($allglossariessql)", $params);

        if ($glossaries = $DB->get_records_sql($allglossariessql, $params)) {
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
            $DB->delete_records_select('glossary_ratings', "entryid IN ($mainentriessql)", $params);
            $DB->delete_records_select('glossary_comments', "entryid IN ($mainentriessql)", $params);
            $DB->delete_records_select('glossary_entries', "glossaryid IN ($mainglossariessql)", $params);

            if ($glossaries = $DB->get_records_sql($mainglossariessql, $params)) {
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
            $DB->delete_records_select('glossary_ratings', "entryid IN ($secondaryentriessql)", $params);
            $DB->delete_records_select('glossary_comments', "entryid IN ($secondaryentriessql)", $params);
            $DB->delete_records_select('glossary_entries', "glossaryid IN ($secondaryglossariessql)", $params);
            // remove exported source flag from entries in main glossary
            $DB->execute("UPDATE {glossary_entries
                             SET sourceglossaryid=0
                           WHERE glossaryid IN ($mainglossariessql)", $params);

            if ($glossaries = $DB->get_records_sql($secondaryglossariessql, $params)) {
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
                         FROM {glossary_entries} e
                              JOIN {glossary} g ON e.glossaryid = g.id
                              LEFT JOIN {user} u ON e.userid = u.id
                        WHERE g.course = ? AND e.userid > 0";

        $course_context = get_context_instance(CONTEXT_COURSE, $data->courseid);
        $notenrolled = array();
        if ($rs = $DB->get_recordset_sql($entriessql, $params)) {
            foreach ($rs as $entry) {
                if (array_key_exists($entry->userid, $notenrolled) or !$entry->userexists or $entry->userdeleted
                  or !has_capability('moodle/course:view', $course_context , $entry->userid)) {
                    $DB->delete_records('glossary_ratings', array('entryid'=>$entry->id));
                    $DB->delete_records('glossary_comments', array('entryid'=>$entry->id));
                    $DB->delete_records('glossary_entries', array('id'=>$entry->id));
                    fulldelete("$CFG->dataroot/$data->courseid/moddata/glossary/$entry->glossaryid");
                    $notenrolled[$entry->userid] = true;
                }
            }
            $rs->close();
            $status[] = array('component'=>$componentstr, 'item'=>get_string('deletenotenrolled', 'glossary'), 'error'=>false);
        }
    }

    // remove all ratings
    if (!empty($data->reset_glossary_ratings)) {
        $DB->delete_records_select('glossary_ratings', "entryid IN ($allentriessql)", $params);
        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            glossary_reset_gradebook($data->courseid);
        }
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallratings'), 'error'=>false);
    }

    // remove all comments
    if (!empty($data->reset_glossary_comments)) {
        $DB->delete_records_select('glossary_comments', "entryid IN ($allentriessql)", $params);
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

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function glossary_supports($feature) {
    switch($feature) {
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE: return true;
        default: return null;
    }
}

require_once($CFG->libdir . '/portfoliolib.php');
class glossary_csv_portfolio_caller extends portfolio_module_caller_base {

    private $glossary;
    private $exportdata;

    public function __construct($callbackargs) {
        global $DB;
        if (!$this->cm = get_coursemodule_from_id('glossary', $callbackargs['id'])) {
            portfolio_exporter::raise_error('invalidid', 'glossary');
        }
        if (!$this->glossary = $DB->get_record('glossary', array('id' => $this->cm->instance))) {
            portfolio_exporter::raise_error('invalidid', 'glossary');
        }
        $entries = $DB->get_records('glossary_entries', array('glossaryid' => $this->glossary->id));
        list($where, $params) = $DB->get_in_or_equal(array_keys($entries));

        $aliases = $DB->get_records_select('glossary_alias', 'entryid' . $where, $params);
        $categoryentries = $DB->get_records_sql('SELECT ec.entryid, c.name FROM {glossary_entries_categories} ec
            JOIN {glossary_categories} c
            ON c.id = ec.categoryid
            WHERE ec.entryid ' . $where, $params);

        $this->exportdata = array('entries' => $entries, 'aliases' => $aliases, 'categoryentries' => $categoryentries);
    }

    public function expected_time() {
        //@todo check number of records maybe
        return PORTFOLIO_TIME_MODERATE;
    }

    public function get_sha1() {
        return sha1(serialize($this->exportdata));
    }

    public function prepare_package() {
        $entries = $this->exportdata['entries'];
        $aliases = array();
        $categories = array();
        if (is_array($this->exportdata['aliases'])) {
            foreach ($this->exportdata['aliases'] as $alias) {
                if (!array_key_exists($alias->entryid, $aliases)) {
                    $aliases[$alias->entryid] = array();
                }
                $aliases[$alias->entryid][] = $alias->alias;
            }
        }
        if (is_array($this->exportdata['categoryentries'])) {
            foreach ($this->exportdata['categoryentries'] as $cat) {
                if (!array_key_exists($cat->entryid, $categories)) {
                    $categories[$cat->entryid] = array();
                }
                $categories[$cat->entryid][] = $cat->name;
            }
        }
        $csv = glossary_generate_export_csv($entries, $aliases, $categories);
        return $this->exporter->write_new_file($csv, clean_filename($this->cm->name) . '.csv');
    }

    public function check_permissions() {
        // @todo
        return true;
    }

    public static function display_name() {
        return get_string('modulename', 'glossary');
    }
}

class glossary_entry_portfolio_caller extends portfolio_module_caller_base {

    private $glossary;
    private $entry;

    public function __construct($callbackargs) {
        global $DB;
        if (!$this->cm = get_coursemodule_from_id('glossary', $callbackargs['id'])) {
            portfolio_exporter::raise_error('invalidid', 'glossary');
        }
        if (!$this->glossary = $DB->get_record('glossary', array('id' => $this->cm->instance))) {
            portfolio_exporter::raise_error('invalidid', 'glossary');
        }
        if (!array_key_exists('entryid', $callbackargs)
            || !$this->entry = $DB->get_record('glossary_entries', array('id' => $callbackargs['entryid']))) {
            portfolio_exporter::raise_error('noentry', 'glossary');
        }
        /*
        $aliases = $DB->get_records('glossary_alias', array('entryid' => $this->entry->id));
        $categories = $DB->get_records_sql('SELECT ec.entryid, c.name
            FROM {glossary_entries_categories} ec
            JOIN {glossary_categories} c
            ON c.id = ec.categoryid
            WHERE ec.entryid = ?', array($this->entry->id));
        */
    }

    public function expected_time() {
        return PORTFOLIO_TIME_LOW;
    }

    public function check_permissions() {
        //@ penny todo
        return true;
    }

    public static function display_name() {
        return get_string('modname', 'glossary');
    }

    public function prepare_package() {
        // in case we don't have USER this will make the entry be printed
        $this->entry->approved = true;
        define('PORTFOLIO_INTERNAL', true);
        ob_start();
        glossary_print_entry($this->get('course'), $this->cm, $this->glossary, $this->entry, null, null, false);
        $content = ob_get_clean();
        return $this->exporter->write_new_file($content, clean_filename($this->entry->concept) . '.html');
    }

    public function get_sha1() {
        return sha1(serialize($this->entry));
    }
}

?>
