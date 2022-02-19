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

namespace enrol_lti\local\ltiadvantage\entity;
use moodle_url;

/**
 * Class registration_url, representing a single dynamic registration URL.
 *
 * @package enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration_url extends moodle_url {

    /** @var string the address of the registration URL. */
    protected $address;

    /** @var string the random token used to secure this registration URL. */
    protected $token;

    /** @var int Unix time at which this registration URL is no longer valid. */
    protected $expirytime;

    /**
     * Constructor.
     *
     * @param int $expirytime the unix time after which the URL is deemed invalid.
     * @param string|null $token the unique token securing requests to the URL.
     * @throws \coding_exception if the token or expiry time is invalid.
     */
    public function __construct(int $expirytime, string $token = null) {
        global $CFG;
        if ($expirytime < 0) {
            throw new \coding_exception('Invalid registration_url expiry time. Must be greater than or equal to 0.');
        }
        $this->address = $CFG->wwwroot . '/enrol/lti/register.php';
        $this->expirytime = $expirytime;
        if (is_null($token)) {
            $bytes = random_bytes(30);
            $token = bin2hex($bytes);
        }
        $this->token = $token;

        parent::__construct($this->address, ['token' => $this->token], null);
    }

    /**
     * Get the expiry time of this registration_url instance.
     *
     * @return int the unix time of the expiry.
     */
    public function get_expiry_time(): int {
        return $this->expirytime;
    }
}
