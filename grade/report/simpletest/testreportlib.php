<?php // $Id$
/**
 * Unit tests for grade/report/lib.php.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/grade/report/lib.php');

/**
 * @TODO create a set of mock objects to simulate the database operations. We don't want to connect to any real sql server.
 */
class gradereportlib_test extends UnitTestCase {
    var $courseid = 1;
    var $context = null;
    var $report = null;

    function setUp() {
        $this->report = new grade_report($this->courseid, $this->context);
    }

}

?>
