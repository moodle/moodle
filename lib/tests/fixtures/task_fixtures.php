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
 * Fixtures for task tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;
defined('MOODLE_INTERNAL') || die();

/**
 * Test class.
 *
 * @copyright 2022 Catalyst IT Australia Pty Ltd
 * @author Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhoc_test_task extends \core\task\adhoc_task {

    /**
     * Constructor.
     *
     * @param int|null $nextruntime Next run time
     * @param int|null $timestarted Time started
     */
    public function __construct(?int $nextruntime = null, ?int $timestarted = null) {
        if ($nextruntime) {
            $this->set_next_run_time($nextruntime);
        }

        if ($timestarted) {
            $this->set_timestarted($timestarted);
        }
    }

    /**
     * Get task name
     *
     * @return string
     */
    public function get_name() {
        return 'Test adhoc class';
    }

    /**
     * Execute.
     */
    public function execute() {
    }
}

/**
 * Test class.
 *
 * @copyright 2022 Catalyst IT Australia Pty Ltd
 * @author Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhoc_test2_task extends adhoc_test_task {
}

/**
 * Test class.
 *
 * @copyright 2022 Catalyst IT Australia Pty Ltd
 * @author Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhoc_test3_task extends adhoc_test_task {
}

/**
 * Test class.
 *
 * @copyright 2022 Catalyst IT Australia Pty Ltd
 * @author Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhoc_test4_task extends adhoc_test_task {
}

/**
 * Test class.
 *
 * @copyright 2022 Catalyst IT Australia Pty Ltd
 * @author Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhoc_test5_task extends adhoc_test_task {
}

/**
 * Test adhoc-task with no-retry customdata.
 *
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhoc_test6_task extends adhoc_test_task {
    /**
     * Execute.
     *
     * @return void
     */
    public function execute(): void {
        // Something wrong happened, do not retry.
        $this->set_custom_data((object) ['noretry' => true]);
    }

}

class scheduled_test_task extends \core\task\scheduled_task {
    public function get_name() {
        return "Test task";
    }

    public function execute() {
    }
}

class scheduled_test2_task extends \core\task\scheduled_task {
    public function get_name() {
        return "Test task 2";
    }

    public function execute() {
    }
}

class scheduled_test3_task extends \core\task\scheduled_task {
    public function get_name() {
        return "Test task 3";
    }

    public function execute() {
    }
}

namespace mod_fake\task;

class adhoc_component_task extends \core\task\adhoc_task {
    public function execute() {

    }
}
