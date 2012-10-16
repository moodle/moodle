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
 * Course format class to allow plugins developed for Moodle 2.3 to work in the new API
 *
 * @package    core_course
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Course format class to allow plugins developed for Moodle 2.3 to work in the new API
 *
 * @package    core_course
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_legacy extends format_base {

    /**
     * Returns true if this course format uses sections
     *
     * This function calls function callback_FORMATNAME_uses_sections() if it exists
     *
     * @return bool
     */
    public function uses_sections() {
        global $CFG;
        // Note that lib.php in course format folder is already included by now
        $featurefunction = 'callback_'.$this->format.'_uses_sections';
        if (function_exists($featurefunction)) {
            return $featurefunction();
        }
        return false;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * This function calls function callback_FORMATNAME_get_section_name() if it exists
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        // Use course formatter callback if it exists
        $namingfunction = 'callback_'.$this->format.'_get_section_name';
        if (function_exists($namingfunction) && ($course = $this->get_course())) {
            return $namingfunction($course, $this->get_section($section));
        }

        // else, default behavior:
        return parent::get_section_name($section);
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * This function calls function callback_FORMATNAME_get_section_url() if it exists
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        // Use course formatter callback if it exists
        $featurefunction = 'callback_'.$this->format.'_get_section_url';
        if (function_exists($featurefunction) && ($course = $this->get_course())) {
            if (is_object($section)) {
                $sectionnum = $section->section;
            } else {
                $sectionnum = $section;
            }
            if ($sectionnum) {
                $url = $featurefunction($course, $sectionnum);
                if ($url || !empty($options['navigation'])) {
                    return $url;
                }
            }
        }

        // else, default behavior:
        return parent::get_view_url($section, $options);
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * This function calls function callback_FORMATNAME_ajax_support() if it exists
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     * The property (array)testedbrowsers can be used as a parameter for {@link ajaxenabled()}.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        // set up default values
        $ajaxsupport = parent::supports_ajax();

        // get the information from the course format library
        $featurefunction = 'callback_'.$this->format.'_ajax_support';
        if (function_exists($featurefunction)) {
            $formatsupport = $featurefunction();
            if (isset($formatsupport->capable)) {
                $ajaxsupport->capable = $formatsupport->capable;
            }
            if (is_array($formatsupport->testedbrowsers)) {
                $ajaxsupport->testedbrowsers = $formatsupport->testedbrowsers;
            }
        }
        return $ajaxsupport;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * First this function calls callback_FORMATNAME_display_content() if it exists to check
     * if the navigation should be extended at all
     *
     * Then it calls function callback_FORMATNAME_load_content() if it exist to actually extend
     * navigation
     *
     * By default the parent method is called
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     * @return array Array of sections where each element also contains the element 'sectionnode'
     *     referring to the corresponding section node
     */
    public function extend_course_navigation(&$navigation, navigation_node $node) {
        // check if there are callbacks to extend course navigation
        $displayfunc = 'callback_'.$this->format.'_display_content';
        if (function_exists($displayfunc) && !$displayfunc()) {
            return array();
        }
        $featurefunction = 'callback_'.$this->format.'_load_content';
        if (function_exists($featurefunction) && ($course = $this->get_course())) {
            return $featurefunction($navigation, $course, $node);
        } else {
            return parent::extend_navigation($navigation, $node);
        }
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * This function calls function callback_FORMATNAME_ajax_section_move() if it exists
     *
     * @return array This will be passed in ajax respose
     */
    function ajax_section_move() {
        $featurefunction = 'callback_'.$this->format.'_ajax_section_move';
        if (function_exists($featurefunction) && ($course = $this->get_course())) {
            return $featurefunction($course);
        } else {
            return parent::ajax_section_move();
        }
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * This function checks the existence of the file config.php in the course format folder.
     * If file exists and contains the code
     * $format['defaultblocks'] = 'leftblock1,leftblock2:rightblock1,rightblock2';
     * these blocks are used, otherwise parent function is called
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        global $CFG;
        $formatconfig = $CFG->dirroot.'/course/format/'.$this->format.'/config.php';
        $format = array(); // initialize array in external file
        if (is_readable($formatconfig)) {
            include($formatconfig);
        }
        if (!empty($format['defaultblocks'])) {
            return blocks_parse_default_blocks_list($format['defaultblocks']);
        }
        return parent::get_default_blocks();
    }
}