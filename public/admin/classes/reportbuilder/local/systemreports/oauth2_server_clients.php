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

namespace core_admin\reportbuilder\local\systemreports;

use core\oauth2\server\entity\client_entity;
use core\output\help_icon;
use core_admin\route\controller\oauth2\server\client_management;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core_reportbuilder\output\report_action;
use core_reportbuilder\system_report;
use lang_string;

/**
 * OAuth2 server clients system report class.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Gehoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class oauth2_server_clients extends system_report {
    /**
     * Initialise report parameters, table, columns and filters.
     */
    protected function initialise(): void {
        // Main DB table definition from oauth2_server_clients schema.
        $this->set_main_table('oauth2_server_clients', 'client');
        // Register entity name for columns and filters.
        $this->annotate_entity('client', new lang_string('oauth2server_client', 'admin'));
        // Create a custom action (button) right above the table that provides navigation to the 'Create client' page.
        $createclienturl = \core\router\util::get_path_for_callable([client_management::class, 'create_client']);
        $this->set_report_action(new report_action(
            get_string('oauth2server_clientcreate', 'admin'),
            [
                'href' => ($createclienturl->out(false)),
                'class' => 'btn btn-primary',
            ],
            'a',
        ));

        // Add columns and filters.
        $this->add_columns();
        $this->add_filters();
    }

    /**
     * Validates capability to view report.
     */
    protected function can_view(): bool {
        $composer = \core\di::get(\core\composer::class);
        // The 'league/oauth2-server' composer package needs to be installed and the user has to have a capability to
        // manage OAuth 2 clients.
        $iscomposerpackageinstalled = $composer->get_package_status('league/oauth2-server')->installed;
        $canmanageoauth2clients = has_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        return $iscomposerpackageinstalled && $canmanageoauth2clients;
    }

    /**
     * Add report columns.
     */
    protected function add_columns(): void {
        // Client Name.
        $now = \core\di::get(\core\clock::class)->time();
        $paramnow = \core_reportbuilder\local\helpers\database::generate_param_name();

        $this->add_column((new column(
            'name',
            new lang_string('name', 'core'),
            'client'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields(
                'client.name,
                client.status,
                client.isconfidential'
            )
            ->add_field(
                "(SELECT COUNT(*)
                        FROM {oauth2_server_client_secrets} client_secret
                       WHERE client_secret.clientidentifier = client.clientidentifier
                         AND client_secret.revoked = 0
                         AND client_secret.expirytime > :{$paramnow})",
                'activesecretcount',
                [$paramnow => $now],
            )
            ->set_is_sortable(true)
            ->add_callback(function ($value, \stdClass $row): string {
                $name = s($value);

                // For active confidential clients, show a warning next to the name if there is no active secret
                // associated with the client.
                if ($row->isconfidential && $row->status == client_entity::STATUS_ACTIVE && (int) $row->activesecretcount == 0) {
                    $icon = \html_writer::tag(
                        'i',
                        '',
                        [
                            'class' => 'fa fa-exclamation-triangle text-warning',
                            'aria-hidden' => 'true',
                        ]
                    );
                    $warning = \html_writer::tag(
                        'a',
                        $icon,
                        [
                            'class' => 'text-decoration-none ms-2',
                            'role' => 'button',
                            'aria-label' => get_string('oauth2server_viewwarningdetails', 'admin'),
                            'tabindex' => '0',
                            'data-bs-toggle' => 'popover',
                            'data-bs-trigger' => 'focus',
                            'data-bs-placement' => 'right',
                            'data-bs-content' => get_string('oauth2server_clientnoactivesecretwarning', 'admin'),
                        ]
                    );

                    $name .= $warning;
                }

                return $name;
            }));

        // Client identifier.
        $this->add_column((new column(
            'clientidentifier',
            new lang_string('oauth2server_clientidentifier', 'admin'),
            'client'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields('client.clientidentifier')
            ->set_is_sortable(false)
            ->add_callback(function ($value) {
                return \html_writer::tag('code', s($value));
            }));

        // Client type.
        $this->add_column((new column(
            'isconfidential',
            new lang_string('oauth2server_clienttype', 'admin'),
            'client'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields('client.isconfidential')
            ->set_is_sortable(true)
            ->add_callback(function ($value) {
                return $value
                    ? get_string('oauth2server_clienttypeconfidential', 'admin')
                    : get_string('oauth2server_clienttypepublic', 'admin');
            }));

        // Status.
        $this->add_column((new column(
            'status',
            new lang_string('status', 'core'),
            'client'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields('client.status')
            ->set_is_sortable(true)
            ->add_callback(function ($value) {
                $isactive = ((int) $value === client_entity::STATUS_ACTIVE);

                $clientstatus = $isactive
                    ? get_string('oauth2server_statusactive', 'admin')
                    : get_string('oauth2server_statusrevoked', 'admin');

                // Set the badge type based on status.
                $badgetype = $isactive ? 'success' : 'danger';

                return \html_writer::tag(
                    'span',
                    $clientstatus,
                    [
                        'class' => "badge bg-{$badgetype}-subtle text-{$badgetype}-emphasis",
                    ]
                );
            }));

        // Time Created.
        $this->add_column((new column(
            'timecreated',
            new lang_string('oauth2server_createdcolumn', 'admin'),
            'client'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields('client.timecreated')
            ->set_is_sortable(true)
            ->set_help_icon(new help_icon('oauth2server_createdcolumn', 'admin', \core_date::get_user_timezone()))
            ->add_callback(function ($value) {
                return $value ? userdate($value, get_string('strftimedatemonthtimeshort24', 'langconfig')) : '-';
            }));

        // Last Accessed.
        $this->add_column((new column(
            'lastaccessed',
            new lang_string('lastaccess', 'core'),
            'client'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields('client.lastaccessed')
            ->set_is_sortable(true)
            ->add_callback(function ($value) {
                return $value ? userdate($value, get_string('strftimedatemonthtimeshort24', 'langconfig')) : '-';
            }));

        // Custom Actions.
        $this->add_column((new column(
            'actions',
            new lang_string('actions', 'core'),
            'client'
        ))
            ->set_type(column::TYPE_TEXT)
            // Add all fields needed to build the action URLs.
            ->add_fields('client.id, client.name, client.status, client.isconfidential')
            ->set_is_sortable(false)
            ->add_callback(function ($value, \stdClass $row): string {
                $actions = [];

                // Edit link.
                $editurl = \core\router\util::get_path_for_callable(
                    [\core_admin\route\controller\oauth2\server\client_management::class, 'edit_client'],
                    ['client' => $row->id],
                );

                $actions[] = \html_writer::link($editurl, get_string('edit', 'moodle'));

                // Display the relevant actions when the client status is revoked.
                if ((int) $row->status === client_entity::STATUS_REVOKED) {
                    // Enable link.
                    $actions[] = \html_writer::tag(
                        'button',
                        get_string('enable', 'core'),
                        [
                            'class' => 'btn btn-link p-0',
                            'data-action' => 'client-enable',
                            'data-id' => $row->id,
                            'data-name' => $row->name,
                        ],
                    );

                    // Delete link.
                    $actions[] = \html_writer::tag(
                        'button',
                        get_string('delete', 'core'),
                        [
                            'class' => 'btn btn-link text-danger p-0',
                            'data-action' => 'client-delete',
                            'data-id' => $row->id,
                            'data-name' => $row->name,
                        ],
                    );
                }

                // Display the relevant actions when the client status is active.
                if ((int) $row->status === client_entity::STATUS_ACTIVE) {
                    if ($row->isconfidential) {
                        // Manage secrets link.
                        $editurl = \core\router\util::get_path_for_callable(
                            [\core_admin\route\controller\oauth2\server\client_management::class, 'manage_client_secrets'],
                            ['client' => $row->id],
                        );

                        $actions[] = \html_writer::link($editurl, get_string('oauth2server_managesecrets', 'admin'));
                    }

                    // Revoke link.
                    $actions[] = \html_writer::tag(
                        'button',
                        get_string('oauth2server_clientrevoke', 'admin'),
                        [
                            'class' => 'btn btn-link text-danger p-0',
                            'data-action' => 'client-revoke',
                            'data-id' => $row->id,
                            'data-name' => $row->name,
                        ],
                    );
                }

                $separator = \html_writer::span('', 'vr mx-2');
                return \html_writer::div(implode($separator, $actions), 'd-flex text-nowrap align-items-center');
            }));
    }

    /**
     * Add report filters.
     */
    protected function add_filters(): void {
        // Name filter.
        $this->add_filter((new filter(
            \core_reportbuilder\local\filters\text::class,
            'name',
            new lang_string('name', 'core'),
            'client',
            'client.name'
        ))->add_joins($this->get_joins()));

        // Type filter.
        $this->add_filter((new filter(
            \core_reportbuilder\local\filters\select::class,
            'type',
            new lang_string('oauth2server_clienttype', 'admin'),
            'client',
            'client.isconfidential'
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                0 => get_string('oauth2server_clienttypepublic', 'admin'),
                1 => get_string('oauth2server_clienttypeconfidential', 'admin'),
            ]));

        // Status filter.
        $this->add_filter((new filter(
            \core_reportbuilder\local\filters\select::class,
            'status',
            new lang_string('status', 'core'),
            'client',
            'client.status'
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                client_entity::STATUS_ACTIVE => get_string('oauth2server_statusactive', 'admin'),
                client_entity::STATUS_REVOKED => get_string('oauth2server_statusrevoked', 'admin'),
            ]));
    }
}
