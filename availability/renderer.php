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
 * Renderer for availability display.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renderer for availability display.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_availability_renderer extends plugin_renderer_base {
    /**
     * Renders HTML for the result of two or more availability restriction
     * messages being combined in a list.
     *
     * The supplied messages should already take account of the 'not' option,
     * e.g. an example message could be 'User profile field Department must
     * not be set to Maths'.
     *
     * This function will not be called unless there are at least two messages.
     *
     * @param bool $root True if this is a root-level list for an activity
     * @param bool $andoperator True if the messages are being combined as AND
     * @param bool $roothidden True if the root level should use 'hidden' message
     * @param array $messages Messages to render
     * @return string Combined HTML
     */
    public function multiple_messages($root, $andoperator, $roothidden, array $messages) {
        // Get initial message.
        $out = get_string('list_' . ($root ? 'root_' : '') .
                ($andoperator ? 'and' : 'or') . ($roothidden ? '_hidden' : ''),
                'availability');

        // Make the list.
        $out .= html_writer::start_tag('ul');
        foreach ($messages as $message) {
            $out .= html_writer::tag('li', $message);
        }
        $out .= html_writer::end_tag('ul');
        return $out;
    }
}
