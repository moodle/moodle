var CONTROLMENU_NAME = "Controlmenu",
    CONTROLMENU;

/**
 * CONTROLMENU
 * This is a drop down list of buttons triggered (and aligned to) a button.
 *
 * @namespace M.editor_atto.controlmenu
 * @class controlmenu
 * @constructor
 * @extends M.core.dialogue
 */
CONTROLMENU = function(config) {
    config.draggable = false;
    config.center = false;
    config.width = 'auto';
    config.lightbox = false;
    config.footerContent = '';
    config.hideOn = [ { eventName: 'clickoutside' } ];

    CONTROLMENU.superclass.constructor.apply(this, [config]);
};

Y.extend(CONTROLMENU, M.core.dialogue, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer : function(config) {
        var body, headertext, bb;
        CONTROLMENU.superclass.initializer.call(this, config);

        bb = this.get('boundingBox');
        bb.addClass('editor_atto_controlmenu');

        // Close the menu when clicked outside (excluding the button that opened the menu).
        body = this.bodyNode;

        headertext = Y.Node.create('<h3/>');
        headertext.addClass('accesshide');
        headertext.setHTML(this.get('headerText'));
        body.prepend(headertext);
    }

}, {
    NAME : CONTROLMENU_NAME,
    ATTRS : {
        /**
         * The header for the drop down (only accessible to screen readers).
         *
         * @attribute headerText
         * @type String
         * @default ''
         */
        headerText : {
            value : ''
        }

    }
});

M.editor_atto = M.editor_atto || {};
M.editor_atto.controlmenu = CONTROLMENU;
