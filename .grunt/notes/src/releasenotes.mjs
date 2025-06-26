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

import { getAllComponents } from './components.mjs';
import { getCombinedNotesByComponent } from './note.mjs';
import logger from './logger.mjs';
import { existsSync } from 'fs';

/**
 * Generate links to component-specific upgrade notes for each component.
 *
 * @param {string|undefined} tag The git tag to link to
 * @returns {Promise<void>}
 */
export default async (tag) => {
    const notes = await getCombinedNotesByComponent();

    if (Object.keys(notes).length === 0) {
        logger.warn('No notes to generate');
        return;
    }

    if (!tag) {
        logger.error('No tag provided');
        return;
    }

    // Generate the links for the components which have upgrade notes in this release.
    let upgradeNotes = `### Component API updates\n<!--cspell: disable -->\n\n`;

    const componentList = Object.fromEntries(
        Object.values(getAllComponents()).map(({path, value}) => [value, path]),
    );
    Object.entries(notes)
    .map(([component]) => ({
        component,
        componentPath: componentList[component],
    }))
    .filter(({ componentPath }) => existsSync(`./${componentPath}/UPGRADING.md`))
    .forEach(({component, componentPath}) => {
        upgradeNotes += `- [${component}](https://github.com/moodle/moodle/blob/${tag}/${componentPath}/UPGRADING.md)\n`;
    });

    console.log(upgradeNotes);
};
