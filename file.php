<?PHP // $Id$
      // This function fetches files from the data directory
      // Syntax:   file.php/courseid/dir/.../dir/filename.ext

    require("config.php");
    require("files/mimetypes.php");

    $lifetime = 86400;

    if (!$PATH_INFO) {
        error("This script DEPENDS on $PATH_INFO being available.  Read the README.");
    }

    $args = get_slash_arguments();
    $numargs = count($args);
    $courseid = (integer)$args[0];

    $course = get_record("course", "id", $courseid);

    if ($course->category) {
        require_login($courseid);
    }

    // it's OK to get here if no course was specified

    $pathname = "$CFG->dataroot$PATH_INFO";
    $filename = $args[$numargs-1];

    $mimetype = mimeinfo("type", $filename);

    if (file_exists($pathname)) {
        $lastmodified = filemtime($pathname);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
        header("Cache-control: max_age = $lifetime"); // a day
        header("Pragma: ");
        header("Content-Length: ".filesize($pathname));
        header("Content-type: $mimetype");
        readfile("$pathname");
    } else {
        error("Sorry, but the file you are looking for was not found", "course/view.php?id=$courseid");
    }

    exit;
?>
