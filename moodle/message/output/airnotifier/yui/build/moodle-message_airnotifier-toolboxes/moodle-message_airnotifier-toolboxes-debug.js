YUI.add('moodle-message_airnotifier-toolboxes', function (Y, NAME) {

/**
 * Provides a tool for enabling/disabling elements using AJAX/REST.
 *
 * @module moodle-message_airnotifier-toolboxes
 */

// The CSS selectors we use.
var CSS = {
    AIRNOTIFIERCONTENT: 'div[data-processor-name="airnotifier"]',
    HIDEDEVICE: 'a.hidedevice',
    DEVICELI: 'li.airnotifierdevice',
    DIMCLASS: 'dimmed',
    DIMMEDTEXT: 'dimmed_text',
    DEVICEIDPREFIX: 'deviceid-'
};

/**
 * The toolbox classes
 *
 * TOOLBOX is a generic class which should never be directly instantiated
 * DEVICETOOLBOX is a class extending TOOLBOX containing code specific to devices
 */
var TOOLBOX = function() {
    TOOLBOX.superclass.constructor.apply(this, arguments);
};

Y.extend(TOOLBOX, Y.Base, {
    /**
     * Replace the button click at the selector with the specified
     * callback
     *
     * @param toolboxtarget The selector of the working area
     * @param selector The 'button' to replace
     * @param callback The callback to apply
     * @param cursor An optional cursor style to apply
     */
    replace_button: function(toolboxtarget, selector, callback, cursor) {
        if (!cursor) {
            // Set the default cursor type to pointer to match the anchor.
            cursor = 'pointer';
        }
        var button = Y.one(toolboxtarget).all(selector)
        .setStyle('cursor', cursor);

        // On isn't chainable and will return an event.
        button.on('click', callback, this);

        return button;
    },
    /**
     * Toggle the visibility and availability for the specified
     * device show/hide button
     */
    toggle_hide_device_ui: function(button) {

        var element = button.ancestor(CSS.DEVICELI);
        var hideicon = button.one('img');

        var toggle_class = CSS.DIMMEDTEXT;

        var status = '';
        if (element.hasClass(toggle_class)) {
            status = 'hide';
        } else {
            status = 'show';
        }

        // Change the UI.
        element.toggleClass(toggle_class);
        // We need to toggle dimming on the description too element.all(CSS.CONTENTAFTERLINK).toggleClass(CSS.DIMMEDTEXT);.
        var newstring = M.util.get_string(status, 'moodle');
        hideicon.setAttrs({
            'alt': newstring,
            'title': newstring,
            'src': M.util.image_url('t/' + status)
        });
        button.set('title', newstring);
        button.set('className', 'editing_' + status);
    },
    /**
     * Send a request using the REST API
     *
     * @param data The data to submit
     * @param statusspinner (optional) A statusspinner which may contain a section loader
     * @param callbacksuccess Call back on success
     * @return response responseText field from responce
     */
    send_request: function(data, statusspinner, callbacksuccess) {
        // Default data structure
        if (!data) {
            data = {};
        }
        // Handle any variables which we must pass back through to.
        var pageparams = this.get('config').pageparams,
            varname;
        for (varname in pageparams) {
            data[varname] = pageparams[varname];
        }

        if (statusspinner) {
            statusspinner.show();
        }

        data.sesskey = M.cfg.sesskey;

        var uri = M.cfg.wwwroot + this.get('ajaxurl');

        // Define the configuration to send with the request.
        var responsetext = [];
        var config = {
            method: 'POST',
            data: data,
            on: {
                success: function(tid, response) {
                    try {
                        responsetext = Y.JSON.parse(response.responseText);
                        if (responsetext.error) {
                            Y.use('moodle-core-notification-ajaxexception', function() {
                                return new M.core.ajaxException(responsetext).show();
                            });
                        } else if (responsetext.success) {
                            callbacksuccess();
                        }
                    } catch (e) {
                        // Ignore.
                    }
                    if (statusspinner) {
                        statusspinner.hide();
                    }
                },
                failure: function(tid, response) {
                    if (statusspinner) {
                        statusspinner.hide();
                    }
                    Y.use('moodle-core-notification-ajaxexception', function() {
                        return new M.core.ajaxException(response).show();
                    });
                }
            },
            context: this,
            sync: false
        };

        // Send the request.
        Y.io(uri, config);
        return responsetext;
    },
    /**
     * Return the module ID for the specified element
     *
     * @param element The <li> element to determine a module-id number for
     * @return string The module ID
     */
    get_element_id: function(element) {
        return element.get('id').replace(CSS.DEVICEIDPREFIX, '');
    }
},
{
    NAME: 'device-toolbox',
    ATTRS: {
        ajaxurl: {
            'value': 0
        },
        config: {
            'value': 0
        }
    }
}
);

var DEVICETOOLBOX = function() {
    DEVICETOOLBOX.superclass.constructor.apply(this, arguments);
};

Y.extend(DEVICETOOLBOX, TOOLBOX, {

    /**
     * Initialize the device toolbox
     *
     * Updates all span.commands with relevant handlers and other required changes
     */
    initializer: function() {
        this.setup_for_device();
    },
    /**
     * Update any span.commands within the scope of the specified
     * selector with AJAX equivelants
     *
     * @param baseselector The selector to limit scope to
     * @return void
     */
    setup_for_device: function(baseselector) {
        if (!baseselector) {
            baseselector = CSS.AIRNOTIFIERCONTENT;
        }

        Y.all(baseselector).each(this._setup_for_device, this);
    },
    _setup_for_device: function(toolboxtarget) {

        // Show/Hide.
        this.replace_button(toolboxtarget, CSS.HIDEDEVICE, this.toggle_hide_device);
    },
    toggle_hide_device: function(e) {
        // Prevent the default button action.
        e.preventDefault();

        // Get the element we're working on.
        var element = e.target.ancestor(CSS.DEVICELI);

        var button = e.target.ancestor('a', true);

        var value;
        // Enable the device in case the CSS is dimmed.
        if (element.hasClass(CSS.DIMMEDTEXT)) {
            value = 1;
        } else {
            value = 0;
        }

        // Send the request.
        var data = {
            'field': 'enable',
            'enable': value,
            'id': this.get_element_id(element)
        };
        var spinner = M.util.add_spinner(Y, element);

        var context = this;
        var callback = function() {
            context.toggle_hide_device_ui(button);
        };
        this.send_request(data, spinner, callback);
    }
}, {
    NAME: 'message-device-toolbox',
    ATTRS: {
}
});

M.message = M.message || {};

M.message.init_device_toolbox = function(config) {
    return new DEVICETOOLBOX(config);
};



}, '@VERSION@', {"requires": ["base", "node", "io"]});
