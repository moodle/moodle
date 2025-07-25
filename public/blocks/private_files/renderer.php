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
 * Print private files tree
 *
 * @package    block_private_files
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\html_writer;
use core\url;

class block_private_files_renderer extends plugin_renderer_base {

    /**
     * Prints private files tree view
     * @return string
     */
    public function private_files_tree() {
        return $this->render(new private_files_tree);
    }

    public function render_private_files_tree(private_files_tree $tree) {
        if (empty($tree->dir['subdirs']) && empty($tree->dir['files'])) {
            $html = $this->output->box(get_string('nofilesavailable', 'repository'));
        } else {
            $htmlid = 'private_files_tree_'.uniqid();
            $this->page->requires->js_call_amd('block_private_files/files_tree', 'init', [$htmlid]);
            $html = '<div id="'.$htmlid.'">';
            $html .= $this->htmllize_tree($tree, $tree->dir, true);
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Internal function - creates htmls structure suitable for core/tree AMD.
     *
     * @param private_files_tree $tree The renderable tree.
     * @param array $dir The directory in the tree
     * @param bool $isroot If it is the root directory in the tree.
     * @return string
     */
    protected function htmllize_tree($tree, $dir, $isroot) {
        global $CFG;

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        if ($isroot) {
            $result = '<ul role="tree" aria-label="' . s(get_string('privatefiles')) . '">';
        } else {
            $result = '<ul role="group" aria-hidden="true">';
        }
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon(file_folder_icon(), '');
            $content = $this->htmllize_tree($tree, $subdir, false);
            if ($content) {
                $result .= '<li role="treeitem" aria-expanded="false"><p>' . $image . s($subdir['dirname']) . '</p>' .
                    $content . '</li>';
            } else {
                $result .= '<li role="treeitem"><p>' . $image . s($subdir['dirname']) . '</p></li>';
            }
        }
        foreach ($dir['files'] as $file) {
            $filename = $file->get_filename();
            $url = url::make_pluginfile_url(
                contextid: $tree->context->id,
                component: 'user',
                area: 'private',
                itemid: null,
                pathname: $file->get_filepath(),
                filename: $filename,
                forcedownload: true
            )->out();
            $image = $this->output->pix_icon(file_file_icon($file), '');
            $result .= '<li role="treeitem">'.html_writer::link($url, $image.$filename, ['tabindex' => -1]).'</li>';
        }
        $result .= '</ul>';

        return $result;
    }
}

class private_files_tree implements renderable {
    public $context;
    public $dir;
    public function __construct() {
        global $USER;
        $this->context = context_user::instance($USER->id);
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'user', 'private', 0);
    }
}
