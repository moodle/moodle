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

namespace aiprovider_openai;

use core_ai\aiactions;
use core_ai\rate_limiter;
use Psr\Http\Message\RequestInterface;

/**
 * Class provider.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider extends \core_ai\provider {
    /** @var string The openAI API key. */
    private string $apikey;

    /** @var string The organisation ID that goes with the key. */
    private string $orgid;

    /** @var bool Is global rate limiting for the API enabled. */
    private bool $enableglobalratelimit;

    /** @var int The global rate limit. */
    private int $globalratelimit;

    /** @var bool Is user rate limiting for the API enabled */
    private bool $enableuserratelimit;

    /** @var int The user rate limit. */
    private int $userratelimit;

    /**
     * Class constructor.
     */
    public function __construct() {
        // Get api key from config.
        $this->apikey = get_config('aiprovider_openai', 'apikey');
        // Get api org id from config.
        $this->orgid = get_config('aiprovider_openai', 'orgid');
        // Get global rate limit from config.
        $this->enableglobalratelimit = get_config('aiprovider_openai', 'enableglobalratelimit');
        $this->globalratelimit = get_config('aiprovider_openai', 'globalratelimit');
        // Get user rate limit from config.
        $this->enableuserratelimit = get_config('aiprovider_openai', 'enableuserratelimit');
        $this->userratelimit = get_config('aiprovider_openai', 'userratelimit');
    }

    /**
     * Get the list of actions that this provider supports.
     *
     * @return array An array of action class names.
     */
    public function get_action_list(): array {
        return [
            \core_ai\aiactions\generate_text::class,
            \core_ai\aiactions\generate_image::class,
            \core_ai\aiactions\summarise_text::class,
        ];
    }

    /**
     * Generate a user id.
     *
     * This is a hash of the site id and user id,
     * this means we can determine who made the request
     * but don't pass any personal data to OpenAI.
     *
     * @param string $userid The user id.
     * @return string The generated user id.
     */
    public function generate_userid(string $userid): string {
        global $CFG;
        return hash('sha256', $CFG->siteidentifier . $userid);
    }

    /**
     * Update a request to add any headers required by the provider.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    public function add_authentication_headers(RequestInterface $request): RequestInterface {
        return $request
            ->withAddedHeader('Authorization', "Bearer {$this->apikey}")
            ->withAddedHeader('OpenAI-Organization', $this->orgid);
    }

    #[\Override]
    public function is_request_allowed(aiactions\base $action): array|bool {
        $ratelimiter = \core\di::get(rate_limiter::class);
        $component = \core\component::get_component_from_classname(get_class($this));

        // Check the user rate limit.
        if ($this->enableuserratelimit) {
            if (!$ratelimiter->check_user_rate_limit(
                component: $component,
                ratelimit: $this->userratelimit,
                userid: $action->get_configuration('userid')
            )) {
                return [
                    'success' => false,
                    'errorcode' => 429,
                    'errormessage' => 'User rate limit exceeded',
                ];
            }
        }

        // Check the global rate limit.
        if ($this->enableglobalratelimit) {
            if (!$ratelimiter->check_global_rate_limit(
                component: $component,
                ratelimit: $this->globalratelimit
            )) {
                return [
                    'success' => false,
                    'errorcode' => 429,
                    'errormessage' => 'Global rate limit exceeded',
                ];
            }
        }

        return true;
    }

    /**
     * Get any action settings for this provider.
     *
     * @param string $action The action class name.
     * @param \admin_root $ADMIN The admin root object.
     * @param string $section The section name.
     * @param bool $hassiteconfig Whether the current user has moodle/site:config capability.
     * @return array An array of settings.
     */
    public function get_action_settings(
        string $action,
        \admin_root $ADMIN,
        string $section,
        bool $hassiteconfig
    ): array {
        $actionname = substr($action, (strrpos($action, '\\') + 1));
        $settings = [];
        if ($actionname === 'generate_text' || $actionname === 'summarise_text') {
            // Add the model setting.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_openai/action_{$actionname}_model",
                new \lang_string("action:{$actionname}:model", 'aiprovider_openai'),
                new \lang_string("action:{$actionname}:model_desc", 'aiprovider_openai'),
                'gpt-4o',
                PARAM_TEXT,
            );
            // Add API endpoint.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_openai/action_{$actionname}_endpoint",
                new \lang_string("action:{$actionname}:endpoint", 'aiprovider_openai'),
                '',
                'https://api.openai.com/v1/chat/completions',
                PARAM_URL,
            );
            // Add system instruction settings.
            $settings[] = new \admin_setting_configtextarea(
                "aiprovider_openai/action_{$actionname}_systeminstruction",
                new \lang_string("action:{$actionname}:systeminstruction", 'aiprovider_openai'),
                new \lang_string("action:{$actionname}:systeminstruction_desc", 'aiprovider_openai'),
                $action::get_system_instruction(),
                PARAM_TEXT
            );
        } else if ($actionname === 'generate_image') {
            // Add the model setting.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_openai/action_{$actionname}_model",
                new \lang_string("action:{$actionname}:model", 'aiprovider_openai'),
                new \lang_string("action:{$actionname}:model_desc", 'aiprovider_openai'),
                'dall-e-3',
                PARAM_TEXT,
            );
            // Add API endpoint.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_openai/action_{$actionname}_endpoint",
                new \lang_string("action:{$actionname}:endpoint", 'aiprovider_openai'),
                '',
                'https://api.openai.com/v1/images/generations',
                PARAM_URL,
            );
        }

        return $settings;
    }

    /**
     * Check this provider has the minimal configuration to work.
     *
     * @return bool Return true if configured.
     */
    public function is_provider_configured(): bool {
        return !empty($this->apikey);
    }
}
