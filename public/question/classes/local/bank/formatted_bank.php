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

namespace core_question\local\bank;

use core\context;
use core_course\cm_info;
use JsonSerializable;

/**
 * Data class to hold bank info and categories, and return them with formatted names for output.
 *
 * @package   core_question
 * @copyright 2026 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class formatted_bank implements JsonSerializable {
    /**
     * @var bool True if the bank is the module currently being viewed.
     */
    public bool $current;

    /**
     * Constructor
     *
     * @param cm_info $cminfo The course module info for the bank's course module
     * @param context $filtercontext The context used for applying filters when formatting strings.
     * @param array $questioncategories Array of categories belonging to the bank.
     * @param bool $shared True if the bank contains shared questions, false if it contains private questions.
     * @param bool $recent True if the bank was recently viewed by the user.
     */
    public function __construct(
        /** @var cm_info $cminfo Course module info. */
        public cm_info $cminfo,
        /** @var context $filtercontext The context used for applying filters when formatting strings. */
        public context $filtercontext,
        /** @var array $questioncategories Array of categories belonging to the bank. */
        public array $questioncategories,
        /** @var bool True if the bank contains shared questions, false if it contains private questions. */
        public bool $shared,
        /** @var bool True if the bank was recently viewed by the user. */
        public bool $recent,
    ) {
    }

    /**
     * Return the question bank formatted for output.
     *
     * @return \stdClass
     */
    public function get_formatted(): \stdClass {
        $filteroptions = [
            'escape' => false,
            'context' => $this->filtercontext,
        ];
        $formattedname = $this->cminfo->get_formatted_name($filteroptions);
        return (object) [
            'name' => $formattedname,
            'modid' => $this->cminfo->id,
            'contextid' => $this->cminfo->context->id,
            'coursenamebankname' => get_string(
                'coursenamebankname',
                'question',
                (object) [
                    'coursename' => format_string($this->cminfo->get_course()->shortname, true, $filteroptions),
                    'bankname' => $formattedname,
                ],
            ),
            'cminfo' => $this->cminfo,
            'questioncategories' => $this->questioncategories,
            'shared' => $this->shared,
            'recent' => $this->recent,
        ];
    }

    /**
     * Return the formatted object for encoding as JSON, for example by web service routes.
     */
    public function jsonSerialize(): \stdClass {
        return $this->get_formatted();
    }

    /**
     * Format multiple banks.
     *
     * @param formatted_bank[] $banks The formatted_bank objects to return formatted.
     * @return array The formatted bank data.
     */
    public static function format_banks(array $banks): array {
        return array_map(
            fn($bank) => $bank->get_formatted(),
            $banks,
        );
    }
}
