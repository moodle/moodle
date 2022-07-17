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
 * Provides {@see \tool_pluginskel\local\skel\privacy_provider_file} class.
 *
 * @package     tool_pluginskel
 * @subpackage  skel
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\skel;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing the classes/privacy/provider.php file.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class privacy_provider_file extends php_single_file {

    /**
     * Set additional data to be available to the template.
     *
     * @param array $data
     */
    public function set_data(array $data) {

        parent::set_data($data);

        if (empty($data['privacy']['haspersonaldata'])) {
            $this->data['privacy']['_implementedinterfaces'] = ' \core_privacy\local\metadata\null_provider';
            $this->manager->add_lang_string('privacy:metadata', $data['name'].' does not store any personal data');
            return;
        }

        $this->set_data_implementedinterfaces($data);
        $this->set_data_metadbfields($data);
        $this->set_data_metasubsystems($data);
        $this->set_data_metaexternal($data);
        $this->set_data_userpreferences($data);
    }

    /**
     * Populates the helper recipe value /privacy/_implementedinterfaces
     *
     * @param array $data
     */
    protected function set_data_implementedinterfaces(array $data) {

        $implementedinterfaces = [
            '\core_privacy\local\metadata\provider',
            '\core_privacy\local\request\plugin\provider',
        ];

        if (!empty($data['privacy']['meta']['userpreferences'])) {
            $implementedinterfaces[] = '\core_privacy\local\request\user_preference_provider';
        }

        array_walk($implementedinterfaces, function(&$line) {
            $line = str_repeat(' ', 8).$line;
        });

        $implementedinterfaces[0] = "\n".$implementedinterfaces[0];

        $this->data['privacy']['_implementedinterfaces'] = implode(",\n", $implementedinterfaces);
    }

    /**
     * Populates the helper recipe value /privacy/_metadbfields
     *
     * @param array $data
     */
    protected function set_data_metadbfields(array $data) {

        if (!empty($data['privacy']['meta']['dbfields'])) {
            $this->data['privacy']['_metadbfields'] = [];

            foreach ($data['privacy']['meta']['dbfields'] as $dbtable => $dbfields) {
                // Convert the full table name to a string id, e.g. 'local_foo_bar' -> 'foobar'.
                $tabletostring = str_replace('_', '', preg_replace('/^'.$data['component'].'_/', '', $dbtable));
                $add = [
                    'name' => $dbtable,
                    'stringid' => 'privacy:metadata:db:'.$tabletostring,
                    'fields' => [],
                ];
                $this->manager->add_lang_string($add['stringid'], 'Describe table '.$dbtable.' here.');

                foreach ($dbfields as $dbfield) {
                    $stringid = 'privacy:metadata:db:'.$tabletostring.':'.$dbfield;
                    $add['fields'][] = [
                        'name' => $dbfield,
                        'stringid' => $stringid,
                    ];
                    $this->manager->add_lang_string($stringid, 'Describe field '.$dbfield.' here.');
                }

                $this->data['privacy']['_metadbfields'][] = $add;
            }
        }
    }

    /**
     * Populates the helper recipe value /privacy/_metasubsystems
     *
     * @param array $data
     */
    protected function set_data_metasubsystems(array $data) {

        if (!empty($data['privacy']['meta']['subsystems'])) {
            $this->data['privacy']['_metasubsystems'] = [];

            foreach ($this->normalize_names_and_fields($data['privacy']['meta']['subsystems']) as $input => $fields) {
                $subsystem = $input;

                // Drop the eventual core_ prefix so that we validate the name against the list of known subsystems.
                if (strpos($subsystem, 'core_') === 0) {
                    $subsystem = substr($subsystem, 5);
                }

                list($type, $name) = \core_component::normalize_component($subsystem);

                if ($type !== 'core') {
                    throw new \coding_exception('Unknown core subsystem: '.$input);
                }

                $add = [
                    'name' => $name,
                    'stringid' => 'privacy:metadata:subsystem:'.$name,
                    'fields' => [],
                ];

                if ($fields) {
                    $add['hasfields'] = true;
                    foreach ($fields as $field) {
                        $stringid = 'privacy:metadata:subsystem:'.$name.':'.$field;
                        $add['fields'][] = [
                            'name' => $field,
                            'stringid' => $stringid,
                        ];
                        $this->manager->add_lang_string($stringid, 'Describe field '.$field.' here.');
                    }
                }

                $this->manager->add_lang_string($add['stringid'], 'Describe how the '.$name.' subsystem is used by the plugin.');

                $this->data['privacy']['_metasubsystems'][] = $add;
            }
        }
    }

    /**
     * Populates the helper recipe value /privacy/_metaexternal
     *
     * @param array $data
     */
    protected function set_data_metaexternal(array $data) {

        if (!empty($data['privacy']['meta']['external'])) {
            $this->data['privacy']['_metaexternal'] = [];

            foreach ($this->normalize_names_and_fields($data['privacy']['meta']['external']) as $system => $fields) {
                $add = [
                    'name' => $system,
                    'stringid' => 'privacy:metadata:external:'.$system,
                    'fields' => [],
                ];
                $this->manager->add_lang_string($add['stringid'], 'Describe external system '.$system.' here.');

                if ($fields) {
                    $add['hasfields'] = true;
                    foreach ($fields as $field) {
                        $stringid = 'privacy:metadata:external:'.$system.':'.$field;
                        $add['fields'][] = [
                            'name' => $field,
                            'stringid' => $stringid,
                        ];
                        $this->manager->add_lang_string($stringid, 'Describe field '.$field.' here.');
                    }
                }

                $this->data['privacy']['_metaexternal'][] = $add;
            }
        }
    }

    /**
     * Prepares strings describing the user preferences.
     *
     * @param array $data
     */
    protected function set_data_userpreferences(array $data) {

        if (!empty($data['privacy']['meta']['userpreferences'])) {
            $this->data['privacy']['_metauserpreferences'] = [];

            foreach ($data['privacy']['meta']['userpreferences'] as $prefname) {
                // If the prefname starts with the component name, drop the prefix.
                $prefnamewoprefix = str_replace('_', '', preg_replace('/^'.$data['component'].'_/', '', $prefname));
                $stringid = 'privacy:metadata:preference:'.$prefnamewoprefix;

                $this->data['privacy']['_metauserpreferences'][] = [
                    'name' => $prefname,
                    'shortname' => $prefnamewoprefix,
                    'stringid' => $stringid,
                ];

                $this->manager->add_lang_string($stringid, 'Describe user preference '.$prefname.' here.');
            }
        }

        if (!empty($this->data['privacy']['_metauserpreferences'])) {
            $this->data['privacy']['_hasuserpreferences'] = true;
        }
    }

    /**
     * Normalize the given structure and prepare the list of names and its fields
     *
     * Privacy API metadata provider methods add_subsystem_link() and add_external_location_link() can be described via
     * a list of names and optional fields. The recipe YAML file may look like this:
     *
     * ```
     * subsystems:
     *   - core_comment
     *   - core_portfolio:
     *       - firstname
     *       - lastname
     *   - core_files
     * ```
     *
     * This helper method makes sure that such a structure is correctly parsed.
     *
     * @param array $input The input array node, such as the contents of the 'subsystems' node
     * @return array of name => fields
     */
    protected function normalize_names_and_fields(array $input) {

        $output = [];

        foreach ($input as $item) {
            if (is_array($item)) {
                foreach ($item as $name => $fields) {
                    $output[$name] = $fields;
                }

            } else {
                $output[$item] = [];
            }
        }

        return $output;
    }
}
