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
 * User evidence competency persistent class tests.
 *
 * @package    core_competency
 * @copyright  2016 Serge Gauthier - <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_competency\user_evidence_competency;

/**
 * User evidence competency persistent testcase.
 *
 * @package    core_competency
 * @copyright  2016 Serge Gauthier - <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_user_evidence_competency_testcase extends advanced_testcase {

    public function test_get_user_competencies_by_userevidenceid() {
        global $CFG, $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $u1 = $dg->create_user();

        // Create framework with competencies.
        $fw = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $fw->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $fw->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $fw->get_id()));
        $c4 = $lpg->create_competency(array('competencyframeworkid' => $fw->get_id()));

        // Create a plan with competencies.
        $p1 = $lpg->create_plan(array('userid' => $u1->id));
        $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c2->get_id()));
        $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c3->get_id()));
        $lpg->create_plan_competency(array('planid' => $p1->get_id(), 'competencyid' => $c4->get_id()));

        // Create a prior learning evidence and link competencies.
        $ue1 = $lpg->create_user_evidence(array('userid' => $u1->id));
        $uec11 = $lpg->create_user_evidence_competency(array('userevidenceid' => $ue1->get_id(), 'competencyid' => $c1->get_id()));
        $uec12 = $lpg->create_user_evidence_competency(array('userevidenceid' => $ue1->get_id(), 'competencyid' => $c2->get_id()));
        $uec13 = $lpg->create_user_evidence_competency(array('userevidenceid' => $ue1->get_id(), 'competencyid' => $c3->get_id()));
        $uc11 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c1->get_id()));
        $uc12 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c2->get_id()));
        $uc13 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c3->get_id()));

        // Create an other prior learning evidence and link competencies.
        $ue2 = $lpg->create_user_evidence(array('userid' => $u1->id));
        $uec22 = $lpg->create_user_evidence_competency(array('userevidenceid' => $ue2->get_id(), 'competencyid' => $c4->get_id()));
        $uc22 = $lpg->create_user_competency(array('userid' => $u1->id, 'competencyid' => $c4->get_id()));

        // Check the user competencies associated to the first prior learning evidence.
        $ucs = user_evidence_competency::get_user_competencies_by_userevidenceid($ue1->get_id());
        $this->assertCount(3, $ucs);
        $uc = array_shift($ucs);
        $this->assertEquals($uc->get_id(), $uc11->get_id());
        $uc = array_shift($ucs);
        $this->assertEquals($uc->get_id(), $uc12->get_id());
        $uc = array_shift($ucs);
        $this->assertEquals($uc->get_id(), $uc13->get_id());

        // Check the user competencies associated to the second prior learning evidence.
        $ucs = user_evidence_competency::get_user_competencies_by_userevidenceid($ue2->get_id());
        $this->assertCount(1, $ucs);
        $uc = array_shift($ucs);
        $this->assertEquals($uc->get_id(), $uc22->get_id());
    }
}
