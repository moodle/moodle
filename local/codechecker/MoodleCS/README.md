# Moodle Coding Style

<div aria-hidden="true">

[![Latest Stable Version](https://poser.pugx.org/moodlehq/moodle-cs/v/stable)](https://packagist.org/packages/moodlehq/moodle-cs)
[![Release Date of the Latest Version](https://img.shields.io/github/release-date/moodlehq/moodle-cs.svg?maxAge=1800)](https://github.com/moodlehq/moodle-cs/releases)

[![Unit Tests](https://github.com/moodlehq/moodle-cs/actions/workflows/phpcs.yml/badge.svg)](https://github.com/moodlehq/moodle-cs/actions/workflows/phpcs.yml)

[![License](https://poser.pugx.org/moodlehq/moodle-cs/license)](https://github.com/moodlehq/moodle-cs/blob/main/LICENSE)
[![Total Downloads](https://poser.pugx.org/moodlehq/moodle-cs/downloads)](https://packagist.org/packages/moodlehq/moodle-cs/stats)
[![Number of Contributors](https://img.shields.io/github/contributors/moodlehq/moodle-cs.svg?maxAge=3600)](https://github.com/moodlehq/moodle-cs/graphs/contributors)

</div>

## Information

This repository contains the Moodle Coding Style configuration.

Currently this only includes the configuration for PHP Coding style, but this
may be extended to include custom rules for JavaScript, and any other supported
languages or syntaxes.


## Installation

### Using Composer

You can include these coding style rules using Composer to make them available
globally across your system.

This will install the correct version of phpcs, with the Moodle rules, and their
dependencies.

```
composer global require moodlehq/moodle-cs
```

### As a part of moodle-local_codechecker

This plugin is included as part of the [moodle-local_codechecker
plugin](https://github.com/moodlehq/moodle-local_codechecker).


## Configuration

You can set the Moodle standard as the system default:
```
phpcs --config-set default_standard moodle
```

This will inform most IDEs automatically.
Alternatively you can configuration your IDE to use phpcs with the Moodle
ruleset as required.


### IDE Integration

#### PhpStorm

1. Open PhpStorm preferences
2. Go to Inspections > PHP > PHP Code Sniffer Validation
3. In the 'coding standard' dropdown, select 'moodle'

#### Sublime Text

Find documentation [here](https://docs.moodle.org/dev/Setting_up_Sublime2#Sublime_PHP_CS).

1. Go in your Sublime Text to Preferences -> Package Control -> Package Control: Install Package
2. Write 'phpcs' in the search field, if you see Phpcs and SublimeLinter-phpcs, click on them to install them.
3. If not, check if they are already installed Preferences -> Package Control -> Package Control: Remove Package.
4. To set your codecheck to moodle standards go to Preferences -> Package Settings -> PHP Code Sniffer -> Settings-User and write:

        { "phpcs_additional_args": {
                "--standard": "moodle",
                "-n": "
            },
        }

5. If you don’t have the auto-save plugin turned on, YOU’RE DONE!
6. If you have the auto-save plugin turned on, because the codecheck gets triggered on save, the quick panel will keep popping making it impossible to type.
   To stop quick panel from showing go to Settings-User file and add:

        "phpcs_show_quick_panel": false,

   The line with the error will still get marked and if you’ll click on it you’ll see the error text in the status bar.

#### VSCode

Find documentation [here](https://docs.moodle.org/dev/Setting_up_VSCode#PHP_CS).

1. Install [PHPSniffer](https://marketplace.visualstudio.com/items?itemName=wongjn.php-sniffer).
2. Open VSCode settings.json and add the following setting to define standard PHP CS (if you haven't set it as default in your system):

        "phpSniffer.standard": "moodle",
