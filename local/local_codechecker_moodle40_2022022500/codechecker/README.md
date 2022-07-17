Moodle Code Checker
===================

[![Codechecker CI](https://github.com/moodlehq/moodle-local_codechecker/actions/workflows/ci.yml/badge.svg)](https://github.com/moodlehq/moodle-local_codechecker/actions/workflows/ci.yml)

Information
-----------

This Moodle plugin uses the [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) tool to
check that code follows the [Moodle coding style](http://docs.moodle.org/dev/Coding_style). It
implements and reuses a set of 'sniffs' that check many aspects of the code,
including the awesome [PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility) ones.

It was created by developers at the Open University, including Sam Marshall,
Tim Hunt and Jenny Gray. It is now maintained by Moodle HQ.

Available releases can be downloaded and installed from
<https://moodle.org/plugins/view.php?plugin=local_codechecker>.

To install it using git, type this command in the root of your Moodle install:

    git clone git://github.com/moodlehq/moodle-local_codechecker.git local/codechecker

Then add /local/codechecker to your git ignore.

Additionally, remember to only use the version of PHPCS located in ``phpcs/bin/phpcs`` rather than installing PHPCS directly. Add the location of the PHPCS executable to your system path, tell PHPCS about the Moodle coding standard with ``phpcs --config-set installed_paths /path/to/moodle-local_codechecker``  and set the default coding standard to Moodle with ``phpcs --config-set default_standard moodle``.  You can now test a file (or folder) with: ``phpcs /path/to/file.php``.

Alternatively, download the zip from
<https://github.com/moodlehq/moodle-local_codechecker/zipball/master>,
unzip it into the local folder, and then rename the new folder to "codechecker".

After you have installed this local plugin, you
should see a new option in the settings block:

> Site administration -> Development -> Code checker

We hope you find this tool useful. Please feel free to enhance it.
Report any idea or bug in [the Tracker](https://tracker.moodle.org/issues/?jql=project%20%3D%20CONTRIB%20AND%20component%20%3D%20%22Local%3A+Code+checker%22), thanks!


## Composer installation

1. Install via composer:
```
composer global require moodlehq/moodle-local_codechecker
```

This will install the correct version of phpcs, with the Moodle rules installed, and install the rules.

You can set the Moodle standard as the system default:
```
phpcs --config-set default_standard moodle
```

This will inform most IDEs automatically.


IDE Integration
---------------

### Eclipse:

1. Outdated!: If if you use Eclipse for development, you might want to install the PHP CodeSniffer plugin (http://www.phpsrc.org/).
2. Create a new "CodeSniffer standard" in the preferences page.
3. Point it at the moodle directory inside the codechecker folder.
4. Thank Michael Aherne from University of Strathclyde who worked this out!

### PhpStorm

1. Install the phpcs cli tool
2. Open PhpStorm preferences
3. Go to PHP > CodeSniffer and supply the path to the phpcs executable
4. Go to Inspections > PHP > PHP Code Sniffer Validation
5. In the 'coding standard' dropdown select 'custom' and press the [...]
   button next to the path to the coding standard. Point is at the moodle
   directory inside the this plugin directory.

### Sublime Text

Find documentation [here](https://docs.moodle.org/dev/Setting_up_Sublime2#Sublime_PHP_CS).

1. Install PHP CS following steps described in [this moodle docs page](https://docs.moodle.org/dev/CodeSniffer#Installing_PHP_CS).
2. Go in your Sublime Text to Preferences -> Package Control -> Package Control: Install Package
3. Write 'phpcs' in the search field, if you see Phpcs and SublimeLinter-phpcs, click on them to install them.
4. If not, check if they are already installed Preferences -> Package Control -> Package Control: Remove Package.
5. To set your codecheck to moodle standards go to Preferences -> Package Settings -> PHP Code Sniffer -> Settings-User and write:

        { "phpcs_additional_args": {
                "--standard": "moodle",
                "-n": "
            },
        }

6. If you don’t have the auto-save plugin turned on, YOU’RE DONE!
7. If you have the auto-save plugin turned on, because the codecheck gets triggered on save, the quick panel will keep popping making it impossible to type.
   To stop quick panel from showing go to Settings-User file and add:

        "phpcs_show_quick_panel": false,

   The line with the error will still get marked and if you’ll click on it you’ll see the error text in the status bar.

### VSCode

Find documentation [here](https://docs.moodle.org/dev/Setting_up_VSCode#PHP_CS).

1. Install PHP CS following steps described in [this moodle docs page](https://docs.moodle.org/dev/CodeSniffer#Installing_PHP_CS).
3. Install [PHPSniffer](https://marketplace.visualstudio.com/items?itemName=wongjn.php-sniffer).
2. Open VSCode settings.json and add the following setting to define standard PHP CS (if you haven't set it as default in your system):

        "phpSniffer.standard": "moodle",
