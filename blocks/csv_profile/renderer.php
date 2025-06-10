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
 * CSV profile field import/update/delete block.
 *
 * @package   block_csv_profile
 * @copyright 2012 onwared Ted vd Brink, Brightally custom code
 * @copyright 2018 onwards Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class extends Moodle plugin renderer base class
 *
 */
class block_csv_profile_renderer extends plugin_renderer_base {

    /**
     * Prints files tree view
     * @param int $context provides the Moodle context ID
     * @return object/array of data for the tree
     */
    public function csv_profile_tree($context) {
        return $this->render(new csv_profile_tree($context));
    }

    /**
     * Prints private files tree view
     * @param object/array $tree contains data for building tree
     * @return string containing HTML
     */
    public function render_csv_profile_tree(csv_profile_tree $tree) {
        $module = array('name' => 'block_csv_profile',
                        'fullpath' => '/blocks/csv_profile/module.js',
                        'requires' => array('yui2-treeview'));
        if (empty($tree->dir['subdirs']) && empty($tree->dir['files'])) {
            $html = $this->output->box(get_string('nofilesavailable', 'repository'));
        } else {
            $htmlid = 'csv_profile_tree_' . uniqid();
            $this->page->requires->js_init_call('M.block_csv_profile.init_tree', array(false, $htmlid));
            $html = '<div id="' . $htmlid . '">';
            $html .= $this->htmllize_tree($tree, $tree->dir);
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     * @param object/array $tree
     * @param string $dir
     * @return string $result HTML data
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
            $image = $this->output->pix_icon("f/folder", $subdir['dirname'], 'moodle', array('class' => 'icon'));
            $result .= '<li yuiConfig=\'' . json_encode($yuiconfig) . '\'><div>' .
                    $image . ' ' . s($subdir['dirname']) . '</div> ' .
                    $this->htmllize_tree($tree, $subdir) . '</li>';
        }

        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/blocks/csv_profile/getfile.php",
                    '/' . $tree->context->id . '/user/csvprofile' . $file->get_filepath() .
                    $file->get_filename(), true);
            $filename = $file->get_filename();
            $icon = mimeinfo("icon", $filename);
            if (strlen($filename) > 10) {
                $pi = pathinfo($filename);
                $txt = $pi['filename'];
                $ext = $pi['extension'];
                $filename = substr($filename, 0, 14) . '...' . $ext;
            }

            $image = $this->output->pix_icon("f/$icon", $filename, 'moodle', array('class' => 'icon'));
            $result .= '<li yuiConfig=\'' . json_encode($yuiconfig) . '\'><div>' .
                    html_writer::link($url, $image . '&nbsp;' . $filename) . '</div></li>';
        }
        $result .= '</ul>';

        return $result;
    }
}

/**
 * Class extends Moodle renderable base class
 *
 */
class csv_profile_tree implements renderable {
    public $context;
    public $dir;
    /**
     * Constructor function
     * @param int $context
     */
    public function __construct($context) {
        global $USER;
        $this->context = $context;
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'user', 'csvprofile', 0);
    }
}
