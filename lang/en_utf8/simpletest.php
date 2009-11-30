<?PHP // $Id$
      // simpletest.php - created with Moodle 1.7 beta + (2006101003)


$string['all'] = 'ALL';
$string['addconfigprefix'] = 'Add prefix to config file';
$string['codecoverageanalysis'] = 'Perform code coverage analysis.';
$string['codecoveragecompletereport'] = '(view code coverage complete report)';
$string['codecoveragedisabled'] = 'Cannot enable code coverage in this server (missing xdebug extension).';
$string['codecoveragelatestdetails'] = '(on $a->date, with $a->files files, $a->percentage covered)';
$string['codecoveragelatestreport'] = 'view latest code coverage complete report';
$string['confignonwritable'] = 'The file config.php is not writeable by the web server. Either change its permissions, or edit it with the appropriate user account, and add the following line before the closing php tag: <br />
\$CFG->unittestprefix = \'tst_\' // Change tst_ to a prefix of your choice, different from \$CFG->prefix';
$string['coveredlines'] = 'Covered lines';
$string['coveredpercentage'] = 'Overall Code Coverage';
$string['deletingnoninsertedrecord'] = 'Trying to delete a record that was not inserted by these unit tests (id $a->id in table $a->table).';
$string['deletingnoninsertedrecords'] = 'Trying to delete records that were not inserted by these unit tests (from table $a->table).';
$string['droptesttables'] = 'Drop test tables';
$string['exception'] = 'Exception';
$string['executablelines'] = 'Executable lines';
$string['fail'] = 'Fail';
$string['ignorefile'] = 'Ignore tests in the file';
$string['ignorethisfile'] = 'Re-run the tests ignoring this test file.';
$string['installtesttables'] = 'Install test tables';
$string['moodleunittests'] = 'Moodle unit tests: $a';
$string['notice'] = 'Notice';
$string['onlytest'] = 'Only run tests in';
$string['pass'] = 'Pass';
$string['pathdoesnotexist'] = 'The path \'$a\' does not exist.';
$string['prefix'] = 'Unit test tables prefix';
$string['prefixnotset'] = 'The unit test database table prefix is not configured. Fill and submit this form to add it to config.php.';
$string['reinstalltesttables'] = 'Reinstall test tables';
$string['retest'] = 'Re-run the tests';
$string['retestonlythisfile'] = 'Re-run only this test file.';
$string['runall'] = 'Run the tests from all the test files.';
$string['runat'] = 'Run at $a.';
$string['runonlyfile'] = 'Run only the tests in this file';
$string['runonlyfolder'] = 'Run only the tests in this folder';
$string['runtests'] = 'Run tests';
$string['rununittests'] = 'Run the unit tests';
$string['showpasses'] = 'Show passes as well as fails.';
$string['showsearch'] = 'Show the search for test files.';
$string['skip'] = 'Skip';
$string['stacktrace'] = 'Stack trace:';
$string['summary'] = '{$a->run}/{$a->total} test cases complete: <strong>{$a->passes}</strong> passes, <strong>{$a->fails}</strong> fails and <strong>{$a->exceptions}</strong> exceptions.';
$string['tablesnotsetup'] = 'Unit test tables are not yet built. Do you want to build them now?.';
$string['testdboperations'] = 'Test Database operations';
$string['testtablesneedupgrade'] = 'The test DB tables need to be upgraded. Do you wish to proceed with the upgrade now?';
$string['testtablesok'] = 'The test DB tables were successfully installed.';
$string['testtablescsvfileunwritable'] = 'The test tables CSV file is not writable ($a->filename)';
$string['timetakes'] = 'Time taken: $a.';
$string['totallines'] = 'Total lines';
$string['thorough'] = 'Run a thorough test (may be slow).';
$string['updatingnoninsertedrecord'] = 'Trying to update a record that was not inserted by these unit tests (id $a->id in table $a->table).';
$string['uncaughtexception'] = 'Uncaught exception [{$a->getMessage()}] in [{$a->getFile()}:{$a->getLine()}] TESTS ABORTED.';
$string['uncoveredlines'] = 'Uncovered lines';
$string['unittests'] = 'Unit tests';
$string['unittestprefixsetting'] = 'Unit test prefix: <strong>$a->unittestprefix</strong> (Edit config.php to modify this).';
$string['version'] = 'Using <a href=\"http://sourceforge.net/projects/simpletest/\">SimpleTest</a> version $a.';

?>
