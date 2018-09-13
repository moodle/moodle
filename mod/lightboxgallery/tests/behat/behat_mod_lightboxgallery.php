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
 * Steps definitions related with the forum activity.
 *
 * @package    mod_lightboxgallery
 * @category   test
 * @copyright  2016 Blackboard
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode;
/**
 * Lightboxgallery-related steps definitions.
 *
 * @package    mod_lightboxgallery
 * @category   test
 * @copyright  2016 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_lightboxgallery extends behat_base {

    /**
     * Allow a gallery to be viewed just by knowing its idnumber.
     * This is a helper function to jump to a gallery without going throught
     * the course page, simulating the case where the user knows the URL but
     * can't go through the course (ispublic flag is set).
     *
     * @Given /^I view the lightboxgallery with idnumber "(?P<lightboxgallery_idnumber>(?:[^"]|\\")*)"$/
     * @param string $idnumber
     */
    public function i_view_the_lightboxgallery_with_idnumber($idnumber) {
        global $DB;

        $sql = "SELECT cm.id
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                WHERE m.name = 'lightboxgallery' AND cm.idnumber = ?";
        $cm = $DB->get_record_sql($sql, [$idnumber]);

        $href = new moodle_url('/mod/lightboxgallery/view.php', ['id' => $cm->id]);
        $this->getSession()->visit($href->out());
    }

}
