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
 * Common setup.
 *
 * @package    auth_iomadsaml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use auth_iomadsaml2\event\cert_regenerated;

require_once(__DIR__ . '/setuplib.php');

global $CFG, $iomadsaml2auth;

// Tell SSP that we are on 443 if we are terminating SSL elsewhere.
if (isset($CFG->sslproxy) && $CFG->sslproxy) {
      $_SERVER['SERVER_PORT'] = '443';
}

$iomadsaml2auth = new \auth_iomadsaml2\auth();

// Auto create unique certificates for this moodle SP.
//
// This is one area which many SSP instances get horridly wrong and leave the
// default certificates which is very insecure. Here we create a customized
// cert/key pair just-in-time. If for some reason you do want to use existing
// files then just copy them over the files in /sitedata/iomadsaml2/.
$iomadsaml2auth->get_iomadsaml2_directory(); // It will create it if needed.
$missingcertpem = !file_exists($iomadsaml2auth->certpem);
$missingcertcrt = !file_exists($iomadsaml2auth->certcrt);
if ($missingcertpem || $missingcertcrt) {
    // Could not find one or both certificates. Log an error.
    $errorstring = "";
    $missingcertpem ? $errorstring .= "= Missing cert pem file! =\n" : null;
    $missingcertcrt ? $errorstring .= "= Missing cert crt file! = \n" : null;
    $errorstring .= "Now regenerating iomadsaml2 certificates...";
    if (!(PHPUNIT_TEST || (defined('BEHAT_TEST') && BEHAT_TEST) ||
            defined('BEHAT_SITE_RUNNING'))) {
        debugging($errorstring);
    }
    try {
        create_certificates($iomadsaml2auth);
    } catch (iomadsaml2_exception $exception) {
        debugging($exception->getMessage(), DEBUG_DEVELOPER, $exception->getTrace());
    }
    cert_regenerated::create(['other' => ['reason' => $errorstring]])->trigger();
}

SimpleSAML\Configuration::setConfigDir("$CFG->dirroot/auth/iomadsaml2/config");
