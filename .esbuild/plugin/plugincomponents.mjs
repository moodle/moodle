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

/**
 * Core esbuild library for Moodle ESM components.
 *
 * This is the single source of all build logic. The Grunt tasks
 * (.grunt/tasks/react.js) import from here rather than duplicating
 * configuration or build steps.
 *
 * Source layout convention:
 *   <component>/js/esm/src/**\/*.{ts,tsx}  →  <component>/js/esm/build/**\/*.js
 *
 * Exports:
 *   buildPluginComponents()            Glob for every js/esm/src tree across
 *                                      core and plugins, compile all in parallel.
 *   watchComponents(onRebuild)         Start esbuild's native watch mode so the
 *                                      compiler rebuilds affected files on save.
 *
 * @copyright  2026 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import esbuild from "esbuild";
import { glob } from "glob";
import chalk from "chalk";
import path from "path";
import fs from "fs";
import { getOwningComponentDirectory } from "../../.grunt/components.js";

const projectRoot = process.cwd();

/**
 * Resolve a path from the current project root.
 *
 * @param {...string} segments Path segments to resolve.
 * @returns {string} Absolute path from project root.
 */
const fromRoot = (...segments) => path.resolve(projectRoot, ...segments);

/**
 * Create an incremental dot progress indicator.
 * Prints one dot per completed build and wraps lines for readability.
 *
 * @param {number} total Total number of items to build.
 * @returns {{ start: () => void, tick: () => void, succeed: (msg: string) => void, fail: (msg: string) => void }}
 */
function createDotProgress(total) {
    const dotsPerLine = 60;
    let printed = 0;

    return {
        start: () => {
            process.stdout.write(`${chalk.cyan("Building")} `);
        },
        tick: () => {
            printed++;
            process.stdout.write(chalk.cyan("."));
            if (printed % dotsPerLine === 0 && printed < total) {
                process.stdout.write(` ${chalk.dim(`[${printed}/${total}]`)}\n`);
            }
        },
        succeed: (msg) => {
            process.stdout.write(`  ${chalk.dim(`[${printed}/${total}]`)}\n`);
            process.stdout.write(`${chalk.green("✓")} ${msg}\n`);
        },
        fail: (msg) => {
            process.stdout.write(`  ${chalk.dim(`[${printed}/${total}]`)}\n`);
            process.stdout.write(`${chalk.red("✗")} ${msg}\n`);
        },
    };
}

/**
 * Build a single React component entry file.
 *
 * @param {string} entry Absolute entry file path.
 * @param {import('esbuild').BuildOptions} buildConfig Shared esbuild configuration.
 * @returns {Promise<{file: string, output?: string, error: Error|null}>}
 */
async function buildComponent(entry, buildConfig) {
    const resolved = resolveComponentPaths(entry);
    if (!resolved) {
        return { file: entry, error: new Error(`Unknown path pattern: ${entry}`) };
    }

    const { file, output } = resolved;
    fs.mkdirSync(path.dirname(output), { recursive: true });

    try {
        const prodBuild = esbuild.build({
            ...buildConfig,
            entryPoints: [entry],
            outfile: output,
            define: {
                'process.env.NODE_ENV': '"production"',
            },
        });

        const devBuild = esbuild.build({
            ...buildConfig,
            entryPoints: [entry],
            outfile: output.replace(/\.js$/, '.dev.js'),
            define: {
                'process.env.NODE_ENV': '"development"',
            },
        });

        await Promise.all([
            prodBuild,
            devBuild,
        ]);

        return { file, output, error: null };
    } catch (error) {
        return { file, error: error instanceof Error ? error : new Error(String(error)) };
    }
}

const configPlugin = () => ({
    name: 'moodle-build-config',
    setup(build) {
        const options = build.initialOptions
        options.define = options.define || {}

        if (options.define['process.env.NODE_ENV'] === '"development"') {
            const getOwnerDirectory = (entry) => {
                if (options.outdir) {
                    return getOwningComponentDirectory(entry.in);
                } else {
                    return getOwningComponentDirectory(entry);
                }
            };
            const owningComponent = getOwnerDirectory(options.entryPoints?.[0] ?? '');

            options.jsxDev = true;
            options.keepNames = true;
            options.minify = false;
            options.sourcemap = 'linked';
            // This path is relative to `/core/esm/[revision]/[component]/js/esm/build` because that's where the output files are written.
            // By specifying the sourceRoot it is possible to link the source files via a workspace in the browser for in-browser editing of code.
            options.sourceRoot = `../../../../../../sources/${owningComponent}/js/esm/build`;
            options.treeShaking = false;
        } else {
            options.jsxDev = false;
            options.keepNames = false;
            options.minify = true;
            options.sourcemap = false;
            options.treeShaking = true;
        }
    },
});

/**
 * Resolve source and output paths for a component entry.
 *
 * @param {string} entry Absolute component source path.
 * @returns {{file: string, output: string} | null} Relative file info and output path, or null for unsupported paths.
 */
function resolveComponentPaths(entry) {
    const rel = path.relative(projectRoot, entry);

    if (rel.includes(path.join('esm', 'src'))) {

        const [part, rawFile] = rel.split(path.join('esm', 'src'));
        const file = rawFile.replace(/^[\/\\]/, '');

        return {
            file,
            output: fromRoot(part, 'esm', 'build', file.replace(/\.(ts|tsx)$/, '.js')),
        };
    }
    return null;
}

/**
 * Run all builds in parallel, showing incremental dot progress.
 *
 * @param {string[]} entryPoints Absolute entry file paths.
 * @param {import('esbuild').BuildOptions} buildConfig Shared esbuild configuration.
 * @returns {Promise<{errors: {file: string, output?: string, error: Error|null}[]}>}
 */
async function runParallelBuilds(entryPoints, buildConfig) {
    const total = entryPoints.length * 3;
    /** @type {{file: string, output?: string, error: Error|null}[]} */
    const errors = [];
    const startTime = Date.now();

    const progress = createDotProgress(total);
    progress.start();

    await Promise.all(entryPoints.map(async (entry) => {
        const result = await buildComponent(entry, buildConfig);
        if (result.error) {
            errors.push(result);
        }
        progress.tick();
        progress.tick();
        progress.tick();
    }));

    const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
    const succeeded = total - errors.length;

    if (errors.length > 0) {
        progress.fail(`${succeeded}/${total} built · ${elapsed}s`);
        for (const e of errors) {
            console.error(chalk.red(`    ✗ ${e.file}: ${e.error?.message ?? ''}`));
        }
    } else {
        progress.succeed(chalk.bold(`${total} components built`) + chalk.dim(` · ${elapsed}s`));
    }

    return { errors };
}

/**
 * An esbuild plugin that externalizes relative imports within the same esm/src/ directory.
 *
 * This prevents sibling modules from being inlined into each output bundle, so that
 * a change to a dependency (e.g. Storage) does not alter the built output of
 * its consumers (e.g. LocalStorage, SessionStorage).
 *
 * Relative imports are rewritten from their source extension (.ts/.tsx) to .js in the output.
 *
 * @returns {import('esbuild').Plugin}
 */
function externalRelativeImports() {
    return {
        name: 'external-relative-imports',
        setup(build) {
            // Match relative imports (starting with ./ or ../).
            build.onResolve({ filter: /^\.\.?\// }, (args) => {
                // Only externalize imports within esm/src trees.
                if (!args.importer || !args.importer.includes(path.join('esm', 'src'))) {
                    return null;
                }

                // Resolve the full path to determine if it's within the same esm/src tree.
                const resolved = path.resolve(path.dirname(args.importer), args.path);
                if (!resolved.includes(path.join('esm', 'src'))) {
                    return null;
                }

                // Rewrite the extension to remove any extension.
                let externalPath = args.path;
                if (/\.(ts|tsx|js|jsx)$/.test(externalPath)) {
                    externalPath = externalPath.replace(/\.(ts|tsx|js|jsx)$/, '');
                }

                return { path: externalPath, external: true };
            });
        },
    };
}

/**
 * Create the shared esbuild build configuration.
 *
 * @returns {import('esbuild').BuildOptions} esbuild configuration object.
 */
function createBuildConfig() {
    return {
        bundle: false,
        format: "esm",
        jsx: "automatic",
        treeShaking: true,
        plugins: [configPlugin(), externalRelativeImports()],
        define: { 'process.env.NODE_ENV': '"production"' },
    };
}

/**
 * Build all plugin and core React components.
 *
 * @returns {Promise<void>}
 */
export async function buildPluginComponents() {
    console.log(chalk.green('> Building components...'));

    const entryPoints = glob.sync("**/js/esm/src/**/*.{ts,tsx}", {
        cwd: projectRoot,
        absolute: true,
        ignore: [
            `${process.cwd()}/node_modules/**`,
            `${process.cwd()}/vendor/**`,
        ],
    });

    const buildConfig = createBuildConfig();

    const { errors } = await runParallelBuilds(entryPoints, buildConfig);

    if (errors.length > 0) {
        throw new Error(`React build failed: ${errors.length} component(s) could not be built`);
    }
}

const getProductionBuild = (entryPoints, buildConfig, watchReporter) => {
    const entryPairs = entryPoints.flatMap(entry => {
        const resolved = resolveComponentPaths(entry);
        if (!resolved) {
            return [];
        }
        fs.mkdirSync(path.dirname(resolved.output), { recursive: true });
        return [{ in: entry, out: path.relative(projectRoot, resolved.output).replace(/\.js$/, '') }];
    });

    return esbuild.context({
        ...buildConfig,
        entryPoints: entryPairs,
        outdir: projectRoot,
        metafile: true,
        plugins: [...(buildConfig.plugins || []), watchReporter],
        define: {
            'process.env.NODE_ENV': '"production"',
        },
    });
};

const getDevelopmentBuild = (entryPoints, buildConfig, watchReporter) => {
    // Map each source file to an {in, out} pair so esbuild can write each
    // component to its custom output directory while sharing a single context.
    // The 'out' path is relative to outdir (projectRoot) and has no extension —
    // esbuild appends the appropriate extension automatically.
    // Build entry pairs and ensure output directories exist in a single pass.
    const entryPairs = entryPoints.flatMap(entry => {
        const resolved = resolveComponentPaths(entry);
        if (!resolved) {
            return [];
        }
        fs.mkdirSync(path.dirname(resolved.output), { recursive: true });
        return [{ in: entry, out: path.relative(projectRoot, resolved.output).replace(/\.js$/, '.dev') }];
    });

    return esbuild.context({
        ...buildConfig,
        entryPoints: entryPairs,
        outdir: projectRoot,
        metafile: true,

        plugins: [...(buildConfig.plugins || []), watchReporter],
        define: {
            'process.env.NODE_ENV': '"development"',
        },
    });
};
// Report build results to the terminal after every build (initial and on each change).
// metafile: true populates result.metafile.outputs so we know which files were written.
// On a rebuild only the affected outputs appear, so it effectively names the changed file.
/** @type {import('esbuild').Plugin} */
const getWatchReporter = (onRebuild) => ({
    name: 'watch-reporter',
    setup(build) {
        let isInitial = true;
        let startTime = 0;

        build.onStart(() => {
            startTime = Date.now();
        });

        build.onEnd((result) => {
            const now = new Date().toLocaleTimeString();
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
            const isDev = build.initialOptions.jsxDev;
            const buildType = isDev ? 'development' : 'production';

            if (result.errors.length > 0) {
                console.error(chalk.red(`[${now}] ✗ ${buildType} build failed (${result.errors.length} error(s))`) + chalk.dim(` · ${elapsed}s`));
                return;
            }

            const outputs = Object.keys(result.metafile?.outputs ?? {});

            console.log(chalk.green(`[${now}] ✓ ${outputs.length} ${buildType} component(s) built`) + chalk.dim(` · ${elapsed}s`));

            if (isInitial) {
                isInitial = false;
            } else if (onRebuild) {
                // entryPoint is the source file (relative to projectRoot) that triggered
                // this rebuild. Pass it to the caller so they can run follow-up tasks
                // (e.g. linting) without this module needing to know about them.
                const srcFiles = Object.values(result.metafile?.outputs ?? {})
                    .map(output => output.entryPoint)
                    .filter(/** @param {string|undefined} f */ f => !!f);
                onRebuild(/** @type {string[]} */(srcFiles));
            }
        });
    },
});

/**
 * Start esbuild in native watch mode over all React components.
 *
 * Creates a single incremental build context for all entry points so that
 * esbuild can reuse its internal graph between rebuilds instead of starting
 * from scratch on every file change.
 *
 * @param {((srcFiles: string[]) => void) | undefined} [onRebuild] Called with the rebuilt entry source
 *        files (relative to project root) after each non-initial successful rebuild. Use this to run
 *        follow-up tasks such as linting without coupling them to this module.
 * @returns {Promise<import('esbuild').BuildContext|null>} The active context, or null if no source files exist.
 */
export async function watchComponents(onRebuild) {
    const entryPoints = glob.sync("**/js/esm/src/**/*.{ts,tsx}", {
        cwd: projectRoot,
        absolute: true,
        ignore: [
            `${process.cwd()}/node_modules/**`,
            `${process.cwd()}/vendor/**`,
        ],
    });

    if (entryPoints.length === 0) {
        return null;
    }

    const buildConfig = createBuildConfig();

    const [
        productionContext,
        developmentContext,
    ] = await Promise.all([
        getProductionBuild(entryPoints, buildConfig, getWatchReporter(onRebuild)),
        // Do not pass a callback for dev builds - we only need to perform the callbacks once.
        getDevelopmentBuild(entryPoints, buildConfig, getWatchReporter()),
    ]);

    await Promise.all([
        productionContext.watch(),
        developmentContext.watch(),
    ]);

    return [
        productionContext,
        developmentContext,
    ];
}
