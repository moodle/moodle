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
 * Strings for component 'tool_uploaduser', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    tool
 * @subpackage uploaduser
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allowdeletes'] = 'Allow deletes';
$string['allowrenames'] = 'Allow renames';
$string['allowsuspends'] = 'Allow suspending and activating of accounts';
$string['assignedsysrole'] = 'Assigned system role {$a}';
$string['clidefault'] = 'Default:';
$string['clierrorargument'] = 'Value for argument --{$a->name} is not valid. Allowed values: {$a->values}';
$string['clifile'] = 'Path to CSV file with the user data. Required.';
$string['clifilenotreadable'] = 'File {$a} does not exist or is not readable';
$string['clihelp'] = 'Print out this help.';
$string['climissingargument'] = 'Argument --{$a} is required';
$string['clititle'] = 'Command line Upload user tool.';
$string['clivalidationerror'] = 'Validation error:';
$string['csvdelimiter'] = 'CSV delimiter';
$string['defaultvalues'] = 'Default values';
$string['deleteerrors'] = 'Delete errors';
$string['encoding'] = 'Encoding';
$string['errormnetadd'] = 'Can not add remote users';
$string['errorprefix'] = 'Error:';
$string['errors'] = 'Errors';
$string['examplecsv'] = 'Example text file';
$string['examplecsv_help'] = 'To use the example text file, download it then open it with a text or spreadsheet editor. Leave the first line unchanged, then edit the following lines (records) and add your user data, adding more lines as necessary. Save the file as CSV then upload it.

The example text file may also be used for testing, as you are able to preview user data and can choose to cancel the action before user accounts are created.';
$string['infoprefix'] = 'Info:';
$string['invalidupdatetype'] = 'This option cannot be selected with the chosen upload type.';
$string['invaliduserdata'] = 'Invalid data detected for user {$a} and it has been automatically cleaned.';
$string['invalidtheme'] = 'Theme "{$a}" is not installed and will be ignored.';
$string['linex'] = 'Line {$a}';
$string['nochanges'] = 'No changes';
$string['notheme'] = 'No theme is defined for this user.';
$string['pluginname'] = 'User upload';
$string['renameerrors'] = 'Rename errors';
$string['requiredtemplate'] = 'Required. You may use template syntax here (%l = lastname, %f = firstname, %u = username). See help for details and examples.';
$string['rowpreviewnum'] = 'Preview rows';
$string['unassignedsysrole'] = 'Unassigned system role {$a}';
$string['userthemesnotallowed'] = 'User themes are not enabled, so any included in the upload users file will be ignored.';
$string['uploadpicture_baduserfield'] = 'The user attribute specified is not valid. Please, try again.';
$string['uploadpicture_cannotmovezip'] = 'Cannot move zip file to temporary directory.';
$string['uploadpicture_cannotprocessdir'] = 'Cannot process unzipped files.';
$string['uploadpicture_cannotsave'] = 'Cannot save picture for user {$a}. Check original picture file.';
$string['uploadpicture_cannotunzip'] = 'Cannot unzip pictures file.';
$string['uploadpicture_invalidfilename'] = 'Picture file {$a} has invalid characters in its name. Skipping.';
$string['uploadpicture_overwrite'] = 'Overwrite existing user pictures?';
$string['uploadpicture_userfield'] = 'User attribute to use to match pictures:';
$string['uploadpicture_usernotfound'] = 'User with a \'{$a->userfield}\' value of \'{$a->uservalue}\' does not exist. Skipping.';
$string['uploadpicture_userskipped'] = 'Skipping user {$a} (already has a picture).';
$string['uploadpicture_userupdated'] = 'Picture updated for user {$a}.';
$string['uploadpictures'] = 'Upload user pictures';
$string['uploadpictures_help'] = 'User pictures can be uploaded as a zip file of image files. The image files should be named chosen-user-attribute.extension, for example user1234.jpg for a user with username user1234.';
$string['uploadusers'] = 'Upload users';
$string['uploadusers_help'] = 'Users may be uploaded (and optionally enrolled in courses) via text file. The format of the file should be as follows:

* Each line of the file contains one record
* Each record is a series of data separated by commas (or other delimiters)
* The first record contains a list of fieldnames defining the format of the rest of the file
* Required fieldnames are username, password, firstname, lastname, email';
$string['uploadusers_link'] = 'admin/tool/uploaduser/index';
$string['uploaduserspreview'] = 'Upload users preview';
$string['uploadusersresult'] = 'Upload users results';
$string['uploaduser:uploaduserpictures'] = 'Upload user pictures';
$string['useraccountupdated'] = 'User updated';
$string['useraccountuptodate'] = 'User up-to-date';
$string['userdeleted'] = 'User deleted';
$string['userrenamed'] = 'User renamed';
$string['userscreated'] = 'Users created';
$string['usersdeleted'] = 'Users deleted';
$string['usersrenamed'] = 'Users renamed';
$string['usersskipped'] = 'Users skipped';
$string['usersupdated'] = 'Users updated';
$string['usersweakpassword'] = 'Users having a weak password';
$string['uubulk'] = 'Select for bulk user actions';
$string['uubulkall'] = 'All users';
$string['uubulknew'] = 'New users';
$string['uubulkupdated'] = 'Updated users';
$string['uucsvline'] = 'CSV line';
$string['uulegacy1role'] = '(Original Student) typeN=1';
$string['uulegacy2role'] = '(Original Teacher) typeN=2';
$string['uulegacy3role'] = '(Original Non-editing teacher) typeN=3';
$string['uunoemailduplicates'] = 'Prevent email address duplicates';
$string['uuoptype'] = 'Upload type';
$string['uuoptype_addinc'] = 'Add all, append number to usernames if needed';
$string['uuoptype_addnew'] = 'Add new only, skip existing users';
$string['uuoptype_addupdate'] = 'Add new and update existing users';
$string['uuoptype_update'] = 'Update existing users only';
$string['uupasswordcron'] = 'Generated in cron';
$string['uupasswordnew'] = 'New user password';
$string['uupasswordold'] = 'Existing user password';
$string['uustandardusernames'] = 'Standardise usernames';
$string['uuupdateall'] = 'Override with file and defaults';
$string['uuupdatefromfile'] = 'Override with file';
$string['uuupdatemissing'] = 'Fill in missing from file and defaults';
$string['uuupdatetype'] = 'Existing user details';
$string['uuusernametemplate'] = 'Username template';
$string['privacy:metadata'] = 'The User upload plugin does not store any personal data.';
$string['warningprefix'] = 'Warning:';
