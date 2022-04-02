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
 * Tests for core_message_inbound to test Variable Envelope Return Path functionality.
 *
 * @package    core_message
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/fixtures/inbound_fixtures.php');

/**
 * Tests for core_message_inbound to test Variable Envelope Return Path functionality.
 *
 * @package    core_message
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class inbound_test extends \advanced_testcase {

    /**
     * Perform setup tasks generic to each test.
     * This includes:
     * * configuring the messageinbound_mailbox.
     */
    public function setUp(): void {
        global $CFG;

        $this->resetAfterTest(true);

        // Setup the default Inbound Message mailbox settings.
        $CFG->messageinbound_domain = 'example.com';
        $CFG->messageinbound_enabled = true;

        // Must be no longer than 15 characters.
        $CFG->messageinbound_mailbox = 'moodlemoodle123';
    }

    /**
     * Helper to create a new Inbound Message handler.
     *
     * @param $handlerclass The class of the handler to create
     * @param $enabled Whether the handler should be enabled
     * @param $component The component
     * @param $namepace The namepace
     */
    public function helper_create_handler($handlerclass, $enabled = true, $component = 'core_test', $namespace = '\\core\\test\\') {
        global $DB;

        $classname = $namespace . $handlerclass;
        $record = \core\message\inbound\manager::record_from_handler(new $classname());
        $record->component = $component;
        $record->enabled = $enabled;
        $record->id = $DB->insert_record('messageinbound_handlers', $record);
        $handler = core_message_inbound_test_manager::handler_from_record($record);

        return $handler;
    }

    /**
     * Test that the enabled check perform as expected.
     */
    public function test_is_enabled() {
        global $CFG;

        // First clear all of the settings set in the setUp.
        $CFG->messageinbound_domain = null;
        $CFG->messageinbound_enabled = null;
        $CFG->messageinbound_mailbox = null;

        $this->assertFalse(\core\message\inbound\manager::is_enabled());

        // Check whether only setting the enabled flag keeps it disabled.
        $CFG->messageinbound_enabled = true;
        $this->assertFalse(\core\message\inbound\manager::is_enabled());

        // Check that the mailbox entry on it's own does not enable Inbound Message handling.
        $CFG->messageinbound_mailbox = 'moodlemoodle123';
        $CFG->messageinbound_domain = null;
        $this->assertFalse(\core\message\inbound\manager::is_enabled());

        // And that the domain on it's own does not.
        $CFG->messageinbound_domain = 'example.com';
        $CFG->messageinbound_mailbox = null;
        $this->assertFalse(\core\message\inbound\manager::is_enabled());

        // And that an invalid mailbox does not.
        $CFG->messageinbound_mailbox = '';
        $CFG->messageinbound_domain = 'example.com';
        $this->assertFalse(\core\message\inbound\manager::is_enabled());

        // And that an invalid domain does not.
        $CFG->messageinbound_domain = '';
        $CFG->messageinbound_mailbox = 'moodlemoodle123';
        $this->assertFalse(\core\message\inbound\manager::is_enabled());

        // Finally a test that ensures that all settings correct enables the system.
        $CFG->messageinbound_mailbox = 'moodlemoodle123';
        $CFG->messageinbound_domain = 'example.com';
        $CFG->messageinbound_enabled = true;

        $this->assertTrue(\core\message\inbound\manager::is_enabled());
    }

    /**
     * Test that data items conform to RFCs 5231, and 5322 standards for
     * addressing, and to RFC 5233 for sub-addressing.
     */
    public function test_address_constraints() {
        $handler = $this->helper_create_handler('handler_one');

        // Using the handler created, generate an address for our data entry.
        $processor = new core_message_inbound_test_helper();
        $processor->set_handler($handler->classname);

        // Generate some IDs for the data and generate addresses for them.
        $dataids = array(
            -1,
            0,
            42,
            1073741823,
            2147483647,
        );

        $user = $this->getDataGenerator()->create_user();
        foreach ($dataids as $dataid) {
            $processor->set_data($dataid);
            $address = $processor->generate($user->id);
            $this->assertNotNull($address);
            $this->assertTrue(strlen($address) > 0, 'No address generated.');
            $this->assertTrue(strpos($address, '@') !== false, 'No domain found.');
            $this->assertTrue(strpos($address, '+') !== false, 'No subaddress found.');

            // The localpart must be less than 64 characters.
            list($localpart) = explode('@', $address);
            $this->assertTrue(strlen($localpart) <= 64, 'Localpart section of address too long');

            // And the data section should be no more than 48 characters.
            list(, $datasection) = explode('+', $localpart);
            $this->assertTrue(strlen($datasection) <= 48, 'Data section of address too long');
        }
    }

    /**
     * Test that the generated e-mail addresses are sufficiently random by
     * testing the multiple handlers, multiple users, and multiple data
     * items.
     */
    public function test_address_uniqueness() {
        // Generate a set of handlers. These are in two components, and each
        // component has two different generators.
        $handlers = array();
        $handlers[] = $this->helper_create_handler('handler_one', true, 'core_test');
        $handlers[] = $this->helper_create_handler('handler_two', true, 'core_test');
        $handlers[] = $this->helper_create_handler('handler_three', true, 'core_test_example');
        $handlers[] = $this->helper_create_handler('handler_four', true, 'core_test_example');

        // Generate some IDs for the data and generate addresses for them.
        $dataids = array(
            0,
            42,
            1073741823,
            2147483647,
        );

        $users = array();
        for ($i = 0; $i < 5; $i++) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        // Store the addresses for later comparison.
        $addresses = array();

        foreach ($handlers as $handler) {
            $processor = new core_message_inbound_test_helper();
            $processor->set_handler($handler->classname);

            // Check each dataid.
            foreach ($dataids as $dataid) {
                $processor->set_data($dataid);

                // Check each user.
                foreach ($users as $user) {
                    $address = $processor->generate($user->id);
                    $this->assertFalse(isset($addresses[$address]));
                    $addresses[$address] = true;
                }
            }
        }
    }

    /**
     * Test address parsing of a generated address.
     */
    public function test_address_parsing() {
        $dataid = 42;

        // Generate a handler to use for this set of tests.
        $handler = $this->helper_create_handler('handler_one');

        // And a user.
        $user = $this->getDataGenerator()->create_user();

        // Using the handler created, generate an address for our data entry.
        $processor = new core_message_inbound_test_helper();
        $processor->set_handler($handler->classname);
        $processor->set_data($dataid);
        $address = $processor->generate($user->id);

        // We should be able to parse the address.
        $parser = new core_message_inbound_test_helper();
        $parser->process($address);
        $parsedresult = $parser->get_data();
        $this->assertEquals($user->id, $parsedresult->userid);
        $this->assertEquals($dataid, $parsedresult->datavalue);
        $this->assertEquals($dataid, $parsedresult->data->datavalue);
        $this->assertEquals($handler->id, $parsedresult->handlerid);
        $this->assertEquals($handler->id, $parsedresult->data->handler);
    }

    /**
     * Test address parsing of an address with an unrecognised format.
     */
    public function test_address_validation_invalid_format_failure() {
        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);

        // Check that validation fails when no address has been processed.
        $result = $parser->validate($user->email);
        $this->assertEquals(\core\message\inbound\address_manager::VALIDATION_INVALID_ADDRESS_FORMAT, $result);

        // Test that an address without data fails validation.
        $parser->process('bob@example.com');
        $result = $parser->validate($user->email);
        $this->assertEquals(\core\message\inbound\address_manager::VALIDATION_INVALID_ADDRESS_FORMAT, $result);

        // Test than address with a subaddress but invalid data fails with VALIDATION_UNKNOWN_DATAKEY.
        $parser->process('bob+nodata@example.com');
        $result = $parser->validate($user->email);
        $this->assertEquals(\core\message\inbound\address_manager::VALIDATION_INVALID_ADDRESS_FORMAT, $result);
    }

    /**
     * Test address parsing of an address with an unknown handler.
     */
    public function test_address_validation_unknown_handler() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Remove the handler record to invalidate it.
        $DB->delete_records('messageinbound_handlers', array(
            'id' => $handler->id,
        ));

        $parser->process($address);
        $result = $parser->validate($user->email);
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_UNKNOWN_HANDLER;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address with a disabled handler.
     */
    public function test_address_validation_disabled_handler() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Disable the handler.
        $record = \core\message\inbound\manager::record_from_handler($handler);
        $record->enabled = false;
        $DB->update_record('messageinbound_handlers', $record);

        $parser->process($address);
        $result = $parser->validate($user->email);
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_DISABLED_HANDLER;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address for an invalid user.
     */
    public function test_address_validation_invalid_user() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate(-1);

        $parser->process($address);
        $result = $parser->validate($user->email);
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_UNKNOWN_USER;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address for a disabled user.
     */
    public function test_address_validation_disabled_user() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Unconfirm the user.
        $user->confirmed = 0;
        $DB->update_record('user', $user);

        $parser->process($address);
        $result = $parser->validate($user->email);
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_DISABLED_USER;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address for an invalid key.
     */
    public function test_address_validation_invalid_key() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Remove the data record to invalidate it.
        $DB->delete_records('messageinbound_datakeys', array(
            'handler' => $handler->id,
            'datavalue' => $dataid,
        ));

        $parser->process($address);
        $result = $parser->validate($user->email);
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_UNKNOWN_DATAKEY;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address for an expired key.
     */
    public function test_address_validation_expired_key() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Expire the key by setting it's expiry time in the past.
        $key = $DB->get_record('messageinbound_datakeys', array(
            'handler' => $handler->id,
            'datavalue' => $dataid,
        ));

        $key->expires = time() - 3600;
        $DB->update_record('messageinbound_datakeys', $key);

        $parser->process($address);
        $result = $parser->validate($user->email);
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_EXPIRED_DATAKEY;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address for an invalid hash.
     */
    public function test_address_validation_invalid_hash() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Expire the key by setting it's expiry time in the past.
        $key = $DB->get_record('messageinbound_datakeys', array(
            'handler' => $handler->id,
            'datavalue' => $dataid,
        ));

        $key->datakey = 'invalid value';
        $DB->update_record('messageinbound_datakeys', $key);

        $parser->process($address);
        $result = $parser->validate($user->email);
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_INVALID_HASH;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address for an invalid sender.
     */
    public function test_address_validation_invalid_sender() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        $parser->process($address);
        $result = $parser->validate('incorrectuser@example.com');
        $expectedfail = \core\message\inbound\address_manager::VALIDATION_ADDRESS_MISMATCH;
        $this->assertEquals($expectedfail, $result & $expectedfail);
    }

    /**
     * Test address parsing of an address for an address which is correct.
     */
    public function test_address_validation_success() {
        global $DB;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');
        $dataid = 42;

        $parser = new core_message_inbound_test_helper();

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        $parser->process($address);
        $result = $parser->validate($user->email);
        $this->assertEquals(\core\message\inbound\address_manager::VALIDATION_SUCCESS, $result);

    }

    /**
     * Test that a handler with no default expiration does not have an
     * expiration time applied.
     */
    public function test_default_hander_expiry_unlimited() {
        global $DB;

        // Set the default expiry of the handler to 0 - no expiration.
        $expiration = 0;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');

        $record = \core\message\inbound\manager::record_from_handler($handler);
        $record->defaultexpiration = $expiration;
        $DB->update_record('messageinbound_handlers', $record);

        // Generate an address for the handler.
        $dataid = 42;

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Check that the datakey created matches the expirytime.
        $key = $DB->get_record('messageinbound_datakeys', array('handler' => $record->id, 'datavalue' => $dataid));

        $this->assertNull($key->expires);
    }

    /**
     * Test application of the default expiry on a handler.
     */
    public function test_default_hander_expiry_low() {
        global $DB;

        // Set the default expiry of the handler to 60 seconds.
        $expiration = 60;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');

        $record = \core\message\inbound\manager::record_from_handler($handler);
        $record->defaultexpiration = $expiration;
        $DB->update_record('messageinbound_handlers', $record);

        // Generate an address for the handler.
        $dataid = 42;

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Check that the datakey created matches the expirytime.
        $key = $DB->get_record('messageinbound_datakeys', array('handler' => $record->id, 'datavalue' => $dataid));

        $this->assertEquals($key->timecreated + $expiration, $key->expires);
    }

    /**
     * Test application of the default expiry on a handler.
     */
    public function test_default_hander_expiry_medium() {
        global $DB;

        // Set the default expiry of the handler to 3600 seconds.
        $expiration = 3600;

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $handler = $this->helper_create_handler('handler_one');

        $record = \core\message\inbound\manager::record_from_handler($handler);
        $record->defaultexpiration = $expiration;
        $DB->update_record('messageinbound_handlers', $record);

        // Generate an address for the handler.
        $dataid = 42;

        $generator = new core_message_inbound_test_helper();
        $generator->set_handler($handler->classname);
        $generator->set_data($dataid);
        $address = $generator->generate($user->id);

        // Check that the datakey created matches the expirytime.
        $key = $DB->get_record('messageinbound_datakeys', array('handler' => $record->id, 'datavalue' => $dataid));

        $this->assertEquals($key->timecreated + $expiration, $key->expires);
    }

}

/**
 * A helper function for unit testing to expose protected functions in the core_message_inbound API for testing.
 *
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_inbound_test_helper extends \core\message\inbound\address_manager {
    /**
     * The validate function.
     *
     * @param string $address
     * @return int
     */
    public function validate($address) {
        return parent::validate($address);
    }

    /**
     * The get_data function.
     *
     * @return stdClass
     */
    public function get_data() {
        return parent::get_data();
    }

    /**
     * The address processor function.
     *
     * @param string $address
     * @return void
     */
    public function process($address) {
        return parent::process($address);
    }
}

/**
 * A helper function for unit testing to expose protected functions in the core_message_inbound API for testing.
 *
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_inbound_test_manager extends \core\message\inbound\manager {
    /**
     * Helper to fetch make the handler_from_record public for unit testing.
     *
     * @param $record The handler record to fetch
     */
    public static function handler_from_record($record) {
        return parent::handler_from_record($record);
    }
}
