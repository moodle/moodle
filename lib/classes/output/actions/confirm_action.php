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

/**
 * Confirm action
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class confirm_action extends component_action {
    /**
     * Constructs the confirm action object
     *
     * @param string $message The message to display to the user when they are shown
     *    the confirm dialogue.
     * @param string $callback Deprecated since 2.7
     * @param string $continuelabel The string to use for he continue button
     * @param string $cancellabel The string to use for the cancel button
     */
    public function __construct($message, $callback = null, $continuelabel = null, $cancellabel = null) {
        if ($callback !== null) {
            debugging(
                'The callback argument to new confirm_action() has been deprecated.' .
                    ' If you need to use a callback, please write Javascript to use moodle-core-notification-confirmation ' .
                    'and attach to the provided events.',
                DEBUG_DEVELOPER,
            );
        }
        parent::__construct('click', 'M.util.show_confirm_dialog', [
            'message' => $message,
            'continuelabel' => $continuelabel,
            'cancellabel' => $cancellabel,
        ]);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(confirm_action::class, \confirm_action::class);
