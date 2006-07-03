<?php
/**
 * This file is used to include a link to the unit tests on the report page
 * /admin/report.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version $Id$
 * @package SimpleTestEx
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); //  It must be included from a Moodle page
}
$langfile = 'simpletest';

print_heading(get_string('unittests', $langfile));
print_heading('<a href="'.$CFG->wwwroot.'/admin/report/simpletest/index.php">' . 
        get_string('rununittests', $langfile) . '</a>', '', 3);
?>