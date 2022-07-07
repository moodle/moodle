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
 * Strings for component 'portfolio_mahara', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   portfolio_mahara
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['enableleap2a'] = 'Enable Leap2A portfolio support (requires Mahara 1.3 or higher)';
$string['err_invalidhost'] = 'Invalid MNet host';
$string['err_invalidhost_help'] = 'This plugin is misconfigured to point to an invalid (or deleted) MNet host.  This plugin relies on Moodle Networking peers with SSO IDP published, SSO_SP subscribed, and portfolio subscribed <b>and</b> published.';
$string['err_networkingoff'] = 'MNet is off';
$string['err_networkingoff_help'] = 'MNet authentication is currently disabled. Please enable it before trying to configure this plugin. Any instances of this plugin have been hidden until MNet is enabled. They will then need to be manually set to visible again.';
$string['err_nomnetauth'] = 'The MNet authentication plugin is disabled';
$string['err_nomnetauth_help'] = 'The MNet authentication plugin is disabled, but is required for this service';
$string['err_nomnethosts'] = 'Relies on MNet';
$string['err_nomnethosts_help'] = 'This plugin relies on MNet peers with SSO IDP published, SSO SP subscribed, portfolio services published <b>and</b> subscribed as well as the MNet authentication plugin.  Any instances of this plugin have been hidden until these conditions are met. They will then manually need setting to visible again.';
$string['failedtojump'] = 'Failed to start communication with remote server';
$string['failedtoping'] = 'Failed to start communication with remote server: {$a}';
$string['mnethost'] = 'MNet host';
$string['mnet_nofile'] = 'Could not find file in transfer object - weird error';
$string['mnet_nofilecontents'] = 'Found file in transfer object, but could not get contents - weird error: {$a}';
$string['mnet_noid'] = 'Could not find the matching transfer record for this token';
$string['mnet_notoken'] = 'Could not find token matching this transfer';
$string['mnet_wronghost'] = 'Remote host did not match the transfer record for this token';
$string['pf_description'] = 'Allow users to push Moodle content to this host<br />Subscribe to <b>and</b> publish this service to allow authenticated users in your site to push content to {$a}<br /><ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Identify Provider) service to {$a}.</li><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Service Provider) service on {$a}</li><li><em>Dependency</em>: You must also enable the MNet authentication plugin.</li></ul><br />';
$string['pf_name'] = 'Portfolio services';
$string['pluginname'] = 'Mahara ePortfolio';
$string['privacy:metadata'] = 'This plugin sends data externally to a linked Mahara application. It does not store data locally.';
$string['privacy:metadata:data'] = 'Personal data passed through from the portfolio subsystem.';
$string['senddisallowed'] = 'You cannot transfer files to Mahara at this time';
$string['url'] = 'URL';
