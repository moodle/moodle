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
 * Form.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;

use core_form\dynamic_form;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/duration.php');
require_once(__DIR__ . '/itemspertime.php');

/**
 * Form.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cheatguard extends dynamic_form {

    use dynamic_world_trait;

    /** @var string */
    protected $routename = 'rules';

    public function process_dynamic_submission() {
        $config = $this->get_world()->get_config();
        $data = $this->get_data();
        unset($data->contextid);
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
        $world = $this->get_world();
        $renderer = \block_xp\di::get('renderer');
        $config = \block_xp\di::get('config');
        $urlresolver = \block_xp\di::get('url_resolver');

        $mform = $this->_form;
        $mform->addElement('hidden', 'contextid', $this->get_world()->get_context()->id);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('selectyesno', 'enablecheatguard', get_string('enablecheatguard', 'block_xp'));
        $mform->addHelpButton('enablecheatguard', 'enablecheatguard', 'block_xp');

        $mform->addElement('block_xp_form_itemspertime', 'maxactionspertime', get_string('maxactionspertime', 'block_xp'), [
            'maxunit' => 60,
            'itemlabel' => get_string('actions', 'block_xp'),
        ]);
        $mform->addHelpButton('maxactionspertime', 'maxactionspertime', 'block_xp');
        $mform->disabledIf('maxactionspertime', 'enablecheatguard', 'eq', 0);

        $mform->addElement('block_xp_form_duration', 'timebetweensameactions', get_string('timebetweensameactions', 'block_xp'), [
            'maxunit' => 60,
            'optional' => false,        // We must set this...
        ]);
        $mform->addHelpButton('timebetweensameactions', 'timebetweensameactions', 'block_xp');
        $mform->disabledIf('timebetweensameactions', 'enablecheatguard', 'eq', 0);

        if ($world->get_config()->get('enablecheatguard') && $config->get('enablepromoincourses')) {
            $worldconfig = $world->get_config();
            $timeframe = max(0, $worldconfig->get('timebetweensameactions'), $worldconfig->get('timeformaxactions'));

            $promourl = $urlresolver->reverse('promo', ['courseid' => $world->get_courseid()]);
            if ($timeframe > HOURSECS * 6) {
                $mform->addElement('static', '', '', $renderer->notification_without_close(
                    get_string('promocheatguard', 'block_xp', ['url' => $promourl->out()]
                ), 'warning'));
            }
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

        // Convert back from itemspertime.
        if (!isset($data->maxactionspertime) || !is_array($data->maxactionspertime)) {
            $data->maxactionspertime = 0;
            $data->timeformaxactions = 0;
        } else {
            $data->timeformaxactions = (int) $data->maxactionspertime['time'];
            $data->maxactionspertime = (int) $data->maxactionspertime['points'];
        }

        // When the cheat guard is disabled, we remove the config fields so that
        // we can keep the defaults and the data previously submitted by the user.
        if (empty($data->enablecheatguard)) {
            unset($data->maxactionspertime);
            unset($data->timeformaxactions);
            unset($data->timebetweensameactions);
        }

        return $data;
    }

    /**
     * Set the data.
     *
     * @param mixed $data The data.
     */
    public function set_data($data) {
        $data = (array) $data;

        // Convert to itemspertime.
        if (isset($data['maxactionspertime']) && isset($data['timeformaxactions'])) {
            $data['maxactionspertime'] = [
                'points' => (int) $data['maxactionspertime'],
                'time' => (int) $data['timeformaxactions'],
            ];
            unset($data['timeformaxactions']);
        }

        parent::set_data($data);
    }

}
