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
 * @package   theme_iomad
 * @copyright 2013 Howard Miller
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
function theme_iomad_process_css($css, $theme) {
    global $CFG;

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_iomad_set_customcss($css, $customcss);

    // deal with webfonts
    $tag = '[[font:theme|astonish.woff]]';
    $replacement = $CFG->wwwroot.'/theme/iomad/fonts/astonish.woff';
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
function theme_iomad_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $filename = $args[1];
    $itemid = $args[0];
    if ($filearea == 'logo') {
        $itemid = 0;
    }

    if (!$file = $fs->get_file($context->id, 'theme_iomad', $filearea, $itemid, '/', $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload);
}


/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_iomad_set_customcss($css, $customcss) {
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
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_iomad_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG, $USER, $DB;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    // get logos
    $theme = $page->theme;
    $logo = $theme->setting_file_url('logo', 'logo');
    if (empty($logo)) {
        $logo = $CFG->wwwroot.'/theme/iomad/pix/iomad_logo.png';
    }
    $clientlogo = '';
    $companycss = '';
    if ($companyid = iomad::is_company_user()) {
        $context = context_system::instance();
        if ($files = $DB->get_records('files', array('contextid' => $context->id, 'component' => 'theme_iomad', 'filearea' => 'companylogo', 'itemid' => $companyid))) {
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
        $companycss .= iomad::get_company_customcss($companyid);
    }

    $return->heading = '<div id="sitelogo">' . 
        '<a href="' . $CFG->wwwroot . '" ><img src="' . $logo . '" /></a></div>';
    $return->heading .= '<div id="siteheading">' . $output->page_heading() . '</div>';
    if ($clientlogo) {
        $return->heading .= '<div id="clientlogo">' . 
            '<a href="' . $CFG->wwwroot . '" ><img src="' . $clientlogo . '" /></a></div>';
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">'.$page->theme->settings->footnote.'</div>';
    }

    $return->companycss = $companycss;

    return $return;
}

/**
 * All theme functions should start with theme_iomad_
 * @deprecated since 2.5.1
 */
function iomad_process_css() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_iomad_
 * @deprecated since 2.5.1
 */
function iomad_set_logo() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_iomad_
 * @deprecated since 2.5.1
 */
function iomad_set_customcss() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/* perficio_process_css  - Processes perficio specific tags in CSS files
 *
 * [[logo]] gets replaced with the full url to the company logo
 * [[company:$property]] gets replaced with the property of the $USER->company object
 *     available properties are: id, shortname, name, logo_filename + the fields in company->cssfields, currently  bgcolor_header and bgcolor_content
 *
 */
function theme_iomad_process_company_css($css, $theme) {
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
