<?php  // $Id$

/// Library of functions and constants for module wiki
/// (replace wiki with the name of your module and delete this line)


$wiki_CONSTANT = 7;     /// for example
$site = get_site();
$WIKI_TYPES = array ('teacher' =>   get_string('defaultcourseteacher'),
                     'group' =>     get_string('groups',"wiki"),
                     'student' =>   get_string('defaultcoursestudent') );
define("EWIKI_ESCAPE_AT", 0);       # For the algebraic filter

// How long locks stay around without being confirmed (seconds)
define("WIKI_LOCK_PERSISTENCE",120);

// How often to confirm that you still want a lock
define("WIKI_LOCK_RECONFIRM",60);

// Session variable used to store wiki locks
define('SESSION_WIKI_LOCKS','wikilocks');

/*** Moodle 1.7 compatibility functions *****
 *
 ********************************************/
function wiki_context($wiki) {
    //TODO: add some $cm caching if needed
    if (is_object($wiki)) {
        $wiki = $wiki->id;
    }
    if (! $cm = get_coursemodule_from_instance('wiki', $wiki)) {
        error('Course Module ID was incorrect');
    }

    return get_context_instance(CONTEXT_MODULE, $cm->id);
}

function wiki_is_teacher($wiki, $userid=NULL) {
    return has_capability('mod/wiki:manage', wiki_context($wiki), $userid);
}

function wiki_is_teacheredit($wiki, $userid=NULL) {
    return has_capability('mod/wiki:manage', wiki_context($wiki), $userid)
       and has_capability('moodle/site:accessallgroups', wiki_context($wiki), $userid);
}

function wiki_is_student($wiki, $userid=NULL) {
    return has_capability('mod/wiki:participate', wiki_context($wiki), $userid);
}

function wiki_get_students($wiki, $groups='', $sort='u.lastaccess', $fields='u.*') {
    return $users = get_users_by_capability(wiki_context($wiki), 'mod/wiki:participate', $fields, $sort, '', '', $groups);
}

/* end of compatibility functions */


function wiki_add_instance($wiki) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $wiki->timemodified = time();

    # May have to add extra stuff in here #

    /// Determine the pagename for this wiki and save.
    $wiki->pagename = wiki_page_name($wiki);

    return insert_record("wiki", $wiki);
}


function wiki_update_instance($wiki) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    /// Determine the pagename for this wiki.
    $wiki->pagename = wiki_page_name($wiki);

    $wiki->timemodified = time();
    $wiki->id = $wiki->instance;
    return update_record("wiki", $wiki);
}

/// Delete all Directories recursively
function wiki_rmdir($basedir) {
  $handle = @opendir($basedir);
  if($handle) {
    while (false!==($folder = readdir($handle))) {
       if($folder != "." && $folder != ".." && $folder != "CVS") {
          wiki_rmdir("$basedir/$folder");  // recursive
       }
    }
    closedir($handle);
  }
  @rmdir($basedir);
}

function wiki_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.
    global $CFG;

    if (! $wiki = get_record("wiki", "id", $id)) {
        return false;
    }

    $result = true;

    #Delete Files
### Should probably check regardless of this setting in case its been changed...
    if($wiki->ewikiacceptbinary) {
      if ($basedir = $CFG->dataroot."/".$wiki->course."/".$CFG->moddata."/wiki/$id") {
          if ($files = get_directory_list($basedir)) {
              foreach ($files as $file) {
                  #if ($file != $exception) {
                      unlink("$basedir/$file");
                      notify("Existing file '$file' has been deleted!");
                  #}
              }
          }
          #if (!$exception) {  // Delete directory as well, if empty
              wiki_rmdir("$basedir");
          #}
      }
    }

    # Delete any dependent records here #
    if(!delete_records("wiki_locks","wikiid",$wiki->id)) {
        $result = false;
    }

    if (! delete_records("wiki", "id", $wiki->id)) {
        $result = false;
    }

    /// Delete all wiki_entries and wiki_pages.
    if (($wiki_entries = wiki_get_entries($wiki)) !== false) {
        foreach ($wiki_entries as $wiki_entry) {
            if (! delete_records("wiki_pages", "wiki", "$wiki_entry->id")) {
                $result = false;
            }
            if (! delete_records("wiki_entries", "id", "$wiki_entry->id")) {
                $result = false;
            }
        }
    }

    return $result;
}

function wiki_user_outline($course, $user, $mod, $wiki) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    $return = NULL;
    return $return;
}

function wiki_user_complete($course, $user, $mod, $wiki) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function wiki_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in wiki activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG;
    
    $sql = "SELECT l.*, cm.instance FROM {$CFG->prefix}log l 
                INNER JOIN {$CFG->prefix}course_modules cm ON l.cmid = cm.id 
            WHERE l.time > '$timestart' AND l.course = {$course->id} 
                AND l.module = 'wiki' AND action LIKE 'edit%'
            ORDER BY l.time ASC";
            
    if (!$logs = get_records_sql($sql)){
        return false;
    }

    $modinfo = get_fast_modinfo($course);
    $wikis = array();

    foreach ($logs as $log) {
        $cm = $modinfo->instances['wiki'][$log->instance];
        if (!$cm->uservisible) {
            continue;
        }

    /// Process log->url and rebuild it here to properly clean the pagename - MDL-15896
        $extractedpage = preg_replace('/^.*&page=/', '', $log->url);
        $log->url = preg_replace('/page=.*$/', 'page='.urlencode($extractedpage), $log->url);

        $wikis[$log->info] = wiki_log_info($log);
        $wikis[$log->info]->pagename = $log->info;
        $wikis[$log->info]->time = $log->time;
        $wikis[$log->info]->url  = str_replace('&', '&amp;', $log->url);
    }

    if (!$wikis) {
        return false;
    }
    print_headline(get_string('updatedwikipages', 'wiki').':', 3);
    foreach ($wikis as $wiki) {
        print_recent_activity_note($wiki->time, $wiki, $wiki->pagename,
                                   $CFG->wwwroot.'/mod/wiki/'.$wiki->url);
    }

    return false;
}

function wiki_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT u.firstname, u.lastname
                             FROM {$CFG->prefix}user u
                            WHERE u.id = '$log->userid'");
}

function wiki_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    // Delete expired locks
    $result=delete_records_select('wiki_locks','lockedseen < '.(time()-WIKI_LOCK_PERSISTENCE));

    return $result;
}

function wiki_get_participants($wikiid) {
//Returns the users with data in one wiki
//(users with records in wiki_pages and wiki_entries)

    global $CFG;

    //Get users from wiki_pages
    $st_pages = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}wiki_entries e,
                                      {$CFG->prefix}wiki_pages p
                                 WHERE e.wikiid = '$wikiid' and
                                       p.wiki = e.id and
                                       u.id = p.userid");

    //Get users from wiki_entries
    $st_entries = get_records_sql("SELECT DISTINCT u.id, u.id
                                   FROM {$CFG->prefix}user u,
                                        {$CFG->prefix}wiki_entries e
                                   WHERE e.wikiid = '$wikiid' and
                                         u.id = e.userid");

    //Add entries to pages
    if ($st_entries) {
        foreach ($st_entries as $st_entry) {
            $st_pages[$st_entry->id] = $st_entry;
        }
    }

    return $st_pages;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other wiki functions go here.  Each of them must have a name that
/// starts with wiki_

function wiki_wiki_name($wikiname) {
/// Return the passed in string in Wiki name format.
/// Remove any leading and trailing whitespace, capitalize all the words
/// and then remove any internal whitespace.

    if (wiki_is_wiki_name($wikiname)) {
        return $wikiname;
    }
    else {
        /// Create uppercase words and remove whitespace.
        $wikiname = preg_replace("/(\w+)\s/", "$1", ucwords(trim($wikiname)));

        /// Check again - there may only be one word.
        if (wiki_is_wiki_name($wikiname)) {
            return $wikiname;
        }
        /// If there is only one word, append default wiki name to it.
        else {
            return $wikiname.get_string('wikidefaultpagename', 'wiki');
        }
    }
}

function wiki_is_wiki_name($wikiname) {
/// Check for correct wikiname syntax and return true or false.

    /// If there are spaces between the words, incorrect format.
    if (preg_match_all('/\w+/', $wikiname, $out) > 1) {
        return false;
    }
    /// If there isn't more than one group of uppercase letters separated by
    /// lowercase letters or '_', incorrect format.
    else if (preg_match_all('/[A-Z]+[a-z_]+/', $wikiname, $out) > 1) {
        return true;
    }
    else {
        return false;
    }
}

function wiki_page_name(&$wiki) {
/// Determines the wiki's page name and returns it.
    if (!empty($wiki->initialcontent)) {
        $ppos = strrpos($wiki->initialcontent, '/');
        if ($ppos === false) {
            $pagename = $wiki->initialcontent;
        }
        else {
            $pagename = substr($wiki->initialcontent, $ppos+1);
        }
    }
    else if (!empty($wiki->pagename)) {
        $pagename = $wiki->pagename;
    }
    else {
        $pagename = $wiki->name;
    }
    return $pagename;
}

function wiki_content_dir(&$wiki) {
/// Determines the wiki's default content directory (if there is one).
    global $CFG;

    if (!empty($wiki->initialcontent)) {
        $ppos = strrpos($wiki->initialcontent, '/');
        if ($ppos === false) {
            $subdir = '';
        }
        else {
            $subdir = substr($wiki->initialcontent, 0, $ppos+1);
        }
        $contentdir = $CFG->dataroot.'/'.$wiki->course.'/'.$subdir;
    }
    else {
        $contentdir = false;
    }
    return $contentdir;
}

function wiki_get_course_wikis($courseid, $wtype='*') {
/// Returns all wikis for the specified course and optionally of the specified type.

    $select = 'course = '.$courseid;
    if ($wtype != '*') {
        $select .= ' AND wtype = \''.$wtype.'\'';
    }
    return get_records_select('wiki', $select, 'id');
}

function wiki_has_entries(&$wiki) {
/// Returns true if wiki already has wiki entries; otherwise false.

    return record_exists('wiki_entries', 'wikiid', $wiki->id);
}

function wiki_get_entries(&$wiki, $byindex=NULL) {
/// Returns an array with all wiki entries indexed by entry id; false if there are none.
/// If the optional $byindex is specified, returns the entries indexed by that field.
/// Valid values for $byindex are 'student', 'group'.
    global $CFG;
    
    if ($byindex == 'student') {
        return get_records('wiki_entries', 'wikiid', $wiki->id, '',
                           'userid,id,wikiid,course,groupid,pagename,timemodified');
    }
    else if ($byindex == 'group') {
        return get_records('wiki_entries', 'wikiid', $wiki->id, '',
                           'groupid,id,wikiid,course,userid,pagename,timemodified');
    }
    else {
        return get_records('wiki_entries', 'wikiid', $wiki->id);
    }
}

function wiki_get_default_entry(&$wiki, &$course, $userid=0, $groupid=0) {
/// Returns the wiki entry according to the wiki type.
/// Optionally, will return wiki entry for $userid student wiki, or
/// $groupid group or teacher wiki.
/// Creates one if it needs to and it can.
    global $USER;
    /// If there is a groupmode, get the user's group id.
    $groupmode = groups_get_activity_groupmode($wiki);
    // if groups mode is in use and no group supplied, use the first one found
    if ($groupmode && !$groupid) {
        if(($mygroupids=mygroupid($course->id)) && count($mygroupids)>0) {
            // Use first group. They ought to be able to change later
            $groupid=$mygroupids[0];
        } else {
            // Whatever groups are in the course, pick one
            $coursegroups = groups_get_all_groups($course->id);
            if(!$coursegroups || count($coursegroups)==0) {
                error("Can't access wiki in group mode when no groups are configured for the course");
            }
            $unkeyed=array_values($coursegroups); // Make sure first item is index 0
            $groupid=$unkeyed[0]->id;
        }
    }

    /// If the wiki entry doesn't exist, can this user create it?
    if (($wiki_entry = wiki_get_entry($wiki, $course, $userid, $groupid)) === false) {
        if (wiki_can_add_entry($wiki, $USER, $course, $userid, $groupid)) {
            wiki_add_entry($wiki, $course, $userid, $groupid);
            if (($wiki_entry = wiki_get_entry($wiki, $course, $userid, $groupid)) === false) {
                error("Could not add wiki entry.");
            }
        }
    }
    //print_object($wiki_entry);
    return $wiki_entry;
}

function wiki_get_entry(&$wiki, &$course, $userid=0, $groupid=0) {
/// Returns the wiki entry according to the wiki type.
/// Optionally, will return wiki entry for $userid student wiki, or
/// $groupid group or teacher wiki.
    global $USER;

    switch ($wiki->wtype) {
    case 'student':
        /// If a specific user was requested, return it, if allowed.
        if ($userid and wiki_user_can_access_student_wiki($wiki, $userid, $course)) {
            $wentry = wiki_get_student_entry($wiki, $userid);
        }

        /// If there is no entry for this user, check if this user is a teacher.
        else if (!$wentry = wiki_get_student_entry($wiki, $USER->id)) {
/*            if (wiki_is_teacher($wiki, $USER->id)) {
                /// If this user is a teacher, return the first entry.
                if ($wentries = wiki_get_entries($wiki)) {
                    $wentry = current($wentries);
                }
            }*/
        }
        break;

    case 'group':
        /// If there is a groupmode, get the user's group id.
        $groupmode = groups_get_activity_groupmode($wiki);
        if($groupmode) {
            if(!$groupid) {
                if(($mygroupids=mygroupid($course->id)) && count($mygroupids)>0) {
                    // Use first group. They ought to be able to change later
                    $groupid=$mygroupids[0];
                } else {
                    // Whatever groups are in the course, pick one
                    $coursegroups = groups_get_all_groups($course->id);
                    if(!$coursegroups || count($coursegroups)==0) {
                        error("Can't access wiki in group mode when no groups are configured for the course");
                    }
                    $unkeyed=array_values($coursegroups); // Make sure first item is index 0
                    $groupid=$unkeyed[0]->id;
                }
            }

            //echo "groupid is in wiki_get_entry ".$groupid."<br />";
            /// If a specific group was requested, return it, if allowed.
            if ($groupid and wiki_user_can_access_group_wiki($wiki, $groupid, $course)) {
                $wentry = wiki_get_group_entry($wiki, $groupid);
            } else {
                error("Cannot access any groups for this wiki");
            }
        }
        /// If mode is 'nogroups', then groupid is zero.
        else {
            $wentry = wiki_get_group_entry($wiki, 0);
        }
        break;

    case 'teacher':
        /// If there is a groupmode, get the user's group id.
        if (groupmode($course, $wiki)) {
            $mygroupids = mygroupid($course->id);//same here, default to the first one
            $groupid = $groupid ? $groupid : $mygroupids[0]/*mygroupid($course->id)*/;
        }

        /// If a specific group was requested, return it, if allowed.
        if (wiki_user_can_access_teacher_wiki($wiki, $groupid, $course)) {
            $wentry = wiki_get_teacher_entry($wiki, $groupid);
        }
        break;
    }
    return $wentry;
}

function wiki_get_teacher_entry(&$wiki, $groupid=0) {
/// Returns the wiki entry for the wiki teacher type.
    return get_record('wiki_entries', 'wikiid', $wiki->id, 'course', $wiki->course, 'groupid', $groupid);
}

function wiki_get_group_entry(&$wiki, $groupid=null) {
/// Returns the wiki entry for the given group.
    return get_record('wiki_entries', 'wikiid', $wiki->id, 'groupid', $groupid);
}

function wiki_get_student_entry(&$wiki, $userid=null) {
/// Returns the wiki entry for the given student.
    global $USER;

    if (is_null($userid)) {
        $userid = $USER->id;
    }
    return get_record('wiki_entries', 'wikiid', $wiki->id, 'userid', $userid);
}

function wiki_get_other_wikis(&$wiki, &$user, &$course, $currentid=0) {
    /// Returns a list of other wikis to display, depending on the type, group and user.
    /// Returns the key containing the currently selected entry as well.

    global $CFG, $id;

    $wikis = false;

    $groupmode = groups_get_activity_groupmode($wiki);
    $mygroupid = mygroupid($course->id);
    $isteacher = wiki_is_teacher($wiki, $user->id);
    $isteacheredit = wiki_is_teacheredit($wiki, $user->id);

    $groupingid = null;
    $cm = new stdClass;
    $cm->id = $wiki->cmid;
    $cm->groupmode = $wiki->groupmode;
    $cm->groupingid = $wiki->groupingid;
    $cm->groupmembersonly = $wiki->groupmembersonly;
    if (!empty($CFG->enablegroupings) && !empty($cm->groupingid)) {
        $groupingid = $wiki->groupingid;
    }
    
    
    switch ($wiki->wtype) {

    case 'student':
        /// Get all the existing entries for this wiki.
        $wiki_entries = wiki_get_entries($wiki, 'student');
        
        if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid)) {
            $sql = "SELECT gm.userid FROM {$CFG->prefix}groups_members gm " .
                    "INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = gg.groupid " .
                    "WHERE gg.groupingid = $wiki->groupingid ";
    
            $groupingmembers = get_records_sql($sql);
        }
        
        if ($isteacher and (SITEID != $course->id)) {

            /// If the user is an editing teacher, or a non-editing teacher not assigned to a group, show all student
            /// wikis, regardless of creation.
            if ((SITEID != $course->id) and ($isteacheredit or ($groupmode == NOGROUPS))) {

                if ($students = get_course_students($course->id)) {
                    /// Default pagename is dependent on the wiki settings.
                    $defpagename = empty($wiki->pagename) ? get_string('wikidefaultpagename', 'wiki') : $wiki->pagename;

                    foreach ($students as $student) {
                        if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid) && empty($groupingmembers[$student->id])) {
                            continue;
                        }
                        /// If this student already has an entry, use its pagename.
                        if ($wiki_entries[$student->id]) {
                            $pagename = $wiki_entries[$student->id]->pagename;
                        }
                        else {
                            $pagename = $defpagename;
                        }

                        $key = 'view.php?id='.$id.'&userid='.$student->id.'&page='.$pagename;
                        $wikis[$key] = fullname($student).':'.$pagename;
                    }
                }
            }
            else if ($groupmode == SEPARATEGROUPS) {

                if ($students = wiki_get_students($wiki, $mygroupid)) {
                    $defpagename = empty($wiki->pagename) ? get_string('wikidefaultpagename', 'wiki') : $wiki->pagename;
                    foreach ($students as $student) {
                        if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid) && empty($groupingmembers[$student->id])) {
                            continue;
                        }
                        /// If this student already has an entry, use its pagename.
                        if ($wiki_entries[$student->id]) {
                            $pagename = $wiki_entries[$student->id]->pagename;
                        }
                        else {
                            $pagename = $defpagename;
                        }

                        $key = 'view.php?id='.$id.'&userid='.$student->id.'&page='.$pagename;
                        $wikis[$key] = fullname($student).':'.$pagename;
                    }
                }
            }
            else if ($groupmode == VISIBLEGROUPS) {
                /// Get all students in your group.
                if ($students = wiki_get_students($wiki, $mygroupid)) {
                    $defpagename = empty($wiki->pagename) ? get_string('wikidefaultpagename', 'wiki') : $wiki->pagename;
                    foreach ($students as $student) {
                        if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid) && empty($groupingmembers[$student->id])) {
                            continue;
                        }
                        /// If this student already has an entry, use its pagename.
                        if ($wiki_entries[$student->id]) {
                            $pagename = $wiki_entries[$student->id]->pagename;
                        }
                        else {
                            $pagename = $defpagename;
                        }
                        $key = 'view.php?id='.$id.'&userid='.$student->id.'&page='.$pagename;
                        $wikis[$key] = fullname($student).':'.$pagename;
                    }
                }
                /// Get all student wikis created, regardless of group.
                if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid)) {
                    $sql = 'SELECT w.id, w.userid, w.pagename, u.firstname, u.lastname '
                          .'    FROM '.$CFG->prefix.'wiki_entries w '
                          .'    INNER JOIN '.$CFG->prefix.'user u ON w.userid = u.id '
                          .'    INNER JOIN '.$CFG->prefix.'groups_members gm ON gm.userid = u.id '
                          .'    INNER JOIN '.$CFG->prefix.'groupings_groups gg ON gm.groupid = gg.groupid '
                          .'    WHERE w.wikiid = '.$wiki->id.' AND gg.groupingid =  '.$wiki->groupingid
                          .'    ORDER BY w.id';
                } else {
                    $sql = 'SELECT w.id, w.userid, w.pagename, u.firstname, u.lastname '
                          .'    FROM '.$CFG->prefix.'wiki_entries w, '.$CFG->prefix.'user u '
                          .'    WHERE w.wikiid = '.$wiki->id.' AND u.id = w.userid '
                          .'    ORDER BY w.id';
                }
                $wiki_entries = get_records_sql($sql);
                $wiki_entries=is_array($wiki_entries)?$wiki_entries:array();
                foreach ($wiki_entries as $wiki_entry) {
                    $key = 'view.php?id='.$id.'&userid='.$wiki_entry->userid.'&page='.$wiki_entry->pagename;
                    $wikis[$key] = fullname($wiki_entry).':'.$wiki_entry->pagename;
                    if ($currentid == $wiki_entry->id) {
                        $wikis['selected'] = $key;
                    }
                }
            }
        }
        else {
            /// A user can see other student wikis if they are a member of the same
            /// group (for separate groups) or there are visible groups, or if this is
            /// a site-level wiki, and they are an administrator.
            if (($groupmode == VISIBLEGROUPS) or wiki_is_teacheredit($wiki)) {
                $viewall = true;
            }
            else if ($groupmode == SEPARATEGROUPS) {
                $viewall = mygroupid($course->id);
            }
            else {
                $viewall = false;
            }

            if ($viewall !== false) {
                if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid)) {
                    $sql = 'SELECT w.id, w.userid, w.pagename, u.firstname, u.lastname '
                          .'    FROM '.$CFG->prefix.'wiki_entries w '
                          .'    INNER JOIN '.$CFG->prefix.'user u ON w.userid = u.id '
                          .'    INNER JOIN '.$CFG->prefix.'groups_members gm ON gm.userid = u.id '
                          .'    INNER JOIN '.$CFG->prefix.'groupings_groups gg ON gm.groupid = gg.groupid '
                          .'    WHERE w.wikiid = '.$wiki->id.' AND gg.groupingid =  '.$wiki->groupingid
                          .'    ORDER BY w.id';
                } else {
                    $sql = 'SELECT w.id, w.userid, w.pagename, u.firstname, u.lastname '
                          .'    FROM '.$CFG->prefix.'wiki_entries w, '.$CFG->prefix.'user u '
                          .'    WHERE w.wikiid = '.$wiki->id.' AND u.id = w.userid '
                          .'    ORDER BY w.id';
                }
                $wiki_entries = get_records_sql($sql);
                $wiki_entries=is_array($wiki_entries)?$wiki_entries:array();
                foreach ($wiki_entries as $wiki_entry) {
                    if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid) && empty($groupingmembers[$wiki_entry->userid])) {
                        continue;
                    }
                
                    if (($viewall === true) or groups_is_member($viewall, $wiki_entry->userid)) {
                        $key = 'view.php?id='.$id.'&userid='.$wiki_entry->userid.'&page='.$wiki_entry->pagename;
                        $wikis[$key] = fullname($wiki_entry).':'.$wiki_entry->pagename;
                        if ($currentid == $wiki_entry->id) {
                            $wikis['selected'] = $key;
                        }
                    }
                }
            }
        }
        break;

    case 'group':
        /// If the user is an editing teacher, or a non-editing teacher not assigned to a group, show all group
        /// wikis, regardless of creation.

        /// If user is a member of multiple groups, need to show current group etc?

        /// Get all the existing entries for this wiki.
        $wiki_entries = wiki_get_entries($wiki, 'group');
        
        if ($groupmode and ($isteacheredit or ($isteacher and !$mygroupid))) {
            if ($groups = groups_get_all_groups($course->id, null, $groupingid)) {
                $defpagename = empty($wiki->pagename) ? get_string('wikidefaultpagename', 'wiki') : $wiki->pagename;
                foreach ($groups as $group) {

                    /// If this group already has an entry, use its pagename.
                    if (isset($wiki_entries[$group->id])) {
                        $pagename = $wiki_entries[$group->id]->pagename;
                    }
                    else {
                        $pagename = $defpagename;
                    }

                    $key = 'view.php?id='.$id.($group->id?"&groupid=".$group->id:"").'&page='.$pagename;
                    $wikis[$key] = $group->name.':'.$pagename;
                }
            }
        }
        //if a studnet with multiple groups in SPG
        else if ($groupmode == SEPARATEGROUPS){
            if ($groups = groups_get_all_groups($course->id, $user->id, $groupingid)){

                $defpagename = empty($wiki->pagename) ? get_string('wikidefaultpagename', 'wiki') : $wiki->pagename;
                foreach ($groups as $group) {
                    /// If this group already has an entry, use its pagename.
                    if (isset($wiki_entries[$group->id])) {
                        $pagename = $wiki_entries[$group->id]->pagename;
                    }
                    else {
                        $pagename = $defpagename;
                    }
                    $key = 'view.php?id='.$id.($group->id?"&groupid=".$group->id:"").'&page='.$pagename;
                    $wikis[$key] = $group->name.':'.$pagename;
                }

            }

        }
        /// A user can see other group wikis if there are visible groups.
        else if ($groupmode == VISIBLEGROUPS) {
            if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid)) {
                $sql = 'SELECT w.id, w.groupid, w.pagename, g.name as gname '
                      .'    FROM '.$CFG->prefix.'wiki_entries w '
                      .'    INNER JOIN '.$CFG->prefix.'groups g ON g.id = w.groupid '
                      .'    INNER JOIN '.$CFG->prefix.'groupings_groups gg ON g.id = gg.groupid '
                      .'    WHERE w.wikiid = '.$wiki->id.' AND gg.groupingid =  '.$wiki->groupingid
                      .'    ORDER BY w.groupid';
            } else {
                $sql = 'SELECT w.id, w.groupid, w.pagename, g.name as gname '
                      .'    FROM '.$CFG->prefix.'wiki_entries w, '.$CFG->prefix.'groups g '
                      .'    WHERE w.wikiid = '.$wiki->id.' AND g.id = w.groupid '
                      .'    ORDER BY w.groupid';
            }
            $wiki_entries = get_records_sql($sql);
            $wiki_entries=is_array($wiki_entries)?$wiki_entries:array();
            foreach ($wiki_entries as $wiki_entry) {
                $key = 'view.php?id='.$id.($wiki_entry->groupid?"&groupid=".$wiki_entry->groupid:"").'&page='.$wiki_entry->pagename;
                $wikis[$key] = $wiki_entry->gname.':'.$wiki_entry->pagename;
                if ($currentid == $wiki_entry->id) {
                    $wikis['selected'] = $key;
                }
            }
        }
        break;

    case 'teacher':
        if ($isteacher) {
            /// If the user is an editing teacher, or a non-editing teacher not assigned to a group, show all
            /// teacher wikis, regardless of creation.
            if ($groupmode and ($isteacheredit or ($isteacher and !$mygroupid))) {
                if ($groups = groups_get_all_groups($course->id, null, $groupingid)) {
                    $defpagename = empty($wiki->pagename) ? get_string('wikidefaultpagename', 'wiki') : $wiki->pagename;
                    foreach ($groups as $group) {
                        /// If this group already has an entry, use its pagename.
                        if ($wiki_entries[$group->id]) {
                            $pagename = $wiki_entries[$group->id]->pagename;
                        }
                        else {
                            $pagename = $defpagename;
                        }

                        $key = 'view.php?id='.$id.($group->id?"&groupid=".$group->id:"").'&page='.$pagename;
                        $wikis[$key] = $group->name.':'.$pagename;
                    }
                }
            }
            /// A teacher can see all other group teacher wikis.
            else if ($groupmode) {
            if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid)) {
                $sql = 'SELECT w.id, w.groupid, w.pagename, g.name as gname '
                      .'    FROM '.$CFG->prefix.'wiki_entries w '
                      .'    INNER JOIN '.$CFG->prefix.'groups g ON g.id = w.groupid '
                      .'    INNER JOIN '.$CFG->prefix.'groupings_groups gg ON g.id = gg.groupid '
                      .'    WHERE w.wikiid = '.$wiki->id.' AND gg.groupingid =  '.$wiki->groupingid
                      .'    ORDER BY w.groupid';
            } else {
                $sql = 'SELECT w.id, w.groupid, w.pagename, g.name as gname '
                      .'    FROM '.$CFG->prefix.'wiki_entries w, '.$CFG->prefix.'groups g '
                      .'    WHERE w.wikiid = '.$wiki->id.' AND g.id = w.groupid '
                      .'    ORDER BY w.groupid';
            }
                $wiki_entries = get_records_sql($sql);
                $wiki_entries=is_array($wiki_entries)?$wiki_entries:array();
                foreach ($wiki_entries as $wiki_entry) {
                    $key = 'view.php?id='.$id.($wiki_entry->groupid?"&groupid=".$wiki_entry->groupid:"").'&page='.$wiki_entry->pagename;
                    $wikis[$key] = $wiki_entry->gname.':'.$wiki_entry->pagename;
                    if ($currentid == $wiki_entry->id) {
                        $wikis['selected'] = $key;
                    }
                }
            }
        }
        else {
            /// A user can see other teacher wikis if they are a teacher, a member of the same
            /// group (for separate groups) or there are visible groups.
            if ($groupmode == VISIBLEGROUPS) {
                $viewall = true;
            }
            else if ($groupmode == SEPARATEGROUPS) {
                $viewall = $mygroupid;
            }
            else {
                $viewall = false;
            }
            if ($viewall !== false) {
                if (!empty($CFG->enablegroupings) && !empty($wiki->groupingid)) {
                    $sql = 'SELECT w.id, w.groupid, w.pagename, g.name as gname '
                          .'    FROM '.$CFG->prefix.'wiki_entries w '
                          .'    INNER JOIN '.$CFG->prefix.'groups g ON g.id = w.groupid '
                          .'    INNER JOIN '.$CFG->prefix.'groupings_groups gg ON g.id = gg.groupid '
                          .'    WHERE w.wikiid = '.$wiki->id.' AND gg.groupingid =  '.$wiki->groupingid
                          .'    ORDER BY w.groupid';
                } else {
                    $sql = 'SELECT w.id, w.groupid, w.pagename, g.name as gname '
                          .'    FROM '.$CFG->prefix.'wiki_entries w, '.$CFG->prefix.'groups g '
                          .'    WHERE w.wikiid = '.$wiki->id.' AND g.id = w.groupid '
                          .'    ORDER BY w.groupid';
                }
                $wiki_entries = get_records_sql($sql);
                $wiki_entries=is_array($wiki_entries)?$wiki_entries:array();


                foreach ($wiki_entries as $wiki_entry) {
                    if (($viewall === true) or @in_array($wiki_entry->groupid, $viewall)/*$viewall == $wiki_entry->groupid*/) {
                        $key = 'view.php?id='.$id.($wiki_entry->groupid?"&groupid=".$wiki_entry->groupid:"").'&page='.$wiki_entry->pagename;
                        $wikis[$key] = $wiki_entry->gname.':'.$wiki_entry->pagename;
                        if ($currentid == $wiki_entry->id) {
                            $wikis['selected'] = $key;
                        }
                    }
                }
            }
        }
        break;
    }
    
    return $wikis;
}

function wiki_add_entry(&$wiki, &$course, $userid=0, $groupid=0) {
/// Adds a new wiki entry of the specified type, unless already entered.
/// No checking is done here. It is assumed that the caller has the correct
/// privileges to add this entry.

    global $USER;

    /// If this wiki already has a wiki_type entry, return false.
    if (wiki_get_entry($wiki, $course, $userid, $groupid) !== false) {
        return false;
    }

    $wiki_entry = new Object();

    switch ($wiki->wtype) {

    case 'student':
        $wiki_entry->wikiid = $wiki->id;
        $wiki_entry->userid = $userid ? $userid : $USER->id;
        $wiki_entry->pagename = wiki_page_name($wiki);
        $wiki_entry->timemodified = time();
        break;

    case 'group':
        /// Get the groupmode. It's been added to the wiki object.
        $groupmode = groups_get_activity_groupmode($wiki);

        ///give the first groupid by default and try
        $mygroups = mygroupid($course->id);

        /// If there is a groupmode, get the group id.
        if ($groupmode) {
            $groupid = $groupid ? $groupid : $mygroups[0]/*mygroupid($course->id)*/;
        }
        /// If mode is 'nogroups', then groupid is zero.
        else {
            $groupid = 0;
        }
        $wiki_entry->wikiid = $wiki->id;
        $wiki_entry->groupid = $groupid;
        $wiki_entry->pagename = wiki_page_name($wiki);
        $wiki_entry->timemodified = time();

        break;

    case 'teacher':
        /// Get the groupmode. It's been added to the wiki object.
        $groupmode = groups_get_activity_groupmode($wiki);

        /// If there is a groupmode, get the user's group id.
        if ($groupmode and $groupid == 0) {
            $mygroupid = mygroupid($course->id);
            $groupid = $mygroupid[0]/*mygroupid($course->id)*/;
        }

        $wiki_entry->wikiid = $wiki->id;
        $wiki_entry->course = $wiki->course;
        $wiki_entry->groupid = $groupid;
        $wiki_entry->pagename = wiki_page_name($wiki);
        $wiki_entry->timemodified = time();
        break;
    }
    $wiki_entry->pagename = addslashes($wiki_entry->pagename);

    return insert_record("wiki_entries", $wiki_entry, true);
}

function wiki_can_add_entry(&$wiki, &$user, &$course, $userid=0, $groupid=0) {
/// Returns true or false if the user can add a wiki entry for this wiki.

    /// Get the groupmode. It's been added to the wiki object.
    $groupmode = groups_get_activity_groupmode($wiki);
    $mygroupid = mygroupid($course->id);

    switch ($wiki->wtype) {

    case 'student':
///     A student can create their own wiki, if they are a member of that course.
///     A user can create their own wiki at the site level.
        if ($userid == 0) {
            return (wiki_is_student($wiki, $user->id) or wiki_is_student($wiki, $user->id));
        }
///     An editing teacher can create any student wiki, or
///     a non-editing teacher, if not assigned to a group can create any student wiki, or if assigned to a group can
///     create any student wiki in their group.
        else {
            return ((($userid == $user->id) and wiki_is_student($wiki, $user->id)) or wiki_is_teacheredit($wiki) or
                    (wiki_is_teacher($wiki) and (!$groupmode or $mygroupid == 0 or (groups_is_member($mygroupid, $userid)))));
        }
        break;

    case 'group':
        /// If mode is 'nogroups', then all participants can add wikis.
        if (wiki_is_teacheredit($wiki, $user->id)) {
            return true;
        }

        if (!$groupmode) {
            return (wiki_is_student($wiki, $user->id) or wiki_is_teacher($wiki, $user->id));
        }
        /// If not requesting a group, must be a member of a group.
        else if ($groupid == 0) {
            return ($mygroupid != 0);
        }
        /// If requesting a group, must be an editing teacher, a non-editing teacher with no assigned group,
        /// or a non-editing teacher requesting their group. or a student in group, but wiki is empty.
        else {
            return (wiki_is_teacheredit($wiki) or
                   (wiki_is_teacher($wiki) and ($mygroupid == 0 or @in_array($groupid, $mygroupid))) or
                   (wiki_is_student($wiki, $user->id) and @in_array($groupid, $mygroupid))
                   );
        }
        break;

    case 'teacher':
        /// If mode is 'nogroups', then all teachers can add wikis.
        if (!$groupmode) {
            return wiki_is_teacher($wiki, $user->id);
        }
        /// If not requesting a group, must be a member of a group.
        else if ($groupid == 0) {
            return ($mygroupid != 0 and wiki_is_teacher($wiki));
        }
        /// If there is a group mode, non-editing teachers with an assigned group, can only create wikis
        /// in their group. Non-editing teachers with no assigned group and editing teachers can create any wiki.
        else {
            return (wiki_is_teacheredit($wiki) or
                    (wiki_is_teacher($wiki) and ($mygroupid == 0 or @in_array($groupid, $mygroupid))));
        }
        break;
    }

    return false;
}

function wiki_can_edit_entry(&$wiki_entry, &$wiki, &$user, &$course) {
/// Returns true or false if the user can edit this wiki entry.

    $can_edit = false;
    $groupmode = groups_get_activity_groupmode($wiki);
    $mygroupid = mygroupid($course->id);

    /// Editing teacher's and admins can edit all wikis, non-editing teachers can edit wikis in their groups,
    /// or all wikis if group mode is 'no groups' or they don't belong to a group.
    if (wiki_is_teacheredit($wiki, $user->id) or
        ((!$groupmode or $mygroupid == 0) and wiki_is_teacher($wiki, $user->id))) {
        $can_edit = true;
    }
    else {
        switch ($wiki->wtype) {

        /// Only a teacher or the owner of a student wiki can edit it.
        case 'student':
            $can_edit = (($user->id == $wiki_entry->userid) or
                         ($groupmode and wiki_is_teacher($wiki, $user->id) and
                          groups_is_member($mygroupid, $wiki_entry->userid)));
            break;

        case 'group':
            /// If there is a groupmode, determine the user's group status.
            if ($groupmode) {
                /// If the user is a member of the wiki group, they can edit the wiki.
                $can_edit = groups_is_member($wiki_entry->groupid, $user->id);
            }
            /// If mode is 'nogroups', then all participants can edit the wiki.
            else {
                $can_edit = (wiki_is_student($wiki, $user->id) or wiki_is_teacher($wiki, $user->id));
            }
            break;

        case 'teacher':
            /// If there is a groupmode, determine the user's group status.
            if ($groupmode) {
                /// If the user is a member of the wiki group, they can edit the wiki.
                $can_edit = (wiki_is_teacher($wiki, $user->id) and groups_is_member($wiki_entry->groupid, $user->id));
            }
            else {
                $can_edit = wiki_is_teacher($wiki, $user->id);
            }
            break;
        }
    }
    return $can_edit;
}

function wiki_user_can_access_student_wiki(&$wiki, $userid, &$course) {
    global $USER;

    /// Get the groupmode. It's been added to the wiki object.
    $groupmode = groups_get_activity_groupmode($wiki);
    $usersgroup = mygroupid($course->id);
    $isteacher = wiki_is_teacher($wiki, $USER->id);

    /// If this user is allowed to access this wiki then return TRUE.
    /// *** THIS COULD BE A PROBLEM, IF STUDENTS COULD EVER BE PART OF MORE THAN ONE GROUP ***
    /// A user can access a student wiki, if:
    ///     - it is their wiki,
    ///     - group mode is VISIBLEGROUPS,
    ///     - group mode is SEPARATEGROUPS, and the user is a member of the requested user's group,
    ///     - they are an editing teacher or administrator,
    ///     - they are a non-editing teacher not assigned to a specific group,
    ///     - they are a non-editing teacher and group mode is NOGROUPS.
    ///     - they are an administrator (mostly for site-level wikis).
    if (($userid and ($USER->id == $userid)) or ($groupmode == VISIBLEGROUPS) or
        (($groupmode == SEPARATEGROUPS) and groups_is_member($usersgroup, $userid)) or
        (wiki_is_teacheredit($wiki, $USER->id)) or
        (wiki_is_teacher($wiki, $USER->id) and (!$usersgroup or $groupmode == NOGROUPS))) {
        $can_access = true;
    }
    else {
        $can_access = false;
    }
    return $can_access;
}

function wiki_user_can_access_group_wiki(&$wiki, $groupid, &$course) {
    global $USER;

    /// Get the groupmode. It's been added to the wiki object.
    $groupmode = groups_get_activity_groupmode($wiki);
    $usersgroup = mygroupid($course->id);
    $isteacher = wiki_is_teacher($wiki, $USER->id);

    /// A user can access a group wiki, if:
    ///     - group mode is NOGROUPS,
    ///     - group mode is VISIBLEGROUPS,
    ///     - group mode is SEPARATEGROUPS, and they are a member of the requested group,
    ///     - they are an editing teacher or administrator,
    ///     - they are a non-editing teacher not assigned to a specific group.
    if (($groupmode == NOGROUPS) or ($groupmode == VISIBLEGROUPS) or
        (($groupmode == SEPARATEGROUPS) and @in_array($groupid, $usersgroup)/*($usersgroup == $groupid)*/) or
        (wiki_is_teacheredit($wiki, $USER->id)) or
        (wiki_is_teacher($wiki, $USER->id) and !$usersgroup)) {
        $can_access = true;
    }
    else {
        $can_access = false;
    }
    return $can_access;
}

function wiki_user_can_access_teacher_wiki(&$wiki, $groupid, &$course) {
    global $USER;

    /// Get the groupmode. It's been added to the wiki object.
    $groupmode = groups_get_activity_groupmode($wiki);

    /// A user can access a teacher wiki, if:
    ///     - group mode is NOGROUPS,
    ///     - group mode is VISIBLEGROUPS,
    ///     - group mode is SEPARATEGROUPS, and they are a member of the requested group,
    ///     - they are a teacher or administrator,
    if (($groupmode == NOGROUPS) or ($groupmode == VISIBLEGROUPS) or
        (($groupmode == SEPARATEGROUPS) and (@in_array($groupid, mygroupid($course->id))/*mygroupid($course->id) == $groupid*/)) or
        (wiki_is_teacher($wiki, $USER->id))){
        $can_access = true;
    }
    else {
        $can_access = false;
    }
    return $can_access;
}

function wiki_get_owner(&$wiki_entry) {
    if ($wiki_entry->userid > 0) {
        $user = get_record('user', 'id', $wiki_entry->userid);
        $owner = fullname($user);
    }
    else if ($wiki_entry->groupid > 0) {
        $owner = groups_get_group_name($wiki_entry->groupid); //TODO:check.
    }
    else if ($wiki_entry->course > 0) {
        $course = get_record('course', 'id', $wiki_entry->course);
        $owner = $course->shortname;
    }
    else {
        $owner = '- '.get_string("ownerunknown","wiki").' -';
    }
    return $owner;
}

function wiki_print_search_form($cmid, $search="", $userid, $groupid, $return=false) {
    global $CFG;
    # TODO: Add Group and User !!!
    $output = "<form id=\"search\" action=\"$CFG->wwwroot/mod/wiki/view.php\">";
    $output .="<fieldset class='invisiblefieldset'>";
    $output .= "<span style='font-size:0.6em;'>";
    $output .= "<input value=\"".get_string("searchwiki", "wiki").":\" type=\"submit\" />";
    $output .= "<input name=\"id\" type=\"hidden\" value=\"$cmid\" />";
    $output = $output.($groupid?"<input name=\"groupid\" type=\"hidden\" value=\"$groupid\" />":"");
    $output = $output.($userid?"<input name=\"userid\" type=\"hidden\" value=\"$userid\" />":"");
    $output .= "<input name=\"q\" type=\"text\" size=\"20\" value=\"".s($search)."\" />".' ';
    $output .= "</span>";
    $output .= "<input name=\"page\" type=\"hidden\" value=\"SearchPages\" />";
    $output .= "</fieldset>";
    $output .= "</form>";

    if ($return) {
        return $output;
    }
    echo $output;
}

function wiki_print_wikilinks_block($cmid, $binary=false, $return=false) {
/// Prints a link-list of special wiki-pages
   global $CFG, $ewiki_title;

   $links=array();

   $links["SiteMap"]=get_string("sitemap", "wiki");
   $links["PageIndex"]=get_string("pageindex", "wiki");
   $links["NewestPages"]=get_string("newestpages", "wiki");
   $links["MostVisitedPages"]=get_string("mostvisitedpages", "wiki");
   $links["MostOftenChangedPages"]=get_string("mostoftenchangedpages", "wiki");
   $links["UpdatedPages"]=get_string("updatedpages", "wiki");
   $links["OrphanedPages"]=get_string("orphanedpages", "wiki");
   $links["WantedPages"]=get_string("wantedpages", "wiki");
   $links["WikiExport"]=get_string("wikiexport", "wiki");
   if($binary) {
     $links["FileDownload"]=get_string("filedownload", "wiki");
   }
   popup_form(EWIKI_SCRIPT, $links, "wikilinks", "", get_string("choosewikilinks", "wiki"), "", "", $return);
}

function wiki_print_page_actions($cmid, $specialpages, $page, $action, $binary=false, $canedit=true) {
/// Displays actions which can be performed on the page

  $page=array();

  // Edit this Page
  if (in_array($action, array("edit", "links", "info", "attachments"))) {
    $page["view/$page"]=get_string("viewpage","wiki");
  }
  if ($canedit && !in_array($page, $specialpages) && $action != "edit") {
    $page["edit/$page"]=get_string("editthispage","wiki");
  }
  if ($action != "links") {
    $page["links/$page"]=get_string("backlinks","wiki");
  }
  if ($canedit && !in_array($page, $specialpages) && $action!="info") {
    $page["info/$page"]=get_string("pageinfo","wiki");
  }
  if($canedit && $binary && !in_array($page, $specialpages) && $action != "attachments") {
    $page["attachments/$page"]=get_string("attachments","wiki");
  }

  popup_form(EWIKI_SCRIPT, $page, "wikiactions", "", get_string("action", "wiki"), "", "", false);
}

function wiki_print_administration_actions($wiki, $cmid, $userid, $groupid, $page, $noeditor, $course) {
/// Displays actions which can be performed on the page

  /// Create the URL
  $ewscript = 'admin.php?id='.$cmid;
  if (isset($userid) && $userid!=0) $ewscript .= '&amp;userid='.$userid;
  if (isset($groupid) && $groupid!=0) $ewscript .= '&amp;groupid='.$groupid;
  if (isset($page)) $ewscript .= '&amp;page='.$page;
  $ewscript.="&amp;action=";


    /// Build that action array according to wiki flags.
    $action = array();
    $isteacher = wiki_is_teacher($wiki);

    if ($wiki->setpageflags or $isteacher) {
        $action['setpageflags'] = get_string('setpageflags', 'wiki');
    }
    if ($wiki->removepages or $isteacher) {
        $action['removepages']  = get_string('removepages', 'wiki');
    }
    if ($wiki->strippages or $isteacher) {
        $action['strippages']  = get_string('strippages', 'wiki');
    }
    if ($wiki->revertchanges or $isteacher) {
        $action['revertpages'] = get_string('revertpages', 'wiki');
    }

  if($noeditor) {
    $action["checklinks"]=get_string("checklinks", "wiki");
  }
  popup_form($ewscript, $action, "wikiadministration", "", get_string("chooseadministration", "wiki"), "", "", false);
}

function wiki_admin_get_flagarray() {
  $ret = array(
     EWIKI_DB_F_TEXT => get_string("flagtxt","wiki"),
     EWIKI_DB_F_BINARY => get_string("flagbin","wiki"),
     EWIKI_DB_F_DISABLED => get_string("flagoff","wiki"),
     EWIKI_DB_F_HTML => get_string("flaghtm","wiki"),
     EWIKI_DB_F_READONLY => get_string("flagro","wiki"),
     EWIKI_DB_F_WRITEABLE => get_string("flagwr","wiki"),
  );

  return $ret;
}

///////// Ewiki Administration. Mostly taken from the ewiki/tools folder and changed
function wiki_admin_setpageflags_list($pageflagstatus) {
  $FD = wiki_admin_get_flagarray();
  $table = new Object();
  $table->head = array(get_string("pagename","wiki"), get_string("flags","wiki"));
  if($pageflagstatus) {
    $table->head[]=get_string("status","wiki");
  }

  $result = ewiki_database("GETALL", array("version", "flags"));
  while ($row = $result->get()) {
    $id = $row["id"];
    $data = ewiki_database("GET", $row);

    $cell_pagename="";
    $cell_flags="";
    if ($data["flags"] & EWIKI_DB_F_TEXT) {
        $cell_pagename .= '<a href="' . EWIKI_SCRIPT . $id . '">';
    } else {
        $cell_pagename .= '<a href="' . EWIKI_SCRIPT_BINARY . $id . '">';
    }
    $cell_pagename .= s($id) . '</a> / '.get_string("version","wiki").": ".$row["version"];

    foreach ($FD as $n=>$str) {
        $cell_flags .='<input type="checkbox" name="flags['. rawurlencode($id)
            . '][' . $n . ']" value="1" '
            . (($data["flags"] & $n) ? "checked=\"checked\"" : "")
            . ' />'.$str. ' ';
    }
    if($pageflagstatus) {
      $table->data[]=array($cell_pagename, $cell_flags, $pageflagstatus[$id]);
    } else {
      $table->data[]=array($cell_pagename, $cell_flags);
    }
  }
  return $table;
}

function wiki_admin_setpageflags($pageflags) {
  $FD = wiki_admin_get_flagarray();

  $status=array();
  if($pageflags) {
     foreach($pageflags as $page=>$fa) {

        $page = rawurldecode($page);

        $flags = 0;
        $fstr = "";
        foreach($fa as $num=>$isset) {
           if ($isset) {
              $flags += $num;
              $fstr .= ($fstr?",":""). $FD[$num];
           }
        }

        #$status[$page] .= "{$flags}=[{$fstr}]";

        $data = ewiki_database("GET", array("id" => $page));

        if ($data["flags"] != $flags) {
           $data["flags"] = $flags;
           $data["author"] = "ewiki-tools, " . ewiki_author();
           $data["version"]++;
           ewiki_database("WRITE", $data);
           $status[$page] =  "<b>".get_string("flagsset","wiki")."</b> ".$status[$page];
        }
     }
  }
  return $status;
}


function wiki_admin_remove_list($listall="") {
  /// Table header
  $table = new Object();
  $table->head = array("&nbsp;", get_string("pagename","wiki"), get_string("errororreason","wiki"));

  /// Get all pages
  $result = ewiki_database("GETALL", array("version"));
  $selected = array();

  /// User wants to see all pages
  if ($listall) {
    while ($row = $result->get()) {
      $selected[$row["id"]] = get_string("listall","wiki")."<br />";
    }
  }
  while ($page = $result->get()) {
    $id = $page["id"];
    $page = ewiki_database("GET", array("id"=>$id));
    $flags = $page["flags"];
    #print "$id ".strlen(trim(($page["content"])))."<br />";

    if (!strlen(trim(($page["content"]))) && !($flags & EWIKI_DB_F_BINARY)) {
        @$selected[$id] .= get_string("emptypage","wiki")."<br />";
    }

    // Check for orphaned pages
    $result2 = ewiki_database("SEARCH", array("content" => $id));
    $orphanedpage=true;
    if ($result2 && $result2->count()) {
        while ($row = $result2->get()) {
          $checkcontent = ewiki_database("GET", array("id"=>$row["id"]));
          $checkcontent = strtolower($checkcontent["content"]);

          if(strpos($checkcontent, strtolower($id)) !== false) {
            $orphanedpage=false;
          }

          #echo "rc({$row['id']})==>($id): $check2 <br />";
        }
    }

    /// Some more reasons for Deletion...
    if ($orphanedpage && $id!=EWIKI_PAGE_INDEX &&!($flags & EWIKI_DB_F_BINARY)) {
        @$selected[$id] .= get_string("orphanedpage","wiki")."<br />";
    }

    if ($flags & EWIKI_DB_F_DISABLED) {
        @$selected[$id] .= get_string("disabledpage","wiki")."<br />";
    }

    if (($flags & 3) == 3) {
        @$selected[$id] .= get_string("errorbinandtxt","wiki")."<br />";
    }

    if (!($flags & 3)) {
        @$selected[$id] .= get_string("errornotype","wiki")."<br />";
    }

    if ($flags & EWIKI_DB_F_HTML) {
        @$selected[$id] .= get_string("errorhtml","wiki")."<br />";
    }

    if (($flags & EWIKI_DB_F_READONLY) && !($flags & EWIKI_DB_F_BINARY)) {
        @$selected[$id] .= get_string("readonly","wiki")."<br />";
    }

    if (($flags & EWIKI_DB_F_READONLY) && ($flags & EWIKI_DB_F_WRITEABLE)) {
        @$selected[$id] .= get_string("errorroandwr","wiki")."<br />";
    }

    if (strlen($page["content"]) >= 65536) {
        @$selected[$id] .= get_string("errorsize","wiki")."<br />";
    }

    if (strpos($page["refs"], "\n".get_string("deletemewikiword","wiki")."\n")!==false) {
        @$selected[$id] .= get_string("deletemewikiwordfound","wiki",get_string("deletemewikiword","wiki"))."<br />";
    }
  }

  foreach ($selected as $id => $reason) {
    $table_checkbox='<input type="checkbox" value="'.rawurlencode($id).'" name="pagestodelete[]" />';

    #-- link & id
    if (strpos($id, EWIKI_IDF_INTERNAL) === false) {
        $table_page='<a href="' . ewiki_script("", $id) . '">';
    } else {
        $table_page='<a href="' . ewiki_script_binary("", $id) . '">';
    }
    $table_page .= s($id) . '</a>';

    #-- print reason
    $table_reason=$reason;

    $table->data[]=array($table_checkbox, $table_page, $table_reason);
  }

  return $table;
}

/// This function actually removes the pages
function wiki_admin_remove($pagestodelete, $course, $wiki, $userid, $groupid) {
  $ret="";
  foreach ($pagestodelete as $id) {

    $id = rawurldecode($id);

    $data = ewiki_database("GET", array("id"=>$id));
    for ($version=1; $version<=$data["version"]; $version++) {
        ewiki_database("DELETE", array("id"=>$id, "version"=>$version));
        if($data["flags"] & EWIKI_DB_F_BINARY) {
          $filepath=moodle_binary_get_path($id, $data["meta"], $course, $wiki, $userid, $groupid);
          @unlink("$filepath");
        }
    }

  }
  return $ret;
}

function wiki_admin_strip_list($pagestostrip="",$version="",$err="") {
  /// Table header
  $table = new Object();
  $table->head = array("&nbsp;", get_string("pagename","wiki"), get_string("deleteversions","wiki"));

  $vc=ewiki_database("COUNTVERSIONS", array());
  $result = ewiki_database("GETALL",array());
  $i=0;
  while ($row = $result->get()) {
     $id = $row["id"];
     if($vc[$id]>1) {
        $error="";
        if($err[$id]) {
          $error=" ".join(", ",$err[$id]);
        }
        $checked="";
        if($pagestostrip=="" || $pagestostrip[$i]) {
          $checked=" checked=\"checked\"";
        }
        if($version=="") {
          $versiondefault="1-".($row["version"]-1);
        } else {
          $versiondefault=$version[$i];
        }
        $table->data[]=array('<input type="checkbox" value="'.rawurlencode($id).'" name="pagestostrip['.$i.']" '.$checked.' />',
                        '<A HREF="'.EWIKI_SCRIPT.$id.'">'.s($id).'</A> / '.get_string("version","wiki").": ".$row["version"],
                        '<input name="version['.$i.']" value="'.$versiondefault.'" size="7" />'.$error);

      }
      $i++;
  }
  return $table;
}

function wiki_admin_strip_versions($pagestostrip, $version, &$err) {
  $ret=array();
  foreach ($pagestostrip as $key => $id_ue) {

    $id = rawurldecode($id_ue);
    if (preg_match('/^(\d+)[-\s._:]+(\d+)$/', trim($version[$key]), $uu)) {
      $versA = $uu[1];
      $versZ = $uu[2];

      // Let the last Version in the database
      $checkdata = ewiki_database("GET", array("id" => $id));
      if($versZ>=$checkdata["version"]) {
            $err[$id][] = get_string("versionrangetoobig","wiki");
      } else {
        if($versA<=$versZ) {
          for ($v=$versA; $v<=$versZ; $v++) {
              $ret[$id][]=$v;
          }
        } else {
          $err[$id][]=get_string("wrongversionrange","wiki",$version[$key]);
        }
      }
    }
    else {
      $err[$id][]=get_string("wrongversionrange","wiki",$version[$key]);
    }
  }
  return $ret;
}

function wiki_admin_strip($pagestostrip) {
  /// Purges old page-versions
  foreach($pagestostrip as $id => $versions) {
    foreach($versions as $version) {
      ewiki_database("DELETE", array("id"=>$id, "version"=>$version));
    }
  }
}

function wiki_admin_checklinks_list() {
  $ret=array();
  $result = ewiki_database("GETALL",array());
  while ($row = $result->get()) {
    if(!($row["flags"] & EWIKI_DB_F_BINARY)) {
      $index=s($row["id"]);
      $ret[$index] = $row["id"];
    }
  }
  return $ret;
}

function wiki_admin_checklinks($pagetocheck) {
  /// Checks http:// Links
  $ret="";
  if($pagetocheck) {
     $get = ewiki_database("GET", array("id" => $pagetocheck));
     $content = $get["content"];

     preg_match_all('_(http.?://[^\s"\'<>#,;]+[^\s"\'<>#,;.])_', $content, $links);
     $badlinks = array();
     if(!$links[1]) {
       $ret = get_string("nolinksfound","wiki")."<br /><br />";
     } else {
       foreach ($links[1] as $href) {
          #print "[ $href ]";
          #$d = @implode("", @file($href));
          $d="";
          if($checkfd = @fopen($href, 'r')) {
            fclose($checkfd);
            $d="OK";
          }
          if (empty($d) || !strlen(trim($d)) || stristr("not found", $d) || stristr("error 404", $d)) {
             $ret.="[".get_string("linkdead","wiki")."] $href <br />\n";
             $badlinks[] = $href;
          } else {
             $ret.="[".get_string("linkok","wiki")."] $href <br />\n";
          }
       }
     }

     /// Remove old Notices
     $content = eregi_replace(' µµ__~\['.get_string("offline","wiki").'\]__µµ ','', $content);

     #-- replace dead links
     foreach ($badlinks as $href) {
        $content = preg_replace("\377^(.*)($href)\377m", '$1 µµ__~['.get_string("offline","wiki").']__µµ $2', $content);
     }

     #-- compare against db content
     if ($content != $get["content"]) {
        $get["content"] = $content;
        $get["version"]++;
        $get["author"] = ewiki_author("ewiki_checklinks");
        $get["lastmodified"] = time();

        ewiki_database("WRITE", $get);
     }
  }
  return $ret;
}

function wiki_admin_revert($proceed, $authorfieldpattern, $changesfield, $howtooperate, $deleteversions) {
  $ret="";
  #-- params
  $m_time = $changesfield * 3600;
  $depth = $deleteversions - 1;
  $depth = ($depth>0?$depth:0);

  #-- walk through
  $result = ewiki_database("GETALL", array("id", "author", "lastmodified"));
  while ($row = $result->get()) {
    $id = $row["id"];
    #-- which versions to check
    $verZ = $row["version"];
    if ($howtooperate=="lastonly") {
      $verA = $verZ;
    }
    else {
      $verA = $verZ-$depth;
      if ($verA <= 0) {
          $verA = 1;
      }
    }

    for ($ver=$verA; $ver<=$verZ; $ver++) {
      #-- load current $ver database entry
      if ($verA != $verZ) {
          $row = ewiki_database("GET", array("id"=>$id, "version"=>$ver));
      }

      #-- match
      if (stristr($row["author"], $authorfieldpattern) && ($row["lastmodified"] + $m_time > time())) {
        $ret .= "$id (".get_string("versionstodelete","wiki").": ";
        #-- delete multiple versions
        if ($howtooperate=="allsince") {
          while ($ver<=$verZ) {
              $ret .= " $ver";
              if ($proceed) {
                ewiki_database("DELETE", array("id"=>$id, "version"=>$ver));
              }
              $ver++;
          }
        }
        #-- or just the affected one
        else {
          $ret .= " $ver";
          if ($proceed) {
            ewiki_database("DELETE", $row);
          }
        }
        $ret .= ")<br />";
        break;
      }
    } #-- for($ver)
  } #-- while($row)
  return $ret;
}


function wiki_get_view_actions() {
    return array('view','view all');
}

function wiki_get_post_actions() {
    return array('hack');
}


/**
 * Obtains an editing lock on a wiki page.
 * @param int $wikiid ID of wiki object.
 * @param string $pagename Name of page.
 * @return array Two-element array with a boolean true (if lock has been obtained)
 *   or false (if lock was held by somebody else). If lock was held by someone else,
 *   the values of the wiki_locks entry are held in the second element; if lock was
 *   held by current user then the the second element has a member ->id only.
 */
function wiki_obtain_lock($wikiid,$pagename) {
    global $USER;

    // Check for lock
    $alreadyownlock=false;
    if($lock=get_record('wiki_locks','pagename',$pagename,'wikiid', $wikiid)) {
        // Consider the page locked if the lock has been confirmed within WIKI_LOCK_PERSISTENCE seconds
        if($lock->lockedby==$USER->id) {
            // Cool, it's our lock, do nothing except remember it in session
            $lockid=$lock->id;
            $alreadyownlock=true;
        } else if(time()-$lock->lockedseen < WIKI_LOCK_PERSISTENCE) {
            return array(false,$lock);
        } else {
            // Not locked any more. Get rid of the old lock record.
            if(!delete_records('wiki_locks','pagename',$pagename,'wikiid', $wikiid)) {
                error('Unable to delete lock record');
            }
        }
    }

    // Add lock
    if(!$alreadyownlock) {
        // Lock page
        $newlock=new stdClass;
        $newlock->lockedby=$USER->id;
        $newlock->lockedsince=time();
        $newlock->lockedseen=$newlock->lockedsince;
        $newlock->wikiid=$wikiid;
        $newlock->pagename=$pagename;
        if(!$lockid=insert_record('wiki_locks',$newlock)) {
            error('Unable to insert lock record');
        }
    }

    // Store lock information in session so we can clear it later
    if(!array_key_exists(SESSION_WIKI_LOCKS,$_SESSION)) {
        $_SESSION[SESSION_WIKI_LOCKS]=array();
    }
    $_SESSION[SESSION_WIKI_LOCKS][$wikiid.'_'.$pagename]=$lockid;
    $lockdata=new StdClass;
    $lockdata->id=$lockid;
    return array(true,$lockdata);
}

/**
 * If the user has an editing lock, releases it. Has no effect otherwise.
 * Note that it doesn't matter if this isn't called (as happens if their
 * browser crashes or something) since locks time out anyway. This is just
 * to avoid confusion of the 'what? it says I'm editing that page but I'm
 * not, I just saved it!' variety.
 * @param int $wikiid ID of wiki object.
 * @param string $pagename Name of page.
 */
function wiki_release_lock($wikiid,$pagename) {
    if(!array_key_exists(SESSION_WIKI_LOCKS,$_SESSION)) {
        // No locks at all in session
        return;
    }

    $key=$wikiid.'_'.$pagename;

    if(array_key_exists($key,$_SESSION[SESSION_WIKI_LOCKS])) {
        $lockid=$_SESSION[SESSION_WIKI_LOCKS][$key];
        unset($_SESSION[SESSION_WIKI_LOCKS][$key]);
        if(!delete_records('wiki_locks','id',$lockid)) {
            error("Unable to delete lock record.");
        }
    }
}

/**
 * Returns all other caps used in module
 */
function wiki_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames');
}

?>
