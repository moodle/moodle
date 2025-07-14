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

namespace core\tests;

/**
 * Trait support full/deep plugin/subplugin mocking, forcing \core\component to rebuild in full.
 *
 * Useful for testing core\component, plugin managers, and lower level classes of that nature.
 *
 * For other plugin mocking, shallow mocking may be more suitable. See:
 * {@see \advanced_testcase::add_mocked_plugintype}
 * {@see \advanced_testcase::add_mocked_plugin()}
 *
 * @package    core
 * @category   phpunit
 * @copyright  2025 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait fake_plugins_test_trait {
    /** @var bool Whether to fully reset the component cache after each test */
    protected bool $fullcomponentreset = false;

    /**
     * Reset the component cache.
     *
     * This is an After test method that supplements the test tearDown to ensure the component cache is reset.
     *
     * Please note that the component cache is always partially reset in the main tearDown method.
     * This method will extend the reset to also cause re-reads off disk.
     */
    #[\PHPUnit\Framework\Attributes\After]
    public function reset_component_cache(): void {
        if ($this->fullcomponentreset === true) {
            \core\component::reset(true);
        }
        $this->fullcomponentreset = false;
    }

    /**
     * Call this method to force the component cache to be fully reset after each test.
     *
     * Normally the component cache is reset, but only partially, to speed up the tests.
     * This method will cause the reset to also cause re-reads off disk.
     *
     * @param bool $reset Whether to fully reset the component cache after each test
     */
    protected function fully_reset_component_after_test(bool $reset = true): void {
        $this->fullcomponentreset = $reset;
    }

    /**
     * Add a mocked plugintype at the component sources level, allowing \core\component to pick up the type and its plugins.
     *
     * Unlike {@see add_mocked_plugintype}, this method doesn't inject the mocked type into the existing cache at surface level,
     * but instead injects it into component sources, allowing \core\component to fully populate its caches from the mock sources.
     *
     * Please note that tests calling this method must be run in separate isolation mode.
     * Please avoid using this if at all possible.
     *
     * @param string $plugintype The name of the plugintype
     * @param string $path The path to the plugintype's root
     * @param bool $subpluginsupport whether the mock plugintype supports subplugins.
     * @return void
     */
    protected function add_full_mocked_plugintype(
        string $plugintype,
        string $path,
        bool $subpluginsupport = false
    ): void {
        require_phpunit_isolation();
        $this->fully_reset_component_after_test(true);

        // Inject the plugintype into the mock component sources. This will be picked up during \core\component::init().
        $mockedcomponent = new \ReflectionClass(\core\component::class);
        $componentsource = $mockedcomponent->getStaticPropertyValue('componentsource');
        $componentsourcekey = 'plugintypes';
        if (object_property_exists($componentsource[$componentsourcekey], $plugintype)) {
            throw new \coding_exception("The plugintype '{$plugintype}' already exists in component sources.");
        }
        $componentsource[$componentsourcekey]->$plugintype = $path;
        $mockedcomponent->setStaticPropertyValue('componentsource', $componentsource);

        // Force subplugin support for the plugin type, if specified.
        if ($subpluginsupport) {
            $typessupporting = $mockedcomponent->getStaticPropertyValue('supportsubplugins');
            if (array_search($plugintype, $typessupporting) === false) {
                $typessupporting[] = $plugintype;
            }
            $mockedcomponent->setStaticPropertyValue('supportsubplugins', $typessupporting);
        }

        // Force clear the static plugintypes cache, as this cache determines whether \core\component::init() will rebuild
        // core\component caches from component sources.
        $mockedcomponent->setStaticPropertyValue('plugintypes', null);

        // Mock the installation of all plugins belonging to the plugintype (and those from the subtypes, if supported).
        $allpluginsoftype = \core\component::get_plugin_list($plugintype);
        foreach ($allpluginsoftype as $name => $plugindir) {
            // Mock the installation of the plugin.
            $plugin = (object) [];
            require("$plugindir/version.php");
            $fullpluginname = $plugintype . '_' . $name;
            set_config('version', $plugin->version, $fullpluginname);
            update_capabilities($fullpluginname);

            // Mock the installation of the subplugins, if supported.
            if ($subpluginsupport) {
                if ($subpluginsoftype = \core\component::get_all_subplugins($fullpluginname)) {
                    $alltypes = \core\component::get_all_plugin_types();
                    foreach ($subpluginsoftype as $subplugintype => $subplugins) {
                        foreach ($subplugins as $index => $name) {
                            $subplugindir = $alltypes[$subplugintype] . '/' . $name;
                            $fullsubpluginname = $subplugintype . '_' . $name;
                            $plugin = (object) [];
                            require("$subplugindir/version.php");
                            set_config('version', $plugin->version, $fullsubpluginname);
                            update_capabilities($fullsubpluginname);
                        }
                    }
                }
            }
        }

        // Finally purge whatever was already cached in plugin_manager.
        \cache::make('core', 'plugin_manager')->purge();
    }

    /**
     * Helper to deprecate a mocked plugin type at the component sources level.
     *
     * This method is to be used alongside {@see add_full_mocked_plugintype} only. It does not support deprecating shallow mocks
     * of plugin types, such as those created with {@see add_mocked_plugintype}.
     *
     * @param string $plugintype the plugin type.
     * @return void
     * @throws coding_exception if the plugintype hasn't already been mocked or if it's already been deprecated.
     */
    protected function deprecate_full_mocked_plugintype(
        string $plugintype,
    ): void {
        $this->fully_reset_component_after_test(true);

        $mockedcomponent = new \ReflectionClass(\core\component::class);
        $componentsource = $mockedcomponent->getStaticPropertyValue('componentsource');
        $deprecatedkey = 'deprecatedplugintypes';
        $typeskey = 'plugintypes';
        $componentsource['deprecatedplugintypes'] = $componentsource['deprecatedplugintypes'] ?? (object) [];
        if (!object_property_exists($componentsource[$typeskey], $plugintype)) {
            throw new coding_exception("The plugintype '{$plugintype}' does not exist and cannot be deprecated.");
        }
        if (object_property_exists($componentsource[$deprecatedkey], $plugintype)) {
            throw new coding_exception("The plugintype '{$plugintype}' has already been deprecated.");
        }
        $componentsource[$deprecatedkey]->$plugintype = $componentsource[$typeskey]->$plugintype;
        unset($componentsource[$typeskey]->$plugintype);
        $mockedcomponent->setStaticPropertyValue('componentsource', $componentsource);

        // Force clear the static plugintypes cache, as this cache determines whether \core\component::init() will rebuild
        // \core\component caches from component sources.
        $mockedcomponent->setStaticPropertyValue('plugintypes', null);
    }

    /**
     * Helper to delete a mocked plugin type at the component sources level.
     *
     * This method is to be used alongside {@see add_full_mocked_plugintype} only. It does not support deleting shallow mocks
     * of plugin types, such as those created with {@see add_mocked_plugintype}.
     *
     * @param string $plugintype the plugin type.
     * @return void
     * @throws coding_exception if the plugintype hasn't already been mocked or if it's already been deprecated.
     */
    protected function delete_full_mocked_plugintype(
        string $plugintype,
    ): void {
        $this->fully_reset_component_after_test(true);

        \core\component::classloader(\core\component::class);
        $mockedcomponent = new \ReflectionClass(\core\component::class);
        $componentsource = $mockedcomponent->getStaticPropertyValue('componentsource');
        $deletedkey = 'deletedplugintypes';
        $typeskey = 'plugintypes';
        $componentsource['deletedplugintypes'] = $componentsource['deletedplugintypes'] ?? (object) [];
        if (!object_property_exists($componentsource[$typeskey], $plugintype)) {
            throw new coding_exception("The plugintype '{$plugintype}' does not exist and cannot be deleted.");
        }
        if (object_property_exists($componentsource[$deletedkey], $plugintype)) {
            throw new coding_exception("The plugintype '{$plugintype}' has already been deleted.");
        }
        $componentsource[$deletedkey]->$plugintype = $componentsource[$typeskey]->$plugintype;
        unset($componentsource[$typeskey]->$plugintype);
        $mockedcomponent->setStaticPropertyValue('componentsource', $componentsource);

        // Force clear the static plugintypes cache, as this cache determines whether \core\component::init() will rebuild
        // core\component caches from component sources.
        $mockedcomponent->setStaticPropertyValue('plugintypes', null);
    }
}
