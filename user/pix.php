<?PHP // $Id$
      // This function fetches user pictures from the data directory
      // Syntax:   file.php/userid/f1.jpg

    require("../config.php");

    $lifetime = 86400;

    if (!$PATH_INFO) {
        error("This script DEPENDS on $PATH_INFO being available.  Read the README.");
    }

    $args = get_slash_arguments();
    $numargs = count($args);

    if ($numargs == 2) {
        $userid = (integer)$args[0];
        $image  = $args[1];
    } else {
        $userid = 0;
        $image  = "f1.jpg";
    }

    $pathname = "$CFG->dataroot/users/$userid/$image";
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
