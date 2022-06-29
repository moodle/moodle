/**
 * This is a wrapper task to handle the grunt watch command. It attempts to use
 * Watchman to monitor for file changes, if it's installed, because it's much faster.
 *
 * If Watchman isn't installed then it falls back to the grunt-contrib-watch file
 * watcher for backwards compatibility.
 */

/* eslint-env node */

module.exports = grunt => {
    /**
     * This is a wrapper task to handle the grunt watch command. It attempts to use
     * Watchman to monitor for file changes, if it's installed, because it's much faster.
     *
     * If Watchman isn't installed then it falls back to the grunt-contrib-watch file
     * watcher for backwards compatibility.
     */
    const watchHandler = function() {
        const async = require('async');
        const watchTaskDone = this.async();
        let watchInitialised = false;
        let watchTaskQueue = {};
        let processingQueue = false;

        const watchman = require('fb-watchman');
        const watchmanClient = new watchman.Client();

        // Grab the tasks and files that have been queued up and execute them.
        var processWatchTaskQueue = function() {
            if (!Object.keys(watchTaskQueue).length || processingQueue) {
                // If there is nothing in the queue or we're already processing then wait.
                return;
            }

            processingQueue = true;

            // Grab all tasks currently in the queue.
            var queueToProcess = watchTaskQueue;
            // Reset the queue.
            watchTaskQueue = {};

            async.forEachSeries(
                Object.keys(queueToProcess),
                function(task, next) {
                    var files = queueToProcess[task];
                    var filesOption = '--files=' + files.join(',');
                    grunt.log.ok('Running task ' + task + ' for files ' + filesOption);

                    // Spawn the task in a child process so that it doesn't kill this one
                    // if it failed.
                    grunt.util.spawn(
                        {
                            // Spawn with the grunt bin.
                            grunt: true,
                            // Run from current working dir and inherit stdio from process.
                            opts: {
                                cwd: grunt.moodleEnv.fullRunDir,
                                stdio: 'inherit'
                            },
                            args: [task, filesOption]
                        },
                        function(err, res, code) {
                            if (code !== 0) {
                                // The grunt task failed.
                                grunt.log.error(err);
                            }

                            // Move on to the next task.
                            next();
                        }
                    );
                },
                function() {
                    // No longer processing.
                    processingQueue = false;
                    // Once all of the tasks are done then recurse just in case more tasks
                    // were queued while we were processing.
                    processWatchTaskQueue();
                }
            );
        };

        const originalWatchConfig = grunt.config.get(['watch']);
        const watchConfig = Object.keys(originalWatchConfig).reduce(function(carry, key) {
            if (key == 'options') {
                return carry;
            }

            const value = originalWatchConfig[key];

            const taskNames = value.tasks;
            const files = value.files;
            let excludes = [];
            if (value.excludes) {
                excludes = value.excludes;
            }

            taskNames.forEach(function(taskName) {
                carry[taskName] = {
                    files,
                    excludes,
                };
            });

            return carry;
        }, {});

        watchmanClient.on('error', function(error) {
            // We have to add an error handler here and parse the error string because the
            // example way from the docs to check if Watchman is installed doesn't actually work!!
            // See: https://github.com/facebook/watchman/issues/509
            if (error.message.match('Watchman was not found')) {
                // If watchman isn't installed then we should fallback to the other watch task.
                grunt.log.ok('It is recommended that you install Watchman for better performance using the "watch" command.');

                // Fallback to the old grunt-contrib-watch task.
                grunt.renameTask('watch-grunt', 'watch');
                grunt.task.run(['watch']);
                // This task is finished.
                watchTaskDone(0);
            } else {
                grunt.log.error(error);
                // Fatal error.
                watchTaskDone(1);
            }
        });

        watchmanClient.on('subscription', function(resp) {
            if (resp.subscription !== 'grunt-watch') {
                return;
            }

            resp.files.forEach(function(file) {
                grunt.log.ok('File changed: ' + file.name);

                var fullPath = grunt.moodleEnv.fullRunDir + '/' + file.name;
                Object.keys(watchConfig).forEach(function(task) {

                    const fileGlobs = watchConfig[task].files;
                    var match = fileGlobs.some(function(fileGlob) {
                        return grunt.file.isMatch(`**/${fileGlob}`, fullPath);
                    });

                    if (match) {
                        // If we are watching a subdirectory then the file.name will be relative
                        // to that directory. However the grunt tasks  expect the file paths to be
                        // relative to the Gruntfile.js location so let's normalise them before
                        // adding them to the queue.
                        var relativePath = fullPath.replace(grunt.moodleEnv.gruntFilePath + '/', '');
                        if (task in watchTaskQueue) {
                            if (!watchTaskQueue[task].includes(relativePath)) {
                                watchTaskQueue[task] = watchTaskQueue[task].concat(relativePath);
                            }
                        } else {
                            watchTaskQueue[task] = [relativePath];
                        }
                    }
                });
            });

            processWatchTaskQueue();
        });

        process.on('SIGINT', function() {
            // Let the user know that they may need to manually stop the Watchman daemon if they
            // no longer want it running.
            if (watchInitialised) {
                grunt.log.ok('The Watchman daemon may still be running and may need to be stopped manually.');
            }

            process.exit();
        });

        // Initiate the watch on the current directory.
        watchmanClient.command(['watch-project', grunt.moodleEnv.fullRunDir], function(watchError, watchResponse) {
            if (watchError) {
                grunt.log.error('Error initiating watch:', watchError);
                watchTaskDone(1);
                return;
            }

            if ('warning' in watchResponse) {
                grunt.log.error('warning: ', watchResponse.warning);
            }

            var watch = watchResponse.watch;
            var relativePath = watchResponse.relative_path;
            watchInitialised = true;

            watchmanClient.command(['clock', watch], function(clockError, clockResponse) {
                if (clockError) {
                    grunt.log.error('Failed to query clock:', clockError);
                    watchTaskDone(1);
                    return;
                }

                // Generate the expression query used by watchman.
                // Documentation is limited, but see https://facebook.github.io/watchman/docs/expr/allof.html for examples.
                // We generate an expression to match any value in the files list of all of our tasks, but excluding
                // all value in the  excludes list of that task.
                //
                // [anyof, [
                //      [allof, [
                //          [anyof, [
                //              ['match', validPath, 'wholename'],
                //              ['match', validPath, 'wholename'],
                //          ],
                //          [not,
                //              [anyof, [
                //                  ['match', invalidPath, 'wholename'],
                //                  ['match', invalidPath, 'wholename'],
                //              ],
                //          ],
                //      ],
                var matchWholeName = fileGlob => ['match', fileGlob, 'wholename'];
                var matches = Object.keys(watchConfig).map(function(task) {
                    const matchAll = [];
                    matchAll.push(['anyof'].concat(watchConfig[task].files.map(matchWholeName)));

                    if (watchConfig[task].excludes.length) {
                        matchAll.push(['not', ['anyof'].concat(watchConfig[task].excludes.map(matchWholeName))]);
                    }

                    return ['allof'].concat(matchAll);
                });

                matches = ['anyof'].concat(matches);

                var sub = {
                    expression: matches,
                    // Which fields we're interested in.
                    fields: ["name", "size", "type"],
                    // Add our time constraint.
                    since: clockResponse.clock
                };

                if (relativePath) {
                    /* eslint-disable camelcase */
                    sub.relative_root = relativePath;
                }

                watchmanClient.command(['subscribe', watch, 'grunt-watch', sub], function(subscribeError) {
                    if (subscribeError) {
                        // Probably an error in the subscription criteria.
                        grunt.log.error('failed to subscribe: ', subscribeError);
                        watchTaskDone(1);
                        return;
                    }

                    grunt.log.ok('Listening for changes to files in ' + grunt.moodleEnv.fullRunDir);
                });
            });
        });
    };

    // Rename the grunt-contrib-watch "watch" task because we're going to wrap it.
    grunt.renameTask('watch', 'watch-grunt');

    // Register the new watch handler.
    grunt.registerTask('watch', 'Run tasks on file changes', watchHandler);

    grunt.config.merge({
        watch: {
            options: {
                nospawn: true // We need not to spawn so config can be changed dynamically.
            },
        },
    });

    return watchHandler;
};
