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

namespace core\output\actions;

use core\exception\coding_exception;
use core\output\renderer_base;
use core\output\templatable;
use stdClass;

/**
 * Helper class used by other components that involve an action on the page (URL or JS).
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class component_action implements templatable {
    /**
     * @var string $event The DOM event that will trigger this action when caught
     */
    public $event;

    /**
     * @var string A function name to call when the button is clicked
     * The JS function you create must have two arguments:
     *      1. The event object
     *      2. An object/array of arguments ($jsfunctionargs)
     */
    public $jsfunction = false;

    /**
     * @var array An array of arguments to pass to the JS function
     */
    public $jsfunctionargs = [];

    /**
     * Constructor
     * @param string $event DOM event
     * @param string $jsfunction An optional JS function. Required if jsfunctionargs is given
     * @param array $jsfunctionargs An array of arguments to pass to the jsfunction
     */
    public function __construct($event, $jsfunction, $jsfunctionargs = []) {
        $this->event = $event;

        $this->jsfunction = $jsfunction;
        $this->jsfunctionargs = $jsfunctionargs;

        if (!empty($this->jsfunctionargs)) {
            if (empty($this->jsfunction)) {
                throw new coding_exception('The component_action object needs a jsfunction value to pass the jsfunctionargs to.');
            }
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $args = !empty($this->jsfunctionargs) ? json_encode($this->jsfunctionargs) : false;
        return (object) [
            'event' => $this->event,
            'jsfunction' => $this->jsfunction,
            'jsfunctionargs' => $args,
        ];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(component_action::class, \component_action::class);
