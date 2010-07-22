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

    public function backup_details($details, $nextstageurl) {
        $yestick = $this->output->pix_icon('i/tick_green_big', get_string('yes'));
        $notick = $this->output->pix_icon('i/cross_red_big', get_string('no'));

        $html  = html_writer::start_tag('div', array('class'=>'backup-restore'));

        $html .= html_writer::start_tag('div', array('class'=>'backup-section'));
        $html .= $this->output->heading(get_string('backupdetails', 'backup'), 2, array('class'=>'header'));
        $html .= $this->backup_detail_pair(get_string('backuptype', 'backup'), get_string('backuptype'.$details->type, 'backup'));
        $html .= $this->backup_detail_pair(get_string('backupformat', 'backup'), get_string('backupformat'.$details->format, 'backup'));
        $html .= $this->backup_detail_pair(get_string('backupmode', 'backup'), get_string('backupmode'.$details->mode, 'backup'));
        $html .= $this->backup_detail_pair(get_string('backupdate', 'backup'), userdate($details->backup_date));
        $html .= $this->backup_detail_pair(get_string('moodleversion', 'backup'), 
                html_writer::tag('span', $details->moodle_release, array('class'=>'moodle_release')).
                html_writer::tag('span', '['.$details->moodle_version.']', array('class'=>'moodle_version sub-detail')));
        $html .= $this->backup_detail_pair(get_string('backupversion', 'backup'),
                html_writer::tag('span', $details->backup_release, array('class'=>'moodle_release')).
                html_writer::tag('span', '['.$details->backup_version.']', array('class'=>'moodle_version sub-detail')));
        $html .= $this->backup_detail_pair(get_string('originalwwwroot', 'backup'),
                html_writer::tag('span', $details->original_wwwroot, array('class'=>'originalwwwroot')).
                html_writer::tag('span', '['.$details->original_site_identifier_hash.']', array('class'=>'sitehash sub-detail')));
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div', array('class'=>'backup-section settings-section'));
        $html .= $this->output->heading(get_string('backupsettings', 'backup'), 2, array('class'=>'header'));
        foreach ($details->root_settings as $label=>$value) {
            if ($label == 'filename') continue;
            $html .= $this->backup_detail_pair(get_string('general'.str_replace('_','',$label), 'backup'), $value?$yestick:$notick);
        }
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div', array('class'=>'backup-section'));
        $html .= $this->output->heading(get_string('backupcoursedetails', 'backup'), 2, array('class'=>'header'));
        $html .= $this->backup_detail_pair(get_string('coursetitle', 'backup'), $details->course->title);
        $html .= $this->backup_detail_pair(get_string('courseid', 'backup'), $details->course->courseid);

        $html .= html_writer::start_tag('div', array('class'=>'backup-sub-section'));
        $html .= $this->output->heading(get_string('backupcoursesections', 'backup'), 3, array('class'=>'subheader'));
        foreach ($details->sections as $key=>$section) {
            $included = $key.'_included';
            $userinfo = $key.'_userinfo';
            if ($section->settings[$included] && $section->settings[$userinfo]) {
                $value = get_string('sectionincanduser','backup');
            } else if ($section->settings[$included]) {
                $value = get_string('sectioninc','backup');
            } else {
                continue;
            }
            $html .= $this->backup_detail_pair(get_string('backupcoursesection', 'backup', $section->title), $value);
            $table = null;
            foreach ($details->activities as $activitykey=>$activity) {
                if ($activity->sectionid != $section->sectionid) {
                    continue;
                }
                if (empty($table)) {
                    $table = new html_table();
                    $table->head = array('Module', 'Title', 'Userinfo');
                    $table->colclasses = array('modulename', 'moduletitle', 'userinfoincluded');
                    $table->align = array('left','left', 'center');
                    $table->attributes = array('class'=>'activitytable generaltable');
                    $table->data = array();
                }
                $table->data[] = array(
                    get_string('pluginname', $activity->modulename),
                    $activity->title,
                    ($activity->settings[$activitykey.'_userinfo'])?$yestick:$notick,
                );
            }
            if (!empty($table)) {
                $html .= $this->backup_detail_pair(get_string('sectionactivities','backup'), html_writer::table($table));
            }
            
        }
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');

        $html .= $this->output->single_button($nextstageurl, get_string('continue'), 'post');

        return $html;
    }

    public function course_selector(moodle_url $nextstageurl, $details, $categories, $courses, $currentcourse = null) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        $nextstageurl->param('sesskey', sesskey());

        $form = html_writer::start_tag('form', array('method'=>'post', 'action'=>$nextstageurl->out_omit_querystring()));
        foreach ($nextstageurl->params() as $key=>$value) {
            $form .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>$key, 'value'=>$value));
        }

        $html  = html_writer::start_tag('div', array('class'=>'backup-course-selector backup-restore'));

        // Current course
        if (!empty($currentcourse)) {
            $html .= $form;
            $form .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'targetid', 'value'=>$currentcourse));
            $html .= html_writer::start_tag('div', array('class'=>'bcs-current-course backup-section'));
            $html .= $this->output->heading(get_string('restoretocurrentcourse', 'backup'), 2, array('class'=>'header'));
            $html .= $this->backup_detail_input(get_string('restoretocurrentcourseadding', 'backup'), 'radio', 'target', backup::TARGET_CURRENT_ADDING);
            $html .= $this->backup_detail_input(get_string('restoretocurrentcoursedeleting', 'backup'), 'radio', 'target', backup::TARGET_CURRENT_DELETING);
            $html .= $this->backup_detail_pair('', html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('continue'))));
            $html .= html_writer::end_tag('div');
            $html .= html_writer::end_tag('form');
        }

        // New course
        $html .= $form;
        $html .= html_writer::start_tag('div', array('class'=>'bcs-new-course backup-section'));
        $html .= $this->output->heading(get_string('restoretonewcourse', 'backup'), 2, array('class'=>'header'));
        $html .= $this->backup_detail_input(get_string('restoretonewcourse', 'backup'), 'radio', 'target', backup::TARGET_NEW_COURSE, array('checked'=>'checked'));
        $html .= $this->backup_detail_select(get_string('coursecategory', 'backup'), 'targetid', $categories);
        $html .= $this->backup_detail_pair('', html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('continue'))));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');

        // Existing course
        $html .= $form;
        $html .= html_writer::start_tag('div', array('class'=>'bcs-existing-course backup-section'));
        $html .= $this->output->heading(get_string('restoretoexistingcourse', 'backup'), 2, array('class'=>'header'));
        $html .= $this->backup_detail_input(get_string('restoretoexistingcourseadding', 'backup'), 'radio', 'target', backup::TARGET_EXISTING_ADDING);
        $html .= $this->backup_detail_input(get_string('restoretoexistingcoursedeleting', 'backup'), 'radio', 'target', backup::TARGET_EXISTING_DELETING);
        $html .= $this->backup_detail_select(get_string('restoretocourse', 'backup'), 'targetid', $courses);
        $html .= $this->backup_detail_pair('', html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('continue'))));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');

        $html .= html_writer::end_tag('div');
        return $html;
    }

    protected function backup_detail_pair($label, $value) {
        static $count= 0;
        $count++;
        $html  = html_writer::start_tag('div', array('class'=>'detail-pair'));
        $html .= html_writer::tag('label', $label, array('class'=>'detail-pair-label', 'for'=>'detail-pair-value-'.$count));
        $html .= html_writer::tag('div', $value, array('class'=>'detail-pair-value', 'name'=>'detail-pair-value-'.$count));
        $html .= html_writer::end_tag('div');
        return $html;
    }

    protected function backup_detail_input($label, $type, $name, $value, array $attributes=array()) {
        return $this->backup_detail_pair($label, html_writer::empty_tag('input', $attributes+array('name'=>$name, 'type'=>$type, 'value'=>$value)));
    }

    protected function backup_detail_select($label, $name, $options, $selected='', $nothing=false, array $attributes=array()) {
        return $this->backup_detail_pair($label, html_writer::select($options, 'targetid', '', false, $attributes));
    }

    public function precheck_notices($results) {
        $output = html_writer::start_tag('div', array('class'=>'restore-precheck-notices'));
        if (array_key_exists('errors', $results)) {
            foreach ($results['errors'] as $error) {
                $output .= $this->output->notification($error);
            }
        }
        if (array_key_exists('warnings', $results)) {
            foreach ($results['warnings'] as $warning) {
                $output .= $this->output->notification($warning, 'notifywarning notifyproblem');
            }
        }
        return $output.html_writer::end_tag('div');
    }

    public function continue_button($url) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $url->param('sesskey', sesskey());
        $button = new single_button($url, get_string('continue'), 'post');
        $button->class = 'continuebutton';
        return $this->render($button);
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