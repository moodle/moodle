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
 * @package moodlecore
 * @subpackage backup-plan
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class defining the needed stuf for one restore step
 *
 * TODO: Finish phpdocs
 */
abstract class restore_step extends base_step {

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $task = null) {
        if (!is_null($task) && !($task instanceof restore_task)) {
            throw new restore_step_exception('wrong_restore_task_specified');
        }
        parent::__construct($name, $task);
    }

    protected function get_restoreid() {
        if (is_null($this->task)) {
            throw new restore_step_exception('not_specified_restore_task');
        }
        return $this->task->get_restoreid();
    }

    /**
     * Apply course startdate offset based in original course startdate and course_offset_startdate setting
     * Note we are using one static cache here, but *by restoreid*, so it's ok for concurrence/multiple
     * executions in the same request
     *
     * Note: The policy is to roll date only for configurations and not for user data. see MDL-9367.
     *
     * @param int $value Time value (seconds since epoch), or empty for nothing
     * @return int Time value after applying the date offset, or empty for nothing
     */
    public function apply_date_offset($value) {

        // Empties don't offset - zeros (int and string), false and nulls return original value.
        if (empty($value)) {
            return $value;
        }

        static $cache = array();
        // Lookup cache.
        if (isset($cache[$this->get_restoreid()])) {
            return $value + $cache[$this->get_restoreid()];
        }
        // No cache, let's calculate the offset.
        $original = $this->task->get_info()->original_course_startdate;
        $setting = 0;
        if ($this->setting_exists('course_startdate')) { // Seting may not exist (MDL-25019).
            $settingobject = $this->task->get_setting('course_startdate');
            if (method_exists($settingobject, 'get_normalized_value')) {
                $setting = $settingobject->get_normalized_value();
            } else {
                $setting = $settingobject->get_value();
            }
        }

        if (empty($original) || empty($setting)) {
            // Original course has not startdate or setting doesn't exist, offset = 0.
            $cache[$this->get_restoreid()] = 0;

        } else {
            // Arrived here, let's calculate the real offset.
            $cache[$this->get_restoreid()] = $setting - $original;
        }

        // Return the passed value with cached offset applied.
        return $value + $cache[$this->get_restoreid()];
    }

    /**
     * Returns symmetric-key AES-256 decryption of base64 encoded contents.
     *
     * This method is used in restore operations to decrypt contents encrypted with
     * {@link encrypted_final_element} automatically decoding (base64) and decrypting
     * contents using the key stored in backup_encryptkey config.
     *
     * Requires openssl, cipher availability, and key existence (backup
     * automatically sets it if missing). Integrity is provided via HMAC.
     *
     * @param string $value {@link encrypted_final_element} value to decode and decrypt.
     * @return string|null decoded and decrypted value or null if the operation can not be performed.
     */
    public function decrypt($value) {

        // No openssl available, skip this field completely.
        if (!function_exists('openssl_encrypt')) {
            return null;
        }

        // No hash available, skip this field completely.
        if (!function_exists('hash_hmac')) {
            return null;
        }

        // Cypher not available, skip this field completely.
        if (!in_array(backup::CIPHER, openssl_get_cipher_methods())) {
            return null;
        }

        // Get the decrypt key. Skip if missing.
        $key = get_config('backup', 'backup_encryptkey');
        if ($key === false) {
            return null;
        }

        // And decode it.
        $key = base64_decode($key);

        // Arrived here, let's proceed with authentication (provides integrity).
        $hmaclen = 32; // SHA256 is 32 bytes.
        $ivlen = openssl_cipher_iv_length(backup::CIPHER);
        list($hmac, $iv, $text) = array_values(unpack("a{$hmaclen}hmac/a{$ivlen}iv/a*text", base64_decode($value)));

        // Verify HMAC matches expectations, skip if not (integrity failed).
        if (!hash_equals($hmac, hash_hmac('sha256', $iv . $text, $key, true))) {
            return null;
        }

        // Arrived here, integrity is ok, let's decrypt.
        $result = openssl_decrypt($text, backup::CIPHER, $key, OPENSSL_RAW_DATA, $iv);

        // For some reason decrypt failed (strange, HMAC check should have deteted it), skip this field completely.
        if ($result === false) {
            return null;
        }

        return $result;
    }
}

/*
 * Exception class used by all the @restore_step stuff
 */
class restore_step_exception extends base_step_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
