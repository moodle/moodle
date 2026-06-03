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
/* jshint node: true, browser: false */
/* eslint-env node */
// @ts-nocheck

/**
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const configuration = {
    compilerOptions: {
        baseUrl: ".",
        paths: {
        },
        target: "es2015",
        allowSyntheticDefaultImports: false,
    },
    exclude: [
        "node_modules",
    ],
    include: [
        'lib/bundles/**/*',
        'node_modules/@types/**/*.d.ts',
    ],
};

/**
 * Generate jsconfig.json with AMD module path aliases for all components.
 *
 * @param {object} grunt The Grunt instance.
 */
const generateJsconfig = (grunt) => {
    const jsconfigData = Object.assign({}, configuration);
    const path = require('path');

    const {fetchComponentData} = require(path.join(process.cwd(), '.grunt', 'components.js'));

    const componentData = fetchComponentData().components;
    for (const [thisPath, component] of Object.entries(componentData)) {
        jsconfigData.compilerOptions.paths[`${component}/*`] = [`${thisPath}/amd/src/*`];
        jsconfigData.include.push(`${thisPath}/amd/src/**/*`);
    }

    grunt.file.write('jsconfig.json', JSON.stringify(jsconfigData, null, "  ") + "\n");
    grunt.log.write('✓ Generating jsconfig.json\n');
};

/**
 * Generate tsconfig.aliases.json with TypeScript path aliases for all ESM components.
 *
 * @returns {Promise<void>}
 */
const generateTsAliases = async() => {
    const {generateAliases} = await import('../../.esbuild/generate-aliases.mjs');
    generateAliases();
};

module.exports = (grunt) => {
    const handler = async function() {
        const done = this.async();

        try {
            generateJsconfig(grunt);
            await generateTsAliases();
            done();
        } catch (err) {
            grunt.log.error(err.message);
            done(false);
        }
    };
    grunt.registerTask('jsconfig', 'Generate a jsconfig configuration compatible with the LSP', handler);
};
