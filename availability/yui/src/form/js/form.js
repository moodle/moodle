/**
 * Provides interface for users to edit availability settings on the
 * module/section editing form.
 *
 * The system works using this JavaScript plus form.js files inside each
 * condition plugin.
 *
 * The overall concept is that data is held in a textarea in the form in JSON
 * format. This JavaScript converts the textarea into a set of controls
 * generated here and by the relevant plugins.
 *
 * (Almost) all data is held directly by the state of the HTML controls, and
 * can be updated to the form field by calling the 'update' method, which
 * this code and the plugins call if any HTML control changes.
 *
 * @module moodle-core_availability-form
 */
M.core_availability = M.core_availability || {};

/**
 * Core static functions for availability settings in editing form.
 *
 * @class M.core_availability.form
 * @static
 */
M.core_availability.form = {
    /**
     * Object containing installed plugins. They are indexed by plugin name.
     *
     * @property plugins
     * @type Object
     */
    plugins : {},

    /**
     * Availability field (textarea).
     *
     * @property field
     * @type Y.Node
     */
    field : null,

    /**
     * Main div that replaces the availability field.
     *
     * @property mainDiv
     * @type Y.Node
     */
    mainDiv : null,

    /**
     * Object that represents the root of the tree.
     *
     * @property rootList
     * @type M.core_availability.List
     */
    rootList : null,

    /**
     * Counter used when creating anything that needs an id.
     *
     * @property idCounter
     * @type Number
     */
    idCounter : 0,

    /**
     * Called to initialise the system when the page loads. This method will
     * also call the init method for each plugin.
     *
     * @method init
     */
    init : function(pluginParams) {
        // Init all plugins.
        for(var plugin in pluginParams) {
            var params = pluginParams[plugin];
            var pluginClass = M[params[0]].form;
            pluginClass.init.apply(pluginClass, params);
        }

        // Get the availability field, hide it, and replace with the main div.
        this.field = Y.one('#id_availabilityconditionsjson');
        this.field.setAttribute('aria-hidden', 'true');
        // The fcontainer class here is inappropriate, but is necessary
        // because otherwise it is impossible to make Behat work correctly on
        // these controls as Behat incorrectly decides they're a moodleform
        // textarea. IMO Behat should not know about moodleforms at all and
        // should look purely at HTML elements on the page, but until it is
        // fixed to do this or fixed in some other way to only detect moodleform
        // elements that specifically match what those elements should look like,
        // then there is no good solution.
        this.mainDiv = Y.Node.create('<div class="availability-field fcontainer"></div>');
        this.field.insert(this.mainDiv, 'after');

        // Get top-level tree as JSON.
        var value = this.field.get('value');
        var data = null;
        if (value !== '') {
            try {
                data = Y.JSON.parse(value);
            } catch(x) {
                // If the JSON data is not valid, treat it as empty.
                this.field.set('value', '');
            }
        }
        this.rootList = new M.core_availability.List(data, true);
        this.mainDiv.appendChild(this.rootList.node);

        // Update JSON value after loading (to reflect any changes that need
        // to be made to make it valid).
        this.update();
        this.rootList.renumber();

        // Mark main area as dynamically updated.
        this.mainDiv.setAttribute('aria-live', 'polite');

        // Listen for form submission - to avoid having our made-up fields
        // submitted, we need to disable them all before submit.
        this.field.ancestor('form').on('submit', function() {
            this.mainDiv.all('input,textarea,select').set('disabled', true);
        }, this);
    },

    /**
     * Called at any time to update the hidden field value.
     *
     * This should be called whenever any value changes in the form settings.
     *
     * @method update
     */
    update : function() {
        // Convert tree to value.
        var jsValue = this.rootList.getValue();

        // Store any errors (for form reporting) in 'errors' value if present.
        var errors = [];
        this.rootList.fillErrors(errors);
        if (errors.length !== 0) {
            jsValue.errors = errors;
        }

        // Set into hidden form field, JS-encoded.
        this.field.set('value', Y.JSON.stringify(jsValue));
    }
};


/**
 * Base object for plugins. Plugins should use Y.Object to extend this class.
 *
 * @class M.core_availability.plugin
 * @static
 */
M.core_availability.plugin = {
    /**
     * True if users are allowed to add items of this plugin at the moment.
     *
     * @property allowAdd
     * @type Boolean
     */
    allowAdd : false,

    /**
     * Called (from PHP) to initialise the plugin. Should usually not be
     * overridden by child plugin.
     *
     * @method init
     * @param {String} component Component name e.g. 'availability_date'
     */
    init : function(component, allowAdd, params) {
        var name = component.replace(/^availability_/, '');
        this.allowAdd = allowAdd;
        M.core_availability.form.plugins[name] = this;
        this.initInner.apply(this, params);
    },

    /**
     * Init method for plugin to override. (Default does nothing.)
     *
     * This method will receive any parameters defined in frontend.php
     * get_javascript_init_params.
     *
     * @method initInner
     * @protected
     */
    initInner : function() {
    },

    /**
     * Gets a YUI node representing the controls for this plugin on the form.
     *
     * Must be implemented by sub-object; default throws an exception.
     *
     * @method getNode
     * @return {Y.Node} YUI node
     */
    getNode : function() {
        throw 'getNode not implemented';
    },

    /**
     * Fills in the value from this plugin's controls into a value object,
     * which will later be converted to JSON and stored in the form field.
     *
     * Must be implemented by sub-object; default throws an exception.
     *
     * @method fillValue
     * @param {Object} value Value object (to be written to)
     * @param {Y.Node} node YUI node (same one returned from getNode)
     */
    fillValue : function() {
        throw 'fillValue not implemented';
    },

    /**
     * Fills in any errors from this plugin's controls. If there are any
     * errors, push them into the supplied array.
     *
     * Errors are Moodle language strings in format component:string, e.g.
     * 'availability_date:error_date_past_end_of_world'.
     *
     * The default implementation does nothing.
     *
     * @method fillErrors
     * @param {Array} errors Array of errors (push new errors here)
     * @param {Y.Node} node YUI node (same one returned from getNode)
     */
    fillErrors : function() {
    },

    /**
     * Focuses the first thing in the plugin after it has been added.
     *
     * The default implementation uses a simple algorithm to identify the
     * first focusable input/select and then focuses it.
     */
    focusAfterAdd : function(node) {
        var target = node.one('input:not([disabled]),select:not([disabled])');
        target.focus();
    }
};


/**
 * Maintains a list of children and settings for how they are combined.
 *
 * @class M.core_availability.List
 * @constructor
 * @param {Object} json Decoded JSON value
 * @param {Boolean} [false] root True if this is root level list
 * @param {Boolean} [false] root True if parent is root level list
 */
M.core_availability.List = function(json, root, parentRoot) {
    // Set default value for children. (You can't do this in the prototype
    // definition, or it ends up sharing the same array between all of them.)
    this.children = [];

    if (root !== undefined) {
        this.root = root;
    }
    var strings = M.str.availability;
    // Create DIV structure (without kids).
    this.node = Y.Node.create('<div class="availability-list"><h3 class="accesshide"></h3>' +
            '<div class="availability-inner">' +
            '<div class="availability-header">' + strings.listheader_sign_before +
            ' <label><span class="accesshide">' + strings.label_sign +
            ' </span><select class="availability-neg" title="' + strings.label_sign + '">' +
            '<option value="">' + strings.listheader_sign_pos + '</option>' +
            '<option value="!">' + strings.listheader_sign_neg + '</option></select></label> ' +
            '<span class="availability-single">' + strings.listheader_single + '</span>' +
            '<span class="availability-multi">' + strings.listheader_multi_before +
            ' <label><span class="accesshide">' + strings.label_multi + ' </span>' +
            '<select class="availability-op" title="' + strings.label_multi + '"><option value="&">' +
            strings.listheader_multi_and + '</option>' +
            '<option value="|">' + strings.listheader_multi_or + '</option></select></label> ' +
            strings.listheader_multi_after + '</span></div>' +
            '<div class="availability-children"></div>' +
            '<div class="availability-none">' + M.str.moodle.none + '</div>' +
            '<div class="availability-button"></div></div></div>');
    if (!root) {
        this.node.addClass('availability-childlist');
    }
    this.inner = this.node.one('> .availability-inner');

    var shown = true;
    if (root) {
        // If it's the root, add an eye icon as first thing in header.
        if (json && json.show !== undefined) {
            shown = json.show;
        }
        this.eyeIcon = new M.core_availability.EyeIcon(false, shown);
        this.node.one('.availability-header').get('firstChild').insert(
                this.eyeIcon.span, 'before');
    } else if (parentRoot) {
        // When the parent is root, add an eye icon before the main list div.
        if (json && json.showc !== undefined) {
            shown = json.showc;
        }
        this.eyeIcon = new M.core_availability.EyeIcon(false, shown);
        this.inner.insert(this.eyeIcon.span, 'before');
    }

    if (!root) {
        // If it's not the root, add a delete button to the 'none' option.
        // You can only delete lists when they have no children so this will
        // automatically appear at the correct time.
        var deleteIcon = new M.core_availability.DeleteIcon(this);
        var noneNode = this.node.one('.availability-none');
        noneNode.appendChild(document.createTextNode(' '));
        noneNode.appendChild(deleteIcon.span);

        // Also if it's not the root, none is actually invalid, so add a label.
        noneNode.appendChild(Y.Node.create('<span class="label label-warning">' +
                M.str.availability.invalid + '</span>'));
    }

    // Create the button and add it.
    var button = Y.Node.create('<button type="button" class="btn btn-default">' +
            M.str.availability.addrestriction + '</button>');
    button.on("click", function() { this.clickAdd(); }, this);
    this.node.one('div.availability-button').appendChild(button);

    if (json) {
        // Set operator from JSON data.
        switch (json.op) {
            case '&' :
            case '|' :
                this.node.one('.availability-neg').set('value', '');
                break;
            case '!&' :
            case '!|' :
                this.node.one('.availability-neg').set('value', '!');
                break;
        }
        switch (json.op) {
            case '&' :
            case '!&' :
                this.node.one('.availability-op').set('value', '&');
                break;
            case '|' :
            case '!|' :
                this.node.one('.availability-op').set('value', '|');
                break;
        }

        // Construct children.
        for (var i = 0; i < json.c.length; i++) {
            var child = json.c[i];
            if (this.root && json && json.showc !== undefined) {
                child.showc = json.showc[i];
            }
            var newItem;
            if (child.type !== undefined) {
                // Plugin type.
                newItem = new M.core_availability.Item(child, this.root);
            } else {
                // List type.
                newItem = new M.core_availability.List(child, false, this.root);
            }
            this.addChild(newItem);
        }
    }

    // Add update listeners to the dropdowns.
    this.node.one('.availability-neg').on('change', function() {
        // Update hidden field and HTML.
        M.core_availability.form.update();
        this.updateHtml();
    }, this);
    this.node.one('.availability-op').on('change', function() {
        // Update hidden field.
        M.core_availability.form.update();
        this.updateHtml();
    }, this);

    // Update HTML to hide unnecessary parts.
    this.updateHtml();
};

/**
 * Adds a child to the end of the list (in HTML and stored data).
 *
 * @method addChild
 * @private
 * @param {M.core_availability.Item|M.core_availability.List} newItem Child to add
 */
M.core_availability.List.prototype.addChild = function(newItem) {
    if (this.children.length > 0) {
        // Create connecting label (text will be filled in later by updateHtml).
        this.inner.one('.availability-children').appendChild(Y.Node.create(
                '<div class="availability-connector">' +
                '<span class="label"></span>' +
                '</div>'));
    }
    // Add item to array and to HTML.
    this.children.push(newItem);
    this.inner.one('.availability-children').appendChild(newItem.node);
};

/**
 * Focuses something after a new list is added.
 *
 * @method focusAfterAdd
 */
M.core_availability.List.prototype.focusAfterAdd = function() {
    this.inner.one('button').focus();
};

/**
 * Checks whether this list uses the individual show icons or the single one.
 *
 * (Basically, AND and the equivalent NOT OR list can have individual show icons
 * so that you hide the activity entirely if a user fails one condition, but
 * may display it with information about the condition if they fail a different
 * one. That isn't possible with OR and NOT AND because for those types, there
 * is not really a concept of which single condition caused the user to fail
 * it.)
 *
 * Method can only be called on the root list.
 *
 * @method isIndividualShowIcons
 * @return {Boolean} True if using the individual icons
 */
M.core_availability.List.prototype.isIndividualShowIcons = function() {
    if (!this.root) {
        throw 'Can only call this on root list';
    }
    var neg = this.node.one('.availability-neg').get('value') === '!';
    var isor = this.node.one('.availability-op').get('value') === '|';
    return (!neg && !isor) || (neg && isor);
};

/**
 * Renumbers the list and all children.
 *
 * @method renumber
 * @param {String} parentNumber Number to use in heading for this list
 */
M.core_availability.List.prototype.renumber = function(parentNumber) {
    // Update heading for list.
    var headingParams = { count: this.children.length };
    var prefix;
    if (parentNumber === undefined) {
        headingParams.number = '';
        prefix = '';
    } else {
        headingParams.number = parentNumber + ':';
        prefix = parentNumber + '.';
    }
    var heading = M.util.get_string('setheading', 'availability', headingParams);
    this.node.one('> h3').set('innerHTML', heading);

    // Do children.
    for (var i = 0; i < this.children.length; i++) {
        var child = this.children[i];
        child.renumber(prefix + (i + 1));
    }
};

/**
 * Updates HTML for the list based on the current values, for example showing
 * the 'None' text if there are no children.
 *
 * @method updateHtml
 */
M.core_availability.List.prototype.updateHtml = function() {
    // Control children appearing or not appearing.
    if (this.children.length > 0) {
        this.inner.one('> .availability-children').removeAttribute('aria-hidden');
        this.inner.one('> .availability-none').setAttribute('aria-hidden', 'true');
        this.inner.one('> .availability-header').removeAttribute('aria-hidden');
        if (this.children.length > 1) {
            this.inner.one('.availability-single').setAttribute('aria-hidden', 'true');
            this.inner.one('.availability-multi').removeAttribute('aria-hidden');
        } else {
            this.inner.one('.availability-single').removeAttribute('aria-hidden');
            this.inner.one('.availability-multi').setAttribute('aria-hidden', 'true');
        }
    } else {
        this.inner.one('> .availability-children').setAttribute('aria-hidden', 'true');
        this.inner.one('> .availability-none').removeAttribute('aria-hidden');
        this.inner.one('> .availability-header').setAttribute('aria-hidden', 'true');
    }

    // For root list, control eye icons.
    if (this.root) {
        var showEyes = this.isIndividualShowIcons();

        // Individual icons.
        for (var i = 0; i < this.children.length; i++) {
            var child = this.children[i];
            if (showEyes) {
                child.eyeIcon.span.removeAttribute('aria-hidden');
            } else {
                child.eyeIcon.span.setAttribute('aria-hidden', 'true');
            }
        }

        // Single icon is the inverse.
        if (showEyes) {
            this.eyeIcon.span.setAttribute('aria-hidden', 'true');
        } else {
            this.eyeIcon.span.removeAttribute('aria-hidden');
        }
    }

    // Update connector text.
    var connectorText;
    if (this.inner.one('.availability-op').get('value') === '&') {
        connectorText = M.str.availability.and;
    } else {
        connectorText = M.str.availability.or;
    }
    this.inner.all('> .availability-children > .availability-connector span.label').each(function(span) {
        span.set('innerHTML', connectorText);
    });
};

/**
 * Deletes a descendant item (Item or List). Called when the user clicks a
 * delete icon.
 *
 * This is a recursive function.
 *
 * @method deleteDescendant
 * @param {M.core_availability.Item|M.core_availability.List} descendant Item to delete
 * @return {Boolean} True if it was deleted
 */
M.core_availability.List.prototype.deleteDescendant = function(descendant) {
    // Loop through children.
    for (var i = 0; i < this.children.length; i++) {
        var child = this.children[i];
        if (child === descendant) {
            // Remove from internal array.
            this.children.splice(i, 1);
            var target = child.node;
            // Remove one of the connector nodes around target (if any left).
            if (this.children.length > 0) {
                if (target.previous('.availability-connector')) {
                    target.previous('.availability-connector').remove();
                } else {
                    target.next('.availability-connector').remove();
                }
            }
            // Remove target itself.
            this.inner.one('> .availability-children').removeChild(target);
            // Update the form and the list HTML.
            M.core_availability.form.update();
            this.updateHtml();
            // Focus add button for this list.
            this.inner.one('> .availability-button').one('button').focus();
            return true;
        } else if (child instanceof M.core_availability.List) {
            // Recursive call.
            var found = child.deleteDescendant(descendant);
            if (found) {
                return true;
            }
        }
    }

    return false;
};

/**
 * Shows the 'add restriction' dialogue box.
 *
 * @method clickAdd
 */
M.core_availability.List.prototype.clickAdd = function() {
    var content = Y.Node.create('<div>' +
            '<ul class="list-unstyled"></ul>' +
            '<div class="availability-buttons mdl-align">' +
            '<button type="button" class="btn btn-default">' + M.str.moodle.cancel +
            '</button></div></div>');
    var cancel = content.one('button');

    // Make a list of all the dialog options.
    var dialogRef = { dialog: null };
    var ul = content.one('ul');
    var li, id, button, label;
    for (var type in M.core_availability.form.plugins) {
        // Plugins might decide not to display their add button.
        if (!M.core_availability.form.plugins[type].allowAdd) {
            continue;
        }
        // Add entry for plugin.
        li = Y.Node.create('<li class="clearfix"></li>');
        id = 'availability_addrestriction_' + type;
        var pluginStrings = M.str['availability_' + type];
        button = Y.Node.create('<button type="button" class="btn btn-default"' +
                'id="' + id + '">' + pluginStrings.title + '</button>');
        button.on('click', this.getAddHandler(type, dialogRef), this);
        li.appendChild(button);
        label = Y.Node.create('<label for="' + id + '">' +
                pluginStrings.description + '</label>');
        li.appendChild(label);
        ul.appendChild(li);
    }
    // Extra entry for lists.
    li = Y.Node.create('<li class="clearfix"></li>');
    id = 'availability_addrestriction_list_';
    button = Y.Node.create('<button type="button" class="btn btn-default"' +
            'id="' + id + '">' + M.str.availability.condition_group + '</button>');
    button.on('click', this.getAddHandler(null, dialogRef), this);
    li.appendChild(button);
    label = Y.Node.create('<label for="' + id + '">' +
            M.str.availability.condition_group_info + '</label>');
    li.appendChild(label);
    ul.appendChild(li);

    var config = {
        headerContent : M.str.availability.addrestriction,
        bodyContent : content,
        additionalBaseClass : 'availability-dialogue',
        draggable : true,
        modal : true,
        closeButton : false,
        width : '450px'
    };
    dialogRef.dialog = new M.core.dialogue(config);
    dialogRef.dialog.show();
    cancel.on('click', function() {
        dialogRef.dialog.destroy();
        // Focus the button they clicked originally.
        this.inner.one('> .availability-button').one('button').focus();
    }, this);
};

/**
 * Gets an add handler function used by the dialogue to add a particular item.
 *
 * @method getAddHandler
 * @param {String|Null} type Type name of plugin or null to add lists
 * @param {Object} dialogRef Reference to object that contains dialog
 * @param {M.core.dialogue} dialogRef.dialog Dialog object
 * @return {Function} Add handler function to call when adding that thing
 */
M.core_availability.List.prototype.getAddHandler = function(type, dialogRef) {
    return function() {
        if (type) {
            // Create an Item object to represent the child.
            newItem = new M.core_availability.Item({ type: type, creating: true }, this.root);
        } else {
            // Create a new List object to represent the child.
            newItem = new M.core_availability.List({ c: [], showc: true }, false, this.root);
        }
        // Add to list.
        this.addChild(newItem);
        // Update the form and list HTML.
        M.core_availability.form.update();
        M.core_availability.form.rootList.renumber();
        this.updateHtml();
        // Hide dialog.
        dialogRef.dialog.destroy();
        newItem.focusAfterAdd();
    };
};

/**
 * Gets the value of the list ready to convert to JSON and fill form field.
 *
 * @method getValue
 * @return {Object} Value of list suitable for use in JSON
 */
M.core_availability.List.prototype.getValue = function() {
    // Work out operator from selects.
    var value = {};
    value.op = this.node.one('.availability-neg').get('value') +
            this.node.one('.availability-op').get('value');

    // Work out children from list.
    value.c = [];
    var i;
    for (i = 0; i < this.children.length; i++) {
        value.c.push(this.children[i].getValue());
    }

    // Work out show/showc for root level.
    if (this.root) {
        if (this.isIndividualShowIcons()) {
            value.showc = [];
            for (i = 0; i < this.children.length; i++) {
                value.showc.push(!this.children[i].eyeIcon.isHidden());
            }
        } else {
            value.show = !this.eyeIcon.isHidden();
        }
    }
    return value;
};

/**
 * Checks whether this list has any errors (incorrect user input). If so,
 * an error string identifier in the form langfile:langstring should be pushed
 * into the errors array.
 *
 * @method fillErrors
 * @param {Array} errors Array of errors so far
 */
M.core_availability.List.prototype.fillErrors = function(errors) {
    // List with no items is an error (except root).
    if (this.children.length === 0 && !this.root) {
        errors.push('availability:error_list_nochildren');
    }
    // Pass to children.
    for (var i = 0; i < this.children.length; i++) {
        this.children[i].fillErrors(errors);
    }
};

/**
 * Eye icon for this list (null if none).
 *
 * @property eyeIcon
 * @type M.core_availability.EyeIcon
 */
M.core_availability.List.prototype.eyeIcon = null;

/**
 * True if list is special root level list.
 *
 * @property root
 * @type Boolean
 */
M.core_availability.List.prototype.root = false;

/**
 * Array containing children (Lists or Items).
 *
 * @property children
 * @type M.core_availability.List[]|M.core_availability.Item[]
 */
M.core_availability.List.prototype.children = null;

/**
 * HTML outer node for list.
 *
 * @property node
 * @type Y.Node
 */
M.core_availability.List.prototype.node = null;

/**
 * HTML node for inner div that actually is the displayed list.
 *
 * @property node
 * @type Y.Node
 */
M.core_availability.List.prototype.inner = null;


/**
 * Represents a single condition.
 *
 * @class M.core_availability.Item
 * @constructor
 * @param {Object} json Decoded JSON value
 * @param {Boolean} root True if this item is a child of the root list.
 */
M.core_availability.Item = function(json, root) {
    this.pluginType = json.type;
    if (M.core_availability.form.plugins[json.type] === undefined) {
        // Handle undefined plugins.
        this.plugin = null;
        this.pluginNode = Y.Node.create('<div class="availability-warning">' +
                M.str.availability.missingplugin + '</div>');
    } else {
        // Plugin is known.
        this.plugin = M.core_availability.form.plugins[json.type];
        this.pluginNode = this.plugin.getNode(json);

        // Add a class with the plugin Frankenstyle name to make CSS easier in plugin.
        this.pluginNode.addClass('availability_' + json.type);
    }

    this.node = Y.Node.create('<div class="availability-item"><h3 class="accesshide"></h3></div>');

    // Add eye icon if required. This icon is added for root items, but may be
    // hidden depending on the selected list operator.
    if (root) {
        var shown = true;
        if(json.showc !== undefined) {
            shown = json.showc;
        }
        this.eyeIcon = new M.core_availability.EyeIcon(true, shown);
        this.node.appendChild(this.eyeIcon.span);
    }

    // Add plugin controls.
    this.pluginNode.addClass('availability-plugincontrols');
    this.node.appendChild(this.pluginNode);

    // Add delete button for node.
    var deleteIcon = new M.core_availability.DeleteIcon(this);
    this.node.appendChild(deleteIcon.span);

    // Add the invalid marker (empty).
    this.node.appendChild(document.createTextNode(' '));
    this.node.appendChild(Y.Node.create('<span class="label label-warning"/>'));
};

/**
 * Obtains the value of this condition, which will be serialized into JSON
 * format and stored in the form.
 *
 * @method getValue
 * @return {Object} JavaScript object containing value of this item
 */
M.core_availability.Item.prototype.getValue = function() {
    value = { 'type' : this.pluginType };
    if (this.plugin) {
        this.plugin.fillValue(value, this.pluginNode);
    }
    return value;
};

/**
 * Checks whether this condition has any errors (incorrect user input). If so,
 * an error string identifier in the form langfile:langstring should be pushed
 * into the errors array.
 *
 * @method fillErrors
 * @param {Array} errors Array of errors so far
 */
M.core_availability.Item.prototype.fillErrors = function(errors) {
    var before = errors.length;
    if (this.plugin) {
        // Pass to plugin.
        this.plugin.fillErrors(errors, this.pluginNode);
    } else {
        // Unknown plugin is an error
        errors.push('core_availability:item_unknowntype');
    }
    // If any errors were added, add the marker to this item.
    var errorLabel = this.node.one('> .label-warning');
    if (errors.length !== before && !errorLabel.get('firstChild')) {
        errorLabel.appendChild(document.createTextNode(M.str.availability.invalid));
    } else if (errors.length === before && errorLabel.get('firstChild')) {
        errorLabel.get('firstChild').remove();
    }
};

/**
 * Renumbers the item.
 *
 * @method renumber
 * @param {String} number Number to use in heading for this item
 */
M.core_availability.Item.prototype.renumber = function(number) {
    // Update heading for item.
    var headingParams = { number: number };
    if (this.plugin) {
        headingParams.type = M.str['availability_' + this.pluginType].title;
    } else {
        headingParams.type = '[' + this.pluginType + ']';
    }
    headingParams.number = number + ':';
    var heading = M.util.get_string('itemheading', 'availability', headingParams);
    this.node.one('> h3').set('innerHTML', heading);
};

/**
 * Focuses something after a new item is added.
 *
 * @method focusAfterAdd
 */
M.core_availability.Item.prototype.focusAfterAdd = function() {
    this.plugin.focusAfterAdd(this.pluginNode);
};

/**
 * Name of plugin.
 *
 * @property pluginType
 * @type String
 */
M.core_availability.Item.prototype.pluginType = null;

/**
 * Object representing plugin form controls.
 *
 * @property plugin
 * @type Object
 */
M.core_availability.Item.prototype.plugin = null;

/**
 * Eye icon for item.
 *
 * @property eyeIcon
 * @type M.core_availability.EyeIcon
 */
M.core_availability.Item.prototype.eyeIcon = null;

/**
 * HTML node for item.
 *
 * @property node
 * @type Y.Node
 */
M.core_availability.Item.prototype.node = null;

/**
 * Inner part of node that is owned by plugin.
 *
 * @property pluginNode
 * @type Y.Node
 */
M.core_availability.Item.prototype.pluginNode = null;


/**
 * Eye icon (to control show/hide of the activity if the user fails a condition).
 *
 * There are individual eye icons (show/hide control for a single condition) and
 * 'all' eye icons (show/hide control that applies to the entire item, whatever
 * reason it fails for). This is necessary because the individual conditions
 * don't make sense for OR and AND NOT lists.
 *
 * @class M.core_availability.EyeIcon
 * @constructor
 * @param {Boolean} individual True if the icon is controlling a single condition
 * @param {Boolean} shown True if icon is initially in shown state
 */
M.core_availability.EyeIcon = function(individual, shown) {
    this.individual = individual;
    this.span = Y.Node.create('<a class="availability-eye" href="#" role="button">');
    var iconBase = M.cfg.wwwroot + '/theme/image.php/' + M.cfg.theme + '/core/' + M.cfg.themerev;
    var icon = Y.Node.create('<img />');
    this.span.appendChild(icon);

    // Set up button text and icon.
    var suffix = individual ? '_individual' : '_all';
    var setHidden = function() {
        icon.set('src', iconBase + '/i/show');
        icon.set('alt', M.str.availability['hidden' + suffix]);
        this.span.set('title', M.str.availability['hidden' + suffix] + ' \u2022 ' +
                M.str.availability.show_verb);
    };
    var setShown = function() {
        icon.set('src', iconBase + '/i/hide');
        icon.set('alt', M.str.availability['shown' + suffix]);
        this.span.set('title', M.str.availability['shown' + suffix] + ' \u2022 ' +
                M.str.availability.hide_verb);
    };
    if(shown) {
        setShown.call(this);
    } else {
        setHidden.call(this);
    }

    // Update when button is clicked.
    var click = function(e) {
        e.preventDefault();
        if (this.isHidden()) {
            setShown.call(this);
        } else {
            setHidden.call(this);
        }
        M.core_availability.form.update();
    };
    this.span.on('click', click, this);
    this.span.on('key', click, 'up:32', this);
    this.span.on('key', function(e) { e.preventDefault(); }, 'down:32', this);
};

/**
 * True if this eye icon is an individual one (see above).
 *
 * @property individual
 * @type Boolean
 */
M.core_availability.EyeIcon.prototype.individual = false;

/**
 * YUI node for the span that contains this icon.
 *
 * @property span
 * @type Y.Node
 */
M.core_availability.EyeIcon.prototype.span = null;

/**
 * Checks the current state of the icon.
 *
 * @method isHidden
 * @return {Boolean} True if this icon is set to 'hidden'
 */
M.core_availability.EyeIcon.prototype.isHidden = function() {
    var suffix = this.individual ? '_individual' : '_all';
    var compare = M.str.availability['hidden' + suffix];
    return this.span.one('img').get('alt') === compare;
};


/**
 * Delete icon (to delete an Item or List).
 *
 * @class M.core_availability.DeleteIcon
 * @constructor
 * @param {M.core_availability.Item|M.core_availability.List} toDelete Thing to delete
 */
M.core_availability.DeleteIcon = function(toDelete) {
    this.span = Y.Node.create('<a class="availability-delete" href="#" title="' +
            M.str.moodle['delete'] + '" role="button">');
    var img = Y.Node.create('<img src="' +
            M.cfg.wwwroot + '/theme/image.php/' + M.cfg.theme + '/core/' + M.cfg.themerev +
            '/t/delete" alt="' + M.str.moodle['delete'] + '" />');
    this.span.appendChild(img);
    var click = function(e) {
        e.preventDefault();
        M.core_availability.form.rootList.deleteDescendant(toDelete);
        M.core_availability.form.rootList.renumber();
    };
    this.span.on('click', click, this);
    this.span.on('key', click, 'up:32', this);
    this.span.on('key', function(e) { e.preventDefault(); }, 'down:32', this);
};

/**
 * YUI node for the span that contains this icon.
 *
 * @property span
 * @type Y.Node
 */
M.core_availability.DeleteIcon.prototype.span = null;
