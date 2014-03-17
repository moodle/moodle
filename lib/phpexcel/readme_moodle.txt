Description of PHPExcel 1.8.0 import into Moodle

Steps:
 * download release package from https://github.com/PHPOffice/PHPExcel
 * copy Classes directory
 * update lib/thirdpartylibs.xml
 * apply changes
 * go to http://127.0.0.1/lib/tests/other/spreadsheettestpage.php
   and test the generated files

Changes:
 * Shared/File/sys_get_temp_dir() - we can not guarantee sys_get_temp_dir() works everywhere
