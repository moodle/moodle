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
 * Core swizzle operations: source resolution, eject, wrap, target discovery.
 *
 * All functions are grunt-free — they return values or throw; callers handle output.
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import fs from 'fs';
import path from 'path';
import {parseSpecifier, loadComponents} from './utils.mjs';

/**
 * Derive the absolute path to a component's source file from its specifier.
 *
 * Handles the core component specially — core is not in componentPathMap.
 *
 * @param {string} specifier Module specifier (@moodle/lms/<component>/<module>).
 * @param {string} rootDir   Project root directory.
 * @returns {string|null}
 */
function resolveSourceFile(specifier, rootDir) {
    const parsed = parseSpecifier(specifier);
    if (!parsed) {
        return null;
    }
    const {component, module} = parsed;

    if (component === 'core') {
        const base = path.join(rootDir, 'public', 'lib', 'js', 'esm', 'src');
        const candidates = [
            path.join(base, `${module}.tsx`),
            path.join(base, `${module}.ts`),
            path.join(base, module, 'index.tsx'),
            path.join(base, module, 'index.ts'),
        ];
        return candidates.find(f => fs.existsSync(f)) ?? null;
    }

    const components = loadComponents(rootDir);
    const componentPath = Object.entries(components).find(([, name]) => name === component)?.[0];
    if (!componentPath) {
        return null;
    }

    const candidates = [
        path.join(rootDir, componentPath, 'js', 'esm', 'src', `${module}.tsx`),
        path.join(rootDir, componentPath, 'js', 'esm', 'src', `${module}.ts`),
        path.join(rootDir, componentPath, 'js', 'esm', 'src', module, 'index.tsx'),
        path.join(rootDir, componentPath, 'js', 'esm', 'src', module, 'index.ts'),
    ];
    return candidates.find(f => fs.existsSync(f)) ?? null;
}

/**
 * True when a resolved source file is the index of a directory-based module.
 *
 * @param {string|null} filePath
 * @returns {boolean}
 */
function isDirectoryBasedFile(filePath) {
    return Boolean(filePath) && path.basename(filePath).replace(/\.(ts|tsx)$/, '') === 'index';
}

/**
 * Return all source files for a component module (primary + co-located extras).
 *
 * @param {string} specifier
 * @param {string} rootDir
 * @returns {{ primary: string, extras: string[] } | null}
 */
function resolveSourceFiles(specifier, rootDir) {
    const primary = resolveSourceFile(specifier, rootDir);
    if (!primary) {
        return null;
    }

    if (isDirectoryBasedFile(primary)) {
        const dir = path.dirname(primary);
        const extras = fs.readdirSync(dir)
            .filter(name => path.join(dir, name) !== primary)
            .map(name => path.join(dir, name));
        return {primary, extras};
    }

    const stem = primary.slice(0, primary.lastIndexOf('.'));
    const dir = path.dirname(primary);
    const extras = fs.readdirSync(dir)
        .filter(name => {
            const full = path.join(dir, name);
            return full !== primary && full.startsWith(`${stem}.`);
        })
        .map(name => path.join(dir, name));

    return {primary, extras};
}

/**
 * Return the destination path inside the target theme's override tree.
 *
 * @param {string} specifier
 * @param {object} target
 * @param {string} rootDir
 * @returns {string}
 */
export function resolveDestFile(specifier, target, rootDir) {
    const {component, module} = parseSpecifier(specifier);
    const isDirectoryBased = isDirectoryBasedFile(resolveSourceFile(specifier, rootDir));
    const base = path.join(rootDir, 'public', 'theme', target.name, 'js', 'esm', 'src', 'overrides');

    if (isDirectoryBased) {
        return path.join(base, component, module, 'index.tsx');
    }
    return path.join(base, component, `${module}.tsx`);
}

/**
 * Convert snake_case or kebab-case to PascalCase.
 *
 * @param {string} str
 * @returns {string}
 */
function toPascalCase(str) {
    return str.replace(/(?:^|[_-])([a-z])/g, (_, c) => c.toUpperCase());
}

/**
 * Read the parent theme chain from a theme's config.php.
 *
 * @param {string} themeName
 * @param {string} rootDir
 * @returns {string[]}
 */
function readThemeParents(themeName, rootDir) {
    const configPath = path.join(rootDir, 'public', 'theme', themeName, 'config.php');
    if (!fs.existsSync(configPath)) {
        return [];
    }
    const content = fs.readFileSync(configPath, 'utf8');
    const match = content.match(/\$THEME\s*->\s*parents\s*=\s*\[([^\]]*)\]/);
    if (!match) {
        return [];
    }
    return [...match[1].matchAll(/['"]([^'"]+)['"]/g)].map(m => m[1]);
}

/**
 * Walk a theme's full ancestor chain (parents, grandparents, ...), nearest first.
 *
 * @param {string} themeName
 * @param {string} rootDir
 * @returns {string[]}
 */
function collectAncestorChain(themeName, rootDir) {
    const visited = new Set([themeName]);
    const queue = [...readThemeParents(themeName, rootDir)];
    const chain = [];

    while (queue.length > 0) {
        const name = queue.shift();
        if (visited.has(name)) {
            continue;
        }
        visited.add(name);
        chain.push(name);
        queue.push(...readThemeParents(name, rootDir));
    }

    return chain;
}

/**
 * Resolve the import specifier a wrap scaffold should use to reach the original.
 *
 * @param {string} specifier
 * @param {{type: 'theme', name: string}} target
 * @param {string} rootDir
 * @returns {string}
 */
function resolveParentImport(specifier, target, rootDir) {
    const {subpath} = parseSpecifier(specifier);
    const ancestors = collectAncestorChain(target.name, rootDir);

    for (const parent of ancestors) {
        const buildFile = path.join(
            rootDir, 'public', 'theme', parent,
            'js', 'esm', 'build', 'overrides', `${subpath}.js`,
        );
        if (fs.existsSync(buildFile)) {
            return `@moodle/lms/theme-${parent}/${subpath}`;
        }
    }

    return `@moodle/lms/theme-original/${subpath}`;
}

/**
 * Generate the wrap scaffold source for a component.
 *
 * @param {string} specifier
 * @param {{type: 'theme', name: string}} target
 * @param {string} parentImport
 * @returns {string}
 */
function generateWrapScaffold(specifier, target, parentImport) {
    const {component, module} = parseSpecifier(specifier);

    const isNamedParent = !parentImport.includes('/theme-original/');
    const chainNote = '// component, preserving the full theme override chain.';
    const bypassNote = '// component, bypassing any active theme overrides.';
    const importComment = isNamedParent
        ? `// ${parentImport} resolves to the nearest ancestor theme's version of this\n${chainNote}`
        : `// ${parentImport} resolves to the core (upstream) version of this\n${bypassNote}`;

    const moduleTag = `theme_${target.name}/${component}/${module}`;
    const label = `${target.name} theme`;

    return `// This file was generated by \`node scripts/swizzle.mjs\`.
// It wraps the original component — edit freely.
${importComment}
//
// To revert to the original, delete this file and rebuild.

import type {Props} from '${parentImport}';
import OriginalComponent from '${parentImport}';

/**
 * ${label} wrapper for ${module}.
 *
 * Renders the original component unchanged. Add your customisations here:
 *   - Extra markup before or after the original
 *   - Additional props or context
 *   - Side-effects (analytics, logging, etc.)
 *
 * @module     ${moduleTag}
 */
export default function ${toPascalCase(module)}(props: Props) {
    return (
        <>
            {/* TODO: add your customisation around the original */}
            <OriginalComponent {...props} />
        </>
    );
}
`;
}

/**
 * Discover all installed themes as swizzle targets.
 *
 * @param {string} rootDir
 * @returns {Array<{name: string, value: object}>}
 */
export function discoverTargets(rootDir) {
    const choices = [];
    const themeRoot = path.join(rootDir, 'public', 'theme');

    if (!fs.existsSync(themeRoot)) {
        return choices;
    }

    for (const name of fs.readdirSync(themeRoot)) {
        if (
            fs.statSync(path.join(themeRoot, name)).isDirectory() &&
            fs.existsSync(path.join(themeRoot, name, 'config.php'))
        ) {
            choices.push({name: `theme_${name}`, value: {type: 'theme', name}});
        }
    }

    return choices;
}

/**
 * Remove stale override files from a directory-based component's override dir.
 *
 * @param {string} destDir
 * @param {string} destFile
 * @param {string} rootDir
 * @returns {string[]} Relative paths of removed files.
 */
function removeStaleFiles(destDir, destFile, rootDir) {
    if (!fs.existsSync(destDir)) {
        return [];
    }
    const removed = [];
    for (const name of fs.readdirSync(destDir)) {
        const full = path.join(destDir, name);
        if (full !== destFile) {
            fs.unlinkSync(full);
            removed.push(path.relative(rootDir, full));
        }
    }
    return removed;
}

/**
 * Perform the eject action: copy the original source into the target theme.
 *
 * @param {string} specifier
 * @param {string} destFile
 * @param {string} rootDir
 * @returns {{files: string[]}} Relative paths of all copied files.
 * @throws {Error} When the source file cannot be located.
 */
export function performEject(specifier, destFile, rootDir) {
    const sourceFiles = resolveSourceFiles(specifier, rootDir);
    if (!sourceFiles) {
        throw new Error(`Could not locate source file for ${specifier}`);
    }
    fs.mkdirSync(path.dirname(destFile), {recursive: true});
    fs.copyFileSync(sourceFiles.primary, destFile);

    const files = [path.relative(rootDir, destFile)];
    for (const extra of sourceFiles.extras) {
        const extraDest = path.join(path.dirname(destFile), path.basename(extra));
        fs.copyFileSync(extra, extraDest);
        files.push(path.relative(rootDir, extraDest));
    }
    return {files};
}

/**
 * Perform the wrap action: write a scaffold into the target theme.
 *
 * @param {string} specifier
 * @param {{type: 'theme', name: string}} target
 * @param {string} destFile
 * @param {string} rootDir
 * @returns {{parentImport: string, staleRemoved: string[]}}
 */
export function performWrap(specifier, target, destFile, rootDir) {
    const isDirectoryBased = isDirectoryBasedFile(resolveSourceFile(specifier, rootDir));

    const staleRemoved = isDirectoryBased
        ? removeStaleFiles(path.dirname(destFile), destFile, rootDir)
        : [];

    const parentImport = resolveParentImport(specifier, target, rootDir);
    const scaffold = generateWrapScaffold(specifier, target, parentImport);
    fs.mkdirSync(path.dirname(destFile), {recursive: true});
    fs.writeFileSync(destFile, scaffold);
    return {parentImport, staleRemoved};
}
