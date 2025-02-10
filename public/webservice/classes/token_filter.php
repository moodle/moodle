<?php
// This file is part of Moodle - https://moodle.org/
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
 * Provides the {@see core_webservice\token_filter} class.
 *
 * @package     core_webservice
 * @copyright   2020 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_webservice;

use moodleform;

/**
 * Form allowing to filter displayed tokens.
 *
 * @copyright 2020 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @deprecated since 4.5 MDL-79496. Table replaced with a report builder system report.
 * @todo MDL-79909 This will be deleted in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: null,
    since: '4.5',
    reason: 'Filters replaced with a report builder system report',
    mdl: 'MDL-79496',
)]
class token_filter extends moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;
        $presetdata = $this->_customdata;

        $mform->addElement('header', 'tokenfilter', get_string('tokenfilter', 'webservice'));

        if (empty($presetdata->token) && empty($presetdata->users) && empty($presetdata->services)) {
            $mform->setExpanded('tokenfilter', false);
        } else {
            $mform->setExpanded('tokenfilter', true);
        }

        // Token name.
        $mform->addElement('text', 'name', get_string('tokenname', 'core_webservice'), ['size' => 32]);
        $mform->setType('name', PARAM_TEXT);

        // User selector.
        $attributes = [
            'multiple' => true,
            'ajax' => 'core_user/form_user_selector',
            'valuehtmlcallback' => function($userid) {
                global $DB, $OUTPUT;

                $context = \context_system::instance();
                $fields = \core_user\fields::for_name()->with_identity($context, false);
                $record = \core_user::get_user($userid, 'id' . $fields->get_sql()->selects, MUST_EXIST);

                $user = (object)[
                    'id' => $record->id,
                    'fullname' => fullname($record, has_capability('moodle/site:viewfullnames', $context)),
                    'extrafields' => [],
                ];

                foreach ($fields->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $extrafield) {
                    $user->extrafields[] = (object)[
                        'name' => $extrafield,
                        'value' => s($record->$extrafield)
                    ];
                }

                return $OUTPUT->render_from_template('core_user/form_user_selector_suggestion', $user);
            },
        ];
        $mform->addElement('autocomplete', 'users', get_string('user'), [], $attributes);

        // Service selector.
        $options = $DB->get_records_menu('external_services', null, 'name ASC', 'id, name');
        $attributes = [
            'multiple' => true,
        ];
        $mform->addElement('autocomplete', 'services', get_string('service', 'webservice'), $options, $attributes);

        // Action buttons.
        $mform->addGroup([
            $mform->createElement('submit', 'submitbutton', get_string('tokenfiltersubmit', 'core_webservice')),
            $mform->createElement('submit', 'resetbutton', get_string('tokenfilterreset', 'core_webservice'), [], false),
        ], 'actionbuttons', '', ' ', false);
    }
}
