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
 * File manager render
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

        $html = '<div>';
        foreach($tree->path as $path) {
            $html .= $path;
            $html .= ' / ';
        }
        $html .= '</div>';

        $html .= '<div id="course-file-tree-view" class="filemanager-container">';
        if (empty($tree->tree)) {
            $html .= get_string('nofilesavailable', 'repository');
        } else {
            $this->page->requires->js_init_call('M.core_filetree.init');
            $html .= '<ul>';
            foreach($tree->tree as $node) {
                $link_attributes = array();
                if (!empty($node['isdir'])) {
                    $class = ' class="file-tree-folder"';
                } else {
                    $class = ' class="file-tree-file"';
                    $link_attributes['target'] = '_blank';
                }
                $html .= '<li '.$class.'>';
                $html .= html_writer::link($node['url'], $node['filename'], $link_attributes);
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div>';
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

    /**
     * Constructor of moodle_file_tree_viewer class
     * @param file_info $file_info
     * @param array $options
     */
    public function __construct(file_info $file_info, array $options = null) {
        global $CFG;

        //note: this MUST NOT use get_file_storage() !!!!!!!!!!!!!!!!!!!!!!!!!!!!

        $this->options = (array)$options;
        if (isset($this->options['visible_areas'])) {
            $visible_areas = (array)$this->options['visible_areas'];
        } else {
            $visible_areas = false;
        }

        $this->tree = array();
        $children = $file_info->get_children();
        $parent_info = $file_info->get_parent();

        $level = $parent_info;
        $this->path = array();
        while ($level) {
            $params = $level->get_params();
            $context = get_context_instance_by_id($params['contextid']);
            // lock user in course level
            if ($context->contextlevel == CONTEXT_COURSECAT or $context->contextlevel == CONTEXT_SYSTEM) {
                break;
            }
            $url = new moodle_url('/files/index.php', $params);
            $this->path[] = html_writer::link($url->out(false), $level->get_visible_name());
            $level = $level->get_parent();
        }
        $this->path = array_reverse($this->path);
        $this->path[] = $file_info->get_visible_name();

        foreach ($children as $child) {
            $filedate = $child->get_timemodified();
            $filesize = $child->get_filesize();
            $mimetype = $child->get_mimetype();
            $params = $child->get_params();
            $url = new moodle_url('/files/index.php', $params);
            $fileitem = array(
                    'params'   => $params,
                    'filename' => $child->get_visible_name(),
                    'filedate' => $filedate ? userdate($filedate) : '',
                    'filesize' => $filesize ? display_size($filesize) : ''
                    );
            if ($child->is_directory()) {
                $fileitem['isdir'] = true;
                $fileitem['url'] = $url->out(false);
                if ($visible_areas !== false) {
                    if (!isset($visible_areas[$params['component']][$params['filearea']])) {
                        continue;
                    }
                }
            } else {
                $fileitem['url'] = $child->get_url();
            }
            $this->tree[] = $fileitem;
        }
    }
}
