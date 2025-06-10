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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\services;

use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\SettingsHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class encryption_service {

    /**
     * Auth lifetime.
     */
    const AUTH_LIFE_TIME = 60;

    /**
     * Define the number of blocks that should be read from the source file for each chunk.
     * For 'AES-256-CBC' each block consist of 16 bytes.
     * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
     * to read/write shorter or longer chunks.
     */
    const FILE_ENCRYPTION_BLOCKS = 10000;

    /**
     * Encryption algorithm.
     */
    const ENCRYPTION_ALG = 'AES-256-CBC';

    /** @var string */
    public $encryptionkey;
    /** @var string */
    public $clientidentifier;

    /**
     * Encryption service construct.
     * @throws \dml_exception
     */
    public function __construct() {
        $this->encryptionkey    = SettingsHelper::get_setting('encryptionkey');
        $this->clientidentifier = SettingsHelper::get_setting('clientidentifier');
    }

    /**
     * Validate credentials method.
     *
     * @return bool
     */
    public function validate_credentials() {
        return (empty($this->encryptionkey) || empty($this->clientidentifier)) ? false : true;
    }

    /**
     * Encrypt data
     *
     * @param string $data
     * @return string Encrypted data
     */
    public function encrypt($data) {
        $ivlength = openssl_cipher_iv_length(self::ENCRYPTION_ALG);
        $iv = openssl_random_pseudo_bytes($ivlength);

        return openssl_encrypt(
            $data, self::ENCRYPTION_ALG, $this->encryptionkey, 0, $iv
            ) . ".." . base64_encode($iv);
    }

    /**
     * Decrypt data
     *
     * @param string $data
     * @return false|array
     */
    public function decrypt($encryptedddata) {
        list($data, $iv) = explode('..', $encryptedddata);
        $iv = base64_decode($iv);

        return openssl_decrypt($data, self::ENCRYPTION_ALG, $this->encryptionkey, 0, $iv);
    }

    /**
     * Build auth header.
     *
     * @return string
     */
    public function build_auth_header() {
        return $this->encrypt(json_encode([
            'clientidentifier' => $this->clientidentifier,
            'exp' => time() + self::AUTH_LIFE_TIME,
        ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * Encrypt file
     *
     * @param string $sourcefilepath
     * @param string $destfilepath
     * @return bool
     */
    public function encrypt_file($sourcefilepath, $destfilepath) {

        $ivlength = openssl_cipher_iv_length(self::ENCRYPTION_ALG);
        $iv = openssl_random_pseudo_bytes($ivlength);

        $result = true;
        if ($fpout = fopen($destfilepath, 'w')) {
            // Put the initialzation vector to the beginning of the file.
            fwrite($fpout, $iv);
            if ($fpin = fopen($sourcefilepath, 'rb')) {
                while (!feof($fpin)) {
                    $plaintext = fread($fpin, $ivlength * self::FILE_ENCRYPTION_BLOCKS);
                    $ciphertext = openssl_encrypt($plaintext, self::ENCRYPTION_ALG, $this->encryptionkey, OPENSSL_RAW_DATA, $iv);
                    fwrite($fpout, $ciphertext);
                }
                fclose($fpin);
            } else {
                DebugHelper::error_log('Can not open file: ' . $sourcefilepath);
                $result = false;
            }
            fclose($fpout);
        } else {
            DebugHelper::error_log('Can not open file: ' . $destfilepath);
            $result = false;
        }

        return $result;
    }

    /**
     * Decrypt file
     *
     * @param string $data
     * @return false|array
     */
    public function decrypt_file($sourcefilepath, $destfilepath) {

        $ivlength = openssl_cipher_iv_length(self::ENCRYPTION_ALG);

        $result = true;
        if ($fpout = fopen($destfilepath, 'w')) {
            if ($fpin = fopen($sourcefilepath, 'rb')) {
                // Get the initialzation vector from the beginning of the file.
                $iv = fread($fpin, $ivlength);
                while (!feof($fpin)) {
                    // We have to read one block more for decrypting than for encrypting.
                    $ciphertext = fread($fpin, $ivlength * (self::FILE_ENCRYPTION_BLOCKS + 1));
                    $plaintext = openssl_decrypt($ciphertext, self::ENCRYPTION_ALG, $this->encryptionkey,
                        OPENSSL_RAW_DATA, $iv);

                    fwrite($fpout, $plaintext);
                }
                fclose($fpin);
            } else {
                DebugHelper::error_log('Can not open file: ' . $sourcefilepath);
                $result = false;
            }
            fclose($fpout);
        } else {
            DebugHelper::error_log('Can not open file: ' . $destfilepath);
            $result = false;
        }

        return $result;
    }
}
