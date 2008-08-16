<?php
/*
 * debug information for developer only
 */
$string['authpluginnotfound'] = 'Authentication plugin $a not found.';
$string['blocknotexist'] = '$a block doesn\'t exist';
$string['cannotbenull'] = '$a cannot be null!';
$string['cannotcreateadminuser'] = 'SERIOUS ERROR: Could not create admin user record !!!';
$string['cannotdowngrade'] = 'Cannot downgrade from $a->oldversion to $a->newversion.';
$string['cannotinitpage'] = 'Cannot fully initialize page: invalid $a->name id $a->id';
$string['cannotupgradecapabilities'] = 'Had trouble upgrading the core capabilities for the Roles System';
$string['cannotupdateversion'] = 'Upgrade failed!  (Could not update version in config table)';
$string['cannotupdaterelease'] = 'ERROR: Could not update release version in database!!';
$string['cannotsetupsite'] = 'Serious Error! Could not set up the site!';
$string['cannotsetuptable'] = '$a tables could NOT be set up successfully!';
$string['cannotfindadmin'] = 'Could not find an admin user!';
$string['cannotupgradedbcustom'] = 'Upgrade of local database customisations failed! (Could not update version in config table)';
$string['configmoodle'] = 'Moodle has not been configured yet. You need to edit config.php first.';
$string['dbnotinsert'] = 'Database error - Cannot insert ($a)';
$string['dbnotupdate'] = 'Database error - Cannot update ($a)';
$string['dbnotsupport'] = 'Error: Your database ($a) is not yet fully supported by Moodle or install.xml is not present. See the lib/db directory.';
$string['dbnotsetup'] = 'Error: Main databases NOT set up successfully';
$string['doesnotworkwitholdversion'] = 'This script does not work with this old version of Moodle';
$string['erroroccur'] = 'An error has occurred during this process';
$string['fixsetting'] = 'Please fix your settings in config.php: <p>You have:</p> <p>\$CFG->dirroot = \"$a->current\";</p> <p>but it should be:</p> <p>\$CFG->dirroot = \"$a->found\"</p>';
$string['invalideventdata'] = 'Incorrect eventadata submitted: $a';
$string['invalidarraysize'] = 'Incorrect size of arrays in params of $a';
$string['missingconfigversion'] = 'Config table does not contain version, can not continue, sorry.';
$string['mustbeoveride'] = 'Abstract $a method must be overriden.';
$string['morethanonerecordinfetch'] = 'Found more than one record in fetch() !';
$string['noadminrole'] = 'No admin role could be found';
$string['noactivityname'] = 'Page object derived from page_generic_activity but did not define $this->activityname';
$string['noblocks'] = 'No blocks installed!';
$string['noblockbase'] = 'Class block_base is not defined or file not found for /blocks/moodleblock.class.php';
$string['nocaps'] = 'Error: no capabilitites defined!';
$string['nocate'] = 'No categories!';
$string['notables'] = 'No Tables!';
$string['nopageclass'] = 'Imported $a but found no page classes';
$string['noreports'] = 'No reports accessible';
$string['nomodules'] = 'No modules found!!';
$string['modulenotexist'] = '$a module doesn\'t exist';
$string['phpvaroff'] = 'The PHP server variable \'$a->name\' should be Off - $a->link';
$string['phpvaron'] = 'The PHP server variable \'$a->name\' is not turned On - $a->link';
$string['sessionmissing'] = '$a object missing from session';
$string['siteisnotdefined'] = 'Site is not defined!';
$string['sqlrelyonobsoletetable'] = 'This SQL relies on obsolete table(s): $a!  Your code must be fixed by a developer.';
$string['upgradefail'] = 'Upgrade failed! see: $a';
$string['withoutversion'] = 'Main version.php file is missing, not readable or broken.';
$string['xmlizeunavailable'] = 'xmlize functions are not available';

?>
