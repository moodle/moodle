<?PHP  // $Id$

    require("../config.php");

    $info = document_file("files.php", false);

    include($info->filepath);

    print_header();

    echo "<FONT SIZE=2 FACE=\"san-serif\">";
    foreach ($string as $file => $filename) {
        echo "<LI><A TARGET=\"main\" HREF=\"$CFG->wwwroot/doc/?file=$file\">$filename</A></LI>";
    }
    echo "</FONT>";
    
?>
