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
 * Strings for custom file types.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addfiletypes'] = 'Add a new file type';
$string['corestring'] = 'Alternative language string';
$string['corestring_help'] = 'This setting can be used to select a different language string from the core mimetypes.php language file. Generally it should be left blank. For custom types, use the description field.';
$string['defaulticon'] = 'Default icon for MIME type';
$string['defaulticon_help'] = 'If there are multiple file extensions with the same MIME type, select this option for one of the extensions so that its icon will be used when determining an icon from the MIME type.';
$string['delete_confirmation'] = 'Are you absolutely sure you want to remove <strong>.{$a}</strong>?';
$string['deletea'] = 'Delete {$a}';
$string['deletefiletypes'] = 'Delete a file type';
$string['description'] = 'Custom description';
$string['description_help'] = 'Simple file type description, e.g. \'Kindle ebook\'. If your site supports multiple languages and uses the multi-language filter, you can enter multi-language tags in this field to supply a description in different languages.';
$string['descriptiontype'] = 'Description type';
$string['descriptiontype_help'] = 'There are three possible ways to specify a description.

* Default behaviour uses the MIME type. If there is a language string in mimetypes.php corresponding to that MIME type, it will be used; otherwise the MIME type itself will be displayed to users.
* You can specify a custom description on this form.
* You can specify the name of a languge string in mimetypes.php to use instead of the MIME type.';
$string['descriptiontype_default'] = 'Default (MIME type or corresponding language string if available)';
$string['descriptiontype_custom'] = 'Custom description specified in this form';
$string['descriptiontype_lang'] = 'Alternative language string (from mimetypes.php)';
$string['displaydescription'] = 'Description';
$string['editfiletypes'] = 'Edit an existing file type';
$string['emptylist'] = 'There are no file types defined.';
$string['error_addentry'] = 'The file type extension, description,  MIME type, and icon must not contain line feed and semicolon characters.';
$string['error_defaulticon'] = 'Another file extension with the same MIME type is already marked as the default icon.';
$string['error_extension'] = 'The file type extension <strong>{$a}</strong> already exists or is invalid. File extensions must be unique and must not contain special characters.';
$string['error_notfound'] = 'The file type with extension {$a} cannot be found.';
$string['extension'] = 'Extension';
$string['extension_help'] = 'File name extension without the dot, e.g. \'mobi\'';
$string['groups'] = 'Type groups';
$string['groups_help'] = 'Optional list of file type groups that this type belongs to. These are generic categories such as \'document\' and \'image\'.';
$string['icon'] = 'File icon';
$string['icon_help'] = 'Icon filename.

The list of icons is taken from the /pix/f directory inside your Moodle installation. You can add custom icons to this folder if required.';
$string['mimetype'] = 'MIME type';
$string['mimetype_help'] = 'MIME type associated with this file type, e.g. \'application/x-mobipocket-ebook\'';
$string['pluginname'] = 'File types';
$string['revert'] = 'Restore {$a} to Moodle defaults';
$string['revert_confirmation'] = 'Are you sure you want to restore <strong>.{$a}</strong> to Moodle defaults, discarding your changes?';
$string['revertfiletype'] = 'Restore a file type';
$string['source'] = 'Type';
$string['source_custom'] = 'Custom';
$string['source_deleted'] = 'Deleted';
$string['source_modified'] = 'Modified';
$string['source_standard'] = 'Standard';
$string['privacy:metadata'] = 'The File types plugin does not store any personal data.';
