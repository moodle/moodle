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
 * Contains class mod_h5pactivity\output\result\matching
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
 * Class to display H5P matching result.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matching extends result {

    /**
     * Return the options data structure.
     *
     * @return array of options
     */
    protected function export_options(): ?array {
        // Suppose H5P choices have only list of valid answers.
        $correctpattern = reset($this->correctpattern);

        $additionals = $this->additionals;

        // Get sources (options).
        if (isset($additionals->source)) {
            $options = $this->get_descriptions($additionals->source);
        } else {
            $options = [];
        }

        // Get targets.
        if (isset($additionals->target)) {
            $targets = $this->get_descriptions($additionals->target);
        } else {
            $targets = [];
        }

        // Correct answers.
        foreach ($correctpattern as $pattern) {
            if (!is_array($pattern) || count($pattern) != 2) {
                continue;
            }
            // One pattern must be from options and the other from targets.
            if (isset($options[$pattern[0]]) && isset($targets[$pattern[1]])) {
                $option = $options[$pattern[0]];
                $target = $targets[$pattern[1]];
            } else if (isset($targets[$pattern[0]]) && isset($options[$pattern[1]])) {
                $option = $options[$pattern[1]];
                $target = $targets[$pattern[0]];
            } else {
                $option = null;
            }
            if ($option) {
                $option->correctanswer = $this->get_answer(parent::TEXT, $target->description);
                $option->correctanswerid = $target->id;
            }
        }

        // User responses.
        foreach ($this->response as $response) {
            if (!is_array($response) || count($response) != 2) {
                continue;
            }
            // One repsonse must be from options and the other from targets.
            if (isset($options[$response[0]]) && isset($targets[$response[1]])) {
                $option = $options[$response[0]];
                $target = $targets[$response[1]];
                $answer = $response[1];
            } else if (isset($targets[$response[0]]) && isset($options[$response[1]])) {
                $option = $options[$response[1]];
                $target = $targets[$response[0]];
                $answer = $response[0];
            } else {
                $option = null;
            }
            if ($option) {
                if (isset($option->correctanswerid) && $option->correctanswerid == $answer) {
                    $state = parent::CORRECT;
                } else {
                    $state = parent::INCORRECT;
                }
                $option->useranswer = $this->get_answer($state, $target->description);
            }
        }
        return $options;
    }

    /**
     * Return a label for result user options/choices
     *
     * Specific result types can override this method to customize
     * the result options table header.
     *
     * @return string to use in options table
     */
    protected function get_optionslabel(): string {
        return get_string('result_matching', 'mod_h5pactivity');
    }
}
