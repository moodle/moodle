<?php
    include("../../../config.php");
    require_once($CFG->dirroot.'/lib/languages.php');

    $id            = optional_param('id', SITEID, PARAM_INT);
    $httpsrequired = optional_param('httpsrequired', 0, PARAM_BOOL); //flag indicating editor on page with required https

    require_course_login($id);

    $lastmodified = filemtime("htmlarea.php");
    $lifetime = 1800;

    // Commenting this out since it's creating problems
    // where solution seem to be hard to find...
    // http://moodle.org/mod/forum/discuss.php?d=34376
    //if ( function_exists('ob_gzhandler') ) {
    //    ob_start("ob_gzhandler");
    //}

    header("Content-type: application/x-javascript; charset: utf-8");  // Correct MIME type
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
    header("Cache-control: max_age = $lifetime");
    header("Pragma: ");

    $lang = current_language();

    if (empty($lang)) {
        $lang = "en";
    }

    if ($httpsrequired or (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off')) {
        $url = preg_replace('|https?://[^/]+|', '', $CFG->wwwroot).'/lib/editor/htmlarea/';
    } else {
        $url = $CFG->wwwroot.'/lib/editor/htmlarea/';
    }

    $strheading = get_string("heading", "editor");
    $strnormal = get_string("normal", "editor");
    $straddress = get_string("address", "editor");
    $strpreformatted = get_string("preformatted", "editor");
    $strlang = get_string('lang', 'editor');
    $strmulti = get_string('multi', 'editor');
?>

// htmlArea v3.0 - Copyright (c) 2002, 2003 interactivetools.com, inc.
// This copyright notice MUST stay intact for use (see license.txt).
//
// Portions (c) dynarch.com, 2003-2004
//
// A free WYSIWYG editor replacement for <textarea> fields.
// For full source code and docs, visit http://www.interactivetools.com/
//
// Version 3.0 developed by Mihai Bazon.
//   http://dynarch.com/mishoo
//
// $Id$

if (typeof _editor_url == "string") {
    // Leave exactly one backslash at the end of _editor_url
    _editor_url = _editor_url.replace(/\x2f*$/, '/');
} else {
    //alert("WARNING: _editor_url is not set!  You should set this variable to the editor files path; it should preferably be an absolute path, like in '/htmlarea', but it can be relative if you prefer.  Further we will try to load the editor files correctly but we'll probably fail.");
    _editor_url = '<?php echo $url; ?>';// we need relative path to site root for editor in pages wit hrequired https
}

// make sure we have a language
if (typeof _editor_lang == "string") {
    _editor_lang = "en"; // should always be english in moodle.
} else {
    _editor_lang = "en";
}

// Creates a new HTMLArea object.  Tries to replace the textarea with the given
// ID with it.
function HTMLArea(textarea, config) {
    if (HTMLArea.checkSupportedBrowser()) {
        if (typeof config == "undefined") {
            this.config = new HTMLArea.Config();
        } else {
            this.config = config;
        }
        this._htmlArea = null;
        this._textArea = textarea;
        this._editMode = "wysiwyg";
        this.plugins = {};
        this._timerToolbar = null;
        this._timerUndo = null;
        this._undoQueue = new Array(this.config.undoSteps);
        this._undoPos = -1;
        this._customUndo = true;
        this._mdoc = document; // cache the document, we need it in plugins
        this.doctype = '';
        this.dropdowns = [];   // Array of select elements in the toolbar
    }
};

// load some scripts
(function() {
    var scripts = HTMLArea._scripts = [ _editor_url + "htmlarea.js",
                        _editor_url + "dialog.js",
                        _editor_url + "popupwin.js" ];
    var head = document.getElementsByTagName("head")[0];
    // start from 1, htmlarea.js is already loaded
    for (var i = 1; i < scripts.length; ++i) {
        var script = document.createElement("script");
        script.src = scripts[i];
        head.appendChild(script);
    }
})();

// cache some regexps
HTMLArea.RE_tagName = /(<\/|<)\s*([^ \t\n>]+)/ig;
HTMLArea.RE_doctype = /(<!doctype((.|\n)*?)>)\n?/i;
HTMLArea.RE_head    = /<head>((.|\n)*?)<\/head>/i;
HTMLArea.RE_body    = /<body>((.|\n)*?)<\/body>/i;
HTMLArea.RE_blocktag = /^(h1|h2|h3|h4|h5|h6|p|address|pre)$/i;
HTMLArea.RE_junktag = /^\/($|\/)/;
// Hopefully a complete list of tags that MSIEs parser will consider
// as possible content tags. Retrieved from
// http://www.echoecho.com/htmlreference.htm
HTMLArea.RE_msietag  = /^\/?(a|abbr|acronym|address|applet|area|b|base|basefont|bdo|bgsound|big|blink|blockquote|body|br|button|caption|center|cite|code|col|colgroup|comment|dd|del|dfn|dir|div|dl|dt|em|embed|fieldset|font|form|frame|frameset|h1|h2|h3|h4|h5|h6|head|hr|html|i|iframe|ilayer|img|input|ins|isindex|kbd|keygen|label|layer|legend|li|link|map|marquee|menu|meta|multicol|nobr|noembed|noframes|nolayer|noscript|object|ol|optgroup|option|p|param|plaintext|pre|q|s|samp|script|select|server|small|spacer|span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|title|tr|tt|u|ul|var)$/i

HTMLArea.Config = function () {
    this.version = "3.0";

    this.width = "auto";
    this.height = "auto";

    // enable creation of a status bar?
    this.statusBar = true;

    // maximum size of the undo queue
    this.undoSteps = 20;

    // the time interval at which undo samples are taken
    this.undoTimeout = 500; // 1/2 sec.

    // the next parameter specifies whether the toolbar should be included
    // in the size or not.
    this.sizeIncludesToolbar = true;

    // if true then HTMLArea will retrieve the full HTML, starting with the
    // <HTML> tag.
    this.fullPage = false;

    // style included in the iframe document
    this.pageStyle = "body { background-color: #fff; font-family: 'Times New Roman', Times; } \n .lang { background-color: #dee; }";

    // set to true if you want Word code to be cleaned upon Paste
    this.killWordOnPaste = true;

    // BaseURL included in the iframe document
    this.baseURL = document.baseURI || document.URL;
    if (this.baseURL && this.baseURL.match(/(.*)\/([^\/]+)/))
        this.baseURL = RegExp.$1 + "/";

    // URL-s
    this.imgURL = "images/";
    this.popupURL = "popups/";

    this.toolbar = [
        [ "fontname", "space",
          "fontsize", "space",
          "formatblock", "space",
          "language", "space",
          "bold", "italic", "underline", "strikethrough", "separator",
          "subscript", "superscript", "separator",
          "clean", "separator", "undo", "redo" ],

        [ "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
          "lefttoright", "righttoleft", "separator",
          "insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
          "forecolor", "hilitecolor", "separator",
          "inserthorizontalrule", "createanchor", "createlink", "unlink", "nolink", "separator",
          "insertimage", "inserttable",
          "insertsmile", "insertchar", "search_replace",
          <?php if (!empty($CFG->aspellpath) && file_exists($CFG->aspellpath) && !empty($CFG->editorspelling)) {
              echo '"separator","spellcheck",';
            } ?>
          "separator", "htmlmode", "separator", "popupeditor"]
    ];

    this.fontname = {
        "Arial":       'arial,helvetica,sans-serif',
        "Courier New":     'courier new,courier,monospace',
        "Georgia":     'georgia,times new roman,times,serif',
        "Tahoma":      'tahoma,arial,helvetica,sans-serif',
        "Times New Roman": 'times new roman,times,serif',
        "Verdana":     'verdana,arial,helvetica,sans-serif',
        "Impact":           'impact',
        "WingDings":       'wingdings'
    };

    this.fontsize = {
        "1 (8 pt)":  "1",
        "2 (10 pt)": "2",
        "3 (12 pt)": "3",
        "4 (14 pt)": "4",
        "5 (18 pt)": "5",
        "6 (24 pt)": "6",
        "7 (36 pt)": "7"
    };

    this.formatblock = {
        "":"",
        "<?php echo $strheading ?> 1": "h1",
        "<?php echo $strheading ?> 2": "h2",
        "<?php echo $strheading ?> 3": "h3",
        "<?php echo $strheading ?> 4": "h4",
        "<?php echo $strheading ?> 5": "h5",
        "<?php echo $strheading ?> 6": "h6",
        "<?php echo $strnormal ?>": "p",
        "<?php echo $straddress ?>": "address",
        "<?php echo $strpreformatted ?>": "pre"
    };

    this.language = {
        "<?php echo $strlang; ?>":"",
        <?php
        $strlangarray = '';
        foreach ($LANGUAGES as $key => $name) {
            $key = str_replace('_', '-', $key);
            $strlangarray .= '"'.$key.'": "'.$key.'",';
        }
        $strlangarray .= '"'.$strmulti.'": "multi",';

        foreach ($LANGUAGES as $key => $name) {
            $key = str_replace('_', '-', $key);
            $strlangarray .= '"'.$key.' ": "'.$key.'_ML",';
        }
        $strlangarray = substr($strlangarray, 0, -1);
        echo $strlangarray;
        ?>
    };

    this.customSelects = {};

    function cut_copy_paste(e, cmd, obj) {
        e.execCommand(cmd);
    };

    this.btnList = {
        bold: [ "Bold", "ed_format_bold.gif", false, function(e) {e.execCommand("bold");} ],
        italic: [ "Italic", "ed_format_italic.gif", false, function(e) {e.execCommand("italic");} ],
        underline: [ "Underline", "ed_format_underline.gif", false, function(e) {e.execCommand("underline");} ],
        strikethrough: [ "Strikethrough", "ed_format_strike.gif", false, function(e) {e.execCommand("strikethrough");} ],
        subscript: [ "Subscript", "ed_format_sub.gif", false, function(e) {e.execCommand("subscript");} ],
        superscript: [ "Superscript", "ed_format_sup.gif", false, function(e) {e.execCommand("superscript");} ],
        justifyleft: [ "Justify Left", "ed_align_left.gif", false, function(e) {e.execCommand("justifyleft");} ],
        justifycenter: [ "Justify Center", "ed_align_center.gif", false, function(e) {e.execCommand("justifycenter");} ],
        justifyright: [ "Justify Right", "ed_align_right.gif", false, function(e) {e.execCommand("justifyright");} ],
        justifyfull: [ "Justify Full", "ed_align_justify.gif", false, function(e) {e.execCommand("justifyfull");} ],
        insertorderedlist: [ "Ordered List", "ed_list_num.gif", false, function(e) {e.execCommand("insertorderedlist");} ],
        insertunorderedlist: [ "Bulleted List", "ed_list_bullet.gif", false, function(e) {e.execCommand("insertunorderedlist");} ],
        outdent: [ "Decrease Indent", "ed_indent_less.gif", false, function(e) {e.execCommand("outdent");} ],
        indent: [ "Increase Indent", "ed_indent_more.gif", false, function(e) {e.execCommand("indent");} ],
        forecolor: [ "Font Color", "ed_color_fg.gif", false, function(e) {e.execCommand("forecolor");} ],
        hilitecolor: [ "Background Color", "ed_color_bg.gif", false, function(e) {e.execCommand("hilitecolor");} ],
        inserthorizontalrule: [ "Horizontal Rule", "ed_hr.gif", false, function(e) {e.execCommand("inserthorizontalrule");} ],
        createanchor: [ "Create anchor", "ed_anchor.gif", false, function(e) {e.execCommand("createanchor", true);} ],
        createlink: [ "Insert Web Link", "ed_link.gif", false, function(e) {e.execCommand("createlink", true);} ],
        unlink: [ "Remove Link", "ed_unlink.gif", false, function(e) {e.execCommand("unlink");} ],
        nolink: [ "No link", "ed_nolink.gif", false, function(e) {e.execCommand("nolink");} ],
        insertimage: [ "Insert/Modify Image", "ed_image.gif", false, function(e) {e.execCommand("insertimage");} ],
        inserttable: [ "Insert Table", "insert_table.gif", false, function(e) {e.execCommand("inserttable");} ],
        htmlmode: [ "Toggle HTML Source", "ed_html.gif", true, function(e) {e.execCommand("htmlmode");} ],
        popupeditor: [ "Enlarge Editor", "fullscreen_maximize.gif", true, function(e) {e.execCommand("popupeditor");} ],
        about: [ "About this editor", "ed_about.gif", true, function(e) {e.execCommand("about");} ],
        showhelp: [ "Help using editor", "ed_help.gif", true, function(e) {e.execCommand("showhelp");} ],
        undo: [ "Undoes your last action", "ed_undo.gif", false, function(e) {e.execCommand("undo");} ],
        redo: [ "Redoes your last action", "ed_redo.gif", false, function(e) {e.execCommand("redo");} ],
        clean: [ "Clean Word HTML", "ed_wordclean.gif", false, function(e) {e.execCommand("killword"); }],
        lefttoright: [ "Direction left to right", "ed_left_to_right.gif", false, function(e) {e.execCommand("lefttoright");} ],
        righttoleft: [ "Direction right to left", "ed_right_to_left.gif", false, function(e) {e.execCommand("righttoleft");} ],
        <?php if (!empty($CFG->aspellpath) && file_exists($CFG->aspellpath) && !empty($CFG->editorspelling)) {
            echo 'spellcheck: ["Spell-check", "spell-check.gif", false, spellClickHandler ],'."\n";
        }?>
        insertsmile: ["Insert Smiley", "em.icon.smile.gif", false, function(e) {e.execCommand("insertsmile");} ],
        insertchar: [ "Insert Char", "icon_ins_char.gif", false, function(e) {e.execCommand("insertchar");} ],
        search_replace: [ "Search and replace", "ed_replace.gif", false, function(e) {e.execCommand("searchandreplace");} ]
    };

    // initialize tooltips from the I18N module and generate correct image path
    for (var i in this.btnList) {
        var btn = this.btnList[i];
        btn[1] = _editor_url + this.imgURL + btn[1];
        if (typeof HTMLArea.I18N.tooltips[i] != "undefined") {
            btn[0] = HTMLArea.I18N.tooltips[i];
        }
    }
};

HTMLArea.Config.prototype.registerButton = function(id, tooltip, image, textMode, action, context) {
    var the_id;
    if (typeof id == "string") {
        the_id = id;
    } else if (typeof id == "object") {
        the_id = id.id;
    } else {
        alert("ERROR [HTMLArea.Config::registerButton]:\ninvalid arguments");
        return false;
    }
    // check for existing id
    if (typeof this.customSelects[the_id] != "undefined") {
        // alert("WARNING [HTMLArea.Config::registerDropdown]:\nA dropdown with the same ID already exists.");
    }
    if (typeof this.btnList[the_id] != "undefined") {
        // alert("WARNING [HTMLArea.Config::registerDropdown]:\nA button with the same ID already exists.");
    }
    switch (typeof id) {
        case "string": this.btnList[id] = [ tooltip, image, textMode, action, context ]; break;
        case "object": this.btnList[id.id] = [ id.tooltip, id.image, id.textMode, id.action, id.context ]; break;
    }
};

HTMLArea.Config.prototype.registerDropdown = function(object) {
    // check for existing id
    if (typeof this.customSelects[object.id] != "undefined") {
        // alert("WARNING [HTMLArea.Config::registerDropdown]:\nA dropdown with the same ID already exists.");
    }
    if (typeof this.btnList[object.id] != "undefined") {
        // alert("WARNING [HTMLArea.Config::registerDropdown]:\nA button with the same ID already exists.");
    }
    this.customSelects[object.id] = object;
};

HTMLArea.Config.prototype.hideSomeButtons = function(remove) {
    var toolbar = this.toolbar;
    for (var i in toolbar) {
        var line = toolbar[i];
        for (var j = line.length; --j >= 0; ) {
            if (remove.indexOf(" " + line[j] + " ") >= 0) {
                var len = 1;
                if (/separator|space/.test(line[j + 1])) {
                    len = 2;
                }
                line.splice(j, len);
            }
        }
    }
};

/** Helper function: replace all TEXTAREA-s in the document with HTMLArea-s. */
HTMLArea.replaceAll = function(config) {
    var tas = document.getElementsByTagName("textarea");
    for (var i = tas.length; i > 0; (new HTMLArea(tas[--i], config)).generate());
};

/** Helper function: replaces the TEXTAREA with the given ID with HTMLArea. */
HTMLArea.replace = function(id, config) {
    var ta = HTMLArea.getElementById("textarea", id);
    return ta ? (new HTMLArea(ta, config)).generate() : null;
};

// Creates the toolbar and appends it to the _htmlarea
HTMLArea.prototype._createToolbar = function () {
    var editor = this;  // to access this in nested functions

    var toolbar = document.createElement("div");
    this._toolbar = toolbar;
    toolbar.className = "toolbar";
    toolbar.unselectable = "1";
    var tb_row = null;
    var tb_objects = new Object();
    this._toolbarObjects = tb_objects;

    // creates a new line in the toolbar
    function newLine() {
        var table = document.createElement("table");
        table.border = "0px";
        table.cellSpacing = "0px";
        table.cellPadding = "0px";
        toolbar.appendChild(table);
        // TBODY is required for IE, otherwise you don't see anything
        // in the TABLE.
        var tb_body = document.createElement("tbody");
        table.appendChild(tb_body);
        tb_row = document.createElement("tr");
        tb_body.appendChild(tb_row);
    }; // END of function: newLine
    // init first line
    newLine();

    function setButtonStatus(id, newval) {
        var oldval = this[id];
        var el = this.element;
        if (oldval != newval) {
            switch (id) {
                case "enabled":
                if (newval) {
                    HTMLArea._removeClass(el, "buttonDisabled");
                    el.disabled = false;
                } else {
                    HTMLArea._addClass(el, "buttonDisabled");
                    el.disabled = true;
                }
                break;
                case "active":
                if (newval) {
                    HTMLArea._addClass(el, "buttonPressed");
                } else {
                    HTMLArea._removeClass(el, "buttonPressed");
                }
                break;
            }
            this[id] = newval;
        }
    }; // END of function: setButtonStatus

    function createSelect(txt) {
        var options = null;
        var el = null;
        var cmd = null;
        var customSelects = editor.config.customSelects;
        var context = null;
        switch (txt) {
            case "fontsize":
            case "fontname":
            case "formatblock":
            case "language":
            options = editor.config[txt];
            cmd = txt;
            break;
            default:
            // try to fetch it from the list of registered selects
            cmd = txt;
            var dropdown = customSelects[cmd];
            if (typeof dropdown != "undefined") {
                options = dropdown.options;
                context = dropdown.context;
            } else {
                alert("ERROR [createSelect]:\nCan't find the requested dropdown definition");
            }
            break;
        }
        if (options) {
            el = document.createElement("select");
            var obj = {
                name    : txt, // field name
                element : el,   // the UI element (SELECT)
                enabled : true, // is it enabled?
                text    : false, // enabled in text mode?
                cmd : cmd, // command ID
                state   : setButtonStatus, // for changing state
                context : context
            };
            tb_objects[txt] = obj;
            for (var i in options) {
                var op = document.createElement("option");
                op.appendChild(document.createTextNode(i));
                op.value = options[i];
                el.appendChild(op);
            }
            HTMLArea._addEvent(el, "change", function () {
                editor._comboSelected(el, txt);
            });
        }
        editor.dropdowns[txt] = el;  // Keep track of the element for keyboard
                                     // access later.
        return el;
    }; // END of function: createSelect

    // appends a new button to toolbar
    function createButton(txt) {
        // the element that will be created
        var el = null;
        var btn = null;
        switch (txt) {
            case "separator":
            el = document.createElement("div");
            el.className = "separator";
            break;
            case "space":
            el = document.createElement("div");
            el.className = "space";
            break;
            case "linebreak":
            newLine();
            return false;
            case "textindicator":
            el = document.createElement("div");
            el.appendChild(document.createTextNode("A"));
            el.className = "indicator";
            el.title = HTMLArea.I18N.tooltips.textindicator;
            var obj = {
                name    : txt, // the button name (i.e. 'bold')
                element : el, // the UI element (DIV)
                enabled : true, // is it enabled?
                active  : false, // is it pressed?
                text    : false, // enabled in text mode?
                cmd : "textindicator", // the command ID
                state   : setButtonStatus // for changing state
            };
            tb_objects[txt] = obj;
            break;
            default:
            btn = editor.config.btnList[txt];
        }
        if (!el && btn) {
            el = document.createElement("div");
            el.title = btn[0];
            el.className = "button";
            // let's just pretend we have a button object, and
            // assign all the needed information to it.
            var obj = {
                name    : txt, // the button name (i.e. 'bold')
                element : el, // the UI element (DIV)
                enabled : true, // is it enabled?
                active  : false, // is it pressed?
                text    : btn[2], // enabled in text mode?
                cmd : btn[3], // the command ID
                state   : setButtonStatus, // for changing state
                context : btn[4] || null // enabled in a certain context?
            };
            tb_objects[txt] = obj;
            // handlers to emulate nice flat toolbar buttons
            HTMLArea._addEvent(el, "mouseover", function () {
                if (obj.enabled) {
                    HTMLArea._addClass(el, "buttonHover");
                }
            });
            HTMLArea._addEvent(el, "mouseout", function () {
                if (obj.enabled) with (HTMLArea) {
                    _removeClass(el, "buttonHover");
                    _removeClass(el, "buttonActive");
                    (obj.active) && _addClass(el, "buttonPressed");
                }
            });
            HTMLArea._addEvent(el, "mousedown", function (ev) {
                if (obj.enabled) with (HTMLArea) {
                    _addClass(el, "buttonActive");
                    _removeClass(el, "buttonPressed");
                    _stopEvent(is_ie ? window.event : ev);
                }
            });
            // when clicked, do the following:
            HTMLArea._addEvent(el, "click", function (ev) {
                if (obj.enabled) with (HTMLArea) {
                    _removeClass(el, "buttonActive");
                    _removeClass(el, "buttonHover");
                    obj.cmd(editor, obj.name, obj);
                    _stopEvent(is_ie ? window.event : ev);
                }
            });
            var img = document.createElement("img");
            img.src = btn[1];
            img.style.width = "18px";
            img.style.height = "18px";
            el.appendChild(img);
        } else if (!el) {
            el = createSelect(txt);
        }
        if (el) {
            var tb_cell = document.createElement("td");
            tb_row.appendChild(tb_cell);
            tb_cell.appendChild(el);
        } else {
            alert("FIXME: Unknown toolbar item: " + txt);
        }
        return el;
    };

    var first = true;
    for (var i in this.config.toolbar) {
        if (this.config.toolbar.propertyIsEnumerable(i)) { // fix for prototype.js compatibility
        if (!first) {
            createButton("linebreak");
        } else {
            first = false;
        }
        var group = this.config.toolbar[i];
        for (var j in group) {
                if (group.propertyIsEnumerable(j)) { // fix for prototype.js compatibility
            var code = group[j];
            if (/^([IT])\[(.*?)\]/.test(code)) {
                // special case, create text label
                var l7ed = RegExp.$1 == "I"; // localized?
                var label = RegExp.$2;
                if (l7ed) {
                    label = HTMLArea.I18N.custom[label];
                }
                var tb_cell = document.createElement("td");
                tb_row.appendChild(tb_cell);
                tb_cell.className = "label";
                tb_cell.innerHTML = label;
            } else {
                createButton(code);
                    }
                }
            }
        }
    }

    this._htmlArea.appendChild(toolbar);
};

HTMLArea.prototype._createStatusBar = function() {
    var statusbar = document.createElement("div");
    statusbar.className = "statusBar";
    this._htmlArea.appendChild(statusbar);
    this._statusBar = statusbar;
    // statusbar.appendChild(document.createTextNode(HTMLArea.I18N.msg["Path"] + ": "));
    // creates a holder for the path view
    div = document.createElement("span");
    div.className = "statusBarTree";
    div.innerHTML = HTMLArea.I18N.msg["Path"] + ": ";
    this._statusBarTree = div;
    this._statusBar.appendChild(div);
    if (!this.config.statusBar) {
        // disable it...
        statusbar.style.display = "none";
    }
};

// Creates the HTMLArea object and replaces the textarea with it.
HTMLArea.prototype.generate = function () {
    var editor = this;  // we'll need "this" in some nested functions

    // get the textarea
    var textarea = this._textArea;
    if (typeof textarea == "string") {
        // it's not element but ID
        this._textArea = textarea = HTMLArea.getElementById("textarea", textarea);
    }
    // Fix for IE's sticky bug. Editor doesn't load
    // editing area.
    var height;
    if ( textarea.offsetHeight && textarea.offsetHeight > 0 ) {
        height = textarea.offsetHeight;
    } else {
        height = 300;
    }
    this._ta_size = {
        w: textarea.offsetWidth,
        h: height
    };
    textarea.style.display = "none";

    // create the editor framework
    var htmlarea = document.createElement("div");
    htmlarea.className = "htmlarea";
    this._htmlArea = htmlarea;

    // insert the editor before the textarea.
    //Bug fix - unless the textarea is nested within its label, in which case insert editor before label.
    if (textarea.parentNode.nodeName.toLowerCase()=='label') {
        textarea.parentNode.parentNode.insertBefore(htmlarea,textarea.parentNode);
    }
    else {
        textarea.parentNode.insertBefore(htmlarea, textarea);
    }

    if (textarea.form) {
        // we have a form, on submit get the HTMLArea content and
        // update original textarea.
        var f = textarea.form;
        if (typeof f.onsubmit == "function") {
            var funcref = f.onsubmit;
            if (typeof f.__msh_prevOnSubmit == "undefined") {
                f.__msh_prevOnSubmit = [];
            }
            f.__msh_prevOnSubmit.push(funcref);
        }
        f.onsubmit = function() {
            // Moodle hack. Bug fix #2736
            var test = editor.getHTML();
            test = test.replace(/<br \/>/gi, '');
            test = test.replace(/\&nbsp\;/gi, '');
            test = test.trim();
            //alert(test + test.length);
            if (test.length < 1) {
                editor._textArea.value = test.trim();
            } else {
                editor._textArea.value = editor.getHTML();
            }
            // Moodle hack end.
            var a = this.__msh_prevOnSubmit;
            var ret = true;
            // call previous submit methods if they were there.
            if (typeof a != "undefined") {
                for (var i = a.length; --i >= 0;) {
                    ret = a[i]() && ret;
                }
            }
            return ret;
        };
        if (typeof f.onreset == "function") {
            var funcref = f.onreset;
            if (typeof f.__msh_prevOnReset == "undefined") {
                f.__msh_prevOnReset = [];
            }
            f.__msh_prevOnReset.push(funcref);
        }
        f.onreset = function() {
            editor.setHTML(editor._textArea.value);
            editor.updateToolbar();
            var a = this.__msh_prevOnReset;
            // call previous reset methods if they were there.
            if (typeof a != "undefined") {
                for (var i = a.length; --i >= 0;) {
                    a[i]();
                }
            }
        };
    }

    // add a handler for the "back/forward" case -- on body.unload we save
    // the HTML content into the original textarea.
    try {
    window.onunload = function() {
        editor._textArea.value = editor.getHTML();
    };
    } catch(e) {};

    // creates & appends the toolbar
    this._createToolbar();

    // create the IFRAME
    var iframe = document.createElement("iframe");

    iframe.src = "<?php echo $url; ?>blank.html";

    iframe.className = "iframe";

    htmlarea.appendChild(iframe);

    var editor = this
    editor._iframe = iframe;
    var doc = editor._iframe.contentWindow.document;
    editor._doc = doc;

    // Generate iframe content
    var html = ""
    if (!editor.config.fullPage) {
        html = "<html>\n";
        html += "<head>\n";
        html += '<meta http-equiv="content-type" content="text/html; charset=utf-8" />\n';
        if (editor.config.baseURL)
            html += '<base href="' + editor.config.baseURL + '" />';
        html += '<style type="text/css">\n' + editor.config.pageStyle + "td { border: 1px dotted gray; } body { direction: <?php echo get_string('thisdirection')?>; } </style>\n"; // RTL support: direction added for RTL support
        html += "</head>\n";
        html += '<body>\n';
        html += editor._textArea.value;
        html = html.replace(/<nolink>/gi, '<span class="nolink">').
                    replace(/<\/nolink>/gi, '</span>');
        html += "</body>\n";
        html += "</html>";
    } else {
        html = editor._textArea.value;
        if (html.match(HTMLArea.RE_doctype)) {
            editor.setDoctype(RegExp.$1);
            html = html.replace(HTMLArea.RE_doctype, "");
        }
    }

    // Write content to iframe
    doc.open();
    doc.write(html);
    doc.close();

    // The magic: onClick the designMode is set to 'on'
    // This one is for click on HTMLarea toolbar and else
    if(HTMLArea.is_gecko) {
        HTMLArea._addEvents(
          this._htmlArea,
          ["mousedown"],
          function(event) {
            if(editor.designModeIsOn != true)
            {
                editor.designModeIsOn = true;
                try {
                  doc.designMode = "on";
                } catch (e) {
                  alert(e)
                };
            }
          }
        );

        // This one is for click in iframe
        HTMLArea._addEvents(
          editor._iframe.contentWindow,
          ["mousedown"],
          function(event) {
            if(editor.designModeIsOn != true)
            {
                editor.designModeIsOn = true;
                try {
                  doc.designMode = "on";
                } catch (e) {
                  alert(e)
                };
            }
          }
        );
    }
    // creates & appends the status bar, if the case
    this._createStatusBar();

    // remove the default border as it keeps us from computing correctly
    // the sizes.  (somebody tell me why doesn't this work in IE)

    if (!HTMLArea.is_ie) {
        iframe.style.borderWidth = "1px";
    }

    // size the IFRAME according to user's prefs or initial textarea
    var height = (this.config.height == "auto" ? (this._ta_size.h) : this.config.height);
    height = parseInt(height);
    var width = (this.config.width == "auto" ? (this._toolbar.offsetWidth) : this.config.width);
    width = (width == 0 ? 598 : width);
    //width = Math.max(parseInt(width), 598);

    width = String(width);
    if (width.match(/^\d+$/)) { // is this a pure int? if so, let it be in px, and remove 2px
        height -= 2;
        width  -= 2;
        width=width+"px";
    }

    iframe.style.width = width;

    if (this.config.sizeIncludesToolbar) {
        // substract toolbar height
        height -= this._toolbar.offsetHeight;
        height -= this._statusBar.offsetHeight;
    }
    if (height < 0) {
        height = 0;
    }
    iframe.style.height = height + "px";

    // the editor including the toolbar now have the same size as the
    // original textarea.. which means that we need to reduce that a bit.
    textarea.style.width = iframe.style.width;
    textarea.style.height = iframe.style.height;

    if (HTMLArea.is_ie) {
        doc.body.contentEditable = true;
    }

    // intercept some events; for updating the toolbar & keyboard handlers
    HTMLArea._addEvents
          (doc, ["keydown", "keypress", "mousedown", "mouseup", "drag"],
          function (event) {
              return editor._editorEvent(HTMLArea.is_ie ? editor._iframe.contentWindow.event : event);
          });

    // check if any plugins have registered refresh handlers
    for (var i in editor.plugins) {
        var plugin = editor.plugins[i].instance;
        if (typeof plugin.onGenerate == "function") {
            plugin.onGenerate();
        }
        if (typeof plugin.onGenerateOnce == "function") {
            plugin.onGenerateOnce();
            plugin.onGenerateOnce = null;
        }
    }

    // Moodle fix for bug Bug #2521 Too long statusbar line in IE
    //
    //setTimeout(function() {
    //    editor.updateToolbar();
    //}, 250);

    if (typeof editor.onGenerate == "function") {
        editor.onGenerate();
    }
};


// Switches editor mode; parameter can be "textmode" or "wysiwyg".  If no
// parameter was passed this function toggles between modes.
HTMLArea.prototype.setMode = function(mode) {
    if (typeof mode == "undefined") {
        mode = ((this._editMode == "textmode") ? "wysiwyg" : "textmode");
    }
    switch (mode) {
        case "textmode":
        this._textArea.value = this.getHTML();
        this._iframe.style.display = "none";
        this._textArea.style.display = "block";
        if (this.config.statusBar) {
            while(this._statusBar.childNodes.length>0) {
                this._statusBar.removeChild(this._statusBar.childNodes[0]);
            }

            this._statusBar.appendChild(document.createTextNode(HTMLArea.I18N.msg["TEXT_MODE"]));
        }
        break;
        case "wysiwyg":
        if (HTMLArea.is_gecko) {
            // disable design mode before changing innerHTML
            try {
            this._doc.designMode = "off";
            } catch(e) {};
        }
        if (!this.config.fullPage)
            this._doc.body.innerHTML = this.getHTML();
        else
            this.setFullHTML(this.getHTML());
        this._iframe.style.display = "block";
        this._textArea.style.display = "none";
        if (HTMLArea.is_gecko) {
            // we need to refresh that info for Moz-1.3a
            try {
            this._doc.designMode = "on";
            //this._doc.focus();
            } catch(e) {};
        }
        if (this.config.statusBar) {
            this._statusBar.innerHTML = '';
            this._statusBar.appendChild(this._statusBarTree);
        }
        break;
        default:
        alert("Mode <" + mode + "> not defined!");
        return false;
    }
    this._editMode = mode;
    this.focusEditor();
};

HTMLArea.prototype.setFullHTML = function(html) {
    var save_multiline = RegExp.multiline;
    RegExp.multiline = true;
    if (html.match(HTMLArea.RE_doctype)) {
        this.setDoctype(RegExp.$1);
        html = html.replace(HTMLArea.RE_doctype, "");
    }
    RegExp.multiline = save_multiline;
    if (!HTMLArea.is_ie) {
        if (html.match(HTMLArea.RE_head))
            this._doc.getElementsByTagName("head")[0].innerHTML = RegExp.$1;
        if (html.match(HTMLArea.RE_body))
            this._doc.getElementsByTagName("body")[0].innerHTML = RegExp.$1;
    } else {
        var html_re = /<html>((.|\n)*?)<\/html>/i;
        html = html.replace(html_re, "$1");
        this._doc.open();
        this._doc.write(html);
        this._doc.close();
        this._doc.body.contentEditable = true;
        return true;
    }
};

// Category: PLUGINS

HTMLArea.prototype.registerPlugin2 = function(plugin, args) {
    if (typeof plugin == "string")
        plugin = eval(plugin);
    var obj = new plugin(this, args);
    if (obj) {
        var clone = {};
        var info = plugin._pluginInfo;
        for (var i in info)
            clone[i] = info[i];
        clone.instance = obj;
        clone.args = args;
        this.plugins[plugin._pluginInfo.name] = clone;
    } else
        alert("Can't register plugin " + plugin.toString() + ".");
};

// Create the specified plugin and register it with this HTMLArea
HTMLArea.prototype.registerPlugin = function() {
    var plugin = arguments[0];
    var args = [];
    for (var i = 1; i < arguments.length; ++i)
        args.push(arguments[i]);
    this.registerPlugin2(plugin, args);
};

HTMLArea.loadPlugin = function(pluginName) {
    var dir = _editor_url + "plugins/" + pluginName;
    var plugin = pluginName.replace(/([a-z])([A-Z])([a-z])/g,
                    function (str, l1, l2, l3) {
                        return l1 + "-" + l2.toLowerCase() + l3;
                    }).toLowerCase() + ".js";
    var plugin_file = dir + "/" + plugin;
    var plugin_lang = dir + "/lang/" + HTMLArea.I18N.lang + ".js";
    HTMLArea._scripts.push(plugin_file, plugin_lang);
    document.write("<script type='text/javascript' src='" + plugin_file + "'></script>");
    document.write("<script type='text/javascript' src='" + plugin_lang + "'></script>");
};

HTMLArea.loadStyle = function(style, plugin) {
    var url = _editor_url || '';
    if (typeof plugin != "undefined") {
        url += "plugins/" + plugin + "/";
    }
    url += style;
    document.write("<style type='text/css'>@import url(" + url + ");</style>");
};
HTMLArea.loadStyle("htmlarea.css");

// Category: EDITOR UTILITIES

// The following function is a slight variation of the word cleaner code posted
// by Weeezl (user @ InteractiveTools forums).
HTMLArea.prototype._wordClean = function() {
    this._unnestBlocks();

    var D = this.getInnerHTML();
    if (/[Mm]so/.test(D)) {

        // make one line
        D = D.replace(/\r\n/g, '\[br\]').
            replace(/\n/g, '').
            replace(/\r/g, '').
            replace(/\&nbsp\;/g,' ');

        // keep tags, strip attributes
        D = D.replace(/ class=[^\s|>]*/gi,'').
            //replace(/<p [^>]*TEXT-ALIGN: justify[^>]*>/gi,'<p align="justify">').
            replace(/ style=\"[^>]*\"/gi,'').
            replace(/ align=[^\s|>]*/gi,'');

        //clean up tags
        D = D.replace(/<b [^>]*>/gi,'<b>').
            replace(/<i [^>]*>/gi,'<i>').
            replace(/<li [^>]*>/gi,'<li>').
            replace(/<ul [^>]*>/gi,'<ul>');

        // replace outdated tags
        D = D.replace(/<b>/gi,'<strong>').
            replace(/<\/b>/gi,'</strong>');

        // mozilla doesn't like <em> tags
        D = D.replace(/<em>/gi,'<i>').
            replace(/<\/em>/gi,'</i>');

        // kill unwanted tags
        D = D.replace(/<\?xml:[^>]*>/g, '').       // Word xml
            replace(/<\/?st1:[^>]*>/g,'').     // Word SmartTags
            replace(/<\/?[a-z]\:[^>]*>/g,'').  // All other funny Word non-HTML stuff
            replace(/<\/?personname[^>]*>/gi,'').
            replace(/<\/?font[^>]*>/gi,'').    // Disable if you want to keep font formatting
            replace(/<\/?span[^>]*>/gi,' ').
            replace(/<\/?div[^>]*>/gi,' ').
            replace(/<\/?pre[^>]*>/gi,' ').
            replace(/<(\/?)(h[1-6]+)[^>]*>/gi,'<$1$2>');

        // Lorenzo Nicola's addition
        // to get rid off silly word generated tags.
        D = D.replace(/<!--\[[^\]]*\]-->/gi,' ');

        //remove empty tags
        //D = D.replace(/<strong><\/strong>/gi,'').
        //replace(/<i><\/i>/gi,'').
        //replace(/<P[^>]*><\/P>/gi,'');
        D = D.replace(/<h[1-6]+>\s?<\/h[1-6]+>/gi, ''); // Remove empty headings

        // nuke double tags
        oldlen = D.length + 1;
        while(oldlen > D.length) {
            oldlen = D.length;
            // join us now and free the tags, we'll be free hackers, we'll be free... ;-)
            D = D.replace(/<([a-z][a-z]*)> *<\/\1>/gi,' ').
                replace(/<([a-z][a-z]*)> *<([a-z][^>]*)> *<\/\1>/gi,'<$2>');
        }
        D = D.replace(/<([a-z][a-z]*)><\1>/gi,'<$1>').
            replace(/<\/([a-z][a-z]*)><\/\1>/gi,'<\/$1>');

        // nuke double spaces
        D = D.replace(/  */gi,' ');

        // Split into lines and remove
        // empty lines and add carriage returns back
        var splitter  = /\[br\]/g;
        var emptyLine = /^\s+\s+$/g;
        var strHTML   = '';
        var toLines   = D.split(splitter);
        for (var i = 0; i < toLines.length; i++) {
            var line = toLines[i];
            if (line.length < 1) {
                continue;
            }

            if (emptyLine.test(line)) {
                continue;
            }

            line = line.replace(/^\s+\s+$/g, '');
            strHTML += line + '\n';
        }
        D = strHTML;
        strHTML = '';

        this.setHTML(D);
        this.updateToolbar();
    }
};

HTMLArea.prototype._unnestBlockWalk = function(node, unnestingParent) {
    if (HTMLArea.RE_blocktag.test(node.nodeName)) {
	if (unnestingParent) {
	    if (node.nextSibling) {
		var splitNode = this._doc.createElement(unnestingParent.nodeName.toLowerCase());
		while (node.nextSibling) {
		    splitNode.appendChild(node.nextSibling);
		}
		unnestingParent.parentNode.insertBefore(splitNode, unnestingParent.nextSibling);
	    }
	    unnestingParent.parentNode.insertBefore(node, unnestingParent.nextSibling);
	    return;
	}
	else if (node.firstChild) {
	    this._unnestBlockWalk(node.firstChild, node);
	}
    } else {
	if (node.firstChild) {
	    this._unnestBlockWalk(node.firstChild, null);
	}
    }
    if (node.nextSibling) {
	this._unnestBlockWalk(node.nextSibling, unnestingParent);
    }
}

HTMLArea.prototype._unnestBlocks = function() {
    this._unnestBlockWalk(this._doc.documentElement, null);
}

HTMLArea.prototype.forceRedraw = function() {
    this._doc.body.style.visibility = "hidden";
    this._doc.body.style.visibility = "visible";
    // this._doc.body.innerHTML = this.getInnerHTML();
};

// focuses the iframe window.  returns a reference to the editor document.
HTMLArea.prototype.focusEditor = function() {
    switch (this._editMode) {
        case "wysiwyg" : this._iframe.contentWindow.focus(); break;
        case "textmode": this._textArea.focus(); break;
        default    : alert("ERROR: mode " + this._editMode + " is not defined");
    }
    return this._doc;
};

// takes a snapshot of the current text (for undo)
HTMLArea.prototype._undoTakeSnapshot = function() {
    ++this._undoPos;
    if (this._undoPos >= this.config.undoSteps) {
        // remove the first element
        this._undoQueue.shift();
        --this._undoPos;
    }
    // use the fasted method (getInnerHTML);
    var take = true;
    var txt = this.getInnerHTML();
    if (this._undoPos > 0)
        take = (this._undoQueue[this._undoPos - 1] != txt);
    if (take) {
        this._undoQueue[this._undoPos] = txt;
    } else {
        this._undoPos--;
    }
};

HTMLArea.prototype.undo = function() {
    if (this._undoPos > 0) {
        var txt = this._undoQueue[--this._undoPos];
        if (txt) this.setHTML(txt);
        else ++this._undoPos;
    }
};

HTMLArea.prototype.redo = function() {
    if (this._undoPos < this._undoQueue.length - 1) {
        var txt = this._undoQueue[++this._undoPos];
        if (txt) this.setHTML(txt);
        else --this._undoPos;
    }
};

// updates enabled/disable/active state of the toolbar elements
HTMLArea.prototype.updateToolbar = function(noStatus) {
    var doc = this._doc;
    var text = (this._editMode == "textmode");
    var ancestors = null;
    if (!text) {
        ancestors = this.getAllAncestors();
        if (this.config.statusBar && !noStatus) {

            while(this._statusBarTree.childNodes.length>0) {
                this._statusBarTree.removeChild(this._statusBarTree.childNodes[0]);
            }

            this._statusBarTree.appendChild(document.createTextNode(HTMLArea.I18N.msg["Path"] + ": "));

            for (var i = ancestors.length; --i >= 0;) {
                var el = ancestors[i];
                if (!el) {
                    // hell knows why we get here; this
                    // could be a classic example of why
                    // it's good to check for conditions
                    // that are impossible to happen ;-)
                    continue;
                }
                var a = document.createElement("a");
                a.href = "#";
                a.el = el;
                a.editor = this;
                a.onclick = function() {
                    this.blur();
                    this.editor.selectNodeContents(this.el);
                    this.editor.updateToolbar(true);
                    return false;
                };
                a.oncontextmenu = function() {
                    // TODO: add context menu here
                    this.blur();
                    var info = "Inline style:\n\n";
                    info += this.el.style.cssText.split(/;\s*/).join(";\n");
                    alert(info);
                    return false;
                };
                var txt = el.tagName.toLowerCase();
                a.title = el.style.cssText;
                if (el.id) {
                    txt += "#" + el.id;
                }
                if (el.className) {
                    txt += "." + el.className;
                }
                a.appendChild(document.createTextNode(txt));
                this._statusBarTree.appendChild(a);
                if (i != 0) {
                    this._statusBarTree.appendChild(document.createTextNode(String.fromCharCode(0xbb)));
                }
            }
        }
    }
    for (var i in this._toolbarObjects) {
        var btn = this._toolbarObjects[i];
        var cmd = i;
        var inContext = true;
        if (btn.context && !text) {
            inContext = false;
            var context = btn.context;
            var attrs = [];
            if (/(.*)\[(.*?)\]/.test(context)) {
                context = RegExp.$1;
                attrs = RegExp.$2.split(",");
            }
            context = context.toLowerCase();
            var match = (context == "*");
            for (var k in ancestors) {
                if (!ancestors[k]) {
                    // the impossible really happens.
                    continue;
                }
                if (match || (ancestors[k].tagName.toLowerCase() == context)) {
                    inContext = true;
                    for (var ka in attrs) {
                        if (!eval("ancestors[k]." + attrs[ka])) {
                            inContext = false;
                            break;
                        }
                    }
                    if (inContext) {
                        break;
                    }
                }
            }
        }
        btn.state("enabled", (!text || btn.text) && inContext);
        if (typeof cmd == "function") {
            continue;
        }
        // look-it-up in the custom dropdown boxes
        var dropdown = this.config.customSelects[cmd];
        if ((!text || btn.text) && (typeof dropdown != "undefined")) {
            dropdown.refresh(this);
            continue;
        }
        switch (cmd) {
            case "fontname":
            case "fontsize":
            case "formatblock":
                if (!text) try {
                    var value = ("" + doc.queryCommandValue(cmd)).toLowerCase();
                    if (!value) {
                        // FIXME: what do we do here?
                        break;
                    }
                    var options = this.config[cmd];
                    var k = 0;
                    // btn.element.selectedIndex = 0;
                    for (var j in options) {
                        // FIXME: the following line is scary.
                        if ((j.toLowerCase() == value) ||
                            (options[j].substr(0, value.length).toLowerCase() == value)) {
                            btn.element.selectedIndex = k;
                            break;
                        }
                        ++k;
                    }
                } catch(e) {};
                break;
            case "language":
                if (!text) try {
                    var value;
                    parentEl = this.getParentElement();
                    if (parentEl.getAttribute('lang')) {
                        // A language was previously defined for the block.
                        if (parentEl.getAttribute('class') == 'multilang') {
                            value = parentEl.getAttribute('lang')+'_ML';
                        } else {
                            value = parentEl.getAttribute('lang');
                        }
                    } else {
                        value = '';
                    }
                    var options = this.config[cmd];
                    var k = 0;
                    for (var j in options) {
                        // FIXME: the following line is scary.
                        if ((j.toLowerCase() == value) ||
                            (options[j].substr(0, value.length).toLowerCase() == value)) {
                            btn.element.selectedIndex = k;
                            break;
                        }
                        ++k;
                    }
                } catch(e) {};
                break;
            case "textindicator":
                if (!text) {
                    try {with (btn.element.style) {
                        backgroundColor = HTMLArea._makeColor(
                            doc.queryCommandValue(HTMLArea.is_ie ? "backcolor" : "hilitecolor"));
                        if (/transparent/i.test(backgroundColor)) {
                            // Mozilla
                            backgroundColor = HTMLArea._makeColor(doc.queryCommandValue("backcolor"));
                        }
                        color = HTMLArea._makeColor(doc.queryCommandValue("forecolor"));
                        fontFamily = doc.queryCommandValue("fontname");
                        fontWeight = doc.queryCommandState("bold") ? "bold" : "normal";
                        fontStyle = doc.queryCommandState("italic") ? "italic" : "normal";
                    }} catch (e) {
                        // alert(e + "\n\n" + cmd);
                    }
                }
                break;
            case "htmlmode": btn.state("active", text); break;
            case "lefttoright":
            case "righttoleft":
                var el = this.getParentElement();
                while (el && !HTMLArea.isBlockElement(el))
                    el = el.parentNode;
                if (el)
                    btn.state("active", (el.style.direction == ((cmd == "righttoleft") ? "rtl" : "ltr")));
                break;
            default:
                try {
                    btn.state("active", (!text && doc.queryCommandState(cmd)));
                } catch (e) {}
        }
    }
    // take undo snapshots
    if (this._customUndo && !this._timerUndo) {
        this._undoTakeSnapshot();
        var editor = this;
        this._timerUndo = setTimeout(function() {
            editor._timerUndo = null;
        }, this.config.undoTimeout);
    }
    // check if any plugins have registered refresh handlers
    for (var i in this.plugins) {
        var plugin = this.plugins[i].instance;
        if (typeof plugin.onUpdateToolbar == "function")
            plugin.onUpdateToolbar();
    }
};

/** Returns a node after which we can insert other nodes, in the current
 * selection.  The selection is removed.  It splits a text node, if needed.
 */
HTMLArea.prototype.insertNodeAtSelection = function(toBeInserted) {
    if (!HTMLArea.is_ie) {
        var sel = this._getSelection();
        var range = this._createRange(sel);
        // remove the current selection
        sel.removeAllRanges();
        range.deleteContents();
        var node = range.startContainer;
        var pos = range.startOffset;
        switch (node.nodeType) {
            case 3: // Node.TEXT_NODE
            // we have to split it at the caret position.
            if (toBeInserted.nodeType == 3) {
                // do optimized insertion
                node.insertData(pos, toBeInserted.data);
                range = this._createRange();
                range.setEnd(node, pos + toBeInserted.length);
                range.setStart(node, pos + toBeInserted.length);
                sel.addRange(range);
            } else {
                node = node.splitText(pos);
                var selnode = toBeInserted;
                if (toBeInserted.nodeType == 11 /* Node.DOCUMENT_FRAGMENT_NODE */) {
                    selnode = selnode.firstChild;
                }
                node.parentNode.insertBefore(toBeInserted, node);
                this.selectNodeContents(selnode);
                this.updateToolbar();
            }
            break;
            case 1: // Node.ELEMENT_NODE
            var selnode = toBeInserted;
            if (toBeInserted.nodeType == 11 /* Node.DOCUMENT_FRAGMENT_NODE */) {
                selnode = selnode.firstChild;
            }
            node.insertBefore(toBeInserted, node.childNodes[pos]);
            this.selectNodeContents(selnode);
            this.updateToolbar();
            break;
        }
    } else {
        return null;    // this function not yet used for IE <FIXME>
    }
};

// Returns the deepest node that contains both endpoints of the selection.
HTMLArea.prototype.getParentElement = function() {
    var sel = this._getSelection();
    var range = this._createRange(sel);
    if (HTMLArea.is_ie) {
        switch (sel.type) {
            case "Text":
            case "None":
            return range.parentElement();
            case "Control":
            return range.item(0);
            default:
            return this._doc.body;
        }
    } else try {
        var p = range.commonAncestorContainer;
        if (!range.collapsed && range.startContainer == range.endContainer &&
            range.startOffset - range.endOffset <= 1 && range.startContainer.hasChildNodes())
            p = range.startContainer.childNodes[range.startOffset];
        /*
        alert(range.startContainer + ":" + range.startOffset + "\n" +
              range.endContainer + ":" + range.endOffset);
        */
        while (p.nodeType == 3) {
            p = p.parentNode;
        }
        return p;
    } catch (e) {
        return null;
    }
};

// Returns an array with all the ancestor nodes of the selection.
HTMLArea.prototype.getAllAncestors = function() {
    var p = this.getParentElement();
    var a = [];
    while (p && (p.nodeType == 1) && (p.tagName.toLowerCase() != 'body')) {
        a.push(p);
        p = p.parentNode;
    }
    a.push(this._doc.body);
    return a;
};

// Selects the contents inside the given node
HTMLArea.prototype.selectNodeContents = function(node, pos) {
    this.focusEditor();
    this.forceRedraw();
    var range;
    var collapsed = (typeof pos != "undefined");
    if (HTMLArea.is_ie) {
        range = this._doc.body.createTextRange();
        range.moveToElementText(node);
        (collapsed) && range.collapse(pos);
        range.select();
    } else {
        var sel = this._getSelection();
        range = this._doc.createRange();
        range.selectNodeContents(node);
        (collapsed) && range.collapse(pos);
        sel.removeAllRanges();
        sel.addRange(range);
    }
};

// Call this function to insert HTML code at the current position.  It deletes
// the selection, if any.
HTMLArea.prototype.insertHTML = function(html) {
    var sel = this._getSelection();
    var range = this._createRange(sel);
    if (HTMLArea.is_ie) {
        range.pasteHTML(html);
    } else {
        // construct a new document fragment with the given HTML
        var fragment = this._doc.createDocumentFragment();
        var div = this._doc.createElement("div");
        div.innerHTML = html;
        while (div.firstChild) {
            // the following call also removes the node from div
            fragment.appendChild(div.firstChild);
        }
        // this also removes the selection
        var node = this.insertNodeAtSelection(fragment);
    }
};

// Call this function to surround the existing HTML code in the selection with
// your tags.  FIXME: buggy!  This function will be deprecated "soon".
HTMLArea.prototype.surroundHTML = function(startTag, endTag) {
    var html = this.getSelectedHTML();
    // the following also deletes the selection
    this.insertHTML(startTag + html + endTag);
};

/// Retrieve the selected block
HTMLArea.prototype.getSelectedHTML = function() {
    var sel = this._getSelection();
    var range = this._createRange(sel);
    var existing = null;
    if (HTMLArea.is_ie) {
        existing = range.htmlText;
    } else {
        existing = HTMLArea.getHTML(range.cloneContents(), false, this);
    }
    return existing;
};

/// Return true if we have some selection
HTMLArea.prototype.hasSelectedText = function() {
    // FIXME: come _on_ mishoo, you can do better than this ;-)
    return this.getSelectedHTML() != '';
};

HTMLArea.prototype._createLink = function(link) {
    var editor = this;
    var allinks = editor._doc.getElementsByTagName('A');
    var anchors = new Array();
    for(var i = 0; i < allinks.length; i++) {
        var attrname = allinks[i].getAttribute('name');
        if((HTMLArea.is_ie ? attrname.length > 0 : attrname != null)) {
            anchors[i] = allinks[i].getAttribute('name');
        }
    }
    var outparam = null;
    if (typeof link == "undefined") {
        link = this.getParentElement();
        if (link && !/^a$/i.test(link.tagName)) {
            if(link.tagName.toLowerCase() != 'img') {
                link = null;
                var sel = this._getSelection();
                var rng = this._createRange(sel);
                var len = HTMLArea.is_ie ? rng.text.toString().length : sel.toString().length;
                if(len < 1) {
                    alert("<?php print_string("alertnoselectedtext","editor");?>");
                    return false;
                }
            }
            link = null;
        }
    }
    if (link) {
        outparam = {
        f_href   : HTMLArea.is_ie ? editor.stripBaseURL(link.href) : link.getAttribute("href"),
        f_title  : link.title,
        f_target : link.target,
        f_anchors: anchors
    };
    } else {
        outparam = {
        f_anchors:anchors };
    }
    this._popupDialog("link_std.php?id=<?php echo $id; ?>", function(param) {
        if (!param) {
            return false;
        }
        var a = link;
        if (!a) {
            // Create a temporary unique link, insert it then find it and set the correct parameters
            var tmpLink = 'http://www.moodle.org/'+Math.random();
            var elm = editor._doc.execCommand("createlink",false,tmpLink);
            var links=editor._doc.getElementsByTagName("a");
            for(var i=0;i<links.length;i++){
                var link=links[i];
                if(link.href==tmpLink) {
                    link.href=param.f_href.trim();
                    if(param.f_target){
                        link.target=param.f_target.trim();
                    }
                    if(param.f_title){
                        link.title=param.f_title.trim();
                    }
                    break;
                }
            }
        } else {
            var href = param.f_href.trim();
            editor.selectNodeContents(a);
            if (href == "") {
                editor._doc.execCommand("unlink", false, null);
                editor.updateToolbar();
                return false;
            } else {
                a.href = href;
            }
        }
        if (!(a && /^a$/i.test(a.tagName))) {
            return false;
        }
        a.target = param.f_target.trim();
        a.title = param.f_title.trim();
        editor.selectNodeContents(a);
        editor.updateToolbar();
    }, outparam);
};

// Called when the user clicks on "InsertImage" button.  If an image is already
// there, it will just modify it's properties.
HTMLArea.prototype._insertImage = function(image) {

    // Make sure that editor has focus
    this.focusEditor();
    var editor = this;  // for nested functions
    var outparam = null;
    if (typeof image == "undefined") {
        image = this.getParentElement();
        if (image && !/^img$/i.test(image.tagName))
            image = null;
    }
    if (image) outparam = {
        f_url    : HTMLArea.is_ie ? editor.stripBaseURL(image.src) : image.getAttribute("src"),
        f_alt    : image.alt,
        f_border : image.border,
        f_align  : image.align,
        f_vert   : image.vspace,
        f_horiz  : image.hspace,
        f_width  : image.width,
        f_height : image.height
    };
    this._popupDialog("<?php
    if(!empty($id) and has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id))) {
        echo "insert_image.php?id=$id";
    } else {
        echo "insert_image_std.php?id=$id";
    }?>", function(param) {
        if (!param) {   // user must have pressed Cancel
            return false;
        }
        var img = image;
        if (!img) {
            var sel = editor._getSelection();
            var range = editor._createRange(sel);
                if (HTMLArea.is_ie) {
                editor._doc.execCommand("insertimage", false, param.f_url);
                }
            if (HTMLArea.is_ie) {
                img = range.parentElement();
                // wonder if this works...
                if (img.tagName.toLowerCase() != "img") {
                    img = img.previousSibling;
                }
            } else {
                // MOODLE HACK: startContainer.perviousSibling
                // Doesn't work so we'll use createElement and
                // insertNodeAtSelection
                //img = range.startContainer.previousSibling;
                var img = editor._doc.createElement("img");
                img.setAttribute("src",""+ param.f_url +"");
                img.setAttribute("alt",""+ param.f_alt +"");
                editor.insertNodeAtSelection(img);
            }
        } else {
            img.src = param.f_url;
        }
        for (field in param) {
            var value = param[field];
            switch (field) {
                case "f_alt"    : img.alt    = value; img.title = value; break;
                case "f_border" : img.border = parseInt(value || "0"); break;
                case "f_align"  : img.align  = value; break;
                case "f_vert"   : img.vspace = parseInt(value || "0"); break;
                case "f_horiz"  : img.hspace = parseInt(value || "0"); break;
                case "f_width"  :
                    if(value != 0) {
                        img.width = parseInt(value);
                    } else {
                        break;
                    }
                    break;
                case "f_height"  :
                    if(value != 0) {
                        img.height = parseInt(value);
                    } else {
                        break;
                    }
                    break;
            }
        }
    }, outparam);
};

// Called when the user clicks the Insert Table button
HTMLArea.prototype._insertTable = function() {
    var sel = this._getSelection();
    var range = this._createRange(sel);
    var editor = this;  // for nested functions
    this._popupDialog("insert_table.php?id=<?php echo $id; ?>", function(param) {
        if (!param) {   // user must have pressed Cancel
            return false;
        }
        var doc = editor._doc;
        // create the table element
        var table = doc.createElement("table");
        // assign the given arguments
        for (var field in param) {
            var value = param[field];
            if (!value) {
                continue;
            }
            switch (field) {
                case "f_width"   : table.width = value + param["f_unit"]; break;
                case "f_align"   : table.align   = value; break;
                case "f_border"  : table.border  = parseInt(value); break;
                case "f_spacing" : table.cellspacing = parseInt(value); break;
                case "f_padding" : table.cellpadding = parseInt(value); break;
            }
        }
        var tbody = doc.createElement("tbody");
        table.appendChild(tbody);
        for (var i = 0; i < param["f_rows"]; ++i) {
            var tr = doc.createElement("tr");
            tbody.appendChild(tr);
            for (var j = 0; j < param["f_cols"]; ++j) {
                var td = doc.createElement("td");
                /// Moodle hack
                if(param["f_unit"] == "px") {
                    tdwidth = Math.round(table.width / param["f_cols"]);
                } else {
                    tdwidth = Math.round(100 / param["f_cols"]);
                }
                td.setAttribute("width",tdwidth + param["f_unit"]);
                td.setAttribute("valign","top");
                /// Moodle hack -ends
                tr.appendChild(td);
                // Mozilla likes to see something inside the cell.
                (HTMLArea.is_gecko) && td.appendChild(doc.createElement("br"));
            }
        }
        if (HTMLArea.is_ie) {
            range.pasteHTML(table.outerHTML);
        } else {
            // insert the table
            editor.insertNodeAtSelection(table);
        }
        return true;
    }, null);
};

/// Moodle hack - insertSmile
HTMLArea.prototype._insertSmile = function() {
    // Make sure that editor has focus
    this.focusEditor();
    var sel = this._getSelection();
    var range = this._createRange(sel);
    var editor = this;  // for nested functions
    this._popupDialog("dlg_ins_smile.php?id=<?php echo $id; ?>", function(imgString) {
        if(!imgString) {
            return false;
        }
        if (HTMLArea.is_ie) {
            range.pasteHTML(imgString);
        } else {
            editor.insertHTML(imgString);
        }
        return true;
    }, null);
};

HTMLArea.prototype._insertChar = function() {
    var sel = this._getSelection();
    var range = this._createRange(sel);
    var editor = this;  // for nested functions
    this._popupDialog("dlg_ins_char.php?id=<?php echo $id; ?>", function(sChar) {
        if(!sChar) {
            return false;
        }
        if (HTMLArea.is_ie) {
            range.pasteHTML(sChar);
        } else {
            // insert the table
            editor.insertHTML(sChar);
        }
        return true;
    }, null);
};

HTMLArea.prototype._removelink = function() {
    var editor = this;
    link = this.getParentElement();
    editor.selectNodeContents(link);

    this._doc.execCommand("unlink", false, null);
    this.focusEditor();
};

HTMLArea.prototype._createanchor = function () {
    var editor = this;
    var sel = this._getSelection();
    var rng = this._createRange(sel);
    var len = HTMLArea.is_ie ? rng.text.toString().length : sel.toString().length;
    if(len < 1) {
        alert("<?php print_string("alertnoselectedtext","editor");?>");
        return false;
    }
    this._popupDialog("createanchor.php?id=<?php echo $id; ?>", function(objAn) {
        if(!objAn) {
            return false;
        }
        var str = '<a name="'+ objAn.anchor+'">';
        str += HTMLArea.is_ie ? rng.text : sel ;
        str += '</a>';
        editor.insertHTML(str);
    },null);
};

HTMLArea.prototype._nolinktag = function () {

    var editor = this;
    var sel = this._getSelection();
    var rng = this._createRange(sel);
    var len = HTMLArea.is_ie ? rng.text.toString().length : sel.toString().length;

    if (len < 1) {
        alert("<?php print_string("alertnoselectedtext","editor");?>");
        return false;
    }
    var str = '<span class="nolink">';
    str += HTMLArea.is_ie ? rng.text : sel;
    str += '</span>';
    editor.insertHTML(str);
    this.focusEditor();

};

HTMLArea.prototype._searchReplace = function() {

    var editor = this;
    var selectedtxt = "";
    <?php
    $strreplaced = addslashes(get_string('itemsreplaced','editor'));
    $strnotfound = addslashes(get_string('searchnotfound','editor'));
    ?>
    var strReplaced = '<?php echo $strreplaced ?>';
    var strNotfound = '<?php echo $strnotfound ?>';
    var ile;

    //in source mode mozilla show errors, try diffrent method
    if (editor._editMode == "wysiwyg") {
        selectedtxt = editor.getSelectedHTML();
    } else {
        if (HTMLArea.is_ie) {
            selectedtxt = document.selection.createRange().text;
        } else {
            selectedtxt = getMozSelection(editor._textArea);
        }
    }

    outparam = {
        f_search : selectedtxt
    };

    //Call Search And Replace popup window
    editor._popupDialog( "searchandreplace.php?id=<?php echo $id; ?>", function( entity ) {
        if ( !entity ) {
            //user must have pressed Cancel
            return false;
        }
        var text = editor.getHTML();
        var search = entity[0];
        var replace = entity[1];
        var delim = entity[2];
        var regularx = entity[3];
        var closesar = entity[4];
        ile = 0;
        if (search.length < 1) {
            alert ("Enter a search word! \n search for: " + entity[0]);
        } else {
            if (regularx) {
            var regX = new RegExp (search, delim) ;
            var text = text.replace ( regX,
            function (str, n) {
                // Increment our counter variable.
                ile++ ;
                //return replace ;
                return str.replace( regX, replace) ;
                }
            )

            } else {
                while (text.indexOf(search)>-1) {
                    pos = text.indexOf(search);
                    text = "" + (text.substring(0, pos) + replace + text.substring((pos + search.length), text.length));
                    ile++;
                }
            }

            editor.setHTML(text);
            editor.forceRedraw();
            if (ile > 0) {
                alert(ile + ' ' + strReplaced);
            } else {
                alert (strNotfound + "\n");
            }
        }
    }, outparam);

    function getMozSelection(txtarea) {
        var selLength = txtarea.textLength;
        var selStart = txtarea.selectionStart;
        var selEnd = txtarea.selectionEnd;
        if (selEnd==1 || selEnd==2) selEnd=selLength;
        return (txtarea.value).substring(selStart, selEnd);
    }
};

/// Moodle hack's ends
//
// Category: EVENT HANDLERS

// el is reference to the SELECT object
// txt is the name of the select field, as in config.toolbar
HTMLArea.prototype._comboSelected = function(el, txt) {
    this.focusEditor();
    var value = el.options[el.selectedIndex].value;
    switch (txt) {
        case "fontname":
        case "fontsize": this.execCommand(txt, false, value); break;
        case "language":
            this.setLang(value);
            break;
        case "formatblock":
            (HTMLArea.is_ie) && (value = "<" + value + ">");
            this.execCommand(txt, false, value);
            break;
        default:
        // try to look it up in the registered dropdowns
        var dropdown = this.config.customSelects[txt];
        if (typeof dropdown != "undefined") {
            dropdown.action(this);
        } else {
            alert("FIXME: combo box " + txt + " not implemented");
        }
    }
};


/**
 * Used to set the language for the selected content.
 * We use the <span lang="en" class="multilang">content</span> format for
 * content that should be marked for multilang filter use, and
 * <span lang="en">content</span> for normal content for which we want to
 * set the language (for screen reader usage, for example).
 */
HTMLArea.prototype.setLang = function(lang) {

    if (lang == 'multi') {
        // This is just the separator in the dropdown. Does nothing.
        return;
    }

    var editor = this;
    var selectedHTML = editor.getSelectedHTML();
    var multiLang = false;

    var re = new RegExp('_ML', 'g');
    if (lang.match(re)) {
        multiLang = true;
        lang = lang.replace(re, '');
    }

    // Remove all lang attributes from span tags in selected html.
    selectedHTML = selectedHTML.replace(/(<span[^>]*)lang="[^"]*"([^>]*>)/, "$1$2");
    selectedHTML = selectedHTML.replace(/(<span[^>]*)class="multilang"([^>]*>)/, "$1$2");

    // If a span tag is now empty, delete it.
    selectedHTML = selectedHTML.replace(/<span\s*>(.*?)<\/span>/, "$1");


    var parentEl = this.getParentElement();
    var insertNewSpan = false;

    if (parentEl.nodeName == 'SPAN' && parentEl.getAttribute('lang')) {
        // A language was previously defined for the current block.
        // Check whether the selected text makes up the whole of the block
        // contents.
        var re = new RegExp(parentEl.innerHTML);

        if (selectedHTML.match(re)) {
            // The selected text makes up the whole of the span block.
            if (lang != '') {
                parentEl.setAttribute('lang', lang);
                if (multiLang) {
                    parentEl.setAttribute('class', 'multilang');
                }
            } else {
                parentEl.removeAttribute('lang');

                var classAttr = parentEl.getAttribute('class');
                if (classAttr) {
                    classAttr = classAttr.replace(/multilang/, '').trim();
                }
                if (classAttr == '') {
                    parentEl.removeAttribute('class');
                }
                if (parentEl.attributes.length == 0) {
                    // The span is no longer needed.
                    for (i=0; i<parentEl.childNodes.length; i++) {
                        parentEl.parentNode.insertBefore(parentEl.childNodes[i], parentEl);
                    }
                    parentEl.parentNode.removeChild(parentEl);
                }
            }
        } else {
            insertNewSpan = true;
        }
    } else {
        insertNewSpan = true;
    }

    if (insertNewSpan && lang != '') {
        var str  = '<span lang="'+lang.trim()+'"';
            str += ' class="multilang"';
        str += '>';
        str += selectedHTML;
        str += '</span>';
        editor.insertHTML(str);
    }
}


// the execCommand function (intercepts some commands and replaces them with
// our own implementation)
HTMLArea.prototype.execCommand = function(cmdID, UI, param) {
    var editor = this;  // for nested functions
    this.focusEditor();
    cmdID = cmdID.toLowerCase();
    switch (cmdID) {
        case "htmlmode" : this.setMode(); break;
        case "hilitecolor":
        (HTMLArea.is_ie) && (cmdID = "backcolor");
        case "forecolor":
            this._popupDialog("select_color.php?id=<?php echo $id; ?>", function(color) {
                if (color) { // selection not canceled
                    editor._doc.execCommand(cmdID, false, "#" + color);
                }
            }, HTMLArea._colorToRgb(this._doc.queryCommandValue(cmdID)));
            break;
        case "createanchor": this._createanchor(); break;
        case "createlink":
        this._createLink();
        break;
        case "unlink": this._removelink(); break;
        case "nolink": this._nolinktag(); break;
        case "popupeditor":
        // this object will be passed to the newly opened window
        HTMLArea._object = this;
        if (HTMLArea.is_ie) {
            {
                window.open(this.popupURL("fullscreen.php?id=<?php echo $id;?>"), "ha_fullscreen",
                    "toolbar=no,location=no,directories=no,status=no,menubar=no," +
                        "scrollbars=no,resizable=yes,width=800,height=600");
            }
        } else {
            window.open(this.popupURL("fullscreen.php?id=<?php echo $id;?>"), "ha_fullscreen",
                    "toolbar=no,menubar=no,personalbar=no,width=800,height=600," +
                    "scrollbars=no,resizable=yes");
        }
        break;
        case "undo":
        case "redo":
        if (this._customUndo)
            this[cmdID]();
        else
            this._doc.execCommand(cmdID, UI, param);
        break;
        case "inserttable": this._insertTable(); break;
        case "insertimage": this._insertImage(); break;
        case "insertsmile": this._insertSmile(); break;
        case "insertchar": this._insertChar(); break;
        case "searchandreplace": this._searchReplace(); break;
        case "about"    : this._popupDialog("about.html", null, this); break;
        case "showhelp" : window.open(_editor_url + "reference.html", "ha_help"); break;

        case "killword": this._wordClean(); break;

        case "cut":
        case "copy":
        case "paste":
        try {
            // Paste first then clean
            this._doc.execCommand(cmdID, UI, param);
            if (this.config.killWordOnPaste) {
                this._wordClean();
            }
        } catch (e) {
            if (HTMLArea.is_gecko) {
                if (confirm("<?php
                    $strmoz = get_string('cutpastemozilla','editor');
                    $strmoz = preg_replace("/[\n|\r]+/", "", $strmoz);
                    $strmoz = str_replace('<br />', '\\n', $strmoz);

                    echo addslashes($strmoz);

                    ?>"))
                    window.open("http://moodle.org/mozillahelp");
            }
        }
        break;
        case "lefttoright":
        case "righttoleft":
        var dir = (cmdID == "righttoleft") ? "rtl" : "ltr";
        var el = this.getParentElement();
        while (el && !HTMLArea.isBlockElement(el))
            el = el.parentNode;
        if (el) {
            if (el.style.direction == dir)
                el.style.direction = "";
            else
                el.style.direction = dir;
        }
        break;
        default: this._doc.execCommand(cmdID, UI, param);
    }
    this.updateToolbar();
    return false;
};


/**
 * A generic event handler for things that happen in the IFRAME's document.
 * This function also handles key bindings.
 */
HTMLArea.prototype._editorEvent = function(ev) {

    var editor = this;
    var keyEvent = (HTMLArea.is_ie && ev.type == "keydown") || (ev.type == "keypress");

    if (keyEvent) {

        for (var i in editor.plugins) {
            var plugin = editor.plugins[i].instance;
            if (typeof plugin.onKeyPress == "function") plugin.onKeyPress(ev);
        }

        var sel = null;
        var range = null;
        var key = String.fromCharCode(HTMLArea.is_ie ? ev.keyCode : ev.charCode).toLowerCase();
        var cmd = null;
        var value = null;

        if (ev.ctrlKey && !ev.altKey) {
            /**
             * Ctrl modifier only.
             * We use these for shortcuts that change existing content,
             * e.g. make text bold.
             */
            switch (key) {

                case 'a':
                    // Select all.
                    if (!HTMLArea.is_ie) {
                        // KEY select all
                        sel = this._getSelection();
                        sel.removeAllRanges();
                        range = this._createRange();
                        range.selectNodeContents(this._doc.body);
                        sel.addRange(range);
                        HTMLArea._stopEvent(ev);
                    }
                    break;

                // For the dropdowns, we assign focus to them so that they are
                // keyboard accessible.
                case 'o':
                    editor.dropdowns['fontname'].focus();
                    break;
                case 'p':
                    editor.dropdowns['fontsize'].focus();
                    break;
                case 'h':
                    editor.dropdowns['formatblock'].focus();
                    break;
                case '=':
                    editor.dropdowns['language'].focus();
                    break;

                case 'b': cmd = "bold"; break;
                case 'i': cmd = "italic"; break;
                case 'u': cmd = "underline"; break;
                case 's': cmd = "strikethrough"; break;
                case ',': cmd = "subscript"; break;
                case '.': cmd = "superscript"; break;

                case 'v':
                    if (! HTMLArea.is_gecko ) {
                        cmd = "paste";
                    }
                    break;

                case '0': cmd = "killword"; break;
                case 'z': cmd = "undo"; break;
                case 'y': cmd = "redo"; break;
                case 'l': cmd = "justifyleft"; break;
                case 'e': cmd = "justifycenter"; break;
                case 'r': cmd = "justifyright"; break;
                case 'j': cmd = "justifyfull"; break;
                case '/': cmd = "lefttoright"; break;
                case '|': cmd = "righttoleft"; break;
                case ';': cmd = "outdent"; break;
                case "'": cmd = "indent"; break;
                case 'g': cmd = "forecolor"; break;
                case 'k': cmd = "hilitecolor"; break;
                case 'f': cmd = "searchandreplace"; break;
                case '`': cmd = "htmlmode"; break;  // FIXME: can't toggle from source code to wysiwyg

                case 'm':
                    // Toggle fullscreen on or off.
                    if (this.config.btnList['popupeditor'][0] == 'Enlarge Editor') {
                        cmd = 'popupeditor';
                    } else {
                        window.close();
                    }
                    break;

                // Headings.
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                cmd = "formatblock";
                value = "h" + key;
                if (HTMLArea.is_ie) {
                    value = "<" + value + ">";
                }
                break;

            } // End switch (key)


        } else if (ev.ctrlKey && ev.altKey) {
            /**
             * Ctrl + Alt modifiers.
             * We use these for shortcuts that insert stuff, e.g. images.
             */
            switch (key) {
                case 'o': cmd = "insertorderedlist"; break;
                case 'u': cmd = "insertunorderedlist"; break;
                case 'r': cmd = "inserthorizontalrule"; break;
                case 'a': cmd = "createanchor"; break;
                case 'l': cmd = "createlink"; break;
                case 'd': cmd = "unlink"; break;
                case 'n': cmd = "nolink"; break;
                case 'i': cmd = 'insertimage'; break;
                case 't': cmd = 'inserttable'; break;
                case 's': cmd = 'insertsmile'; break;
                case 'c': cmd = 'insertchar'; break;
            }
        }

        if (cmd) {
            // execute simple command
            this.execCommand(cmd, false, value);
            HTMLArea._stopEvent(ev);
        }
    } // End if (keyEvent)

    /*
    else if (keyEvent) {
        // other keys here
        switch (ev.keyCode) {
            case 13: // KEY enter
            // if (HTMLArea.is_ie) {
            this.insertHTML("<br />");
            HTMLArea._stopEvent(ev);
            // }
            break;
        }
    }
    */

    // Update the toolbar state after some time.
    if (editor._timerToolbar) {
        clearTimeout(editor._timerToolbar);
    }
    editor._timerToolbar = setTimeout(function() {
        editor.updateToolbar();
        editor._timerToolbar = null;
    }, 50);
};


// retrieve the HTML
HTMLArea.prototype.getHTML = function() {
    switch (this._editMode) {
        case "wysiwyg"  :
        if (!this.config.fullPage) {
            return HTMLArea.getHTML(this._doc.body, false, this);
        } else
            return this.doctype + "\n" + HTMLArea.getHTML(this._doc.documentElement, true, this);
        case "textmode" : return this._textArea.value;
        default     : alert("Mode <" + mode + "> not defined!");
    }
    return false;
};

// retrieve the HTML (fastest version, but uses innerHTML)
HTMLArea.prototype.getInnerHTML = function() {
    switch (this._editMode) {
        case "wysiwyg"  :
        if (!this.config.fullPage)
            return this._doc.body.innerHTML;
        else
            return this.doctype + "\n" + this._doc.documentElement.innerHTML;
        case "textmode" : return this._textArea.value;
        default     : alert("Mode <" + mode + "> not defined!");
    }
    return false;
};

// completely change the HTML inside
HTMLArea.prototype.setHTML = function(html) {
    switch (this._editMode) {
        case "wysiwyg"  :
        if (!this.config.fullPage)
            this._doc.body.innerHTML = html;
        else
            // this._doc.documentElement.innerHTML = html;
            this._doc.body.innerHTML = html;
        break;
        case "textmode" : this._textArea.value = html; break;
        default     : alert("Mode <" + mode + "> not defined!");
    }
    return false;
};

// sets the given doctype (useful when config.fullPage is true)
HTMLArea.prototype.setDoctype = function(doctype) {
    this.doctype = doctype;
};

/***************************************************
 *  Category: UTILITY FUNCTIONS
 ***************************************************/

// browser identification

HTMLArea.agt = navigator.userAgent.toLowerCase();
HTMLArea.is_ie     = ((HTMLArea.agt.indexOf("msie") != -1) && (HTMLArea.agt.indexOf("opera") == -1));
HTMLArea.is_opera  = (HTMLArea.agt.indexOf("opera") != -1);
HTMLArea.is_mac    = (HTMLArea.agt.indexOf("mac") != -1);
HTMLArea.is_mac_ie = (HTMLArea.is_ie && HTMLArea.is_mac);
HTMLArea.is_win_ie = (HTMLArea.is_ie && !HTMLArea.is_mac);
HTMLArea.is_gecko  = (navigator.product == "Gecko");
HTMLArea.is_safari = (HTMLArea.agt.indexOf("safari") != -1);

// variable used to pass the object to the popup editor window.
HTMLArea._object = null;

// function that returns a clone of the given object
HTMLArea.cloneObject = function(obj) {
    var newObj = new Object;

    // check for array objects
    if (obj.constructor.toString().indexOf("function Array(") >= 0) {
        newObj = obj.constructor();
    }

    // check for function objects (as usual, IE is phucked up)
    if (obj.constructor.toString().indexOf("function Function(") >= 0) {
        newObj = obj; // just copy reference to it
    } else for (var n in obj) {
        var node = obj[n];
        if (typeof node == 'object') { newObj[n] = HTMLArea.cloneObject(node); }
        else                         { newObj[n] = node; }
    }

    return newObj;
};

// FIXME!!! this should return false for IE < 5.5
HTMLArea.checkSupportedBrowser = function() {
    if (HTMLArea.is_gecko) {
        if (navigator.productSub < 20021201) {
            alert("You need at least Mozilla-1.3 Alpha.\n" +
                  "Sorry, your Gecko is not supported.");
            return false;
        }
        if (navigator.productSub < 20030210) {
            alert("Mozilla < 1.3 Beta is not supported!\n" +
                  "I'll try, though, but it might not work.");
        }
    }
    if(HTMLArea.is_safari) {
        return false;
    }
    return HTMLArea.is_gecko || HTMLArea.is_ie;
};

// selection & ranges

// returns the current selection object
HTMLArea.prototype._getSelection = function() {
    if (HTMLArea.is_ie) {
        return this._doc.selection;
    } else {
        return this._iframe.contentWindow.getSelection();
    }
};

// returns a range for the current selection
HTMLArea.prototype._createRange = function(sel) {
    if (HTMLArea.is_ie) {
        return sel.createRange();
    } else {
        // Commented out because we need the dropdowns to be able to keep
        // focus for keyboard accessibility. Comment by Vy-Shane Sin Fat.
        //this.focusEditor();
        if (typeof sel != "undefined") {
            try {
            return sel.getRangeAt(0);
            } catch(e) {
                return this._doc.createRange();
            }
        } else {
            return this._doc.createRange();
        }
    }
};

// event handling

HTMLArea._addEvent = function(el, evname, func) {
    if (HTMLArea.is_ie) {
        el.attachEvent("on" + evname, func);
    } else {
        el.addEventListener(evname, func, true);
    }
};

HTMLArea._addEvents = function(el, evs, func) {
    for (var i in evs) {
        HTMLArea._addEvent(el, evs[i], func);
    }
};

HTMLArea._removeEvent = function(el, evname, func) {
    if (HTMLArea.is_ie) {
        el.detachEvent("on" + evname, func);
    } else {
        el.removeEventListener(evname, func, true);
    }
};

HTMLArea._removeEvents = function(el, evs, func) {
    for (var i in evs) {
        HTMLArea._removeEvent(el, evs[i], func);
    }
};

HTMLArea._stopEvent = function(ev) {
    if (HTMLArea.is_ie) {
        ev.cancelBubble = true;
        ev.returnValue = false;
    } else {
        ev.preventDefault();
        ev.stopPropagation();
    }
};

HTMLArea._removeClass = function(el, className) {
    if (!(el && el.className)) {
        return;
    }
    var cls = el.className.split(" ");
    var ar = new Array();
    for (var i = cls.length; i > 0;) {
        if (cls[--i] != className) {
            ar[ar.length] = cls[i];
        }
    }
    el.className = ar.join(" ");
};

HTMLArea._addClass = function(el, className) {
    // remove the class first, if already there
    HTMLArea._removeClass(el, className);
    el.className += " " + className;
};

HTMLArea._hasClass = function(el, className) {
    if (!(el && el.className)) {
        return false;
    }
    var cls = el.className.split(" ");
    for (var i = cls.length; i > 0;) {
        if (cls[--i] == className) {
            return true;
        }
    }
    return false;
};

HTMLArea.isBlockElement = function(el) {

    var blockTags = " body form textarea fieldset ul ol dl li div " +
        "p h1 h2 h3 h4 h5 h6 quote pre table thead " +
        "tbody tfoot tr td iframe address ";
    try {
    return (blockTags.indexOf(" " + el.tagName.toLowerCase() + " ") != -1);
    } catch (e) {}

};

HTMLArea.needsClosingTag = function(el) {
    var closingTags = " head script style div span tr td tbody table em strong font a title iframe object applet ";
    return (closingTags.indexOf(" " + el.tagName.toLowerCase() + " ") != -1);
};

// performs HTML encoding of some given string
HTMLArea.htmlEncode = function(str) {
    // we don't need regexp for that, but.. so be it for now.
    str = str.replace(/&/ig, "&amp;");
    str = str.replace(/</ig, "&lt;");
    str = str.replace(/>/ig, "&gt;");
    str = str.replace(/\x22/ig, "&quot;");
    // \x22 means '"' -- we use hex reprezentation so that we don't disturb
    // JS compressors (well, at least mine fails.. ;)
    return str;
};

HTMLArea.isStandardTag = function (el) {
    return HTMLArea.RE_msietag.test(el.tagName);
};
HTMLArea.isSingleTag = function (el) {
    var re = /^(br|hr|img|input|link|meta|param|embed|area)$/i;
    return re.test(el.tagName.toLowerCase());
};
// Retrieves the HTML code from the given node.  This is a replacement for
// getting innerHTML, using standard DOM calls.
HTMLArea.getHTML = function(root, outputRoot, editor) {
    var html = "";
    switch (root.nodeType) {
        case 1: // Node.ELEMENT_NODE
        case 11: // Node.DOCUMENT_FRAGMENT_NODE
        var closed;
        var i;
        var root_tag = (root.nodeType == 1) ? root.tagName.toLowerCase() : '';
	if (HTMLArea.RE_junktag.test(root_tag)) {
	    return '';
	}
        if (HTMLArea.is_ie && root_tag == "head") {
            if (outputRoot)
                html += "<head>";
            // lowercasize
            var save_multiline = RegExp.multiline;
            RegExp.multiline = true;
            var txt = root.innerHTML.replace(HTMLArea.RE_tagName, function(str, p1, p2) {
                return p1 + p2.toLowerCase();
            });
            RegExp.multiline = save_multiline;
            html += txt;
            if (outputRoot)
                html += "</head>";
            break;
        } else if (outputRoot) {
            closed = (!(root.hasChildNodes() || !HTMLArea.isSingleTag(root)));
            html = "<" + root.tagName.toLowerCase();
            var attrs = root.attributes;
            for (i = 0; i < attrs.length; ++i) {
                var a = attrs.item(i);
                if (!a.specified) {
                    continue;
                }
                var name = a.nodeName.toLowerCase();
                if (/_moz|contenteditable|_msh/.test(name)) {
                    // avoid certain attributes
                    continue;
                }
                var value;
                if (name != "style") {
                    //
                    // Using Gecko the values of href and src are converted to absolute links
                    // unless we get them using nodeValue()
                    if (typeof root[a.nodeName] != "undefined" && name != "href" && name != "src") {
                        value = root[a.nodeName];
                    } else {
                        // This seems to be working, but if it does cause
                        // problems later on return the old value...
                        if (name.toLowerCase() == "href" && name.toLowerCase() == "src") {
                            value = root[a.nodeName];
                        } else {
                        value = a.nodeValue;
                        }
                        if (HTMLArea.is_ie && (name == "href" || name == "src")) {
                            value = editor.stripBaseURL(value);
                        }
                    }
                } else { // IE fails to put style in attributes list
                    // FIXME: cssText reported by IE is UPPERCASE
                    value = root.style.cssText.toLowerCase();
                }
                if (/(_moz|^$)/.test(value)) {
                    // Mozilla reports some special tags
                    // here; we don't need them.
                    continue;
                }
                html += " " + name + '="' + value + '"';
            }
            html += closed ? " />" : ">";
        }
        for (i = root.firstChild; i; i = i.nextSibling) {
            html += HTMLArea.getHTML(i, true, editor);
        }
        if (outputRoot && !closed) {
            if ( HTMLArea.is_ie && !HTMLArea.isStandardTag(root) ) {
                html += '';
            } else {
                html += "</" + root.tagName.toLowerCase() + ">";
            }
        }
        break;
        case 3: // Node.TEXT_NODE
        // If a text node is alone in an element and all spaces, replace it with an non breaking one
        // This partially undoes the damage done by moz, which translates '&nbsp;'s into spaces in the data element
        if ( !root.previousSibling && !root.nextSibling && root.data.match(/^\s*$/i) && root.data.length > 1 ) html = '&nbsp;';
        else html = HTMLArea.htmlEncode(root.data);
        break;
        case 8: // Node.COMMENT_NODE
        html = "<!--" + root.data + "-->";
        break;      // skip comments, for now.
    }

    return HTMLArea.indent(html);
};

HTMLArea.prototype.stripBaseURL = function(string) {
    var baseurl = this.config.baseURL;

    // IE adds the path to an anchor, converting #anchor
    // to path/#anchor which of course needs to be fixed
    var index = string.indexOf("/#")+1;
    if ((index > 0) && (string.indexOf(baseurl) > -1)) {
        return string.substr(index);
    }
    return string; // Moodle doesn't use the code below because
                   // Moodle likes to keep absolute links

    // strip to last directory in case baseurl points to a file
    baseurl = baseurl.replace(/[^\/]+$/, '');
    var basere = new RegExp(baseurl);
    string = string.replace(basere, "");

    // strip host-part of URL which is added by MSIE to links relative to server root
    baseurl = baseurl.replace(/^(https?:\/\/[^\/]+)(.*)$/, '$1');
    basere = new RegExp(baseurl);
    return string.replace(basere, "");
};

String.prototype.trim = function() {
    a = this.replace(/^\s+/, '');
    return a.replace(/\s+$/, '');
};

// creates a rgb-style color from a number
HTMLArea._makeColor = function(v) {
    if (typeof v != "number") {
        // already in rgb (hopefully); IE doesn't get here.
        return v;
    }
    // IE sends number; convert to rgb.
    var r = v & 0xFF;
    var g = (v >> 8) & 0xFF;
    var b = (v >> 16) & 0xFF;
    return "rgb(" + r + "," + g + "," + b + ")";
};

// returns hexadecimal color representation from a number or a rgb-style color.
HTMLArea._colorToRgb = function(v) {
    if (!v)
        return '';

    // returns the hex representation of one byte (2 digits)
    function hex(d) {
        return (d < 16) ? ("0" + d.toString(16)) : d.toString(16);
    };

    if (typeof v == "number") {
        // we're talking to IE here
        var r = v & 0xFF;
        var g = (v >> 8) & 0xFF;
        var b = (v >> 16) & 0xFF;
        return "#" + hex(r) + hex(g) + hex(b);
    }

    if (v.substr(0, 3) == "rgb") {
        // in rgb(...) form -- Mozilla
        var re = /rgb\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)/;
        if (v.match(re)) {
            var r = parseInt(RegExp.$1);
            var g = parseInt(RegExp.$2);
            var b = parseInt(RegExp.$3);
            return "#" + hex(r) + hex(g) + hex(b);
        }
        // doesn't match RE?!  maybe uses percentages or float numbers
        // -- FIXME: not yet implemented.
        return null;
    }

    if (v.substr(0, 1) == "#") {
        // already hex rgb (hopefully :D )
        return v;
    }

    // if everything else fails ;)
    return null;
};

HTMLArea.prototype._popupDialog = function(url, action, init) {
    Dialog(this.popupURL(url), action, init);
};

// paths

HTMLArea.prototype.imgURL = function(file, plugin) {
    if (typeof plugin == "undefined")
        return _editor_url + file;
    else
        return _editor_url + "plugins/" + plugin + "/img/" + file;
};

HTMLArea.prototype.popupURL = function(file) {
    var url = "";
    if (file.match(/^plugin:\/\/(.*?)\/(.*)/)) {
        var plugin = RegExp.$1;
        var popup = RegExp.$2;
        if (!/\.html$/.test(popup))
            popup += ".html";
        url = _editor_url + "plugins/" + plugin + "/popups/" + popup;
    } else
        url = _editor_url + this.config.popupURL + file;
    return url;
};

/**
 * FIX: Internet Explorer returns an item having the _name_ equal to the given
 * id, even if it's not having any id.  This way it can return a different form
 * field even if it's not a textarea.  This workarounds the problem by
 * specifically looking to search only elements having a certain tag name.
 */
HTMLArea.getElementById = function(tag, id) {
    var el, i, objs = document.getElementsByTagName(tag);
    for (i = objs.length; --i >= 0 && (el = objs[i]);)
        if (el.id == id)
            return el;
    return null;
};
// Modified version of GetHtml plugin's indent.
HTMLArea.indent = function(s, sindentChar) {
    var c = [
    /*0*/  new RegExp().compile(/<\/?(div|p|h[1-6]|table|tr|td|th|ul|ol|li|blockquote|object|br|hr|img|embed|param|pre|script|html|head|body|meta|link|title|area)[^>]*>/g),
    /*1*/  new RegExp().compile(/<\/(div|p|h[1-6]|table|tr|td|th|ul|ol|li|blockquote|object|html|head|body|script)( [^>]*)?>/g),//blocklevel closing tag
    /*2*/  new RegExp().compile(/<(div|p|h[1-6]|table|tr|td|th|ul|ol|li|blockquote|object|html|head|body|script)( [^>]*)?>/g),//blocklevel opening tag
    /*3*/  new RegExp().compile(/<(br|hr|img|embed|param|pre|meta|link|title|area)[^>]*>/g),//singlet tag
    /*4*/  new RegExp().compile(/(^|<\/(pre|script)>)(\s|[^\s])*?(<(pre|script)[^>]*>|$)/g),//find content NOT inside pre and script tags
    /*5*/  new RegExp().compile(/(<pre[^>]*>)(\s|[^\s])*?(<\/pre>)/g),//find content inside pre tags
    /*6*/  new RegExp().compile(/(^|<!--(\s|\S)*?-->)((\s|\S)*?)(?=<!--(\s|\S)*?-->|$)/g),//find content NOT inside comments
    /*7*/  new RegExp().compile(/<\/(table|tbody|tr|td|th|ul|ol|object|html|head|body)( [^>]*)?>/g),//blocklevel closing tag
    ];
    HTMLArea.__nindent = 0;
    HTMLArea.__sindent = "";
    HTMLArea.__sindentChar = (typeof sindentChar == "undefined") ? "  " : sindentChar;

    if(HTMLArea.is_gecko) { //moz changes returns into <br> inside <pre> tags
        s = s.replace(c[5], function(str){return str.replace(/<br \/>/g,"\n")});
    }
    s = s.replace(c[4], function(strn) { //skip pre and script tags
      strn = strn.replace(c[6], function(st,$1,$2,$3) { //exclude comments
        string = $3.replace(/[\n\r]/gi, " ").replace(/\s+/gi," ").replace(c[0], function(str) {
            if (str.match(c[2])) {
                var s = "\n" + HTMLArea.__sindent + str;
                // blocklevel openingtag - increase indent
                HTMLArea.__sindent += HTMLArea.__sindentChar;
                ++HTMLArea.__nindent;
                return s;
            } else if (str.match(c[1])) {
                // blocklevel closingtag - decrease indent
                --HTMLArea.__nindent;
                HTMLArea.__sindent = "";
                for (var i=HTMLArea.__nindent;i>0;--i) {
                    HTMLArea.__sindent += HTMLArea.__sindentChar;
                }
                return (str.match(c[7]) ? "\n" + HTMLArea.__sindent : "") + str;
            }
            return str; // this won't actually happen
        });
        return $1 + string;
      });return strn;
    });
    if (s.charAt(0) == "\n") {
        return s.substring(1, s.length);
    }
    s = s.replace(/ *\n/g,'\n');//strip spaces at end of lines
    return s;
};
