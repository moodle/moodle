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

namespace aiprovider_awsbedrock;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use core_ai\form\action_settings_form;

/**
 * Class provider.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider extends \core_ai\provider {
    #[\Override]
    public static function get_action_list(): array {
        return [
            \core_ai\aiactions\generate_text::class,
            \core_ai\aiactions\generate_image::class,
            \core_ai\aiactions\summarise_text::class,
            \core_ai\aiactions\explain_text::class,
        ];
    }

    /**
     * Create the Bedrock API client.
     *
     * @param string $region The AWS region the model is hosted in
     * @param string $version The version of the webservice to utilize.
     * @return BedrockRuntimeClient The client used to make requests.
     */
    public function create_bedrock_client(
        string $region,
        string $version = 'latest'
    ): BedrockRuntimeClient {
        $factory = \core\di::get(bedrock_client_factory::class);
        return $factory->create_client(
            region: $region,
            key: $this->config['apikey'],
            secret: $this->config['apisecret'],
            version: $version
        );
    }

    #[\Override]
    public static function get_action_settings(
        string $action,
        array $customdata = [],
    ): action_settings_form|bool {
        $actionname = substr($action, (strrpos($action, '\\') + 1));
        $customdata['actionname'] = $actionname;
        $customdata['action'] = $action;
        $customdata['providername'] = 'aiprovider_awsbedrock';
        if ($actionname === 'generate_text' || $actionname === 'summarise_text' || $actionname === 'explain_text') {
            return new form\action_generate_text_form(customdata: $customdata);
        } else if ($actionname === 'generate_image') {
            return new form\action_generate_image_form(customdata: $customdata);
        }

        return false;
    }

    #[\Override]
    public static function get_action_setting_defaults(string $action): array {
        $actionname = substr($action, (strrpos($action, '\\') + 1));
        $customdata = [
            'actionname' => $actionname,
            'action' => $action,
            'providername' => 'aiprovider_awsbedrock',
        ];
        if ($actionname === 'generate_text' || $actionname === 'summarise_text' || $actionname === 'explain_text') {
            $mform = new form\action_generate_text_form(customdata: $customdata);
            return $mform->get_defaults();
        } else if ($actionname === 'generate_image') {
            $mform = new form\action_generate_image_form(customdata: $customdata);
            return $mform->get_defaults();
        }

        return [];
    }

    #[\Override]
    public function is_provider_configured(): bool {
        return !empty($this->config['apikey']) && !empty($this->config['apisecret']);
    }
}
