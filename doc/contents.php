<?PHP  // $Id$

    require("../config.php");

    if (! $info = document_file("files.php", false)) {
        error("404 - File Not Found");
    }

    $string = array();
    if ($CFG->forcelogin) {
        require_login();
    }

    include($info->filepath);

    print_header();

    $ulopen = false;

    foreach ($string as $file => $filename) {
        if (substr($file,0,1) == "-") {
            if($ulopen) {
                echo '</ul>';
            }
            echo '<h1>'.$filename.'</h1><ul>';
            $ulopen = true;
        } else {
            echo '<li><a target="main" href="'.$CFG->wwwroot.'/doc/?file='.$file.'">'.$filename.'</a></li>';
        }
    }
    if($ulopen) {
        echo '</ul>';
    }

    // Sloppy way to produce valid markup... there should be a print_footer_minimal().
    echo '</div></div></body></html>';
    
?>
