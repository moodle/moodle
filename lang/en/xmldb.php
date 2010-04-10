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
$string['aftertable'] = 'After Table:';
$string['back'] = 'Back';
$string['backtomainview'] = 'Back To Main';
$string['binaryincorrectlength'] = 'Incorrect length for binary field';
$string['cannotuseidfield'] = 'Cannot insert the "id" field. It is an autonumeric column';
$string['completelogbelow'] = '(see the complete log of the search below)';
$string['confirmdeletefield'] = 'Are you absolutely sure that you want to delete the field:';
$string['confirmdeleteindex'] = 'Are you absolutely sure that you want to delete the index:';
$string['confirmdeletekey'] = 'Are you absolutely sure that you want to delete the key:';
$string['confirmdeletesentence'] = 'Are you absolutely sure that you want to delete the sentence';
$string['confirmdeletestatement'] = 'Are you absolutely sure that you want to delete the statement and all its sentences:';
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
$string['confirmcheckforeignkeys'] = 'This functionality will search for potential violations of the foreign keys defined in the install.xml definitions. (Moodle does not currently generate acutal foreign key constraints in the database, which is why invalid data may be present.)<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of missing indexes.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckindexes'] = 'This functionality will search for potential missing indexes in your Moodle server, generating (but not executing!) automatically the needed SQL statements to keep everything updated.<br /><br />
Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of missing indexes.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmrevertchanges'] = 'Are you absolutely sure that you want to revert changes perfomed over:';
$string['create'] = 'Create';
$string['createtable'] = 'Create Table:';
$string['defaultincorrect'] = 'Incorrect default';
$string['delete'] = 'Delete';
$string['delete_field'] = 'Delete Field';
$string['delete_index'] = 'Delete Index';
$string['delete_key'] = 'Delete Key';
$string['delete_sentence'] = 'Delete Sentence';
$string['delete_statement'] = 'Delete Statement';
$string['delete_table'] = 'Delete Table';
$string['delete_xml_file'] = 'Delete XML File';
$string['doc'] = 'Doc';
$string['docindex'] = 'Documentation Index:';
$string['documentationintro'] = 'This documentation is generated automatically from the XMLDB database definition. It is available only in English.';
$string['down'] = 'Down';
$string['duplicate'] = 'Duplicate';
$string['duplicatefieldname'] = 'Another field with that name exists';
$string['duplicatekeyname'] = 'Another key with that name exists';
$string['edit'] = 'Edit';
$string['edit_field'] = 'Edit Field';
$string['edit_field_save'] = 'Save Field';
$string['edit_index'] = 'Edit Index';
$string['edit_index_save'] = 'Save Index';
$string['edit_key'] = 'Edit Key';
$string['edit_key_save'] = 'Save Key';
$string['edit_sentence'] = 'Edit Sentence';
$string['edit_sentence_save'] = 'Save Sentence';
$string['edit_statement'] = 'Edit Statement';
$string['edit_table'] = 'Edit Table';
$string['edit_table_save'] = 'Save Table';
$string['edit_xml_file'] = 'Edit XML File';
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
$string['generate_all_documentation'] = 'All the Documentation';
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
$string['main_view'] = 'Main View';
$string['masterprimaryuniqueordernomatch'] = 'The fields in your foreign key must be listed in the same order as they are lised in the UNIQUE KEY on the referenced table.';
$string['missing'] = 'Missing';
$string['missingfieldsinsentence'] = 'Missing fields in sentence';
$string['missingindexes'] = 'Missing Indexes Found';
$string['missingvaluesinsentence'] = 'Missing values in sentence';
$string['mustselectonefield'] = 'You must select one field to see field related actions!';
$string['mustselectoneindex'] = 'You must select one index to see index related actions!';
$string['mustselectonekey'] = 'You must select one key to see key related actions!';
$string['mysqlextracheckbigints'] = 'Under MySQL it also looks for incorrectly signed bigints, generating the required SQL to be executed in order to fix all them.';
$string['newfield'] = 'New Field';
$string['newindex'] = 'New Index';
$string['newkey'] = 'New Key';
$string['newsentence'] = 'New Sentence';
$string['newstatement'] = 'New Statement';
$string['new_statement'] = 'New Statement';
$string['newtable'] = 'New Table';
$string['newtablefrommysql'] = 'New Table From MySQL';
$string['new_table_from_mysql'] = 'New Table From MySQL';
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
$string['revert_changes'] = 'Revert Changes';
$string['save'] = 'Save';
$string['searchresults'] = 'Search Results';
$string['selectaction'] = 'Select Action:';
$string['selectdb'] = 'Select Database:';
$string['selectfieldkeyindex'] = 'Select Field/Key/Index:';
$string['selectonecommand'] = 'Please, select one Action from the list to view PHP code';
$string['selectonefieldkeyindex'] = 'Please, select one Field/Key/Index from the list to view the PHP code';
$string['selecttable'] = 'Select Table:';
$string['sentences'] = 'Sentences';
$string['statements'] = 'Statements';
$string['statementtable'] = 'Statement Table:';
$string['statementtype'] = 'Statement Type:';
$string['table'] = 'Table';
$string['tables'] = 'Tables';
$string['test'] = 'Test';
$string['textincorrectlength'] = 'Incorrect length for text field';
$string['unload'] = 'Unload';
$string['up'] = 'Up';
$string['view'] = 'View';
$string['viewedited'] = 'View Edited';
$string['vieworiginal'] = 'View Original';
$string['viewphpcode'] = 'View PHP Code';
$string['view_reserved_words'] = 'View Reserved Words';
$string['viewsqlcode'] = 'View SQL Code';
$string['view_structure_php'] = 'View Structure PHP';
$string['view_structure_sql'] = 'View Structure SQL';
$string['view_table_php'] = 'View Table PHP';
$string['view_table_sql'] = 'View Table SQL';
$string['viewxml'] = 'XML';
$string['violatedforeignkeys'] = 'Violated foreign keys';
$string['violatedforeignkeysfound'] = 'Violated foreign keys found';
$string['violations'] = 'Violations';
$string['wrong'] = 'Wrong';
$string['wrongdefaults'] = 'Wrong Defaults Found';
$string['wrongints'] = 'Wrong Integers Found';
$string['wronglengthforenum'] = 'Incorrect length for enum field';
$string['wrongnumberoffieldsorvalues'] = 'Incorrect number of fields or values in sentence';
$string['wrongreservedwords'] = 'Currently Used Reserved Words<br />(note that table names aren\'t important if using $CFG->prefix)';
$string['yesmissingindexesfound'] = 'Some missing indexes have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more missing indexes are found.';
$string['yeswrongdefaultsfound'] = 'Some inconsistent defaults have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to fix them all (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more iconsistent defaults are found.';
$string['yeswrongintsfound'] = 'Some wrong integers have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more wrong integers are found.';
