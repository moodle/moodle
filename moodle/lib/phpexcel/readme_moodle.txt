Description of PHPExcel 1.8.0 import into Moodle

Steps:
 * download release package from https://github.com/PHPOffice/PHPExcel
 * copy contents of Classes directory
 * update lib/thirdpartylibs.xml
 * apply changes
 * go to http://127.0.0.1/lib/tests/other/spreadsheettestpage.php
   and test the generated files

Changes:
 * Shared/File/sys_get_temp_dir() - we can not guarantee sys_get_temp_dir() works everywhere
 * Shared/OLE has been removed. OLE is not DFSG compliant and is not being used in core code.
 * Excel5 files have been removed. These files are for Excel version 5 (created in 1993) and are not used in core code.
   The files removed are:
   - PHPExcel/Shared/Excel5.php
   - PHPExcel/Shared/OLE.php
   - PHPExcel/Shared/OLERead.php
   - PHPExcel/Writer/Excel5.php
   - PHPExcel/Writer/Excel5/BIFFwriter.php
   - PHPExcel/Writer/Excel5/Escher.php
   - PHPExcel/Writer/Excel5/Font.php
   - PHPExcel/Writer/Excel5/Parser.php
   - PHPExcel/Writer/Excel5/Workbook.php
   - PHPExcel/Writer/Excel5/Worksheet.php
   - PHPExcel/Writer/Excel5/Xf.php
   lib/excellib.class.php has been updated so that only 2007 excel spreadsheets will be produced.
 * MDL-52336 patch for PHP7 compatibility, after upgrade make sure that these changes are included and remove this line



