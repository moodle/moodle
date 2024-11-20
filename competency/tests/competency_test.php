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

namespace core_competency;

/**
 * Competency testcase.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_test extends \advanced_testcase {

    public function test_get_framework_depth(): void {
        $this->resetAfterTest();

        $ccg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $f1 = $ccg->create_framework();
        $f2 = $ccg->create_framework();
        $f3 = $ccg->create_framework();
        $f4 = $ccg->create_framework();

        $f1c1 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id')]);
        $f1c11 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id'), 'parentid' => $f1c1->get('id')]);
        $f1c111 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id'), 'parentid' => $f1c11->get('id')]);
        $f1c1111 = $ccg->create_competency(['competencyframeworkid' => $f1->get('id'), 'parentid' => $f1c111->get('id')]);

        $f2c1 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $f2c2 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);
        $f2c21 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id'), 'parentid' => $f2c2->get('id')]);
        $f2c22 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id'), 'parentid' => $f2c2->get('id')]);
        $f2c211 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id'), 'parentid' => $f2c21->get('id')]);
        $f2c221 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id'), 'parentid' => $f2c22->get('id')]);
        $f2c222 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id'), 'parentid' => $f2c22->get('id')]);
        $f2c223 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id'), 'parentid' => $f2c22->get('id')]);
        $f2c3 = $ccg->create_competency(['competencyframeworkid' => $f2->get('id')]);

        $f3c1 = $ccg->create_competency(['competencyframeworkid' => $f3->get('id')]);

        $this->assertEquals(4, competency::get_framework_depth($f1->get('id')));
        $this->assertEquals(3, competency::get_framework_depth($f2->get('id')));
        $this->assertEquals(1, competency::get_framework_depth($f3->get('id')));
        $this->assertEquals(0, competency::get_framework_depth($f4->get('id')));
    }

}
