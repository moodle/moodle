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
 * Contains class mod_h5pactivity\output\result\fillin
 *
 * @package   mod_h5pactivity
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\output\result;

defined('MOODLE_INTERNAL') || die();

use mod_h5pactivity\output\result;
use renderer_base;
use stdClass;

/**
 * Class to display H5P fill-in result.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fillin extends result {

    /**
     * Return the options data structure.
     *
     * @return array of options
     */
    protected function export_options(): ?array {

        $correctpatterns = $this->correctpattern;

        $additionals = $this->additionals;

        $extensions = (array) $additionals->extensions ?? [];

        // There are two way in which H5P could force case sensitivity, with extensions
        // or using options in the correctpatterns. By default it is case sensible.
        $casesensitive = $extensions['https://h5p.org/x-api/case-sensitivity'] ?? true;
        if (!empty($this->result->correctpattern) && strpos($this->result->correctpattern, '{case_matters=false}') !== null) {
                $casesensitive = false;
        }

        $values = [];
        // Add all possibilities from $additionals.
        if (isset($extensions['https://h5p.org/x-api/alternatives'])) {
            foreach ($extensions['https://h5p.org/x-api/alternatives'] as $key => $value) {
                if (!is_array($value)) {
                    $value = [$value];
                }
                $values[$key] = ($casesensitive) ? $value : array_change_key_case($value);
            }
        }
        // Add possibilities from correctpattern.
        foreach ($correctpatterns as $correctpattern) {
            foreach ($correctpattern as $key => $pattern) {
                // The xAPI admits more params a part form values.
                // For now this extra information is not used in reporting
                // but it is posible future H5P types need them.
                $value = preg_replace('/\{.+=.*\}/', '', $pattern);
                $value = ($casesensitive) ? $value : strtolower($value);
                if (!isset($values[$key])) {
                    $values[$key] = [];
                }
                if (!in_array($value, $values[$key])) {
                    array_unshift($values[$key], $value);
                }
            }
        }

        // Generate options.
        $options = [];
        $num = 1;
        foreach ($values as $key => $value) {
            $option = (object)[
                'id' => $key,
                'description' => get_string('result_fill-in_gap', 'mod_h5pactivity', $num),
            ];

            $gapresponse = $this->response[$key] ?? null;
            $gapresponse = ($casesensitive) ? $gapresponse : strtolower($gapresponse);
            if ($gapresponse !== null && in_array($gapresponse, $value)) {
                $state = parent::CORRECT;
            } else {
                $state = parent::INCORRECT;
            }
            $option->useranswer = $this->get_answer($state, $gapresponse);

            $option->correctanswer = $this->get_answer(parent::TEXT, implode(' / ', $value));

            $options[] = $option;
            $num++;
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
