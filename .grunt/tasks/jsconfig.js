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
    include: [],
};

module.exports = (grunt) => {
    const handler = () => {
        const jsconfigData = Object.assign({}, configuration);

        const path = require('path');
        const {fetchComponentData} = require(path.join(process.cwd(), '.grunt', 'components.js'));

        const componentData = fetchComponentData().components;
        for (const [thisPath, component] of Object.entries(componentData)) {
            jsconfigData.compilerOptions.paths[`${component}/*`] = [`public/${thisPath}/amd/src/*`];
            jsconfigData.include.push(`public/${thisPath}/amd/src/**/*`);
        }

        grunt.file.write('jsconfig.json', JSON.stringify(jsconfigData, null, "  ") + "\n");
    };
    grunt.registerTask('jsconfig', 'Generate a jsconfig configuration compatible with the LSP', handler);
};
