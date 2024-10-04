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
 * Helpers for restrict course categories unit tests.
 *
 * @package    mod_lti
 * @copyright  2023 Jackson D'Souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */
trait mod_lti_course_categories_trait {

    /**
     * Setup course categories.
     *
     * @return array
     */
    public function setup_course_categories(): array {
        global $DB;

        $topcatdbrecord = $DB->get_record('course_categories', ['parent' => 0]);

        $subcata = $this->getDataGenerator()->create_category(['parent' => $topcatdbrecord->id, 'name' => 'cata']);
        $subcatadbrecord = $DB->get_record('course_categories', ['id' => $subcata->id]);

        $subcatca = $this->getDataGenerator()->create_category(['parent' => $subcata->id, 'name' => 'catca']);
        $subcatcadbrecord = $DB->get_record('course_categories', ['id' => $subcatca->id]);

        $subcatb = $this->getDataGenerator()->create_category(['parent' => $topcatdbrecord->id, 'name' => 'catb']);
        $subcatbdbrecord = $DB->get_record('course_categories', ['id' => $subcatb->id]);

        $subcatcb = $this->getDataGenerator()->create_category(['parent' => $subcatb->id, 'name' => 'catcb']);
        $subcatcbdbrecord = $DB->get_record('course_categories', ['id' => $subcatcb->id]);

        return [
            'topcat' => $topcatdbrecord,
            'subcata' => $subcatadbrecord,
            'subcatca' => $subcatcadbrecord,
            'subcatb' => $subcatb,
            'subcatcb' => $subcatcbdbrecord
        ];
    }

}
