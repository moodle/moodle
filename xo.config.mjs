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
 * XO configuration for Moodle TypeScript sources.
 *
 * XO lints TypeScript only. AMD and YUI JavaScript stay on ESLint via .eslintrc.
 * Run it with `grunt xo`, which supplies the source globs; XO is not invoked bare
 * because it would otherwise discover every .html and .md file in the tree.
 *
 * Every override below defers to a Moodle convention that XO's defaults contradict.
 * Do not add overrides for rules that are merely inconvenient — see MDLSITE-8249.
 *
 * @copyright  2026 Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {createRequire} from 'node:module';
import reactHooks from 'eslint-plugin-react-hooks';

const require = createRequire(import.meta.url);
const ComponentList = require('./.grunt/components.js');

/** @type {import('xo').FlatXoConfig} */
const xoConfig = [
    {
        // The third-party libraries and component .eslintignore entries that ESLint skips.
        ignores: [...ComponentList.getThirdPartyPaths(), ...ComponentList.getLocalIgnorePaths('.eslintignore')],
    },
    {
        files: ['**/js/esm/src/**/*.ts', '**/js/esm/src/**/*.tsx'],
        space: 4,
        plugins: {'react-hooks': reactHooks},
        rules: {
            // The hooks rules only. eslint-plugin-react, which XO's own React config also
            // pulls in, calls context.getFilename() and so crashes on the ESLint 10 that XO
            // bundles. Most of what it checks — prop types, display names — TypeScript
            // already covers. Revisit when it supports ESLint 10. The plugin's presets also
            // carry the React Compiler rules, which are left for a separate decision.
            'react-hooks/rules-of-hooks': 'error',
            'react-hooks/exhaustive-deps': 'warn',

            // Moodle docblocks prefix continuation lines with an asterisk; XO defaults to 'never'.
            'jsdoc/require-asterisk-prefix': ['error', 'always'],

            // @license carries the GPL URL and @since carries a Moodle release, neither of which
            // is the SPDX expression or semver string this rule expects.
            'jsdoc/check-values': 'off',

            // An options bag still needs its own @param, but its keys are described once on
            // the options type. Repeating each key in the docblock duplicates that.
            'jsdoc/require-param': ['error', {
                // XO's defaults, with checkDestructured flipped off.
                ignoreWhenAllParamsMissing: true,
                contexts: ['ArrowFunctionExpression', 'FunctionDeclaration', 'FunctionExpression'],
                autoIncrementBase: 0,
                checkConstructors: true,
                checkDestructured: false,
                checkDestructuredRoots: true,
                checkGetters: false,
                checkRestProperty: false,
                checkSetters: false,
            }],
            'jsdoc/check-param-names': ['error', {checkDestructured: false}],

            // Core's file headers, TypeScript included, cite http://moodle.org/ and
            // http://www.gnu.org/copyleft/gpl.html.
            'unicorn/prefer-https': 'off',

            // A module's filename is its public import specifier, so it follows Moodle module
            // naming (snake_case, as in core/react_autoinit) rather than XO's kebab-case.
            // React components are PascalCase and utility modules camelCase.
            'unicorn/filename-case': ['error', {cases: {camelCase: true, pascalCase: true, snakeCase: true}}],

            // The import-x fixer merges "import type {Bar}" into "import Foo" from the same module
            // as "import Foo, type {Bar}", which does not parse; prefer-inline does not cover a
            // default import. ESLint's own rule reports duplicates without fixing them.
            'import-x/no-duplicates': 'off',
            'no-duplicate-imports': ['error', {allowSeparateTypeImports: true}],

            // These rules recommend Array#toSorted (ES2023) and Promise.withResolvers
            // (ES2024). Both are newer than the browsers listed under "browserslist" in
            // package.json, and esbuild sets no target, so neither is downlevelled or
            // polyfilled. Revisit when that list moves.
            'unicorn/no-array-sort': 'off',
            'unicorn/prefer-promise-with-resolvers': 'off',

            // Same reason: Array#at (ES2022), logical assignment such as ??= (ES2021) and the
            // regex v flag (ES2024) are newer than browserslist. The syntax ones are a
            // SyntaxError on load there, which takes the whole bundle down. The u flag is ES2015.
            'unicorn/prefer-at': 'off',
            'unicorn/logical-assignment-operators': 'off',

            // Top-level await (ES2022) needs Safari 15, so a module using it does not load in
            // the older Safari and iOS that browserslist lists.
            'unicorn/prefer-top-level-await': 'off',
            '@typescript-eslint/prefer-nullish-coalescing': ['error', {
                // XO's options plus ignoreIfStatements, so an explicit if-assign is not pushed to ??=.
                ignoreTernaryTests: false,
                ignoreConditionalTests: false,
                ignoreMixedLogicalExpressions: false,
                ignoreIfStatements: true,
            }],
            'require-unicode-regexp': ['error', {requireFlag: 'u'}],

            // The rule still expands the rest of its word list. These are exempt because
            // expanding them makes the name worse: "params" and "utils" are the published
            // option key and module name, "props" is React's own term for a component's
            // inputs, and "func"/"args" would become "function_"/"arguments_", which carry
            // a trailing underscore only to dodge a reserved word. "repository" is the name core
            // gives every web service module, and the rule would shorten it to "repo".
            'unicorn/name-replacements': ['error', {
                replacements: {
                    params: false, utils: false, func: false, args: false, props: false, repository: false,
                },
            }],

            // XO's list with null removed. Its autofix rewrites the type but not the value, and
            // the values are null by contract: Storage.getItem, useRef, and web service args,
            // where null is sent and undefined is dropped from the request.
            '@typescript-eslint/no-restricted-types': ['error', {
                types: {
                    object: {message: 'Use `Record<string, unknown>` instead.', fixWith: 'Record<string, unknown>'},
                    Buffer: {message: 'Use Uint8Array instead.', suggest: ['Uint8Array']},
                    '[]': 'Don\'t use the empty array type `[]`. Use `SomeType[]` instead.',
                },
            }],

            // Moodle option names mirror the server-side parameter names they are sent as
            // (loginrequired, nosessionupdate), so they do not carry an English boolean prefix.
            'unicorn/consistent-boolean-name': 'off',

            // Match the 132-column limit that .eslintrc applies to AMD and YUI sources.
            '@stylistic/max-len': ['error', {code: 132, tabWidth: 4, ignoreUrls: true}],

            // XO's strictCamelCase is kept for the names Moodle chooses. The relaxations
            // below cover names it does not choose, which cannot be renamed to satisfy a
            // linter without changing a wire format or an existing API.
            '@typescript-eslint/naming-convention': [
                'error',
                {
                    // PascalCase is permitted because JSX resolves a lowercase tag to an
                    // HTML element, so a React component must start with a capital.
                    selector: ['variable', 'function'],
                    format: ['strictCamelCase', 'StrictPascalCase', 'UPPER_CASE'],
                    leadingUnderscore: 'allowSingleOrDouble',
                    trailingUnderscore: 'allow',
                },
                {
                    selector: ['classProperty', 'parameterProperty', 'classMethod', 'objectLiteralMethod', 'accessor'],
                    format: ['strictCamelCase'],
                    leadingUnderscore: 'allowSingleOrDouble',
                    trailingUnderscore: 'allow',
                },
                {
                    // Object keys and type members frequently name something external: HTTP
                    // headers such as Accept, console.table column labels, log level maps,
                    // and the snake_case M.util and M.str globals these modules declare.
                    selector: ['objectLiteralProperty', 'typeMethod', 'typeProperty'],
                    format: null,
                },
                {
                    // Generic parameters follow the TypeScript T-prefix convention used by
                    // lib.es5.d.ts, which the Pending promise signatures mirror.
                    selector: 'typeParameter',
                    format: ['PascalCase'],
                },
                {selector: 'typeLike', format: ['StrictPascalCase']},
            ],
        },
    },
];

export default xoConfig;
