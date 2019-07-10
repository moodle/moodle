Description of PhpSpreadsheet 1.7.0 import into Moodle

Last release package can be found in https://github.com/PHPOffice/PhpSpreadsheet/releases

STEPS:
 * Create a temporary folder outside your moodle installation
 * Execute `composer require phpoffice/phpspreadsheet`
 * Copy contents of 'vendor' directory
 * Update lib/thirdpartylibs.xml
 * Apply the modifications described in the CHANGES section
 * Go to http://localhost/lib/tests/other/spreadsheettestpage.php and test the generated files


CHANGES:
 * Remove the following folders (and their content):
   - vendor/phpoffice/phpspreadsheet/bin
   - vendor/phpoffice/phpspreadsheet/docs
   - vendor/phpoffice/phpspreadsheet/samples

* Remove the hidden folders and files in vendor/phpoffice/phpspreadsheet/:
  - .github/*
  - .gitattributes
  - .gitignore
  - .php_cs.dist
  - .sami.php
  - .scrutinizer.yml
  - .travis.yml

 * Add the Moodle hack in vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Shared/File/sys_get_temp_dir() because we
 can not guarantee sys_get_temp_dir() works everywhere:

     // Moodle hack!
     if (function_exists('make_temp_directory')) {
         $temp = make_temp_directory('phpspreadsheet');
         return realpath(dirname($temp));
     }

 * Shared/OLE has been removed because OLE is not DFSG compliant and is not being used in core code.
   Remove the files/folders:
   - PhpSpreadsheet/Shared/OLE.php
   - PhpSpreadsheet/Shared/OLERead.php
   - PhpSpreadsheet/Shared/OLE/*

 * Xsl files have been removed. These files are for Excel version 5 (created in 1993) and are not used in core code.
   Remove the files/folders:
   - PhpSpreadsheet/Reader/Xls.php
   - PhpSpreadsheet/Reader/Xls/*
   - PhpSpreadsheet/Shared/Xls.php
   - PhpSpreadsheet/Writer/Xls.php
   - PhpSpreadsheet/Writer/Xls/*


COMMENTS:
 * lib/excellib.class.php has been updated so that only xslx spreadsheets will be produced.
