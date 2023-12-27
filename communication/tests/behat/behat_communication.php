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

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../tests/communication_test_helper_trait.php');

/**
 * Class behat_communication for behat custom steps and configuration for communication api.
 *
 * @package    core_communication
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_communication extends \behat_base {
    use \core_communication\communication_test_helper_trait;

    /**
     * Configure and enable communication experimental feature.
     *
     * @Given /^I enable communication experimental feature$/
     */
    public function enable_communication_experimental_feature(): void {
        $this->setup_communication_configs();
    }

    /**
     * Disable communication experimental feature.
     *
     * @Given /^I disable communication experimental feature$/
     */
    public function disable_communication_experimental_feature(): void {
        $this->disable_communication_configs();
    }
}
