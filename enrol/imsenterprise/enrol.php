<?php
/**
* @author Dan Stowell
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package enrol_imsenterprise
*/
require_once("$CFG->libdir/blocklib.php");
require_once($CFG->dirroot.'/group/lib.php');

// The following flags are set in the configuration
// $CFG->enrol_imsfilelocation:        where is the file we are looking for?
// $CFG->enrol_logtolocation:          if you want to store a log of processing, specify filepath here
// $CFG->enrol_allowinternal:          allow internal enrolment in courses
// $CFG->enrol_emailadmins:            email a notification to the admin
// $CFG->enrol_createnewusers:         should this script create user accounts for those who don't seem to be registered yet?
// $CFG->enrol_imsdeleteusers:         should this script mark user accounts as deleted, if the data requests this?
// $CFG->enrol_fixcaseusernames:       whether to force all usernames to lowercase
// $CFG->enrol_fixcasepersonalnames:   convert personal names, e.g. from "TOM VEK" to "Tom Vek"
// $CFG->enrol_truncatecoursecodes:    if this number is greater than zero, truncate the codes found in the IMS data to the given number of characters
// $CFG->enrol_imsunenrol:             allow this script to UNENROL students/tutors from courses (if the data marks them as having left the course)
// $CFG->enrol_createnewcourses:       should this script create a (hidden, empty) course for any course that doesn't seem to have been registered yet?
// $CFG->enrol_createnewcategories:    should this script create a (hidden) category if Moodle doesn't have one by the same name as the desired one?
// $CFG->enrol_imssourcedidfallback:   some systems don't output a <userid> element (contrary to the specifications). If this is the case, activating this setting will cause us to use the <sourcedid><id> element instead as the userid. This may or may not be desirable in your situation.
// $CFG->enrol_includephoto:           Process IMS <photo> tag to create user photo. Be warned that this may add significant server load.

/*

Note for programmers:

This class uses regular expressions to mine the data file. The main reason is
that XML handling changes from PHP 4 to PHP 5, so this should work on both.

One drawback is that the pattern-matching doesn't (currently) handle XML
namespaces - it only copes with a <group> tag if it says <group>, and not
(for example) <ims:group>.

This should also be able to handle VERY LARGE FILES - so the entire IMS file is
NOT loaded into memory at once. It's handled line-by-line, 'forgetting' tags as
soon as they are processed.

N.B. The "sourcedid" ID code is translated to Moodle's "idnumber" field, both
for users and for courses.

*/


class enrolment_plugin_imsenterprise {

    var $log;

// The "roles" hard-coded in the IMS specification are:
var $imsroles = array(
'01'=>'Learner',
'02'=>'Instructor',
'03'=>'Content Developer',
'04'=>'Member',
'05'=>'Manager',
'06'=>'Mentor',
'07'=>'Administrator',
'08'=>'TeachingAssistant',
);
// PLEASE NOTE: It may seem odd that "Content Developer" has a space in it
// but "TeachingAssistant" doesn't. That's what the spec says though!!!


/**
* This function is only used when first setting up the plugin, to
* decide which role assignments to recommend by default.
* For example, IMS role '01' is 'Learner', so may map to 'student' in Moodle.
*/
function determine_default_rolemapping($imscode) {
    switch($imscode) {
        case '01':
        case '04':
            $shortname = 'student';
            break;
        case '06':
        case '08':
            $shortname = 'teacher';
            break;
        case '02':
        case '03':
            $shortname = 'editingteacher';
            break;
        case '05':
        case '07':
            $shortname = 'admin';
            break;
        default:
            return 0; // Zero for no match
    }
    return get_field('role', 'id', 'shortname', $shortname);
}



/// Override the base config_form() function
function config_form($frm) {
    global $CFG, $imsroles;

    $vars = array('enrol_imsfilelocation', 'enrol_logtolocation', 'enrol_createnewusers', 'enrol_fixcaseusernames', 'enrol_fixcasepersonalnames', 'enrol_truncatecoursecodes',
            'enrol_createnewcourses', 'enrol_createnewcategories', 'enrol_createnewusers', 'enrol_mailadmins',
            'enrol_imsunenrol', 'enrol_imssourcedidfallback', 'enrol_imscapitafix', 'enrol_imsrestricttarget', 'enrol_imsdeleteusers',
            'enrol_imse_imsrolemap01','enrol_imse_imsrolemap02','enrol_imse_imsrolemap03','enrol_imse_imsrolemap04',
            'enrol_imse_imsrolemap05','enrol_imse_imsrolemap06','enrol_imse_imsrolemap07','enrol_imse_imsrolemap08');
    foreach ($vars as $var) {
        if (!isset($frm->$var)) {
            $frm->$var = '';
        }
    }
    include ("$CFG->dirroot/enrol/imsenterprise/config.html");
}


/// Override the base process_config() function
function process_config($config) {

    if (!isset($config->enrol_imsfilelocation)) {
        $config->enrol_imsfilelocation = '';
    }
    set_config('enrol_imsfilelocation', $config->enrol_imsfilelocation);

    if (!isset($config->enrol_logtolocation)) {
        $config->enrol_logtolocation = '';
    }
    set_config('enrol_logtolocation', $config->enrol_logtolocation);

    if (!isset($config->enrol_fixcaseusernames)) {
        $config->enrol_fixcaseusernames = '';
    }
    set_config('enrol_fixcaseusernames', $config->enrol_fixcaseusernames);

    if (!isset($config->enrol_fixcasepersonalnames)) {
        $config->enrol_fixcasepersonalnames = '';
    }
    set_config('enrol_fixcasepersonalnames', $config->enrol_fixcasepersonalnames);

    if (!isset($config->enrol_truncatecoursecodes)) {
        $config->enrol_truncatecoursecodes = 0;
    }
    set_config('enrol_truncatecoursecodes', intval($config->enrol_truncatecoursecodes));

    if (!isset($config->enrol_createnewcourses)) {
        $config->enrol_createnewcourses = '';
    }
    set_config('enrol_createnewcourses', $config->enrol_createnewcourses);

    if (!isset($config->enrol_createnewcategories)) {
        $config->enrol_createnewcategories = '';
    }
    set_config('enrol_createnewcategories', $config->enrol_createnewcategories);

    if (!isset($config->enrol_createnewusers)) {
        $config->enrol_createnewusers = '';
    }
    set_config('enrol_createnewusers', $config->enrol_createnewusers);

    if (!isset($config->enrol_imsdeleteusers)) {
        $config->enrol_imsdeleteusers = '';
    }
    set_config('enrol_imsdeleteusers', $config->enrol_imsdeleteusers);

    if (!isset($config->enrol_mailadmins)) {
        $config->enrol_mailadmins = '';
    }
    set_config('enrol_mailadmins', $config->enrol_mailadmins);

    if (!isset($config->enrol_imsunenrol)) {
        $config->enrol_imsunenrol = '';
    }
    set_config('enrol_imsunenrol', $config->enrol_imsunenrol);

    if (!isset($config->enrol_imssourcedidfallback)) {
        $config->enrol_imssourcedidfallback = '';
    }
    set_config('enrol_imssourcedidfallback', $config->enrol_imssourcedidfallback);

    if (!isset($config->enrol_imscapitafix)) {
        $config->enrol_imscapitafix = '';
    }
    set_config('enrol_imscapitafix', $config->enrol_imscapitafix);

    //Antoni Mas. 07/12/2005. Incloem la opci de la foto dels usuaris
    if (!isset($config->enrol_processphoto)) {
        $config->enrol_processphoto = '';
    }
    set_config('enrol_processphoto', $config->enrol_processphoto);

    if (!isset($config->enrol_imsrestricttarget)) {
        $config->enrol_imsrestricttarget = '';
    }
    set_config('enrol_imsrestricttarget', $config->enrol_imsrestricttarget);



    foreach($this->imsroles as $imsrolenum=>$imsrolename){
        $configref = 'enrol_imse_imsrolemap' . $imsrolenum;
        if (!isset($config->$configref)) {
            echo "<p>Resetting config->$configref</p>";
            $config->$configref = 0;
        }
        set_config('enrol_imse_imsrolemap' . $imsrolenum, $config->$configref);
    }


    set_config('enrol_ims_prev_md5',  ''); // Forget the MD5 - to force re-processing if we change the config setting
    set_config('enrol_ims_prev_time', ''); // Ditto
    return true;

}

function get_access_icons($course){}

/**
* Read in an IMS Enterprise file.
* Originally designed to handle v1.1 files but should be able to handle
* earlier types as well, I believe.
*
*/
function cron() {
    global $CFG;

    if (empty($CFG->enrol_imsfilelocation)) {
        // $filename = "$CFG->dirroot/enrol/imsenterprise/example.xml";  // Default location
        $filename = "$CFG->dataroot/1/imsenterprise-enrol.xml";  // Default location
    } else {
        $filename = $CFG->enrol_imsfilelocation;
    }

    $this->logfp = false; // File pointer for writing log data to
    if(!empty($CFG->enrol_logtolocation)) {
        $this->logfp = fopen($CFG->enrol_logtolocation, 'a');
    }



    if ( file_exists($filename) ) {
        @set_time_limit(0);
        $starttime = time();

        $this->log_line('----------------------------------------------------------------------');
        $this->log_line("IMS Enterprise enrol cron process launched at " . userdate(time()));
        $this->log_line('Found file '.$filename);
        $this->xmlcache = '';

        // Make sure we understand how to map the IMS-E roles to Moodle roles
        $this->load_role_mappings();

        $md5 = md5_file($filename); // NB We'll write this value back to the database at the end of the cron
        $filemtime = filemtime($filename);

        // Decide if we want to process the file (based on filepath, modification time, and MD5 hash)
        // This is so we avoid wasting the server's efforts processing a file unnecessarily
        if(empty($CFG->enrol_ims_prev_path)  || ($filename != $CFG->enrol_ims_prev_path)){
            $fileisnew = true;
        }elseif(isset($CFG->enrol_ims_prev_time) && ($filemtime <= $CFG->enrol_ims_prev_time)){
            $fileisnew = false;
            $this->log_line('File modification time is not more recent than last update - skipping processing.');
        }elseif(isset($CFG->enrol_ims_prev_md5) && ($md5 == $CFG->enrol_ims_prev_md5)){
            $fileisnew = false;
            $this->log_line('File MD5 hash is same as on last update - skipping processing.');
        }else{
            $fileisnew = true; // Let's process it!
        }

        if($fileisnew){

            $listoftags = array('group', 'person', 'member', 'membership', 'comments', 'properties'); // The list of tags which should trigger action (even if only cache trimming)
            $this->continueprocessing = true; // The <properties> tag is allowed to halt processing if we're demanding a matching target

            // FIRST PASS: Run through the file and process the group/person entries
            if (($fh = fopen($filename, "r")) != false) {

                $line = 0;
                while ((!feof($fh)) && $this->continueprocessing) {

                    $line++;
                    $curline = fgets($fh);
                    $this->xmlcache .= $curline; // Add a line onto the XML cache

                    while(true){
                      // If we've got a full tag (i.e. the most recent line has closed the tag) then process-it-and-forget-it.
                      // Must always make sure to remove tags from cache so they don't clog up our memory
                      if($tagcontents = $this->full_tag_found_in_cache('group', $curline)){
                          $this->process_group_tag($tagcontents);
                          $this->remove_tag_from_cache('group');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('person', $curline)){
                          $this->process_person_tag($tagcontents);
                          $this->remove_tag_from_cache('person');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('membership', $curline)){
                          $this->process_membership_tag($tagcontents);
                          $this->remove_tag_from_cache('membership');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('comments', $curline)){
                          $this->remove_tag_from_cache('comments');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('properties', $curline)){
                          $this->process_properties_tag($tagcontents);
                          $this->remove_tag_from_cache('properties');
                      }else{
                    break;
                  }
                } // End of while-tags-are-detected
                } // end of while loop
                fclose($fh);
                fix_course_sortorder();
            } // end of if(file_open) for first pass

            /*


            SECOND PASS REMOVED
            Since the IMS specification v1.1 insists that "memberships" should come last,
            and since vendors seem to have done this anyway (even with 1.0),
            we can sensibly perform the import in one fell swoop.


            // SECOND PASS: Now go through the file and process the membership entries
            $this->xmlcache = '';
            if (($fh = fopen($filename, "r")) != false) {
                $line = 0;
                while ((!feof($fh)) && $this->continueprocessing) {
                    $line++;
                    $curline = fgets($fh);
                    $this->xmlcache .= $curline; // Add a line onto the XML cache

                    while(true){
                  // Must always make sure to remove tags from cache so they don't clog up our memory
                  if($tagcontents = $this->full_tag_found_in_cache('group', $curline)){
                          $this->remove_tag_from_cache('group');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('person', $curline)){
                          $this->remove_tag_from_cache('person');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('membership', $curline)){
                          $this->process_membership_tag($tagcontents);
                          $this->remove_tag_from_cache('membership');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('comments', $curline)){
                          $this->remove_tag_from_cache('comments');
                      }elseif($tagcontents = $this->full_tag_found_in_cache('properties', $curline)){
                          $this->remove_tag_from_cache('properties');
                      }else{
                    break;
                  }
                }
                } // end of while loop
                fclose($fh);
            } // end of if(file_open) for second pass


           */

            $timeelapsed = time() - $starttime;
            $this->log_line('Process has completed. Time taken: '.$timeelapsed.' seconds.');


        } // END of "if file is new"


        // These variables are stored so we can compare them against the IMS file, next time round.
        set_config('enrol_ims_prev_time', $filemtime);
        set_config('enrol_ims_prev_md5',  $md5);
        set_config('enrol_ims_prev_path', $filename);



    }else{ // end of if(file_exists)
        $this->log_line('File not found: '.$filename);
    }

    if (!empty($CFG->enrol_mailadmins)) {
        $msg = "An IMS enrolment has been carried out within Moodle.\nTime taken: $timeelapsed seconds.\n\n";
        if(!empty($CFG->enrol_logtolocation)){
            if($this->logfp){
                $msg .= "Log data has been written to:\n";
                $msg .= "$CFG->enrol_logtolocation\n";
                $msg .= "(Log file size: ".ceil(filesize($CFG->enrol_logtolocation)/1024)."Kb)\n\n";
            }else{
                $msg .= "The log file appears not to have been successfully written.\nCheck that the file is writeable by the server:\n";
                $msg .= "$CFG->enrol_logtolocation\n\n";
            }
        }else{
            $msg .= "Logging is currently not active.";
        }

        email_to_user(get_admin(), get_admin(), "Moodle IMS Enterprise enrolment notification", $msg);
        $this->log_line('Notification email sent to administrator.');

    }

    if($this->logfp){
      fclose($this->logfp);
    }


} // end of cron() function

/**
* Check if a complete tag is found in the cached data, which usually happens
* when the end of the tag has only just been loaded into the cache.
* Returns either false, or the contents of the tag (including start and end).
* @param string $tagname Name of tag to look for
* @param string $latestline The very last line in the cache (used for speeding up the match)
*/
function full_tag_found_in_cache($tagname, $latestline){ // Return entire element if found. Otherwise return false.
    if(strpos(strtolower($latestline), '</'.strtolower($tagname).'>')===false){
        return false;
    }elseif(preg_match('{(<'.$tagname.'\b.*?>.*?</'.$tagname.'>)}is', $this->xmlcache, $matches)){
        return $matches[1];
    }else return false;
}

/**
* Remove complete tag from the cached data (including all its contents) - so
* that the cache doesn't grow to unmanageable size
* @param string $tagname Name of tag to look for
*/
function remove_tag_from_cache($tagname){ // Trim the cache so we're not in danger of running out of memory.
    ///echo "<p>remove_tag_from_cache: $tagname</p>";  flush();  ob_flush();
    //  echo "<p>remove_tag_from_cache:<br />".htmlspecialchars($this->xmlcache);
    $this->xmlcache = trim(preg_replace('{<'.$tagname.'\b.*?>.*?</'.$tagname.'>}is', '', $this->xmlcache, 1)); // "1" so that we replace only the FIRST instance
    //  echo "<br />".htmlspecialchars($this->xmlcache)."</p>";
}

/**
* Very simple convenience function to return the "recstatus" found in person/group/role tags.
* 1=Add, 2=Update, 3=Delete, as specified by IMS, and we also use 0 to indicate "unspecified".
* @param string $tagdata the tag XML data
* @param string $tagname the name of the tag we're interested in
*/
function get_recstatus($tagdata, $tagname){
    if(preg_match('{<'.$tagname.'\b[^>]*recstatus\s*=\s*["\'](\d)["\']}is', $tagdata, $matches)){
        // echo "<p>get_recstatus($tagname) found status of $matches[1]</p>";
        return intval($matches[1]);
    }else{
        // echo "<p>get_recstatus($tagname) found nothing</p>";
        return 0; // Unspecified
    }
}

/**
* Process the group tag. This defines a Moodle course.
* @param string $tagconents The raw contents of the XML element
*/
function process_group_tag($tagcontents){
    global $CFG;

    // Process tag contents
    unset($group);
    if(preg_match('{<sourcedid>.*?<id>(.+?)</id>.*?</sourcedid>}is', $tagcontents, $matches)){
        $group->coursecode = trim($matches[1]);
    }
    if(preg_match('{<description>.*?<short>(.*?)</short>.*?</description>}is', $tagcontents, $matches)){
        $group->description = trim($matches[1]);
    }
    if(preg_match('{<org>.*?<orgunit>(.*?)</orgunit>.*?</org>}is', $tagcontents, $matches)){
        $group->category = trim($matches[1]);
    }

    $recstatus = ($this->get_recstatus($tagcontents, 'group'));
    //echo "<p>get_recstatus for this group returned $recstatus</p>";

    if(!(strlen($group->coursecode)>0)){
        $this->log_line('Error at line '.$line.': Unable to find course code in \'group\' element.');
    }else{
        // First, truncate the course code if desired
        if(intval($CFG->enrol_truncatecoursecodes)>0){
            $group->coursecode = ($CFG->enrol_truncatecoursecodes > 0)
                     ? substr($group->coursecode, 0, intval($CFG->enrol_truncatecoursecodes))
                     : $group->coursecode;
        }

        /* -----------Course aliasing is DEACTIVATED until a more general method is in place---------------

       // Second, look in the course alias table to see if the code should be translated to something else
        if($aliases = get_field('enrol_coursealias', 'toids', 'fromid', $group->coursecode)){
            $this->log_line("Found alias of course code: Translated $group->coursecode to $aliases");
            // Alias is allowed to be a comma-separated list, so let's split it
            $group->coursecode = explode(',', $aliases);
        }
       */

        // For compatibility with the (currently inactive) course aliasing, we need this to be an array
        $group->coursecode = array($group->coursecode);

        // Third, check if the course(s) exist
        foreach($group->coursecode as $coursecode){
            $coursecode = trim($coursecode);
            if(!get_field('course', 'id', 'idnumber', $coursecode)) {
              if(!$CFG->enrol_createnewcourses) {
                  $this->log_line("Course $coursecode not found in Moodle's course idnumbers.");
              } else {
                // Create the (hidden) course(s) if not found
                $course = new object();
                $course->fullname = $group->description;
                $course->shortname = $coursecode;
                $course->idnumber = $coursecode;
                $course->format = 'topics';
                $course->visible = 0;
                // Insert default names for teachers/students, from the current language
                $site = get_site();
                if (current_language() == $CFG->lang) {
                    $course->teacher  = $site->teacher;
                    $course->teachers = $site->teachers;
                    $course->student  = $site->student;
                    $course->students = $site->students;
                } else {
                    $course->teacher = get_string("defaultcourseteacher");
                    $course->teachers = get_string("defaultcourseteachers");
                    $course->student = get_string("defaultcoursestudent");
                    $course->students = get_string("defaultcoursestudents");
                }

                // Handle course categorisation (taken from the group.org.orgunit field if present)
                if(strlen($group->category)>0){
                    // If the category is defined and exists in Moodle, we want to store it in that one
                    if($catid = get_field('course_categories', 'id', 'name', addslashes($group->category))){
                        $course->category = $catid;
                    }elseif($CFG->enrol_createnewcategories){
                        // Else if we're allowed to create new categories, let's create this one
                        $newcat->name = $group->category;
                       $newcat->visible = 0;
                       if($catid = insert_record('course_categories', addslashes_object($newcat))){
                           $course->category = $catid;
                           $this->log_line("Created new (hidden) category, #$catid: $newcat->name");
                       }else{
                           $this->log_line('Failed to create new category: '.$newcat->name.' so using default category instead.');
                           $course->category = 1;
                       }
                    }else{
                        // If not found and not allowed to create, stick with default
                        $this->log_line('Category '.$group->category.' not found in Moodle database, so using default category instead.');
                        $course->category = 1;
                    }
                }else{
                    $course->category = 1;
                }
                $course->timecreated = time();
                $course->startdate = time();
                $course->numsections = 1;
                // Choose a sort order that puts us at the start of the list!
                $sortinfo = get_record_sql('SELECT MIN(sortorder) AS min,
                           MAX(sortorder) AS max
                            FROM ' . $CFG->prefix . 'course WHERE category<>0');
                if (is_object($sortinfo)) { // no courses?
                    $max   = $sortinfo->max;
                    $min   = $sortinfo->min;
                    unset($sortinfo);
                    $course->sortorder = $min - 1;
                }else{
                    $course->sortorder = 1000;
                }
                if($course->id = insert_record('course', addslashes_object($course))){

                    // Setup the blocks
                    $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
                    blocks_repopulate_page($page); // Return value not checked because you can always edit later

                    $section = new object();
                    $section->course = $course->id;   // Create a default section.
                    $section->section = 0;
                    $section->id = insert_record("course_sections", $section);

                    add_to_log(SITEID, "course", "new", "view.php?id=$course->id", "$course->fullname (ID $course->id)");

                    $this->log_line("Created course $coursecode in Moodle (Moodle ID is $course->id)");
                }else{
                    $this->log_line('Failed to create course '.$coursecode.' in Moodle');
                }
              }
            }elseif($recstatus==3 && ($courseid = get_field('course', 'id', 'idnumber', $coursecode))){
                // If course does exist, but recstatus==3 (delete), then set the course as hidden
                set_field('course', 'visible', '0', 'id', $courseid);
            }
        } // End of foreach(coursecode)
    }
} // End process_group_tag()

/**
* Process the person tag. This defines a Moodle user.
* @param string $tagconents The raw contents of the XML element
*/
function process_person_tag($tagcontents){
    global $CFG;

    if(preg_match('{<sourcedid>.*?<id>(.+?)</id>.*?</sourcedid>}is', $tagcontents, $matches)){
        $person->idnumber = trim($matches[1]);
    }
    if(preg_match('{<name>.*?<n>.*?<given>(.+?)</given>.*?</n>.*?</name>}is', $tagcontents, $matches)){
        $person->firstname = trim($matches[1]);
    }
    if(preg_match('{<name>.*?<n>.*?<family>(.+?)</family>.*?</n>.*?</name>}is', $tagcontents, $matches)){
        $person->lastname = trim($matches[1]);
    }
    if(preg_match('{<userid>(.*?)</userid>}is', $tagcontents, $matches)){
        $person->username = trim($matches[1]);
    }
    if($CFG->enrol_imssourcedidfallback && trim($person->username)==''){
      // This is the point where we can fall back to useing the "sourcedid" if "userid" is not supplied
      // NB We don't use an "elseif" because the tag may be supplied-but-empty
        $person->username = $person->idnumber;
    }
    if(preg_match('{<email>(.*?)</email>}is', $tagcontents, $matches)){
        $person->email = trim($matches[1]);
    }
    if(preg_match('{<url>(.*?)</url>}is', $tagcontents, $matches)){
        $person->url = trim($matches[1]);
    }
    if(preg_match('{<adr>.*?<locality>(.+?)</locality>.*?</adr>}is', $tagcontents, $matches)){
        $person->city = trim($matches[1]);
    }
    if(preg_match('{<adr>.*?<country>(.+?)</country>.*?</adr>}is', $tagcontents, $matches)){
        $person->country = trim($matches[1]);
    }

    // Fix case of some of the fields if required
    if($CFG->enrol_fixcaseusernames && isset($person->username)){
        $person->username = strtolower($person->username);
    }
    if($CFG->enrol_fixcasepersonalnames){
        if(isset($person->firstname)){
            $person->firstname = ucwords(strtolower($person->firstname));
        }
        if(isset($person->lastname)){
            $person->lastname = ucwords(strtolower($person->lastname));
        }
    }

    $recstatus = ($this->get_recstatus($tagcontents, 'person'));


    // Now if the recstatus is 3, we should delete the user if-and-only-if the setting for delete users is turned on
    // In the "users" table we can do this by setting deleted=1
    if($recstatus==3){

        if($CFG->enrol_imsdeleteusers){ // If we're allowed to delete user records
            // Make sure their "deleted" field is set to one
            set_field('user', 'deleted', 1, 'username', $person->username);
            $this->log_line("Marked user record for user '$person->username' (ID number $person->idnumber) as deleted.");
        }else{
            $this->log_line("Ignoring deletion request for user '$person->username' (ID number $person->idnumber).");
        }

    }else{ // Add or update record


        // If the user exists (matching sourcedid) then we don't need to do anything.
        if(!get_field('user', 'id', 'idnumber', $person->idnumber) && $CFG->enrol_createnewusers){
            // If they don't exist and haven't a defined username, we log this as a potential problem.
            if((!isset($person->username)) || (strlen($person->username)==0)){
                $this->log_line("Cannot create new user for ID # $person->idnumber - no username listed in IMS data for this person.");
            }elseif(get_field('user', 'id', 'username', $person->username)){
                // If their idnumber is not registered but their user ID is, then add their idnumber to their record
                set_field('user', 'idnumber', addslashes($person->idnumber), 'username', $person->username);
            }else{

            // If they don't exist and they have a defined username, and $CFG->enrol_createnewusers == true, we create them.
            $person->lang = 'manual'; //TODO: this needs more work due tu multiauth changes
            $person->auth = $CFG->auth;
            $person->confirmed = 1;
            $person->timemodified = time();
            $person->mnethostid = $CFG->mnet_localhost_id;
            if($id = insert_record('user', addslashes_object($person))){
    /*
    Photo processing is deactivated until we hear from Moodle dev forum about modification to gdlib.

                                 //Antoni Mas. 07/12/2005. If a photo URL is specified then we might want to load
                                 // it into the user's profile. Beware that this may cause a heavy overhead on the server.
                                 if($CFG->enrol_processphoto){
                                   if(preg_match('{<photo>.*?<extref>(.*?)</extref>.*?</photo>}is', $tagcontents, $matches)){
                                     $person->urlphoto = trim($matches[1]);
                                   }
                                   //Habilitam el flag que ens indica que el personatge t foto prpia.
                                   $person->picture = 1;
                                   //Llibreria creada per nosaltres mateixos.
                                   require_once($CFG->dirroot.'/lib/gdlib.php');
                                   if ($usernew->picture = save_profile_image($id, $person->urlphoto,'user')) {
                                     set_field('user', 'picture', $usernew->picture, 'id', $id);  /// Note picture in DB
                                   }
                                 }
    */
                    $this->log_line("Created user record for user '$person->username' (ID number $person->idnumber).");
                }else{
                    $this->log_line("Database error while trying to create user record for user '$person->username' (ID number $person->idnumber).");
                }
            }
        }elseif($CFG->enrol_createnewusers){
            $this->log_line("User record already exists for user '$person->username' (ID number $person->idnumber).");

            // Make sure their "deleted" field is set to zero.
            set_field('user', 'deleted', 0, 'idnumber', $person->idnumber);
        }else{
            $this->log_line("No user record found for '$person->username' (ID number $person->idnumber).");
        }

    } // End of are-we-deleting-or-adding

} // End process_person_tag()

/**
* Process the membership tag. This defines whether the specified Moodle users
* should be added/removed as teachers/students.
* @param string $tagconents The raw contents of the XML element
*/
function process_membership_tag($tagcontents){
    global $CFG;
    $memberstally = 0;
    $membersuntally = 0;

    // In order to reduce the number of db queries required, group name/id associations are cached in this array:
    $groupids = array();

    if(preg_match('{<sourcedid>.*?<id>(.+?)</id>.*?</sourcedid>}is', $tagcontents, $matches)){
        $ship->coursecode = ($CFG->enrol_truncatecoursecodes > 0)
                                 ? substr(trim($matches[1]), 0, intval($CFG->enrol_truncatecoursecodes))
                                 : trim($matches[1]);
        $ship->courseid = get_field('course', 'id', 'idnumber', $ship->coursecode);
    }
    if($ship->courseid && preg_match_all('{<member>(.*?)</member>}is', $tagcontents, $membermatches, PREG_SET_ORDER)){
        foreach($membermatches as $mmatch){
            unset($member);
            unset($memberstoreobj);
            if(preg_match('{<sourcedid>.*?<id>(.+?)</id>.*?</sourcedid>}is', $mmatch[1], $matches)){
                $member->idnumber = trim($matches[1]);
            }
            if(preg_match('{<role\s+roletype=["\'](.+?)["\'].*?>}is', $mmatch[1], $matches)){
                $member->roletype = trim($matches[1]); // 01 means Student, 02 means Instructor, 3 means ContentDeveloper, and there are more besides
            }elseif($CFG->enrol_imscapitafix && preg_match('{<roletype>(.+?)</roletype>}is', $mmatch[1], $matches)){
                // The XML that comes out of Capita Student Records seems to contain a misinterpretation of the IMS specification!
                $member->roletype = trim($matches[1]); // 01 means Student, 02 means Instructor, 3 means ContentDeveloper, and there are more besides
            }
            if(preg_match('{<role\b.*?<status>(.+?)</status>.*?</role>}is', $mmatch[1], $matches)){
                $member->status = trim($matches[1]); // 1 means active, 0 means inactive - treat this as enrol vs unenrol
            }

            $recstatus = ($this->get_recstatus($mmatch[1], 'role'));
            if($recstatus==3){
              $member->status = 0; // See above - recstatus of 3 (==delete) is treated the same as status of 0
              //echo "<p>process_membership_tag: unenrolling member due to recstatus of 3</p>";
            }

            $timeframe->begin = 0;
            $timeframe->end = 0;
            if(preg_match('{<role\b.*?<timeframe>(.+?)</timeframe>.*?</role>}is', $mmatch[1], $matches)){
                $timeframe = $this->decode_timeframe($matches[1]);
            }
            if(preg_match('{<role\b.*?<extension>.*?<cohort>(.+?)</cohort>.*?</extension>.*?</role>}is', $mmatch[1], $matches)){
                $member->groupname = trim($matches[1]);
                // The actual processing (ensuring a group record exists, etc) occurs below, in the enrol-a-student clause
            }

            $rolecontext = get_context_instance(CONTEXT_COURSE, $ship->courseid);
            $rolecontext = $rolecontext->id; // All we really want is the ID
//$this->log_line("Context instance for course $ship->courseid is...");
//print_r($rolecontext);

            // Add or remove this student or teacher to the course...
            $memberstoreobj->userid = get_field('user', 'id', 'idnumber', $member->idnumber);
            $memberstoreobj->enrol = 'imsenterprise';
            $memberstoreobj->course = $ship->courseid;
            $memberstoreobj->time = time();
            $memberstoreobj->timemodified = time();
            if($memberstoreobj->userid){

                // Decide the "real" role (i.e. the Moodle role) that this user should be assigned to.
                // Zero means this roletype is supposed to be skipped.
                $moodleroleid = $this->rolemappings[$member->roletype];
                if(!$moodleroleid){
                    $this->log_line("SKIPPING role $member->roletype for $memberstoreobj->userid ($member->idnumber) in course $memberstoreobj->course");
                    continue;
                }

                if(intval($member->status) == 1){

                    // Enrol unsing the generic role_assign() function

                    if ((!role_assign($moodleroleid, $memberstoreobj->userid, 0, $rolecontext, $timeframe->begin, $timeframe->end, 0, 'imsenterprise')) && (trim($memberstoreobj->userid)!='')) {
                        $this->log_line("Error enrolling user #$memberstoreobj->userid ($member->idnumber) to role $member->roletype in course $memberstoreobj->course");
                    }else{
                        $this->log_line("Enrolled user #$memberstoreobj->userid ($member->idnumber) to role $member->roletype in course $memberstoreobj->course");
                        $memberstally++;

                        // At this point we can also ensure the group membership is recorded if present
                        if(isset($member->groupname)){
                            // Create the group if it doesn't exist - either way, make sure we know the group ID
                            if(isset($groupids[$member->groupname])){
                                $member->groupid = $groupids[$member->groupname]; // Recall the group ID from cache if available
                            }else{
                                if($groupid = get_field('groups', 'id', 'name', addslashes($member->groupname), 'courseid', $ship->courseid)){
                                    $member->groupid = $groupid;
                                    $groupids[$member->groupname] = $groupid; // Store ID in cache
                                }else{
                                    // Attempt to create the group
                                    $group->name = addslashes($member->groupname);
                                    $group->courseid = $ship->courseid;
                                    $group->timecreated = time();
                                    $group->timemodified = time();
                                    $groupid = insert_record('groups', $group);
                                    $this->log_line('Added a new group for this course: '.$group->name);
                                    $groupids[$member->groupname] = $groupid; // Store ID in cache
                                    $member->groupid = $groupid;
                                }
                            }
                            // Add the user-to-group association if it doesn't already exist
                            if($member->groupid) {
                                groups_add_member($member->groupid, $memberstoreobj->userid);
                            }
                        } // End of group-enrolment (from member.role.extension.cohort tag)

                    }
                }elseif($CFG->enrol_imsunenrol){
                    // Unenrol

                    if (! role_unassign($moodleroleid, $memberstoreobj->userid, 0, $rolecontext, 'imsenterprise')) {
                        $this->log_line("Error unenrolling $memberstoreobj->userid from role $moodleroleid in course");
                    }else{
                        $membersuntally++;
                        $this->log_line("Unenrolled $member->idnumber from role $moodleroleid in course");
                    }
                }

            }
        }
        $this->log_line("Added $memberstally users to course $ship->coursecode");
        if($membersuntally > 0){
            $this->log_line("Removed $membersuntally users from course $ship->coursecode");
        }
    }
} // End process_membership_tag()

/**
* Process the properties tag. The only data from this element
* that is relevant is whether a <target> is specified.
* @param string $tagconents The raw contents of the XML element
*/
function process_properties_tag($tagcontents){
    global $CFG;

    if($CFG->enrol_imsrestricttarget){
        if(!(preg_match('{<target>'.preg_quote($CFG->enrol_imsrestricttarget).'</target>}is', $tagcontents, $matches))){
            $this->log_line("Skipping processing: required target \"$CFG->enrol_imsrestricttarget\" not specified in this data.");
            $this->continueprocessing = false;
        }
    }
}

/**
* Store logging information. This does two things: uses the {@link mtrace()}
* function to print info to screen/STDOUT, and also writes log to a text file
* if a path has been specified.
* @param string $string Text to write (newline will be added automatically)
*/
function log_line($string){
    mtrace($string);
    if($this->logfp) {
        fwrite($this->logfp, $string . "\n");
    }
}

/**
* Process the INNER contents of a <timeframe> tag, to return beginning/ending dates.
*/
function decode_timeframe($string){ // Pass me the INNER CONTENTS of a <timeframe> tag - beginning and/or ending is returned, in unix time, zero indicating not specified
    $ret->begin = $ret->end = 0;
    // Explanatory note: The matching will ONLY match if the attribute restrict="1"
    // because otherwise the time markers should be ignored (participation should be
    // allowed outside the period)
    if(preg_match('{<begin\s+restrict="1">(\d\d\d\d)-(\d\d)-(\d\d)</begin>}is', $string, $matches)){
        $ret->begin = mktime(0,0,0, $matches[2], $matches[3], $matches[1]);
    }
    if(preg_match('{<end\s+restrict="1">(\d\d\d\d)-(\d\d)-(\d\d)</end>}is', $string, $matches)){
        $ret->end = mktime(0,0,0, $matches[2], $matches[3], $matches[1]);
    }
    return $ret;
} // End decode_timeframe

/**
* Load the role mappings (from the config), so we can easily refer to
* how an IMS-E role corresponds to a Moodle role
*/
function load_role_mappings() {
    $this->rolemappings = array();
    foreach($this->imsroles as $imsrolenum=>$imsrolename) {
        $this->rolemappings[$imsrolenum] = $this->rolemappings[$imsrolename]
            = get_field('config', 'value', 'name', 'enrol_imse_imsrolemap' . $imsrolenum);
    }
}

} // end of class

?>
