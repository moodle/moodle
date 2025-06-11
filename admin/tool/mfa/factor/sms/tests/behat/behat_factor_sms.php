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
 * Behat custom steps and configuration for factor_sms.
 *
 * @package   factor_sms
 * @category  test
 * @copyright 2023 <raquel.ortega@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../../../../lib/behat/behat_field_manager.php');

/**
 * Behat custom steps and configuration for factor_sms.
 *
 * @package   factor_sms
 * @category  test
 * @copyright 2023 <raquel.ortega@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_factor_sms extends behat_base {

    /**
     * Sets the given field with a valid code created in tool_mfa_secrets table
     *
     * @Given /^I set the field "(?P<field_string>(?:[^"]|\\")*)" with valid code$/
     *
     * @param string $field
     */
    public function i_set_the_field_with_valid_code(string $field): void {
        global $DB, $USER;

        $record = $DB->get_record('tool_mfa_secrets',
            ['userid' => $USER->id, 'revoked' => '0']
        );
        $field = behat_field_manager::get_form_field_from_label($field, $this);
        $field->set_value($record->secret);
    }
}
