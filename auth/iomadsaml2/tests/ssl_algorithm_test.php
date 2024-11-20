<?php
// This file is part of IOMAD SAML2 Authentication Plugin
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

use auth_iomadsaml2\ssl_algorithms;

/**
 * Test Saml2 SSL Algorithms.
 *
 * @package    auth_iomadsaml2
 * @author     Adam Lynam <adam.lynam@catalyst.net.nz>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_ssl_algorithms_test extends basic_testcase {
    public function test_default_saml_signature_algorithm_is_valid_saml_signature_algorithm() {
        $this->assertTrue(array_key_exists(ssl_algorithms::get_default_saml_signature_algorithm(),
            ssl_algorithms::get_valid_saml_signature_algorithms()));
    }

    public function test_sha256_is_valid_saml_signature_algorithm() {
        $this->assertTrue(array_key_exists('http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
            ssl_algorithms::get_valid_saml_signature_algorithms()));
    }

    public function test_sha256_is_matching_digest_algorithm_for_default_saml_algorithm() {
        $this->assertEquals('SHA256', ssl_algorithms::convert_signature_algorithm_to_digest_alg_format(
            ssl_algorithms::get_default_saml_signature_algorithm()));
    }

    public function test_sha256_is_matching_digest_algorithm_for_garbage_algorithm() {
        $this->assertEquals('SHA256', ssl_algorithms::convert_signature_algorithm_to_digest_alg_format('garbage nonsense'));
    }
}
