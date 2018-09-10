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
 * This file contains main class for the course format Onetopic
 *
 * @since     2.0
 * @package   format_onetopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

/**
 * Main class for the Onetopic course format
 *
 * @since 2.0
 * @package format_onetopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_onetopic extends format_base {

    /** @var int The summary is not a template */
    const TEMPLATETOPIC_NOT = 0;

    /** @var int The summary is a single template */
    const TEMPLATETOPIC_SINGLE = 1;

    /** @var int The summary is a template, list the resources that are not referenced */
    const TEMPLATETOPIC_LIST = 2;

    /**
     * Creates a new instance of class
     *
     * Please use {@link course_get_format($courseorid)} to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     * @return format_base
     */
    protected function __construct($format, $courseid) {
        parent::__construct($format, $courseid);

        // Hack for section number, when not is like a param in the url.
        global $section, $PAGE, $USER, $urlparams, $DB;

        if ($courseid) {
            $numsections = $DB->get_field('course_sections', 'MAX(section)', array('course' => $courseid), MUST_EXIST);
            if (empty($section)) {
                $course = format_base::get_course();
                if (isset($USER->display[$courseid])
                        && ($PAGE->pagetype == 'course-view-onetopic' || $PAGE->pagetype == 'course-view')
                        && isset($urlparams) && is_array($urlparams) && $numsections >= $USER->display[$course->id]) {

                    $section = $USER->display[$courseid];
                    $urlparams['section'] = $USER->display[$courseid];
                    $PAGE->set_url('/course/view.php', $urlparams);

                }
            }
        }
    }

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * Use section name is specified by user. Otherwise use default ("Topic #")
     *
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
     *
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * If the section number is not 0, the base implementation of format_base::get_default_section_name which uses
     * the string with the key = 'sectionname' from the course format's lang file + the section number will be used.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_topics');
        } else {
            // Use format_base::get_default_section_name implementation which
            // will display the section name in "Topic n" format.
            return parent::get_default_section_name($section);
        }
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
    public function get_view_url($section, $options = array()) {
        global $CFG;
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
            $url->param('section', $sectionno);
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        global $COURSE, $USER;

        if (!isset($USER->onetopic_da)) {
            $USER->onetopic_da = array();
        }

        if (empty($COURSE)) {
            $disableajax = false;
        } else {
            $disableajax = isset($USER->onetopic_da[$COURSE->id]) ? $USER->onetopic_da[$COURSE->id] : false;
        }

        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = !$disableajax;
        return $ajaxsupport;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE, $COURSE, $USER;

        // If section is specified in course/view.php, make sure it is expanded in navigation.
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ((!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {

                if ($selectedsection !== null) {
                    $navigation->includesectionnum = $selectedsection;
                } else if (isset($USER->display[$COURSE->id])) {
                    $navigation->includesectionnum = $USER->display[$COURSE->id];
                }
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
     * Definitions of the additional options that this course format uses for course
     *
     * Topics format uses the following options:
     * - coursedisplay
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
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT
                ),
                'hidetabsbar' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay,
                    'type' => PARAM_INT
                ),
                'templatetopic' => array(
                    'default' => self::TEMPLATETOPIC_NOT,
                    'type' => PARAM_INT
                ),
                'templatetopic_icons' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                )
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
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
                'hidetabsbar' => array(
                    'label' => get_string('hidetabsbar', 'format_onetopic'),
                    'help' => 'hidetabsbar',
                    'help_component' => 'format_onetopic',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('no'),
                            1 => new lang_string('yes')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay', 'format_onetopic'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single', 'format_onetopic'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi', 'format_onetopic')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'format_onetopic',
                ),
                'templatetopic' => array(
                    'label' => new lang_string('templatetopic', 'format_onetopic'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            self::TEMPLATETOPIC_NOT => new lang_string('templetetopic_not', 'format_onetopic'),
                            self::TEMPLATETOPIC_SINGLE => new lang_string('templetetopic_single', 'format_onetopic'),
                            self::TEMPLATETOPIC_LIST => new lang_string('templetetopic_list', 'format_onetopic')
                        )
                    ),
                    'help' => 'templatetopic',
                    'help_component' => 'format_onetopic',
                ),
                'templatetopic_icons' => array(
                    'label' => get_string('templatetopic_icons', 'format_onetopic'),
                    'help' => 'templatetopic_icons',
                    'help_component' => 'format_onetopic',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('no'),
                            1 => new lang_string('yes')
                        )
                    ),
                )
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@link course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $COURSE;
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

        return $elements;
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'onetopic', we try to copy
     * special options from the previous format.
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB;
        $data = (array)$data;
        if ($oldcourse !== null) {
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'hidetabsbar') {
                        // If previous format does not have the field 'hidetabsbar' and $data['hidetabsbar'] is not set,
                        // we fill it with the default option.
                        $data['hidetabsbar'] = 0;
                    } else if ($key === 'templatetopic') {
                        $data['templatetopic'] = self::TEMPLATETOPIC_NOT;
                    } else if ($key === 'templatetopic_icons') {
                        $data['templatetopic_icons'] = 0;
                    }
                }
            }
        }
        return $this->update_format_options($data);
    }

    /**
     * Definitions of the additional options that this course format uses for section
     *
     * See {@link format_base::course_format_options()} for return array definition.
     *
     * Additionally section format options may have property 'cache' set to true
     * if this option needs to be cached in {@link get_fast_modinfo()}. The 'cache' property
     * is recommended to be set only for fields used in {@link format_base::get_section_name()},
     * {@link format_base::extend_course_navigation()} and {@link format_base::get_view_url()}
     *
     * For better performance cached options are recommended to have 'cachedefault' property
     * Unlike 'default', 'cachedefault' should be static and not access get_config().
     *
     * Regardless of value of 'cache' all options are accessed in the code as
     * $sectioninfo->OPTIONNAME
     * where $sectioninfo is instance of section_info, returned by
     * get_fast_modinfo($course)->get_section_info($sectionnum)
     * or get_fast_modinfo($course)->get_section_info_all()
     *
     * All format options for particular section are returned by calling:
     * $this->get_format_options($section);
     *
     * @param bool $foreditform
     * @return array
     */
    public function section_format_options($foreditform = false) {
        static $sectionformatoptions = false;

        if ($sectionformatoptions === false) {
            $sectionformatoptions = array(
                'level' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                ),
                'firsttabtext' => array(
                    'default' => 0,
                    'type' => PARAM_TEXT
                ),
                'fontcolor' => array(
                    'default' => '',
                    'type' => PARAM_RAW
                ),
                'bgcolor' => array(
                    'default' => '',
                    'type' => PARAM_RAW
                ),
                'cssstyles' => array(
                    'default' => '',
                    'type' => PARAM_RAW
                )
            );
        }

        if ($foreditform) {
            $sectionformatoptionsedit = array(
                'level' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                    'label' => get_string('level', 'format_onetopic'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => get_string('asprincipal', 'format_onetopic'),
                            1 => get_string('aschild', 'format_onetopic')
                        )
                    ),
                    'help' => 'level',
                    'help_component' => 'format_onetopic',
                ),
                'firsttabtext' => array(
                    'default' => get_string('index', 'format_onetopic'),
                    'type' => PARAM_TEXT,
                    'label' => get_string('firsttabtext', 'format_onetopic'),
                    'help' => 'firsttabtext',
                    'help_component' => 'format_onetopic',
                ),
                'fontcolor' => array(
                    'default' => '',
                    'type' => PARAM_RAW,
                    'label' => get_string('fontcolor', 'format_onetopic'),
                    'help' => 'fontcolor',
                    'help_component' => 'format_onetopic',
                ),
                'bgcolor' => array(
                    'default' => '',
                    'type' => PARAM_RAW,
                    'label' => get_string('bgcolor', 'format_onetopic'),
                    'help' => 'bgcolor',
                    'help_component' => 'format_onetopic',
                ),
                'cssstyles' => array(
                    'default' => '',
                    'type' => PARAM_RAW,
                    'label' => get_string('cssstyles', 'format_onetopic'),
                    'help' => 'cssstyles',
                    'help_component' => 'format_onetopic',
                )
            );

            $sectionformatoptions = $sectionformatoptionsedit;
        }
        return $sectionformatoptions;
    }


    /**
     * Whether this format allows to delete sections
     *
     * Do not call this function directly, instead use {@link course_can_delete_section()}
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
    public function inplace_editable_render_section_name($section, $linkifneeded = true,
                                                         $editable = null, $edithint = null, $editlabel = null) {
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname', 'format_topics');
        }
        if (empty($editlabel)) {
            $title = get_section_name($section->course, $section);
            $editlabel = new lang_string('newsectionname', 'format_topics', $title);
        }
        return parent::inplace_editable_render_section_name($section, $linkifneeded, $editable, $edithint, $editlabel);
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
        global $PAGE;

        if ($section->section && ($action === 'setmarker' || $action === 'removemarker')) {
            // Format 'topics' allows to set and remove markers in addition to common section actions.
            require_capability('moodle/course:setcurrentsection', context_course::instance($this->courseid));
            course_set_marker($this->courseid, ($action === 'setmarker') ? $section->section : 0);
            return null;
        }

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_topics');
        $rv['section_availability'] = $renderer->section_availability($this->get_section($section));
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
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_onetopics_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            array($itemid, 'onetopic'), MUST_EXIST);
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}

/**
 * Class used in order to replace tags into text. It is a part of templates functionality.
 *
 * Called by preg_replace_callback in renderer.php.
 *
 * @since 2.0
 * @package format_onetopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_onetopic_replace_regularexpression {
    /** @var string Text to search */
    public $_string_search;

    /** @var string Text to replace */
    public $_string_replace;

    /** @var string Temporal key */
    public $_tag_string = '{label_tag_replace}';

    /**
     * Replace a tag into a text.
     *
     * @param array $match
     * @return array
     */
    public function replace_tag_in_expresion ($match) {

        $term = $match[0];
        $term = str_replace("[[", '', $term);
        $term = str_replace("]]", '', $term);

        $text = strip_tags($term);

        if (strpos($text, ':') > -1) {

            $pattern = '/([^:])+:/i';
            $text = preg_replace($pattern, '', $text);

            // Change text for alternative text.
            $newreplace = str_replace($this->_string_search, $text, $this->_string_replace);

            // Posible html tags position.
            $pattern = '/([>][^<]*:[^<]*[<])+/i';
            $term = preg_replace($pattern, '><:><', $term);

            $pattern = '/([>][^<]*:[^<]*$)+/i';
            $term = preg_replace($pattern, '><:>', $term);

            $pattern = '/(^[^<]*:[^<]*[<])+/i';
            $term = preg_replace($pattern, '<:><', $term);

            $pattern = '/(^[^<]*:[^<]*$)/i';
            $term = preg_replace($pattern, '<:>', $term);

            $pattern = '/([>][^<^:]*[<])+/i';
            $term = preg_replace($pattern, '><', $term);

            $term = str_replace('<:>', $newreplace, $term);
        } else {
            // Change tag for resource or mod name.
            $newreplace = str_replace($this->_tag_string, $this->_string_search, $this->_string_replace);
            $term = str_replace($this->_string_search, $newreplace, $term);
        }
        return $term;
    }
}
