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
 * This file contains the strings used by backup
 *
 * @package   moodlecore
 * @copyright 2010 Eloy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['autoactivedisabled'] = 'Disabled';
$string['autoactiveenabled'] = 'Enabled';
$string['autoactivemanual'] = 'Manual';
$string['autoactivedescription'] = 'Choose whether or not to do automated backups. If manual is selected automated backups will be possible only by through the automated backups CLI script. This can be done either manually on the command line or through cron.';
$string['automatedbackupschedule'] = 'Schedule';
$string['automatedbackupschedulehelp'] = 'Choose which days of the week to perform automated backups.';
$string['automatedbackupsinactive'] = 'Automated backups haven\'t been enabled by the site admin';
$string['automatedbackupstatus'] = 'Automated backup status';
$string['automatedsetup'] = 'Automated backup setup';
$string['automatedsettings'] = 'Automated backup settings';
$string['automatedstorage'] = 'Automated backup storage';
$string['automatedstoragehelp'] = 'Choose the location where you want backups to be stored when they are automatically created.';
$string['backupactivity'] = 'Backup activity: {$a}';
$string['backupcourse'] = 'Backup course: {$a}';
$string['backupcoursedetails'] = 'Course details';
$string['backupcoursesection'] = 'Section: {$a}';
$string['backupcoursesections'] = 'Course sections';
$string['backupdate'] = 'Date taken';
$string['backupdetails'] = 'Backup details';
$string['backupdetailsnonstandardinfo'] = 'The selected file is not a standard Moodle backup file. The restore process will try to convert the backup file into the standard format and then restore it.';
$string['backupformat'] = 'Format';
$string['backupformatmoodle1'] = 'Moodle 1';
$string['backupformatmoodle2'] = 'Moodle 2';
$string['backupformatimscc'] = 'IMS Common Cartridge';
$string['backupformatunknown'] = 'Unknown format';
$string['backupmode'] = 'Mode';
$string['backupmode10'] = 'General';
$string['backupmode20'] = 'Import';
$string['backupmode30'] = 'Hub';
$string['backupmode40'] = 'Same site';
$string['backupmode50'] = 'Automated';
$string['backupmode60'] = 'Converted';
$string['backupsection'] = 'Backup course section: {$a}';
$string['backupsettings'] = 'Backup settings';
$string['backupsitedetails'] = 'Site details';
$string['backupstage1action'] = 'Next';
$string['backupstage2action'] = 'Next';
$string['backupstage4action'] = 'Perform backup';
$string['backupstage8action'] = 'Continue';
$string['backupstage16action'] = 'Continue';
$string['backuptype'] = 'Type';
$string['backuptypeactivity'] = 'Activity';
$string['backuptypecourse'] = 'Course';
$string['backupversion'] = 'Backup version';
$string['cannotfindassignablerole'] = 'The {$a} role in the backup file cannot be mapped to any of the roles that you are allowed to assign.';
$string['choosefilefromcoursebackup'] = 'Course backup area';
$string['choosefilefromcoursebackup_help'] = 'When backup courses using default settings, backup files will be stored here';
$string['choosefilefromuserbackup'] = 'User private backup area';
$string['choosefilefromuserbackup_help'] = 'When backup courses with "Anonymize user information" option ticked, backup files will be stored here';
$string['choosefilefromactivitybackup'] = 'Activity backup area';
$string['choosefilefromactivitybackup_help'] = 'When backup activities using default settings, backup files will be stored here';
$string['choosefilefromautomatedbackup'] = 'Automated backups';
$string['choosefilefromautomatedbackup_help'] = 'Contains automatically generated backups.';
$string['configgeneralactivities'] = 'Sets the default for including activities in a backup.';
$string['configgeneralanonymize'] = 'If enabled all information pertaining to users will be anonymised by default.';
$string['configgeneralblocks'] = 'Sets the default for including blocks in a backup.';
$string['configgeneralcomments'] = 'Sets the default for including comments in a backup.';
$string['configgeneralfilters'] = 'Sets the default for including filters in a backup.';
$string['configgeneralhistories'] = 'Sets the default for including user history within a backup.';
$string['configgenerallogs'] = 'If enabled logs will be included in backups by default.';
$string['configgeneralroleassignments'] = 'If enabled by default roles assignments will also be backed up.';
$string['configgeneraluserscompletion'] = 'If enabled user completion information will be included in backups by default.';
$string['configgeneralusers'] = 'Sets the default for whether to include users in backups.';
$string['confirmcancel'] = 'Cancel backup';
$string['confirmcancelquestion'] = 'Are you sure you wish to cancel?
Any information you have entered will be lost.';
$string['confirmcancelyes'] = 'Cancel';
$string['confirmcancelno'] = 'Stay';
$string['confirmnewcoursecontinue'] = 'New course warning';
$string['confirmnewcoursecontinuequestion'] = 'A temporary (hidden) course will be created by the course restoration process. To abort restoration click cancel. Do not close the browser while restoring.';
$string['coursecategory'] = 'Category the course will be restored into';
$string['courseid'] = 'Original ID';
$string['coursesettings'] = 'Course settings';
$string['coursetitle'] = 'Title';
$string['currentstage1'] = 'Initial settings';
$string['currentstage2'] = 'Schema settings';
$string['currentstage4'] = 'Confirmation and review';
$string['currentstage8'] = 'Perform backup';
$string['currentstage16'] = 'Complete';
$string['dependenciesenforced'] = 'Your settings have been altered due to unmet dependencies';
$string['enterasearch'] = 'Enter a search';
$string['error_block_for_module_not_found'] = 'Orphan block instance (id: {$a->bid}) for course module (id: {$a->mid}) found. This block will not be backed up';
$string['error_course_module_not_found'] = 'Orphan course module (id: {$a}) found. This module will not be backed up.';
$string['errorfilenamerequired'] = 'You must enter a valid filename for this backup';
$string['errorfilenamemustbezip'] = 'The filename you enter must be a ZIP file and have the .mbz extension';
$string['errorminbackup20version'] = 'This backup file has been created with one development version of Moodle backup ({$a->backup}). Minimum required is {$a->min}. Cannot be restored.';
$string['errorrestorefrontpage'] = 'Restoring over front page is not allowed.';
$string['errorinvalidformat'] = 'Unknown backup format';
$string['errorinvalidformatinfo'] = 'The selected file is not a valid Moodle backup file and can\'t be restored.';
$string['executionsuccess'] = 'The backup file was successfully created.';
$string['filename'] = 'Filename';
$string['generalactivities'] = 'Include activities';
$string['generalanonymize'] = 'Anonymise information';
$string['generalbackdefaults'] = 'General backup defaults';
$string['generalblocks'] = 'Include blocks';
$string['generalcomments'] = 'Include comments';
$string['generalfilters'] = 'Include filters';
$string['generalhistories'] = 'Include histories';
$string['generalgradehistories'] = 'Include histories';
$string['generallogs'] = 'Include logs';
$string['generalroleassignments'] = 'Include role assignments';
$string['generaluserscompletion'] = 'Include user completion information';
$string['generalusers'] = 'Include users';
$string['importfile'] = 'Import a backup file';
$string['importbackupstage1action'] = 'Next';
$string['importbackupstage2action'] = 'Next';
$string['importbackupstage4action'] = 'Perform import';
$string['importbackupstage8action'] = 'Continue';
$string['importbackupstage16action'] = 'Continue';
$string['importcurrentstage0'] = 'Course selection';
$string['importcurrentstage1'] = 'Initial settings';
$string['importcurrentstage2'] = 'Schema settings';
$string['importcurrentstage4'] = 'Confirmation and review';
$string['importcurrentstage8'] = 'Perform import';
$string['importcurrentstage16'] = 'Complete';
$string['importsuccess'] = 'Import complete. Click continue to return to the course.';
$string['includeactivities'] = 'Include:';
$string['includeditems'] = 'Included items:';
$string['includesection'] = 'Section {$a}';
$string['includeuserinfo'] = 'User data';
$string['locked'] = 'Locked';
$string['lockedbypermission'] = 'You don\'t have sufficient permissions to change this setting';
$string['lockedbyconfig'] = 'This setting has been locked by the default backup settings';
$string['lockedbyhierarchy'] = 'Locked by dependencies';
$string['managefiles'] = 'Manage backup files';
$string['moodleversion'] = 'Moodle version';
$string['moreresults'] = 'There are too many results, enter a more specific search.';
$string['nomatchingcourses'] = 'There are no courses to display';
$string['norestoreoptions'] = 'There are no categories or existing courses you can restore to.';
$string['originalwwwroot'] = 'URL of backup';
$string['previousstage'] = 'Previous';
$string['qcategory2coursefallback'] = 'The questions category "{$a->name}", originally at system/course category context in backup file, will be created at course context by restore';
$string['qcategorycannotberestored'] = 'The questions category "{$a->name}" cannot be created by restore';
$string['question2coursefallback'] = 'The questions category "{$a->name}", originally at system/course category context in backup file, will be created at course context by restore';
$string['questionegorycannotberestored'] = 'The questions "{$a->name}" cannot be created by restore';
$string['restoreactivity'] = 'Restore activity';
$string['restorecourse'] = 'Restore course';
$string['restorecoursesettings'] = 'Course settings';
$string['restoreexecutionsuccess'] = 'The course was restored successfully, clicking the continue button below will take you to view the course you restored.';
$string['restorenewcoursefullname'] = 'New course name';
$string['restorenewcourseshortname'] = 'New course short name';
$string['restorenewcoursestartdate'] = 'New start date';
$string['restorerootsettings'] = 'Restore settings';
$string['restoresection'] = 'Restore section';
$string['restorestage1'] = 'Confirm';
$string['restorestage1action'] = 'Next';
$string['restorestage2'] = 'Destination';
$string['restorestage2action'] = 'Next';
$string['restorestage4'] = 'Settings';
$string['restorestage4action'] = 'Next';
$string['restorestage8'] = 'Schema';
$string['restorestage8action'] = 'Next';
$string['restorestage16'] = 'Review';
$string['restorestage16action'] = 'Perform restore';
$string['restorestage32'] = 'Process';
$string['restorestage32action'] = 'Continue';
$string['restorestage64'] = 'Complete';
$string['restorestage64action'] = 'Continue';
$string['restoretarget'] = 'Restore target';
$string['restoretocourse'] = 'Restore to course: ';
$string['restoretocurrentcourse'] = 'Restore into this course';
$string['restoretocurrentcourseadding'] = 'Merge the backup course into this course';
$string['restoretocurrentcoursedeleting'] = 'Delete the contents of this course and then restore';
$string['restoretoexistingcourse'] = 'Restore into an existing course';
$string['restoretoexistingcourseadding'] = 'Merge the backup course into the existing course';
$string['restoretoexistingcoursedeleting'] = 'Delete the contents of the existing course and then restore';
$string['restoretonewcourse'] = 'Restore as a new course';
$string['restoringcourse'] = 'Course restoration in progress';
$string['restoringcourseshortname'] = 'restoring';
$string['restorerolemappings'] = 'Restore role mappings';
$string['rootsettings'] = 'Backup settings';
$string['rootsettingusers'] = 'Include enrolled users';
$string['rootsettinganonymize'] = 'Anonymize user information';
$string['rootsettingroleassignments'] = 'Include user role assignments';
$string['rootsettingactivities'] = 'Include activities';
$string['rootsettingblocks'] = 'Include blocks';
$string['rootsettingfilters'] = 'Include filters';
$string['rootsettingcomments'] = 'Include comments';
$string['rootsettinguserscompletion'] = 'Include user completion details';
$string['rootsettinglogs'] = 'Include course logs';
$string['rootsettinggradehistories'] = 'Include grade history';
$string['storagecourseonly'] = 'Course backup filearea';
$string['storagecourseandexternal'] = 'Course backup filearea and the specified directory';
$string['storageexternalonly'] = 'Specified directory for automated backups';
$string['sectionincanduser'] = 'Included in backup along with user information';
$string['sectioninc'] = 'Included in backup (no user information)';
$string['sectionactivities'] = 'Activities';
$string['selectacategory'] = 'Select a category';
$string['selectacourse'] = 'Select a course';
$string['setting_overwriteconf'] = 'Overwrite course configuration';
$string['setting_course_fullname'] = 'Course name';
$string['setting_course_shortname'] = 'Course short name';
$string['setting_course_startdate'] = 'Course startdate';
$string['totalcategorysearchresults'] = 'Total categories: {$a}';
$string['totalcoursesearchresults'] = 'Total courses: {$a}';
