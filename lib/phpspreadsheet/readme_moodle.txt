Description of PhpSpreadsheet 1.10.1 import into Moodle

Last release package can be found in https://github.com/PHPOffice/PhpSpreadsheet/releases

NOTICE:
 * Before running composer command, make sure you have the composer version updated.
 * Composer version 1.9.1 2019-11-01 17:20:17

STEPS:
 * Create a temporary folder outside your moodle installation
 * Execute `composer require phpoffice/phpspreadsheet`
 * Remove the old 'vendor' directory in lib/phpspreadsheet/
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
  - .gitattributes
  - .gitignore
  - .php_cs.dist
  - .sami.php
  - .scrutinizer.yml
  - .travis.yml

 * Add the next Moodle hack at the beginning of the function sysGetTempDir()
located in vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Shared/File.php
    // Moodle hack!
     if (function_exists('make_temp_directory')) {
         $temp = make_temp_directory('phpspreadsheet');
         return realpath(dirname($temp));
     }
  We need this hack because we can not guarantee sysGetTempDir() works everywhere.

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

* Remove the next files in vendor/markbaker/ related to external testing that we don't need matrix/:
  - infection.json.dist (PHP mutation testing framework configuration file)
  - phpstan.neon (PHP static analyzer configuration file)
