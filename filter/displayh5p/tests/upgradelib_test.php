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

namespace filter_displayh5p;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/displayh5p/db/upgradelib.php');
require_once("$CFG->libdir/filterlib.php");

/**
 * Unit tests for the upgradelib of the Display H5P filter.
 *
 * @package    filter_displayh5p
 * @category   test
 * @copyright 2019 Carlos Escobedo <carlos@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class upgradelib_test extends \advanced_testcase {

    /**
     * test_filter_displayh5p_reorder
     */
    public function test_filter_displayh5p_reorder(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // We disable the three filters involved to reorder.
        // To do this, we will enable them in order and so they will be placed sorted.
        filter_set_global_state('displayh5p', TEXTFILTER_DISABLED);
        filter_set_global_state('activitynames', TEXTFILTER_DISABLED);
        filter_set_global_state('urltolink', TEXTFILTER_DISABLED);
        // First, we enabled activitynames and urltolink.
        // So, displayh5p will be below them.
        filter_set_global_state('activitynames', TEXTFILTER_ON);
        filter_set_global_state('urltolink', TEXTFILTER_ON);

        // We get the new order of the filter.
        $states = filter_get_global_states();
        $displayh5ppos = $states['displayh5p']->sortorder;
        $activitynamespos = $states['activitynames']->sortorder;
        $urltolinkpos = $states['urltolink']->sortorder;

        // Make sure that activitynames and urltolink are over the displayh5p.
        $this->assertLessThan($displayh5ppos, $activitynamespos);
        $this->assertLessThan($displayh5ppos, $urltolinkpos);

        // Call the function to reorder displayh5p.
        filter_displayh5p_reorder();
        // Get the new orders.
        $states = filter_get_global_states();
        $displayh5ppos = $states['displayh5p']->sortorder;
        $activitynamespos = $states['activitynames']->sortorder;
        $urltolinkpos = $states['urltolink']->sortorder;
        // Make sure that displayh5p are over activitynames and urltolink.
        $this->assertLessThan($activitynamespos, $displayh5ppos);
        $this->assertLessThan($urltolinkpos, $displayh5ppos);

        // Make sure that displayh5p is enabled.
        $this->assertEquals(TEXTFILTER_ON, $states['displayh5p']->active);
    }
}
