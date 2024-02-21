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
 * Function to generate the destination for the minification task
 * (e.g. build/file.min.js). This function will be passed to
 * the rename property of files array when building dynamically:
 * http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
 *
 * @param {String} destPath the current destination
 * @param {String} srcPath the  matched src path
 * @return {String} The rewritten destination path.
 */
const babelRename = function(destPath, srcPath) {
    destPath = srcPath.replace(`amd/src`, `amd/build`);
    destPath = destPath.replace(/\.js$/, '.min.js');
    return destPath;
};

module.exports = grunt => {
    // Load the Ignorefiles tasks.
    require('./ignorefiles')(grunt);

    // Load the Shifter tasks.
    require('./shifter')(grunt);

    // Load ESLint.
    require('./eslint')(grunt);

    // Load jsconfig.
    require('./jsconfig')(grunt);

    // Load JSDoc.
    require('./jsdoc')(grunt);

    const path = require('path');

    // Register JS tasks.
    grunt.registerTask('yui', ['eslint:yui', 'shifter']);
    grunt.registerTask('amd', ['ignorefiles', 'eslint:amd', 'rollup']);
    grunt.registerTask('js', ['amd', 'yui']);

    // Register NPM tasks.
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-rollup');

    const babelTransform = require('@babel/core').transform;
    const babel = (options = {}) => {
        return {
            name: 'babel',

            transform: (code, id) => {
                grunt.log.debug(`Transforming ${id}`);
                options.filename = id;
                const transformed = babelTransform(code, options);

                return {
                    code: transformed.code,
                    map: transformed.map
                };
            }
        };
    };

    // Note: We have to use a rate limit plugin here because rollup runs all tasks asynchronously and in parallel.
    // When we kick off a full run, if we kick off a rollup of every file this will fork-bomb the machine.
    // To work around this we use a concurrent Promise queue based on the number of available processors.
    const rateLimit = () => {
        const queue = [];
        let queueRunner;

        const startQueue = () => {
            if (queueRunner) {
                return;
            }

            queueRunner = setTimeout(() => {
                const limit = Math.max(1, require('os').cpus().length / 2);
                grunt.log.debug(`Starting rollup with queue size of ${limit}`);
                runQueue(limit);
            }, 100);
        };

        // The queue runner will run the next `size` items in the queue.
        const runQueue = (size = 1) => {
            queue.splice(0, size).forEach(resolve => {
                grunt.log.debug(`Item resolved. Kicking off next one.`);
                resolve();
            });
        };

        return {
            name: 'ratelimit',

            // The options hook is run in parallel.
            // We can return an unresolved Promise which is queued for later resolution.
            options: async(options) => {
                return new Promise(resolve => {
                    queue.push(resolve);
                    startQueue();
                    return options;
                });
            },

            // When an item in the queue completes, start the next item in the queue.
            generateBundle: (options, bundle) => {
                grunt.log.debug(`Finished output phase for ${Object.keys(bundle).join(', ')}`);
                runQueue();
            },
        };
    };

    const terser = require('rollup-plugin-terser').terser;
    grunt.config.merge({
        rollup: {
            options: {
                format: 'esm',
                dir: 'output',
                sourcemap: true,
                treeshake: false,
                context: 'window',
                plugins: [
                    rateLimit(),
                    babel({
                        sourceMaps: true,
                        comments: false,
                        compact: false,
                        plugins: [
                            'transform-es2015-modules-amd-lazy',
                            'system-import-transformer',
                            // This plugin modifies the Babel transpiling for "export default"
                            // so that if it's used then only the exported value is returned
                            // by the generated AMD module.
                            //
                            // It also adds the Moodle plugin name to the AMD module definition
                            // so that it can be imported as expected in other modules.
                            path.resolve('.grunt/babel-plugin-add-module-to-define.js')
                        ],
                        presets: [
                            ['@babel/preset-env', {
                                modules: false,
                                useBuiltIns: false
                            }]
                        ]
                    }),

                    terser({
                        // Do not mangle variables.
                        // Makes debugging easier.
                        mangle: false,
                    }),
                ],
            },
            dist: {
                files: [{
                    expand: true,
                    src: grunt.moodleEnv.files ? grunt.moodleEnv.files : grunt.moodleEnv.amdSrc,
                    rename: babelRename
                }],
            },
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

    // Add the 'js' task as a startup task.
    grunt.moodleEnv.startupTasks.push('js');

    // On watch, we dynamically modify config to build only affected files. This
    // method is slightly complicated to deal with multiple changed files at once (copied
    // from the grunt-contrib-watch readme).
    let changedFiles = Object.create(null);
    const onChange = grunt.util._.debounce(function() {
        const files = Object.keys(changedFiles);
        grunt.config('rollup.dist.files', [{expand: true, src: files, rename: babelRename}]);
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
