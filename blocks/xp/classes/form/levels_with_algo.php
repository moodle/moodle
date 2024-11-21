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
 * Block XP levels form.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

use moodleform;
use block_xp\local\xp\level_with_name;
use block_xp\local\xp\level_with_description;

/**
 * Block XP levels form class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since 3.10.0. Use external service instead.
 */
class levels_with_algo extends moodleform {

    /** @var config The config. */
    protected $config;

    /**
     * Form definintion.
     *
     * @return void
     */
    public function definition() {
        global $OUTPUT;

        debugging('The class \block_xp\form\levels_with_algo is deprecated.', DEBUG_DEVELOPER);

        $mform = $this->_form;
        $config = isset($this->_customdata['config']) ? $this->_customdata['config'] : null;

        $mform->setDisableShortforms(true);
        $mform->addElement('header', 'hdrgen', get_string('general', 'form'));

        $mform->addElement('text', 'levels', get_string('levelcount', 'block_xp'));
        $mform->addRule('levels', get_string('required'), 'required');
        $mform->setType('levels', PARAM_INT);

        $mform->addElement('selectyesno', 'usealgo', get_string('usealgo', 'block_xp'));

        $mform->addElement('text', 'basexp', get_string('basexp', 'block_xp'));
        $mform->disabledIf('basexp', 'usealgo', 'eq', 0);
        $mform->setType('basexp', PARAM_INT);
        $mform->setAdvanced('basexp', true);

        $mform->addElement('text', 'coefxp', get_string('coefxp', 'block_xp'));
        $mform->disabledIf('coefxp', 'usealgo', 'eq', 0);
        $mform->setType('coefxp', PARAM_FLOAT);
        $mform->setAdvanced('coefxp', true);

        $mform->addElement('submit', 'updateandpreview', get_string('updateandpreview', 'block_xp'));
        $mform->registerNoSubmitButton('updateandpreview');

        // First level.
        $mform->addElement('header', 'hdrlevel1', get_string('levelx', 'block_xp', 1));
        $mform->addElement('static', 'lvlxp_1', get_string('pointsrequired', 'block_xp'), 0);

        $mform->addElement('text', 'lvlname_1', get_string('levelname', 'block_xp'), ['maxlength' => 40]);
        $mform->addRule('lvlname_1', get_string('maximumchars', '', 40), 'maxlength', 40);
        $mform->setType('lvlname_1', PARAM_NOTAGS);
        $mform->addHelpButton('lvlname_1', 'levelname', 'block_xp');

        $mform->addElement('text', 'lvldesc_1', get_string('leveldesc', 'block_xp'), ['maxlength' => 255, 'size' => 50]);
        $mform->addRule('lvldesc_1', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setType('lvldesc_1', PARAM_NOTAGS);
        $mform->addHelpButton('lvldesc_1', 'leveldesc', 'block_xp');

        $mform->addelement('hidden', 'insertlevelshere');
        $mform->setType('insertlevelshere', PARAM_BOOL);

        $this->add_action_buttons();

    }

    /**
     * Definition after data.
     *
     * @return void
     */
    public function definition_after_data() {
        $mform = $this->_form;

        // Ensure that the values are not wrong, the validation on save will catch those problems.
        $levels = max((int) $mform->exportValue('levels'), 2);
        $base = max((int) $mform->exportValue('basexp'), 1);
        $coef = max((float) $mform->exportValue('coefxp'), 1);

        $defaultlevels = \block_xp\local\xp\algo_levels_info::get_xp_with_algo($levels, $base, $coef);

        // Add the levels.
        for ($i = 2; $i <= $levels; $i++) {
            $el =& $mform->createElement('header', 'hdrlevel' . $i, get_string('levelx', 'block_xp', $i));
            $mform->insertElementBefore($el, 'insertlevelshere');

            $el =& $mform->createElement('text', 'lvlxp_' . $i, get_string('pointsrequired', 'block_xp'));
            $mform->insertElementBefore($el, 'insertlevelshere');
            $mform->setType('lvlxp_' . $i, PARAM_INT);
            $mform->disabledIf('lvlxp_' . $i, 'usealgo', 'eq', 1);
            if ($mform->exportValue('usealgo') == 1) {
                // Force the constant value when the algorightm is used.
                $mform->setConstant('lvlxp_' . $i, $defaultlevels[$i]);
            }

            $el =& $mform->createElement('text', 'lvlname_' . $i, get_string('levelname', 'block_xp'), ['maxlength' => 40]);
            $mform->insertElementBefore($el, 'insertlevelshere');
            $mform->addRule('lvlname_' . $i, get_string('maximumchars', '', 40), 'maxlength', 40);
            $mform->setType('lvlname_' . $i, PARAM_NOTAGS);

            $el =& $mform->createElement('text', 'lvldesc_' . $i, get_string('leveldesc', 'block_xp'),
                ['maxlength' => 255, 'size' => 50]);
            $mform->insertElementBefore($el, 'insertlevelshere');
            $mform->addRule('lvldesc_' . $i, get_string('maximumchars', '', 255), 'maxlength', 255);
            $mform->setType('lvldesc_' . $i, PARAM_NOTAGS);
        }
    }

    /**
     * Get the levels info from submitted data.
     *
     * @return block_xp\local\levels Levels.
     */
    public function get_levels_from_data() {
        $data = parent::get_data();
        if (!$data) {
            return $data;
        }

        // Rearranging the information.
        $newdata = [
            'usealgo' => $data->usealgo,
            'base' => $data->basexp,
            'coef' => $data->coefxp,
            'xp' => [
                '1' => 0,
            ],
            'desc' => [],
            'name' => [],
        ];

        $keys = ['xp', 'desc', 'name'];
        for ($i = 1; $i <= $data->levels; $i++) {
            foreach ($keys as $key) {
                $datakey = 'lvl' . $key . '_' . $i;
                if (!empty($data->{$datakey})) {
                    $newdata[$key][$i] = $data->{$datakey};
                }
            }
        }

        return new \block_xp\local\xp\algo_levels_info($newdata);
    }

    /**
     * Set the data from the levels.
     *
     * Note that this does not use the interface levels_info. This is
     * dependent on the default implementation.
     *
     * @param \block_xp\local\xp\algo_levels_info $levels Levels.
     */
    public function set_data_from_levels(\block_xp\local\xp\algo_levels_info $levels) {
        $data = [
            'levels' => $levels->get_count(),
            'usealgo' => (int) $levels->get_use_algo(),
            'coefxp' => $levels->get_coef(),
            'basexp' => $levels->get_base(),
        ];
        foreach ($levels->get_levels() as $level) {
            $data['lvlxp_' . $level->get_level()] = $level->get_xp_required();
            $data['lvldesc_' . $level->get_level()] = $level instanceof level_with_description ? $level->get_description() : '';
            $data['lvlname_' . $level->get_level()] = $level instanceof level_with_name ? $level->get_name() : '';
        }
        $this->set_data($data);
    }

    /**
     * Data validate.
     *
     * @param array $data The data submitted.
     * @param array $files The files submitted.
     * @return array of errors.
     */
    public function validation($data, $files) {
        $errors = [];
        if ($data['levels'] < 2) {
            $errors['levels'] = get_string('errorlevelsincorrect', 'block_xp');
        }

        // Validating the XP points.
        if (!isset($errors['levels'])) {
            $lastxp = 0;
            for ($i = 2; $i <= $data['levels']; $i++) {
                $key = 'lvlxp_' . $i;
                $xp = isset($data[$key]) ? (int) $data[$key] : -1;
                if ($xp <= 0) {
                    $errors['lvlxp_' . $i] = get_string('invalidxp', 'block_xp');
                } else if ($lastxp >= $xp) {
                    $errors['lvlxp_' . $i] = get_string('errorxprequiredlowerthanpreviouslevel', 'block_xp');
                }
                $lastxp = $xp;
            }
        }

        return $errors;
    }

}
