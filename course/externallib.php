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
 * External course API
 *
 * @package    core_course
 * @category   external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use core_course\external\course_summary_exporter;

require_once("$CFG->libdir/externallib.php");
require_once(__DIR__."/lib.php");

/**
 * Course external functions
 *
 * @package    core_course
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class core_course_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function get_course_contents_parameters() {
        return new external_function_parameters(
                array('courseid' => new external_value(PARAM_INT, 'course id'),
                      'options' => new external_multiple_structure (
                              new external_single_structure(
                                array(
                                    'name' => new external_value(PARAM_ALPHANUM,
                                                'The expected keys (value format) are:
                                                excludemodules (bool) Do not return modules, return only the sections structure
                                                excludecontents (bool) Do not return module contents (i.e: files inside a resource)
                                                includestealthmodules (bool) Return stealth modules for students in a special
                                                    section (with id -1)
                                                sectionid (int) Return only this section
                                                sectionnumber (int) Return only this section with number (order)
                                                cmid (int) Return only this module information (among the whole sections structure)
                                                modname (string) Return only modules with this name "label, forum, etc..."
                                                modid (int) Return only the module with this id (to be used with modname'),
                                    'value' => new external_value(PARAM_RAW, 'the value of the option,
                                                                    this param is personaly validated in the external function.')
                              )
                      ), 'Options, used since Moodle 2.9', VALUE_DEFAULT, array())
                )
        );
    }

    /**
     * Get course contents
     *
     * @param int $courseid course id
     * @param array $options Options for filtering the results, used since Moodle 2.9
     * @return array
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function get_course_contents($courseid, $options = array()) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        require_once($CFG->libdir . '/completionlib.php');

        //validate parameter
        $params = self::validate_parameters(self::get_course_contents_parameters(),
                        array('courseid' => $courseid, 'options' => $options));

        $filters = array();
        if (!empty($params['options'])) {

            foreach ($params['options'] as $option) {
                $name = trim($option['name']);
                // Avoid duplicated options.
                if (!isset($filters[$name])) {
                    switch ($name) {
                        case 'excludemodules':
                        case 'excludecontents':
                        case 'includestealthmodules':
                            $value = clean_param($option['value'], PARAM_BOOL);
                            $filters[$name] = $value;
                            break;
                        case 'sectionid':
                        case 'sectionnumber':
                        case 'cmid':
                        case 'modid':
                            $value = clean_param($option['value'], PARAM_INT);
                            if (is_numeric($value)) {
                                $filters[$name] = $value;
                            } else {
                                throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                            }
                            break;
                        case 'modname':
                            $value = clean_param($option['value'], PARAM_PLUGIN);
                            if ($value) {
                                $filters[$name] = $value;
                            } else {
                                throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                            }
                            break;
                        default:
                            throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                    }
                }
            }
        }

        //retrieve the course
        $course = $DB->get_record('course', array('id' => $params['courseid']), '*', MUST_EXIST);

        if ($course->id != SITEID) {
            // Check course format exist.
            if (!file_exists($CFG->dirroot . '/course/format/' . $course->format . '/lib.php')) {
                throw new moodle_exception('cannotgetcoursecontents', 'webservice', '', null,
                                            get_string('courseformatnotfound', 'error', $course->format));
            } else {
                require_once($CFG->dirroot . '/course/format/' . $course->format . '/lib.php');
            }
        }

        // now security checks
        $context = context_course::instance($course->id, IGNORE_MISSING);
        try {
            self::validate_context($context);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $course->id;
            throw new moodle_exception('errorcoursecontextnotvalid', 'webservice', '', $exceptionparam);
        }

        $canupdatecourse = has_capability('moodle/course:update', $context);

        //create return value
        $coursecontents = array();

        if ($canupdatecourse or $course->visible
                or has_capability('moodle/course:viewhiddencourses', $context)) {

            //retrieve sections
            $modinfo = get_fast_modinfo($course);
            $sections = $modinfo->get_section_info_all();
            $courseformat = course_get_format($course);
            $coursenumsections = $courseformat->get_last_section_number();
            $stealthmodules = array();   // Array to keep all the modules available but not visible in a course section/topic.

            $completioninfo = new completion_info($course);

            //for each sections (first displayed to last displayed)
            $modinfosections = $modinfo->get_sections();
            foreach ($sections as $key => $section) {

                // This becomes true when we are filtering and we found the value to filter with.
                $sectionfound = false;

                // Filter by section id.
                if (!empty($filters['sectionid'])) {
                    if ($section->id != $filters['sectionid']) {
                        continue;
                    } else {
                        $sectionfound = true;
                    }
                }

                // Filter by section number. Note that 0 is a valid section number.
                if (isset($filters['sectionnumber'])) {
                    if ($key != $filters['sectionnumber']) {
                        continue;
                    } else {
                        $sectionfound = true;
                    }
                }

                // reset $sectioncontents
                $sectionvalues = array();
                $sectionvalues['id'] = $section->id;
                $sectionvalues['name'] = get_section_name($course, $section);
                $sectionvalues['visible'] = $section->visible;

                $options = (object) array('noclean' => true);
                list($sectionvalues['summary'], $sectionvalues['summaryformat']) =
                        external_format_text($section->summary, $section->summaryformat,
                                $context->id, 'course', 'section', $section->id, $options);
                $sectionvalues['section'] = $section->section;
                $sectionvalues['hiddenbynumsections'] = $section->section > $coursenumsections ? 1 : 0;
                $sectionvalues['uservisible'] = $section->uservisible;
                if (!empty($section->availableinfo)) {
                    $sectionvalues['availabilityinfo'] = \core_availability\info::format_info($section->availableinfo, $course);
                }

                $sectioncontents = array();

                // For each module of the section.
                if (empty($filters['excludemodules']) and !empty($modinfosections[$section->section])) {
                    foreach ($modinfosections[$section->section] as $cmid) {
                        $cm = $modinfo->cms[$cmid];

                        // Stop here if the module is not visible to the user on the course main page:
                        // The user can't access the module and the user can't view the module on the course page.
                        if (!$cm->uservisible && !$cm->is_visible_on_course_page()) {
                            continue;
                        }

                        // This becomes true when we are filtering and we found the value to filter with.
                        $modfound = false;

                        // Filter by cmid.
                        if (!empty($filters['cmid'])) {
                            if ($cmid != $filters['cmid']) {
                                continue;
                            } else {
                                $modfound = true;
                            }
                        }

                        // Filter by module name and id.
                        if (!empty($filters['modname'])) {
                            if ($cm->modname != $filters['modname']) {
                                continue;
                            } else if (!empty($filters['modid'])) {
                                if ($cm->instance != $filters['modid']) {
                                    continue;
                                } else {
                                    // Note that if we are only filtering by modname we don't break the loop.
                                    $modfound = true;
                                }
                            }
                        }

                        $module = array();

                        $modcontext = context_module::instance($cm->id);

                        //common info (for people being able to see the module or availability dates)
                        $module['id'] = $cm->id;
                        $module['name'] = external_format_string($cm->name, $modcontext->id);
                        $module['instance'] = $cm->instance;
                        $module['contextid'] = $modcontext->id;
                        $module['modname'] = (string) $cm->modname;
                        $module['modplural'] = (string) $cm->modplural;
                        $module['modicon'] = $cm->get_icon_url()->out(false);
                        $module['indent'] = $cm->indent;
                        $module['onclick'] = $cm->onclick;
                        $module['afterlink'] = $cm->afterlink;
                        $module['customdata'] = json_encode($cm->customdata);
                        $module['completion'] = $cm->completion;
                        $module['noviewlink'] = plugin_supports('mod', $cm->modname, FEATURE_NO_VIEW_LINK, false);

                        // Check module completion.
                        $completion = $completioninfo->is_enabled($cm);
                        if ($completion != COMPLETION_DISABLED) {
                            $completiondata = $completioninfo->get_data($cm, true);
                            $module['completiondata'] = array(
                                'state'         => $completiondata->completionstate,
                                'timecompleted' => $completiondata->timemodified,
                                'overrideby'    => $completiondata->overrideby,
                                'valueused'     => core_availability\info::completion_value_used($course, $cm->id)
                            );
                        }

                        if (!empty($cm->showdescription) or $module['noviewlink']) {
                            // We want to use the external format. However from reading get_formatted_content(), $cm->content format is always FORMAT_HTML.
                            $options = array('noclean' => true);
                            list($module['description'], $descriptionformat) = external_format_text($cm->content,
                                FORMAT_HTML, $modcontext->id, $cm->modname, 'intro', $cm->id, $options);
                        }

                        //url of the module
                        $url = $cm->url;
                        if ($url) { //labels don't have url
                            $module['url'] = $url->out(false);
                        }

                        $canviewhidden = has_capability('moodle/course:viewhiddenactivities',
                                            context_module::instance($cm->id));
                        //user that can view hidden module should know about the visibility
                        $module['visible'] = $cm->visible;
                        $module['visibleoncoursepage'] = $cm->visibleoncoursepage;
                        $module['uservisible'] = $cm->uservisible;
                        if (!empty($cm->availableinfo)) {
                            $module['availabilityinfo'] = \core_availability\info::format_info($cm->availableinfo, $course);
                        }

                        // Availability date (also send to user who can see hidden module).
                        if ($CFG->enableavailability && ($canviewhidden || $canupdatecourse)) {
                            $module['availability'] = $cm->availability;
                        }

                        // Return contents only if the user can access to the module.
                        if ($cm->uservisible) {
                            $baseurl = 'webservice/pluginfile.php';

                            // Call $modulename_export_contents (each module callback take care about checking the capabilities).
                            require_once($CFG->dirroot . '/mod/' . $cm->modname . '/lib.php');
                            $getcontentfunction = $cm->modname.'_export_contents';
                            if (function_exists($getcontentfunction)) {
                                $contents = $getcontentfunction($cm, $baseurl);
                                $module['contentsinfo'] = array(
                                    'filescount' => count($contents),
                                    'filessize' => 0,
                                    'lastmodified' => 0,
                                    'mimetypes' => array(),
                                );
                                foreach ($contents as $content) {
                                    // Check repository file (only main file).
                                    if (!isset($module['contentsinfo']['repositorytype'])) {
                                        $module['contentsinfo']['repositorytype'] =
                                            isset($content['repositorytype']) ? $content['repositorytype'] : '';
                                    }
                                    if (isset($content['filesize'])) {
                                        $module['contentsinfo']['filessize'] += $content['filesize'];
                                    }
                                    if (isset($content['timemodified']) &&
                                            ($content['timemodified'] > $module['contentsinfo']['lastmodified'])) {

                                        $module['contentsinfo']['lastmodified'] = $content['timemodified'];
                                    }
                                    if (isset($content['mimetype'])) {
                                        $module['contentsinfo']['mimetypes'][$content['mimetype']] = $content['mimetype'];
                                    }
                                }

                                if (empty($filters['excludecontents']) and !empty($contents)) {
                                    $module['contents'] = $contents;
                                } else {
                                    $module['contents'] = array();
                                }
                            }
                        }

                        // Assign result to $sectioncontents, there is an exception,
                        // stealth activities in non-visible sections for students go to a special section.
                        if (!empty($filters['includestealthmodules']) && !$section->uservisible && $cm->is_stealth()) {
                            $stealthmodules[] = $module;
                        } else {
                            $sectioncontents[] = $module;
                        }

                        // If we just did a filtering, break the loop.
                        if ($modfound) {
                            break;
                        }

                    }
                }
                $sectionvalues['modules'] = $sectioncontents;

                // assign result to $coursecontents
                $coursecontents[$key] = $sectionvalues;

                // Break the loop if we are filtering.
                if ($sectionfound) {
                    break;
                }
            }

            // Now that we have iterated over all the sections and activities, check the visibility.
            // We didn't this before to be able to retrieve stealth activities.
            foreach ($coursecontents as $sectionnumber => $sectioncontents) {
                $section = $sections[$sectionnumber];
                // Show the section if the user is permitted to access it OR
                // if it's not available but there is some available info text which explains the reason & should display OR
                // the course is configured to show hidden sections name.
                $showsection = $section->uservisible ||
                    ($section->visible && !$section->available && !empty($section->availableinfo)) ||
                    (!$section->visible && empty($courseformat->get_course()->hiddensections));

                if (!$showsection) {
                    unset($coursecontents[$sectionnumber]);
                    continue;
                }

                // Remove section and modules information if the section is not visible for the user.
                if (!$section->uservisible) {
                    $coursecontents[$sectionnumber]['modules'] = array();
                    // Remove summary information if the section is completely hidden only,
                    // even if the section is not user visible, the summary is always displayed among the availability information.
                    if (!$section->visible) {
                        $coursecontents[$sectionnumber]['summary'] = '';
                    }
                }
            }

            // Include stealth modules in special section (without any info).
            if (!empty($stealthmodules)) {
                $coursecontents[] = array(
                    'id' => -1,
                    'name' => '',
                    'summary' => '',
                    'summaryformat' => FORMAT_MOODLE,
                    'modules' => $stealthmodules
                );
            }

        }
        return $coursecontents;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function get_course_contents_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Section ID'),
                    'name' => new external_value(PARAM_RAW, 'Section name'),
                    'visible' => new external_value(PARAM_INT, 'is the section visible', VALUE_OPTIONAL),
                    'summary' => new external_value(PARAM_RAW, 'Section description'),
                    'summaryformat' => new external_format_value('summary'),
                    'section' => new external_value(PARAM_INT, 'Section number inside the course', VALUE_OPTIONAL),
                    'hiddenbynumsections' => new external_value(PARAM_INT, 'Whether is a section hidden in the course format',
                                                                VALUE_OPTIONAL),
                    'uservisible' => new external_value(PARAM_BOOL, 'Is the section visible for the user?', VALUE_OPTIONAL),
                    'availabilityinfo' => new external_value(PARAM_RAW, 'Availability information.', VALUE_OPTIONAL),
                    'modules' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'id' => new external_value(PARAM_INT, 'activity id'),
                                    'url' => new external_value(PARAM_URL, 'activity url', VALUE_OPTIONAL),
                                    'name' => new external_value(PARAM_RAW, 'activity module name'),
                                    'instance' => new external_value(PARAM_INT, 'instance id', VALUE_OPTIONAL),
                                    'contextid' => new external_value(PARAM_INT, 'Activity context id.', VALUE_OPTIONAL),
                                    'description' => new external_value(PARAM_RAW, 'activity description', VALUE_OPTIONAL),
                                    'visible' => new external_value(PARAM_INT, 'is the module visible', VALUE_OPTIONAL),
                                    'uservisible' => new external_value(PARAM_BOOL, 'Is the module visible for the user?',
                                        VALUE_OPTIONAL),
                                    'availabilityinfo' => new external_value(PARAM_RAW, 'Availability information.',
                                        VALUE_OPTIONAL),
                                    'visibleoncoursepage' => new external_value(PARAM_INT, 'is the module visible on course page',
                                        VALUE_OPTIONAL),
                                    'modicon' => new external_value(PARAM_URL, 'activity icon url'),
                                    'modname' => new external_value(PARAM_PLUGIN, 'activity module type'),
                                    'modplural' => new external_value(PARAM_TEXT, 'activity module plural name'),
                                    'availability' => new external_value(PARAM_RAW, 'module availability settings', VALUE_OPTIONAL),
                                    'indent' => new external_value(PARAM_INT, 'number of identation in the site'),
                                    'onclick' => new external_value(PARAM_RAW, 'Onclick action.', VALUE_OPTIONAL),
                                    'afterlink' => new external_value(PARAM_RAW, 'After link info to be displayed.',
                                        VALUE_OPTIONAL),
                                    'customdata' => new external_value(PARAM_RAW, 'Custom data (JSON encoded).', VALUE_OPTIONAL),
                                    'noviewlink' => new external_value(PARAM_BOOL, 'Whether the module has no view page',
                                        VALUE_OPTIONAL),
                                    'completion' => new external_value(PARAM_INT, 'Type of completion tracking:
                                        0 means none, 1 manual, 2 automatic.', VALUE_OPTIONAL),
                                    'completiondata' => new external_single_structure(
                                        array(
                                            'state' => new external_value(PARAM_INT, 'Completion state value:
                                                0 means incomplete, 1 complete, 2 complete pass, 3 complete fail'),
                                            'timecompleted' => new external_value(PARAM_INT, 'Timestamp for completion status.'),
                                            'overrideby' => new external_value(PARAM_INT, 'The user id who has overriden the
                                                status.'),
                                            'valueused' => new external_value(PARAM_BOOL, 'Whether the completion status affects
                                                the availability of another activity.', VALUE_OPTIONAL),
                                        ), 'Module completion data.', VALUE_OPTIONAL
                                    ),
                                    'contents' => new external_multiple_structure(
                                          new external_single_structure(
                                              array(
                                                  // content info
                                                  'type'=> new external_value(PARAM_TEXT, 'a file or a folder or external link'),
                                                  'filename'=> new external_value(PARAM_FILE, 'filename'),
                                                  'filepath'=> new external_value(PARAM_PATH, 'filepath'),
                                                  'filesize'=> new external_value(PARAM_INT, 'filesize'),
                                                  'fileurl' => new external_value(PARAM_URL, 'downloadable file url', VALUE_OPTIONAL),
                                                  'content' => new external_value(PARAM_RAW, 'Raw content, will be used when type is content', VALUE_OPTIONAL),
                                                  'timecreated' => new external_value(PARAM_INT, 'Time created'),
                                                  'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                                                  'sortorder' => new external_value(PARAM_INT, 'Content sort order'),
                                                  'mimetype' => new external_value(PARAM_RAW, 'File mime type.', VALUE_OPTIONAL),
                                                  'isexternalfile' => new external_value(PARAM_BOOL, 'Whether is an external file.',
                                                    VALUE_OPTIONAL),
                                                  'repositorytype' => new external_value(PARAM_PLUGIN, 'The repository type for external files.',
                                                    VALUE_OPTIONAL),

                                                  // copyright related info
                                                  'userid' => new external_value(PARAM_INT, 'User who added this content to moodle'),
                                                  'author' => new external_value(PARAM_TEXT, 'Content owner'),
                                                  'license' => new external_value(PARAM_TEXT, 'Content license'),
                                                  'tags' => new external_multiple_structure(
                                                       \core_tag\external\tag_item_exporter::get_read_structure(), 'Tags',
                                                            VALUE_OPTIONAL
                                                   ),
                                              )
                                          ), VALUE_DEFAULT, array()
                                      ),
                                    'contentsinfo' => new external_single_structure(
                                        array(
                                            'filescount' => new external_value(PARAM_INT, 'Total number of files.'),
                                            'filessize' => new external_value(PARAM_INT, 'Total files size.'),
                                            'lastmodified' => new external_value(PARAM_INT, 'Last time files were modified.'),
                                            'mimetypes' => new external_multiple_structure(
                                                new external_value(PARAM_RAW, 'File mime type.'),
                                                'Files mime types.'
                                            ),
                                            'repositorytype' => new external_value(PARAM_PLUGIN, 'The repository type for
                                                the main file.', VALUE_OPTIONAL),
                                        ), 'Contents summary information.', VALUE_OPTIONAL
                                    ),
                                )
                            ), 'list of module'
                    )
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function get_courses_parameters() {
        return new external_function_parameters(
                array('options' => new external_single_structure(
                            array('ids' => new external_multiple_structure(
                                        new external_value(PARAM_INT, 'Course id')
                                        , 'List of course id. If empty return all courses
                                            except front page course.',
                                        VALUE_OPTIONAL)
                            ), 'options - operator OR is used', VALUE_DEFAULT, array())
                )
        );
    }

    /**
     * Get courses
     *
     * @param array $options It contains an array (list of ids)
     * @return array
     * @since Moodle 2.2
     */
    public static function get_courses($options = array()) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        //validate parameter
        $params = self::validate_parameters(self::get_courses_parameters(),
                        array('options' => $options));

        //retrieve courses
        if (!array_key_exists('ids', $params['options'])
                or empty($params['options']['ids'])) {
            $courses = $DB->get_records('course');
        } else {
            $courses = $DB->get_records_list('course', 'id', $params['options']['ids']);
        }

        //create return value
        $coursesinfo = array();
        foreach ($courses as $course) {

            // now security checks
            $context = context_course::instance($course->id, IGNORE_MISSING);
            $courseformatoptions = course_get_format($course)->get_format_options();
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $exceptionparam = new stdClass();
                $exceptionparam->message = $e->getMessage();
                $exceptionparam->courseid = $course->id;
                throw new moodle_exception('errorcoursecontextnotvalid', 'webservice', '', $exceptionparam);
            }
            if ($course->id != SITEID) {
                require_capability('moodle/course:view', $context);
            }

            $courseinfo = array();
            $courseinfo['id'] = $course->id;
            $courseinfo['fullname'] = external_format_string($course->fullname, $context->id);
            $courseinfo['shortname'] = external_format_string($course->shortname, $context->id);
            $courseinfo['displayname'] = external_format_string(get_course_display_name_for_list($course), $context->id);
            $courseinfo['categoryid'] = $course->category;
            list($courseinfo['summary'], $courseinfo['summaryformat']) =
                external_format_text($course->summary, $course->summaryformat, $context->id, 'course', 'summary', 0);
            $courseinfo['format'] = $course->format;
            $courseinfo['startdate'] = $course->startdate;
            $courseinfo['enddate'] = $course->enddate;
            if (array_key_exists('numsections', $courseformatoptions)) {
                // For backward-compartibility
                $courseinfo['numsections'] = $courseformatoptions['numsections'];
            }

            $handler = core_course\customfield\course_handler::create();
            if ($customfields = $handler->export_instance_data($course->id)) {
                $courseinfo['customfields'] = [];
                foreach ($customfields as $data) {
                    $courseinfo['customfields'][] = [
                        'type' => $data->get_type(),
                        'value' => $data->get_value(),
                        'valueraw' => $data->get_data_controller()->get_value(),
                        'name' => $data->get_name(),
                        'shortname' => $data->get_shortname()
                    ];
                }
            }

            //some field should be returned only if the user has update permission
            $courseadmin = has_capability('moodle/course:update', $context);
            if ($courseadmin) {
                $courseinfo['categorysortorder'] = $course->sortorder;
                $courseinfo['idnumber'] = $course->idnumber;
                $courseinfo['showgrades'] = $course->showgrades;
                $courseinfo['showreports'] = $course->showreports;
                $courseinfo['newsitems'] = $course->newsitems;
                $courseinfo['visible'] = $course->visible;
                $courseinfo['maxbytes'] = $course->maxbytes;
                if (array_key_exists('hiddensections', $courseformatoptions)) {
                    // For backward-compartibility
                    $courseinfo['hiddensections'] = $courseformatoptions['hiddensections'];
                }
                // Return numsections for backward-compatibility with clients who expect it.
                $courseinfo['numsections'] = course_get_format($course)->get_last_section_number();
                $courseinfo['groupmode'] = $course->groupmode;
                $courseinfo['groupmodeforce'] = $course->groupmodeforce;
                $courseinfo['defaultgroupingid'] = $course->defaultgroupingid;
                $courseinfo['lang'] = clean_param($course->lang, PARAM_LANG);
                $courseinfo['timecreated'] = $course->timecreated;
                $courseinfo['timemodified'] = $course->timemodified;
                $courseinfo['forcetheme'] = clean_param($course->theme, PARAM_THEME);
                $courseinfo['enablecompletion'] = $course->enablecompletion;
                $courseinfo['completionnotify'] = $course->completionnotify;
                $courseinfo['courseformatoptions'] = array();
                foreach ($courseformatoptions as $key => $value) {
                    $courseinfo['courseformatoptions'][] = array(
                        'name' => $key,
                        'value' => $value
                    );
                }
            }

            if ($courseadmin or $course->visible
                    or has_capability('moodle/course:viewhiddencourses', $context)) {
                $coursesinfo[] = $courseinfo;
            }
        }

        return $coursesinfo;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function get_courses_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'course id'),
                            'shortname' => new external_value(PARAM_RAW, 'course short name'),
                            'categoryid' => new external_value(PARAM_INT, 'category id'),
                            'categorysortorder' => new external_value(PARAM_INT,
                                    'sort order into the category', VALUE_OPTIONAL),
                            'fullname' => new external_value(PARAM_RAW, 'full name'),
                            'displayname' => new external_value(PARAM_RAW, 'course display name'),
                            'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                            'summary' => new external_value(PARAM_RAW, 'summary'),
                            'summaryformat' => new external_format_value('summary'),
                            'format' => new external_value(PARAM_PLUGIN,
                                    'course format: weeks, topics, social, site,..'),
                            'showgrades' => new external_value(PARAM_INT,
                                    '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                            'newsitems' => new external_value(PARAM_INT,
                                    'number of recent items appearing on the course page', VALUE_OPTIONAL),
                            'startdate' => new external_value(PARAM_INT,
                                    'timestamp when the course start'),
                            'enddate' => new external_value(PARAM_INT,
                                    'timestamp when the course end'),
                            'numsections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) number of weeks/topics',
                                    VALUE_OPTIONAL),
                            'maxbytes' => new external_value(PARAM_INT,
                                    'largest size of file that can be uploaded into the course',
                                    VALUE_OPTIONAL),
                            'showreports' => new external_value(PARAM_INT,
                                    'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT,
                                    '1: available to student, 0:not available', VALUE_OPTIONAL),
                            'hiddensections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students',
                                    VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible',
                                    VALUE_OPTIONAL),
                            'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no',
                                    VALUE_OPTIONAL),
                            'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id',
                                    VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT,
                                    'timestamp when the course have been created', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT,
                                    'timestamp when the course have been modified', VALUE_OPTIONAL),
                            'enablecompletion' => new external_value(PARAM_INT,
                                    'Enabled, control via completion and activity settings. Disbaled,
                                        not shown in activity settings.',
                                    VALUE_OPTIONAL),
                            'completionnotify' => new external_value(PARAM_INT,
                                    '1: yes 0: no', VALUE_OPTIONAL),
                            'lang' => new external_value(PARAM_SAFEDIR,
                                    'forced course language', VALUE_OPTIONAL),
                            'forcetheme' => new external_value(PARAM_PLUGIN,
                                    'name of the force theme', VALUE_OPTIONAL),
                            'courseformatoptions' => new external_multiple_structure(
                                new external_single_structure(
                                    array('name' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                                        'value' => new external_value(PARAM_RAW, 'course format option value')
                                )), 'additional options for particular course format', VALUE_OPTIONAL
                             ),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    ['name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                     'shortname' => new external_value(PARAM_ALPHANUMEXT, 'The shortname of the custom field'),
                                     'type'  => new external_value(PARAM_COMPONENT,
                                         'The type of the custom field - text, checkbox...'),
                                     'valueraw' => new external_value(PARAM_RAW, 'The raw value of the custom field'),
                                     'value' => new external_value(PARAM_RAW, 'The value of the custom field')]
                                ), 'Custom fields and associated values', VALUE_OPTIONAL),
                        ), 'course'
                )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function create_courses_parameters() {
        $courseconfig = get_config('moodlecourse'); //needed for many default values
        return new external_function_parameters(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'fullname' => new external_value(PARAM_TEXT, 'full name'),
                            'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                            'categoryid' => new external_value(PARAM_INT, 'category id'),
                            'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                            'summary' => new external_value(PARAM_RAW, 'summary', VALUE_OPTIONAL),
                            'summaryformat' => new external_format_value('summary', VALUE_DEFAULT),
                            'format' => new external_value(PARAM_PLUGIN,
                                    'course format: weeks, topics, social, site,..',
                                    VALUE_DEFAULT, $courseconfig->format),
                            'showgrades' => new external_value(PARAM_INT,
                                    '1 if grades are shown, otherwise 0', VALUE_DEFAULT,
                                    $courseconfig->showgrades),
                            'newsitems' => new external_value(PARAM_INT,
                                    'number of recent items appearing on the course page',
                                    VALUE_DEFAULT, $courseconfig->newsitems),
                            'startdate' => new external_value(PARAM_INT,
                                    'timestamp when the course start', VALUE_OPTIONAL),
                            'enddate' => new external_value(PARAM_INT,
                                    'timestamp when the course end', VALUE_OPTIONAL),
                            'numsections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) number of weeks/topics',
                                    VALUE_OPTIONAL),
                            'maxbytes' => new external_value(PARAM_INT,
                                    'largest size of file that can be uploaded into the course',
                                    VALUE_DEFAULT, $courseconfig->maxbytes),
                            'showreports' => new external_value(PARAM_INT,
                                    'are activity report shown (yes = 1, no =0)', VALUE_DEFAULT,
                                    $courseconfig->showreports),
                            'visible' => new external_value(PARAM_INT,
                                    '1: available to student, 0:not available', VALUE_OPTIONAL),
                            'hiddensections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students',
                                    VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible',
                                    VALUE_DEFAULT, $courseconfig->groupmode),
                            'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no',
                                    VALUE_DEFAULT, $courseconfig->groupmodeforce),
                            'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id',
                                    VALUE_DEFAULT, 0),
                            'enablecompletion' => new external_value(PARAM_INT,
                                    'Enabled, control via completion and activity settings. Disabled,
                                        not shown in activity settings.',
                                    VALUE_OPTIONAL),
                            'completionnotify' => new external_value(PARAM_INT,
                                    '1: yes 0: no', VALUE_OPTIONAL),
                            'lang' => new external_value(PARAM_SAFEDIR,
                                    'forced course language', VALUE_OPTIONAL),
                            'forcetheme' => new external_value(PARAM_PLUGIN,
                                    'name of the force theme', VALUE_OPTIONAL),
                            'courseformatoptions' => new external_multiple_structure(
                                new external_single_structure(
                                    array('name' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                                        'value' => new external_value(PARAM_RAW, 'course format option value')
                                )),
                                    'additional options for particular course format', VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'shortname'  => new external_value(PARAM_ALPHANUMEXT, 'The shortname of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                )), 'custom fields for the course', VALUE_OPTIONAL
                            )
                    )), 'courses to create'
                )
            )
        );
    }

    /**
     * Create  courses
     *
     * @param array $courses
     * @return array courses (id and shortname only)
     * @since Moodle 2.2
     */
    public static function create_courses($courses) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        require_once($CFG->libdir . '/completionlib.php');

        $params = self::validate_parameters(self::create_courses_parameters(),
                        array('courses' => $courses));

        $availablethemes = core_component::get_plugin_list('theme');
        $availablelangs = get_string_manager()->get_list_of_translations();

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['courses'] as $course) {

            // Ensure the current user is allowed to run this function
            $context = context_coursecat::instance($course['categoryid'], IGNORE_MISSING);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $exceptionparam = new stdClass();
                $exceptionparam->message = $e->getMessage();
                $exceptionparam->catid = $course['categoryid'];
                throw new moodle_exception('errorcatcontextnotvalid', 'webservice', '', $exceptionparam);
            }
            require_capability('moodle/course:create', $context);

            // Fullname and short name are required to be non-empty.
            if (trim($course['fullname']) === '') {
                throw new moodle_exception('errorinvalidparam', 'webservice', '', 'fullname');
            } else if (trim($course['shortname']) === '') {
                throw new moodle_exception('errorinvalidparam', 'webservice', '', 'shortname');
            }

            // Make sure lang is valid
            if (array_key_exists('lang', $course)) {
                if (empty($availablelangs[$course['lang']])) {
                    throw new moodle_exception('errorinvalidparam', 'webservice', '', 'lang');
                }
                if (!has_capability('moodle/course:setforcedlanguage', $context)) {
                    unset($course['lang']);
                }
            }

            // Make sure theme is valid
            if (array_key_exists('forcetheme', $course)) {
                if (!empty($CFG->allowcoursethemes)) {
                    if (empty($availablethemes[$course['forcetheme']])) {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', 'forcetheme');
                    } else {
                        $course['theme'] = $course['forcetheme'];
                    }
                }
            }

            //force visibility if ws user doesn't have the permission to set it
            $category = $DB->get_record('course_categories', array('id' => $course['categoryid']));
            if (!has_capability('moodle/course:visibility', $context)) {
                $course['visible'] = $category->visible;
            }

            //set default value for completion
            $courseconfig = get_config('moodlecourse');
            if (completion_info::is_enabled_for_site()) {
                if (!array_key_exists('enablecompletion', $course)) {
                    $course['enablecompletion'] = $courseconfig->enablecompletion;
                }
            } else {
                $course['enablecompletion'] = 0;
            }

            $course['category'] = $course['categoryid'];

            // Summary format.
            $course['summaryformat'] = external_validate_format($course['summaryformat']);

            if (!empty($course['courseformatoptions'])) {
                foreach ($course['courseformatoptions'] as $option) {
                    $course[$option['name']] = $option['value'];
                }
            }

            // Custom fields.
            if (!empty($course['customfields'])) {
                foreach ($course['customfields'] as $field) {
                    $course['customfield_'.$field['shortname']] = $field['value'];
                }
            }

            //Note: create_course() core function check shortname, idnumber, category
            $course['id'] = create_course((object) $course)->id;

            $resultcourses[] = array('id' => $course['id'], 'shortname' => $course['shortname']);
        }

        $transaction->allow_commit();

        return $resultcourses;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function create_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'       => new external_value(PARAM_INT, 'course id'),
                    'shortname' => new external_value(PARAM_RAW, 'short name'),
                )
            )
        );
    }

    /**
     * Update courses
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function update_courses_parameters() {
        return new external_function_parameters(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'ID of the course'),
                            'fullname' => new external_value(PARAM_TEXT, 'full name', VALUE_OPTIONAL),
                            'shortname' => new external_value(PARAM_TEXT, 'course short name', VALUE_OPTIONAL),
                            'categoryid' => new external_value(PARAM_INT, 'category id', VALUE_OPTIONAL),
                            'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                            'summary' => new external_value(PARAM_RAW, 'summary', VALUE_OPTIONAL),
                            'summaryformat' => new external_format_value('summary', VALUE_OPTIONAL),
                            'format' => new external_value(PARAM_PLUGIN,
                                    'course format: weeks, topics, social, site,..', VALUE_OPTIONAL),
                            'showgrades' => new external_value(PARAM_INT,
                                    '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                            'newsitems' => new external_value(PARAM_INT,
                                    'number of recent items appearing on the course page', VALUE_OPTIONAL),
                            'startdate' => new external_value(PARAM_INT,
                                    'timestamp when the course start', VALUE_OPTIONAL),
                            'enddate' => new external_value(PARAM_INT,
                                    'timestamp when the course end', VALUE_OPTIONAL),
                            'numsections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) number of weeks/topics', VALUE_OPTIONAL),
                            'maxbytes' => new external_value(PARAM_INT,
                                    'largest size of file that can be uploaded into the course', VALUE_OPTIONAL),
                            'showreports' => new external_value(PARAM_INT,
                                    'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT,
                                    '1: available to student, 0:not available', VALUE_OPTIONAL),
                            'hiddensections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) How the hidden sections in the course are
                                        displayed to students', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible', VALUE_OPTIONAL),
                            'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no', VALUE_OPTIONAL),
                            'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id', VALUE_OPTIONAL),
                            'enablecompletion' => new external_value(PARAM_INT,
                                    'Enabled, control via completion and activity settings. Disabled,
                                        not shown in activity settings.', VALUE_OPTIONAL),
                            'completionnotify' => new external_value(PARAM_INT, '1: yes 0: no', VALUE_OPTIONAL),
                            'lang' => new external_value(PARAM_SAFEDIR, 'forced course language', VALUE_OPTIONAL),
                            'forcetheme' => new external_value(PARAM_PLUGIN, 'name of the force theme', VALUE_OPTIONAL),
                            'courseformatoptions' => new external_multiple_structure(
                                new external_single_structure(
                                    array('name' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                                        'value' => new external_value(PARAM_RAW, 'course format option value')
                                )), 'additional options for particular course format', VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'shortname'  => new external_value(PARAM_ALPHANUMEXT, 'The shortname of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    ]
                                ), 'Custom fields', VALUE_OPTIONAL),
                        )
                    ), 'courses to update'
                )
            )
        );
    }

    /**
     * Update courses
     *
     * @param array $courses
     * @since Moodle 2.5
     */
    public static function update_courses($courses) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        $warnings = array();

        $params = self::validate_parameters(self::update_courses_parameters(),
                        array('courses' => $courses));

        $availablethemes = core_component::get_plugin_list('theme');
        $availablelangs = get_string_manager()->get_list_of_translations();

        foreach ($params['courses'] as $course) {
            // Catch any exception while updating course and return as warning to user.
            try {
                // Ensure the current user is allowed to run this function.
                $context = context_course::instance($course['id'], MUST_EXIST);
                self::validate_context($context);

                $oldcourse = course_get_format($course['id'])->get_course();

                require_capability('moodle/course:update', $context);

                // Check if user can change category.
                if (array_key_exists('categoryid', $course) && ($oldcourse->category != $course['categoryid'])) {
                    require_capability('moodle/course:changecategory', $context);
                    $course['category'] = $course['categoryid'];
                }

                // Check if the user can change fullname, and the new value is non-empty.
                if (array_key_exists('fullname', $course) && ($oldcourse->fullname != $course['fullname'])) {
                    require_capability('moodle/course:changefullname', $context);
                    if (trim($course['fullname']) === '') {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', 'fullname');
                    }
                }

                // Check if the user can change shortname, and the new value is non-empty.
                if (array_key_exists('shortname', $course) && ($oldcourse->shortname != $course['shortname'])) {
                    require_capability('moodle/course:changeshortname', $context);
                    if (trim($course['shortname']) === '') {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', 'shortname');
                    }
                }

                // Check if the user can change the idnumber.
                if (array_key_exists('idnumber', $course) && ($oldcourse->idnumber != $course['idnumber'])) {
                    require_capability('moodle/course:changeidnumber', $context);
                }

                // Check if user can change summary.
                if (array_key_exists('summary', $course) && ($oldcourse->summary != $course['summary'])) {
                    require_capability('moodle/course:changesummary', $context);
                }

                // Summary format.
                if (array_key_exists('summaryformat', $course) && ($oldcourse->summaryformat != $course['summaryformat'])) {
                    require_capability('moodle/course:changesummary', $context);
                    $course['summaryformat'] = external_validate_format($course['summaryformat']);
                }

                // Check if user can change visibility.
                if (array_key_exists('visible', $course) && ($oldcourse->visible != $course['visible'])) {
                    require_capability('moodle/course:visibility', $context);
                }

                // Make sure lang is valid.
                if (array_key_exists('lang', $course) && ($oldcourse->lang != $course['lang'])) {
                    require_capability('moodle/course:setforcedlanguage', $context);
                    if (empty($availablelangs[$course['lang']])) {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', 'lang');
                    }
                }

                // Make sure theme is valid.
                if (array_key_exists('forcetheme', $course)) {
                    if (!empty($CFG->allowcoursethemes)) {
                        if (empty($availablethemes[$course['forcetheme']])) {
                            throw new moodle_exception('errorinvalidparam', 'webservice', '', 'forcetheme');
                        } else {
                            $course['theme'] = $course['forcetheme'];
                        }
                    }
                }

                // Make sure completion is enabled before setting it.
                if (array_key_exists('enabledcompletion', $course) && !completion_info::is_enabled_for_site()) {
                    $course['enabledcompletion'] = 0;
                }

                // Make sure maxbytes are less then CFG->maxbytes.
                if (array_key_exists('maxbytes', $course)) {
                    // We allow updates back to 0 max bytes, a special value denoting the course uses the site limit.
                    // Otherwise, either use the size specified, or cap at the max size for the course.
                    if ($course['maxbytes'] != 0) {
                        $course['maxbytes'] = get_max_upload_file_size($CFG->maxbytes, $course['maxbytes']);
                    }
                }

                if (!empty($course['courseformatoptions'])) {
                    foreach ($course['courseformatoptions'] as $option) {
                        if (isset($option['name']) && isset($option['value'])) {
                            $course[$option['name']] = $option['value'];
                        }
                    }
                }

                // Prepare list of custom fields.
                if (isset($course['customfields'])) {
                    foreach ($course['customfields'] as $field) {
                        $course['customfield_' . $field['shortname']] = $field['value'];
                    }
                }

                // Update course if user has all required capabilities.
                update_course((object) $course);
            } catch (Exception $e) {
                $warning = array();
                $warning['item'] = 'course';
                $warning['itemid'] = $course['id'];
                if ($e instanceof moodle_exception) {
                    $warning['warningcode'] = $e->errorcode;
                } else {
                    $warning['warningcode'] = $e->getCode();
                }
                $warning['message'] = $e->getMessage();
                $warnings[] = $warning;
            }
        }

        $result = array();
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function update_courses_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function delete_courses_parameters() {
        return new external_function_parameters(
            array(
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'course ID')),
            )
        );
    }

    /**
     * Delete courses
     *
     * @param array $courseids A list of course ids
     * @since Moodle 2.2
     */
    public static function delete_courses($courseids) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/course/lib.php");

        // Parameter validation.
        $params = self::validate_parameters(self::delete_courses_parameters(), array('courseids'=>$courseids));

        $warnings = array();

        foreach ($params['courseids'] as $courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));

            if ($course === false) {
                $warnings[] = array(
                                'item' => 'course',
                                'itemid' => $courseid,
                                'warningcode' => 'unknowncourseidnumber',
                                'message' => 'Unknown course ID ' . $courseid
                            );
                continue;
            }

            // Check if the context is valid.
            $coursecontext = context_course::instance($course->id);
            self::validate_context($coursecontext);

            // Check if the current user has permission.
            if (!can_delete_course($courseid)) {
                $warnings[] = array(
                                'item' => 'course',
                                'itemid' => $courseid,
                                'warningcode' => 'cannotdeletecourse',
                                'message' => 'You do not have the permission to delete this course' . $courseid
                            );
                continue;
            }

            if (delete_course($course, false) === false) {
                $warnings[] = array(
                                'item' => 'course',
                                'itemid' => $courseid,
                                'warningcode' => 'cannotdeletecategorycourse',
                                'message' => 'Course ' . $courseid . ' failed to be deleted'
                            );
                continue;
            }
        }

        fix_course_sortorder();

        return array('warnings' => $warnings);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function delete_courses_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function duplicate_course_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course to duplicate id'),
                'fullname' => new external_value(PARAM_TEXT, 'duplicated course full name'),
                'shortname' => new external_value(PARAM_TEXT, 'duplicated course short name'),
                'categoryid' => new external_value(PARAM_INT, 'duplicated course category parent'),
                'visible' => new external_value(PARAM_INT, 'duplicated course visible, default to yes', VALUE_DEFAULT, 1),
                'options' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                                'name' => new external_value(PARAM_ALPHAEXT, 'The backup option name:
                                            "activities" (int) Include course activites (default to 1 that is equal to yes),
                                            "blocks" (int) Include course blocks (default to 1 that is equal to yes),
                                            "filters" (int) Include course filters  (default to 1 that is equal to yes),
                                            "users" (int) Include users (default to 0 that is equal to no),
                                            "enrolments" (int) Include enrolment methods (default to 1 - restore only with users),
                                            "role_assignments" (int) Include role assignments  (default to 0 that is equal to no),
                                            "comments" (int) Include user comments  (default to 0 that is equal to no),
                                            "userscompletion" (int) Include user course completion information  (default to 0 that is equal to no),
                                            "logs" (int) Include course logs  (default to 0 that is equal to no),
                                            "grade_histories" (int) Include histories  (default to 0 that is equal to no)'
                                            ),
                                'value' => new external_value(PARAM_RAW, 'the value for the option 1 (yes) or 0 (no)'
                            )
                        )
                    ), VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Duplicate a course
     *
     * @param int $courseid
     * @param string $fullname Duplicated course fullname
     * @param string $shortname Duplicated course shortname
     * @param int $categoryid Duplicated course parent category id
     * @param int $visible Duplicated course availability
     * @param array $options List of backup options
     * @return array New course info
     * @since Moodle 2.3
     */
    public static function duplicate_course($courseid, $fullname, $shortname, $categoryid, $visible = 1, $options = array()) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        // Parameter validation.
        $params = self::validate_parameters(
                self::duplicate_course_parameters(),
                array(
                      'courseid' => $courseid,
                      'fullname' => $fullname,
                      'shortname' => $shortname,
                      'categoryid' => $categoryid,
                      'visible' => $visible,
                      'options' => $options
                )
        );

        // Context validation.

        if (! ($course = $DB->get_record('course', array('id'=>$params['courseid'])))) {
            throw new moodle_exception('invalidcourseid', 'error');
        }

        // Category where duplicated course is going to be created.
        $categorycontext = context_coursecat::instance($params['categoryid']);
        self::validate_context($categorycontext);

        // Course to be duplicated.
        $coursecontext = context_course::instance($course->id);
        self::validate_context($coursecontext);

        $backupdefaults = array(
            'activities' => 1,
            'blocks' => 1,
            'filters' => 1,
            'users' => 0,
            'enrolments' => backup::ENROL_WITHUSERS,
            'role_assignments' => 0,
            'comments' => 0,
            'userscompletion' => 0,
            'logs' => 0,
            'grade_histories' => 0
        );

        $backupsettings = array();
        // Check for backup and restore options.
        if (!empty($params['options'])) {
            foreach ($params['options'] as $option) {

                // Strict check for a correct value (allways 1 or 0, true or false).
                $value = clean_param($option['value'], PARAM_INT);

                if ($value !== 0 and $value !== 1) {
                    throw new moodle_exception('invalidextparam', 'webservice', '', $option['name']);
                }

                if (!isset($backupdefaults[$option['name']])) {
                    throw new moodle_exception('invalidextparam', 'webservice', '', $option['name']);
                }

                $backupsettings[$option['name']] = $value;
            }
        }

        // Capability checking.

        // The backup controller check for this currently, this may be redundant.
        require_capability('moodle/course:create', $categorycontext);
        require_capability('moodle/restore:restorecourse', $categorycontext);
        require_capability('moodle/backup:backupcourse', $coursecontext);

        if (!empty($backupsettings['users'])) {
            require_capability('moodle/backup:userinfo', $coursecontext);
            require_capability('moodle/restore:userinfo', $categorycontext);
        }

        // Check if the shortname is used.
        if ($foundcourses = $DB->get_records('course', array('shortname'=>$shortname))) {
            foreach ($foundcourses as $foundcourse) {
                $foundcoursenames[] = $foundcourse->fullname;
            }

            $foundcoursenamestring = implode(',', $foundcoursenames);
            throw new moodle_exception('shortnametaken', '', '', $foundcoursenamestring);
        }

        // Backup the course.

        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
        backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $USER->id);

        foreach ($backupsettings as $name => $value) {
            if ($setting = $bc->get_plan()->get_setting($name)) {
                $bc->get_plan()->get_setting($name)->set_value($value);
            }
        }

        $backupid       = $bc->get_backupid();
        $backupbasepath = $bc->get_plan()->get_basepath();

        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];

        $bc->destroy();

        // Restore the backup immediately.

        // Check if we need to unzip the file because the backup temp dir does not contains backup files.
        if (!file_exists($backupbasepath . "/moodle_backup.xml")) {
            $file->extract_to_pathname(get_file_packer('application/vnd.moodle.backup'), $backupbasepath);
        }

        // Create new course.
        $newcourseid = restore_dbops::create_new_course($params['fullname'], $params['shortname'], $params['categoryid']);

        $rc = new restore_controller($backupid, $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $USER->id, backup::TARGET_NEW_COURSE);

        foreach ($backupsettings as $name => $value) {
            $setting = $rc->get_plan()->get_setting($name);
            if ($setting->get_status() == backup_setting::NOT_LOCKED) {
                $setting->set_value($value);
            }
        }

        if (!$rc->execute_precheck()) {
            $precheckresults = $rc->get_precheck_results();
            if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($backupbasepath);
                }

                $errorinfo = '';

                foreach ($precheckresults['errors'] as $error) {
                    $errorinfo .= $error;
                }

                if (array_key_exists('warnings', $precheckresults)) {
                    foreach ($precheckresults['warnings'] as $warning) {
                        $errorinfo .= $warning;
                    }
                }

                throw new moodle_exception('backupprecheckerrors', 'webservice', '', $errorinfo);
            }
        }

        $rc->execute_plan();
        $rc->destroy();

        $course = $DB->get_record('course', array('id' => $newcourseid), '*', MUST_EXIST);
        $course->fullname = $params['fullname'];
        $course->shortname = $params['shortname'];
        $course->visible = $params['visible'];

        // Set shortname and fullname back.
        $DB->update_record('course', $course);

        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backupbasepath);
        }

        // Delete the course backup file created by this WebService. Originally located in the course backups area.
        $file->delete();

        return array('id' => $course->id, 'shortname' => $course->shortname);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.3
     */
    public static function duplicate_course_returns() {
        return new external_single_structure(
            array(
                'id'       => new external_value(PARAM_INT, 'course id'),
                'shortname' => new external_value(PARAM_RAW, 'short name'),
            )
        );
    }

    /**
     * Returns description of method parameters for import_course
     *
     * @return external_function_parameters
     * @since Moodle 2.4
     */
    public static function import_course_parameters() {
        return new external_function_parameters(
            array(
                'importfrom' => new external_value(PARAM_INT, 'the id of the course we are importing from'),
                'importto' => new external_value(PARAM_INT, 'the id of the course we are importing to'),
                'deletecontent' => new external_value(PARAM_INT, 'whether to delete the course content where we are importing to (default to 0 = No)', VALUE_DEFAULT, 0),
                'options' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                                'name' => new external_value(PARAM_ALPHA, 'The backup option name:
                                            "activities" (int) Include course activites (default to 1 that is equal to yes),
                                            "blocks" (int) Include course blocks (default to 1 that is equal to yes),
                                            "filters" (int) Include course filters  (default to 1 that is equal to yes)'
                                            ),
                                'value' => new external_value(PARAM_RAW, 'the value for the option 1 (yes) or 0 (no)'
                            )
                        )
                    ), VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Imports a course
     *
     * @param int $importfrom The id of the course we are importing from
     * @param int $importto The id of the course we are importing to
     * @param bool $deletecontent Whether to delete the course we are importing to content
     * @param array $options List of backup options
     * @return null
     * @since Moodle 2.4
     */
    public static function import_course($importfrom, $importto, $deletecontent = 0, $options = array()) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        // Parameter validation.
        $params = self::validate_parameters(
            self::import_course_parameters(),
            array(
                'importfrom' => $importfrom,
                'importto' => $importto,
                'deletecontent' => $deletecontent,
                'options' => $options
            )
        );

        if ($params['deletecontent'] !== 0 and $params['deletecontent'] !== 1) {
            throw new moodle_exception('invalidextparam', 'webservice', '', $params['deletecontent']);
        }

        // Context validation.

        if (! ($importfrom = $DB->get_record('course', array('id'=>$params['importfrom'])))) {
            throw new moodle_exception('invalidcourseid', 'error');
        }

        if (! ($importto = $DB->get_record('course', array('id'=>$params['importto'])))) {
            throw new moodle_exception('invalidcourseid', 'error');
        }

        $importfromcontext = context_course::instance($importfrom->id);
        self::validate_context($importfromcontext);

        $importtocontext = context_course::instance($importto->id);
        self::validate_context($importtocontext);

        $backupdefaults = array(
            'activities' => 1,
            'blocks' => 1,
            'filters' => 1
        );

        $backupsettings = array();

        // Check for backup and restore options.
        if (!empty($params['options'])) {
            foreach ($params['options'] as $option) {

                // Strict check for a correct value (allways 1 or 0, true or false).
                $value = clean_param($option['value'], PARAM_INT);

                if ($value !== 0 and $value !== 1) {
                    throw new moodle_exception('invalidextparam', 'webservice', '', $option['name']);
                }

                if (!isset($backupdefaults[$option['name']])) {
                    throw new moodle_exception('invalidextparam', 'webservice', '', $option['name']);
                }

                $backupsettings[$option['name']] = $value;
            }
        }

        // Capability checking.

        require_capability('moodle/backup:backuptargetimport', $importfromcontext);
        require_capability('moodle/restore:restoretargetimport', $importtocontext);

        $bc = new backup_controller(backup::TYPE_1COURSE, $importfrom->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);

        foreach ($backupsettings as $name => $value) {
            $bc->get_plan()->get_setting($name)->set_value($value);
        }

        $backupid       = $bc->get_backupid();
        $backupbasepath = $bc->get_plan()->get_basepath();

        $bc->execute_plan();
        $bc->destroy();

        // Restore the backup immediately.

        // Check if we must delete the contents of the destination course.
        if ($params['deletecontent']) {
            $restoretarget = backup::TARGET_EXISTING_DELETING;
        } else {
            $restoretarget = backup::TARGET_EXISTING_ADDING;
        }

        $rc = new restore_controller($backupid, $importto->id,
                backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, $restoretarget);

        foreach ($backupsettings as $name => $value) {
            $rc->get_plan()->get_setting($name)->set_value($value);
        }

        if (!$rc->execute_precheck()) {
            $precheckresults = $rc->get_precheck_results();
            if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($backupbasepath);
                }

                $errorinfo = '';

                foreach ($precheckresults['errors'] as $error) {
                    $errorinfo .= $error;
                }

                if (array_key_exists('warnings', $precheckresults)) {
                    foreach ($precheckresults['warnings'] as $warning) {
                        $errorinfo .= $warning;
                    }
                }

                throw new moodle_exception('backupprecheckerrors', 'webservice', '', $errorinfo);
            }
        } else {
            if ($restoretarget == backup::TARGET_EXISTING_DELETING) {
                restore_dbops::delete_course_content($importto->id);
            }
        }

        $rc->execute_plan();
        $rc->destroy();

        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backupbasepath);
        }

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.4
     */
    public static function import_course_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function get_categories_parameters() {
        return new external_function_parameters(
            array(
                'criteria' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'key' => new external_value(PARAM_ALPHA,
                                         'The category column to search, expected keys (value format) are:'.
                                         '"id" (int) the category id,'.
                                         '"ids" (string) category ids separated by commas,'.
                                         '"name" (string) the category name,'.
                                         '"parent" (int) the parent category id,'.
                                         '"idnumber" (string) category idnumber'.
                                         ' - user must have \'moodle/category:manage\' to search on idnumber,'.
                                         '"visible" (int) whether the returned categories must be visible or hidden. If the key is not passed,
                                             then the function return all categories that the user can see.'.
                                         ' - user must have \'moodle/category:manage\' or \'moodle/category:viewhiddencategories\' to search on visible,'.
                                         '"theme" (string) only return the categories having this theme'.
                                         ' - user must have \'moodle/category:manage\' to search on theme'),
                            'value' => new external_value(PARAM_RAW, 'the value to match')
                        )
                    ), 'criteria', VALUE_DEFAULT, array()
                ),
                'addsubcategories' => new external_value(PARAM_BOOL, 'return the sub categories infos
                                          (1 - default) otherwise only the category info (0)', VALUE_DEFAULT, 1)
            )
        );
    }

    /**
     * Get categories
     *
     * @param array $criteria Criteria to match the results
     * @param booln $addsubcategories obtain only the category (false) or its subcategories (true - default)
     * @return array list of categories
     * @since Moodle 2.3
     */
    public static function get_categories($criteria = array(), $addsubcategories = true) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        // Validate parameters.
        $params = self::validate_parameters(self::get_categories_parameters(),
                array('criteria' => $criteria, 'addsubcategories' => $addsubcategories));

        // Retrieve the categories.
        $categories = array();
        if (!empty($params['criteria'])) {

            $conditions = array();
            $wheres = array();
            foreach ($params['criteria'] as $crit) {
                $key = trim($crit['key']);

                // Trying to avoid duplicate keys.
                if (!isset($conditions[$key])) {

                    $context = context_system::instance();
                    $value = null;
                    switch ($key) {
                        case 'id':
                            $value = clean_param($crit['value'], PARAM_INT);
                            $conditions[$key] = $value;
                            $wheres[] = $key . " = :" . $key;
                            break;

                        case 'ids':
                            $value = clean_param($crit['value'], PARAM_SEQUENCE);
                            $ids = explode(',', $value);
                            list($sqlids, $paramids) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
                            $conditions = array_merge($conditions, $paramids);
                            $wheres[] = 'id ' . $sqlids;
                            break;

                        case 'idnumber':
                            if (has_capability('moodle/category:manage', $context)) {
                                $value = clean_param($crit['value'], PARAM_RAW);
                                $conditions[$key] = $value;
                                $wheres[] = $key . " = :" . $key;
                            } else {
                                // We must throw an exception.
                                // Otherwise the dev client would think no idnumber exists.
                                throw new moodle_exception('criteriaerror',
                                        'webservice', '', null,
                                        'You don\'t have the permissions to search on the "idnumber" field.');
                            }
                            break;

                        case 'name':
                            $value = clean_param($crit['value'], PARAM_TEXT);
                            $conditions[$key] = $value;
                            $wheres[] = $key . " = :" . $key;
                            break;

                        case 'parent':
                            $value = clean_param($crit['value'], PARAM_INT);
                            $conditions[$key] = $value;
                            $wheres[] = $key . " = :" . $key;
                            break;

                        case 'visible':
                            if (has_capability('moodle/category:viewhiddencategories', $context)) {
                                $value = clean_param($crit['value'], PARAM_INT);
                                $conditions[$key] = $value;
                                $wheres[] = $key . " = :" . $key;
                            } else {
                                throw new moodle_exception('criteriaerror',
                                        'webservice', '', null,
                                        'You don\'t have the permissions to search on the "visible" field.');
                            }
                            break;

                        case 'theme':
                            if (has_capability('moodle/category:manage', $context)) {
                                $value = clean_param($crit['value'], PARAM_THEME);
                                $conditions[$key] = $value;
                                $wheres[] = $key . " = :" . $key;
                            } else {
                                throw new moodle_exception('criteriaerror',
                                        'webservice', '', null,
                                        'You don\'t have the permissions to search on the "theme" field.');
                            }
                            break;

                        default:
                            throw new moodle_exception('criteriaerror',
                                    'webservice', '', null,
                                    'You can not search on this criteria: ' . $key);
                    }
                }
            }

            if (!empty($wheres)) {
                $wheres = implode(" AND ", $wheres);

                $categories = $DB->get_records_select('course_categories', $wheres, $conditions);

                // Retrieve its sub subcategories (all levels).
                if ($categories and !empty($params['addsubcategories'])) {
                    $newcategories = array();

                    // Check if we required visible/theme checks.
                    $additionalselect = '';
                    $additionalparams = array();
                    if (isset($conditions['visible'])) {
                        $additionalselect .= ' AND visible = :visible';
                        $additionalparams['visible'] = $conditions['visible'];
                    }
                    if (isset($conditions['theme'])) {
                        $additionalselect .= ' AND theme= :theme';
                        $additionalparams['theme'] = $conditions['theme'];
                    }

                    foreach ($categories as $category) {
                        $sqlselect = $DB->sql_like('path', ':path') . $additionalselect;
                        $sqlparams = array('path' => $category->path.'/%') + $additionalparams; // It will NOT include the specified category.
                        $subcategories = $DB->get_records_select('course_categories', $sqlselect, $sqlparams);
                        $newcategories = $newcategories + $subcategories;   // Both arrays have integer as keys.
                    }
                    $categories = $categories + $newcategories;
                }
            }

        } else {
            // Retrieve all categories in the database.
            $categories = $DB->get_records('course_categories');
        }

        // The not returned categories. key => category id, value => reason of exclusion.
        $excludedcats = array();

        // The returned categories.
        $categoriesinfo = array();

        // We need to sort the categories by path.
        // The parent cats need to be checked by the algo first.
        usort($categories, "core_course_external::compare_categories_by_path");

        foreach ($categories as $category) {

            // Check if the category is a child of an excluded category, if yes exclude it too (excluded => do not return).
            $parents = explode('/', $category->path);
            unset($parents[0]); // First key is always empty because path start with / => /1/2/4.
            foreach ($parents as $parentid) {
                // Note: when the parent exclusion was due to the context,
                // the sub category could still be returned.
                if (isset($excludedcats[$parentid]) and $excludedcats[$parentid] != 'context') {
                    $excludedcats[$category->id] = 'parent';
                }
            }

            // Check the user can use the category context.
            $context = context_coursecat::instance($category->id);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $excludedcats[$category->id] = 'context';

                // If it was the requested category then throw an exception.
                if (isset($params['categoryid']) && $category->id == $params['categoryid']) {
                    $exceptionparam = new stdClass();
                    $exceptionparam->message = $e->getMessage();
                    $exceptionparam->catid = $category->id;
                    throw new moodle_exception('errorcatcontextnotvalid', 'webservice', '', $exceptionparam);
                }
            }

            // Return the category information.
            if (!isset($excludedcats[$category->id])) {

                // Final check to see if the category is visible to the user.
                if (core_course_category::can_view_category($category)) {

                    $categoryinfo = array();
                    $categoryinfo['id'] = $category->id;
                    $categoryinfo['name'] = external_format_string($category->name, $context);
                    list($categoryinfo['description'], $categoryinfo['descriptionformat']) =
                        external_format_text($category->description, $category->descriptionformat,
                                $context->id, 'coursecat', 'description', null);
                    $categoryinfo['parent'] = $category->parent;
                    $categoryinfo['sortorder'] = $category->sortorder;
                    $categoryinfo['coursecount'] = $category->coursecount;
                    $categoryinfo['depth'] = $category->depth;
                    $categoryinfo['path'] = $category->path;

                    // Some fields only returned for admin.
                    if (has_capability('moodle/category:manage', $context)) {
                        $categoryinfo['idnumber'] = $category->idnumber;
                        $categoryinfo['visible'] = $category->visible;
                        $categoryinfo['visibleold'] = $category->visibleold;
                        $categoryinfo['timemodified'] = $category->timemodified;
                        $categoryinfo['theme'] = clean_param($category->theme, PARAM_THEME);
                    }

                    $categoriesinfo[] = $categoryinfo;
                } else {
                    $excludedcats[$category->id] = 'visibility';
                }
            }
        }

        // Sorting the resulting array so it looks a bit better for the client developer.
        usort($categoriesinfo, "core_course_external::compare_categories_by_sortorder");

        return $categoriesinfo;
    }

    /**
     * Sort categories array by path
     * private function: only used by get_categories
     *
     * @param array $category1
     * @param array $category2
     * @return int result of strcmp
     * @since Moodle 2.3
     */
    private static function compare_categories_by_path($category1, $category2) {
        return strcmp($category1->path, $category2->path);
    }

    /**
     * Sort categories array by sortorder
     * private function: only used by get_categories
     *
     * @param array $category1
     * @param array $category2
     * @return int result of strcmp
     * @since Moodle 2.3
     */
    private static function compare_categories_by_sortorder($category1, $category2) {
        return strcmp($category1['sortorder'], $category2['sortorder']);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.3
     */
    public static function get_categories_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'category id'),
                    'name' => new external_value(PARAM_RAW, 'category name'),
                    'idnumber' => new external_value(PARAM_RAW, 'category id number', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'category description'),
                    'descriptionformat' => new external_format_value('description'),
                    'parent' => new external_value(PARAM_INT, 'parent category id'),
                    'sortorder' => new external_value(PARAM_INT, 'category sorting order'),
                    'coursecount' => new external_value(PARAM_INT, 'number of courses in this category'),
                    'visible' => new external_value(PARAM_INT, '1: available, 0:not available', VALUE_OPTIONAL),
                    'visibleold' => new external_value(PARAM_INT, '1: available, 0:not available', VALUE_OPTIONAL),
                    'timemodified' => new external_value(PARAM_INT, 'timestamp', VALUE_OPTIONAL),
                    'depth' => new external_value(PARAM_INT, 'category depth'),
                    'path' => new external_value(PARAM_TEXT, 'category path'),
                    'theme' => new external_value(PARAM_THEME, 'category theme', VALUE_OPTIONAL),
                ), 'List of categories'
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function create_categories_parameters() {
        return new external_function_parameters(
            array(
                'categories' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name' => new external_value(PARAM_TEXT, 'new category name'),
                                'parent' => new external_value(PARAM_INT,
                                        'the parent category id inside which the new category will be created
                                         - set to 0 for a root category',
                                        VALUE_DEFAULT, 0),
                                'idnumber' => new external_value(PARAM_RAW,
                                        'the new category idnumber', VALUE_OPTIONAL),
                                'description' => new external_value(PARAM_RAW,
                                        'the new category description', VALUE_OPTIONAL),
                                'descriptionformat' => new external_format_value('description', VALUE_DEFAULT),
                                'theme' => new external_value(PARAM_THEME,
                                        'the new category theme. This option must be enabled on moodle',
                                        VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Create categories
     *
     * @param array $categories - see create_categories_parameters() for the array structure
     * @return array - see create_categories_returns() for the array structure
     * @since Moodle 2.3
     */
    public static function create_categories($categories) {
        global $DB;

        $params = self::validate_parameters(self::create_categories_parameters(),
                        array('categories' => $categories));

        $transaction = $DB->start_delegated_transaction();

        $createdcategories = array();
        foreach ($params['categories'] as $category) {
            if ($category['parent']) {
                if (!$DB->record_exists('course_categories', array('id' => $category['parent']))) {
                    throw new moodle_exception('unknowcategory');
                }
                $context = context_coursecat::instance($category['parent']);
            } else {
                $context = context_system::instance();
            }
            self::validate_context($context);
            require_capability('moodle/category:manage', $context);

            // this will validate format and throw an exception if there are errors
            external_validate_format($category['descriptionformat']);

            $newcategory = core_course_category::create($category);
            $context = context_coursecat::instance($newcategory->id);

            $createdcategories[] = array(
                'id' => $newcategory->id,
                'name' => external_format_string($newcategory->name, $context),
            );
        }

        $transaction->allow_commit();

        return $createdcategories;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function create_categories_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'new category id'),
                    'name' => new external_value(PARAM_RAW, 'new category name'),
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function update_categories_parameters() {
        return new external_function_parameters(
            array(
                'categories' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id'       => new external_value(PARAM_INT, 'course id'),
                            'name' => new external_value(PARAM_TEXT, 'category name', VALUE_OPTIONAL),
                            'idnumber' => new external_value(PARAM_RAW, 'category id number', VALUE_OPTIONAL),
                            'parent' => new external_value(PARAM_INT, 'parent category id', VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_RAW, 'category description', VALUE_OPTIONAL),
                            'descriptionformat' => new external_format_value('description', VALUE_DEFAULT),
                            'theme' => new external_value(PARAM_THEME,
                                    'the category theme. This option must be enabled on moodle', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Update categories
     *
     * @param array $categories The list of categories to update
     * @return null
     * @since Moodle 2.3
     */
    public static function update_categories($categories) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::update_categories_parameters(), array('categories' => $categories));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['categories'] as $cat) {
            $category = core_course_category::get($cat['id']);

            $categorycontext = context_coursecat::instance($cat['id']);
            self::validate_context($categorycontext);
            require_capability('moodle/category:manage', $categorycontext);

            // this will throw an exception if descriptionformat is not valid
            external_validate_format($cat['descriptionformat']);

            $category->update($cat);
        }

        $transaction->allow_commit();
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.3
     */
    public static function update_categories_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function delete_categories_parameters() {
        return new external_function_parameters(
            array(
                'categories' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'category id to delete'),
                            'newparent' => new external_value(PARAM_INT,
                                'the parent category to move the contents to, if specified', VALUE_OPTIONAL),
                            'recursive' => new external_value(PARAM_BOOL, '1: recursively delete all contents inside this
                                category, 0 (default): move contents to newparent or current parent category (except if parent is root)', VALUE_DEFAULT, 0)
                        )
                    )
                )
            )
        );
    }

    /**
     * Delete categories
     *
     * @param array $categories A list of category ids
     * @return array
     * @since Moodle 2.3
     */
    public static function delete_categories($categories) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        // Validate parameters.
        $params = self::validate_parameters(self::delete_categories_parameters(), array('categories' => $categories));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['categories'] as $category) {
            $deletecat = core_course_category::get($category['id'], MUST_EXIST);
            $context = context_coursecat::instance($deletecat->id);
            require_capability('moodle/category:manage', $context);
            self::validate_context($context);
            self::validate_context(get_category_or_system_context($deletecat->parent));

            if ($category['recursive']) {
                // If recursive was specified, then we recursively delete the category's contents.
                if ($deletecat->can_delete_full()) {
                    $deletecat->delete_full(false);
                } else {
                    throw new moodle_exception('youcannotdeletecategory', '', '', $deletecat->get_formatted_name());
                }
            } else {
                // In this situation, we don't delete the category's contents, we either move it to newparent or parent.
                // If the parent is the root, moving is not supported (because a course must always be inside a category).
                // We must move to an existing category.
                if (!empty($category['newparent'])) {
                    $newparentcat = core_course_category::get($category['newparent']);
                } else {
                    $newparentcat = core_course_category::get($deletecat->parent);
                }

                // This operation is not allowed. We must move contents to an existing category.
                if (!$newparentcat->id) {
                    throw new moodle_exception('movecatcontentstoroot');
                }

                self::validate_context(context_coursecat::instance($newparentcat->id));
                if ($deletecat->can_move_content_to($newparentcat->id)) {
                    $deletecat->delete_move($newparentcat->id, false);
                } else {
                    throw new moodle_exception('youcannotdeletecategory', '', '', $deletecat->get_formatted_name());
                }
            }
        }

        $transaction->allow_commit();
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function delete_categories_returns() {
        return null;
    }

    /**
     * Describes the parameters for delete_modules.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_modules_parameters() {
        return new external_function_parameters (
            array(
                'cmids' => new external_multiple_structure(new external_value(PARAM_INT, 'course module ID',
                        VALUE_REQUIRED, '', NULL_NOT_ALLOWED), 'Array of course module IDs'),
            )
        );
    }

    /**
     * Deletes a list of provided module instances.
     *
     * @param array $cmids the course module ids
     * @since Moodle 2.5
     */
    public static function delete_modules($cmids) {
        global $CFG, $DB;

        // Require course file containing the course delete module function.
        require_once($CFG->dirroot . "/course/lib.php");

        // Clean the parameters.
        $params = self::validate_parameters(self::delete_modules_parameters(), array('cmids' => $cmids));

        // Keep track of the course ids we have performed a capability check on to avoid repeating.
        $arrcourseschecked = array();

        foreach ($params['cmids'] as $cmid) {
            // Get the course module.
            $cm = $DB->get_record('course_modules', array('id' => $cmid), '*', MUST_EXIST);

            // Check if we have not yet confirmed they have permission in this course.
            if (!in_array($cm->course, $arrcourseschecked)) {
                // Ensure the current user has required permission in this course.
                $context = context_course::instance($cm->course);
                self::validate_context($context);
                // Add to the array.
                $arrcourseschecked[] = $cm->course;
            }

            // Ensure they can delete this module.
            $modcontext = context_module::instance($cm->id);
            require_capability('moodle/course:manageactivities', $modcontext);

            // Delete the module.
            course_delete_module($cm->id);
        }
    }

    /**
     * Describes the delete_modules return value.
     *
     * @return external_single_structure
     * @since Moodle 2.5
     */
    public static function delete_modules_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_course_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of the course'),
                'sectionnumber' => new external_value(PARAM_INT, 'section number', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Trigger the course viewed event.
     *
     * @param int $courseid id of course
     * @param int $sectionnumber sectionnumber (0, 1, 2...)
     * @return array of warnings and status result
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function view_course($courseid, $sectionnumber = 0) {
        global $CFG;
        require_once($CFG->dirroot . "/course/lib.php");

        $params = self::validate_parameters(self::view_course_parameters(),
                                            array(
                                                'courseid' => $courseid,
                                                'sectionnumber' => $sectionnumber
                                            ));

        $warnings = array();

        $course = get_course($params['courseid']);
        $context = context_course::instance($course->id);
        self::validate_context($context);

        if (!empty($params['sectionnumber'])) {

            // Get section details and check it exists.
            $modinfo = get_fast_modinfo($course);
            $coursesection = $modinfo->get_section_info($params['sectionnumber'], MUST_EXIST);

            // Check user is allowed to see it.
            if (!$coursesection->uservisible) {
                require_capability('moodle/course:viewhiddensections', $context);
            }
        }

        course_view($context, $params['sectionnumber']);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function view_course_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function search_courses_parameters() {
        return new external_function_parameters(
            array(
                'criterianame'  => new external_value(PARAM_ALPHA, 'criteria name
                                                        (search, modulelist (only admins), blocklist (only admins), tagid)'),
                'criteriavalue' => new external_value(PARAM_RAW, 'criteria value'),
                'page'          => new external_value(PARAM_INT, 'page number (0 based)', VALUE_DEFAULT, 0),
                'perpage'       => new external_value(PARAM_INT, 'items per page', VALUE_DEFAULT, 0),
                'requiredcapabilities' => new external_multiple_structure(
                    new external_value(PARAM_CAPABILITY, 'Capability string used to filter courses by permission'),
                    'Optional list of required capabilities (used to filter the list)', VALUE_DEFAULT, array()
                ),
                'limittoenrolled' => new external_value(PARAM_BOOL, 'limit to enrolled courses', VALUE_DEFAULT, 0),
                'onlywithcompletion' => new external_value(PARAM_BOOL, 'limit to courses where completion is enabled',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Return the course information that is public (visible by every one)
     *
     * @param  core_course_list_element $course        course in list object
     * @param  stdClass       $coursecontext course context object
     * @return array the course information
     * @since  Moodle 3.2
     */
    protected static function get_course_public_information(core_course_list_element $course, $coursecontext) {

        static $categoriescache = array();

        // Category information.
        if (!array_key_exists($course->category, $categoriescache)) {
            $categoriescache[$course->category] = core_course_category::get($course->category, IGNORE_MISSING);
        }
        $category = $categoriescache[$course->category];

        // Retrieve course overview used files.
        $files = array();
        foreach ($course->get_course_overviewfiles() as $file) {
            $fileurl = moodle_url::make_webservice_pluginfile_url($file->get_contextid(), $file->get_component(),
                                                                    $file->get_filearea(), null, $file->get_filepath(),
                                                                    $file->get_filename())->out(false);
            $files[] = array(
                'filename' => $file->get_filename(),
                'fileurl' => $fileurl,
                'filesize' => $file->get_filesize(),
                'filepath' => $file->get_filepath(),
                'mimetype' => $file->get_mimetype(),
                'timemodified' => $file->get_timemodified(),
            );
        }

        // Retrieve the course contacts,
        // we need here the users fullname since if we are not enrolled can be difficult to obtain them via other Web Services.
        $coursecontacts = array();
        foreach ($course->get_course_contacts() as $contact) {
             $coursecontacts[] = array(
                'id' => $contact['user']->id,
                'fullname' => $contact['username'],
                'roles' => array_map(function($role){
                    return array('id' => $role->id, 'name' => $role->displayname);
                }, $contact['roles']),
                'role' => array('id' => $contact['role']->id, 'name' => $contact['role']->displayname),
                'rolename' => $contact['rolename']
             );
        }

        // Allowed enrolment methods (maybe we can self-enrol).
        $enroltypes = array();
        $instances = enrol_get_instances($course->id, true);
        foreach ($instances as $instance) {
            $enroltypes[] = $instance->enrol;
        }

        // Format summary.
        list($summary, $summaryformat) =
            external_format_text($course->summary, $course->summaryformat, $coursecontext->id, 'course', 'summary', null);

        $categoryname = '';
        if (!empty($category)) {
            $categoryname = external_format_string($category->name, $category->get_context());
        }

        $displayname = get_course_display_name_for_list($course);
        $coursereturns = array();
        $coursereturns['id']                = $course->id;
        $coursereturns['fullname']          = external_format_string($course->fullname, $coursecontext->id);
        $coursereturns['displayname']       = external_format_string($displayname, $coursecontext->id);
        $coursereturns['shortname']         = external_format_string($course->shortname, $coursecontext->id);
        $coursereturns['categoryid']        = $course->category;
        $coursereturns['categoryname']      = $categoryname;
        $coursereturns['summary']           = $summary;
        $coursereturns['summaryformat']     = $summaryformat;
        $coursereturns['summaryfiles']      = external_util::get_area_files($coursecontext->id, 'course', 'summary', false, false);
        $coursereturns['overviewfiles']     = $files;
        $coursereturns['contacts']          = $coursecontacts;
        $coursereturns['enrollmentmethods'] = $enroltypes;
        $coursereturns['sortorder']         = $course->sortorder;

        $handler = core_course\customfield\course_handler::create();
        if ($customfields = $handler->export_instance_data($course->id)) {
            $coursereturns['customfields'] = [];
            foreach ($customfields as $data) {
                $coursereturns['customfields'][] = [
                    'type' => $data->get_type(),
                    'value' => $data->get_value(),
                    'valueraw' => $data->get_data_controller()->get_value(),
                    'name' => $data->get_name(),
                    'shortname' => $data->get_shortname()
                ];
            }
        }

        return $coursereturns;
    }

    /**
     * Search courses following the specified criteria.
     *
     * @param string $criterianame  Criteria name (search, modulelist (only admins), blocklist (only admins), tagid)
     * @param string $criteriavalue Criteria value
     * @param int $page             Page number (for pagination)
     * @param int $perpage          Items per page
     * @param array $requiredcapabilities Optional list of required capabilities (used to filter the list).
     * @param int $limittoenrolled  Limit to only enrolled courses
     * @param int onlywithcompletion Limit to only courses where completion is enabled
     * @return array of course objects and warnings
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function search_courses($criterianame,
                                          $criteriavalue,
                                          $page=0,
                                          $perpage=0,
                                          $requiredcapabilities=array(),
                                          $limittoenrolled=0,
                                          $onlywithcompletion=0) {
        global $CFG;

        $warnings = array();

        $parameters = array(
            'criterianame'  => $criterianame,
            'criteriavalue' => $criteriavalue,
            'page'          => $page,
            'perpage'       => $perpage,
            'requiredcapabilities' => $requiredcapabilities,
            'limittoenrolled' => $limittoenrolled,
            'onlywithcompletion' => $onlywithcompletion
        );
        $params = self::validate_parameters(self::search_courses_parameters(), $parameters);
        self::validate_context(context_system::instance());

        $allowedcriterianames = array('search', 'modulelist', 'blocklist', 'tagid');
        if (!in_array($params['criterianame'], $allowedcriterianames)) {
            throw new invalid_parameter_exception('Invalid value for criterianame parameter (value: '.$params['criterianame'].'),' .
                'allowed values are: '.implode(',', $allowedcriterianames));
        }

        if ($params['criterianame'] == 'modulelist' or $params['criterianame'] == 'blocklist') {
            require_capability('moodle/site:config', context_system::instance());
        }

        $paramtype = array(
            'search' => PARAM_RAW,
            'modulelist' => PARAM_PLUGIN,
            'blocklist' => PARAM_INT,
            'tagid' => PARAM_INT
        );
        $params['criteriavalue'] = clean_param($params['criteriavalue'], $paramtype[$params['criterianame']]);

        // Prepare the search API options.
        $searchcriteria = array();
        $searchcriteria[$params['criterianame']] = $params['criteriavalue'];
        if ($params['onlywithcompletion']) {
            $searchcriteria['onlywithcompletion'] = true;
        }

        $options = array();
        if ($params['perpage'] != 0) {
            $offset = $params['page'] * $params['perpage'];
            $options = array('offset' => $offset, 'limit' => $params['perpage']);
        }

        // Search the courses.
        $courses = core_course_category::search_courses($searchcriteria, $options, $params['requiredcapabilities']);
        $totalcount = core_course_category::search_courses_count($searchcriteria, $options, $params['requiredcapabilities']);

        if (!empty($limittoenrolled)) {
            // Get the courses where the current user has access.
            $enrolled = enrol_get_my_courses(array('id', 'cacherev'));
        }

        $finalcourses = array();
        $categoriescache = array();

        foreach ($courses as $course) {
            if (!empty($limittoenrolled)) {
                // Filter out not enrolled courses.
                if (!isset($enrolled[$course->id])) {
                    $totalcount--;
                    continue;
                }
            }

            $coursecontext = context_course::instance($course->id);

            $finalcourses[] = self::get_course_public_information($course, $coursecontext);
        }

        return array(
            'total' => $totalcount,
            'courses' => $finalcourses,
            'warnings' => $warnings
        );
    }

    /**
     * Returns a course structure definition
     *
     * @param  boolean $onlypublicdata set to true, to retrieve only fields viewable by anyone when the course is visible
     * @return array the course structure
     * @since  Moodle 3.2
     */
    protected static function get_course_structure($onlypublicdata = true) {
        $coursestructure = array(
            'id' => new external_value(PARAM_INT, 'course id'),
            'fullname' => new external_value(PARAM_RAW, 'course full name'),
            'displayname' => new external_value(PARAM_RAW, 'course display name'),
            'shortname' => new external_value(PARAM_RAW, 'course short name'),
            'categoryid' => new external_value(PARAM_INT, 'category id'),
            'categoryname' => new external_value(PARAM_RAW, 'category name'),
            'sortorder' => new external_value(PARAM_INT, 'Sort order in the category', VALUE_OPTIONAL),
            'summary' => new external_value(PARAM_RAW, 'summary'),
            'summaryformat' => new external_format_value('summary'),
            'summaryfiles' => new external_files('summary files in the summary field', VALUE_OPTIONAL),
            'overviewfiles' => new external_files('additional overview files attached to this course'),
            'contacts' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'contact user id'),
                        'fullname'  => new external_value(PARAM_NOTAGS, 'contact user fullname'),
                    )
                ),
                'contact users'
            ),
            'enrollmentmethods' => new external_multiple_structure(
                new external_value(PARAM_PLUGIN, 'enrollment method'),
                'enrollment methods list'
            ),
            'customfields' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                        'shortname' => new external_value(PARAM_RAW,
                            'The shortname of the custom field - to be able to build the field class in the code'),
                        'type'  => new external_value(PARAM_ALPHANUMEXT,
                            'The type of the custom field - text field, checkbox...'),
                        'valueraw' => new external_value(PARAM_RAW, 'The raw value of the custom field'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                    )
                ), 'Custom fields', VALUE_OPTIONAL),
        );

        if (!$onlypublicdata) {
            $extra = array(
                'idnumber' => new external_value(PARAM_RAW, 'Id number', VALUE_OPTIONAL),
                'format' => new external_value(PARAM_PLUGIN, 'Course format: weeks, topics, social, site,..', VALUE_OPTIONAL),
                'showgrades' => new external_value(PARAM_INT, '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                'newsitems' => new external_value(PARAM_INT, 'Number of recent items appearing on the course page', VALUE_OPTIONAL),
                'startdate' => new external_value(PARAM_INT, 'Timestamp when the course start', VALUE_OPTIONAL),
                'enddate' => new external_value(PARAM_INT, 'Timestamp when the course end', VALUE_OPTIONAL),
                'maxbytes' => new external_value(PARAM_INT, 'Largest size of file that can be uploaded into', VALUE_OPTIONAL),
                'showreports' => new external_value(PARAM_INT, 'Are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                'visible' => new external_value(PARAM_INT, '1: available to student, 0:not available', VALUE_OPTIONAL),
                'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible', VALUE_OPTIONAL),
                'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no', VALUE_OPTIONAL),
                'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id', VALUE_OPTIONAL),
                'enablecompletion' => new external_value(PARAM_INT, 'Completion enabled? 1: yes 0: no', VALUE_OPTIONAL),
                'completionnotify' => new external_value(PARAM_INT, '1: yes 0: no', VALUE_OPTIONAL),
                'lang' => new external_value(PARAM_SAFEDIR, 'Forced course language', VALUE_OPTIONAL),
                'theme' => new external_value(PARAM_PLUGIN, 'Fame of the forced theme', VALUE_OPTIONAL),
                'marker' => new external_value(PARAM_INT, 'Current course marker', VALUE_OPTIONAL),
                'legacyfiles' => new external_value(PARAM_INT, 'If legacy files are enabled', VALUE_OPTIONAL),
                'calendartype' => new external_value(PARAM_PLUGIN, 'Calendar type', VALUE_OPTIONAL),
                'timecreated' => new external_value(PARAM_INT, 'Time when the course was created', VALUE_OPTIONAL),
                'timemodified' => new external_value(PARAM_INT, 'Last time  the course was updated', VALUE_OPTIONAL),
                'requested' => new external_value(PARAM_INT, 'If is a requested course', VALUE_OPTIONAL),
                'cacherev' => new external_value(PARAM_INT, 'Cache revision number', VALUE_OPTIONAL),
                'filters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'filter'  => new external_value(PARAM_PLUGIN, 'Filter plugin name'),
                            'localstate' => new external_value(PARAM_INT, 'Filter state: 1 for on, -1 for off, 0 if inherit'),
                            'inheritedstate' => new external_value(PARAM_INT, '1 or 0 to use when localstate is set to inherit'),
                        )
                    ),
                    'Course filters', VALUE_OPTIONAL
                ),
                'courseformatoptions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'Course format option name.'),
                            'value' => new external_value(PARAM_RAW, 'Course format option value.'),
                        )
                    ),
                    'Additional options for particular course format.', VALUE_OPTIONAL
                ),
            );
            $coursestructure = array_merge($coursestructure, $extra);
        }
        return new external_single_structure($coursestructure);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function search_courses_returns() {
        return new external_single_structure(
            array(
                'total' => new external_value(PARAM_INT, 'total course count'),
                'courses' => new external_multiple_structure(self::get_course_structure(), 'course'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_course_module_parameters() {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'The course module id')
            )
        );
    }

    /**
     * Return information about a course module.
     *
     * @param int $cmid the course module id
     * @return array of warnings and the course module
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_course_module($cmid) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::get_course_module_parameters(), array('cmid' => $cmid));
        $warnings = array();

        $cm = get_coursemodule_from_id(null, $params['cmid'], 0, true, MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // If the user has permissions to manage the activity, return all the information.
        if (has_capability('moodle/course:manageactivities', $context)) {
            require_once($CFG->dirroot . '/course/modlib.php');
            require_once($CFG->libdir . '/gradelib.php');

            $info = $cm;
            // Get the extra information: grade, advanced grading and outcomes data.
            $course = get_course($cm->course);
            list($newcm, $newcontext, $module, $extrainfo, $cw) = get_moduleinfo_data($cm, $course);
            // Grades.
            $gradeinfo = array('grade', 'gradepass', 'gradecat');
            foreach ($gradeinfo as $gfield) {
                if (isset($extrainfo->{$gfield})) {
                    $info->{$gfield} = $extrainfo->{$gfield};
                }
            }
            if (isset($extrainfo->grade) and $extrainfo->grade < 0) {
                $info->scale = $DB->get_field('scale', 'scale', array('id' => abs($extrainfo->grade)));
            }
            // Advanced grading.
            if (isset($extrainfo->_advancedgradingdata)) {
                $info->advancedgrading = array();
                foreach ($extrainfo as $key => $val) {
                    if (strpos($key, 'advancedgradingmethod_') === 0) {
                        $info->advancedgrading[] = array(
                            'area' => str_replace('advancedgradingmethod_', '', $key),
                            'method' => $val
                        );
                    }
                }
            }
            // Outcomes.
            foreach ($extrainfo as $key => $val) {
                if (strpos($key, 'outcome_') === 0) {
                    if (!isset($info->outcomes)) {
                        $info->outcomes = array();
                    }
                    $id = str_replace('outcome_', '', $key);
                    $outcome = grade_outcome::fetch(array('id' => $id));
                    $scaleitems = $outcome->load_scale();
                    $info->outcomes[] = array(
                        'id' => $id,
                        'name' => external_format_string($outcome->get_name(), $context->id),
                        'scale' => $scaleitems->scale
                    );
                }
            }
        } else {
            // Return information is safe to show to any user.
            $info = new stdClass();
            $info->id = $cm->id;
            $info->course = $cm->course;
            $info->module = $cm->module;
            $info->modname = $cm->modname;
            $info->instance = $cm->instance;
            $info->section = $cm->section;
            $info->sectionnum = $cm->sectionnum;
            $info->groupmode = $cm->groupmode;
            $info->groupingid = $cm->groupingid;
            $info->completion = $cm->completion;
        }
        // Format name.
        $info->name = external_format_string($cm->name, $context->id);
        $result = array();
        $result['cm'] = $info;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_course_module_returns() {
        return new external_single_structure(
            array(
                'cm' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'The course module id'),
                        'course' => new external_value(PARAM_INT, 'The course id'),
                        'module' => new external_value(PARAM_INT, 'The module type id'),
                        'name' => new external_value(PARAM_RAW, 'The activity name'),
                        'modname' => new external_value(PARAM_COMPONENT, 'The module component name (forum, assign, etc..)'),
                        'instance' => new external_value(PARAM_INT, 'The activity instance id'),
                        'section' => new external_value(PARAM_INT, 'The module section id'),
                        'sectionnum' => new external_value(PARAM_INT, 'The module section number'),
                        'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                        'groupingid' => new external_value(PARAM_INT, 'Grouping id'),
                        'completion' => new external_value(PARAM_INT, 'If completion is enabled'),
                        'idnumber' => new external_value(PARAM_RAW, 'Module id number', VALUE_OPTIONAL),
                        'added' => new external_value(PARAM_INT, 'Time added', VALUE_OPTIONAL),
                        'score' => new external_value(PARAM_INT, 'Score', VALUE_OPTIONAL),
                        'indent' => new external_value(PARAM_INT, 'Indentation', VALUE_OPTIONAL),
                        'visible' => new external_value(PARAM_INT, 'If visible', VALUE_OPTIONAL),
                        'visibleoncoursepage' => new external_value(PARAM_INT, 'If visible on course page', VALUE_OPTIONAL),
                        'visibleold' => new external_value(PARAM_INT, 'Visible old', VALUE_OPTIONAL),
                        'completiongradeitemnumber' => new external_value(PARAM_INT, 'Completion grade item', VALUE_OPTIONAL),
                        'completionview' => new external_value(PARAM_INT, 'Completion view setting', VALUE_OPTIONAL),
                        'completionexpected' => new external_value(PARAM_INT, 'Completion time expected', VALUE_OPTIONAL),
                        'showdescription' => new external_value(PARAM_INT, 'If the description is showed', VALUE_OPTIONAL),
                        'availability' => new external_value(PARAM_RAW, 'Availability settings', VALUE_OPTIONAL),
                        'grade' => new external_value(PARAM_FLOAT, 'Grade (max value or scale id)', VALUE_OPTIONAL),
                        'scale' => new external_value(PARAM_TEXT, 'Scale items (if used)', VALUE_OPTIONAL),
                        'gradepass' => new external_value(PARAM_RAW, 'Grade to pass (float)', VALUE_OPTIONAL),
                        'gradecat' => new external_value(PARAM_INT, 'Grade category', VALUE_OPTIONAL),
                        'advancedgrading' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'area' => new external_value(PARAM_AREA, 'Gradable area name'),
                                    'method'  => new external_value(PARAM_COMPONENT, 'Grading method'),
                                )
                            ),
                            'Advanced grading settings', VALUE_OPTIONAL
                        ),
                        'outcomes' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'id' => new external_value(PARAM_ALPHANUMEXT, 'Outcome id'),
                                    'name'  => new external_value(PARAM_RAW, 'Outcome full name'),
                                    'scale' => new external_value(PARAM_TEXT, 'Scale items')
                                )
                            ),
                            'Outcomes information', VALUE_OPTIONAL
                        ),
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_course_module_by_instance_parameters() {
        return new external_function_parameters(
            array(
                'module' => new external_value(PARAM_COMPONENT, 'The module name'),
                'instance' => new external_value(PARAM_INT, 'The module instance id')
            )
        );
    }

    /**
     * Return information about a course module.
     *
     * @param string $module the module name
     * @param int $instance the activity instance id
     * @return array of warnings and the course module
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_course_module_by_instance($module, $instance) {

        $params = self::validate_parameters(self::get_course_module_by_instance_parameters(),
                                            array(
                                                'module' => $module,
                                                'instance' => $instance,
                                            ));

        $warnings = array();
        $cm = get_coursemodule_from_instance($params['module'], $params['instance'], 0, false, MUST_EXIST);

        return self::get_course_module($cm->id);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_course_module_by_instance_returns() {
        return self::get_course_module_returns();
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function get_user_navigation_options_parameters() {
        return new external_function_parameters(
            array(
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'Course id.')),
            )
        );
    }

    /**
     * Return a list of navigation options in a set of courses that are avaialable or not for the current user.
     *
     * @param array $courseids a list of course ids
     * @return array of warnings and the options availability
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function get_user_navigation_options($courseids) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        // Parameter validation.
        $params = self::validate_parameters(self::get_user_navigation_options_parameters(), array('courseids' => $courseids));
        $courseoptions = array();

        list($courses, $warnings) = external_util::validate_courses($params['courseids'], array(), true);

        if (!empty($courses)) {
            foreach ($courses as $course) {
                // Fix the context for the frontpage.
                if ($course->id == SITEID) {
                    $course->context = context_system::instance();
                }
                $navoptions = course_get_user_navigation_options($course->context, $course);
                $options = array();
                foreach ($navoptions as $name => $available) {
                    $options[] = array(
                        'name' => $name,
                        'available' => $available,
                    );
                }

                $courseoptions[] = array(
                    'id' => $course->id,
                    'options' => $options
                );
            }
        }

        $result = array(
            'courses' => $courseoptions,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function get_user_navigation_options_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Course id'),
                            'options' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'name' => new external_value(PARAM_ALPHANUMEXT, 'Option name'),
                                        'available' => new external_value(PARAM_BOOL, 'Whether the option is available or not'),
                                    )
                                )
                            )
                        )
                    ), 'List of courses'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function get_user_administration_options_parameters() {
        return new external_function_parameters(
            array(
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'Course id.')),
            )
        );
    }

    /**
     * Return a list of administration options in a set of courses that are available or not for the current user.
     *
     * @param array $courseids a list of course ids
     * @return array of warnings and the options availability
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function get_user_administration_options($courseids) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        // Parameter validation.
        $params = self::validate_parameters(self::get_user_administration_options_parameters(), array('courseids' => $courseids));
        $courseoptions = array();

        list($courses, $warnings) = external_util::validate_courses($params['courseids'], array(), true);

        if (!empty($courses)) {
            foreach ($courses as $course) {
                $adminoptions = course_get_user_administration_options($course, $course->context);
                $options = array();
                foreach ($adminoptions as $name => $available) {
                    $options[] = array(
                        'name' => $name,
                        'available' => $available,
                    );
                }

                $courseoptions[] = array(
                    'id' => $course->id,
                    'options' => $options
                );
            }
        }

        $result = array(
            'courses' => $courseoptions,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function get_user_administration_options_returns() {
        return self::get_user_navigation_options_returns();
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function get_courses_by_field_parameters() {
        return new external_function_parameters(
            array(
                'field' => new external_value(PARAM_ALPHA, 'The field to search can be left empty for all courses or:
                    id: course id
                    ids: comma separated course ids
                    shortname: course short name
                    idnumber: course id number
                    category: category id the course belongs to
                ', VALUE_DEFAULT, ''),
                'value' => new external_value(PARAM_RAW, 'The value to match', VALUE_DEFAULT, '')
            )
        );
    }


    /**
     * Get courses matching a specific field (id/s, shortname, idnumber, category)
     *
     * @param  string $field field name to search, or empty for all courses
     * @param  string $value value to search
     * @return array list of courses and warnings
     * @throws  invalid_parameter_exception
     * @since Moodle 3.2
     */
    public static function get_courses_by_field($field = '', $value = '') {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir . '/filterlib.php');

        $params = self::validate_parameters(self::get_courses_by_field_parameters(),
            array(
                'field' => $field,
                'value' => $value,
            )
        );
        $warnings = array();

        if (empty($params['field'])) {
            $courses = $DB->get_records('course', null, 'id ASC');
        } else {
            switch ($params['field']) {
                case 'id':
                case 'category':
                    $value = clean_param($params['value'], PARAM_INT);
                    break;
                case 'ids':
                    $value = clean_param($params['value'], PARAM_SEQUENCE);
                    break;
                case 'shortname':
                    $value = clean_param($params['value'], PARAM_TEXT);
                    break;
                case 'idnumber':
                    $value = clean_param($params['value'], PARAM_RAW);
                    break;
                default:
                    throw new invalid_parameter_exception('Invalid field name');
            }

            if ($params['field'] === 'ids') {
                // Preload categories to avoid loading one at a time.
                $courseids = explode(',', $value);
                list ($listsql, $listparams) = $DB->get_in_or_equal($courseids);
                $categoryids = $DB->get_fieldset_sql("
                        SELECT DISTINCT cc.id
                          FROM {course} c
                          JOIN {course_categories} cc ON cc.id = c.category
                         WHERE c.id $listsql", $listparams);
                core_course_category::get_many($categoryids);

                // Load and validate all courses. This is called because it loads the courses
                // more efficiently.
                list ($courses, $warnings) = external_util::validate_courses($courseids, [],
                        false, true);
            } else {
                $courses = $DB->get_records('course', array($params['field'] => $value), 'id ASC');
            }
        }

        $coursesdata = array();
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            $canupdatecourse = has_capability('moodle/course:update', $context);
            $canviewhiddencourses = has_capability('moodle/course:viewhiddencourses', $context);

            // Check if the course is visible in the site for the user.
            if (!$course->visible and !$canviewhiddencourses and !$canupdatecourse) {
                continue;
            }
            // Get the public course information, even if we are not enrolled.
            $courseinlist = new core_course_list_element($course);

            // Now, check if we have access to the course, unless it was already checked.
            try {
                if (empty($course->contextvalidated)) {
                    self::validate_context($context);
                }
            } catch (Exception $e) {
                // User can not access the course, check if they can see the public information about the course and return it.
                if (core_course_category::can_view_course_info($course)) {
                    $coursesdata[$course->id] = self::get_course_public_information($courseinlist, $context);
                }
                continue;
            }
            $coursesdata[$course->id] = self::get_course_public_information($courseinlist, $context);
            // Return information for any user that can access the course.
            $coursefields = array('format', 'showgrades', 'newsitems', 'startdate', 'enddate', 'maxbytes', 'showreports', 'visible',
                'groupmode', 'groupmodeforce', 'defaultgroupingid', 'enablecompletion', 'completionnotify', 'lang', 'theme',
                'marker');

            // Course filters.
            $coursesdata[$course->id]['filters'] = filter_get_available_in_context($context);

            // Information for managers only.
            if ($canupdatecourse) {
                $managerfields = array('idnumber', 'legacyfiles', 'calendartype', 'timecreated', 'timemodified', 'requested',
                    'cacherev');
                $coursefields = array_merge($coursefields, $managerfields);
            }

            // Populate fields.
            foreach ($coursefields as $field) {
                $coursesdata[$course->id][$field] = $course->{$field};
            }

            // Clean lang and auth fields for external functions (it may content uninstalled themes or language packs).
            if (isset($coursesdata[$course->id]['theme'])) {
                $coursesdata[$course->id]['theme'] = clean_param($coursesdata[$course->id]['theme'], PARAM_THEME);
            }
            if (isset($coursesdata[$course->id]['lang'])) {
                $coursesdata[$course->id]['lang'] = clean_param($coursesdata[$course->id]['lang'], PARAM_LANG);
            }

            $courseformatoptions = course_get_format($course)->get_config_for_external();
            foreach ($courseformatoptions as $key => $value) {
                $coursesdata[$course->id]['courseformatoptions'][] = array(
                    'name' => $key,
                    'value' => $value
                );
            }
        }

        return array(
            'courses' => $coursesdata,
            'warnings' => $warnings
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function get_courses_by_field_returns() {
        // Course structure, including not only public viewable fields.
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(self::get_course_structure(false), 'Course'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function check_updates_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id to check'),
                'tocheck' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'contextlevel' => new external_value(PARAM_ALPHA, 'The context level for the file location.
                                                                                Only module supported right now.'),
                            'id' => new external_value(PARAM_INT, 'Context instance id'),
                            'since' => new external_value(PARAM_INT, 'Check updates since this time stamp'),
                        )
                    ),
                    'Instances to check'
                ),
                'filter' => new external_multiple_structure(
                    new external_value(PARAM_ALPHANUM, 'Area name: configuration, fileareas, completion, ratings, comments,
                                                        gradeitems, outcomes'),
                    'Check only for updates in these areas', VALUE_DEFAULT, array()
                )
            )
        );
    }

    /**
     * Check if there is updates affecting the user for the given course and contexts.
     * Right now only modules are supported.
     * This WS calls mod_check_updates_since for each module to check if there is any update the user should we aware of.
     *
     * @param int $courseid the list of modules to check
     * @param array $tocheck the list of modules to check
     * @param array $filter check only for updates in these areas
     * @return array list of updates and warnings
     * @throws moodle_exception
     * @since Moodle 3.2
     */
    public static function check_updates($courseid, $tocheck, $filter = array()) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        $params = self::validate_parameters(
            self::check_updates_parameters(),
            array(
                'courseid' => $courseid,
                'tocheck' => $tocheck,
                'filter' => $filter,
            )
        );

        $course = get_course($params['courseid']);
        $context = context_course::instance($course->id);
        self::validate_context($context);

        list($instances, $warnings) = course_check_updates($course, $params['tocheck'], $filter);

        $instancesformatted = array();
        foreach ($instances as $instance) {
            $updates = array();
            foreach ($instance['updates'] as $name => $data) {
                if (empty($data->updated)) {
                    continue;
                }
                $updatedata = array(
                    'name' => $name,
                );
                if (!empty($data->timeupdated)) {
                    $updatedata['timeupdated'] = $data->timeupdated;
                }
                if (!empty($data->itemids)) {
                    $updatedata['itemids'] = $data->itemids;
                }
                $updates[] = $updatedata;
            }
            if (!empty($updates)) {
                $instancesformatted[] = array(
                    'contextlevel' => $instance['contextlevel'],
                    'id' => $instance['id'],
                    'updates' => $updates
                );
            }
        }

        return array(
            'instances' => $instancesformatted,
            'warnings' => $warnings
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function check_updates_returns() {
        return new external_single_structure(
            array(
                'instances' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'contextlevel' => new external_value(PARAM_ALPHA, 'The context level'),
                            'id' => new external_value(PARAM_INT, 'Instance id'),
                            'updates' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'name' => new external_value(PARAM_ALPHANUMEXT, 'Name of the area updated.'),
                                        'timeupdated' => new external_value(PARAM_INT, 'Last time was updated', VALUE_OPTIONAL),
                                        'itemids' => new external_multiple_structure(
                                            new external_value(PARAM_INT, 'Instance id'),
                                            'The ids of the items updated',
                                            VALUE_OPTIONAL
                                        )
                                    )
                                )
                            )
                        )
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_updates_since_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id to check'),
                'since' => new external_value(PARAM_INT, 'Check updates since this time stamp'),
                'filter' => new external_multiple_structure(
                    new external_value(PARAM_ALPHANUM, 'Area name: configuration, fileareas, completion, ratings, comments,
                                                        gradeitems, outcomes'),
                    'Check only for updates in these areas', VALUE_DEFAULT, array()
                )
            )
        );
    }

    /**
     * Check if there are updates affecting the user for the given course since the given time stamp.
     *
     * This function is a wrapper of self::check_updates for retrieving all the updates since a given time for all the activities.
     *
     * @param int $courseid the list of modules to check
     * @param int $since check updates since this time stamp
     * @param array $filter check only for updates in these areas
     * @return array list of updates and warnings
     * @throws moodle_exception
     * @since Moodle 3.3
     */
    public static function get_updates_since($courseid, $since, $filter = array()) {
        global $CFG, $DB;

        $params = self::validate_parameters(
            self::get_updates_since_parameters(),
            array(
                'courseid' => $courseid,
                'since' => $since,
                'filter' => $filter,
            )
        );

        $course = get_course($params['courseid']);
        $modinfo = get_fast_modinfo($course);
        $tocheck = array();

        // Retrieve all the visible course modules for the current user.
        $cms = $modinfo->get_cms();
        foreach ($cms as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $tocheck[] = array(
                'id' => $cm->id,
                'contextlevel' => 'module',
                'since' => $params['since'],
            );
        }

        return self::check_updates($course->id, $tocheck, $params['filter']);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function get_updates_since_returns() {
        return self::check_updates_returns();
    }

    /**
     * Parameters for function edit_module()
     *
     * @since Moodle 3.3
     * @return external_function_parameters
     */
    public static function edit_module_parameters() {
        return new external_function_parameters(
            array(
                'action' => new external_value(PARAM_ALPHA,
                    'action: hide, show, stealth, duplicate, delete, moveleft, moveright, group...', VALUE_REQUIRED),
                'id' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED),
                'sectionreturn' => new external_value(PARAM_INT, 'section to return to', VALUE_DEFAULT, null),
            ));
    }

    /**
     * Performs one of the edit module actions and return new html for AJAX
     *
     * Returns html to replace the current module html with, for example:
     * - empty string for "delete" action,
     * - two modules html for "duplicate" action
     * - updated module html for everything else
     *
     * Throws exception if operation is not permitted/possible
     *
     * @since Moodle 3.3
     * @param string $action
     * @param int $id
     * @param null|int $sectionreturn
     * @return string
     */
    public static function edit_module($action, $id, $sectionreturn = null) {
        global $PAGE, $DB;
        // Validate and normalize parameters.
        $params = self::validate_parameters(self::edit_module_parameters(),
            array('action' => $action, 'id' => $id, 'sectionreturn' => $sectionreturn));
        $action = $params['action'];
        $id = $params['id'];
        $sectionreturn = $params['sectionreturn'];

        // Set of permissions an editing user may have.
        $contextarray = [
                'moodle/course:update',
                'moodle/course:manageactivities',
                'moodle/course:activityvisibility',
                'moodle/course:sectionvisibility',
                'moodle/course:movesections',
                'moodle/course:setcurrentsection',
        ];
        $PAGE->set_other_editing_capability($contextarray);

        list($course, $cm) = get_course_and_cm_from_cmid($id);
        $modcontext = context_module::instance($cm->id);
        $coursecontext = context_course::instance($course->id);
        self::validate_context($modcontext);
        $courserenderer = $PAGE->get_renderer('core', 'course');
        $completioninfo = new completion_info($course);

        switch($action) {
            case 'hide':
            case 'show':
            case 'stealth':
                require_capability('moodle/course:activityvisibility', $modcontext);
                $visible = ($action === 'hide') ? 0 : 1;
                $visibleoncoursepage = ($action === 'stealth') ? 0 : 1;
                set_coursemodule_visible($id, $visible, $visibleoncoursepage);
                \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();
                break;
            case 'duplicate':
                require_capability('moodle/course:manageactivities', $coursecontext);
                require_capability('moodle/backup:backuptargetimport', $coursecontext);
                require_capability('moodle/restore:restoretargetimport', $coursecontext);
                if (!course_allowed_module($course, $cm->modname)) {
                    throw new moodle_exception('No permission to create that activity');
                }
                if ($newcm = duplicate_module($course, $cm)) {
                    $cm = get_fast_modinfo($course)->get_cm($id);
                    $newcm = get_fast_modinfo($course)->get_cm($newcm->id);
                    return $courserenderer->course_section_cm_list_item($course, $completioninfo, $cm, $sectionreturn) .
                        $courserenderer->course_section_cm_list_item($course, $completioninfo, $newcm, $sectionreturn);
                }
                break;
            case 'groupsseparate':
            case 'groupsvisible':
            case 'groupsnone':
                require_capability('moodle/course:manageactivities', $modcontext);
                if ($action === 'groupsseparate') {
                    $newgroupmode = SEPARATEGROUPS;
                } else if ($action === 'groupsvisible') {
                    $newgroupmode = VISIBLEGROUPS;
                } else {
                    $newgroupmode = NOGROUPS;
                }
                if (set_coursemodule_groupmode($cm->id, $newgroupmode)) {
                    \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();
                }
                break;
            case 'moveleft':
            case 'moveright':
                require_capability('moodle/course:manageactivities', $modcontext);
                $indent = $cm->indent + (($action === 'moveright') ? 1 : -1);
                if ($cm->indent >= 0) {
                    $DB->update_record('course_modules', array('id' => $cm->id, 'indent' => $indent));
                    rebuild_course_cache($cm->course);
                }
                break;
            case 'delete':
                require_capability('moodle/course:manageactivities', $modcontext);
                course_delete_module($cm->id, true);
                return '';
            default:
                throw new coding_exception('Unrecognised action');
        }

        $cm = get_fast_modinfo($course)->get_cm($id);
        return $courserenderer->course_section_cm_list_item($course, $completioninfo, $cm, $sectionreturn);
    }

    /**
     * Return structure for edit_module()
     *
     * @since Moodle 3.3
     * @return external_description
     */
    public static function edit_module_returns() {
        return new external_value(PARAM_RAW, 'html to replace the current module with');
    }

    /**
     * Parameters for function get_module()
     *
     * @since Moodle 3.3
     * @return external_function_parameters
     */
    public static function get_module_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED),
                'sectionreturn' => new external_value(PARAM_INT, 'section to return to', VALUE_DEFAULT, null),
            ));
    }

    /**
     * Returns html for displaying one activity module on course page
     *
     * @since Moodle 3.3
     * @param int $id
     * @param null|int $sectionreturn
     * @return string
     */
    public static function get_module($id, $sectionreturn = null) {
        global $PAGE;
        // Validate and normalize parameters.
        $params = self::validate_parameters(self::get_module_parameters(),
            array('id' => $id, 'sectionreturn' => $sectionreturn));
        $id = $params['id'];
        $sectionreturn = $params['sectionreturn'];

        // Set of permissions an editing user may have.
        $contextarray = [
            'moodle/course:update',
            'moodle/course:manageactivities',
            'moodle/course:activityvisibility',
            'moodle/course:sectionvisibility',
            'moodle/course:movesections',
            'moodle/course:setcurrentsection',
        ];
        $PAGE->set_other_editing_capability($contextarray);

        // Validate access to the course (note, this is html for the course view page, we don't validate access to the module).
        list($course, $cm) = get_course_and_cm_from_cmid($id);
        self::validate_context(context_course::instance($course->id));

        $courserenderer = $PAGE->get_renderer('core', 'course');
        $completioninfo = new completion_info($course);
        return $courserenderer->course_section_cm_list_item($course, $completioninfo, $cm, $sectionreturn);
    }

    /**
     * Return structure for get_module()
     *
     * @since Moodle 3.3
     * @return external_description
     */
    public static function get_module_returns() {
        return new external_value(PARAM_RAW, 'html to replace the current module with');
    }

    /**
     * Parameters for function edit_section()
     *
     * @since Moodle 3.3
     * @return external_function_parameters
     */
    public static function edit_section_parameters() {
        return new external_function_parameters(
            array(
                'action' => new external_value(PARAM_ALPHA, 'action: hide, show, stealth, setmarker, removemarker', VALUE_REQUIRED),
                'id' => new external_value(PARAM_INT, 'course section id', VALUE_REQUIRED),
                'sectionreturn' => new external_value(PARAM_INT, 'section to return to', VALUE_DEFAULT, null),
            ));
    }

    /**
     * Performs one of the edit section actions
     *
     * @since Moodle 3.3
     * @param string $action
     * @param int $id section id
     * @param int $sectionreturn section to return to
     * @return string
     */
    public static function edit_section($action, $id, $sectionreturn) {
        global $DB;
        // Validate and normalize parameters.
        $params = self::validate_parameters(self::edit_section_parameters(),
            array('action' => $action, 'id' => $id, 'sectionreturn' => $sectionreturn));
        $action = $params['action'];
        $id = $params['id'];
        $sr = $params['sectionreturn'];

        $section = $DB->get_record('course_sections', array('id' => $id), '*', MUST_EXIST);
        $coursecontext = context_course::instance($section->course);
        self::validate_context($coursecontext);

        $rv = course_get_format($section->course)->section_action($section, $action, $sectionreturn);
        if ($rv) {
            return json_encode($rv);
        } else {
            return null;
        }
    }

    /**
     * Return structure for edit_section()
     *
     * @since Moodle 3.3
     * @return external_description
     */
    public static function edit_section_returns() {
        return new external_value(PARAM_RAW, 'Additional data for javascript (JSON-encoded string)');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_enrolled_courses_by_timeline_classification_parameters() {
        return new external_function_parameters(
            array(
                'classification' => new external_value(PARAM_ALPHA, 'future, inprogress, or past'),
                'limit' => new external_value(PARAM_INT, 'Result set limit', VALUE_DEFAULT, 0),
                'offset' => new external_value(PARAM_INT, 'Result set offset', VALUE_DEFAULT, 0),
                'sort' => new external_value(PARAM_TEXT, 'Sort string', VALUE_DEFAULT, null),
                'customfieldname' => new external_value(PARAM_ALPHANUMEXT, 'Used when classification = customfield',
                    VALUE_DEFAULT, null),
                'customfieldvalue' => new external_value(PARAM_RAW, 'Used when classification = customfield',
                    VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Get courses matching the given timeline classification.
     *
     * NOTE: The offset applies to the unfiltered full set of courses before the classification
     * filtering is done.
     * E.g.
     * If the user is enrolled in 5 courses:
     * c1, c2, c3, c4, and c5
     * And c4 and c5 are 'future' courses
     *
     * If a request comes in for future courses with an offset of 1 it will mean that
     * c1 is skipped (because the offset applies *before* the classification filtering)
     * and c4 and c5 will be return.
     *
     * @param  string $classification past, inprogress, or future
     * @param  int $limit Result set limit
     * @param  int $offset Offset the full course set before timeline classification is applied
     * @param  string $sort SQL sort string for results
     * @param  string $customfieldname
     * @param  string $customfieldvalue
     * @return array list of courses and warnings
     * @throws  invalid_parameter_exception
     */
    public static function get_enrolled_courses_by_timeline_classification(
        string $classification,
        int $limit = 0,
        int $offset = 0,
        string $sort = null,
        string $customfieldname = null,
        string $customfieldvalue = null
    ) {
        global $CFG, $PAGE, $USER;
        require_once($CFG->dirroot . '/course/lib.php');

        $params = self::validate_parameters(self::get_enrolled_courses_by_timeline_classification_parameters(),
            array(
                'classification' => $classification,
                'limit' => $limit,
                'offset' => $offset,
                'sort' => $sort,
                'customfieldvalue' => $customfieldvalue,
            )
        );

        $classification = $params['classification'];
        $limit = $params['limit'];
        $offset = $params['offset'];
        $sort = $params['sort'];
        $customfieldvalue = $params['customfieldvalue'];

        switch($classification) {
            case COURSE_TIMELINE_ALLINCLUDINGHIDDEN:
                break;
            case COURSE_TIMELINE_ALL:
                break;
            case COURSE_TIMELINE_PAST:
                break;
            case COURSE_TIMELINE_INPROGRESS:
                break;
            case COURSE_TIMELINE_FUTURE:
                break;
            case COURSE_FAVOURITES:
                break;
            case COURSE_TIMELINE_HIDDEN:
                break;
            case COURSE_CUSTOMFIELD:
                break;
            default:
                throw new invalid_parameter_exception('Invalid classification');
        }

        self::validate_context(context_user::instance($USER->id));

        $requiredproperties = course_summary_exporter::define_properties();
        $fields = join(',', array_keys($requiredproperties));
        $hiddencourses = get_hidden_courses_on_timeline();
        $courses = [];

        // If the timeline requires really all courses, get really all courses.
        if ($classification == COURSE_TIMELINE_ALLINCLUDINGHIDDEN) {
            $courses = course_get_enrolled_courses_for_logged_in_user(0, $offset, $sort, $fields, COURSE_DB_QUERY_LIMIT);

            // Otherwise if the timeline requires the hidden courses then restrict the result to only $hiddencourses.
        } else if ($classification == COURSE_TIMELINE_HIDDEN) {
            $courses = course_get_enrolled_courses_for_logged_in_user(0, $offset, $sort, $fields,
                COURSE_DB_QUERY_LIMIT, $hiddencourses);

            // Otherwise get the requested courses and exclude the hidden courses.
        } else {
            $courses = course_get_enrolled_courses_for_logged_in_user(0, $offset, $sort, $fields,
                COURSE_DB_QUERY_LIMIT, [], $hiddencourses);
        }

        $favouritecourseids = [];
        $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($USER->id));
        $favourites = $ufservice->find_favourites_by_type('core_course', 'courses');

        if ($favourites) {
            $favouritecourseids = array_map(
                function($favourite) {
                    return $favourite->itemid;
                }, $favourites);
        }

        if ($classification == COURSE_FAVOURITES) {
            list($filteredcourses, $processedcount) = course_filter_courses_by_favourites(
                $courses,
                $favouritecourseids,
                $limit
            );
        } else if ($classification == COURSE_CUSTOMFIELD) {
            list($filteredcourses, $processedcount) = course_filter_courses_by_customfield(
                $courses,
                $customfieldname,
                $customfieldvalue,
                $limit
            );
        } else {
            list($filteredcourses, $processedcount) = course_filter_courses_by_timeline_classification(
                $courses,
                $classification,
                $limit
            );
        }

        $renderer = $PAGE->get_renderer('core');
        $formattedcourses = array_map(function($course) use ($renderer, $favouritecourseids) {
            context_helper::preload_from_record($course);
            $context = context_course::instance($course->id);
            $isfavourite = false;
            if (in_array($course->id, $favouritecourseids)) {
                $isfavourite = true;
            }
            $exporter = new course_summary_exporter($course, ['context' => $context, 'isfavourite' => $isfavourite]);
            return $exporter->export($renderer);
        }, $filteredcourses);

        return [
            'courses' => $formattedcourses,
            'nextoffset' => $offset + $processedcount
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_enrolled_courses_by_timeline_classification_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(course_summary_exporter::get_read_structure(), 'Course'),
                'nextoffset' => new external_value(PARAM_INT, 'Offset for the next request')
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function set_favourite_courses_parameters() {
        return new external_function_parameters(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'course ID'),
                            'favourite' => new external_value(PARAM_BOOL, 'favourite status')
                        )
                    )
                )
            )
        );
    }

    /**
     * Set the course favourite status for an array of courses.
     *
     * @param  array $courses List with course id's and favourite status.
     * @return array Array with an array of favourite courses.
     */
    public static function set_favourite_courses(
        array $courses
    ) {
        global $USER;

        $params = self::validate_parameters(self::set_favourite_courses_parameters(),
            array(
                'courses' => $courses
            )
        );

        $warnings = [];

        $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($USER->id));

        foreach ($params['courses'] as $course) {

            $warning = [];

            $favouriteexists = $ufservice->favourite_exists('core_course', 'courses', $course['id'],
                    \context_course::instance($course['id']));

            if ($course['favourite']) {
                if (!$favouriteexists) {
                    try {
                        $ufservice->create_favourite('core_course', 'courses', $course['id'],
                                \context_course::instance($course['id']));
                    } catch (Exception $e) {
                        $warning['courseid'] = $course['id'];
                        if ($e instanceof moodle_exception) {
                            $warning['warningcode'] = $e->errorcode;
                        } else {
                            $warning['warningcode'] = $e->getCode();
                        }
                        $warning['message'] = $e->getMessage();
                        $warnings[] = $warning;
                        $warnings[] = $warning;
                    }
                } else {
                    $warning['courseid'] = $course['id'];
                    $warning['warningcode'] = 'coursealreadyfavourited';
                    $warning['message'] = 'Course already favourited';
                    $warnings[] = $warning;
                }
            } else {
                if ($favouriteexists) {
                    try {
                        $ufservice->delete_favourite('core_course', 'courses', $course['id'],
                                \context_course::instance($course['id']));
                    } catch (Exception $e) {
                        $warning['courseid'] = $course['id'];
                        if ($e instanceof moodle_exception) {
                            $warning['warningcode'] = $e->errorcode;
                        } else {
                            $warning['warningcode'] = $e->getCode();
                        }
                        $warning['message'] = $e->getMessage();
                        $warnings[] = $warning;
                        $warnings[] = $warning;
                    }
                } else {
                    $warning['courseid'] = $course['id'];
                    $warning['warningcode'] = 'cannotdeletefavourite';
                    $warning['message'] = 'Could not delete favourite status for course';
                    $warnings[] = $warning;
                }
            }
        }

        return [
            'warnings' => $warnings
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function set_favourite_courses_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.6
     */
    public static function get_recent_courses_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, default to current user', VALUE_DEFAULT, 0),
                'limit' => new external_value(PARAM_INT, 'result set limit', VALUE_DEFAULT, 0),
                'offset' => new external_value(PARAM_INT, 'Result set offset', VALUE_DEFAULT, 0),
                'sort' => new external_value(PARAM_TEXT, 'Sort string', VALUE_DEFAULT, null)
            )
        );
    }

    /**
     * Get last accessed courses adding additional course information like images.
     *
     * @param int $userid User id from which the courses will be obtained
     * @param int $limit Restrict result set to this amount
     * @param int $offset Skip this number of records from the start of the result set
     * @param string|null $sort SQL string for sorting
     * @return array List of courses
     * @throws  invalid_parameter_exception
     */
    public static function get_recent_courses(int $userid = 0, int $limit = 0, int $offset = 0, string $sort = null) {
        global $USER, $PAGE;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $params = self::validate_parameters(self::get_recent_courses_parameters(),
            array(
                'userid' => $userid,
                'limit' => $limit,
                'offset' => $offset,
                'sort' => $sort
            )
        );

        $userid = $params['userid'];
        $limit = $params['limit'];
        $offset = $params['offset'];
        $sort = $params['sort'];

        $usercontext = context_user::instance($userid);

        self::validate_context($usercontext);

        if ($userid != $USER->id and !has_capability('moodle/user:viewdetails', $usercontext)) {
            return array();
        }

        $courses = course_get_recent_courses($userid, $limit, $offset, $sort);

        $renderer = $PAGE->get_renderer('core');

        $recentcourses = array_map(function($course) use ($renderer) {
            context_helper::preload_from_record($course);
            $context = context_course::instance($course->id);
            $isfavourite = !empty($course->component);
            $exporter = new course_summary_exporter($course, ['context' => $context, 'isfavourite' => $isfavourite]);
            return $exporter->export($renderer);
        }, $courses);

        return $recentcourses;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.6
     */
    public static function get_recent_courses_returns() {
        return new external_multiple_structure(course_summary_exporter::get_read_structure(), 'Courses');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_enrolled_users_by_cmid_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'id of the course module', VALUE_REQUIRED),
            'groupid' => new external_value(PARAM_INT, 'id of the group', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Get all users in a course for a given cmid.
     *
     * @param int $cmid Course Module id from which the users will be obtained
     * @param int $groupid Group id from which the users will be obtained
     * @return array List of users
     * @throws invalid_parameter_exception
     */
    public static function get_enrolled_users_by_cmid(int $cmid, int $groupid = 0) {
    global $PAGE;
        $warnings = [];

        [
            'cmid' => $cmid,
            'groupid' => $groupid,
        ] = self::validate_parameters(self::get_enrolled_users_by_cmid_parameters(), [
                'cmid' => $cmid,
                'groupid' => $groupid,
        ]);

        list($course, $cm) = get_course_and_cm_from_cmid($cmid);
        $coursecontext = context_course::instance($course->id);
        self::validate_context($coursecontext);

        $enrolledusers = get_enrolled_users($coursecontext, '', $groupid);

        $users = array_map(function ($user) use ($PAGE) {
            $user->fullname = fullname($user);
            $userpicture = new user_picture($user);
            $userpicture->size = 1;
            $user->profileimage = $userpicture->get_url($PAGE)->out(false);
            return $user;
        }, $enrolledusers);
        sort($users);

        return [
            'users' => $users,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_enrolled_users_by_cmid_returns() {
        return new external_single_structure([
            'users' => new external_multiple_structure(self::user_description()),
            'warnings' => new external_warnings(),
        ]);
    }

    /**
     * Create user return value description.
     *
     * @return external_description
     */
    public static function user_description() {
        $userfields = array(
            'id'    => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            'profileimage' => new external_value(PARAM_URL, 'The location of the users larger image', VALUE_OPTIONAL),
            'fullname' => new external_value(PARAM_TEXT, 'The full name of the user', VALUE_OPTIONAL),
            'firstname'   => new external_value(
                    core_user::get_property_type('firstname'),
                        'The first name(s) of the user',
                        VALUE_OPTIONAL),
            'lastname'    => new external_value(
                    core_user::get_property_type('lastname'),
                        'The family name of the user',
                        VALUE_OPTIONAL),
        );
        return new external_single_structure($userfields);
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function add_content_item_to_user_favourites_parameters() {
        return new external_function_parameters([
            'componentname' => new external_value(PARAM_TEXT,
                'frankenstyle name of the component to which the content item belongs', VALUE_REQUIRED),
            'contentitemid' => new external_value(PARAM_INT, 'id of the content item', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)
        ]);
    }

    /**
     * Add a content item to a user's favourites.
     *
     * @param string $componentname the name of the component from which this content item originates.
     * @param int $contentitemid the id of the content item.
     * @return stdClass the exporter content item.
     */
    public static function add_content_item_to_user_favourites(string $componentname, int $contentitemid) {
        global $USER;

        [
            'componentname' => $componentname,
            'contentitemid' => $contentitemid,
        ] = self::validate_parameters(self::add_content_item_to_user_favourites_parameters(),
            [
                'componentname' => $componentname,
                'contentitemid' => $contentitemid,
            ]
        );

        self::validate_context(context_user::instance($USER->id));

        $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();

        return $contentitemservice->add_to_user_favourites($USER, $componentname, $contentitemid);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function add_content_item_to_user_favourites_returns() {
        return \core_course\local\exporters\course_content_item_exporter::get_read_structure();
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function remove_content_item_from_user_favourites_parameters() {
        return new external_function_parameters([
            'componentname' => new external_value(PARAM_TEXT,
                'frankenstyle name of the component to which the content item belongs', VALUE_REQUIRED),
            'contentitemid' => new external_value(PARAM_INT, 'id of the content item', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Remove a content item from a user's favourites.
     *
     * @param string $componentname the name of the component from which this content item originates.
     * @param int $contentitemid the id of the content item.
     * @return stdClass the exported content item.
     */
    public static function remove_content_item_from_user_favourites(string $componentname, int $contentitemid) {
        global $USER;

        [
            'componentname' => $componentname,
            'contentitemid' => $contentitemid,
        ] = self::validate_parameters(self::remove_content_item_from_user_favourites_parameters(),
            [
                'componentname' => $componentname,
                'contentitemid' => $contentitemid,
            ]
        );

        self::validate_context(context_user::instance($USER->id));

        $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();

        return $contentitemservice->remove_from_user_favourites($USER, $componentname, $contentitemid);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function remove_content_item_from_user_favourites_returns() {
        return \core_course\local\exporters\course_content_item_exporter::get_read_structure();
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_course_content_items_returns() {
        return new external_single_structure([
            'content_items' => new external_multiple_structure(
                \core_course\local\exporters\course_content_item_exporter::get_read_structure()
            ),
        ]);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_content_items_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'ID of the course', VALUE_REQUIRED),
        ]);
    }

    /**
     * Given a course ID fetch all accessible modules for that course
     *
     * @param int $courseid The course we want to fetch the modules for
     * @return array Contains array of modules and their metadata
     */
    public static function get_course_content_items(int $courseid) {
        global $USER;

        [
            'courseid' => $courseid,
        ] = self::validate_parameters(self::get_course_content_items_parameters(), [
            'courseid' => $courseid,
        ]);

        $coursecontext = context_course::instance($courseid);
        self::validate_context($coursecontext);
        $course = get_course($courseid);

        $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();

        $contentitems = $contentitemservice->get_content_items_for_user_in_course($USER, $course);
        return ['content_items' => $contentitems];
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function toggle_activity_recommendation_parameters() {
        return new external_function_parameters([
            'area' => new external_value(PARAM_TEXT, 'The favourite area (itemtype)', VALUE_REQUIRED),
            'id' => new external_value(PARAM_INT, 'id of the activity or whatever', VALUE_REQUIRED),
        ]);
    }

    /**
     * Update the recommendation for an activity item.
     *
     * @param  string $area identifier for this activity.
     * @param  int $id Associated id. This is needed in conjunction with the area to find the recommendation.
     * @return array some warnings or something.
     */
    public static function toggle_activity_recommendation(string $area, int $id): array {
        ['area' => $area, 'id' => $id] = self::validate_parameters(self::toggle_activity_recommendation_parameters(),
                ['area' => $area, 'id' => $id]);

        $context = context_system::instance();
        self::validate_context($context);

        require_capability('moodle/course:recommendactivity', $context);

        $manager = \core_course\local\factory\content_item_service_factory::get_content_item_service();

        $status = $manager->toggle_recommendation($area, $id);
        return ['id' => $id, 'area' => $area, 'status' => $status];
    }

    /**
     * Returns warnings.
     *
     * @return external_description
     */
    public static function toggle_activity_recommendation_returns() {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'id of the activity or whatever'),
                'area' => new external_value(PARAM_TEXT, 'The favourite area (itemtype)'),
                'status' => new external_value(PARAM_BOOL, 'If created or deleted'),
            ]
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_activity_chooser_footer_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'ID of the course', VALUE_REQUIRED),
            'sectionid' => new external_value(PARAM_INT, 'ID of the section', VALUE_REQUIRED),
        ]);
    }

    /**
     * Given a course ID we need to build up a footre for the chooser.
     *
     * @param int $courseid The course we want to fetch the modules for
     * @param int $sectionid The section we want to fetch the modules for
     * @return array
     */
    public static function get_activity_chooser_footer(int $courseid, int $sectionid) {
        [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
        ] = self::validate_parameters(self::get_activity_chooser_footer_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
        ]);

        $coursecontext = context_course::instance($courseid);
        self::validate_context($coursecontext);

        $activeplugin = get_config('core', 'activitychooseractivefooter');

        if ($activeplugin !== COURSE_CHOOSER_FOOTER_NONE) {
            $footerdata = component_callback($activeplugin, 'custom_chooser_footer', [$courseid, $sectionid]);
            return [
                'footer' => true,
                'customfooterjs' => $footerdata->get_footer_js_file(),
                'customfootertemplate' => $footerdata->get_footer_template(),
                'customcarouseltemplate' => $footerdata->get_carousel_template(),
            ];
        } else {
            return [
                'footer' => false,
            ];
        }
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_activity_chooser_footer_returns() {
        return new external_single_structure(
            [
                'footer' => new external_value(PARAM_BOOL, 'Is a footer being return by this request?', VALUE_REQUIRED),
                'customfooterjs' => new external_value(PARAM_RAW, 'The path to the plugin JS file', VALUE_OPTIONAL),
                'customfootertemplate' => new external_value(PARAM_RAW, 'The prerendered footer', VALUE_OPTIONAL),
                'customcarouseltemplate' => new external_value(PARAM_RAW, 'Either "" or the prerendered carousel page',
                    VALUE_OPTIONAL),
            ]
        );
    }
}
