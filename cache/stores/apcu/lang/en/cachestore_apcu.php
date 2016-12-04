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
 * APCu cache store language strings.
 *
 * @package    cachestore_apcu
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['clusternotice'] = 'Please be aware that APCu is only a suitable choice for single node sites or caches that can be stored locally.
For more information, see the <a href="{$a}">APC user cache documentation</a>.';
$string['notice'] = 'Notice';
$string['pluginname'] = 'APC user cache (APCu)';
$string['prefix'] = 'Prefix';
$string['prefix_help'] = 'The above prefix gets used for all keys being stored in this APC store instance. By default the database prefix is used.';
$string['prefixinvalid'] = 'The prefix you have selected is invalid. You can only use a-z A-Z 0-9-_.';
$string['prefixnotunique'] = 'The prefix you have selected is not unique. Please choose a unique prefix.';
$string['testperformance'] = 'Test performance';
$string['testperformance_desc'] = 'If enabled, APCu performance will be included when viewing the Test performance page. Enabling this on a production site is not recommended.';
