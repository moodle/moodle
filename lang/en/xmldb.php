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
 * Strings for component 'xmldb', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   xmldb
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actual'] = 'Actual';
$string['aftertable'] = 'After table:';
$string['back'] = 'Back';
$string['backtomainview'] = 'Back to main';
$string['binaryincorrectlength'] = 'Incorrect length for binary field';
$string['cannotuseidfield'] = 'Cannot insert the "id" field. It is an autonumeric column';
$string['completelogbelow'] = '(see the complete log of the search below)';
$string['confirmdeletefield'] = 'Are you absolutely sure that you want to delete the field:';
$string['confirmdeleteindex'] = 'Are you absolutely sure that you want to delete the index:';
$string['confirmdeletekey'] = 'Are you absolutely sure that you want to delete the key:';
$string['confirmdeletetable'] = 'Are you absolutely sure that you want to delete the table:';
$string['confirmdeletexmlfile'] = 'Are you absolutely sure that you want to delete the file:';
$string['confirmcheckbigints'] = 'This functionality will search for <a href="http://tracker.moodle.org/browse/MDL-11038">potential wrong integer fields</a> in your Moodle server, generating (but not executing!) automatically the needed SQL statements to have all the integers in your DB properly defined.<br /><br />
Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of wrong integers.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckdefaults'] = 'This functionality will search for inconsistent default values in your Moodle server, generating (but not executing!) the needed SQL statements to have all the default values properly defined.<br /><br />
Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of inconsistent default values.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckforeignkeys'] = 'This functionality will search for potential violations of the foreign keys defined in the install.xml definitions. (Moodle does not currently generate actual foreign key constraints in the database, which is why invalid data may be present.)<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of missing indexes.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckindexes'] = 'This functionality will search for potential missing indexes in your Moodle server, generating (but not executing!) automatically the needed SQL statements to keep everything updated.<br /><br />
Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of missing indexes.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmrevertchanges'] = 'Are you absolutely sure that you want to revert changes performed over:';
$string['create'] = 'Create';
$string['createtable'] = 'Create table:';
$string['defaultincorrect'] = 'Incorrect default';
$string['delete'] = 'Delete';
$string['delete_field'] = 'Delete field';
$string['delete_index'] = 'Delete index';
$string['delete_key'] = 'Delete key';
$string['delete_table'] = 'Delete table';
$string['delete_xml_file'] = 'Delete XML file';
$string['doc'] = 'Doc';
$string['docindex'] = 'Documentation index:';
$string['documentationintro'] = 'This documentation is generated automatically from the XMLDB database definition. It is available only in English.';
$string['down'] = 'Down';
$string['duplicate'] = 'Duplicate';
$string['duplicatefieldname'] = 'Another field with that name exists';
$string['duplicatekeyname'] = 'Another key with that name exists';
$string['edit'] = 'Edit';
$string['edit_field'] = 'Edit field';
$string['edit_field_save'] = 'Save field';
$string['edit_index'] = 'Edit index';
$string['edit_index_save'] = 'Save index';
$string['edit_key'] = 'Edit key';
$string['edit_key_save'] = 'Save key';
$string['edit_table'] = 'Edit table';
$string['edit_table_save'] = 'Save table';
$string['edit_xml_file'] = 'Edit XML file';
$string['enumvaluesincorrect'] = 'Incorrect values for enum field';
$string['expected'] = 'Expected';
$string['extensionrequired'] = 'Sorry - the PHP extension \'{$a}\' is required for this action. Please install the extension if you want to use this feature.';
$string['field'] = 'Field';
$string['fieldnameempty'] = 'Name field empty';
$string['fields'] = 'Fields';
$string['fieldsnotintable'] = 'Field doesn\'t exist in table';
$string['fieldsusedinkey'] = 'This field is used as key.';
$string['filenotwriteable'] = 'File not writeable';
$string['fkviolationdetails'] = 'Foreign key {$a->keyname} on table {$a->tablename} is violated by {$a->numviolations} out of {$a->numrows} rows.';
$string['floatincorrectdecimals'] = 'Incorrect number of decimals for float field';
$string['floatincorrectlength'] = 'Incorrect length for float field';
$string['float2numbernote'] = 'Notice: Although "float" fields are 100% supported by XMLDB, it\'s recommended to migrate to "number" fields instead.';
$string['generate_all_documentation'] = 'All the documentation';
$string['generate_documentation'] = 'Documentation';
$string['gotolastused'] = 'Go to last used file';
$string['change'] = 'Change';
$string['charincorrectlength'] = 'Incorrect length for char field';
$string['checkbigints'] = 'Check bigints';
$string['check_bigints'] = 'Look for incorrect DB integers';
$string['checkdefaults'] = 'Check defaults';
$string['check_defaults'] = 'Look for inconsistent default values';
$string['checkforeignkeys'] = 'Check foreign keys';
$string['check_foreign_keys'] = 'Look for foreign key violations';
$string['checkindexes'] = 'Check indexes';
$string['check_indexes'] = 'Look for missing DB indexes';
$string['incorrectfieldname'] = 'Incorrect name';
$string['index'] = 'Index';
$string['indexes'] = 'Indexes';
$string['integerincorrectlength'] = 'Incorrect length for integer field';
$string['key'] = 'Key';
$string['keys'] = 'Keys';
$string['listreservedwords'] = 'List of Reserved Words<br />(used to keep <a href="http://docs.moodle.org/en/XMLDB_reserved_words" target="_blank">XMLDB_reserved_words</a> updated)';
$string['load'] = 'Load';
$string['main_view'] = 'Main view';
$string['masterprimaryuniqueordernomatch'] = 'The fields in your foreign key must be listed in the same order as they are listed in the UNIQUE KEY on the referenced table.';
$string['missing'] = 'Missing';
$string['missingindexes'] = 'Missing indexes found';
$string['mustselectonefield'] = 'You must select one field to see field related actions!';
$string['mustselectoneindex'] = 'You must select one index to see index related actions!';
$string['mustselectonekey'] = 'You must select one key to see key related actions!';
$string['mysqlextracheckbigints'] = 'Under MySQL it also looks for incorrectly signed bigints, generating the required SQL to be executed in order to fix all them.';
$string['newfield'] = 'New field';
$string['newindex'] = 'New index';
$string['newkey'] = 'New key';
$string['newtable'] = 'New table';
$string['newtablefrommysql'] = 'New table from MySQL';
$string['new_table_from_mysql'] = 'New table from MySQL';
$string['nomasterprimaryuniquefound'] = 'The column(s) that you foreign key references must be included in a primary or unique KEY in the referenced table. Note, the column being in a UNIQUE INDEX is not good enough.';
$string['nomissingindexesfound'] = 'No missing indexes have been found, your DB doesn\'t need further actions.';
$string['noviolatedforeignkeysfound'] = 'No violated foreign keys found';
$string['nowrongdefaultsfound'] = 'No inconsistent default values have been found, your DB does not need further actions.';
$string['nowrongintsfound'] = 'No wrong integers have been found, your DB doesn\'t need further actions.';
$string['numberincorrectdecimals'] = 'Incorrect number of decimals for number field';
$string['numberincorrectlength'] = 'Incorrect length for number field';
$string['pendingchanges'] = 'Note: You have performed changes to this file. They can be saved at any moment.';
$string['pendingchangescannotbesaved'] = 'There are changes in this file but they cannot be saved! Please verify that both the directory and the "install.xml" within it have write permissions for the web server.';
$string['pendingchangescannotbesavedreload'] = 'There are changes in this file but they cannot be saved! Please verify that both the directory and the "install.xml" within it have write permissions for the web server. Then reload this page and you should be able to save those changes.';
$string['reserved'] = 'Reserved';
$string['reservedwords'] = 'Reserved words';
$string['revert'] = 'Revert';
$string['revert_changes'] = 'Revert changes';
$string['save'] = 'Save';
$string['searchresults'] = 'Search results';
$string['selectaction'] = 'Select action:';
$string['selectdb'] = 'Select database:';
$string['selectfieldkeyindex'] = 'Select field/key/index:';
$string['selectonecommand'] = 'Please select one action from the list to view PHP code';
$string['selectonefieldkeyindex'] = 'Please select one field/key/index from the list to view the PHP code';
$string['selecttable'] = 'Select table:';
$string['table'] = 'Table';
$string['tables'] = 'Tables';
$string['textincorrectlength'] = 'Incorrect length for text field';
$string['unload'] = 'Unload';
$string['up'] = 'Up';
$string['view'] = 'View';
$string['viewedited'] = 'View edited';
$string['vieworiginal'] = 'View original';
$string['viewphpcode'] = 'View PHP code';
$string['view_reserved_words'] = 'View reserved words';
$string['viewsqlcode'] = 'View SQL code';
$string['view_structure_php'] = 'View structure PHP';
$string['view_structure_sql'] = 'View structure SQL';
$string['view_table_php'] = 'View table PHP';
$string['view_table_sql'] = 'View table SQL';
$string['viewxml'] = 'XML';
$string['violatedforeignkeys'] = 'Violated foreign keys';
$string['violatedforeignkeysfound'] = 'Violated foreign keys found';
$string['violations'] = 'Violations';
$string['wrong'] = 'Wrong';
$string['wrongdefaults'] = 'Wrong defaults found';
$string['wrongints'] = 'Wrong integers found';
$string['wronglengthforenum'] = 'Incorrect length for enum field';
$string['wrongreservedwords'] = 'Currently used reserved words<br />(note that table names aren\'t important if using $CFG->prefix)';
$string['yesmissingindexesfound'] = 'Some missing indexes have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more missing indexes are found.';
$string['yeswrongdefaultsfound'] = 'Some inconsistent defaults have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to fix them all (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more inconsistent defaults are found.';
$string['yeswrongintsfound'] = 'Some wrong integers have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more wrong integers are found.';
