<?PHP // $Id$
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once("../../config.php");

    $CFG->algebrafilterdir = "filter/algebra";
    $CFG->texfilterdir     = "filter/tex";
    $CFG->algebraimagedir  = "filter/algebra";

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
        $pathname = "$CFG->dataroot/$CFG->algebraimagedir/$image";
        $filetype = "image/gif";
    } else {
        error("No valid arguments supplied");
    }


    if (!file_exists($pathname)) {
        $md5 = str_replace('.gif','',$image);
        if ($texcache = get_record("cache_filters", "filter", "algebra", "md5key", $md5)) {
            if (!file_exists("$CFG->dataroot/$CFG->algebraimagedir")) {
                make_upload_directory($CFG->algebraimagedir);
            }

            $texexp = $texcache->rawtext;
            $texexp = str_replace('&lt;','<',$texexp);
            $texexp = str_replace('&gt;','>',$texexp);
            $texexp = preg_replace('!\r\n?!',' ',$texexp);
            $texexp = '\Large ' . $texexp;

            if ((PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows")) {
                $texexp = str_replace('"','\"',$texexp);
                $cmd = "$CFG->dirroot/$CFG->texfilterdir/mimetex.exe";
                $cmd = str_replace(' ','^ ',$cmd);
                $cmd .= " ++ -e  \"$pathname\" \"$texexp\"";
            } else if (is_executable("$CFG->dirroot/$CFG->texfilterdir/mimetex")) {   /// Use the custom binary

                $cmd = "$CFG->dirroot/$CFG->texfilterdir/mimetex -e $pathname ". escapeshellarg($texexp);
                
            } else {                                                           /// Auto-detect the right TeX binary
                switch (PHP_OS) {

                    case "Linux":
                        $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.linux\" -e \"$pathname\" ". escapeshellarg($texexp);
                    break;

                    case "Darwin":
                        $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin\" -e \"$pathname\" ". escapeshellarg($texexp);
                    break;

                    default:      /// Nothing was found, so tell them how to fix it.
                        echo "Make sure you have an appropriate MimeTeX binary here:\n\n"; 
                        echo "    $CFG->dirroot/$CFG->texfilterdir/mimetex\n\n";
                        echo "and that it has the right permissions set on it as executable program.\n\n";
                        echo "You can get the latest binaries for your ".PHP_OS." platform from: \n\n";
                        echo "    http://moodle.org/download/mimetex/";
                        exit;
                    break;
                }
            }
            system($cmd, $status);
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
        echo "The shell command<br>$cmd<br>returned status = $status<br>\n";
        echo "Image not found!<br>";
        echo "Please try the <a href=\"$CFG->wwwroot/filter/algebra/algebradebug.php\">debugging script</a>";
    }

    exit;
?>
