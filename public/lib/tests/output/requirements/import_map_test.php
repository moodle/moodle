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
        $map = new import_map();
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
        (new import_map())->jsonSerialize();
    }

    /**
     * jsonSerialize() returns an array with an 'imports' key.
     */
    public function test_jsonserialize_returns_imports_structure(): void {
        $map = new import_map();
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
        $map = new import_map();
        $map->set_default_loader(new \core\url('https://example.com/esm/12345/'));
        $map->add_import('my-module');

        $data = $map->jsonSerialize();

        $this->assertEquals('https://example.com/esm/12345/my-module', $data['imports']['my-module']);
    }

    /**
     * add_import() with an explicit \core\url uses that URL verbatim, ignoring the default loader.
     */
    public function test_add_import_with_explicit_url(): void {
        $map = new import_map();
        $map->set_default_loader(new \core\url('https://example.com/'));
        $map->add_import('my-module', loader: new \core\url('https://cdn.example.com/my-module.js'));

        $data = $map->jsonSerialize();

        $this->assertEquals('https://cdn.example.com/my-module.js', $data['imports']['my-module']);
    }

    /**
     * add_import() with no $path and no explicit $loader appends the specifier to the loader URL.
     */
    public function test_add_import_without_path_uses_specifier(): void {
        $map = new import_map();
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

        $map = new import_map();
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

        $map = new import_map();
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

        $map = new import_map();
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
        $map = new import_map();
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

        $map = new import_map();
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
        $map = new import_map();
        $map->set_default_loader(new \core\url('https://example.com/'));

        $dir = \core\component::get_component_directory('core');
        $result = $map->get_path_for_script(1, '@moodle/lms/core/react_autoinit');
        $this->assertEquals("{$dir}/js/esm/build/react_autoinit.js", $result);
    }

    /**
     * resolve_module_identifier() returns early when suffix is already present in the request and file exists.
     */
    public function test_component_resolve_skips_suffix_when_present(): void {
        $map = new import_map();
        $map->set_default_loader(new \core\url('https://example.com/'));

        $dir = \core\component::get_component_directory('core');
        $result = $map->get_path_for_script(1, '@moodle/lms/core/react_autoinit.js');
        $this->assertEquals("{$dir}/js/esm/build/react_autoinit.js", $result);
    }

    /**
     * resolve_module_identifier() throws not_found_exception when the module file does not exist.
     */
    public function test_component_resolve_throws_when_not_found(): void {
        $map = new import_map();
        $map->set_default_loader(new \core\url('https://example.com/'));

        $this->expectException(\core\exception\not_found_exception::class);
        $map->get_path_for_script(1, '@moodle/lms/core/nonexistent_module_xyz_12345');
    }
}
