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
 * This is a babel plugin to add the Moodle module names to the AMD modules
 * as part of the transpiling process.
 *
 * In addition it will also add a return statement for the default export if the
 * module is using default exports. This is a highly specific Moodle thing because
 * we're transpiling to AMD and none of the existing Babel 7 plugins work correctly.
 *
 * This will fix the issue where an ES6 module using "export default Foo" will be
 * transpiled into an AMD module that returns {default: Foo}; Instead it will now
 * just simply return Foo.
 *
 * Note: This means all other named exports in that module are ignored and won't be
 * exported.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";
/* eslint-env node */

module.exports = ({template, types}) => {
    const fs = require('fs');
    const path = require('path');
    const cwd = process.cwd();
    const ComponentList = require(path.resolve('GruntfileComponents.js'));

    /**
     * Search the list of components that match the given file name
     * and return the Moodle component for that file, if found.
     *
     * Throw an exception if no matching component is found.
     *
     * @throws {Error}
     * @param {string} searchFileName The file name to look for.
     * @return {string} Moodle component
     */
    function getModuleNameFromFileName(searchFileName) {
        searchFileName = fs.realpathSync(searchFileName);
        const relativeFileName = searchFileName.replace(`${cwd}${path.sep}`, '').replace(/\\/g, '/');
        const [componentPath, file] = relativeFileName.split('/amd/src/');
        const fileName = file.replace('.js', '');

        // Check subsystems first which require an exact match.
        const componentName = ComponentList.getComponentFromPath(componentPath);
        if (componentName) {
            return `${componentName}/${fileName}`;
        }

        // This matches the previous PHP behaviour that would throw an exception
        // if it couldn't parse an AMD file.
        throw new Error(`Unable to find module name for ${searchFileName} (${componentPath}::${file}}`);
    }

    /**
     * This is heavily inspired by the babel-plugin-add-module-exports plugin.
     * See: https://github.com/59naga/babel-plugin-add-module-exports
     *
     * This is used when we detect a module using "export default Foo;" to make
     * sure the transpiled code just returns Foo directly rather than an object
     * with the default property (i.e. {default: Foo}).
     *
     * Note: This means that we can't support modules that combine named exports
     * with a default export.
     *
     * @param {String} path
     * @param {String} exportObjectName
     */
    function addModuleExportsDefaults(path, exportObjectName) {
        const rootPath = path.findParent(path => {
            return path.key === 'body' || !path.parentPath;
        });

        // HACK: `path.node.body.push` instead of path.pushContainer(due doesn't work in Plugin.post).
        // This is hardcoded to work specifically with AMD.
        rootPath.node.body.push(template(`return ${exportObjectName}.default`)());
    }

    return {
        pre() {
            this.seenDefine = false;
            this.addedReturnForDefaultExport = false;
        },
        visitor: {
            // Plugin ordering is only respected if we visit the "Program" node.
            // See: https://babeljs.io/docs/en/plugins.html#plugin-preset-ordering
            //
            // We require this to run after the other AMD module transformation so
            // let's visit the "Program" node.
            Program: {
                exit(path) {
                    path.traverse({
                        CallExpression(path) {
                            // If we find a "define" function call.
                            if (!this.seenDefine && path.get('callee').isIdentifier({name: 'define'})) {
                                // We only want to modify the first instance of define that we find.
                                this.seenDefine = true;
                                // Get the Moodle component for the file being processed.
                                var moduleName = getModuleNameFromFileName(this.file.opts.filename);
                                // Add the module name as the first argument to the define function.
                                path.node.arguments.unshift(types.stringLiteral(moduleName));
                                // Add a space after the define function in the built file so that previous versions
                                // of Moodle will not try to add the module name to the file when it's being served
                                // by PHP. This forces the regex in PHP to not match for this file.
                                path.node.callee.name = 'define ';
                            }

                            // Check for any Object.defineProperty('exports', 'default') calls.
                            if (!this.addedReturnForDefaultExport && path.get('callee').matchesPattern('Object.defineProperty')) {
                                const [identifier, prop] = path.get('arguments');
                                const objectName = identifier.get('name').node;
                                const propertyName = prop.get('value').node;

                                if ((objectName === 'exports' || objectName === '_exports') && propertyName === 'default') {
                                    addModuleExportsDefaults(path, objectName);
                                    this.addedReturnForDefaultExport = true;
                                }
                            }
                        },
                        AssignmentExpression(path) {
                            // Check for an exports.default assignments.
                            if (
                                !this.addedReturnForDefaultExport &&
                                (
                                    path.get('left').matchesPattern('exports.default') ||
                                    path.get('left').matchesPattern('_exports.default')
                                )
                            ) {
                                const objectName = path.get('left.object.name').node;
                                addModuleExportsDefaults(path, objectName);
                                this.addedReturnForDefaultExport = true;
                            }
                        }
                    }, this);
                }
            }
        }
    };
};
