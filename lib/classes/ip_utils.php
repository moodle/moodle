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
 * Contains a simple class providing some useful internet protocol-related functions.
 *
 * @package   core
 * @copyright 2016 Jake Dallimore
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jake Dallimore <jrhdallimore@gmail.com>
 */

namespace core;

defined('MOODLE_INTERNAL') || exit();

/**
 * Static helper class providing some useful internet-protocol-related functions.
 *
 * @package   core
 * @copyright 2016 Jake Dallimore
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jake Dallimore <jrhdallimore@gmail.com>
 */
final class ip_utils {
    /**
     * Syntax checking for domain names, including fully qualified domain names.
     *
     * This function does not verify the existence of the domain name. It only verifies syntactic correctness.
     * This is based on RFC1034/1035 and does not provide support for validation of internationalised domain names (IDNs).
     * All IDNs must be prior-converted to their ascii-compatible encoding before being passed to this function.
     *
     * @param string $domainname the input string to check.
     * @return bool true if the string has valid syntax, false otherwise.
     */
    public static function is_domain_name($domainname) {
        if (!is_string($domainname)) {
            return false;
        }
        // Usually the trailing dot (null label) is omitted, but is valid if supplied. We'll just remove it and validate as normal.
        $domainname = rtrim($domainname, '.');

        // The entire name cannot exceed 253 ascii characters (255 octets, less the leading label-length byte and null label byte).
        if (strlen($domainname) > 253) {
            return false;
        }
        // Tertiary domain labels can have 63 octets max, and must not have begin or end with a hyphen.
        // The TLD label cannot begin with a number, but otherwise, is only loosely restricted here (TLD list is not checked).
        $domaintertiary = '([a-zA-Z0-9](([a-zA-Z0-9-]{0,61})[a-zA-Z0-9])?\.)*';
        $domaintoplevel = '([a-zA-Z](([a-zA-Z0-9-]*)[a-zA-Z0-9])?)';
        $address = '(' . $domaintertiary .  $domaintoplevel . ')';
        $regexp = '#^' . $address . '$#i'; // Case insensitive matching.
        return preg_match($regexp, $domainname, $match) == true; // False for error, 0 for no match - we treat the same.
    }

    /**
     * Checks whether the input string is a valid wildcard domain matching pattern.
     *
     * A domain matching pattern is essentially a domain name with a single, leading wildcard (*) label, and at least one other
     * label. The wildcard label is considered to match at least one label at or above (to the left of) its position in the string,
     * but will not match the trailing domain (everything to its right).
     *
     * The string must be dot-separated, and the whole pattern must follow the domain name syntax rules defined in RFC1034/1035.
     * Namely, the character type (ascii), total-length (253) and label-length (63) restrictions. This function only confirms
     * syntactic correctness. It does not check for the existence of the domain/subdomains.
     *
     * For example, the string '*.example.com' is a pattern deemed to match any direct subdomain of
     * example.com (such as test.example.com), any higher level subdomains (e.g. another.test.example.com) but will not match
     * the 'example.com' domain itself.
     *
     * @param string $pattern the string to check.
     * @return bool true if the input string is a valid domain wildcard matching pattern, false otherwise.
     */
    public static function is_domain_matching_pattern($pattern) {
        if (!is_string($pattern)) {
            return false;
        }
        // Usually the trailing dot (null label) is omitted, but is valid if supplied. We'll just remove it and validate as normal.
        $pattern = rtrim($pattern, '.');

        // The entire name cannot exceed 253 ascii characters (255 octets, less the leading label-length byte and null label byte).
        if (strlen($pattern) > 253) {
            return false;
        }
        // A valid pattern must left-positioned wildcard symbol (*).
        // Tertiary domain labels can have 63 octets max, and must not have begin or end with a hyphen.
        // The TLD label cannot begin with a number, but otherwise, is only loosely restricted here (TLD list is not checked).
        $wildcard = '((\*)\.){1}';
        $domaintertiary = '([a-zA-Z0-9](([a-zA-Z0-9-]{0,61})[a-zA-Z0-9])?\.)*';
        $domaintoplevel = '([a-zA-Z](([a-zA-Z0-9-]*)[a-zA-Z0-9])?)';
        $address = '(' . $wildcard . $domaintertiary .  $domaintoplevel . ')';
        $regexp = '#^' . $address . '$#i'; // Case insensitive matching.
        return preg_match($regexp, $pattern, $match) == true; // False for error, 0 for no match - we treat the same.
    }

    /**
     * Syntax validation for IP addresses, supporting both IPv4 and Ipv6 formats.
     *
     * @param string $address the address to check.
     * @return bool true if the address is a valid IPv4 of IPv6 address, false otherwise.
     */
    public static function is_ip_address($address) {
        return filter_var($address, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Syntax validation for IPv4 addresses.
     *
     * @param string $address the address to check.
     * @return bool true if the address is a valid IPv4 address, false otherwise.
     */
    public static function is_ipv4_address($address) {
        return filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Syntax checking for IPv4 address ranges.
     * Supports CIDR notation and last-group ranges.
     * Eg. 127.0.0.0/24 or 127.0.0.80-255
     *
     * @param string $addressrange the address range to check.
     * @return bool true if the string is a valid range representation, false otherwise.
     */
    public static function is_ipv4_range($addressrange) {
        // Check CIDR notation.
        if (preg_match('#^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\/(\d{1,2})$#', $addressrange, $match)) {
            $address = "{$match[1]}.{$match[2]}.{$match[3]}.{$match[4]}";
            return self::is_ipv4_address($address) && $match[5] <= 32;
        }
        // Check last-group notation.
        if (preg_match('#^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})-(\d{1,3})$#', $addressrange, $match)) {
            $address = "{$match[1]}.{$match[2]}.{$match[3]}.{$match[4]}";
            return self::is_ipv4_address($address) && $match[5] <= 255 && $match[5] >= $match[4];
        }
        return false;
    }

    /**
     * Syntax validation for IPv6 addresses.
     * This function does not check whether the address is assigned, only its syntactical correctness.
     *
     * @param string $address the address to check.
     * @return bool true if the address is a valid IPv6 address, false otherwise.
     */
    public static function is_ipv6_address($address) {
        return filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Syntax validation for IPv6 address ranges.
     * Supports CIDR notation and last-group ranges.
     * Eg. fe80::d98c/64 or fe80::d98c-ffee
     *
     * @param string $addressrange the IPv6 address range to check.
     * @return bool true if the string is a valid range representation, false otherwise.
     */
    public static function is_ipv6_range($addressrange) {
        // Check CIDR notation.
        $ipv6parts = explode('/', $addressrange);
        if (count($ipv6parts) == 2) {
            $range = (int)$ipv6parts[1];
            return self::is_ipv6_address($ipv6parts[0]) && (string)$range === $ipv6parts[1] && $range >= 0 && $range <= 128;
        }
        // Check last-group notation.
        $ipv6parts = explode('-', $addressrange);
        if (count($ipv6parts) == 2) {
            $addressparts = explode(':', $ipv6parts[0]);
            $rangestart = $addressparts[count($addressparts) - 1];
            $rangeend = $ipv6parts[1];
            return self::is_ipv6_address($ipv6parts[0]) && ctype_xdigit($rangestart) && ctype_xdigit($rangeend)
            && strlen($rangeend) <= 4 && strlen($rangestart) <= 4 && hexdec($rangeend) >= hexdec($rangestart);
        }
        return false;
    }

    /**
     * Checks the domain name against a list of allowed domains. The list of allowed domains is may use
     * wildcards that match {@link is_domain_matching_pattern()}.
     *
     * @param  string $domain Domain address
     * @param  array $alloweddomains An array of allowed domains.
     * @return boolean True if the domain matches one of the entries in the allowed domains list.
     */
    public static function is_domain_in_allowed_list($domain, $alloweddomains) {

        if (!self::is_domain_name($domain)) {
            return false;
        }

        foreach ($alloweddomains as $alloweddomain) {
            if (strpos($alloweddomain, '*') !== false) {
                if (!self::is_domain_matching_pattern($alloweddomain)) {
                    continue;
                }
                // Use of wildcard for possible subdomains.
                $escapeperiods = str_replace('.', '\.', $alloweddomain);
                $replacewildcard = str_replace('*', '.*', $escapeperiods);
                $ultimatepattern = '/' . $replacewildcard . '$/';
                if (preg_match($ultimatepattern, $domain)) {
                    return true;
                }
            } else {
                if (!self::is_domain_name($alloweddomain)) {
                    continue;
                }
                // Strict domain setting.
                if ($domain === $alloweddomain) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Is an ip in a given list of subnets?
     *
     * @param string $ip - the IP to test against the list
     * @param string $list - the list of IP subnets
     * @param string $delim a delimiter of the list
     * @return bool
     */
    public static function is_ip_in_subnet_list($ip, $list, $delim = "\n") {
        $list = explode($delim, $list);
        foreach ($list as $line) {
            $tokens = explode('#', $line);
            $subnet = trim($tokens[0]);
            if (address_in_subnet($ip, $subnet)) {
                return true;
            }
        }
        return false;
    }

}
