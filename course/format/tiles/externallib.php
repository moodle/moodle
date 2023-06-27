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
 * Format tiles external API
 *
 * @package    format_tiles
 * @copyright  2018 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use format_tiles\tile_photo;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/course/format/tiles/locallib.php');

/**
 * Format tiles external functions
 *
 * @package    format_tiles
 * @category   external
 * @copyright  2018 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class format_tiles_external extends external_api
{
    /**
     * Teacher is changing the icon for a course section or whole course using AJAX
     * @param Integer $courseid the id of this course
     * @param Integer $sectionid the number of the section in this course - zero if whole course
     * @param String $filename the icon filename or photo filename for this tile.
     * @param string $imagetype whether it's a tile icon or a background photo.
     * @param int $sourcecontextid the context id of the source photo or icon.
     * @param int $sourceitemid the item id of the course photo or icon.
     * @return [] status and image URL if applicable.
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public static function set_image(
        $courseid, $sectionid, $filename, $imagetype = 'tileicon', $sourcecontextid = 0, $sourceitemid = 0
    ) {
        global $DB;

        $data = self::validate_parameters(self::set_image_parameters(),
            array(
                'courseid' => $courseid,
                'sectionid' => $sectionid,
                'image' => $filename,
                'sourcecontextid' => $sourcecontextid,
                'sourceitemid' => $sourceitemid,
                'imagetype' => $imagetype
            )
        );

        // Section id of zero means we are changing the course icon.  Otherwise check sec id is valid.
        if ($data['sectionid'] !== 0 && $DB->get_record('course_sections',
                array('course' => $data['courseid'], 'id' => $data['sectionid'])) === false) {
            throw new invalid_parameter_exception('Invalid course and section id combination');
        }

        $context = context_course::instance($data['courseid']);
        self::validate_context($context);
        require_capability('moodle/course:viewhiddenactivities', $context); // This allows non-editing teachers for the course.

        switch ($data['imagetype']) {
            case 'tileicon':
                $result = self::set_tile_icon($data);
                break;
            case 'tilephoto':
                if (!get_config('format_tiles', 'allowphototiles')) {
                    throw new invalid_parameter_exception("Photo tiles are disabled by site admin");
                }
                $result = self::set_tile_photo($data);
                break;
            case 'draftfile':
                $result = self::set_tile_photo_from_draftfile($data);
                break;
            default:
                throw new invalid_parameter_exception('Image type is invalid ' . $data['imagetype']);
        }
        return $result;
    }

    /**
     * Given a draft file uploaded by user, save top this plugin's file area.
     * @param [] $data
     * @return array
     * @throws dml_exception
     * @throws file_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     * @throws stored_file_creation_exception
     */
    private static function set_tile_photo_from_draftfile($data) {
        if (!$data['sourcecontextid'] || !$data['sourceitemid']) {
            throw new invalid_parameter_exception("Invalid source context id or source item id");
        }
        $tilephoto = new tile_photo($data['courseid'], $data['sectionid']);
        $fs = get_file_storage();
        $sourcefile = $fs->get_file(
            $data['sourcecontextid'],
            'user',
            'draft',
            $data['sourceitemid'],
            '/',
            $data['image']
        );
        $newfile = $tilephoto->set_file_from_stored_file($sourcefile, $data['image']);
        if ($newfile) {
            return array(
                'status' => true,
                'imageurl' => $tilephoto->get_image_url()
            );
        } else {
            return array(
                'status' => false,
                'imageurl' => ''
            );
        }
    }

    /**
     * Given the data describing the photo we want and the tile to apply it to, set the tile to use that photo.
     * @param [] $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws file_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     * @throws stored_file_creation_exception
     */
    private static function set_tile_photo($data) {
        $sourcecontext = context::instance_by_id($data['sourcecontextid']);
        $issettingsampleimage =
            $sourcecontext->contextlevel == CONTEXT_SYSTEM && $data['sourceitemid'] == 0 & $data['image'] == 'sample_image.jpg';

        if (!$data['sourcecontextid'] || (!$data['sourceitemid'] && !$issettingsampleimage)) {
            throw new invalid_parameter_exception("Invalid source context id or source item id");
        }

        if ($sourcecontext->contextlevel !== CONTEXT_COURSE && !$issettingsampleimage) {
            throw new InvalidArgumentException("Invalid context level");
        }

        if ($data['sourcecontextid'] &&!$issettingsampleimage) {
            // Arguably we don't need to do this as the only files the user will see are those they posted themselves.
            // This is thanks to the database query which generates the files list. So they could see them once.
            require_capability('moodle/course:viewhiddenactivities', $sourcecontext);
        }
        $courseid = $sourcecontext->instanceid;
        if ($issettingsampleimage) {
            $sourcefile = tile_photo::get_sample_image_file();
        } else {
            $sourcephoto = new tile_photo($courseid, $data['sourceitemid']);
            $sourcefile = $sourcephoto->get_file();
        }

        $tilephoto = new tile_photo($data['courseid'], $data['sectionid']);
        $file = $tilephoto->set_file_from_stored_file($sourcefile, $data['image']);
        if ($file) {
            return array(
                'status' => true,
                'imageurl' => $tilephoto->get_image_url()
            );
        } else {
            return array(
                'status' => false,
                'imageurl' => ''
            );
        }
    }

    /**
     * Given the data describing the icon we want and the tile to apply it to, set the tile to use that icon
     * @param [] $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    private static function set_tile_icon($data) {
        global $DB;
        $availableicons = (new \format_tiles\icon_set)->available_tile_icons($data['courseid']);
        if (!isset($availableicons[$data['image']])) {
            throw new invalid_parameter_exception('Icon is invalid');
        }

        if ($data['sectionid'] === 0) {
            $optionname = 'defaulttileicon'; // All default icon for whole course.
        } else {
            $optionname = 'tileicon'; // Icon for just this tile.
        }

        $existingicon = $DB->get_record(
            'course_format_options',
            ['courseid' => $data['courseid'], 'format' => 'tiles', 'sectionid' => $data['sectionid'], 'name' => $optionname]
        );
        if (!isset($existingicon->value)) {
            // No icon is presently stored for this so we need to insert new record.
            $record = new stdClass();
            $record->courseid = $data['courseid'];
            $record->format = 'tiles';
            $record->sectionid = $data['sectionid'];
            $record->name = $optionname;
            $record->value = $data['image'];
            $result = $DB->insert_record('course_format_options', $record);
        } else if ($data['sectionid'] != 0) {
            // We are dealing with a tile icon for one particular section, so check if user has picked the course default.
            $defaulticonthiscourse = $DB->get_record(
                'course_format_options',
                ['courseid' => $data['courseid'], 'format' => 'tiles', 'sectionid' => 0, 'name' => 'defaulttileicon']
            )->value;
            if ($data['image'] == $defaulticonthiscourse) {
                // Using default icon for a tile do don't store anything in database = default.
                $result = $DB->delete_records(
                    'course_format_options',
                    ['courseid' => $data['courseid'], 'format' => 'tiles', 'sectionid' => $data['sectionid'], 'name' => 'tileicon']
                );
            } else {
                // User has not picked default and there is an existing record so update it.
                $existingicon->value = $data['image'];
                $result = $DB->update_record('course_format_options', $existingicon);
            }
        } else {
            // Updating existing course icon record.
            $existingicon->value = $data['image'];
            $result = $DB->update_record('course_format_options', $existingicon);
        }

        if ($data['sectionid'] !== 0) {
            // If there is a tile photo attached to this tile, clear it.
            $tilephoto = new tile_photo($data['courseid'], $data['sectionid']);
            $tilephoto->clear();
        }
        return  array(
            'status' => $result ? true : false,
            'imageurl' => ''
        );
    }

    /**
     * Returns description of get_instance_info() parameters.
     *
     * @return external_function_parameters
     */
    public static function set_image_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id whose icon/image we are setting'),
                'sectionid' => new external_value(
                    PARAM_INT,
                    'Section id whose icon/image we are setting (zero means whole course not just one section)'
                ),
                'image' => new external_value(PARAM_RAW, 'File name for the image picked'),
                'imagetype' => new external_value(PARAM_RAW, 'Image type for image picked (tileicon, tilephoto, draftfile)'),
                'sourcecontextid' => new external_value(
                    PARAM_INT, 'File table context id for the photo file picked (0 if unused)', VALUE_DEFAULT, 0
                ),
                'sourceitemid' => new external_value(
                    PARAM_INT, 'File table item id for the photo file picked (0 if unused)', VALUE_DEFAULT, 0
                )
            )
        );
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function set_image_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'Whether the image was set'),
            'imageurl' => new external_value(PARAM_RAW, 'Image URL if background photo set (not used for icons)'),
        ));
    }

    /**
     * Get the HTML for a single section page for a course
     * (i.e. the list of activities and resources comprising the contents of a tile)
     * Intended to be called from AJAX so that the result can be added to the multi
     * tiles page by JS
     *
     * The method returns the HTML rather than the underlying course data to save making
     * another round trip to the server to render the HTML from the data, via the mustache
     * template. This would have been another way of doing it, and would be easy to achieve
     * by calling the template from JS.
     *
     * @param int $courseid
     * @param int $sectionid we want to display
     * @param boolean $setjsusedsession whether to set the session jsenabled flag to true
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_single_section_page_html($courseid, $sectionid, $setjsusedsession = false) {
        global $PAGE, $SESSION;
        $params = self::validate_parameters(
            self::get_single_section_page_html_parameters(),
            array(
                'courseid' => $courseid,
                'sectionid' => $sectionid,
                'setjsusedsession' => $setjsusedsession
            )
        );

        // Request and permission validation.
        // Ensure user has access to course context.
        // validate_context() below ends up calling require_login($courseid).
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);

        $course = get_course($params['courseid']);
        $renderer = $PAGE->get_renderer('format_tiles');
        $templateable = new \format_tiles\output\course_output($course, true, $params['sectionid']);
        $data = $templateable->export_for_template($renderer);
        $template = $params['sectionid'] == 0 ? 'format_tiles/section_zero' : 'format_tiles/single_section';
        $result = array(
            'html' => $renderer->render_from_template($template, $data)
        );
        // This session var is used later, when user revisits main course page, or a single section, for a course using this format.
        // If set to true, the page can safely be rendered from PHP in the javascript friendly format.
        // (A <noscript> box will be displayed only to users who have JS disabled with a link to switch to non JS format).
        if ($params['setjsusedsession']) {
            $SESSION->format_tiles_jssuccessfullyused = 1;
        }
        return $result;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_single_section_page_html_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'sectionid' => new external_value(PARAM_INT, 'Section id'),
                'setjsusedsession' => new external_value(
                    PARAM_BOOL,
                    'Whether to set the session flag for JS successfully used',
                    VALUE_DEFAULT,
                    0,
                    true
                )
            )
        );
    }

    /**
     *
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_single_section_page_html_returns () {
        return new external_single_structure(
            array(
                'html' => new external_value(PARAM_RAW, 'HTML for the single section (tile contents)')
            )
        );
    }

    /**
     * Get the HTML for a single page for display in a modal window
     * @param int $courseid
     * @param int $cmid we want to display
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_mod_page_html($courseid, $cmid) {
        global $DB, $PAGE;
        $params = self::validate_parameters(
            self::get_mod_page_html_parameters(),
            array('courseid' => $courseid, 'cmid' => $cmid)
        );
        // Request and permission validation.
        $modcontext = context_module::instance($params['cmid']);
        self::validate_context($modcontext);

        $result = array('status' => false, 'warnings' => [], 'html' => '');
        $mod = get_fast_modinfo($params['courseid'])->get_cm($params['cmid']);
        require_capability('mod/' . $mod->modname . ':view', $modcontext);
        if ($mod && $mod->uservisible) {
            if (array_search($mod->modname, explode(",", get_config('format_tiles', 'modalmodules'))) === false) {
                throw new invalid_parameter_exception('Not allowed to call this mod type - disabled by site admin');
            }
            if ($mod->modname == 'page') {
                // Record from the page table.
                $record = $DB->get_record($mod->modname, array('id' => $mod->instance), 'intro, content, revision, contentformat');
                $renderer = $PAGE->get_renderer('format_tiles');
                $content = $renderer->format_cm_content_text($mod, $record, $modcontext);
                $result['status'] = true;
                $result['html'] = $content;
                return $result;
            } else {
                throw new invalid_parameter_exception('Only page modules allowed through this service');
            }
        } else {
            $result['status'] = false;
            $result['html'] = '';
            $result['warnings'][] = 'Course module is not available';
        }
        return $result;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_mod_page_html_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'cmid' => new external_value(PARAM_INT, 'Course module id'),
            )
        );
    }

    /**
     *
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_mod_page_html_returns () {
        return new external_single_structure(
            array(
                'html' => new external_value(PARAM_RAW, 'HTML for the course module')
            )
        );
    }

    /**
     * Log that fact that the user clicked a tile
     * @param int $courseid
     * @param int $sectionid we are viewing
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function log_tile_click($courseid, $sectionid) {
        $params = self::validate_parameters(
            self::log_tile_click_parameters(),
            array('courseid' => $courseid, 'sectionid' => $sectionid)
        );
        // Request and permission validation.
        $coursecontext = context_course::instance($params['courseid']);
        self::validate_context($coursecontext);

        course_view(context_course::instance($courseid), $sectionid);
        return array('status' => true, 'warnings' => []);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function log_tile_click_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'sectionid' => new external_value(PARAM_INT, 'Section id viewed', VALUE_DEFAULT, 0, true),
            )
        );
    }

    /**
     *
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function log_tile_click_returns () {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success')
            )
        );
    }

    /**
     * Simulate the resource/view.php and page/view.php etc logging when caleld from AJAX
     *
     * This is a re-implementation of the core service only required because the core
     * version is not callable from AJAX
     * @see mod_resource_external::log_resource_view() for example
     * @param int $courseid the course id where the module is
     * @param int $cmid the resource module instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function log_mod_view($courseid, $cmid) {
        global $DB, $USER;
        $params = self::validate_parameters(
            self::log_mod_view_parameters(),
            array(
                'courseid' => $courseid,
                'cmid' => $cmid
            )
        );
        list($course, $cm) = get_course_and_cm_from_cmid($params['cmid'], '', $params['courseid']);

        // Request and permission validation.
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/' . $cm->modname . ':view', $context);

        $allowedmodalmodules  = format_tiles_allowed_modal_modules();
        if (array_search($cm->modname, $allowedmodalmodules['modules']) === false
            && count($allowedmodalmodules['resources']) == 0) {
            throw new invalid_parameter_exception(
                'Not allowed to log views of this mod type - disabled by site admin or incorrect device type.'
            . ' If you are testing this may be because you have not refreshed since switching device types');
        }
        $modobject = $DB->get_record($cm->modname, array('id' => $cm->instance), '*', MUST_EXIST);

        // Trigger course_module_viewed event.
        switch ($cm->modname) {
            case 'page':
                page_view($modobject, $course, $cm, $context);
                break;
            case 'resource':
                resource_view($modobject, $course, $cm, $context);
                break;
            case 'url':
                url_view($modobject, $course, $cm, $context);
                break;
            default:
                throw new invalid_parameter_exception('No logging method provided for type |' . $cm->modname . '|');
            // TODO add more to these if more modules added.
        }

        // If this item is using automatic completion, mark the item as complete.
        $completion = new completion_info($course);
        if ($completion->is_enabled() && $cm->completion == COMPLETION_TRACKING_AUTOMATIC) {
            $completion->update_state($cm, COMPLETION_COMPLETE, $USER->id);
        }

        $result = array();
        $result['status'] = true;
        return $result;
    }

    /**
     * Simulate the resource/view.php web interface page: trigger events, completion, etc...
     *
     * This is a re-implementation of the core service, only required because the core
     * version is not callable from AJAX
     * @see mod_resource_external::log_resource_view_parameters()
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function log_mod_view_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'cmid' => new external_value(PARAM_INT, 'course module id')
            )
        );
    }

    /**
     *
     * Returns description of method result value
     *
     * This is a re-implementation of the core service only required because the core
     * version is not callable from AJAX
     * @see mod_resource_external::log_resource_view_returns()
     * @return external_description
     * @since Moodle 3.0
     */
    public static function log_mod_view_returns () {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success')
            )
        );
    }


    /**
     * Get the available icon set
     * @param int $courseid
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_icon_set($courseid) {
        $params = self::validate_parameters(
            self::get_icon_set_parameters(),
            array('courseid' => $courseid)
        );
        // Request and permission validation.
        // Note course id could be zero if creating new course.

        if ($params['courseid'] != 0) {
            $context = context_course::instance($params['courseid']);
        } else {
            $context = context_coursecat::instance(optional_param('category', 0, PARAM_INT));
        }
        self::validate_context($context);
        if (!has_capability('moodle/course:update', $context) && !has_capability('moodle/course:create', $context)) {
            if (!has_capability('moodle/course:update', $context)) {
                throw new required_capability_exception(
                    $context,
                    'moodle/course:update',
                    "nopermissions",
                    ""
                );
            } else {
                throw new required_capability_exception(
                    $context,
                    'moodle/course:create',
                    "nopermissions",
                    ""
                );
            }
        }
        $data = array(
            'status' => true,
            'warnings' => [],
            'icons' => json_encode((new \format_tiles\icon_set)->available_tile_icons($courseid)),
            'photos' => ''
        );
        if (get_config('format_tiles', 'allowphototiles')) {
            $data['photos'] = json_encode(tile_photo::get_photo_library_photos($context->id));
        }
        return $data;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_icon_set_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
            )
        );
    }

    /**
     *
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function get_icon_set_returns () {
        return new external_single_structure(
            array(
                'icons' => new external_value(PARAM_RAW, 'Icon set available for use on tile icons (JSON array)'),
                'photos' => new external_value(PARAM_RAW, 'Recent photos set for teacher photo library (JSON array)'),
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Set the result of the JS calculation of the optimal width of the main tiles window for a course.
     * This has to be by course as they have different numbers of tiles.
     * We can then use this to render the page from PHP at the correct width initially next time.
     * @param int $courseid the course id we are in
     * @param int $width the JS calculated width
     * @see format_tiles_width_template_data() for where this is used.
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function set_session_width($courseid, $width) {
        global $SESSION;
        $params = self::validate_parameters(
            self::set_session_width_parameters(),
            array('courseid' => $courseid, 'width' => $width)
        );
        // Request and permission validation - validate_context() includes require_login() check.
        $coursecontext = context_course::instance($params['courseid']);
        self::validate_context($coursecontext);
        $sessionvar = 'format_tiles_width_' . $params['courseid'];

        if (!get_config('format_tiles', 'fittilestowidth')) {
            throw new invalid_parameter_exception("Setting tiles width is disabled by site admin");
        }

        if ($params['width'] < 300 || $params['width'] > 3000) {
            // Value passed is out of bounds, so unset as something has gone wrong.
            unset($SESSION->$sessionvar);
            return array('status' => false, 'warnings' => ['Session width out bounds']);
        }

        $SESSION->$sessionvar = $params['width'];
        return array('status' => true, 'warnings' => []);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function set_session_width_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'width' => new external_value(
                    PARAM_INT,
                    'The JS calculated width optimal width for tiles window (used to render from PHP next time)',
                    VALUE_DEFAULT,
                    0,
                    true
                ),
            )
        );
    }

    /**
     *
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function set_session_width_returns () {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success')
            )
        );
    }

    /**
     * Return some information about a section or a set of sections in a course.
     * This may be called as a user progresses through course activities (with course completion).
     * The data provided enable the tiles to be updated client side with progress info ana availability.
     * @param int $courseid
     * @param array $sectionnums
     * @return array of warnings and status result
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     * @since Moodle 3.8
     */
    public static function get_section_information($courseid, $sectionnums) {
        global $PAGE;
        $params = self::validate_parameters(
            self::get_section_information_parameters(),
            array(
                'courseid' => $courseid,
                'sectionnums' => $sectionnums,
            )
        );

        // Request and permission validation.
        // Ensure user has access to course context.
        // validate_context() below ends up calling require_login($courseid).
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);

        $sections = [];
        $warnings = [];

        $course = get_course($params['courseid']);
        $modinfo = get_fast_modinfo($course);
        $sectioninfo = $modinfo->get_section_info_all();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);
        $renderer = $PAGE->get_renderer('format_tiles');
        $templateable = new \format_tiles\output\course_output($course, true);
        $showprogressaspercent = $templateable->courseformatoptions['courseshowtileprogress'] == 2;
        $overall = ['complete' => 0, 'outof' => 0];
        // First add the info about the section and its availability.
        foreach ($sectionnums as $sectionnum) {
            if (isset($sectioninfo[$sectionnum]) && ($sectioninfo[$sectionnum]->visible || $canviewhidden)) {
                $section = $sectioninfo[$sectionnum];
                $sections[$sectionnum] = array(
                    'sectionid' => $section->id,
                    'sectionnum' => $sectionnum,
                    'isavailable' => $section->available,
                    'isclickable' => $section->available || $section->uservisible,
                    'availabilitymessage' => $renderer->section_availability_message($section, $canviewhidden),
                    'numcomplete' => -1, // If we have data, we replace this below.
                    'numoutof' => -1 // If we have data, we replace this below.
                );
            } else {
                $warnings[] = array(
                    'item' => $sectionnum,
                    'warningcode' => 'errorrequestnotfound',
                    'message' => 'No section information available to user for section number ' . $sectionnum
                );
            }
        }

        // Next, if completion is enabled, add info about this user's progress.
        $completionenabled = $course->enablecompletion && !isguestuser();
        if ($completionenabled) {
            foreach ($sections as $section) {
                if (isset($modinfo->sections[$section['sectionnum']])) {
                    $completionthistile = $templateable->section_progress(
                        $modinfo->sections[$section['sectionnum']],
                        $modinfo->cms
                    );
                } else {
                    $completionthistile = ['completed' => 0, 'outof' => 0];
                }
                $completiondata = $templateable->completion_indicator(
                    $completionthistile['completed'],
                    $completionthistile['outof'],
                    $showprogressaspercent,
                    false
                );
                foreach ($completiondata as $k => $v) {
                    // Add percent, percentcircumf, percentoffset, issingledigit.
                    $sections[$section['sectionnum']][strtolower($k)] = $v;
                }
                $overall['complete'] += $completionthistile['completed'];
                $overall['outof'] += $completionthistile['outof'];
            }
        }
        return array(
            'sections' => array_values($sections),
            'overall' => $overall,
            'status' => true,
            'warnings' => $warnings
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.8
     */
    public static function get_section_information_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'sectionnums' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Section number to get info for', VALUE_REQUIRED, null, NULL_ALLOWED),
                    'Section numbers in this course to get info for',
                    VALUE_REQUIRED,
                    []
                ),
            )
        );
    }

    /**
     *
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.8
     */
    public static function get_section_information_returns() {
        return new external_single_structure(
            array(
                'sections' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'sectionid' => new external_value(PARAM_INT, 'Section id'),
                            'sectionnum' => new external_value(PARAM_INT, 'Section number in course'),
                            'numcomplete' => new external_value(
                                PARAM_INT,
                                'Number of activities completed in this section by this user'
                            ),
                            'numoutof' => new external_value(
                                PARAM_INT,
                                'Number of possible activities in this section for this user'
                            ),
                            'percent' => new external_value(PARAM_INT, 'Percent complete', VALUE_OPTIONAL, 0),
                            'percentcircumf' => new external_value(
                                PARAM_FLOAT, 'Circumference of radial indicator', VALUE_OPTIONAL, 0
                            ),
                            'percentoffset' => new external_value(
                                PARAM_INT, 'Percent offset for radial indicator'. VALUE_OPTIONAL, 0
                            ),
                            'iscomplete' => new external_value(PARAM_BOOL, 'Is the section complete'. VALUE_OPTIONAL, false),
                            'isavailable' => new external_value(PARAM_BOOL, 'Is the section available (not restricted)'),
                            'isclickable' => new external_value(PARAM_BOOL, 'Is the section clickable / expandable'),
                            'availabilitymessage' => new external_value(PARAM_RAW, 'If the section is restricted, explains why')
                        )
                    )
                ),
                'overall' => new external_single_structure(
                    array(
                        'complete' => new external_value(PARAM_INT, 'How many activities complete overall'),
                        'outof' => new external_value(PARAM_INT, 'How many activities out of overall'),
                    )
                ),
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }
}
