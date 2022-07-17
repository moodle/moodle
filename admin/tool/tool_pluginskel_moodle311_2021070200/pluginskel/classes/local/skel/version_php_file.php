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
 * Provides tool_pluginskel\local\skel\version_php_file class.
 *
 * @package     tool_pluginskel
 * @subpackage  skel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\skel;

use tool_pluginskel\local\util\exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing the plugin's version.php file.
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class version_php_file extends php_internal_file {

    /** @var string[] Moodle versions - see https://docs.moodle.org/dev/Releases */
    protected static $moodleversions = [
        '2021051700' => '3.11',
        '2020110900' => '3.10',
        '2020061500' => '3.9',
        '2019111800' => '3.8',
        '2019052000' => '3.7',
        '2018120300' => '3.6',
        '2018051700' => '3.5',
        '2017111300' => '3.4',
        '2017051500' => '3.3',
        '2016120500' => '3.2',
        '2016052300' => '3.1',
        '2015111600' => '3.0',
        '2015051100' => '2.9',
        '2014111000' => '2.8',
        '2014051200' => '2.7',
        '2013111800' => '2.6',
        '2013051400' => '2.5',
        '2012120300' => '2.4',
        '2012062500' => '2.3',
        '2011120500' => '2.2',
    ];

    /**
     * Set the data to be eventually rendered.
     *
     * @param array $data
     */
    public function set_data(array $data) {

        parent::set_data($data);

        if (empty($this->data['version'])) {
            $this->data['version'] = date('Ymd').'00';
        } else {
            if (!preg_match('/^2[0-9]{3}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])[0-9]{2}$/', $this->data['version'])) {
                throw new exception('Invalid version number: '.$this->data['version']);
            }
        }

        if (!empty($this->data['dependencies'])) {
            $this->data['has_dependencies'] = true;
        }

        if (!empty($this->data['requires'])) {
            if (!preg_match('/^2[0-9]{3}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])[0-9]{2}$/', $this->data['requires'])) {
                $moodleversion = array_search($this->data['requires'], self::$moodleversions);
                if ($moodleversion === false) {
                    throw new exception('Unknown required Moodle version: '.$this->data['requires']);
                } else {
                    $this->data['requires'] = $moodleversion;
                }
            }
        }
    }

    /**
     * Returns a list of the variables needed to render the template.
     *
     * @param string $plugintype
     * @return string[]
     */
    public static function get_template_variables($plugintype = null) {

        $templatevars = array(
            array('name' => 'component', 'type' => 'text', 'required' => true),
            array('name' => 'release', 'type' => 'text', 'default' => '0.1.0'),
            array('name' => 'version', 'type' => 'int', 'default' => date('Ymd').'00'),
            array('name' => 'requires', 'type' => 'multiple-options', 'required' => true, 'values' => self::$moodleversions),
            array('name' => 'dependencies', 'type' => 'numeric-array', 'values' => array(
                  array('name' => 'plugin', 'type' => 'text'),
                  array('name' => 'version', 'type' => 'text'))),
        );

        $maturities = array(
            'MATURITY_ALPHA' => 'MATURITY_ALPHA',
            'MATURITY_BETA' => 'MATURITY_BETA',
            'MATURITY_RC' => 'MATURITY_RC',
            'MATURITY_STABLE' => 'MATURITY_STABLE'
        );

        $templatevars[] = array(
            'name' => 'maturity',
            'type' => 'multiple-options',
            'values' => $maturities,
            'default' => 'MATURITY_ALPHA',
        );

        return $templatevars;
    }
}
