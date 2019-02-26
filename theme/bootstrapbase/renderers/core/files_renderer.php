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

namespace theme_bootstrapbase\output\core;

use plugin_renderer_base;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/files/renderer.php');

/**
 * Rendering of files viewer related widgets.
 * @package   theme_bootstrapbase
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Rendering of files viewer related widgets.
 * @package   theme_bootstrapbase
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class files_renderer extends \core_files_renderer {
    /**
     * FileManager JS template for window with file information/actions.
     *
     * All content must be enclosed in one element, CSS for this class must define width and
     * height of the window;
     *
     * Thumbnail image will be added as content to the element with class 'fp-thumbnail';
     *
     * Inside the window the elements with the following classnames must be present:
     * 'fp-saveas', 'fp-author', 'fp-license', 'fp-path'. Inside each of them must be
     * one input element (or select in case of fp-license and fp-path). They may also have labels.
     * The elements will be assign with class 'uneditable' and input/select element will become
     * disabled if they are not applicable for the particular file;
     *
     * There may be present elements with classes 'fp-original', 'fp-datemodified', 'fp-datecreated',
     * 'fp-size', 'fp-dimensions', 'fp-reflist'. They will receive additional class 'fp-unknown' if
     * information is unavailable. If there is information available, the content of embedded
     * element with class 'fp-value' will be substituted with the value;
     *
     * The value of Original ('fp-original') is loaded in separate request. When it is applicable
     * but not yet loaded the 'fp-original' element receives additional class 'fp-loading';
     *
     * The value of 'Aliases/Shortcuts' ('fp-reflist') is also loaded in separate request. When it
     * is applicable but not yet loaded the 'fp-original' element receives additional class
     * 'fp-loading'. The string explaining that XX references exist will replace content of element
     * 'fp-refcount'. Inside '.fp-reflist .fp-value' each reference will be enclosed in <li>;
     *
     * Elements with classes 'fp-file-update', 'fp-file-download', 'fp-file-delete', 'fp-file-zip',
     * 'fp-file-unzip', 'fp-file-setmain' and 'fp-file-cancel' will hold corresponding onclick
     * events (there may be several elements with class 'fp-file-cancel');
     *
     * When confirm button is pressed and file is being selected, the top element receives
     * additional class 'loading'. It is removed when response from server is received.
     *
     * When any of the input fields is changed, the top element receives class 'fp-changed';
     * When current file can be set as main - top element receives class 'fp-cansetmain';
     * When current file is folder/zip/file - top element receives respectfully class
     * 'fp-folder'/'fp-zip'/'fp-file';
     *
     * @return string
     */
    protected function fm_js_template_fileselectlayout() {
        global $OUTPUT;
        $strloading  = get_string('loading', 'repository');
        $iconprogress = $this->pix_icon('i/loading_small', $strloading).'';
        $rv = '
<div class="filemanager fp-select">
    <div class="fp-select-loading">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
    <form class="form-horizontal">
        <button class="fp-file-download">'.get_string('download').'</button>
        <button class="fp-file-delete">'.get_string('delete').'</button>
        <button class="fp-file-setmain">'.get_string('setmainfile', 'repository').'</button>
        <span class="fp-file-setmain-help">'.$OUTPUT->help_icon('setmainfile', 'repository').'</span>
        <button class="fp-file-zip">'.get_string('zip', 'editor').'</button>
        <button class="fp-file-unzip">'.get_string('unzip').'</button>
        <div class="fp-hr"></div>

        <div class="fp-forminset">
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">'.get_string('name', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-author control-group clearfix">
                    <label class="control-label">'.get_string('author', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-license control-group clearfix">
                    <label class="control-label">'.get_string('chooselicense', 'repository').'</label>
                    <div class="controls">
                        <select></select>
                    </div>
                </div>
                <div class="fp-path control-group clearfix">
                    <label class="control-label">'.get_string('path', 'repository').'</label>
                    <div class="controls">
                        <select></select>
                    </div>
                </div>
                <div class="fp-original control-group clearfix">
                    <label class="control-label">'.get_string('original', 'repository').'</label>
                    <div class="controls">
                        <span class="fp-originloading">'.$iconprogress.' '.$strloading.'</span><span class="fp-value"></span>
                    </div>
                </div>
                <div class="fp-reflist control-group clearfix">
                    <label class="control-label">'.get_string('referenceslist', 'repository').'</label>
                    <div class="controls">
                        <p class="fp-refcount"></p>
                        <span class="fp-reflistloading">'.$iconprogress.' '.$strloading.'</span>
                        <ul class="fp-value"></ul>
                    </div>
                </div>
        </div>
        <div class="fp-select-buttons">
            <button class="fp-file-update btn-primary btn">'.get_string('update', 'moodle').'</button>
            <button class="fp-file-cancel btn-cancel btn">'.get_string('cancel').'</button>
        </div>
    </form>
    <div class="fp-info clearfix">
        <div class="fp-hr"></div>
        <p class="fp-thumbnail"></p>
        <div class="fp-fileinfo">
            <div class="fp-datemodified">'.get_string('lastmodified', 'repository').' <span class="fp-value"></span></div>
            <div class="fp-datecreated">'.get_string('datecreated', 'repository').' <span class="fp-value"></span></div>
            <div class="fp-size">'.get_string('size', 'repository').' <span class="fp-value"></span></div>
            <div class="fp-dimensions">'.get_string('dimensions', 'repository').' <span class="fp-value"></span></div>
        </div>
    </div>
</div>';
        return $rv;
    }

    /**
     * FileManager JS template for popup confirm dialogue window.
     *
     * Must have one top element, CSS for this element must define width and height of the window;
     *
     * content of element with class 'fp-dlg-text' will be replaced with dialog text;
     * elements with classes 'fp-dlg-butconfirm' and 'fp-dlg-butcancel' will
     * hold onclick events;
     *
     * @return string
     */
    protected function fm_js_template_confirmdialog() {
        $rv = '
<div class="filemanager fp-dlg">
    <div class="fp-dlg-text"></div>
    <button class="fp-dlg-butconfirm btn-primary btn">'.get_string('ok').'</button>
    <button class="fp-dlg-butcancel btn-cancel btn">'.get_string('cancel').'</button>
</div>';
        return $rv;
    }

    /**
     * Template for FilePicker with general layout (not QuickUpload).
     *
     * Must have one top element containing everything else (recommended <div class="file-picker">),
     * CSS for this element must define width and height of the filepicker window. Or CSS must
     * define min-width, max-width, min-height and max-height and in this case the filepicker
     * window will be resizeable;
     *
     * Element with class 'fp-viewbar' will have the class 'enabled' or 'disabled' when view mode
     * can be changed or not;
     * Inside element with class 'fp-viewbar' there are expected elements with classes
     * 'fp-vb-icons', 'fp-vb-tree' and 'fp-vb-details'. They will handle onclick events to switch
     * between the view modes, the last clicked element will have the class 'checked';
     *
     * Element with class 'fp-repo' is a template for displaying one repository. Other repositories
     * will be attached as siblings (classes first/last/even/odd will be added respectfully).
     * The currently selected repostory will have class 'active'. Contents of element with class
     * 'fp-repo-name' will be replaced with repository name, source of image with class
     * 'fp-repo-icon' will be replaced with repository icon;
     *
     * Element with class 'fp-content' is obligatory and will hold the current contents;
     *
     * Element with class 'fp-paging' will contain page navigation (will be deprecated soon);
     *
     * Element with class 'fp-path-folder' is a template for one folder in path toolbar.
     * It will hold mouse click event and will be assigned classes first/last/even/odd respectfully.
     * Parent element will receive class 'empty' when there are no folders to be displayed;
     * The content of subelement with class 'fp-path-folder-name' will be substituted with folder name;
     *
     * Element with class 'fp-toolbar' will have class 'empty' if all 'Back', 'Search', 'Refresh',
     * 'Logout', 'Manage' and 'Help' are unavailable for this repo;
     *
     * Inside fp-toolbar there are expected elements with classes fp-tb-back, fp-tb-search,
     * fp-tb-refresh, fp-tb-logout, fp-tb-manage and fp-tb-help. Each of them will have
     * class 'enabled' or 'disabled' if particular repository has this functionality.
     * Element with class 'fp-tb-search' must contain empty form inside, it's contents will
     * be substituted with the search form returned by repository (in the most cases it
     * is generated with template core_repository_renderer::repository_default_searchform);
     * Other elements must have either <a> or <button> element inside, it will hold onclick
     * event for corresponding action; labels for fp-tb-back and fp-tb-logout may be
     * replaced with those specified by repository;
     *
     * @return string
     */
    protected function fp_js_template_generallayout() {
        $rv = '
<div tabindex="0" class="file-picker fp-generallayout" role="dialog" aria-live="assertive">
    <div class="fp-repo-area">
        <ul class="fp-list" role="tablist">
            <li class="fp-repo" role="tab" aria-selected="false" tabindex="-1">
                <a href="#" tabindex="-1"><img class="fp-repo-icon" alt=" " width="16" height="16" />&nbsp;
                    <span class="fp-repo-name"></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="fp-repo-items" tabindex="0">
        <div class="fp-navbar">
            <div>
                <div class="fp-toolbar">
                    <div class="fp-tb-back">
                        <a href="#">'.get_string('back', 'repository').'</a>
                    </div>
                    <div class="fp-tb-search">
                        <form></form>
                    </div>
                    <div class="fp-tb-refresh">
                        <a title="'. get_string('refresh', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/refresh', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-logout">
                        <a title="'. get_string('logout', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/logout', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-manage">
                        <a title="'. get_string('manageurl', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/setting', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-help">
                        <a title="'. get_string('help', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/help', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-message"></div>
                </div>
                <div class="fp-viewbar">
                    <a role="button" title="'. get_string('displayicons', 'repository') .'" class="fp-vb-icons" href="#">
                        ' . $this->pix_icon('fp/view_icon_active', '', 'theme') . '
                    </a>
                    <a role="button" title="'. get_string('displaydetails', 'repository') .'" class="fp-vb-details" href="#">
                        ' . $this->pix_icon('fp/view_list_active', '', 'theme') . '
                    </a>
                    <a role="button" title="'. get_string('displaytree', 'repository') .'" class="fp-vb-tree" href="#">
                        ' . $this->pix_icon('fp/view_tree_active', '', 'theme') . '
                    </a>
                </div>
                <div class="fp-clear-left"></div>
            </div>
            <div class="fp-pathbar">
                 <span class="fp-path-folder"><a class="fp-path-folder-name" href="#"></a></span>
            </div>
        </div>
        <div class="fp-content"></div>
    </div>
</div>';
        return $rv;
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
     * Element with class 'fp-content' will be populated with files list;
     * Element with class 'fp-btn-add' will hold onclick event for adding a file (opening filepicker);
     * Element with class 'fp-btn-mkdir' will hold onclick event for adding new folder;
     * Element with class 'fp-btn-download' will hold onclick event for download action;
     *
     * Element with class 'fp-path-folder' is a template for one folder in path toolbar.
     * It will hold mouse click event and will be assigned classes first/last/even/odd respectfully.
     * Parent element will receive class 'empty' when there are no folders to be displayed;
     * The content of subelement with class 'fp-path-folder-name' will be substituted with folder name;
     *
     * Element with class 'fp-viewbar' will have the class 'enabled' or 'disabled' when view mode
     * can be changed or not;
     * Inside element with class 'fp-viewbar' there are expected elements with classes
     * 'fp-vb-icons', 'fp-vb-tree' and 'fp-vb-details'. They will handle onclick events to switch
     * between the view modes, the last clicked element will have the class 'checked';
     *
     * @param form_filemanager $fm
     * @return string
     */
    protected function fm_print_generallayout($fm) {
        global $OUTPUT;
        $options = $fm->options;
        $client_id = $options->client_id;
        $straddfile  = get_string('addfile', 'repository');
        $strmakedir  = get_string('makeafolder', 'moodle');
        $strdownload = get_string('downloadfolder', 'repository');
        $strloading  = get_string('loading', 'repository');
        $strdroptoupload = get_string('droptoupload', 'moodle');
        $icon_progress = $OUTPUT->pix_icon('i/loading_small', $strloading).'';
        $restrictions = $this->fm_print_restrictions($fm);
        $strdndnotsupported = get_string('dndnotsupported_insentence', 'moodle').$OUTPUT->help_icon('dndnotsupported');
        $strdndenabledinbox = get_string('dndenabled_inbox', 'moodle');
        $loading = get_string('loading', 'repository');
        $straddfiletext = get_string('addfiletext', 'repository');
        $strcreatefolder = get_string('createfolder', 'repository');
        $strdownloadallfiles = get_string('downloadallfiles', 'repository');

        $html = '
<div id="filemanager-'.$client_id.'" class="filemanager fm-loading">
    <div class="fp-restrictions">
        '.$restrictions.'
        <span class="dnduploadnotsupported-message"> - '.$strdndnotsupported.' </span>
    </div>
    <div class="fp-navbar">
        <div class="filemanager-toolbar">
            <div class="fp-toolbar">
                <div class="fp-btn-add">
                    <a role="button" title="' . $straddfile . '" href="#">
                        ' . $this->pix_icon('a/add_file', $straddfiletext) . '
                    </a>
                </div>
                <div class="fp-btn-mkdir">
                    <a role="button" title="' . $strmakedir . '" href="#">
                        ' . $this->pix_icon('a/create_folder', $strcreatefolder) . '
                    </a>
                </div>
                <div class="fp-btn-download">
                    <a role="button" title="' . $strdownload . '" href="#">
                        ' . $this->pix_icon('a/download_all', $strdownloadallfiles) . '
                    </a>
                </div>
                <span class="fp-img-downloading">
                    ' . $this->pix_icon('i/loading_small', '') . '
                </span>
            </div>
            <div class="fp-viewbar">
                <a title="'. get_string('displayicons', 'repository') .'" class="fp-vb-icons" href="#">
                    ' . $this->pix_icon('fp/view_icon_active', get_string('displayasicons', 'repository'), 'theme') . '
                </a>
                <a title="'. get_string('displaydetails', 'repository') .'" class="fp-vb-details" href="#">
                    ' . $this->pix_icon('fp/view_list_active', get_string('displayasdetails', 'repository'), 'theme') . '
                </a>
                <a title="'. get_string('displaytree', 'repository') .'" class="fp-vb-tree" href="#">
                    ' . $this->pix_icon('fp/view_tree_active', get_string('displayastree', 'repository'), 'theme') . '
                </a>
            </div>
        </div>
        <div class="fp-pathbar">
            <span class="fp-path-folder"><a class="fp-path-folder-name" href="#"></a></span>
        </div>
    </div>
    <div class="filemanager-loading mdl-align">'.$icon_progress.'</div>
    <div class="filemanager-container" >
        <div class="fm-content-wrapper">
            <div class="fp-content"></div>
            <div class="fm-empty-container">
                <div class="dndupload-message">'.$strdndenabledinbox.'<br/><div class="dndupload-arrow"></div></div>
            </div>
            <div class="dndupload-target">'.$strdroptoupload.'<br/><div class="dndupload-arrow"></div></div>
            <div class="dndupload-progressbars"></div>
            <div class="dndupload-uploadinprogress">'.$icon_progress.'</div>
        </div>
        <div class="filemanager-updating">'.$icon_progress.'</div>
    </div>
</div>';
        return $html;
    }

    /**
     * Returns HTML for default repository searchform to be passed to Filepicker
     *
     * This will be used as contents for search form defined in generallayout template
     * (form with id {TOOLSEARCHID}).
     * Default contents is one text input field with name="s"
     */
    public function repository_default_searchform() {
        $searchinput = \html_writer::label(get_string('searchrepo', 'repository'),
                'reposearch', false, array('class' => 'accesshide'));
        $searchinput .= \html_writer::empty_tag('input', array('type' => 'text',
                'id' => 'reposearch', 'name' => 's', 'value' => get_string('search', 'repository')));
        $str = \html_writer::tag('div', $searchinput, array('class' => "fp-def-search"));

        return $str;
    }

    /**
     * FilePicker JS template for 'Upload file' repository
     *
     * Content to display when user chooses 'Upload file' repository (will be nested inside
     * element with class 'fp-content').
     *
     * Must contain form (enctype="multipart/form-data" method="POST")
     *
     * The elements with the following classnames must be present:
     * 'fp-file', 'fp-saveas', 'fp-setauthor', 'fp-setlicense'. Inside each of them must have
     * one input element (or select in case of fp-setlicense). They may also have labels.
     *
     * Element with class 'fp-upload-btn' will hold onclick event for uploading the file;
     *
     * Please note that some fields may be hidden using CSS if this is part of quickupload form
     *
     * @return string
     */
    protected function fp_js_template_uploadform() {
        $rv = '
<div class="fp-upload-form">
    <div class="fp-content-center">
        <form enctype="multipart/form-data" method="POST" class="form-horizontal">
            <div class="fp-formset">
                <div class="fp-file control-group clearfix">
                    <label class="control-label">'.get_string('attachment', 'repository').'</label>
                    <div class="controls">
                        <input type="file"/>
                    </div>
                </div>
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">'.get_string('saveas', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setauthor control-group clearfix">
                    <label class="control-label">'.get_string('author', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setlicense control-group clearfix">
                    <label class="control-label">'.get_string('chooselicense', 'repository').'</label>
                    <div class="controls">
                        <select ></select>
                    </div>
                </div>
            </div>
        </form>
        <div class="mdl-align">
            <button class="fp-upload-btn btn-primary btn">'.get_string('upload', 'repository').'</button>
        </div>
    </div>
</div> ';
        return $rv;
    }

    /**
     * FilePicker JS template for repository login form including templates for each element type
     *
     * Must contain one <form> element with templates for different input types inside:
     * Elements with classes 'fp-login-popup', 'fp-login-textarea', 'fp-login-select' and
     * 'fp-login-input' are templates for displaying respective login form elements. Inside
     * there must be exactly one element with type <button>, <textarea>, <select> or <input>
     * (i.e. fp-login-popup should have <button>, fp-login-textarea should have <textarea>, etc.);
     * They may also contain the <label> element and it's content will be substituted with
     * label;
     *
     * You can also define elements with classes 'fp-login-checkbox', 'fp-login-text'
     * but if they are not found, 'fp-login-input' will be used;
     *
     * Element with class 'fp-login-radiogroup' will be used for group of radio inputs. Inside
     * it should hava a template for one radio input (with class 'fp-login-radio');
     *
     * Element with class 'fp-login-submit' will hold on click mouse event (form submission). It
     * will be removed if at least one popup element is present;
     *
     * @return string
     */
    protected function fp_js_template_loginform() {
        $rv = '
<div class="fp-login-form">
    <div class="fp-content-center">
        <form class="form-horizontal">
            <div class="fp-formset">
                <div class="fp-login-popup control-group clearfix">
                    <div class="controls fp-popup">
                        <button class="fp-login-popup-but btn-primary btn">'.get_string('login', 'repository').'</button>
                    </div>
                </div>
                <div class="fp-login-textarea control-group clearfix">
                    <div class="controls"><textarea></textarea></div>
                </div>
                <div class="fp-login-select control-group clearfix">
                    <label class="control-label"></label>

                    <div class="controls"><select></select></div>
                </div>';
        $rv .= '
                <div class="fp-login-input control-group clearfix">
                    <label class="control-label"></label>
                    <div class="controls"><input/></div>
                </div>
                <div class="fp-login-radiogroup control-group clearfix">
                    <label class="control-label"></label>
                    <div class="controls fp-login-radio"><input /> <label></label></div>
                </div>
            </div>
            <p><button class="fp-login-submit btn-primary btn">'.get_string('submit', 'repository').'</button></p>
        </form>
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for window appearing to select a file.
     *
     * All content must be enclosed in one element, CSS for this class must define width and
     * height of the window;
     *
     * Thumbnail image will be added as content to the element with class 'fp-thumbnail';
     *
     * Inside the window the elements with the following classnames must be present:
     * 'fp-saveas', 'fp-linktype-2', 'fp-linktype-1', 'fp-linktype-4', 'fp-setauthor',
     * 'fp-setlicense'. Inside each of them must have one input element (or select in case of
     * fp-setlicense). They may also have labels.
     * The elements will be assign with class 'uneditable' and input/select element will become
     * disabled if they are not applicable for the particular file;
     *
     * There may be present elements with classes 'fp-datemodified', 'fp-datecreated', 'fp-size',
     * 'fp-license', 'fp-author', 'fp-dimensions'. They will receive additional class 'fp-unknown'
     * if information is unavailable. If there is information available, the content of embedded
     * element with class 'fp-value' will be substituted with the value;
     *
     * Elements with classes 'fp-select-confirm' and 'fp-select-cancel' will hold corresponding
     * onclick events;
     *
     * When confirm button is pressed and file is being selected, the top element receives
     * additional class 'loading'. It is removed when response from server is received.
     *
     * @return string
     */
    protected function fp_js_template_selectlayout() {
        $rv = '
<div class="file-picker fp-select">
    <div class="fp-select-loading">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
    <form class="form-horizontal">
        <div class="fp-forminset">
                <div class="fp-linktype-2 control-group control-radio clearfix">
                    <label class="control-label control-radio">'.get_string('makefileinternal', 'repository').'</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-linktype-1 control-group control-radio clearfix">
                    <label class="control-label control-radio">'.get_string('makefilelink', 'repository').'</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-linktype-4 control-group control-radio clearfix">
                    <label class="control-label control-radio">'.get_string('makefilereference', 'repository').'</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-linktype-8 control-group control-radio clearfix">
                    <label class="control-label control-radio">'.get_string('makefilecontrolledlink', 'repository').'</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">'.get_string('saveas', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setauthor control-group clearfix">
                    <label class="control-label">'.get_string('author', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setlicense control-group clearfix">
                    <label class="control-label">'.get_string('chooselicense', 'repository').'</label>
                    <div class="controls">
                        <select></select>
                    </div>
                </div>
        </div>
       <div class="fp-select-buttons">
            <button class="fp-select-confirm btn-primary btn">'.get_string('getfile', 'repository').'</button>
            <button class="fp-select-cancel btn-cancel btn">'.get_string('cancel').'</button>
        </div>
    </form>
    <div class="fp-info clearfix">
        <div class="fp-hr"></div>
        <p class="fp-thumbnail"></p>
        <div class="fp-fileinfo">
            <div class="fp-datemodified">'.get_string('lastmodified', 'repository').'<span class="fp-value"></span></div>
            <div class="fp-datecreated">'.get_string('datecreated', 'repository').'<span class="fp-value"></span></div>
            <div class="fp-size">'.get_string('size', 'repository').'<span class="fp-value"></span></div>
            <div class="fp-license">'.get_string('license', 'repository').'<span class="fp-value"></span></div>
            <div class="fp-author">'.get_string('author', 'repository').'<span class="fp-value"></span></div>
            <div class="fp-dimensions">'.get_string('dimensions', 'repository').'<span class="fp-value"></span></div>
        </div>
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists (multiple-file version).
     *
     * Must have one top element, CSS for this element must define width and height of the window;
     *
     * content of element with class 'fp-dlg-text' will be replaced with dialog text;
     * elements with classes 'fp-dlg-butoverwrite', 'fp-dlg-butrename' and 'fp-dlg-butcancel' will
     * hold onclick events;
     *
     * content of element with class 'fp-dlg-butrename' will be substituted with appropriate string
     * (Note that it may have long text)
     *
     * @return string
     */
    protected function fp_js_template_processexistingfilemultiple() {
        $rv = '
<div class="file-picker fp-dlg">
    <p class="fp-dlg-text"></p>
    <a class="fp-dlg-butoverwrite fp-panel-button" href="#">'.get_string('overwrite', 'repository').'</a>
    <a class="fp-dlg-butcancel fp-panel-button" href="#">'.get_string('cancel').'</a>
    <a class="fp-dlg-butrename fp-panel-button" href="#"></a>
    <br/>
    <a class="fp-dlg-butoverwriteall fp-panel-button" href="#">'.get_string('overwriteall', 'repository').'</a>
    <a class="fp-dlg-butrenameall fp-panel-button" href="#">'.get_string('renameall', 'repository').'</a>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists.
     *
     * Must have one top element, CSS for this element must define width and height of the window;
     *
     * content of element with class 'fp-dlg-text' will be replaced with dialog text;
     * elements with classes 'fp-dlg-butoverwrite', 'fp-dlg-butrename',
     * 'fp-dlg-butoverwriteall', 'fp-dlg-butrenameall' and 'fp-dlg-butcancel' will
     * hold onclick events;
     *
     * content of element with class 'fp-dlg-butrename' will be substituted with appropriate string
     * (Note that it may have long text)
     *
     * @return string
     */
    protected function fp_js_template_processexistingfile() {
        $rv = '
<div class="file-picker fp-dlg">
    <p class="fp-dlg-text"></p>
    <div class="fp-dlg-buttons">
        <button class="fp-dlg-butoverwrite btn">'.get_string('overwrite', 'repository').'</button>
        <button class="fp-dlg-butrename btn"></button>
        <button class="fp-dlg-butcancel btn btn-cancel">'.get_string('cancel').'</button>
    </div>
</div>';
        return $rv;
    }
}
