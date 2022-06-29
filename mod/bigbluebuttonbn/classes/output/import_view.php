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

namespace mod_bigbluebuttonbn\output;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\local\helpers\roles;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderable for the import page.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Darko Miletic  (darko.miletic [at] gmail [dt] com)
 */
class import_view implements renderable, templatable {
    /**
     * @var instance $destinationinstance
     */
    protected $destinationinstance;

    /**
     * @var int|null $sourceinstanceid the source instance id or null if it is not yet set.
     */
    protected $sourceinstanceid;

    /**
     * @var int|null $sourcecourseid the source instance id or null if it is not yet set.
     */
    protected $sourcecourseid;

    /**
     * import_view constructor.
     *
     * @param instance $destinationinstance
     * @param int $sourcecourseid
     * @param int $sourceinstanceid
     */
    public function __construct(instance $destinationinstance, int $sourcecourseid, int $sourceinstanceid) {
        $this->destinationinstance = $destinationinstance;
        $this->sourcecourseid = $sourcecourseid >= 0 ? $sourcecourseid : null;
        $this->sourceinstanceid = $sourceinstanceid >= 0 ? $sourceinstanceid : null;
    }

    /**
     * Defer to template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $courses = roles::import_get_courses_for_select($this->destinationinstance);
        if (config::get('importrecordings_from_deleted_enabled')) {
            $courses[0] = get_string('recordings_from_deleted_activities', 'mod_bigbluebuttonbn');
            ksort($courses);
        }
        $context = (object) [
            'bbbid' => $this->destinationinstance->get_instance_id(),
            'has_recordings' => true,
            'bbbsourceid' => 0
        ];

        if (!empty($this->sourceinstanceid)) {
            $context->sourceid = $this->sourceinstanceid;
            $context->search = [
                'value' => ''
            ];
            $sourceinstance = instance::get_from_instanceid($this->sourceinstanceid);
            if ($sourceinstance->is_type_room_only()) {
                $context->has_recordings = false;
            }
            $context->bbbsourceid = $sourceinstance->get_instance_id();
        }

        // Now the selects.
        if (!empty($this->sourcecourseid)) {
            $selectrecords = [];

            $cms = get_fast_modinfo($this->sourcecourseid)->instances['bigbluebuttonbn'];
            foreach ($cms as $cm) {
                if ($cm->id == $this->destinationinstance->get_cm_id()) {
                    // Skip the target instance.
                    continue;
                }

                if ($cm->deletioninprogress) {
                    // Check if the BBB is not currently scheduled for deletion.
                    continue;
                }

                $selectrecords[$cm->instance] = $cm->name;
            }
            if (config::get('importrecordings_from_deleted_enabled')) {
                $selectrecords[0] =
                    get_string('recordings_from_deleted_activities', 'mod_bigbluebuttonbn');
            }
            $actionurl = $this->destinationinstance->get_import_url();
            $actionurl->param('sourcecourseid', $this->sourcecourseid);

            $select = new \single_select(
                $actionurl,
                'sourcebn',
                $selectrecords,
                $this->sourceinstanceid ?? ""
            );
            $context->bbb_select = $select->export_for_template($output);
        }
        $context->sourcecourseid = $this->sourcecourseid ?? 0;

        // Course selector.
        $context->course_select = (new \single_select(
            $this->destinationinstance->get_import_url(),
            'sourcecourseid',
            $courses,
            $this->sourcecourseid ?? ""
        ))->export_for_template($output);

        if (!is_null($this->sourcecourseid)) {
            $context->has_selected_course = true;
        }

        // Back button.
        $context->back_button = (new \single_button(
            $this->destinationinstance->get_view_url(),
            get_string('view_recording_button_return', 'mod_bigbluebuttonbn')
        ))->export_for_template($output);

        return $context;
    }
}
