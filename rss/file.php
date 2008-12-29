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

    // hack for problems with concurrent use of $nomoodlecookie and capabilities MDL-7243
    // it should be replaced once we get to codes in urls 
    $USER = new object();
    $USER->id = 0;

    // disable moodle specific debug messages
    disable_debugging();

    $relativepath = get_file_argument('file.php');


    if (!$relativepath) {
        rss_not_found();
    }

    // extract relative path components
    $args = explode('/', trim($relativepath, '/'));

    if (count($args) < 5) {
        rss_not_found();
    }

    $courseid   = (int)$args[0];
    $userid     = (int)$args[1];
    $modulename = clean_param($args[2], PARAM_FILE);
    $instance   = $args[3];
    $filename   = 'rss.xml';

    if ($isblog = $modulename == 'blog') {
       $blogid   = (int)$args[4];  // could be groupid / courseid  / userid  depending on $instance
       if ($args[5] != 'rss.xml') {
           $tagid = (int)$args[5];
       } else {
           $tagid = 0;
       }
    } else {
        $instance = (int)$instance;  // we know it's an id number
    }


    if (!$course = get_record('course', 'id', $courseid)) {
        rss_not_found();
    }

    //Check name of module
    if (!$isblog) {
        $mods = get_list_of_plugins('mod');
        if (!in_array(strtolower($modulename), $mods)) {
            rss_not_found();
        }
        //Get course_module to check it's visible
        if (!$cm = get_coursemodule_from_instance($modulename,$instance,$courseid)) {
            rss_not_found();
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $isuser = has_capability('moodle/course:view', $context, $userid);   // Not ideal, this should be module-specific, but deferring until RSS gets a revamp with codes in the URLs
    } else {
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $isuser = has_capability('moodle/course:view', $context, $userid);
    }
    
    //Check for "security" if !course->guest or course->password
    if ($course->id != SITEID) {
        if ((!$course->guest || $course->password) && (!$isuser)) {
            rss_not_found();
        }
    }

    //Do not allow acesss to hidden courses or activities because we can not trust user id - this will be improved in 2.0
    if (!$isblog and (!$course->visible || !$cm->visible)) {
        rss_not_found();
    }

    //Work out the filename of the RSS file
    if ($isblog) {
        require_once($CFG->dirroot.'/blog/rsslib.php');
        $pathname = blog_generate_rss_feed($instance, $blogid, $tagid);
    } else {
        $pathname = $CFG->dataroot.'/rss/'.$modulename.'/'.$instance.'.xml';
    }

    //Check that file exists
    if (!file_exists($pathname)) {
        rss_not_found();
    }

    //Send it to user!
    send_file($pathname, $filename, $lifetime);

    function rss_not_found() {
        /// error, send some XML with error message
        global $lifetime, $filename;
        send_file(rss_geterrorxmlfile(), $filename, $lifetime, false, true);
    }
?>
