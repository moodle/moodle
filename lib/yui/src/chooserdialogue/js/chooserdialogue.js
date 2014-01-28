var CHOOSERDIALOGUE = function() {
    CHOOSERDIALOGUE.superclass.constructor.apply(this, arguments);
};

Y.extend(CHOOSERDIALOGUE, Y.Base, {
    // The panel widget
    panel: null,

    // The submit button - we disable this until an element is set
    submitbutton : null,

    // The chooserdialogue container
    container : null,

    // Any event listeners we may need to cancel later
    listenevents : [],

    bodycontent : null,
    headercontent : null,
    instanceconfig : null,

    setup_chooser_dialogue : function(bodycontent, headercontent, config) {
        this.bodycontent = bodycontent;
        this.headercontent = headercontent;
        this.instanceconfig = config;
    },

    prepare_chooser : function () {
        if (this.panel) {
            return;
        }

        // Set Default options
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
            render : false,
            closeButtonTitle : this.get('closeButtonTitle')
        };

        // Override with additional options
        for (paramkey in this.instanceconfig) {
          params[paramkey] = this.instanceconfig[paramkey];
        }

        // Create the panel
        this.panel = new M.core.dialogue(params);

        // Remove the template for the chooser
        this.bodycontent.remove();
        this.headercontent.remove();

        // Hide and then render the panel
        this.panel.hide();
        this.panel.render();

        // Set useful links
        this.container = this.panel.get('boundingBox').one('.choosercontainer');
        this.options = this.container.all('.option input[type=radio]');

        // Add the chooserdialogue class to the container for styling
        this.panel.get('boundingBox').addClass('chooserdialogue');
    },

    /**
      * Display the module chooser
      *
      * @param e Event Triggering Event
      * @return void
      */
    display_chooser : function (e) {
        var bb, dialogue, thisevent;
        this.prepare_chooser();

        // Stop the default event actions before we proceed
        e.preventDefault();

        bb = this.panel.get('boundingBox');
        dialogue = this.container.one('.alloptions');

        // This will detect a change in orientation and retrigger centering
        thisevent = Y.one('document').on('orientationchange', function() {
            this.center_dialogue(dialogue);
        }, this);
        this.listenevents.push(thisevent);

        // Detect window resizes (most browsers)
        thisevent = Y.one('window').on('resize', function() {
            this.center_dialogue(dialogue);
        }, this);
        this.listenevents.push(thisevent);

        // These will trigger a check_options call to display the correct help
        thisevent = this.container.on('click', this.check_options, this);
        this.listenevents.push(thisevent);
        thisevent = this.container.on('key_up', this.check_options, this);
        this.listenevents.push(thisevent);
        thisevent = this.container.on('dblclick', function(e) {
            if (e.target.ancestor('div.option')) {
                this.check_options();

                // Prevent duplicate submissions
                this.submitbutton.setAttribute('disabled', 'disabled');
                this.options.setAttribute('disabled', 'disabled');
                this.cancel_listenevents();

                this.container.one('form').submit();
            }
        }, this);
        this.listenevents.push(thisevent);

        this.container.one('form').on('submit', function() {
            // Prevent duplicate submissions on submit
            this.submitbutton.setAttribute('disabled', 'disabled');
            this.options.setAttribute('disabled', 'disabled');
            this.cancel_listenevents();
        }, this);

        // Hook onto the cancel button to hide the form
        thisevent = this.container.one('.addcancel').on('click', this.cancel_popup, this);
        this.listenevents.push(thisevent);

        // Hide will be managed by cancel_popup after restoring the body overflow
        thisevent = bb.one('button.closebutton').on('click', this.cancel_popup, this);
        this.listenevents.push(thisevent);

        // Grab global keyup events and handle them
        thisevent = Y.one('document').on('keydown', this.handle_key_press, this);
        this.listenevents.push(thisevent);

        // Add references to various elements we adjust
        this.jumplink     = this.container.one('.jump');
        this.submitbutton = this.container.one('.submitbutton');

        // Disable the submit element until the user makes a selection
        this.submitbutton.set('disabled', 'true');

        // Ensure that the options are shown
        this.options.removeAttribute('disabled');

        // Display the panel
        this.panel.show();

        // Re-centre the dialogue after we've shown it.
        this.center_dialogue(dialogue);

        // Finally, focus the first radio element - this enables form selection via the keyboard
        this.container.one('.option input[type=radio]').focus();

        // Trigger check_options to set the initial jumpurl
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
        var thisevent;
        while (this.listenevents.length) {
            thisevent = this.listenevents.shift();
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
        var bb = this.panel.get('boundingBox'),
            winheight = bb.get('winHeight'),
            newheight, totalheight;

        if (this.panel.shouldResizeFullscreen()) {
            // No custom sizing required for a fullscreen dialog.
            return;
        }

        // Try and set a sensible max-height -- this must be done before setting the top
        // Set a default height of 640px
        newheight = this.get('maxheight');
        if (winheight <= newheight) {
            // Deal with smaller window sizes
            if (winheight <= this.get('minheight')) {
                newheight = this.get('minheight');
            } else {
                newheight = winheight;
            }
        }

        // If the dialogue is larger than a reasonable minimum height, we
        // disable the page scrollbars.
        if (newheight > this.get('minheight')) {
            // Disable the page scrollbars.
            if (this.panel.lockScroll && !this.panel.lockScroll.isActive()) {
                this.panel.lockScroll.enableScrollLock();
            }
        } else {
            // Re-enable the page scrollbars.
            if (this.panel.lockScroll && this.panel.lockScroll.isActive()) {
                this.panel.lockScroll.disableScrollLock();
            }
        }

        // Take off 15px top and bottom for borders, plus 40px each for the title and button area before setting the
        // new max-height
        totalheight = newheight;
        newheight = newheight - (15 + 15 + 40 + 40);
        dialogue.setStyle('maxHeight', newheight + 'px');

        dialogueheight = bb.getStyle('height');
        if (dialogueheight.match(/.*px$/)) {
            dialogueheight = dialogueheight.replace(/px$/, '');
        } else {
            dialogueheight = totalheight;
        }

        if (dialogueheight < this.get('baseheight')) {
            dialogueheight = this.get('baseheight');
            dialogue.setStyle('height', dialogueheight + 'px');
        }

        this.panel.centerDialogue();
    },

    handle_key_press : function(e) {
        if (e.keyCode === 27) {
            this.cancel_popup(e);
        }
    },

    cancel_popup : function (e) {
        // Prevent normal form submission before hiding
        e.preventDefault();
        this.hide();
    },

    hide : function() {
        // Cancel all listen events
        this.cancel_listenevents();

        this.container.detachAll();
        this.panel.hide();
    },

    check_options : function() {
        // Check which options are set, and change the parent class
        // to show/hide help as required
        this.options.each(function(thisoption) {
            var optiondiv = thisoption.get('parentNode').get('parentNode');
            if (thisoption.get('checked')) {
                optiondiv.addClass('selected');

                // Trigger any events for this option
                this.option_selected(thisoption);

                // Ensure that the form may be submitted
                this.submitbutton.removeAttribute('disabled');

                // Ensure that the radio remains focus so that keyboard navigation is still possible
                thisoption.focus();
            } else {
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
M.core = M.core || {};
M.core.chooserdialogue = CHOOSERDIALOGUE;
