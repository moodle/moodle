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
 * Contains class mod_h5pactivity\output\result\truefalse
 *
 * @package   mod_h5pactivity
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\output\result;

defined('MOODLE_INTERNAL') || die();

use mod_h5pactivity\output\result;
use renderer_base;

/**
 * Class to display H5P choice result.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class truefalse extends result {

    /**
     * Return the options data structure.
     *
     * @return array of options
     */
    protected function export_options(): ?array {

        // This interaction type have only one entry which is the correct option.
        $correctpattern = reset($this->correctpattern);
        $correctpattern = filter_var(reset($correctpattern), FILTER_VALIDATE_BOOLEAN);
        $correctpattern = $correctpattern ? 'true' : 'false';

        $response = filter_var(reset($this->response), FILTER_VALIDATE_BOOLEAN);
        $response = $response ? 'true' : 'false';

        $options = [
            (object)[
                'id' => 'true',
                'description' => get_string('true', 'mod_h5pactivity'),
            ],
            (object)[
                'id' => 'false',
                'description' => get_string('false', 'mod_h5pactivity'),
            ],
        ];
        foreach ($options as $value) {
            $correctstate = ($value->id == $correctpattern) ? parent::CHECKED : parent::UNCHECKED;

            if ($value->id == $response) {
                $answerstate = ($correctstate == parent::CHECKED) ? parent::PASS : parent::FAIL;
                $value->useranswer = $this->get_answer($answerstate);
            }

            $value->correctanswer = $this->get_answer($correctstate);
        }

        return $options;
    }
}
