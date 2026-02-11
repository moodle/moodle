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
 * Question Import for H5P Quiz content type
 *
 * @package    qformat_h5p
 * @copyright  2020 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace qformat_h5p\local;

use stdClass;
use context_user;

/**
 * Question Import for H5P Quiz content type
 *
 * @copyright  2020 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class type_hotspot extends type_mc {
    /** @var int height of background in hectopixels */
    protected $height = 4;

    /** @var int width  of background in hectopixels */
    protected $width = 6;

    /**
     * Converts the content object to question object
     *
     * @return object question data
     */
    public function import_question() {
        global $CFG;

        // Get prescribed dimensions of background.
        $this->params->question = new stdClass();
        $this->params->question->settings = new stdClass();
        if (empty($this->params->imageMultipleHotspotQuestion)) {
            $this->params->question->settings->background
                = $this->params->imageHotspotQuestion->backgroundImageSettings;
            $noofdrags = 1;
            $zones = $this->params->imageHotspotQuestion->hotspotSettings->hotspot;
            $taskdescription = $this->params->imageHotspotQuestion->hotspotSettings->taskDescription;
        } else {
            $this->params->question->settings->background
                = $this->params->imageMultipleHotspotQuestion->backgroundImageSettings->backgroundImage;
            $noofdrags = 0;
            $zones = $this->params->imageMultipleHotspotQuestion->hotspotSettings->hotspot;
            $taskdescription = $this->params->imageMultipleHotspotQuestion->hotspotSettings->taskDescription;
        }
        $this->height = $this->params->question->settings->background->height / 100;
        $this->width = $this->params->question->settings->background->width / 100;

        if (!$itemid = $this->import_question_files_as_draft($this->params->question)) {
            return '';
        }
        $qo = $this->import_headers();
        $qo->questiontext = $taskdescription;
        $qo->questiontextformat = FORMAT_HTML;
        $qo->bgimage = $itemid;
        $qo->qtype = 'ddmarker';

        $qo->drags = [
            [
                'noofdrags' => $noofdrags,
                'label' => '+',
            ],
        ];
        $qo->drops = [];
        foreach ($zones as $zone) {
            if (!empty($zone->userSettings->correct)) {
                $settings = $zone->computedSettings;
                if ($settings->figure == 'rectangle') {
                    $coords = round($settings->x * $this->width) . ',' . round($settings->y * $this->height) . ';' .
                        round($settings->width * $this->width) . ',' . round($settings->height * $this->height);
                    $shape = 'rectangle';
                } else if (abs(1 - $settings->height * $this->height / ($settings->width * $this->width)) < 0.05) {
                    $shape = 'circle';
                    $radius = round($settings->width * $this->width / 2);
                    $coords = round($settings->x * $this->width + $settings->width / 2) . ',' .
                        round($settings->y * $this->height + $settings->height / 2) . ';' . $radius;
                } else {
                    $shape = 'polygon';
                    $coord = [];
                    for ($angle = 0; $angle < 2 * M_PI; $angle += 30 * M_PI / 360) {
                        $coord[] = round(($settings->x + (1 - cos($angle)) * $settings->width / 2) * $this->width) . ',' .
                            round(($settings->y + (1 - sin($angle)) * $settings->height / 2) * $this->height);
                    }
                    $coords = implode(';', $coord);
                }
                $qo->drops[] = [
                    'choice' => 1,
                    'shape' => $shape,
                    'coords' => $coords,
                ];
            }
        }
        return $qo;
    }

    /**
     * Parse attached file used as drags or background
     *
     * @param object $question the object containing file params
     * @return int the itemid to be used for filearea
     */
    protected function import_question_files_as_draft($question) {
        global $USER;

        $metadata = null;
        if (!empty($question->settings->background)) {
            $filepath = $this->tempdir . '/content/' . $question->settings->background->path;
            $height = $question->settings->background->height;
            $width = $question->settings->background->width;
            if (!empty($question->settings->metadata)) {
                $metadata = $question->settings->metadata;
            } else if (!empty($question->settings->copyright)) {
                $metadata = $question->settings->copyright;
            }
        } else {
            return '';
        }

        $fs = get_file_storage();
        $itemid = file_get_unused_draft_itemid();
        $filerecord = [
            'author'    => $this->get_author($metadata),
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $itemid,
            'filepath'  => '/',
            'filename'  => preg_replace('/.*\\//', '', $filepath),
            'license'  => $this->get_license($metadata),
        ];

        $file = $fs->create_file_from_pathname($filerecord, $filepath);

        return $itemid;
        // Resize the image to desired size.
        $itemid = file_get_unused_draft_itemid();
        $filerecord['itemid'] = $itemid;
        $fs->create_file_from_string($filerecord, $file->resize_image($width, $height));

        return $itemid;
    }
}
