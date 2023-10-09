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
 * This file contains main class for the course format Weeks
 *
 * @since     Moodle 2.0
 * @package   format_weeks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');
require_once($CFG->dirroot. '/course/lib.php');

/**
 * Main class for the Weeks course format
 *
 * @package    format_weeks
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_weeks extends core_courseformat\base {

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    public function uses_course_index() {
        return true;
    }

    public function uses_indentation(): bool {
        return (get_config('format_weeks', 'indentation')) ? true : false;
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    public function page_title(): string {
        return get_string('weeklyoutline');
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            // Return the name the user set.
            return format_string($section->name, true, array('context' => context_course::instance($this->courseid)));
        } else {
            return $this->get_default_section_name($section);
        }
    }

    /**
     * Returns the default section name for the weekly course format.
     *
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * Otherwise, the default format of "[start date] - [end date]" will be returned.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_weeks');
        } else {
            $dates = $this->get_section_dates($section);

            // We subtract 24 hours for display purposes.
            $dates->end = ($dates->end - 86400);

            $dateformat = get_string('strftimedateshort');
            $weekday = userdate($dates->start, $dateformat);
            $endweekday = userdate($dates->end, $dateformat);
            return $weekday.' - '.$endweekday;
        }
    }

    /**
     * Returns the name for the highlighted section.
     *
     * @return string The name for the highlighted section based on the given course format.
     */
    public function get_section_highlighted_name(): string {
        return get_string('currentsection', 'format_weeks');
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
                $usercoursedisplay = $course->coursedisplay ?? COURSE_DISPLAY_SINGLEPAGE;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                if (empty($CFG->linkcoursesections) && !empty($options['navigation'])) {
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

    public function supports_components() {
        return true;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // if section is specified in course/view.php, make sure it is expanded in navigation
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }
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
    function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $current = -1;
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
                if ($this->is_section_current($section)) {
                    $current = $number;
                }
            }
        }
        return array('sectiontitles' => $titles, 'current' => $current, 'action' => 'move');
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
     * Weeks format uses the following options:
     * - coursedisplay
     * - hiddensections
     * - automaticenddate
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
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay ?? COURSE_DISPLAY_SINGLEPAGE,
                    'type' => PARAM_INT,
                ),
                'automaticenddate' => array(
                    'default' => 1,
                    'type' => PARAM_BOOL,
                ),
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
                ),
                'automaticenddate' => array(
                    'label' => new lang_string('automaticenddate', 'format_weeks'),
                    'help' => 'automaticenddate',
                    'help_component' => 'format_weeks',
                    'element_type' => 'advcheckbox',
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

        // Re-order things.
        $mform->insertElementBefore($mform->removeElement('automaticenddate', false), 'idnumber');
        $mform->disabledIf('enddate', 'automaticenddate', 'checked');
        foreach ($elements as $key => $element) {
            if ($element->getName() == 'automaticenddate') {
                unset($elements[$key]);
            }
        }

        return $elements;
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'weeks', we try to copy options
     * 'coursedisplay', 'numsections' and 'hiddensections' from the previous format.
     * If previous course format did not have 'numsections' option, we populate it with the
     * current number of sections
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
                    }
                }
            }
        }
        return $this->update_format_options($data);
    }

    /**
     * Return the start and end date of the passed section
     *
     * @param int|stdClass|section_info $section section to get the dates for
     * @param int $startdate Force course start date, useful when the course is not yet created
     * @return stdClass property start for startdate, property end for enddate
     */
    public function get_section_dates($section, $startdate = false) {
        global $USER;

        if ($startdate === false) {
            $course = $this->get_course();
            $userdates = course_get_course_dates_for_user_id($course, $USER->id);
            $startdate = $userdates['start'];
        }

        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }

        // Create a DateTime object for the start date.
        $startdateobj = new DateTime("@$startdate");

        // Calculate the interval for one week.
        $oneweekinterval = new DateInterval('P7D');

        // Calculate the interval for the specified number of sections.
        for ($i = 1; $i < $sectionnum; $i++) {
            $startdateobj->add($oneweekinterval);
        }

        // Calculate the end date.
        $enddateobj = clone $startdateobj;
        $enddateobj->add($oneweekinterval);

        $dates = new stdClass();
        $dates->start = $startdateobj->getTimestamp();
        $dates->end = $enddateobj->getTimestamp();

        return $dates;
    }

    /**
     * Returns true if the specified week is current
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function is_section_current($section) {
        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }
        if ($sectionnum < 1) {
            return false;
        }
        $timenow = time();
        $dates = $this->get_section_dates($section);
        return (($timenow >= $dates->start) && ($timenow < $dates->end));
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
            $edithint = new lang_string('editsectionname', 'format_weeks');
        }
        if (empty($editlabel)) {
            $title = get_section_name($section->course, $section);
            $editlabel = new lang_string('newsectionname', 'format_weeks', $title);
        }
        return parent::inplace_editable_render_section_name($section, $linkifneeded, $editable, $edithint, $editlabel);
    }

    /**
     * Returns the default end date for weeks course format.
     *
     * @param moodleform $mform
     * @param array $fieldnames The form - field names mapping.
     * @return int
     */
    public function get_default_course_enddate($mform, $fieldnames = array()) {

        if (empty($fieldnames['startdate'])) {
            $fieldnames['startdate'] = 'startdate';
        }

        if (empty($fieldnames['numsections'])) {
            $fieldnames['numsections'] = 'numsections';
        }

        $startdate = $this->get_form_start_date($mform, $fieldnames);
        if ($mform->elementExists($fieldnames['numsections'])) {
            $numsections = $mform->getElementValue($fieldnames['numsections']);
            $numsections = $mform->getElement($fieldnames['numsections'])->exportValue($numsections);
        } else if ($this->get_courseid()) {
            // For existing courses get the number of sections.
            $numsections = $this->get_last_section_number();
        } else {
            // Fallback to the default value for new courses.
            $numsections = get_config('moodlecourse', $fieldnames['numsections']);
        }

        // Final week's last day.
        $dates = $this->get_section_dates(intval($numsections), $startdate);
        return $dates->end;
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

    public function section_action($section, $action, $sr) {
        global $PAGE;

        // Call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_weeks');

        if (!($section instanceof section_info)) {
            $modinfo = course_modinfo::instance($this->courseid);
            $section = $modinfo->get_section_info($section->section);
        }
        $elementclass = $this->get_output_classname('content\\section\\availability');
        $availability = new $elementclass($this, $section);

        $rv['section_availability'] = $renderer->render($availability);

        return $rv;
    }

    /**
     * Updates the end date for a course in weeks format if option automaticenddate is set.
     *
     * This method is called from event observers and it can not use any modinfo or format caches because
     * events are triggered before the caches are reset.
     *
     * @param int $courseid
     */
    public static function update_end_date($courseid) {
        global $DB, $COURSE;

        // Use one DB query to retrieve necessary fields in course, value for automaticenddate and number of the last
        // section. This query will also validate that the course is indeed in 'weeks' format.
        $insql = "SELECT c.id, c.format, c.startdate, c.enddate, MAX(s.section) AS lastsection
                    FROM {course} c
                    JOIN {course_sections} s
                      ON s.course = c.id
                   WHERE c.format = :format
                     AND c.id = :courseid
                GROUP BY c.id, c.format, c.startdate, c.enddate";
        $sql = "SELECT co.id, co.format, co.startdate, co.enddate, co.lastsection, fo.value AS automaticenddate
                  FROM ($insql) co
             LEFT JOIN {course_format_options} fo
                    ON fo.courseid = co.id
                   AND fo.format = co.format
                   AND fo.name = :optionname
                   AND fo.sectionid = 0";
        $course = $DB->get_record_sql($sql,
            ['optionname' => 'automaticenddate', 'format' => 'weeks', 'courseid' => $courseid]);

        if (!$course) {
            // Looks like it is a course in a different format, nothing to do here.
            return;
        }

        // Create an instance of this class and mock the course object.
        $format = new format_weeks('weeks', $courseid);
        $format->course = $course;

        // If automaticenddate is not specified take the default value.
        if (!isset($course->automaticenddate)) {
            $defaults = $format->course_format_options();
            $course->automaticenddate = $defaults['automaticenddate']['default'];
        }

        // Check that the course format for setting an automatic date is set.
        if (!empty($course->automaticenddate)) {
            // Get the final week's last day.
            $dates = $format->get_section_dates((int)$course->lastsection);

            // Set the course end date.
            if ($course->enddate != $dates->end) {
                $DB->set_field('course', 'enddate', $dates->end, array('id' => $course->id));
                if (isset($COURSE->id) && $COURSE->id == $courseid) {
                    $COURSE->enddate = $dates->end;
                }
            }
        }
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of configuration settings
     * @since Moodle 3.5
     */
    public function get_config_for_external() {
        // Return everything (nothing to hide).
        $formatoptions = $this->get_format_options();
        $formatoptions['indentation'] = get_config('format_weeks', 'indentation');
        return $formatoptions;
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
function format_weeks_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            array($itemid, 'weeks'), MUST_EXIST);
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}
