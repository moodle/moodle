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

    /**
     * Prints the file manager and initializes all necessary libraries
     *
     * <pre>
     * $fm = new form_filemanager($options);
     * $output = get_renderer('core', 'files');
     * echo $output->render($fm);
     * </pre>
     *
     * @param form_filemanager $fm File manager to render
     * @return string HTML fragment
     */
    function render_form_filemanager($fm) {
        static $filemanagertemplateloaded;
        $html = $this->file_manager_html($fm);
        $module = array(
            'name'=>'form_filemanager',
            'fullpath'=>'/lib/form/filemanager.js',
            'requires' => array('core_filepicker', 'base', 'io-base', 'node', 'json', 'yui2-button', 'yui2-container', 'yui2-layout', 'yui2-menu', 'yui2-treeview', 'core_dndupload'),
            'strings' => array(array('loading', 'repository'), array('nomorefiles', 'repository'), array('confirmdeletefile', 'repository'),
                 array('add', 'repository'), array('accessiblefilepicker', 'repository'), array('move', 'moodle'),
                 array('cancel', 'moodle'), array('download', 'moodle'), array('ok', 'moodle'),
                 array('emptylist', 'repository'), array('nofilesattached', 'repository'), array('entername', 'repository'), array('enternewname', 'repository'),
                 array('zip', 'editor'), array('unzip', 'moodle'), array('rename', 'moodle'), array('delete', 'moodle'),
                 array('cannotdeletefile', 'error'), array('confirmdeletefile', 'repository'),
                 array('nopathselected', 'repository'), array('popupblockeddownload', 'repository'),
                 array('draftareanofiles', 'repository'), array('path', 'moodle'), array('setmainfile', 'repository'),
                 array('moving', 'repository'), array('files', 'moodle'), array('serverconnection', 'error')
            )
        );
        if (empty($filemanagertemplateloaded)) {
            $filemanagertemplateloaded = true;
            $this->page->requires->js_init_call('M.form_filemanager.set_templates',
                    array(array(
                        'onefile' => '___fullname___ ___action___'
                    )), true, $module);
        }
        $this->page->requires->js_init_call('M.form_filemanager.init', array($fm->options), true, $module);

        // non javascript file manager
        $html .= '<noscript>';
        $html .= "<div><object type='text/html' data='".$fm->get_nonjsurl()."' height='160' width='600' style='border:1px solid #000'></object></div>";
        $html .= '</noscript>';


        return $html;
    }

    /**
     * Returns html for displaying one file manager
     *
     * The main element in HTML must have id="filemanager-{$client_id}" and
     * class="filemanager fm-loading";
     * After all necessary code on the page (both html and javascript) is loaded,
     * the class fm-loading will be removed and added class fm-loaded;
     * The main element (class=filemanager) will be assigned the following classes:
     * 'fm-maxfiles' - when filemanager has maximum allowed number of files;
     * 'fm-nofiles' - when filemanager has no files at all (although there might be folders);
     * 'fm-noitems' - when current view (folder) has no items - neither files nor folders;
     * 'fm-updating' - when current view is being updated (usually means that loading icon is to be displayed);
     * 'fm-nomkdir' - when 'Make folder' action is unavailable (empty($fm->options->subdirs) == true)
     *
     * Element with class 'filemanager-container' will be holding evens for dnd upload (dragover, etc.).
     * It will have class:
     * 'dndupload-ready' - when a file is being dragged over the browser
     * 'dndupload-over' - when file is being dragged over this filepicker (additional to 'dndupload-ready')
     * 'dndupload-uploading' - during the upload process (note that after dnd upload process is
     * over, the file manager will refresh the files list and therefore will have for a while class
     * fm-updating. Both waiting processes should look similar so the images don't jump for user)
     *
     * If browser supports Drag-and-drop, the body element will have class 'dndsupported',
     * otherwise - 'dndnotsupported';
     *
     * Element with class 'fm-filelist' will be populated with files list;
     * Element with class 'fm-breadcrumb' will be populated with the path or have class 'fm-empty' when empty;
     * Element with class 'fm-btn-add' will hold onclick event for adding a file (opening filepicker);
     * Element with class 'fm-btn-mkdir' will hold onclick event for adding new folder;
     * Element with class 'fm-btn-download' will hold onclick event for download action;
     *
     * @param form_filemanager $fm
     * @return string
     */
    private function file_manager_html($fm) {
        global $OUTPUT;
        $options = $fm->options;
        $client_id = $options->client_id;
        $straddfile  = get_string('addfile', 'repository');
        $strmakedir  = get_string('makeafolder', 'moodle');
        $strdownload = get_string('downloadfolder', 'repository');
        $strloading  = get_string('loading', 'repository');
        $strnofilesattached = get_string('nofilesattached', 'repository');
        $strdroptoupload = get_string('droptoupload', 'moodle');
        $icon_progress = $OUTPUT->pix_icon('i/loading_small', $strloading).'';
        $restrictions = $this->file_manager_restrictions($fm);
        $strdndenabled = get_string('dndenabled_insentence', 'moodle').$OUTPUT->help_icon('dndenabled');
        $strdndenabledinbox = get_string('dndenabled_inbox', 'moodle');
        $loading = get_string('loading', 'repository');

        $html .= <<<FMHTML
<div id="filemanager-{$client_id}" class="filemanager fm-loading">
    <div class="filemanager-loading mdl-align">{$icon_progress}</div>
    <div class="fm-breadcrumb"></div>
    <div class="filemanager-toolbar">
        <input type="button" class="fm-btn-add" value="{$straddfile}" />
        <input type="button" class="fm-btn-mkdir" value="{$strmakedir}" />
        <input type="button" class="fm-btn-download" value="{$strdownload}" />
        {$restrictions}
        <span class="dndupload-message"> - $strdndenabled </span>
    </div>
    <div class="filemanager-container" >
        <ul class="fm-filelist"></ul>
        <div class="fm-empty-container mdl-align">{$strnofilesattached}
            <span class="dndupload-message">{$strdndenabledinbox}</span>
        </div>
        <div class="dndupload-target">{$strdroptoupload}</div>
        <div class="dndupload-uploadinprogress">{$icon_progress}</div>
        <div class="filemanager-updating">{$icon_progress}</div>
    </div>
</div>
<div class="clearer"></div>
FMHTML;
        return $html;
    }

    /**
     * Displays restrictions for the file manager
     *
     * @param form_filemanager $fm
     * @return string
     */
    private function file_manager_restrictions($fm) {
        $maxbytes = display_size($fm->options->maxbytes);
        if (empty($options->maxfiles) || $options->maxfiles == -1) {
            $maxsize = get_string('maxfilesize', 'moodle', $maxbytes);
            //$string['maxfilesize'] = 'Maximum size for new files: {$a}';
        } else {
            $strparam = (object)array('size' => $maxbytes, 'attachments' => $options->maxfiles);
            $maxsize = get_string('maxsizeandattachments', 'moodle', $strparam);
            //$string['maxsizeandattachments'] = 'Maximum size for new files: {$a->size}, maximum attachments: {$a->attachments}';
        }
        // TODO MDL-32020 also should say about 'File types accepted'
        return '<span>'. $maxsize. '</span>';
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
