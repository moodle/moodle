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

    // disable moodle specific debug messages
    disable_debugging();

    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/filter/tex/lib.php');

    $cmd    = '';               // Initialise these variables
    $status = '';

    //error_reporting(E_ALL);

    $relativepath = get_file_argument('pix.php');

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 1) {
        $image    = $args[0];
        $pathname = $CFG->dataroot.'/filter/algebra/'.$image;
    } else {
        error('No valid arguments supplied');
    }

    if (!file_exists($pathname)) {
        $md5 = str_replace('.gif','',$image);
        if ($texcache = get_record('cache_filters', 'filter', 'algebra', 'md5key', $md5)) {
            if (!file_exists($CFG->dataroot.'/filter/algebra')) {
                make_upload_directory('filter/algebra');
            }

            $texexp = $texcache->rawtext;
            $texexp = str_replace('&lt;','<',$texexp);
            $texexp = str_replace('&gt;','>',$texexp);
            $texexp = preg_replace('!\r\n?!',' ',$texexp);
            $texexp = '\Large ' . $texexp;
            $cmd = tex_filter_get_cmd($pathname, $texexp);
            system($cmd, $status);
        }
    }

    if (file_exists($pathname)) {
        send_file($pathname, $image);
    } else {
        if (debugging()) {
            echo "The shell command<br />$cmd<br />returned status = $status<br />\n";
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/algebra/algebradebug.php\">debugging script</a>";
        } else {
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/algebra/algebradebug.php\">debugging script</a><br />";
            echo "Please turn on debug mode in site configuration to see more info here.";
        }
    }
?>
