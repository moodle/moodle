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
 * Strings for component 'repository', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   repository
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accessiblefilepicker'] = 'Accessible file picker';
$string['activaterep'] = 'Active repositories';
$string['activerepository'] = 'Available repository plugins';
$string['add'] = 'Add';
$string['addfile'] = 'Add...';
$string['addplugin'] = 'Add a repository plugin';
$string['allowexternallinks'] = 'Allow external links';
$string['areamainfile'] = 'Main file';
$string['coursebackup'] = 'Course backups';
$string['pluginname'] = 'Repository plugin name'; // todo fix this, this string identifier is reserved
$string['pluginnamehelp'] = 'If you leave this empty the default name will be used.';
$string['sectionbackup'] = 'Section backups';
$string['activitybackup'] = 'Activity backup';
$string['areacategoryintro'] = 'Category introduction';
$string['areacourseintro'] = 'Course introduction';
$string['areacourseoverviewfiles'] = 'Course summary files';
$string['arearoot'] = 'System';
$string['areauserdraft'] = 'Drafts';
$string['areauserbackup'] = 'User backup';
$string['areauserpersonal'] = 'Private files';
$string['areauserprofile'] = 'Profile';
$string['attachedfiles'] = 'Attached files';
$string['attachment'] = 'Attachment';
$string['author'] = 'Author';
$string['back'] = '&laquo; Back';
$string['backtodraftfiles'] = '&laquo; Back to draft files manager';
$string['cachecleared'] = 'Cached files are removed';
$string['cacheexpire'] = 'Cache expire';
$string['cannotaccessparentwin'] = 'If parent window is on HTTPS, then we are not allowed to access window.opener object, so we cannot refresh the repository for you automatically, but we already got your session, just go back to file picker and select the repository again, it should work now.';
$string['cannotdelete'] = 'Cannot delete this file.';
$string['cannotdownload'] = 'Cannot download this file';
$string['cannotdownloaddir'] = 'Cannot download this folder';
$string['cannotinitplugin'] = 'Call plugin_init failed';
$string['cleancache'] = 'Clean my cache files';
$string['close'] = 'Close';
$string['commonrepositorysettings'] = 'Common repository settings';
$string['configallowexternallinks'] = 'This option enables all users to choose whether or not external media is copied into Moodle or not. If this is off then media is always copied into Moodle (this is usually best for overall data integrity and security).  If this is on then users can choose each time they add media to a text.';
$string['configcacheexpire'] = 'The amount of time that file listings are cached locally (in seconds) when browsing external repositories.';
$string['configsaved'] = 'Configuration saved!';
$string['confirmdelete'] = 'Are you sure you want to delete this repository - {$a}? If you choose "Continue and download", file references to external contents will be downloaded to moodle, but it could take long time to process.';
$string['confirmdeletefile'] = 'Are you sure you want to delete this file?';
$string['confirmrenamefile'] = 'Are you sure you want to rename/move this file? There are {$a} alias/shortcut files that use this file as their source. If you proceed then those aliases will be converted to true copies.';
$string['confirmdeletefilewithhref'] = 'Are you sure you want to delete this file? There are {$a} alias/shortcut files that use this file as their source. If you proceed then those aliases will be converted to true copies.';
$string['confirmdeletefolder'] = 'Are you sure you want to delete this folder? All files and subfolders will be deleted.';
$string['confirmremove'] = 'Are you sure you want to remove this repository plugin, its options and <strong>all of its instances</strong> - {$a}? If you choose "Continue and download", file references to external contents will be downloaded to moodle, but it could take long time to process.';
$string['confirmrenamefolder'] = ' Are you sure you want to move/rename this folder? Any alias/shortcut files that reference files in this folder will be converted into true copies.';
$string['continueuninstall'] = 'Continue';
$string['continueuninstallanddownload'] = 'Continue and download';
$string['copying'] = 'Copying';
$string['create'] = 'Create';
$string['createfolderfail'] = 'Fail to create this folder';
$string['createfoldersuccess'] = 'Create folder successfully';
$string['createinstance'] = 'Create a repository instance';
$string['createrepository'] = 'Create a repository instance';
$string['createxxinstance'] = 'Create "{$a}" instance';
$string['date'] = 'Date';
$string['datecreated'] = 'Created';
$string['deleted'] = 'Repository deleted';
$string['deleterepository'] = 'Delete this repository';
$string['detailview'] = 'View details';
$string['dimensions'] = 'Dimensions';
$string['disabled'] = 'Disabled';
$string['displaydetails'] = 'Display folder with file details';
$string['displayicons'] = 'Display folder with file icons';
$string['displaytree'] = 'Display folder as file tree';
$string['download'] = 'Download';
$string['downloadfolder'] = 'Download all';
$string['downloadsucc'] = 'The file has been downloaded successfully';
$string['draftareanofiles'] = 'Cannot be downloaded because there is no files attached';
$string['editrepositoryinstance'] = 'Edit repository instance';
$string['emptylist'] = 'Empty list';
$string['emptytype'] = 'Cannot create repository type: type name is empty';
$string['enablecourseinstances'] = 'Allow users to add a repository instance into the course';
$string['enableuserinstances'] = 'Allow users to add a repository instance into the user context';
$string['enter'] = 'Enter';
$string['entername'] = 'Please enter folder name';
$string['enternewname'] = 'Please enter the new file name';
$string['error'] = 'An unknown error occurred!';
$string['errordoublereference'] = 'Unable to overwrite file with a shortcut/alias because shortcuts to this file already exist.';
$string['errornotyourfile'] = 'You cannot pick file which is not added by your';
$string['erroruniquename'] = 'Repository instance name should be unique';
$string['errorpostmaxsize'] = 'The uploaded file may exceed the post_max_size directive in php.ini.';
$string['errorwhilecommunicatingwith'] = 'Error while communicating with the repository \'{$a}\'.';
$string['errorwhiledownload'] = 'An error occurred while downloading the file: {$a}';
$string['existingrepository'] = 'This repository already exists';
$string['federatedsearch'] = 'Federated search';
$string['fileexists'] = 'File name already being used, please use another name';
$string['fileexistsdialog_editor'] = 'A file with that name has already been attached to the text you are editing.';
$string['fileexistsdialog_filemanager'] = 'A file with that name has already been attached';
$string['fileexistsdialogheader'] = 'File exists';
$string['filename'] = 'Filename';
$string['filenotnull'] = 'You must select a file to upload.';
$string['filesaved'] = 'The file has been saved';
$string['filepicker'] = 'File picker';
$string['filesizenull'] = 'File size cannot be determined';
$string['folderexists'] = 'Folder name already being used, please use another name';
$string['foldernotfound'] = 'Folder not found';
$string['folderrecurse'] = 'Folder can not be moved to it\'s own subfolder';
$string['getfile'] = 'Select this file';
$string['hidden'] = 'Hidden';
$string['help'] = 'Help';
$string['choosealink'] = 'Choose a link...';
$string['chooselicense'] = 'Choose license';
$string['iconview'] = 'View as icons';
$string['imagesize'] = '{$a->width} x {$a->height} px';
$string['instance'] = 'instance';
$string['instancedeleted'] = 'Instance deleted';
$string['instances'] = 'Repository instances';
$string['instancesforsite'] = '{$a} Site-wide common instance(s)';
$string['instancesforcourses'] = '{$a} Course-wide common instance(s)';
$string['instancesforusers'] = '{$a} User private instance(s)';
$string['invalidjson'] = 'Invalid JSON string';
$string['invalidplugin'] = 'Invalid repository {$a} plug-in';
$string['invalidfiletype'] = '{$a} filetype cannot be accepted.';
$string['invalidrepositoryid'] = 'Invalid repository ID';
$string['invalidparams'] = 'Invalid parameters';
$string['isactive'] = 'Active?';
$string['keyword'] = 'Keyword';
$string['linkexternal'] = 'Link external';
$string['listview'] = 'View as list';
$string['loading'] = 'Loading...';
$string['login'] = 'Login';
$string['logout'] = 'Logout';
$string['lostsource'] = 'Error. Source is missing. {$a}';
$string['makefileinternal'] = 'Make a copy of the file';
$string['makefilelink'] = 'Link to the file directly';
$string['makefilereference'] = 'Create an alias/shortcut to the file';
$string['manage'] = 'Manage repositories';
$string['manageurl'] = 'Manage';
$string['manageuserrepository'] = 'Manage individual repository';
$string['moving'] = 'Moving';
$string['newfolder'] = 'New folder';
$string['newfoldername'] = 'New folder name:';
$string['noenter'] = 'Nothing entered';
$string['nofilesattached'] = 'No files attached';
$string['nofilesavailable'] = 'No files available';
$string['nomorefiles'] = 'No more attachments allowed';
$string['nopathselected'] = 'No destination path select yet (double click tree node to select)';
$string['nopermissiontoaccess'] = 'No permission to access this repository.';
$string['noresult'] = 'No search result';
$string['norepositoriesavailable'] = 'Sorry, none of your current repositories can return files in the required format.';
$string['norepositoriesexternalavailable'] = 'Sorry, none of your current repositories can return external files.';
$string['notyourinstances'] = 'You can not view/edit repository instances of another user';
$string['off'] = 'Enabled but hidden';
$string['original'] = 'Original';
$string['openpicker'] = 'Choose a file...';
$string['operation'] = 'Operation';
$string['on'] = 'Enabled and visible';
$string['overwrite'] = 'Overwrite';
$string['overwriteall'] = 'Overwrite all';
$string['personalrepositories'] = 'Available repository instances';
$string['plugin'] = 'Repository plug-ins';
$string['pluginerror'] = 'Errors in repository plugin.';
$string['popup'] = 'Click "Login" button to login';
$string['popupblockeddownload'] = 'The downloading window is blocked, please allow the popup window, and try again.';
$string['preview'] = 'Preview';
$string['privatefilesof'] = '{$a} Private files';
$string['readonlyinstance'] = 'You cannot edit/delete a read-only instance';
$string['referencesexist'] = 'There are {$a} alias/shortcut files that use this file as their source';
$string['referenceslist'] = 'Aliases/Shortcuts';
$string['refresh'] = 'Refresh';
$string['refreshnonjsfilepicker'] = 'Please close this window and refresh non-javascript file picker';
$string['removed'] = 'Repository removed';
$string['renameall'] = 'Rename all';
$string['renameto'] = 'Rename to "{$a}"';
$string['repositories'] = 'Repositories';
$string['repository'] = 'Repository';
$string['repositorycourse'] = 'Course repositories';
$string['repositoryicon'] = 'Repository icon';
$string['repositoryerror'] = 'Remote repository returned error: {$a}';
$string['save'] = 'Save';
$string['saveas'] = 'Save as';
$string['saved'] = 'Saved';
$string['saving'] = 'Saving';
$string['automatedbackup'] = 'Automated backups';
$string['search'] = 'Search';
$string['searching'] = 'Search in';
$string['searchrepo'] = 'Search repository';
$string['select'] = 'Select';
$string['settings'] = 'Settings';
$string['setupdefaultplugins'] = 'Setting up default repository plugins';
$string['setmainfile'] = 'Set main file';
$string['siteinstances'] = 'Repositories instances of the site';
$string['size'] = 'Size';
$string['submit'] = 'Submit';
$string['sync'] = 'Sync';
$string['thumbview'] = 'View as icons';
$string['title'] = 'Choose a file...';
$string['type'] = 'Type';
$string['typenotvisible'] = 'Type not visible';
$string['unknownoriginal'] = 'Unknown';
$string['upload'] = 'Upload this file';
$string['uploading'] = 'Uploading...';
$string['uploadsucc'] = 'The file has been uploaded successfully';
$string['undisclosedsource'] = '(Undisclosed)';
$string['undisclosedreference'] = '(Undisclosed)';
$string['uselatestfile'] = 'Use latest file';
$string['usercontextrepositorydisabled'] = 'You cannot edit this repository in user context';
$string['usenonjsfilemanager'] = 'Open file manager in new window';
$string['usenonjsfilepicker'] = 'Open file picker in new window';
$string['unzipped'] = 'Unzipped successfully';
$string['wrongcontext'] = 'You cannot access to this context';
$string['xhtmlerror'] = 'You are probably using XHTML strict header, some YUI Component doesn\'t work in this mode, please turn it off in moodle';
$string['ziped'] = 'Compress folder successfully';
