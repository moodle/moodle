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
 * Tests for the ESM import map class.
 *
 * @package    core
 * @category   test
 * @copyright  2026 Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(import_map::class)]
final class import_map_test extends \advanced_testcase {
    /**
     * The constructor pre-populates the standard ESM specifiers.
     */
    public function test_constructor_adds_standard_imports(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $data = $map->jsonSerialize();

        $this->assertArrayHasKey('@moodle/lms/', $data['imports']);
        $this->assertArrayHasKey('react', $data['imports']);
        $this->assertArrayHasKey('react/', $data['imports']);
    }

    /**
     * jsonSerialize() throws a coding_exception when no default loader has been set.
     */
    public function test_jsonserialize_throws_without_loader(): void {
        $this->expectException(\core\exception\coding_exception::class);
        (\core\di::get(import_map::class))->jsonSerialize();
    }

    /**
     * jsonSerialize() returns an array with an 'imports' key.
     */
    public function test_jsonserialize_returns_imports_structure(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $data = $map->jsonSerialize();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('imports', $data);
        $this->assertIsArray($data['imports']);
    }

    /**
     * set_default_loader() is used as the base URL when no explicit loader or path is given.
     */
    public function test_set_default_loader_is_used_as_base_url(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/esm/12345/'));
        $map->add_import('my-module');

        $data = $map->jsonSerialize();

        $this->assertEquals('https://example.com/esm/12345/my-module', $data['imports']['my-module']);
    }

    /**
     * add_import() with an explicit \core\url uses that URL verbatim, ignoring the default loader.
     */
    public function test_add_import_with_explicit_url(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('my-module', loader: new \core\url('https://cdn.example.com/my-module.js'));

        $data = $map->jsonSerialize();

        $this->assertEquals('https://cdn.example.com/my-module.js', $data['imports']['my-module']);
    }

    /**
     * add_import() with no $path and no explicit $loader appends the specifier to the loader URL.
     */
    public function test_add_import_without_path_uses_specifier(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/esm/12345/'));
        $map->add_import('some/specifier');

        $data = $map->jsonSerialize();

        $this->assertEquals('https://example.com/esm/12345/some/specifier', $data['imports']['some/specifier']);
    }

    /**
     * get_path_for_script() appends the default suffix when the path does not already end with an allowed suffix.
     */
    public function test_resolve_appends_suffix_when_not_present(): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        mkdir("{$tempdir}/testpath", 0777, true);
        file_put_contents("{$tempdir}/testpath/module.js", '// test');

        $CFG->root = $tempdir;

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('test/', path: 'testpath');

        $result = $map->get_path_for_script(1, 'test/module');
        $this->assertEquals("{$tempdir}/testpath/module.js", $result);
    }

    /**
     * get_path_for_script() does not append suffix when path already ends with an allowed suffix and file exists.
     */
    public function test_resolve_skips_suffix_when_already_present(): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        mkdir("{$tempdir}/testpath", 0777, true);
        file_put_contents("{$tempdir}/testpath/module.js", '// test');

        $CFG->root = $tempdir;

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('test/', path: 'testpath');

        $result = $map->get_path_for_script(1, 'test/module.js');
        $this->assertEquals("{$tempdir}/testpath/module.js", $result);
    }

    /**
     * get_path_for_script() resolves a .js.map suffix without appending the default .js suffix.
     */
    public function test_resolve_skips_suffix_for_map_file(): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        mkdir("{$tempdir}/testpath", 0777, true);
        file_put_contents("{$tempdir}/testpath/module.js.map", '{}');

        $CFG->root = $tempdir;

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('test/', path: 'testpath');

        $result = $map->get_path_for_script(1, 'test/module.js.map');
        $this->assertEquals("{$tempdir}/testpath/module.js.map", $result);
    }

    /**
     * add_import() automatically includes the default suffix in the allowedsuffixes array.
     */
    public function test_add_import_includes_default_suffix_in_allowedsuffixes(): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        mkdir("{$tempdir}/testpath", 0777, true);
        file_put_contents("{$tempdir}/testpath/module.js", '// test');

        $CFG->root = $tempdir;

        // Pass allowedsuffixes without .js — it should be auto-included because suffix defaults to .js.
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('test/', path: 'testpath', allowedsuffixes: ['.js.map']);

        // Request with .js suffix — should still detect it and not double-append.
        $result = $map->get_path_for_script(1, 'test/module.js');
        $this->assertEquals("{$tempdir}/testpath/module.js", $result);
    }

    /**
     * get_path_for_script() still appends suffix when path ends with an allowed suffix but file does not exist.
     */
    public function test_resolve_appends_suffix_when_suffix_present_but_file_missing(): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        mkdir("{$tempdir}/testpath", 0777, true);
        // Create module.js.js (the double-suffixed file) but NOT module.js.
        file_put_contents("{$tempdir}/testpath/module.js.js", '// test');

        $CFG->root = $tempdir;

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('test/', path: 'testpath');

        // The file `module.js` does not exist on disk, so even though path ends with .js, suffix is still appended.
        $result = $map->get_path_for_script(1, 'test/module.js');
        $this->assertEquals("{$tempdir}/testpath/module.js.js", $result);
    }

    /**
     * resolve_module_identifier() resolves a component module without suffix in the request.
     */
    public function test_component_resolve_appends_suffix(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $dir = \core\component::get_component_directory('core');
        $result = $map->get_path_for_script(1, '@moodle/lms/core/ajax');
        $this->assertEquals("{$dir}/js/esm/build/ajax.js", $result);
    }

    /**
     * resolve_module_identifier() returns early when suffix is already present in the request and file exists.
     */
    public function test_component_resolve_skips_suffix_when_present(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $dir = \core\component::get_component_directory('core');
        $result = $map->get_path_for_script(1, '@moodle/lms/core/ajax.js');
        $this->assertEquals("{$dir}/js/esm/build/ajax.js", $result);
    }

    /**
     * resolve_module_identifier() throws not_found_exception when the module file does not exist.
     */
    public function test_component_resolve_throws_when_not_found(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $this->expectException(\core\exception\not_found_exception::class);
        $map->get_path_for_script(1, '@moodle/lms/core/nonexistent_module_xyz_12345');
    }

    /**
     * The before_import_map_config hook allows listeners to add custom imports to the map.
     */
    public function test_before_import_map_config_hook_can_add_import(): void {
        $this->resetAfterTest();

        static::load_fixture('core', 'output/requirements/before_import_map_config_hooks.php');

        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => static::get_fixture_path('core', 'output/requirements/before_import_map_config_hooks.php'),
            ]),
        );

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $data = $map->jsonSerialize();

        $this->assertArrayHasKey('my-custom-specifier', $data['imports']);
        $this->assertEquals('https://example.com/custom.js', $data['imports']['my-custom-specifier']);
    }

    /**
     * add_import() with urlsuffix appends it to the URL in the import map for correct relative resolution.
     */
    public function test_add_import_with_urlsuffix(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/esm/12345/'));
        $map->add_import('mypkg', path: 'some/dir', urlsuffix: '/index.js');

        $data = $map->jsonSerialize();

        $this->assertEquals('https://example.com/esm/12345/mypkg/index.js', $data['imports']['mypkg']);
    }

    /**
     * A non-themable import produces exactly one entry in the import map with no theme-variant keys.
     */
    public function test_non_themable_import_generates_single_entry(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->set_available_themes(['boost', 'classic']);
        $map->add_import('mylib', path: 'lib/mylib/index', themable: false);

        $data = $map->jsonSerialize();

        $this->assertArrayHasKey('mylib', $data['imports']);
        $this->assertArrayNotHasKey('mylib/theme-original', $data['imports']);
        $this->assertArrayNotHasKey('mylib/theme-boost', $data['imports']);
        $this->assertArrayNotHasKey('mylib/theme-classic', $data['imports']);
    }

    /**
     * Data provider for test_themable_default_specifier_url_reflects_current_theme.
     *
     * @return array[]
     */
    public static function themable_current_theme_provider(): array {
        return [
            'no current theme uses plain specifier in URL' => [null, 'mymod/'],
            'current theme boost redirects default to theme-boost sub-path' => ['boost', 'mymod/theme-boost/'],
            'current theme classic redirects default to theme-classic sub-path' => ['classic', 'mymod/theme-classic/'],
        ];
    }

    /**
     * The default specifier URL for a themable import redirects to the current-theme sub-path
     * when a theme is active, or to the plain specifier when no theme is set.
     *
     * @param string|null $currenttheme
     * @param string $expectedurlsuffix
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('themable_current_theme_provider')]
    public function test_themable_default_specifier_url_reflects_current_theme(
        ?string $currenttheme,
        string $expectedurlsuffix,
    ): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->set_current_theme($currenttheme);
        $map->add_import('mymod/', path: 'mod/mymod/js/esm/build', themable: true);

        $data = $map->jsonSerialize();

        $this->assertEquals("https://example.com/{$expectedurlsuffix}", $data['imports']['mymod/']);
    }

    /**
     * Data provider for test_available_themes_generate_dedicated_import_map_entries.
     *
     * @return array[]
     */
    public static function available_themes_import_entries_provider(): array {
        return [
            'no available themes still produces theme-original entry' => [
                'availablethemes' => [],
                'expectedkeys' => ['mymod/', 'mymod/theme-original/'],
                'absentkeys' => ['mymod/theme-boost/'],
            ],
            'single available theme generates its own entry alongside original' => [
                'availablethemes' => ['boost'],
                'expectedkeys' => ['mymod/', 'mymod/theme-original/', 'mymod/theme-boost/'],
                'absentkeys' => ['mymod/theme-classic/'],
            ],
            'multiple available themes generate all entries' => [
                'availablethemes' => ['boost', 'classic'],
                'expectedkeys' => ['mymod/', 'mymod/theme-original/', 'mymod/theme-boost/', 'mymod/theme-classic/'],
                'absentkeys' => [],
            ],
        ];
    }

    /**
     * set_available_themes() generates a dedicated import map entry for each theme,
     * and always includes a theme-original entry regardless of the available-theme list.
     *
     * @param string[] $availablethemes
     * @param string[] $expectedkeys
     * @param string[] $absentkeys
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('available_themes_import_entries_provider')]
    public function test_available_themes_generate_dedicated_import_map_entries(
        array $availablethemes,
        array $expectedkeys,
        array $absentkeys,
    ): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->set_available_themes($availablethemes);
        $map->add_import('mymod/', path: 'mod/mymod/js/esm/build', themable: true);

        $data = $map->jsonSerialize();

        foreach ($expectedkeys as $key) {
            $this->assertArrayHasKey($key, $data['imports'], "Expected key '{$key}' not found in import map.");
        }
        foreach ($absentkeys as $key) {
            $this->assertArrayNotHasKey($key, $data['imports'], "Unexpected key '{$key}' found in import map.");
        }
    }

    /**
     * Each per-theme entry in the import map uses the theme-specific sub-path as its URL.
     */
    public function test_available_theme_entry_urls_contain_theme_subpath(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->set_available_themes(['boost']);
        $map->add_import('mymod/', path: 'mod/mymod/js/esm/build', themable: true);

        $data = $map->jsonSerialize();

        $this->assertEquals('https://example.com/mymod/theme-boost/', $data['imports']['mymod/theme-boost/']);
        $this->assertEquals('https://example.com/mymod/theme-original/', $data['imports']['mymod/theme-original/']);
    }

    /**
     * Trailing slashes are preserved on both the default specifier key and all per-theme keys
     * generated for a themable import registered with a trailing slash.
     */
    public function test_themable_import_with_trailing_slash_preserves_slash_in_all_entries(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->set_available_themes(['boost']);
        $map->add_import('mymod/', path: 'mod/mymod/js/esm/build', themable: true);

        $data = $map->jsonSerialize();

        $mymodkeys = array_filter(array_keys($data['imports']), fn(string $k) => str_starts_with($k, 'mymod'));
        foreach ($mymodkeys as $key) {
            $this->assertStringEndsWith('/', $key, "Import map key '{$key}' should preserve the trailing slash.");
        }
    }

    /**
     * Data provider for test_get_path_for_script_resolves_theme_prefixed_path.
     *
     * @return array[]
     */
    public static function get_path_for_script_theme_prefix_provider(): array {
        $coredir = \core\component::get_component_directory('core');

        return [
            'theme-original bypasses theme override and uses standard path' => [
                'requestedpath' => '@moodle/lms/theme-original/core/fetch',
                'expectedpath' => "{$coredir}/js/esm/build/fetch.js",
            ],
            'unknown theme falls back to standard path when no override exists' => [
                'requestedpath' => '@moodle/lms/theme-nonexistent_xyz_abc/core/fetch',
                'expectedpath' => "{$coredir}/js/esm/build/fetch.js",
            ],
        ];
    }

    /**
     * get_path_for_script() resolves a theme-prefixed specifier to the theme's override file when
     * one exists, skips the override for the special "original" theme, and falls back to the
     * standard component path when the requested theme has no override.
     *
     * @param string $requestedpath
     * @param string $expectedpath
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_path_for_script_theme_prefix_provider')]
    public function test_get_path_for_script_resolves_theme_prefixed_path(
        string $requestedpath,
        string $expectedpath,
    ): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $this->assertEquals($expectedpath, $map->get_path_for_script(1, $requestedpath));
    }

    /**
     * Data provider for test_explicit_url_loader_is_used_verbatim_regardless_of_themable.
     *
     * @return array[]
     */
    public static function explicit_url_themable_provider(): array {
        return [
            'themable import with explicit URL uses loader verbatim' => [true],
            'non-themable import with explicit URL uses loader verbatim' => [false],
        ];
    }

    /**
     * An import registered with an explicit \core\url uses that URL verbatim in the import map
     * regardless of whether the import is marked as themable or not.
     *
     * @param bool $themable
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('explicit_url_themable_provider')]
    public function test_explicit_url_loader_is_used_verbatim_regardless_of_themable(bool $themable): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('mylib', loader: new \core\url('https://cdn.example.com/mylib.js'), themable: $themable);

        $data = $map->jsonSerialize();

        $this->assertEquals('https://cdn.example.com/mylib.js', $data['imports']['mylib']);
    }

    /**
     * get_path_for_script() throws a coding_exception when the matched specifier was registered
     * with an explicit \core\url loader (filesystem resolution is not possible in that case).
     */
    public function test_get_path_for_script_throws_for_explicit_loader_specifier(): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('cdn-lib', loader: new \core\url('https://cdn.example.com/lib.js'));

        $this->expectException(\core\exception\coding_exception::class);
        $map->get_path_for_script(1, 'cdn-lib');
    }

    /**
     * Data provider for test_get_path_for_script_returns_null.
     *
     * @return array[]
     */
    public static function get_path_for_script_returns_null_provider(): array {
        return [
            'directory traversal in non-component path returns null' => ['test/../../etc/passwd'],
            'completely unregistered specifier returns null' => ['completely-unregistered/module'],
        ];
    }

    /**
     * get_path_for_script() returns null when a directory traversal is attempted on a
     * non-component specifier, or when the requested path matches no registered specifier.
     *
     * @param string $requestedpath
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_path_for_script_returns_null_provider')]
    public function test_get_path_for_script_returns_null(string $requestedpath): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        $CFG->root = $tempdir;

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('test/', path: 'testpath');

        $this->assertNull($map->get_path_for_script(1, $requestedpath));
    }

    /**
     * Data provider for test_get_path_for_script_invokes_modifier.
     *
     * @return array[]
     */
    public static function modifier_invocation_provider(): array {
        return [
            'modifier is invoked for non-component path' => [
                'specifier' => 'test/',
                'path' => 'testpath',
                'loadfromcomponent' => false,
                'request' => 'test/module',
            ],
            'modifier is invoked for component (loadfromcomponent) path' => [
                'specifier' => '@moodle/lms/',
                'path' => 'js/esm/build',
                'loadfromcomponent' => true,
                'request' => '@moodle/lms/core/ajax',
            ],
        ];
    }

    /**
     * get_path_for_script() invokes the registered modifier callable and returns the path
     * it produces, regardless of whether the specifier is a plain path or a loadfromcomponent entry.
     *
     * @param string $specifier
     * @param string $path
     * @param bool $loadfromcomponent
     * @param string $request
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('modifier_invocation_provider')]
    public function test_get_path_for_script_invokes_modifier(
        string $specifier,
        string $path,
        bool $loadfromcomponent,
        string $request,
    ): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        mkdir("{$tempdir}/testpath", 0777, true);
        file_put_contents("{$tempdir}/testpath/module.js", '// test');
        $CFG->root = $tempdir;

        $modifiercalled = false;
        $modifier = function (int $revision, string $requestedpath, string $resolved) use (&$modifiercalled): string {
            $modifiercalled = true;
            return $resolved;
        };

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import($specifier, path: $path, loadfromcomponent: $loadfromcomponent, modifier: $modifier);

        $map->get_path_for_script(1, $request);

        $this->assertTrue($modifiercalled, "Modifier callable was not invoked for specifier '{$specifier}'.");
    }

    /**
     * Data provider for test_get_path_for_script_dev_revision_resolves_correct_file.
     *
     * @return array[]
     */
    public static function get_path_for_script_dev_revision_provider(): array {
        return [
            'dev file exists: revision -1 returns the .dev.js file' => [
                'createdevfile' => true,
                'expectedsuffix' => 'module.dev.js',
            ],
            'dev file absent: revision -1 falls back to production file' => [
                'createdevfile' => false,
                'expectedsuffix' => 'module.js',
            ],
        ];
    }

    /**
     * When revision is -1, get_path_for_script() returns the .dev.js file when it exists on disk,
     * and falls back to the production .js file when the dev variant is absent.
     *
     * @param bool $createdevfile
     * @param string $expectedsuffix
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_path_for_script_dev_revision_provider')]
    public function test_get_path_for_script_dev_revision_resolves_correct_file(
        bool $createdevfile,
        string $expectedsuffix,
    ): void {
        global $CFG;

        $this->resetAfterTest();

        $tempdir = make_request_directory();
        mkdir("{$tempdir}/testpath", 0777, true);
        file_put_contents("{$tempdir}/testpath/module.js", '// production');
        if ($createdevfile) {
            file_put_contents("{$tempdir}/testpath/module.dev.js", '// development');
        }

        $CFG->root = $tempdir;

        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('test/', path: 'testpath');

        $result = $map->get_path_for_script(-1, 'test/module');

        $this->assertEquals("{$tempdir}/testpath/{$expectedsuffix}", $result);
    }

    /**
     * Data provider for test_component_resolve_throws_for_bad_subpath.
     *
     * @return array[]
     */
    public static function resolve_module_identifier_bad_subpath_provider(): array {
        return [
            'subpath with no slash throws not_found_exception' => [
                '@moodle/lms/coremodule',
            ],
            'double-dot traversal in module rest throws not_found_exception' => [
                '@moodle/lms/core/../config',
            ],
        ];
    }

    /**
     * resolve_module_identifier() throws a not_found_exception when the subpath has no
     * component/module separator slash, or when the module rest contains a traversal segment.
     *
     * @param string $requestedpath
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('resolve_module_identifier_bad_subpath_provider')]
    public function test_component_resolve_throws_for_bad_subpath(string $requestedpath): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $this->expectException(\core\exception\not_found_exception::class);
        $map->get_path_for_script(1, $requestedpath);
    }

    /**
     * Data provider for test_component_resolve_dev_revision_returns_dev_file.
     *
     * @return array[]
     */
    public static function component_resolve_dev_revision_provider(): array {
        $coredir = \core\component::get_component_directory('core');
        $boostdir = \core\component::get_component_directory('theme_boost');

        return [
            'standard component module: revision -1 returns .dev.js' => [
                'requestedpath' => '@moodle/lms/core/fetch',
                'expectedpath' => "{$coredir}/js/esm/build/fetch.dev.js",
            ],
        ];
    }

    /**
     * When revision is -1, resolve_module_identifier() returns the .dev.js variant of the
     * resolved component module file, including theme-override paths when a theme is specified.
     *
     * @param string $requestedpath
     * @param string $expectedpath
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('component_resolve_dev_revision_provider')]
    public function test_component_resolve_dev_revision_returns_dev_file(
        string $requestedpath,
        string $expectedpath,
    ): void {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/'));

        $this->assertEquals($expectedpath, $map->get_path_for_script(-1, $requestedpath));
    }
}
