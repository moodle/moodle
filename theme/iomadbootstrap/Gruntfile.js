/**
 * Gruntfile for compiling theme_bootstrap .less files.
 *
 * This file configures tasks to be run by Grunt
 * http://gruntjs.com/ for the current theme.
 *
 *
 * Requirements:
 * -------------
 * nodejs, npm, grunt-cli.
 *
 * Installation:
 * -------------
 * node and npm: instructions at http://nodejs.org/
 *
 * grunt-cli: `[sudo] npm install -g grunt-cli`
 *
 * node dependencies: run `npm install` in the root directory.
 *
 *
 * Usage:
 * ------
 * Call tasks from the theme root directory. Default behaviour
 * (calling only `grunt`) is to run the watch task detailed below.
 *
 *
 * Porcelain tasks:
 * ----------------
 * The nice user interface intended for everyday use. Provide a
 * high level of automation and convenience for specific use-cases.
 *
 * grunt watch   Watch the less directory (and all subdirectories)
 *               for changes to *.less files then on detection
 *               run 'grunt compile'
 *
 *               Options:
 *
 *               --dirroot=<path>  Optional. Explicitly define the
 *                                 path to your Moodle root directory
 *                                 when your theme is not in the
 *                                 standard location.
 * grunt compile Run the .less files through the compiler, create the
 *               RTL version of the output, then run decache so that
 *               the results can be seen on the next page load.
 *
 *               Options:
 *
 *               --dirroot=<path>  Optional. Explicitly define the
 *                                 path to your Moodle root directory
 *                                 when your theme is not in the
 *                                 standard location.
 *
 *
 * Plumbing tasks & targets:
 * -------------------------
 * Lower level tasks encapsulating a specific piece of functionality
 * but usually only useful when called in combination with another.
 *
 * grunt less         Compile all less files.
 *
 * grunt less:moodle  Compile Moodle less files only.
 *
 * grunt less:editor  Compile editor less files only.
 *
 * grunt decache      Clears the Moodle theme cache.
 *
 *                    Options:
 *
 *                    --dirroot=<path>  Optional. Explicitly define
 *                                      the path to your Moodle root
 *                                      directory when your theme is
 *                                      not in the standard location.
 *
 *
 * grunt replace             Run all text replace tasks.
 *
 * grunt replace:rtl_images  Add _rtl to the filenames of certain images
 *                           that require flipping for use with RTL
 *                           languages.
 *
 * grunt replace:font_fix    Correct the format for the Moodle font
 *                           loader to pick up the Glyphicon font.
 *
 *
 * grunt cssflip    Create moodle-rtl.css by flipping the direction styles
 *                  in moodle.css.
 *
 *
 * @package theme
 * @subpackage bootstrap
 * @author Joby Harding www.iamjoby.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// jshint node: true, strict: false

module.exports = function(grunt) {

    // Import modules.
    var path = require('path');
    var os = require('os');

    // PHP strings for exec task.
    var moodleroot = path.dirname(path.dirname(__dirname)),
        configfile = '',
        decachephp = '',
        dirrootopt = grunt.option('dirroot') || process.env.MOODLE_DIR || '';

    // Allow user to explicitly define Moodle root dir.
    if ('' !== dirrootopt) {
        moodleroot = path.resolve(dirrootopt);
    }

    configfile = path.join(moodleroot, 'config.php');

    decachephp += 'define(\'CLI_SCRIPT\', true);';
    decachephp += 'require(\'' + configfile + '\');';
    decachephp += 'theme_reset_all_caches();';

    grunt.initConfig({
        less: {
            // Compile moodle styles.
            moodle: {
                options: {
                    compress: false,
                    strictMath: true
                },
                src: 'less/moodle.less',
                dest: 'style/moodle.css'
            },
            // Compile editor styles.
            editor: {
                options: {
                    compress: false,
                    strictMath: true
                },
                src: 'less/editor.less',
                dest: 'style/editor.css'
            }
        },
        autoprefixer: {
            options: {
                browsers: [
                    'Android 2.3',
                    'Android >= 4',
                    'Chrome >= 20',
                    'Firefox >= 24', // Firefox 24 is the latest ESR
                    'Explorer >= 8',
                    'iOS >= 6',
                    'Opera >= 12',
                    'Safari >= 6'
                ]
            },
            core: {
                options: {
                    map: false
                },
                src: ['style/moodle.css', 'style/moodle-rtl.css', 'style/editor.css'],
            },
        },
        cssmin: {
            options: {
                compatibility: 'ie8',
                keepSpecialComments: '*',
                noAdvanced: true
            },
            core: {
                files: {
                    'style/moodle_min.css': 'style/moodle.css',
                    'style/editor_min.css': 'style/editor.css'
                }
            }
        },
        csscomb: {
            options: {
                config: 'less/bootstrap3/.csscomb.json'
            },
            dist: {
                expand: true,
                cwd: 'style/',
                src: ['moodle.css', 'editor.css'],
                dest: 'style/'
            }
        },
        exec: {
            decache: {
                cmd: 'php -r "' + decachephp + '"',
                callback: function(error) {
                    // exec will output error messages
                    // just add one to confirm success.
                    if (!error) {
                        grunt.log.writeln("Moodle theme cache reset.");
                    }
                }
            }
        },
        watch: {
            // Watch for any changes to less files and compile.
            files: ["less/**/*.less"],
            tasks: ["compile"],
            options: {
                spawn: false,
                livereload: true
            }
        },
        cssflip: {
            rtl: {
                src: 'style/moodle.css',
                dest: 'style/moodle-rtl.css'
            }
        },
        replace: {
            rtl_images: {
                src: 'style/moodle-rtl.css',
                overwrite: true,
                replacements: [{
                    from: '[[pix:theme|fp/path_folder]]',
                    to: '[[pix:theme|fp/path_folder_rtl]]'
                }, {
                    from: '[[pix:t/collapsed]]',
                    to: '[[pix:t/collapsed_rtl]]'
                }, {
                    from: '[[pix:t/collapsed_empty]]',
                    to: '[[pix:t/collapsed_empty_rtl]]'
                }, {
                    from: '[[pix:y/tn]]',
                    to: '[[pix:y/tn_rtl]]'
                }, {
                    from: '[[pix:y/tp]]',
                    to: '[[pix:y/tp_rtl]]'
                }, {
                    from: '[[pix:y/ln]]',
                    to: '[[pix:y/ln_rtl]]'
                }, {
                    from: '[[pix:y/lp]]',
                    to: '[[pix:y/lp_rtl]]'
                }]
            }
        }
    });

    // Load contrib tasks.
    grunt.loadNpmTasks("grunt-autoprefixer");
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-exec");
    grunt.loadNpmTasks("grunt-text-replace");
    grunt.loadNpmTasks("grunt-css-flip");
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-csscomb');

    // Register tasks.
    grunt.registerTask("default", ["watch"]);
    grunt.registerTask("decache", ["exec:decache"]);

    grunt.registerTask("compile", [
        "less",
        "cssflip",
        "replace:rtl_images",
        "autoprefixer",
        "csscomb",
        "cssmin",
        "decache"
    ]);

    grunt.registerTask('amd', function() {
        grunt.fail.warn([
            "The task 'amd' is not configured in the theme Gruntfile.",
            'Specify the path to your $CFG->dirroot Gruntfile e.g.',
            '',
            'grunt amd --gruntfile="/path/to/dirroot/Gruntfile.js"',
            '',
            ''
        ].join(os.EOL));
    });
};
