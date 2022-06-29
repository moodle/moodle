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
 * Class used to encrypt or decrypt data.
 *
 * @package core
 * @copyright 2020 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

/**
 * Class used to encrypt or decrypt data.
 *
 * @package core
 * @copyright 2020 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 3.11 MDL-71420 - the openssl part of the class only.
 * @todo MDL-71421 Remove the openssl part in Moodle 4.2.
 */
class encryption {
    /** @var string Encryption method: Sodium */
    const METHOD_SODIUM = 'sodium';

    // TODO: MDL-71421 - Remove the following openssl constants and all uses once sodium becomes a requirement in Moodle 4.2.

    /** @var string Encryption method: hand-coded OpenSSL (less safe) */
    const METHOD_OPENSSL = 'openssl-aes-256-ctr';

    /** @var string OpenSSL cipher method */
    const OPENSSL_CIPHER = 'AES-256-CTR';

    /**
     * Checks if Sodium is installed.
     *
     * @return bool True if the Sodium extension is available
     */
    public static function is_sodium_installed(): bool {
        return extension_loaded('sodium');
    }

    /**
     * Gets the encryption method to use. We use the Sodium extension if it is installed, or
     * otherwise, OpenSSL.
     *
     * @return string Current encryption method
     */
    protected static function get_encryption_method(): string {
        if (self::is_sodium_installed()) {
            return self::METHOD_SODIUM;
        } else {
            return self::METHOD_OPENSSL;
        }
    }

    /**
     * Creates a key for the server.
     *
     * @param string|null $method Encryption method (only if you want to create a non-default key)
     * @param bool $chmod If true, restricts the file access of the key
     * @throws \moodle_exception If the server already has a key, or there is an error
     */
    public static function create_key(?string $method = null, bool $chmod = true): void {
        if ($method === null) {
            $method = self::get_encryption_method();
        }

        if (self::key_exists($method)) {
            throw new \moodle_exception('encryption_keyalreadyexists', 'error');
        }

        // Don't make it read-only in Behat or it will fail to clear for future runs.
        if (defined('BEHAT_SITE_RUNNING')) {
            $chmod = false;
        }

        // Generate the key.
        switch ($method) {
            case self::METHOD_SODIUM:
                $key = sodium_crypto_secretbox_keygen();
                break;
            case self::METHOD_OPENSSL:
                $key = openssl_random_pseudo_bytes(32);
                break;
            default:
                throw new \coding_exception('Unknown method: ' . $method);
        }

        // Store the key, making it readable only by server.
        $folder = self::get_key_folder();
        check_dir_exists($folder);
        $keyfile = self::get_key_file($method);
        file_put_contents($keyfile, $key);
        if ($chmod) {
            chmod($keyfile, 0400);
        }
    }

    /**
     * Gets the folder used to store the secret key.
     *
     * @return string Folder path
     */
    protected static function get_key_folder(): string {
        global $CFG;
        return ($CFG->secretdataroot ?? $CFG->dataroot . '/secret') . '/key';
    }

    /**
     * Gets the file path used to store the secret key. The filename contains the cipher method,
     * so that if necessary to transition in future it would be possible to have multiple.
     *
     * @param string|null $method Encryption method (only if you want to get a non-default key)
     * @return string Full path to file
     */
    public static function get_key_file(?string $method = null): string {
        if ($method === null) {
            $method = self::get_encryption_method();
        }

        return self::get_key_folder() . '/' . $method . '.key';
    }

    /**
     * Checks if there is a key file.
     *
     * @param string|null $method Encryption method (only if you want to check a non-default key)
     * @return bool True if there is a key file
     */
    public static function key_exists(?string $method = null): bool {
        if ($method === null) {
            $method = self::get_encryption_method();
        }

        return file_exists(self::get_key_file($method));
    }

    /**
     * Gets the current key, automatically creating it if there isn't one yet.
     *
     * @param string|null $method Encryption method (only if you want to get a non-default key)
     * @return string The key (binary)
     * @throws \moodle_exception If there isn't one already (and creation is disabled)
     */
    protected static function get_key(?string $method = null): string {
        global $CFG;

        if ($method === null) {
            $method = self::get_encryption_method();
        }

        $keyfile = self::get_key_file($method);
        if (!file_exists($keyfile) && empty($CFG->nokeygeneration)) {
            self::create_key($method);
        }
        $result = @file_get_contents($keyfile);
        if ($result === false) {
            throw new \moodle_exception('encryption_nokey', 'error');
        }
        return $result;
    }

    /**
     * Gets the length in bytes of the initial values data required.
     *
     * @param string $method Crypto method
     * @return int Length in bytes
     */
    protected static function get_iv_length(string $method): int {
        switch ($method) {
            case self::METHOD_SODIUM:
                return SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
            case self::METHOD_OPENSSL:
                return openssl_cipher_iv_length(self::OPENSSL_CIPHER);
            default:
                throw new \coding_exception('Unknown method: ' . $method);
        }
    }

    /**
     * Encrypts data using the server's key.
     *
     * Note there is a special case - the empty string is not encrypted.
     *
     * @param string $data Data to encrypt, or empty string for no data
     * @param string|null $method Encryption method (only if you want to use a non-default method)
     * @return string Encrypted data, or empty string for no data
     * @throws \moodle_exception If the key doesn't exist, or the string is too long
     */
    public static function encrypt(string $data, ?string $method = null): string {
        if ($data === '') {
            return '';
        } else {
            if ($method === null) {
                $method = self::get_encryption_method();
            }

            // Create IV.
            $iv = random_bytes(self::get_iv_length($method));

            // Encrypt data.
            switch($method) {
                case self::METHOD_SODIUM:
                    try {
                        $encrypted = sodium_crypto_secretbox($data, $iv, self::get_key($method));
                    } catch (\SodiumException $e) {
                        throw new \moodle_exception('encryption_encryptfailed', 'error', '', null, $e->getMessage());
                    }
                    break;

                case self::METHOD_OPENSSL:
                    // This may not be a secure authenticated encryption implementation;
                    // administrators should enable the Sodium extension.
                    $key = self::get_key($method);
                    if (strlen($key) !== 32) {
                            throw new \moodle_exception('encryption_invalidkey', 'error');
                    }
                    $encrypted = @openssl_encrypt($data, self::OPENSSL_CIPHER, $key, OPENSSL_RAW_DATA, $iv);
                    if ($encrypted === false) {
                        throw new \moodle_exception('encryption_encryptfailed', 'error',
                                '', null, openssl_error_string());
                    }
                    $hmac = hash_hmac('sha256', $iv . $encrypted, $key, true);
                    $encrypted .= $hmac;
                    break;

                default:
                    throw new \coding_exception('Unknown method: ' . $method);
            }

            // Encrypted data is cipher method plus IV plus encrypted data.
            return $method . ':' . base64_encode($iv . $encrypted);
        }
    }

    /**
     * Decrypts data using the server's key. The decryption works with either supported method.
     *
     * @param string $data Data to decrypt
     * @return string Decrypted data
     */
    public static function decrypt(string $data): string {
        if ($data === '') {
            return '';
        } else {
            if (preg_match('~^(' . self::METHOD_OPENSSL . '|' . self::METHOD_SODIUM . '):~', $data, $matches)) {
                $method = $matches[1];
            } else {
                throw new \moodle_exception('encryption_wrongmethod', 'error');
            }
            $realdata = base64_decode(substr($data, strlen($method) + 1), true);
            if ($realdata === false) {
                throw new \moodle_exception('encryption_decryptfailed', 'error',
                        '', null, 'Invalid base64 data');
            }

            $ivlength = self::get_iv_length($method);
            if (strlen($realdata) < $ivlength + 1) {
                throw new \moodle_exception('encryption_decryptfailed', 'error',
                        '', null, 'Insufficient data');
            }
            $iv = substr($realdata, 0, $ivlength);
            $encrypted = substr($realdata, $ivlength);

            switch ($method) {
                case self::METHOD_SODIUM:
                    try {
                        $decrypted = sodium_crypto_secretbox_open($encrypted, $iv, self::get_key($method));
                    } catch (\SodiumException $e) {
                        throw new \moodle_exception('encryption_decryptfailed', 'error',
                                '', null, $e->getMessage());
                    }
                    // Sodium returns false if decryption fails because data is invalid.
                    if ($decrypted === false) {
                        throw new \moodle_exception('encryption_decryptfailed', 'error',
                                '', null, 'Integrity check failed');
                    }
                    break;

                case self::METHOD_OPENSSL:
                    if (strlen($encrypted) < 33) {
                        throw new \moodle_exception('encryption_decryptfailed', 'error',
                                '', null, 'Insufficient data');
                    }
                    $hmac = substr($encrypted, -32);
                    $encrypted = substr($encrypted, 0, -32);
                    $key = self::get_key($method);
                    $expectedhmac = hash_hmac('sha256', $iv . $encrypted, $key, true);
                    if ($hmac !== $expectedhmac) {
                        throw new \moodle_exception('encryption_decryptfailed', 'error',
                                '', null, 'Integrity check failed');
                    }

                    $decrypted = @openssl_decrypt($encrypted, self::OPENSSL_CIPHER, $key, OPENSSL_RAW_DATA, $iv);
                    if ($decrypted === false) {
                        throw new \moodle_exception('encryption_decryptfailed', 'error',
                                '', null, openssl_error_string());
                    }
                    break;

                default:
                    throw new \coding_exception('Unknown method: ' . $method);
            }

            return $decrypted;
        }
    }
}
