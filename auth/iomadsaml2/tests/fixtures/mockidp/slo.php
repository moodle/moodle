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
 * Test to mock the logout behaviour of the IdP.
 *
 * This test fixture mocks the logout behaviour of the IdP behaviour by providing a basic user
 * interface that can be manipulated with specific Behat steps.
 *
 * The step that works on this page is:
 *
 * When the mock SAML IdP confirms logout
 *
 * Note that the UI in this fixture doesn't use Moodle conventions (for example there is no header
 * or footer) - this is on purpose because it's mocking an IdP which would be a separate server.
 *
 * @package    auth_iomadsaml2
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file does not need require_login, skip codechecker here.
// phpcs:disable moodle.Files.RequireLogin.Missing
require(__DIR__ . '/../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

// Get the request data.
$requestparam = required_param('SAMLRequest', PARAM_RAW);
$request = gzinflate(base64_decode($requestparam));
$domxml = new DOMDocument();
$domxml->loadXML($request);

$login = optional_param('login', 0, PARAM_INT);

if ($login) {
    require_once(__DIR__ . '/../../../setup.php');

    $logouturl = $CFG->wwwroot . '/auth/iomadsaml2/sp/iomadsaml2-logout.php/' . $iomadsaml2auth->spname;

    // Get data from input request.
    $xpath = new DOMXPath($domxml);
    $id = $xpath->evaluate('normalize-space(/*/@ID)');

    // Get time in UTC.
    $datetime = new DateTime();
    $datetime->setTimezone(new DatetimeZone('UTC'));
    $instant = $datetime->format('Y-m-d') . 'T' . $datetime->format('H:i:s') . 'Z';

    // Get our own IdP URL.
    $baseurl = $CFG->wwwroot . '/auth/iomadsaml2/tests/fixtures/mockidp';
    $issuer = $baseurl . '/idpmetadata.php';

    // Construct XML without signature.
    $responsexml = <<<EOF
<samlp:LogoutResponse xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
        xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
        ID="{$id}_2" Version="2.0" IssueInstant="{$instant}"
        Destination="{$logouturl}" InResponseTo="{$id}">
  <saml:Issuer>{$issuer}</saml:Issuer>
  <samlp:Status>
    <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
  </samlp:Status>
</samlp:LogoutResponse>
EOF;

    // Redirect to it.
    header('HTTP/1.1 302 Redirect');
    header('Location: ' . $logouturl . '?SAMLResponse=' . urlencode(base64_encode(gzdeflate($responsexml))));
    exit;
}

echo '<!doctype html>';
echo html_writer::start_tag('html');
echo html_writer::tag('head', html_writer::tag('title', 'Mock IdP logout'));
echo html_writer::start_tag('body');
echo html_writer::tag('h1', 'Mock IdP logout');

// Display the input request.
$domxml->preserveWhiteSpace = false;
$domxml->formatOutput = true;
echo html_writer::tag('pre', s($domxml->saveXML()));

// Show a form.
echo html_writer::start_tag('form', ['method' => 'post', 'action' => 'slo.php']);
echo html_writer::empty_tag('input',
        ['type' => 'hidden', 'name' => 'SAMLRequest', 'value' => $requestparam]);
echo html_writer::start_div();
echo html_writer::tag('div', html_writer::tag('button', 'Submit',
        ['type' => 'submit', 'name' => 'login', 'value' => 1]));

echo html_writer::end_div();
echo html_writer::end_tag('form');

echo html_writer::end_tag('body');
echo html_writer::end_tag('html');
