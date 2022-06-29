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
$string['deletepreset'] = 'Are you sure you want to delete the site admin preset {$a}?';
$string['deletepreviouslyapplied'] = 'This preset has been previously applied. Deleting a preset removes it from your site completely. You will not be able to revert your settings to how they were before applying this preset.';
$string['deletepresettitle'] = 'Delete {$a} preset?';
$string['deleteshow'] = 'Delete site admin preset';
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
$string['import'] = 'Import';
$string['imported'] = 'Imported';
$string['importdescription'] = 'Import site admin settings as a preset to apply to your site.';
$string['importexecute'] = 'Import site admin preset';
$string['importshow'] = 'Import site admin preset';
$string['includesensiblesettings'] = 'Include settings with passwords';
$string['includesensiblesettings_help'] = 'Settings with passwords contain sensitive information specific to your site. Only include these settings if you are creating a preset to reuse on your site.';
$string['loaddescription'] = 'Review the setting changes before applying this preset.';
$string['loadexecute'] = 'Site admin preset applied';
$string['loadpreview'] = 'Preview site admin preset';
$string['loadselected'] = 'Apply';
$string['loadshow'] = 'Apply site admin preset';
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
$string['privacy:metadata'] = 'The Site admin presets tool does not store any personal data.';
$string['renamepreset'] = 'Name (optional)';
$string['rollback'] = 'Restore this version';
$string['rollbackdescription'] = 'Use the \'Restore this version\' link to revert to the settings just before the preset was applied.';
$string['rollbackexecute'] = 'Restored version from the site admin preset {$a}';
$string['rollbackfailures'] = 'The following settings cannot be reverted, as the value was changed after applying the preset.';
$string['rollbackresults'] = 'Settings successfully restored';
$string['rollbackshow'] = '{$a} preset version history';
$string['selectfile'] = 'Select file';
$string['settingname'] = 'Setting name';
$string['settingsapplied'] = 'Setting changes';
$string['settingsappliednotification'] = 'Review the following setting changes which have been applied.
<br/>If you change your mind, you can undo the setting changes via \'Show version history\' in the preset actions menu.';
$string['settingsnotapplicable'] = 'Settings not applicable to this Moodle version';
$string['settingsnotapplied'] = 'Unchanged settings';
$string['settingstobeapplied'] = 'Setting changes';
$string['showhistory'] = 'Show version history';
$string['site'] = 'Site';
$string['timeapplied'] = 'Date';
$string['wrongfile'] = 'Wrong file';
