MOODLE-SPECIFIC PEAR MODIFICATIONS
==================================


Spreadsheet/Excel
=================

1/ These functions:
    _calculateSharedStringsSizes()
    _storeSharedStringsTable()
have been inserted, replacing the original functions in order to make the function
work with 2-byte data.  The patch is discussed at this URL:
    http://pear.php.net/bugs/bug.php?id=1572
and documented for Moodle at:
    http://tracker.moodle.org/browse/MDL-9911

2/ Changed ereg_ to preg_
* http://cvs.moodle.org/moodle/lib/excel/Parser.php.diff?r1=1.1&r2=1.2
* http://cvs.moodle.org/moodle/lib/excel/Worksheet.php.diff?r1=1.1&r2=1.2

3/ removed deprecated "=& new"

4/ MDL-20876 - replaced deprecated split() with explode() or str_split() where appropriate

Such modifications should be carefully each time the Excel PEAR package is updated
to a new release within Moodle.

5/ static keywords in OLE.php
* static function Asc2Ucs()
* static function LocalDate2OLE()

XML/Parser
=================
1/ changed ereg_ to preg_
* http://cvs.moodle.org/moodle/lib/pear/XML/Parser.php.diff?r1=1.1&r2=1.2


Quickforms
==========
Full of our custom hacks, no way to upgrade to latest upstream.
Most probably we will stop using this library in the future.

MDL-20876 - replaced split() with explode() or preg_split() where appropriate

