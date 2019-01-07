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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file keeps track of upgrades to the lti module
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

/**
 * xmldb_lti_upgrade is the function that upgrades
 * the lti module database when is needed
 *
 * This function is automaticly called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 */
function xmldb_lti_upgrade($oldversion) {
    global $CFG, $DB;

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2017111301) {

        // A bug in the LTI plugin incorrectly inserted a grade item for
        // LTI instances which were set to not allow grading.
        // The change finds any LTI which does not have grading enabled,
        // and updates any grades to delete them.

        $ltis = $DB->get_recordset_sql("
                SELECT
                       l.id,
                       l.course,
                       l.instructorchoiceacceptgrades,
                       t.enabledcapability,
                       t.toolproxyid,
                       tc.value AS acceptgrades
                  FROM {lti} l
            INNER JOIN {grade_items} gt
                    ON l.id = gt.iteminstance
             LEFT JOIN {lti_types} t
                    ON t.id = l.typeid
             LEFT JOIN {lti_types_config} tc
                    ON tc.typeid = t.id AND tc.name = 'acceptgrades'
                 WHERE gt.itemmodule = 'lti'
                   AND gt.itemtype = 'mod'
        ");

        foreach ($ltis as $lti) {
            $acceptgrades = true;
            if (empty($lti->toolproxyid)) {
                $typeacceptgrades = isset($lti->acceptgrades) ? $lti->acceptgrades : 2;
                if (!($typeacceptgrades == 1 ||
                        ($typeacceptgrades == 2 && $lti->instructorchoiceacceptgrades == 1))) {
                    $acceptgrades = false;
                }
            } else {
                $enabledcapabilities = explode("\n", $lti->enabledcapability);
                $acceptgrades = in_array('Result.autocreate', $enabledcapabilities);
            }

            if (!$acceptgrades) {
                // Required when doing CLI upgrade.
                require_once($CFG->libdir . '/gradelib.php');
                grade_update('mod/lti', $lti->course, 'mod', 'lti', $lti->id, 0, null, array('deleted' => 1));
            }

        }

        $ltis->close();

        upgrade_mod_savepoint(true, 2017111301, 'lti');
    }

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    return true;

}
