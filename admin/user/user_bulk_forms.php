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
 * Bulk user action forms
 *
 * @package    core
 * @copyright  Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/datalib.php');

/**
 * Bulk user action form
 *
 * @copyright  Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_bulk_action_form extends moodleform {

    /** @var bool */
    protected $hasbulkactions = false;

    /** @var array|null */
    protected $actions = null;

    /**
     * Returns an array of action_link's of all bulk actions available for this user.
     *
     * @param bool $flatlist whether to return a flat list (for easier searching) or a list with
     *     option groups that can be used to build a select element
     * @return array of action_link objects
     */
    public function get_actions(bool $flatlist = true): array {
        if ($this->actions === null) {
            $this->actions = $this->build_actions();
            $this->hasbulkactions = !empty($this->actions);
        }
        if ($flatlist) {
            return array_reduce($this->actions, fn($carry, $item) => $carry + $item, []);
        }
        return $this->actions;
    }

    /**
     * Builds the list of bulk user actions available for this user.
     *
     * @return array
     */
    protected function build_actions(): array {

        global $CFG;

        $canaccessbulkactions = has_any_capability(['moodle/user:update', 'moodle/user:delete'], context_system::instance());

        $syscontext = context_system::instance();
        $actions = [];
        if (has_capability('moodle/user:update', $syscontext)) {
            $actions['confirm'] = new action_link(
                new moodle_url('/admin/user/user_bulk_confirm.php'),
                get_string('confirm'));
        }
        if ($canaccessbulkactions && has_capability('moodle/site:readallmessages', $syscontext) && !empty($CFG->messaging)) {
            $actions['message'] = new action_link(
                new moodle_url('/admin/user/user_bulk_message.php'),
                get_string('messageselectadd'));
        }
        if (has_capability('moodle/user:delete', $syscontext)) {
            $actions['delete'] = new action_link(
                new moodle_url('/admin/user/user_bulk_delete.php'),
                get_string('delete'));
        }
        if ($canaccessbulkactions) {
            $actions['displayonpage'] = new action_link(
                new moodle_url('/admin/user/user_bulk_display.php'),
                get_string('displayonpage'));
        }

        if (has_capability('moodle/user:update', $syscontext)) {
            $actions['download'] = new action_link(
                new moodle_url('/admin/user/user_bulk_download.php'),
                get_string('download', 'admin'));
        }

        if (has_capability('moodle/user:update', $syscontext)) {
            $actions['forcepasswordchange'] = new action_link(
                new moodle_url('/admin/user/user_bulk_forcepasswordchange.php'),
                get_string('forcepasswordchange'));
        }
        if ($canaccessbulkactions && has_capability('moodle/cohort:assign', $syscontext)) {
            $actions['addtocohort'] = new action_link(
                new moodle_url('/admin/user/user_bulk_cohortadd.php'),
                get_string('bulkadd', 'core_cohort'));
        }

        // Collect all bulk user actions.
        $hook = new \core_user\hook\extend_bulk_user_actions();

        // Add actions from core.
        foreach ($actions as $identifier => $action) {
            $hook->add_action($identifier, $action);
        }

        // Add actions from the legacy callback 'bulk_user_actions'.
        $moreactions = get_plugins_with_function('bulk_user_actions', 'lib.php', true, true);
        foreach ($moreactions as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginactions = $pluginfunction();
                foreach ($pluginactions as $identifier => $action) {
                    $hook->add_action($identifier, $action);
                }
            }
        }

        // Any plugin can append user bulk actions to this list by implementing a hook callback.
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

        // This method may be called from 'Bulk actions' and 'Browse user list' pages. Some actions
        // may be irrelevant in one of the contexts and they can be excluded by specifying the
        // 'excludeactions' customdata.
        $excludeactions = $this->_customdata['excludeactions'] ?? [];
        foreach ($excludeactions as $excludeaction) {
            $hook->add_action($excludeaction, null);
        }
        return $hook->get_actions();
    }

    /**
     * Form definition
     */
    public function definition() {
        $mform =& $this->_form;

        // Most bulk actions perform a redirect on selection, so we shouldn't trigger formchange warnings (specifically because
        // the user must have _already_ changed the current form by selecting users to perform the action on).
        $mform->disable_form_change_checker();

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);

        // When 'passuserids' is specified in the customdata, the user ids are expected in the form
        // data rather than in the $SESSION->bulk_users .
        $passuserids = !empty($this->_customdata['passuserids']);
        $mform->addElement('hidden', 'passuserids', $passuserids);
        $mform->setType('passuserids', PARAM_BOOL);

        $mform->addElement('hidden', 'userids');
        $mform->setType('userids', PARAM_SEQUENCE);

        $actions = ['' => [0 => get_string('choose') . '...']];
        $bulkactions = $this->get_actions(false);
        foreach ($bulkactions as $category => $categoryactions) {
            $actions[$category] = array_map(fn($action) => $action->text, $categoryactions);
        }
        $objs = array();
        $objs[] = $selectel = $mform->createElement('selectgroups', 'action', get_string('userbulk', 'admin'), $actions);
        $selectel->setHiddenLabel(true);
        if (empty($this->_customdata['hidesubmit'])) {
            $objs[] =& $mform->createElement('submit', 'doaction', get_string('go'));
        }
        $mform->addElement('group', 'actionsgrp', get_string('withselectedusers'), $objs, ' ', false);
    }

    /**
     * Is there at least one available bulk action in this form
     *
     * @return bool
     */
    public function has_bulk_actions(): bool {
        return $this->hasbulkactions;
    }
}

/**
 * Bulk user form
 *
 * @copyright  Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_bulk_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {

        $mform =& $this->_form;
        $acount =& $this->_customdata['acount'];
        $scount =& $this->_customdata['scount'];
        $ausers =& $this->_customdata['ausers'];
        $susers =& $this->_customdata['susers'];
        $total  =& $this->_customdata['total'];

        $achoices = array();
        $schoices = array();

        if (is_array($ausers)) {
            if ($total == $acount) {
                $achoices[0] = get_string('allusers', 'bulkusers', $total);
            } else {
                $a = new stdClass();
                $a->total  = $total;
                $a->count = $acount;
                $achoices[0] = get_string('allfilteredusers', 'bulkusers', $a);
            }
            $achoices = $achoices + $ausers;

            if ($acount > MAX_BULK_USERS) {
                $achoices[-1] = '...';
            }

        } else {
            $achoices[-1] = get_string('nofilteredusers', 'bulkusers', $total);
        }

        if (is_array($susers)) {
            $a = new stdClass();
            $a->total  = $total;
            $a->count = $scount;
            $schoices[0] = get_string('allselectedusers', 'bulkusers', $a);
            $schoices = $schoices + $susers;

            if ($scount > MAX_BULK_USERS) {
                $schoices[-1] = '...';
            }

        } else {
            $schoices[-1] = get_string('noselectedusers', 'bulkusers');
        }

        $mform->addElement('header', 'users', get_string('usersinlist', 'bulkusers'));

        $objs = array();
        $objs[0] =& $mform->createElement('select', 'ausers', get_string('available', 'bulkusers'), $achoices, 'size="15"');
        $objs[0]->setMultiple(true);
        $objs[1] =& $mform->createElement('select', 'susers', get_string('selected', 'bulkusers'), $schoices, 'size="15"');
        $objs[1]->setMultiple(true);

        $grp =& $mform->addElement('group', 'usersgrp', get_string('users', 'bulkusers'), $objs, ' ', false);
        $mform->addHelpButton('usersgrp', 'users', 'bulkusers');

        $mform->addElement('static', 'comment');

        $objs = array();
        $objs[] =& $mform->createElement('submit', 'addsel', get_string('addsel', 'bulkusers'));
        $objs[] =& $mform->createElement('submit', 'removesel', get_string('removesel', 'bulkusers'));
        $grp =& $mform->addElement('group', 'buttonsgrp', get_string('selectedlist', 'bulkusers'), $objs, null, false);
        $mform->addHelpButton('buttonsgrp', 'selectedlist', 'bulkusers');
        $objs = array();
        $objs[] =& $mform->createElement('submit', 'addall', get_string('addall', 'bulkusers'));
        $objs[] =& $mform->createElement('submit', 'removeall', get_string('removeall', 'bulkusers'));
        $grp =& $mform->addElement('group', 'buttonsgrp2', '', $objs, null, false);

        $renderer =& $mform->defaultRenderer();
        $template = '<label class="qflabel" style="vertical-align:top">{label}</label> {element}';
        $renderer->setGroupElementTemplate($template, 'usersgrp');
    }
}
