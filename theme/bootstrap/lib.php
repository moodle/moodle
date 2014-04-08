<?php
// This file is part of The Bootstrap 3 Moodle theme
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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrap
 * @copyright  2014 Bas Brands, www.basbrands.nl
 * @authors    Bas Brands, David Scotson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/local/iomad/lib/user.php');
require_once($CFG->dirroot.'/local/iomad/lib/iomad.php');

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_bootstrap_process_css($css, $theme) {

    $settings = get_object_vars($theme->settings);

    $css = theme_bootstrap_delete_css($settings, $css);

    $settings['brandcss'] = theme_bootstrap_brand_font_css($settings);

    return theme_bootstrap_replace_settings($settings, $css);
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
function theme_bootstrap_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $filename = $args[1];
    $itemid = $args[0];
    if ($filearea == 'logo') {
        $itemid = 0;
    }

    if (!$file = $fs->get_file($context->id, 'theme_bootstrap', $filearea, $itemid, '/', $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload);
}

function theme_bootstrap_delete_css($settings, $css) {
    if ($settings['deletecss'] == true) {
        $find[] = '/-webkit-border-radius:[^;]*;/';
        $find[] = '/-webkit-box-shadow:[^;]*;/';
        $find[] = '/-moz-border-radius:[^;]*;/';
        $find[] = '/-moz-box-shadow:[^;]*;/';
        return preg_replace($find, '', $css);
    } else {
        return $css;
    }
}

/**
 * For each setting called e.g. "customcss" this looks for the string
 * "[[setting:customcss]]" in the CSS and replaces it with
 * the value held in the $settings array for the key
 * "customcss".
 *
 * @param array $settings containing setting names and values
 * @param string $css The CSS
 * @return string The CSS with replacements made
 */
function theme_bootstrap_replace_settings($settings, $css) {
    foreach ($settings as $name => $value) {
        $find[] = "[[setting:$name]]";
        $replace[] = $value;
    }
    return str_replace($find, $replace, $css);
}

function theme_bootstrap_brand_font_css($settings) {
    $fontname = $settings['brandfont'];
    if ($fontname === '') {
        return '';
    }
    $fontweight = $settings['brandfontweight'];
    return ".navbar-default .navbar-brand,
            .navbar-inverse .navbar-brand {
                font-family: $fontname, serif;
                font-weight: $fontweight;
            }";
}

/**
 * This function creates the dynamic HTML needed for the 
 * layout and then passes it back in an object so it can
 * be echo'd to the page.
 *
 * This keeps the logic out of the layout files.
 */
function theme_bootstrap_html_for_settings($PAGE) {
    global $CFG, $DB, $USER, $SITE;

    $settings = $PAGE->theme->settings;

    $html = new stdClass;

    if ($settings->inversenavbar == true) {
        $html->navbarclass = 'navbar navbar-inverse';
    } else {
        $html->navbarclass = 'navbar navbar-default';
    }

    $fluid = (!empty($PAGE->layout_options['fluid']));
    if ($fluid || $settings->fluidwidth == true) {
        $html->containerclass = 'container-fluid';
    } else {
        $html->containerclass = 'container';
    }

    $html->brandfontlink = theme_bootstrap_brand_font_link($settings);

    // get logos
    $theme = $PAGE->theme;
    $logo = $theme->setting_file_url('logo', 'logo');
    if (empty($logo)) {
        $logo = $CFG->wwwroot.'/theme/iomad/pix/iomad_logo.png';
    }
    $clientlogo = '';
    $companycss = '';
    if ($companyid = iomad::is_company_user()) {
        $context = context_system::instance();
        if ($files = $DB->get_records('files', array('contextid' => $context->id,
                                                     'component' => 'theme_iomad',
                                                     'filearea' => 'companylogo',
                                                     'itemid' => $companyid))) {
            foreach ($files as $file) {
                if ($file->filename != '.') {
                    $clientlogo = $CFG->wwwroot . "/pluginfile.php/{$context->id}/theme_iomad/companylogo/$companyid/{$file->filename}";
                }
            }
        }
        company_user::load_company();
        $companycss = ".header, .navbar { background: [[company:bgcolor_header]]; }
                       .block .content { background: [[company:bgcolor_content]]; }";
        foreach($USER->company as $key => $value) {
            if (isset($value)) {
                $companycss = preg_replace("/\[\[company:$key\]\]/", $value, $companycss);
            }
        }
    }

    $html->heading = '<div id="sitelogo">' . 
        '<a href="' . $CFG->wwwroot . '" ><img src="' . $logo . '" /></a></div>';
    $html->heading .= '<div id="siteheading"><span>' . $SITE->fullname . '</span></div>';
    if ($clientlogo) {
        $html->heading .= '<div id="clientlogo">' . 
            '<a href="' . $CFG->wwwroot . '" ><img src="' . $clientlogo . '" /></a></div>';
    }

    $html->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $html->footnote = '<div class="footnote text-center">'.$PAGE->theme->settings->footnote.'</div>';
    }

    $html->companycss = $companycss;

    return $html;
}

function theme_bootstrap_brand_font_link($settings) {
    global $SITE;
    $fontname = $settings->brandfont;
    if ($fontname === '') {
        return '';
    }
    $fontname = urlencode($fontname);
    $text = urlencode(str_replace(' ', '', $SITE->shortname));
    $fontweight = $settings->brandfontweight;
    $fontitalic = '';
    if ($settings->brandfontitalic == true) {
        $fontitalic = 'italic';
    }
    return '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family='
            .$fontname.':'.$fontweight.$fontitalic.'&amp;text='.$text.'">';
}

function bootstrap_grid($hassidepre, $hassidepost) {
    if ($hassidepre && $hassidepost) {
        $regions = array('content' => 'col-sm-6 col-sm-push-3 col-lg-8 col-lg-push-2');
        $regions['pre'] = 'col-sm-3 col-sm-pull-6 col-lg-2 col-lg-pull-8';
        $regions['post'] = 'col-sm-3 col-lg-2';
    } else if ($hassidepre && !$hassidepost) {
        $regions = array('content' => 'col-sm-9 col-sm-push-3 col-lg-10 col-lg-push-2');
        $regions['pre'] = 'col-sm-3 col-sm-pull-9 col-lg-2 col-lg-pull-10';
        $regions['post'] = 'emtpy';
    } else if (!$hassidepre && $hassidepost) {
        $regions = array('content' => 'col-sm-9 col-lg-10');
        $regions['pre'] = 'empty';
        $regions['post'] = 'col-sm-3 col-lg-2';
    } else if (!$hassidepre && !$hassidepost) {
        $regions = array('content' => 'col-md-12');
        $regions['pre'] = 'empty';
        $regions['post'] = 'empty';
    }
    return $regions;
}

function theme_bootstrap_initialise_reader(moodle_page $page) {
    $page->requires->yui_module('moodle-theme_bootstrap-reader', 'M.theme_bootstrap.initreader', array());
}

/* bootstrap_process_company_css  - Processes perficio specific tags in CSS files
 *
 * [[logo]] gets replaced with the full url to the company logo
 * [[company:$property]] gets replaced with the property of the $USER->company object
 *     available properties are: id, shortname, name, logo_filename + the fields in company->cssfields, currently  bgcolor_header and bgcolor_content
 *
 */
function theme_bootstrap_process_company_css($css, $theme) {
    global $USER;

    company_user::load_company();

    if (isset($USER->company)) {
        // replace company properties
        foreach($USER->company as $key => $value) {
            if (isset($value)) {
                $css = preg_replace("/\[\[company:$key\]\]/", $value, $css);
            }
        }

    }
    return $css;

}

