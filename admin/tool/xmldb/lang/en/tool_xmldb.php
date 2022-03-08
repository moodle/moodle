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
 * Strings for component 'tool_xmldb', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    tool_xmldb
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actual'] = 'Actual';
$string['addpersistent'] = 'Add mandatory persistent fields';
$string['aftertable'] = 'After table:';
$string['back'] = 'Back';
$string['backtomainview'] = 'Back to main';
$string['cannotuseidfield'] = 'Cannot insert the "id" field. It is an autonumeric column';
$string['completelogbelow'] = '(see the complete log of the search below)';
$string['confirmdeletefield'] = 'Are you absolutely sure that you want to delete the field:';
$string['confirmdeleteindex'] = 'Are you absolutely sure that you want to delete the index:';
$string['confirmdeletekey'] = 'Are you absolutely sure that you want to delete the key:';
$string['confirmdeletetable'] = 'Are you absolutely sure that you want to delete the table:';
$string['confirmdeletexmlfile'] = 'Are you absolutely sure that you want to delete the file:';
$string['confirmcheckbigints'] = 'This functionality will search for <a href="https://tracker.moodle.org/browse/MDL-11038">potential wrong integer fields</a> in your Moodle server, generating (but not executing!) automatically the needed SQL statements to have all the integers in your DB properly defined.

Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of wrong integers.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckdefaults'] = 'This functionality will search for inconsistent default values in your Moodle server, generating (but not executing!) the needed SQL statements to have all the default values properly defined.

Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of inconsistent default values.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckforeignkeys'] = 'This functionality will search for potential violations of the foreign keys defined in the install.xml definitions. (Moodle does not currently generate actual foreign key constraints in the database, which is why invalid data may be present.)

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search for potential violations of the foreign keys.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckindexes'] = 'This functionality will search for potential missing indexes in your Moodle server, generating (but not executing!) automatically the needed SQL statements to keep everything updated.

Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of missing indexes.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckoraclesemantics'] = 'This functionality will search for <a href="https://tracker.moodle.org/browse/MDL-29322">Oracle varchar2 columns using BYTE semantics</a> in your Moodle server, generating (but not executing!) automatically the needed SQL statements to have all the columns converted to use CHAR semantics instead (better for cross-db compatibility and increased contents max. length).

Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of BYTE semantics.

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
$string['duplicatefieldsused'] = 'Duplicate fields used';
$string['duplicatekeyname'] = 'Another key with that name exists';
$string['duplicatetablename'] = 'Another table with that name exists';
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
$string['extraindexesfound'] = 'Extra indexes found';
$string['field'] = 'Field';
$string['fieldnameempty'] = 'Name field empty';
$string['fields'] = 'Fields';
$string['fieldsnotintable'] = 'Field doesn\'t exist in table';
$string['fieldsusedinindex'] = 'This field is used as index';
$string['fieldsusedinkey'] = 'This field is used as key.';
$string['filemodifiedoutfromeditor'] = 'Warning: File locally modified while using the XMLDB Editor. Saving will overwrite local changes.';
$string['filenotwriteable'] = 'File not writeable';
$string['fkunknownfield'] = 'Foreign key {$a->keyname} on table {$a->tablename} points to a non-existent field {$a->reffield} in referenced table {$a->reftable}.';
$string['fkunknowntable'] = 'Foreign key {$a->keyname} on table {$a->tablename} points to a non-existent table {$a->reftable}.';
$string['fkviolationdetails'] = 'Foreign key {$a->keyname} on table {$a->tablename} is violated by {$a->numviolations} out of {$a->numrows} rows.';
$string['floatincorrectdecimals'] = 'Incorrect number of decimals for float field';
$string['floatincorrectlength'] = 'Incorrect length for float field';
$string['float2numbernote'] = 'Notice: Although "float" fields are 100% supported by XMLDB, it\'s recommended to migrate to "number" fields instead.';
$string['generate_all_documentation'] = 'All the documentation';
$string['generate_documentation'] = 'Documentation';
$string['gotolastused'] = 'Go to last used file';
$string['change'] = 'Change';
$string['charincorrectlength'] = 'Incorrect length for char field';
$string['checkbigints'] = 'Check integers';
$string['check_bigints'] = 'Look for incorrect DB integers';
$string['checkdefaults'] = 'Check defaults';
$string['check_defaults'] = 'Look for inconsistent default values';
$string['checkforeignkeys'] = 'Check foreign keys';
$string['check_foreign_keys'] = 'Look for foreign key violations';
$string['checkindexes'] = 'Check indexes';
$string['check_indexes'] = 'Look for missing DB indexes';
$string['checkoraclesemantics'] = 'Check semantics';
$string['check_oracle_semantics'] = 'Look for incorrect length semantics';
$string['duplicateindexname'] = 'Duplicate index name';
$string['incorrectfieldname'] = 'Incorrect name';
$string['index'] = 'Index';
$string['indexes'] = 'Indexes';
$string['indexnameempty'] = 'Index name is empty';
$string['integerincorrectlength'] = 'Incorrect length for integer field';
$string['incorrectindexname'] = 'Incorrect index name';
$string['incorrectkeyname'] = 'Incorrect key name';
$string['incorrecttablename'] = 'Incorrect table name';
$string['key'] = 'Key';
$string['keynameempty'] = 'The key name cannot be empty';
$string['keys'] = 'Keys';
$string['listreservedwords'] = 'List of reserved words<br />(used to keep <a href="https://docs.moodle.org/en/XMLDB_reserved_words" target="_blank">XMLDB reserved words</a> updated)';
$string['load'] = 'Load';
$string['main_view'] = 'Main view';
$string['masterprimaryuniqueordernomatch'] = 'The fields in your foreign key must be listed in the same order as they are listed in the UNIQUE KEY on the referenced table.';
$string['missing'] = 'Missing';
$string['missingindexes'] = 'Missing indexes found';
$string['mustselectonefield'] = 'You must select one field to see field related actions!';
$string['mustselectoneindex'] = 'You must select one index to see index related actions!';
$string['mustselectonekey'] = 'You must select one key to see key related actions!';
$string['newfield'] = 'New field';
$string['newindex'] = 'New index';
$string['newkey'] = 'New key';
$string['newtable'] = 'New table';
$string['newtablefrommysql'] = 'New table from MySQL';
$string['new_table_from_mysql'] = 'New table from MySQL';
$string['nofieldsspecified'] = 'No fields specified';
$string['nomasterprimaryuniquefound'] = 'The column(s) that your foreign key references must be included in a primary or unique KEY in the referenced table. Note that the column being in a UNIQUE INDEX is not good enough.';
$string['nomissingorextraindexesfound'] = 'No missing or extra indexes have been found, so no further action is required.';
$string['noreffieldsspecified'] = 'No reference fields specified';
$string['noreftablespecified'] = 'Specified reference table not found';
$string['noviolatedforeignkeysfound'] = 'No violated foreign keys found';
$string['nowrongdefaultsfound'] = 'No inconsistent default values have been found, your DB does not need further actions.';
$string['nowrongintsfound'] = 'No wrong integers have been found, your DB doesn\'t need further actions.';
$string['nowrongoraclesemanticsfound'] = 'No Oracle columns using BYTE semantics have been found, your DB doesn\'t need further actions.';
$string['numberincorrectdecimals'] = 'Incorrect number of decimals for number field';
$string['numberincorrectlength'] = 'Incorrect length for number field';
$string['numberincorrectwholepart'] = 'Too big whole number part for number field';
$string['pendingchanges'] = 'Note: You have performed changes to this file. They can be saved at any moment.';
$string['pendingchangescannotbesaved'] = 'There are changes in this file but they cannot be saved! Please verify that both the directory and the "install.xml" within it have write permissions for the web server.';
$string['pendingchangescannotbesavedreload'] = 'There are changes in this file but they cannot be saved! Please verify that both the directory and the "install.xml" within it have write permissions for the web server. Then reload this page and you should be able to save those changes.';
$string['persistentfieldsconfirm'] = 'Do you want to add the following fields: ';
$string['persistentfieldscomplete'] = 'The following fields have been added: ';
$string['persistentfieldsexist'] = 'The following fields already exist: ';
$string['pluginname'] = 'XMLDB editor';
$string['primarykeyonlyallownotnullfields'] = 'Primary keys cannot be null';
$string['reconcilefiles'] = 'Reconcile XMLDB files';
$string['reconcile_files'] = 'Look for XMLDB files needing reconciling';
$string['reconcile_files_intro'] = 'This functionality looks though the contents of all XMLDB files to verify that they match the results of generating them from the XMLDB editor.

A list of files needing to be reconciled (regenerated) will be displayed and the XMLDB editor can be used to fix them.';
$string['reconcile_files_no'] = 'All files are OK. No reconciling is needed.';
$string['reconcile_files_yes'] = 'Found files to reconcile: ';
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
$string['tablenameempty'] = 'The table name cannot be empty';
$string['tables'] = 'Tables';
$string['unknownfield'] = 'Refers to an unknown field';
$string['unknowntable'] = 'Refers to an unknown table';
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
$string['wrongnumberofreffields'] = 'Wrong number of reference fields';
$string['wrongreservedwords'] = 'Currently used reserved words<br />(note that table names aren\'t important if using $CFG->prefix)';
$string['wrongoraclesemantics'] = 'Wrong Oracle BYTE semantics found';
$string['yesextraindexesfound'] = 'The following additional indexes were found.';
$string['yesmissingindexesfound'] = '<p>Some missing indexes have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to create all of them. Remember to backup your data first!</p>
<p>After doing that, it\'s highly recommended to execute this utility again to check that no more missing indexes are found.</p>';
$string['yeswrongdefaultsfound'] = '<p>Some inconsistent defaults have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to fix them all. Remember to backup your data first!</p>
<p>After doing that, it\'s highly recommended to execute this utility again to check that no more inconsistent defaults are found.</p>';
$string['yeswrongintsfound'] = '<p>Some wrong integers have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to fix them. Remember to backup your data first!</p>
<p>After fixing them, it is highly recommended to execute this utility again to check that no more wrong integers are found.</p>';
$string['yeswrongoraclesemanticsfound'] = '<p>Some Oracle columns using BYTE semantics have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to convert them all. Remember to backup your data first!</p>
<p>After doing that, it\'s highly recommended to execute this utility again to check that no more wrong semantics are found.</p>';
$string['privacy:metadata'] = 'The XMLDB editor plugin does not store any personal data.';
