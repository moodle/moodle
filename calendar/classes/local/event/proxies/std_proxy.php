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

/**
 * std_proxy class.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\proxies;

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\interfaces\proxy_interface;
use core_calendar\local\event\exceptions\member_does_not_exist_exception;

/**
 * stdClass proxy.
 *
 * This class is intended to proxy things like user, group, etc 'classes'
 * It will only run the callback to load the object from the DB when necessary.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class std_proxy implements proxy_interface {
    /**
     * @var int $id The ID of the database record.
     */
    protected $id;

    /**
     * @var \stdClass $class The class we are proxying.
     */
    protected $class;

    /**
     * @var callable $callback Callback to run which will load the class to proxy.
     */
    protected $callback;

    /**
     * Constructor.
     *
     * @param int      $id       The ID of the record in the database.
     * @param callable $callback Callback to load the class.
     */
    public function __construct($id, callable $callback) {
        $this->id = $id;
        $this->callback = $callback;
    }

    public function get($member) {
        if (!property_exists($this->get_proxied_instance(), $member)) {
            throw new member_does_not_exist_exception(sprintf('Member %s does not exist', $member));
        }
        return $this->get_proxied_instance()->{$member};
    }

    public function set($member, $value) {
        if (!property_exists($this->get_proxied_instance(), $member)) {
            throw new member_does_not_exist_exception(sprintf('Member %s does not exist', $member));
        }

        $this->get_proxied_instance()->{$member} = $value;
    }

    public function get_proxied_instance() {
        if ($this->class) {
            return $this->class;
        } else {
            $callback = $this->callback;
            return $callback($this->id);
        }
    }
}
