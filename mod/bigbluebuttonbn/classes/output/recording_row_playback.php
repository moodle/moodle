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
use mod_bigbluebuttonbn\local\bigbluebutton\recordings\recording_data;
use mod_bigbluebuttonbn\local\helpers\roles;
use mod_bigbluebuttonbn\recording;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderer for recording row playback column
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent.david [at] call-learning [dt] fr)
 */
class recording_row_playback implements renderable, templatable {

    /**
     * @var $instance
     */
    protected $instance;

    /**
     * @var $recording
     */
    protected $recording;

    /**
     * recording_row_playback constructor.
     *
     * @param recording $rec
     * @param instance|null $instance $instance
     */
    public function __construct(recording $rec, ?instance $instance) {
        $this->instance = $instance ?? null;
        $this->recording = $rec;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $ispublished = $this->recording->get('published');
        $recordingid = $this->recording->get('id');
        $context = (object) [
            'dataimported' => $this->recording->get('imported'),
            'id' => 'playbacks-' . $this->recording->get('id'),
            'recordingid' => $recordingid,
            'additionaloptions' => '',
            'playbacks' => [],
        ];

        $playbacks = $this->recording->get('playbacks');
        if ($ispublished && $playbacks) {
            foreach ($playbacks as $playback) {
                if ($this->should_be_included($playback)) {
                    $linkattributes = [
                        'id' => "recording-play-{$playback['type']}-{$recordingid}",
                        'class' => 'btn btn-sm btn-default',
                        'data-action' => 'play',
                        'data-target' => $playback['type'],
                    ];
                    $actionlink = new \action_link(
                        $playback['url'],
                        recording_data::type_text($playback['type']),
                        null,
                        $linkattributes
                    );
                    $context->playbacks[] = $actionlink->export_for_template($output);
                }
            }
        }
        return $context;
    }
    /**
     * Helper function renders the link used for recording type in row for the data used by the recording table.
     *
     * @param array $playback
     * @return bool
     */
    protected function should_be_included(array $playback): bool {
        // All types that are not restricted are included.
        if (array_key_exists('restricted', $playback) && strtolower($playback['restricted']) == 'false') {
            return true;
        }
        // All types that are not statistics are included.
        if ($playback['type'] != 'statistics') {
            return true;
        }

        // Exclude imported recordings.
        if ($this->recording->get('imported')) {
            return false;
        }

        // Exclude non moderators.
        if ($this->instance) {
            if (!$this->instance->is_admin() && !$this->instance->is_moderator()) {
                return false;
            }
        } else {
            return roles::has_capability_in_course($this->recording->get('courseid'), 'mod/bigbluebuttonbn:managerecordings');
        }
        return true;

    }
}
