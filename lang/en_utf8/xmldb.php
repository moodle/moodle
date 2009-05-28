<?PHP // $Id$ 
      // xmldb.php - created with Moodle 1.7 beta + (2006101003)


$string['aftertable'] = 'After Table:';
$string['back'] = 'Back';
$string['backtomainview'] = 'Back To Main';
$string['binaryincorrectlength'] = 'Incorrect length for binary field';
$string['butis'] = 'but is';
$string['cannotuseidfield'] = 'Cannot insert the \"id\" field. It is an autonumeric column';
$string['change'] = 'Change';
$string['charincorrectlength'] = 'Incorrect length for char field';
$string['checkbigints'] = 'Check Bigints';
$string['checkdefaults'] = 'Check Defaults';
$string['checkindexes'] = 'Check Indexes';
$string['check_defaults'] = 'Look for inconsistent default values';
$string['check_bigints'] = 'Look for incorrect DB integers';
$string['check_indexes'] = 'Look for missing DB indexes';
$string['completelogbelow'] = '(see the complete log of the search below)';
$string['confirmcheckbigints'] = 'This functionality will search for <a href=\"http://tracker.moodle.org/browse/MDL-11038\">potential wrong integer fields</a> in your Moodle server, generating (but not executing!) automatically the needed SQL statements to have all the integers in your DB properly defined.<br /><br />
Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of wrong integers.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckdefaults'] = 'This functionality will search for inconsistent default values in your Moodle server, generating (but not executing!) the needed SQL statements to have all the default values properly defined.<br /><br />
Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of wrong integers.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckindexes'] = 'This functionality will search for potential missing indexes in your Moodle server, generating (but not executing!) automatically the needed SQL statements to keep everything updated.<br /><br />
Once generated you can copy such statements and execute them safely with your favourite SQL interface (don\'t forget to backup your data before doing that).<br /><br />
It\'s highly recommended to be running the latest (+ version) available of your Moodle release (1.8, 1.9, 2.x ...) before executing the search of missing indexes.<br /><br />
This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmdeletefield'] = 'Are you absolutely sure that you want to delete the field:';
$string['confirmdeleteindex'] = 'Are you absolutely sure that you want to delete the index:';
$string['confirmdeletekey'] = 'Are you absolutely sure that you want to delete the key:';
$string['confirmdeletesentence'] = 'Are you absolutely sure that you want to delete the sentence';
$string['confirmdeletestatement'] = 'Are you absolutely sure that you want to delete the statement and all its sentences:';
$string['confirmdeletetable'] = 'Are you absolutely sure that you want to delete the table:';
$string['confirmdeletexmlfile'] = 'Are you absolutely sure that you want to delete the file:';
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
$string['down'] = 'Down';
$string['duplicate'] = 'Duplicate';
$string['duplicatefieldname'] = 'Another field with that name exists';
$string['edit'] = 'Edit';
$string['edit_field'] = 'Edit Field';
$string['edit_index'] = 'Edit Index';
$string['edit_key'] = 'Edit Key';
$string['edit_sentence'] = 'Edit Sentence';
$string['edit_statement'] = 'Edit Statement';
$string['edit_table'] = 'Edit Table';
$string['edit_xml_file'] = 'Edit XML File';
$string['enumvaluesincorrect'] = 'Incorrect values for enum field';
$string['field'] = 'Field';
$string['fieldnameempty'] = 'Name field empty';
$string['fields'] = 'Fields';
$string['filenotwriteable'] = 'File not writeable';
$string['floatincorrectdecimals'] = 'Incorrect number of decimals for float field';
$string['floatincorrectlength'] = 'Incorrect length for float field';
$string['gotolastused'] = 'Go to last used file';
$string['incorrectfieldname'] = 'Incorrect name';
$string['index'] = 'Index';
$string['indexes'] = 'Indexes';
$string['integerincorrectlength'] = 'Incorrect length for integer field';
$string['key'] = 'Key';
$string['keys'] = 'Keys';
$string['listreservedwords'] = 'List of Reserved Words<br />(used to keep <a href=\"http://docs.moodle.org/en/XMLDB_reserved_words\" target=\"_blank\">XMLDB_reserved_words</a> updated)';
$string['load'] = 'Load';
$string['missing'] = 'Missing';
$string['missingindexes'] = 'Missing Indexes Found';
$string['main_view'] = 'Main View';
$string['missingfieldsinsentence'] = 'Missing fields in sentence';
$string['missingvaluesinsentence'] = 'Missing values in sentence';
$string['mustselectonefield'] = 'You must select one field to see field related actions!';
$string['mustselectoneindex'] = 'You must select one index to see index related actions!';
$string['mustselectonekey'] = 'You must select one key to see key related actions!';
$string['mysqlextracheckbigints'] = 'Under MySQL it also looks for incorrectly signed bigints, generating the required SQL to be executed in order to fix all them.';
$string['new_statement'] = 'New Statement';
$string['new_table_from_mysql'] = 'New Table From MySQL';
$string['newfield'] = 'New Field';
$string['newindex'] = 'New Index';
$string['newkey'] = 'New Key';
$string['newsentence'] = 'New Sentence';
$string['newstatement'] = 'New Statement';
$string['newtable'] = 'New Table';
$string['newtablefrommysql'] = 'New Table From MySQL';
$string['nomissingindexesfound'] = 'No missing indexes have been found, your DB doesn\'t need further actions.';
$string['nowrongdefaultsfound'] = 'No inconsistent default values have been found, your DB does not need further actions.';
$string['nowrongintsfound'] = 'No wrong integers have been found, your DB doesn\'t need further actions.';
$string['numberincorrectdecimals'] = 'Incorrect number of decimals for number field';
$string['numberincorrectlength'] = 'Incorrect length for number field';
$string['reserved'] = 'Reserved';
$string['reservedwords'] = 'Reserved Words';
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
$string['shouldbe'] = 'should be';
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
$string['view_reserved_words'] = 'View Reserved Words';
$string['view_structure_php'] = 'View Structure PHP';
$string['view_structure_sql'] = 'View Structure SQL';
$string['view_table_php'] = 'View Table PHP';
$string['view_table_sql'] = 'View Table SQL';
$string['viewedited'] = 'View Edited';
$string['vieworiginal'] = 'View Original';
$string['viewphpcode'] = 'View PHP Code';
$string['viewsqlcode'] = 'View SQL Code';
$string['wrong'] = 'Wrong';
$string['wrongdefaults'] = 'Wrong Defaults Found';
$string['wrongints'] = 'Wrong Integers Found';
$string['wronglengthforenum'] = 'Incorrect length for enum field';
$string['wrongnumberoffieldsorvalues'] = 'Incorrect number of fields or values in sentence';
$string['wrongreservedwords'] = 'Currently Used Reserved Words<br />(note that table names aren\'t important if using \$CFG->prefix)';
$string['yeswrongdefaultsfound'] = 'Some inconsistent defaults have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to fix them all (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more iconsistent defaults are found.';
$string['yesmissingindexesfound'] = 'Some missing indexes have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more missing indexes are found.';
$string['yeswrongintsfound'] = 'Some wrong integers have been found in your DB. Here are their details and the needed SQL statements to be executed with your favourite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more wrong integers are found.';
?>
