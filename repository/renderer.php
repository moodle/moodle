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
     * Element with class 'fp-path-folder' will contain template for one folder in path toolbar.
     * It will hold mouse click event and will be assigned classes first/last/even/odd respectfully.
     * The content of element with class 'fp-path-folder-name' will be substituted with folder name;
     * Parent element will receive class 'empty' when there are no folders to be displayed;
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
      <div class="{!}fp-toolbar">
        <div class="{!}fp-tb-back"><a>'.get_string('back', 'repository').'</a></div>
        <div class="{!}fp-tb-search">
          <img src="'.$this->pix_url('a/search').'" />
          <form/>
        </div>
        <div class="{!}fp-tb-refresh"><a><img src="'.$this->pix_url('a/refresh').'" />'.get_string('refresh', 'repository').'</a></div>
        <div class="{!}fp-tb-logout"><img src="'.$this->pix_url('a/logout').'" /><a></a></div>
        <div class="{!}fp-tb-manage"><a><img src="'.$this->pix_url('a/setting').'" /> '.get_string('manageurl', 'repository').'</a></div>
        <div class="{!}fp-tb-help"><a><img src="'.$this->pix_url('a/help').'" /> '.get_string('help').'</a></div>
      </div>
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
     * Template for displaying link/loading progress for fetching of the next page
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
    public function js_template_nextpage() {
        $rv = '<div class="{!}fp-nextpage">
  <div class="fp-nextpage-link"><a href="#">'.get_string('more').'</a></div>
  <div class="fp-nextpage-loading">
    <img src="'.$this->pix_url('i/loading').'" />
    <p>'.get_string('loading', 'repository').'</p>
  </div>
</div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
     * Template for window appearing to select a file.
     *
     * All content must be enclosed in an element with class 'fp-select', CSS for this class
     * must define width and height of the window;
     *
     * Thumbnail image will be added as content to the element with class 'fp-thumbnail';
     *
     * Inside the window the elements with the following classnames must be present:
     * 'fp-saveas', 'fp-linkexternal', 'fp-setauthor', 'fp-setlicense'. Inside each of them must have
     * one input element (or select in case of fp-setlicense). They may also have labels.
     * The elements will be assign with class 'uneditable' and input/select element will become
     * disabled if they are not applicable for the particular file;
     *
     * There may be present elements with classes 'fp-datemodified', 'fp-datecreated', 'fp-size',
     * 'fp-license', 'fp-author'. They will receive additional class 'fp-unknown' if information
     * is unavailable. If there is information available, the content of embedded element
     * with class 'fp-value' will be substituted with the value;
     *
     * Elements with classes 'fp-select-confirm' and 'fp-select-cancel' will hold corresponding
     * onclick events;
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
<p class="{!}fp-thumbnail"></p>
<table width="100%">
<tr class="{!}fp-saveas"><td class="mdl-right"><label>'.get_string('saveas', 'repository').'</label>:</td>
<td class="mdl-left"><input type="text"/></td></tr>
<tr class="{!}fp-linkexternal"><td></td>
<td class="mdl-left"><input type="checkbox"/><label>'.get_string('linkexternal', 'repository').'</label></td></tr>
<tr class="{!}fp-setauthor"><td class="mdl-right"><label>'.get_string('author', 'repository').'</label>:</td>
<td class="mdl-left"><input type="text" /></td></tr>
<tr class="{!}fp-setlicense"><td class="mdl-right"><label>'.get_string('chooselicense', 'repository').'</label>:</td>
<td class="mdl-left"><select></select></td></tr>
</table>
<p><button class="{!}fp-select-confirm" >'.get_string('getfile', 'repository').'</button>
<button class="{!}fp-select-cancel" >'.get_string('cancel').'</button></p>
</form>
<div class="{!}fp-datemodified">'.get_string('lastmodified', 'moodle').': <span class="fp-value"/></div>
<div class="{!}fp-datecreated">'.get_string('datecreated', 'repository').': <span class="fp-value"/></div>
<div class="{!}fp-size">'.get_string('size', 'repository').': <span class="fp-value"/></div>
<div class="{!}fp-license">'.get_string('license', 'moodle').': <span class="fp-value"/></div>
<div class="{!}fp-author">'.get_string('author', 'repository').': <span class="fp-value"/></div>
<div class="{!}fp-dimensions">'.get_string('dimensions', 'repository').': <span class="fp-value"/></div>
</div>';
        return preg_replace('/\{\!\}/', '', $rv);
    }

    /**
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
    public function js_template_uploadform() {
        $rv = '<div class="fp-upload-form mdl-align">
<form enctype="multipart/form-data" method="POST">
  <table width="100%">
    <tr class="{!}fp-file">
      <td class="mdl-right"><label>'.get_string('attachment', 'repository').'</label>:</td>
      <td class="mdl-left"><input type="file"/></td>
    </tr>
    <tr class="{!}fp-saveas">
      <td class="mdl-right"><label>'.get_string('saveas', 'repository').'</label>:</td>
      <td class="mdl-left"><input type="text"/></td>
    </tr>
    <tr class="{!}fp-setauthor">
      <td class="mdl-right"><label>'.get_string('author', 'repository').'</label>:</td>
      <td class="mdl-left"><input type="text"/></td>
    </tr>
    <tr class="{!}fp-setlicense">
      <td class="mdl-right"><label>'.get_string('chooselicense', 'repository').'</label>:</td>
      <td class="mdl-left"><select/></td>
    </tr>
  </table>
</form>
<div><button class="{!}fp-upload-btn">'.get_string('upload', 'repository').'</button></div>
</div> ';
        return preg_replace('/\{\!\}/', '', $rv);
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
