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
 * This file contains main class for the course format SoftCourse
 *
 * @since     Moodle 2.0
 * @package   format_softcourse
 * @copyright 2019 Pimenko <contact@pimenko.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/lib.php');

use core\output\inplace_editable;

/**
 * Main class for the Soft Course format
 *
 * @package    format_softcourse
 * @copyright  2019 Pimenko <contact@pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_softcourse extends core_courseformat\base {

    /**
     * Returns true if this course format uses sections.
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns true if this course format uses course index
     *
     * This function may be called without specifying the course id
     * i.e. in course_index_drawer()
     *
     * @return bool
     */
    public function uses_course_index() {
        return true;
    }

    /**
     * Returns true if this course format is compatible with content components.
     *
     * Using components means the content elements can watch the frontend course state and
     * react to the changes. Formats with component compatibility can have more interactions
     * without refreshing the page, like having drag and drop from the course index to reorder
     * sections and activities.
     *
     * @return bool if the format is compatible with components.
     */
    public function supports_components() {
        return true;
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
        // Allow the third visibility state inside visible sections or in section 0.
        return !$section->section || $section->visible;
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    /**
     * Determines whether a section can be deleted.
     *
     * Checks if the given section can be deleted.
     *
     * @param stdClass $section Section object to be checked for deletion.
     * @return bool Returns true if the section can be deleted, false otherwise.
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = []) {
        $course = $this->get_course();
        $url = new moodle_url(
            '/course/view.php',
            [ 'id' => $course->id ],
        );

        if (array_key_exists(
                'sr',
                $options,
            ) && !is_null($options['sr'])) {
            $sectionno = $options['sr'];
        } else if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }

        if ($this->uses_sections() && $sectionno !== null) {
            // The url includes the parameter to expand the section by default.
            if (!array_key_exists(
                'expanded',
                $options,
            )) {
                $options['expanded'] = true;
            }
            if ($options['expanded']) {
                // This parameter is being set by default.
                $url->param(
                    'expandsection',
                    $sectionno,
                );
            }
            $url->set_anchor('section-' . $sectionno);
        }

        return $url;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@see course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $COURSE;

        $elements = parent::create_edit_form_elements(
            $mform,
            $forsection,
        );

        if (!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID)) {
            // Add "numsections" element to the create course form - it will force new course to be prepopulated
            // with empty sections.
            // The "Number of sections" option is no longer available when editing course, instead teachers should
            // delete and add sections when needed.
            $courseconfig = get_config('moodlecourse');
            $max = (int) $courseconfig->maxsections;
            $element = $mform->addElement(
                'select',
                'numsections',
                get_string('numberweeks'),
                range(
                    0,
                    $max ?: 52,
                ),
            );
            $mform->setType(
                'numsections',
                PARAM_INT,
            );
            if (is_null($mform->getElementValue('numsections'))) {
                $mform->setDefault(
                    'numsections',
                    $courseconfig->numsections,
                );
            }
            array_unshift(
                $elements,
                $element,
            );
        }

        // Put the old value of format option introduction in the editor.
        if (isset($this->get_format_options()['introduction'])) {
            $element = $mform->getElement('introduction');
            $element->setValue([ 'text' => $this->get_format_options()['introduction'] ]);
            $element->setMaxfiles(EDITOR_UNLIMITED_FILES);
        }

        return $elements;
    }

    /**
     * Returns the information about the ajax support in the given source format.
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Loads all of the course sections into the navigation.
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     * @return void
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // If section is specified in course/view.php, make sure it is expanded in navigation.
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param(
                'section',
                null,
                PARAM_INT,
            );
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') && $PAGE->url->compare(
                    new moodle_url('/course/view.php'),
                    URL_MATCH_BASE,
                )) {
                $navigation->includesectionnum = $selectedsection;
            }
        }

        // Check if there are callbacks to extend course navigation.
        parent::extend_course_navigation(
            $navigation,
            $node,
        );

        // We want to remove the general section if it is empty.
        $modinfo = get_fast_modinfo($this->get_course());
        $sections = $modinfo->get_sections();
        if (!isset($sections[0])) {
            // The general section is empty to find the navigation node for it we need to get its ID.
            $section = $modinfo->get_section_info(0);
            $generalsection = $node->get(
                $section->id,
                navigation_node::TYPE_SECTION,
            );
            if ($generalsection) {
                // We found the node - now remove it.
                $generalsection->remove();
            }
        }
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'topics', we try to copy options
     * 'coursedisplay' and 'hiddensections' from the previous format.
     *
     * @param stdClass|array $data return value from {@see moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@see update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        $data = (array) $data;
        if ($oldcourse !== null) {
            $oldcourse = (array) $oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists(
                    $key,
                    $data,
                )) {
                    if (array_key_exists(
                        $key,
                        $oldcourse,
                    )) {
                        $data[$key] = $oldcourse[$key];
                    }
                }
            }
        }

        // Managing of image in the introduction.
        if (isset($data['introduction']) && $introductiondraftid = file_get_submitted_draft_itemid('introduction')) {
            $context = context_course::instance($this->courseid);
            $options = [ 'subdirs' => false ];

            // Retrieve the image in the draftfilearea and put it into the introduction filearea of the plugin.
            $data['introduction']['text'] = file_save_draft_area_files(
                $introductiondraftid,
                $context->id,
                'format_softcourse',
                'introduction',
                time(),
                null,
                $data['introduction']['text'],
            );
            $data['introduction']['text'] = file_rewrite_pluginfile_urls(
                $data['introduction']['text'],
                'pluginfile.php',
                $context->id,
                'format_softcourse',
                'introduction',
                time(),
            );
        }

        return $this->update_format_options($data);
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Soft Course format uses the following options:
     * - coursedisplay
     * - hideallsections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = [
                'hideallsections' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
                'hidesectionzero' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],

                'introduction' => [
                    'default' => '',
                    'type' => PARAM_RAW,
                ],
                'coursedisplay' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                ],
            ];
        }
        if ($foreditform) {
            $optionsedit = [
                'hideallsections' => [
                    'label' => get_string(
                        'hideallsections',
                        "format_softcourse",
                    ),
                    'help' => 'hideallsections',
                    'help_component' => 'format_softcourse',
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            0 => get_string(
                                'hideallsectionsno',
                                "format_softcourse",
                            ),
                            1 => get_string(
                                'hideallsectionsyes',
                                "format_softcourse",
                            ),
                        ],
                    ],
                ],
                'hidesectionzero' => [
                    'label' => get_string(
                        'hidesectionzero',
                        "format_softcourse",
                    ),
                    'help' => 'hidesectionzero',
                    'help_component' => 'format_softcourse',
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            0 => get_string(
                                'hidesectionzerono',
                                "format_softcourse",
                            ),
                            1 => get_string(
                                'hidesectionzeroyes',
                                "format_softcourse",
                            ),
                        ],
                    ],
                ],
                'introduction' => [
                    'label' => get_string(
                        'introduction',
                        "format_softcourse",
                    ),
                    'help' => 'introduction',
                    'help_component' => 'format_softcourse',
                    'element_type' => 'editor',
                    'maxfiles' => EDITOR_UNLIMITED_FILES,
                ],
                'coursedisplay' => [
                    'label' => get_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            COURSE_DISPLAY_SINGLEPAGE => get_string('coursedisplay_single'),
                        ],
                    ],
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                ],
            ];
            $courseformatoptions = array_merge_recursive(
                $courseformatoptions,
                $optionsedit,
            );
        }
        return $courseformatoptions;
    }

    /**
     * Custom action after section has been moved in AJAX mode.
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        global $PAGE;
        $titles = [];
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title(
                    $section,
                    $course,
                );
            }
        }
        return [
            'sectiontitles' => $titles,
            'action' => 'move',
        ];
    }

    /**
     * Callback used in WS core_course_edit_section when teacher performs an AJAX action on a section (show/hide).
     *
     * Access to the course is already validated in the WS but the callback has to make sure
     * that particular action is allowed by checking capabilities
     *
     * Course formats should register.
     *
     * @param section_info|stdClass $section
     * @param string $action
     * @param int $sr
     * @return null|array any data for the Javascript post-processor (must be json-encodeable)
     */
    public function section_action($section, $action, $sr) {
        global $PAGE;

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action(
            $section,
            $action,
            $sr,
        );
        $renderer = $PAGE->get_renderer('format_softcourse');

        if (!($section instanceof section_info)) {
            $modinfo = $this->get_modinfo();
            $section = $modinfo->get_section_info($section->section);
        }
        $elementclass = $this->get_output_classname('content\\section\\availability');
        $availability = new $elementclass(
            $this,
            $section,
        );

        $rv['section_availability'] = $renderer->render($availability);
        return $rv;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * Use section name is specified by user. Otherwise use default ("Topic #").
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        // If section name is not 'custom' we won't display it.
        if ((string) $section->name !== '') {
            return format_string(
                $section->name,
                true,
                [ 'context' => context_course::instance($this->courseid) ],
            );
        } else {
            return $this->get_default_section_name($section);
        }
    }

    /**
     * Returns the default section name for the softcourse course format.
     *
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * If the section number is not 0, the base implementation of course_format::get_default_section_name which uses
     * the string with the key = 'sectionname' from the course format's lang file + the section number will be used.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string(
                'section0name',
                'format_softcourse',
            );
        } else {
            // Return default section name.
            return parent::get_default_section_name($section);
        }
    }

    /**
     * Prepares values of course or section format options before storing them in DB
     *
     * If an option has invalid value it is not returned
     *
     * @param array $rawdata associative array of the proposed course/section format options
     * @param int|null $sectionid null if it is course format option
     * @return array array of options that have valid values
     */
    protected function validate_format_options(array $rawdata, int $sectionid = null): array {
        if (!$sectionid) {
            $allformatoptions = $this->course_format_options(true);
        } else {
            $allformatoptions = $this->section_format_options(true);
        }
        $data = array_intersect_key(
            $rawdata,
            $allformatoptions,
        );
        foreach ($data as $key => $value) {
            $option = $allformatoptions[$key] + [
                    'type' => PARAM_RAW,
                    'element_type' => null,
                    'element_attributes' => [
                        [
                        ],
                    ],
                ];
            if ($option['element_type'][0] == 'editor') {
                $data[$key] = clean_param(
                    $value['text'],
                    $option['type'],
                );
            } else {
                $data[$key] = clean_param(
                    $value,
                    $option['type'],
                );
            }

            if ($option['element_type'] === 'select' && !array_key_exists(
                    $data[$key],
                    $option['element_attributes'][0],
                )) {
                // Value invalid for select element, skip.
                unset($data[$key]);
            }
        }
        return $data;
    }
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return inplace_editable
 */
function format_softcourse_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            [
                $itemid,
                'softcourse',
            ],
            MUST_EXIST,
        );
        return course_get_format($section->course)->inplace_editable_update_section_name(
            $section,
            $itemtype,
            $newvalue,
        );
    }
    return null;
}

/**
 * Softcourse plugin function
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The cm object.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function format_softcourse_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($filearea == 'sectionimage' || $filearea == 'introduction') {
        $relativepath = implode(
            '/',
            $args,
        );
        $contextid = $context->id;
        $fullpath = "/$contextid/format_softcourse/$filearea/$relativepath";
        $fs = get_file_storage();
        $file = $fs->get_file_by_hash(sha1($fullpath));
        if ($file) {
            send_stored_file(
                $file,
                null,
                0,
                $forcedownload,
                $options,
            );
            return true;
        }
    }
}
