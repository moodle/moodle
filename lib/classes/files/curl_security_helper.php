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
 * Contains a class providing functions used to check the allowed/blocked host/ports for curl.
 *
 * @package   core
 * @copyright 2016 Jake Dallimore
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jake Dallimore <jrhdallimore@gmail.com>
 */

namespace core\files;
use core\ip_utils;

defined('MOODLE_INTERNAL') || exit();

/**
 * Host and port checking for curl.
 *
 * This class provides a means to check URL/host/port against the system-level cURL security entries.
 * It does not provide a means to add URLs, hosts or ports to the allowed/blocked lists; this is configured manually
 * via the site admin section of Moodle (See: 'Site admin' > 'Security' > 'HTTP Security').
 *
 * This class is currently used by the 'curl' wrapper class in lib/filelib.php.
 * Depends on:
 *  core\ip_utils (several functions)
 *  moodlelib (clean_param)
 *
 * @package   core
 * @copyright 2016 Jake Dallimore
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jake Dallimore <jrhdallimore@gmail.com>
 */
class curl_security_helper extends curl_security_helper_base {
    /**
     * @var array of supported transport schemes and their respective default ports.
     */
    protected $transportschemes = [
        'http' => 80,
        'https' => 443
    ];

    /**
     * @var string the host of the URL being checked by the helper.
     */
    protected $host;

    /**
     * @var array IP address or addresses the URL is allowed to be requested from (passed the blocked hosts check).
     */
    protected $allowedips = [];

    /**
     * @var ?int The port the URL is allowed to be requested from (passed the allowed port check).
     */
    protected $allowedport;

    /**
     * Checks whether the given URL is blocked by checking its address and port number against the allow/block lists.
     * The behaviour of this function can be classified as strict, as it returns true for URLs which are invalid or
     * could not be parsed, as well as those valid URLs which were found in the blocklist.
     *
     * @param string $urlstring the URL to check.
     * @param int $notused There used to be an optional parameter $maxredirects for a short while here, not used any more.
     * @return bool true if the URL is blocked or invalid and false if the URL is not blocked.
     */
    public function url_is_blocked($urlstring, $notused = null) {

        if ($notused !== null) {
            debugging('The $maxredirects parameter of curl_security_helper::url_is_blocked() has been dropped!', DEBUG_DEVELOPER);
        }

        // If no config data is present, then all hosts/ports are allowed.
        if (!$this->is_enabled()) {
            return false;
        }

        // Try to parse the URL to get the 'host' and 'port' components.
        try {
            $url = new \moodle_url($urlstring);
            $parsed['scheme'] = $url->get_scheme();
            $parsed['host'] = $url->get_host();
            $parsed['port'] = $url->get_port();
        } catch (\moodle_exception $e) {
            // Moodle exception is thrown if the $urlstring is invalid. Treat as blocked.
            return true;
        }

        $this->host = $parsed['host'];

        // The port will be empty unless explicitly set in the $url (uncommon), so try to infer it from the supported schemes.
        if (!$parsed['port'] && $parsed['scheme'] && isset($this->transportschemes[$parsed['scheme']])) {
            $parsed['port'] = $this->transportschemes[$parsed['scheme']];
        }

        if ($parsed['port'] && $parsed['host']) {
            // Check the host and port against the allow/block entries.
            return $this->host_is_blocked($parsed['host']) || $this->port_is_blocked($parsed['port']);
        }
        return true;
    }

    /**
     * Returns a string message describing a blocked URL. E.g. 'This URL is blocked'.
     *
     * @return string the string error.
     */
    public function get_blocked_url_string() {
        return get_string('curlsecurityurlblocked', 'admin');
    }

    /**
     * Checks whether the host portion of a url is blocked.
     * The host portion may be a FQDN, IPv4 address or a IPv6 address wrapped in square brackets, as per standard URL notation.
     * E.g.
     *     images.example.com
     *     127.0.0.1
     *     [0.0.0.0.0.0.0.1]
     * The method logic is as follows:
     * 1. Check the host component against the list of IPv4/IPv6 addresses and ranges.
     *  - This will perform a DNS forward lookup if required.
     * 2. Check the host component against the list of domain names and wildcard domain names.
     *  - This will perform a DNS reverse lookup if required.
     *
     * The behaviour of this function can be classified as strict, as it returns true for hosts which are invalid or
     * could not be parsed, as well as those valid URLs which were found in the blocklist.
     *
     * @param string $host the host component of the URL to check against the blocklist.
     * @return bool true if the host is both valid and blocked, false otherwise.
     */
    protected function host_is_blocked($host) {
        if (!$this->is_enabled() || empty($host) || !is_string($host)) {
            return false;
        }

        // Fix for square brackets in the 'host' portion of the URL (only occurs if an IPv6 address is specified).
        $host = str_replace(array('[', ']'), '', $host); // RFC3986, section 3.2.2.
        $blockedhosts = $this->get_blocked_hosts_by_category();

        if (ip_utils::is_ip_address($host)) {
            if ($this->address_explicitly_blocked($host)) {
                return true;
            }

            // Only perform a reverse lookup if there is a point to it (i.e. we have rules to check against).
            if ($blockedhosts['domain'] || $blockedhosts['domainwildcard']) {
                // DNS reverse lookup - supports both IPv4 and IPv6 address formats.
                $hostname = gethostbyaddr($host);
                if ($hostname !== $host && $this->host_explicitly_blocked($hostname)) {
                    return true;
                }
            }
        } else if (ip_utils::is_domain_name($host)) {
            if ($this->host_explicitly_blocked($host)) {
                return true;
            }

            // Only perform a forward lookup if there are IP rules to check against.
            if ($blockedhosts['ipv4'] || $blockedhosts['ipv6']) {
                // DNS forward lookup - returns a list of only IPv4 addresses!
                $hostips = $this->get_host_list_by_name($host);

                // If we don't get a valid record, bail (so cURL is never called).
                if (!$hostips) {
                    return true;
                }

                // If any of the returned IPs are in the blocklist, block the request. Otherwise, temporarily record the IPs.
                $allowedips = [];
                foreach ($hostips as $hostip) {
                    if ($this->address_explicitly_blocked($hostip)) {
                        return true;
                    }
                    $allowedips[] = $hostip;
                }

                // If none of the IPs are blocked, set them on the allow list so we can enforce them on subsequent requests.
                $this->allowedips = $allowedips;
            }
        } else {
            // Was not something we consider to be a valid IP or domain name, block it.
            return true;
        }

        return false;
    }

    /**
     * Retrieve all hosts for a domain name.
     *
     * @param string $param
     * @return array An array of IPs associated with the host name.
     */
    protected function get_host_list_by_name($host) {
        return ($hostips = gethostbynamel($host)) ? $hostips : [];
    }

    /**
     * Checks whether the given port is blocked, as determined by its absence on the ports allowlist.
     * Ports are assumed to be blocked unless found in the allowlist.
     *
     * @param integer|string $port the port to check against the ports allowlist.
     * @return bool true if the port is blocked, false otherwise.
     */
    protected function port_is_blocked($port) {
        $portnum = intval($port);
        // Intentionally block port 0 and below and check the int cast was valid.
        if (empty($port) || (string)$portnum !== (string)$port || $port < 0) {
            return true;
        }
        $allowedports = $this->get_allowed_ports();

        $isblocked = !empty($allowedports) && !in_array($portnum, $allowedports);

        // If port is allowed, add it to our allow list so we can enforce it on subsequent requests.
        if (!$isblocked) {
            $this->allowedport = $portnum;
        }

        return $isblocked;
    }

    /**
     * Convenience method to check whether we have any entries in the host blocklist or ports allowlist admin settings.
     * If no entries are found at all, the assumption is that the blocklist is disabled entirely.
     *
     * @return bool true if one or more entries exist, false otherwise.
     */
    public function is_enabled() {
        return (!empty($this->get_allowed_ports()) || !empty($this->get_blocked_hosts()));
    }

    /**
     * Checks whether the input address is blocked by at any of the IPv4 or IPv6 address rules.
     *
     * @param string $addr the ip address to check.
     * @return bool true if the address is covered by an entry in the blocklist, false otherwise.
     */
    protected function address_explicitly_blocked($addr) {
        $blockedhosts = $this->get_blocked_hosts_by_category();
        $iphostsblocked = array_merge($blockedhosts['ipv4'], $blockedhosts['ipv6']);
        return address_in_subnet($addr, implode(',', $iphostsblocked), true);
    }

    /**
     * Checks whether the input hostname is blocked by any of the domain/wildcard rules.
     *
     * @param string $host the hostname to check
     * @return bool true if the host is covered by an entry in the blocklist, false otherwise.
     */
    protected function host_explicitly_blocked($host) {
        $blockedhosts = $this->get_blocked_hosts_by_category();
        $domainhostsblocked = array_merge($blockedhosts['domain'], $blockedhosts['domainwildcard']);
        return ip_utils::is_domain_in_allowed_list($host, $domainhostsblocked);
    }

    /**
     * Helper to get all entries from the admin setting, as an array, sorted by classification.
     * Classifications include 'ipv4', 'ipv6', 'domain', 'domainwildcard'.
     *
     * @return array of host/domain/ip entries from the 'curlsecurityblockedhosts' config.
     */
    protected function get_blocked_hosts_by_category() {
        // For each of the admin setting entries, check and place in the correct section of the config array.
        $config = ['ipv6' => [], 'ipv4' => [], 'domain' => [], 'domainwildcard' => []];
        $entries = $this->get_blocked_hosts();
        foreach ($entries as $entry) {
            if (ip_utils::is_ipv6_address($entry) || ip_utils::is_ipv6_range($entry)) {
                $config['ipv6'][] = $entry;
            } else if (ip_utils::is_ipv4_address($entry) || ip_utils::is_ipv4_range($entry)) {
                $config['ipv4'][] = $entry;
            } else if (ip_utils::is_domain_name($entry)) {
                $config['domain'][] = $entry;
            } else if (ip_utils::is_domain_matching_pattern($entry)) {
                $config['domainwildcard'][] = $entry;
            }
        }
        return $config;
    }

    /**
     * Helper that returns the allowed ports, as defined in the 'curlsecurityallowedport' setting.
     *
     * @return array the array of allowed ports.
     */
    protected function get_allowed_ports() {
        global $CFG;
        if (!isset($CFG->curlsecurityallowedport)) {
            return [];
        }
        return array_filter(array_map('trim', explode("\n", $CFG->curlsecurityallowedport)), function($entry) {
            return !empty($entry);
        });
    }

    /**
     * Helper that returns the blocked hosts, as defined in the 'curlsecurityblockedhosts' setting.
     *
     * @return array the array of blocked host entries.
     */
    protected function get_blocked_hosts() {
        global $CFG;
        if (!isset($CFG->curlsecurityblockedhosts)) {
            return [];
        }
        return array_filter(array_map('trim', explode("\n", $CFG->curlsecurityblockedhosts)), function($entry) {
            return !empty($entry);
        });
    }

    /**
     * Helper that returns host, IP and port information for the URL that has passed the blocked hosts/allowed ports checks.
     *
     * This data is in a format compatible with CURLOPT_RESOLVE, so it can be passed directly into that option.
     * Doing so will prevent cURL re-fetching the info from DNS, preventing subsequent requests to the remote host from
     * modifying the IP/port to ones that haven't been validated.
     *
     * @return array of strings in the format hostname:port:ip_address.
     * @throws \coding_exception
     */
    public function get_resolve_info(): array {
        if (empty($this->host || empty($this->allowedips) || empty($this->allowedport))) {
            $exception = 'In the curl_security_helper class, url_is_blocked() must be called before get_resolve_info() is called.';
            throw new \core\exception\coding_exception($exception);
        }

        return array_map(fn($ip) => "$this->host:$this->allowedport:$ip", $this->allowedips);
    }
}
