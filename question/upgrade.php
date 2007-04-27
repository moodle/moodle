<?php
/**
 * This file contains dtabase upgrade code that is called from lib/db/upgrade.php,
 * and also check methods that can be used for pre-install checks via
 * admin/environment.php and lib/environmentlib.php.
 *
 * @copyright &copy; 2007 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package package_name
 *//** */

/**
 * This test is becuase the RQP question type was included in core
 * up to and including Moodle 1.8, and was removed before Moodle 1.9.
 * 
 * Therefore, we want to check whether any rqp questions exist in the database
 * before doing the upgrade. However, the check is not relevant if that 
 * question type was never installed, or if the person has chosen to 
 * manually reinstall the rqp question type from contrib.
 * 
 * @param $version the version to test.
 * @return null if the test is irrelevant, or true or false depending on whether the test passes.
 */
function question_check_no_rqp_questions($result) {
    global $CFG;

    if (empty($CFG->qtype_rqp_version) || is_dir($CFG->dirroot . '/question/type/rqp')) {
        return null;
    } else {
        $result->setStatus(count_records('question', 'qtype', 'rqp') == 0);
    }
    return $result;
}
?>
