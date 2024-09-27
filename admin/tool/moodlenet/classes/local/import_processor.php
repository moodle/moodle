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
 * Contains the import_processor class.
 *
 * @package tool_moodlenet
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_moodlenet\local;

/**
 * The import_processor class.
 *
 * The import_processor objects provide a means to import a remote resource into a course section, delegating the handling of
 * content to the relevant module, via its dndupload_handler callback.
 *
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_processor {

    /** @var object The course that we are uploading to */
    protected $course = null;

    /** @var int The section number we are uploading to */
    protected $section = null;

    /** @var import_handler_registry $handlerregistry registry object to use for cross checking the supplied handler.*/
    protected $handlerregistry;

    /** @var import_handler_info $handlerinfo information about the module handling the import.*/
    protected $handlerinfo;

    /** @var \stdClass $user the user conducting the import.*/
    protected $user;

    /** @var remote_resource $remoteresource the remote resource being imported.*/
    protected $remoteresource;

    /** @var string[] $descriptionoverrides list of modules which support having their descriptions updated, post-import. */
    protected $descriptionoverrides = ['folder', 'page', 'resource', 'scorm', 'url'];

    /**
     * The import_processor constructor.
     *
     * @param \stdClass $course the course object.
     * @param int $section the section number in the course, starting at 0.
     * @param remote_resource $remoteresource the remote resource to import.
     * @param import_handler_info $handlerinfo information about which module is handling the import.
     * @param import_handler_registry $handlerregistry A registry of import handlers, to use for validation.
     * @throws \coding_exception If any of the params are invalid.
     */
    public function __construct(\stdClass $course, int $section, remote_resource $remoteresource, import_handler_info $handlerinfo,
            import_handler_registry $handlerregistry) {

        global $DB, $USER;

        if ($section < 0) {
            throw new \coding_exception("Invalid section number $section. Must be > 0.");
        }
        if (!$DB->record_exists('modules', array('name' => $handlerinfo->get_module_name()))) {
            throw new \coding_exception("Module {$handlerinfo->get_module_name()} does not exist");
        }

        $this->course = $course;
        $this->section = $section;
        $this->handlerregistry = $handlerregistry;
        $this->user = $USER;
        $this->remoteresource = $remoteresource;
        $this->handlerinfo = $handlerinfo;

        // ALL handlers must have a strategy and ANY strategy can process ANY resource.
        // It is therefore NOT POSSIBLE to have a resource that CANNOT be processed by a handler.
        // So, there's no need to verify that the remote_resource CAN be handled by the handler. It always can.
    }

    /**
     * Run the import process, including file download, module creation and cleanup (cache purge, etc).
     */
    public function process(): void {
        // Allow the strategy to do setup for this file import.
        $moduledata = $this->handlerinfo->get_strategy()->import($this->remoteresource, $this->user, $this->course, $this->section);

        // Create the course module, and add that information to the data to be sent to the plugin handling the resource.
        $cmdata = $this->create_course_module($this->course, $this->section, $this->handlerinfo->get_module_name());
        $moduledata->coursemodule = $cmdata->id;

        // Now, send the data to the handling plugin to let it set up.
        $instanceid = plugin_callback('mod', $this->handlerinfo->get_module_name(), 'dndupload', 'handle', [$moduledata],
            'invalidfunction');
        if ($instanceid == 'invalidfunction') {
            $name = $this->handlerinfo->get_module_name();
            throw new \coding_exception("$name does not support drag and drop upload (missing {$name}_dndupload_handle function)");
        }

        // Now, update the module description if the module supports it and only if it's not currently set.
        $this->update_module_description($instanceid);

        // Finish setting up the course module.
        $this->finish_setup_course_module($instanceid, $cmdata->id);
    }

    /**
     * Update the module's description (intro), if that feature is supported.
     *
     * @param int $instanceid the instance id of the module to update.
     */
    protected function update_module_description(int $instanceid): void {
        global $DB, $CFG;
        require_once($CFG->libdir . '/moodlelib.php');

        if (plugin_supports('mod', $this->handlerinfo->get_module_name(), FEATURE_MOD_INTRO, true)) {
            require_once($CFG->libdir . '/editorlib.php');
            require_once($CFG->libdir . '/modinfolib.php');

            $rec = $DB->get_record($this->handlerinfo->get_module_name(), ['id' => $instanceid]);

            if (empty($rec->intro) || in_array($this->handlerinfo->get_module_name(), $this->descriptionoverrides)) {
                $updatedata = (object)[
                    'id' => $instanceid,
                    'intro' => clean_param($this->remoteresource->get_description(), PARAM_TEXT),
                    'introformat' => editors_get_preferred_format()
                ];

                $DB->update_record($this->handlerinfo->get_module_name(), $updatedata);

                rebuild_course_cache($this->course->id, true);
            }
        }
    }

    /**
     * Create the course module to hold the file/content that has been uploaded.
     * @param \stdClass $course the course object.
     * @param int $section the section.
     * @param string $modname the name of the module, e.g. 'label'.
     * @return \stdClass the course module data.
     */
    protected function create_course_module(\stdClass $course, int $section, string $modname): \stdClass {
        global $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $modname, $section);
        $data->visible = false; // The module is created in a hidden state.
        $data->coursemodule = $data->id = add_course_module($data);
        return $data;
    }

    /**
     * Finish off any course module setup, such as adding to the course section and firing events.
     *
     * @param int $instanceid id returned by the mod when it was created.
     * @param int $cmid the course module record id, for removal if something went wrong.
     */
    protected function finish_setup_course_module($instanceid, int $cmid): void {
        global $DB;

        if (!$instanceid) {
            // Something has gone wrong - undo everything we can.
            course_delete_module($cmid);
            throw new \moodle_exception('errorcreatingactivity', 'moodle', '', $this->handlerinfo->get_module_name());
        }

        // Note the section visibility.
        $visible = get_fast_modinfo($this->course)->get_section_info($this->section)->visible;

        $DB->set_field('course_modules', 'instance', $instanceid, array('id' => $cmid));

        // Rebuild the course cache after update action.
        rebuild_course_cache($this->course->id, true);

        course_add_cm_to_section($this->course, $cmid, $this->section, modname: $this->handlerinfo->get_module_name());

        set_coursemodule_visible($cmid, $visible);
        if (!$visible) {
            $DB->set_field('course_modules', 'visibleold', 1, array('id' => $cmid));
        }

        // Retrieve the final info about this module.
        $info = get_fast_modinfo($this->course, $this->user->id);
        if (!isset($info->cms[$cmid])) {
            // The course module has not been properly created in the course - undo everything.
            course_delete_module($cmid);
            throw new \moodle_exception('errorcreatingactivity', 'moodle', '', $this->handlerinfo->get_module_name());
        }
        $mod = $info->get_cm($cmid);

        // Trigger course module created event.
        $event = \core\event\course_module_created::create_from_cm($mod);
        $event->trigger();
    }
}

