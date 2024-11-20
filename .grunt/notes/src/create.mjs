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

import inquirer from 'inquirer';
import chalk from 'chalk';

import { createNote } from './note.mjs';
import { getInitialValues } from './helpers.mjs';
import * as Prompts from './prompts.mjs';
import logger from './logger.mjs';

export default async (options) => {
    // Processs the initial values.
    const initialValues = getInitialValues(options);

    // Fetch information.
    const messages = [];
    const { issueNumber } = await inquirer.prompt([
        Prompts.getIssuePrompt(),
    ], initialValues);

    let selection = {};
    let notePath;
    do {
        selection = {};
        selection = await inquirer.prompt([
            Prompts.getComponentsPrompt(),
            Prompts.getTypePrompt(),
            Prompts.getMessagePromptInput(),
        ], initialValues);
        if (selection.message === '') {
            selection = Object.assign(
                selection,
                await inquirer.prompt([
                    Prompts.getMessagePromptEditor(),
                ]),
            );
        }

            logger.info(`
    Creating upgrade note with the following options:

    - Issue:     ${chalk.bold(issueNumber)}
    - Component: ${chalk.bold(selection.components)}
    - Type:      ${chalk.bold(selection.type)}
    - Message:
    ${chalk.bold(selection.message)}
`);

        messages.push({
            components: [selection.components],
            type: selection.type,
            message: selection.message,
        });

        // Save the note so far.
        if (notePath) {
            await createNote(issueNumber, messages, notePath);
            logger.info(`Updated note at: ${chalk.underline(chalk.bold(notePath))}`);
        } else {
            notePath = await createNote(issueNumber, messages);
            logger.info(`Note created at: ${chalk.underline(chalk.bold(notePath))}`);
        }

        selection = Object.assign(
            selection,
            await inquirer.prompt([
                Prompts.getAddAnotherPrompt(),
            ], initialValues),
        );
    } while (selection.addAnother);
};
