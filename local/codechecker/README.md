Moodle Code Checker
===================

[![Codechecker CI](https://github.com/moodlehq/moodle-local_codechecker/actions/workflows/ci.yml/badge.svg)](https://github.com/moodlehq/moodle-local_codechecker/actions/workflows/ci.yml)

Information
-----------

This Moodle plugin uses the [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) tool to
check that code follows the [Moodle coding style](https://moodledev.io/general/development/policies/codingstyle).
It uses the [Moodle Coding Style](https://github.com/moodlehq/moodle-cs) 'sniffs' that check many aspects of the code, including the awesome
[PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility) ones.

It was created by developers at the Open University, including Sam Marshall,
Tim Hunt and Jenny Gray. It is now maintained by Moodle HQ.

Available releases can be downloaded and installed from
<https://moodle.org/plugins/view.php?plugin=local_codechecker>.

To install it using git, type this command in the root of your Moodle install:

    git clone https://github.com/moodlehq/moodle-local_codechecker.git local/codechecker

Then add /local/codechecker to your git ignore.

Additionally, remember to only use the version of PHPCS located in ``phpcs/bin/phpcs`` rather than installing PHPCS directly. Add the location of the PHPCS executable to your system path, tell PHPCS about the Moodle coding standard with ``phpcs --config-set installed_paths /path/to/moodle-local_codechecker``  and set the default coding standard to Moodle with ``phpcs --config-set default_standard moodle``.  You can now test a file (or folder) with: ``phpcs /path/to/file.php``.

Alternatively, download the zip from
<https://github.com/moodlehq/moodle-local_codechecker/zipball/master>,
unzip it into the local folder, and then rename the new folder to "codechecker".

After you have installed this local plugin, you
should see a new option in the settings block:

> Site administration -> Development -> Code checker

We hope you find this tool useful. Feel free to enhance it! Also, you can report any idea or bug using GitHub's issues and pull requests, thanks!


Integrations
------------

Since version v4.0.0 this plugin shouldn't be used as source for any integration with IDEs or tools, and [Moodle Coding Style](https://github.com/moodlehq/moodle-cs) (the new source for the moodle-cs standard) should be used instead.

Please refer to the information available in that repository to know more about how to install, configure and integrate it with your development environment.
