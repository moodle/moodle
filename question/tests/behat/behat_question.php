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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');
require_once(__DIR__ . '/behat_question_base.php');

/**
 * Deprecated class, only kept for backwards compatibility.
 *
 * @package   core_question
 * @category  test
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 4.0. Use behat_core_question instead
 *      (if you need to refer to this class at all, which you probably don't).
 */
class behat_question extends behat_deprecated_base {
    public function __call($name, $arguments) {
        if (method_exists(behat_core_question::class, $name)) {
            $this->deprecated_message('The behat_question class has been moved to behat_core_question.');
            $this->execute("behat_core_question::{$name}", $arguments);
        }
    }
}
