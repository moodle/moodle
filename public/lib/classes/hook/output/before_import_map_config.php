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

namespace core\hook\output;

/**
 * A hook to allow plugins to register additional entries in the ES module import map
 * before it is serialised to the page.
 *
 * Listeners receive this hook after the standard Moodle specifiers have been registered
 * (react, react-dom, @moodlehq/design-system, @moodle/lms/, etc.) and may call
 * {@see add_import()} to add their own specifiers or override existing ones.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('output')]
#[\core\attribute\label('Allows plugins to register additional entries in the ES module import map')]
class before_import_map_config {
    /**
     * Constructor.
     *
     * @param \core\output\requirements\import_map $importmap The import map instance to modify.
     */
    public function __construct(
        /** @var \core\output\requirements\import_map The import map instance to modify */
        private readonly \core\output\requirements\import_map $importmap,
    ) {
    }

    /**
     * Register a specifier in the import map.
     *
     * This method delegates directly to {@see \core\output\requirements\import_map::add_import()}.
     * Refer to that method for full parameter documentation.
     *
     * @param string $specifier The bare specifier used in import statements (e.g. `my-lib`, `@vendor/pkg/`).
     * @param \core\url|null $loader Absolute URL written verbatim into the import map.
     * @param string|null $path Filesystem path relative to $CFG->root used by the ESM controller.
     * @param bool $loadfromcomponent When true, the specifier is resolved per Moodle component.
     * @param string $suffix File extension suffix appended when resolving filesystem paths.
     * @param array|null $devreplacements Optional mapping of specifiers to use in development mode.
     * @param callable|null $modifier Optional callable to transform the resolved path before serving.
     * @param string[] $allowedsuffixes List of file suffixes accepted without appending $suffix.
     * @param string $urlsuffix Extra path appended to the specifier in the import map URL.
     * @param bool $themable When true, the specifier is resolved per theme.
     */
    public function add_import(
        string $specifier,
        ?\core\url $loader = null,
        ?string $path = null,
        bool $loadfromcomponent = false,
        string $suffix = '.js',
        ?array $devreplacements = null,
        ?callable $modifier = null,
        array $allowedsuffixes = [],
        string $urlsuffix = '',
        bool $themable = false,
    ): void {
        $this->importmap->add_import(
            $specifier,
            $loader,
            $path,
            $loadfromcomponent,
            $suffix,
            $devreplacements,
            $modifier,
            $allowedsuffixes,
            $urlsuffix,
            $themable,
        );
    }
}
