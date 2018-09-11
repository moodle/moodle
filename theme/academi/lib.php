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
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param type|string $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_academi_get_pre_scss($theme) {
    $scss = theme_academi_set_fontwww();
    return $scss;
}

/**
 * Get extra scss from settings.
 * @param type|string $theme
 * @return type|string
 */
function theme_academi_get_extra_scss($theme) {
    return !empty($theme->settings->customcss) ? $theme->settings->customcss : '';
}

/**
 * Get theme main scss content.
 * @param type|string $theme
 * @return string
 */
function theme_academi_get_main_scss_content($theme) {
    global $CFG;
    $scss = '';
    $parentconfig = theme_config::load('boost');
    $scss .= theme_boost_get_main_scss_content($parentconfig);
    $themescssfile = $CFG->dirroot.'/theme/academi/scss/preset/theme.scss';
    if ( file_exists($themescssfile) ) {
        $scss .= file_get_contents($themescssfile);
    }
    return $scss;
}

/**
 * Load the Jquery and migration files
 * Load the our theme js file
 * @param moodle_page $page.
 */
function theme_academi_page_init(moodle_page $page) {
    $page->requires->jquery();
    $page->requires->js('/theme/academi/javascript/theme.js');
}

/**
 * Loads the CSS Styles and replace the background images.
 * If background image not available in the settings take the default images.
 *
 * @param string $css
 * @param string $theme
 * @return string
 */
function theme_academi_process_css($css, $theme) {
    global $OUTPUT, $CFG;
    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = theme_academi_pre_css_set_fontwww($css);
    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    return $css;
}

/**
 * Adds the logo to CSS.
 *
 * @param string $css The CSS.
 * @param string $logo The URL of the logo.
 * @return string The parsed CSS
 */
function theme_academi_set_logo($css, $logo) {
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
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
function theme_academi_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;

    if (empty($theme)) {
        $theme = theme_config::load('academi');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {

        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'style') {
            theme_academi_serve_css($args[1]);
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
function theme_academi_serve_css($filename) {
    global $CFG;
    if (!empty($CFG->themedir)) {
        $thestylepath = $CFG->themedir . '/academi/style/';
    } else {
        $thestylepath = $CFG->dirroot . '/theme/academi/style/';
    }
    $thesheet = $thestylepath . $filename;

    /* http://css-tricks.com/snippets/php/intelligent-php-cache-control/ - rather than /lib/csslib.php as it is a static file who's
      contents should only change if it is rebuilt.  But! There should be no difference with TDM on so will see for the moment if
      that decision is a factor. */

    $etagfile = md5_file($thesheet);
    // File.
    $lastmodified = filemtime($thesheet);
    // Header.
    $ifmodifiedsince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
    $etagheader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

    if ((($ifmodifiedsince) && (strtotime($ifmodifiedsince) == $lastmodified)) || $etagheader == $etagfile) {
        theme_academi_send_unmodified($lastmodified, $etagfile);
    }
    theme_academi_send_cached_css($thestylepath, $filename, $lastmodified, $etagfile);
}

/**
 * Set browser cache used in php header.
 * @param type|string $lastmodified
 * @param type|string $etag
 */
function theme_academi_send_unmodified($lastmodified, $etag) {
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
 *  Cached css for theme_academi
 *
 * @param type|string $path
 * @param type|string $filename
 * @param type|string $lastmodified
 * @param type|string $etag
 */
function theme_academi_send_cached_css($path, $filename, $lastmodified, $etag) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/configonlylib.php');  // For min_enable_zlib_compression function.
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
 * @param type|string $css The original CSS.
 * @param type|string $customcss The custom CSS to add.
 * @return type|string The CSS which now contains our custom CSS.
 */
function theme_academi_set_customcss($css, $customcss) {
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
function theme_academi_get_html_for_settings(renderer_base $output, moodle_page $page) {
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
 * @param type|string $css
 * @return type|string
 */
function theme_academi_pre_css_set_fontwww($css) {
    global $CFG, $PAGE;
    if (empty($CFG->themewww)) {
        $themewww = $CFG->wwwroot."/theme";
    } else {
        $themewww = $CFG->themewww;
    }

    $tag = '[[setting:fontwww]]';
    $theme = theme_config::load('academi');
    $css = str_replace($tag, $themewww.'/academi/fonts/', $css);
    return $css;
}

/**
 * Load the font folder path into the scss.
 * @param type|string $css
 * @return type|string
 */
function theme_academi_set_fontwww() {
    global $CFG, $PAGE;
    if (empty($CFG->themewww)) {
        $themewww = $CFG->wwwroot."/theme";
    } else {
        $themewww = $CFG->themewww;
    }
    $theme = theme_config::load('academi');
    $fontwww = '$fontwww: "'.$themewww.'/academi/fonts/"'.";\n";
    return $fontwww;
}


// Logo Image URL Fetch from theme settings.
// @ return string.
if (!function_exists('get_logo_url')) {
    /**
     * Description
     * @return type|string
     */
    function get_logo_url() {
        global $OUTPUT;
        static $theme;
        if (empty($theme)) {
            $theme = theme_config::load('academi');
        }
        $logo = $theme->setting_file_url('logo', 'logo');
        $logo = empty($logo) ? $OUTPUT->image_url('home/logo', 'theme') : $logo;
        return $logo;
    }
}

/**
 * Renderer the slider images.
 * @param type|string $p
 * @param type|string $sliname
 * @return type|string
 */
function theme_academi_render_slideimg($p, $sliname) {
    global $PAGE, $OUTPUT;
    $nos = theme_academi_get_setting('numberofslides');
    $i = $p % 3;
    $slideimage = $OUTPUT->image_url('home/slide'.$i, 'theme');
    // Get slide image or fallback to default.
    if (theme_academi_get_setting($sliname)) {
        $slideimage = $PAGE->theme->setting_file_url($sliname , $sliname);
    }
    return $slideimage;
}

/**
 * Description
 * @param type|string $setting
 * @param type|bool $format
 * @return type|string
 */
function theme_academi_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('academi');
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
     * Url for theme_academi.
     * @return type|string
     */
    function theme_url() {
        global $CFG, $PAGE;
        $themeurl = $CFG->wwwroot.'/theme/'. $PAGE->theme->name;
        return $themeurl;
    }
}

/**
 * Footer Info links.
 * @return type|string
 */
function  theme_academi_infolink() {
    $infolink = theme_academi_get_setting('infolink');
    $content = "";
    $infosettings = explode("\n", $infolink);
    foreach ($infosettings as $key => $settingval) {

        $expset = explode("|", $settingval);
        if (isset($expset[1])) {
            list($ltxt, $lurl) = $expset;
        }

        if (isset($ltxt) != '' || isset($lurl) != '') {
            $ltxt = trim($ltxt);
            $lurl = trim($lurl);
        }
        if (empty($ltxt)) {
            continue;
        }
        $content .= '<li><a href="'.$lurl.'" target="_blank">'.$ltxt.'</a></li>';
    }
    return $content;
}


