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
 * Grunt task for linting TypeScript sources with XO.
 *
 * @copyright  2026 Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const path = require('path');
const {spawn} = require('child_process');

/**
 * Run XO over the supplied files or globs.
 *
 * XO is always given explicit targets. Left to itself it would discover every .html and
 * .md file in the tree, which the bundled ESLint 10 also lints.
 *
 * @param {Grunt} grunt
 * @param {string[]} [targets] Files or globs to lint. Defaults to every TypeScript source,
 *                             narrowed by --files or the current component.
 * @param {object} options
 * @param {boolean} options.fix Whether to apply auto-fixes to the source files.
 * @returns {Promise<void>} Rejects when XO reports a problem.
 */
const lintTypescript = (grunt, targets, {fix = false} = {}) => new Promise((resolve, reject) => {
    // --files can name any file, and XO would lint AMD JavaScript with its own rules.
    targets ??= grunt.moodleEnv.files
        ? grunt.moodleEnv.files.filter(file => /\.tsx?$/.test(file))
        : grunt.moodleEnv.reactSrc;

    if (targets.length === 0) {
        resolve();
        return;
    }

    const xoBin = path.join(grunt.moodleEnv.gruntFilePath, 'node_modules', '.bin', 'xo');
    const args = fix ? ['--fix', ...targets] : targets;

    spawn(xoBin, args, {cwd: grunt.moodleEnv.gruntFilePath, stdio: 'inherit'})
        .on('error', reject)
        .on('close', code => {
            if (code === 0) {
                resolve();
                return;
            }
            reject(new Error('XO reported problems in the TypeScript sources.'));
        });
});

/**
 * Create a linter that keeps XO loaded between runs.
 *
 * The CLI reloads XO's plugin graph on every invocation, which costs over a second
 * before it reads a line of source. A watch process is long-lived, so it loads XO
 * once and reuses it: the first run pays for building the TypeScript program, and
 * each one after it costs only the linting.
 *
 * One-shot tasks keep using {@link lintTypescript}, whose process exits immediately
 * and so gains nothing from this while losing the CLI's warm cache.
 *
 * @param {Grunt} grunt
 * @returns {() => Promise<{text: string}[]>} Resolves to the problems found, empty when clean.
 */
const createResidentLinter = grunt => {
    let xo;

    return async() => {
        const {Xo} = await import('xo');
        const formatter = await import('eslint-formatter-pretty');

        xo ??= new Xo({cwd: grunt.moodleEnv.gruntFilePath});

        const report = await xo.lintFiles(grunt.moodleEnv.reactSrc);

        if (report.errorCount > 0 || report.warningCount > 0) {
            // The report carries rulesMeta, which the formatter reads to link each rule to its docs.
            grunt.log.writeln(formatter.default(report.results, report));
        }

        return report.errorCount > 0 ? [{text: 'XO reported problems in the TypeScript sources.'}] : [];
    };
};

module.exports = grunt => {
    /**
     * Lint the TypeScript sources.
     *
     * Pass --fix to apply auto-fixes. Pass --files to narrow the run to specific paths.
     */
    const handler = function() {
        const done = this.async();

        (async() => {
            try {
                // XO resolves types through tsconfig.json, which extends the generated
                // tsconfig.aliases.json. Without it XO exits on an unresolved extends.
                const {generateAliases} = await import('../../.esbuild/generate-aliases.mjs');
                generateAliases();

                await lintTypescript(grunt, undefined, {fix: !!grunt.option('fix')});
                done();
            } catch (err) {
                grunt.log.error(err.message);
                done(false);
            }
        })();
    };

    grunt.registerTask('xo', 'Lint TypeScript sources with XO', handler);
};

module.exports.lintTypescript = lintTypescript;
module.exports.createResidentLinter = createResidentLinter;
