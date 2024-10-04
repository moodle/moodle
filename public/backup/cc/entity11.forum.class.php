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

class cc11_forum extends entities11 {

    public function full_path($path, $dir_sep = DIRECTORY_SEPARATOR) {

        $token = '$IMS-CC-FILEBASE$';
        $path = str_replace($token, '', $path);

        if (is_string($path) && ($path != '')) {
            $dir_sep;
            $dot_dir = '.';
            $up_dir = '..';
            $length = strlen($path);
            $rtemp = trim($path);
            $start = strrpos($path, $dir_sep);
            $can_continue = ($start !== false);
            $result = $can_continue ? '' : $path;
            $rcount = 0;

            while ($can_continue) {

                $dir_part = ($start !== false) ? substr($rtemp, $start + 1, $length - $start) : $rtemp;
                $can_continue = ($dir_part !== false);

                if ($can_continue) {
                    if ($dir_part != $dot_dir) {
                        if ($dir_part == $up_dir) {
                            $rcount++;
                        } else {
                            if ($rcount > 0) {
                                $rcount --;
                            } else {
                                $result = ($result == '') ? $dir_part : $dir_part . $dir_sep . $result;
                            }
                        }
                    }
                    $rtemp = substr($path, 0, $start);
                    $start = strrpos($rtemp, $dir_sep);
                    $can_continue = (($start !== false) || (strlen($rtemp) > 0));
                }
            }
        }

        return $result;
    }

    public function generate_node() {

        cc2moodle::log_action('Creating Forum mods');

        $response = '';

        if (!empty(cc2moodle::$instances['instances'][MOODLE_TYPE_FORUM])) {
            foreach (cc2moodle::$instances['instances'][MOODLE_TYPE_FORUM] as $instance) {
                $response .= $this->create_node_course_modules_mod_forum($instance);
            }
        }

        return $response;
    }

    private function create_node_course_modules_mod_forum($instance) {

        $sheet_mod_forum = cc112moodle::loadsheet(SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_FORUM);

        $topic_data = $this->get_topic_data($instance);

        $result = '';
        if (!empty($topic_data)) {

            $find_tags = array('[#mod_instance#]',
                                   '[#mod_forum_title#]',
                                   '[#mod_forum_intro#]',
                                   '[#date_now#]');

            $replace_values = array($instance['instance'],
            //To be more true to the actual forum name we use only forum title
            self::safexml($topic_data['title']),
            self::safexml($topic_data['description']),
            time());

            $result = str_replace($find_tags, $replace_values, $sheet_mod_forum);

        }

        return $result;
    }

    public function get_topic_data($instance) {

        $topic_data = array();

        $topic_file = $this->get_external_xml($instance['resource_indentifier']);

        if (!empty($topic_file)) {

            $topic_file_path = cc2moodle::$path_to_manifest_folder . DIRECTORY_SEPARATOR . $topic_file;
            $topic_file_dir = dirname($topic_file_path);
            $topic = $this->load_xml_resource($topic_file_path);

            if (!empty($topic)) {

                $xpath = cc2moodle::newx_path($topic, cc112moodle::$forumns);

                $topic_title = $xpath->query('/dt:topic/dt:title');
                if ($topic_title->length > 0 && !empty($topic_title->item(0)->nodeValue)) {
                    $topic_title = $topic_title->item(0)->nodeValue;
                } else {
                    $topic_title = 'Untitled Topic';
                }

                $topic_text = $xpath->query('/dt:topic/dt:text');
                $topic_text = !empty($topic_text->item(0)->nodeValue) ? $this->update_sources($topic_text->item(0)->nodeValue, dirname($topic_file)) : '';
                $topic_text = !empty($topic_text) ? str_replace("%24", "\$", $this->include_titles($topic_text)) : '';

                if (!empty($topic_title)) {
                    $topic_data['title'] = $topic_title;
                    $topic_data['description'] = $topic_text;
                }
            }

            $topic_attachments = $xpath->query('/dt:topic/dt:attachments/dt:attachment/@href');

            if ($topic_attachments->length > 0) {

                $attachment_html = '';

                foreach ($topic_attachments as $file) {
                    $attachment_html .= $this->generate_attachment_html($this->full_path($file->nodeValue,'/'));
                }

                $topic_data['description'] = !empty($attachment_html) ? $topic_text . '<p>Attachments:</p>' . $attachment_html : $topic_text;
            }
        }

        return $topic_data;
    }

    private function generate_attachment_html($filename) {

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

