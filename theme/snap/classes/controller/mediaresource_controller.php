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

namespace theme_snap\controller;

/**
 * Deadlines Controller.
 * Handles requests for media elements that can be viewed inline.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mediaresource_controller extends controller_abstract {
    /**
     * Do any security checks needed for the passed action
     *
     * @param string $action
     */
    public function require_capability($action) {
        global $PAGE;

        if (empty($PAGE->cm->id)) {
            throw new \invalid_parameter_exception('Context did not refer to a module');
        }

        $context  = \context_module::instance($PAGE->cm->id);

        switch($action) {
            case 'get_media':
                require_capability('mod/resource:view', $context);
                break;
            default:
                require_capability('mod/resource:view', $context);
        }
    }

    /**
     * Get media html for resource.
     *
     * @param $resource
     * @param $context
     * @param \cm_info $cm
     * @return string
     */
    private function get_media_html($resource, $context, \cm_info $cm) {
        global $OUTPUT;

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
        if (count($files) < 1) {
            $content = $OUTPUT->notification(get_string('filenotfound', 'resource'));
        } else {
            $file = reset($files);
            unset($files);
            $embedoptions = array(
                \core_media_manager::OPTION_TRUSTED => true,
                \core_media_manager::OPTION_BLOCK => true,
            );
            $path = '/'.$context->id.'/mod_resource/content/'.$resource->revision.$file->get_filepath().$file->get_filename();
            $moodleurl = new \moodle_url('/pluginfile.php' . $path);
            $embedhtml = \core_media_manager::instance()->embed_url($moodleurl, $resource->name, 0, 0, $embedoptions);
            // Modal title.
            $content = "<h5 class='snap-lightbox-title'>".format_string($resource->name)."</h5>";

            // Grid me up.
            if (!empty($resource->intro)) {
                $lightboxgrid = "<div class='col-sm-8'>$embedhtml</div>";
                $lightboxgrid .= "<div class='col-sm-4 snap-lightbox-description'>".
                        format_module_intro('resource', $resource, $cm->id)."</div>";
            } else {
                $lightboxgrid = "<div class='col-sm-12'>$embedhtml</div>";
            }
            $content .= "<div class='row'>$lightboxgrid</div>";
        }
        return ($content);
    }

    /**
     * Read media resource
     *
     * @throws \coding_exception
     * @return stdClass
     */
    private function read_media() {
        global $PAGE, $COURSE, $DB;

        $cm = $PAGE->cm;
        $context  = \context_module::instance($cm->id);

        // Trigger module instance viewed event.
        $event = \mod_resource\event\course_module_viewed::create(array(
            'objectid' => $cm->instance,
            'context' => $context,
        ));
        $resource = $DB->get_record('resource', array('id' => $cm->instance));
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $COURSE);
        $event->add_record_snapshot('resource', $resource);
        $event->trigger();

        $resource->content = $this->get_media_html($resource, $context, $cm);

        // Update 'viewed' state if required by completion system.
        $completion = new \completion_info($COURSE);
        $completion->set_module_viewed($cm);
        $renderer = $PAGE->get_renderer('core', 'course');
        $resource->completionhtml = $renderer->snap_course_section_cm_completion($COURSE, $completion, $cm);

        return $resource;
    }

    /**
     * Get the media content.
     *
     * @return string
     */
    public function get_media_action() {
        $media = $this->read_media();

        return json_encode(array(
            'html' => $media->content,
            'completionhtml' => $media->completionhtml,
        ));
    }

}
