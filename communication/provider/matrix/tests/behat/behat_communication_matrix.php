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

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use communication_matrix\matrix_test_helper_trait;
use Moodle\BehatExtension\Exception\SkippedException;

require_once(__DIR__ . '/../matrix_test_helper_trait.php');
require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../tests/communication_test_helper_trait.php');

/**
 * Class behat_communication_matrix for behat custom steps and configuration for matrix.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_communication_matrix extends \behat_base {
    use \core_communication\communication_test_helper_trait;
    use matrix_test_helper_trait;

    /**
     * BeforeScenario hook to reset the mock server.
     *
     * @BeforeScenario @communication_matrix
     *
     * @param BeforeScenarioScope $scope
     */
    public function before_scenario(BeforeScenarioScope $scope) {
        if (defined('TEST_COMMUNICATION_MATRIX_MOCK_SERVER')) {
            $this->reset_mock();
        }
    }

    /**
     * Setup and configure and mock server for matrix.
     *
     * @Given /^a Matrix mock server is configured$/
     */
    public function initialize_mock_server(): void {
        if (!defined('TEST_COMMUNICATION_MATRIX_MOCK_SERVER')) {
            throw new SkippedException(
                'The TEST_COMMUNICATION_MATRIX_MOCK_SERVER constant must be defined to run communication_matrix tests'
            );
        }
        $this->setup_communication_configs();
        $this->initialise_mock_configs();
    }
}
