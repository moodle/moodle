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
 * Visuals form.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

use block_xp\di;
use html_writer;
use moodleform;

/**
 * Visuals form class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class visuals extends moodleform {

    /**
     * The definition.
     */
    public function definition() {
        $renderer = di::get('renderer');

        $mform = $this->_form;
        $mform->addElement('filemanager', 'badges', get_string('levelbadges', 'block_xp'), null, $this->_customdata['fmoptions']);
        $mform->addHelpButton('badges', 'levelbadges', 'block_xp');

        if ($this->_customdata['showpromo'] ?? true) {
            $addonrequired = $renderer->render_from_template('block_xp/addon-required', [
                'promourl' => $this->_customdata['promourl'],
            ]);
            $mform->addElement('select', 'currencytheme', get_string('currencysign', 'block_xp') . ' ' . $addonrequired,
                ['' => get_string('currencysignxp', 'block_xp')], ['disabled' => 'disabled']);
            $mform->addHelpButton('currencytheme', 'currencysign', 'block_xp');
        }

        $this->add_action_buttons();
    }

}
