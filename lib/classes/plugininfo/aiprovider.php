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

namespace core\plugininfo;

use core_plugin_manager;
use moodle_url;

/**
 * AI placement plugin info class.
 *
 * @package    core
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aiprovider extends base {
    /** @var string Enable a plugin */
    public const ENABLE = 'enable';

    /** @var string Disable a plugin */
    public const DISABLE = 'disable';

    /** @var string Move a plugin up in the plugin order */
    public const UP = 'up';

    /** @var string Move a plugin down in the plugin order */
    public const DOWN = 'down';

    #[\Override]
    public function is_uninstall_allowed(): bool {
        return true;
    }

    #[\Override]
    public function get_settings_section_name(): string {
        return $this->type . '_' . $this->name;
    }

    #[\Override]
    public static function get_manage_url(): moodle_url {
        return new moodle_url('/admin/settings.php', [
            'section' => 'aiprovider',
        ]);
    }

    #[\Override]
    public static function get_enabled_plugins(): ?array {
        $pluginmanager = core_plugin_manager::instance();
        $plugins = $pluginmanager->get_installed_plugins('aiprovider');

        if (!$plugins) {
            return [];
        }

        $plugins = array_keys($plugins);

        // Filter to return only enabled plugins.
        $enabled = [];
        foreach ($plugins as $plugin) {
            $disabled = get_config('aiprovider_' . $plugin, 'disabled');
            if (empty($disabled)) {
                $enabled[$plugin] = $plugin;
            }
        }
        return $enabled;
    }

    /**
     * Returns the list of available actions with provider.
     *
     * @return array
     */
    public static function get_provider_actions(): array {
        return [self::UP, self::DOWN];
    }

    #[\Override]
    public function uninstall_cleanup(): void {
        global $DB;

        $provider = $this->get_settings_section_name() . '\provider';
        $DB->delete_records('ai_providers', ['provider' => $provider]);

        parent::uninstall_cleanup();
    }
}
