<?PHP  // $Id$

    require("../config.php");

    require_variable($id);      // course context
    require_variable($file);    // file in this directory to view

    if (! $course = get_record("course", "id", $id)) {
        error("Course is misconfigured");
    }

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    $file = clean_filename($file);

    if (file_exists($file)) {
        $strhelp = get_string("help");
        print_header("$course->shortname: $strhelp", "$course->fullname", 
                     "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> -> $strhelp");
        echo "<BLOCKQUOTE>";
        include($file);
        echo "</BLOCKQUOTE>";
    }

?>

