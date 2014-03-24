YUI.add('moodle-message_airnotifier-toolboxes', function(Y) {
    WAITICON = {
        'pix':"i/loading_small",
        'component':'moodle'
    };
    // The CSS selectors we use
    var CSS = {
        AIRNOTIFIERCONTENT : 'fieldset#messageprocessor_airnotifier',
        HIDEDEVICE : 'a.hidedevice',
        DELETEDEVICE : 'a.deletedevice',
        DEVICELI : 'li.airnotifierdevice',
        DIMCLASS : 'dimmed',
        DIMMEDTEXT : 'dimmed_text',
        DEVICEIDPREFIX : 'deviceid-'
    };

    /**
     * The toolbox classes
     *
     * TOOLBOX is a generic class which should never be directly instantiated
     * DEVICETOOLBOX is a class extending TOOLBOX containing code specific to devices
     */
    var TOOLBOX = function() {
        TOOLBOX.superclass.constructor.apply(this, arguments);
    }

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
        replace_button : function(toolboxtarget, selector, callback, cursor) {
            if (!cursor) {
                // Set the default cursor type to pointer to match the
                // anchor
                cursor = 'pointer';
            }
            var button = Y.one(toolboxtarget).all(selector)
            .setStyle('cursor', cursor);

            // on isn't chainable and will return an event
            button.on('click', callback, this);

            return button;
        },
        /**
           * Toggle the visibility and availability for the specified
           * device show/hide button
           */
        toggle_hide_device_ui : function(button) {

            var element = button.ancestor(CSS.DEVICELI);
            var hideicon = button.one('img');

            var dimarea;
            var toggle_class = CSS.DIMMEDTEXT;

            var status = '';
            var value;
            if (element.hasClass(toggle_class)) {
                status = 'hide';
                value = 1;
            } else {
                status = 'show';
                value = 0;
            }

            // Change the UI
            element.toggleClass(toggle_class);
            // We need to toggle dimming on the description too
            //            element.all(CSS.CONTENTAFTERLINK).toggleClass(CSS.DIMMEDTEXT);
            var newstring = M.util.get_string(status, 'moodle');
            hideicon.setAttrs({
                'alt' : newstring,
                'title' : newstring,
                'src'   : M.util.image_url('t/' + status)
            });
            button.set('title', newstring);
            button.set('className', 'editing_'+status);

            return value;
        },
        /**
         * Send a request using the REST API
         *
         * @param data The data to submit
         * @param statusspinner (optional) A statusspinner which may contain a section loader
         * @param optionalconfig (optional) Any additional configuration to submit
         * @return response responseText field from responce
         */
        send_request : function(data, statusspinner, optionalconfig) {
            // Default data structure
            if (!data) {
                data = {};
            }
            // Handle any variables which we must pass back through to
            var pageparams = this.get('config').pageparams;
            for (varname in pageparams) {
                data[varname] = pageparams[varname];
            }

            data.sesskey = M.cfg.sesskey;

            var uri = M.cfg.wwwroot + this.get('ajaxurl');

            // Define the configuration to send with the request
            var responsetext = [];
            var config = {
                method: 'POST',
                data: data,
                on: {
                    success: function(tid, response) {
                        try {
                            responsetext = Y.JSON.parse(response.responseText);
                            if (responsetext.error) {
                                new M.core.ajaxException(responsetext);
                            }
                        } catch (e) {}
                        if (statusspinner) {
                            window.setTimeout(function(e) {
                                statusspinner.hide();
                            }, 400);
                        }
                    },
                    failure : function(tid, response) {
                        if (statusspinner) {
                            statusspinner.hide();
                        }
                        new M.core.ajaxException(response);
                    }
                },
                context: this,
                sync: true
            }

            // Apply optional config
            if (optionalconfig) {
                for (varname in optionalconfig) {
                    config[varname] = optionalconfig[varname];
                }
            }

            if (statusspinner) {
                statusspinner.show();
            }

            // Send the request
            Y.io(uri, config);
            return responsetext;
        },
        /**
         * Return the module ID for the specified element
         *
         * @param element The <li> element to determine a module-id number for
         * @return string The module ID
         */
        get_element_id : function(element) {
            return element.get('id').replace(CSS.DEVICEIDPREFIX, '');
        }
    },
    {
        NAME : 'device-toolbox',
        ATTRS : {
            ajaxurl : {
                'value' : 0
            },
            config : {
                'value' : 0
            }
        }
    }
    );

    var DEVICETOOLBOX = function() {
        DEVICETOOLBOX.superclass.constructor.apply(this, arguments);
    }

    Y.extend(DEVICETOOLBOX, TOOLBOX, {

        /**
         * Initialize the device toolbox
         *
         * Updates all span.commands with relevant handlers and other required changes
         */
        initializer : function(config) {
            this.setup_for_device();
        },
        /**
         * Update any span.commands within the scope of the specified
         * selector with AJAX equivelants
         *
         * @param baseselector The selector to limit scope to
         * @return void
         */
        setup_for_device : function(baseselector) {
            if (!baseselector) {
                var baseselector = CSS.AIRNOTIFIERCONTENT;
            }

            Y.all(baseselector).each(this._setup_for_device, this);
        },
        _setup_for_device : function(toolboxtarget) {
            // Delete
            this.replace_button(toolboxtarget, CSS.DELETEDEVICE, this.delete_device);

            // Show/Hide
            var showhide = this.replace_button(toolboxtarget, CSS.HIDEDEVICE, this.toggle_hide_device);
        },
        delete_device : function(e) {
            // Prevent the default button action
            e.preventDefault();

            // Get the element we're working on
            var element   = e.target.ancestor(CSS.DEVICELI);

            var confirmstring = '';
            var plugindata = {
                name : element.one('*').getHTML() //get the label
            }
            confirmstring = M.util.get_string('deletecheckdevicename', 'message_airnotifier', plugindata);

            // Confirm element removal
            if (!confirm(confirmstring)) {
                return false;
            }

            // Actually remove the element
            element.remove();
            var data = {
                'class' : 'device',
                'action' : 'DELETE',
                'id'    : this.get_element_id(element)
            };
            this.send_request(data);
        },
        toggle_hide_device : function(e) {
            // Prevent the default button action
            e.preventDefault();

            // Get the element we're working on
            var element = e.target.ancestor(CSS.DEVICELI);

            var button = e.target.ancestor('a', true);

            var value = this.toggle_hide_device_ui(button);

            // Send the request
            var data = {
                'field' : 'enable',
                'enable' : value,
                'id'    : this.get_element_id(element)
            };
            var spinner = M.util.add_spinner(Y, element);
            this.send_request(data, spinner);
        }
    }, {
        NAME : 'message-device-toolbox',
        ATTRS : {
    }
    });

    M.message = M.message || {};

    M.message.init_device_toolbox = function(config) {
        return new DEVICETOOLBOX(config);
    };

},
'@VERSION@', {
    requires : ['base', 'node', 'io']
}
);
