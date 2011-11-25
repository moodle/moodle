YUI.add('moodle-core-chooserdialogue', function(Y) {

    var CHOOSERDIALOGUE = function() {
        CHOOSERDIALOGUE.superclass.constructor.apply(this, arguments);
    }

    Y.extend(CHOOSERDIALOGUE, Y.Base, {
        // The overlay widget
        overlay: null,

        // The submit button - we disable this until an element is set
        submitbutton : null,

        // The chooserdialogue container
        container : null,

        setup_chooser_dialogue : function(bodycontent, headercontent, config) {
            // Set Default options
            var params = {
                bodyContent : bodycontent.get('innerHTML'),
                headerContent : headercontent.get('innerHTML'),
                visible : false, // Hide by default
                zindex : 100, // Display in front of other items
                lightbox : true, // This dialogue should be modal
                shim : true
            }

            // Override with additional options
            for (paramkey in config) {
              params[paramkey] = config[paramkey];
            }

            // Create the overlay
            this.overlay = new M.core.dialogue(params);

            // Remove the template for the chooser
            bodycontent.remove();
            headercontent.remove();

            // Hide and then render the overlay
            this.overlay.hide();
            this.overlay.render();

            // Set useful links
            this.container = this.overlay.get('boundingBox').one('#choosercontainer');
            this.options = this.container.all('.option input[type=radio]');
        },
        /**
         * Display the module chooser
         *
         * @param e Event Triggering Event
         * @return void
         */
        display_chooser : function (e) {
            // Stop the default event actions before we proceed
            e.preventDefault();

            var bb = this.overlay.get('boundingBox');
            var dialogue = this.container.one('.alloptions');

            // Set the dialogue height
            this.calculate_height(dialogue);

            // These will trigger a check_options call to display the correct help
            this.container.on('click', this.check_options, this);
            this.container.on('key_up', this.check_options, this);
            this.container.on('dblclick', function(e) {
                if (e.target.ancestor('div.option')) {
                    this.check_options();
                    this.container.one('form').submit();
                }
            }, this);

            // Hook onto the cancel button to hide the form
            this.container.one('#addcancel').on('click', this.cancel_popup, this);

            // Grab global keyup events and handle them
            Y.one('document').on('keyup', this.handle_key_press, this);

            // Add references to various elements we adjust
            this.jumplink     = this.container.one('#jump');
            this.submitbutton = this.container.one('#submitbutton');

            // Disable the submit element until the user makes a selection
            this.submitbutton.set('disabled', 'true');

            // Display the overlay
            this.overlay.show();

            // Finally, focus the first radio element - this enables form selection via the keyboard
            this.container.one('.option input[type=radio]').focus();

            // Trigger check_options to set the initial jumpurl
            this.check_options();
        },
        /**
         * Calculate the optimum height of the chooser dialogue
         *
         * This tries to set a sensible maximum and minimum to ensure that some options are always shown, and preferably
         * all, whilst fitting the box within the current viewport
         *
         * @param dialogue Y.Node The dialogue
         * @return void
         */
        calculate_height : function(dialogue) {
            var winheight = this.overlay.get('boundingBox').get('winHeight');

            // Try and set a sensible max-height -- this must be done before setting the top
            // Set a default height of 640px
            var newheight = this.get('maxheight');
            if (winheight <= newheight) {
                // Deal with smaller window sizes
                if (winheight <= this.get('minheight')) {
                    newheight = this.get('minheight');
                } else {
                    newheight = winheight;
                }
            }

            // Set a fixed position if the window is large enough
            if (newheight > this.get('minheight')) {
                this.overlay.get('boundingBox').setStyle('position', 'fixed');
            } else {
                this.overlay.get('boundingBox').setStyle('position', 'absolute');
            }

            // Take off 15px top and bottom for borders, plus 40px each for the title and button area before setting the
            // new max-height
            newheight = newheight - (15 + 15 + 40 + 40);
            dialogue.setStyle('max-height', newheight + 'px');

            // Re-calculate the location now that we've changed the size
            this.overlay.centerDialogue();
        },
        handle_key_press : function(e) {
            if (e.keyCode == 27) {
                this.cancel_popup(e);
            }
        },
        cancel_popup : function (e) {
            // Prevent normal form submission before hiding
            e.preventDefault();
            this.hide();
        },
        hide : function() {
            // Detach the global keypress handler before hiding
            Y.one('document').detach('keyup', this.handle_key_press, this);
            this.container.detachAll();
            this.overlay.hide();
        },
        check_options : function(e) {
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
        option_selected : function(e) {
        }
    },
    {
        NAME : 'moodle-core-chooserdialogue',
        ATTRS : {
            minheight : {
                value : 300
            },
            maxheight : {
                value : 660
            }
        }
    });
    M.core = M.core || {};
    M.core.chooserdialogue = CHOOSERDIALOGUE;
},
'@VERSION@', {
    requires:['base', 'overlay', 'moodle-enrol-notification']
}
);
