Description of PhpSpreadsheet 1.10.1 import into Moodle

Last release package can be found in https://github.com/PHPOffice/PhpSpreadsheet/releases

NOTICE:
 * Before running composer command, make sure you have the composer version updated.
 * Composer version 2.0.8 2020-12-03 17:20:38

STEPS:
 * Create a temporary folder outside your moodle installation
 * Remove the libraries that Moodle already includes from composer.json
   - ezyang/htmlpurifier
   - maennchen/zipstream-php
 * Execute `composer require phpoffice/phpspreadsheet`
 * Check to make sure the following directories haven't been created
   - /vendor/myclabs/*
   - /vendor/maennchen/*
   - /vendor/symfony/polyfill-mbstring
   - /vendor/ezyang/htmlpurifier
 * If it has pulled these through, remove them from the required packages and run composer again.
 * Check any new libraries that have been added and make sure they do not exist in Moodle already.
 * Remove the old 'vendor' directory in lib/phpspreadsheet/
 * Copy contents of 'vendor' directory
 * Create a commit with only the library changes
 * Update lib/thirdpartylibs.xml
 * Apply the modifications described in the CHANGES section
 * Create another commit with the previous two steps of changes
 * Go to http://localhost/lib/tests/other/spreadsheettestpage.php and test the generated files


CHANGES:
 * If the following exist, remove the following folders (and their content):
   - vendor/phpoffice/phpspreadsheet/bin
   - vendor/phpoffice/phpspreadsheet/docs
   - vendor/phpoffice/phpspreadsheet/samples

* Remove all the hidden folders and files in vendor/phpoffice/phpspreadsheet/ (find . -name ".*"):
  - .DS_Store
  - .gitattributes
  - .gitignore
  - .php_cs.dist
  - .sami.php
  - .scrutinizer.yml
  - .travis.yml
  - .phpcs.xml.dist
  - vendor/psr/simple-cache/.editorconfig
  - vendor/psr/http-factory/.gitignore
  - vendor/psr/http-factory/.pullapprove.yml

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