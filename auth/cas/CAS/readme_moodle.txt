Description of phpCAS import into Moodle

Last release can be found at https://github.com/apereo/phpCAS/releases

NOTICE:
 * Before running composer command, make sure you have the composer version updated.
 * Composer version 2.7.7 2024-06-16 19:06:42

STEPS:
 * Make sure you're using the lowest supported PHP version for the given release (e.g. PHP 8.1 for Moodle 4.5)
 * Create a temporary folder outside your Moodle installation e.g. /tmp/phpcas
 * Create a composer.json file with the following content inside the temporary folder (you will need to replace X.YY to the proper version to be upgraded):
{
    "require": {
        "apereo/phpcas": "X.YY"
    },
    "replace": {
        "psr/log": "*"
    }
}
 * Execute 'composer require apereo/phpcas' inside the temporary folder.
 * Check to make sure the following directory hasn't been created
   - vendor/psr/log
 * Check any new libraries that have been added and make sure they do not exist in Moodle already.
 * Remove the 'vendor' directory in auth/cas/CAS/
 * Copy the '/tmp/phpcas/vendor' directory into auth/cas/CAS/
 * Create a commit with only the library changes.
   - Note: Make sure to check the list of unversioned files and add any new files to the staging area.
 * Update auth/cas/thirdpartylibs.xml
 * Create another commit with the previous change
