<?php // $Id$ preview for insert image dialog
    
    include("../../../config.php");
    require("../../../files/mimetypes.php");
    
    require_variable($id);
    require_variable($imageurl);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can use this functionality");
    }

    $imageurl = rawurldecode($imageurl);   /// Full URL starts with $CFG->wwwroot/file.php
    $imagepath = str_replace("$CFG->wwwroot/file.php", '', $imageurl);
    $imagepath = str_replace("?file=", '', $imagepath); // if we're using second option of file path.
    
    if ($imagepath != $imageurl) {         /// This is an internal image
        $size = getimagesize($CFG->dataroot.$imagepath);
    }
    
    $width = $size[0];
    $height = $size[1];
    settype($width, "integer");
    settype($height, "integer");
    
    if ($height >= 200) {
        $division = ($height / 190);
        $width = round($width / $division);
        $height = 190;
    }
    
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>Preview</title>\n";
    echo "<style type=\"text/css\">\n";
    echo " body { margin: 2px; }\n";
    echo "</style>\n";
    echo "</head>\n";
    echo "<body bgcolor=\"#ffffff\">\n";
    print "<img src=\"$imageurl\" width=\"$width\" height=\"$height\" alt=\"\">";
    echo "</body>\n</html>\n";
    
?>
