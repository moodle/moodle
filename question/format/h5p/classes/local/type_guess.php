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
class type_guess extends type_mc {
    /**
     * Constructor
     *
     * @param object $content object from content.json file
     * @param string $tempdir temporary directory location
     */
    public function __construct($content, $tempdir) {

        $this->metadata = $content->metadata;

        $this->library = $content->library;

        $this->params = (object) [
            'answer' => $content->params->solutionText,
            'media' => $content->params->media,
            'question' => $content->params->taskDescription,
        ];
        $this->params->media->type = $content->params->media;

        $this->tempdir = $tempdir;
    }

    /**
     * Converts the content object to question object
     *
     * @return object question data
     */
    public function import_question() {
        $qo = $this->import_headers();
        $qo->qtype = 'shortanswer';

        $qo->answer = [$this->params->answer];
        $qo->fraction = [1];
        $qo->feedback = [
            [
                'text' => '',
                'format' => FORMAT_HTML,
            ],
        ];
        return $qo;
    }
}
