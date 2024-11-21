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
 * File.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;

use block_xp\di;
use block_xp\local\config\course_world_config;
use core_form\dynamic_form;

/**
 * File.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class leaderboard extends dynamic_form {

    use dynamic_world_trait;

    /** @var string */
    protected $routename = 'ladder';

    public function process_dynamic_submission() {
        $config = $this->get_world()->get_config();
        $data = $this->get_data();
        $config->set_many((array) $data);
    }

    public function set_data_for_dynamic_submission(): void {
        $config = $this->get_world()->get_config();
        $this->set_data([
            'contextid' => $this->get_world()->get_context()->id,
        ] + $config->get_all());
    }

    /**
     * Form definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'contextid', $this->get_world()->get_context()->id);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('selectyesno', 'enableladder', get_string('enableladder', 'block_xp'));
        $mform->addHelpButton('enableladder', 'enableladder', 'block_xp');

        $els = [];
        $els[] = $mform->createElement('select', 'choices', '', [
            get_string('ladderisodefault', 'block_xp'),
            get_string('ladderisocohorts', 'block_xp'),
        ], ['disabled' => 'disabled']);
        $els[] = $mform->createElement(staticfield::name(), 'addonrequired', '', function() {
            $renderer = di::get('renderer');
            return $renderer->render_from_template('block_xp/addon-required', [
                'promourl' => di::get('url_resolver')->reverse('promo', ['courseid' => $this->world->get_courseid()])->out(false),
            ]);
        });
        $mform->addElement('group', 'ladderiso', get_string('ladderiso', 'block_xp'), $els);
        $mform->addHelpButton('ladderiso', 'ladderiso', 'block_xp');

        $mform->addElement('select', 'identitymode', get_string('anonymity', 'block_xp'), [
            course_world_config::IDENTITY_OFF => get_string('hideparticipantsidentity', 'block_xp'),
            course_world_config::IDENTITY_ON => get_string('displayparticipantsidentity', 'block_xp'),
        ]);
        $mform->addHelpButton('identitymode', 'anonymity', 'block_xp');

        $mform->addElement('select', 'neighbours', get_string('limitparticipants', 'block_xp'), [
            0 => get_string('displayeveryone', 'block_xp'),
            1 => get_string('displayoneneigbour', 'block_xp'),
            2 => get_string('displaynneighbours', 'block_xp', '2'),
            3 => get_string('displaynneighbours', 'block_xp', '3'),
            4 => get_string('displaynneighbours', 'block_xp', '4'),
            5 => get_string('displaynneighbours', 'block_xp', '5'),
        ]);
        $mform->addHelpButton('neighbours', 'limitparticipants', 'block_xp');

        $mform->addElement('select', 'rankmode', get_string('ranking', 'block_xp'), [
            course_world_config::RANK_OFF => get_string('hiderank', 'block_xp'),
            course_world_config::RANK_ON => get_string('displayrank', 'block_xp'),
            course_world_config::RANK_REL => get_string('displayrelativerank', 'block_xp'),
        ]);
        $mform->addHelpButton('rankmode', 'ranking', 'block_xp');

        $el = $mform->addElement('select', 'laddercols', get_string('ladderadditionalcols', 'block_xp'), [
            'xp' => get_string('total', 'block_xp'),
            'progress' => get_string('progress', 'block_xp'),
        ], ['style' => 'height: 4em;']);
        $el->setMultiple(true);
        $mform->addHelpButton('laddercols', 'ladderadditionalcols', 'block_xp');
    }

    /**
     * Definition after data.
     *
     * @return void
     */
    public function after_definition() {
        parent::after_definition();

        $mform = $this->_form;
        $configlocked = \block_xp\di::get('config_locked');
        foreach ($configlocked->get_all() as $key => $islocked) {
            if (!$islocked || !$mform->elementExists($key)) {
                continue;
            }
            $mform->hardFreeze($key);
        }
    }

    /**
     * Get the data.
     *
     * @return stdClass
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return $data;
        }

        // When not selecting any, the data is not sent.
        if (!isset($data->laddercols)) {
            $data->laddercols = [];
        }
        $data->laddercols = implode(',', $data->laddercols);

        // Remove placeholder.
        if (is_array($data->ladderiso ?? null)) {
            unset($data->ladderiso);
        }

        // Remove what we is for internal use.
        unset($data->contextid);

        return $data;
    }

    /**
     * Set the data.
     *
     * @param mixed $data The data.
     */
    public function set_data($data) {
        $data = (array) $data;
        if (isset($data['laddercols'])) {
            $data['laddercols'] = explode(',', $data['laddercols']);
        }
        parent::set_data($data);
    }

}
