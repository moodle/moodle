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

/** @var {Object} A list of subsystems in Moodle */
const componentData = {};

/**
 * Load details of all moodle modules.
 *
 * @returns {object}
 */
const fetchComponentData = () => {
    const fs = require('fs');
    const path = require('path');
    const glob = require('glob');
    const gruntFilePath = process.cwd();

    if (!Object.entries(componentData).length) {
        componentData.subsystems = {};
        componentData.pathList = [];
        componentData.components = {};
        componentData.standardComponents = {};

        // Fetch the component definiitions from the distributed JSON file.
        const components = JSON.parse(fs.readFileSync(`${gruntFilePath}/lib/components.json`));
        const pluginData = JSON.parse(fs.readFileSync(`${gruntFilePath}/lib/plugins.json`));

        componentData.pluginTypes = components.plugintypes;

        const standardPlugins = Object.entries(pluginData.standard).map(
            ([pluginType, pluginNames]) => {
                return pluginNames.map(pluginName => `${pluginType}_${pluginName}`);
            }
        ).reduce((acc, val) => acc.concat(val), []);

        // Build the list of moodle subsystems.
        componentData.subsystems.lib = 'core';
        componentData.pathList.push(process.cwd() + path.sep + 'lib');
        for (const [component, thisPath] of Object.entries(components.subsystems)) {
            if (thisPath) {
                // Prefix "core_" to the front of the subsystems.
                componentData.subsystems[thisPath] = `core_${component}`;
                componentData.pathList.push(process.cwd() + path.sep + thisPath);
            }
        }

        // The list of components includes the list of subsystems.
        componentData.components = {...componentData.subsystems};

        const subpluginAdder = (subpluginType, subpluginTypePath) => {
            glob.sync(`${subpluginTypePath}/*/version.php`).forEach(versionPath => {
                const componentPath = fs.realpathSync(path.dirname(versionPath));
                const componentName = path.basename(componentPath);
                const frankenstyleName = `${subpluginType}_${componentName}`;

                componentData.components[`${subpluginTypePath}/${componentName}`] = frankenstyleName;
                componentData.pathList.push(componentPath);
            });
        };

        // Go through each of the plugintypes.
        Object.entries(components.plugintypes).forEach(([pluginType, pluginTypePath]) => {
            // We don't allow any code in this place..?
            glob.sync(`${pluginTypePath}/*/version.php`).forEach(versionPath => {
                const componentPath = fs.realpathSync(path.dirname(versionPath));
                const componentName = path.basename(componentPath);
                const frankenstyleName = `${pluginType}_${componentName}`;
                componentData.components[`${pluginTypePath}/${componentName}`] = frankenstyleName;
                componentData.pathList.push(componentPath);

                // Look for any subplugins.
                const subPluginConfigurationFile = `${componentPath}/db/subplugins.json`;
                if (fs.existsSync(subPluginConfigurationFile)) {
                    const subpluginList = JSON.parse(fs.readFileSync(fs.realpathSync(subPluginConfigurationFile)));

                    if (subpluginList.subplugintypes) {
                        Object.entries(subpluginList.subplugintypes).forEach(([subpluginType, subpluginTypePath]) => {
                            subpluginAdder(
                                subpluginType,
                                `${pluginTypePath}/${componentName}/${subpluginTypePath}`
                            );
                        });
                    } else if (subpluginList.plugintypes) {
                        Object.entries(subpluginList.plugintypes).forEach(([subpluginType, subpluginTypePath]) => {
                            subpluginAdder(subpluginType, subpluginTypePath);
                        });
                    }
                }
            });
        });


        // Create a list of the standard subsystem and plugins.
        componentData.standardComponents = Object.fromEntries(
            Object.entries(componentData.components).filter(([, name]) => {
                if (name === 'core' || name.startsWith('core_')) {
                    return true;
                }
                return standardPlugins.indexOf(name) !== -1;
            })
        );

        componentData.componentMapping = Object.fromEntries(
            Object.entries(componentData.components).map(([path, name]) => [name, path])
        );
    }

    return componentData;
};

/**
 * Get the list of component paths.
 *
 * @param   {string} relativeTo
 * @returns {array}
 */
const getComponentPaths = (relativeTo = '') => fetchComponentData().pathList.map(componentPath => {
    return componentPath.replace(relativeTo, '');
});

/**
 * Get the list of paths to build AMD sources.
 *
 * @returns {Array}
 */
const getAmdSrcGlobList = () => {
    const globList = [];
    fetchComponentData().pathList.forEach(componentPath => {
        globList.push(`${componentPath}/amd/src/*.js`);
        globList.push(`${componentPath}/amd/src/**/*.js`);
    });

    return globList;
};

/**
 * Get the list of paths to build YUI sources.
 *
 * @param {String} relativeTo
 * @returns {Array}
 */
const getYuiSrcGlobList = relativeTo => {
    const globList = [];
    fetchComponentData().pathList.forEach(componentPath => {
        const relativeComponentPath = componentPath.replace(relativeTo, '');
        globList.push(`${relativeComponentPath}/yui/src/**/*.js`);
    });

    return globList;
};

/**
 * Get the list of paths to thirdpartylibs.xml.
 *
 * @param {String} relativeTo
 * @returns {Array}
 */
const getThirdPartyLibsList = relativeTo => {
    const fs = require('fs');
    const path = require('path');

    return fetchComponentData().pathList
        .map(componentPath => path.relative(relativeTo, componentPath) + '/thirdpartylibs.xml')
        .map(componentPath => componentPath.replace(/\\/g, '/'))
        .filter(path => fs.existsSync(path))
        .sort();
};

/**
 * Get the list of thirdparty library paths.
 *
 * @returns {array}
 */
const getThirdPartyPaths = () => {
    const DOMParser = require('@xmldom/xmldom').DOMParser;
    const fs = require('fs');
    const path = require('path');
    const xpath = require('xpath');

    const thirdpartyfiles = getThirdPartyLibsList(fs.realpathSync('./'));
    const libs = ['node_modules/', 'vendor/'];

    const addLibToList = lib => {
        if (!lib.match('\\*') && fs.statSync(lib).isDirectory()) {
            // Ensure trailing slash on dirs.
            lib = lib.replace(/\/?$/, '/');
        }

        // Look for duplicate paths before adding to array.
        if (libs.indexOf(lib) === -1) {
            libs.push(lib);
        }
    };

    thirdpartyfiles.forEach(function(file) {
        const dirname = path.dirname(file);

        const xmlContent = fs.readFileSync(file, 'utf8');
        const doc = new DOMParser().parseFromString(xmlContent);
        const nodes = xpath.select("/libraries/library/location/text()", doc);

        nodes.forEach(function(node) {
            let lib = path.posix.join(dirname, node.toString());
            addLibToList(lib);
        });
    });

    return libs;

};

/**
 * Find the name of the component matching the specified path.
 *
 * @param {String} path
 * @returns {String|null} Name of matching component.
 */
const getComponentFromPath = path => {
    const componentList = fetchComponentData().components;

    if (componentList.hasOwnProperty(path)) {
        return componentList[path];
    }

    return null;
};

/**
 * Check whether the supplied path, relative to the Gruntfile.js, is in a known component.
 *
 * @param {String} checkPath The path to check. This can be with either Windows, or Linux directory separators.
 * @returns {String|null}
 */
const getOwningComponentDirectory = checkPath => {
    const path = require('path');

    // Fetch all components into a reverse sorted array.
    // This ensures that components which are within the directory of another component match first.
    const pathList = Object.keys(fetchComponentData().components).sort().reverse();
    for (const componentPath of pathList) {
        // If the componentPath is the directory being checked, it will be empty.
        // If the componentPath is a parent of the directory being checked, the relative directory will not start with ..
        if (!path.relative(componentPath, checkPath).startsWith('..')) {
            return componentPath;
        }
    }

    return null;
};

/**
 * Get the latest tag in a remote GitHub repository.
 *
 * @param {string} url The remote repository.
 * @returns {Array}
 */
const getRepositoryTags = async(url) => {
    const gtr = require('git-tags-remote');
    try {
        const tags = await gtr.get(url);
        if (tags !== undefined) {
            return tags;
        }
    } catch (error) {
        return [];
    }
    return [];
};

/**
 * Get the list of thirdparty libraries that could be upgraded.
 *
 * @returns {Array}
 */
const getThirdPartyLibsUpgradable = async() => {
    const libraries = getThirdPartyLibsData().filter((library) => !!library.repository);
    const upgradableLibraries = [];
    const versionCompare = (a, b) => {
        if (a === b) {
            return 0;
        }

        const aParts = a.split('.');
        const bParts = b.split('.');

        for (let i = 0; i < Math.min(aParts.length, bParts.length); i++) {
            const aPart = parseInt(aParts[i], 10);
            const bPart = parseInt(bParts[i], 10);
            if (aPart > bPart) {
                // 1.1.0 > 1.0.9
                return 1;
            } else if (aPart < bPart) {
                // 1.0.9 < 1.1.0
                return -1;
            } else {
                // Same version.
                continue;
            }
        }

        if (aParts.length > bParts.length) {
            // 1.0.1 > 1.0
            return 1;
        }

        // 1.0 < 1.0.1
        return -1;
    };

    for (let library of libraries) {
        upgradableLibraries.push(
            getRepositoryTags(library.repository).then((tagMap) => {
                library.version = library.version.replace(/^v/, '');
                const currentVersion = library.version.replace(/moodle-/, '');
                const currentMajorVersion = library.version.split('.')[0];
                const tags = [...tagMap]
                    .map((tagData) => tagData[0])
                    .filter((tag) => !tag.match(/(alpha|beta|rc)/))
                    .map((tag) => tag.replace(/^v/, ''))
                    .sort((a, b) => versionCompare(b, a));
                if (!tags.length) {
                    library.warning = "Unable to find any comparable tags.";
                    return library;
                }

                library.latestVersion = tags[0];
                tags.some((tag) => {
                    if (!tag) {
                        return false;
                    }

                    // See if the version part matches.
                    const majorVersion = tag.split('.')[0];
                    if (majorVersion === currentMajorVersion) {
                        library.latestSameMajorVersion = tag;
                        return true;
                    }
                    return false;
                });


                if (versionCompare(currentVersion, library.latestVersion) > 0) {
                    // Moodle somehow has a newer version than the latest version.
                    library.warning = `Newer version found: ${currentVersion} > ${library.latestVersion} for ${library.name}`;
                    return library;
                }


                if (library.version !== library.latestVersion) {
                    // Delete version and add it again at the end of the array. That way, current and new will stay closer.
                    delete library.version;
                    library.version = currentVersion;
                    return library;
                }
                return null;
            })
        );
    }

    return (await Promise.all(upgradableLibraries)).filter((library) => !!library);
};

/**
 * Get the list of thirdparty libraries.
 *
 * @returns {Array}
 */
const getThirdPartyLibsData = () => {
    const DOMParser = require('@xmldom/xmldom').DOMParser;
    const fs = require('fs');
    const xpath = require('xpath');
    const path = require('path');

    const libraryList = [];
    const libraryFields = [
        'location',
        'name',
        'version',
        'repository',
    ];

    const thirdpartyfiles = getThirdPartyLibsList(fs.realpathSync('./'));
    thirdpartyfiles.forEach(function(libraryPath) {
        const xmlContent = fs.readFileSync(libraryPath, 'utf8');
        const doc = new DOMParser().parseFromString(xmlContent);
        const libraries = xpath.select("/libraries/library", doc);
        for (const library of libraries) {
            const libraryData = [];
            for (const field of libraryFields) {
                libraryData[field] = xpath.select(`${field}/text()`, library)?.toString();
            }
            libraryData.location = path.join(path.dirname(libraryPath), libraryData.location);
            libraryList.push(libraryData);
        }
    });

    return libraryList.sort((a, b) => a.location.localeCompare(b.location));
};

module.exports = {
    fetchComponentData,
    getAmdSrcGlobList,
    getComponentFromPath,
    getComponentPaths,
    getOwningComponentDirectory,
    getYuiSrcGlobList,
    getThirdPartyLibsList,
    getThirdPartyPaths,
    getThirdPartyLibsUpgradable,
};
