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
 * Strings for component 'tool_httpsreplace'
 *
 * @package    tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['count'] = 'Number of embeded content items';
$string['disclaimer'] = 'I understand the risks of this operation';
$string['doclink'] = 'Read more documentation on the wiki';
$string['doit'] = 'Perform replacement';
$string['domain'] = 'Problematic domain';
$string['domainexplain'] = 'When an instance is moved from http to https, all embeded http content will stop working. This tool always you to automatically convert the http content to https. Below is a report of content that may not work once you run this script. You may want to check each one has https available or find alternative resources.';
$string['domainexplainhelp'] = 'These domains are found in your content, but do not appear to support https content. After switching to https, the content included from these sites will no longer display within Moodle for users with secure modern browsers. It is possible that these sites are temporarily or permanently unavailable and will not work with either security setting. Proceed only after reviewing these results and determining if this externally hosted content is non-essential.';
$string['notimplemented'] = 'Sorry, this feature is not implemented in your database driver.';
$string['oktoprocede'] = 'The scan finds no issues with your content. You can proceed to upgrade any http content to use https.';
$string['pageheader'] = 'Upgrade externally hosted content urls to https';
$string['pluginname'] = 'HTTPS conversion tool';
$string['replacing'] = 'Replacing http content with https...';
$string['takeabackupwarning'] = 'Once this tool run, changes made can\'t be reverted. A complete backup should be made before running this script! There is a low risk that the wrong content will be replaced, introducing problems.';
