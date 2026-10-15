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
use core\oauth2\server\expiry_helper;
use core\output\help_icon;
use core_reportbuilder\local\report\column;
use core_reportbuilder\system_report;
use lang_string;

/**
 * OAuth2 client secrets system report class.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Gehoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class oauth2_server_client_secrets extends system_report {
    /**
     * Initialise report parameters, table, columns and filters.
     */
    protected function initialise(): void {
        // Main DB table definition from oauth2_server_client_secrets schema.
        $this->set_main_table('oauth2_server_client_secrets', 'client_secret');
        $clientidentifier = $this->get_parameter('clientidentifier', '', PARAM_TEXT);
        $this->add_base_condition_simple("client_secret.clientidentifier", $clientidentifier);
        $this->add_base_condition_simple("client_secret.revoked", 0);
        // Register entity name for columns and filters.
        $this->annotate_entity('client_secret', new lang_string('oauth2server_client', 'admin'));
        $clientjoinsql = "LEFT JOIN {oauth2_server_clients} client
                                 ON client.clientidentifier = client_secret.clientidentifier";
        $this->add_join($clientjoinsql);
        $this->add_columns();
        $this->set_default_no_results_notice(new lang_string('oauth2server_noactivesecrets', 'admin'));
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
        // Time Created.
        $this->add_column((new column(
            'timecreated',
            new lang_string('oauth2server_createdcolumn', 'admin'),
            'client_secret'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields('client_secret.timecreated')
            ->set_is_sortable(true)
            ->set_help_icon(new help_icon('oauth2server_createdcolumn', 'admin', \core_date::get_user_timezone()))
            ->add_callback(function ($value) {
                return $value ? userdate($value, get_string('strftimedatemonthtimeshort24', 'langconfig')) : '-';
            }));

        $now = \core\di::get(\core\clock::class)->time();
        // Set threshold to midnight of day 31 (+31 days) so the less-than-or-equal check inclusively covers all
        // secrets expiring through 23:59:59 on the 30th calendar day.
        $expirythreshold = \core\di::get(expiry_helper::class)->get_expiry_threshold(31)->getTimestamp();

        // Expiry time.
        $this->add_column((new column(
            'expirytime',
            new lang_string('oauth2server_expirycolumn', 'admin'),
            'client_secret'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields('client_secret.expirytime, client.status as clientstatus')
            ->set_is_sortable(true)
            ->set_help_icon(new help_icon('oauth2server_expirycolumn', 'admin', \core_date::get_user_timezone()))
            ->add_callback(function ($value, \stdClass $row) use ($now, $expirythreshold) {
                $expirytime = $row->expirytime;
                $isclientactive = (int) $row->clientstatus === client_entity::STATUS_ACTIVE;

                if (!$expirytime) {
                    return '-';
                }

                $expiry = userdate(
                    $expirytime,
                    get_string('strftimedatemonthtimeshort24', 'langconfig')
                );

                // If the client is active and the secret is expiring within 30 days, show a warning icon next to the
                // expiry date.
                if ($isclientactive && $expirytime > $now && $expirytime <= $expirythreshold) {
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
                            'data-bs-content' => get_string('oauth2server_secretexpirywarning', 'admin'),
                        ]
                    );

                    $expiry .= $warning;
                }

                return $expiry;
            }));

        // Status.
        $this->add_column((new column(
            'status',
            new lang_string('status', 'core'),
            'client_secret'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields('client_secret.expirytime, client.status as clientstatus')
            ->set_is_sortable(true)
            ->add_callback(function ($value, \stdClass $row) use ($now) {
                $isexpired = $row->expirytime <= $now;
                $isclientdisabled = (int) $row->clientstatus === client_entity::STATUS_DISABLED;

                if ($isexpired) { // Secret is expired.
                    $statusstring = get_string('oauth2server_statusexpired', 'admin');
                    $badgetype = 'danger';
                } else {
                    if ($isclientdisabled) { // Secret is inactive because the client is disabled.
                        $statusstring = get_string('oauth2server_statusinactive', 'admin');
                        $badgetype = 'warning';
                    } else { // Secret is active.
                        $statusstring = get_string('oauth2server_statusactive', 'admin');
                        $badgetype = 'success';
                    }
                }

                return \html_writer::tag(
                    'span',
                    $statusstring,
                    ['class' => "badge bg-{$badgetype}-subtle text-{$badgetype}-emphasis"],
                );
            }));

        // Last Accessed.
        $this->add_column((new column(
            'lastaccessed',
            new lang_string('lastaccess', 'core'),
            'client_secret'
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields('client_secret.lastaccessed')
            ->set_is_sortable(true)
            ->add_callback(function ($value) {
                return $value ? userdate($value, get_string('strftimedatemonthtimeshort24', 'langconfig')) : '-';
            }));

        // Custom Actions.
        $this->add_column((new column(
            'actions',
            new lang_string('actions', 'core'),
            'client_secret'
        ))
            ->set_type(column::TYPE_TEXT)
            // Add all fields needed to build the action URLs.
            ->add_fields('client_secret.id')
            ->set_is_sortable(false)
            ->add_callback(function ($value): string {
                // Revoke link.
                return \html_writer::tag(
                    'button',
                    get_string('oauth2server_revoke', 'admin'),
                    [
                        'class' => 'btn btn-link text-danger p-0',
                        'data-action' => 'client-secret-revoke',
                        'data-id' => $value,
                    ],
                );
            }));
    }
}
