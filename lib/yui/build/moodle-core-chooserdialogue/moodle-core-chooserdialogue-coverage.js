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
_yuitest_coverage["build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js",
    code: []
};
_yuitest_coverage["build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js"].code=["YUI.add('moodle-core-chooserdialogue', function (Y, NAME) {","","var CHOOSERDIALOGUE = function() {","    CHOOSERDIALOGUE.superclass.constructor.apply(this, arguments);","};","","Y.extend(CHOOSERDIALOGUE, Y.Base, {","    // The panel widget","    panel: null,","","    // The submit button - we disable this until an element is set","    submitbutton : null,","","    // The chooserdialogue container","    container : null,","","    // Any event listeners we may need to cancel later","    listenevents : [],","","    // The initial overflow setting","    initialoverflow : '',","","    bodycontent : null,","    headercontent : null,","    instanceconfig : null,","","    setup_chooser_dialogue : function(bodycontent, headercontent, config) {","        this.bodycontent = bodycontent;","        this.headercontent = headercontent;","        this.instanceconfig = config;","    },","","    prepare_chooser : function () {","        if (this.panel) {","            return;","        }","","        // Set Default options","        var paramkey,","            params = {","            bodyContent : this.bodycontent.get('innerHTML'),","            headerContent : this.headercontent.get('innerHTML'),","            width : '540px',","            draggable : true,","            visible : false, // Hide by default","            zindex : 100, // Display in front of other items","            lightbox : true, // This dialogue should be modal","            shim : true,","            closeButtonTitle : this.get('closeButtonTitle')","        };","","        // Override with additional options","        for (paramkey in this.instanceconfig) {","          params[paramkey] = this.instanceconfig[paramkey];","        }","","        // Create the panel","        this.panel = new M.core.dialogue(params);","","        // Remove the template for the chooser","        this.bodycontent.remove();","        this.headercontent.remove();","","        // Hide and then render the panel","        this.panel.hide();","        this.panel.render();","","        // Set useful links","        this.container = this.panel.get('boundingBox').one('.choosercontainer');","        this.options = this.container.all('.option input[type=radio]');","","        // Add the chooserdialogue class to the container for styling","        this.panel.get('boundingBox').addClass('chooserdialogue');","    },","","    /**","      * Display the module chooser","      *","      * @param e Event Triggering Event","      * @return void","      */","    display_chooser : function (e) {","        var bb, dialogue, thisevent;","        this.prepare_chooser();","","        // Stop the default event actions before we proceed","        e.preventDefault();","","        bb = this.panel.get('boundingBox');","        dialogue = this.container.one('.alloptions');","","        // Get the overflow setting when the chooser was opened - we","        // may need this later","        if (Y.UA.ie > 0) {","            this.initialoverflow = Y.one('html').getStyle('overflow');","        } else {","            this.initialoverflow = Y.one('body').getStyle('overflow');","        }","","        // This will detect a change in orientation and retrigger centering","        thisevent = Y.one('document').on('orientationchange', function() {","            this.center_dialogue(dialogue);","        }, this);","        this.listenevents.push(thisevent);","","        // Detect window resizes (most browsers)","        thisevent = Y.one('window').on('resize', function() {","            this.center_dialogue(dialogue);","        }, this);","        this.listenevents.push(thisevent);","","        // These will trigger a check_options call to display the correct help","        thisevent = this.container.on('click', this.check_options, this);","        this.listenevents.push(thisevent);","        thisevent = this.container.on('key_up', this.check_options, this);","        this.listenevents.push(thisevent);","        thisevent = this.container.on('dblclick', function(e) {","            if (e.target.ancestor('div.option')) {","                this.check_options();","","                // Prevent duplicate submissions","                this.submitbutton.setAttribute('disabled', 'disabled');","                this.options.setAttribute('disabled', 'disabled');","                this.cancel_listenevents();","","                this.container.one('form').submit();","            }","        }, this);","        this.listenevents.push(thisevent);","","        this.container.one('form').on('submit', function() {","            // Prevent duplicate submissions on submit","            this.submitbutton.setAttribute('disabled', 'disabled');","            this.options.setAttribute('disabled', 'disabled');","            this.cancel_listenevents();","        }, this);","","        // Hook onto the cancel button to hide the form","        thisevent = this.container.one('.addcancel').on('click', this.cancel_popup, this);","        this.listenevents.push(thisevent);","","        // Hide will be managed by cancel_popup after restoring the body overflow","        thisevent = bb.one('button.closebutton').on('click', this.cancel_popup, this);","        this.listenevents.push(thisevent);","","        // Grab global keyup events and handle them","        thisevent = Y.one('document').on('keydown', this.handle_key_press, this);","        this.listenevents.push(thisevent);","","        // Add references to various elements we adjust","        this.jumplink     = this.container.one('.jump');","        this.submitbutton = this.container.one('.submitbutton');","","        // Disable the submit element until the user makes a selection","        this.submitbutton.set('disabled', 'true');","","        // Ensure that the options are shown","        this.options.removeAttribute('disabled');","","        // Display the panel","        this.panel.show();","","        // Re-centre the dialogue after we've shown it.","        this.center_dialogue(dialogue);","","        // Finally, focus the first radio element - this enables form selection via the keyboard","        this.container.one('.option input[type=radio]').focus();","","        // Trigger check_options to set the initial jumpurl","        this.check_options();","    },","","    /**","      * Cancel any listen events in the listenevents queue","      *","      * Several locations add event handlers which should only be called before the form is submitted. This provides","      * a way of cancelling those events.","      *","      * @return void","      */","    cancel_listenevents : function () {","        // Detach all listen events to prevent duplicate triggers","        var thisevent;","        while (this.listenevents.length) {","            thisevent = this.listenevents.shift();","            thisevent.detach();","        }","    },","","    /**","      * Calculate the optimum height of the chooser dialogue","      *","      * This tries to set a sensible maximum and minimum to ensure that some options are always shown, and preferably","      * all, whilst fitting the box within the current viewport.","      *","      * @param dialogue Y.Node The dialogue","      * @return void","      */","    center_dialogue : function(dialogue) {","        var bb = this.panel.get('boundingBox'),","            winheight = bb.get('winHeight'),","            winwidth = bb.get('winWidth'),","            offsettop = 0,","            newheight, totalheight, dialoguetop, dialoguewidth, dialogueleft;","","        // Try and set a sensible max-height -- this must be done before setting the top","        // Set a default height of 640px","        newheight = this.get('maxheight');","        if (winheight <= newheight) {","            // Deal with smaller window sizes","            if (winheight <= this.get('minheight')) {","                newheight = this.get('minheight');","            } else {","                newheight = winheight;","            }","        }","","        // Set a fixed position if the window is large enough","        if (newheight > this.get('minheight')) {","            bb.setStyle('position', 'fixed');","            // Disable the page scrollbars","            if (Y.UA.ie > 0) {","                Y.one('html').setStyle('overflow', 'hidden');","            } else {","                Y.one('body').setStyle('overflow', 'hidden');","            }","        } else {","            bb.setStyle('position', 'absolute');","            offsettop = Y.one('window').get('scrollTop');","            // Ensure that the page scrollbars are enabled","            if (Y.UA.ie > 0) {","                Y.one('html').setStyle('overflow', this.initialoverflow);","            } else {","                Y.one('body').setStyle('overflow', this.initialoverflow);","            }","        }","","        // Take off 15px top and bottom for borders, plus 40px each for the title and button area before setting the","        // new max-height","        totalheight = newheight;","        newheight = newheight - (15 + 15 + 40 + 40);","        dialogue.setStyle('maxHeight', newheight + 'px');","","        dialogueheight = bb.getStyle('height');","        if (dialogueheight.match(/.*px$/)) {","            dialogueheight = dialogueheight.replace(/px$/, '');","        } else {","            dialogueheight = totalheight;","        }","","        if (dialogueheight < this.get('baseheight')) {","            dialogueheight = this.get('baseheight');","            dialogue.setStyle('height', dialogueheight + 'px');","        }","","","        // Re-calculate the location now that we've changed the size","        dialoguetop = Math.max(12, ((winheight - dialogueheight) / 2)) + offsettop;","","        // We need to set the height for the yui3-widget - can't work","        // out what we're setting at present -- shoud be the boudingBox","        bb.setStyle('top', dialoguetop + 'px');","","        // Calculate the left location of the chooser","        // We don't set a minimum width in the same way as we do height as the width would be far lower than the","        // optimal width for moodle anyway.","        dialoguewidth = bb.get('offsetWidth');","        dialogueleft = (winwidth - dialoguewidth) / 2;","        bb.setStyle('left', dialogueleft + 'px');","    },","","    handle_key_press : function(e) {","        if (e.keyCode === 27) {","            this.cancel_popup(e);","        }","    },","","    cancel_popup : function (e) {","        // Prevent normal form submission before hiding","        e.preventDefault();","        this.hide();","    },","","    hide : function() {","        // Cancel all listen events","        this.cancel_listenevents();","","        // Re-enable the page scrollbars","        if (Y.UA.ie > 0) {","            Y.one('html').setStyle('overflow', this.initialoverflow);","        } else {","            Y.one('body').setStyle('overflow', this.initialoverflow);","        }","","        this.container.detachAll();","        this.panel.hide();","    },","","    check_options : function() {","        // Check which options are set, and change the parent class","        // to show/hide help as required","        this.options.each(function(thisoption) {","            var optiondiv = thisoption.get('parentNode').get('parentNode');","            if (thisoption.get('checked')) {","                optiondiv.addClass('selected');","","                // Trigger any events for this option","                this.option_selected(thisoption);","","                // Ensure that the form may be submitted","                this.submitbutton.removeAttribute('disabled');","","                // Ensure that the radio remains focus so that keyboard navigation is still possible","                thisoption.focus();","            } else {","                optiondiv.removeClass('selected');","            }","        }, this);","    },","","    option_selected : function() {","    }","},","{","    NAME : 'moodle-core-chooserdialogue',","    ATTRS : {","        minheight : {","            value : 300","        },","        baseheight: {","            value : 400","        },","        maxheight : {","            value : 660","        },","        closeButtonTitle : {","            validator : Y.Lang.isString,","            value : 'Close'","        }","    }","});","M.core = M.core || {};","M.core.chooserdialogue = CHOOSERDIALOGUE;","","","}, '@VERSION@', {\"requires\": [\"base\", \"panel\", \"moodle-core-notification\"]});"];
_yuitest_coverage["build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js"].lines = {"1":0,"3":0,"4":0,"7":0,"28":0,"29":0,"30":0,"34":0,"35":0,"39":0,"53":0,"54":0,"58":0,"61":0,"62":0,"65":0,"66":0,"69":0,"70":0,"73":0,"83":0,"84":0,"87":0,"89":0,"90":0,"94":0,"95":0,"97":0,"101":0,"102":0,"104":0,"107":0,"108":0,"110":0,"113":0,"114":0,"115":0,"116":0,"117":0,"118":0,"119":0,"122":0,"123":0,"124":0,"126":0,"129":0,"131":0,"133":0,"134":0,"135":0,"139":0,"140":0,"143":0,"144":0,"147":0,"148":0,"151":0,"152":0,"155":0,"158":0,"161":0,"164":0,"167":0,"170":0,"183":0,"184":0,"185":0,"186":0,"200":0,"208":0,"209":0,"211":0,"212":0,"214":0,"219":0,"220":0,"222":0,"223":0,"225":0,"228":0,"229":0,"231":0,"232":0,"234":0,"240":0,"241":0,"242":0,"244":0,"245":0,"246":0,"248":0,"251":0,"252":0,"253":0,"258":0,"262":0,"267":0,"268":0,"269":0,"273":0,"274":0,"280":0,"281":0,"286":0,"289":0,"290":0,"292":0,"295":0,"296":0,"302":0,"303":0,"304":0,"305":0,"308":0,"311":0,"314":0,"316":0,"342":0,"343":0};
_yuitest_coverage["build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js"].functions = {"CHOOSERDIALOGUE:3":0,"setup_chooser_dialogue:27":0,"prepare_chooser:33":0,"(anonymous 2):101":0,"(anonymous 3):107":0,"(anonymous 4):117":0,"(anonymous 5):131":0,"display_chooser:82":0,"cancel_listenevents:181":0,"center_dialogue:199":0,"handle_key_press:272":0,"cancel_popup:278":0,"hide:284":0,"(anonymous 6):302":0,"check_options:299":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js"].coveredLines = 119;
_yuitest_coverage["build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js"].coveredFunctions = 16;
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 1);
YUI.add('moodle-core-chooserdialogue', function (Y, NAME) {

_yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 3);
var CHOOSERDIALOGUE = function() {
    _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "CHOOSERDIALOGUE", 3);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 4);
CHOOSERDIALOGUE.superclass.constructor.apply(this, arguments);
};

_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 7);
Y.extend(CHOOSERDIALOGUE, Y.Base, {
    // The panel widget
    panel: null,

    // The submit button - we disable this until an element is set
    submitbutton : null,

    // The chooserdialogue container
    container : null,

    // Any event listeners we may need to cancel later
    listenevents : [],

    // The initial overflow setting
    initialoverflow : '',

    bodycontent : null,
    headercontent : null,
    instanceconfig : null,

    setup_chooser_dialogue : function(bodycontent, headercontent, config) {
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "setup_chooser_dialogue", 27);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 28);
this.bodycontent = bodycontent;
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 29);
this.headercontent = headercontent;
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 30);
this.instanceconfig = config;
    },

    prepare_chooser : function () {
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "prepare_chooser", 33);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 34);
if (this.panel) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 35);
return;
        }

        // Set Default options
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 39);
var paramkey,
            params = {
            bodyContent : this.bodycontent.get('innerHTML'),
            headerContent : this.headercontent.get('innerHTML'),
            width : '540px',
            draggable : true,
            visible : false, // Hide by default
            zindex : 100, // Display in front of other items
            lightbox : true, // This dialogue should be modal
            shim : true,
            closeButtonTitle : this.get('closeButtonTitle')
        };

        // Override with additional options
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 53);
for (paramkey in this.instanceconfig) {
          _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 54);
params[paramkey] = this.instanceconfig[paramkey];
        }

        // Create the panel
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 58);
this.panel = new M.core.dialogue(params);

        // Remove the template for the chooser
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 61);
this.bodycontent.remove();
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 62);
this.headercontent.remove();

        // Hide and then render the panel
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 65);
this.panel.hide();
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 66);
this.panel.render();

        // Set useful links
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 69);
this.container = this.panel.get('boundingBox').one('.choosercontainer');
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 70);
this.options = this.container.all('.option input[type=radio]');

        // Add the chooserdialogue class to the container for styling
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 73);
this.panel.get('boundingBox').addClass('chooserdialogue');
    },

    /**
      * Display the module chooser
      *
      * @param e Event Triggering Event
      * @return void
      */
    display_chooser : function (e) {
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "display_chooser", 82);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 83);
var bb, dialogue, thisevent;
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 84);
this.prepare_chooser();

        // Stop the default event actions before we proceed
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 87);
e.preventDefault();

        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 89);
bb = this.panel.get('boundingBox');
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 90);
dialogue = this.container.one('.alloptions');

        // Get the overflow setting when the chooser was opened - we
        // may need this later
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 94);
if (Y.UA.ie > 0) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 95);
this.initialoverflow = Y.one('html').getStyle('overflow');
        } else {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 97);
this.initialoverflow = Y.one('body').getStyle('overflow');
        }

        // This will detect a change in orientation and retrigger centering
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 101);
thisevent = Y.one('document').on('orientationchange', function() {
            _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "(anonymous 2)", 101);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 102);
this.center_dialogue(dialogue);
        }, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 104);
this.listenevents.push(thisevent);

        // Detect window resizes (most browsers)
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 107);
thisevent = Y.one('window').on('resize', function() {
            _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "(anonymous 3)", 107);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 108);
this.center_dialogue(dialogue);
        }, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 110);
this.listenevents.push(thisevent);

        // These will trigger a check_options call to display the correct help
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 113);
thisevent = this.container.on('click', this.check_options, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 114);
this.listenevents.push(thisevent);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 115);
thisevent = this.container.on('key_up', this.check_options, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 116);
this.listenevents.push(thisevent);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 117);
thisevent = this.container.on('dblclick', function(e) {
            _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "(anonymous 4)", 117);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 118);
if (e.target.ancestor('div.option')) {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 119);
this.check_options();

                // Prevent duplicate submissions
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 122);
this.submitbutton.setAttribute('disabled', 'disabled');
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 123);
this.options.setAttribute('disabled', 'disabled');
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 124);
this.cancel_listenevents();

                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 126);
this.container.one('form').submit();
            }
        }, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 129);
this.listenevents.push(thisevent);

        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 131);
this.container.one('form').on('submit', function() {
            // Prevent duplicate submissions on submit
            _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "(anonymous 5)", 131);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 133);
this.submitbutton.setAttribute('disabled', 'disabled');
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 134);
this.options.setAttribute('disabled', 'disabled');
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 135);
this.cancel_listenevents();
        }, this);

        // Hook onto the cancel button to hide the form
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 139);
thisevent = this.container.one('.addcancel').on('click', this.cancel_popup, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 140);
this.listenevents.push(thisevent);

        // Hide will be managed by cancel_popup after restoring the body overflow
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 143);
thisevent = bb.one('button.closebutton').on('click', this.cancel_popup, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 144);
this.listenevents.push(thisevent);

        // Grab global keyup events and handle them
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 147);
thisevent = Y.one('document').on('keydown', this.handle_key_press, this);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 148);
this.listenevents.push(thisevent);

        // Add references to various elements we adjust
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 151);
this.jumplink     = this.container.one('.jump');
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 152);
this.submitbutton = this.container.one('.submitbutton');

        // Disable the submit element until the user makes a selection
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 155);
this.submitbutton.set('disabled', 'true');

        // Ensure that the options are shown
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 158);
this.options.removeAttribute('disabled');

        // Display the panel
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 161);
this.panel.show();

        // Re-centre the dialogue after we've shown it.
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 164);
this.center_dialogue(dialogue);

        // Finally, focus the first radio element - this enables form selection via the keyboard
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 167);
this.container.one('.option input[type=radio]').focus();

        // Trigger check_options to set the initial jumpurl
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 170);
this.check_options();
    },

    /**
      * Cancel any listen events in the listenevents queue
      *
      * Several locations add event handlers which should only be called before the form is submitted. This provides
      * a way of cancelling those events.
      *
      * @return void
      */
    cancel_listenevents : function () {
        // Detach all listen events to prevent duplicate triggers
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "cancel_listenevents", 181);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 183);
var thisevent;
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 184);
while (this.listenevents.length) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 185);
thisevent = this.listenevents.shift();
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 186);
thisevent.detach();
        }
    },

    /**
      * Calculate the optimum height of the chooser dialogue
      *
      * This tries to set a sensible maximum and minimum to ensure that some options are always shown, and preferably
      * all, whilst fitting the box within the current viewport.
      *
      * @param dialogue Y.Node The dialogue
      * @return void
      */
    center_dialogue : function(dialogue) {
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "center_dialogue", 199);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 200);
var bb = this.panel.get('boundingBox'),
            winheight = bb.get('winHeight'),
            winwidth = bb.get('winWidth'),
            offsettop = 0,
            newheight, totalheight, dialoguetop, dialoguewidth, dialogueleft;

        // Try and set a sensible max-height -- this must be done before setting the top
        // Set a default height of 640px
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 208);
newheight = this.get('maxheight');
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 209);
if (winheight <= newheight) {
            // Deal with smaller window sizes
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 211);
if (winheight <= this.get('minheight')) {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 212);
newheight = this.get('minheight');
            } else {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 214);
newheight = winheight;
            }
        }

        // Set a fixed position if the window is large enough
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 219);
if (newheight > this.get('minheight')) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 220);
bb.setStyle('position', 'fixed');
            // Disable the page scrollbars
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 222);
if (Y.UA.ie > 0) {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 223);
Y.one('html').setStyle('overflow', 'hidden');
            } else {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 225);
Y.one('body').setStyle('overflow', 'hidden');
            }
        } else {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 228);
bb.setStyle('position', 'absolute');
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 229);
offsettop = Y.one('window').get('scrollTop');
            // Ensure that the page scrollbars are enabled
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 231);
if (Y.UA.ie > 0) {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 232);
Y.one('html').setStyle('overflow', this.initialoverflow);
            } else {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 234);
Y.one('body').setStyle('overflow', this.initialoverflow);
            }
        }

        // Take off 15px top and bottom for borders, plus 40px each for the title and button area before setting the
        // new max-height
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 240);
totalheight = newheight;
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 241);
newheight = newheight - (15 + 15 + 40 + 40);
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 242);
dialogue.setStyle('maxHeight', newheight + 'px');

        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 244);
dialogueheight = bb.getStyle('height');
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 245);
if (dialogueheight.match(/.*px$/)) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 246);
dialogueheight = dialogueheight.replace(/px$/, '');
        } else {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 248);
dialogueheight = totalheight;
        }

        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 251);
if (dialogueheight < this.get('baseheight')) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 252);
dialogueheight = this.get('baseheight');
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 253);
dialogue.setStyle('height', dialogueheight + 'px');
        }


        // Re-calculate the location now that we've changed the size
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 258);
dialoguetop = Math.max(12, ((winheight - dialogueheight) / 2)) + offsettop;

        // We need to set the height for the yui3-widget - can't work
        // out what we're setting at present -- shoud be the boudingBox
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 262);
bb.setStyle('top', dialoguetop + 'px');

        // Calculate the left location of the chooser
        // We don't set a minimum width in the same way as we do height as the width would be far lower than the
        // optimal width for moodle anyway.
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 267);
dialoguewidth = bb.get('offsetWidth');
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 268);
dialogueleft = (winwidth - dialoguewidth) / 2;
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 269);
bb.setStyle('left', dialogueleft + 'px');
    },

    handle_key_press : function(e) {
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "handle_key_press", 272);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 273);
if (e.keyCode === 27) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 274);
this.cancel_popup(e);
        }
    },

    cancel_popup : function (e) {
        // Prevent normal form submission before hiding
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "cancel_popup", 278);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 280);
e.preventDefault();
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 281);
this.hide();
    },

    hide : function() {
        // Cancel all listen events
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "hide", 284);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 286);
this.cancel_listenevents();

        // Re-enable the page scrollbars
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 289);
if (Y.UA.ie > 0) {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 290);
Y.one('html').setStyle('overflow', this.initialoverflow);
        } else {
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 292);
Y.one('body').setStyle('overflow', this.initialoverflow);
        }

        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 295);
this.container.detachAll();
        _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 296);
this.panel.hide();
    },

    check_options : function() {
        // Check which options are set, and change the parent class
        // to show/hide help as required
        _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "check_options", 299);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 302);
this.options.each(function(thisoption) {
            _yuitest_coverfunc("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", "(anonymous 6)", 302);
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 303);
var optiondiv = thisoption.get('parentNode').get('parentNode');
            _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 304);
if (thisoption.get('checked')) {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 305);
optiondiv.addClass('selected');

                // Trigger any events for this option
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 308);
this.option_selected(thisoption);

                // Ensure that the form may be submitted
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 311);
this.submitbutton.removeAttribute('disabled');

                // Ensure that the radio remains focus so that keyboard navigation is still possible
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 314);
thisoption.focus();
            } else {
                _yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 316);
optiondiv.removeClass('selected');
            }
        }, this);
    },

    option_selected : function() {
    }
},
{
    NAME : 'moodle-core-chooserdialogue',
    ATTRS : {
        minheight : {
            value : 300
        },
        baseheight: {
            value : 400
        },
        maxheight : {
            value : 660
        },
        closeButtonTitle : {
            validator : Y.Lang.isString,
            value : 'Close'
        }
    }
});
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 342);
M.core = M.core || {};
_yuitest_coverline("build/moodle-core-chooserdialogue/moodle-core-chooserdialogue.js", 343);
M.core.chooserdialogue = CHOOSERDIALOGUE;


}, '@VERSION@', {"requires": ["base", "panel", "moodle-core-notification"]});
