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
 * Approved result set for unit testing.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\tests\request;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Fetch Result Set.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class approved_contextlist extends \core_privacy\local\request\approved_contextlist {
    /**
     * Add a single context to this approved_contextlist.
     *
     * @param   \context    $context        The context to be added.
     * @return  $this
     */
    public function add_context(\context $context) {
        return $this->add_context_by_id($context->id);
    }

    /**
     * Add a single context to this approved_contextlist by it's ID.
     *
     * @param   int         $contextid      The context to be added.
     * @return  $this
     */
    public function add_context_by_id($contextid) {
        return $this->set_contextids(array_merge($this->get_contextids(), [$contextid]));
    }

    /**
     * Add a set of contexts to this approved_contextlist.
     *
     * @param   \context[]  $contexts       The contexts to be added.
     * @return  $this
     */
    public function add_contexts(array $contexts) {
        foreach ($contexts as $context) {
            $this->add_context($context);
        }
    }

    /**
     * Add a set of contexts to this approved_contextlist by ID.
     *
     * @param   int[]       $contexts       The contexts to be added.
     * @return  $this
     */
    public function add_contexts_by_id(array $contexts) {
        foreach ($contexts as $contextid) {
            $this->add_context_by_id($contextid);
        }
    }
}
