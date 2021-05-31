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
 * The block_iomad_company_admin company course updated event.
 *
 * @package    block_iomad_company_admin
 * @copyright  2018 E-Learn Design Ltd. http://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The block_iomad_company_admin company course updated event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int companyid: the id of the company.
 *
 * }
 *
 * @package    block_iomad_company_admin
 * @since      Moodle 3.6
 * @copyright  2020 E-Learn Design Ltd. http://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class company_course_updated extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'iomad_courses';
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('companycourseupdated', 'block_iomad_company_admin');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' updaed the course with id'" . s($this->courseid);
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/block/iomad_company_admin/iomad_courses_form.php');
    }

    /**
     * Return the legacy event log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array($this->userid, 'iomad', 'company course updated ', '/blocks/ioamd_company_admin/iomad_courses_form.php',
            ' course id ' . $this->courseid, $this->contextinstanceid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['iomadcourse'])) {
            throw new \coding_exception('The \'iomadcourse\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        $othermapped = array();

        return $othermapped;
    }
}
