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
 * PHPUnit tool_brickfield tests
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Mike Churchward (mike@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_brickfield;

/**
 * Unit tests for {@registration tool_brickfield\registration.php}.
 * @group tool_brickfield
 */
class registration_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/admin/tool/brickfield/tests/generator/mock_registration.php');
        require_once($CFG->dirroot . '/admin/tool/brickfield/tests/generator/mock_brickfieldconnect.php');
    }

    /**
     * Tests the state of the registration system when first installed.
     * @throws \dml_exception
     */
    public function test_initial_state() {
        $this->resetAfterTest();
        $regobj = new mock_registration();

        // Initial state of system.
        $this->assertFalse($regobj->toolkit_is_active());
        $this->assertFalse($regobj->validation_pending());
        $this->assertFalse($regobj->validation_error());
        $this->assertEmpty($regobj->get_api_key());
        $this->assertEmpty($regobj->get_secret_key());
    }

    /**
     * Test the various states for setting registration keys.
     * @throws \dml_exception
     */
    public function test_set_keys_for_registration() {
        $this->resetAfterTest();
        $regobj = new mock_registration();

        // State when invalid format keys are sent.
        $this->assertFalse($regobj->set_keys_for_registration('123', 'abc'));
        $this->assertTrue($regobj->is_not_entered());
        $this->assertFalse($regobj->validation_pending());
        $this->assertEmpty($regobj->get_api_key());
        $this->assertEmpty($regobj->get_secret_key());

        // State when valid format keys are sent.
        $this->assertTrue($regobj->set_keys_for_registration(mock_brickfieldconnect::VALIDAPIKEY,
            mock_brickfieldconnect::VALIDSECRETKEY));
        $this->assertTrue($regobj->validation_pending());
        $this->assertEquals($regobj->get_api_key(), mock_brickfieldconnect::VALIDAPIKEY);
        $this->assertEquals($regobj->get_secret_key(), mock_brickfieldconnect::VALIDSECRETKEY);
    }

    /**
     * Test the validation system through its several states.
     * @throws \dml_exception
     */
    public function test_validation() {
        $this->resetAfterTest();
        $regobj = new mock_registration();

        // Set invalid format keys and validate the system.
        $this->assertFalse($regobj->set_keys_for_registration('123', 'abc'));
        // Run validate function. State should end up as 'NOT_ENTERED'.
        $this->assertFalse($regobj->validate());
        $this->assertTrue($regobj->is_not_entered());

        // Set valid keys and validate the system.
        $this->assertTrue($regobj->set_keys_for_registration(mock_brickfieldconnect::VALIDAPIKEY,
            mock_brickfieldconnect::VALIDSECRETKEY));
        // Run validate function. State should end up as valid, 'VALIDATED'.
        $this->assertTrue($regobj->validate());
        $this->assertTrue($regobj->toolkit_is_active());
        $this->assertFalse($regobj->validation_pending());
        $this->assertFalse($regobj->validation_error());

        // Set invalid keys and validate the system.
        $this->assertTrue($regobj->set_keys_for_registration('123456789012345678901234567890cd',
            'cd123456789012345678901234567890'));
        // Run validate function. State should end up as valid, not validated, 'ERROR'.
        $this->assertTrue($regobj->validate());
        $this->assertTrue($regobj->toolkit_is_active());
        $this->assertTrue($regobj->validation_pending());
        $this->assertTrue($regobj->validation_error());
    }

    /**
     * Tests the system after validation grace periods expire.
     * @throws \dml_exception
     */
    public function test_validation_time_expiry() {
        $this->resetAfterTest();
        $regobj = new mock_registration();

        // Set valid keys and validate the system.
        $this->assertTrue($regobj->set_keys_for_registration(mock_brickfieldconnect::VALIDAPIKEY,
            mock_brickfieldconnect::VALIDSECRETKEY));
        // Run validate function. State should end up as valid, 'VALIDATED'.
        $this->assertTrue($regobj->validate());
        $this->assertTrue($regobj->toolkit_is_active());

        // Invalidate the validation time.
        $regobj->invalidate_validation_time();
        // Run validate function. State should end up as valid, 'VALIDATED'.
        $this->assertTrue($regobj->validate());
        $this->assertTrue($regobj->toolkit_is_active());

        // Set invalid keys and validate the system.
        $this->assertTrue($regobj->set_keys_for_registration('123456789012345678901234567890cd',
            'cd123456789012345678901234567890'));
        // Run validate function. State should end up as valid, not validated, 'ERROR'.
        $this->assertTrue($regobj->validate());
        $this->assertTrue($regobj->toolkit_is_active());
        $this->assertTrue($regobj->validation_pending());
        $this->assertTrue($regobj->validation_error());

        // Invalidate the validation time.
        $regobj->invalidate_validation_time();
        // Run validate function. State should end up as  not valid.
        $this->assertFalse($regobj->validate());
        $this->assertFalse($regobj->toolkit_is_active());
    }

    /**
     * Tests the system after summary data time periods expire.
     * @throws \dml_exception
     */
    public function test_summary_time_expiry() {
        $this->resetAfterTest();
        $regobj = new mock_registration();

        // Set valid keys and validate the system.
        $this->assertTrue($regobj->set_keys_for_registration(mock_brickfieldconnect::VALIDAPIKEY,
            mock_brickfieldconnect::VALIDSECRETKEY));
        // Run validate function. State should end up as valid, 'VALIDATED'.
        $this->assertTrue($regobj->validate());
        $this->assertTrue($regobj->toolkit_is_active());

        // Invalidate the summary time.
        $regobj->invalidate_summary_time();
        // Run validate function. State should end up as not valid.
        $this->assertFalse($regobj->validate());
        $this->assertFalse($regobj->toolkit_is_active());

        // Set invalid keys and validate the system.
        $this->assertTrue($regobj->set_keys_for_registration('123456789012345678901234567890cd',
            'cd123456789012345678901234567890'));
        // Run validate function. State should end up as invalid.
        $this->assertFalse($regobj->validate());
        $this->assertFalse($regobj->toolkit_is_active());

        // Set invalid keys and validate the system.
        $this->assertTrue($regobj->set_keys_for_registration('123456789012345678901234567890cd',
            'cd123456789012345678901234567890'));
        // Mark the summary data as sent, and revalidate the system.
        $regobj->mark_summary_data_sent();
        // Run validate function. State should end up as valid, not validated, 'ERROR'.
        $this->assertTrue($regobj->validate());
        $this->assertTrue($regobj->toolkit_is_active());
        $this->assertTrue($regobj->validation_pending());
        $this->assertTrue($regobj->validation_error());
    }
}
