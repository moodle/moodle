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
// @ts-nocheck

/**
 * Grunt tasks for building ESM components.
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const {lintTypescript, createResidentLinter} = require('./xo');

module.exports = grunt => {
    /**
     * Register ESM task — build or watch ESM components.
     *
     * Modes:
     *   grunt esm          — production build
     *   grunt esm:dev      — deprecated (behaves like grunt esm)
     *   grunt esm:watch    — lint, then rebuild on save (incremental context)
     *
     * Pass --skip-lint to build without running XO first.
     *
     * Note: esm:watch drives its own loop — a change is reported, the sources are
     * linted, and only then is the esbuild context rebuilt. It stays separate from
     * grunt-contrib-watch so esbuild's incremental build graph is reused between
     * rebuilds rather than starting from scratch on each change.
     */
    grunt.registerTask('esm', 'Build all ESM components', function(mode) {
        const done = this.async();
        const isWatch = mode === 'watch';

        const skipLint = !!grunt.option('skip-lint');

        if (mode === 'dev') {
            grunt.log.warn('esm:dev is deprecated. Output is always production-mode. Use "grunt esm" instead.');
        }

        if (skipLint) {
            grunt.log.warn('Linting skipped (--skip-lint). Run "grunt xo" before committing.');
        }

        if (isWatch) {
            // Lint before each rebuild.
            const beforeBuild = skipLint ? async() => [] : createResidentLinter(grunt);

            (async() => {
                try {
                    const {watchComponents} = await import('../../.esbuild/plugin/plugincomponents.mjs');
                    const {generateAliases} = await import('../../.esbuild/generate-aliases.mjs');

                    generateAliases();

                    const stopWatching = await watchComponents(beforeBuild);

                    if (!stopWatching) {
                        grunt.log.warn('No ESM source files found. Nothing to watch.');
                        done();
                        return;
                    }

                    grunt.log.ok('esbuild is watching for ESM changes. Press Ctrl+C to stop.');

                    // Keep the process alive until the user interrupts. done() is intentionally
                    // not called here — grunt's async mechanism holds the process open.
                    process.on('SIGINT', async() => {
                        await stopWatching();
                        done();
                    });
                } catch (err) {
                    grunt.log.error(err.message);
                    done(false);
                }
            })();

            return;
        }

        grunt.log.writeln('Building ESM components...');

        (async() => {
            try {
                const {generateAliases} = await import('../../.esbuild/generate-aliases.mjs');
                const {buildPluginComponents} = await import('../../.esbuild/plugin/plugincomponents.mjs');
                const {applyDefaultSwizzleSafety} = await import('../../scripts/lib/swizzle/index.mjs');

                generateAliases();

                // Lint before building.
                if (!skipLint) {
                    await lintTypescript(grunt);
                }

                await buildPluginComponents();

                const defaulted = applyDefaultSwizzleSafety(grunt.moodleEnv.gruntFilePath);
                if (defaulted.length > 0) {
                    grunt.log.warn(
                        `${defaulted.length} component(s) had no explicit swizzle safety level and were ` +
                        `defaulted to risky/risky in swizzle.json: ${defaulted.join(', ')}`
                    );
                }

                done();
            } catch (err) {
                grunt.log.error(err.message);
                done(false);
            }
        })();
    });

    grunt.registerTask('react', ['esm']);
    grunt.registerTask('react:watch', ['esm:watch']);
};
