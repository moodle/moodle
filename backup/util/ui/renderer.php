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
 * This file contains backup and restore output renderers
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The primary renderer for the backup.
 *
 * Can be retrieved with the following code:
 * <?php
 * $renderer = $PAGE->get_renderer('core','backup');
 * ?>
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_renderer extends plugin_renderer_base {
    /**
     * Renderers a progress bar for the backup or restore given the items that
     * make it up.
     * @param array $items An array of items
     * @return string
     */
    public function progress_bar(array $items) {
        foreach ($items as &$item) {
            $text = $item['text'];
            unset($item['text']);
            if (array_key_exists('link', $item)) {
                $link = $item['link'];
                unset($item['link']);
                $item = html_writer::link($link, $text, $item);
            } else {
                $item = html_writer::tag('span', $text, $item);
            }
        }
        return html_writer::tag('div', join(get_separator(), $items), array('class'=>'backup_progress clearfix'));
    }
    /**
     * Prints a dependency notification
     * @param string $message
     * @return string
     */
    public function dependency_notification($message) {
        return html_writer::tag('div', $message, array('class'=>'notification dependencies_enforced'));
    }
    /**
     * Print a backup files tree
     * @param file_info $fileinfo
     * @param array $options
     * @return string
     */
    public function backup_files_viewer(file_info $fileinfo, array $options = null) {
        $tree = new backup_files_viewer($fileinfo, $options);
        return $this->render($tree);
    }

    public function render_backup_files_viewer(backup_files_viewer $tree) {
        $module = array('name'=>'backup_files_tree', 'fullpath'=>'/backup/util/ui/module.js', 'requires'=>array('yui2-treeview', 'yui2-json'), 'strings'=>array(array('restore', 'moodle')));
        $htmlid = 'backup-treeview-'.uniqid();
        $this->page->requires->js_init_call('M.core_backup_files_tree.init', array($htmlid), false, $module);

        $html = '<div>';
        foreach($tree->path as $path) {
            $html .= $path;
            $html .= ' / ';
        }
        $html .= '</div>';

        $html .= '<div id="'.$htmlid.'" class="filemanager-container">';
        if (empty($tree->tree)) {
            $html .= get_string('nofilesavailable', 'repository');
        } else {
            $html .= '<ul>';
            foreach($tree->tree as $node) {
                $link_attributes = array();
                if (!empty($node['isdir'])) {
                    $class = ' class="file-tree-folder"';
                    $restore_link = '';
                } else {
                    $class = ' class="file-tree-file"';
                    $link_attributes['target'] = '_blank';
                    $restore_link = html_writer::link($node['restoreurl'], get_string('restore', 'moodle'), $link_attributes);
                }
                $html .= '<li '.$class.'>';
                $html .= html_writer::link($node['url'], $node['filename'], $link_attributes);
                // when js is off, use this restore link
                // otherwise, yui treeview will generate a restore link in js
                $html .= ' '.$restore_link;
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div>';
        return $html;
    }
}
/**
 * Data structure representing backup files viewer
 *
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class backup_files_viewer implements renderable {
    public $tree;
    public $path;

    /**
     * Constructor of backup_files_viewer class
     * @param file_info $file_info
     * @param array $options
     */
    public function __construct(file_info $file_info, array $options = null) {
        global $CFG;
        $this->options = (array)$options;

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
            $url = new moodle_url('/backup/restorefile.php', $params);
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
            $fileitem = array(
                    'params'   => $params,
                    'filename' => $child->get_visible_name(),
                    'filedate' => $filedate ? userdate($filedate) : '',
                    'filesize' => $filesize ? display_size($filesize) : ''
                    );
            if ($child->is_directory()) {
                // ignore all other fileares except backup_course backup_section and backup_activity
                if ($params['component'] != 'backup' or !in_array($params['filearea'], array('course', 'section', 'activity'))) {
                    continue;
                }
                $fileitem['isdir'] = true;
                // link to this folder
                $folderurl = new moodle_url('/backup/restorefile.php', $params);
                $fileitem['url'] = $folderurl->out(false);
            } else {
                $restoreurl = new moodle_url('/backup/restorefile.php', array_merge($params, array('action'=>'choosebackupfile')));
                // link to this file
                $fileitem['url'] = $child->get_url();
                $fileitem['restoreurl'] = $restoreurl->out(false);
            }
            $this->tree[] = $fileitem;
        }
    }
}
