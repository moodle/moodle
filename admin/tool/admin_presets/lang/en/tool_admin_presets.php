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
 * Admin tool presets plugin to load some settings.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actionexport'] = 'Create preset';
$string['actionexportbutton'] = 'Create preset';
$string['actionimport'] = 'Import preset';
$string['actualvalue'] = 'Actual value';
$string['applyaction'] = 'Review settings and apply';
$string['applypresetdescription'] = 'If you change your mind after applying this preset, you can undo the setting changes via \'Show version history\' in the preset actions menu.';
$string['author'] = 'Author';
$string['basedescription'] = 'Presets allow you to easily switch between different site admin configurations. After selecting a preset, you can turn on more features any time as required.';
$string['created'] = 'Created';
$string['currentvalue'] = 'Current value';
$string['deletepreset'] = 'Are you sure you want to delete "{$a}" site admin preset?';
$string['deletepreviouslyapplied'] = 'This preset has been previously applied. Deleting a preset removes it from your site completely. You will not be able to revert your settings to how they were before applying this preset';
$string['deletepresettitle'] = 'Delete {$a} preset?';
$string['deleteshow'] = 'Delete site admin preset';
$string['disabled'] = 'Disabled';
$string['disabledwithvalue'] = 'Disabled ({$a})';
$string['enabled'] = 'Enabled';
$string['errordeleting'] = 'Error deleting from database.';
$string['errorinserting'] = 'Error inserting into database.';
$string['errornopreset'] = 'It doesn\'t exists a preset with that name.';
$string['eventpresetdeleted'] = 'Preset deleted';
$string['eventpresetdownloaded'] = 'Preset downloaded';
$string['eventpresetexported'] = 'Preset created';
$string['eventpresetimported'] = 'Preset imported';
$string['eventpresetloaded'] = 'Preset applied';
$string['eventpresetpreviewed'] = 'Preset previewed';
$string['eventpresetreverted'] = 'Preset restored';
$string['eventpresetslisted'] = 'Presets have been listed';
$string['exportdescription'] = 'Save all your current site admin settings as a preset to share or reuse.';
$string['exportshow'] = 'Create site admin preset';
$string['falseaction'] = 'Action not supported in this version.';
$string['falsemode'] = 'Mode not supported in this version.';
$string['fullpreset'] = 'Full';
$string['fullpresetdescription'] = 'All the Starter features plus External (LTI) tool, SCORM, Workshop, Analytics, Badges, Competencies, Learning plans and lots more.';
$string['import'] = 'Import';
$string['imported'] = 'Imported';
$string['importdescription'] = 'Import site admin settings as a preset to apply to your site.';
$string['importexecute'] = 'Import site admin preset';
$string['importshow'] = 'Import site admin preset';
$string['includesensiblesettings'] = 'Include settings with passwords';
$string['includesensiblesettings_help'] = 'Settings with passwords contain sensitive information specific to your site. Only include these settings if you are creating a preset to reuse on your site. You can find the list of settings with passwords in Site admin preset settings in the Site administration';
$string['starterpreset'] = 'Starter';
$string['starterpresetdescription'] = 'Moodle with all of the most popular features, including Assignment, Feedback, Forum, H5P, Quiz and Completion tracking.';
$string['loaddescription'] = 'Review the setting changes before applying this preset.';
$string['loadexecute'] = 'Site admin preset applied';
$string['loadpreview'] = 'Preview site admin preset';
$string['loadselected'] = 'Apply';
$string['loadshow'] = 'Apply site admin preset';
$string['markedasadvanced'] = 'marked as advanced';
$string['markedasforced'] = 'marked as forced';
$string['markedaslocked'] = 'marked as locked';
$string['markedasnonadvanced'] = 'marked as non advanced';
$string['markedasnonforced'] = 'marked as non forced';
$string['markedasnonlocked'] = 'marked as non locked';
$string['newvalue'] = 'New value';
$string['nopresets'] = 'You don\'t have any site admin preset.';
$string['nosettingswillbeapplied'] = 'These settings are the same as the current settings; there are no changes to apply.';
$string['nothingloaded'] = 'No setting changes have been made because the settings in the preset are the same as on your site.';
$string['novalidsettings'] = 'No valid settings';
$string['novalidsettingsselected'] = 'No valid settings selected';
$string['oldvalue'] = 'Old value';
$string['pluginname'] = 'Site admin presets';
$string['presetapplicationslisttable'] = 'Site admin preset applications table';
$string['presetslisttable'] = 'Site admin presets table';
$string['presetmoodlerelease'] = 'Moodle release';
$string['presetname'] = 'Preset name';
$string['presetsettings'] = 'Preset settings';
$string['previewpreset'] = 'Preview preset';
$string['privacy:metadata:admin_presets'] = 'The list of configuration presets.';
$string['privacy:metadata:admin_presets:comments'] = 'A description about the preset.';
$string['privacy:metadata:admin_presets:moodlerelease'] = 'The Moodle release version where the preset is based on.';
$string['privacy:metadata:admin_presets:name'] = 'The name of the preset.';
$string['privacy:metadata:admin_presets:site'] = 'The Moodle site where this preset was created.';
$string['privacy:metadata:admin_presets:timecreated'] = 'The time that the change was made.';
$string['privacy:metadata:admin_presets:userid'] = 'The user who create the preset.';
$string['privacy:metadata:tool_admin_presets_app'] = 'The configuration presets that have been applied.';
$string['privacy:metadata:tool_admin_presets_app:adminpresetid'] = 'The id of the preset applied.';
$string['privacy:metadata:tool_admin_presets_app:time'] = 'The time that the preset was applied.';
$string['privacy:metadata:tool_admin_presets_app:userid'] = 'The user who applied the preset.';
$string['renamepreset'] = 'Name (optional)';
$string['rollback'] = 'Restore this version';
$string['rollbackdescription'] = 'Use the \'Restore this version\' link to revert to the settings just before the preset was applied.';
$string['rollbackexecute'] = 'Restored version from "{$a}" site admin preset';
$string['rollbackfailures'] = 'The following settings can not be restored, the actual values differs from the values applied by the preset';
$string['rollbackresults'] = 'Settings successfully restored';
$string['rollbackshow'] = '{$a} preset version history';
$string['selectfile'] = 'Select file';
$string['sensiblesettings'] = 'Settings with passwords';
$string['sensiblesettingstext'] = 'Settings with passwords or other sensitive information can be excluded when creating a site admin preset. Enter additional settings with format SETTINGNAME@@PLUGINNAME separated by commas.';
$string['settingname'] = 'Setting name';
$string['settingsapplied'] = 'Setting changes';
$string['settingsappliednotification'] = 'Review the following setting changes which have been applied.
<br/>If you change your mind, you can undo the setting changes via \'Show version history\' in the preset actions menu.';
$string['settingsnotapplicable'] = 'Settings not applicable to this Moodle version';
$string['settingsnotapplied'] = 'Unchanged settings';
$string['settingstobeapplied'] = 'Setting changes';
$string['showhistory'] = 'Show version history';
$string['site'] = 'Site';
$string['skippedchanges'] = 'Skipped settings table';
$string['timeapplied'] = 'Date';
$string['wrongfile'] = 'Wrong file';
$string['wrongid'] = 'Wrong id';
