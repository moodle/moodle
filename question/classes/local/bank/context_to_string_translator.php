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

/**
 * Converts contextlevels to strings and back to help with reading/writing contexts to/from import/export files.
 *
 * @package   core_question
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_to_string_translator {

    /**
     * @var array used to translate between contextids and strings for this context.
     */
    protected $contexttostringarray = [];

    /**
     * context_to_string_translator constructor.
     *
     * @param \context[] $contexts
     */
    public function __construct($contexts) {
        $this->generate_context_to_string_array($contexts);
    }

    /**
     * Context to string.
     *
     * @param int $contextid
     * @return mixed
     */
    public function context_to_string($contextid) {
        return $this->contexttostringarray[$contextid];
    }

    /**
     * String to context.
     *
     * @param string $contextname
     * @return false|int|string
     */
    public function string_to_context($contextname) {
        return array_search($contextname, $this->contexttostringarray);
    }

    /**
     * Generate context to array.
     *
     * @param \context[] $contexts
     */
    protected function generate_context_to_string_array($contexts) {
        if (!$this->contexttostringarray) {
            $catno = 1;
            /** @var \context $context */
            foreach ($contexts as $context) {
                switch ($context->contextlevel) {
                    case CONTEXT_MODULE :
                        $contextstring = 'module';
                        break;
                    case CONTEXT_COURSE :
                        $contextstring = 'course';
                        break;
                    case CONTEXT_COURSECAT :
                        $contextstring = "cat$catno";
                        $catno++;
                        break;
                    case CONTEXT_SYSTEM :
                        $contextstring = 'system';
                        break;
                    default:
                        throw new \coding_exception('Unexpected context level ' .
                                \context_helper::get_level_name($context->contextlevel) . ' for context ' .
                                $context->id . ' in generate_context_to_string_array. ' .
                                'Questions can never exist in this type of context.');
                }
                $this->contexttostringarray[$context->id] = $contextstring;
            }
        }
    }

}
