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
 * Moodle swizzle CLI.
 *
 * Provides subcommands for working with React component overrides (swizzling).
 *
 * Usage:
 *   node scripts/swizzle.mjs                                    — interactive wizard
 *   node scripts/swizzle.mjs manifest set [s] [eject] [wrap]     — set safety level(s) for a component
 *   node scripts/swizzle.mjs manifest generate                  — default undeclared components to risky/risky
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {Command} from 'commander';
import {createRequire} from 'module';
import {fileURLToPath} from 'url';
import path from 'path';
import fs from 'fs';
import chalk from 'chalk';
import {getRootDir} from './lib/util.mjs';
import {
    generateSwizzleManifest, applyDefaultSwizzleSafety,
    setComponentSafety, VALID_LEVELS, VALID_FIELDS,
    discoverTargets, resolveDestFile, performEject, performWrap,
} from './lib/swizzle/index.mjs';

const _require = createRequire(fileURLToPath(import.meta.url));
const rootDir = getRootDir();

/** Human-readable labels for each VALID_FIELDS entry, used in the `manifest set` prompt. */
const FIELD_LABELS = {
    both: 'Both eject and wrap',
    eject: 'Eject only',
    wrap: 'Wrap only',
};

// ---------------------------------------------------------------------------
// Interactive wizard
// ---------------------------------------------------------------------------

/**
 * Run the interactive swizzle wizard: select a component, action, and target theme.
 *
 * @returns {Promise<void>}
 */
async function runInteractive() {
    const inquirer = (await import('inquirer')).default;
    const Enquirer = _require('enquirer');

    // 1. Load manifest.
    const manifest = generateSwizzleManifest(rootDir);
    const allEntries = Object.entries(manifest);
    if (allEntries.length === 0) {
        console.log('No swizzleable components found.');
        return;
    }

    // 2. Infer target from CWD.
    let inferredTarget = null;
    const relativeCwd = path.relative(rootDir, process.cwd());
    const themeMatch = relativeCwd.match(/^public\/theme\/([^/]+)/);
    if (themeMatch) {
        inferredTarget = {type: 'theme', name: themeMatch[1]};
        console.log(`  Target: theme_${inferredTarget.name} (inferred from current directory)`);
    }

    // 3. Select component.
    const {specifier} = await Enquirer.prompt({
        type: 'autocomplete',
        name: 'specifier',
        message: 'Which component do you want to swizzle?',
        limit: 4,
        footer: '\n  (Move up and down to reveal more choices)',
        choices: allEntries.map(([spec]) => ({name: spec, value: spec})),
    });

    const componentConfig = manifest[specifier];
    let target = inferredTarget;

    // 4. Prohibited gate.
    const bothProhibited = componentConfig.actions.eject === 'prohibited'
        && componentConfig.actions.wrap === 'prohibited';
    if (bothProhibited) {
        console.log('');
        console.log(`  ✗ ${specifier}`);
        console.log('');
        console.log('  This component is marked Prohibited by its author.');
        console.log('  Overriding it would depend on internal implementation details');
        console.log('  that are not part of the public API and may change without notice.');
        console.log('');
        console.log('  What you can do instead:');
        console.log('    - Check whether a hook or event achieves the same result');
        console.log('    - Ask the plugin maintainer to expose a supported override point');
        console.log('    - Look for a safe/risky component nearby that covers your use case');
        console.log('');
        return;
    }

    // 5. Select action.
    const actionChoices = [];
    if (componentConfig.actions.wrap !== 'prohibited') {
        actionChoices.push({
            name: `Wrap   – generate a scaffold that decorates the original` +
                ` [${componentConfig.actions.wrap}]`,
            value: 'wrap',
            'short': 'Wrap',
        });
    }
    if (componentConfig.actions.eject !== 'prohibited') {
        actionChoices.push({
            name: `Eject  – copy the original source into your target` +
                ` (you own it from now on) [${componentConfig.actions.eject}]`,
            value: 'eject',
            'short': 'Eject',
        });
    }
    actionChoices.push({
        name: chalk.yellow('Exit   – cancel and do nothing'),
        value: 'exit',
        'short': 'Exit',
    });

    const {action} = await inquirer.prompt([{
        type: 'list',
        name: 'action',
        message: 'Which swizzle action do you want?',
        choices: actionChoices,
    }]);

    if (action === 'exit') {
        console.log('Aborted.');
        return;
    }

    // Safety gate.
    if (componentConfig.actions[action] === 'risky') {
        const riskyMsg = `⚠  ${action} on ${specifier} is Risky` +
            ` — internals may change between minor releases.\n  Continue anyway?`;
        const {confirmed} = await inquirer.prompt([{
            type: 'confirm',
            name: 'confirmed',
            message: riskyMsg,
            'default': false,
        }]);
        if (!confirmed) {
            console.log('Aborted.');
            return;
        }
    }

    // 6. Prompt for target if not inferred.
    if (!target) {
        const targetChoices = discoverTargets(rootDir);
        if (targetChoices.length === 0) {
            console.error('No themes found. Install a theme under public/theme/ first.');
            process.exit(1);
        }
        const answer = await inquirer.prompt([{
            type: 'list',
            name: 'target',
            message: 'Which theme should receive the override?',
            choices: targetChoices,
        }]);
        target = answer.target;
    }

    // 7. Check for existing file.
    const destFile = resolveDestFile(specifier, target, rootDir);
    if (fs.existsSync(destFile)) {
        const {overwrite} = await inquirer.prompt([{
            type: 'confirm',
            name: 'overwrite',
            message: `${path.relative(rootDir, destFile)} already exists. Overwrite?`,
            'default': false,
        }]);
        if (!overwrite) {
            console.log('Aborted.');
            return;
        }
    }

    // 8. Perform the action.
    if (action === 'eject') {
        const {files} = performEject(specifier, destFile, rootDir);
        console.log(chalk.green(`  Ejected → ${files[0]}`));
        for (const f of files.slice(1)) {
            console.log(chalk.green(`          → ${f}`));
        }
        console.log('  The original source has been copied into your target.');
        console.log('  You now own these files — keep them up to date after Moodle upgrades.');
    } else {
        const {parentImport, staleRemoved} = performWrap(specifier, target, destFile, rootDir);
        for (const f of staleRemoved) {
            console.log(chalk.yellow(`  Removed stale → ${f}`));
        }
        console.log(chalk.green(`  Wrapped → ${path.relative(rootDir, destFile)}`));
        console.log('  A scaffold has been generated. Edit it to add your customisations.');
        console.log(`  The upstream component is imported via: ${parentImport}`);
    }

    console.log('');
    console.log('  Next step: run `grunt react[:watch]` to compile the override.');
}

// ---------------------------------------------------------------------------
// CLI
// ---------------------------------------------------------------------------

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    const program = new Command();

    program
        .name('swizzle')
        .description('Moodle swizzle CLI tools')
        .action(async() => {
            try {
                await runInteractive();
            } catch (err) {
                if (err && err.message) {
                    console.error(`Error: ${err.message}`);
                }
                process.exit(1);
            }
        });

    const manifestCmd = program
        .command('manifest')
        .description('Manage per-component swizzle safety levels');

    manifestCmd
        .command('set [specifier] [ejectLevel] [wrapLevel]')
        .description(
            'Set the eject/wrap safety level(s) for a component. Provide one level to set both ' +
            `eject and wrap, or two levels (eject, then wrap) to set them independently. Valid levels: ` +
            `${VALID_LEVELS.join(', ')}`
        )
        .action(async(specifier, ejectLevel, wrapLevel) => {
            try {
                if (!specifier) {
                    const manifest = generateSwizzleManifest(rootDir);
                    const allEntries = Object.entries(manifest);
                    if (allEntries.length === 0) {
                        console.error('No components found.');
                        process.exit(1);
                    }
                    const Enquirer = _require('enquirer');
                    ({specifier} = await Enquirer.prompt({
                        type: 'autocomplete',
                        name: 'specifier',
                        message: 'Which component?',
                        limit: 4,
                        footer: '\n  (Move up and down to reveal more choices)',
                        choices: allEntries.map(([spec]) => ({name: spec, value: spec})),
                    }));

                    const inquirer = (await import('inquirer')).default;
                    const {field} = await inquirer.prompt([{
                        type: 'list',
                        name: 'field',
                        message: 'Apply to?',
                        choices: VALID_FIELDS.map(field => ({name: FIELD_LABELS[field], value: field})),
                    }]);
                    ({level: ejectLevel} = await inquirer.prompt([{
                        type: 'list',
                        name: 'level',
                        message: 'Which safety level?',
                        choices: VALID_LEVELS,
                    }]));

                    setComponentSafety(rootDir, specifier, ejectLevel, field);
                    return;
                }

                if (ejectLevel && wrapLevel) {
                    setComponentSafety(rootDir, specifier, ejectLevel, 'eject', false);
                    setComponentSafety(rootDir, specifier, wrapLevel, 'wrap');
                    return;
                }

                if (!ejectLevel) {
                    const inquirer = (await import('inquirer')).default;
                    ({level: ejectLevel} = await inquirer.prompt([{
                        type: 'list',
                        name: 'level',
                        message: 'Which safety level?',
                        choices: VALID_LEVELS,
                    }]));
                }

                setComponentSafety(rootDir, specifier, ejectLevel, 'both');
            } catch (err) {
                if (err && err.message) {
                    console.error(`Error: ${err.message}`);
                }
                process.exit(1);
            }
        });

    manifestCmd
        .command('generate')
        .description(
            'Add a risky/risky entry to swizzle.json for every discovered component that has none yet'
        )
        .action(() => {
            try {
                const added = applyDefaultSwizzleSafety(rootDir);
                if (added.length === 0) {
                    console.log('✓ Every discovered component already has an explicit safety level.');
                    return;
                }
                console.log(`✓ Defaulted ${added.length} component(s) to risky/risky:`);
                for (const specifier of added) {
                    console.log(`  - ${specifier}`);
                }
                console.log('  Review these and set `safe` where appropriate.');
            } catch (err) {
                if (err && err.message) {
                    console.error(`Error: ${err.message}`);
                }
                process.exit(1);
            }
        });

    program.parse();
}
