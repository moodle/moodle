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
 * Base class for course format plugins
 *
 * @package    core_course
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use core_courseformat\base as course_format;
use core_courseformat\output\site_renderer;

/**
 * Returns an instance of format class (extending course_format) for given course
 *
 * @param int|stdClass $courseorid either course id or
 *     an object that has the property 'format' and may contain property 'id'
 * @return course_format
 */
function course_get_format($courseorid) {
    return course_format::instance($courseorid);
}

/**
 * Pseudo course format used for the site main page
 *
 * @package    core_course
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_site extends course_format {

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            // Return the name the user set.
            return format_string($section->name, true, array('context' => context_course::instance($this->courseid)));
        }
        // The section zero is located in a block.
        if ($section->sectionnum == 0) {
            return get_string('block');
        }
        return get_string('site');
    }

    /**
     * For this fake course referring to the whole site, the site homepage is always returned
     * regardless of arguments
     *
     * @param int|stdClass $section
     * @param array $options
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        return new moodle_url('/', array('redirect' => 0));
    }

    /**
     * Returns the list of blocks to be automatically added on the site frontpage when moodle is installed
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return blocks_get_default_site_course_blocks();
    }

    #[\Override]
    public function supports_ajax() {
        // All home page is rendered in the backend, we only need an ajax editor components in edit mode.
        // This will also prevent redirectng to the login page when a guest tries to access the site,
        // and will make the home page loading faster.
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = $this->show_editor();
        return $ajaxsupport;
    }

    #[\Override]
    public function supports_components() {
        return true;
    }

    #[\Override]
    public function uses_sections() {
        return true;
    }

    /**
     * Definitions of the additional options that site uses
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => 1,
                    'type' => PARAM_INT,
                ),
            );
        }
        return $courseformatoptions;
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     *
     * @param stdClass|cm_info $cm course module (may be null if we are displaying a form for adding a module)
     * @param stdClass|section_info $section section where this module is located or will be added to
     * @return bool
     */
    public function allow_stealth_module_visibility($cm, $section) {
        return true;
    }

    /**
     * Returns instance of page renderer used by the site page
     *
     * @param moodle_page $page the current page
     * @return renderer_base
     */
    public function get_renderer(moodle_page $page) {
        return new site_renderer($page, null);
    }

    /**
     * Site format uses only section 1.
     *
     * @return int
     */
    public function get_sectionnum(): int {
        return 1;
    }
}

/**
 * 'Converts' a value from what is stored in the database into what is used by edit forms.
 *
 * @param array $dest The destination array
 * @param array $source The source array
 * @param array $option The definition structure of the option.
 * @param string $optionname The name of the option, as provided in the definition.
 */
function contract_value(array &$dest, array $source, array $option, string $optionname): void {
    if (substr($optionname, -7) == '_editor') { // Suffix '_editor' indicates that the element is an editor.
        $name = substr($optionname, 0, -7);
        if (isset($source[$name])) {
            $dest[$optionname] = [
                'text' => clean_param_if_not_null($source[$name], $option['type'] ?? PARAM_RAW),
                'format' => clean_param_if_not_null($source[$name . 'format'], PARAM_INT),
            ];
        }
    } else {
        if (isset($source[$optionname])) {
            $dest[$optionname] = clean_param_if_not_null($source[$optionname], $option['type'] ?? PARAM_RAW);
        }
    }
}

/**
 * Cleans the given param, unless it is null.
 *
 * @param mixed $param The variable we are cleaning.
 * @param string $type Expected format of param after cleaning.
 * @return mixed Null if $param is null, otherwise the cleaned value.
 * @throws coding_exception
 */
function clean_param_if_not_null($param, string $type = PARAM_RAW) {
    if ($param === null) {
        return null;
    } else {
        return clean_param($param, $type);
    }
}

/**
 * 'Converts' a value from what is used in edit forms into a value(s) to be stored in the database.
 *
 * @param array $dest The destination array
 * @param array $source The source array
 * @param array $option The definition structure of the option.
 * @param string $optionname The name of the option, as provided in the definition.
 */
function expand_value(array &$dest, array $source, array $option, string $optionname): void {
    if (substr($optionname, -7) == '_editor') { // Suffix '_editor' indicates that the element is an editor.
        $name = substr($optionname, 0, -7);
        if (is_string($source[$optionname])) {
            $dest[$name]            = clean_param($source[$optionname], $option['type'] ?? PARAM_RAW);
            $dest[$name . 'format'] = 1;
        } else {
            $dest[$name]            = clean_param($source[$optionname]['text'], $option['type'] ?? PARAM_RAW);
            $dest[$name . 'format'] = clean_param($source[$optionname]['format'], PARAM_INT);
        }
        unset($dest[$optionname]);
    } else {
        $dest[$optionname] = clean_param($source[$optionname], $option['type'] ?? PARAM_RAW);
    }
}

/**
 * Course-module fragment renderer method.
 *
 * The fragment arguments are id and sr (section return).
 *
 * @param array $args The fragment arguments.
 * @return string The rendered cm item.
 *
 * @throws require_login_exception
 */
function core_courseformat_output_fragment_cmitem($args): string {
    global $PAGE;

    [$course, $cm] = get_course_and_cm_from_cmid($args['id']);
    if (!can_access_course($course, null, '', true) || !$cm->uservisible) {
        throw new require_login_exception('Activity is not available');
    }

    $format = course_get_format($course);
    if (isset($args['pagesectionid'])) {
        $format->set_sectionid($args['pagesectionid']);
    } else if (isset($args['sr'])) {
        $format->set_sectionnum($args['sr']);
    }
    $renderer = $format->get_renderer($PAGE);
    $section = $cm->get_section_info();
    return $renderer->course_section_updated_cm_item($format, $section, $cm);
}

/**
 * Section fragment renderer method.
 *
 * The fragment arguments are courseid, section id and sr (section return).
 *
 * @param array $args The fragment arguments.
 * @return string The rendered section.
 *
 * @throws require_login_exception
 */
function core_courseformat_output_fragment_section($args): string {
    global $PAGE;

    $course = get_course($args['courseid']);
    if (!can_access_course($course, null, '', true)) {
        throw new require_login_exception('Course is not available');
    }

    $format = course_get_format($course);
    if (isset($args['pagesectionid'])) {
        $format->set_sectionid($args['pagesectionid']);
    } else if (isset($args['sr'])) {
        $format->set_sectionnum($args['sr']);
    }

    $modinfo = $format->get_modinfo();
    $section = $modinfo->get_section_info_by_id($args['id'], MUST_EXIST);
    if (!$section->uservisible) {
        throw new require_login_exception('Section is not available');
    }

    $renderer = $format->get_renderer($PAGE);
    return $renderer->course_section_updated($format, $section);
}
