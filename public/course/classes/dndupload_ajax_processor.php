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

namespace core_course;

use cm_info;
use core\context\course as context_course;
use core\exception\coding_exception;
use core\exception\moodle_exception;
use core_text;
use course_modinfo;
use navigation_cache;
use repository;
use stdClass;

/**
 * Processes the upload, creating the course module and returning the result
 *
 * @package    core
 * @copyright  2012 Davo Smith
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dndupload_ajax_processor {
    /** Returned when no error has occurred */
    public const ERROR_OK = 0;

    /** @var object The course that we are uploading to */
    protected $course = null;

    /** @var context_course The course context for capability checking */
    protected $context = null;

    /** @var int The section number we are uploading to */
    protected $section = null;

    /** @var string The type of upload (e.g. 'Files', 'text/plain') */
    protected $type = null;

    /** @var object The details of the module type that will be created */
    protected $module = null;

    /** @var object The course module that has been created */
    protected $cm = null;

    /** @var dndupload_handler used to check the allowed file types */
    protected $dnduploadhandler = null;

    /** @var string The name to give the new activity instance */
    protected $displayname = null;

    /**
     * Set up some basic information needed to handle the upload
     *
     * @param int $courseid The ID of the course we are uploading to
     * @param int $section The section number we are uploading to
     * @param string $type The type of upload (as reported by the browser)
     * @param string $modulename The name of the module requested to handle this upload
     */
    public function __construct($courseid, $section, $type, $modulename) {
        global $DB;

        if (!defined('AJAX_SCRIPT')) {
            throw new coding_exception('dndupload_ajax_processor should only be used within AJAX requests');
        }

        $this->course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

        require_login($this->course, false);
        $this->context = context_course::instance($this->course->id);

        if (!is_number($section) || $section < 0) {
            throw new coding_exception("Invalid section number $section");
        }
        $this->section = $section;
        $this->type = $type;

        if (!$this->module = $DB->get_record('modules', ['name' => $modulename])) {
            throw new coding_exception("Module $modulename does not exist");
        }

        $this->dnduploadhandler = new dndupload_handler($this->course);
    }

    /**
     * Check if this upload is a 'file' upload
     *
     * @return bool true if it is a 'file' upload, false otherwise
     */
    protected function is_file_upload() {
        return ($this->type == 'Files');
    }

    /**
     * Process the upload - creating the module in the course and returning the result to the browser
     *
     * @param string $displayname optional the name (from the browser) to give the course module instance
     * @param string $content optional the content of the upload (for non-file uploads)
     */
    public function process($displayname = null, $content = null) {
        require_capability('moodle/course:manageactivities', $this->context);

        if ($this->is_file_upload()) {
            require_capability('moodle/course:managefiles', $this->context);
            if ($content != null) {
                throw new moodle_exception('fileuploadwithcontent', 'moodle');
            }
        } else {
            if (empty($content)) {
                throw new moodle_exception('dnduploadwithoutcontent', 'moodle');
            }
        }

        require_sesskey();

        $this->displayname = $displayname;

        if ($this->is_file_upload()) {
            $this->handle_file_upload();
        } else {
            $this->handle_other_upload($content);
        }
    }

    /**
     * Handle uploads containing files - create the course module, ask the upload repository
     * to process the file, ask the mod to set itself up, then return the result to the browser
     */
    protected function handle_file_upload() {
        global $CFG;

        $this->load_repository();

        // Add the file to a draft file area.
        $draftitemid = file_get_unused_draft_itemid();
        $maxbytes = get_user_max_upload_file_size($this->context, $CFG->maxbytes, $this->course->maxbytes);
        $types = $this->dnduploadhandler->get_handled_file_types($this->module->name);
        $repo = repository::get_instances(['type' => 'upload', 'currentcontext' => $this->context]);
        if (empty($repo)) {
            throw new moodle_exception('errornouploadrepo', 'moodle');
        }
        $repo = reset($repo); // Get the first (and only) upload repo.
        // Pre-emptively purge the navigation cache so the upload repo can close the session.
        navigation_cache::destroy_volatile_caches();
        $details = $repo->process_upload(null, $maxbytes, $types, '/', $draftitemid);
        if (empty($this->displayname)) {
            $this->displayname = $this->display_name_from_file($details['file']);
        }

        // Create a course module to hold the new instance.
        $this->create_course_module();

        // Ask the module to set itself up.
        $moduledata = $this->prepare_module_data($draftitemid);
        $instanceid = plugin_callback('mod', $this->module->name, 'dndupload', 'handle', [$moduledata], 'invalidfunction');
        if ($instanceid === 'invalidfunction') {
            throw new coding_exception(sprintf(
                "%s does not support drag and drop upload (missing %s_dndupload_handle function",
                $this->module->name,
                $this->module->name,
            ));
        }

        // Finish setting up the course module.
        $this->finish_setup_course_module($instanceid);
    }

    /**
     * Handle uploads not containing file - create the course module, ask the mod to
     * set itself up, then return the result to the browser
     *
     * @param string $content the content uploaded to the browser
     */
    protected function handle_other_upload($content) {
        // Check this plugin is registered to handle this type of upload.
        if (!$this->dnduploadhandler->has_type_handler($this->module->name, $this->type)) {
            $info = (object)['modname' => $this->module->name, 'type' => $this->type];
            throw new moodle_exception('moddoesnotsupporttype', 'moodle', $info);
        }

        // Create a course module to hold the new instance.
        $this->create_course_module();

        // Ask the module to set itself up.
        $moduledata = $this->prepare_module_data(null, $content);
        $instanceid = plugin_callback('mod', $this->module->name, 'dndupload', 'handle', [$moduledata], 'invalidfunction');
        if ($instanceid === 'invalidfunction') {
            throw new coding_exception(sprintf(
                "%s does not support drag and drop upload (missing %s_dndupload_handle function",
                $this->module->name,
                $this->module->name,
            ));
        }

        // Finish setting up the course module.
        $this->finish_setup_course_module($instanceid);
    }

    /**
     * Generate the name of the mod instance from the name of the file
     * (remove the extension and convert underscore => space
     *
     * @param string $filename the filename of the uploaded file
     * @return string the display name to use
     */
    protected function display_name_from_file($filename) {
        $pos = core_text::strrpos($filename, '.');
        if ($pos) { // Want to skip if $pos === 0 OR $pos === false.
            $filename = core_text::substr($filename, 0, $pos);
        }
        return str_replace('_', ' ', $filename);
    }

    /**
     * Create the coursemodule to hold the file/content that has been uploaded
     */
    protected function create_course_module() {
        global $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');
        [$module, $context, $cw, $cm, $data] = prepare_new_moduleinfo_data($this->course, $this->module->name, $this->section);

        $data->coursemodule = $data->id = add_course_module($data);
        $this->cm = $data;
    }

    /**
     * Gather together all the details to pass on to the mod, so that it can initialise it's
     * own database tables
     *
     * @param int $draftitemid optional the id of the draft area containing the file (for file uploads)
     * @param string $content optional the content dropped onto the course (for non-file uploads)
     * @return object data to pass on to the mod, containing:
     *              string $type the 'type' as registered with dndupload_handler (or 'Files')
     *              object $course the course the upload was for
     *              int $draftitemid optional the id of the draft area containing the files
     *              int $coursemodule id of the course module that has already been created
     *              string $displayname the name to use for this activity (can be overriden by the mod)
     */
    protected function prepare_module_data($draftitemid = null, $content = null) {
        $data = new stdClass();
        $data->type = $this->type;
        $data->course = $this->course;
        if ($draftitemid) {
            $data->draftitemid = $draftitemid;
        } else if ($content) {
            $data->content = $content;
        }
        $data->coursemodule = $this->cm->id;
        $data->displayname = $this->displayname;
        return $data;
    }

    /**
     * Called after the mod has set itself up, to finish off any course module settings
     * (set instance id, add to correct section, set visibility, etc.) and send the response
     *
     * @param int $instanceid id returned by the mod when it was created
     */
    protected function finish_setup_course_module($instanceid) {
        global $DB, $USER;

        if (!$instanceid) {
            // Something has gone wrong - undo everything we can.
            \core_courseformat\formatactions::cm($this->course->id)->delete($this->cm->id);
            throw new moodle_exception('errorcreatingactivity', 'moodle', '', $this->module->name);
        }

        // Note the section visibility.
        $visible = get_fast_modinfo($this->course)->get_section_info($this->section)->visible;

        $DB->set_field('course_modules', 'instance', $instanceid, ['id' => $this->cm->id]);

        course_modinfo::purge_course_module_cache($this->course->id, $this->cm->id);
        // Rebuild the course cache after update action.
        rebuild_course_cache($this->course->id, true, true);

        $sectionid = course_add_cm_to_section($this->course, $this->cm->id, $this->section, modname: $this->module->name);

        set_coursemodule_visible($this->cm->id, $visible);
        if (!$visible) {
            $DB->set_field('course_modules', 'visibleold', 1, ['id' => $this->cm->id]);
        }

        // Retrieve the final info about this module.
        $info = get_fast_modinfo($this->course);
        if (!isset($info->cms[$this->cm->id])) {
            // The course module has not been properly created in the course - undo everything.
            \core_courseformat\formatactions::cm($this->course->id)->delete($this->cm->id);
            throw new moodle_exception('errorcreatingactivity', 'moodle', '', $this->module->name);
        }
        $mod = $info->get_cm($this->cm->id);

        // Trigger course module created event.
        $event = \core\event\course_module_created::create_from_cm($mod);
        $event->trigger();

        $this->send_response($mod);
    }

    /**
     * Send the details of the newly created activity back to the client browser
     *
     * @param cm_info $mod details of the mod just created
     */
    protected function send_response($mod) {
        global $OUTPUT, $PAGE;

        $resp = new stdClass();
        $resp->error = self::ERROR_OK;
        $resp->elementid = 'module-' . $mod->id;
        $resp->cmid = $mod->id;

        $format = course_get_format($this->course);
        $renderer = $format->get_renderer($PAGE);
        $modinfo = $format->get_modinfo();
        $section = $modinfo->get_section_info($mod->sectionnum);

        // Get the new element html content.
        $resp->fullcontent = $renderer->course_section_updated_cm_item($format, $section, $mod);

        echo $OUTPUT->header();
        echo json_encode($resp);
        die();
    }

    /**
     * Load the repository libraries.
     */
    private function load_repository(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/repository/lib.php");
    }

}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(dndupload_ajax_processor::class, \dndupload_ajax_processor::class);
