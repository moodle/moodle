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
 * Strings for component 'tool_bloglevelupgrade', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    tool
 * @subpackage bloglevelupgrade
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['bloglevelupgradedescription'] = '<p>This site has recently been upgraded to Moodle 2.0.</p>
<p>Blog visibility was simplified in 2.0, but your site still uses one of the old visibility types. </p>
<p>To preserve the course-based or group-based visibility of the blog entries on your site, you need to run the following upgrade script, which will create a special "blog" type forum in each course whose enrolled users have posted blog entries, and will copy these blog entries in this special forum. </p>
<p>Blogs will then be entirely switched off at the site level. No blog entries will be deleted in the process.</p>
<p>You can run the script by visiting <a href="{$a->fixurl}">the blog level upgrade page</a>.</p>';
$string['bloglevelupgradeinfo'] = 'Blog visibility was simplified in 2.0, but your site still uses one of the old visibility types. To preserve the course-based or group-based visibility of the blog entries on your site, the following upgrade script will create a special "blog" type forum in each course whose enrolled users have posted blog entries, and will copy these blog entries in this special forum. Blogs will then be entirely switched off at the site level. No blog entries will be deleted in the process.';
$string['bloglevelupgradeprogress'] = 'Conversion progress: {$a->userscount} users reviewed, {$a->blogcount} entries converted.';
$string['pluginname'] = 'Blog visibility upgrade';
