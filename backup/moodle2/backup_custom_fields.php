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
 * Defines various element classes used in specific areas
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of backup_final_element that provides one interceptor for anonymization of data
 *
 * This class overwrites the standard set_value() method, in order to get (by name)
 * functions from backup_anonymizer_helper executed, producing anonymization of information
 * to happen in a clean way
 *
 * TODO: Finish phpdocs
 */
class anonymizer_final_element extends backup_final_element {

    public function set_value($value) {
        // Get parent name
        $pname = $this->get_parent()->get_name();
        // Get my name
        $myname = $this->get_name();
        // Define class and function name
        $classname = 'backup_anonymizer_helper';
        $methodname= 'process_' . $pname . '_' . $myname;
        // Invoke the interception method
        $result = call_user_func(array($classname, $methodname), $value);
        // Finally set it
        parent::set_value($result);
    }
}

/**
 * Implementation of backup_final_element that provides special handling of mnethosturl
 *
 * This class overwrites the standard set_value() method, in order to decide,
 * based on various config options, what to do with the field.
 *
 * TODO: Finish phpdocs
 */
class mnethosturl_final_element extends backup_final_element {

    public function set_value($value) {
        global $CFG;

        $localhostwwwroot = backup_plan_dbops::get_mnet_localhost_wwwroot();

        // If user wwwroot matches mnet local host one or if
        // there isn't associated wwwroot, skip sending it to file
        if ($localhostwwwroot == $value || empty($value)) {
            // Do nothing
        } else {
            parent::set_value($value);
        }
    }
}

/**
 * Implementation of {@link backup_final_element} that provides base64 encoding.
 *
 * This final element transparently encodes with base64_encode() contents that
 * normally are not safe for being stored in utf-8 xml files (binaries, serialized
 * data...).
 */
class base64_encode_final_element extends backup_final_element {

    /**
     * Set the value for the final element, encoding it as utf-8/xml safe base64.
     *
     * @param string $value Original value coming from backup step source, usually db.
     */
    public function set_value($value) {
        parent::set_value(base64_encode($value));
    }
}

/**
 * Implementation of {@link backup_final_element} that provides symmetric-key AES-256 encryption of contents.
 *
 * This final element transparently encrypts, for secure storage and transport, any content
 * that shouldn't be shown normally in plain text. Usually, passwords or keys that cannot use
 * hashing algorithms, although potentially can encrypt any content. All information is encoded
 * using base64.
 *
 * Features:
 *   - requires openssl extension to work. Without it contents are completely omitted.
 *   - automatically creates an appropriate default key for the site and stores it into backup_encryptkey config (bas64 encoded).
 *   - uses a different appropriate init vector for every operation, which is transmited with the encrypted contents.
 *   - all generated data is base64 encoded for safe transmission.
 *   - automatically adds "encrypted" attribute for easier detection.
 *   - implements HMAC for providing integrity.
 *
 * @copyright 2017 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class encrypted_final_element extends backup_final_element {

    /** @var string cypher appropiate raw key for backups in the site. Defaults to backup_encryptkey config. */
    protected $key = null;

    /**
     * Constructor - instantiates a encrypted_final_element, specifying its basic info.
     *
     * Overridden to automatically add the 'encrypted' attribute if missing.
     *
     * @param string $name name of the element
     * @param array  $attributes attributes this element will handle (optional, defaults to null)
     */
    public function __construct($name, $attributes = null) {
        parent::__construct($name, $attributes);
        if (! $this->get_attribute('encrypted')) {
            $this->add_attributes('encrypted');
        }
    }

    /**
     * Set the encryption key manually, overriding default backup_encryptkey config.
     *
     * @param string $key key to be used for encrypting. Required to be 256-bit key.
     *               Use a safe generation technique. See self::generate_encryption_random_key() below.
     */
    protected function set_key($key) {
        $bytes = strlen($key); // Get key length in bytes.

        // Only accept keys with the expected (backup::CIPHERKEYLEN) key length. There are a number of hashing,
        // random generators to achieve this esasily, like the one shown below to create the default
        // site encryption key and ivs.
        if ($bytes !== backup::CIPHERKEYLEN) {
            $info = (object)array('expected' => backup::CIPHERKEYLEN, 'found' => $bytes);
            throw new base_element_struct_exception('encrypted_final_element incorrect key length', $info);
        }
        // Everything went ok, store the key.
        $this->key = $key;
    }

    /**
     * Set the value of the field.
     *
     * This method sets the value of the element, encrypted using the specified key for it,
     * defaulting to (and generating) backup_encryptkey config. HMAC is used for integrity.
     *
     * @param string $value plain-text content the will be stored encrypted and encoded.
     */
    public function set_value($value) {

        // No openssl available, skip this field completely.
        if (!function_exists('openssl_encrypt')) {
            return;
        }

        // No hmac available, skip this field completely.
        if (!function_exists('hash_hmac')) {
            return;
        }

        // Cypher not available, skip this field completely.
        if (!in_array(backup::CIPHER, openssl_get_cipher_methods())) {
            return;
        }

        // Ensure we have a good key, manual or default.
        if (empty($this->key)) {
            // The key has not been set manually, look for it at config (base64 encoded there).
            $enckey = get_config('backup', 'backup_encryptkey');
            if ($enckey === false) {
                // Has not been set, calculate and save an appropiate random key automatically.
                $enckey = base64_encode(self::generate_encryption_random_key(backup::CIPHERKEYLEN));
                set_config('backup_encryptkey', $enckey, 'backup');
            }
            $this->set_key(base64_decode($enckey));
        }

        // Now we need an iv for this operation.
        $iv = self::generate_encryption_random_key(openssl_cipher_iv_length(backup::CIPHER));

        // Everything is ready, let's encrypt and prepend the 1-shot iv.
        $value = $iv . openssl_encrypt($value, backup::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);

        // Calculate the hmac of the value (iv + encrypted) and prepend it.
        $hmac = hash_hmac('sha256', $value, $this->key, true);
        $value = $hmac . $value;

        // Ready, set the encoded value.
        parent::set_value(base64_encode($value));

        // Finally, if the field has an "encrypted" attribute, set it to true.
        if ($att = $this->get_attribute('encrypted')) {
            $att->set_value('true');
        }
    }

    /**
     * Generate an appropiate random key to be used for encrypting backup information.
     *
     * Normally used as site default encryption key (backup_encryptkey config) and also
     * for calculating the init vectors.
     *
     * Note that until PHP 5.6.12 openssl_random_pseudo_bytes() did NOT
     * use a "cryptographically strong algorithm" {@link https://bugs.php.net/bug.php?id=70014}
     * But it's beyond my crypto-knowledge when it's worth finding a *real* better alternative.
     *
     * @param int $bytes Number of bytes to determine the key length expected.
     */
    protected static function generate_encryption_random_key($bytes) {
        return openssl_random_pseudo_bytes($bytes);
    }
}

/**
 * Implementation of backup_nested_element that provides special handling of files
 *
 * This class overwrites the standard fill_values() method, so it gets intercepted
 * for each file record being set to xml, in order to copy, at the same file, the
 * physical file from moodle file storage to backup file storage
 *
 * TODO: Finish phpdocs
 */
class file_nested_element extends backup_nested_element {

    protected $backupid;

    public function process($processor) {
        // Get current backupid from processor, we'll need later
        if (is_null($this->backupid)) {
            $this->backupid = $processor->get_var(backup::VAR_BACKUPID);
        }
        return parent::process($processor);
    }

    public function fill_values($values) {
        // Fill values
        parent::fill_values($values);
        // Do our own tasks (copy file from moodle to backup)
        try {
            backup_file_manager::copy_file_moodle2backup($this->backupid, $values);
        } catch (file_exception $e) {
            $this->add_result(array('missing_files_in_pool' => true));

            // Build helpful log message with all information necessary to identify
            // file location.
            $context = context::instance_by_id($values->contextid, IGNORE_MISSING);
            $contextname = '';
            if ($context) {
                $contextname = ' \'' . $context->get_context_name() . '\'';
            }
            $message = 'Missing file in pool: ' . $values->filepath  . $values->filename .
                    ' (context ' . $values->contextid . $contextname . ', component ' .
                    $values->component . ', filearea ' . $values->filearea . ', itemid ' .
                    $values->itemid . ') [' . $e->debuginfo . ']';
            $this->add_log($message, backup::LOG_WARNING);
        }
    }
}

/**
 * Implementation of backup_optigroup_element to be used by plugins stuff.
 * Split just for better separation and future specialisation
 */
class backup_plugin_element extends backup_optigroup_element { }

/**
 * Implementation of backup_optigroup_element to be used by subplugins stuff.
 * Split just for better separation and future specialisation
 */
class backup_subplugin_element extends backup_optigroup_element { }
