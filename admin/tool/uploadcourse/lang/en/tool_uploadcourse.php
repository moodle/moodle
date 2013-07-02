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
 * Strings for component 'tool_uploadcourse', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    tool_uploadcourse
 * @subpackage uploadcourse
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allowdeletes'] = 'Allow deletes';
$string['allowrenames'] = 'Allow renames';
$string['allowresets'] = 'Allow resets';
$string['cannotdeletecoursenotexist'] = 'Cannot delete a course that does not exist';
$string['cannotgenerateshortnameupdatemode'] = 'Cannot generate a shortname when updates are allowed';
$string['cannotreadbackupfile'] = 'Cannot read the backup file';
$string['cannotrenamecoursenotexist'] = 'Cannot rename a course that does not exist';
$string['cannotrenameidnumberconflict'] = 'Cannot rename the course, the ID number conflicts with an existing course';
$string['cannotrenameshortnamealreadyinuse'] = 'Cannot rename the course, the shortname is already used';
$string['canonlyrenameinupdatemode'] = 'Can only rename a course when update is allowed';
$string['canonlyresetcourseinupdatemode'] = 'Can only reset a course in update mode';
$string['couldnotresolvecatgorybyid'] = 'Could not resolve category by ID';
$string['couldnotresolvecatgorybyidnumber'] = 'Could not resolve category by ID number';
$string['couldnotresolvecatgorybypath'] = 'Could not resolve category by path';
$string['coursecreated'] = 'Course created';
$string['coursedeleted'] = 'Course deleted';
$string['coursedeletionnotallowed'] = 'Course deletion is not allowed';
$string['coursedoesnotexistandcreatenotallowed'] = 'The course does not exist and creating course is not allowed';
$string['courseexistsanduploadnotallowed'] = 'The course exists and update is not allowed';
$string['courseidnumberincremented'] = 'Course ID number incremented {$a->from} -> {$a->to}';
$string['courseprocess'] = 'Course process';
$string['courserenamed'] = 'Course renamed';
$string['courserenamingnotallowed'] = 'Course renaming is not allowed';
$string['coursereset'] = 'Course reset';
$string['courseresetnotallowed'] = 'Course reset now allowed';
$string['courserestored'] = 'Course restored';
$string['coursestotal'] = 'Courses total: {$a}';
$string['coursescreated'] = 'Courses created: {$a}';
$string['coursesupdated'] = 'Courses updated: {$a}';
$string['coursesdeleted'] = 'Courses deleted: {$a}';
$string['courseserrors'] = 'Courses errors: {$a}';
$string['courseshortnameincremented'] = 'Course shortname incremented {$a->from} -> {$a->to}';
$string['courseshortnamegenerated'] = 'Course shortname generated: {$a}';
$string['coursetemplatename'] = 'Restore from this course after upload';
$string['coursetemplatename_help'] = 'Enter an existing course shortname to use as a template for the creation of all courses.';
$string['coursetorestorefromdoesnotexist'] = 'The course to restore from does not exist';
$string['courseupdated'] = 'Course updated';
$string['csvdelimiter'] = 'CSV delimiter';
$string['csvfileerror'] = 'There is something wrong with the format of the CSV file. Please check the number of headings and columns match, and that the delimiter and file encoding are correct: {$a}';
$string['csvline'] = 'Line';
$string['defaultvalues'] = 'Default course values';
$string['encoding'] = 'Encoding';
$string['errorwhilerestoringcourse'] = 'Error while restoring the course';
$string['errorwhiledeletingcourse'] = 'Error while deleting the course';
$string['generatedshortnameinvalid'] = 'The generated shortname is invalid';
$string['generatedshortnamealreadyinuse'] = 'The generated shortname is already in use';
$string['id'] = 'ID';
$string['importoptions'] = 'Import options';
$string['idnumberalreadyinuse'] = 'ID number already used by a course';
$string['invalidbackupfile'] = 'Invalid backup file';
$string['invalidcourseformat'] = 'Invalid course format';
$string['invalidcsvfile'] = 'Invalid input CSV file';
$string['invalidencoding'] = 'Invalid encoding';
$string['invalidmode'] = 'Invalid mode selected';
$string['invalideupdatemode'] = 'Invalid update mode selected';
$string['invalidroles'] = 'Invalid role names: {$a}';
$string['invalidshortname'] = 'Invalid shortname';
$string['missingmandatoryfields'] = 'Missing value for mandatory fields: {$a}';
$string['missingshortnamenotemplate'] = 'Missing shortname and shortname template not set';
$string['mode'] = 'Upload mode';
$string['preview'] = 'Preview';
$string['reset'] = 'Reset course after upload';
$string['result'] = 'Result';
$string['restoreafterimport'] = 'Restore after import';
$string['rowpreviewnum'] = 'Preview rows';
$string['shortnametemplate'] = 'Template to generate a shortname';
$string['shortnametemplate_help'] = 'The short name of the course is displayed in the navigation. You may use template syntax here (%f = fullname, %i = idnumber), or enter an initial value that is incremented.';
$string['templatefile'] = 'Restore from this file after upload';
$string['templatefile_help'] = 'Select a file to use as a template for the creation of all courses.';
$string['unknownimportmode'] = 'Unknown import mode';
$string['updatemode'] = 'Update mode';
$string['updatemodedoessettonothing'] = 'Update mode does not allow anything to be updated';
$string['uploadcourses'] = 'Upload courses';
$string['uploadcourses_help'] = 'Courses may be uploaded via text file. The format of the file should be as follows:

* Each line of the file contains one record
* Each record is a series of data separated by commas (or other delimiters)
* The first record contains a list of fieldnames defining the format of the rest of the file
* Required fieldnames are shortname, fullname, summary and category';
$string['uploadcoursesresult'] = 'Upload courses results';





$string['deleteerrors'] = 'Delete errors';
$string['errors'] = 'Errors';
$string['invalidinput'] = 'You must specify a valid combination of --action and --mode';
$string['nochanges'] = 'No changes';
$string['pluginname'] = 'Course upload';
$string['renameerrors'] = 'Rename errors';
$string['requiredtemplate'] = 'Required. You may use template syntax here (%l = lastname, %f = firstname, %u = coursename). See help for details and examples.';
$string['uploadpicture_badcoursefield'] = 'The course attribute specified is not valid. Please, try again.';
$string['uploadpicture_cannotmovezip'] = 'Cannot move zip file to temporary directory.';
$string['uploadpicture_cannotprocessdir'] = 'Cannot process unzipped files.';
$string['uploadpicture_cannotsave'] = 'Cannot save picture for course {$a}. Check original picture file.';
$string['uploadpicture_cannotunzip'] = 'Cannot unzip pictures file.';
$string['uploadpicture_invalidfilename'] = 'Picture file {$a} has invalid characters in its name. Skipping.';
$string['uploadpicture_overwrite'] = 'Overwrite existing course pictures?';
$string['uploadpicture_coursefield'] = 'Course attribute to use to match pictures:';
$string['uploadpicture_coursenotfound'] = 'Course with a \'{$a->coursefield}\' value of \'{$a->coursevalue}\' does not exist. Skipping.';
$string['uploadpicture_courseskipped'] = 'Skipping course {$a} (already has a picture).';
$string['uploadpicture_courseupdated'] = 'Picture updated for course {$a}.';
$string['uploadpictures'] = 'Upload course pictures';
$string['uploadpictures_help'] = 'Course pictures can be uploaded as a zip file of image files. The image files should be named chosen-course-attribute.extension, for example course1234.jpg for a course with coursename course1234.';

$string['uploadcoursespreview'] = 'Upload courses preview';
$string['courseuptodate'] = 'Course up-to-date';
$string['courseupdated'] = 'Course updated';
$string['coursedeleted'] = 'Course deleted';
$string['courserenamed'] = 'Course renamed';
$string['coursesrenamed'] = 'Courses renamed';
$string['coursesskipped'] = 'Courses skipped';
$string['coursenotadded'] = 'Course not added - already exists';
$string['coursenotaddederror'] = 'Course not added - error';
$string['coursenotdeletederror'] = 'Course not deleted - error';
$string['coursenotdeletedmissing'] = 'Course not deleted - missing';
$string['coursenotdeletedoff'] = 'Course not deleted - delete off';
$string['coursenotdeletedadmin'] = 'Course not deleted - no admin access';
$string['coursenotupdatederror'] = 'Course not updated - error';
$string['coursenotupdatednotexists'] = 'Course not updated - does not exist';
$string['coursenotupdatedadmin'] = 'Course not updated - no admin';
$string['coursenotrenamedexists'] = 'Course not renamed - target exists';
$string['coursenotrenamedmissing'] = 'Course not renamed - source missing';
$string['coursenotrenamedoff'] = 'Course not renamed - renaming off';
$string['coursenotrenamedadmin'] = 'Course not renamed - no admin';
$string['invalidvalue'] = 'Invalid value for field {$a}';
$string['shortnamecourse'] = 'Shortname';

$string['idnumbernotunique'] = 'idnumber is not unique';
$string['ccbulk'] = 'Select for bulk operations';
$string['ccbulkall'] = 'All courses';
$string['ccbulknew'] = 'New courses';
$string['ccbulkupdated'] = 'Updated courses';
$string['cclegacy1role'] = '(Original Student) typeN=1';
$string['cclegacy2role'] = '(Original Teacher) typeN=2';
$string['cclegacy3role'] = '(Original Non-editing teacher) typeN=3';
$string['ccnoemailduplicates'] = 'Prevent email address duplicates';

$string['ccoptype_addinc'] = 'Add all, append number to shortnames if needed';
$string['ccoptype_addnew'] = 'Add new only, skip existing courses';
$string['ccoptype_addupdate'] = 'Add new and update existing courses';
$string['ccoptype_update'] = 'Update existing courses only';
$string['ccpasswordcron'] = 'Generated in cron';
$string['ccpasswordnew'] = 'New course password';
$string['ccpasswordold'] = 'Existing course password';
$string['ccstandardshortnames'] = 'Standardise shortnames';
$string['ccupdateall'] = 'Override with file and defaults';
$string['ccupdatefromfile'] = 'Override with file';
$string['ccupdatemissing'] = 'Fill in missing from file and defaults';
$string['ccupdatetype'] = 'Existing course details';

$string['ccfullnametemplate'] = 'Fullname template';
$string['ccidnumbertemplate'] = 'Idnumber template';
$string['missingtemplate'] = 'Template not found';
$string['missing'] = 'missing';
$string['incorrectformat'] = 'Invalid format specified';
$string['incorrecttemplatefile'] = 'Template file not found';
$string['invalidenrolmethod'] = 'Invalid enrolment method';
$string['invalidaction'] = 'Invalid action selected';


$string['invalidcategory'] = 'Invalid category';
$string['invalidbackupfile'] = 'Invalid backup file';

