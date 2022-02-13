Description of phpCAS 1.4.0 import into Moodle

Last release package can be found in hhttps://github.com/apereo/phpCAS/releases

NOTICE:
 * Before running composer command, make sure you have the composer version updated.
 * Composer version 2.2.4 2022-01-08 12:30:42

STEPS:
 * Create a temporary folder outside your moodle installation
 * Execute `composer require apereo/phpcas:VERSION`
 * Check any new libraries that have been added and make sure they do not exist in Moodle already.
 * Remove the old 'vendor' directory in auth/cas/CAS/
 * Copy contents of 'vendor' directory
 * Create a commit with only the library changes
 * Update auth/cas/thirdpartylibs.xml
 * Apply the modifications described in the CHANGES section
 * Create another commit with the previous two steps of changes

CHANGES:
 * Remove all the hidden folders and files in vendor/apereo/phpcas/ (find . -name ".*"):
  - .codecov.yml
  - .gitattributes
  - .github