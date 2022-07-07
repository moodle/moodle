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
 * Privacy provider tests.
 *
 * @package    portfolio_flickr
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfolio_flickr_privacy_provider_test extends \core_privacy\tests\provider_testcase {

    /**
     *  Verify that a collection of metadata is returned for this component and that it just links to an external location.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('portfolio_flickr');
        $collection = \portfolio_flickr\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
        $items = $collection->get_collection();
        $this->assertEquals(1, count($items));
        $this->assertInstanceOf(\core_privacy\local\metadata\types\external_location::class, $items[0]);
    }
}
