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
 * Incoming Message address manager.
 *
 * @package    core_message
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\message\inbound;

defined('MOODLE_INTERNAL') || die();

/**
 * Incoming Message address manager.
 *
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class address_manager {

    /**
     * @var int The size of the hash component of the address.
     * Note: Increasing this value will invalidate all previous key values
     * and reduce the potential length of the e-mail address being checked.
     * Do not change this value.
     */
    const HASHSIZE = 24;

    /**
     * @var int A validation status indicating successful validation
     */
    const VALIDATION_SUCCESS = 0;

    /**
     * @var int A validation status indicating an invalid address format.
     * Typically this is an address which does not contain a subaddress or
     * all of the required data.
     */
    const VALIDATION_INVALID_ADDRESS_FORMAT = 1;

    /**
     * @var int A validation status indicating that a handler could not
     * be found for this address.
     */
    const VALIDATION_UNKNOWN_HANDLER = 2;

    /**
     * @var int A validation status indicating that an unknown user was specified.
     */
    const VALIDATION_UNKNOWN_USER = 4;

    /**
     * @var int A validation status indicating that the data key specified could not be found.
     */
    const VALIDATION_UNKNOWN_DATAKEY = 8;

    /**
     * @var int A validation status indicating that the mail processing handler was not enabled.
     */
    const VALIDATION_DISABLED_HANDLER = 16;

    /**
     * @var int A validation status indicating that the user specified was deleted or unconfirmed.
     */
    const VALIDATION_DISABLED_USER = 32;

    /**
     * @var int A validation status indicating that the datakey specified had reached it's expiration time.
     */
    const VALIDATION_EXPIRED_DATAKEY = 64;

    /**
     * @var int A validation status indicating that the hash could not be verified.
     */
    const VALIDATION_INVALID_HASH = 128;

    /**
     * @var int A validation status indicating that the originator address did not match the user on record.
     */
    const VALIDATION_ADDRESS_MISMATCH = 256;

    /**
     * The handler for the subsequent Inbound Message commands.
     * @var \core\message\inbound\handler
     */
    private $handler;

    /**
     * The ID of the data record
     * @var int
     */
    private $datavalue;

    /**
     * The ID of the data record
     * @var string
     */
    private $datakey;

    /**
     * The processed data record.
     * @var \stdClass
     */
    private $record;

    /**
     * The user.
     * @var \stdClass
     */
    private $user;

    /**
     * Set the handler to use for the subsequent Inbound Message commands.
     *
     * @param string $classname The name of the class for the handler.
     */
    public function set_handler($classname) {
        $this->handler = manager::get_handler($classname);
    }

    /**
     * Return the active handler.
     *
     * @return \core\message\inbound\handler|null;
     */
    public function get_handler() {
        return $this->handler;
    }

    /**
     * Specify an integer data item value for this record.
     *
     * @param int $datavalue The value of the data item.
     * @param string $datakey A hash to use for the datakey
     */
    public function set_data($datavalue, $datakey = null) {
        $this->datavalue = $datavalue;

        // We must clear the datakey when changing the datavalue.
        $this->set_data_key($datakey);
    }

    /**
     * Specify a known data key for this data item.
     *
     * If specified, the datakey must already exist in the messageinbound_datakeys
     * table, typically as a result of a previous Inbound Message setup.
     *
     * This is intended as a performance optimisation when sending many
     * e-mails with different data to many users.
     *
     * @param string $datakey A hash to use for the datakey
     */
    public function set_data_key($datakey = null) {
        $this->datakey = $datakey;
    }

    /**
     * Return the data key for the data item.
     *
     * If no data key has been defined yet, this will call generate_data_key() to generate a new key on the fly.
     * @return string The secret key for this data item.
     */
    public function fetch_data_key() {
        global $CFG, $DB;

        // Only generate a key if Inbound Message is actually enabled, and the handler is enabled.
        if (!isset($CFG->messageinbound_enabled) || !$this->handler || !$this->handler->enabled) {
            return null;
        }

        if (!isset($this->datakey)) {
            // Attempt to fetch an existing key first if one has not already been specified.
            $datakey = $DB->get_field('messageinbound_datakeys', 'datakey', array(
                    'handler' => $this->handler->id,
                    'datavalue' => $this->datavalue,
                ));
            if (!$datakey) {
                $datakey = $this->generate_data_key();
            }
            $this->datakey = $datakey;
        }

        return $this->datakey;
    }

    /**
     * Generate a new secret key for the current data item and handler combination.
     *
     * @return string The new generated secret key for this data item.
     */
    protected function generate_data_key() {
        global $DB;

        $key = new \stdClass();
        $key->handler = $this->handler->id;
        $key->datavalue = $this->datavalue;
        $key->datakey = md5($this->datavalue . '_' . time() . random_string(40));
        $key->timecreated = time();

        if ($this->handler->defaultexpiration) {
            // Apply the default expiration time to the datakey.
            $key->expires = $key->timecreated + $this->handler->defaultexpiration;
        }
        $DB->insert_record('messageinbound_datakeys', $key);

        return $key->datakey;
    }

    /**
     * Generate an e-mail address for the Inbound Message handler, storing a private
     * key for the data object if one was not specified.
     *
     * @param int $userid The ID of the user to generated an address for.
     * @param string $userkey The unique key for this user. If not specified this will be retrieved using
     * get_user_key(). This key must have been created using get_user_key(). This parameter is provided as a performance
     * optimisation for when generating multiple addresses for the same user.
     * @return string|null The generated address, or null if an address could not be generated.
     */
    public function generate($userid, $userkey = null) {
        global $CFG;

        // Ensure that Inbound Message is enabled and that there is enough information to proceed.
        if (!manager::is_enabled()) {
            return null;
        }

        if ($userkey == null) {
            $userkey = get_user_key('messageinbound_handler', $userid);
        }

        // Ensure that the minimum requirements are in place.
        if (!isset($this->handler) || !$this->handler) {
            throw new \coding_exception('Inbound Message handler not specified.');
        }

        // Ensure that the requested handler is actually enabled.
        if (!$this->handler->enabled) {
            return null;
        }

        if (!isset($this->datavalue)) {
            throw new \coding_exception('Inbound Message data item has not been specified.');
        }

        $data = array(
            self::pack_int($this->handler->id),
            self::pack_int($userid),
            self::pack_int($this->datavalue),
            pack('H*', substr(md5($this->fetch_data_key() . $userkey), 0, self::HASHSIZE)),
        );
        $subaddress = base64_encode(implode($data));

        return $CFG->messageinbound_mailbox . '+' . $subaddress . '@' . $CFG->messageinbound_domain;
    }

    /**
     * Determine whether the supplied address is of the correct format.
     *
     * @param string $address The address to test
     * @return bool Whether the address matches the correct format
     */
    public static function is_correct_format($address) {
        global $CFG;
        // Messages must match the format mailbox+[data]@domain.
        return preg_match('/' . $CFG->messageinbound_mailbox . '\+[^@]*@' . $CFG->messageinbound_domain . '/', $address);
    }

    /**
     * Process an inbound address to obtain the data stored within it.
     *
     * @param string $address The fully formed e-mail address to process.
     */
    protected function process($address) {
        global $DB;

        if (!self::is_correct_format($address)) {
            // This address does not contain a subaddress to parse.
            return;
        }

        // Ensure that the instance record is empty.
        $this->record = null;

        $record = new \stdClass();
        $record->address = $address;

        list($localpart) = explode('@', $address, 2);
        list($record->mailbox, $encodeddata) = explode('+', $localpart, 2);
        $data = base64_decode($encodeddata, true);
        if (!$data) {
            // This address has no valid data.
            return;
        }

        $content = @unpack('N2handlerid/N2userid/N2datavalue/H*datakey', $data);

        if (!$content) {
            // This address has no data.
            return;
        }

        if (PHP_INT_SIZE === 8) {
            // 64-bit machine.
            $content['handlerid'] = $content['handlerid1'] << 32 | $content['handlerid2'];
            $content['userid']    = $content['userid1'] << 32    | $content['userid2'];
            $content['datavalue'] = $content['datavalue1'] << 32 | $content['datavalue2'];
        } else {
            if ($content['handlerid1'] > 0 || $content['userid1'] > 0 || $content['datavalue1'] > 0) {
                // Any 64-bit integer which is greater than the 32-bit integer size will have a non-zero value in the first
                // half of the integer.
                throw new \moodle_exception('Mixed environment.' .
                    ' Key generated with a 64-bit machine but received into a 32-bit machine.');
            }
            $content['handlerid'] = $content['handlerid2'];
            $content['userid']    = $content['userid2'];
            $content['datavalue'] = $content['datavalue2'];
        }

        // Clear the 32-bit to 64-bit variables away.
        unset($content['handlerid1']);
        unset($content['handlerid2']);
        unset($content['userid1']);
        unset($content['userid2']);
        unset($content['datavalue1']);
        unset($content['datavalue2']);

        $record = (object) array_merge((array) $record, $content);

        // Fetch the user record.
        $record->user = $DB->get_record('user', array('id' => $record->userid));

        // Fetch and set the handler.
        if ($handler = manager::get_handler_from_id($record->handlerid)) {
            $this->handler = $handler;

            // Retrieve the record for the data key.
            $record->data = $DB->get_record('messageinbound_datakeys',
                    array('handler' => $handler->id, 'datavalue' => $record->datavalue));
        }

        $this->record = $record;
    }

    /**
     * Retrieve the data parsed from the address.
     *
     * @return \stdClass the parsed data.
     */
    public function get_data() {
        return $this->record;
    }

    /**
     * Ensure that the parsed data is valid, and if the handler requires address validation, validate the sender against
     * the user record of identified user record.
     *
     * @param string $address The fully formed e-mail address to process.
     * @return int The validation status.
     */
    protected function validate($address) {
        if (!$this->record) {
            // The record does not exist, so there is nothing to validate against.
            return self::VALIDATION_INVALID_ADDRESS_FORMAT;
        }

        // Build the list of validation errors.
        $returnvalue = 0;

        if (!$this->handler) {
            $returnvalue += self::VALIDATION_UNKNOWN_HANDLER;
        } else if (!$this->handler->enabled) {
            $returnvalue += self::VALIDATION_DISABLED_HANDLER;
        }

        if (!isset($this->record->data) || !$this->record->data) {
            $returnvalue += self::VALIDATION_UNKNOWN_DATAKEY;
        } else if ($this->record->data->expires != 0 && $this->record->data->expires < time()) {
            $returnvalue += self::VALIDATION_EXPIRED_DATAKEY;
        } else {

            if (!$this->record->user) {
                $returnvalue += self::VALIDATION_UNKNOWN_USER;
            } else {
                if ($this->record->user->deleted || !$this->record->user->confirmed) {
                    $returnvalue += self::VALIDATION_DISABLED_USER;
                }

                $userkey = get_user_key('messageinbound_handler', $this->record->user->id);
                $hashvalidation = substr(md5($this->record->data->datakey . $userkey), 0, self::HASHSIZE) == $this->record->datakey;
                if (!$hashvalidation) {
                    // The address data did not check out, so the originator is deemed invalid.
                    $returnvalue += self::VALIDATION_INVALID_HASH;
                }

                if ($this->handler->validateaddress) {
                    // Validation of the sender's e-mail address is also required.
                    if ($address !== $this->record->user->email) {
                        // The e-mail address of the originator did not match the
                        // address held on record for this user.
                        $returnvalue += self::VALIDATION_ADDRESS_MISMATCH;
                    }
                }
            }
        }

        return $returnvalue;
    }

    /**
     * Process the message recipient, load the handler, and then validate
     * the sender with the associated data record.
     *
     * @param string $recipient The recipient of the message
     * @param string $sender The sender of the message
     */
    public function process_envelope($recipient, $sender) {
        // Process the recipient address to retrieve the handler data.
        $this->process($recipient);

        // Validate the retrieved data against the e-mail address of the originator.
        return $this->validate($sender);
    }

    /**
     * Process the message against the relevant handler.
     *
     * @param \stdClass $messagedata The data for the current message being processed.
     * @return mixed The result of the handler's message processor. A truthy result suggests a successful send.
     */
    public function handle_message(\stdClass $messagedata) {
        $this->record = $this->get_data();
        return $this->handler->process_message($this->record, $messagedata);
    }

    /**
     * Pack an integer into a pair of 32-bit numbers.
     *
     * @param int $int The integer to pack
     * @return string The encoded binary data
     */
    protected function pack_int($int) {
        // If PHP environment is running on a 64-bit.
        if (PHP_INT_SIZE === 8) {
            // Will be used to ensures that the result remains as a 32-bit unsigned integer and
            // doesn't extend beyond 32 bits.
            $notation = 0xffffffff;

            if ($int < 0) {
                // If the given integer is negative, set it to -1.
                $l = -1;
            } else {
                // Otherwise, calculate the upper 32 bits of the 64-bit integer.
                $l = ($int >> 32) & $notation;
            }

            // Calculate the lower 32 bits of the 64-bit integer.
            $r = $int & $notation;

            // Pack the values of $l (upper 32 bits) and $r (lower 32 bits) into a binary string format.
            return pack('NN', $l, $r);
        } else {
            // Pack the values into a binary string format.
            return pack('NN', 0, $int);
        }
    }
}
