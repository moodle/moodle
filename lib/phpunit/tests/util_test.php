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

/**
 * PHPUnit Util tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Test util extra features.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_phpunit_util_testcase extends advanced_testcase {
    /**
     * @dataProvider set_table_modified_by_sql_provider
     */
    public function test_set_table_modified_by_sql($sql, $expectations) {
        phpunit_util::reset_updated_table_list();
        phpunit_util::set_table_modified_by_sql($sql);
        foreach ($expectations as $table => $present) {
            $this->assertEquals($present, !empty(phpunit_util::$tableupdated[$table]));
        }
    }

    public function set_table_modified_by_sql_provider() {
        global $DB;
        $prefix = $DB->get_prefix();

        return array(
            'Basic update' => array(
                'sql'           => "UPDATE {$prefix}user SET username = username || '_test'",
                'expectations'  => array(
                    'user'      => true,
                    'course'    => false,
                ),
            ),
            'Basic update with a fieldname sharing the same prefix' => array(
                'sql'           => "UPDATE {$prefix}user SET {$prefix}username = username || '_test'",
                'expectations'  => array(
                    'user'      => true,
                    'course'    => false,
                ),
            ),
            'Basic update with a table which contains the prefix' => array(
                'sql'           => "UPDATE {$prefix}user{$prefix} SET username = username || '_test'",
                'expectations'  => array(
                    "user{$prefix}" => true,
                    'course'        => false,
                ),
            ),
            'Update table with a numeric name' => array(
                'sql'           => "UPDATE {$prefix}example42 SET username = username || '_test'",
                'expectations'  => array(
                    'example42' => true,
                    'user'      => false,
                    'course'    => false,
                ),
            ),
            'Drop basic table' => array(
                'sql'           => "DROP TABLE {$prefix}user",
                'expectations'  => array(
                    'user'      => true,
                    'course'    => false,
                ),
            ),
            'Drop table with a numeric name' => array(
                'sql'           => "DROP TABLE {$prefix}example42",
                'expectations'  => array(
                    'example42' => true,
                    'user'      => false,
                    'course'    => false,
                ),
            ),
            'Insert in table' => array(
                'sql'           => "INSERT INTO {$prefix}user (username,password) VALUES ('moodle', 'test')",
                'expectations'  => array(
                    'user'      => true,
                    'course'    => false,
                ),
            ),
        );
    }
}
