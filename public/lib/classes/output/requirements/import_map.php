<?php
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

namespace core\output\requirements;

/**
 * The import map requirement class, which defines the import map for ES module loading.
 *
 * This class is responsible for defining the import map that will be used by the ES module loader to
 * resolve module specifiers to URLs.
 *
 * A default loader URL should be set for the import map, which will be used for any specifiers
 * that do not have a specific loader defined.
 *
 * The import map can be extended by adding additional imports with specific loaders, or overriding
 * the standard loaders, during a pre_render hook.
 *
 * The import map will be serialized to JSON and included in the page output as a script tag with type "importmap".
 *
 * The class should be fetched using the dependency injection container, and the default loader URL
 * should be set before the page is rendered.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_map implements \JsonSerializable {
    /** @var array The list of imports */
    protected array $imports = [];

    /**
     * @var bool Whether $imports has been sorted longest-key-first for prefix matching.
     *
     * The flag is reset to false whenever add_import() is called so the sort is re-applied
     * if new entries are registered after the first resolution.
     */
    private bool $importssorted = false;

    /** @var \core\url The default loader URL to use */
    protected \core\url $loader;

    /**
     * Initialise the import_map requirement by setting the standard import list.
     */
    public function __construct() {
        $this->add_standard_imports();
    }

    /**
     * Prepare the content for json encoding.
     *
     * @return array[]|array{imports: array}
     */
    public function jsonSerialize(): array {
        $importmap = [
            'imports' => [],
        ];

        if (!isset($this->loader)) {
            throw new \core\exception\coding_exception('Default loader URL must be set before serializing the import map.');
        }

        foreach ($this->imports as $specifier => $importdata) {
            $loader = $importdata->loader instanceof \core\url
                ? $importdata->loader
                : new \core\url($this->loader->out(false) . $specifier);
            $importmap['imports'][$specifier] = $loader->out(false);
        }

        return $importmap;
    }

    /**
     * Set the default loader URL.
     *
     * @param \core\url $loader The default loader URL.
     */
    public function set_default_loader(\core\url $loader): void {
        $this->loader = $loader;
    }

    /**
     * Add the standard entries to the importmap.
     * @return void
     */
    protected function add_standard_imports(): void {
        $this->add_import(
            '@moodle/lms/',
            path: 'js/esm/build',
            loadfromcomponent: true,
        );
        $this->add_import(
            '@moodlehq/design-system',
            path: 'lib/js/bundles/design-system/index.js',
        );
        $this->add_import(
            'react',
            path: 'lib/js/bundles/react/react',
        );
        $this->add_import(
            'react/',
            path: 'lib/js/bundles/react',
        );
        $this->add_import(
            'react-dom',
            path: 'lib/js/bundles/react-dom/react-dom',
        );
        $this->add_import(
            'react-dom/',
            path: 'lib/js/bundles/react-dom',
            modifier: $this->resolve_react_dev_path(...),
        );
    }

    /**
     * Modifier for React imports that resolves to the unminified development build when in developer mode.
     *
     * When the JS revision is -1 (developer mode / cachejs disabled), this substitutes the
     * `.development.js` variant of a React bundle if it exists on disk, giving better stack
     * traces and warnings during development.
     *
     * @param int $revision The JS revision number (-1 signals developer mode).
     * @param string $requestedpath The bare specifier path that was requested.
     * @param string $resolvedpath The resolved absolute filesystem path.
     * @return string The (possibly substituted) absolute filesystem path to serve.
     */
    protected function resolve_react_dev_path(int $revision, string $requestedpath, string $resolvedpath): string {
        if ($revision === -1) {
            // During development, resolve to the unminified version of React for better debugging.
            $unminifiedpath = str_replace('.js', '.development.js', $resolvedpath);
            if (file_exists($unminifiedpath)) {
                return $unminifiedpath;
            }
        }

        return $resolvedpath;
    }

    /**
     * Register a specifier in the import map.
     *
     * @param string $specifier The bare specifier used in import statements (e.g. `react`, `@moodle/lms/`).
     * @param \core\url|null $loader Absolute URL written verbatim into the import map.
     *   When provided, $path is ignored for URL generation.
     * @param string|null $path Filesystem path relative to $CFG->root, used by the ESM controller
     *   to locate the file on disk. Has no effect on the URL in the import map.
     * @param bool $loadfromcomponent When true, the specifier is treated as a `<component>/<module>`
     *   prefix and resolved to the component's `js/esm/build/` directory. Used internally for `@moodle/lms/`.
     * @param string $suffix File extension suffix appended when resolving filesystem paths (defaults to `.js`).
     * @param callable|null $modifier Optional callable (int $revision, string $requestedpath, string $resolvedpath): string
     *   to transform the resolved filesystem path before the file is served. Not used for URL generation.
     * @param string[] $allowedsuffixes List of allowed suffixes for the resolved file.
     *   If the resolved path already ends with one of these suffixes, the default suffix will not be appended.
     *   Defaults to ['.js', '.js.map'] so that source maps are served without double-suffix mangling.
     */
    public function add_import(
        string $specifier,
        ?\core\url $loader = null,
        ?string $path = null,
        bool $loadfromcomponent = false,
        string $suffix = '.js',
        ?callable $modifier = null,
        array $allowedsuffixes = ['.js', '.js.map'],
    ): void {
        if (!in_array($suffix, $allowedsuffixes, true)) {
            $allowedsuffixes[] = $suffix;
        }
        $this->imports[$specifier] = (object) [
            'loader' => $loader,
            'path' => $path,
            'loadfromcomponent' => $loadfromcomponent,
            'suffix' => $suffix,
            'allowedsuffixes' => $allowedsuffixes,
            'modifier' => $modifier,
        ];
        $this->importssorted = false;
    }

    /**
     * Resolve a bare specifier path to an absolute filesystem path.
     *
     * Entries are matched longest-key-first so a more-specific prefix always wins
     * (e.g. `react/` is matched before `react`). Returns null if no entry matches.
     *
     * @param int $revision The JS revision number, used for modifier callables to determine if in developer mode.
     * @param string $requestedpath The bare specifier path (e.g. `react`, `@moodle/lms/mod_book/viewer`).
     * @return string|null Absolute filesystem path to the JS file, or null if unresolved.
     */
    public function get_path_for_script(
        int $revision,
        string $requestedpath,
    ): ?string {
        global $CFG;

        // Sort longest-key-first once so a more-specific prefix always wins over a shorter one.
        if (!$this->importssorted) {
            uksort($this->imports, fn ($a, $b) => strlen($b) <=> strlen($a));
            $this->importssorted = true;
        }

        foreach ($this->imports as $specifier => $importdata) {
            if (!str_starts_with($requestedpath, $specifier)) {
                continue;
            }

            if ($importdata->loader !== null) {
                throw new \core\exception\coding_exception(
                    'Import map entries with explicit loaders cannot be resolved to filesystem paths.',
                );
            }

            if ($importdata->loadfromcomponent) {
                $subpath = substr($requestedpath, strlen($specifier));
                $resolved = $this->resolve_module_identifier($importdata, $subpath);
                if ($importdata->modifier !== null) {
                    $resolved = ($importdata->modifier)($revision, $requestedpath, $resolved);
                }
                return $resolved;
            }

            $pathremainder = substr($requestedpath, strlen($specifier));
            // Reject '..' as a path segment to prevent directory traversal. A single dot in a
            // filename (e.g. 'button.small') is allowed because it is not a segment on its own.
            if (in_array('..', explode('/', $pathremainder), true)) {
                return null;
            }

            $resolved = implode('/', array_filter([
                $CFG->root,
                $importdata->path,
                $pathremainder,
            ]));

            $suffixpresent = false;
            foreach ($importdata->allowedsuffixes as $allowedsuffix) {
                if (str_ends_with($resolved, $allowedsuffix) && file_exists($resolved)) {
                    $suffixpresent = true;
                }
            }

            if (!$suffixpresent) {
                // If the requested path already ends with an accepted suffix, don't try appending it again.
                // This allows specifiers to include the suffix if needed.
                $resolved .= $importdata->suffix;
            }

            if ($importdata->modifier !== null) {
                $resolved = ($importdata->modifier)($revision, $requestedpath, $resolved);
            }

            return $resolved;
        }

        return null;
    }

    /**
     * Resolve a `<component>/<module>` subpath to an absolute filesystem path.
     *
     * For example, `mod_book/viewer` resolves to
     * `<dirroot>/mod/book/js/esm/build/viewer.js`.
     *
     * @param object $importdata The import entry containing the path and loadfromcomponent flag.
     * @param string $subpath The subpath after the specifier prefix (e.g. `mod_book/viewer`).
     * @return string Absolute path to the JS file.
     * @throws \core\exception\not_found_exception If the subpath is missing a slash, contains `..`,
     *   the component is unknown, or the resolved file does not exist.
     */
    protected function resolve_module_identifier(object $importdata, string $subpath): string {
        if (!str_contains($subpath, '/')) {
            throw new \core\exception\not_found_exception('component', $subpath);
        }

        [$component, $modulerest] = explode('/', $subpath, 2);

        // Reject '..' as a path segment to prevent directory traversal. A single dot in a
        // filename (e.g. 'button.small') is allowed because it is not a segment on its own.
        if (in_array('..', explode('/', $modulerest), true)) {
            throw new \core\exception\not_found_exception('script', $subpath);
        }

        // Resolve the component directory; an unknown component name returns null.
        $dir = \core\component::get_component_directory($component);
        $file = "{$dir}/{$importdata->path}/{$modulerest}";

        foreach ($importdata->allowedsuffixes as $allowedsuffix) {
            if (str_ends_with($file, $allowedsuffix) && file_exists($file)) {
                return $file;
            }
        }

        $file .= $importdata->suffix;
        if (file_exists($file)) {
            return $file;
        }

        throw new \core\exception\not_found_exception('script', $subpath);
    }
}
