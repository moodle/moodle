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
 * Used to validate a textarea used for domain names, wildcard domain names and IP addresses/ranges (both IPv4 and IPv6 format).
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 Jake Dallimore (jrhdallimore@gmail.com)
 */
class admin_setting_configmixedhostiplist extends admin_setting_configtextarea {

    /**
     * Validate the contents of the textarea as either IP addresses, domain name or wildcard domain name (RFC 4592).
     * Used to validate a new line separated list of entries collected from a textarea control.
     *
     * This setting provides support for internationalised domain names (IDNs), however, such UTF-8 names will be converted to
     * their ascii-compatible encoding (punycode) on save, and converted back to their UTF-8 representation when fetched
     * via the get_setting() method, which has been overriden.
     *
     * @param string $data A list of FQDNs, DNS wildcard format domains, and IP addresses, separated by new lines.
     * @return mixed bool true for success or string:error on failure
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }
        $entries = explode("\n", $data);
        $badentries = [];

        foreach ($entries as $key => $entry) {
            $entry = trim($entry);
            if (empty($entry)) {
                return get_string('validateemptylineerror', 'admin');
            }

            // Validate each string entry against the supported formats.
            if (\core\ip_utils::is_ip_address($entry) || \core\ip_utils::is_ipv6_range($entry)
                    || \core\ip_utils::is_ipv4_range($entry) || \core\ip_utils::is_domain_name($entry)
                    || \core\ip_utils::is_domain_matching_pattern($entry)) {
                continue;
            }

            // Otherwise, the entry is invalid.
            $badentries[] = $entry;
        }

        if (count($badentries) > 0) {
            $badentries = implode(get_string('listsep', 'core_langconfig') . ' ', $badentries);
            return get_string('validateerrorlist', 'admin', $badentries);
        }
        return true;
    }

    /**
     * Convert any lines containing international domain names (IDNs) to their ascii-compatible encoding (ACE).
     *
     * @param string $data the setting data, as sent from the web form.
     * @return string $data the setting data, with all IDNs converted (using punycode) to their ascii encoded version.
     */
    protected function ace_encode($data) {
        if (empty($data)) {
            return $data;
        }
        $entries = explode("\n", $data);
        foreach ($entries as $key => $entry) {
            $entry = trim($entry);
            // This regex matches any string that has non-ascii character.
            if (preg_match('/[^\x00-\x7f]/', $entry)) {
                // If we can convert the unicode string to an idn, do so.
                // Otherwise, leave the original unicode string alone and let the validation function handle it (it will fail).
                $val = idn_to_ascii($entry, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
                $entries[$key] = $val ? $val : $entry;
            }
        }
        return implode("\n", $entries);
    }

    /**
     * Decode any ascii-encoded domain names back to their utf-8 representation for display.
     *
     * @param string $data the setting data, as found in the database.
     * @return string $data the setting data, with all ascii-encoded IDNs decoded back to their utf-8 representation.
     */
    protected function ace_decode($data) {
        $entries = explode("\n", $data);
        foreach ($entries as $key => $entry) {
            $entry = trim($entry);
            if (strpos($entry, 'xn--') !== false) {
                $entries[$key] = idn_to_utf8($entry, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
            }
        }
        return implode("\n", $entries);
    }

    /**
     * Override, providing utf8-decoding for ascii-encoded IDN strings.
     *
     * @return mixed returns punycode-converted setting string if successful, else null.
     */
    public function get_setting() {
        // Here, we need to decode any ascii-encoded IDNs back to their native, utf-8 representation.
        $data = $this->config_read($this->name);
        if (function_exists('idn_to_utf8') && !is_null($data)) {
            $data = $this->ace_decode($data);
        }
        return $data;
    }

    /**
     * Override, providing ascii-encoding for utf8 (native) IDN strings.
     *
     * @param string $data
     * @return string
     */
    public function write_setting($data) {
        if ($this->paramtype === PARAM_INT and $data === '') {
            // Do not complain if '' used instead of 0.
            $data = 0;
        }

        // Try to convert any non-ascii domains to ACE prior to validation - we can't modify anything in validate!
        if (function_exists('idn_to_ascii')) {
            $data = $this->ace_encode($data);
        }

        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }
}
