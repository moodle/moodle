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
_yuitest_coverage["build/moodle-core-popuphelp/moodle-core-popuphelp.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-core-popuphelp/moodle-core-popuphelp.js",
    code: []
};
_yuitest_coverage["build/moodle-core-popuphelp/moodle-core-popuphelp.js"].code=["YUI.add('moodle-core-popuphelp', function (Y, NAME) {","","function POPUPHELP() {","    POPUPHELP.superclass.constructor.apply(this, arguments);","}","","var SELECTORS = {","        CLICKABLELINKS: 'span.helptooltip > a',","        FOOTER: 'div.moodle-dialogue-ft'","    },","","    CSS = {","        ICON: 'icon',","        ICONPRE: 'icon-pre'","    },","    ATTRS = {};","","// Set the modules base properties.","POPUPHELP.NAME = 'moodle-core-popuphelp';","POPUPHELP.ATTRS = ATTRS;","","Y.extend(POPUPHELP, Y.Base, {","    panel: null,","","    initializer: function() {","        Y.one('body').delegate('click', this.display_panel, SELECTORS.CLICKABLELINKS, this);","    },","","    display_panel: function(e) {","        if (!this.panel) {","            this.panel = new M.core.tooltip({","                bodyhandler: this.set_body_content,","                footerhandler: this.set_footer,","                initialheadertext: M.util.get_string('loadinghelp', 'moodle'),","                initialfootertext: ''","            });","        }","","        // Call the tooltip setup.","        this.panel.display_panel(e);","    },","","    /**","      * Override the footer handler to add a 'More help' link where relevant.","      *","      * @param {Object} helpobject The object returned from the AJAX call.","      */","    set_footer: function(helpobject) {","        // Check for an optional link to documentation on moodle.org.","        if (helpobject.doclink) {","            // Wrap a help icon and the morehelp text in an anchor. The class of the anchor should","            // determine whether it's opened in a new window or not.","            doclink = Y.Node.create('<a />')","                .setAttrs({","                    'href': helpobject.doclink.link","                })","                .addClass(helpobject.doclink['class']);","            helpicon = Y.Node.create('<img />')","                .setAttrs({","                    'src': M.util.image_url('docs', 'core')","                })","                .addClass(CSS.ICON)","                .addClass(CSS.ICONPRE);","            doclink.appendChild(helpicon);","            doclink.appendChild(helpobject.doclink.linktext);","","            // Set the footerContent to the contents of the doclink.","            this.set('footerContent', doclink);","            this.bb.one(SELECTORS.FOOTER).show();","        } else {","            this.bb.one(SELECTORS.FOOTER).hide();","        }","    }","});","M.core = M.core || {};","M.core.popuphelp = M.core.popuphelp || null;","M.core.init_popuphelp = M.core.init_popuphelp || function(config) {","    // Only set up a single instance of the popuphelp.","    if (!M.core.popuphelp) {","        M.core.popuphelp = new POPUPHELP(config);","    }","    return M.core.popuphelp;","};","","","}, '@VERSION@', {\"requires\": [\"moodle-core-tooltip\"]});"];
_yuitest_coverage["build/moodle-core-popuphelp/moodle-core-popuphelp.js"].lines = {"1":0,"3":0,"4":0,"7":0,"19":0,"20":0,"22":0,"26":0,"30":0,"31":0,"40":0,"50":0,"53":0,"58":0,"64":0,"65":0,"68":0,"69":0,"71":0,"75":0,"76":0,"77":0,"79":0,"80":0,"82":0};
_yuitest_coverage["build/moodle-core-popuphelp/moodle-core-popuphelp.js"].functions = {"POPUPHELP:3":0,"initializer:25":0,"display_panel:29":0,"set_footer:48":0,"(anonymous 2):77":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-core-popuphelp/moodle-core-popuphelp.js"].coveredLines = 25;
_yuitest_coverage["build/moodle-core-popuphelp/moodle-core-popuphelp.js"].coveredFunctions = 6;
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 1);
YUI.add('moodle-core-popuphelp', function (Y, NAME) {

_yuitest_coverfunc("build/moodle-core-popuphelp/moodle-core-popuphelp.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 3);
function POPUPHELP() {
    _yuitest_coverfunc("build/moodle-core-popuphelp/moodle-core-popuphelp.js", "POPUPHELP", 3);
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 4);
POPUPHELP.superclass.constructor.apply(this, arguments);
}

_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 7);
var SELECTORS = {
        CLICKABLELINKS: 'span.helptooltip > a',
        FOOTER: 'div.moodle-dialogue-ft'
    },

    CSS = {
        ICON: 'icon',
        ICONPRE: 'icon-pre'
    },
    ATTRS = {};

// Set the modules base properties.
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 19);
POPUPHELP.NAME = 'moodle-core-popuphelp';
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 20);
POPUPHELP.ATTRS = ATTRS;

_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 22);
Y.extend(POPUPHELP, Y.Base, {
    panel: null,

    initializer: function() {
        _yuitest_coverfunc("build/moodle-core-popuphelp/moodle-core-popuphelp.js", "initializer", 25);
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 26);
Y.one('body').delegate('click', this.display_panel, SELECTORS.CLICKABLELINKS, this);
    },

    display_panel: function(e) {
        _yuitest_coverfunc("build/moodle-core-popuphelp/moodle-core-popuphelp.js", "display_panel", 29);
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 30);
if (!this.panel) {
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 31);
this.panel = new M.core.tooltip({
                bodyhandler: this.set_body_content,
                footerhandler: this.set_footer,
                initialheadertext: M.util.get_string('loadinghelp', 'moodle'),
                initialfootertext: ''
            });
        }

        // Call the tooltip setup.
        _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 40);
this.panel.display_panel(e);
    },

    /**
      * Override the footer handler to add a 'More help' link where relevant.
      *
      * @param {Object} helpobject The object returned from the AJAX call.
      */
    set_footer: function(helpobject) {
        // Check for an optional link to documentation on moodle.org.
        _yuitest_coverfunc("build/moodle-core-popuphelp/moodle-core-popuphelp.js", "set_footer", 48);
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 50);
if (helpobject.doclink) {
            // Wrap a help icon and the morehelp text in an anchor. The class of the anchor should
            // determine whether it's opened in a new window or not.
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 53);
doclink = Y.Node.create('<a />')
                .setAttrs({
                    'href': helpobject.doclink.link
                })
                .addClass(helpobject.doclink['class']);
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 58);
helpicon = Y.Node.create('<img />')
                .setAttrs({
                    'src': M.util.image_url('docs', 'core')
                })
                .addClass(CSS.ICON)
                .addClass(CSS.ICONPRE);
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 64);
doclink.appendChild(helpicon);
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 65);
doclink.appendChild(helpobject.doclink.linktext);

            // Set the footerContent to the contents of the doclink.
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 68);
this.set('footerContent', doclink);
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 69);
this.bb.one(SELECTORS.FOOTER).show();
        } else {
            _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 71);
this.bb.one(SELECTORS.FOOTER).hide();
        }
    }
});
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 75);
M.core = M.core || {};
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 76);
M.core.popuphelp = M.core.popuphelp || null;
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 77);
M.core.init_popuphelp = M.core.init_popuphelp || function(config) {
    // Only set up a single instance of the popuphelp.
    _yuitest_coverfunc("build/moodle-core-popuphelp/moodle-core-popuphelp.js", "(anonymous 2)", 77);
_yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 79);
if (!M.core.popuphelp) {
        _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 80);
M.core.popuphelp = new POPUPHELP(config);
    }
    _yuitest_coverline("build/moodle-core-popuphelp/moodle-core-popuphelp.js", 82);
return M.core.popuphelp;
};


}, '@VERSION@', {"requires": ["moodle-core-tooltip"]});
