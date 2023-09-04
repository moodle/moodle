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
namespace auth_iomadsaml2;

defined('MOODLE_INTERNAL') || die();

global $iomadsaml2auth;

/**
 * SSL Algorithms class
 *
 * @package    auth_iomadsaml2
 * @author     Adam Lynam <adam.lynam@catalyst.net.nz>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class ssl_algorithms {
    /**
     * Return a sensible default signature algorithm for simplesamlphp config.
     */
    public static function get_default_saml_signature_algorithm() {
        // Sha1 is deprecated so we default to something more sensible.
        return 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
    }

    /**
     * Return an array of signature algorithms in a form suitable for feeding into a dropdown form.
     */
    public static function get_valid_saml_signature_algorithms() {
        $return = array();
        $return['http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'] = get_string('sha256', 'auth_iomadsaml2');
        $return['http://www.w3.org/2001/04/xmldsig-more#rsa-sha384'] = get_string('sha384', 'auth_iomadsaml2');
        $return['http://www.w3.org/2001/04/xmldsig-more#rsa-sha512'] = get_string('sha512', 'auth_iomadsaml2');
        $return['http://www.w3.org/2000/09/xmldsig#rsa-sha1'] = get_string('sha1', 'auth_iomadsaml2');
        return $return;
    }

    /**
     * Return an array of digest algorithms in a form suitable for feeding into a dropdown form.
     *
     * @param string $signaturealgorithm
     * @return string
     */
    public static function convert_signature_algorithm_to_digest_alg_format($signaturealgorithm) {
        switch($signaturealgorithm) {
            case 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256':
                return 'SHA256';
            case 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384':
                return 'SHA384';
            case 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512':
                return 'SHA512';
            case 'http://www.w3.org/2000/09/xmldsig#rsa-sha1':
                return 'SHA1';
        }

        return 'SHA256';
    }
}
