<?php
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
 * Moodle course unit test for Kaltura
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner Inc
 * @copyright  (C) 2008-2014 http://www.remote-learner.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/kalvidassign/locallib.php');

/**
 * @group mod_kalvidassign
 */
class locallib_testcase extends advanced_testcase {
    /**
     * This function tests output from kalvidassign_get_player_dimensions()
     */
    public function test_kalvidassign_get_player_dimensions_return_defaults() {
        $this->resetAfterTest(true);

        $result = kalvidassign_get_player_dimensions();

        $this->assertCount(2, $result);
        $this->assertEquals(400, $result[0]);
        $this->assertEquals(365, $result[1]);
    }

    /**
     * This function tests output from kalvidassign_get_player_dimensions()
     */
    public function test_kalvidassign_get_player_dimensions_return_configured_results() {
        $this->resetAfterTest(true);

        set_config('kalvidassign_player_width', 500, 'local_kaltura');
        set_config('kalvidassign_player_height', 500, 'local_kaltura');

        $result = kalvidassign_get_player_dimensions();

        $this->assertCount(2, $result);
        $this->assertEquals('500', $result[0]);
        $this->assertEquals('500', $result[1]);
    }

    /**
     * This function tests output from kalvidassign_get_player_dimensions()
     */
    public function test_kalvidassign_get_player_dimensions_return_default_results_when_empty() {
        $this->resetAfterTest(true);

        $result = kalvidassign_get_player_dimensions();

        set_config('kalvidassign_player_width', '', 'local_kaltura');
        set_config('kalvidassign_player_height', '', 'local_kaltura');
        $this->assertCount(2, $result);
        $this->assertEquals(400, $result[0]);
        $this->assertEquals(365, $result[1]);
    }
}