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
     * Get the list of feature files to pass to the gherkin linter.
     *
     * @returns {Array}
     */
    const getGherkinLintTargets = () => {
        if (grunt.moodleEnv.files) {
            // Specific files were requested. Only check these.
            return grunt.moodleEnv.files;
        }

        if (grunt.moodleEnv.inComponent) {
            return [`${grunt.moodleEnv.runDir}/tests/behat/*.feature`];
        }

        return ['**/tests/behat/*.feature'];
    };

    const handler = function() {
        const done = this.async();
        const options = grunt.config('gherkinlint.options');

        // Grab the gherkin-lint linter and required scaffolding.
        const linter = require('gherkin-lint/dist/linter.js');
        const featureFinder = require('gherkin-lint/dist/feature-finder.js');
        const configParser = require('gherkin-lint/dist/config-parser.js');
        const formatter = require('gherkin-lint/dist/formatters/stylish.js');

        // Run the linter.
        return linter.lint(
            featureFinder.getFeatureFiles(grunt.file.expand(options.files)),
            configParser.getConfiguration(configParser.defaultConfigFileName)
        )
        .then(results => {
            // Print the results out uncondtionally.
            formatter.printResults(results);

            return results;
        })
        .then(results => {
            // Report on the results.
            // The done function takes a bool whereby a falsey statement causes the task to fail.
            return results.every(result => result.errors.length === 0);
        })
        .then(done); // eslint-disable-line promise/no-callback-in-promise
    };

    grunt.registerTask('gherkinlint', 'Run gherkinlint against the current directory', handler);

    grunt.config.set('gherkinlint', {
        options: {
            files: getGherkinLintTargets(),
        }
    });

    grunt.config.merge({
        watch: {
            gherkinlint: {
                files: [grunt.moodleEnv.inComponent ? 'tests/behat/*.feature' : '**/tests/behat/*.feature'],
                tasks: ['gherkinlint'],
            },
        },
    });

    return handler;
};
