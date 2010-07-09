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
 * A custom renderer class that extends the plugin_renderer_base and
 * is used by the assignment module.
 *
 * @package mod-assignment
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class mod_assignment_renderer extends plugin_renderer_base {

    /**
     * @return string
     */
    public function assignment_files($context, $itemid) {
        return $this->render(new assignment_files($context, $itemid));
    }

    public function render_assignment_files(assignment_files $tree) {
        $module = array('name'=>'mod_assignment_files', 'fullpath'=>'/mod/assignment/assignment.js', 'requires'=>array('yui2-treeview'));
        $htmlid = 'assignment_files_tree_'.uniqid();
        $this->page->requires->js_init_call('M.mod_assignment.init_tree', array(true, $htmlid));
        $html = '<div id="'.$htmlid.'">';
        $html .= $this->htmllize_tree($tree, $tree->dir);
        $html .= '</div>';
        return $html;
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     */
    protected function htmllize_tree($tree, $dir) {
        global $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon("/f/folder", $subdir['dirname'], 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.s($subdir['dirname']).'</div> '.$this->htmllize_tree($tree, $subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$tree->context->id.'/mod_assignment/submission/'.$file->get_itemid().'/'.$file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
            $icon = substr(mimeinfo("icon", $filename), 0, -4);
            $image = $this->output->pix_icon("/f/$icon", $filename, 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.html_writer::link($url, $filename).'</div></li>';
        }
        $result .= '</ul>';

        return $result;
    }
}

class assignment_files implements renderable {
    public $context;
    public $dir;
    public function __construct($context, $itemid) {
        global $USER;
        $this->context = $context;
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'mod_assignment', 'submission', $itemid);
    }
}
