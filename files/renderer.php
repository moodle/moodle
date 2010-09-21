<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

defined('MOODLE_INTERNAL') || die();

/**
 * Rendering of files viewer related widgets.
 * @package   core
 * @subpackage file
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */

/**
 * File browser render
 *
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class core_files_renderer extends plugin_renderer_base {

    public function files_tree_viewer(file_info $file_info, array $options = null) {
        $tree = new files_tree_viewer($file_info, $options);
        return $this->render($tree);
    }

    public function render_files_tree_viewer(files_tree_viewer $tree) {
        $html = $this->output->heading_with_help(get_string('coursefiles'), 'courselegacyfiles', 'moodle');

        $html .= $this->output->container_start('coursefilesbreadcrumb');
        foreach($tree->path as $path) {
            $html .= $path;
            $html .= ' / ';
        }
        $html .= $this->output->container_end();

        $html .= $this->output->box_start();
        $table = new html_table();
        $table->head = array(get_string('filename', 'backup'), get_string('size'), get_string('modified'));
        $table->align = array('left', 'right', 'right');
        $table->width = '100%';
        $table->data = array();

        foreach ($tree->tree as $file) {
            if (!empty($file['isdir'])) {
                $table->data[] = array(
                    html_writer::link($file['url'], $this->output->pix_icon('f/folder', 'icon') . ' ' . $file['filename']),
                    '',
                    $file['filedate'],
                    );
            } else {
                $table->data[] = array(
                    html_writer::link($file['url'], $this->output->pix_icon('f/'.mimeinfo('icon', $file['filename']), get_string('icon')) . ' ' . $file['filename']),
                    $file['filesize'],
                    $file['filedate'],
                    );
            }
        }

        $html .= html_writer::table($table);
        $html .= $this->output->single_button(new moodle_url('/files/coursefilesedit.php', array('contextid'=>$tree->context->id)), get_string('coursefilesedit'), 'get');
        $html .= $this->output->box_end();
        return $html;
    }
}


/**
 * Data structure representing a general moodle file tree viewer
 *
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class files_tree_viewer implements renderable {
    public $tree;
    public $path;
    public $context;

    /**
     * Constructor of moodle_file_tree_viewer class
     * @param file_info $file_info
     * @param array $options
     */
    public function __construct(file_info $file_info, array $options = null) {
        global $CFG;

        //note: this MUST NOT use get_file_storage() !!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $this->options = (array)$options;
        $this->context = $options['context'];

        $this->tree = array();
        $children = $file_info->get_children();
        $current_file_params = $file_info->get_params();
        $parent_info = $file_info->get_parent();
        $level = $parent_info;
        $this->path = array();
        while ($level) {
            $params = $level->get_params();
            $context = get_context_instance_by_id($params['contextid']);
            // $this->context is current context
            if ($context->id != $this->context->id or empty($params['filearea'])) {
                break;
            }
            // unset unused parameters
            unset($params['component']);
            unset($params['filearea']);
            unset($params['filename']);
            unset($params['itemid']);
            $url = new moodle_url('/files/index.php', $params);
            $this->path[] = html_writer::link($url, $level->get_visible_name());
            $level = $level->get_parent();
        }
        $this->path = array_reverse($this->path);
        if ($current_file_params['filepath'] != '/') {
            $this->path[] = $file_info->get_visible_name();
        }

        foreach ($children as $child) {
            $filedate = $child->get_timemodified();
            $filesize = $child->get_filesize();
            $mimetype = $child->get_mimetype();
            $params = $child->get_params();
            unset($params['component']);
            unset($params['filearea']);
            unset($params['filename']);
            unset($params['itemid']);
            $fileitem = array(
                    'params'   => $params,
                    'filename' => $child->get_visible_name(),
                    'filedate' => $filedate ? userdate($filedate) : '',
                    'filesize' => $filesize ? display_size($filesize) : ''
                    );
            $url = new moodle_url('/files/index.php', $params);
            if ($child->is_directory()) {
                $fileitem['isdir'] = true;
                $fileitem['url'] = $url->out(false);
            } else {
                $fileitem['url'] = $child->get_url();
            }
            $this->tree[] = $fileitem;
        }
    }
}
