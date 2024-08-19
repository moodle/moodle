Description of axe import into Moodle

1/ Download the latest axe code somewhere (example /tmp/axe) using:

mkdir -p /tmp/axe
cd /tmp/axe
npm install axe-core --save-dev

Note down the version number displayed by the command, to update lib/thirdpartylibs.xml accordingly.

If the command does not output a version number, the version number can be found in package.json. You can simply open package.json
using your desired editor and look for the version number of axe-core. Alternatively, you may use the following commands:
- MacOS:
    cat package.json | grep axe-core
- Linux:
    cat package.json | grep axe-core
    or
    jq -r '.devDependencies."axe-core"' package.json

2/ Copy the following file to your local Moodle directory, to replace the old one:

cp /tmp/axe/node_modules/axe-core/axe.min.js [PATH TO YOUR MOODLE]/lib/behat/axe/

3/ Update lib/thirdpartylibs.xml with the new version number.

4/ Update the PHPDoc block of \behat_accessibility::run_axe_validation_for_tags() in
[PATH TO YOUR MOODLE]/lib/tests/behat/behat_accessibility.php to reflect the new version number.

5/ Run behat tests labelled with @accessibility and confirm they are passing with the new library version, or fix the failures
because the new version might raise issues that weren't detected previously:

php admin/tool/behat/cli/init.php --add-core-features-to-theme
php admin/tool/behat/cli/run.php --tags=@accessibility
