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
     * @return array|null of options
     */
    protected function export_options(): ?array {
        // Suppose H5P choices have only list of valid answers.
        $correctpattern = reset($this->correctpattern);

        $additionals = $this->additionals;

        // Get sources (options).
        if (isset($additionals->source)) {
            $sources = $this->get_descriptions($additionals->source);
        } else {
            $sources = [];
        }

        // Get targets.
        if (isset($additionals->target)) {
            $targets = $this->get_descriptions($additionals->target);
        } else {
            $targets = [];
        }
        // Create original options array.
        $options = array_map(function ($source) {
            $cloneddraggable = clone $source;
            $cloneddraggable->correctanswers = [];
            return $cloneddraggable;
        }, $sources);

        // Fill options with correct answers flags if they exist.
        foreach ($correctpattern as $pattern) {
            if (!is_array($pattern) || count($pattern) != 2) {
                continue;
            }
            // We assume here that the activity is following the convention sets in:
            // https://github.com/h5p/h5p-php-report/blob/master/type-processors/matching-processor.class.php
            // i.e. source is index 1 and dropzone is index 0.
            if (isset($sources[$pattern[1]]) && isset($targets[$pattern[0]])) {
                $target = $targets[$pattern[0]];
                $source = $sources[$pattern[1]];
                $currentoption = $options[$source->id];
                $currentoption->correctanswers[$target->id] = $target->description;
            }
        }

        // Fill in user responses.
        foreach ($this->response as $response) {
            if (!is_array($response) || count($response) != 2) {
                continue;
            }
            if (isset($sources[$response[1]]) && isset($targets[$response[0]])) {
                $source = $sources[$response[1]];
                $target = $targets[$response[0]];
                $answer = $response[0];
                $option = $options[$source->id] ?? null;
                if ($option) {
                    if (isset($option->correctanswers[$answer])) {
                        $state = parent::CORRECT;
                    } else {
                        $state = parent::INCORRECT;
                    }
                    $option->useranswer = $this->get_answer($state, $target->description);
                }
            }
        }

        // Fill in unchecked options.
        foreach ($options as $option) {
            if (!isset($option->useranswer)) {
                if (!empty($option->correctanswers)) {
                    $option->useranswer = $this->get_answer(parent::INCORRECT,
                        get_string('answer_noanswer', 'mod_h5pactivity'));
                } else {
                    $option->useranswer = $this->get_answer(parent::CORRECT,
                        get_string('answer_noanswer', 'mod_h5pactivity'));
                }
            }
        }

        // Now flattern correct answers.
        foreach ($options as $option) {
            $option->correctanswer = $this->get_answer( parent::TEXT, join(', ', $option->correctanswers));
            unset($option->correctanswers);
        }
        return array_values($options);
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
