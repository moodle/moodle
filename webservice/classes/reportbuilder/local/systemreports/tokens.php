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

declare(strict_types=1);

namespace core_webservice\reportbuilder\local\systemreports;

use context_system;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\{action, column};
use core_reportbuilder\system_report;
use core_webservice\reportbuilder\local\entities\{token, service};
use lang_string;
use moodle_url;
use pix_icon;

/**
 * Tokens system report
 *
 * @package    core_webservice
 * @copyright  2023 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tokens extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        global $USER;

        $entitytoken = new token();
        $entitytokenalias = $entitytoken->get_table_alias('external_tokens');

        $this->set_main_table('external_tokens', $entitytokenalias);
        $this->add_entity($entitytoken);

        $entityservice = new service();
        $entityservicealias = $entityservice->get_table_alias('external_services');
        $this->add_entity($entityservice->add_join(
            "LEFT JOIN {external_services} {$entityservicealias}
                    ON {$entityservicealias}.id = {$entitytokenalias}.externalserviceid"
        ));

        $entityuser = new user();
        $entityuseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser->add_join(
            "LEFT JOIN {user} {$entityuseralias} ON {$entityuseralias}.id = {$entitytokenalias}.userid"
        ));

        $entitycreator = new user();
        $entitycreator->set_entity_name('creator');
        $entitycreatoralias = $entitycreator->get_table_alias('user');
        $this->add_entity($entitycreator->add_join(
            "LEFT JOIN {user} {$entitycreatoralias} ON {$entitycreatoralias}.id = {$entitytokenalias}.creatorid"
        ));

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entitytokenalias}.id");

        // Only show tokens created by the current user for non-manager users.
        if (!has_capability('moodle/webservice:managealltokens', context_system::instance())) {
            $this->add_base_condition_simple("{$entitycreatoralias}.userid", $USER->id);
        }

        $this->add_columns($entityuseralias, $entityservicealias);
        $this->add_filters();
        $this->add_actions();

        $this->set_initial_sort_column('token:validuntil', SORT_ASC);
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     *
     * @param string $entityuseralias
     * @param string $entityservicealias
     */
    public function add_columns(string $entityuseralias, string $entityservicealias): void {
        $this->add_columns_from_entities([
            'token:name',
            'user:fullnamewithlink',
        ]);

        // Include all identity field columns.
        $identitycolumns = $this->get_entity('user')->get_identity_columns($this->get_context());
        foreach ($identitycolumns as $identitycolumn) {
            $this->add_column($identitycolumn);
        }

        $this->add_columns_from_entities([
            'service:name',
            'token:iprestriction',
            'token:validuntil',
            'token:lastaccess',
            'creator:fullnamewithlink',
        ]);

        $this->get_column('user:fullnamewithlink')
            ->set_title(new lang_string('user'));
        $this->get_column('service:name')
            ->set_title(new lang_string('service', 'core_webservice'));
        $this->get_column('creator:fullnamewithlink')
            ->set_title(new lang_string('tokencreator', 'core_webservice'))
            ->set_is_available(has_capability('moodle/webservice:managealltokens', context_system::instance()));

        $this->add_column((new column(
            'missingcapabilities',
            new lang_string('missingcaps', 'webservice'),
            'user'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$entityuseralias}.id", 'userid')
            ->add_fields(implode(', ', [
                "{$entityservicealias}.id",
                "{$entityservicealias}.shortname",
            ]))
            ->add_callback(static function(string $value, \stdClass $row): string {
                global $OUTPUT;
                $missingcapabilities = self::get_missing_capabilities((int)$row->userid, (int)$row->id, $row->shortname);
                if (empty($missingcapabilities)) {
                    return '';
                }
                $missingcapabilities = array_map(function($missingcapability) {
                    return (object)[
                        'name' => $missingcapability,
                        'link' => get_capability_docs_link((object)['name' => $missingcapability]),
                    ];
                }, $missingcapabilities);
                return $OUTPUT->render_from_template('core_webservice/missing_capabilities', [
                    'missingcapabilities' => $missingcapabilities,
                    'helpicon' => $OUTPUT->help_icon('missingcaps', 'webservice'),
                ]);
            })
        );
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'token:name',
            'user:fullname',
            'service:name',
            'token:validuntil',
        ];

        $this->add_filters_from_entities($filters);

        $this->get_filter('user:fullname')
            ->set_header(new lang_string('user'));
        $this->get_filter('service:name')
            ->set_header(new lang_string('service', 'core_webservice'));
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {

        // Action to delete token.
        $this->add_action((new action(
            new moodle_url('/admin/webservice/tokens.php', [
                'action' => 'delete',
                'tokenid' => ':id',
            ]),
            new pix_icon('t/delete', '', 'core'),
            ['class' => 'text-danger'],
            false,
            new lang_string('delete', 'core')
        )));
    }

    /**
     * Get the missing capabilities for a user
     *
     * @param int $userid
     * @param int $serviceid
     * @param string $serviceshortname
     * @return array
     */
    protected static function get_missing_capabilities(int $userid, int $serviceid, string $serviceshortname): array {
        global $CFG;
        require_once($CFG->dirroot . '/webservice/lib.php');

        $webservicemanager = new \webservice();
        $usermissingcaps = $webservicemanager->get_missing_capabilities_by_users([['id' => $userid]], $serviceid);
        if ($serviceshortname != MOODLE_OFFICIAL_MOBILE_SERVICE && !is_siteadmin($userid)) {
            return $usermissingcaps[$userid] ?? [];
        }
        return [];
    }
}
