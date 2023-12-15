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

namespace core\local\guzzle;

use core\files\curl_security_helper_base;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Class to check request against curl security helper.
 *
 * @package   core
 * @copyright 2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_request {

    /** @var curl_security_helper_base The helper to use */
    protected $securityhelper;

    /** @var array The settings for the request */
    protected $settings;

    /**
     * Initial setup for the request.
     *
     * @param array $settings
     * @return callable
     */
    public static function setup(array $settings): callable {
        return static function (callable $handler) use ($settings): self {
            return new self($handler, $settings);
        };
    }

    /**
     * The following handler.
     *
     * @var callable(RequestInterface, array): PromiseInterface
     */
    private $nexthandler;

    /**
     * Check request constructor.
     *
     * @param callable $next The following handler
     * @param array $settings The settings of the request
     */
    public function __construct(callable $next, array $settings) {
        $this->nexthandler = $next;
        $this->settings = $settings;
    }

    /**
     * Set the security according to the settings.
     *
     * @param curl_security_helper_base $securityhelper The security helper to use
     * @return void
     */
    protected function set_security(curl_security_helper_base $securityhelper): void {
        $this->securityhelper = $securityhelper;
    }

    /**
     * Curl security setup.
     *
     * @param RequestInterface $request The request interface
     * @param array $options The options from the request
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface {
        global $USER;

        $fn = $this->nexthandler;
        $settings = $this->settings;

        if (!empty($settings['ignoresecurity'])) {
            return $fn($request, $options);
        }

        // Curl security setup. Allow injection of a security helper, but if not found, default to the core helper.
        if (isset($settings['securityhelper']) && $settings['securityhelper'] instanceof \core\files\curl_security_helper_base) {
            $this->set_security($settings['securityhelper']);
        } else {
            $this->set_security(new \core\files\curl_security_helper());
        }

        if ($this->securityhelper->url_is_blocked((string) $request->getUri())) {
            $msg = $this->securityhelper->get_blocked_url_string();
            debugging(
                sprintf('Blocked %s [user %d]', $msg, $USER->id),
                DEBUG_NONE
            );

            throw new RequestException($msg, $request);
        }

        return $fn($request, $options);
    }
}
