<?php // $Id$
      // This function fetches group pictures from the data directory
      // Syntax:   pix.php/groupid/f1.jpg or pix.php/groupid/f2.jpg
      //     OR:   ?file=groupid/f1.jpg or ?file=groupid/f2.jpg

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../config.php");

    $lifetime = 86400;

    if (isset($file)) {     // workaround for situations where / syntax doesn't work
        $pathinfo = $file;

    } else {
        $pathinfo = get_slash_arguments("pixgroup.php");
    }

    if (! $args = parse_slash_arguments($pathinfo)) {
        error("No valid arguments supplied");
    }

    $numargs = count($args);

    if ($numargs == 2) {
        $groupid = (integer)$args[0];
        $image  = $args[1];
        $pathname = "$CFG->dataroot/groups/$groupid/$image";
        $filetype = "image/jpeg";
    } else {
        $pathname = "$CFG->dirroot/pix/g/f1.png";
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
