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
 * "Business" class for Iomad Learning Paths
 *
 * @package    local_iomadlearninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_iomad_learningpath;

defined('MOODLE_INTERNAL') || die();

class companypaths {

    protected $context;

    protected $companyid;

    public function __construct($companyid, $context) {
        $this->context = $context;
        $this->companyid = $companyid;
    }

    /**
     * Get learning paths for company.
     * @return array
     */
    public function get_paths() {
        global $DB;

        $paths = $DB->get_records('local_iomad_learningpath', array('company' => $this->companyid));
        
        return $paths;
    }

    /**
     * Get/check path
     * @param in $id (0 = new/empty)
     * @return object $path
     */
    public function get_path($id) {
        global $DB;

        if ($path = $DB->get_record('local_iomad_learningpath', array('id' => $id))) {
            if ($path->company != $this->companyid) {
                throw new \Exception("Company id does not match expected");
            }

            return $path;
        } else {
            $path = new \stdClass;
            $path->company = $this->companyid;
            $path->timecreated = time();
            $path->timeupdated = time();
            $path->name = '';
            $path->description = '';
            $path->active = 0;

            return $path;
        }
    }

}
