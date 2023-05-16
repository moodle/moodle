<?php
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true); // Because it interferes with caching

    require_once('../../config.php');

    if (!filter_is_enabled('tex')) {
        throw new \moodle_exception('filternotenabled');
    }

    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/filter/tex/lib.php');
    require_once($CFG->dirroot.'/filter/tex/latex.php');

    $cmd    = '';               // Initialise these variables
    $status = '';

    $relativepath = get_file_argument();

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 1) {
        $image    = $args[0];
        $pathname = $CFG->dataroot.'/filter/tex/'.$image;
    } else {
        throw new \moodle_exception('invalidarguments', 'error');
    }

    if (!file_exists($pathname)) {
        $convertformat = get_config('filter_tex', 'convertformat');
        if (strpos($image, '.png')) {
            $convertformat = 'png';
        }
        $md5 = str_replace(".{$convertformat}", '', $image);
        if ($texcache = $DB->get_record('cache_filters', array('filter'=>'tex', 'md5key'=>$md5))) {
            if (!file_exists($CFG->dataroot.'/filter/tex')) {
                make_upload_directory('filter/tex');
            }

            // try and render with latex first
            $latex = new latex();
            $density = get_config('filter_tex', 'density');
            $background = get_config('filter_tex', 'latexbackground');
            $texexp = $texcache->rawtext; // the entities are now decoded before inserting to DB
            $lateximage = $latex->render($texexp, $image, 12, $density, $background);
            if ($lateximage) {
                copy($lateximage, $pathname);
            } else {
                // failing that, use mimetex
                $texexp = $texcache->rawtext;
                $texexp = str_replace('&lt;', '<', $texexp);
                $texexp = str_replace('&gt;', '>', $texexp);
                $texexp = preg_replace('!\r\n?!', ' ', $texexp);
                $texexp = '\Large '.$texexp;
                $cmd = filter_tex_get_cmd($pathname, $texexp);
                system($cmd, $status);
            }
        }
    }

    if (file_exists($pathname)) {
        send_file($pathname, $image, YEARSECS, 0, false, false, '', false, [
            'cacheability' => 'public',
            'immutable' => true,
        ]);
    } else {
        if (debugging()) {
            echo "The shell command<br />$cmd<br />returned status = $status<br />\n";
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/tex/texdebug.php\">debugging script</a>";
        } else {
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/tex/texdebug.php\">debugging script</a><br />";
            echo "Please turn on debug mode in site configuration to see more info here.";
        }
    }
