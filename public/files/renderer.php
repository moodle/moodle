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

    public function files_tree_viewer(file_info $file_info, ?array $options = null) {
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
        $table->head = array(get_string('name'), get_string('lastmodified'), get_string('size', 'repository'), get_string('type', 'repository'));
        $table->align = array('left', 'left', 'left', 'left');
        $table->width = '100%';
        $table->data = array();

        foreach ($tree->tree as $file) {
            $filedate = $filesize = $filetype = '';
            if ($file['filedate']) {
                $filedate = userdate($file['filedate'], get_string('strftimedatetimeshort', 'langconfig'));
            }
            if (empty($file['isdir'])) {
                if ($file['filesize']) {
                    $filesize = display_size($file['filesize']);
                }
                $fileicon = file_file_icon($file);
                $filetype = get_mimetype_description($file);
            } else {
                $fileicon = file_folder_icon();
            }
            $table->data[] = array(
                html_writer::link($file['url'], $this->output->pix_icon($fileicon, get_string('icon')) . ' ' . $file['filename']),
                $filedate,
                $filesize,
                $filetype
                );
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
    public function render_form_filemanager($fm) {
        $html = $this->fm_print_generallayout($fm);
        $module = array(
            'name'=>'form_filemanager',
            'fullpath'=>'/lib/form/filemanager.js',
            'requires' => array('moodle-core-notification-dialogue', 'core_filepicker', 'base', 'io-base', 'node', 'json', 'core_dndupload', 'panel', 'resize-plugin', 'dd-plugin'),
            'strings' => array(
                array('error', 'moodle'), array('info', 'moodle'), array('confirmdeletefile', 'repository'),
                array('draftareanofiles', 'repository'), array('entername', 'repository'), array('enternewname', 'repository'),
                array('invalidjson', 'repository'), array('popupblockeddownload', 'repository'),
                array('unknownoriginal', 'repository'), array('confirmdeletefolder', 'repository'),
                array('confirmdeletefilewithhref', 'repository'), array('confirmrenamefolder', 'repository'),
                array('confirmrenamefile', 'repository'), array('newfolder', 'repository'), array('edit', 'moodle'),
                array('originalextensionchange', 'repository'), array('originalextensionremove', 'repository'),
                array('aliaseschange', 'repository'), ['nofilesselected', 'repository'],
                ['confirmdeleteselectedfile', 'repository'], ['selectall', 'moodle'], ['deselectall', 'moodle'],
                ['selectallornone', 'form'],
            )
        );
        if ($this->page->requires->should_create_one_time_item_now('core_file_managertemplate')) {
            $this->page->requires->js_init_call('M.form_filemanager.set_templates',
                    array($this->filemanager_js_templates()), true, $module);
        }
        $this->page->requires->js_call_amd('core/checkbox-toggleall', 'init');
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
     * @param form_filemanager $fm
     * @return string
     */
    protected function fm_print_generallayout($fm) {
        $context = [
                'client_id' => $fm->options->client_id,
                'helpicon' => $this->help_icon('setmainfile', 'repository'),
                'restrictions' => $this->fm_print_restrictions($fm)
        ];
        return $this->render_from_template('core/filemanager_page_generallayout', $context);
    }

    /**
     * FileManager JS template for displaying one file in 'icon view' mode.
     *
     * Except for elements described in fp_js_template_iconfilename, this template may also
     * contain element with class 'fp-contextmenu'. If context menu is available for this
     * file, the top element will receive the additional class 'fp-hascontextmenu' and
     * the element with class 'fp-contextmenu' will hold onclick event for displaying
     * the context menu.
     *
     * @see fp_js_template_iconfilename()
     * @return string
     */
    protected function fm_js_template_iconfilename() {
        $rv = '
<div class="fp-file">
    <a href="#" class="d-block aabtn">
    <div style="position:relative;">
        <div class="fp-thumbnail"></div>
        <div class="fp-reficons1"></div>
        <div class="fp-reficons2"></div>
    </div>
    <div class="fp-filename-field">
        <div class="fp-filename text-truncate"></div>
    </div>
    </a>
    <a class="fp-contextmenu btn btn-icon btn-light border" href="#">
        <span>'.$this->pix_icon('i/menu', '▶').'</span></a>
</div>';
        return $rv;
    }

    /**
     * FileManager JS template for displaying file name in 'table view' and 'tree view' modes.
     *
     * Except for elements described in fp_js_template_listfilename, this template may also
     * contain element with class 'fp-contextmenu'. If context menu is available for this
     * file, the top element will receive the additional class 'fp-hascontextmenu' and
     * the element with class 'fp-contextmenu' will hold onclick event for displaying
     * the context menu.
     *
     * @todo MDL-32736 remove onclick="return false;"
     * @see fp_js_template_listfilename()
     * @return string
     */
    protected function fm_js_template_listfilename() {
        $rv = '
<span class="fp-filename-icon">
    <a href="#">
    <span class="fp-icon"></span>
    <span class="fp-reficons1"></span>
    <span class="fp-reficons2"></span>
    <span class="fp-filename"></span>
    </a>
    <a class="fp-contextmenu" href="#" onclick="return false;">'.$this->pix_icon('i/menu', '▶').'</a>
</span>';
        return $rv;
    }

    /**
     * FileManager JS template for displaying 'Make new folder' dialog.
     *
     * Must be wrapped in an element, CSS for this element must define width and height of the window;
     *
     * Must have one input element with type="text" (for users to enter the new folder name);
     *
     * content of element with class 'fp-dlg-curpath' will be replaced with current path where
     * new folder is about to be created;
     * elements with classes 'fp-dlg-butcreate' and 'fp-dlg-butcancel' will hold onclick events;
     *
     * @return string
     */
    protected function fm_js_template_mkdir() {
        $rv = '
<div class="filemanager fp-mkdir-dlg" role="dialog" aria-live="assertive" aria-labelledby="fp-mkdir-dlg-title">
    <div class="fp-mkdir-dlg-text">
        <label id="fp-mkdir-dlg-title">' . get_string('newfoldername', 'repository') . '</label><br/>
        <input type="text" class="form-control"/>
    </div>
    <button class="fp-dlg-butcreate btn-primary btn">'.get_string('makeafolder').'</button>
    <button class="fp-dlg-butcancel btn-cancel btn">'.get_string('cancel').'</button>
</div>';
        return $rv;
    }

    /**
     * FileManager JS template for error/info message displayed as a separate popup window.
     *
     * @see fp_js_template_message()
     * @return string
     */
    protected function fm_js_template_message() {
        return $this->fp_js_template_message();
    }

    /**
     * FileManager JS template for window with file information/actions.
     *
     */
    protected function fm_js_template_fileselectlayout() {
        $context = [
                'helpicon' => $this->help_icon('setmainfile', 'repository'),
                'licensehelpicon' => $this->create_license_help_icon_context(),
                'columns' => true
        ];
        return $this->render_from_template('core/filemanager_fileselect', $context);
    }

    /**
     * FileManager JS template for popup confirm dialogue window.
     *
     * @return string
     */
    protected function fm_js_template_confirmdialog() {
        return $this->render_from_template('core/filemanager_confirmdialog', []);
    }

    /**
     * Returns all FileManager JavaScript templates as an array.
     *
     * @return array
     */
    public function filemanager_js_templates() {
        $class_methods = get_class_methods($this);
        $templates = array();
        foreach ($class_methods as $method_name) {
            if (preg_match('/^fm_js_template_(.*)$/', $method_name, $matches))
            $templates[$matches[1]] = $this->$method_name();
        }
        return $templates;
    }

    /**
     * Displays restrictions for the file manager
     *
     * @param form_filemanager $fm
     * @return string
     */
    protected function fm_print_restrictions($fm) {
        $maxbytes = display_size($fm->options->maxbytes, 0);
        $strparam = (object) array('size' => $maxbytes, 'attachments' => $fm->options->maxfiles,
            'areasize' => display_size($fm->options->areamaxbytes, 0));
        $hasmaxfiles = !empty($fm->options->maxfiles) && $fm->options->maxfiles > 0;
        $hasarealimit = !empty($fm->options->areamaxbytes) && $fm->options->areamaxbytes != -1;
        if ($hasmaxfiles && $hasarealimit) {
            $maxsize = get_string('maxsizeandattachmentsandareasize', 'moodle', $strparam);
        } else if ($hasmaxfiles) {
            $maxsize = get_string('maxsizeandattachments', 'moodle', $strparam);
        } else if ($hasarealimit) {
            $maxsize = get_string('maxsizeandareasize', 'moodle', $strparam);
        } else {
            $maxsize = get_string('maxfilesize', 'moodle', $maxbytes);
        }

        return '<span>'. $maxsize . '</span>';
    }

    /**
     * Template for FilePicker with general layout (not QuickUpload).
     *
     *
     * @return string
     */
    protected function fp_js_template_generallayout() {
        return $this->render_from_template('core/filemanager_modal_generallayout', []);
    }

    /**
     * FilePicker JS template for displaying one file in 'icon view' mode.
     *
     * the element with class 'fp-thumbnail' will be resized to the repository thumbnail size
     * (both width and height, unless min-width and/or min-height is set in CSS) and the content of
     * an element will be replaced with an appropriate img;
     *
     * the width of element with class 'fp-filename' will be set to the repository thumbnail width
     * (unless min-width is set in css) and the content of an element will be replaced with filename
     * supplied by repository;
     *
     * top element(s) will have class fp-folder if the element is a folder;
     *
     * List of files will have parent <div> element with class 'fp-iconview'
     *
     * @return string
     */
    protected function fp_js_template_iconfilename() {
        $rv = '
<a class="fp-file" href="#" >
    <div style="position:relative;">
        <div class="fp-thumbnail"></div>
        <div class="fp-reficons1"></div>
        <div class="fp-reficons2"></div>
    </div>
    <div class="fp-filename-field">
        <p class="fp-filename text-truncate"></p>
    </div>
</a>';
        return $rv;
    }

    /**
     * FilePicker JS template for displaying file name in 'table view' and 'tree view' modes.
     *
     * content of the element with class 'fp-icon' will be replaced with an appropriate img;
     *
     * content of element with class 'fp-filename' will be replaced with filename supplied by
     * repository;
     *
     * top element(s) will have class fp-folder if the element is a folder;
     *
     * Note that tree view and table view are the YUI widgets and therefore there are no
     * other templates. The widgets will be wrapped in <div> with class fp-treeview or
     * fp-tableview (respectfully).
     *
     * @return string
     */
    protected function fp_js_template_listfilename() {
        $rv = '
<span class="fp-filename-icon">
    <a href="#">
        <span class="fp-icon"></span>
        <span class="fp-filename"></span>
    </a>
</span>';
        return $rv;
    }

    /**
     * FilePicker JS template for displaying link/loading progress for fetching of the next page
     *
     * This text is added to .fp-content AFTER .fp-iconview/.fp-treeview/.fp-tableview
     *
     * Must have one parent element with class 'fp-nextpage'. It will be assigned additional
     * class 'loading' during loading of the next page (it is recommended that in this case the link
     * becomes unavailable). Also must contain one element <a> or <button> that will hold
     * onclick event for displaying of the next page. The event will be triggered automatically
     * when user scrolls to this link.
     *
     * @return string
     */
    protected function fp_js_template_nextpage() {
        $rv = '
<div class="fp-nextpage">
    <div class="fp-nextpage-link"><a href="#">'.get_string('more').'</a></div>
    <div class="fp-nextpage-loading">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for window appearing to select a file.
     *
     * @return string
     */
    protected function fp_js_template_selectlayout() {
        $context = [
            'licensehelpicon' => $this->create_license_help_icon_context()
        ];
        return $this->render_from_template('core/filemanager_selectlayout', $context);
    }

    /**
     * FilePicker JS template for 'Upload file' repository
     *
     * @return string
     */
    protected function fp_js_template_uploadform() {
        $context = [
            'licensehelpicon' => $this->create_license_help_icon_context()
        ];
        return $this->render_from_template('core/filemanager_uploadform', $context);
    }

    /**
     * FilePicker JS template to display during loading process (inside element with class 'fp-content').
     *
     * @return string
     */
    protected function fp_js_template_loading() {
        return '
<div class="fp-content-loading">
    <div class="fp-content-center">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
</div>';
    }

    /**
     * FilePicker JS template for error (inside element with class 'fp-content').
     *
     * must have element with class 'fp-error', its content will be replaced with error text
     * and the error code will be assigned as additional class to this element
     * used errors: invalidjson, nofilesavailable, norepositoriesavailable
     *
     * @return string
     */
    protected function fp_js_template_error() {
        $rv = '
<div class="fp-content-error" ><div class="fp-error"></div></div>';
        return $rv;
    }

    /**
     * FilePicker JS template for error/info message displayed as a separate popup window.
     *
     * Must be wrapped in one element, CSS for this element must define
     * width and height of the window. It will be assigned with an additional class 'fp-msg-error'
     * or 'fp-msg-info' depending on message type;
     *
     * content of element with class 'fp-msg-text' will be replaced with error/info text;
     *
     * element with class 'fp-msg-butok' will hold onclick event
     *
     * @return string
     */
    protected function fp_js_template_message() {
        $rv = '
<div class="file-picker fp-msg" role="alertdialog" aria-live="assertive" aria-labelledby="fp-msg-labelledby">
    <p class="fp-msg-text" id="fp-msg-labelledby"></p>
    <button class="fp-msg-butok btn-primary btn">'.get_string('ok').'</button>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists.
     *
     * @return string
     */
    protected function fp_js_template_processexistingfile() {
        return $this->render_from_template('core/filemanager_processexistingfile', []);
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists
     * (multiple-file version).
     *
     * @return string
     */
    protected function fp_js_template_processexistingfilemultiple() {
        return $this->render_from_template('core/filemanager_processexistingfilemultiple', []);
    }

    /**
     * FilePicker JS template for repository login form including templates for each element type
     *
     * @return string
     */
    protected function fp_js_template_loginform() {
        return $this->render_from_template('core/filemanager_loginform', []);
    }

    /**
     * Returns all FilePicker JavaScript templates as an array.
     *
     * @return array
     */
    public function filepicker_js_templates() {
        $class_methods = get_class_methods($this);
        $templates = array();
        foreach ($class_methods as $method_name) {
            if (preg_match('/^fp_js_template_(.*)$/', $method_name, $matches))
            $templates[$matches[1]] = $this->$method_name();
        }
        return $templates;
    }

    /**
     * Returns HTML for default repository searchform to be passed to Filepicker
     *
     * This will be used as contents for search form defined in generallayout template
     * (form with id {TOOLSEARCHID}).
     * Default contents is one text input field with name="s"
     */
    public function repository_default_searchform() {
        return $this->render_from_template('core/filemanager_default_searchform', []);
    }

    /**
     * Create the context for rendering help icon with license links displaying all licenses and sources.
     *
     * @return \stdClass $iconcontext the context for rendering license help info.
     */
    protected function create_license_help_icon_context(): stdClass {
        $licensecontext = new stdClass();

        $licenses = [];
        // Discard licenses without a name or source from enabled licenses.
        foreach (license_manager::get_active_licenses() as $license) {
            if (!empty($license->fullname) && !empty($license->source)) {
                $licenses[] = $license;
            }
        }

        $licensecontext->licenses = $licenses;
        $helptext = $this->render_from_template('core/filemanager_licenselinks', $licensecontext);

        $iconcontext = new stdClass();
        $iconcontext->text = $helptext;
        $iconcontext->alt = get_string('helpprefix2', 'moodle', get_string('chooselicense', 'repository'));

        return $iconcontext;
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

    /** @var array file tree viewer options. */
    protected array $options = [];

    /**
     * Constructor of moodle_file_tree_viewer class
     * @param file_info $file_info
     * @param array $options
     */
    public function __construct(file_info $file_info, ?array $options = null) {
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
            $context = context::instance_by_id($params['contextid']);
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
                    'mimetype' => $child->get_mimetype(),
                    'filedate' => $filedate ? $filedate : '',
                    'filesize' => $filesize ? $filesize : ''
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
