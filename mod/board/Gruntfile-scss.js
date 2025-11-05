"use strict";

// To use run following from mod/board/ directory:
//   npm ci
//   ./node_modules/grunt/bin/grunt --gruntfile=Gruntfile-scss.js

module.exports = function(grunt) {
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks("grunt-stylelint");

    var sass = require('sass');

    grunt.initConfig({
        sass: {
            development: {
                options: {
                    implementation: sass,
                    style: "expanded",
                },
                files: {
                    "styles.css": "scss/styles.scss"
                }
            }
        },
        stylelint: {
            development: {
                options: {
                    fix: true
                },
                files: {
                    'styles.css' : 'styles.css'
                }
            }
        }
    });

    grunt.registerTask("default", ["sass:development", "stylelint:development"]);
};
