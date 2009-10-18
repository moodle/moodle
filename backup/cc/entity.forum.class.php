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

class forum extends entities {

    public function generate_node () {

        cc2moodle::log_action('Creating Forum mods');

        $response = '';

        if (!empty(cc2moodle::$instances['instances'][MOODLE_TYPE_FORUM])) {
            foreach (cc2moodle::$instances['instances'][MOODLE_TYPE_FORUM] as $instance) {
                $response .= $this->create_node_course_modules_mod_forum($instance);
            }
        }

        return $response;
    }

    private function create_node_course_modules_mod_forum ($instance) {

        $sheet_mod_forum = cc2moodle::loadsheet(SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_FORUM);

        $topic_data = $this->get_topic_data($instance);

        if (!empty($topic_data)) {

            $find_tags = array('[#mod_instance#]',
                               '[#mod_forum_title#]',
                               '[#mod_forum_intro#]',
                               '[#date_now#]');

            $replace_values = array($instance['instance'],
                                    $instance['title'] . ' - ' . $topic_data['title'],
                                    $topic_data['description'],
                                    time());

            return str_replace($find_tags, $replace_values, $sheet_mod_forum);

        } else {
            return '';
        }
    }

    public function get_topic_data ($instance) {

        $topic_data = '';

        $namespaces = array('dt' => 'http://www.imsglobal.org/xsd/imsdt_v1p0');

        $topic_file = $this->get_external_xml($instance['resource_indentifier']);

        if (!empty($topic_file)) {

            $topic = $this->load_xml_resource(cc2moodle::$path_to_manifest_folder . DIRECTORY_SEPARATOR . $topic_file);

            if (!empty($topic)) {

                $xpath = cc2moodle::newx_path($topic, $namespaces);

                $topic_title = $xpath->query('/dt:topic/title');
                $topic_title = !empty($topic_title->item(0)->nodeValue) ? $topic_title->item(0)->nodeValue : '';

                $topic_text = $xpath->query('/dt:topic/text');
                $topic_text = !empty($topic_text->item(0)->nodeValue) ? $this->update_sources($topic_text->item(0)->nodeValue, dirname($topic_file)) : '';
                $topic_text = !empty($topic_text) ? str_replace("%24", "\$", $this->include_titles($topic_text)) : '';

                if (!empty($topic_title)) {
                    $topic_data['title'] = $topic_title;
                    $topic_data['description'] = $topic_text;
                }
            }

            $topic_attachments = $xpath->query('/dt:topic/attachments/attachment/@href');

            if ($topic_attachments->length > 0) {

                $attachment_html = '';

                foreach ($topic_attachments as $file) {
                    $attachment_html .= $this->generate_attachment_html($file->nodeValue);
                }

                $topic_data['description'] = !empty($attachment_html) ? $topic_text . '<p>Attachments:</p>' . $attachment_html : $topic_text;
            }
        }

        return $topic_data;
    }

    private function generate_attachment_html ($filename) {

        $images_extensions = array('gif' , 'jpeg' , 'jpg' , 'jif' , 'jfif' , 'png' , 'bmp');

        $fileinfo = pathinfo($filename);

        if (in_array($fileinfo['extension'], $images_extensions)) {
            return '<img src="$@FILEPHP@$/' . $filename . '" title="' . $fileinfo['basename'] . '" alt="' . $fileinfo['basename'] . '" /><br />';
        } else {
            return '<a href="$@FILEPHP@$/' . $filename . '" title="' . $fileinfo['basename'] . '" alt="' . $fileinfo['basename'] . '">' . $fileinfo['basename'] . '</a><br />';
        }

        return '';
    }
}
?>
