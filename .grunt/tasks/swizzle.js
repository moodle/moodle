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
 * Grunt task registrations for the React component swizzle system.
 *
 * These are thin wrappers that delegate to scripts/swizzle.mjs so that the
 * swizzle commands are available both as `grunt swizzle[:<subcommand>]` and
 * as `node scripts/swizzle.mjs [subcommand]`.
 *
 * All logic lives in scripts/lib/swizzle/.
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const path = require('path');
const {spawn} = require('child_process');

module.exports = (grunt) => {
    const rootDir = process.cwd();
    const script = path.join(rootDir, 'scripts', 'swizzle.mjs');

    /**
     * Spawn scripts/swizzle.mjs with the given arguments and call done when it exits.
     *
     * @param {string[]} args   Arguments to pass to the script.
     * @param {Function} done   Grunt async done callback.
     * @param {string}   cwd    Working directory for the child process.
     */
    function spawnScript(args, done, cwd = rootDir) {
        const proc = spawn(process.execPath, [script, ...args], {stdio: 'inherit', cwd});
        proc.on('close', code => done(code === 0));
    }

    grunt.registerTask(
        'swizzle',
        'Interactively wrap or eject a React component into your theme',
        function() {
            spawnScript([], this.async(), process.cwd());
        }
    );
};
