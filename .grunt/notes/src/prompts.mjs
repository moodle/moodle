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
import SearchList from 'inquirer-search-list';

import { getNoteNames } from './noteTypes.mjs';
import { getAllComponents } from './components.mjs';
import {
    formatComponent,
    formatIssueNumber,
    validateComponent,
    validateIssueNumber,
 } from './helpers.mjs';

/**
 * A Search List which accepts an initial value.
 */
class SearchListWithInitialValue extends SearchList {
    constructor(options, ...args) {
        super(options, ...args);

        if (options.default) {
            const pointer = this.filterList.findIndex((item) => {
                return item.value === options.default;
            });
            if (pointer > -1) {
                this.pointer = pointer;
            }
        }
    }
}

inquirer.registerPrompt('search-list', SearchListWithInitialValue);

/**
 * Get the issue type prompt.
 *
 * @param {string} defaultData The initially selected value
 * @returns {Object}
 */
export const getTypePrompt = (defaultData) => ({
    default: defaultData,
    type: 'search-list',
    message: 'Type of change',
    name: 'type',
    choices: getNoteNames(),
    validate: (selection) => {
        if (selection.length < 1) {
            return 'You must select at least one type of change';
        }

        return true;
    },
});

/**
 * Get the component prompt.
 *
 * @param {string} [defaultValue='core'] The initally selected value.
 * @returns
 */
export const getComponentsPrompt = (defaultValue) => {
    if (!defaultValue ) {
        defaultValue = 'core';
    }

    return {
        choices: getAllComponents(),
        default: defaultValue,
        type: 'search-list',
        message: 'Component',
        name: 'components',
        validate: validateComponent,
        filter: formatComponent,
    };
};

/**
 * Get the issue number prompt as an inline input.
 *
 * @param {string} defaultData
 * @returns {object}
 */
export const getIssuePrompt = (defaultData) => ({
    default: defaultData,
    type: 'input',
    message: 'Tracker issue number',
    name: 'issueNumber',
    validate: validateIssueNumber,
    filter: formatIssueNumber,
});

/**
 * Get a message prompt.
 *
 * @param {string} defaultData
 * @returns
 */
export const getMessagePromptEditor = (defaultData) => ({
    default: defaultData,
    type: process.stdin.isTTY ? 'editor' : 'input',
    postfix: '.md',
    message: 'Message',
    name: 'message',
    waitUserInput: false,
    validate: (input) => {
        if (!input) {
            return 'You must provide a message';
        }
        return true;
    },
    // Remove any trailing whitespace.
    filter: (input) => input.split('\n').map((line) => line.trimEnd()).join('\n'),
});

/**
 * Get a message prompt.
 *
 * @param {string} defaultData
 * @returns
 */
export const getMessagePromptInput = (defaultData) => ({
    default: defaultData,
    type: 'input',
    message: 'Message (leave empty to use editor)',
    name: 'message',
    filter: (input) => input.trim(),
});

/**
 * Get a prompt to ask the user if they wish to add another entry.
 *
 * @returns {Object}
 */
export const getAddAnotherPrompt = () => ({
    type: 'confirm',
    message: 'Do you want to add another note?',
    default: false,
    name: 'addAnother',
});
