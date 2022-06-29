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

/**
 * Helper functions for working with Moodle component names, directories, and sources.
 *
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";
/* eslint-env node */

// Do not include any plugins as stanard.
const plugins = [];

plugins.push('plugins/markdown');

/**
 * Get the source configuration.
 *
 * @return {Object}
 */
const getSource = () => {
    const glob = require('glob');
    const path = require('path');
    const ComponentList = require(path.resolve('.grunt/components.js'));
    const thirdPartyPaths = ComponentList.getThirdPartyPaths();

    const source = {
        include: [],
        includePattern: ".+\\.js$",
    };

    let includeList = [];

    ComponentList.getAmdSrcGlobList().forEach(async pattern => {
        includeList.push(...glob.sync(pattern));
    });

    const cwdLength = process.cwd().length + 1;
    includeList.forEach(path => {
        if (source.include.indexOf(path) !== -1) {
            // Ensure no duplicates.
            return;
        }

        const relPath = path.substring(cwdLength);
        if (thirdPartyPaths.indexOf(relPath) !== -1) {
            return;
        }

        source.include.push(path);
    });

    source.include.push('.grunt/jsdoc/README.md');
    return source;
};

const tags = {
    // Allow the use of unknown tags.
    // We have a lot of legacy uses of these.
    allowUnknownTags: true,

    // We make use of jsdoc and closure dictionaries as standard.
    dictionaries: [
        'jsdoc',
        'closure',
    ],
};

// Template configuraiton.
const templates = {
    cleverLinks: false,
    monospaceLinks: false,
};

module.exports = {
    opts: {
        destination: "./jsdoc/",
        template: "node_modules/docdash",
    },
    plugins,
    recurseDepth: 10,
    source: getSource(),
    sourceType: 'module',
    tags,
    templates,
    docdash: {
        collapse: true,
        search: true,
        sort: true,
        sectionOrder: [
            "Namespaces",
            "Modules",
            "Events",
            "Classes",
            "Externals",
            "Mixins",
            "Tutorials",
            "Interfaces"
        ],
        "menu": {
            "Developer Docs": {
                href: "https://docs.moodle.org/dev",
                target: "_blank",
                "class": "menu-item",
                id: "devdocs"
            },
            "MDN Docs": {
                href: "https://developer.mozilla.org/en-US/docs/Web/JavaScript",
                target: "_blank",
                "class": "menu-item",
                id: "mdndocs",
            },
        },
        typedefs: true,
    },
};
