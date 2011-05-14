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
 * Unit tests for (some of) ../locallib.php.
 *
 * @package    qtype
 * @subpackage opaque
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for (some of) ../locallib.php.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_opaque_locallib_test extends UnitTestCase {
    public function test_is_same_engine() {
        $manager = new qtype_opaque_engine_manager();

        $engine1 = new stdClass();
        $engine1->name = 'OpenMark live servers';
        $engine1->passkey = '';
        $engine1->questionengines = array(
                'http://ltsweb1.open.ac.uk/om-qe/services/Om',
                'http://ltsweb2.open.ac.uk/om-qe/services/Om');
        $engine1->questionbanks = array(
                'https://ltsweb1.open.ac.uk/openmark/!question',
                'https://ltsweb2.open.ac.uk/openmark/!question');

        $engine2 = new stdClass();
        $engine2->name = 'OpenMark live servers';
        $engine2->passkey = '';
        $engine2->questionengines = array(
                'http://ltsweb1.open.ac.uk/om-qe/services/Om',
                'http://ltsweb2.open.ac.uk/om-qe/services/Om');
        $engine2->questionbanks = array(
                'https://ltsweb1.open.ac.uk/openmark/!question',
                'https://ltsweb2.open.ac.uk/openmark/!question');
        $this->assertTrue($manager->is_same_engine($engine1, $engine2));

        $engine2->questionbanks = array(
                'https://ltsweb2.open.ac.uk/openmark/!question',
                'https://ltsweb1.open.ac.uk/openmark/!question');
        $this->assertTrue($manager->is_same_engine($engine1, $engine2));

        $engine2->name = 'Frog';
        $this->assertTrue($manager->is_same_engine($engine1, $engine2));

        $engine2->passkey = 'newt';
        $this->assertFalse($manager->is_same_engine($engine1, $engine2));

        $engine2->passkey = '';
        $engine2->questionengines = array(
                'http://ltsweb2.open.ac.uk/om-qe/services/Om');
        $this->assertFalse($manager->is_same_engine($engine1, $engine2));
    }
}
