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

namespace core;

use advanced_testcase;

/**
 * Test encryption.
 *
 * @package core
 * @copyright 2020 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  \core\encryption
 */
final class encryption_test extends advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        require_once(__DIR__ . '/fixtures/testable_encryption.php');
    }

    /**
     * Return a list of supported encryption methods
     *
     * @return array[] Array of method options for test
     */
    public static function encryption_method_provider(): array {
        return [
            'Sodium' => [encryption::METHOD_SODIUM],
        ];
    }

    /**
     * Tests the create_keys and get_key functions.
     *
     * @param string $method Encryption method
     * @dataProvider encryption_method_provider
     */
    public function test_create_key(string $method): void {
        encryption::create_key($method);
        $key = testable_encryption::get_key($method);
        $this->assertEquals(32, strlen($key));

        $this->expectExceptionMessage('Key already exists');
        encryption::create_key($method);
    }

    /**
     * Tests encryption and decryption with empty strings.
     */
    public function test_encrypt_and_decrypt_empty(): void {
        $this->assertEquals('', encryption::encrypt(''));
        $this->assertEquals('', encryption::decrypt(''));
    }

    /**
     * Tests encryption when the keys weren't created yet.
     *
     * @param string $method Encryption method
     * @dataProvider encryption_method_provider
     */
    public function test_encrypt_nokeys(string $method): void {
        global $CFG;

        // Prevent automatic generation of keys.
        $CFG->nokeygeneration = true;
        $this->expectExceptionMessage('Key not found');
        encryption::encrypt('frogs', $method);
    }

    /**
     * Tests decryption when the data has a different encryption method
     */
    public function test_decrypt_wrongmethod(): void {
        $this->expectExceptionMessage('Data does not match a supported encryption method');
        encryption::decrypt('FAKE-CIPHER-METHOD:xx');
    }

    /**
     * Tests decryption when not enough data is supplied to get the IV and some data.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_decrypt_tooshort(string $method): void {
        switch ($method) {
            case encryption::METHOD_SODIUM:
                // Sodium needs 25 bytes at least as far as our code is concerned (24 bytes IV + 1
                // byte data); it splits out any authentication hashes itself.
                $justtooshort = '0123456789abcdef01234567';
                break;
        }

        $this->expectExceptionMessage('Insufficient data');
        encryption::decrypt($method . ':' .base64_encode($justtooshort));
    }

    /**
     * Tests decryption when data is not valid base64.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_decrypt_notbase64(string $method): void {
        $this->expectExceptionMessage('Invalid base64 data');
        encryption::decrypt($method . ':' . chr(160));
    }

    /**
     * Tests decryption when the keys weren't created yet.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_decrypt_nokeys(string $method): void {
        global $CFG;

        // Prevent automatic generation of keys.
        $CFG->nokeygeneration = true;
        $this->expectExceptionMessage('Key not found');
        encryption::decrypt($method . ':' . base64_encode(
                '0123456789abcdef0123456789abcdef0123456789abcdef0'));
    }

    /**
     * Test automatic generation of keys when needed.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_auto_key_generation(string $method): void {

        // Allow automatic generation (default).
        $encrypted = encryption::encrypt('frogs', $method);
        $this->assertEquals('frogs', encryption::decrypt($encrypted));
    }

    /**
     * Checks that invalid key causes failures.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_invalid_key(string $method): void {
        global $CFG;

        // Set the key to something bogus.
        $folder = $CFG->dataroot . '/secret/key';
        check_dir_exists($folder);
        file_put_contents(encryption::get_key_file($method), 'silly');

        switch ($method) {
            case encryption::METHOD_SODIUM:
                $this->expectExceptionMessageMatches('/(should|must) be SODIUM_CRYPTO_SECRETBOX_KEYBYTES bytes/');
                break;
        }

        encryption::encrypt('frogs', $method);
    }

    /**
     * Checks that modified data causes failures.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_modified_data(string $method): void {
        $encrypted = encryption::encrypt('frogs', $method);
        $mainbit = base64_decode(substr($encrypted, strlen($method) + 1));
        $mainbit = substr($mainbit, 0, 16) . 'X' . substr($mainbit, 16);
        $encrypted = $method . ':' . base64_encode($mainbit);
        $this->expectExceptionMessage('Integrity check failed');
        encryption::decrypt($encrypted);
    }

    /**
     * Tests encryption and decryption for real.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_encrypt_and_decrypt_realdata(string $method): void {

        // Encrypt short string.
        $encrypted = encryption::encrypt('frogs', $method);
        $this->assertNotEquals('frogs', $encrypted);
        $this->assertEquals('frogs', encryption::decrypt($encrypted));

        // Encrypt really long string (1 MB).
        $long = str_repeat('X', 1024 * 1024);
        $this->assertEquals($long, encryption::decrypt(encryption::encrypt($long, $method)));
    }
}
