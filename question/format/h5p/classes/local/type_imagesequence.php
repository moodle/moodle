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
class type_imagesequence extends type_mc {
    /**
     * Converts the content object to question object
     *
     * @return object question data
     */
    public function import_question() {
        global $OUTPUT;

        $pluginmanager = \core_plugin_manager::instance();
        if (!$pluginmanager->get_plugin_info('qtype_ordering')) {
            return;
        }

        $this->params->question = $this->params->taskDescription;
        $qo = $this->import_headers();
        $qo->qtype = 'ordering';
        $qo->layouttype = 1;
        $qo->selecttype = 0;
        $qo->selectcount = 0;
        $qo->gradingtype = 0;
        $qo->showgrading = 1;
        $qo->numberingstyle = 'none';

        $qo->answer = [];
        $qo->feedback = [];

        $images = array_column($this->params->sequenceImages, 'image');
        $height = 1.0 * min(array_column($images, 'height'));

        foreach ($this->params->sequenceImages as $image) {
            $qo->answer[] = [
                'text' => $OUTPUT->render_from_template('qformat_h5p/imagesequence', [
                    'description' => $image->imageDescription,
                    'height' => $height,
                    'path' => $image->image->path,
                ]),
                'format' => FORMAT_HTML,
                'itemid' => $this->import_image_file_as_draft($image->image),
            ];
        }

        return $qo;
    }

    /**
     * Parse attached file used for answer
     *
     * @param object $image the object containing file params
     * @return int the itemid to be used for filearea
     */
    protected function import_image_file_as_draft($image) {
        global $USER;

        if (empty($image->path)) {
            return '';
        }
        $filepath = $this->tempdir . '/content/' . $image->path;
        if (empty($image->copyright)) {
            $metadata = $image->metadata;
        } else {
            $metadata = $image->copyright;
        }

        $fs = get_file_storage();
        $itemid = file_get_unused_draft_itemid();
        $filerecord = [
            'author'    => $this->get_author($metadata),
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $itemid,
            'filepath'  => '/images/',
            'filename'  => preg_replace('/.*\\//', '', $filepath),
            'license'  => $this->get_license($metadata),
        ];
        $file = $fs->create_file_from_pathname($filerecord, $filepath);
        return $itemid;
    }
}
