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
 * Strings for component 'portfolio_flickr', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   portfolio_flickr
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['apikey'] = 'API key';
$string['contenttype'] = 'Content types';
$string['err_noapikey'] = 'No API key';
$string['err_noapikey_help'] = 'There is no API key configured for this plugin. You can get one of these from Flickr services page.';
$string['hidefrompublicsearches'] = 'Hide these images from public searches?';
$string['isfamily'] = 'Visible to family';
$string['isfriend'] = 'Visible to friends';
$string['ispublic'] = 'Public (anyone can see them)';
$string['moderate'] = 'Moderate';
$string['noauthtoken'] = 'Could not retrieve an authentication token for use in this session';
$string['other'] = 'Art, illustration, CGI, or other non-photographic images';
$string['photo'] = 'Photos';
$string['pluginname'] = 'Flickr.com';
$string['privacy:metadata'] = 'This plugin sends data externally to a linked Flickr account. It does not store data locally.';
$string['privacy:metadata:data'] = 'Personal data passed through from the portfolio subsystem.';
$string['restricted'] = 'Restricted';
$string['safe'] = 'Safe';
$string['safetylevel'] = 'Safety level';
$string['screenshot'] = 'Screenshots';
$string['set'] = 'Set';
$string['setupinfo'] = 'Setup instructions';
$string['setupinfodetails'] = 'To obtain API key and the secret string, log in to Flickr and <a href="{$a->applyurl}">apply for a new key</a>. Once new key and secret are generated for you, follow the \'Edit auth flow for this app\' link at the page. Select \'App Type\' to \'Web Application\'. Into the \'Callback URL\' field, put the value: <br /><code>{$a->callbackurl}</code><br />Optionally, you can also provide your Moodle site description and logo. These values can be set later at <a href="{$a->keysurl}">the page</a> listing your Flickr applications.';
$string['sharedsecret'] = 'Secret string';
$string['title'] = 'Title';
$string['uploadfailed'] = 'Failed to upload image(s) to flickr.com: {$a}';
