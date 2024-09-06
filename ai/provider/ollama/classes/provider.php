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

namespace aiprovider_ollama;

use core_ai\aiactions;
use core_ai\rate_limiter;
use Psr\Http\Message\RequestInterface;

/**
 * Class provider.
 *
 * @package    aiprovider_ollama
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider extends \core_ai\provider {
    /** @var bool Is basic authentication enabled. */
    private bool $basicauthenabled;

    /** @var string The basic auth username. */
    private string $username;

    /** @var string The basic auth password. */
    private string $password;

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
        // Basic auth enabled.
        $this->basicauthenabled = get_config('aiprovider_ollama', 'enablebasicauth');
        // Get basic auth username from config.
        $this->username = get_config('aiprovider_ollama', 'username');
        // Get basic auth password from config.
        $this->password = get_config('aiprovider_ollama', 'password');
        // Get global rate limit from config.
        $this->enableglobalratelimit = get_config('aiprovider_ollama', 'enableglobalratelimit');
        $this->globalratelimit = get_config('aiprovider_ollama', 'globalratelimit');
        // Get user rate limit from config.
        $this->enableuserratelimit = get_config('aiprovider_ollama', 'enableuserratelimit');
        $this->userratelimit = get_config('aiprovider_ollama', 'userratelimit');
    }

    /**
     * Get the list of actions that this provider supports.
     *
     * @return array An array of action class names.
     */
    public function get_action_list(): array {
        return [
            \core_ai\aiactions\generate_text::class,
            \core_ai\aiactions\summarise_text::class,
        ];
    }


    /**
     * Update a request to add any headers required by the provider.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     */
    public function add_authentication_headers(RequestInterface $request): RequestInterface {
        if (!$this->basicauthenabled) {
            return $request;
        } else {
            // Add the Authorization header for basic auth
            $authHeader = 'Basic ' . base64_encode($this->username . ':' . $this->password);
            return $request
                ->withAddedHeader('Authorization', $authHeader);
        }
    }

    /**
     * Check if the request is allowed by the rate limiter.
     *
     * @param aiactions\base $action The action to check.
     * @return array|bool True on success, array of error details on failure.
     */
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
        $actionname =  substr($action, (strrpos($action, '\\') + 1));
        $settings = [];
        if ($actionname === 'generate_text' || $actionname === 'summarise_text') {
            // Add the model setting.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_ollama/action_{$actionname}_model",
                new \lang_string("action:{$actionname}:model", 'aiprovider_ollama'),
                new \lang_string("action:{$actionname}:model_desc", 'aiprovider_ollama'),
                'llama3.1:8b',
                PARAM_TEXT,
            );
            // Add system instruction settings.
            $settings[] = new \admin_setting_configtextarea(
                "aiprovider_ollama/action_{$actionname}_systeminstruction",
                new \lang_string("action:{$actionname}:systeminstruction", 'aiprovider_ollama'),
                new \lang_string("action:{$actionname}:systeminstruction_desc", 'aiprovider_ollama'),
                $action::get_system_instruction(),
                PARAM_TEXT
            );
        }

        return $settings;
    }
}
