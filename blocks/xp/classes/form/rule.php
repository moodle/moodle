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

use block_xp\di;
use context;
use core_form\dynamic_form;
use moodle_url;

/**
 * Form.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule extends dynamic_form {

    /** @var object The rule record. */
    protected $rule;

    /**
     * Get the rule.
     *
     * @return object The rule record.
     */
    protected function get_rule() {
        if (!isset($this->rule)) {
            $this->rule = di::get('db')->get_record('block_xp_rule', ['id' => $this->optional_param('id', 0, PARAM_INT)]);
        }
        return $this->rule;
    }

    protected function get_context_for_dynamic_submission(): context {
        return context::instance_by_id($this->get_rule()->contextid);
    }

    protected function check_access_for_dynamic_submission(): void {
        $worldfactory = di::get('context_world_factory');
        $world = $worldfactory->get_world_from_context(\context::instance_by_id($this->get_rule()->contextid));
        $perms = $world->get_access_permissions();
        $perms->require_manage();
    }

    public function process_dynamic_submission() {
        $data = $this->get_data();
        $rule = $this->get_rule();
        $rule->points = $data->points;
        di::get('db')->update_record('block_xp_rule', $rule);
    }

    public function set_data_for_dynamic_submission(): void {
        $this->set_data([
            'id' => $this->get_rule()->id,
            'points' => $this->get_rule()->points,
        ]);
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $urlresolver = di::get('url_resolver');
        $rule = $this->get_rule();

        $worldfactory = di::get('context_world_factory');
        $world = $worldfactory->get_world_from_context(\context::instance_by_id($this->get_rule()->contextid));

        $urlname = 'completionrules';
        $anchorname = $rule->type;

        $url = $urlresolver->reverse($urlname, ['courseid' => $world->get_courseid()]);
        $url->set_anchor($anchorname);

        return $url;
    }

    /**
     * The definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $this->get_rule()->id);

        $mform->addElement('text', 'points', get_string('pointstoaward', 'block_xp'), ['size' => 5]);
        $mform->setType('points', PARAM_INT);
        $mform->addHelpButton('points', 'pointstoaward', 'block_xp');
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['points'] < 0 || $data['points'] > 9999999) {
            $errors['points'] = get_string('invaliddata', 'core_error');
        }

        return $errors;
    }

}
