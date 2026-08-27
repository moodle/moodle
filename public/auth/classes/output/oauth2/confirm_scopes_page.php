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

namespace core_auth\output\oauth2;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use core\router\scope\abstract_scope;

/**
 * Renderable for the Confirm Scopes page.
 *
 * @package    core_auth
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirm_scopes_page extends oauth2_page {
    /**
     * Create an instance of the form page.
     *
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client The client entity
     * @param array $grantedscopes The list of scopes the user has already granted
     * @param array $requestedscopes The list of scopes the client is requesting
     * @param \core\url $actionurl The URL the scope confirmation form should submit to
     * @param \stdClass $user The user entity
     */
    public function __construct(
        \League\OAuth2\Server\Entities\ClientEntityInterface $client,
        /** @var array The list of scopes the user has already granted */
        protected array $grantedscopes,
        /** @var array The list of scopes the client is requesting */
        protected array $requestedscopes,
        /** @var \core\url The URL the scope confirmation form should submit to */
        private \core\url $actionurl,
        /** @var \stdClass The user entity */
        protected \stdClass $user,
    ) {
        $this->client = $client;
    }

    #[\Override]
    public function export_for_template(\core\output\renderer_base $renderer): \stdClass {
        global $CFG, $PAGE, $SITE;

        $data = new \stdClass();
        $data->actionurl = $this->actionurl->out(false);
        $data->userinfo = $this->get_user_info($renderer);
        $data->client = $this->get_client_info();
        $data->sesskey = sesskey();

        // Maintenance banner. Matches core_auth\output\login::export_for_template().
        $maintenance = null;
        if ($CFG->maintenance_enabled == true) {
            $maintenance = !empty($CFG->maintenance_message)
                ? $CFG->maintenance_message
                : get_string('sitemaintenance', 'admin');
        }
        $data->maintenance = format_text($maintenance, FORMAT_MOODLE);

        // Language menu.
        $languagedata = new \core\output\language_menu($PAGE);
        $data->languagemenu = $languagedata->export_for_action_menu($renderer);

        // Site name and logo.
        $data->sitename = \format_string(
            $SITE->fullname,
            true,
            ['context' => \core\context\course::instance(SITEID), 'escape' => false],
        );
        $data->logourl = null;
        $logourl = $renderer->get_logo_url();
        if ($logourl) {
            $data->logourl = $logourl->out(false);
        }

        // Auth instructions, shown in the branding panel when the admin has configured them.
        $data->hasauthinstructions = !empty($CFG->auth_instructions);
        $data->authinstructions = null;
        if (!empty($CFG->auth_instructions)) {
            $data->authinstructions = format_text(
                $CFG->auth_instructions,
                FORMAT_MOODLE,
                ['context' => \core\context\system::instance()],
            );
        }

        $normalisescope = static function (ScopeEntityInterface&abstract_scope $scope): \stdClass {
            return (object) [
                'identifier' => $scope->getIdentifier(),
                'description' => $scope->get_description(),
                'summary' => $scope->get_summary(),
            ];
        };

        $data->requestedscopes = array_map(
            $normalisescope,
            // Filter out any missing scopes.
            array_filter(
                $this->requestedscopes,
                static fn($scope) => $scope instanceof ScopeEntityInterface && $scope instanceof abstract_scope,
            ),
        );

        $data->grantedscopes = array_map(
            $normalisescope,
            // Filter out any missing scopes.
            array_filter(
                $this->grantedscopes,
                static fn($scope) => $scope instanceof ScopeEntityInterface && $scope instanceof abstract_scope
            ),
        );
        $data->hasgrantedscopes = $data->grantedscopes !== [];

        return $data;
    }

    #[\Override]
    public function get_template_name(\core\output\renderer_base $renderer): string {
        return 'core/oauth2/confirm_scopes_page';
    }
}
