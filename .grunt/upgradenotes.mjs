#!/usr/bin/env node
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

import { Argument, Option, program } from 'commander';
import chalk from 'chalk';

import { getNoteNames } from './notes/src/noteTypes.mjs';
import createAction from './notes/src/create.mjs';
import generateAction from './notes/src/generate.mjs';
import logger from './notes/src/logger.mjs';

console.log(`
${chalk.bold(chalk.underline(chalk.green('Moodle Upgrade Notes Generator')))}

This tool is used to generate the upgrade notes for changes you make in Moodle.

Please remember that the intended audience of these changes is
${chalk.italic('plugin developers')} who need to know how to update their plugins
for a new Moodle version.

Upgrade notes should not be used to document changes for site administrators, or
for internal API changes which are not expected to be used outside of the
relevant component.
`)

program.configureHelp({
    helpWidth: 100,
});

program.on('option:verbose', () => {
    logger.level = 'verbose';
});

program.addOption(
    new Option(
        '-v, --verbose',
        'Output more information during the generation process',
    )
    .default(false)
);

// Define the command line options.
program
    .command('create')
    .summary('Generate a new upgrade note')
    .addOption(
        new Option('-t, --type <type>', `The type of change to document. Valid types are: ${getNoteNames().join(', ')}`)
    )
    .addOption(new Option('-i, --issue <issue>', 'The tracker issue number'))
    .addOption(new Option('-c, --component <component>', 'The component to write a note for'))
    .addOption(new Option(
        '-m, --message <message>',
        'The message to use for the upgrade note',
    ))
    .action((options) => createAction(options));

program
    .command('summary')
    .summary('Generate a local copy of the upgrade notes summary')
    .addArgument(
        new Argument('[version]', 'The Moodle version to create the summary notes for')
    )
    .action((version) => generateAction(version));

program
    .command('release')
    .summary('Generate the markdown copies of the upgrade notes for a Moodle release')
    .addArgument(
        new Argument('[version]', 'The Moodle version to create the release notes for')
    )
    .addOption(
        new Option(
            '--generate-upgrade-notes',
            'Generate the UPGRADING.md notes for the release. ' +
                'Note: This option is intended for use by the release manager when generating the upgrade notes.',
        )
        .default(true)
    )
    .addOption(
        new Option(
            '-d, --delete-notes',
            'Delete the notes after generating the UPGRADING.md notes for the release. ' +
                'Note: This option is intended for use by the release manager when generating the upgrade notes.' +
                'This option has no effect unless --generate-upgrade-notes is also set.'
        )
        .default(false)
    )
    .action((version, options) => generateAction(version, options));

program.parse();
