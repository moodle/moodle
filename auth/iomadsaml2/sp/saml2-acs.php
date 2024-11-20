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

// @codingStandardsIgnoreStart
require_once(__DIR__ . '/../../../config.php');
// @codingStandardsIgnoreEnd
require('../setup.php');

// First setup the PATH_INFO because that's how SSP rolls.
$_SERVER['PATH_INFO'] = '/' . $iomadsaml2auth->spname;

try {
    require($CFG->dirroot.'/auth/iomadsaml2/.extlib/simplesamlphp/modules/saml/www/sp/saml2-acs.php');
} catch (Exception $e) {
    throw new iomadsaml2_exception($e->getMessage(), $e->getTraceAsString());
}

