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
 * Test page for SAML
 *
 * @package    auth_iomadsaml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Require_login is not needed here.
// phpcs:disable moodle.Files.RequireLogin.Missing
require_once(__DIR__ . '/../../config.php');
require('setup.php');

// Check we are in debug mode to use this tool.
if (!$iomadsaml2auth->is_debugging()) {
    throw new \moodle_exception('testdebuggingdisabled', 'auth_iomadsaml2');
}

if (!\auth_iomadsaml2\api::is_enabled()) {
    throw new \moodle_exception('plugindisabled', 'auth_iomadsaml2');
}

$idp = optional_param('idp', '', PARAM_TEXT);
$logout = optional_param('logout', false, PARAM_BOOL);
$idplogout = optional_param('idplogout', '', PARAM_TEXT);
$testtype = optional_param('testtype', 'login', PARAM_TEXT);
$passive = optional_param('passive', false, PARAM_BOOL);
$passivefail = optional_param('passivefail', false, PARAM_BOOL);
$trylogin = optional_param('login', false, PARAM_BOOL);

if ($testtype === 'passive') {
    $passive = true;
}

if (!empty($idp)) {
    $SESSION->iomadsaml2idp = $idp;
    echo "<p>Setting IdP via param</p>";
}

if (empty($SESSION->iomadsaml2idp)) {
    // Specify the default IdP to use.
    $SESSION->iomadsaml2idp = reset($iomadsaml2auth->metadataentities)->md5entityid;
    echo '<p>Setting IdP to default</p>';
}

if (!empty($logout)) {
    $SESSION->iomadsaml2idp = $idplogout;
}

echo '<p>SP name: ' . $iomadsaml2auth->spname;
echo '<p>Which IdP will be used? ' . s($SESSION->iomadsaml2idp);

$auth = new SimpleSAML\Auth\Simple($iomadsaml2auth->spname);

foreach ($iomadsaml2auth->metadataentities as $idpentity) {
    echo '<hr>';
    echo "<h4>IDP: $idpentity->entityid</h4>";
    echo "<p>md5: $idpentity->md5entityid</p>";
    echo "<p>check: " . md5($idpentity->entityid) . "</p>";
}

if ($logout) {
    $url = new moodle_url('/auth/iomadsaml2/test.php');
    $auth->logout(['ReturnTo' => $url->out(false)]);
}

if (!$auth->isAuthenticated() && $passive) {
    /* Prevent it from calling the missing post redirection. /auth/iomadsaml2/sp/module.php/core/postredirect.php */
    $auth->requireAuth(array(
        'KeepPost' => false,
        'isPassive' => true,
        'ErrorURL' => $CFG->wwwroot . '/auth/iomadsaml2/test.php?passivefail=1'
    ));
} else if (!$auth->isAuthenticated() && $trylogin) {
    $auth->requireAuth(array(
        'KeepPost' => false
    ));
} else if (!$auth->isAuthenticated()) {
    echo '<p>You are not logged in: <a href="?login=true">Login</a> | <a href="?passive=true">isPassive test</a></p>';
    if ($passivefail) {
        $state = \SimpleSAML\Auth\State::loadExceptionState();
        $exception = $state[\SimpleSAML\Auth\State::EXCEPTION_DATA];
        echo "Passive test failed with error: " . $exception->getMessage();
    }
} else {
    echo '<hr>';
    echo 'Authed with IdP ' . $auth->getAuthData('saml:sp:IdP');
    echo '<pre>';
    echo json_encode($auth->getAttributes(), JSON_PRETTY_PRINT);
    echo '</pre>';
    echo '<p>You are logged in: <a href="?logout=true&idplogout=' . md5($auth->getAuthData('saml:sp:IdP')) . '">Logout</a></p>';
}

