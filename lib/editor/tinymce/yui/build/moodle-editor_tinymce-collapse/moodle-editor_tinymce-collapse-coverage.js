if (typeof _yuitest_coverage == "undefined"){
    _yuitest_coverage = {};
    _yuitest_coverline = function(src, line){
        var coverage = _yuitest_coverage[src];
        if (!coverage.lines[line]){
            coverage.calledLines++;
        }
        coverage.lines[line]++;
    };
    _yuitest_coverfunc = function(src, name, line){
        var coverage = _yuitest_coverage[src],
            funcId = name + ":" + line;
        if (!coverage.functions[funcId]){
            coverage.calledFunctions++;
        }
        coverage.functions[funcId]++;
    };
}
_yuitest_coverage["build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js",
    code: []
};
_yuitest_coverage["build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js"].code=["YUI.add('moodle-editor_tinymce-collapse', function (Y, NAME) {","","var COLLAPSE = function() {","    COLLAPSE.superclass.constructor.apply(this, arguments);","};","","Y.extend(COLLAPSE, Y.Base, {","    // A location to store the node toggling template so that we do not have to create it each time.","    toggleNodeTemplate : null,","","    /**","      * Set up basic values for static access.","      */","    init : function() {","        this.initialise_toggles(10);","    },","","    /**","     * Has TinyMCE been loaded and the editors been initialised?","     * Designed mainly for IE","     * @return bool","     */","    editors_initialised : function() {","        return typeof tinyMCE !== 'undefined';","    },","","    initialise_toggles : function(refreshes) {","        var editors_initialised = this.editors_initialised(), self = this, editor;","        if (!editors_initialised && refreshes) {","            setTimeout(function() {","                    self.initialise_toggles(refreshes - 1);","                }, 100);","            return;","        }","","        // Create the toggle template for use later","        this.toggleNodeTemplate = Y.Node.create('<a class=\"toggle_editor_toolbar\" />');","        this.toggleNodeTemplate.setContent(M.util.get_string('showeditortoolbar', 'form'));","","        // Delegate clicks of the toggle_editor_toolbar","        Y.one('body').delegate('click', this.toggle_collapse_from_event, 'a.toggle_editor_toolbar', this);","","        // Set up editors which have already been created","        for (editor in tinyMCE.editors) {","            this.setup_collapse(tinyMCE.editors[editor]);","        }","","        // Set up for future editors.","        // I haven't yet found a way of directly delegating the editor.onInit event. Instead we have to listen for the","        // tinyMCE.onAddEditor event, and then add a further event listener to the editor's onInit event.","        // onAddEditor is triggered before the editor has been created.","        // We use Y.Bind to ensure that context is maintained.","        tinyMCE.onAddEditor.add(Y.bind(this.add_setup_collapse_listener, this));","","    },","","    /**","      * Setup a listener for a new editor which will actually set the editor up","      * @param {Manager} mgr","      * @param {Editor} ed","      */","    add_setup_collapse_listener : function (mgr, ed) {","        // Bind the editor.onInit function to set this editor up. This ensures we maintain our context (this)","        ed.onInit.add(Y.bind(this.setup_collapse, this));","    },","","    /**","      * Setup the toggle system for the provided editor","      *","      * @param {Editor} ed The TinyMCE editor instance","      */","    setup_collapse : function(ed) {","        var textarea = Y.Node(ed.getElement()),","            editortable = Y.Node(ed.getContainer()).one('> table'),","            thisToggleNode;","","        // Does this text area support collapsing at all?","        if (!textarea.hasClass('collapsible')) {","            return;","        }","","        // Did we find an appropriate table to work with","        if (!editortable) {","            return;","        }","","        // Add toggle button.","        thisToggleNode = this.toggleNodeTemplate.cloneNode(true);","        editortable.get('parentNode').insert(thisToggleNode, editortable);","","        // Toggle the toolbars initially.","        if (Y.Node(ed.getElement()).hasClass('collapsed')) {","            this.toggle_collapse(thisToggleNode, editortable, 0);","        } else {","            this.toggle_collapse(thisToggleNode, editortable, 1);","        }","    },","","    /**","      * Toggle the specified editor toolbars.","      *","      * @param {Node} button The toggle button which we have to change the text for","      * @param {Node} editortable The table which the tinyMCE editor is in","      * @param {Boolean} newstate The intended toggle state","      */","    toggle_collapse : function(button, editortable, newstate) {","        var toolbar = editortable.one('td.mceToolbar').ancestor('tr'),","            statusbar = editortable.one('.mceStatusbar').ancestor('tr'),","            editor, iframe, size;","","        // Check whether we have a state already.","        if (typeof newstate === 'undefined') {","            if (toolbar.getStyle('display') === 'none') {","                newstate = 1;","            } else {","                newstate = 0;","            }","        }","","        // Toggle the various states and update the button text to suit","        if (newstate === 0) {","            toolbar.hide();","            statusbar.hide();","            button.setContent(M.util.get_string('showeditortoolbar', 'form'));","        } else {","            toolbar.show();","            statusbar.show();","            button.setContent(M.util.get_string('hideeditortoolbar', 'form'));","        }","","        // TinyMCE renders the toolbar and path bar as part of the textarea. So toggling these items","        // changes the required size of the rendered textarea. Frustrating but it's the way it's built.","        // So we get TinyMCE to resize itself for us. Clunky but it works.","","        // Get the tinyMCE editor object for this text area.","        editorid = editortable.ancestor('div').one('textarea').get('id');","        editor = tinyMCE.getInstanceById(editorid);","","        // Somehow, this editor did not exist.","        if (!editor) {","            return;","        }","","        // Resize editor to reflect presence of toolbar and path bar..","        iframe = editor.getBody();","        if (iframe) {","            size = tinymce.DOM.getSize(iframe);","            // If objects exist resize editor.","            if (size) {","                editor.theme.resizeTo(size.w, size.h);","            }","        }","    },","","    toggle_collapse_from_event : function(thisevent) {","        var button = thisevent.target.ancestor('a', true),","            editortable = thisevent.target.ancestor('span', true).one('table.mceLayout');","        this.toggle_collapse(button, editortable);","    }","});","","M.editor_collapse = M.editor_collapse || {};","M.editor_collapse.init = function(params) {","    return new COLLAPSE(params);","};","","","}, '@VERSION@', {\"requires\": [\"base\", \"node\", \"dom\"]});"];
_yuitest_coverage["build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js"].lines = {"1":0,"3":0,"4":0,"7":0,"15":0,"24":0,"28":0,"29":0,"30":0,"31":0,"33":0,"37":0,"38":0,"41":0,"44":0,"45":0,"53":0,"64":0,"73":0,"78":0,"79":0,"83":0,"84":0,"88":0,"89":0,"92":0,"93":0,"95":0,"107":0,"112":0,"113":0,"114":0,"116":0,"121":0,"122":0,"123":0,"124":0,"126":0,"127":0,"128":0,"136":0,"137":0,"140":0,"141":0,"145":0,"146":0,"147":0,"149":0,"150":0,"156":0,"158":0,"162":0,"163":0,"164":0};
_yuitest_coverage["build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js"].functions = {"COLLAPSE:3":0,"init:14":0,"editors_initialised:23":0,"(anonymous 2):30":0,"initialise_toggles:27":0,"add_setup_collapse_listener:62":0,"setup_collapse:72":0,"toggle_collapse:106":0,"toggle_collapse_from_event:155":0,"init:163":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js"].coveredLines = 54;
_yuitest_coverage["build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js"].coveredFunctions = 11;
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 1);
YUI.add('moodle-editor_tinymce-collapse', function (Y, NAME) {

_yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 3);
var COLLAPSE = function() {
    _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "COLLAPSE", 3);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 4);
COLLAPSE.superclass.constructor.apply(this, arguments);
};

_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 7);
Y.extend(COLLAPSE, Y.Base, {
    // A location to store the node toggling template so that we do not have to create it each time.
    toggleNodeTemplate : null,

    /**
      * Set up basic values for static access.
      */
    init : function() {
        _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "init", 14);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 15);
this.initialise_toggles(10);
    },

    /**
     * Has TinyMCE been loaded and the editors been initialised?
     * Designed mainly for IE
     * @return bool
     */
    editors_initialised : function() {
        _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "editors_initialised", 23);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 24);
return typeof tinyMCE !== 'undefined';
    },

    initialise_toggles : function(refreshes) {
        _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "initialise_toggles", 27);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 28);
var editors_initialised = this.editors_initialised(), self = this, editor;
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 29);
if (!editors_initialised && refreshes) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 30);
setTimeout(function() {
                    _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "(anonymous 2)", 30);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 31);
self.initialise_toggles(refreshes - 1);
                }, 100);
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 33);
return;
        }

        // Create the toggle template for use later
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 37);
this.toggleNodeTemplate = Y.Node.create('<a class="toggle_editor_toolbar" />');
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 38);
this.toggleNodeTemplate.setContent(M.util.get_string('showeditortoolbar', 'form'));

        // Delegate clicks of the toggle_editor_toolbar
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 41);
Y.one('body').delegate('click', this.toggle_collapse_from_event, 'a.toggle_editor_toolbar', this);

        // Set up editors which have already been created
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 44);
for (editor in tinyMCE.editors) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 45);
this.setup_collapse(tinyMCE.editors[editor]);
        }

        // Set up for future editors.
        // I haven't yet found a way of directly delegating the editor.onInit event. Instead we have to listen for the
        // tinyMCE.onAddEditor event, and then add a further event listener to the editor's onInit event.
        // onAddEditor is triggered before the editor has been created.
        // We use Y.Bind to ensure that context is maintained.
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 53);
tinyMCE.onAddEditor.add(Y.bind(this.add_setup_collapse_listener, this));

    },

    /**
      * Setup a listener for a new editor which will actually set the editor up
      * @param {Manager} mgr
      * @param {Editor} ed
      */
    add_setup_collapse_listener : function (mgr, ed) {
        // Bind the editor.onInit function to set this editor up. This ensures we maintain our context (this)
        _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "add_setup_collapse_listener", 62);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 64);
ed.onInit.add(Y.bind(this.setup_collapse, this));
    },

    /**
      * Setup the toggle system for the provided editor
      *
      * @param {Editor} ed The TinyMCE editor instance
      */
    setup_collapse : function(ed) {
        _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "setup_collapse", 72);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 73);
var textarea = Y.Node(ed.getElement()),
            editortable = Y.Node(ed.getContainer()).one('> table'),
            thisToggleNode;

        // Does this text area support collapsing at all?
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 78);
if (!textarea.hasClass('collapsible')) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 79);
return;
        }

        // Did we find an appropriate table to work with
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 83);
if (!editortable) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 84);
return;
        }

        // Add toggle button.
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 88);
thisToggleNode = this.toggleNodeTemplate.cloneNode(true);
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 89);
editortable.get('parentNode').insert(thisToggleNode, editortable);

        // Toggle the toolbars initially.
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 92);
if (Y.Node(ed.getElement()).hasClass('collapsed')) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 93);
this.toggle_collapse(thisToggleNode, editortable, 0);
        } else {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 95);
this.toggle_collapse(thisToggleNode, editortable, 1);
        }
    },

    /**
      * Toggle the specified editor toolbars.
      *
      * @param {Node} button The toggle button which we have to change the text for
      * @param {Node} editortable The table which the tinyMCE editor is in
      * @param {Boolean} newstate The intended toggle state
      */
    toggle_collapse : function(button, editortable, newstate) {
        _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "toggle_collapse", 106);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 107);
var toolbar = editortable.one('td.mceToolbar').ancestor('tr'),
            statusbar = editortable.one('.mceStatusbar').ancestor('tr'),
            editor, iframe, size;

        // Check whether we have a state already.
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 112);
if (typeof newstate === 'undefined') {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 113);
if (toolbar.getStyle('display') === 'none') {
                _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 114);
newstate = 1;
            } else {
                _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 116);
newstate = 0;
            }
        }

        // Toggle the various states and update the button text to suit
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 121);
if (newstate === 0) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 122);
toolbar.hide();
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 123);
statusbar.hide();
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 124);
button.setContent(M.util.get_string('showeditortoolbar', 'form'));
        } else {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 126);
toolbar.show();
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 127);
statusbar.show();
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 128);
button.setContent(M.util.get_string('hideeditortoolbar', 'form'));
        }

        // TinyMCE renders the toolbar and path bar as part of the textarea. So toggling these items
        // changes the required size of the rendered textarea. Frustrating but it's the way it's built.
        // So we get TinyMCE to resize itself for us. Clunky but it works.

        // Get the tinyMCE editor object for this text area.
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 136);
editorid = editortable.ancestor('div').one('textarea').get('id');
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 137);
editor = tinyMCE.getInstanceById(editorid);

        // Somehow, this editor did not exist.
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 140);
if (!editor) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 141);
return;
        }

        // Resize editor to reflect presence of toolbar and path bar..
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 145);
iframe = editor.getBody();
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 146);
if (iframe) {
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 147);
size = tinymce.DOM.getSize(iframe);
            // If objects exist resize editor.
            _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 149);
if (size) {
                _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 150);
editor.theme.resizeTo(size.w, size.h);
            }
        }
    },

    toggle_collapse_from_event : function(thisevent) {
        _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "toggle_collapse_from_event", 155);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 156);
var button = thisevent.target.ancestor('a', true),
            editortable = thisevent.target.ancestor('span', true).one('table.mceLayout');
        _yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 158);
this.toggle_collapse(button, editortable);
    }
});

_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 162);
M.editor_collapse = M.editor_collapse || {};
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 163);
M.editor_collapse.init = function(params) {
    _yuitest_coverfunc("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", "init", 163);
_yuitest_coverline("build/moodle-editor_tinymce-collapse/moodle-editor_tinymce-collapse.js", 164);
return new COLLAPSE(params);
};


}, '@VERSION@', {"requires": ["base", "node", "dom"]});
