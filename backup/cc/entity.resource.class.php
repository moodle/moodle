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
 * @copyright 2009 Mauro Rondinelli (mauro.rondinelli [AT] uvcms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

class resource extends entities {

    private $namespaces = array('wl' => 'http://www.imsglobal.org/xsd/imswl_v1p0');

    public function generate_node () {

        cc2moodle::log_action('Creating Resource mods');

        $response = '';
        $sheet_mod_resource = cc2moodle::loadsheet(SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_RESOURCE);

        if (!empty(cc2moodle::$instances['instances'][MOODLE_TYPE_RESOURCE])) {
            foreach (cc2moodle::$instances['instances'][MOODLE_TYPE_RESOURCE] as $instance) {
                $response .= $this->create_node_course_modules_mod_resource($sheet_mod_resource, $instance);
            }
        }

        return $response;

    }

    private function create_node_course_modules_mod_resource ($sheet_mod_resource, $instance) {

        $link = '';
        $xpath = cc2moodle::newx_path(CC2Moodle::$manifest, CC2Moodle::$namespaces);

        if ($instance['common_cartriedge_type'] == CC_TYPE_WEBCONTENT || $instance['common_cartriedge_type'] == CC_TYPE_ASSOCIATED_CONTENT) {
            $resource = $xpath->query('/imscc:manifest/imscc:resources/imscc:resource[@identifier="' . $instance['resource_indentifier'] . '"]/@href');
            $resource = !empty($resource->item(0)->nodeValue) ? $resource->item(0)->nodeValue : '';

            if (empty($resource)) {

                unset($resource);

                $resource = $xpath->query('/imscc:manifest/imscc:resources/imscc:resource[@identifier="' . $instance['resource_indentifier'] . '"]/imscc:file/@href');
                $resource = !empty($resource->item(0)->nodeValue) ? $resource->item(0)->nodeValue : '';

            }

            if (!empty($resource)) {
                $link = $resource;
            }
        }

        if ($instance['common_cartriedge_type'] == CC_TYPE_WEBLINK) {

            $external_resource = $xpath->query('/imscc:manifest/imscc:resources/imscc:resource[@identifier="' . $instance['resource_indentifier'] . '"]/imscc:file/@href')->item(0)->nodeValue;

            if ($external_resource) {

                $resource = $this->load_xml_resource(cc2moodle::$path_to_manifest_folder . DIRECTORY_SEPARATOR . $external_resource);

                if (!empty($resource)) {
                    $xpath = cc2moodle::newx_path($resource, $this->namespaces);
                    $resource = $xpath->query('/wl:webLink/url/@href');
                    $link = $resource->item(0)->nodeValue;
                }
            }
        }

        $find_tags = array('[#mod_instance#]',
                           '[#mod_name#]',
                           '[#mod_type#]',
                           '[#mod_reference#]',
                           '[#mod_summary#]',
                           '[#mod_alltext#]',
                           '[#date_now#]');

        $replace_values = array($instance['instance'],
                                $instance['title'],
                                'file',
                                $link,
                                '',
                                '',
                                time());

        return str_replace($find_tags, $replace_values, $sheet_mod_resource);
    }
}
