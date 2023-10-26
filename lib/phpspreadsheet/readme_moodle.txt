Description of PhpSpreadsheet import into Moodle

Last release package can be found in https://github.com/PHPOffice/PhpSpreadsheet/releases

NOTICE:
 * Before running composer command, make sure you have the composer version updated.
 * Composer version 2.2.4 2022-01-08 12:30:42

STEPS:
 * Create a temporary folder outside your moodle installation
 * Create a composer.json file with the following content (you will need to replace X.YY to the proper version to be upgraded):
{
    "require": {
        "phpoffice/phpspreadsheet": "^X.YY"
    },
    "replace": {
        "ezyang/htmlpurifier": "*",
        "maennchen/zipstream-php": "*",
        "myclabs/php-enum": "*",
        "symfony/polyfill-mbstring": "*"
    }
}
 * Execute `composer require phpoffice/phpspreadsheet`
 * Check to make sure the following directories haven't been created
   - /vendor/ezyang/htmlpurifier
   - /vendor/maennchen/*
   - /vendor/myclabs/*
   - /vendor/symfony/polyfill-mbstring
 * If it has pulled these through, remove them from the required packages and run composer again.
 * Check any new libraries that have been added and make sure they do not exist in Moodle already.
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
   - .php-cs-fixer.dist.php
   - vendor/psr/simple-cache/.editorconfig
   - vendor/psr/http-factory/.gitignore
   - vendor/psr/http-factory/.pullapprove.yml
   - vendor/markbaker/matrix/.github
   - vendor/markbaker/complex/.github
 * Remove the next files in related to external testing/analysis that we don't need matrix/:
   - vendor/markbaker/matrix/infection.json.dist (PHP mutation testing framework configuration file)
   - vendor/markbaker/matrix/phpstan.neon (PHP static analyzer configuration file)
   - vendor/phpoffice/phpspreadsheet/phpstan-baseline.neon
   - vendor/phpoffice/phpspreadsheet/phpstan-conditional.php
   - vendor/phpoffice/phpspreadsheet/phpstan.neon.dist
 * Shared/OLE has been removed because OLE is not DFSG compliant and is not being used in core code.
   Remove the files/folders (placed in vendor/phpoffice/phpspreadsheet/src/):
   - PhpSpreadsheet/Shared/OLE.php
   - PhpSpreadsheet/Shared/OLERead.php
   - PhpSpreadsheet/Shared/OLE/*
 * Xsl files have been removed. These files are for Excel version 5 (created in 1993) and are not used in core code.
   Remove the files/folders (placed in vendor/phpoffice/phpspreadsheet/src/):
   - PhpSpreadsheet/Reader/Xls.php
   - PhpSpreadsheet/Reader/Xls/*
   - PhpSpreadsheet/Shared/Xls.php
   - PhpSpreadsheet/Writer/Xls.php
   - PhpSpreadsheet/Writer/Xls/*
 * Remove the old 'vendor' directory in lib/phpspreadsheet/
 * Copy contents of 'vendor' directory
 * Create a commit with only the library changes
 * Update lib/thirdpartylibs.xml
 * Apply the modifications described in the CHANGES section
 * Create another commit with the previous two steps of changes
 * Go to http://<your moodle root>/lib/tests/other/spreadsheettestpage.php and test the generated files


CHANGES:

 * Add the next Moodle hack at the beginning of the function sysGetTempDir()
located in lib/phpspreadsheet/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Shared/File.php
    // Moodle hack!
     if (function_exists('make_temp_directory')) {
         $temp = make_temp_directory('phpspreadsheet');
         return realpath(dirname($temp));
     }
  This hack is needed because it can not be guaranteed that sysGetTempDir() works everywhere.
