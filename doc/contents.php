<?PHP  // $Id$

    require("../config.php");

    if (! $info = document_file("files.php", false)) {
        error("404 - File Not Found");
    }

    include($info->filepath);

    print_header();

    echo "<font size=2 face=\"san-serif\">";
    foreach ($string as $file => $filename) {
        echo "<li><a target=\"main\" href=\"$CFG->wwwroot/doc/?file=$file\">$filename</a></li>";
    }
    echo "</font>";
    
?>
