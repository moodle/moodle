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

namespace core_ai\cache;

use cache_definition;

/**
 * Cache class for AI policy.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class policy implements \cache_data_source {
    /** @var policy|null the singleton instance of this class. */
    protected static ?policy $instance = null;

    #[\Override]
    public static function get_instance_for_cache(cache_definition $definition): policy {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    #[\Override]
    public function load_for_cache($key) {
        global $DB;

        return $DB->record_exists('ai_policy_register', ['userid' => $key]);
    }

    #[\Override]
    public function load_many_for_cache(array $keys): array {
        global $DB;
        $return = [];
        [$insql, $inparams] = $DB->get_in_or_equal($keys);
        $sql = "SELECT userid
                  FROM {ai_policy_register}
                 WHERE userid " . $insql;

        $results = $DB->get_fieldset_sql($sql, $inparams);
        foreach ($keys as $key) {
            $return[$key] = in_array($key, $results);
        }

        return $return;
    }
}
