<?PHP //$Id$
    //This file returns the required rss feeds
    //The URL format MUST include:
    //    course: the course id
    //    user: the user id
    //    name: the name of the module (forum...)
    //    id: the id (instance) of the module (forumid...)
    //If the course has a password or it doesn't
    //allow guest access then the user field is 
    //required to see that the user is enrolled
    //in the course, else no check is performed.
    //This allows to limit a bit the rss access
    //to correct users. It isn't unbreakable,
    //obviously, but its the best I've thought!!

    require_once("../config.php");
    require_once("$CFG->dirroot/files/mimetypes.php");

    $allowed = true;
    $error = false;

    if (empty($CFG->filelifetime)) {
        $CFG->filelifetime = 86400;     /// Seconds for files to remain in caches
    }

    if (isset($file)) {     // workaround for situations where / syntax doesn't work
        $pathinfo = $file;
    } else {
        $pathinfo = get_slash_arguments("file.php");
    }

    if (!$pathinfo) {
        $error = true;
    }

    $pathinfo = urldecode($pathinfo);

    if (! $args = parse_slash_arguments($pathinfo)) {
        $error = true;
    }

    $numargs = count($args);
    if ($numargs < 5 or empty($args[1])) {
        $error = true;
    }

    $courseid = (integer)$args[0];
    $userid = (integer)$args[1];
    $modulename = $args[2];
    $instance = (integer)$args[3];

    if (! $course = get_record("course", "id", $courseid)) {
        $error = true;
    }

    //Get course_module to check it's visible
    if (! $cm = get_coursemodule_from_instance($modulename,$instance,$courseid)) {
        $error = true;
    }
    $cmvisible = $cm->visible;

    $isstudent = isstudent($courseid,$userid);
    $isteacher = isteacher($courseid,$userid);

    //Check for "security" if !course->guest or course->password
    if (!$course->guest || $course->password) {
        $allowed = ($isstudent || $isteacher); 
    }

    //Check for "security" if the course is hidden or the activity is hidden 
    if ($allowed && (!$course->visible || !$cmvisible)) {
        $allowed = $isteacher;
    }

    $pathname = $CFG->dataroot."/rss/".$modulename."/".$instance.".xml";
    $filename = $args[$numargs-1];

    //If the file exists and its allowed for me, download it!
    if (file_exists($pathname) && $allowed && !$error) {
        $lastmodified = filemtime($pathname);
        $mimetype = mimeinfo("type", $filename);
    
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $CFG->filelifetime) . " GMT");
        header("Cache-control: max_age = $CFG->filelifetime");
        header("Pragma: ");
        header("Content-disposition: inline; filename=$filename");
    
        header("Content-length: ".filesize($pathname));
        header("Content-type: $mimetype");
        readfile($pathname);
    }

?>
