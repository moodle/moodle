<?PHP // $Id$
      // This function fetches user pictures from the data directory
      // Syntax:   pix.php/userid/f1.jpg or pix.php/userid/f2.jpg
      //     OR:   ?file=userid/f1.jpg or ?file=userid/f2.jpg

    require("../config.php");

    $lifetime = 86400;

    if (isset($file)) {
        $PATH_INFO = $file;

    } else if (!$PATH_INFO) {
        $PATH_INFO = "";       // Will just show default picture
    }

    $args = get_slash_arguments();
    $numargs = count($args);

    if ($numargs == 2) {
        $userid = (integer)$args[0];
        $image  = $args[1];
        $pathname = "$CFG->dataroot/users/$userid/$image";
    } else {
        $pathname = "$CFG->dirroot/user/default/f1.jpg";
    }

    $lastmodified = filemtime($pathname);

    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
    header("Cache-control: max_age = $lifetime"); // a day
    header("Pragma: ");
    header("Content-Length: ".filesize($pathname));
    header("Content-type: image/jpeg");
    readfile("$pathname");

    exit;
?>
