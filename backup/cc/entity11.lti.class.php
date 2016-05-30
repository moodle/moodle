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
 * @package   moodlecore
 * @subpackage backup-imscc
 * @copyright 2011 Darko Miletic (dmiletic@moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

class cc11_lti extends entities11 {

    public function generate_node () {

        cc2moodle::log_action('Creating BasicLTI mods');

        $response = '';

        if (!empty(cc2moodle::$instances['instances'][MOODLE_TYPE_LTI])) {
            foreach (cc2moodle::$instances['instances'][MOODLE_TYPE_LTI] as $instance) {
                $response .= $this->create_node_course_modules_mod_basiclti($instance);
            }
        }

        return $response;
    }

    private function create_node_course_modules_mod_basiclti ($instance) {

        $sheet_mod_basiclti = cc112moodle::loadsheet(SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_LTI);

        $topic_data = $this->get_basiclti_data($instance);

        $result = '';
        if (!empty($topic_data)) {

            $find_tags = array('[#mod_instance#]'        ,
                               '[#mod_basiclti_name#]'   ,
                               '[#mod_basiclti_intro#]'  ,
                               '[#mod_basiclti_timec#]'  ,
                               '[#mod_basiclti_timem#]'  ,
                               '[#mod_basiclti_toolurl#]',
                               '[#mod_basiclti_icon#]'
                               );

            $replace_values = array($instance['instance'],
                                    $topic_data['title'],
                                    $topic_data['description'],
                                    time(),time(),
                                    $topic_data['launchurl'],
                                    $topic_data['icon']
                                    );

            $result = str_replace($find_tags, $replace_values, $sheet_mod_basiclti);

        }

        return $result;
    }

    protected function getValue($node, $default = '') {
        $result = $default;
        if (is_object($node) && ($node->length > 0) && !empty($node->item(0)->nodeValue)) {
            $result = htmlspecialchars(trim($node->item(0)->nodeValue), ENT_COMPAT, 'UTF-8', false);
        }
        return $result;
    }

    public function get_basiclti_data($instance) {

        $topic_data = '';

        $basiclti_file = $this->get_external_xml($instance['resource_indentifier']);

        if (!empty($basiclti_file)) {
            $basiclti_file_path = cc2moodle::$path_to_manifest_folder . DIRECTORY_SEPARATOR . $basiclti_file;
            $basiclti_file_dir = dirname($basiclti_file_path);
            $basiclti = $this->load_xml_resource($basiclti_file_path);
            if (!empty($basiclti)) {
                $xpath = cc2moodle::newx_path($basiclti, cc112moodle::$basicltins);
                $topic_title = $this->getValue($xpath->query('/xmlns:cartridge_basiclti_link/blti:title'),'Untitled');
                $blti_description = $this->getValue($xpath->query('/xmlns:cartridge_basiclti_link/blti:description'));
                $launch_url = $this->getValue($xpath->query('/xmlns:cartridge_basiclti_link/blti:launch_url'));
                $launch_icon = $this->getValue($xpath->query('/xmlns:cartridge_basiclti_link/blti:icon'));
                $tool_raw = $this->getValue($xpath->query('/xmlns:cartridge_basiclti_link/blti:vendor/lticp:code'),null);
                $tool_url = $this->getValue($xpath->query('/xmlns:cartridge_basiclti_link/blti:vendor/lticp:url'),null);
                $tool_desc = $this->getValue($xpath->query('/xmlns:cartridge_basiclti_link/blti:vendor/lticp:description'),null);
                $topic_data['title'      ] = $topic_title;
                $topic_data['description'] = $blti_description;
                $topic_data['launchurl'  ] = $launch_url;
                $topic_data['icon'       ] = $launch_icon;
                $topic_data['orgid'      ] = $tool_raw;
                $topic_data['orgurl'     ] = $tool_url;
                $topic_data['orgdesc'    ] = $tool_desc;
            }
        }

        return $topic_data;
    }

}

