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
 * Tests for the handling of encrypted contents in backup and restore.
 *
 * @package core_backup
 * @copyright 2016 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_custom_fields.php');

class core_backup_encrypted_content_testscase extends advanced_testcase {

    public function setUp(): void {
        if (!function_exists('openssl_encrypt')) {
            $this->markTestSkipped('OpenSSL extension is not loaded.');

        } else if (!function_exists('hash_hmac')) {
            $this->markTestSkipped('Hash extension is not loaded.');

        } else if (!in_array(backup::CIPHER, openssl_get_cipher_methods())) {
            $this->markTestSkipped('Expected cipher not available: ' . backup::CIPHER);
        }
    }

    public function test_encrypted_final_element() {

        $this->resetAfterTest(true);

        // Some basic verifications.
        $efe = new encrypted_final_element('test', array('encrypted'));
        $this->assertInstanceOf('encrypted_final_element', $efe);
        $this->assertSame('test', $efe->get_name());
        $atts = $efe->get_attributes();
        $this->assertCount(1, $atts);
        $att = reset($atts);
        $this->assertInstanceOf('backup_attribute', $att);
        $this->assertSame('encrypted', $att->get_name());

        // Using a manually defined (incorrect length) key.
        $efe = new encrypted_final_element('test', array('encrypted'));
        $key = 'this_in_not_correct_32_byte_key';
        try {
            set_config('backup_encryptkey', base64_encode($key), 'backup');
            $efe->set_value('tiny_secret');
            $this->fail('Expecting base_element_struct_exception exception, none happened');
        } catch (exception $e) {
            $this->assertInstanceOf('base_element_struct_exception', $e);
            $this->assertEquals('encrypted_final_element incorrect key length', $e->errorcode);

        }

        // Using a manually defined (correct length) key.
        $efe = new encrypted_final_element('test', array('testattr', 'encrypted'));
        $key = hash('md5', 'Moodle rocks and this is not secure key, who cares, it is a test');
        set_config('backup_encryptkey', base64_encode($key), 'backup');
        $this->assertEmpty($efe->get_value());
        $secret = 'This is a secret message that nobody else will be able to read but me 💩 ';
        $efe->set_value($secret);
        $atts = $efe->get_attributes();
        $this->assertCount(2, $atts);
        $this->assertArrayHasKey('encrypted', $atts); // We added it explicitly.
        $this->assertTrue($atts['encrypted']->is_set());
        $this->assertSame('true', $atts['encrypted']->get_value());
        $this->assertNotEmpty($efe->get_value());
        $this->assertTrue($efe->is_set());
        // Get the crypted content and decrypt it manually.
        $ctext = $efe->get_value();
        $hmaclen = 32; // SHA256 is 32 bytes.
        $ivlen = openssl_cipher_iv_length(backup::CIPHER);
        list($hmac, $iv, $text) = array_values(unpack("a{$hmaclen}hmac/a{$ivlen}iv/a*text", base64_decode($ctext)));
        $this->assertSame(hash_hmac('sha256', $iv . $text, $key, true), $hmac);
        $this->assertSame($secret, openssl_decrypt($text, backup::CIPHER, $key, OPENSSL_RAW_DATA, $iv));

        // Using the default site-generated key.
        $efe = new encrypted_final_element('test', array('testattr'));
        $this->assertEmpty($efe->get_value());
        $secret = 'This is a secret message that nobody else will be able to read but me 💩 ';
        $efe->set_value($secret);
        $atts = $efe->get_attributes();
        $this->assertCount(2, $atts);
        $this->assertArrayHasKey('encrypted', $atts); // Was added automatcally, we did not specify it.
        $this->assertTrue($atts['encrypted']->is_set());
        $this->assertSame('true', $atts['encrypted']->get_value());
        $this->assertNotEmpty($efe->get_value());
        $this->assertTrue($efe->is_set());
        // Get the crypted content and decrypt it manually.
        $ctext = $efe->get_value();
        $hmaclen = 32; // SHA256 is 32 bytes.
        $ivlen = openssl_cipher_iv_length(backup::CIPHER);
        list($hmac, $iv, $text) = array_values(unpack("a{$hmaclen}hmac/a{$ivlen}iv/a*text", base64_decode($ctext)));
        $key = base64_decode(get_config('backup', 'backup_encryptkey'));
        $this->assertSame(hash_hmac('sha256', $iv . $text, $key, true), $hmac);
        $this->assertSame($secret, openssl_decrypt($text, backup::CIPHER, $key, OPENSSL_RAW_DATA, $iv));
    }
}
