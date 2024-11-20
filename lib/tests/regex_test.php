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

namespace core;

/**
 * Test PHP regex capability - this may also serve as an example for devs.
 *
 * @package   core
 * @copyright 2015 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */
class regex_test extends \advanced_testcase {
    public function test_whitespace_replacement_with_u(): void {
        $unicode = "Теорія і практика використання системи управління навчанням Moo
dleКиївський національний університет будівництва і архітектури, 21-22 тра
вня 2015 р.http://2015.moodlemoot.in.ua/";

        $whitespaced = preg_replace('/\s+/u', ' ', $unicode);
        $this->assertSame(str_replace("\n", ' ', $unicode), $whitespaced);
    }
}


