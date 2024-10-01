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

import chalk from 'chalk';
import { getAllComponents } from './components.mjs';
import { getCombinedNotesByComponent, deleteAllNotes } from './note.mjs';
import { getNoteName } from './noteTypes.mjs';
import { writeFile, readFile, unlink } from 'fs/promises';
import { join as joinPath } from 'path';
import logger from './logger.mjs';
import { getCurrentVersion } from './helpers.mjs';

/**
 * Helper to fetch the current notes from a file.
 *
 * @param {string} file
 * @returns {Promise<string>}
 */
const getCurrentNotes = async (file) => {
    try {
        return await readFile(file, 'utf8');
    } catch (error) {
        return null;
    }
}

/**
 * Update the UPGRADING.md file.
 *
 * @param {string} upgradeNotes
 * @param {Object} options
 * @param {boolean} options.deleteNotes
 * @returns {Promise<void>}
 */
const updateUpgradeNotes = async (upgradeNotes, options) => {
    const fileName = 'UPGRADING.md';
    // Write the notes to a file.
    logger.info(`Writing notes to ${chalk.underline(chalk.bold(fileName))}`);
    // Prepend to the existing file.
    const existingContent = await getCurrentNotes(fileName);
    if (existingContent) {
        await writeFile(fileName, getUpdatedNotes(existingContent, upgradeNotes));
    } else {
        // This should not normally happen.
        await writeFile(fileName, upgradeNotes);
    }

    if (options.deleteNotes) {
        logger.warn(`>>> Deleting all notes <<<`)
        // Delete the notes.
        deleteAllNotes();
    }
};

/**
 * Create the current summary notes.
 *
 * @param {string} upgradeNotes
 * @returns {Promise<void>}
 */
const createCurrentSummary = async (upgradeNotes) => {
    const fileName = 'UPGRADING-CURRENT.md';
    const notes = `# Moodle upgrade notes\n\n${upgradeNotes}`;
    await writeFile(fileName, notes);

    logger.info(`Running upgrade notes written to ${chalk.underline(chalk.bold(fileName))}`);
};

/**
 * Get the indexes of the lines that contain the version headings.
 *
 * @param {array<string>} lines
 * @returns {array<object>}
 */
const getVersionLineIndexes = (lines) => {
    const h2Indexes = [];
    lines.forEach((line, index) => {
        const matches = line.match(/^##\s(?<version>.*)$/);
        if (matches) {
            h2Indexes.push({
                index,
                line,
                version: matches.groups.version,
            });
        }
    });

    return h2Indexes;
};

/**
 * Find the index of the Unreleased heading.
 *
 * @param {array<object>} versionHeadings
 * @returns {number}
 */
const findUnreleasedHeadingIndex = (versionHeadings) => versionHeadings.findIndex((heading) => {
    if (heading.version === 'Unreleased') {
        // Used if version cannot be guessed.
        return true;
    }

    if (heading.version.endsWith('+')) {
        // Weekly release for a stable branch.
        return true;
    }

    if (heading.version.match(/beta|rc\d/)) {
        // Beta and RC rolls are treated as weeklies.
        return true;
    }

    if (heading.version.endsWith('dev')) {
        // Development version.
        return true;
    }

    return false;
});

/**
 * Get the before and after content, to facilitate replacing any existing Unreleased notes.
 *
 * @param {array<string>} lines
 * @returns {Object} {beforeContent: string, afterContent: string}
 */
const getBeforeAndAfterContent = (lines) => {
    const existingLines = lines.split('\n');
    const versionHeadings = getVersionLineIndexes(existingLines);

    if (versionHeadings.length > 0) {
        const unreleasedHeadingIndex = findUnreleasedHeadingIndex(versionHeadings);
        if (unreleasedHeadingIndex !== -1) {
            const beforeContent = existingLines.slice(0, versionHeadings[unreleasedHeadingIndex].index).join('\n');
            if (versionHeadings.length > unreleasedHeadingIndex + 1) {
                const afterContent = existingLines.slice(versionHeadings[unreleasedHeadingIndex + 1].index).join('\n');
                return {
                    beforeContent,
                    afterContent,
                };
            }
            return {
                beforeContent,
                afterContent: '',
            };
        }

        return {
            beforeContent: existingLines.slice(0, versionHeadings[0].index).join('\n'),
            afterContent: existingLines.slice(versionHeadings[0].index).join('\n'),
        };
    }

    return {
        beforeContent: existingLines.join('\n'),
        afterContent: '',
    }
};

/**
 * Get the notes for the component.
 *
 * @param {string} types
 * @param {Number} headingLevel
 * @returns {string}
 */
const getNotesForComponent = (types, headingLevel) => {
    let upgradeNotes = '';
    Object.entries(types).forEach(([type, notes]) => {
        upgradeNotes += '#'.repeat(headingLevel);
        upgradeNotes += ` ${getNoteName(type)}\n\n`;
        notes.forEach(({ message, issueNumber }) => {
            // Split the message into lines, removing empty lines.
            const messageLines = message
                .split('\n')
                // Remove empty lines between tables, and list entries, but not after lists.
                .filter((line, index, lines) => {
                    if (line.trim().length === 0) {
                        // This line is empty.

                        // If it's the first line in the file, remove it.
                        if (index === 0) {
                            return false;
                        }

                        // This is the last line in the file, remove it.
                        if (index === lines.length - 1) {
                            return false;
                        }

                        // If the previous line relates to a table, remove this line.
                        if (lines[index - 1].match(/^\s*\|/)) {
                            return false;
                        }

                        // If the next line is also empty, do not remove this line.
                        if (lines[index + 1].trim().length === 0) {
                            return true;
                        }

                        // Do not remove the line if the previous line was a list item.
                        if (lines[index - 1].match(/^\s*[-*]\s/)) {
                            return true;
                        }

                        if (lines[index - 1].match(/^\s*\d+\.\s/)) {
                            return true;
                        }

                        // Preserve all other empty lines by default.
                        return true;
                    }

                    // Keep any line which has content.
                    return true;
                });


            const firstLine = messageLines.shift().trim();
            upgradeNotes += `- ${firstLine}\n`;

            messageLines
                .forEach((line) => {
                    upgradeNotes += `  ${line}`.trimEnd() + `\n`;
                });
            upgradeNotes += `\n  For more information see [${issueNumber}](https://tracker.moodle.org/browse/${issueNumber})\n`;
        });
        upgradeNotes += '\n';
    });

    return upgradeNotes;
};

/**
 * Get the updated notes mixed with existing content.
 *
 * @param {string} existingContent
 * @param {string} upgradeNotes
 */
const getUpdatedNotes = (existingContent, upgradeNotes) => {
    const { beforeContent, afterContent } = getBeforeAndAfterContent(existingContent);
    const newContent = `${beforeContent}\n${upgradeNotes}\n${afterContent}`
        .split('\n')
        .filter((line, index, lines) => {
            if (line === '' && lines[index - 1] === '') {
                // Remove multiple consecutive empty lines.
                return false;
            }
            return true;
        })
        .join('\n');

    return newContent;
};

/**
 * Update the notes for each component.
 */
const updateComponentNotes = (
    notes,
    version,
    notesFileName = 'UPGRADING.md',
    removeEmpty = false,
) => {
    return getAllComponents().map(async (component) => {
        logger.verbose(`Updating notes for ${component.name} into ${component.path}`);
        const fileName = joinPath(component.path, notesFileName);

        const existingContent = await getCurrentNotes(fileName);

        if (!existingContent) {
            if (!notes[component.value]) {
                // No existing notes, and no new notes to add.
                return;
            }
        } else {
            if (!notes[component.value]) {
                // There is existing content, but nothing to add.
                if (removeEmpty) {
                    logger.verbose(`Removing empty notes file ${fileName}`);
                    await unlink(fileName);
                }
                return;
            }
        }

        const componentNotes = notes[component.value];
        let upgradeNotes = `## ${version}\n\n`;
        upgradeNotes += getNotesForComponent(componentNotes, 3);

        if (existingContent) {
            await writeFile(fileName, getUpdatedNotes(existingContent, upgradeNotes));
        } else {
            await writeFile(
                fileName,
                `# ${component.name} Upgrade notes\n\n${upgradeNotes}`,
            );
        }
    });
}

/**
 * Generate the upgrade notes for a new release.
 *
 * @param {string|undefined} version
 * @param {Object} options
 * @param {boolean} options.generateUpgradeNotes
 * @param {boolean} options.deleteNotes
 * @returns {Promise<void>}
 */
export default async (version, options = {}) => {
    const notes = await getCombinedNotesByComponent();

    if (Object.keys(notes).length === 0) {
        logger.warn('No notes to generate');
        return;
    }

    if (!version) {
        version = await getCurrentVersion();
    }

    // Generate the upgrade notes for this release.
    // We have
    // - a title with the release name
    // - the change types
    // - which contain the components
    // - which document each change
    let upgradeNotes = `## ${version}\n\n`;

    Object.entries(notes).forEach(([component, types]) => {
        upgradeNotes += `### ${component}\n\n`;
        upgradeNotes += getNotesForComponent(types, 4);
    });

    await Promise.all([
        createCurrentSummary(upgradeNotes),
        ...updateComponentNotes(notes, version, 'UPGRADING-CURRENT.md', true),
    ]);
    if (options.generateUpgradeNotes) {
        await Promise.all(updateComponentNotes(notes, version));
        await updateUpgradeNotes(upgradeNotes, options);
    }
};
