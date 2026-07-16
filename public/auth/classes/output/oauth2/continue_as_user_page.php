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

/**
 * Renderable for the Continue as existing user page.
 *
 * @package    core_auth
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class continue_as_user_page extends oauth2_page {
    use \core_auth\output\login_renderable_trait {
        export_for_template as shared_export_for_template;
    }

    /**
     * Create an instance of the form page.
     *
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client The client entity
     * @param \core\url $action
     * @param \core\url $logoutaction
     * @param \stdClass $user The user entity
     */
    public function __construct(
        \League\OAuth2\Server\Entities\ClientEntityInterface $client,
        \core\url $action,
        /** @var \core\url The logout url */
        private \core\url $logoutaction,
        /** @var \stdClass The user entity */
        protected \stdClass $user,
    ) {
        $this->client = $client;
        $this->set_action_url($action);
    }

    #[\Override]
    public function export_for_template(\core\output\renderer_base $renderer): \stdClass {
        $data = $this->shared_export_for_template($renderer);
        $data->logouturl = $this->logoutaction->out(false);
        $data->userinfo = $this->get_user_info($renderer);
        $data->client = $this->get_client_info();
        $data->sesskey = sesskey();

        return $data;
    }

    #[\Override]
    public function get_template_name(\core\output\renderer_base $renderer): string {
        return 'core/oauth2/continue_as_user_page';
    }
}
