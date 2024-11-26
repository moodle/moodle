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
 * The mod_hvp content user data.
 *
 * @package    mod_hvp
 * @since      Moodle 3.10
 * @copyright  2020 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp;

use curl as moodlecurl;

defined('MOODLE_INTERNAL') || die();

/**
 * Override Moodle's curl class to provide proper PUT support.
 *
 * @package     mod_hvp
 * @copyright   2020 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class curl extends moodlecurl {

    /**
     * @inheritdoc
     */
    public function post($url, $params = '', $options = array()) {
        $options['CURLOPT_POST'] = 1;
        $options['CURLOPT_POSTFIELDS'] = $params;
        return $this->request($url, $options);
    }

    /**
     * @inheritdoc
     */
    public function put($url, $params = '', $options = array()) {
        $options['CURLOPT_CUSTOMREQUEST'] = 'PUT';
        $options['CURLOPT_POSTFIELDS'] = $params;
        return $this->request($url, $options);
    }

}
