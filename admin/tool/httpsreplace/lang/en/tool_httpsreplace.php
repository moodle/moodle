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

$string['complete'] = 'Completed';
$string['count'] = 'Number of embedded content items';
$string['disclaimer'] = 'I understand the risks of this operation';
$string['doclink'] = 'HTTPS conversion tool';
$string['doit'] = 'Perform conversion';
$string['domain'] = 'Problematic domain';
$string['domainexplain'] = 'When a site is moved from HTTP to HTTPS, all embeded HTTP content will stop working. This tool allows you to automatically convert HTTP content to HTTPS.

Before performing the conversion, content will be scanned to find any URLs which may not work after conversion. You may want to check each one has HTTPS available, or find alternative resources.';
$string['domainexplainhelp'] = 'These domains are found in your content, but do not appear to support HTTPS content. After switching to HTTPS, the content included from these sites will no longer display within Moodle for users with secure modern browsers. It is possible that these sites are temporarily or permanently unavailable and will not work with either security setting. Proceed only after reviewing these results and determining if this externally-hosted content is non-essential. Note: This content would no longer work upon switching to HTTPS anyway.';
$string['httpwarning'] = 'This instance is still running on HTTP. You can still run this tool and external content will be changed to HTTPS, but internal content will remain on HTTP. You will need to run this script again after switching to HTTPS to convert internal content.';
$string['notimplemented'] = 'Sorry, this feature is not implemented in your database driver.';
$string['oktoprocede'] = 'The scan finds no issues with your content. You can proceed to upgrade any HTTP content to use HTTPS.';
$string['pageheader'] = 'Upgrade externally-hosted content URLs to HTTPS';
$string['pluginname'] = 'HTTPS conversion tool';
$string['replacing'] = 'Replacing HTTP content with HTTPS...';
$string['searching'] = 'Searching {$a}';
$string['takeabackupwarning'] = 'Warning: After running this tool, changes cannot be reverted. It is recommended that a site backup is made before proceeding, as there is a small risk of wrong content being replaced.';
$string['toolintro'] = 'If you are planning on converting your site to HTTPS, you can use the <a href="{$a}">HTTPS conversion tool</a> to convert your embedded content to HTTPS.';
$string['privacy:metadata'] = 'The HTTPS conversion tool plugin does not store any personal data.';
