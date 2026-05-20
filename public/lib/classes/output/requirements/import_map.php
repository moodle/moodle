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
    /** @var string The name of the default theme */
    private const string DEFAULT_THEME_PLACEHOLDER = 'original';

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

    /** @var string|null The current theme name, used for resolving theme overrides in component-based imports. */
    protected ?string $currenttheme = null;

    /** @var string[] List of available themes, used to validate theme overrides in component-based imports. */
    protected array $availablethemes = [];

    /**
     * Initialise the import_map requirement by setting the standard import list.
     *
     * @param \core\hook\manager $hookmanager The hook manager to dispatch the before_import_map_config event.
     */
    public function __construct(\core\hook\manager $hookmanager) {
        $this->add_standard_imports();

        $hookmanager->dispatch(new \core\hook\output\before_import_map_config($this));
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

        $themes = array_map(
            fn(string $theme): string => "theme-{$theme}",
            [self::DEFAULT_THEME_PLACEHOLDER, ...$this->availablethemes]
        );

        foreach ($this->imports as $specifier => $importdata) {
            if ($importdata->themable) {
                // This import is themable.
                // If we are aware of the current theme, we include it in the import map entry's path.
                // This allows the loader to check for a theme-specific override before falling back to the default path.
                $currenttheme = $this->currenttheme ? "/theme-{$this->currenttheme}" : '';

                // Ensure that we handle trailing slashes correctly by preserving them in the generated import map keys and URLs.
                $endswith = str_ends_with($specifier, '/') ? '/' : '';
                $specifier = rtrim($specifier, '/');

                // For the default entry (without an explicit theme) we resolve to the _current_ theme (if set).
                // That is `@moodle/lms` resolves to `@moodle/lms/theme-boost` when the current theme is boost.
                $themespecifier = "{$specifier}{$currenttheme}{$endswith}";
                $importmap['imports']["{$specifier}{$endswith}"] = $this->loader_url_for($importdata, $themespecifier);

                foreach ($themes as $theme) {
                    // Add an entry for each available theme, so that any theme-specific overrides are usable.
                    // This allows a theme-override to include another theme's override, or the default version when
                    // wrapping a component with a theme-specific version.
                    $themespecifier = "{$specifier}/{$theme}{$endswith}";
                    $importmap['imports'][$themespecifier] = $this->loader_url_for($importdata, $themespecifier);
                }
            } else {
                // This import is not themable, so we generate a single entry without any theme prefix.
                $importmap['imports'][$specifier] = $this->loader_url_for($importdata, $specifier);
            }
        }

        return $importmap;
    }

    /**
     * Set the list of available themes by name.
     *
     * @param string[] $themes
     */
    public function set_available_themes(array $themes): void {
        $this->availablethemes = $themes;
    }

    /**
     * Set the default loader URL.
     *
     * @param \core\url $loader The default loader URL.
     */
    public function set_default_loader(
        \core\url $loader,
    ): void {
        $this->loader = $loader;
    }

    /**
     * Specify the theme in use by the current user in the current context.
     *
     * @param string|null $currenttheme The current theme name.
     */
    public function set_current_theme(?string $currenttheme): void {
        $this->currenttheme = $currenttheme;
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
            themable: true,
        );
        $this->add_import(
            '@moodlehq/design-system',
            path: 'lib/bundles/design-system/js/',
            urlsuffix: '/index.js',
            // Allow theme designers to override the design system components.
            themable: true,
        );
        $this->add_import(
            'react',
            path: 'lib/bundles/react/react',
            themable: false,
        );
        $this->add_import(
            'react/',
            path: 'lib/bundles/react',
            themable: false,
        );
        $this->add_import(
            'react-dom',
            path: 'lib/bundles/react-dom/react-dom',
            themable: false,
        );
        $this->add_import(
            'react-dom/',
            path: 'lib/bundles/react-dom',
            devreplacements: [
                '/\.js$/' => '.development.js',
                '/\.js.map$/' => '.development.js.map',
            ],
            themable: false,
        );
        $this->add_import(
            '@popperjs/core',
            path: 'lib/bundles/@popperjs/core/core.js',
        );
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
     * @param string $urlsuffix Extra path appended to the specifier in the import map URL.
     *   Use this when the entry resolves to a directory's index file so that relative imports
     *   within the module resolve correctly (e.g. '/index.js' for a package-style module).
     * @param bool $themable Whether the import is themable.
     */
    public function add_import(
        string $specifier,
        ?\core\url $loader = null,
        ?string $path = null,
        bool $loadfromcomponent = false,
        string $suffix = '.js',
        ?array $devreplacements = null,
        ?callable $modifier = null,
        array $allowedsuffixes = ['.js', '.js.map'],
        string $urlsuffix = '',
        bool $themable = false,
    ): void {
        if (!in_array($suffix, $allowedsuffixes, true)) {
            $allowedsuffixes[] = $suffix;
        }

        $devreplacements = $devreplacements ?? [
            '/\.js$/' => '.dev.js',
            '/\.js.map$/' => '.dev.js.map',
        ];
        $this->imports[$specifier] = (object) [
            'loader' => $loader,
            'path' => $path,
            'loadfromcomponent' => $loadfromcomponent,
            'suffix' => $suffix,
            'devreplacements' => $devreplacements,
            'allowedsuffixes' => $allowedsuffixes,
            'modifier' => $modifier,
            'urlsuffix' => $urlsuffix,
            'themable' => $themable,
        ];
        $this->importssorted = false;
    }

    /**
     * Return the URL string for an import map entry.
     *
     * When the import was registered with an explicit \core\url loader that URL is returned
     * verbatim; otherwise $specifier is appended to the default loader base URL.
     *
     * @param \stdClass $importdata The import entry metadata.
     * @param string $specifier The fully-qualified specifier string to append to the base URL.
     * @return string
     */
    private function loader_url_for(\stdClass $importdata, string $specifier): string {
        return $importdata->loader instanceof \core\url
            ? $importdata->loader->out(false)
            : $this->loader->out(false) . $specifier . $importdata->urlsuffix;
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
            $matches = [];
            $expression = "#^(?<specifier>{$specifier}(/?theme-(?<theme>[^/]*)/)?)#";
            if (!preg_match($expression, $requestedpath, $matches)) {
                continue;
            }

            if ($importdata->loader !== null) {
                throw new \core\exception\coding_exception(
                    'Import map entries with explicit loaders cannot be resolved to filesystem paths.',
                );
            }

            if ($importdata->loadfromcomponent) {
                $subpath = substr($requestedpath, strlen($matches['specifier']));
                $resolved = $this->resolve_module_identifier(
                    $importdata,
                    $subpath,
                    $revision,
                    theme: $matches['theme'] ?? null,
                );
                if ($importdata->modifier !== null) {
                    $resolved = ($importdata->modifier)($revision, $requestedpath, $resolved);
                }
                return $resolved;
            }

            $pathremainder = substr($requestedpath, strlen($matches['specifier']));
            // Reject '..' as a path segment to prevent directory traversal. A single dot in a
            // filename (e.g. 'button.small') is allowed because it is not a segment on its own.
            if (in_array('..', explode('/', $pathremainder), true)) {
                return null;
            }

            // Get possible paths, including any theme override.
            $paths = $this->get_possible_paths(
                $CFG->root,
                $importdata,
                $matches['theme'] ?? null,
                $specifier,
                $pathremainder,
            );

            foreach ($paths as $resolved) {
                $result = $this->resolve_candidate_path($resolved, $importdata, $revision, $requestedpath);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Resolve a single candidate filesystem path to the best matching file.
     *
     * Appends the default suffix when the path does not already end with a recognised suffix
     * that corresponds to an existing file, applies the optional modifier callable, then returns
     * the dev variant (when revision is -1 and it exists) or the production file if it exists on
     * disk. Returns null when neither file is found.
     *
     * @param string $path Candidate path to resolve (may or may not already carry a suffix).
     * @param \stdClass $importdata The import entry metadata.
     * @param int $revision The JS revision number; -1 triggers dev-file lookup.
     * @param string|null $requestedpath When non-null, passed to the modifier callable (if any).
     * @return string|null Absolute path to the file, or null if not found.
     */
    protected function resolve_candidate_path(
        string $path,
        \stdClass $importdata,
        int $revision,
        ?string $requestedpath = null,
    ): ?string {
        $suffixpresent = false;
        foreach ($importdata->allowedsuffixes as $allowedsuffix) {
            if (str_ends_with($path, $allowedsuffix) && file_exists($path)) {
                $suffixpresent = true;
            }
        }

        if (!$suffixpresent) {
            // If the path does not already end with a recognised suffix, append the default one.
            $path .= $importdata->suffix;
        }

        if ($requestedpath !== null && $importdata->modifier !== null) {
            $path = ($importdata->modifier)($revision, $requestedpath, $path);
        }

        if ($revision === -1) {
            // During development, check if the unminified version of the module exists for better debugging.
            $devfile = preg_replace(
                array_keys($importdata->devreplacements),
                array_values($importdata->devreplacements),
                $path,
            );
            if (file_exists($devfile)) {
                return $devfile;
            }
        }

        return file_exists($path) ? $path : null;
    }

    /**
     * Resolve a `<component>/<module>` subpath to an absolute filesystem path.
     *
     * For example, `mod_book/viewer` resolves to
     * `<dirroot>/mod/book/js/esm/build/viewer.js`.
     *
     * @param \stdClass $importdata The import entry containing the path and loadfromcomponent flag.
     * @param string $subpath The subpath after the specifier prefix (e.g. `mod_book/viewer`).
     * @param int $revision The JS revision number, used for modifier callables to determine if in developer mode.
     * @param string|null $theme The theme name if the specifier includes a theme prefix (e.g. `@moodle/lms/`).
     * @return string Absolute path to the JS file.
     * @throws \core\exception\not_found_exception If the subpath is missing a slash, contains `..`,
     *   the component is unknown, or the resolved file does not exist.
     */
    protected function resolve_module_identifier(
        \stdClass $importdata,
        string $subpath,
        int $revision,
        ?string $theme = null,
    ): string {
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
        $paths = $this->get_possible_paths(
            \core\component::get_component_directory($component),
            $importdata,
            $theme,
            $component,
            $modulerest,
        );

        foreach ($paths as $file) {
            $result = $this->resolve_candidate_path($file, $importdata, $revision);
            if ($result !== null) {
                return $result;
            }
        }

        throw new \core\exception\not_found_exception('script', $subpath);
    }

    /**
     * Get the possible paths to the file on disk.
     *
     * @param string $rootdir
     * @param \stdClass $importdata
     * @param string|null $theme
     * @param string $component
     * @param string $modulerest
     * @return string[]
     */
    protected function get_possible_paths(
        string $rootdir,
        \stdClass $importdata,
        ?string $theme,
        string $component,
        string $modulerest,
    ): array {
        $options = [];

        // First check the specified theme (if any) for an override.
        if ($theme && $theme !== self::DEFAULT_THEME_PLACEHOLDER) {
            $themedir = \core\component::get_component_directory("theme_{$theme}");
            if ($themedir) {
                $options[] = implode(
                    "/",
                    array_filter(
                        [$themedir, 'js', 'esm', 'build', 'overrides', $component, $modulerest],
                    ),
                );
            }
        }

        $options[] = implode("/", array_filter([
            $rootdir,
            $importdata->path,
            $modulerest,
        ]));

        return $options;
    }
}
