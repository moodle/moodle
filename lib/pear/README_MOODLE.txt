MOODLE-SPECIFIC PEAR MODIFICATIONS
==================================


Spreadsheet/Excel
=================

These functions:
    _calculateSharedStringsSizes()
    _storeSharedStringsTable()
have been inserted, replacing the original functions in order to make the function
work with 2-byte data.  The patch is discussed at this URL:
    http://pear.php.net/bugs/bug.php?id=1572
and documented for Moodle at:
    http://tracker.moodle.org/browse/MDL-9911

Such modifications should be carefuly each time the Excel PEAR package is updated
to a new release within Moodle.


PHP/CodeSniffer
===============

A whole Moodle coding standards definition sits in lib/pear/PHP/CodeSniffer/Standards/Moodle

To run the codesniffer, you can call the runsniffer script using your command-line php binary:

Example:  /usr/bin/php lib/pear/PHP/runsniffer mod/forum


