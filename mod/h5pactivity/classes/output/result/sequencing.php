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
 * Contains class mod_h5pactivity\output\result\sequencing
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
 * Class to display H5P sequencing result.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sequencing extends result {

    /**
     * Return the options data structure.
     *
     * @return array of options
     */
    protected function export_options(): ?array {

        $correctpattern = reset($this->correctpattern);

        $additionals = $this->additionals;

        $response = $this->response;

        if (isset($additionals->choices)) {
            $choices = $this->get_descriptions($additionals->choices);
        } else {
            $choices = [];
        }

        $options = [];
        $num = 1;
        foreach ($correctpattern as $key => $pattern) {
            if (!isset($choices[$pattern])) {
                continue;
            }
            $option = (object)[
                'id' => 'true',
                'description' => get_string('result_sequencing_position', 'mod_h5pactivity', $num),
                'correctanswer' => $this->get_answer(parent::TEXT, $choices[$pattern]->description),
                'correctanswerid' => $key,
            ];
            if (isset($response[$key])) {
                $responseid = str_replace('item_', '', $response[$key]);
                $answerstate = ($responseid == $option->correctanswerid) ? parent::PASS : parent::FAIL;
            } else {
                $answerstate = parent::FAIL;
            }
            $option->useranswer = $this->get_answer($answerstate);

            $options[$key] = $option;
            $num ++;
        }
        return $options;
    }

    /**
     * Return a label for result user options/choices.
     *
     * @return string to use in options table
     */
    protected function get_optionslabel(): string {
        return get_string('result_sequencing_choice', 'mod_h5pactivity');
    }

    /**
     * Return a label for result user correct answer.
     *
     * @return string to use in options table
     */
    protected function get_correctlabel(): string {
        return get_string('result_sequencing_answer', 'mod_h5pactivity');
    }
}
