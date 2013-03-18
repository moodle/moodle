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
_yuitest_coverage["build/moodle-core-formautosubmit/moodle-core-formautosubmit.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-core-formautosubmit/moodle-core-formautosubmit.js",
    code: []
};
_yuitest_coverage["build/moodle-core-formautosubmit/moodle-core-formautosubmit.js"].code=["YUI.add('moodle-core-formautosubmit', function (Y, NAME) {","","var CSS,","    FORMAUTOSUBMITNAME = 'core-formautosubmit',","    FORMAUTOSUBMIT,","    INITIALIZED = false;","","// The CSS selectors we use","CSS = {","    AUTOSUBMIT : 'autosubmit'","};","","FORMAUTOSUBMIT = function() {","    FORMAUTOSUBMIT.superclass.constructor.apply(this, arguments);","};","","Y.extend(FORMAUTOSUBMIT, Y.Base, {","","    /**","      * Initialize the module","      */","    initializer : function() {","        // Set up local variables","        var applyto,","            thisselect;","        // We only apply the delegation once","        if (!INITIALIZED) {","            INITIALIZED = true;","            applyto = Y.one('body');","","            // We don't listen for change events by default as using the keyboard triggers these too.","            applyto.delegate('key', this.process_changes, 'press:13', 'select.' + CSS.AUTOSUBMIT, this);","            applyto.delegate('click', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);","","            if (Y.UA.os === 'macintosh' && Y.UA.webkit) {","                // Macintosh webkit browsers like change events, but non-macintosh webkit browsers don't.","                applyto.delegate('change', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);","            }","            if (Y.UA.ios) {","                // IOS doesn't trigger click events because it's touch-based.","                applyto.delegate('change', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);","            }","        }","","        // Assign this select items 'nothing' value and lastindex (current value)","        if (this.get('selectid')) {","            thisselect = Y.one('select#' + this.get('selectid'));","            if (thisselect) {","                if (this.get('nothing')) {","                    thisselect.setData('nothing', this.get('nothing'));","                }","                thisselect.setData('startindex', thisselect.get('selectedIndex'));","            } else {","            }","        }","    },","","    /**","      * Check whether the select element was changed","      */","    check_changed : function(e) {","        var select,","            nothing,","            startindex,","            currentindex,","            previousindex;","        select = e.target.ancestor('select.' + CSS.AUTOSUBMIT, true);","        if (!select) {","            return false;","        }","","        nothing = select.getData('nothing');","        startindex = select.getData('startindex');","        currentindex = select.get('selectedIndex');","","        previousindex = select.getAttribute('data-previousindex');","        select.setAttribute('data-previousindex', currentindex);","        if (!previousindex) {","            previousindex = startindex;","        }","","        // Check whether the field has changed, and is not the 'nothing' value","        if ((nothing===false || select.get('value') !== nothing)","                && startindex !== select.get('selectedIndex') && currentindex !== previousindex) {","            return select;","        }","        return false;","    },","","    /**","      * Process any changes","      */","    process_changes : function(e) {","        var select = this.check_changed(e),","            form;","        if (select) {","            form = select.ancestor('form', true);","            form.submit();","        }","    }","},","{","    NAME : FORMAUTOSUBMITNAME,","    ATTRS : {","        selectid : {","            'value' : ''","        },","        nothing : {","            'value' : ''","        },","        ignorechangeevent : {","            'value' : false","        }","    }","});","","M.core = M.core || {};","M.core.init_formautosubmit = M.core.init_formautosubmit || function(config) {","    return new FORMAUTOSUBMIT(config);","};","","","}, '@VERSION@', {\"requires\": [\"base\", \"event-key\"]});"];
_yuitest_coverage["build/moodle-core-formautosubmit/moodle-core-formautosubmit.js"].lines = {"1":0,"3":0,"9":0,"13":0,"14":0,"17":0,"24":0,"27":0,"28":0,"29":0,"32":0,"33":0,"35":0,"37":0,"39":0,"41":0,"46":0,"47":0,"48":0,"49":0,"50":0,"52":0,"62":0,"67":0,"68":0,"69":0,"72":0,"73":0,"74":0,"76":0,"77":0,"78":0,"79":0,"83":0,"85":0,"87":0,"94":0,"96":0,"97":0,"98":0,"117":0,"118":0,"119":0};
_yuitest_coverage["build/moodle-core-formautosubmit/moodle-core-formautosubmit.js"].functions = {"FORMAUTOSUBMIT:13":0,"initializer:22":0,"check_changed:61":0,"process_changes:93":0,"(anonymous 2):118":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-core-formautosubmit/moodle-core-formautosubmit.js"].coveredLines = 43;
_yuitest_coverage["build/moodle-core-formautosubmit/moodle-core-formautosubmit.js"].coveredFunctions = 6;
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 1);
YUI.add('moodle-core-formautosubmit', function (Y, NAME) {

_yuitest_coverfunc("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 3);
var CSS,
    FORMAUTOSUBMITNAME = 'core-formautosubmit',
    FORMAUTOSUBMIT,
    INITIALIZED = false;

// The CSS selectors we use
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 9);
CSS = {
    AUTOSUBMIT : 'autosubmit'
};

_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 13);
FORMAUTOSUBMIT = function() {
    _yuitest_coverfunc("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", "FORMAUTOSUBMIT", 13);
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 14);
FORMAUTOSUBMIT.superclass.constructor.apply(this, arguments);
};

_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 17);
Y.extend(FORMAUTOSUBMIT, Y.Base, {

    /**
      * Initialize the module
      */
    initializer : function() {
        // Set up local variables
        _yuitest_coverfunc("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", "initializer", 22);
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 24);
var applyto,
            thisselect;
        // We only apply the delegation once
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 27);
if (!INITIALIZED) {
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 28);
INITIALIZED = true;
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 29);
applyto = Y.one('body');

            // We don't listen for change events by default as using the keyboard triggers these too.
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 32);
applyto.delegate('key', this.process_changes, 'press:13', 'select.' + CSS.AUTOSUBMIT, this);
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 33);
applyto.delegate('click', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);

            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 35);
if (Y.UA.os === 'macintosh' && Y.UA.webkit) {
                // Macintosh webkit browsers like change events, but non-macintosh webkit browsers don't.
                _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 37);
applyto.delegate('change', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);
            }
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 39);
if (Y.UA.ios) {
                // IOS doesn't trigger click events because it's touch-based.
                _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 41);
applyto.delegate('change', this.process_changes, 'select.' + CSS.AUTOSUBMIT, this);
            }
        }

        // Assign this select items 'nothing' value and lastindex (current value)
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 46);
if (this.get('selectid')) {
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 47);
thisselect = Y.one('select#' + this.get('selectid'));
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 48);
if (thisselect) {
                _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 49);
if (this.get('nothing')) {
                    _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 50);
thisselect.setData('nothing', this.get('nothing'));
                }
                _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 52);
thisselect.setData('startindex', thisselect.get('selectedIndex'));
            } else {
            }
        }
    },

    /**
      * Check whether the select element was changed
      */
    check_changed : function(e) {
        _yuitest_coverfunc("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", "check_changed", 61);
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 62);
var select,
            nothing,
            startindex,
            currentindex,
            previousindex;
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 67);
select = e.target.ancestor('select.' + CSS.AUTOSUBMIT, true);
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 68);
if (!select) {
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 69);
return false;
        }

        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 72);
nothing = select.getData('nothing');
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 73);
startindex = select.getData('startindex');
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 74);
currentindex = select.get('selectedIndex');

        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 76);
previousindex = select.getAttribute('data-previousindex');
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 77);
select.setAttribute('data-previousindex', currentindex);
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 78);
if (!previousindex) {
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 79);
previousindex = startindex;
        }

        // Check whether the field has changed, and is not the 'nothing' value
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 83);
if ((nothing===false || select.get('value') !== nothing)
                && startindex !== select.get('selectedIndex') && currentindex !== previousindex) {
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 85);
return select;
        }
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 87);
return false;
    },

    /**
      * Process any changes
      */
    process_changes : function(e) {
        _yuitest_coverfunc("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", "process_changes", 93);
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 94);
var select = this.check_changed(e),
            form;
        _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 96);
if (select) {
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 97);
form = select.ancestor('form', true);
            _yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 98);
form.submit();
        }
    }
},
{
    NAME : FORMAUTOSUBMITNAME,
    ATTRS : {
        selectid : {
            'value' : ''
        },
        nothing : {
            'value' : ''
        },
        ignorechangeevent : {
            'value' : false
        }
    }
});

_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 117);
M.core = M.core || {};
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 118);
M.core.init_formautosubmit = M.core.init_formautosubmit || function(config) {
    _yuitest_coverfunc("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", "(anonymous 2)", 118);
_yuitest_coverline("build/moodle-core-formautosubmit/moodle-core-formautosubmit.js", 119);
return new FORMAUTOSUBMIT(config);
};


}, '@VERSION@', {"requires": ["base", "event-key"]});
