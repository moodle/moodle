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

    // Set the background image for the logo
    $logo = $theme->setting_file_url('logo', 'logo');
    if (empty($logo)) {
        $logo = $CFG->wwwroot.'/theme/iomad/pix/iomad_logo.png';
    }
    $css = theme_iomad_set_logo($css, $logo);

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_iomad_set_customcss($css, $customcss);

    $css = theme_iomad_process_company_css($css, $theme);

    // deal with webfonts
    $tag = '[[font:theme|astonish.woff]]';
    $replacement = $CFG->wwwroot.'/theme/iomad/fonts/astonish.woff';
    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Adds the logo to CSS.
 *
 * @param string $css The CSS.
 * @param string $logo The URL of the logo.
 * @return string The parsed CSS
 */
function theme_iomad_set_logo($css, $logo) {
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
function theme_iomad_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $USER, $CFG;

    if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'logo') {
        $theme = theme_config::load('iomad');
        return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
/*
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/theme_iomad/$filearea/$relativepath";
//echo "<pre>$fullpath"; var_dump($args); die;

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload);
*/
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
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    //if (!empty($page->theme->settings->logo)) {
        $return->heading = "<div id='sitelogo'>".html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'logo'))."</div>";
        $return->heading .= "<div id='siteheading'>".$output->page_heading()."</div>";
        $return->heading .= "<div id='clientlogo'>".html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'clientlogo'))."</div>";
    /*} else {
        $return->heading = $output->page_heading();
        $return->heading .= html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'clientlogo'));
    }*/

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">'.$page->theme->settings->footnote.'</div>';
    }

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
        // prepare logo fullpath
        $tag = '[[setting:clientlogo]]';
        $context = get_context_instance(CONTEXT_SYSTEM);
        $logo = file_rewrite_pluginfile_urls('@@PLUGINFILE@@/[[company:logo_filename]]',
                                             'pluginfile.php',
                                             $context->id,
                                             'theme_iomad',
                                             'logo',
                                             $USER->company->id);
        $css = str_replace($tag, $logo, $css);

        // replace company properties
        foreach($USER->company as $key => $value) {
            if (isset($value)) {
                $css = preg_replace("/\[\[company:$key\]\]/", $value, $css);
            }
        }

    }
    return $css;

}
