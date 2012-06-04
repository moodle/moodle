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
 * Strings for component 'portfolio_picasa', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   portfolio_picasa
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['clientid'] = 'Client ID';
$string['noauthtoken'] = 'An authentication token has not been recieved from google. Please ensure you are allowing moodle to access your google account';
$string['nooauthcredentials'] = 'OAuth credentials required.';
$string['nooauthcredentials_help'] = 'To use the Picasa portfolio plugin you must configure OAuth credentials in the portfolio settings.';
$string['oauthinfo'] = '<p>To use the Picasa portfolio you must be registered with Google. Instructions for registing your installation with Google are described in <a href="{$a->docsurl}">Moodle Docs</a>. The redirect url should be set to:</p><p>{$a->callbackurl}</p>';
$string['oauth2upgrade_message_subject'] = 'Important information regarding Picasa portfolio plugin';
$string['oauth2upgrade_message_content'] = 'As part of the upgrade to Moodle 2.3, the Picasa portfolio plugin has been disabled due to changes in Googles API. To re-enable your plugin, you must configure oauth credentials in this plugin.';
$string['oauth2upgrade_message_small'] = 'The Picasa portfolio plugin has been disabled until configured with OAuth2';
$string['pluginname'] = 'Picasa';
$string['sendfailed'] = 'The file {$a} failed to transfer to picasa';
$string['secret'] = 'Secret';
