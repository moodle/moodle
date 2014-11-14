/**
The Moodle Bootstrap theme's bootstrap JavaScript

@namespace Moodle
@module theme_bootstrapbase-bootstrap
**/

/**
The Moodle Bootstrap theme's bootstrap JavaScript

@class Moodle.theme_bootstrapbase.bootstrap
@uses node
@uses selector-css3
@constructor
**/
var CSS = {
        ACTIVE: 'active'
    },
    SELECTORS = {
        NAVBAR_BUTTON: '.btn-navbar',
        // FIXME This is deliberately wrong because of a breaking issue in the upstream library.
        TOGGLECOLLAPSE: '*[data-disabledtoggle="collapse"]'
    },
    NS = Y.namespace('Moodle.theme_bootstrapbase.bootstrap');

/**
 * Initialise the Moodle Bootstrap theme JavaScript
 *
 * @method init
 */
NS.init = function() {
    // We must use these here and *must not* add them to the list of dependencies until
    // Moodle fully supports the gallery.
    // When debugging is disabled and we seed the Loader with out configuration, if these
    // are in the requires array, then the Loader will try to load them from the CDN. It
    // does not know that we have added them to the module rollup.
    Y.use('gallery-bootstrap-dropdown',
            'gallery-bootstrap-collapse',
            'gallery-bootstrap-engine', function() {

        // Set up expandable and show.
        NS.setup_toggle_expandable();
        NS.setup_toggle_show();

        // Set up upstream dropdown delegation.
        Y.Bootstrap.dropdown_delegation();
    });
};

/**
 * Setup toggling of the Toggle Collapse
 *
 * @method setup_toggle_expandable
 * @private
 */
NS.setup_toggle_expandable = function() {
    Y.delegate('click', this.toggle_expandable, Y.config.doc, SELECTORS.TOGGLECOLLAPSE, this);
};

/**
 * Use the Y.Bootstrap.Collapse plugin to toggle collapse.
 *
 * @method toggle_expandable
 * @private
 * @param {EventFacade} e
 */
NS.toggle_expandable = function(e) {
    if (typeof e.currentTarget.collapse === 'undefined') {
        // Only plug if we haven't already.
        e.currentTarget.plug(Y.Bootstrap.Collapse);

        // The plugin will now catch the click and handle the toggle.
        // We only need to do this when we plug the node for the first
        // time.
        e.currentTarget.collapse.toggle();
        e.preventDefault();
    }
};

/**
 * Set up the show toggler for activating the navigation bar
 *
 * @method setup_toggle_show
 * @private
 */
NS.setup_toggle_show = function() {
    Y.delegate('click', this.toggle_show, Y.config.doc, SELECTORS.NAVBAR_BUTTON);
};

/**
 * Toggle hiding of the navigation bar
 *
 * @method toggle_show
 * @private
 * @param {EventFacade} e
 */
NS.toggle_show = function(e) {
    // Toggle the active class on both the clicked .btn-navbar and the
    // associated target, defined by a CSS selector string set as the
    // data-target attribute on the .btn-navbar element in question.
    //
    // This will allow for us to have multiple .btn-navbar elements
    // each with their own collapse/expand targets - these targets
    // should be of class .nav-collapse.
    var myTarget = this.get('parentNode').one(this.getAttribute('data-target'));
    if (myTarget) {
        this.siblings(".btn-navbar").removeClass(CSS.ACTIVE);
        myTarget.siblings(".nav-collapse").removeClass(CSS.ACTIVE);
        myTarget.toggleClass(CSS.ACTIVE);
    }
    e.currentTarget.toggleClass(CSS.ACTIVE);
};
