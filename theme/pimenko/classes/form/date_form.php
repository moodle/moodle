<?php
// This file is part of the Pimenko theme for Moodle
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
 * Theme Pimenko date form feature.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_pimenko\form;

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir."/formslib.php");

use moodleform;

/**
 * Settings form for the date filter.
 *
 * @package     report_digiboard
 * @category    admin
 * @copyright   Pimenko 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class date_form extends moodleform {

    public function definition(): void {
        $mform = $this->_form;
        $mform->disable_form_change_checker();

        if (!$this->_customdata->name) {
            $name = get_string('datefilter', 'theme_pimenko');
        } else {
            $name = $this->_customdata->name;
        }

        $date = $this->_customdata->urlselectedvalue ?? time();

        $mform->addElement('date_selector', 'date_selector', $name);
        $mform->setDefault('date_selector', $date);
    }
}
