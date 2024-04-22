<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Fixtures for advanced_testcase tests.
 *
 * @package   core
 * @category  event
 * @copyright 2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event to test that \advanced_testcase::assertEventContextNotUsed() passes ok when no context is used.
 */
class context_used_in_event_correct extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->context = \context_system::instance();
    }

    public function get_url() {
        return new \moodle_url('/somepath/somefile.php'); // No context used.
    }

    public function get_description() {
        return 'Description'; // No context used.
    }
}

/**
 * Event to test that \advanced_testcase::assertEventContextNotUsed() detects context usage on get_url().
 */
class context_used_in_event_get_url extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->context = \context_system::instance();
    }

    public function get_url() {
        return new \moodle_url('/somepath/somefile.php', ['id' => $this->context->instanceid]); // Causes a PHP Warning.
    }
}

/**
 * Event to test that \advanced_testcase::assertEventContextNotUsed() detects context usage on get_description().
 */
class context_used_in_event_get_description extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->context = \context_system::instance();
    }

    public function get_description() {
        return $this->context->instanceid . " Description"; // Causes a PHP Warning.
    }
}
