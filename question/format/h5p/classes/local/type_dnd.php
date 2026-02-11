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
class type_dnd extends type_mc {
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
        $this->height = $this->params->question->settings->size->height / 100;
        $this->width = $this->params->question->settings->size->width / 100;
        if (empty($this->params->question->settings->background)) {
            require_once($CFG->libdir . '/gdlib.php');
            $background = imagecreate(
                $this->params->question->settings->size->width,
                $this->params->question->settings->size->height
            );
            imagecolorallocate($background, 255, 255, 255);
            imagepng($background, $this->tempdir . '/content/images/background.png');
            $this->params->question->settings->background = (object) [
                'path' => 'images/background.png',
            ];
        }

        if (!$itemid = $this->import_question_files_as_draft($this->params->question)) {
            return '';
        }
        $qo = $this->import_headers();
        $qo->questiontext = '';
        $qo->questiontextformat = FORMAT_HTML;
        $qo->bgimage = $itemid;
        $qo->qtype = 'ddimageortext';

        $qo->drags = [];
        foreach ($this->params->question->task->elements as $dragindex => $element) {
            $itemid = $this->import_question_files_as_draft($element);
            if (!empty($itemid)) {
                $qo->drags[$dragindex] = [
                    'dragitemtype' => 'image',
                    'draggroup' => 1,
                    'infinite' => false,
                ];
            } else {
                $qo->drags[$dragindex] = [
                    'dragitemtype' => 'word',
                    'draggroup' => 1,
                    'infinite' => false,
                ];
                $qo->draglabel[$dragindex] = 'Drag me';
            }
            $qo->dragitem[$dragindex] = $itemid;
            if (!empty($element->type->params->text)) {
                $qo->draglabel[$dragindex] = strip_tags($element->type->params->text);
            } else if (!empty($element->type->params->alt)) {
                $qo->draglabel[$dragindex] = strip_tags($element->type->params->alt);
            }
        }
        $qo->drops = [];
        foreach ($this->params->question->task->dropZones as $zone) {
            $qo->drops[] = [
                'choice' => reset($zone->correctElements) + 1,
                'xleft' => round($zone->x * $this->width),
                'ytop' => round($zone->y * $this->height),
                'droplabel' => !empty($zone->showLabel) ? strip_tags($zone->label) : '',
                'coords' => round($zone->x * $this->width) . ',' . round($zone->y * $this->height) . ';' .
                    round($zone->width * $this->width) . ',' .  round($zone->height * $this->height),
            ];
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
            $height = $question->settings->size->height;
            $width = $question->settings->size->width;
            if (!empty($question->settings->metadata)) {
                $metadata = $question->settings->metadata;
            } else if (!empty($question->settings->copyright)) {
                $metadata = $question->settings->copyright;
            }
        } else if (!empty($question->type->params->file)) {
            $filepath = $this->tempdir . '/content/' . $question->type->params->file->path;
            $height = $question->height * 20;
            $width = $question->width * 20;
            if (!empty($question->type->metadata)) {
                $metadata = $question->type->metadata;
            } else if (!empty($question->type->copyright)) {
                $metadata = $question->type->copyright;
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

        // Resize the image to desired size.
        $itemid = file_get_unused_draft_itemid();
        $filerecord['itemid'] = $itemid;
        $fs->create_file_from_string($filerecord, $file->resize_image($width, $height));

        return $itemid;
    }
}
