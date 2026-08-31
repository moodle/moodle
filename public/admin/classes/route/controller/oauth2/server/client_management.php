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

namespace core_admin\route\controller\oauth2\server;

use core\router\require_login;
use core\oauth2\server\entity\client_entity;
use core_admin\reportbuilder\local\systemreports\oauth2_server_client_secrets;
use core_admin\reportbuilder\local\systemreports\oauth2_server_clients;
use Psr\Http\Message\ResponseInterface;

/**
 * Class client_management.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\router\route(
    title: 'OAuth2 Client Management',
    path: '/oauth2/server/clients',
)]
class client_management {
    /**
     * List the OAuth2 clients.
     *
     * @param ResponseInterface $response The response object
     * @return ResponseInterface The response object with the rendered client list
     */
    #[\core\router\route(
        path: '',
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function list_clients(
        ResponseInterface $response,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $this->setup_admin_page(null, \core\router\util::get_path_for_callable([self::class, 'list_clients']));

        $PAGE->requires->js_call_amd('core_admin/oauth2/server/client/actions/client_revoke', 'init');
        $PAGE->requires->js_call_amd('core_admin/oauth2/server/client/actions/client_reactivate', 'init');
        $PAGE->requires->js_call_amd('core_admin/oauth2/server/client/actions/client_delete', 'init');

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write(
            $OUTPUT->heading(
                get_string('oauth2server_clients', 'admin'),
                2,
                'fw-bold fs-3',
            ),
        );

        // Render the OAuth2 server clients table.
        $report = \core_reportbuilder\system_report_factory::create(
            oauth2_server_clients::class,
            \core\context\system::instance(),
        );

        $response->getBody()->write($report->output());
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Create client route.
     *
     * @param ResponseInterface $response The response object
     * @return ResponseInterface The response object
     */
    #[\core\router\route(
        path: '/create',
        method: ['GET', 'POST'],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function create_client(
        ResponseInterface $response,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $this->setup_admin_page(
            get_string('oauth2server_clientcreate', 'admin'),
            \core\router\util::get_path_for_callable([self::class, 'create_client']),
        );

        $PAGE->set_pagetype('admin-oauth2server-client-create');

        $mform = new \core_admin\form\oauth2\server\create_client_form();

        // Handle form cancellation.
        if ($mform->is_cancelled()) {
            redirect(\core\router\util::get_path_for_callable([self::class, 'list_clients']));
        }

        // Process the form data.
        if ($data = $mform->get_data()) {
            // Sanitize the redirect URIs by trimming whitespace and removing empty entries.
            $redirecturis = array_values(array_filter(array_map('trim', $data->redirecturi ?? [])));

            $ispublicclient = (int) $data->clienttype === client_entity::TYPE_PUBLIC;

            // Set the grant types based on the selected Primary flows.
            $granttypes = [];
            // If Authorization Code flow is selected or the client type is Public, add both Authorization Code and
            // Refresh Token grant types.
            if ($ispublicclient || !empty($data->flow_auth_code)) {
                $granttypes = [
                    client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                    client_entity::GRANT_TYPE_REFRESH_TOKEN,
                ];
            }
            // If Client Credentials flow is selected, add the Client Credentials grant type.
            if (!empty($data->flow_client_credentials)) {
                $granttypes[] = client_entity::GRANT_TYPE_CLIENT_CREDENTIALS;
            }

            $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);
            $cliententity = $clientmanager->create_client(
                $data->name,
                \core\context\system::instance(),
                $granttypes,
                $redirecturis,
                $data->description,
                (int) $data->clienttype === client_entity::TYPE_CONFIDENTIAL,
                $ispublicclient || !empty($data->enablepkce),
            );

            if ($cliententity->isConfidential()) {
                redirect(\core\router\util::get_path_for_callable(
                    [self::class, 'manage_client_secrets'],
                    ['client' => $cliententity->get_id()],
                ));
            }
            redirect(\core\router\util::get_path_for_callable([self::class, 'list_clients']));
        }

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write(
            $OUTPUT->heading(
                get_string('oauth2server_clientcreate', 'admin'),
                2,
                'fw-bold fs-3 mb-5',
            ),
        );
        $response->getBody()->write($mform->render());
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Edit client route.
     *
     * @param ResponseInterface $response The response object
     * @param \core\oauth2\server\entity\client_entity $cliententity The client entity
     * @return ResponseInterface The response object
     */
    #[\core\router\route(
        path: '/{client}/edit',
        pathtypes: [
            new \core_admin\route\parameters\oauth2\server\path_client(),
        ],
        method: ['GET', 'POST'],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function edit_client(
        ResponseInterface $response,
        \core\oauth2\server\entity\client_entity $cliententity,
    ): ResponseInterface {
        global $OUTPUT, $PAGE, $DB;

        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $this->setup_admin_page(
            get_string('oauth2server_clientedit', 'admin'),
            \core\router\util::get_path_for_callable([self::class, 'edit_client'], ['client' => $cliententity->get_id()]),
        );

        $PAGE->set_pagetype('admin-oauth2server-client-edit');

        $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);

        $mform = new \core_admin\form\oauth2\server\edit_client_form(null, ['cliententity' => $cliententity]);

        // Handle form cancellation.
        if ($mform->is_cancelled()) {
            redirect(\core\router\util::get_path_for_callable([self::class, 'list_clients']));
        }

        // Process the form data.
        if ($data = $mform->get_data()) {
            $transaction = $DB->start_delegated_transaction();

            $clientmanager->update_client(
                $cliententity->get_id(),
                [
                    'name' => $data->name,
                    'description' => $data->description,
                ],
            );

            // Sanitize the redirect URIs by trimming whitespace and removing empty entries.
            $redirecturis = array_values(array_filter(array_map('trim', $data->redirecturi ?? [])));
            // Fetch the current records from the database.
            $existingredirecturis = $clientmanager->get_redirect_uris($cliententity->get_id());

            // Find URIs that are in db, but missing from form and delete them.
            $redirecturistodelete = array_diff($existingredirecturis, $redirecturis);
            foreach ($redirecturistodelete as $redirecturi) {
                $clientmanager->remove_redirect_uri($cliententity->get_id(), $redirecturi);
            }

            // Find URIs that are in the form, but missing from the database and add them.
            $redirecturistoadd = array_diff($redirecturis, $existingredirecturis);
            foreach ($redirecturistoadd as $redirecturi) {
                $clientmanager->add_redirect_uri($cliententity->get_id(), $redirecturi);
            }

            $transaction->allow_commit();

            redirect(\core\router\util::get_path_for_callable([self::class, 'list_clients']));
        }

        $response->getBody()->write($OUTPUT->header());

        $isclientactive = $cliententity->get_status() === client_entity::STATUS_ACTIVE;

        $templatedata = [
            'title' => $cliententity->getName(),
            'clientidentifier' => $cliententity->getIdentifier(),
            'isactive' => $isclientactive,
            'isconfidential' => $cliententity->isConfidential(),
            'isauthcodesupported' => in_array(
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                $cliententity->get_grant_types(),
                true,
            ),
            'isclientcredentialssupported' => in_array(
                client_entity::GRANT_TYPE_CLIENT_CREDENTIALS,
                $cliententity->get_grant_types(),
                true,
            ),
            'backurl' => \core\router\util::get_path_for_callable([self::class, 'list_clients'])->out(),
            'editclientform' => $mform->render(),
        ];

        if ($cliententity->isConfidential()) {
            $clientactivesecrets = $clientmanager->get_secrets($cliententity->get_id());
            $templatedata['activesecretscount'] = count($clientactivesecrets);
            $templatedata['managesecretsurl'] = \core\router\util::get_path_for_callable(
                [self::class, 'manage_client_secrets'],
                ['client' => $cliententity->get_id()],
            )->out();
        }

        $editclienthtml = $OUTPUT->render_from_template(
            'core_admin/oauth2/server/edit_client',
            $templatedata,
        );

        // Render the page content.
        $response->getBody()->write($editclienthtml);
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Manage client secrets route.
     *
     * @param ResponseInterface $response The response object
     * @param \core\oauth2\server\entity\client_entity $cliententity The client entity
     * @return ResponseInterface The response object
     */
    #[\core\router\route(
        path: '/{client}/secrets',
        pathtypes: [
            new \core_admin\route\parameters\oauth2\server\path_client(),
        ],
        method: ['GET'],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function manage_client_secrets(
        ResponseInterface $response,
        \core\oauth2\server\entity\client_entity $cliententity,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        if (!$cliententity->isConfidential()) {
            throw new \moodle_exception('oauth2server_secretsnotavailablepublicclient', 'admin');
        }

        if ($cliententity->get_status() !== client_entity::STATUS_ACTIVE) {
            throw new \moodle_exception('oauth2server_secretsnotavailablerevokedclient', 'admin');
        }

        $this->setup_admin_page(
            get_string('oauth2server_managesecrets', 'admin'),
            \core\router\util::get_path_for_callable(
                [self::class, 'manage_client_secrets'],
                ['client' => $cliententity->get_id()],
            ),
        );

        $response->getBody()->write($OUTPUT->header());

        $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);
        // Secrets can be created if the client is active and the total number of currently active secrets is not
        // exceeding the defined limit.
        $isclientactive = $cliententity->get_status() === client_entity::STATUS_ACTIVE;
        $clientactivesecrets = $clientmanager->get_secrets($cliententity->get_id());
        $cancreatesecret = $isclientactive && (count($clientactivesecrets) < $clientmanager::MAX_ACTIVE_SECRETS);

        // Generate the OAuth2 client secrets table.
        $report = \core_reportbuilder\system_report_factory::create(
            oauth2_server_client_secrets::class,
            \core\context\system::instance(),
            parameters: [
                'clientidentifier' => $cliententity->getIdentifier(),
            ]
        );

        $managesecretshtml = $OUTPUT->render_from_template(
            'core_admin/oauth2/server/manage_client_secrets',
            [
                'id' => $cliententity->get_id(),
                'title' => $cliententity->getName(),
                'clientidentifier' => $cliententity->getIdentifier(),
                'isactive' => $isclientactive,
                'backurl' => \core\router\util::get_path_for_callable([self::class, 'list_clients'])->out(),
                'clientsecretstable' => $report->output(),
                'cancreatesecret' => $cancreatesecret,
                'maxsecretsnumber' => $clientmanager::MAX_ACTIVE_SECRETS,
            ],
        );

        // Render the page content.
        $response->getBody()->write($managesecretshtml);

        $PAGE->requires->js_call_amd(
            'core_admin/oauth2/server/client/client_secrets',
            'init',
            [$clientmanager::MAX_ACTIVE_SECRETS],
        );

        $PAGE->requires->js_call_amd('core_admin/oauth2/server/client/actions/client_secret_revoke', 'init');

        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Helper method to set up the admin page.
     *
     * @param string|null $title The title of the page. If not set, defaults to 'OAuth 2 clients'
     *                           ('oauth2server_clients', 'admin').
     * @param \moodle_url|null $url The URL of the page.
     */
    private function setup_admin_page(?string $title = null, ?\moodle_url $url = null): void {
        global $CFG, $PAGE;

        require_once("{$CFG->libdir}/adminlib.php");

        admin_externalpage_setup('oauth2serverclients', '', null, $url ?? '');
        $PAGE->set_context(\core\context\system::instance());
        $PAGE->set_pagelayout('admin');
        if ($title !== null) {
            $PAGE->set_title($title);
        } else {
            $PAGE->set_title(get_string('oauth2server_clients', 'admin'));
        }
    }
}
