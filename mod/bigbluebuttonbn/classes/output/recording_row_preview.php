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
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\recording;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderer for recording_row_preview column
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent.david [at] call-learning [dt] fr)
 */
class recording_row_preview implements renderable, templatable {
    /**
     * @var $recording
     */
    protected $recording;

    /**
     * recording_row_playback constructor.
     *
     * @param recording $rec
     */
    public function __construct(recording $rec) {
        $this->recording = $rec;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG;

        $context = (object) [
            'id' => 'preview-' . $this->recording->get('id'),
            'hidden' => !$this->recording->get('published'),
            'recordingpreviews' => [],
        ];

        $playbacks = $this->recording->get('playbacks');
        foreach ($playbacks as $playback) {
            $thumbnails = [];

            if (isset($playback['preview'])) {
                foreach ($playback['preview'] as $image) {
                    $url = trim($image['url']);
                    $validated = bigbluebutton_proxy::is_remote_resource_valid($url);
                    if ($validated) {
                        $thumbnails[] = $url . '?' . time();
                    }
                }

                if (!empty($thumbnails)) {
                    $context->recordingpreviews[] = (object) [
                        'thumbnails' => $thumbnails,
                    ];
                }
            }
        }

        return $context;
    }
}
