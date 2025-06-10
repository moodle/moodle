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
 * \mod_hvp\content_type_cache_form class
 *
 * @package    mod_hvp
 * @copyright  2017 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_hvp;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Form to update the content type cache that mirrors the available libraries in the H5P hub.
 *
 * @package    mod_hvp
 * @copyright  2017 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_type_cache_form extends \moodleform {

    /**
     * Define form elements
     */
    public function definition() {
        // Get form.
        $mform = $this->_form;

        // Get and format date.
        $lastupdate = get_config('mod_hvp', 'content_type_cache_updated_at');

        $dateformatted = $lastupdate ? \userdate($lastupdate) : get_string('ctcacheneverupdated', 'hvp');

        // Add last update info.
        $mform->addElement('static', 'lastupdate',
            get_string('ctcachelastupdatelabel', 'hvp'), $dateformatted);

        $mform->addElement('static', 'lastupdatedescription', '',
            get_string('ctcachedescription', 'hvp'));

        // Update cache button.
        $this->add_action_buttons(false, get_string('ctcachebuttonlabel', 'hvp'));
    }
}
