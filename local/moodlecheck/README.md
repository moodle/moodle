Moodle PHPdoc Checker
=====================

[![Moodlecheck CI](https://github.com/moodlehq/moodle-local_moodlecheck/actions/workflows/ci.yml/badge.svg)](https://github.com/moodlehq/moodle-local_moodlecheck/actions/workflows/ci.yml)

Installation:
-------------

Install the source into the local/moodlecheck directory in your moodle

Log in as admin and select:

Settings
  Site administration
    Development
      Moodle PHPdoc check

Enter paths to check and select rules to use.

Customization:
--------------

You can add new rules by adding new php files in rules/ directory,
they will be included automatically.

Look at other files in this directory for examples.

Please note that if you register the rule with code 'mynewrule',
the rule registry will look in language file for strings
'rule_mynewrule' and 'error_mynewrule'. If they are not present,
the rule code will be used instead of the rule name and
default error message appears.
