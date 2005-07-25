<?PHP //$Id$
    //This file returns the required rss feeds
    //The URL format MUST include:
    //    course: the course id
    //    user:   the user id
    //    name:   the name of the module (forum...)
    //    id:     the id (instance) of the module (forumid...)
    //If the course has a password or it doesn't
    //allow guest access then the user field is 
    //required to see that the user is enrolled
    //in the course, else no check is performed.
    //This allows to limit a bit the rss access
    //to correct users. It isn't unbreakable,
    //obviously, but its the best I've thought!!

    $nomoodlecookie = true;     // Because it interferes with caching
 
    require_once('../config.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->libdir.'/rsslib.php');


    $lifetime = 3600;  // Seconds for files to remain in caches - 1 hour

    $relativepath = get_file_argument('file.php');

    if (!$relativepath) {
        not_found();
    }

    // extract relative path components
    $args = explode('/', trim($relativepath, '/'));
    
    if (count($args) < 5) {
        not_found();
    }

    $courseid   = (int)$args[0];
    $userid     = (int)$args[1];
    $modulename = clean_param($args[2], PARAM_FILE);
    $instance   = (int)$args[3];
    $filename   = 'rss.xml';
    
    if (!$course = get_record("course", "id", $courseid)) {
        not_found();
    }
    
    //Check name of module
    $mods = get_list_of_plugins("mod");
    if (!in_array(strtolower($modulename), $mods)) {
        not_found();
    }

    //Get course_module to check it's visible
    if (!$cm = get_coursemodule_from_instance($modulename,$instance,$courseid)) {
        not_found();
    }

    $isstudent = isstudent($courseid,$userid);
    $isteacher = isteacher($courseid,$userid);

    //Check for "security" if !course->guest or course->password
    if ($course->id != SITEID) {
        if ((!$course->guest || $course->password) && (!($isstudent || $isteacher))) {
            not_found();
        }
    }

    //Check for "security" if the course is hidden or the activity is hidden 
    if ((!$course->visible || !$cm->visible) && (!$isteacher)) {
        not_found();
    }

    $pathname = $CFG->dataroot.'/rss/'.$modulename.'/'.$instance.'.xml';

    //Check that file exists
    if (!file_exists($pathname)) {
        not_found();
    }

    //Send it to user!
    send_file($pathname, $filename, $lifetime);

    function not_found() {
        /// error, send some XML with error message
        global $lifetime, $filename;
        send_file(rss_geterrorxmlfile(), $filename, $lifetime, false, true);
    }
?>
