<?PHP // $Id$
      // This function fetches files from the data directory
      // Syntax:   file.php/courseid/dir/.../dir/filename.ext

    require_once("config.php");
    require_once("files/mimetypes.php");

    $lifetime = 86400;

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
    $courseid = (integer)$args[0];

    $course = get_record("course", "id", $courseid);

    if ($course->category) {
        require_login($courseid);
    }

    // it's OK to get here if no course was specified

    $pathname = "$CFG->dataroot$pathinfo";
    $filename = $args[$numargs-1];

    if (file_exists($pathname)) {
        $lastmodified = filemtime($pathname);
        $mimetype = mimeinfo("type", $filename);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
        header("Cache-control: max_age = $lifetime"); // a day
        header("Pragma: ");
        header("Content-disposition: inline; filename=$filename");
        header("Content-length: ".filesize($pathname));


        if (empty($CFG->filteruploadedfiles)) {
            header("Content-type: $mimetype");
            readfile($pathname);

        } else {     /// Try and put the file through filters
            if ($mimetype == "text/html") {
                header("Content-type: text/html");
                echo format_text(implode('', file($pathname)), FORMAT_HTML, NULL, $courseid);
    
            } else if ($mimetype == "text/plain") {
                header("Content-type: text/html");
                $options->newlines = false;
                echo "<pre>";
                echo format_text(implode('', file($pathname)), FORMAT_MOODLE, $options, $courseid);
                echo "</pre>";
    
            } else {    /// Just send it out raw
                header("Content-type: $mimetype");
                readfile($pathname);
            }
        }
    } else {
        error("Sorry, but the file you are looking for was not found ($pathname)", "course/view.php?id=$courseid");
    }

    exit;
?>
