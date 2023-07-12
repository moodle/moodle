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
 * Simple slider block for Moodle
 *
 * @package   block_slider
 * @copyright 2015-2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

/**
 * Block slider file function.
 *
 * @param $course
 * @param $birecordorcm
 * @param $context
 * @param $filearea
 * @param $args
 * @param $forcedownload
 * @param array $options
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 * @throws require_login_exception
 * @throws required_capability_exception
 */
function block_slider_pluginfile($course, $birecordorcm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_BLOCK) {
        send_file_not_found();
    }

    // If block is in course context, then check if user has capability to access course.
    if ($context->get_course_context(false)) {
        require_course_login($course);
    } else if ($CFG->forcelogin) {
        require_login();
    } else {
        // Get parent context and see if user have proper permission.
        $parentcontext = $context->get_parent_context();
        if ($parentcontext->contextlevel === CONTEXT_COURSECAT) {
            // Check if category is visible and user can view this category.
            $category = $DB->get_record('course_categories', array('id' => $parentcontext->instanceid), '*', MUST_EXIST);
            if (!$category->visible) {
                require_capability('moodle/category:viewhiddencategories', $parentcontext);
            }
        }
        // At this point there is no way to check SYSTEM or USER context, so ignoring it.
    }

    if ($filearea !== 'content' && $filearea !== 'slider_slides') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    if (!$file = $fs->get_file($context->id, 'block_slider', $filearea, $itemid, $filepath, $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    if ($parentcontext = context::instance_by_id($birecordorcm->parentcontextid, IGNORE_MISSING)) {
        if ($parentcontext->contextlevel == CONTEXT_USER) {
            // Force download on all personal pages including /my/.
            // Because we do not have reliable way to find out from where this is used.
            $forcedownload = true;
        }
    } else {
        // Weird, there should be parent context, better force dowload then.
        $forcedownload = true;
    }

    \core\session\manager::write_close();
    send_stored_file($file, 60 * 60, 0, $forcedownload, $options);
}

/**
 * Displays paypal donation link
 *
 * @throws coding_exception
 */
function slider_donation_link() {
    global $OUTPUT;
    echo html_writer::start_div('span12')
            . html_writer::empty_tag('br')
            . html_writer::tag('p', get_string('donation', 'block_slider'))
            . html_writer::tag('a', html_writer::empty_tag('img',
                    array('src' => $OUTPUT->image_url('paypal', 'block_slider'), 'width' => '125')),
                    array('href' => 'https://www.paypal.me/limsko', 'target' => '_blank'))
            . html_writer::end_div();
}

/**
 * Function for deleting slide with their images.
 *
 * @param $slide object
 * @return bool
 * @throws dml_exception
 */
function block_slider_delete_slide($slide) {
    global $DB;
    block_slider_delete_image($slide->sliderid, $slide->id, $slide->slide_image);
    $DB->delete_records('slider_slides', array('id' => $slide->id));
    return true;
}

/**
 * Deletes images in selected slider.
 *
 * @param $sliderid int Slider ID number
 * @param $slideid int Slide ID
 * @param $slideimage string Slide image name
 * @throws dml_exception
 */
function block_slider_delete_image($sliderid, $slideid, $slideimage = null) {
    global $DB;
    $fs = get_file_storage();
    $context = context_block::instance($sliderid);
    if (!$slideimage) {
        $slideimage = $DB->get_field('slider_slides', 'slide_image', array('sliderid' => $sliderid, 'id' => $slideid));
    }
    if ($file = $fs->get_file($context->id, 'block_slider', 'slider_slides', $slideid, '/', $slideimage)) {
        $file->delete();
    }
}

/**
 * Get settings for BXSlider JS.
 *
 * @param $config
 * @param $sliderid
 * @return array
 */
function bxslider_get_settings($config, $sliderid) {
    $bxpause = isset($config->interval) ? $config->interval : 5000;
    $bxeffect = isset($config->bx_effect) ? $config->bx_effect : 'fade';
    $bxspeed = isset($config->bx_speed) ? $config->bx_speed : 500;
    $bxcaptions = isset($config->bx_captions) ? $config->bx_captions : 0;
    $bxresponsive = isset($config->bx_responsive) ? $config->bx_responsive : 1;
    $bxpager = isset($config->bx_pager) ? $config->bx_pager : 1;
    $bxcontrols = isset($config->bx_controls) ? $config->bx_controls : 1;
    $bxauto = isset($config->bx_auto) ? $config->bx_auto : 1;
    $bxstopautoonclick = isset($config->bx_stopAutoOnClick) ? $config->bx_stopAutoOnClick : 0;
    $bxusecss = isset($config->bx_useCSS) ? $config->bx_useCSS : 0;
    return array($sliderid, $bxpause, $bxeffect, $bxspeed, boolval($bxcaptions), boolval($bxresponsive), boolval($bxpager),
            boolval($bxcontrols), boolval($bxauto), boolval($bxstopautoonclick), boolval($bxusecss));
}