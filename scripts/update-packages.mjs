#!/usr/bin/env node
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Orchestrator that discovers and runs all per-package update scripts in scripts/packages/.
 *
 * To add a new package, drop a new update-{name}.mjs file into scripts/packages/ that exports
 * an async init() function. It will be picked up automatically — no changes to package.json
 * or this file are required.
 *
 * Usage:
 *   node scripts/update-packages.mjs              # update all packages
 *   node scripts/update-packages.mjs --list       # list available packages
 *   node scripts/update-packages.mjs bootstrap    # update a specific package (or several)
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import chalk from 'chalk';
import { Command } from 'commander';
import { readdirSync } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { getRootDir } from './lib/util.mjs';

const rootDir = getRootDir();
const packagesDir = path.join(rootDir, 'scripts', 'packages');

/**
 * Return all available package scripts as { name, file } objects, sorted by name.
 * name is the short name derived by stripping 'update-' and '.mjs' from the filename.
 *
 * @returns {Array<{name: string, file: string}>}
 */
const getAvailablePackages = () =>
    readdirSync(packagesDir)
        .filter((f) => f.startsWith('update-') && f.endsWith('.mjs'))
        .sort()
        .map((f) => ({
            name: f.replace(/^update-/, '').replace(/\.mjs$/, ''),
            file: path.join(packagesDir, f),
        }));

async function updatePackages(names) {
    const available = getAvailablePackages();

    let selected;
    if (names.length === 0) {
        selected = available;
    } else {
        selected = names.map((name) => {
            const pkg = available.find((p) => p.name === name);
            if (!pkg) {
                const known = available.map((p) => p.name).join(', ');
                throw new Error(`Unknown package "${name}". Available: ${known}`);
            }
            return pkg;
        });
    }

    console.log(chalk.blue.bold(`Running ${selected.length} package update script(s)...\n`));

    for (const pkg of selected) {
        const { init } = await import(pkg.file);
        await init();
        console.log('');
    }

    console.log(chalk.green.bold('All packages updated successfully ✓'));
}

function listPackages() {
    const packages = getAvailablePackages();
    console.log(chalk.blue.bold('Available packages:\n'));
    for (const pkg of packages) {
        console.log(`  ${chalk.green(pkg.name.padEnd(20))}  ${chalk.dim(path.relative(rootDir, pkg.file))}`);
    }
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    const program = new Command();
    program
        .name('update-packages')
        .description('Update third-party packages bundled in the Moodle source tree')
        .argument('[packages...]', 'package name(s) to update — omit to update all')
        .option('-l, --list', 'list available packages and exit')
        .action((packages, options) => {
            if (options.list) {
                listPackages();
                return;
            }
            updatePackages(packages).catch((err) => {
                console.error(chalk.red('Package update failed:'), err.message);
                process.exit(1);
            });
        });

    program.parse();
}
