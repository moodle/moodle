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
 * This file contains main class for the course format Tiles
 *
 * @since     Moodle 2.7
 * @package   format_tiles
 * @copyright 2016 David Watson {@link http://evolutioncode.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('FORMAT_TILES_FILTERBAR_NONE', 0);
define('FORMAT_TILES_FILTERBAR_NUMBERS', 1);
define('FORMAT_TILES_FILTERBAR_OUTCOMES', 2);
define('FORMAT_TILES_FILTERBAR_BOTH', 3);

require_once($CFG->dirroot . '/course/format/lib.php');

/**
 * Main class for the course format Tiles
 *
 * @since     Moodle 2.7
 * @package   format_tiles
 * @copyright 2016 David Watson {@link http://evolutioncode.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_tiles extends format_base {

    /**
     *  We want to treat label and plugins that behave like labels as labels.
     * E.g. we don't render them as subtiles but show their content directly on page.
     * This includes plugins like mod_customlabel and mod_unilabel, as defined here.
     * @var []
     */
    public $labellikecoursemods = ['label', 'customlabel', 'unilabel', 'datalynxcoursepage'];

    /**
     * Creates a new instance of class
     *
     * Please use {@see course_get_format($courseorid)} to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     */
    protected function __construct($format, $courseid) {
        if ($courseid === 0) {
            global $COURSE;
            $courseid = $COURSE->id;  // Save lots of global $COURSE as we will never be the site course.
        }
        parent::__construct($format, $courseid);
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
     * @throws moodle_exception
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            return format_string($section->name, true,
                array('context' => context_course::instance($this->courseid)));
        } else if ($section->section == 0) {
            return get_string('section0name', 'format_tiles');
        } else {
            return get_string('sectionname', 'format_tiles') . ' ' . $section->section;
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
     * @throws coding_exception
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_tiles');
        } else {
            // Use format_base::get_default_section_name implementation which will display the section name in "Topic n" format.
            return parent::get_default_section_name($section);
        }
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     * Required in Moodle 3.2 onwards
     *
     * @return bool
     */
    public function supports_news() {
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
     * @throws moodle_exception
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
                $sectionno = $sr;
            }
            if ($sectionno != 0) {
                $url->param('section', $sectionno);
            } else {
                if (!empty($options['navigation'])) {
                    return null;
                }
                $url->set_anchor('section-' . $sectionno);
            }
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
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Override if you need to perform some extra validation of the format options
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param array $errors errors already discovered in edit form validation
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     *         Do not repeat errors from $errors param here
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function edit_form_validation($data, $files, $errors) {
        $courseid = $data['id'];
        $reterrors = array();
        if (!$data['enablecompletion'] && $data['courseshowtileprogress']) {
            $reterrors['courseshowtileprogress'] = get_string('courseshowtileprogress_error', 'format_tiles');
        }
        if (($data['displayfilterbar'] == FORMAT_TILES_FILTERBAR_OUTCOMES
                || $data['displayfilterbar'] == FORMAT_TILES_FILTERBAR_BOTH)
            && empty($this->format_tiles_get_course_outcomes($courseid))) {
            $outcomeslink = html_writer::link(
                new moodle_url('/grade/edit/outcome/course.php', array('id' => $courseid)),
                new lang_string('outcomes', 'format_tiles')
            );
            $reterrors['displayfilterbar'] = get_string('displayfilterbar_error', 'format_tiles') . ' ' . $outcomeslink;
        }
        return $reterrors;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     * @return void
     * @throws coding_exception
     * @throws moodle_exception
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
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
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
        if (get_config('format_tiles', 'usejavascriptnav') && !(\core_useragent::is_ie())) {
            if (!get_user_preferences('format_tiles_stopjsnav', 0)) {
                $url = new moodle_url('/course/view.php', array('id' => $course->id, 'stopjsnav' => 1));
                $settingnode = $node->add(
                    get_string('jsdeactivate', 'format_tiles'),
                    $url->out(),
                    navigation_node::TYPE_SETTING,
                    null,
                    null,
                    new pix_icon(
                        'toggle-on',
                        get_string('jsdeactivate', 'format_tiles'),
                        'format_tiles'
                    )
                );
                $settingnode->nodetype = navigation_node::NODETYPE_LEAF;
                // Can't add classes or ids here if using boost (works in clean).
                $settingnode->id = 'tiles_stopjsnav';
                $settingnode->add_class('tiles_coursenav hidden');

                // Now the Data Preference menu item.
                if (!get_config('format_tiles', 'assumedatastoreconsent')) {
                    $url = new moodle_url('/course/view.php', array('id' => $course->id, 'datapref' => 1));
                    $settingnode = $node->add(
                        get_string('datapref', 'format_tiles'),
                        $url->out(),
                        navigation_node::TYPE_SETTING,
                        null,
                        null,
                        new pix_icon(
                            'i/db',
                            get_string('datapref', 'format_tiles')
                        )
                    );
                    $settingnode->nodetype = navigation_node::NODETYPE_LEAF;

                    // Can't add classes or ids here if using boost (works in clean).
                    $settingnode->id = 'tiles_datapref';
                    $settingnode->add_class('tiles_coursenav hidden');
                }

            } else {
                $settingnode = $node->add(
                    get_string('jsactivate', 'format_tiles'),
                    new moodle_url('/course/view.php', array('id' => $course->id, 'stopjsnav' => 1)),
                    navigation_node::TYPE_SETTING,
                    null,
                    null,
                    new pix_icon(
                        'toggle-off',
                        get_string('jsactivate', 'format_tiles'),
                        'format_tiles'
                    )
                );
                $settingnode->nodetype = navigation_node::NODETYPE_LEAF;

                // Can't add classes or ids here if using boost (works in clean).
                $settingnode->id = 'tiles_stopjsnav';
                $settingnode->add_class('tiles_coursenav hidden');
            }
        }
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax response
     * @throws moodle_exception
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
     * Iterates through all the colours entered by the administrator under the plugin settings page
     * @return array list of all the colours and their names for use in the settings forms
     * @throws dml_exception
     */
    private function format_tiles_get_tiles_palette() {
        $palette = array();
        for ($i = 1; $i <= 10; $i++) {
            $colourname = get_config('format_tiles', 'colourname' . $i);
            $tilecolour = get_config('format_tiles', 'tilecolour' . $i);
            if ($tilecolour != '' and $tilecolour != '#000') {
                $palette[$tilecolour] = $colourname;
            }
        }
        return $palette;
    }

    /**
     * Whether this format allows to delete sections (Moodle 3.1+)
     * If format supports deleting sections it is also recommended to define language string
     * 'deletesection' inside the format.
     * Do not call this function directly, instead use {@see course_can_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * @param bool $foreditform
     * @return array of options
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseformatoptions = array(
                'hiddensections' => array(
                    'default' => 1,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => 1,
                    'type' => PARAM_INT,
                ),
                'defaulttileicon' => array(
                    'default' => 'pie-chart',
                    'type' => PARAM_TEXT,
                ),
                'basecolour' => array(
                    'default' => get_config('format_tiles', 'tilecolour1'),
                    'type' => PARAM_TEXT,
                ),
                'courseusesubtiles' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                ),
                'usesubtilesseczero' => array(
                    'default' => 0,
                    'type' => PARAM_INT
                ),
                'courseshowtileprogress' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                ),
                'displayfilterbar' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                ),
                'courseusebarforheadings' => array(
                    'default' => 1,
                    'type' => PARAM_INT,
                )
            );
            if ((get_config('format_tiles', 'followthemecolour'))) {
                unset($courseformatoptions['basecolour']);
            }
            if (!get_config('format_tiles', 'allowsubtilesview')) {
                unset($courseformatoptions['courseusesubtiles']);
                unset($courseformatoptions['usesubtilesseczero']);
            }
        }

        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $tilespalette = $this->format_tiles_get_tiles_palette();
            $tileicons = (new \format_tiles\icon_set)->available_tile_icons($this->get_courseid());

            $courseformatoptionsedit = array(
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'element_type' => 'hidden',
                    'element_attributes' => array(
                        array(1 => new lang_string('hiddensectionsinvisible'))
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'hidden',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                ),
            );
            $label = get_string('defaulttileicon', 'format_tiles');
            $courseformatoptionsedit['defaulttileicon'] = array(
                'label' => $label,
                'element_type' => 'select',
                'element_attributes' => array($tileicons),
                'help' => 'defaulttileicon',
                'help_component' => 'format_tiles',
            );
            if (!(get_config('format_tiles', 'followthemecolour'))) {
                $courseformatoptionsedit['basecolour'] = array(
                    'label' => new lang_string('basecolour', 'format_tiles'),
                    'element_type' => 'select',
                    'element_attributes' => array($tilespalette),
                    'help' => 'basecolour',
                    'help_component' => 'format_tiles',
                );
            }
            $attributes = array(
                FORMAT_TILES_FILTERBAR_NONE => new lang_string('hide', 'format_tiles'),
                FORMAT_TILES_FILTERBAR_NUMBERS => new lang_string('filternumbers', 'format_tiles'),
            );
            $outcomeslink = '(' . new lang_string('outcomesunavailable', 'format_tiles') . ')';
            global $CFG;
            if (!empty($CFG->enableoutcomes)) {
                $outcomeslink = html_writer::link(
                    new moodle_url('/grade/edit/outcome/course.php',
                        array('id' => $this->get_courseid())),
                    '(' . new lang_string('outcomes', 'format_tiles') . ')'
                );
                $attributes[FORMAT_TILES_FILTERBAR_OUTCOMES] = new lang_string('filteroutcomes', 'format_tiles');
                $attributes[FORMAT_TILES_FILTERBAR_BOTH] = new lang_string('filterboth', 'format_tiles');
            }
            $courseformatoptionsedit['displayfilterbar'] = array(
                'label' => new lang_string('displayfilterbar', 'format_tiles') . ' ' . $outcomeslink,
                'element_type' => 'select',
                'element_attributes' => array($attributes),
                'help' => 'displayfilterbar',
                'help_component' => 'format_tiles',
            );
            $courseformatoptionsedit['courseshowtileprogress'] = array(
                'label' => new lang_string('courseshowtileprogress', 'format_tiles'),
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        0 => new lang_string('hide', 'format_tiles'),
                        1 => new lang_string('asfraction', 'format_tiles'),
                        2 => new lang_string('aspercentagedial', 'format_tiles'),
                    ),
                ),
                'help' => 'courseshowtileprogress',
                'help_component' => 'format_tiles'
            );

            $allowsubtilesview = get_config('format_tiles', 'allowsubtilesview');
            if ($allowsubtilesview) {
                $courseformatoptionsedit['courseusesubtiles'] = array(
                    'label' => new lang_string('courseusesubtiles', 'format_tiles'),
                    'element_type' => 'advcheckbox',
                    'element_attributes' => array(get_string('yes')),
                    'help' => 'courseusesubtiles',
                    'help_component' => 'format_tiles',
                );
            }
            $courseformatoptionsedit['courseusebarforheadings'] = array(
                'label' => new lang_string(
                    'courseusebarforheadings', 'format_tiles'
                ),
                'element_type' => 'advcheckbox',
                'element_attributes' => array(get_string('yes')),
                'help' => 'courseusebarforheadings',
                'help_component' => 'format_tiles',
            );
            if ($allowsubtilesview) {
                $courseformatoptionsedit['usesubtilesseczero'] = array(
                    'label' => new lang_string('usesubtilesseczero', 'format_tiles'),
                    'element_type' => 'advcheckbox',
                    'element_attributes' => array(get_string('notrecommended', 'format_tiles')),
                    'help' => 'usesubtilesseczero',
                    'help_component' => 'format_tiles',
                );
            }

            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Definitions of the additional options that this course format uses for section
     *
     * See {@see format_base::course_format_options()} for return array definition.
     *
     * Additionally section format options may have property 'cache' set to true
     * if this option needs to be cached in {@see get_fast_modinfo()}. The 'cache' property
     * is recommended to be set only for fields used in {@see format_base::get_section_name()},
     * {@see format_base::extend_course_navigation()} and {@see format_base::get_view_url()}
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
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function section_format_options($foreditform = false) {
        global $DB;
        $course = $this->get_course();
        $sectionformatoptions = array(
            'tileicon' => array(
                'default' => '',
                'type' => PARAM_TEXT,
            ),
        );
        if ($course->displayfilterbar == FORMAT_TILES_FILTERBAR_OUTCOMES
            || $course->displayfilterbar == FORMAT_TILES_FILTERBAR_BOTH) {
            $sectionformatoptions['tileoutcomeid'] = array(
                'default' => 0,
                'type' => PARAM_INT,
            );
        }
        if (get_config('format_tiles', 'allowphototiles')) {
            $sectionformatoptions['tilephoto'] = array(
                'default' => '',
                'type' => PARAM_TEXT
            );
        }
        if ($foreditform) {
            $defaultcoursetile = $course->defaulttileicon;
            $defaulticonarray = array(
                '' => get_string('defaultthiscourse', 'format_tiles') . ' (' . $defaultcoursetile . ')'
            );
            $tileicons = (new \format_tiles\icon_set)->available_tile_icons($course->id);
            $tileicons = array_merge($defaulticonarray, $tileicons);
            $sectionformatoptionsedit = array();

            $label = get_string('tileicon', 'format_tiles');
            $sectionformatoptionsedit['tileicon'] = array(
                'label' => $label,
                'element_type' => 'select',
                'element_attributes' => array($tileicons),
                'help' => 'tileicon',
            );

            if ($course->displayfilterbar == FORMAT_TILES_FILTERBAR_OUTCOMES
                || $course->displayfilterbar == FORMAT_TILES_FILTERBAR_BOTH) {
                $outcomeslink = html_writer::link(
                    new moodle_url('/grade/edit/outcome/course.php', array('id' => $course->id)),
                    '(' . new lang_string('outcomes', 'format_tiles') . ')'
                );
                $label = get_string('tileoutcome', 'format_tiles') . ' ' . $outcomeslink;
                $outcomes = $this->format_tiles_get_course_outcomes($course->id);
                if (!empty($outcomes)) {
                    $outcomes[0] = get_string('none', 'format_tiles');
                }
                $sectionformatoptionsedit['tileoutcomeid'] = array(
                    'label' => $label,
                    'element_type' => 'select',
                    'element_attributes' => array($outcomes),
                    'help' => 'tileoutcome',
                );
            }

            if (get_config('format_tiles', 'allowphototiles')) {
                $sectionformatoptionsedit['tilephoto'] = array(
                    'label' => get_string('uploadnewphoto', 'format_tiles'),
                    'element_type' => 'hidden',
                    'element_attributes' => array('' => '')
                );
            }
            $sectionformatoptions = array_merge_recursive($sectionformatoptions, $sectionformatoptionsedit);
        }
        return $sectionformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@see course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     * @throws HTML_QuickForm_Error
     * @throws coding_exception
     * @throws dml_exception
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $COURSE, $PAGE, $DB, $USER;
        $elements = parent::create_edit_form_elements($mform, $forsection);

        // Call the JS edit_form_helper.js, which in turn will call edit_icon_picker.js.
        if ($forsection) {
            $sectionid = optional_param('id', 0, PARAM_INT);
            $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid));
        } else {
            // We are on the course setting page so can ignore section.
            $section = 0;
            $sectionid = 0;
        }
        $jsparams = array(
            'pageType' => $PAGE->pagetype,
            'courseDefaultIcon' => $this->get_format_options()['defaulttileicon'],
            'courseId' => $COURSE->id,
            'sectionId' => $sectionid,
            'section' => $section,
            'userId' => $USER->id,
            get_config('format_tiles', 'allowphototiles') && $section !== 0, // No photos on course page.
            get_config('format_tiles', 'documentationurl')
        );
        $PAGE->requires->js_call_amd('format_tiles/edit_form_helper', 'init', $jsparams);

        if (!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID)) {
            // Add "numsections" to create course form - will force the course pre-populated with empty sections.
            // The "Number of sections" option is no longer available when editing course.
            // Instead teachers should delete and add sections when needed.

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
     * If course format was changed to 'tiles', we try to copy options
     * from the previous format.  We do not copy 'coursedisplay',
     * and 'hiddensections' as a defaut value of one makes sense for these for tiles format,
     * regardless of what they were.
     *
     * @param stdClass|array $data return value from {@see moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@see update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB, $USER;
        $data = (array)$data;
        if ($oldcourse !== null) {
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    }
                }
            }
        }

        if (isset($data['id']) && $data['id']) {
            $courseid = $data['id'];
            $coursecontext = context_course::instance($courseid);

            if (has_capability('moodle/course:update', $coursecontext)) {
                if ($oldcourse !== null && $oldcourse['format'] !== 'tiles') {
                    // We are switching in to tiles from something else.
                    // Double check we don't have any old tiles images in the {files} table.
                    format_tiles\tile_photo::delete_all_tile_photos_course($courseid);
                }

                // If we are changing from Grid format, we iterate through each of the grid images and set it up for this format.
                if ($oldcourse !== null && $oldcourse['format'] == 'grid') {
                    $gridformaticons = $DB->get_records('format_grid_icon', array('courseid' => $courseid), 'sectionid');
                    $fs = get_file_storage();
                    foreach ($gridformaticons as $gridformaticon) {
                        if (!$gridformaticon->image) {
                            continue;
                        }
                        $tilephoto = new \format_tiles\tile_photo($courseid, $gridformaticon->sectionid);
                        $gridfile = $fs->get_file(
                            $coursecontext->id,
                            'course',
                            'section',
                            $gridformaticon->sectionid,
                            '/gridimage/',
                            $gridformaticon->displayedimageindex . '_' . $gridformaticon->image
                        );
                        if ($gridfile) {
                            // We copy the grid image file into Tiles format, so it is included in backups etc.
                            $fs = get_file_storage();
                            $newfilerecord = \format_tiles\tile_photo::file_api_params();
                            $newfilerecord['contextid'] = $coursecontext->id;
                            $newfilerecord['itemid'] = $gridformaticon->sectionid;
                            $newfilerecord['userid'] = $USER->id;
                            $newfilerecord['filename'] = str_replace('_goi_', '_', $gridfile->get_filename());
                            $fs->delete_area_files(
                                $coursecontext->id,
                                $newfilerecord['component'],
                                $newfilerecord['filearea'],
                                $newfilerecord['itemid']
                            );
                            $newfile = $fs->create_file_from_storedfile($newfilerecord, $gridfile);
                            if ($newfile) {
                                $tilephoto->set_file($newfile);
                                // We *could* delete grid format files here, but we don't as they don't belong to us.
                                // If we don't, they will be included in export course archives.
                            }
                        } else {
                            debugging(
                                'Grid format image not found '
                                    . $gridformaticon->displayedimageindex . '_' . $gridformaticon->image,
                                DEBUG_DEVELOPER
                            );
                        }
                    }
                }

                // While we are changing the format options, set section zero to visible if it is hidden.
                // Should never be hidden but rarely it happens, for reasons which are not clear esp with onetopic format.
                // See https://moodle.org/mod/forum/discuss.php?d=356850 and MDL-37256).

                if ($section = $DB->get_record("course_sections", array('course' => $courseid, 'section' => 0))) {
                    if (!$section->visible) {
                        set_section_visible($section->course, 0, 1);
                    }
                }
            }
        }

        if (isset($data['courseusesubtiles']) && $data['courseusesubtiles'] == 0) {
            // We are deactivating sub tiles at course level so do it at sec zero level too.
            $data['usesubtilesseczero'] = 0;
        }
        return $this->update_format_options($data);
    }

    /**
     * Updates format options for a section
     * Includes a check to strip out default values for tile icon or outcome id
     * as it would be wasteful to store large volumes of these on a per section basis
     *
     * Section id is expected in $data->id (or $data['id'])
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data return value from {@see moodleform::get_data()} or array with data
     * @return bool whether there were any changes to the options values
     * @throws dml_exception
     */
    public function update_section_format_options($data) {
        global $DB;
        $data = (array)$data;
        $oldvalues = array(
            'iconthistile' => $DB->get_field(
                'course_format_options', 'value',
                ['courseid' => $this->courseid, 'format' => 'tiles', 'sectionid' => $data['id'], 'name' => 'tileicon']
            ),
            'outcomethistile' => $DB->get_record(
                'course_format_options',
                ['courseid' => $this->courseid, 'format' => 'tiles', 'sectionid' => $data['id'], 'name' => 'tileoutcomeid']
            ),
            'photothistile' => \format_tiles\tile_photo::get_course_format_option_value($data['id'], $this->courseid)
        );

        // If the edit is taking place from format_tiles_inplace_editable(),
        // the data array may not contain the tile icon and outcome id at all.
        // So add these items in if missing.
        if (!isset($data['tileicon']) && $oldvalues['iconthistile']) {
            $data['tileicon'] = $oldvalues['iconthistile'];
        }
        if (!isset($data['tileoutcomeid']) && $oldvalues['outcomethistile']) {
            $data['tileoutcomeid'] = $oldvalues['outcomethistile'];
        }
        if (!isset($data['tilephoto']) && $oldvalues['photothistile']) {
            $data['tilephoto'] = $oldvalues['photothistile'];
        }
        // Unset the new values if null, before we send to update.
        // This is so that we don't get a false positive as to whether it has changed or not.
        if (isset($data['tileicon']) && $data['tileicon'] == '') {
            unset($data['tileicon']);
        }
        if (isset($data['tileoutcomeid']) && $data['tileoutcomeid'] == '0') {
            unset($data['tileoutcomeid']);
        }
        if (isset($data['tilephoto']) && $data['tilephoto'] == '') {
            unset($data['tilephoto']);
        }
        // Now send the update.
        $result = $this->update_format_options($data, $data['id']);

        // Now remove any default values such as '' or '0' which the update stored in the database as they are redundant.
        $keystoremove = ['tileicon', 'tileoutcomeid', 'tilephoto'];
        foreach ($keystoremove as $key) {
            if (!isset($data[$key])) {
                $DB->delete_records(
                    'course_format_options',
                    ['courseid' => $this->courseid, 'format' => 'tiles', 'sectionid' => $data['id'], 'name' => $key]
                );
                if (isset($oldvalues[$key]) && $oldvalues[$key]) {
                    // Used to have a value so return true to indicate it changed.
                    $result = true;
                }
            }
        }
        return $result;
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
     * @throws coding_exception
     */
    public function inplace_editable_render_section_name($section, $linkifneeded = true,
                                                         $editable = null, $edithint = null, $editlabel = null) {
        global $USER;
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname', 'format_tiles');
        }
        if (empty($editlabel)) {
            $title = get_section_name($section->course, $section);
            $editlabel = new lang_string('newsectionname', 'format_tiles', $title);
        }

        if ($editable === null) {
            $editable = !empty($USER->editing) && has_capability('moodle/course:update',
                    context_course::instance($section->course));
        }

        $displayvalue = $title = get_section_name($section->course, $section);
        if ($linkifneeded) {
            // Display link under the section name if the course format setting is to display one section per page.
            $url = new moodle_url(
                '/course/view.php',
                array('id' => $section->course, 'section' => $section->section, 'singlesec' => $section->section)
            );
            if ($url) {
                $displayvalue = html_writer::link($url, $title);
            }
            $itemtype = 'sectionname';
        } else {
            // If $linkifneeded==false, we never display the link (this is used when rendering the section header).
            // Itemtype 'sectionnamenl' (nl=no link) will tell the callback that link should not be rendered -
            // there is no other way callback can know where we display the section name.
            $itemtype = 'sectionnamenl';
        }
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname');
        }
        if (empty($editlabel)) {
            $editlabel = new lang_string('newsectionname', '', $title);
        }

        return new \core\output\inplace_editable('format_' . $this->format, $itemtype, $section->id, $editable,
            $displayvalue, $section->name, $edithint, $editlabel);
    }


    /**
     * Get an array of all the Outcomes set for this course by the teacher, so that they can
     * be attached to individual Tiles, and then used to filter tiles by Outcome
     * @see get_filter_outcome_buttons()
     * @see course_format_options() and the displayfilterbar option
     * @param int $courseid
     * @return array|null
     */
    public function format_tiles_get_course_outcomes($courseid) {
        global $CFG;
        if (!empty($CFG->enableoutcomes)) {
            require_once($CFG->libdir . '/gradelib.php');
            $outcomes = [];
            $outcomesfull = grade_outcome::fetch_all_available($courseid);
            foreach ($outcomesfull as $outcome) {
                $outcomes[$outcome->id] = $outcome->fullname;
            }
            asort($outcomes);
            return $outcomes;
        } else {
            return null;
        }
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     * Copied from format_topics
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
     * @throws moodle_exception
     * @throws required_capability_exception
     */
    public function section_action($section, $action, $sr) {
        global $PAGE;

        if ($section->section && ($action === 'setmarker' || $action === 'removemarker')) {
            // Format 'tiles' allows to set and remove markers in addition to common section actions.
            require_capability('moodle/course:setcurrentsection', context_course::instance($this->courseid));
            course_set_marker($this->courseid, ($action === 'setmarker') ? $section->section : 0);
            return null;
        }

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_tiles');
        $rv['section_availability'] = $renderer->section_availability($this->get_section($section));
        return $rv;
    }

    /**
     * Allows course format to execute code on moodle_page::set_course()
     * Used here to ensure that, before starting to load the page,
     * we establish if the user is changing their pref for using JS nav
     * and change the setting if so
     *
     * @param moodle_page $page instance of page calling set_course
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function page_set_course(moodle_page $page) {
        if (get_config('format_tiles', 'usejavascriptnav')) {
            if (optional_param('stopjsnav', 0, PARAM_INT) == 1) {
                // User is toggling JS nav setting.
                $existingstoppref = get_user_preferences('format_tiles_stopjsnav', 0);
                if (!$existingstoppref) {
                    // Did not already have it disabled.
                    set_user_preference('format_tiles_stopjsnav', 1);
                } else {
                    // User previously disabled it, but now is re-enabling.
                    unset_user_preference('format_tiles_stopjsnav');
                    \core\notification::success(get_string('jsreactivated', 'format_tiles'));
                }
                if ($page->course->id) {
                    redirect(new moodle_url('/course/view.php', array('id' => $page->course->id)));
                }
            }
        }
    }
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable | null
 * @throws dml_exception
 */
function format_tiles_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            array($itemid, 'tiles'), MUST_EXIST);
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}

/**
 * Get icon mapping for font-awesome.
 * @return array the icons for which theme should use font awesome.
 */
function format_tiles_get_fontawesome_icon_map() {
    $iconset = new format_tiles\icon_set();
    return $iconset->get_font_awesome_icon_map();
}

/**
 * Serves any files associated with the plugin (e.g. tile photos).
 * For explanation see https://docs.moodle.org/dev/File_API
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
function format_tiles_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel != CONTEXT_COURSE && $context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }
    if ($filearea !== 'tilephoto') {
        debugging('Invalid file area ' . $filearea);
        send_file_not_found();
    }

    // Make sure the user is logged in and has access to the course.
    require_login($course);

    $fileapiparams = \format_tiles\tile_photo::file_api_params();
    $fs = get_file_storage();
    $sectionid = (int)$args[0];
    $filepath = '/' . $args[1] .'/';
    $filename = $args[2];
    $file = $fs->get_file($context->id, $fileapiparams['component'], $filearea, $sectionid, $filepath, $filename);
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
