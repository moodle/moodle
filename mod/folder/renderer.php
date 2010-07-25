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
 * Folder module renderer
 *
 * @package    mod
 * @subpackage folder
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class mod_folder_renderer extends plugin_renderer_base {

    /**
     * Prints file folder tree view
     * @param object $folder instance
     * @param object $cm instance
     * @param object $course
     * @return void
     */
    public function folder_tree($folder, $cm, $course) {
        $this->render(new folder_tree($folder, $cm, $course));
    }

    public function render_folder_tree(folder_tree $tree) {
        global $PAGE;

        echo '<div id="folder_tree">';
        echo $this->htmllize_tree($tree, $tree->dir);
        echo '</div>';
        $this->page->requires->js_init_call('M.mod_folder.init_tree', array(true));
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     */
    protected function htmllize_tree($tree, $dir) {
        global $CFG;

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $result .= '<li>'.s($subdir['dirname']).' '.$this->htmllize_tree($tree, $subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$tree->context->id.'/mod_folder/content/'.$tree->folder->revision.$file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
            $result .= '<li><span>'.html_writer::link($url, $filename).'</span></li>';
        }
        $result .= '</ul>';

        return $result;
    }
}

class folder_tree implements renderable {
    public $context;
    public $folder;
    public $cm;
    public $course;
    public $dir;

    public function __construct($folder, $cm, $course) {
        $this->folder = $folder;
        $this->cm     = $cm;
        $this->course = $course;

        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'mod_folder', 'content', 0);
    }
}