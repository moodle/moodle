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
 * Example script, showing how it is possible to only do a part-upgrade of the
 * attempt data during the main upgrade, and then finish the job off later.
 *
 * If you want to use this facility, then you need to:
 *
 * 1. Rename this script to partialupgrade.php.
 * 2. Look at the various example functions below for controlling the upgrade,
 *    chooose one you like, and un-comment it. Alternatively, write your own
 *    custom function.
 * 3. Use the List quizzes and attempts options in this plugin, which should now
 *    display updated information.
 * 4. Once you are sure that works, you can proceed with the upgrade as usual.
 *
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * This is a very simple example that just uses a hard-coded array to control
 * which attempts are upgraded.
 *
 * @return array of quiz ids that are the ones to upgrade during the main
 * upgrade from 2.0 to 2.1. Attempts at other quizzes are left alone, you will
 * have to take steps to upgrade them yourself using the facilities provided by
 * this plugin.
 */
//function local_qeupgradehelper_get_quizzes_to_upgrade() {
//    return array(1, 2, 3);
//}


/**
 * This example function uses a list of quiz ids from a file.
 *
 * It is currently set to use the file quiz-ids-to-upgrade.txt in the same
 * folder as this script, but you can change that if you like.
 *
 * That file should contain one quiz id per line, with no punctuation. Any line
 * that does not look like an integer is ignored.
 *
 * @return array of quiz ids that are the ones to upgrade during the main
 * upgrade from 2.0 to 2.1. Attempts at other quizzes are left alone, you will
 * have to take steps to upgrade them yourself using the facilities provided by
 * this plugin.
 */
//function local_qeupgradehelper_get_quizzes_to_upgrade() {
//    global $CFG;
//    $rawids = file($CFG->dirroot . '/local/qeupgradehelper/quiz-ids-to-upgrade.txt');
//    $cleanids = array();
//    foreach ($rawids as $id) {
//        $id = clean_param($id, PARAM_INT);
//        if ($id) {
//            $cleanids[] = $id;
//        }
//    }
//    return $cleanids;
//}


/**
 * This example uses a complex SQL query to decide which attempts to upgrade.
 *
 * The particular example I have done here is to return the ids of all the quizzes
 * in courses that started more recently than one year ago. Of coures, you can
 * write any query you like to meet your needs.
 *
 * Remember that you can use the List quizzes and attempts options option provided
 * by this plugin to verify that your query is selecting the quizzes you intend.
 *
 * @return array of quiz ids that are the ones to upgrade during the main
 * upgrade from 2.0 to 2.1. Attempts at other quizzes are left alone, you will
 * have to take steps to upgrade them yourself using the facilities provided by
 * this plugin.
 */
//function local_qeupgradehelper_get_quizzes_to_upgrade() {
//    global $DB;
//
//    $quizmoduleid = $DB->get_field('modules', 'id', array('name' => 'quiz'));
//
//    $oneyearago = strtotime('-1 year');
//
//    return $DB->get_fieldset_sql('
//        SELECT DISTINCT quiz.id
//
//        FROM {quiz} quiz
//        JOIN {course_modules} cm ON cm.module = :quizmoduleid
//                AND cm.instance = quiz.id
//        JOIN {course} c ON quiz.course = c.id
//
//        WHERE c.startdate > :cutoffdate
//
//        ORDER BY quiz.id
//        ', array('quizmoduleid' => $quizmoduleid, 'cutoffdate' => $oneyearago));
//    ");
//}
