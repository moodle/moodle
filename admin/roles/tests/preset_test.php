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

namespace core_role;

use core_role_preset;

/**
 * Role XML presets test case.
 *
 * @package   core_role
 * @category  test
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preset_test extends \advanced_testcase {
    public function test_xml() {
        global $DB;

        $roles = $DB->get_records('role');

        foreach ($roles as $role) {
            $xml = core_role_preset::get_export_xml($role->id);
            $this->assertTrue(core_role_preset::is_valid_preset($xml));
            $info = core_role_preset::parse_preset($xml);
            $this->assertSame($role->shortname, $info['shortname']);
            $this->assertSame($role->name, $info['name']);
            $this->assertSame($role->description, $info['description']);
            $this->assertSame($role->archetype, $info['archetype']);

            $contextlevels = get_role_contextlevels($role->id);
            $this->assertEquals(array_values($contextlevels), array_values($info['contextlevels']));

            foreach (array('assign', 'override', 'switch', 'view') as $type) {
                $records = $DB->get_records('role_allow_'.$type, array('roleid'=>$role->id), "allow$type ASC");
                $allows = array();
                foreach ($records as $record) {
                    if ($record->{'allow'.$type} == $role->id) {
                        array_unshift($allows, -1);
                    }
                    $allows[] = $record->{'allow'.$type};
                }
                $this->assertEquals($allows, $info['allow'.$type], "$type $role->shortname does not match");
            }

            $capabilities = $DB->get_records_sql(
                "SELECT *
                   FROM {role_capabilities}
                  WHERE contextid = :syscontext AND roleid = :roleid
               ORDER BY capability ASC",
                array('syscontext' => \context_system::instance()->id, 'roleid' => $role->id));

            foreach ($capabilities as $cap) {
                $this->assertEquals($cap->permission, $info['permissions'][$cap->capability]);
                unset($info['permissions'][$cap->capability]);
            }
            // The remainders should be only inherits.
            foreach ($info['permissions'] as $capability => $permission) {
                if ($permission == CAP_INHERIT) {
                    continue;
                }
                $this->fail('only CAP_INHERIT expected');
            }
        }
    }

    /**
     * Tests covered method.
     * @covers \core_role_preset::parse_preset
     */
    public function test_mixed_levels() {
        // The problem here is that we cannot guarantee plugin contexts
        // have unique short names, so we have to also support level numbers.
        $xml = file_get_contents(__DIR__ . '/fixtures/mixed_levels.xml');
        $this->assertTrue(\core_role_preset::is_valid_preset($xml));

        $preset = \core_role_preset::parse_preset($xml);
        $expected = [\core\context\system::LEVEL, \core\context\coursecat::LEVEL, \core\context\course::LEVEL];
        $expected = array_combine($expected, $expected);
        $this->assertSame($expected, $preset['contextlevels']);
    }
}
