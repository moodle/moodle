<?php
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_filters db table
      // and uses LaTeX to create the image file.

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true); // Because it interferes with caching

    require_once('../../config.php');

    if (!filter_is_enabled('algebra')) {
        throw new \moodle_exception('filternotenabled');
    }

    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/filter/tex/lib.php');
    require_once($CFG->dirroot . '/filter/tex/latex.php');

    $relativepath = get_file_argument();

    $args = explode('/', trim($relativepath, '/'));

    if (count($args) == 1) {
        $image    = $args[0];
        $pathname = $CFG->dataroot.'/filter/algebra/'.$image;
    } else {
        throw new \moodle_exception('invalidarguments', 'error');
    }

    if (!file_exists($pathname)) {
        $convertformat = get_config('filter_algebra', 'convertformat');
        if (strpos($image, '.png')) {
            $convertformat = 'png';
        }
        $md5 = str_replace(".{$convertformat}", '', $image);
        if ($texcache = $DB->get_record('cache_filters', array('filter'=>'algebra', 'md5key'=>$md5))) {
            if (!file_exists($CFG->dataroot.'/filter/algebra')) {
                make_upload_directory('filter/algebra');
            }

            // Render with LaTeX.
            $latex = new latex('filter_algebra');
            $density = get_config('filter_algebra', 'density');
            $background = get_config('filter_algebra', 'latexbackground');
            $texexp = $texcache->rawtext;
            $lateximage = $latex->render($texexp, $image, 12, $density, $background);
            if ($lateximage) {
                copy($lateximage, $pathname);
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
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/algebra/algebradebug.php\">debugging script</a>";
        } else {
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/algebra/algebradebug.php\">debugging script</a><br />";
            echo "Please turn on debug mode in site configuration to see more info here.";
        }
    }

