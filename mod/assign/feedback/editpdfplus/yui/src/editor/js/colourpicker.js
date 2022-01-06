/* global Y, M */

var COLOURPICKER_NAME = "Colourpicker",
        COLOURPICKER;

/**
 * Provides an in browser PDF editor.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */

/**
 * COLOURPICKER
 * This is a drop down list of colours.
 *
 * @namespace M.assignfeedback_editpdfplus
 * @class colourpicker
 * @constructor
 * @extends M.assignfeedback_editpdfplus.dropdown
 */
COLOURPICKER = function (config) {
    COLOURPICKER.superclass.constructor.apply(this, [config]);
};

Y.extend(COLOURPICKER, M.assignfeedback_editpdfplus.dropdown, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer: function (config) {
        var colourlist = Y.Node.create('<ul role="menu" class="assignfeedback_editpdfplus_menu"/>'),
                body;
        var iconGoutte;

        // Build a list of coloured buttons.
        Y.each(this.get('colours'), function (rgb, colour) {
            var button, listitem, title;

            title = M.util.get_string(colour, 'assignfeedback_editpdfplus');
            if (colour === "white" || colour === "yellowlemon") {
                iconGoutte = Y.Node.create('<span class="fa-stack fa-lg">'
                        + '<i class="fa fa-square fa-stack-2x" style="color:#E3E3E3;"></i>'
                        + '<i class="fa fa-tint fa-stack-1x fa-inverse" aria-hidden="true" '
                        + 'style="color:' + rgb + ';">'
                        + '</i>'
                        + '</span>');
            } else {
                iconGoutte = Y.Node.create('<span class="fa-stack fa-lg">'
                        + '<i class="fa fa-square-o fa-stack-2x" style="color:#E3E3E3;"></i>'
                        + '<i class="fa fa-tint fa-stack-1x" aria-hidden="true" '
                        + 'style="color:' + rgb + ';">'
                        + '</i>'
                        + '</span>');
            }
            iconGoutte.setAttribute('data-colour', colour);
            button = Y.Node.create('<button class="btn btn-sm" type="button"></button>');
            button.append(iconGoutte);
            button.setAttribute('data-colour', colour);
            button.setAttribute('data-rgb', rgb);
            button.setStyle('backgroundImage', 'none');
            listitem = Y.Node.create('<li/>');
            listitem.append(button);
            colourlist.append(listitem);
        }, this);

        body = Y.Node.create('<div style="max-width:50px;"></div>');

        // Set the call back.
        colourlist.delegate('click', this.callback_handler, 'button', this);
        colourlist.delegate('key', this.callback_handler, 'down:13', 'button', this);

        // Set the accessible header text.
        this.set('headerText', M.util.get_string('colourpicker', 'assignfeedback_editpdfplus'));

        // Set the body content.
        body.append(colourlist);
        this.set('bodyContent', body);

        COLOURPICKER.superclass.initializer.call(this, config);
    },
    callback_handler: function (e) {
        e.preventDefault();

        var callback = this.get('callback'),
                callbackcontext = this.get('context'),
                bind;

        this.hide();

        // Call the callback with the specified context.
        bind = Y.bind(callback, callbackcontext, e);

        bind();
    }
}, {
    NAME: COLOURPICKER_NAME,
    ATTRS: {
        /**
         * The list of colours this colour picker supports.
         *
         * @attribute colours
         * @type {String: String} (The keys of the array are the colour names and the values are localized strings)
         * @default {}
         */
        colours: {
            value: {}
        },

        /**
         * The function called when a new colour is chosen.
         *
         * @attribute callback
         * @type function
         * @default null
         */
        callback: {
            value: null
        },

        /**
         * The context passed to the callback when a colour is chosen.
         *
         * @attribute context
         * @type Y.Node
         * @default null
         */
        context: {
            value: null
        },

        /**
         * The prefix for the icon image names.
         *
         * @attribute iconprefix
         * @type String
         * @default 'colour_'
         */
        iconprefix: {
            value: 'colour_'
        }
    }
});

M.assignfeedback_editpdfplus = M.assignfeedback_editpdfplus || {};
M.assignfeedback_editpdfplus.colourpicker = COLOURPICKER;
