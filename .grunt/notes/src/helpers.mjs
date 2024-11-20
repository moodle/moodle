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

import chalk from 'chalk';
import { isStandardComponent, isCommunityComponent, rewritePlugintypeAsSubsystem } from './components.mjs';
import { isValidNoteName } from './noteTypes.mjs';
import logger from './logger.mjs';
import { readFile } from 'fs/promises';

/**
 * Validate an issue number input
 *
 * @param {string} input
 * @returns {string|boolean}
 */
export const validateIssueNumber = (input) => {
    if (!input) {
        return 'You must provide a tracker issue number';
    }

    if (input.match(/^[a-zA-Z]*-\d+$/)) {
        return true;
    }

    if (input.match(/^\d+$/)) {
        return true;
    }

    return 'The issue number was not recognised as a valid issue number';
};

/**
 * Format an issue number input.
 *
 * @param {string} input
 * @returns {string}
 */
export const formatIssueNumber = (input) => {
    if (input.match(/^[a-zA-Z]*-\d+$/)) {
        return input;
    }

    if (input.match(/^\d+$/)) {
        return `MDL-${input}`;
    }

    return input;
};

/**
 * Validate a component.
 *
 * @param {string} input
 * @returns {string|boolean}
 */
export const validateComponent = (input) => {
    if (isStandardComponent(input)) {
        return true;
    }

    if (isCommunityComponent(input)) {
        return 'Currently only core plugins are supported.';
    }

    return 'The component was not recognised as a standard component';
};

export const formatComponent = (input) => {
    if (rewritePlugintypeAsSubsystem(input)) {
        return `core_${input}`;
    }
    return input;
}

/**
 * Get the initial values from the options.
 *
 * @param {object} options
 * @returns {object}
 */
export const getInitialValues = (options) => {
    const initialValues = {};

    const type = getInitialTypeValue(options);
    if (type) {
        initialValues.type = type;
    }

    const issueNumber = getInitialIssueValue(options);
    if (issueNumber) {
        initialValues.issueNumber = issueNumber;
    }

    const component = getInitialComponentValue(options);
    if (component) {
        initialValues.components = component;
    }

    const message = getInitialMessageValue(options);
    if (message) {
        initialValues.message = message
        initialValues.addAnother = false;
    }

    return initialValues;
};

/**
 * Get the initial type value.
 *
 * @param {Object} options
 * @returns {string|undefined}
 */
const getInitialTypeValue = (options) => {
    if (!options.type) {
        return;
    }

    options.type = options.type.trim().toLowerCase();

    if (isValidNoteName(options.type)) {
        return options.type;
    }

    logger.warn(`Note type "${chalk.underline(chalk.red(options.type))}" is not valid.`);
};

/**
 * Get the initial issue number value.
 *
 * @param {Object} options
 * @returns {string|undefined}
 */

const getInitialIssueValue = (options) => {
    if (!options.issue) {
        return;
    }
    options.issue = options.issue.trim().toUpperCase();

    const issueNumberValidated = validateIssueNumber(options.issue);
    if (issueNumberValidated === true) {
        const issueNumber = formatIssueNumber(options.issue);
        if (issueNumber !== options.issue) {
            logger.warn(
                `Issue number "${chalk.underline(chalk.red(options.issue))}" was updated to ` +
                `"${chalk.underline(chalk.green(issueNumber))}"`
            );
        }

        return issueNumber;
    } else {
        logger.warn(`Issue number "${chalk.underline(chalk.red(options.issue))}" is not valid: ${issueNumberValidated}`);
    }
};

/**
 * Get the initial component value.
 *
 * @param {Object} options
 * @returns {string|undefined}
 */
const getInitialComponentValue = (options) => {
    if (!options.component) {
        return;
    }

    options.component = options.component.trim().toLowerCase();
    const componentValidated = validateComponent(options.component);
    if (componentValidated === true) {
        const component = formatComponent(options.component);
        if (component !== options.component) {
            logger.warn(
                `Component "${chalk.underline(chalk.red(options.component))}" was updated to ` +
                `"${chalk.underline(chalk.green(component))}"`
            );
        }

        return component;
    } else {
        logger.warn(`Component "${chalk.underline(chalk.red(options.component))}" is not valid: ${componentValidated}`);
    }
};

/**
 * Get the initial message value.
 *
 * @param {Object} options
 * @returns {string|undefined}
 */

const getInitialMessageValue = (options) => {
    if (!options.message) {
        return;
    }

    return options.message.trim();
};

/**
 * Get the current version from the project /version.php file.
 *
 * @returns {Promise<string>}
 */
export const getCurrentVersion = async () => {
    const versionRegex = new RegExp(/^ *\$release *= *['\"](?<release>[^ \+]+\+?) *\(Build:.*/m);
    try {
        const versionFile = await readFile('version.php', 'utf8');
        const match = versionFile.match(versionRegex);
        if (match) {
            return match.groups.release;
        }
    } catch(error) {
        logger.error('Unable to read the version file');
    }

    return "Unreleased";
}
