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
 * @package    theme_iomadbootstrap
 * @copyright  2014 Bas Brands, www.basbrands.nl
 * @authors    Bas Brands, David Scotson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/local/iomad/lib/user.php');
require_once($CFG->dirroot.'/local/iomad/lib/iomad.php');

function iomadbootstrap_grid($hassidepre, $hassidepost) {

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
    
    if ('rtl' === get_string('thisdirection', 'langconfig')) {
        if ($hassidepre && $hassidepost) {
            $regions['pre'] = 'col-sm-3  col-sm-push-3 col-lg-2 col-lg-push-2';
            $regions['post'] = 'col-sm-3 col-sm-pull-9 col-lg-2 col-lg-pull-10';
        } else if ($hassidepre && !$hassidepost) {
            $regions = array('content' => 'col-sm-9 col-lg-10');
            $regions['pre'] = 'col-sm-3 col-lg-2';
            $regions['post'] = 'empty';
        } else if (!$hassidepre && $hassidepost) {
            $regions = array('content' => 'col-sm-9 col-sm-push-3 col-lg-10 col-lg-push-2');
            $regions['pre'] = 'empty';
            $regions['post'] = 'col-sm-3 col-sm-pull-9 col-lg-2 col-lg-pull-10';
        }
    }
    return $regions;
}

/**
 * Loads the JavaScript for the zoom function.
 *
 * @param moodle_page $page Pass in $PAGE.
 */
function theme_iomadbootstrap_initialise_zoom(moodle_page $page) {
    user_preference_allow_ajax_update('theme_iomadbootstrap_zoom', PARAM_TEXT);
    $page->requires->yui_module('moodle-theme_bootstrap-zoom', 'M.theme_bootstrap.zoom.init', array());
}

/**
 * Get the user preference for the zoom function.
 */
function theme_iomadbootstrap_get_zoom() {
    return get_user_preferences('theme_iomadbootstrap_zoom', '');
}

/**
 * This function creates the dynamic HTML needed for the
 * layout and then passes it back in an object so it can
 * be echo'd to the page.
 *
 * This keeps the logic out of the layout files.
 */
function theme_iomadbootstrap_html_for_settings($PAGE) {
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

    $html->brandfontlink = theme_iomadbootstrap_brand_font_link($settings);

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

function theme_iomadbootstrap_brand_font_css($settings) {
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

function theme_iomadbootstrap_brand_font_link($settings) {
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
function theme_iomadbootstrap_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $filename = $args[1];
    $itemid = $args[0];
    if ($filearea == 'logo') {
        $itemid = 0;
    }

    if (!$file = $fs->get_file($context->id, 'theme_iomadbootstrap', $filearea, $itemid, '/', $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload);
}
