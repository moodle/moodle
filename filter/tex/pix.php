<?PHP // $Id$
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../../config.php");

    $CFG->texfilterdir = "filter/tex";
    $CFG->teximagedir = "filter/tex";

    error_reporting(E_ALL);

    $lifetime = 86400;
    if (isset($file)) {     // workaround for situations where / syntax doesn't work
        $pathinfo = '/' . $file;
    } else {
        $pathinfo = get_slash_arguments("pix.php");
    }

    if (! $args = parse_slash_arguments($pathinfo)) {
        error("No valid arguments supplied");
    }

    $numargs = count($args);

    if ($numargs == 1) {
        $image  = $args[0];
        $pathname = "$CFG->dataroot/$CFG->teximagedir/$image";
        $filetype = "image/gif";
    } else {
        error("No valid arguments supplied");
    }


    if (!file_exists($pathname)) {
        $md5 = str_replace('.gif','',$image);
        if ($texcache = get_record("cache_filters", "filter", "tex", "md5key", $md5)) {
            if (!file_exists("$CFG->dataroot/$CFG->teximagedir")) {
                make_upload_directory($CFG->teximagedir);
            }

            $texexp = $texcache->rawtext;
            $texexp = str_replace('&lt;','<',$texexp);
            $texexp = str_replace('&gt;','>',$texexp);
            $texexp = preg_replace('!\r\n?!',' ',$texexp);
            $texexp = '\Large ' . $texexp;
            switch (PHP_OS) {
                case "Linux":
                    system("QUERY_STRING=;export QUERY_STRING;$CFG->dirroot/$CFG->texfilterdir/mimetex.linux -d ". escapeshellarg($texexp) . "  >$pathname");
                break;
                case "WINNT":
                case "Windows":
                    $texexp = str_replace('"','\"',$texexp);
                    system("$CFG->dirroot/$CFG->texfilterdir/mimetex.exe -e  $pathname \"$texexp\"");
                break;
                case "Darwin":
                    system("QUERY_STRING=;export QUERY_STRING;$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin -d ". escapeshellarg($texexp) . "  >$pathname");
                break;
            }
        }
    }

    if (file_exists($pathname)) {
        $lastmodified = filemtime($pathname);
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
        header("Cache-control: max_age = $lifetime"); // a day
        header("Pragma: ");
        header("Content-disposition: inline; filename=$image");
        header("Content-length: ".filesize($pathname));
        header("Content-type: $filetype");
        readfile("$pathname");
    } else {
        echo "Image not found!";
    }

    exit;
?>
