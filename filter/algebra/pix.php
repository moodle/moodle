<?PHP // $Id$
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

    $nomoodlecookie = true;     // Because it interferes with caching

    require_once('../../config.php');

    if (empty($CFG->textfilters)) {
        error ('Filter not enabled!');
    } else {
        $filters = explode(',', $CFG->textfilters);
        if (array_search('filter/algebra', $filters) === FALSE) {
            error ('Filter not enabled!');
        }
    }

    require_once($CFG->libdir.'/filelib.php');

    $CFG->texfilterdir     = 'filter/tex';
    $CFG->algebrafilterdir = 'filter/algebra';
    $CFG->algebraimagedir  = 'filter/algebra';


    $cmd    = '';               // Initialise these variables
    $status = '';

    //error_reporting(E_ALL);

    $relativepath = get_file_argument('pix.php');

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 1) {
        $image    = $args[0];
        $pathname = $CFG->dataroot.'/'.$CFG->algebraimagedir.'/'.$image;
    } else {
        error('No valid arguments supplied');
    }

    if (!file_exists($pathname)) {
        $md5 = str_replace('.gif','',$image);
        if ($texcache = get_record('cache_filters', 'filter', 'algebra', 'md5key', $md5)) {
            if (!file_exists($CFG->dataroot.'/'.$CFG->algebraimagedir)) {
                make_upload_directory($CFG->algebraimagedir);
            }

            $texexp = $texcache->rawtext;
            $texexp = str_replace('&lt;','<',$texexp);
            $texexp = str_replace('&gt;','>',$texexp);
            $texexp = preg_replace('!\r\n?!',' ',$texexp);
            $texexp = '\Large ' . $texexp;
            $texexp = escapeshellarg($texexp);

            if ((PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows")) {
                $cmd = "$CFG->dirroot/$CFG->texfilterdir/mimetex.exe";
                $cmd = str_replace(' ','^ ',$cmd);
                $cmd .= " ++ -e  \"$pathname\" -- $texexp";
            } else if (is_executable("$CFG->dirroot/$CFG->texfilterdir/mimetex")) {   /// Use the custom binary

                $cmd = "$CFG->dirroot/$CFG->texfilterdir/mimetex -e $pathname -- $texexp";

            } else {                                                           /// Auto-detect the right TeX binary
                switch (PHP_OS) {

                    case "Linux":
                        $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.linux\" -e \"$pathname\" -- $texexp";
                    break;

                    case "Darwin":
                        $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.darwin\" -e \"$pathname\" -- $texexp";
                    break;

                    default:      /// Nothing was found, so tell them how to fix it.
                        if ($CFG->debug > 7) {
                            echo "Make sure you have an appropriate MimeTeX binary here:\n\n";
                            echo "    $CFG->dirroot/$CFG->texfilterdir/mimetex\n\n";
                            echo "and that it has the right permissions set on it as executable program.\n\n";
                            echo "You can get the latest binaries for your ".PHP_OS." platform from: \n\n";
                            echo "    http://moodle.org/download/mimetex/";
                        } else {
                            echo "Mimetex executable was not found,\n";
                            echo "Please turn on debug mode in site configuration to see more info here.";
                        }
                        die;
                    break;
                }
            }
            system($cmd, $status);
        }
    }

    if (file_exists($pathname)) {
        send_file($pathname, $image);
    } else {
        if ($CFG->debug > 7) {
            echo "The shell command<br />$cmd<br />returned status = $status<br />\n";
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/$CFG->algebrafilterdir/algebradebug.php\">debugging script</a>";
        } else {
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/$CFG->algebrafilterdir/algebradebug.php\">debugging script</a><br />";
            echo "Please turn on debug mode in site configuration to see more info here.";
        }
    }
?>
