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

namespace core\exception;

/**
 * Exceptions indicating user does not have permissions to do something
 * and the execution can not continue.
 *
 * @package    core
 * @subpackage exception
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class required_capability_exception extends moodle_exception {
    /**
     * Constructor.
     *
     * @param \core\context $context The context used for the capability check
     * @param string $capability The required capability
     * @param string $errormessage The error message to show the user
     * @param string $stringfile
     */
    public function __construct($context, $capability, $errormessage, $stringfile) {
        $capabilityname = get_capability_string($capability);
        if ($context->contextlevel == CONTEXT_MODULE && preg_match('/:view$/', $capability)) {
            // Cannot redirect to mod/xx/view.php because we most probably do not have cap to view it.
            // Redirect to the course instead.
            $parentcontext = $context->get_parent_context();
            $link = $parentcontext->get_url();
        } else {
            $link = $context->get_url();
        }
        parent::__construct($errormessage, $stringfile, $link, $capabilityname);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(required_capability_exception::class, \required_capability_exception::class);
