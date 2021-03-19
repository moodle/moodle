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
    const files = grunt.moodleEnv.files;

    // Project configuration.
    grunt.config.merge({
        eslint: {
            // Even though warnings dont stop the build we don't display warnings by default because
            // at this moment we've got too many core warnings.
            // To display warnings call: grunt eslint --show-lint-warnings
            // To fail on warnings call: grunt eslint --max-lint-warnings=0
            // Also --max-lint-warnings=-1 can be used to display warnings but not fail.
            options: {
                quiet: (!grunt.option('show-lint-warnings')) && (typeof grunt.option('max-lint-warnings') === 'undefined'),
                maxWarnings: ((typeof grunt.option('max-lint-warnings') !== 'undefined') ? grunt.option('max-lint-warnings') : -1)
            },

            // Check AMD src files.
            amd: {src: files ? files : grunt.moodleEnv.amdSrc},

            // Check YUI module source files.
            yui: {src: files ? files : grunt.moodleEnv.yuiSrc},
        },
    });

    grunt.loadNpmTasks('grunt-eslint');

    // On watch, we dynamically modify config to build only affected files. This
    // method is slightly complicated to deal with multiple changed files at once (copied
    // from the grunt-contrib-watch readme).
    let changedFiles = Object.create(null);
    const onChange = grunt.util._.debounce(function() {
        const files = Object.keys(changedFiles);
        grunt.config('eslint.amd.src', files);
        grunt.config('eslint.yui.src', files);
        changedFiles = Object.create(null);
    }, 200);

    grunt.event.on('watch', (action, filepath) => {
        changedFiles[filepath] = action;
        onChange();
    });
};
