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
use core_admin\reportbuilder\local\systemreports\oauth2_server_clients;
use Psr\Http\Message\ResponseInterface;

/**
 * Class client_management.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Gehoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\router\route(
    title: 'OAuth2 Client Management',
    path: '/oauth2server/clients',
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

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($OUTPUT->heading(get_string('oauth2server_clients', 'admin'), 2));

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
