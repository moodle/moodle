<?PHP // $Id$
      // This function fetches user pictures from the data directory
      // Syntax:   pix.php/userid/f1.jpg or pix.php/userid/f2.jpg
      //     OR:   ?file=userid/f1.jpg or ?file=userid/f2.jpg

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../config.php");

    $lifetime = 86400;

    if (isset($file)) {     // workaround for situations where / syntax doesn't work
        $pathinfo = $file;

    } else {
        $pathinfo = get_slash_arguments("pix.php");
    }

    if (! $args = parse_slash_arguments($pathinfo)) {
        error("No valid arguments supplied");
    }

    $numargs = count($args);

    if ($numargs == 2) {
        $userid = (integer)$args[0];
        $image  = $args[1];
        $pathname = "$CFG->dataroot/users/$userid/$image";
        $filetype = "image/jpeg";
    } else {
        $pathname = "$CFG->dirroot/pix/u/f1.png";
        $filetype = "image/png";
    }

    $lastmodified = filemtime($pathname);

    if (file_exists($pathname)) {
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
        header("Cache-control: max_age = $lifetime"); // a day
        header("Pragma: ");
        header("Content-disposition: inline; filename=$image");
        header("Content-length: ".filesize($pathname));
        header("Content-type: $filetype");
        readfile("$pathname");
    }

    exit;
?>
