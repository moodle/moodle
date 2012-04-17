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
 * Renderer for filepicker and base repositories output.
 *
 * @package    core_repository
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * The core repository renderer
 *
 * Can be retrieved with the following:
 * $renderer = $PAGE->get_renderer('core', 'repository');
 */
class core_repository_renderer extends plugin_renderer_base {

    /**
     * Template for FilePicker with general layout (not QuickUpload).
     *
     * Must have one top element containing everything else (recommended <div class="file-picker">),
     * CSS for this element must define width and height of the filepicker window;
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
     * Element with class 'fp-path-folder' will contain template for one folder in path toolbar.
     * It will hold mouse click event and will be assigned classes first/last/even/odd respectfully.
     * The content of element with class 'fp-path-folder-name' will be substituted with folder name;
     * Parent element will receive class 'empty' when there are no folders to be displayed;
     *
     * Element with id {TOOLBARID} will have class 'empty' if all 'Search', 'Login', 'Refresh' and
     * 'Logout' are unavailable for this repo;
     * Element with id {TOOLBACKID} will hold the click event for going back to Login form;
     * Element (<form>!) with id {TOOLSEARCHID} is responsible for search inside the repo (if
     * repository provides this functionality), it must have one input-text field;
     * Element with id {TOOLREFRESHID} will hold the click event for refreshing current view;
     * Element with id {TOOLLOGOUTID} will hold the click event for logout (the content of the
     * element will be substituted with logout string provided by repository or default
     * M.str.repository.logout if not specified);
     * Element with id {TOOLMANAGEID} will hold the click event for opening new window to manage
     * repository files;
     * Element with id {TOOLHELPID} will hold the click event for opening new window for help;
     * Elements with ids {TOOLBACKID}, {TOOLSEARCHID}, {TOOLREFRESHID}, {TOOLLOGOUTID},
     * {TOOLMANAGEID}, {TOOLHELPID} and also optional elements width ids wrap-{TOOLBACKID},
     * wrap-{TOOLSEARCHID}, wrap-{TOOLREFRESHID}, wrap-{TOOLLOGOUTID}, wrap-{TOOLMANAGEID} and
     * wrap-{TOOLHELPID} will have class 'enabled' or 'disabled' when applicable or not for
     * the current repository view;
     *
     * @return string
     */
    public function js_template_generallayout() {
        $rv = '
<div class="file-picker fp-generallayout">
  <div>
    <div class="{!}fp-viewbar" style="float:none;">
       <span class=""><button class="{!}fp-vb-icons">'.get_string('iconview', 'repository').'</button></span>
       <span class=""><button class="{!}fp-vb-tree">'.get_string('listview', 'repository').'</button></span>
       <span class=""><button class="{!}fp-vb-details">'.get_string('detailview', 'repository').'</button></span>
    </div>
  </div>
  <div style="vertical-align:top;">
    <div style="width:200px;height:400px;display:inline-block;overflow:auto;">
      <ul class="fp-list">
        <li class="{!}fp-repo"><img class="{!}fp-repo-icon" width="16" height="16" />&nbsp;<span class="{!}fp-repo-name"></span></li>
      </ul>
    </div>
    <div style="width:480px;height:400px;display:inline-block;vertical-align:top;">
      <div class="fp-toolbar" id="{TOOLBARID}" style="background:yellow">
        <div id="wrap-{TOOLBACKID}"><a id="{TOOLBACKID}">'.get_string('back', 'repository').'</a></div>
        <div id="wrap-{TOOLSEARCHID}">
          <img src="'.$this->pix_url('a/search').'" />
          <form id="{TOOLSEARCHID}"></form>
        </div>
        <div id="wrap-{TOOLREFRESHID}"><a id="{TOOLREFRESHID}"><img src="'.$this->pix_url('a/refresh').'" />'.get_string('refresh', 'repository').'</a></div>
        <div id="wrap-{TOOLLOGOUTID}"><img src="'.$this->pix_url('a/logout').'" /><a id="{TOOLLOGOUTID}"></a></div>
        <div id="wrap-{TOOLMANAGEID}"><a id="{TOOLMANAGEID}"><img src="'.$this->pix_url('a/setting').'" /> '.get_string('manageurl', 'repository').'</a></div>
        <div id="wrap-{TOOLHELPID}"><a id="{TOOLHELPID}"><img src="'.$this->pix_url('a/help').'" /> '.get_string('help').'</a></div>
      </div>
      <div class="{!}fp-paging" style="background:pink"></div>
      <div class="fp-pathbar" style="background:#ddffdd">
        <span class="{!}fp-path-folder"><a class="{!}fp-path-folder-name"></a><span>/</span></span>
      </div>
      <div class="{!}fp-content"></div>
    </div>
  </div>
</div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Template for displaying list of files in 'icon view' mode.
     *
     * Element with class 'fp-file' is a template for displaying one file and indicates a place
     * where files shall be output. It also will hold mouse events (click, over, out, etc.);
     *
     * the element with class 'fp-thumbnail' will be resized to the repository thumbnail size
     * (both width and height, unless min-width and/or min-height is set in CSS) and the content of
     * an element will be replaced with an appropriate img;
     *
     * the width of element with class 'fp-filename' will be set to the repository thumbnail width
     * (unless min-width is set in css) and the content of an element will be replaced with filename
     * supplied by repository;
     *
     * @return string
     */
    public function js_template_iconview() {
        $rv = '<div class="fp-iconview">
<div class="{!}fp-file">
    <div class="{!}fp-thumbnail"></div>
    <div class="{!}fp-filename"></div>
</div>
            </div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Template for displaying file name in 'table view' and 'tree view' modes.
     *
     * content of the element with class 'fp-icon' will be replaced with an appropriate img;
     *
     * content of element with class 'fp-filename' will be replaced with filename supplied by
     * repository;
     *
     * Note that tree view and table view are the YUI widgets and therefore there are no
     * other templates. The widgets will be wrapped in <div> with class fp-treeview or
     * fp-tableview (respectfully).
     *
     * @return string
     */
    public function js_template_listfilename() {
        $rv = '<span class="{!}fp-icon"></span> <span class="{!}fp-filename"></span>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Template for window appearing to select a file.
     *
     * All content must be enclosed in an element with class 'fp-select', CSS for this class
     * must define width and height of the window;
     *
     * Image will be added as content to the element with id {IMGID};
     *
     * Inside the window <form> element must be present and contain followng input fields:
     *   {NEWNAMEID} (input-text)
     *   {LINKEXTID} (input-checkbox)
     *   {AUTHORID} (input-text)
     *   {LICENSEID} (select, will be populated with available options)
     *   {BUTCONFIRMID} (will hold onclick event)
     *   {BUTCANCELID} (will hold onclick event)
     *
     * Elements with ids 'wrap-{LINKEXTID}', 'wrap-{AUTHORID}' and 'wrap-{LICENSEID}' may be
     * assigned with class 'uneditable' if not applicable for particular repository;
     *
     * When confirm button is pressed and file is being selected, the top element receives
     * additional class 'loading'. It is removed when response from server is received.
     *
     * @return string
     */
    public function js_template_selectlayout() {
        $rv = '<div class="{!}fp-select">
<div class="fp-select-loading">
<img src="'.$this->pix_url('i/loading').'" />
<p>'.get_string('loading', 'repository').'</p>
</div>
<form>
<p id="{IMGID}"></p>
<table width="100%">
<tr><td class="mdl-right"><label for="{NEWNAMEID}">'.get_string('saveas', 'repository').'</label>:</td>
<td class="mdl-left"><input type="text" id="{NEWNAMEID}" /></td></tr>
<tr id="wrap-{LINKEXTID}"><td></td>
<td class="mdl-left"><input type="checkbox" id="{LINKEXTID}" value="" /><label for="{LINKEXTID}">'.get_string('linkexternal', 'repository').'</label></td></tr>
<tr id="wrap-{AUTHORID}"><td class="mdl-right"><label for="{AUTHORID}">'.get_string('author', 'repository').'</label>:</td>
<td class="mdl-left"><input id="{AUTHORID}" type="text" /></td></tr>
<tr id="wrap-{LICENSEID}"><td class="mdl-right"><label for="{LICENSEID}">'.get_string('chooselicense', 'repository').'</label>:</td>
<td class="mdl-left"><select id="{LICENSEID}"></select></td></tr>
</table>
<p><button id="{BUTCONFIRMID}" >'.get_string('getfile', 'repository').'</button>
<button id="{BUTCANCELID}" >'.get_string('cancel').'</button></p>
</form></div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Content to display when user chooses 'Upload file' repository (will be nested inside
     * element with class 'fp-content').
     *
     * Must contain form (enctype="multipart/form-data" method="POST") with id {UPLOADFORMID}
     * The elements with the following ids are obligatory:
     *   {INPUTFILEID} (input-file)
     *   {NEWNAMEID} (input-text)
     *   {AUTHORID} (input-text)
     *   {LICENSEID} (select, will be populated with available options)
     *   {BUTUPLOADID} (any element type, will hold onclick event)
     *
     * Please note that some fields may be hidden using CSS if this is part of quickupload form
     *
     * @return string
     */
    public function js_template_uploadform() {
        return '<div class="fp-upload-form mdl-align">
<form id="{UPLOADFORMID}" enctype="multipart/form-data" method="POST">
  <table width="100%">
    <tr>
      <td class="mdl-right"><label for="{INPUTFILEID}">'.get_string('attachment', 'repository').'</label>:</td>
      <td class="mdl-left"><input type="file" id="{INPUTFILEID}" /></td>
    </tr>
    <tr>
      <td class="mdl-right"><label for="{NEWNAMEID}">'.get_string('saveas', 'repository').'</label>:</td>
      <td class="mdl-left"><input type="text" id="{NEWNAMEID}" /></td>
    </tr>
    <tr>
      <td class="mdl-right"><label for="{AUTHORID}">'.get_string('author', 'repository').'</label>:</td>
      <td class="mdl-left"><input type="text" id="{AUTHORID}" /></td>
    </tr>
    <tr>
      <td class="mdl-right"><label for="{LICENSEID}">'.get_string('chooselicense', 'repository').'</label>:</td>
      <td class="mdl-left"><select id="{LICENSEID}" /></td>
    </tr>
  </table>
</form>
<div class="fp-upload-btn"><button id="{BUTUPLOADID}">'.get_string('upload', 'repository').'</button></div>
</div> ';
    }

    /**
     * Content to display during loading process in filepicker (inside element with class 'fp-content').
     *
     * @return string
     */
    public function js_template_loading() {
        return '<div style="text-align:center">
<img src="'.$this->pix_url('i/loading').'" />
<p>'.get_string('loading', 'repository').'</p>
</div>';
    }

    /**
     * Template for error displayed in filepicker (inside element with class 'fp-content').
     *
     * must have element with class 'fp-error', its content will be replaced with error text
     * and the error code will be assigned as additional class to this element
     * used errors: invalidjson, nofilesavailable, norepositoriesavailable
     *
     * @return string
     */
    public function js_template_error() {
        $rv = '<div class="{!}fp-error" />';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Template for error/info message displayed as a separate popup window.
     *
     * Must be wrapped in an element with class 'fp-msg', CSS for this element must define
     * width and height of the window. It will be assigned with an additional class 'fp-msg-error'
     * or 'fp-msg-info' depending on message type;
     *
     * content of element with class 'fp-msg-text' will be replaced with error/info text;
     *
     * element with class 'fp-msg-butok' will hold onclick event
     *
     * @return string
     */
    public function js_template_message() {
        $rv = '<div class="{!}fp-msg">
                    <div class="{!}fp-msg-text"></div>
                    <div><button class="{!}fp-msg-butok">'.get_string('ok').'</button></div>
                </div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Template for popup dialogue window asking for action when file with the same name already exists.
     *
     * Must be wrapped in an element with class 'fp-dlg', CSS for this element must define width
     * and height of the window;
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
    public function js_template_processexistingfile() {
        $rv = '<div class="{!}fp-dlg"><div class="{!}fp-dlg-text"></div>
<div class="fp-dlg-but"><button class="{!}fp-dlg-butoverwrite" >'.get_string('overwrite', 'repository').'</button></div>
<div class="fp-dlg-but"><button class="{!}fp-dlg-butrename" /></div>
<div class="fp-dlg-but"><button class="{!}fp-dlg-butcancel" >'.get_string('cancel').'</button></div>
</div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Template for repository login form including templates for each element type
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
    public function js_template_loginform() {
        $rv = '
<div class="fp-login-form">
  <form>
    <table width="100%">
      <tr class="{!}fp-login-popup">
        <td colspan="2">
          <label>'.get_string('popup', 'repository').'</label>
          <p class="fp-popup"><button class="{!}fp-login-popup-but">'.get_string('login', 'repository').'</button></p>
        </td>
      </tr>
      <tr class="{!}fp-login-textarea">
        <td colspan="2"><p><textarea></textarea></p></td>
      </tr>
      <tr class="{!}fp-login-select">
        <td align="right"><label></label></td>
        <td align="left"><select></select></td>
      </tr>
      <tr class="{!}fp-login-input">
        <td align="right" width="30%" valign="center"><label /></td>
        <td align="left"><input/></td>
      </tr>
      <tr class="{!}fp-login-radiogroup">
        <td align="right" width="30%" valign="top"><label /></td>
        <td align="left" valign="top">
          <p class="{!}fp-login-radio"><input /> <label /></p>
        </td>
      </tr>
    </table>
    <p><button class="{!}fp-login-submit">'.get_string('submit', 'repository').'</button></p>
  </form>
</div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Returns all Javascript templates as an array.
     *
     * @return array
     */
    public function filepicker_templates() {
        $class_methods = get_class_methods($this);
        $templates = array();
        foreach ($class_methods as $method_name) {
            if (preg_match('/^js_template_(.*)$/', $method_name, $matches))
            $templates[$matches[1]] = $this->$method_name();
        }
        return $templates;
    }

    /**
     * Outputs HTML for default repository searchform.
     *
     * This will be used as contents for search form defined in generallayout template
     * (form with id {TOOLSEARCHID}).
     * Default contents is one text input field with name="s"
     */
    public function repository_default_searchform() {
        $str = '<label>'.get_string('keyword', 'repository').': </label><br/><input name="s" value="" /><br/>';
        return $str;
    }
}
