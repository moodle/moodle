<?PHP  // $Id$

    require("../config.php");

    $id = optional_param('id',0,PARAM_INT); // course context
    $file = required_param('file',PARAM_FILE);    // file in this directory to view

    $file = clean_filename($file);

    if ($CFG->forcelogin) {
        require_login();
    }

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course is misconfigured");
        }
        $strhelp = get_string("help");
        print_header("$course->shortname: $strhelp", "$course->fullname", 
                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> -> $strhelp");
    } else {
        if (! $site = get_site()) {
            error("Site is misconfigured");
        }
        $strdocumentation = get_string("documentation");
        print_header("$site->shortname: $strhelp", "$site->fullname", 
                     "<a href=\"view.php?file=contents.html\">$strdocumentation</a>");
        
    }

    echo "<blockquote>";

    if (! document_file($file, true)) {
        notify("404 - File Not Found");
    }

    echo "</blockquote>";

?>

