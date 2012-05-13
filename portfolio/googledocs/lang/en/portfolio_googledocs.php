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
 * Strings for component 'portfolio_googledocs', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   portfolio_googledocs
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['clientid'] = 'Client ID';
$string['oauthinfo'] = '<p>To use the google docs portfolio you must be registered with Google. Instructions for registing your installation with Google are described in <a href="{$a->docsurl}">Moodle Docs</a>. The redirect url should be set to:</p><p>{$a->callbackurl}</p>';
$string['noauthtoken'] = 'An authentication token has not been recieved from google. Please ensure you are allowing moodle to access your google account';
$string['nosessiontoken'] = 'A session token does not exist preventing export to google.';
$string['pluginname'] = 'Google Docs';
$string['sendfailed'] = 'The file {$a} failed to transfer to google';
$string['secret'] = 'Secret';
