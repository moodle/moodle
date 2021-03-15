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

/**
 * Function to generate the destination for the uglify task
 * (e.g. build/file.min.js). This function will be passed to
 * the rename property of files array when building dynamically:
 * http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
 *
 * @param {String} destPath the current destination
 * @param {String} srcPath the  matched src path
 * @return {String} The rewritten destination path.
 */
const babelRename = function(destPath, srcPath) {
    destPath = srcPath.replace('src', 'build');
    destPath = destPath.replace('.js', '.min.js');
    return destPath;
};

module.exports = grunt => {
    // Load the Ignorefiles tasks.
    require('./ignorefiles')(grunt);

    // Load the Shifter tasks.
    require('./shifter')(grunt);

    // Load ESLint.
    require('./eslint')(grunt);

    // Load JSDoc.
    require('./jsdoc')(grunt);

    const path = require('path');

    // Register JS tasks.
    grunt.registerTask('yui', ['eslint:yui', 'shifter']);
    grunt.registerTask('amd', ['ignorefiles', 'eslint:amd', 'babel']);
    grunt.registerTask('js', ['amd', 'yui']);

    // Register NPM tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Load the Babel tasks and config.
    grunt.loadNpmTasks('grunt-babel');
    grunt.config.merge({
        babel: {
            options: {
                sourceMaps: true,
                comments: false,
                plugins: [
                    'transform-es2015-modules-amd-lazy',
                    'system-import-transformer',
                    // This plugin modifies the Babel transpiling for "export default"
                    // so that if it's used then only the exported value is returned
                    // by the generated AMD module.
                    //
                    // It also adds the Moodle plugin name to the AMD module definition
                    // so that it can be imported as expected in other modules.
                    path.resolve('.grunt/babel-plugin-add-module-to-define.js'),
                    '@babel/plugin-syntax-dynamic-import',
                    '@babel/plugin-syntax-import-meta',
                    ['@babel/plugin-proposal-class-properties', {'loose': false}],
                    '@babel/plugin-proposal-json-strings'
                ],
                presets: [
                    ['minify', {
                        // This minification plugin needs to be disabled because it breaks the
                        // source map generation and causes invalid source maps to be output.
                        simplify: false,
                        builtIns: false
                    }],
                    ['@babel/preset-env', {
                        targets: {
                            browsers: [
                                ">0.25%",
                                "last 2 versions",
                                "not ie <= 10",
                                "not op_mini all",
                                "not Opera > 0",
                                "not dead"
                            ]
                        },
                        modules: false,
                        useBuiltIns: false
                    }]
                ]
            },
            dist: {
                files: [{
                    expand: true,
                    src: grunt.moodleEnv.files ? grunt.moodleEnv.files : grunt.moodleEnv.amdSrc,
                    rename: babelRename
                }]
            }
        },
    });

    grunt.config.merge({
        watch: {
            amd: {
                files: grunt.moodleEnv.inComponent
                    ? ['amd/src/*.js', 'amd/src/**/*.js']
                    : ['**/amd/src/**/*.js'],
                tasks: ['amd']
            },
        },
    });

    // On watch, we dynamically modify config to build only affected files. This
    // method is slightly complicated to deal with multiple changed files at once (copied
    // from the grunt-contrib-watch readme).
    let changedFiles = Object.create(null);
    const onChange = grunt.util._.debounce(function() {
        const files = Object.keys(changedFiles);
        grunt.config('babel.dist.files', [{expand: true, src: files, rename: babelRename}]);
        changedFiles = Object.create(null);
    }, 200);

    grunt.event.on('watch', function(action, filepath) {
        changedFiles[filepath] = action;
        onChange();
    });

    return {
        babelRename,
    };
};
