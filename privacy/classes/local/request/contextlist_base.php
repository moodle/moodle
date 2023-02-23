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
 * Base implementation of a contextlist.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * Base implementation of a contextlist used to store a set of contexts.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class contextlist_base implements
        // Implement an Iterator to fetch the Context objects.
        \Iterator,

        // Implement the Countable interface to allow the number of returned results to be queried easily.
        \Countable {

    /**
     * @var array List of context IDs.
     *
     * Note: this must not be updated using set_contextids only as this
     * ensures uniqueness.
     */
    private $contextids = [];

    /**
     * @var string component the frankenstyle component name.
     */
    protected $component = '';

    /**
     * @var int Current position of the iterator.
     */
    protected $iteratorposition = 0;

    /**
     * Set the contextids.
     *
     * @param   array   $contextids The list of contexts.
     */
    protected function set_contextids(array $contextids) {
        $this->contextids = array_unique($contextids);
    }

    /**
     * Get the list of context IDs that relate to this request.
     *
     * @return  int[]
     */
    public function get_contextids() : array {
        return $this->contextids;
    }

    /**
     * Get the complete list of context objects that relate to this
     * request.
     *
     * @return  \context[]
     */
    public function get_contexts() : array {
        $contexts = [];
        foreach ($this->contextids as $contextid) {
            // It is possible that this context has been deleted and we now have subsequent calls being made with this
            // contextlist. Exceptions here will stop the further processing of this component and that is why we are
            // doing a try catch.
            try {
                $contexts[] = \context::instance_by_id($contextid);
            } catch (\Exception $e) {
                // Remove this context.
                unset($this->contextids[$this->iteratorposition]);
            }
        }

        return $contexts;
    }

    /**
     * Sets the component for this contextlist.
     *
     * @param string $component the frankenstyle component name.
     */
    protected function set_component($component) {
        $this->component = $component;
    }

    /**
     * Get the name of the component to which this contextlist belongs.
     *
     * @return string the component name associated with this contextlist.
     */
    public function get_component() : string {
        return $this->component;
    }

    /**
     * Return the current context.
     *
     * @return  \context
     */
    #[\ReturnTypeWillChange]
    public function current() {
        // It is possible that this context has been deleted and we now have subsequent calls being made with this
        // contextlist. Exceptions here will stop the further processing of this component and that is why we are
        // doing a try catch.
        try {
            $context = \context::instance_by_id($this->contextids[$this->iteratorposition]);
        } catch (\Exception $e) {
            // Remove this context.
            unset($this->contextids[$this->iteratorposition]);
            // Check to see if there are any more contexts left.
            if ($this->count()) {
                // Move the pointer to the next record and try again.
                $this->next();
                $context = $this->current();
            } else {
                // There are no more context ids left.
                return null;
            }
        }
        return $context;
    }

    /**
     * Return the key of the current element.
     *
     * @return  mixed
     */
    #[\ReturnTypeWillChange]
    public function key() {
        return $this->iteratorposition;
    }

    /**
     * Move to the next context in the list.
     */
    public function next(): void {
        ++$this->iteratorposition;
    }

    /**
     * Check if the current position is valid.
     *
     * @return  bool
     */
    public function valid(): bool {
        return isset($this->contextids[$this->iteratorposition]);
    }

    /**
     * Rewind to the first found context.
     *
     * The list of contexts is uniqued during the rewind.
     * The rewind is called at the start of most iterations.
     */
    public function rewind(): void {
        $this->iteratorposition = 0;
    }

    /**
     * Return the number of contexts.
     */
    public function count(): int {
        return count($this->contextids);
    }
}
