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
 * Repository API unit tests
 *
 * @package   repository
 * @category  phpunit
 * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/repository/lib.php");

class repositorylib_testcase extends advanced_testcase {

    /**
     * Installing repository tests
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     */
    public function test_install_repository() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $syscontext = context_system::instance();
        $repositorypluginname = 'boxnet';
        // override repository permission
        $capability = 'repository/' . $repositorypluginname . ':view';
        $allroles = $DB->get_records_menu('role', array(), 'id', 'archetype, id');
        assign_capability($capability, CAP_ALLOW, $allroles['guest'], $syscontext->id, true);

        $plugintype = new repository_type($repositorypluginname);
        $pluginid = $plugintype->create(false);
        $this->assertInternalType('int', $pluginid);
        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $repository = reset($repos);
        $this->assertInstanceOf('repository', $repository);
        $info = $repository->get_meta();
        $this->assertEquals($repositorypluginname, $info->type);
    }
}
