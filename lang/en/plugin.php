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
 * Defines names of plugin types and some strings used at the plugin managment
 *
 * @package    core
 * @subpackage plugin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['availability'] = 'Availability';
$string['checkforupdates'] = 'Check for available updates';
$string['checkforupdateslast'] = 'Last check done on {$a}';
$string['detectedmisplacedplugin'] = 'Plugin "{$a->component}" is installed in incorrect location "{$a->current}", expected location is "{$a->expected}"';
$string['dependencyinstall'] = 'Install';
$string['dependencyupload'] = 'Upload';
$string['displayname'] = 'Plugin name';
$string['err_response_curl'] = 'Unable to fetch available updates data - unexpected cURL error.';
$string['err_response_format_version'] = 'Unexpected version of the response format. Please try to re-check for available updates.';
$string['err_response_http_code'] = 'Unable to fetch available updates data - unexpected HTTP response code.';
$string['filterall'] = 'Show all';
$string['filtercontribonly'] = 'Show additional plugins only';
$string['filtercontribonlyactive'] = 'Showing additional plugins only';
$string['filterupdatesonly'] = 'Show updateable only';
$string['filterupdatesonlyactive'] = 'Showing updateable only';
$string['moodleversion'] = 'Moodle {$a}';
$string['nonehighlighted'] = 'No plugins require your attention now';
$string['nonehighlightedinfo'] = 'Display the list of all installed plugins anyway';
$string['noneinstalled'] = 'No plugins of this type are installed';
$string['notes'] = 'Notes';
$string['notdownloadable'] = 'Can not download the package';
$string['notdownloadable_help'] = 'ZIP package with the update can not be downloaded automatically. Please refer to the documentation page for more help.';
$string['notdownloadable_link'] = 'admin/mdeploy/notdownloadable';
$string['notwritable'] = 'Plugin files not writable';
$string['notwritable_help'] = 'You have enabled automatic updates deployment and there is an available update for this plugin. However, the plugin files are not writable by the web server so the update cannot be installed automatically.

You need to make the plugin folder and all its contents writable to be able to install the available update automatically.';
$string['notwritable_link'] = 'admin/mdeploy/notwritable';
$string['numtotal'] = 'Installed: {$a}';
$string['numdisabled'] = 'Disabled: {$a}';
$string['numextension'] = 'Additional: {$a}';
$string['numupdatable'] = 'Updates available: {$a}';
$string['otherplugin'] = '{$a->component}';
$string['otherpluginversion'] = '{$a->component} ({$a->version})';
$string['showall'] = 'Reload and show all plugins';
$string['pluginchecknotice'] = 'This page displays plugins that may require your attention during the upgrade. Highlighted items include new plugins that are about to be installed, updated plugins that are about to be upgraded and any missing plugins. Additional plugins are highlighted if there is an available update for them. It is recommended that you check whether there are more recent versions of plugins available and update their source code before continuing with this Moodle upgrade.';
$string['plugindisable'] = 'Disable';
$string['plugindisabled'] = 'Disabled';
$string['pluginenable'] = 'Enable';
$string['pluginenabled'] = 'Enabled';
$string['release'] = 'Release';
$string['requiredby'] = 'Required by: {$a}';
$string['requires'] = 'Requires';
$string['rootdir'] = 'Directory';
$string['settings'] = 'Settings';
$string['somehighlighted'] = 'Number of plugins requiring your attention: {$a}';
$string['somehighlightedinfo'] = 'Display the full list of installed plugins';
$string['somehighlightedonly'] = 'Display only plugins requiring your attention';
$string['source'] = 'Source';
$string['sourceext'] = 'Additional';
$string['sourcestd'] = 'Standard';
$string['status'] = 'Status';
$string['status_delete'] = 'To be deleted';
$string['status_downgrade'] = 'Higher version already installed!';
$string['status_missing'] = 'Missing from disk!';
$string['status_new'] = 'To be installed';
$string['status_nodb'] = 'No database';
$string['status_upgrade'] = 'To be upgraded';
$string['status_uptodate'] = 'Installed';
$string['systemname'] = 'Identifier';
$string['type_auth'] = 'Authentication method';
$string['type_auth_plural'] = 'Authentication methods';
$string['type_availability'] = 'Availability restriction';
$string['type_availability_plural'] = 'Availability restrictions';
$string['type_block'] = 'Block';
$string['type_block_plural'] = 'Blocks';
$string['type_cachelock'] = 'Cache lock handler';
$string['type_cachelock_plural'] = 'Cache lock handlers';
$string['type_cachestore'] = 'Cache store';
$string['type_cachestore_plural'] = 'Cache stores';
$string['type_calendartype'] = 'Calendar type';
$string['type_calendartype_plural'] = 'Calendar types';
$string['type_coursereport'] = 'Course report';
$string['type_coursereport_plural'] = 'Course reports';
$string['type_editor'] = 'Editor';
$string['type_editor_plural'] = 'Editors';
$string['type_enrol'] = 'Enrolment method';
$string['type_enrol_plural'] = 'Enrolment methods';
$string['type_filter'] = 'Text filter';
$string['type_filter_plural'] = 'Text filters';
$string['type_format'] = 'Course format';
$string['type_format_plural'] = 'Course formats';
$string['type_gradeexport'] = 'Grade export method';
$string['type_gradeexport_plural'] = 'Grade export methods';
$string['type_gradeimport'] = 'Grade import method';
$string['type_gradeimport_plural'] = 'Grade import methods';
$string['type_gradereport'] = 'Gradebook report';
$string['type_gradereport_plural'] = 'Gradebook reports';
$string['type_gradingform'] = 'Advanced grading method';
$string['type_gradingform_plural'] = 'Advanced grading methods';
$string['type_local'] = 'Local plugin';
$string['type_local_plural'] = 'Local plugins';
$string['type_message'] = 'Messaging output';
$string['type_message_plural'] = 'Messaging outputs';
$string['type_mnetservice'] = 'MNet service';
$string['type_mnetservice_plural'] = 'MNet services';
$string['type_mod'] = 'Activity module';
$string['type_mod_plural'] = 'Activity modules';
$string['type_plagiarism'] = 'Plagiarism plugin';
$string['type_plagiarism_plural'] = 'Plagiarism plugins';
$string['type_portfolio'] = 'Portfolio';
$string['type_portfolio_plural'] = 'Portfolios';
$string['type_profilefield'] = 'Profile field type';
$string['type_profilefield_plural'] = 'Profile field types';
$string['type_qbehaviour'] = 'Question behaviour';
$string['type_qbehaviour_plural'] = 'Question behaviours';
$string['type_qformat'] = 'Question import/export format';
$string['type_qformat_plural'] = 'Question import/export formats';
$string['type_qtype'] = 'Question type';
$string['type_qtype_plural'] = 'Question types';
$string['type_report'] = 'Site report';
$string['type_report_plural'] = 'Reports';
$string['type_repository'] = 'Repository';
$string['type_repository_plural'] = 'Repositories';
$string['type_theme'] = 'Theme';
$string['type_theme_plural'] = 'Themes';
$string['type_tool'] = 'Admin tool';
$string['type_tool_plural'] = 'Admin tools';
$string['type_webservice'] = 'Webservice protocol';
$string['type_webservice_plural'] = 'Webservice protocols';
$string['updateavailable'] = 'There is a new version {$a} available!';
$string['updateavailable_moreinfo'] = 'More info...';
$string['updateavailable_release'] = 'Release {$a}';
$string['updatepluginconfirm'] = 'Plugin update confirmation';
$string['updatepluginconfirminfo'] = 'You are about to install a new version of the plugin <strong>{$a->name}</strong>. A zip package with version {$a->version} of the plugin will be downloaded from <a href="{$a->url}">{$a->url}</a> and extracted to your Moodle installation so it can upgrade your installation.';
$string['updatepluginconfirmexternal'] = 'It appears that the current version of the plugin has been obtained via source code management system ({$a}) checkout. If you install this update, you will no longer be able to obtain plugin updates from the source code management system. Please ensure that you definitely want to update the plugin before continuing.';
$string['updatepluginconfirmwarning'] = 'Please note that Moodle will not automatically make a backup of your database before the upgrade. We strongly recommend that you make a full snapshot backup now, to cope with the rare case that the new code has bugs that make your site unavailable or even corrupts your database. Proceed at your own risk.';
$string['uninstall'] = 'Uninstall';
$string['uninstallconfirm'] = 'You are about to uninstall the plugin <em>{$a->name}</em>. This will completely delete everything in the database associated with this plugin, including its configuration, log records, user files managed by the plugin etc. There is no way back and Moodle itself does not create any recovery backup. Are you SURE you want to continue?';
$string['uninstalldelete'] = 'All data associated with the plugin <em>{$a->name}</em> has been deleted from the database. To prevent the plugin re-installing itself, its folder <em>{$a->rootdir}</em> must be manually removed from your server now. Moodle itself cannot remove the folder due to write permissions.';
$string['uninstalldeleteconfirm'] = 'All data associated with the plugin <em>{$a->name}</em> has been deleted from the database. To prevent the plugin re-installing itself, its folder <em>{$a->rootdir}</em> must be removed from your server. Do you want to remove the plugin folder now?';
$string['uninstalldeleteconfirmexternal'] = 'It appears that the current version of the plugin has been obtained via source code management system ({$a}) checkout. If you remove the plugin folder, you may lose important local modifications of the code. Please ensure that you definitely want to remove the plugin folder before continuing.';
$string['uninstallextraconfirmblock'] = 'There are {$a->instances} instances of this block.';
$string['uninstallextraconfirmenrol'] = 'There are {$a->enrolments} user enrolments.';
$string['uninstallextraconfirmmod'] = 'There are {$a->instances} instances of this module in {$a->courses} courses.';
$string['uninstalling'] = 'Uninstalling {$a->name}';
$string['version'] = 'Version';
$string['versiondb'] = 'Current version';
$string['versiondisk'] = 'New version';
