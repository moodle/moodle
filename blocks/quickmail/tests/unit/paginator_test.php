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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\repos\pagination\paginator;
use block_quickmail\repos\pagination\paginated;

class block_quickmail_paginator_testcase extends advanced_testcase {

    use has_general_helpers;

    public function test_sets_paginator_properties_scenario_one() {
        $this->resetAfterTest(true);

        $uri = '/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc';

        $paginator = new paginator(25, 1, 10, $uri);

        $this->assertEquals(25, $paginator->total_count);
        $this->assertEquals(1, $paginator->page);
        $this->assertEquals(10, $paginator->per_page);
        $this->assertEquals($uri, $paginator->page_uri);
        $this->assertEquals(3, $paginator->total_pages);
        $this->assertEquals(0, $paginator->offset);
    }

    public function test_sets_paginator_properties_scenario_two() {
        $this->resetAfterTest(true);

        $uri = '/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc';

        $paginator = new paginator(121, 4, 7, $uri);

        $this->assertEquals(121, $paginator->total_count);
        $this->assertEquals(4, $paginator->page);
        $this->assertEquals(7, $paginator->per_page);
        $this->assertEquals($uri, $paginator->page_uri);
        $this->assertEquals(18, $paginator->total_pages);
        $this->assertEquals(21, $paginator->offset);
    }

    public function test_sets_paginator_properties_scenario_three() {
        $this->resetAfterTest(true);

        $uri = '/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc';

        $paginator = new paginator(8, 1, 11, $uri);

        $this->assertEquals(8, $paginator->total_count);
        $this->assertEquals(1, $paginator->page);
        $this->assertEquals(11, $paginator->per_page);
        $this->assertEquals($uri, $paginator->page_uri);
        $this->assertEquals(1, $paginator->total_pages);
        $this->assertEquals(0, $paginator->offset);
    }

    public function test_sets_paginator_page_when_less_than_one() {
        $this->resetAfterTest(true);

        $uri = '/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc';

        $paginator = new paginator(8, -4, 11, $uri);

        $this->assertEquals(1, $paginator->page);
    }

    public function test_sets_paginator_page_when_higher_than_appropriate() {
        $this->resetAfterTest(true);

        $uri = '/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc';

        $paginator = new paginator(12, 8, 11, $uri);

        $this->assertEquals(2, $paginator->page);
    }

    public function test_paginator_returns_paginated_object_scenario_one() {
        $this->resetAfterTest(true);

        $uri = '/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc';

        $paginator = new paginator(25, 1, 10, $uri);

        $paginated = $paginator->paginated();

        $this->assertInstanceOf(paginated::class, $paginated);
        $this->assertEquals(3, $paginated->page_count);
        $this->assertEquals(0, $paginated->offset);
        $this->assertEquals(10, $paginated->per_page);
        $this->assertEquals(1, $paginated->current_page);
        $this->assertEquals(2, $paginated->next_page);
        $this->assertEquals(1, $paginated->previous_page);
        $this->assertEquals(25, $paginated->total_count);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=1', $paginated->uri_for_page);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=1', $paginated->first_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=3', $paginated->last_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=2', $paginated->next_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=1', $paginated->previous_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=', $paginated->empty_uri);
    }

    public function test_paginator_returns_paginated_object_scenario_two() {
        $this->resetAfterTest(true);

        $uri = '/blocks/quickmail/sent.php?courseid=7&page=3&sort=subject&dir=asc';

        $paginator = new paginator(25, 3, 4, $uri);

        $paginated = $paginator->paginated();

        $this->assertEquals(7, $paginated->page_count);
        $this->assertEquals(8, $paginated->offset);
        $this->assertEquals(4, $paginated->per_page);
        $this->assertEquals(3, $paginated->current_page);
        $this->assertEquals(4, $paginated->next_page);
        $this->assertEquals(2, $paginated->previous_page);
        $this->assertEquals(25, $paginated->total_count);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=3', $paginated->uri_for_page);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=1', $paginated->first_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=7', $paginated->last_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=4', $paginated->next_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=2', $paginated->previous_page_uri);
        $this->assertEquals('/blocks/quickmail/sent.php?courseid=7&sort=subject&dir=asc&page=', $paginated->empty_uri);
    }

}
