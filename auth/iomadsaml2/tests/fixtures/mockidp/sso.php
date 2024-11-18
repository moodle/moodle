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
 * This test fixture mocks IdP behaviour by providing a basic user interface that can be manipulated
 * 
 * with specific Behat steps.
 *
 * The steps that work on this page (depending on how it was launched) are:
 *
 * When the mock SAML IdP allows login with the following attributes:
 * When the mock SAML IdP allows passive login with the following attributes:
 * When the mock SAML IdP does not allow passive login
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
$xpath = new DOMXPath($domxml);

if (optional_param('login', 0, PARAM_INT)) {
    require_once(__DIR__ . '/../../../setup.php');
    // Attributes provided by the Behat step.
    $attributes = json_decode(required_param('attributes', PARAM_RAW));

    // Get data from input request.
    $id = $xpath->evaluate('normalize-space(/*/@ID)');
    $destination = $xpath->evaluate('normalize-space(/*/@AssertionConsumerServiceURL)');
    $sp = $xpath->evaluate('normalize-space(/*/*[local-name() = "Issuer"])');

    // Get time in UTC.
    $datetime = new DateTime();
    $datetime->setTimezone(new DatetimeZone('UTC'));
    $instant = $datetime->format('Y-m-d') . 'T' . $datetime->format('H:i:s') . 'Z';
    $datetime->sub(new DateInterval('P1M'));
    $before = $datetime->format('Y-m-d') . 'T' . $datetime->format('H:i:s') . 'Z';
    $datetime->add(new DateInterval('P2M'));
    $after = $datetime->format('Y-m-d') . 'T' . $datetime->format('H:i:s') . 'Z';

    // Get our own IdP URL.
    $baseurl = $CFG->wwwroot . '/auth/iomadsaml2/tests/fixtures/mockidp';
    $issuer = $baseurl . '/idpmetadata.php';

    // Make up a session.
    $session = 'session' . mt_rand(100000, 999999);

    // Construct attributes in XML.
    $attributexml = '';
    foreach ((array)$attributes as $name => $value) {
        $attributexml .= '<saml:Attribute Name="' . $name .
                '" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified">' .
                '<saml:AttributeValue>' . htmlspecialchars($value) . '</saml:AttributeValue>' .
                '</saml:Attribute>' . "\n";
    }

    // Construct XML without signature.
    $responsexml = <<<EOF
<samlp:Response
        xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
        ID="{$id}_2" InResponseTo="{$id}" Version="2.0" IssueInstant="{$instant}" Destination="{$destination}">
    <saml:Issuer>{$issuer}</saml:Issuer>
    <samlp:Status>
        <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
    </samlp:Status>
    <saml:Assertion xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" ID="{$id}_3" Version="2.0"
            IssueInstant="{$instant}">
        <saml:Issuer>{$issuer}</saml:Issuer>
        <saml:Subject>
            <saml:NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient">
                3f7b3dcf-1674-4ecd-92c8-1544f346baf8
            </saml:NameID>
            <saml:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
                <saml:SubjectConfirmationData InResponseTo="{$id}"
                    Recipient="{$destination}"
                    NotOnOrAfter="{$after}"/>
            </saml:SubjectConfirmation>
        </saml:Subject>
        <saml:Conditions
                NotBefore="{$before}"
                NotOnOrAfter="{$after}">
            <saml:AudienceRestriction>
            <saml:Audience>{$sp}</saml:Audience>
            </saml:AudienceRestriction>
        </saml:Conditions>
        <saml:AuthnStatement AuthnInstant="{$instant}" SessionIndex="{$session}">
            <saml:AuthnContext>
                <saml:AuthnContextClassRef>
                    urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport
                </saml:AuthnContextClassRef>
            </saml:AuthnContext>
        </saml:AuthnStatement>
        <saml:AttributeStatement>
            {$attributexml}
        </saml:AttributeStatement>
    </saml:Assertion>
</samlp:Response>
EOF;

    // Load it into a DOM.
    $outdoc = new \DOMDocument();
    $outdoc->loadXML($responsexml);

    // Find the relevant elements.
    $xpath = new DOMXPath($outdoc);
    $assertion = $xpath->query('//*[local-name()="Assertion"]')[0];
    $subject = $xpath->query('child::*[local-name()="Subject"]', $assertion)[0];

    // Sign it using the fixture key/cert.
    $signer = new \SimpleSAML\XML\Signer([]);
    $signer->loadPrivateKey(__DIR__ . '/mock.pem', 'WkKsfgAf9BhHN21LcpVYYnnt6Hr7WAJj127.0.0.1', true);
    $signer->loadCertificate(__DIR__ . '/mock.crt', true);
    $signer->sign($assertion, $assertion, $subject);

    // Don't send as a referer or the login form might end up coming back here.
    header('Referrer-Policy: no-referrer');

    // Output an HTML form that automatically submits this.
    echo '<!doctype html>';
    echo html_writer::start_tag('html');
    echo html_writer::tag('head', html_writer::tag('title', 'Behat SSO redirect back'));
    echo html_writer::start_tag('body');
    echo html_writer::start_tag('form', ['id' => 'frog', 'method' => 'post', 'action' => $destination]);
    echo html_writer::empty_tag('input',
            ['type' => 'hidden', 'name' => 'SAMLResponse', 'value' => base64_encode($outdoc->saveXML())]);
    echo html_writer::end_tag('form');
    echo html_writer::tag('script', 'document.getElementById("frog").submit();');
    echo html_writer::end_tag('form');
    echo html_writer::end_tag('body');
    exit;
}

if (optional_param('nologin', 0, PARAM_INT)) {
    require_once(__DIR__ . '/../../../setup.php');

    // Get data from input request.
    $id = $xpath->evaluate('normalize-space(/*/@ID)');
    $destination = $xpath->evaluate('normalize-space(/*/@AssertionConsumerServiceURL)');
    $sp = $xpath->evaluate('normalize-space(/*/*[local-name() = "Issuer"])');

    // Get time in UTC.
    $datetime = new DateTime();
    $datetime->setTimezone(new DatetimeZone('UTC'));
    $instant = $datetime->format('Y-m-d') . 'T' . $datetime->format('H:i:s') . 'Z';

    // Get our own IdP URL.
    $baseurl = $CFG->wwwroot . '/auth/iomadsaml2/tests/fixtures/mockidp';
    $issuer = $baseurl . '/idpmetadata.php';

    // Construct XML without signature.
    $responsexml = <<<EOF
<samlp:Response
        xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
        ID="{$id}_2" InResponseTo="{$id}" Version="2.0" IssueInstant="{$instant}" Destination="{$destination}">
    <saml:Issuer>{$issuer}</saml:Issuer>
    <samlp:Status>
        <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:NoPassive"/>
    </samlp:Status>
</samlp:Response>
EOF;

    // Redirect to it. Don't send current URL as referer or the login form might end up coming back here.
    header('HTTP/1.1 302 Redirect');
    header('Referrer-Policy: no-referrer');
    header('Location: ' . $destination . '?SAMLResponse=' . urlencode(base64_encode(gzdeflate($responsexml))));
    exit;
}

echo '<!doctype html>';
echo html_writer::start_tag('html');
echo html_writer::tag('head', html_writer::tag('title', 'Mock IdP login'));
echo html_writer::start_tag('body');
echo html_writer::tag('h1', 'Mock IdP login');
$passive = $xpath->evaluate('normalize-space(/*/@IsPassive)') === 'true';
if ($passive) {
    echo html_writer::tag('h2', 'Passive mode');
}

// Display the input request.
$domxml->preserveWhiteSpace = false;
$domxml->formatOutput = true;
echo html_writer::tag('pre', s($domxml->saveXML()));

// Show a form.
echo html_writer::start_tag('form', ['method' => 'post', 'action' => 'sso.php']);
echo html_writer::empty_tag('input',
        ['type' => 'hidden', 'name' => 'SAMLRequest', 'value' => $requestparam]);
echo html_writer::start_div();
echo html_writer::start_tag('label') . 'Attributes<br/>';
$attributes = json_encode(['uid' => 'abc123', 'firstname' => 'Adam', 'surname' => 'Cliff',
        'email' => 'adam.cliff@example.com', 'lang' => 'en'], JSON_PRETTY_PRINT);
echo html_writer::tag('textarea', $attributes,
        ['id' => 'attributes', 'rows' => 10, 'cols' => 60, 'name' => 'attributes']);
echo html_writer::end_tag('label');

$buttons = html_writer::tag('button', 'Logged in',
        ['type' => 'submit', 'id' => 'login', 'name' => 'login', 'value' => 1]);
if ($passive) {
    $buttons .= html_writer::tag('button', 'Not logged in',
            ['type' => 'submit', 'id' => 'nologin', 'name' => 'nologin', 'value' => 1]);
}
echo html_writer::tag('div', $buttons);

echo html_writer::end_div();
echo html_writer::end_tag('form');

echo html_writer::end_tag('body');
echo html_writer::end_tag('html');
