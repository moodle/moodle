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

use Moodle\BehatExtension\Exception\SkippedException;

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Steps definitions related to Analytics.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_analytics extends behat_base {

    /**
     * Check if mlbackend_python is configured.
     *
     * @Given /^a Python Machine Learning backend server is configured$/
     */
    public function backend_is_configured(): void {
        if (!defined('TEST_MLBACKEND_PYTHON_HOST')) {
            throw new SkippedException(
                'The Python Machine Learning backend server must be setup to run tests'
            );
        }
    }

    /**
     * Change the Python Machine Learning backend to use external server.
     *
     * @Given /^I change the Python Machine Learning backend to use external server$/
     */
    public function change_backend_to_external_server(): void {
        set_config('useserver', 1, 'mlbackend_python');
        set_config('host', TEST_MLBACKEND_PYTHON_HOST, 'mlbackend_python');
        set_config('port', TEST_MLBACKEND_PYTHON_PORT, 'mlbackend_python');
        set_config('username', TEST_MLBACKEND_PYTHON_USERNAME, 'mlbackend_python');
        set_config('password', TEST_MLBACKEND_PYTHON_PASSWORD, 'mlbackend_python');
    }
}
