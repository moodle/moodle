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
 * Lang strings
 *
 * @package   report_performance
 * @copyright 2013 Rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['check_backup'] = 'Automated backup';
$string['check_backup_comment_disable'] = 'Performance may be affected during the backup process. If enabled, backups should be scheduled for off-peak times.';
$string['check_backup_comment_enable'] = 'Performance may be affected during the backup process. Backups should be scheduled for off-peak times.';
$string['check_backup_details'] = 'Enabling automated backup will automatically create archives of all the courses on the server at the time you specified.<p>During this process, it will consume more server resources and may affect performance.</p>';
$string['check_cachejs_comment_disable'] = 'If enabled, page loading performance is improved.';
$string['check_cachejs_comment_enable'] = 'If disabled, page might load slow.';
$string['check_cachejs_details'] = 'Javascript caching and compression greatly improves page loading performance. It is strongly recommended for production sites.';
$string['check_dbschema_name'] = 'Database schema check';
$string['check_dbschema_ok'] = 'Database schema is correct.';
$string['check_dbschema_errors'] = 'Database schema is not aligned.';
$string['check_debugmsg_comment_nodeveloper'] = 'If set to DEVELOPER, performance may be affected slightly.';
$string['check_debugmsg_comment_developer'] = 'If set to a value other than DEVELOPER, performance may be improved slightly.';
$string['check_debugmsg_details'] = 'There is rarely any advantage in going to Developer level, unless requested by a developer.<p>Once you have obtained the error message, and copied and pasted it somewhere, it is HIGHLY RECOMMENDED to turn Debug back to NONE. Debug messages can give clues to a hacker as to the setup of your site and may affect performance.</p>';
$string['check_enablestats_comment_disable'] = 'Performance may be affected by statistics processing. If enabled, statistics settings should be set with caution.';
$string['check_enablestats_comment_enable'] = 'Performance may be affected by statistics processing. Statistics settings should be set with caution.';
$string['check_enablestats_details'] = 'Enabling this will process the logs in cronjob and gather some statistics. Depending on the amount of traffic on your site, this can take awhile.<p>During this process, it will consume more server resources and may affect performance.</p>';
$string['check_themedesignermode_comment_enable'] = 'If disabled, images and style sheets are cached, resulting in significant performance improvements.';
$string['check_themedesignermode_comment_disable'] = 'If enabled, images and style sheets will not be cached, resulting in significant performance degradation.';
$string['check_themedesignermode_details'] = 'This is often the cause of slow Moodle sites. <p>On average it might take at least twice the amount of CPU to run a Moodle site with theme designer mode enabled.</p>';
$string['comments'] = 'Comments';
$string['edit'] = 'Edit';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['issue'] = 'Issue';
$string['morehelp'] = 'more help';
$string['performance:view'] = 'View performance report';
$string['performancereportdesc'] = 'This report lists issues which may affect performance of the site {$a}';
$string['pluginname'] = 'Performance overview';
$string['value'] = 'Value';
$string['privacy:metadata'] = 'The Performance overview plugin does not store any personal data.';
