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
 * Contains class mod_h5pactivity\output\result\choice
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
class choice extends result {

    /**
     * Return the options data structure.
     *
     * @return array of options
     */
    protected function export_options(): ?array {

        // Suppose H5P choices have only a single list of valid answers.
        $correctpattern = reset($this->correctpattern);
        if (empty($correctpattern)) {
            $correctpattern = [];
        }

        $additionals = $this->additionals;

        // H5P has a special extension for long choices.
        $extensions = (array) $additionals->extensions ?? [];
        $filter = isset($extensions['https://h5p.org/x-api/line-breaks']) ? true : false;

        if (isset($additionals->choices)) {
            $options = $this->get_descriptions($additionals->choices);
        } else {
            $options = [];
        }

        // Some H5P activities like Find the Words don't user the standard CMI format delimiter
        // and don't use propper choice additionals. In those cases the report needs to fix this
        // using the correct pattern as choices and using a non standard delimiter.
        if (empty($options)) {
            if (count($correctpattern) == 1) {
                $correctpattern = explode(',', reset($correctpattern));
            }
            foreach ($correctpattern as $value) {
                $option = (object)[
                    'id' => $value,
                    'description' => $value,
                ];
                $options[$value] = $option;
            }
        }

        foreach ($options as $key => $value) {
            $correctstate = (in_array($key, $correctpattern)) ? parent::CHECKED : parent::UNCHECKED;
            if (in_array($key, $this->response)) {
                $answerstate = ($correctstate == parent::CHECKED) ? parent::PASS : parent::FAIL;
                // In some cases, like Branching scenario H5P activity, no correct Pattern is provided
                // so any answer is just a check.
                if (empty($correctpattern)) {
                    $answerstate = parent::CHECKED;
                }
                $value->useranswer = $this->get_answer($answerstate);
            }
            $value->correctanswer = $this->get_answer($correctstate);

            if ($filter && $correctstate == parent::UNCHECKED && !isset($value->useranswer)) {
                unset($options[$key]);
            }
        }

        return $options;
    }
}
