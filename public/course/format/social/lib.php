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
 * This file contains main class for the course format Social
 *
 * @since     Moodle 2.0
 * @package   format_social
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

/**
 * Main class for the Social course format
 *
 * @package    format_social
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_social extends core_courseformat\base {

    /**
     * The URL to use for the specified course
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if null the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) ignored by this format
     *     'sr' (int) ignored by this format
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        return new moodle_url('/course/view.php', ['id' => $this->courseid]);
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        // Social course format does not extend navigation, it uses social_activities block instead
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('social_activities')
        );
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * social format uses the following options:
     * - numdiscussions
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseformatoptions = array(
                'numdiscussions' => array(
                    'default' => 10,
                    'type' => PARAM_INT,
                )
            );
        }

        if ($foreditform && !isset($courseformatoptions['numdiscussions']['label'])) {
            $courseformatoptionsedit = array(
                'numdiscussions' => array(
                    'label' => new lang_string('numberdiscussions', 'format_social'),
                    'help' => 'numberdiscussions',
                    'help_component' => 'format_social',
                    'element_type' => 'text',
                )
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
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
     * Return the plugin configs for external functions.
     *
     * @return array the list of configuration settings
     * @since Moodle 3.5
     */
    public function get_config_for_external() {
        // Return everything (nothing to hide).
        return $this->get_format_options();
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

    #[\Override]
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ($section->is_delegated()) {
            return $section->name;
        }
        // Social format only uses one section inside the social activities block.
        return get_string('socialactivities', 'format_social');
    }

    /**
     * Social format uses only section 0.
     *
     * @return int
     */
    #[\Override]
    public function get_sectionnum(): int {
        return 0;
    }

    /**
     * Returns if a specific section is visible to the current user.
     *
     * Formats can override this method to implement any special section logic.
     * Social format does not use any other sections than section 0 and
     * used this method to hide all other sections from the Move section activity.
     *
     * @param section_info $section the section modinfo
     * @return bool;
     */
    #[\Override]
    public function is_section_visible(section_info $section): bool {
        $visible = parent::is_section_visible($section);
        // Social format does only use section 0 as a normal section.
        // Any other included section should be a delegated one (subsections).
        return $visible && ($section->sectionnum == 0 || $section->is_delegated());
    }
}
