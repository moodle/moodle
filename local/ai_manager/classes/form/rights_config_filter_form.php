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

namespace local_ai_manager\form;

use local_ai_manager\local\userinfo;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * A form for filtering for roles and whatever is being injected by a hook.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rights_config_filter_form extends \moodleform {

    /** @var string the filter identifier for the filter provided by the usertable_filter hook. */
    const FILTER_IDENTIFIER_HOOK_FILTER = 'hookfilter';

    /** @var string the filter identifier for the role filter. */
    const FILTER_IDENTIFIER_ROLE_FILTER = 'rolefilter';

    /**
     * Form definition.
     */
    public function definition() {
        $tenant = \core\di::get(\local_ai_manager\local\tenant::class);
        $mform = &$this->_form;
        $attributes = $mform->getAttributes();
        $attributes['class'] = $attributes['class'] . ' col-md-12';
        $mform->setAttributes($attributes);
        $hookfilteroptions = $this->_customdata['hookfilteroptions'];
        $hookfilterlabel = $this->_customdata['hookfilterlabel'];
        $mform->addElement('hidden', 'tenant', $tenant->get_identifier());
        $mform->setType('tenant', PARAM_ALPHANUM);

        $elementarray = [];
        if (!empty($hookfilteroptions)) {
            $filteroptionsautocomplete =
                    $mform->createElement('autocomplete', 'hookfilterids', '', $hookfilteroptions,
                            ['multiple' => true, 'noselectionstring' => $hookfilterlabel]);
            $filteroptionsautocomplete->setMultiple(true);
            $elementarray[] = $filteroptionsautocomplete;
        }

        $rolefilteroptions =
                [
                        userinfo::ROLE_BASIC => get_string(userinfo::get_role_as_string(userinfo::ROLE_BASIC), 'local_ai_manager'),
                        userinfo::ROLE_EXTENDED => get_string(userinfo::get_role_as_string(userinfo::ROLE_EXTENDED),
                                'local_ai_manager'),
                        userinfo::ROLE_UNLIMITED => get_string(userinfo::get_role_as_string(userinfo::ROLE_UNLIMITED),
                                'local_ai_manager'),
                ];
        $rolefilterautocomplete =
                $mform->createElement('autocomplete', 'rolefilterids', '', $rolefilteroptions,
                        ['multiple' => true, 'noselectionstring' => get_string('filterroles', 'local_ai_manager')]);
        $rolefilterautocomplete->setMultiple(true);
        $elementarray[] = $rolefilterautocomplete;

        $elementarray[] = $mform->createElement('submit', 'applyfilter', get_string('applyfilter', 'local_ai_manager'));
        $elementarray[] = $mform->createElement('submit', 'resetfilter', get_string('resetfilter', 'local_ai_manager'));
        $mform->addGroup($elementarray, 'elementarray', '', [' '], false);
    }

    /**
     * Store filterids and rolefilterids in session.
     *
     * @param string $filteridentifier the identifier of the filter
     * @param array $filterids the ids to store for the filter
     */
    public function store_filterids(string $filteridentifier, array $filterids) {
        global $SESSION;
        $key = 'local_ai_manager_' . $filteridentifier;

        // Ensure attribute exists for following lines.
        if (!isset($SESSION->{$key})) {
            $SESSION->{$key} = [];
        }

        if ($SESSION->{$key} !== $filterids) {
            $SESSION->{$key} = $filterids;
        }
    }

    /**
     * Get currently selected filters from user session.
     *
     * @param string $filteridentifier the identifier of the filter
     * @return array of the form [1,3] containing the ids for the filter
     */
    public function get_stored_filterids(string $filteridentifier): array {
        global $SESSION;
        $key = 'local_ai_manager_' . $filteridentifier;

        // Ensure attribute exists for following lines.
        if (!isset($SESSION->{$key})) {
            $SESSION->{$key} = [];
        }

        return $SESSION->{$key};
    }

}
