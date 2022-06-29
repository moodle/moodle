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
class format_legacy extends core_courseformat\base {

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

        // if function is not defined
        if (!$this->uses_sections() ||
                !array_key_exists('coursedisplay', $this->course_format_options())) {
            // default behaviour
            return parent::get_view_url($section, $options);
        }

        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                $usercoursedisplay = $course->coursedisplay ?? COURSE_DISPLAY_SINGLEPAGE;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                if (!empty($options['navigation'])) {
                    return null;
                }
                $url->set_anchor('section-'.$sectionno);
            }
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * This function calls function callback_FORMATNAME_ajax_support() if it exists
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
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
        }
        return $ajaxsupport;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * First this function calls callback_FORMATNAME_display_content() if it exists to check
     * if the navigation should be extended at all
     *
     * Then it calls function callback_FORMATNAME_load_content() if it exists to actually extend
     * navigation
     *
     * By default the parent method is called
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // if course format displays section on separate pages and we are on course/view.php page
        // and the section parameter is specified, make sure this section is expanded in
        // navigation
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }

        // check if there are callbacks to extend course navigation
        $displayfunc = 'callback_'.$this->format.'_display_content';
        if (function_exists($displayfunc) && !$displayfunc()) {
            return;
        }
        $featurefunction = 'callback_'.$this->format.'_load_content';
        if (function_exists($featurefunction) && ($course = $this->get_course())) {
            $featurefunction($navigation, $course, $node);
        } else {
            parent::extend_course_navigation($navigation, $node);
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

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * By default course formats have the options that existed in Moodle 2.3:
     * - coursedisplay
     * - numsections
     * - hiddensections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $courseconfig->numsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections ?? 0,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay ?? COURSE_DISPLAY_SINGLEPAGE,
                    'type' => PARAM_INT,
                ),
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseconfig = get_config('moodlecourse');
            $sectionmenu = array();
            for ($i = 0; $i <= $courseconfig->maxsections; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new lang_string('numberweeks'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                )
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Updates format options for a course
     *
     * Legacy course formats may assume that course format options
     * ('coursedisplay', 'numsections' and 'hiddensections') are shared between formats.
     * Therefore we make sure to copy them from the previous format
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB;
        if ($oldcourse !== null) {
            $data = (array)$data;
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        // If previous format does not have the field 'numsections' and this one does,
                        // and $data['numsections'] is not set fill it with the maximum section number from the DB
                        $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                            WHERE course = ?', array($this->courseid));
                        if ($maxsection) {
                            // If there are no sections, or just default 0-section, 'numsections' will be set to default
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }
        return $this->update_format_options($data);
    }
}
