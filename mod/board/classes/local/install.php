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

namespace mod_board\local;

/**
 * Install functions.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class install {
    /**
     * Create all built-in templates if they don't exist.
     */
    public static function setup_builtin_templates() {
        $builtintemplates = static::get_builtin_templates();
        foreach ($builtintemplates as $template) {
            if (!static::template_exists($template)) {
                template::create($template);
            }
        }
    }

    /**
     * Get an array of all built-in templates.
     *
     * @return array Array of template objects for the built-in templates.
     */
    public static function get_builtin_templates(): array {
        $resourcesdir = __DIR__.'/../../resources/templates';
        $builtin = glob($resourcesdir.'/*.json');
        $templates = [];
        foreach ($builtin as $file) {
            $json = file_get_contents($file);
            $template = template::decode_import_file($json);
            if ($template) {
                $templates[] = $template;
            }
        }
        return $templates;
    }

    /**
     * Check if a template already exists that represents a given template.
     *
     * @param \stdClass $template The template to check.
     * @return bool True if a saved template represents the passed template, false otherwise.
     */
    public static function template_exists(\stdClass $template): bool {
        global $DB;
        $sqlselect = 'name = :name AND '
                     . $DB->sql_compare_text('description', 255) . ' = ' . $DB->sql_compare_text(':description', 255) . ' AND '
                     . $DB->sql_compare_text('columns', 255) . ' = ' . $DB->sql_compare_text(':columns', 255);
        $params = [
            'name' => $template->name,
            'description' => $template->description,
            'columns' => $template->columns,
        ];
        return $DB->record_exists_select('board_templates', $sqlselect, $params);
    }
}
