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
 * Test encryption.
 *
 * @package core
 * @copyright 2020 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

/**
 * Test encryption.
 *
 * @package core
 * @copyright 2020 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class encryption_testcase extends \basic_testcase {

    /**
     * Clear junk created by tests.
     */
    protected function tearDown(): void {
        global $CFG;
        $keyfile = encryption::get_key_file(encryption::METHOD_OPENSSL);
        if (file_exists($keyfile)) {
            chmod($keyfile, 0700);
        }
        $keyfile = encryption::get_key_file(encryption::METHOD_SODIUM);
        if (file_exists($keyfile)) {
            chmod($keyfile, 0700);
        }
        remove_dir($CFG->dataroot . '/secret');
        unset($CFG->nokeygeneration);
    }

    protected function setUp(): void {
        $this->tearDown();

        require_once(__DIR__ . '/fixtures/testable_encryption.php');
    }

    /**
     * Tests using Sodium need to check the extension is available.
     *
     * @param string $method Encryption method
     */
    protected function require_sodium(string $method) {
        if ($method == encryption::METHOD_SODIUM) {
            if (!encryption::is_sodium_installed()) {
                $this->markTestSkipped('Sodium not installed');
            }
        }
    }

    /**
     * Many of the tests work with both encryption methods.
     *
     * @return array[] Array of method options for test
     */
    public function encryption_method_provider(): array {
        return ['Sodium' => [encryption::METHOD_SODIUM], 'OpenSSL' => [encryption::METHOD_OPENSSL]];
    }

    /**
     * Tests the create_keys and get_key functions.
     *
     * @param string $method Encryption method
     * @dataProvider encryption_method_provider
     */
    public function test_create_key(string $method): void {
        $this->require_sodium($method);
        encryption::create_key($method);
        $key = testable_encryption::get_key($method);

        // Conveniently, both encryption methods have the same key length.
        $this->assertEquals(32, strlen($key));

        $this->expectExceptionMessage('Key already exists');
        encryption::create_key($method);
    }

    /**
     * Tests encryption and decryption with empty strings.
     *
     * @throws \moodle_exception
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
        $this->require_sodium($method);

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
        $this->require_sodium($method);

        $this->expectExceptionMessage('Insufficient data');
        switch ($method) {
            case encryption::METHOD_OPENSSL:
                // It needs min 49 bytes (16 bytes IV + 32 bytes HMAC + 1 byte data).
                $justtooshort = '0123456789abcdef0123456789abcdef0123456789abcdef';
                break;
            case encryption::METHOD_SODIUM:
                // Sodium needs 25 bytes at least as far as our code is concerned (24 bytes IV + 1
                // byte data); it splits out any authentication hashes itself.
                $justtooshort = '0123456789abcdef01234567';
                break;
        }

        encryption::decrypt($method . ':' .base64_encode($justtooshort));
    }

    /**
     * Tests decryption when data is not valid base64.
     *
     * @dataProvider encryption_method_provider
     * @param string $method Encryption method
     */
    public function test_decrypt_notbase64(string $method): void {
        $this->require_sodium($method);

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
        $this->require_sodium($method);

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
        $this->require_sodium($method);

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
        $this->require_sodium($method);

        // Set the key to something bogus.
        $folder = $CFG->dataroot . '/secret/key';
        check_dir_exists($folder);
        file_put_contents(encryption::get_key_file($method), 'silly');

        switch ($method) {
            case encryption::METHOD_SODIUM:
                $this->expectExceptionMessageMatches('/(should|must) be SODIUM_CRYPTO_SECRETBOX_KEYBYTES bytes/');
                break;

            case encryption::METHOD_OPENSSL:
                $this->expectExceptionMessage('Invalid key');
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
        $this->require_sodium($method);

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
     * @throws \moodle_exception
     */
    public function test_encrypt_and_decrypt_realdata(string $method): void {
        $this->require_sodium($method);

        // Encrypt short string.
        $encrypted = encryption::encrypt('frogs', $method);
        $this->assertNotEquals('frogs', $encrypted);
        $this->assertEquals('frogs', encryption::decrypt($encrypted));

        // Encrypt really long string (1 MB).
        $long = str_repeat('X', 1024 * 1024);
        $this->assertEquals($long, encryption::decrypt(encryption::encrypt($long, $method)));
    }
}
