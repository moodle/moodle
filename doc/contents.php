<?PHP  // $Id$

    require("../config.php");

    if (! $info = document_file("files.php", false)) {
        error("404 - File Not Found");
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    include($info->filepath);

    print_header();

    foreach ($string as $file => $filename) {
        if (substr($file,0,1) == "-") {
            echo '<p style="font-size:small;margin-bottom:0px;font-family:Trebuchet MS, Verdana, Arial, Helvetica, sans-serif;">'.
                  $filename.'</p>';
        } else {
            echo "<li style=\"font-size:small\"><a target=\"main\" href=\"$CFG->wwwroot/doc/?file=$file\">$filename</a></li>";
        }
    }
    
?>
