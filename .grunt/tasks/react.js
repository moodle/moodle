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
 * Grunt tasks for building React components.
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = grunt => {
    /**
     * Register react task — build or watch React components.
     *
     * Modes:
     *   grunt react          — production build
     *   grunt react:dev      — development build (sourcemaps, no minification)
     *   grunt react:watch    — esbuild native watch (dev mode, incremental context)
     *
     * Note: react:watch uses esbuild's own context.watch() and is intentionally
     * separate from grunt-contrib-watch. This keeps esbuild's incremental build
     * graph alive between rebuilds rather than restarting from scratch on each change.
     */
    grunt.registerTask('react', 'Build all React components', function(mode) {
        const done = this.async();
        const isWatch = mode === 'watch';
        const isDev = isWatch || mode === 'dev';

        if (isWatch) {
            const path = require('path');
            const {spawn} = require('child_process');

            // Run ESLint on the rebuilt source files in check-only mode (no --fix)
            // to avoid writing changes that would re-trigger esbuild.
            const eslintBin = path.join(grunt.moodleEnv.gruntFilePath, 'node_modules', '.bin', 'eslint');
            const onRebuild = (srcFiles) => {
                if (srcFiles.length === 0) {
                    return;
                }
                const absSrcFiles = srcFiles.map(f => path.join(grunt.moodleEnv.gruntFilePath, f));
                spawn(eslintBin, absSrcFiles, {stdio: 'inherit'})
                    .on('error', err => grunt.log.error(`ESLint: ${err.message}`));
            };

            (async() => {
                try {
                    const {watchComponents} = await import('../../.esbuild/plugin/plugincomponents.mjs');

                    const ctx = await watchComponents(true, onRebuild);

                    if (!ctx) {
                        grunt.log.warn('No React source files found. Nothing to watch.');
                        done();
                        return;
                    }

                    grunt.log.ok('esbuild is watching for React changes. Press Ctrl+C to stop.');

                    // Keep the process alive until the user interrupts. done() is intentionally
                    // not called here — grunt's async mechanism holds the process open.
                    process.on('SIGINT', async() => {
                        await ctx.dispose();
                        done();
                    });
                } catch (err) {
                    grunt.log.error(err.message);
                    done(false);
                }
            })();

            return;
        }

        grunt.log.writeln(`Building React components in ${isDev ? 'DEVELOPMENT' : 'PRODUCTION'} mode...`);

        (async() => {
            try {
                const {generateAliases} = await import('../../.esbuild/generate-aliases.mjs');
                const {buildPluginComponents} = await import('../../.esbuild/plugin/plugincomponents.mjs');

                generateAliases();
                await buildPluginComponents(isDev);
                done();
            } catch (err) {
                grunt.log.error(err.message);
                done(false);
            }
        })();
    });

};
