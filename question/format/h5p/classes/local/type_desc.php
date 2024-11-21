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

use qformat_h5p\local;
use stdClass;
use context_user;

/**
 * Question Import for H5P Quiz content type
 *
 * @copyright  2020 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class type_desc extends type_mc {
    /**
     * Converts the content object to question object
     *
     * @return object question data
     */
    public function import_question() {
        // This routine initialises the question object.
        $qo = $this->defaultquestion();

        // Question name.
        $qo->name = $this->clean_question_name($this->metadata->title);
        $qo->questiontextformat = FORMAT_HTML;

        $qo = $this->import_headers();
        $qo->qtype = 'description';
        $qo->questiontext = '<p>' . s(json_encode($this->library));
        $qo->questiontext .= '<p>' . s(json_encode($this->params));
        $qo->questiontext .= '<p>' . s(json_encode($this->metadata));

        return $qo;
    }
}
