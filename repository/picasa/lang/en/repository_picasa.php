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
 * Strings for component 'repository_picasa', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   repository_picasa
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['clientid'] = 'Client ID';
$string['configplugin'] = 'Picasa repository configuration';
$string['oauthinfo'] = '<p>To use this plugin, you must register your site with Google, as described in the documentation <a href="{$a->docsurl}">Google OAuth 2.0 setup</a>.</p><p>As part of the registration process, you will need to enter the following URL as \'Authorized Redirect URIs\':</p><p>{$a->callbackurl}</p><p>Once registered, you will be provided with a client ID and secret which can be used to configure all Google Drive and Picasa plugins.</p>';
$string['oauth2upgrade_message_subject'] = 'Important information regarding Picasa repository plugin';
$string['oauth2upgrade_message_content'] = 'As part of the upgrade to Moodle 2.3, the Picasa portfolio plugin has been disabled. To re-enable it, your Moodle site needs to be registered with Google, as described in the documentation {$a->docsurl}, in order to obtain a client ID and secret. The client ID and secret can then be used to configure all Google Drive and Picasa plugins.';
$string['oauth2upgrade_message_small'] = 'This plugin has been disabled, as it requires configuration as described in the documentation Google OAuth 2.0 setup.';
$string['picasa:view'] = 'View picasa repository';
$string['pluginname'] = 'Picasa web album';
$string['secret'] = 'Secret';
