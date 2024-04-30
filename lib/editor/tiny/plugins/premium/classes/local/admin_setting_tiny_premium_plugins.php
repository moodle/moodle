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

namespace tiny_premium\local;

use tiny_premium\manager;

/**
 * Admin setting for managing Tiny Premium plugins.
 *
 * @package    tiny_premium
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_tiny_premium_plugins extends \admin_setting {

    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct(
            name: 'tiny_premium/premiumplugins',
            visiblename: new \lang_string('premiumplugins', 'tiny_premium'),
            description: new \lang_string('premiumplugins_desc', 'tiny_premium'),
            defaultsetting: '',
        );
    }

    /**
     * Always returns true.
     *
     * @return bool
     */
    public function get_setting(): bool {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything.
     *
     * @param mixed $data
     * @return string Always returns ''
     */
    public function write_setting($data): string {
        return '';
    }

    /**
     * Builds the HTML to display the Tiny Premium plugins table.
     *
     * @param mixed $data Unused
     * @param string $query
     * @return string highlight
     */
    public function output_html($data, $query=''): string {
        global $OUTPUT;

        $return = '';

        // Warn users about an empty API key when displaying enabled plugins.
        if (empty(get_config('tiny_premium', 'apikey')) && !empty(manager::get_enabled_plugins())) {
            $return .= \core\notification::warning(get_string('emptyapikeywarning', 'tiny_premium'));
        }

        $return .= $OUTPUT->box_start('generalbox');
        $return .= $OUTPUT->heading(get_string('premiumplugins', 'tiny_premium'), 3);
        $return .= \html_writer::tag('p', get_string('premiumplugins_desc', 'tiny_premium'));
        $return .= $this->define_manage_tiny_premium_plugins_table();
        $return .= $OUTPUT->box_end();

        return highlight($query, $return);
    }

    /**
     * Defines table for managing Tiny Premium plugins.
     *
     * @return string HTML for table
     */
    public function define_manage_tiny_premium_plugins_table(): string {
        global $OUTPUT;
        $sesskey = sesskey();

        // Set up table.
        $table = new \html_table();
        $table->id = 'managetinypremiumpluginstable';
        $table->attributes['class'] = 'admintable generaltable';
        $table->head  = [
            get_string('name'),
            get_string('enable'),
        ];
        $table->colclasses = [
            'leftalign',
            'centeralign',
        ];
        $table->data  = [];

        // Keep enabled plugins on top.
        $plugins = manager::get_plugins();
        $enabledplugins = manager::get_enabled_plugins();
        $disabledplugins = array_diff($plugins, $enabledplugins);
        $plugins = array_merge($enabledplugins, $disabledplugins);

        foreach ($plugins as $plugin) {

            $pluginname = get_string('premiumplugin:' . $plugin, 'tiny_premium');

            // Determine plugin actions.
            if (manager::is_plugin_enabled($plugin)) {
                $action = 'disable';
                $icon = $OUTPUT->pix_icon('t/hide', get_string('disableplugin', 'core_admin', $pluginname));
                $class = '';
            } else {
                $action = 'enable';
                $icon = $OUTPUT->pix_icon('t/show', get_string('enableplugin', 'core_admin', $pluginname));
                $class = 'dimmed_text';
            }

            // Prepare a link to perform the action.
            $hideshowurl = new \moodle_url('/lib/editor/tiny/plugins/premium/pluginsettings.php', [
                'action' => $action,
                'plugin' => $plugin,
                'sesskey' => $sesskey,
            ]);
            $hideshowlink = \html_writer::link($hideshowurl, $icon);

            // Populate table row.
            $row = new \html_table_row([
                $pluginname,
                $hideshowlink,
            ]);
            $row->attributes['class'] = $class;
            $table->data[] = $row;
        }

        return \html_writer::table($table);
    }
}
