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
namespace mod_bigbluebuttonbn\local\plugins;

use cache_helper;
use context_system;
use core_component;
use core_plugin_manager;
use flexible_table;
use html_writer;
use mod_bigbluebuttonbn\extension;
use moodle_url;
use pix_icon;

/**
 * Class that handles the display and configuration of the list of extension plugins.
 *
 * This is directly taken from the mod_assign code. We might need to have a global API there for this.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class admin_plugin_manager {
    /** @var object the url of the manage submission plugin page */
    private $pageurl;

    /**
     * Constructor for this assignment plugin manager
     *
     */
    public function __construct() {
        $this->pageurl = new moodle_url(admin_page_manage_extensions::ADMIN_PAGE_URL);
    }

    /**
     * This is the entry point for this controller class.
     *
     * @param string|null $action - The action to perform
     * @param string|null $plugin - Optional name of a plugin type to perform the action on
     * @return void
     */
    public function execute(?string $action = null, ?string $plugin = null): void {
        if (empty($action) || empty($plugin)) {
            $action = 'view';
        }
        $this->check_permissions();

        $actionname = "plugins_$action";
        if (method_exists($this, $actionname)) {
            $nextaction = $this->$actionname($plugin);
            if ($nextaction) {
                $this->execute($nextaction, $plugin);
            }
        }
    }

    /**
     * Check this user has permission to edit the list of installed plugins
     *
     * @return void
     */
    private function check_permissions(): void {
        require_login();
        $systemcontext = context_system::instance();
        require_capability('moodle/site:config', $systemcontext);
    }

    /**
     * Write the HTML for the submission plugins table.
     *
     * @return void
     */
    private function plugins_view(): void {
        global $OUTPUT, $CFG;
        require_once($CFG->libdir . '/tablelib.php');
        $this->print_header();
        $table = new flexible_table(extension::BBB_EXTENSION_PLUGIN_NAME . 'pluginsadminttable');
        $table->define_baseurl($this->pageurl);
        $table->define_columns([
            'pluginname',
            'version',
            'hideshow',
            'order',
            'settings',
            'uninstall'
        ]);
        $table->define_headers([
            get_string('subplugintype_bbbext', 'mod_bigbluebuttonbn'),
            get_string('version'), get_string('hide') . '/' . get_string('show'),
            get_string('order'),
            get_string('settings'),
            get_string('uninstallplugin', 'core_admin')
        ]);
        $table->set_attribute('id', extension::BBB_EXTENSION_PLUGIN_NAME . 'plugins');
        $table->set_attribute('class', 'admintable generaltable');
        $table->setup();

        $plugins = $this->get_sorted_plugins_list();
        $instances = core_plugin_manager::instance()->get_plugins_of_type(extension::BBB_EXTENSION_PLUGIN_NAME);

        foreach ($plugins as $idx => $plugin) {
            $componentname = extension::BBB_EXTENSION_PLUGIN_NAME . '_' . $plugin;
            $typebasedir = "";
            if (in_array($plugin, array_keys($instances))) {
                $typebasedir = ($instances[$plugin])->typerootdir;
            }
            $row = [];
            $class = '';
            $pluginversion = get_config($componentname, 'version');
            $row[] = get_string('pluginname', $componentname);
            $row[] = $pluginversion;
            $visible = !get_config($componentname, 'disabled');

            if ($visible) {
                $row[] = $this->format_icon_link('hide', $plugin, 't/hide', get_string('disable'));
            } else {
                $row[] = $this->format_icon_link('show', $plugin, 't/show', get_string('enable'));
                $class = 'dimmed_text';
            }

            $movelinks = '';
            if (!$idx == 0) {
                $movelinks .= $this->format_icon_link('moveup', $plugin, 't/up', get_string('up')) . ' ';
            } else {
                $movelinks .= $OUTPUT->spacer(['width' => 16]);
            }
            if ($idx != count($plugins) - 1) {
                $movelinks .= $this->format_icon_link('movedown', $plugin, 't/down', get_string('down')) . ' ';
            }
            $row[] = $movelinks;

            $exists = file_exists($typebasedir . '/' . $plugin . '/settings.php');
            // We do not display settings for plugin who have not yet been installed (so have no version yet).
            if (!empty($pluginversion) && $exists) {
                $row[] = html_writer::link(
                    new moodle_url('/admin/settings.php', ['section' => $componentname]),
                    get_string('settings')
                );
            } else {
                $row[] = '&nbsp;';
            }
            $url = core_plugin_manager::instance()->get_uninstall_url(
                $componentname,
                'manage'
            );
            if ($url) {
                $row[] = html_writer::link($url, get_string('uninstallplugin', 'core_admin'));
            } else {
                $row[] = '&nbsp;';
            }

            $table->add_data($row, $class);
        }

        $table->finish_output();
        $this->print_footer();
    }

    /**
     * Write the page header
     *
     * @return void
     */
    private function print_header(): void {
        global $OUTPUT;
        $pageidentifier = 'manage' . extension::BBB_EXTENSION_PLUGIN_NAME . 'plugins';
        admin_externalpage_setup($pageidentifier);
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string($pageidentifier, 'mod_bigbluebuttonbn'));
    }

    /**
     * Return a list of plugins sorted by the order defined in the admin interface
     *
     * @return array The list of plugins
     */
    public function get_sorted_plugins_list(): array {
        $names = core_component::get_plugin_list(extension::BBB_EXTENSION_PLUGIN_NAME);
        return extension::get_sorted_plugins_list($names);
    }

    /**
     * Util function for writing an action icon link
     *
     * @param string $action URL parameter to include in the link
     * @param string $plugin URL parameter to include in the link
     * @param string $icon The key to the icon to use (e.g. 't/up')
     * @param string $alt The string description of the link used as the title and alt text
     * @return string The icon/link
     */
    private function format_icon_link(string $action, string $plugin, string $icon, string $alt): string {
        global $OUTPUT;
        return $OUTPUT->action_icon(
                new moodle_url(
                    $this->pageurl,
                    ['action' => $action, 'plugin' => $plugin, 'sesskey' => sesskey()]
                ),
                new pix_icon($icon, $alt, 'moodle', ['title' => $alt]),
                null,
                ['title' => $alt]
            );
    }

    /**
     * Write the page footer
     *
     * @return void
     */
    private function print_footer(): void {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    /**
     * Hide this plugin.
     *
     * @param string $plugin - The plugin to hide
     * @return string The next page to display
     */
    private function plugins_hide(string $plugin): string {
        $class = \core_plugin_manager::resolve_plugininfo_class(extension::BBB_EXTENSION_PLUGIN_NAME);
        $class::enable_plugin($plugin, false);
        cache_helper::purge_by_event('mod_bigbluebuttonbn/pluginenabledisabled');
        // Also clear the cache for all BigBlueButtonModules.
        rebuild_course_cache(0, true);
        return 'view';
    }

    /**
     * Show this plugin.
     *
     * @param string $plugin - The plugin to show
     * @return string The next page to display
     */
    private function plugins_show(string $plugin): string {
        $class = \core_plugin_manager::resolve_plugininfo_class(extension::BBB_EXTENSION_PLUGIN_NAME);
        $class::enable_plugin($plugin, true);
        cache_helper::purge_by_event('mod_bigbluebuttonbn/pluginenabledisabled');
        return 'view';
    }

    /**
     * Move this plugin up
     *
     * We need this function so we can call directly (without the dir parameter)
     * @param string $plugintomove - The plugin to move
     * @return string The next page to display
     */
    private function plugins_moveup(string $plugintomove): string {
        return $this->move_plugin($plugintomove, 'up');
    }

    /**
     * Move this plugin down
     *
     * We need this function so we can call directly (without the dir parameter)
     * @param string $plugintomove - The plugin to move
     * @return string The next page to display
     */
    private function plugins_movedown(string $plugintomove): string {
        return $this->move_plugin($plugintomove, 'down');
    }

    /**
     * Change the order of this plugin.
     *
     * @param string $plugintomove - The plugin to move
     * @param string $dir - up or down
     * @return string The next page to display
     */
    private function move_plugin(string $plugintomove, string $dir): string {
        $plugins = $this->get_sorted_plugins_list();
        $plugins = array_values($plugins);
        $currentindex = array_search($plugintomove, $plugins);
        if ($currentindex === false) {
            return 'view';
        }
        // Make the switch.
        if ($dir === 'up') {
            if ($currentindex > 0) {
                $tempplugin = $plugins[$currentindex - 1];
                $plugins[$currentindex - 1] = $plugins[$currentindex];
                $plugins[$currentindex] = $tempplugin;
            }
        } else if ($dir === 'down') {
            if ($currentindex < (count($plugins) - 1)) {
                $tempplugin = $plugins[$currentindex + 1];
                $plugins[$currentindex + 1] = $plugins[$currentindex];
                $plugins[$currentindex] = $tempplugin;
            }
        }

        // Save the new normal order.
        foreach ($plugins as $key => $plugin) {
            set_config('sortorder', $key, extension::BBB_EXTENSION_PLUGIN_NAME . '_' . $plugin);
        }
        return 'view';
    }
}
