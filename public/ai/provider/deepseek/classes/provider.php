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

namespace aiprovider_deepseek;

use core_ai\form\action_settings_form;
use Psr\Http\Message\RequestInterface;

/**
 * Class provider.
 *
 * @package    aiprovider_deepseek
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider extends \core_ai\provider {
    #[\Override]
    public static function get_action_list(): array {
        return [
            \core_ai\aiactions\generate_text::class,
            \core_ai\aiactions\summarise_text::class,
            \core_ai\aiactions\explain_text::class,
        ];
    }

    #[\Override]
    public static function get_action_settings(
        string $action,
        array $customdata = [],
    ): action_settings_form|bool {
        $actionname = substr($action, (strrpos($action, '\\') + 1));
        $customdata['actionname'] = $actionname;
        $customdata['action'] = $action;
        $customdata['providername'] = 'aiprovider_deepseek';
        if ($actionname === 'generate_text' || $actionname === 'summarise_text' || $actionname === 'explain_text') {
            return new form\action_generate_text_form(customdata: $customdata);
        }

        return false;
    }

    #[\Override]
    public function add_authentication_headers(RequestInterface $request): RequestInterface {
        return $request->withAddedHeader('Authorization', "Bearer {$this->config['apikey']}");
    }

    #[\Override]
    public static function get_action_setting_defaults(string $action): array {
        $actionname = substr($action, (strrpos($action, '\\') + 1));
        $customdata = [
            'actionname' => $actionname,
            'action' => $action,
            'providername' => 'aiprovider_deepseek',
        ];
        if ($actionname === 'generate_text' || $actionname === 'summarise_text' || $actionname === 'explain_text') {
            $mform = new form\action_generate_text_form(customdata: $customdata);
            return $mform->get_defaults();
        }

        return [];
    }

    #[\Override]
    public function is_provider_configured(): bool {
        return !empty($this->config['apikey']);
    }
}
