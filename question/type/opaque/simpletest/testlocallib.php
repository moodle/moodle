<?php
/**
 * Unit tests for (some of) mod/quiz/accessrules.php.
 *
 * @copyright &copy; 2008 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

class opaque_locallib_test extends UnitTestCase {
    function test_is_same_engine() {
        $manager = new qtype_opaque_engine_manager();

        $engine1 = new stdClass;
        $engine1->name = 'OpenMark live servers';
        $engine1->passkey = '';
        $engine1->questionengines = array(
                'http://ltsweb1.open.ac.uk/om-qe/services/Om',
                'http://ltsweb2.open.ac.uk/om-qe/services/Om');
        $engine1->questionbanks = array(
                'https://ltsweb1.open.ac.uk/openmark/!question',
                'https://ltsweb2.open.ac.uk/openmark/!question');

        $engine2 = new stdClass;
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
?>
