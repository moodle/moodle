<?PHP // $Id$
      // This function fetches files from the data directory
      // Syntax:   file.php/courseid/dir/.../dir/filename.ext

    require_once("config.php");
    require_once("files/mimetypes.php");

    if (empty($CFG->filelifetime)) {
        $CFG->filelifetime = 86400;     /// Seconds for files to remain in caches
    }

    if (isset($file)) {     // workaround for situations where / syntax doesn't work
        $pathinfo = $file;
    } else {
        $pathinfo = get_slash_arguments("file.php");
    }

    if (!$pathinfo) {
        error("No file parameters!");
    }

    $pathinfo = urldecode($pathinfo);

    if (! $args = parse_slash_arguments($pathinfo)) {
        error("No valid arguments supplied");
    }

    $numargs = count($args);
    if ($numargs < 2 or empty($args[1])) {
        error("No valid arguments supplied");
    }

    $courseid = (integer)$args[0];

    $course = get_record("course", "id", $courseid);

    if ($course->category) {
        require_login($courseid);
    } else if ($CFG->forcelogin) {
        require_login();
    }

    // it's OK to get here if no course was specified

    $pathname = "$CFG->dataroot$pathinfo";
    if ($pathargs = explode("?",$pathname)) {
        $pathname = $pathargs[0];            // Only keep what's before the '?'
    }
    $filename = $args[$numargs-1];
    if ($fileargs = explode("?",$filename)) {
        $filename = $fileargs[0];            // Only keep what's before the '?'
    }

    if (file_exists($pathname)) {
        $lastmodified = filemtime($pathname);
        $mimetype = mimeinfo("type", $filename);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $CFG->filelifetime) . " GMT");
        header("Cache-control: max_age = $CFG->filelifetime");
        header("Pragma: ");
        header("Content-disposition: inline; filename=$filename");


        if (empty($CFG->filteruploadedfiles)) {
            header("Content-length: ".filesize($pathname));
            header("Content-type: $mimetype");
            readfile($pathname);

        } else {     /// Try and put the file through filters
            if ($mimetype == "text/html") {
                $output = format_text(implode('', file($pathname)), FORMAT_HTML, NULL, $courseid);

                header("Content-length: ".strlen($output));
                header("Content-type: text/html");
                echo $output;
    
            } else if ($mimetype == "text/plain") {
                $options->newlines = false;
                $output = '<pre>'.format_text(implode('', file($pathname)), FORMAT_MOODLE, $options, $courseid).'</pre>';
                header("Content-length: ".strlen($output));
                header("Content-type: text/html");
                echo $output;
    
            } else {    /// Just send it out raw
                header("Content-length: ".filesize($pathname));
                header("Content-type: $mimetype");
                readfile($pathname);
            }
        }
    } else {
        error("Sorry, but the file you are looking for was not found ($pathname)", "course/view.php?id=$courseid");
    }

    exit;
?>
