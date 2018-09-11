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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_essential;

defined('MOODLE_INTERNAL') || die;

class toolbox {

    protected $corerenderer = null;
    protected static $instance;

    private function __construct() {
    }

    public static function get_instance() {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Sets the core_renderer class instance so that when purging all caches and 'theme_xxx_process_css' etc.
     * the settings are correct.
     * @param class core_renderer $core Child object of core_renderer class.
     */
    static public function set_core_renderer($core) {
        $us = self::get_instance();
        // Set only once from the initial calling lib.php process_css function so that subsequent parent calls do not override it.
        // Must happen before parents.
        if (null === $us->corerenderer) {
            $us->corerenderer = $core;
        }
    }

    /**
     * Finds the given setting in the theme from the themes' configuration object.
     * @param string $setting Setting name.
     * @param string $format false|'format_text'|'format_html'.
     * @return any false|value of setting.
     */
    static public function get_setting($setting, $format = false) {
        $us = self::check_corerenderer();

        $settingvalue = $us->get_setting($setting);

        global $CFG;
        require_once($CFG->dirroot . '/lib/weblib.php');
        if (empty($settingvalue)) {
            return false;
        } else if (!$format) {
            return $settingvalue;
        } else if ($format === 'format_text') {
            return format_text($settingvalue, FORMAT_PLAIN);
        } else if ($format === 'format_html') {
            return format_text($settingvalue, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
        } else if ($format === 'format_file_url') {
            return self::setting_file_url($setting, $setting);
        } else {
            return format_string($settingvalue);
        }
    }

    static public function setting_file_url($setting, $filearea) {
        $us = self::check_corerenderer();

        return $us->setting_file_url($setting, $filearea);
    }

    static public function pix_url($imagename, $component) {
        $us = self::check_corerenderer();
        return $us->pix_url($imagename, $component);
    }

    static public function getfontawesomemarkup($theicon, $classes = array(), $attributes = array(), $content = '') {
        $us = self::check_corerenderer();
        return $us->getfontawesomemarkup($theicon, $classes, $attributes, $content);
    }

    /**
     * States if course content search can be used.
     * @return boolean false|true if course content search can be used.
     */
    static public function course_content_search() {
        $canwe = false;
        if (self::get_setting('coursecontentsearch')) {
            global $PAGE;
            // MyDashboard uses columns3.php.  Change if needed. And 'process_content_search' below.
            $essentialsearch = new \moodle_url('index.php');
            $essentialsearch->param('sesskey', sesskey());
            $inspectorscourerdata = array('data' => array('theme' => $essentialsearch->out(false)));
            $PAGE->requires->js_call_amd('theme_essential/inspector_scourer', 'init', $inspectorscourerdata);

            \user_preference_allow_ajax_update('theme_essential_courseitemsearchtype', PARAM_INT);

            $canwe = true;
        }
        return $canwe;
    }

    static public function process_content_search() {
        $term = \optional_param('term', '', PARAM_TEXT);
        if ($term) {
            // Autocomplete AJAX call.
            global $CFG, $PAGE;

            // Might be overkill but would probably stop DOS attack from lots of DB reads.
            \require_sesskey();

            if ($CFG->forcelogin) {
                \require_login();
            }
            $courserenderer = $PAGE->get_renderer('core', 'course');

            echo json_encode($courserenderer->inspector_ajax($term));

            die();
        }

        $pref = \optional_param('pref', '', PARAM_TEXT);
        if (($pref) && ($pref == 'courseitemsearchtype')) {
            // Autocomplete AJAX user preference call.
            global $CFG;

            // Might be overkill but would probably stop DOS attack from lots of DB reads.
            \require_sesskey();

            if ($CFG->forcelogin) {
                \require_login();
            }

            $value = \optional_param('value', '', PARAM_INT);

            // Update.
            if (($value == 0) || ($value == 1)) {
                if (!\set_user_preference('theme_essential_courseitemsearchtype', $value)) {
                    print_error('errorsettinguserpref');
                }
                echo 'OK';
            } else {
                header('HTTP/1.1 406 Not Acceptable');
                echo 'Not Acceptable';
            }

            die();
        }
    }

    static private function check_corerenderer() {
        $us = self::get_instance();
        if (empty($us->corerenderer)) {
            // Use $OUTPUT unless is not a Essential or child core_renderer which can happen on theme switch.
            global $OUTPUT;
            if (property_exists($OUTPUT, 'essential')) {
                $us->corerenderer = $OUTPUT;
            } else {
                // Use $PAGE->theme->name as will be accurate than $CFG->theme when using URL theme changes.
                // Core 'allowthemechangeonurl' setting.
                global $PAGE;
                $corerenderer = null;
                try {
                    $corerenderer = $PAGE->get_renderer('theme_'.$PAGE->theme->name, 'core');
                } catch (\coding_exception $ce) {
                    // Specialised renderer may not exist in theme.  This is not a coding fault.  We just need to cope.
                    $corerenderer = null;
                }
                // Fallback check.
                if (($corerenderer != null) && (property_exists($corerenderer, 'essential'))) {
                    $us->corerenderer = $corerenderer;
                } else {
                    // Probably during theme switch, '$CFG->theme' will be accurrate.
                    global $CFG;
                    try {
                        $corerenderer = $PAGE->get_renderer('theme_'.$CFG->theme, 'core');
                    } catch (\coding_exception $ce) {
                        // Specialised renderer may not exist in theme.  This is not a coding fault.  We just need to cope.
                        $corerenderer = null;
                    }
                    if (($corerenderer != null) && (property_exists($corerenderer, 'essential'))) {
                        $us->corerenderer = $corerenderer;
                    } else {
                        // Last resort.  Hopefully will be fine on next page load for Child themes.
                        // However '***_process_css' in lib.php will be fine as it sets the correct renderer.
                        $us->corerenderer = $PAGE->get_renderer('theme_essential', 'core');
                    }
                }
            }
        }
        return $us->corerenderer;
    }

    /**
     * Finds the given tile file in the theme.
     * @param string $filename Filename without extension to get.
     * @return string Complete path of the file.
     */
    static public function get_tile_file($filename) {
        $us = self::check_corerenderer();
        return $us->get_tile_file($filename);
    }

    static public function get_categories_list() {
        static $catlist = null;
        if (empty($catlist)) {
            global $DB;
            $catlist = $DB->get_records('course_categories', null, 'sortorder', 'id, name, depth, path');

            foreach ($catlist as $category) {
                $category->parents = array();
                if ($category->depth > 1 ) {
                    $path = preg_split('|/|', $category->path, -1, PREG_SPLIT_NO_EMPTY);
                    $category->namechunks = array();
                    foreach ($path as $parentid) {
                        $category->namechunks[] = $catlist[$parentid]->name;
                        $category->parents[] = $parentid;
                    }
                    $category->parents = array_reverse($category->parents);
                } else {
                    $category->namechunks = array($category->name);
                }
            }
        }

        return $catlist;
    }

    // Report Page Title.
    static public function report_page_has_title() {
        global $PAGE;
        $hastitle = true;

        switch ($PAGE->pagetype) {
            case 'grade-report-overview-index':
                $hastitle = false;
                break;
            default:
                break;
        }

        return $hastitle;
    }

    // Page Bottom Region.
    static public function has_page_bottom_region() {
        global $PAGE;
        $hasregion = false;

        switch ($PAGE->pagetype) {
            case 'admin-plugins':
            case 'course-management':
            case 'mod-quiz-edit':
                $hasregion = true;
                break;
            case 'mod-assign-view':
                // Only apply to 'grading' page.
                if (optional_param('action', '', PARAM_TEXT) == 'grading') {
                    $hasregion = true;
                }
                break;
            default:
                break;
        }

        return $hasregion;
    }

    static public function showslider() {
        global $CFG;
        $noslides = self::get_setting('numberofslides');
        if ($noslides && (intval($CFG->version) >= 2013111800)) {
            $devicetype = \core_useragent::get_device_type(); // In useragent.php.
            if (($devicetype == "mobile") && self::get_setting('hideonphone')) {
                $noslides = false;
            } else if (($devicetype == "tablet") && self::get_setting('hideontablet')) {
                $noslides = false;
            }
        }
        return $noslides;
    }

    static public function render_indicators($numberofslides) {
        $indicators = '';
        for ($indicatorslideindex = 0; $indicatorslideindex < $numberofslides; $indicatorslideindex++) {
            $indicators .= '<li data-target="#essentialCarousel" data-slide-to="'.$indicatorslideindex.'"';
            if ($indicatorslideindex == 0) {
                $indicators .= ' class="active"';
            }
            $indicators .= '></li>';
        }
        return $indicators;
    }

    static public function render_slide($slideno, $captionoptions) {
        $slideurl = self::get_setting('slide'.$slideno.'url');
        $slideurltarget = self::get_setting('slide'.$slideno.'target');
        $slidetitle = format_string(self::get_setting('slide'.$slideno));
        $slidecaption = self::get_setting('slide'.$slideno.'caption', 'format_html');
        if ($slideurl) {
            // Strip links from the caption to prevent link in a link.
            $slidecaption = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $slidecaption);
        }
        if ($captionoptions == 0) {
            $slideextraclass = ' side-caption';
        } else {
            $slideextraclass = '';
        }
        $slideextraclass .= ($slideno === 1) ? ' active' : '';
        $slideimagealt = strip_tags($slidetitle);

        // Get slide image or fallback to default.
        $slideimage = self::get_setting('slide'.$slideno.'image');
        if ($slideimage) {
            $slideimage = self::setting_file_url('slide'.$slideno.'image', 'slide'.$slideno.'image');
        } else {
            $slideimage = self::pix_url('default_slide', 'theme');
        }

        if ($slideurl) {
            $slidecontent = '<a href="'.$slideurl.'" target="'.$slideurltarget.'" class="item'.$slideextraclass.'">';
        } else {
            $slidecontent = '<div class="item'.$slideextraclass.'">';
        }

        if ($captionoptions == 0) {
            $slidecontent .= '<div class="container-fluid">';
            $slidecontent .= '<div class="row-fluid">';

            if ($slidetitle || $slidecaption) {
                $slidecontent .= '<div class="span5 the-side-caption">';
                $slidecontent .= '<div class="the-side-caption-content">';
                $slidecontent .= '<h4>'.$slidetitle.'</h4>';
                $slidecontent .= '<div>'.$slidecaption.'</div>';
                $slidecontent .= '</div>';
                $slidecontent .= '</div>';
                $slidecontent .= '<div class="span7">';
            } else {
                $slidecontent .= '<div class="span10 offset1 nocaption">';
            }
            $slidecontent .= '<div class="carousel-image-container">';
            $slidecontent .= '<img src="'.$slideimage.'" alt="'.$slideimagealt.'" class="carousel-image">';
            $slidecontent .= '</div>';
            $slidecontent .= '</div>';

            $slidecontent .= '</div>';
            $slidecontent .= '</div>';
        } else {
            $nocaption = (!($slidetitle || $slidecaption)) ? ' nocaption' : '';
            $slidecontent .= '<div class="carousel-image-container'.$nocaption.'">';
            $slidecontent .= '<img src="'.$slideimage.'" alt="'.$slideimagealt.'" class="carousel-image">';
            $slidecontent .= '</div>';

            // Output title and caption if either is present.
            if ($slidetitle || $slidecaption) {
                $slidecontent .= '<div class="carousel-caption">';
                $slidecontent .= '<div class="carousel-caption-inner">';
                $slidecontent .= '<h4>'.$slidetitle.'</h4>';
                $slidecontent .= '<div>'.$slidecaption.'</div>';
                $slidecontent .= '</div>';
                $slidecontent .= '</div>';
            }
        }
        $slidecontent .= ($slideurl) ? '</a>' : '</div>';

        return $slidecontent;
    }

    static public function render_slide_controls($left) {
        $strprev = get_string('prev');
        $strnext = get_string('next');
        if ($left) {
            $arrowprev = 'left';
            $arrownext = 'right';
        } else {
            $arrowprev = 'right';
            $arrownext = 'left';
        }
        $prev = '<a class="left carousel-control" href="#essentialCarousel" data-slide="prev" aria-label="'.$strprev.'">';
        $prev .= '<span aria-hidden="true" class="fa fa-chevron-circle-'.$arrowprev.'"></span></a>';
        $next = '<a class="right carousel-control" href="#essentialCarousel" data-slide="next" aria-label="'.$strnext.'">';
        $next .= '<span aria-hidden="true" class="fa fa-chevron-circle-'.$arrownext.'"></span></a>';

        return $prev . $next;
    }

    /**
     * Checks if the user is switching colours with a refresh
     *
     * If they are this updates the users preference in the database
     */
    static protected function check_colours_switch() {
        $colours = \optional_param('essentialcolours', null, PARAM_ALPHANUM);
        if (in_array($colours, array('default', 'alternative1', 'alternative2', 'alternative3', 'alternative4'))) {
            \set_user_preference('theme_essential_colours', $colours);
        }
    }

    /**
     * Adds the JavaScript for the colour switcher to the page.
     *
     * The colour switcher is a YUI moodle module that is located in
     *     theme/udemspl/yui/udemspl/udemspl.js
     *
     * @param moodle_page $page
     */
    static public function initialise_colourswitcher(\moodle_page $page) {
        self::check_colours_switch();
        \user_preference_allow_ajax_update('theme_essential_colours', PARAM_ALPHANUM);
        $page->requires->js_call_amd('theme_essential/coloursswitcher', 'init',
            array(array('div' => '#custom_menu_themecolours .dropdown-menu')));
    }

    /**
     * Gets the theme colours the user has selected if enabled or the default if they have never changed.
     *
     * @param string $default The default theme colors to use.
     * @return string The theme colours the user has selected.
     */
    static public function get_colours($default = 'default') {
        $preference = \get_user_preferences('theme_essential_colours', $default);
        foreach (range(1, 4) as $alternativethemenumber) {
            if ($preference == 'alternative'.$alternativethemenumber &&
                self::get_setting('enablealternativethemecolors'.$alternativethemenumber)) {
                return $preference;
            }
        }
        return $default;
    }

    static public function set_font($css, $type, $fontname) {
        $familytag = '[[setting:' . $type . 'font]]';
        $facetag = '[[setting:fontfiles' . $type . ']]';
        if (empty($fontname)) {
            $familyreplacement = 'Verdana';
            $facereplacement = '';
        } else if (self::get_setting('fontselect') === '3') {

            $fontfiles = array();
            $fontfileeot = self::setting_file_url('fontfileeot'.$type, 'fontfileeot'.$type);
            if (!empty($fontfileeot)) {
                $fontfiles[] = "url('".$fontfileeot."?#iefix') format('embedded-opentype')";
            }
            $fontfilewoff = self::setting_file_url('fontfilewoff'.$type, 'fontfilewoff'.$type);
            if (!empty($fontfilewoff)) {
                $fontfiles[] = "url('".$fontfilewoff."') format('woff')";
            }
            $fontfilewofftwo = self::setting_file_url('fontfilewofftwo' . $type, 'fontfilewofftwo'.$type);
            if (!empty($fontfilewofftwo)) {
                $fontfiles[] = "url('".$fontfilewofftwo."') format('woff2')";
            }
            $fontfileotf = self::setting_file_url('fontfileotf'.$type, 'fontfileotf'.$type);
            if (!empty($fontfileotf)) {
                $fontfiles[] = "url('".$fontfileotf."') format('opentype')";
            }
            $fontfilettf = self::setting_file_url('fontfilettf'.$type, 'fontfilettf'.$type);
            if (!empty($fontfilettf)) {
                $fontfiles[] = "url('".$fontfilettf."') format('truetype')";
            }
            $fontfilesvg = self::setting_file_url('fontfilesvg'.$type, 'fontfilesvg'.$type);
            if (!empty($fontfilesvg)) {
                $fontfiles[] = "url('".$fontfilesvg."') format('svg')";
            }

            if (!empty($fontfiles)) {
                $familyreplacement = '"'.$fontname.'"';
                $facereplacement = '@font-face {'.PHP_EOL.'font-family: "'.$fontname.'";'.PHP_EOL;
                $facereplacement .= !empty($fontfileeot) ? "src: url('".$fontfileeot."');".PHP_EOL : '';
                $facereplacement .= "src: ";
                $facereplacement .= implode(",".PHP_EOL." ", $fontfiles);
                $facereplacement .= ";".PHP_EOL."}";
            } else {
                // No files back to default.
                $familyreplacement = 'Verdana';
                $facereplacement = '';
            }
        } else {
            $familyreplacement = '"'.$fontname.'"';
            $facereplacement = '';
        }

        $css = str_replace($familytag, $familyreplacement, $css);
        $css = str_replace($facetag, $facereplacement, $css);

        return $css;
    }

    static public function set_color($css, $themecolor, $tag, $defaultcolour, $alpha = null) {
        if (!($themecolor)) {
            $replacement = $defaultcolour;
        } else {
            $replacement = $themecolor;
        }
        if (!is_null($alpha)) {
            $replacement = self::hex2rgba($replacement, $alpha);
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_alternativecolor($css, $type, $customcolor, $defaultcolour, $alpha = null) {
        $tag = '[[setting:alternativetheme'.$type.']]';
        if (!($customcolor)) {
            $replacement = $defaultcolour;
        } else {
            $replacement = $customcolor;
        }
        if (!is_null($alpha)) {
            $replacement = self::hex2rgba($replacement, $alpha);
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function get_current_category() {
        $us = self::check_corerenderer();

        return $us->get_current_category();
    }

    static public function set_categorycoursetitleimages($css) {
        $tag = '[[setting:categorycoursetitle]]';
        $replacement = '';

        if (self::get_setting('enablecategorycti')) {
            $categories = self::get_categories_list();

            foreach ($categories as $cid => $unused) {
                $image = self::get_setting('categoryct'.$cid.'image');
                $imageurl = false;
                if ($image) {
                    $imageurl = self::setting_file_url('categoryct'.$cid.'image', 'categoryct'.$cid.'image');
                } else {
                    $imageurlsetting = self::get_setting('categoryctimageurl'.$cid);
                    if ($imageurlsetting) {
                        $imageurl = $imageurlsetting;
                    }
                }
                if ($imageurl) {
                    $replacement .= '.categorycti-'.$cid.' {';
                    $replacement .= 'background-image: url(\''.$imageurl.'\');';
                    $replacement .= 'height: '.self::get_setting('categorycti'.$cid.'height').'px;';
                    $replacement .= '}';
                    $replacement .= '.categorycti-'.$cid.' .coursetitle {';
                    $replacement .= 'color: '.self::get_setting('categorycti'.$cid.'textcolour').';';
                    $replacement .= 'background-color: '.self::get_setting('categorycti'.$cid.'textbackgroundcolour').';';
                    $replacement .= 'opacity: '.self::get_setting('categorycti'.$cid.'textbackgroundopactity').';';
                    $replacement .= '}';
                }
            }
        }

        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    /**
     * Returns the RGB for the given hex.
     *
     * @param string $hex
     * @return array
     */
    static private function hex2rgb($hex) {
        // From: http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/.
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array('r' => $r, 'g' => $g, 'b' => $b);
        return $rgb; // Returns the rgb as an array.
    }

    static public function hexadjust($hex, $percentage) {
        $percentage = round($percentage / 100, 2);
        $rgb = self::hex2rgb($hex);
        $r = round($rgb['r'] - ($rgb['r'] * $percentage));
        $g = round($rgb['g'] - ($rgb['g'] * $percentage));
        $b = round($rgb['b'] - ($rgb['b'] * $percentage));

        return '#'.str_pad(dechex(max(0, min(255, $r))), 2, '0', STR_PAD_LEFT)
            .str_pad(dechex(max(0, min(255, $g))), 2, '0', STR_PAD_LEFT)
            .str_pad(dechex(max(0, min(255, $b))), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the RGBA for the given hex and alpha.
     *
     * @param string $hex
     * @param string $alpha
     * @return string
     */
    static private function hex2rgba($hex, $alpha) {
        $rgba = self::hex2rgb($hex);
        $rgba[] = $alpha;
        return 'rgba('.implode(", ", $rgba).')'; // Returns the rgba values separated by commas.
    }

    static public function set_headerbackground($css, $headerbackground) {
        $tag = '[[setting:headerbackground]]';

        $headerbackgroundstyle = self::get_setting('headerbackgroundstyle');
        $replacement = '#page-header {';
        $replacement .= 'background-image: url(\'';
        if ($headerbackground) {
            $replacement .= $headerbackground;
        } else {
            $replacement .= self::pix_url('bg/header', 'theme');
            $headerbackgroundstyle = 'tiled';
        }
        $replacement .= '\');';

        if ($headerbackground) {
            $replacement .= 'background-size: contain;';
        }

        if ($headerbackgroundstyle == 'tiled') {
            $replacement .= 'background-repeat: repeat;';
        } else {
            $replacement .= 'background-repeat: no-repeat;';
            $replacement .= 'background-position: center;';
        }

        $replacement .= '}';

        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_pagebackground($css, $pagebackground) {
        $tag = '[[setting:pagebackground]]';
        if (!($pagebackground)) {
            $replacement = 'none';
        } else {
            $replacement = 'url(\''.$pagebackground.'\')';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_pagebackgroundstyle($css, $style) {
        $tagattach = '[[setting:backgroundattach]]';
        $tagrepeat = '[[setting:backgroundrepeat]]';
        $tagsize = '[[setting:backgroundsize]]';
        $replacementattach = 'fixed';
        $replacementrepeat = 'no-repeat';
        $replacementsize = 'cover';
        if ($style === 'tiled') {
            $replacementrepeat = 'repeat';
            $replacementsize = 'auto';
        } else if ($style === 'stretch') {
            $replacementattach = 'scroll';
        }

        $css = str_replace($tagattach, $replacementattach, $css);
        $css = str_replace($tagrepeat, $replacementrepeat, $css);
        $css = str_replace($tagsize, $replacementsize, $css);
        return $css;
    }

    static public function set_loginbackground($css, $loginbackground) {
        $tag = '[[setting:loginbackground]]';
        if (!($loginbackground)) {
            $replacement = 'none';
        } else {
            $replacement = 'url(\''.$loginbackground.'\')';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_loginbackgroundstyle($css, $style, $opacity) {
        $tagopacity = '[[setting:loginbackgroundopacity]]';
        $tagsize = '[[setting:loginbackgroundstyle]]';
        $replacementsize = 'cover';
        if ($style === 'stretch') {
            $replacementsize = '100% 100%';
        }

        $css = str_replace($tagopacity, $opacity, $css);
        $css = str_replace($tagsize, $replacementsize, $css);
        return $css;
    }

    static public function set_marketingheight($css, $marketingheight, $marketingimageheight) {
        $tag = '[[setting:marketingheight]]';
        $mhreplacement = $marketingheight;
        if (!($mhreplacement)) {
            $mhreplacement = 100;
        }
        $css = str_replace($tag, $mhreplacement.'px', $css);
        $tag = '[[setting:marketingheightwithbutton]]';
        $mhreplacement += 32;
        $css = str_replace($tag, $mhreplacement.'px', $css);

        $tag = '[[setting:marketingimageheight]]';
        $mihreplacement = $marketingimageheight;
        if (!($mihreplacement)) {
            $mihreplacement = 100;
        }
        $css = str_replace($tag, $mihreplacement.'px', $css);

        $tag = '[[setting:marketingheightwithimage]]';
        $replacement = $mhreplacement + $mihreplacement;
        if (!($replacement)) {
            $replacement = 200;
        }
        $css = str_replace($tag, $replacement.'px', $css);
        $tag = '[[setting:marketingheightwithimagewithbutton]]';
        $replacement += 32;
        $css = str_replace($tag, $replacement.'px', $css);

        return $css;
    }

    static public function set_marketingimage($css, $marketingimage, $setting) {
        $tag = '[[setting:'.$setting.']]';
        if (!($marketingimage)) {
            $replacement = 'none';
        } else {
            $replacement = 'url(\''.$marketingimage.'\')';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_customcss($css, $customcss) {
        $tag = '[[setting:customcss]]';
        $replacement = $customcss;
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_integer($css, $setting, $integer, $default) {
        $tag = '[[setting:'.$setting.']]';
        if (!($integer)) {
            $replacement = $default;
        } else {
            $replacement = $integer;
        }
        $css = str_replace($tag, $replacement, $css);

        return $css;
    }

    static public function set_pagewidth($css, $pagewidth) {
        $tag = '[[setting:pagewidth]]';
        $imagetag = '[[setting:pagewidthimage]]';
        $replacement = $pagewidth;
        if (!($replacement)) {
            $replacement = '1200';
        }
        if ($replacement == "100") {
            $css = str_replace($tag, $replacement.'%', $css);
            $css = str_replace($imagetag, '90'.'%', $css);
        } else {
            $css = str_replace($tag, $replacement.'px', $css);
            $css = str_replace($imagetag, $replacement.'px', $css);
        }
        return $css;
    }

    /**
     * States if the browser is not IE9 or less.
     */
    static public function not_lte_ie9() {
        $properties = self::ie_properties();
        if (!is_array($properties)) {
            return true;
        }
        // We have properties, it is a version of IE, so is it greater than 9?
        return ($properties['version'] > 9.0);
    }

    /**
     * States if the browser is IE9 or less.
     */
    static public function lte_ie9() {
        $properties = self::ie_properties();
        if (!is_array($properties)) {
            return false;
        }
        // We have properties, it is a version of IE, so is it greater than 9?
        return ($properties['version'] <= 9.0);
    }

    /**
     * States if the browser is IE by returning properties, otherwise false.
     */
    static protected function ie_properties() {
        $properties = \core_useragent::check_ie_properties(); // In /lib/classes/useragent.php.
        if (!is_array($properties)) {
            return false;
        } else {
            return $properties;
        }
    }

    static public function compile_properties($themename, $array = true) {
        global $CFG, $DB;

        $props = array();
        $themeprops = $DB->get_records('config_plugins', array('plugin' => 'theme_'.$themename));

        if ($array) {
            $props['moodle_version'] = $CFG->version;
            // Put the theme version next so that it will be at the top of the table.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $props['theme_version'] = $themeprop->value;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }

            foreach ($themeprops as $themeprop) {
                $props[$themeprop->name] = $themeprop->value;
            }
        } else {
            $data = new \stdClass();
            $data->id = 0;
            $data->value = $CFG->version;
            $props['moodle_version'] = $data;
            // Convert 'version' to 'theme_version'.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $data = new \stdClass();
                    $data->id = $themeprop->id;
                    $data->name = 'theme_version';
                    $data->value = $themeprop->value;
                    $props['theme_version'] = $data;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }
            foreach ($themeprops as $themeprop) {
                $data = new \stdClass();
                $data->id = $themeprop->id;
                $data->value = $themeprop->value;
                $props[$themeprop->name] = $data;
            }
        }

        return $props;
    }

    static public function put_properties($themename, $props) {
        global $DB;

        // Get the current properties as a reference and for theme version information.
        $currentprops = self::compile_properties($themename, false);

        // Build the report.
        $report = get_string('putpropertyreport', 'theme_essential').PHP_EOL;
        $report .= get_string('putpropertyproperties', 'theme_essential').' \'Moodle\' '.
            get_string('putpropertyversion', 'theme_essential').' '.$props['moodle_version'].'.'.PHP_EOL;
        unset($props['moodle_version']);
        $report .= get_string('putpropertyour', 'theme_essential').' \'Moodle\' '.
            get_string('putpropertyversion', 'theme_essential').' '.$currentprops['moodle_version']->value.'.'.PHP_EOL;
        unset($currentprops['moodle_version']);
        $report .= get_string('putpropertyproperties', 'theme_essential').' \''.ucfirst($themename).'\' '.
            get_string('putpropertyversion', 'theme_essential').' '.$props['theme_version'].'.'.PHP_EOL;
        unset($props['theme_version']);
        $report .= get_string('putpropertyour', 'theme_essential').' \''.ucfirst($themename).'\' '.
            get_string('putpropertyversion', 'theme_essential').' '.$currentprops['theme_version']->value.'.'.PHP_EOL.PHP_EOL;
        unset($currentprops['theme_version']);

        // Pre-process files - using 'theme_essential_pluginfile' in lib.php as a reference.
        // TODO: refactor into one method for both this and that.
        $filestoreport = '';
        $preprocessfilesettings = array('logo', 'headerbackground', 'pagebackground', 'favicon', 'iphoneicon',
            'iphoneretinaicon', 'ipadicon', 'ipadretinaicon', 'loginbackground');
        $fonttypes = array('eot', 'otf', 'svg', 'ttf', 'woff', 'woff2');
        foreach ($fonttypes as $fonttype) {
            $preprocessfilesettings[] = 'fontfile'.$fonttype.'heading';
            $preprocessfilesettings[] = 'fontfile'.$fonttype.'body';
        }
        // Only 3 marketing spots and no setting for the number.
        $preprocessfilesettings = array_merge($preprocessfilesettings, array('marketing1image', 'marketing2image', 'marketing3image'));

        // Slide show.
        for ($propslide = 1; $propslide <= $props['numberofslides']; $propslide++) {
            $preprocessfilesettings[] = 'slide'.$propslide.'image';
        }

        // Process the file properties.
        foreach ($preprocessfilesettings as $preprocessfilesetting) {
            self::put_prop_file_preprocess($preprocessfilesetting, $props, $filestoreport);
            unset($currentprops[$preprocessfilesetting]);
        }

        // Course title images are complex and related to the category id of the installation, so ignore!
        if ((!empty($props['enablecategorycti'])) || (!empty($props['enablecategoryctics']))) {
            $report .= get_string('putpropertiesignorecti', 'theme_essential').PHP_EOL.PHP_EOL;
        }
        $ctikeys = array(
            'enablecategorycti',
            'enablecategoryctics',
            'ctioverrideheight',
            'ctioverridetextcolour',
            'ctioverridetextbackgroundcolour',
            'ctioverridetextbackgroundopacity');
        foreach ($ctikeys as $ctikey) {
            unset($props[$ctikey]);
            unset($currentprops[$ctikey]);
        }
        $propskeys = array_keys($props);
        foreach ($propskeys as $propkey) {
            if (preg_match('#^categoryct#', $propkey) === 1) {
                unset($props[$propkey]);
            }
        }
        $currentpropkeys = array_keys($currentprops);
        foreach ($currentpropkeys as $currentpropkey) {
            if (preg_match('#^categoryct#', $currentpropkey) === 1) {
                unset($currentprops[$currentpropkey]);
            }
        }

        if ($filestoreport) {
            $report .= get_string('putpropertiesreportfiles', 'theme_essential').PHP_EOL.$filestoreport.PHP_EOL;
        }

        // Need to ignore and report on any unknown settings.
        $report .= get_string('putpropertiessettingsreport', 'theme_essential').PHP_EOL;
        $changed = '';
        $unchanged = '';
        $added = '';
        $ignored = '';
        $settinglog = '';
        foreach ($props as $propkey => $propvalue) {
            $settinglog = '\''.$propkey.'\' '.get_string('putpropertiesvalue', 'theme_essential').' \''.$propvalue.'\'';
            if (array_key_exists($propkey, $currentprops)) {
                if ($propvalue != $currentprops[$propkey]->value) {
                    $settinglog .= ' '.get_string('putpropertiesfrom', 'theme_essential').' \''.$currentprops[$propkey]->value.'\'';
                    $changed .= $settinglog.'.'.PHP_EOL;
                    $DB->update_record('config_plugins', array('id' => $currentprops[$propkey]->id, 'value' => $propvalue), true);
                } else {
                    $unchanged .= $settinglog.'.'.PHP_EOL;
                }
            } else if (preg_match('#^slide#', $propkey) === 1) {
                $DB->insert_record('config_plugins', array(
                    'plugin' => 'theme_'.$themename, 'name' => $propkey, 'value' => $propvalue), true);
                $added .= $settinglog.'.'.PHP_EOL;
            } else {
                $ignored .= $settinglog.'.'.PHP_EOL;
            }
        }

        if (!empty($changed)) {
            $report .= get_string('putpropertieschanged', 'theme_essential').PHP_EOL.$changed.PHP_EOL;
        }
        if (!empty($added)) {
            $report .= get_string('putpropertiesadded', 'theme_essential').PHP_EOL.$added.PHP_EOL;
        }
        if (!empty($unchanged)) {
            $report .= get_string('putpropertiesunchanged', 'theme_essential').PHP_EOL.$unchanged.PHP_EOL;
        }
        if (!empty($ignored)) {
            $report .= get_string('putpropertiesignored', 'theme_essential').PHP_EOL.$ignored.PHP_EOL;
        }

        return $report;
    }

    static private function put_prop_file_preprocess($key, &$props, &$filestoreport) {
        if (!empty($props[$key])) {
            $filestoreport .= '\''.$key.'\' '.get_string('putpropertiesvalue', 'theme_essential').' \''.
                \core_text::substr($props[$key], 1).'\'.'.PHP_EOL;
        }
        unset($props[$key]);
    }
}
