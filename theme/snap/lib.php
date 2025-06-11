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
 * Standard library functions for snap theme.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use theme_snap\image;
/**
 * Process site cover image.
 *
 * @throws Exception
 * @throws coding_exception
 * @throws dml_exception
 */
function theme_snap_process_site_coverimage() {
    $context = \context_system::instance();
    \theme_snap\local::process_coverimage($context);
    theme_reset_all_caches();
}

/**
 * CSS Processor
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function theme_snap_process_css($css, theme_config $theme) {

    $css = theme_snap_set_category_colors($css, $theme);

    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = theme_snap_set_logo($css, $logo);

    // Set the custom css.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_snap_set_customcss($css, $customcss);

    return $css;
}

/**
 * Adds the custom category colors to the CSS.
 *
 * @param string $css The CSS.
 * @return string The updated CSS
 */
function theme_snap_set_category_colors($css, $theme) {
    global $DB;

    $tag = '/**setting:categorycolors**/';
    $replacement = '';

    // Get custom menu text color from database.
    $dbcustommenutextcolor = get_config("theme_snap", "custommenutext");
    $dbcustommenutextcoloractive = get_config("theme_snap", "customisecustommenu");
    // Get category colors from database.
    $categorycolors = array();
    $dbcategorycolors = get_config("theme_snap", "category_color");
    if (!empty($dbcategorycolors) && $dbcategorycolors != '0') {
        $categorycolors = json_decode($dbcategorycolors, true);
    }

    if (!empty($categorycolors)) {
        $colors = $categorycolors;

        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($colors));
        $categories = $DB->get_records_select(
            'course_categories',
            'id ' . $insql,
            $inparams,
            // Ordered by path ascending so that the colors of child categories overrides,
            // parent categories by coming later in the CSS output.
            'path ASC'
        );

        $themedirectory = realpath(core_component::get_component_directory('theme_snap'));
        $brandscss = file_get_contents($themedirectory . '/scss/_brandcolor.scss');
        foreach ($categories as $category) {
            $compiler = new core_scss();
            // Rewrite wrapper class with current category id.
            $categoryselector = '.category-' . $category->id . ' {';
            $scss = str_replace('.theme-snap {', $categoryselector, $brandscss);
            $compiler->append_raw_scss($scss);
            $compiler->add_variables([
                'brand-primary' => $colors[$category->id],
                'nav-color' => $colors[$category->id],
                'nav-button-color' => $colors[$category->id],
                'nav-login-bg' => $colors[$category->id],
                'nav-login-color' => '#FFFFFF',
                'custom-menu-text-color' => $dbcustommenutextcoloractive ? $dbcustommenutextcolor : '#FFFFFF',
                'gray-light' => '#6a737b',
                'resource-filter' => 'invert(40%) sepia(7%) saturate(6564%) hue-rotate(168deg) brightness(99%) contrast(75%)',
            ]);

            try {
                $compiled = $compiler->to_css();
            } catch (Exception $e) {
                $compiled = '';
                debugging('Error while compiling SCSS: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
            $replacement = $replacement . $compiled;
        }
    }

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
function theme_snap_set_logo($css, $logo) {
    $tag = '/**setting:logo**/';
    if (is_null($logo)) {
        $replacement = '';
    } else {
        $replacement = "#snap-home.logo, .snap-logo-sitename {background-image: url($logo);}";
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_snap_set_customcss($css, $customcss) {
    $tag = '/**setting:customcss**/';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Based on theme function setting_file_serve.
 * Always sends item 0
 *
 * @param $context
 * @param $filearea
 * @param $args
 * @param $forcedownload
 * @param $options
 * @return bool
 */
function theme_snap_send_file($context, $filearea, $args, $forcedownload, $options) {
    $revision = array_shift($args);
    if ($revision < 0) {
        $lifetime = 0;
    } else {
        $lifetime = DAYSECS * 60;
    }

    $filename = end($args);
    $contextid = $context->id;
    $fullpath = "/$contextid/theme_snap/$filearea/0/$filename";
    $fs = get_file_storage();
    $file = $fs->get_file_by_hash(sha1($fullpath));

    if ($file) {
        send_stored_file($file, $lifetime, 0, $forcedownload, $options);
        return true;
    } else {
        send_file_not_found();
    }
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
function theme_snap_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    $coverimagecontexts = [CONTEXT_SYSTEM, CONTEXT_COURSE, CONTEXT_COURSECAT];

    // System level file areas.
    $sysfileareas = [
        'logo',
        'favicon',
        'fs_one_image',
        'fs_two_image',
        'fs_three_image',
        'fs_four_image',
        'fs_five_image',
        'fs_six_image',
        'slide_one_image',
        'slide_two_image',
        'slide_three_image',
        'loginbgimg'
    ];

    if ($context->contextlevel == CONTEXT_SYSTEM && in_array($filearea, $sysfileareas)) {
        $theme = theme_config::load('snap');
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else if (in_array($context->contextlevel, $coverimagecontexts)
            && $filearea == 'coverimage' || $filearea == 'coursecard'|| $filearea == 'croppedimage') {
        theme_snap_send_file($context, $filearea, $args, $forcedownload, $options);
    } else if ($filearea === 'vendorjs') {
        $pluginpath = __DIR__.'/';
        // Typically CDN fall backs would go in vendorjs.
        $path = $pluginpath.'vendorjs/'.implode('/', $args);
        send_file($path, basename($path));
        return true;
    } else if ($filearea === 'hvp' || $filearea === 'hvpcustomcss') {
        // Call to serve H5P Custom CSS.
        $theme = theme_config::load('snap');
        $hvpcustomcss = $theme->settings->hvpcustomcss;
        theme_snap_serve_hvp_css($args[1], $hvpcustomcss);
    } else {
        send_file_not_found();
    }
}

function theme_snap_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $PAGE;

    if ($PAGE->theme->name === 'snap') {
        if ($iscurrentuser) {
            $str = get_strings(['preferences']);
            if (isset($tree->nodes['editprofile'])) {
                $after = 'editprofile';
            } else {
                $after = null;
            }
            $url = new moodle_url('/user/preferences.php');
            $prefnode = new core_user\output\myprofile\node('contact', 'userpreferences', $str->preferences, $after, $url);

            $tree->add_node($prefnode);
        }
    }
}

function theme_snap_get_main_scss_content($theme) {
    global $CFG;

    // Note, the following code is not fully used yet, only the hardcoded
    // pre and post scss files will be loaded, not any presets defined by
    // settings.

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_snap', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_snap and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        $scss = '@import "boost";';
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/snap/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/snap/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_snap_get_pre_scss($theme) {
    global $CFG;

    $scss = '';

    $settings['brand-primary'] = !empty($theme->settings->themecolor) ? $theme->settings->themecolor : '#3bcedb';
    $userfontsans  = $theme->settings->headingfont;
    if (empty($userfontsans) || in_array($userfontsans, ['Roboto', '"Roboto"'])) {
        $userfontsans = '';
    } else {
        $userfontsans .= ",";
    }
    $fallbacksans = 'Roboto, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    $settings['font-family-feature'] = $userfontsans . $fallbacksans;

    if (!empty($theme->settings->customisenavbar)) {
        $settings['nav-bg'] = !empty($theme->settings->navbarbg) ? $theme->settings->navbarbg : '#ffffff';
        $settings['nav-color'] = !empty($theme->settings->navbarlink) ? $theme->settings->navbarlink : $settings['brand-primary'];
    }
    if (!empty($theme->settings->customisenavbutton)) {
        $settings['nav-button-bg'] = !empty($theme->settings->navbarbuttoncolor) ? $theme->settings->navbarbuttoncolor : "#ffffff";

        if (!empty($theme->settings->navbarbuttonlink)) {
            $settings['nav-button-color'] = $theme->settings->navbarbuttonlink;
        } else {
            $settings['nav-button-color'] = $settings['brand-primary'];
        }
    }
    if (!empty($theme->settings->customisecustommenu)) {
        if (!empty($theme->settings->custommenutext)) {
            $settings['custom-menu-text-color'] = $theme->settings->custommenutext;
        } else {
            $settings['custom-menu-text-color'] = $settings['brand-primary'];
        }
    }

    $settings['feature-spot-background-color'] = !empty($theme->settings->feature_spot_background_color) ?
        $theme->settings->feature_spot_background_color : '#ffffff';

    $settings['feature-spot-title-color'] = !empty($theme->settings->feature_spot_title_color) ?
        $theme->settings->feature_spot_title_color : '#ff7f41';

    $settings['feature-spot-description-color'] = !empty($theme->settings->feature_spot_description_color) ?
        $theme->settings->feature_spot_description_color : '#565656';

    $settings['snap-footer-bg-color'] = !empty($theme->settings->footerbg) ?
        $theme->settings->footerbg : '#474747';

    $settings['snap-footer-txt-color'] = !empty($theme->settings->footertxt) ?
        $theme->settings->footertxt : '#ffffff';

    foreach ($settings as $key => $value) {
        $scss .= '$' . $key . ': ' . $value . ";\n";
    }

    return $scss;
}

/**
 * Fragment API function to render course sections.
 * @param $args
 * @return string
 */
function theme_snap_output_fragment_section($args) {
    global $PAGE, $CFG;
    if (!empty($args['courseid']) && $args['section'] != '') {
        $course = get_course($args['courseid']);
        $PAGE->set_context(\context_course::instance($course->id));
        $format = course_get_format($args['courseid']);
        $formatname = $format->get_format();
        if ($formatname == 'weeks' || $formatname == 'topics' || $formatname == 'tiles') {
            $course = $format->get_course();
            $formatrenderer = $format->get_renderer($PAGE);
            $modinfo = get_fast_modinfo($course);
            $section = $modinfo->get_section_info($args['section']);

            // We need to double check if the page has an instance of SharingCart.
            // Current $PAGE object can't be modified.
            $page = new moodle_page();
            $page->set_course($course);
            $page->set_pagelayout('course');
            $page->set_pagetype('course-view-' . $formatname);
            $page->initialise_theme_and_output();
            $page->blocks->load_blocks();
            $page->blocks->create_all_block_instances();
            if ($page->blocks->is_block_present('sharing_cart') && !empty($section) &&
                file_exists($CFG->dirroot . '/blocks/sharing_cart/amd/src/script.js')) {
                $sectionsjs = new stdClass();
                $sectionsjs->id = $section->id;
                $sectionsjs->name = $section->name;
                $sectionsjs->num = $args['section'];
                $PAGE->requires->js_call_amd(
                    'block_sharing_cart/script',
                    'init',
                    [['add_method' => get_config('block_sharing_cart', 'add_to_sharing_cart')], [$sectionsjs], true]
                );
                $PAGE->requires->strings_for_js(
                    array('yes', 'no', 'ok', 'cancel', 'error', 'edit', 'move', 'delete', 'movehere'),
                    'moodle'
                );

                $PAGE->requires->strings_for_js(
                    array('copyhere', 'notarget', 'backup', 'restore', 'movedir', 'clipboard',
                        'confirm_backup', 'confirm_backup_section', 'confirm_userdata',
                        'confirm_delete', 'clicktomove', 'folder_string',
                        'activity_string', 'delete_folder', 'modal_checkbox',
                        'modal_confirm_backup', 'modal_confirm_delete', 'backup_heavy_load_warning_message',
                        'snap_dialog_restore'),
                    'block_sharing_cart'
                );
            }
            $maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
            if (has_capability('moodle/course:ignorefilesizelimits', $PAGE->context)) {
                $maxbytes = 0;
            }
            $html = $formatrenderer->course_section($course, $section, $modinfo);
            $PAGE->requires->js('/course/dndupload.js');
            $vars = array(
                array('courseid' => $course->id,
                    'maxbytes' => $maxbytes,
                    'showstatus' => false)
            );
            $PAGE->requires->js_call_amd('theme_snap/dndupload-lazy', 'init', $vars);
            return $html;
        }
    }
    return '';
}

function theme_snap_course_module_background_deletion_recommended() {
    // Check if recyclebin is installed.
    $toolplugins = core_plugin_manager::instance()->get_installed_plugins("tool");
    foreach ($toolplugins as $name => $version) {
        if ($name == 'recyclebin') {
            if (\tool_recyclebin\course_bin::is_enabled()) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Serves the H5P Custom CSS.
 *
 * @param type $filename The filename.
 * @param type $hvcustomcss The custom css if exists.
 */
function theme_snap_serve_hvp_css($filename, $hvpcustomcss=false) {
    global $CFG;
    // For min_enable_zlib_compression.
    require_once($CFG->dirroot.'/lib/configonlylib.php');

    if (!empty($hvpcustomcss)) {
        $hvptext = (string)$hvpcustomcss;
        $md5content = md5($hvptext);

        // One day only - the lifetime may get incremented.
        $days = 1;
        $lifetime = 60 * 60 * 24 * $days;

        header('HTTP/1.1 200 OK');
        header('Etag: "' . $md5content . '"');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
        header('Pragma: ');
        header('Cache-Control: public, max-age=' . $lifetime);
        header('Accept-Ranges: none');
        header('Content-Type: text/css; charset=utf-8');
        if (!min_enable_zlib_compression()) {
            header('Content-Length: ' . strlen($hvptext));
        }

        echo $hvptext;
        die;
    }
}

function theme_snap_resize_bgimage_after_save() {
    if (!empty(get_config('theme_snap', 'loginbgimg'))) {
        $fs = get_file_storage();
        $files = $fs->get_area_files(\context_system::instance()->id, 'theme_snap', 'loginbgimg');
        foreach ($files as $file) {
            image::resize($file, false, 1280, 720);
            $file->delete();
        }
    }
}

/**
 * Print the buttons relating to course requests.
 * Copied from course/lib.php.
 * @param context $context current page context.
 * @deprecated since Moodle 4.0
 * @todo Final deprecation MDL-73976
 */
function snap_print_course_request_buttons($context) {
    global $CFG, $DB, $OUTPUT;
    if (empty($CFG->enablecourserequests)) {
        return;
    }
    if (course_request::can_request($context)) {
        // Print a button to request a new course.
        $params = [];
        if ($context instanceof context_coursecat) {
            $params['category'] = $context->instanceid;
        }
        echo $OUTPUT->single_button(new moodle_url('/course/request.php', $params),
            get_string('requestcourse'), 'get');
    }
    // Print a button to manage pending requests.
    if (has_capability('moodle/site:approvecourse', $context)) {
        $disabled = !$DB->record_exists('course_request', array());
        echo $OUTPUT->single_button(new moodle_url('/course/pending.php'), get_string('coursespending'),
            'get', array('disabled' => $disabled));
    }
}

/**
 * Get the current user preferences that are available
 *
 * @return array[]
 */
function theme_snap_user_preferences(): array {
    return [
        'snap-feeds-open' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'snap-message-drawer-open' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        // BEGIN LSU - preferences.
        'snap_user_grouping_year_preference' => [
            'null' => NULL_NOT_ALLOWED,
            'default' => 'all',
            'type' => PARAM_TEXT,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'snap_user_grouping_progress_preference' => [
            'null' => NULL_NOT_ALLOWED,
            'default' => 'all',
            'type' => PARAM_TEXT,
            'choices' => array(
                'all',
                'completed',
                'notcompleted',
            ),
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ]
        // END LSU - preferences.
    ];
}
