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
 * @copyright  2021 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = grunt => {

    const getCssConfigForFiles = files => {
        return {
            stylelint: {
                css: {
                    // Use a fully-qualified path.
                    src: files,
                    options: {
                        quietDeprecationWarnings: true,
                        configOverrides: {
                            rules: {
                                // These rules have to be disabled in .stylelintrc for scss compat.
                                "at-rule-no-unknown": true,
                            }
                        }
                    }
                },
            },
        };
    };

    const getScssConfigForFiles = files => {
        return {
            stylelint: {
                scss: {
                    options: {
                        quietDeprecationWarnings: true,
                        customSyntax: 'postcss-scss',
                    },
                    src: files,
                },
            },
        };
    };

    /**
     * Register any stylelint tasks.
     *
     * @param {Object} grunt
     * @param {Array} files
     * @param {String} fullRunDir
     */
    const registerStyleLintTasks = () => {
        const glob = require('glob');

        // The stylelinters do not handle the case where a configuration was provided but no files were included.
        // Keep track of whether any files were found.
        let hasCss = false;
        let hasScss = false;

        // The stylelint processors do not take a path argument. They always check all provided values.
        // As a result we must check through each glob and determine if any files match the current directory.
        const scssFiles = [];
        const cssFiles = [];

        const requestedFiles = grunt.moodleEnv.files;
        if (requestedFiles) {
            // Grunt was called with a files argument.
            // Check whether each of the requested files matches either the CSS or SCSS source file list.

            requestedFiles.forEach(changedFilePath => {
                let matchesGlob;

                // Check whether this watched path matches any watched SCSS file.
                matchesGlob = grunt.moodleEnv.scssSrc.some(watchedPathGlob => {
                    return glob.sync(watchedPathGlob).indexOf(changedFilePath) !== -1;
                });
                if (matchesGlob) {
                    scssFiles.push(changedFilePath);
                    hasScss = true;
                }

                // Check whether this watched path matches any watched CSS file.
                matchesGlob = grunt.moodleEnv.cssSrc.some(watchedPathGlob => {
                    return glob.sync(watchedPathGlob).indexOf(changedFilePath) !== -1;
                });
                if (matchesGlob) {
                    cssFiles.push(changedFilePath);
                    hasCss = true;
                }
            });
        } else {
            // Grunt was called without a list of files.
            // The start directory (runDir) may be a child dir of the project.
            // Check each scssSrc file to see if it's in the start directory.
            // This means that we can lint just mod/*/styles.css if started in the mod directory.

            grunt.moodleEnv.scssSrc.forEach(path => {
                if (path.startsWith(grunt.moodleEnv.runDir)) {
                    scssFiles.push(path);
                    hasScss = true;
                }
            });

            grunt.moodleEnv.cssSrc.forEach(path => {
                if (path.startsWith(grunt.moodleEnv.runDir)) {
                    cssFiles.push(path);
                    hasCss = true;
                }
            });
        }

        // Register the tasks.
        const scssTasks = ['sass'];
        if (hasScss) {
            grunt.config.merge(getScssConfigForFiles(scssFiles));
            scssTasks.unshift('stylelint:scss');
        }
        scssTasks.unshift('ignorefiles');

        const cssTasks = ['ignorefiles'];
        if (hasCss) {
            grunt.config.merge(getCssConfigForFiles(cssFiles));
            cssTasks.push('stylelint:css');
        }

        // The tasks must be registered, even if empty to ensure a consistent command list.
        // They jsut won't run anything.
        grunt.registerTask('scss', scssTasks);
        grunt.registerTask('rawcss', cssTasks);
    };

    // Register CSS tasks.
    grunt.loadNpmTasks('grunt-stylelint');

    // Register the style lint tasks.
    registerStyleLintTasks();
    grunt.registerTask('css', ['scss', 'rawcss']);

    const getCoreThemeMatches = () => {
        const scssMatch = 'scss/**/*.scss';

        if (grunt.moodleEnv.inTheme) {
            return [scssMatch];
        }

        if (grunt.moodleEnv.runDir.startsWith('theme')) {
            return [`*/${scssMatch}`];
        }

        return [`theme/*/${scssMatch}`];
    };

    // Add the watch configuration for rawcss, and scss.
    grunt.config.merge({
        watch: {
            rawcss: {
                files: [
                    '**/*.css',
                ],
                excludes: [
                    '**/moodle.css',
                    '**/editor.css',
                    'jsdoc/styles/*.css',
                ],
                tasks: ['rawcss']
            },
            scss: {
                files: getCoreThemeMatches(),
                tasks: ['scss']
            },
        },
    });
};
