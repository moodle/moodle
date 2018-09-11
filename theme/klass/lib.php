<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * lib.php
 *
 * @package   theme_klass
 * @copyright 2015 LMSACE Dev Team, lmsace.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * Get the pre scss for the theme
 * @param string $theme
 * @return string $scss.
 */
function theme_klass_get_pre_scss($theme) {
    global $CFG;
    $scss = '';
    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }
    $logo = $theme->setting_file_url('logo', 'logo');
    $scss .= theme_klass_set_logo($scss, $logo);
    $scss .= theme_klass_set_fontwww();
    return $scss;
}

/**
 * Add the custom scss into the theme scss.
 *
 * @param string $theme
 * @return string
 */
function theme_klass_get_extra_scss($theme) {
    return !empty($theme->settings->customcss) ? $theme->settings->customcss : '';
}

/**
 * Get the main scss content for the theme.
 *
 * @param string $theme
 * @return string
 */
function theme_klass_get_main_scss_content($theme) {
    global $CFG;
    $theme = theme_config::load('boost');
    $scss = theme_boost_get_main_scss_content($theme);
    $themescssfile = $CFG->dirroot.'/theme/klass/scss/preset/theme.scss';
    if ( file_exists($themescssfile) ) {
        $scss .= file_get_contents($themescssfile);
    }
    return $scss;
}

/**
 * Load the Jquery and migration files
 * Load the our theme js file
 *
 * @param  moodle_page $page [description]
 */
function theme_klass_page_init(moodle_page $page) {
    $page->requires->jquery();
    $page->requires->js('/theme/klass/javascript/theme.js');
}

/**
 * Loads the CSS Styles and replace the background images.
 * If background image not available in the settings take the default images.
 *
 * @param string $css
 * @param string $theme
 * @return string
 */
function theme_klass_process_css($css, $theme) {
    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = theme_klass_set_logo($css, $logo);
    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_klass_set_customcss($css, $customcss);
    $css = theme_klass_pre_css_set_fontwww($css);
    return $css;
}

/**
 * Adds the logo to CSS.
 *
 * @param string $scss The CSS.
 * @param string $logo The URL of the logo.
 * @return string The parsed CSS
 */
function theme_klass_set_logo($scss, $logo) {
    $tag = '$logo: ';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $scss = str_replace($tag, $replacement, $scss);
    return $scss;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_klass_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('klass');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'footerlogo') {
            return $theme->setting_file_serve('footerlogo', $args, $forcedownload, $options);
        } else if ($filearea === 'style') {
            theme_klass_serve_css($args[1]);
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if (preg_match("/slide[1-9][0-9]*image/", $filearea) !== false) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Serves CSS for image file updated to styles.
 *
 * @param string $filename
 * @return string
 */
function theme_klass_serve_css($filename) {
    global $CFG;
    if (!empty($CFG->themedir)) {
        $thestylepath = $CFG->themedir . '/klass/style/';
    } else {
        $thestylepath = $CFG->dirroot . '/theme/klass/style/';
    }
    $thesheet = $thestylepath . $filename;
    $etagfile = md5_file($thesheet);
    // File.
    $lastmodified = filemtime($thesheet);
    // Header.
    $ifmodifiedsince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
    $etagheader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
    if ((($ifmodifiedsince) && (strtotime($ifmodifiedsince) == $lastmodified)) || $etagheader == $etagfile) {
        theme_klass_send_unmodified($lastmodified, $etagfile);
    }
    theme_klass_send_cached_css($thestylepath, $filename, $lastmodified, $etagfile);
}


/**
 * Set browser cache used in php header.
 *
 * @param  string $lastmodified
 * @param  string $etag
 */
function theme_klass_send_unmodified($lastmodified, $etag) {
    $lifetime = 60 * 60 * 24 * 60;
    header('HTTP/1.1 304 Not Modified');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Content-Type: text/css; charset=utf-8');
    header('Etag: "' . $etag . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    die;
}

/**
 * Cached css.
 * @param  string $path
 * @param  string $filename
 * @param  int $lastmodified
 * @param  string $etag
 */
function theme_klass_send_cached_css($path, $filename, $lastmodified, $etag) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/configonlylib.php');
    // 60 days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;
    header('Etag: "' . $etag . '"');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . filesize($path . $filename));
    }
    readfile($path . $filename);
    die;
}


/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_klass_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add Clean specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_klass_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG;
    $return = new stdClass;
    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }
    if (!empty($page->theme->settings->logo)) {
        $return->heading = html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'logo'));
    } else {
        $return->heading = $output->page_heading();
    }
    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">'.format_text($page->theme->settings->footnote).'</div>';
    }
    return $return;
}

/**
 * Loads the CSS Styles and put the font path
 *
 * @return string
 */
function theme_klass_set_fontwww() {
    global $CFG, $PAGE;

    $themewww = $CFG->wwwroot."/theme";
    $theme = theme_config::load('klass');
    $fontwww = '$fontwww: "'. $themewww.'/klass/fonts/"'.";\n";
    return $fontwww;
}

/**
 * Process the css for font url
 * @param string $css
 * @return string
 */
function theme_klass_pre_css_set_fontwww($css) {
    global $CFG, $PAGE;

    $themewww = $CFG->wwwroot."/theme";
    $tag = '[[setting:fontwww]]';
    $theme = theme_config::load('klass');
    $css = str_replace($tag, $themewww.'/klass/fonts/', $css);
    return $css;
}


// Logo Image URL Fetch from theme settings.
// @ return string.
if (!function_exists('get_logo_url')) {
    /**
     * get_logo_url description
     *
     * @param  string $type
     * @return image
     */
    function get_logo_url($type = 'header') {
        global $OUTPUT;
        static $theme;
        if ( empty($theme)) {
            $theme = theme_config::load('klass');
        }
        if ($type == "header") {
            $logo = $theme->setting_file_url('logo', 'logo');
            $logo = empty($logo) ? $OUTPUT->image_url('home/logo', 'theme') : $logo;
        } else if ($type == "footer") {
            $logo = $theme->setting_file_url('footerlogo', 'footerlogo');
            $logo = empty($logo) ? '' : $logo;
        }
        return $logo;
    }
}

/**
 * Renderer the slideimg
 *
 * @param int $p
 * @param string $sliname
 * @return image
 */
function theme_klass_render_slideimg($p, $sliname) {
    global $PAGE, $OUTPUT;
    $nos = theme_klass_get_setting('numberofslides');
    $i = $p % 3;
    $slideimage = $OUTPUT->image_url('home/slide'.$i, 'theme');
    // Get slide image or fallback to default.
    if (theme_klass_get_setting($sliname)) {
        $slideimage = $PAGE->theme->setting_file_url($sliname , $sliname);
    }
    return $slideimage;
}

/**
 * Function to get the theme setting
 * @param  string $setting
 * @param  boolean $format
 * @return string
 */
function theme_klass_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('klass');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}

// Return the current theme url.
// @ return string.
if (!function_exists('theme_url')) {
    /**
     * theme_url
     *
     * @return string
     */
    function theme_url() {
        global $CFG, $PAGE;
        $themeurl = $CFG->wwwroot.'/theme/'. $PAGE->theme->name;
        return $themeurl;
    }
}

/**
 * Get the infolinks from settings page and display on the footer.
 * @return type|string
 */
function theme_klass_infolink() {
    $infolink = theme_klass_get_setting('infolink');
    $content = "";
    $infosettings = explode("\n", $infolink);
    foreach ($infosettings as $key => $settingval) {
        $expset = explode("|", $settingval);
        if (isset($expset[0]) && isset($expset[1]) ) {
            list($ltxt, $lurl) = $expset;
        } else {
            $ltxt = $expset[0];
            $lurl = "#";
        }
        $ltxt = trim($ltxt);
        $lurl = trim($lurl);

        if (empty($ltxt)) {
            continue;
        }
        $content .= '<li><a href="'.$lurl.'" target="_blank">'.$ltxt.'</a></li>';
    }

    return $content;
}