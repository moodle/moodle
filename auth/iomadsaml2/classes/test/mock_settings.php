<?php
// This file is part of IOMAD SAML2 Authentication Plugin for Moodle
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
namespace auth_iomadsaml2\test;

use admin_setting;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../lib/adminlib.php');

/**
 * Create a mock admin setting
 *
 * @package     auth_iomadsaml2
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mock_settings {
    /** @var admin_setting[] */
    public $fields = [];

    /**
     * Add an admin setting
     *
     * @param \admin_setting $setting
     */
    public function add(admin_setting $setting) {
        $this->fields[$setting->get_id()] = $setting;
    }
}
