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
    /**
     * Register any stylelint tasks.
     *
     * @param {Object} grunt
     * @param {Array} files
     * @param {String} fullRunDir
     */
    const registerStyleLintTasks = () => {
        const files = grunt.moodleEnv.files;
        const fullRunDir = grunt.moodleEnv.fullRunDir;
        const inComponent = grunt.moodleEnv.inComponent;
        const inTheme = grunt.moodleEnv.inTheme;

        const getCssConfigForFiles = files => {
            return {
                stylelint: {
                    css: {
                        // Use a fully-qualified path.
                        src: files,
                        options: {
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
                        options: {syntax: 'scss'},
                        src: files,
                    },
                },
            };
        };

        let hasCss = false;
        let hasScss = false;

        if (files) {
            // Specific files were passed. Just set them up.
            grunt.config.merge(getCssConfigForFiles(files));
            hasCss = true;

            grunt.config.merge(getScssConfigForFiles(files));
            hasScss = true;
        } else {
            // The stylelint system does not handle the case where there was no file to lint.
            // Check whether there are any files to lint in the current directory.
            const glob = require('glob');

            // CSS exists in:
            // [component]/styles.css
            // [theme_pluginname]/css

            if (inComponent) {
                hasScss = false;
                if (inTheme) {
                    const scssSrc = [];
                    glob.sync(`${fullRunDir}/**/*.scss`).forEach(path => scssSrc.push(path));

                    if (scssSrc.length) {
                        grunt.config.merge(getScssConfigForFiles(scssSrc));
                        hasScss = true;
                    }
                }
            } else {
                const scssSrc = [];
                glob.sync(`${fullRunDir}/**/*.scss`).forEach(path => scssSrc.push(path));

                if (scssSrc.length) {
                    grunt.config.merge(getScssConfigForFiles(scssSrc));
                    hasScss = true;
                }
            }

            const cssSrc = [];
            glob.sync(`${fullRunDir}/**/*.css`).forEach(path => cssSrc.push(path));

            if (cssSrc.length) {
                grunt.config.merge(getCssConfigForFiles(cssSrc));
                hasCss = true;
            }
        }

        const scssTasks = ['sass'];
        if (hasScss) {
            scssTasks.unshift('stylelint:scss');
        }
        grunt.registerTask('scss', scssTasks);

        const cssTasks = [];
        if (hasCss) {
            cssTasks.push('stylelint:css');
        }
        grunt.registerTask('rawcss', cssTasks);

        grunt.registerTask('css', ['scss', 'rawcss']);
    };

    // Register CSS tasks.
    grunt.loadNpmTasks('grunt-stylelint');

    grunt.config.merge({
        watch: {
            rawcss: {
                files: [
                    '**/*.css',
                ],
                excludes: [
                    '**/moodle.css',
                    '**/editor.css',
                ],
                tasks: ['rawcss']
            },
        },
    });

    registerStyleLintTasks();
};
