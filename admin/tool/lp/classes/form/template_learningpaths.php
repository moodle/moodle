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
 * Template learningpaths form.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\form;
defined('MOODLE_INTERNAL') || die();

use moodleform;

require_once($CFG->libdir . '/formslib.php');

/**
 * Template learningpaths form class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_learningpaths extends moodleform {

    /**
     * Form definition
     *
     * @return void
     */
    public function definition() {
        global $DB, $companyid;

        $mform = $this->_form;

        $excludesql = "";
        if (!empty($this->_customdata['excludelearningpaths'])) {
            $excludesql = "AND id NOT IN (" . implode(',', $this->_customdata['excludelearningpaths']) .")";
        }

        $learningpaths = $DB->get_records_sql_menu("SELECT id,name FROM {iomad_learningpath}
                                                    WHERE company = :companyid
                                                    $excludesql
                                                    ORDER BY name", ['companyid' => $companyid]);
        $mform->addElement('autocomplete', 'learningpaths', get_string('selectlearningpathstosync', 'block_iomad_learningpath'), $learningpaths, ['multiple' => true]);
        $mform->addElement('submit', 'submit', get_string('addlearningpaths', 'block_iomad_learningpath'));
    }
}
