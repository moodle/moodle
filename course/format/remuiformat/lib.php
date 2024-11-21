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
 * Cards Format - A topics based format that uses card layout to display the activities/section/topics.
 *
 * @package    format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/lib.php'); // For format_remuiformat.

define ('REMUI_CARD_FORMAT', 0);
define ('REMUI_LIST_FORMAT', 1);

/**
 * Cards Format - A topics based format that uses card layout to display the activities/section/topics.
 */
class format_remuiformat extends core_courseformat\base {

    /**
     * Settings
     *
     * @var array
     */
    public $settings = [];

    /**
     * Available layouts.
     *
     * @var array
     */
    private $availablelayouts = [];

    /**
     * Show course activity index on course page left sidebar.
     *
     * @return bool
     */
    public function uses_course_index() {
        return true;
    }

    /**
     * Creates a new instance of class
     * Please use course_get_format($courseorid) to get an instance of the format class
     * @param string $format
     * @param int $courseid
     * @return format_remuiformat
     */
    protected function __construct($format, $courseid) {
        global $PAGE;
        if ($courseid === 0) {
            global $COURSE;
            $courseid = $COURSE->id;  // Save lots of global $COURSE as we will never be the site course.
        }
        $this->availablelayouts = array(
            'REMUI_CARD_FORMAT' => array(
                'format' => REMUI_CARD_FORMAT,
                'optionlabel' => 'remuicourseformat_card',
                'supports' => COURSE_DISPLAY_MULTIPAGE,
            ),
            'REMUI_LIST_FORMAT' => array(
                'format' => REMUI_LIST_FORMAT,
                'optionlabel' => 'remuicourseformat_list',
                'supports' => COURSE_DISPLAY_SINGLEPAGE,
            ),

        );
        // Include course format js module.
        $PAGE->requires->js_call_amd('format_remuiformat/format', 'init', array($this->availablelayouts));

        // Pass constants defined for the formats.
        parent::__construct($format, $courseid);
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     * The property (array)testedbrowsers can be used as a parameter for ajaxenabled().
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Returns the format's settings and gets them if they do not exist.
     * @return array The settings as an array.
     */
    public function get_settings() {
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
            $this->settings['remuicourseimage_filemanager'] = $this->get_remuicourseimage_filemanager();
        }
        return $this->settings;
    }

    /**
     * Indicates this format uses sections.
     * @return bool Returns true
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     * Use section name is specified by user. Otherwise use default ("Topic #")
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            return format_string($section->name, true,
                    array('context' => context_course::instance($this->courseid)));
        } else {
            return $this->get_default_section_name($section);
        }
    }

    /**
     * Returns the default section name for the topics course format.
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * If the section number is not 0, the base implementation of format_remuiformat::get_default_section_name which uses
     * the string with the key = 'sectionname' from the course format's lang file + the section number will be used.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_remuiformat');
        } else {
            // Use format_remuiformat::get_default_section_name implementation which
            // will display the section name in "Topic n" format.
            return get_string('sectionname', 'format_remuiformat').' '. $section->section;
        }
    }

    /**
     * Hide general section when empty
     * @param  object   $course  Course object
     * @param  mod_info $modinfo Module info
     * @return bool              True to hide general section
     */
    public function hide_general_section_when_empty($course, $modinfo = false) {
        global $PAGE;
        if ($modinfo == false) {
            $modinfo = get_fast_modinfo($course);
        }

        $settings = $this->get_settings();
        $sectioninfo = $modinfo->get_section_info(0);

        $hidegeneralsectionwhenempty = gettype($settings['hidegeneralsectionwhenempty']) == 'integer' ?
                                        $settings['hidegeneralsectionwhenempty'] :
                                        (get_config('format_remuiformat', 'hidegeneralsectionwhenempty') || false);

        if ($sectioninfo->summary ||
            !empty($modinfo->sections[0]) ||
            $PAGE->user_is_editing() ||
            !$hidegeneralsectionwhenempty) {
            return false;
        }
        return true;
    }

    /**
     * The URL to use for the specified course (with section)
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
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
                $usercoursedisplay = $course->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                $url->set_anchor('section-'.$sectionno);
            }
        }

        if ((!empty($options['navigation']) || array_key_exists('sr', $options)) && $sectionno !== null) {
            global $PAGE;
            if (strpos($PAGE->url->get_path(), 'course/section.php')) {
                // Display section on separate page.
                $sectioninfo = $this->get_section($sectionno);
                return new moodle_url('/course/section.php', ['id' => $sectioninfo->id]);
            }
        }
        return $url;
    }

    /**
     * Definitions of the additional options that this course format uses for the course.
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;

        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $defaultlayout = get_config('format_remuiformat', 'remuicourseformat');
            $courseformatoptions = array(
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'remuicourseformat' => array(
                    'default' => $defaultlayout,
                    'type' => PARAM_INT
                ),
                'hidegeneralsectionwhenempty' => array(
                    'default' => get_config('format_remuiformat', 'hidegeneralsectionwhenempty') || false,
                    'type' => PARAM_INT
                ),
                'coursedisplay' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                ),
                'remuicourseimage_filemanager' => array(
                    'default' => false,
                    'type' => PARAM_INT
                ),
                'sectiontitlesummarymaxlength' => array(
                    'default' => get_config('format_remuiformat', 'defaultsectionsummarymaxlength'),
                    'type' => PARAM_INT
                ),
                'remuidefaultsectionview' => array(
                    'default' => 1,
                    'type' => PARAM_INT
                ),
                'remuienablecardbackgroundimg' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                ),
                'edw_format_hd_bgpos' => array(
                    'default' => "center",
                    'type' => PARAM_RAW
                ),
                'edw_format_hd_bgsize' => array(
                    'default' => "cover",
                    'type' => PARAM_RAW
                ),
                'headeroverlayopacity' => array(
                    'default' => "100",
                    'type' => PARAM_RAW
                ),
            );
        }

        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {

            $courseconfig = get_config('moodlecourse');
            $courseformatoptionsedit = array(
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
                'remuicourseformat' => array(
                    'label' => new lang_string('remuicourseformat', 'format_remuiformat'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            REMUI_CARD_FORMAT => new lang_string('remuicourseformat_card', 'format_remuiformat'),
                            REMUI_LIST_FORMAT => new lang_string('remuicourseformat_list', 'format_remuiformat'),
                        )
                    ),
                    'help' => 'remuicourseformat',
                    'help_component' => 'format_remuiformat',
                ),
                'hidegeneralsectionwhenempty' => array(
                    'label' => new lang_string('hidegeneralsectionwhenempty', 'format_remuiformat'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('show'),
                            1 => new lang_string('hide')
                        )
                    ),
                    'help' => 'hidegeneralsectionwhenempty',
                    'help_component' => 'format_remuiformat'
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi'),
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                ),
                'remuicourseimage_filemanager' => array(
                    'label' => new lang_string('remuicourseimage_filemanager', 'format_remuiformat'),
                    'element_type' => 'filemanager',
                    'element_attributes' => [[], array(
                        'subdirs' => 0,
                        'maxfiles' => 1,
                        'accepted_types' => array('web_image')
                    )],
                    'help' => 'remuicourseimage_filemanager',
                    'help_component' => 'format_remuiformat',
                ),
                'sectiontitlesummarymaxlength' => array(
                    'label' => new lang_string('sectiontitlesummarymaxlength', 'format_remuiformat'),
                    'element_type' => 'text',
                    'help' => 'sectiontitlesummarymaxlength',
                    'help_component' => 'format_remuiformat'
                ),
                'remuidefaultsectionview' => array(
                    'label' => new lang_string('remuidefaultsectionview', 'format_remuiformat'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('expanded', 'format_remuiformat'),
                            0 => new lang_string('collapsed', 'format_remuiformat')
                        )
                    ),
                    'help' => 'remuidefaultsectionview',
                    'help_component' => 'format_remuiformat'
                ),
                'remuienablecardbackgroundimg' => array(
                    'label' => new lang_string('remuienablecardbackgroundimg', 'format_remuiformat'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('disablecardbackgroundimg', 'format_remuiformat'),
                            1 => new lang_string('enablecardbackgroundimg', 'format_remuiformat')
                        )
                    ),
                    'help' => 'remuienablecardbackgroundimg',
                    'help_component' => 'format_remuiformat'
                ),
                'edw_format_hd_bgpos' => array(
                    'label' => new lang_string('edw_format_hd_bgpos', 'format_remuiformat'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            "bottom" => new lang_string( 'bottom', 'format_remuiformat' ),
                            "center" => new lang_string( 'center', 'format_remuiformat' ),
                            "top" => new lang_string( 'top', 'format_remuiformat' ),
                            "left" => new lang_string( 'left', 'format_remuiformat' ),
                            "right" => new lang_string( 'right', 'format_remuiformat' ),
                        )
                    ),
                    'help' => 'edw_format_hd_bgpos',
                    'help_component' => 'format_remuiformat'
                ),
                'edw_format_hd_bgsize' => array(
                    'label' => new lang_string('edw_format_hd_bgsize', 'format_remuiformat'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            "contain" => new lang_string( 'contain', 'format_remuiformat' ),
                            "auto" => new lang_string( 'auto', 'format_remuiformat' ),
                            "cover" => new lang_string( 'cover', 'format_remuiformat' ),
                        )
                    ),
                    'help' => 'edw_format_hd_bgsize',
                    'help_component' => 'format_remuiformat'
                ),
                'headeroverlayopacity' => array(
                    'label' => new lang_string('headeroverlayopacity', 'format_remuiformat'),
                    'element_type' => 'text',
                    'help' => 'headeroverlayopacity',
                    'help_component' => 'format_remuiformat'
                ),
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }

        return $courseformatoptions;
    }

    /**
     * DB value setter for remuicourseimage_filemanager option
     * @param boolean $itemid Image itemid
     */
    public function set_remuicourseimage_filemanager($itemid = false) {
        global $DB;
        $courseimage = $DB->get_record('course_format_options', array(
            'courseid' => $this->courseid,
            'format' => 'remuiformat',
            'sectionid' => 0,
            'name' => 'remuicourseimage_filemanager'
        ));
        if ($courseimage == false) {
            $courseimage = (object) array(
                'courseid' => $this->courseid,
                'format' => 'remuiformat',
                'sectionid' => 0,
                'name' => 'remuicourseimage_filemanager'
            );
            $courseimage->id = $DB->insert_record('course_format_options', $courseimage);
        }
        $courseimage->value = $itemid;
        $DB->update_record('course_format_options', $courseimage);
        return true;
    }

    /**
     * DB value setter for remuicourseimage_filemanager option
     * @return int Item id
     */
    public function get_remuicourseimage_filemanager() {
        global $DB;
        $itemid = $DB->get_field('course_format_options', 'value', array(
            'courseid' => $this->courseid,
            'format' => 'remuiformat',
            'sectionid' => 0,
            'name' => 'remuicourseimage_filemanager'
        ));
        if (!$itemid) {
            $itemid = file_get_unused_draft_itemid();
        }
        return $itemid;
    }

    /**
     * Adds format options elements to the course/section edit form.
     * This function is called from course_edit_form::definition_after_data().
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $COURSE, $USER, $OUTPUT;

        $elements = parent::create_edit_form_elements($mform, $forsection);
        if (!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID)) {
            // Add "numsections" element to the create course form - it will force new course to be prepopulated
            // with empty sections.
            // The "Number of sections" option is no longer available when editing course, instead teachers should
            // delete and add sections when needed.
            $courseconfig = get_config('moodlecourse');
            $max = (int)$courseconfig->maxsections;
            $element = $mform->addElement('select', 'numsections', get_string('numberweeks'), range(0, $max ?: 52));
            $mform->setType('numsections', PARAM_INT);
            if (is_null($mform->getElementValue('numsections'))) {
                $mform->setDefault('numsections', $courseconfig->numsections);
            }
            array_unshift($elements, $element);
        }

        $fs = get_file_storage();
        $coursecontext = context_course::instance($this->courseid);
        $usercontext = context_user::instance($USER->id);

        $data = new stdClass;
        $fileitemid = $this->get_remuicourseimage_filemanager();
        $fs->delete_area_files($usercontext->id, 'user', 'draft', $fileitemid);
        $data = file_prepare_standard_filemanager(
            $data,
            'remuicourseimage',
            array('accepted_types' => 'images', 'maxfiles' => 1),
            $coursecontext,
            'format_remuiformat',
            'remuicourseimage_filearea',
            $fileitemid
        );
        $mform->setDefault('remuicourseimage_filemanager', $data->remuicourseimage_filemanager);
        foreach ($elements as $key => $element) {
            if ($element->getName() == 'remuicourseimage_filemanager') {
                $element->setMaxfiles(1);
            }
        }

        return $elements;
    }

    /**
     * Whether this format allows to delete sections
     *
     * Do not call this function directly, instead use course_can_delete_section()
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * Prepares the templateable object to display section name
     *
     * @param \section_info|\stdClass $section
     * @param bool $linkifneeded
     * @param bool $editable
     * @param null|lang_string|string $edithint
     * @param null|lang_string|string $editlabel
     * @return \core\output\inplace_editable
     */
    public function inplace_editable_render_section_name(
        $section,
        $linkifneeded = true,
        $editable = null,
        $edithint = null,
        $editlabel = null
    ) {
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname', 'format_remuiformat');
        }
        if (empty($editlabel)) {
            $title = get_section_name($section->course, $section);
            $editlabel = new lang_string('newsectionname', 'format_remuiformat', $title);
        }
        return parent::inplace_editable_render_section_name($section, $linkifneeded, $editable, $edithint, $editlabel);
    }
    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // If section is specified in course/view.php, make sure it is expanded in navigation.
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }

        // Check if there are callbacks to extend course navigation.
        parent::extend_course_navigation($navigation, $node);

        // We want to remove the general section if it is empty.
        $modinfo = get_fast_modinfo($this->get_course());
        $sections = $modinfo->get_sections();
        if (!isset($sections[0])) {
            // The general section is empty to find the navigation node for it we need to get its ID.
            $section = $modinfo->get_section_info(0);
            $generalsection = $node->get($section->id, navigation_node::TYPE_SECTION);
            if ($generalsection) {
                // We found the node - now remove it.
                $generalsection->remove();
            }
        }
    }
    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    public function supports_components() {
        global $CFG;
        if($CFG->branch >= "405") {
            return true;
        }
        return false;
    }

    /**
     * Set the current section number to display.
     * Some formats has the hability to swith from one section to multiple sections per page.
     *
     * @param int|null $sectionnum null for all sections or a sectionid.
     */
    // public function set_section_number(int $sectionnum): void {
    //     global $CFG;
    //     if ((int)$CFG->branch >= 404) {
    //         parent::set_sectionnum($sectionnum);
    //     } else {
    //         parent::set_section_number($sectionnum);
    //     }
    // }

    /**
     * Set if the current format instance will show multiple sections or an individual one.
     *
     * Some formats has the hability to swith from one section to multiple sections per page,
     * output components will use this method to know if the current display is a single or
     * multiple sections.
     *
     * @return int zero for all sections or the sectin number
     */
    // public function get_section_number(): int {
    //     global $CFG;
    //     if ((int)$CFG->branch >= 404) {
    //         return (int)parent::get_sectionnum();
    //     } else {
    //         return parent::get_section_number();
    //     }
    // }
    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array()
        );
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
     * Callback used in WS core_course_edit_section when teacher performs an AJAX action on a section (show/hide)
     *
     * Access to the course is already validated in the WS but the callback has to make sure
     * that particular action is allowed by checking capabilities
     *
     * Course formats should register
     *
     * @param stdClass|section_info $section
     * @param string $action
     * @param int $sr
     * @return null|array|stdClass any data for the Javascript post-processor (must be json-encodeable)
     */
    public function section_action($section, $action, $sr) {
        global $PAGE, $CFG;

        if ($action == 'deleteSection') {
            return null;
        }
        if ($section->section && ($action === 'setmarker' || $action === 'removemarker')) {
            // Format 'topics' allows to set and remove markers in addition to common section actions.
            require_capability('moodle/course:setcurrentsection', context_course::instance($this->courseid));
            course_set_marker($this->courseid, ($action === 'setmarker') ? $section->section : 0);
            return null;
        }

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_topics');
        $format = course_get_format($this->courseid);
        // if($CFG->backup_release > '4.3'){
        if($CFG->branch > '403'){
            $rv['section_availability'] = new \core_courseformat\output\local\content\section\availability($format, $this->get_section($section));
        }else{
            $rv['section_availability'] = $renderer->section_availability($this->get_section($section));
        }

        return $rv;
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

    /**
     * Updates format options for a course
     *
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data      return value from moodleform::get_data() or array with data
     * @param int            $sectionid Section id
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $sectionid = null) {
        if (!isset($data->remuicourseimage_filemanager)) {
            $data->remuicourseimage_filemanager = '';
        }
        if (!empty($data)) {

            // Used optional_param() instead of using $_POST and $_GET.
            $contextid = context_course::instance($this->courseid);
            if (!empty($data->remuicourseimage_filemanager)) {
                file_postupdate_standard_filemanager(
                    $data,
                    'remuicourseimage',
                    array ('accepted_types' => 'images', 'maxfiles' => 1),
                    $contextid,
                    'format_remuiformat',
                    'remuicourseimage_filearea',
                    $data->remuicourseimage_filemanager
                );
            }

            $this->set_remuicourseimage_filemanager($data->remuicourseimage_filemanager);
        }
        return $this->update_format_options($data);
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
            }
        }
        return array('sectiontitles' => $titles, 'action' => 'move');
    }

    /**
     * Extra validation of the format options
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param array $errors errors already discovered in edit form validation
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     *         Do not repeat errors from $errors param here
     */
    public function edit_form_validation($data, $files, $errors) {
        if (isset($data)) {
            $rformat = $data['remuicourseformat'];
            if (isset($rformat)) {
                foreach ($this->availablelayouts as $key => $value) {
                    if ($rformat == $value['format']) {
                        if ($rformat == 0 && $data['coursedisplay'] != $value['supports']) {
                            $errors['coursedisplay'] = get_string('coursedisplay_error', 'format_remuiformat');
                        }
                    }
                }
            }
        }
        return $errors;
    }

}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_remuiformat_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            array($itemid, 'remuiformat'), MUST_EXIST);
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}

/**
 * Serves file from remuicourseimage_filearea
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function format_remuiformat_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB;
    if ($context->contextlevel != CONTEXT_COURSE) {
        return false;
    }
    require_login();
    if ($filearea != 'remuicourseimage_filearea') {
        return false;
    }

    $itemid = (int)array_shift($args);
    $fs = get_file_storage();
    $filename = array_pop($args);

    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }
    $file = $fs->get_file($context->id, 'format_remuiformat', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 0, 0, 0, $options);
}

function format_remuiformat_check_plugin_available($component) {

    list($type, $name) = core_component::normalize_component($component);

    $dir = \core_component::get_plugin_directory($type, $name);
    if (!file_exists($dir ?? '')) {
        return false;
    }
    return true;
}

    /**
     * Get Enrolled Teachers Context
     */
function get_enrolled_teachers_context_formate($course, $frontlineteacher = false) {
    global $OUTPUT, $CFG, $USER;

    $courseid = $course->id;

    $usergroups = groups_get_user_groups($courseid, $USER->id);

    $groupids = 0;

    if($course->groupmode == 1){
        $groupids = $usergroups[0];
    }
    $coursecontext = \context_course::instance($courseid);
    $teachers = get_enrolled_users($coursecontext, 'mod/folder:managefiles', $groupids, '*', 'firstname', $limitfrom = 0, $limitnum = 0, $onlyactive = true);
    $roles =   new stdClass();

    $allroles = get_all_roles();
    foreach($allroles as $singlerole){
        if($singlerole->shortname == 'editingteacher'){
            $roles = $singlerole;
            break;
        }
    }
    if(!isset($roles)){
        $roles->id = "";
    }

    $context = array();

    if ($teachers) {
        $namescount = 4;
        $profilecount = 0;
        foreach ($teachers as $key => $teacher) {
            if ($frontlineteacher && $profilecount < $namescount) {
                $instructor = array();
                $instructor['id'] = $teacher->id;
                $instructor['name'] = fullname($teacher, true);
                $instructor['avatars'] = $OUTPUT->user_picture($teacher);
                $instructor['teacherprofileurl'] = $CFG->wwwroot.'/user/profile.php?id='.$teacher->id;
                if ($profilecount != 0) {
                    $instructor['hasanother'] = true;
                }
                $context['instructors'][] = $instructor;
            }
            $profilecount++;
        }
        if ($profilecount > $namescount) {
            $context['teachercount'] = $profilecount - $namescount;
        }
        $context['participantspageurl'] = $CFG->wwwroot.'/user/index.php?id='.$courseid.'&roleid='.$roles->id;
        $context['hasteachers'] = true;
    }
    return $context;
}

    /**
     * Get course image.
     * @param  stdClass   $corecourselistelement Course list element
     * @param  boolean $islist                Is list
     * @return string                         Course image
     */
function formate_get_course_image($corecourselistelement, $islist = false) {
    global $CFG, $OUTPUT;

    if (!$islist) {
        $corecourselistelement = new \core_course_list_element($corecourselistelement);
    }

    // Course image.
    foreach ($corecourselistelement->get_course_overviewfiles() as $file) {
        $isimage = $file->is_valid_image();
        $courseimage = file_encode_url(
            "$CFG->wwwroot/pluginfile.php",
            '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
            $file->get_filearea(). $file->get_filepath(). $file->get_filename(),
            !$isimage
        );
        if ($isimage) {
            break;
        }
    }
    if (!empty($courseimage)) {
        return $courseimage;
    } else {
        return $OUTPUT->get_generated_image_for_id($corecourselistelement->id);
    }
}
function get_extra_header_context(&$export, $course, $percentage, $imgurl) {
    global $DB, $CFG, $OUTPUT, $PAGE;
    $coursedetails = get_course($course->id);
    if (!is_null($percentage)) {
        $percentage = floor($percentage);
        $export->generalsection['percentage'] = $percentage;
    } else {
        $export->generalsection['percentage'] = 0;
    }
    $categorydetails = $DB->get_record('course_categories', array('id' => $coursedetails->category));
    $rnrshortdesign = '';
    if (format_remuiformat_check_plugin_available("block_edwiserratingreview")) {
        $rnr = new \block_edwiserratingreview\ReviewManager();
        $rnrshortdesign = $rnr->get_short_design_enrolmentpage($course->id);
    }
    $coursesettings = course_get_format($course)->get_settings();
    $export->generalsection['teachers'] = get_enrolled_teachers_context_formate($course, true);
    $export->generalsection['coursefullname'] = format_text($coursedetails->fullname);
    $export->generalsection['coursecategoryname'] = format_text($categorydetails->name);
    $export->generalsection['rnrdesign'] = $rnrshortdesign;
    if (gettype($imgurl) != "object") {
        $imgurl = formate_get_course_image($course);
    }
    $export->generalsection['headercourseimage'] = $imgurl;
    $export->generalsection['remuiheaderimagebgposition'] = $coursesettings['edw_format_hd_bgpos'];
    $export->generalsection['remuiheaderimagebgsize'] = $coursesettings['edw_format_hd_bgsize'];
    $export->generalsection['courseheaderdesign'] = true;
    $export->turneditingonswitch = $OUTPUT->page_heading_button();
    if ($CFG->theme == 'remui') {
        $export->generalsection['courseheaderdesign'] = get_config('theme_remui', 'courseheaderdesign') == 0 ? false : true;
        $export->turneditingonswitch = "";
    }
    $headeroverlayopacity = $coursesettings['headeroverlayopacity'];
    if(is_numeric($headeroverlayopacity) && ($headeroverlayopacity <= 100)){
        $headeroverlayopacity = $headeroverlayopacity / 100;
        $export->generalsection['overlayopacity'] = $headeroverlayopacity;
    }else{
        $export->generalsection['overlayopacity'] = 1;
    }
    $export->generalsection['coursecompletionstatus'] =  $course->enablecompletion;
    $export->generalsection['subsectionjs'] = false;
    $export->generalsection['sectionreturn'] = null;
    if($CFG->branch >='405'){
        $export->generalsection['subsectionjs'] = true;
    }
    return $export->generalsection;
}
