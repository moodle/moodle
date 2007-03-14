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
        if (array_search('filter/tex', $filters) === FALSE) {
            error ('Filter not enabled!');
        }
    }

    require_once($CFG->libdir.'/filelib.php');
    require_once('defaultsettings.php' );
    require_once('latex.php');

    $CFG->texfilterdir = 'filter/tex';
    $CFG->teximagedir  = 'filter/tex';

    // check/initialise default configuration for filter (in defaultsettings.php)
    tex_defaultsettings();

    $cmd    = '';               // Initialise these variables
    $status = '';

    error_reporting(E_ALL);

    $relativepath = get_file_argument('pix.php');

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 1) {
        $image    = $args[0];
        $pathname = $CFG->dataroot.'/'.$CFG->teximagedir.'/'.$image;
    } else {
        error('No valid arguments supplied');
    }

    if (!file_exists($pathname)) {
        $md5 = str_replace('.gif','',$image);
        if ($texcache = get_record('cache_filters', 'filter', 'tex', 'md5key', $md5)) {
            if (!file_exists($CFG->dataroot.'/'.$CFG->teximagedir)) {
                make_upload_directory($CFG->teximagedir);
            }

            // try and render with latex first
            $latex = new latex();
            $density = $CFG->filter_tex_density;
            $background = $CFG->filter_tex_latexbackground;
            $latex_path = $latex->render( $texcache->rawtext, $md5, 12, $density, $background );
            if ($latex_path) {    
                copy( $latex_path, $pathname );
                $latex->clean_up( $md5 );
            }
            else {                    
                // failing that, use mimetex
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

                        case "FreeBSD":
                            $cmd = "\"$CFG->dirroot/$CFG->texfilterdir/mimetex.freebsd\" -e \"$pathname\" $texexp";
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
    }

    if (file_exists($pathname)) {
        send_file($pathname, $image);
    } else {
        if ($CFG->debug > 7) {
            echo "The shell command<br />$cmd<br />returned status = $status<br />\n";
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/$CFG->texfilterdir/texdebug.php\">debugging script</a>";
        } else {
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/$CFG->texfilterdir/texdebug.php\">debugging script</a><br />";
            echo "Please turn on debug mode in site configuration to see more info here.";
        }
    }
?>
