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

namespace core_ai;

use core_ai\form\action_settings_form;
use Psr\Http\Message\RequestInterface;
use Spatie\Cloneable\Cloneable;

/**
 * Class provider.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class provider {
    use Cloneable;

    /** @var string $provider The provider used to make this instance */
    public readonly string $provider;

    /** @var array The configuration for this instance. */
    public readonly array $config;

    /** @var array The action specific settings for this instance. */
    public readonly array $actionconfig;

    /**
     * Create a new provider.
     *
     * @param bool $enabled Whether the gateway is enabled
     * @param string $name The name of the provider config.
     * @param string $config The configuration for this instance.
     * @param string $actionconfig The action specific settings for this instance.
     * @param int|null $id The id of the provider in the database.
     */
    public function __construct(
        /** @var bool Whether the gateway is enabled */
        public readonly bool $enabled,
        /** @var string The name of the provider config. */
        public string $name,
        string $config,
        string $actionconfig = '',
        /** @var null|int The ID of the provider in the database, or null if it has not been persisted yet. */
        public readonly ?int $id = null,
    ) {
        $this->provider = strstr(get_class($this), '\\', true);
        $this->config = json_decode($config, true);
        if ($actionconfig == '') {
            $this->actionconfig = static::initialise_action_settings();
        } else {
            $this->actionconfig = json_decode($actionconfig, true);
        }
    }

    /**
     * Get the actions that this provider supports.
     *
     * Returns an array of action class names.
     *
     * @return array An array of action class names.
     */
    abstract public static function get_action_list(): array;

    /**
     * Initialise the action settings array.
     *
     * @return array The initialised action settings.
     */
    public static function initialise_action_settings(): array {
        $actions = static::get_action_list();
        $actionconfig = [];
        foreach ($actions as $action) {
            $actionconfig[$action] = [
                'enabled' => true,
                'settings' => static::get_action_setting_defaults($action),
            ];
        }
        return $actionconfig;
    }

    /**
     * Given an action class name, return an array of sub actions
     * that this provider supports.
     *
     * @param string $classname The action class name.
     * @return array An array of supported sub actions.
     */
    public function get_sub_actions(string $classname): array {
        return [];
    }

    /**
     * Get the name of the provider.
     *
     * @return string The name of the provider.
     */
    public function get_name(): string {
        return \core\component::get_component_from_classname(get_class($this));
    }

    /**
     * Get any action settings for this provider.
     *
     * @param string $action The action class name.
     * @param array $customdata The customdata for the form.
     * @return action_settings_form|bool The settings form for this action or false in no settings.
     */
    public static function get_action_settings(
        string $action,
        array $customdata = [],
    ): action_settings_form|bool {
        return false;
    }

    /**
     * Get the default settings for an action.
     *
     * @param string $action The action class name.
     * @return array The default settings for the action.
     */
    public static function get_action_setting_defaults(string $action): array {
        return [];
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
        if (isset($this->config['enableuserratelimit']) && $this->config['enableuserratelimit']) {
            if (!$ratelimiter->check_user_rate_limit(
                component: $component,
                ratelimit: $this->config['userratelimit'],
                userid: $action->get_configuration('userid')
            )) {
                $errorhandler = new \core_ai\error\ratelimit(get_string('error:429:internaluser', 'core_ai'));
                return $errorhandler->get_error_details();
            }
        }

        // Check the global rate limit.
        if (isset($this->config['enableglobalratelimit']) && $this->config['enableglobalratelimit']) {
            if (!$ratelimiter->check_global_rate_limit(
                component: $component,
                ratelimit: $this->config['globalratelimit']
            )) {
                $errorhandler = new \core_ai\error\ratelimit(get_string('error:429:internalsitewide', 'core_ai'));
                return $errorhandler->get_error_details();
            }
        }

        return true;
    }

    /**
     * Check if a provider has the minimal configuration to work.
     *
     * @return bool Return true if configured.
     */
    public function is_provider_configured(): bool {
        return false;
    }

    /**
     * Update a request to add any headers required by the provider (if needed).
     * AI providers will need to override this method to add their own headers.
     *
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function add_authentication_headers(RequestInterface $request): RequestInterface {
        return $request;
    }

    /**
     * Generate a user id.
     *
     * This is a hash of the site id and user id,
     * this means we can determine who made the request
     * but don't pass any personal data to the AI provider.
     *
     * @param string $userid The user id.
     * @return string The generated user id.
     */
    public function generate_userid(string $userid): string {
        global $CFG;
        return hash('sha256', $CFG->siteidentifier . $userid);
    }

    /**
     * Convert this object to a stdClass, suitable for saving to the database.
     *
     * @return \stdClass
     */
    public function to_record(): \stdClass {
        return (object) [
            'id' => $this->id,
            'name' => $this->name,
            'provider' => get_class($this),
            'enabled' => $this->enabled,
            'config' => json_encode($this->config),
            'actionconfig' => json_encode($this->actionconfig),
        ];
    }
}
