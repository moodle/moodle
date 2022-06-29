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

require_once(__DIR__ . '/behat_base.php');

/**
 * Base class for steps definitions classes that contain deprecated steps.
 *
 * To be extended by the deprecated steps definitions of the different Moodle components and add-ons.
 * For example, deprecated core steps can be found in lib/tests/behat/behat_deprecated.php ,
 * deprecated steps for mod_forum would be in mod/forum/tests/behat/behat_mod_forum_deprecated.php etc.
 *
 * @package   core
 * @category  test
 * @copyright 2022 Marina Glancy
 * @author    David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_deprecated_base extends behat_base {

    /**
     * Throws an exception if $CFG->behat_usedeprecated is not allowed.
     *
     * @throws Exception
     * @param string|array $alternatives Alternative/s to the requested step
     * @param bool $throwexception If set to true we always throw exception, irrespective of behat_usedeprecated setting.
     * @return void
     */
    protected function deprecated_message($alternatives, bool $throwexception = false): void {
        global $CFG;

        // We do nothing if it is enabled.
        if (!empty($CFG->behat_usedeprecated) && !$throwexception) {
            return;
        }

        if (is_scalar($alternatives)) {
            $alternatives = array($alternatives);
        }

        // Show an appropriate message based on the throwexception flag.
        if ($throwexception) {
            $message = 'This step has been removed. Rather than using this step you can:';
        } else {
            $message = 'Deprecated step, rather than using this step you can:';
        }

        // Add all alternatives to the message.
        foreach ($alternatives as $alternative) {
            $message .= PHP_EOL . '- ' . $alternative;
        }

        if (!$throwexception) {
            $message .= PHP_EOL . '- Set $CFG->behat_usedeprecated in config.php to allow the use of deprecated steps
                    if you don\'t have any other option';
        }

        throw new Exception($message);
    }
}
