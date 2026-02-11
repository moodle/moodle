H5PEditor.List = (function ($) {
  /**
   * List structure.
   *
   * @class
   * @param {*} parent structure
   * @param {Object} field Semantic description of field
   * @param {Array} [parameters] Default parameters for this field
   * @param {Function} setValue Call to set our parameters
   */
  function List(parent, field, parameters, setValue) {
    var self = this;

    // Initialize semantics structure inheritance
    H5PEditor.SemanticStructure.call(self, field, {
      name: 'ListEditor',
      label: H5PEditor.t('core', 'listLabel')
    });

    // Make it possible to travel up tree.
    self.parent = parent; // (Could this be done a better way in the future?)

    /**
     * Keep track of child fields. Should not be exposed directly,
     * create functions for using or finding the children.
     *
     * @private
     * @type {Array}
     */
    var children = [];

    // Prepare the old ready callback system
    var readyCallbacks = [];
    var passReadyCallbacks = true;
    parent.ready(function () {
      passReadyCallbacks = false;
    }); // (In the future we should use the event system for this, i.e. self.once('ready'))

    // Listen for widget changes
    self.on('changeWidget', function () {
      // Append all items to new widget
      for (var i = 0; i < children.length; i++) {
        self.widget.addItem(children[i], i);
      }
    });

    /**
     * Add all items to list without appending to DOM.
     *
     * @public
     */
    var init = function () {
      var i;
      if (parameters !== undefined && parameters.length) {
        for (i = 0; i < parameters.length; i++) {
          if (parameters[i] === null) {
            parameters[i] = undefined;
          }
          addItem(i);
        }
      }
      else {
        if (field.defaultNum === undefined) {
          // Use min or 1 if no default item number is set.
          field.defaultNum = (field.min !== undefined ? field.min : 1);
        }
        // Add default number of fields.
        for (i = 0; i < field.defaultNum; i++) {
          addItem(i);
        }
      }
    };

    /**
     * Make sure list is created when setting a parameter.
     *
     * @private
     * @param {number} index
     * @param {*} value
     */
    var setParameters = function (index, value) {
      if (parameters === undefined) {
        // Create new parameters for list
        parameters = [];
        setValue(field, parameters);
      }
      parameters[index] = value;
    };

    /**
     * Add item to list.
     *
     * @private
     * @param {Number} index
     * @param {*} [paramsOverride] Override params using this value.
     */
    var addItem = function (index, paramsOverride) {
      var childField = field.field;
      var widget = H5PEditor.getWidgetName(childField);

      if ((parameters === undefined || parameters[index] === undefined) && childField['default'] !== undefined) {
        // Use default value
        setParameters(index, childField['default']);
      }
      if (paramsOverride !== undefined) {
        // Use override params
        setParameters(index, paramsOverride);
      }

      var child = children[index] = new H5PEditor.widgets[widget](self, childField, parameters === undefined ? undefined : parameters[index], function (myChildField, value) {
        var i = findIndex(child);
        setParameters(i === undefined ? index : i, value);
      });

      return child;
    };

    /**
     * Finds the index for the given child.
     *
     * @private
     * @param {Object} child field instance
     * @returns {Number} index
     */
    var findIndex = function (child) {
      for (var i = 0; i < children.length; i++) {
        if (children[i] === child) {
          return i;
        }
      }
    };

    /**
     * Get the singular form of the items added in the list.
     *
     * @public
     * @returns {String} The entity type
     */
    self.getEntity = function () {
      return (field.entity === undefined ? 'item' : field.entity);
    };

    /**
     * Adds a new list item and child field at the end of the list
     *
     * @public
     * @param {*} [paramsOverride] Override params using this value.
     * @returns {Boolean}
     */
    self.addItem = function (paramsOverride) {
      var id = children.length;
      if (field.max === id) {
        return false;
      }

      var child = addItem(id, paramsOverride);
      self.widget.addItem(child, id);

      if (!passReadyCallbacks) {
        // Run collected ready callbacks
        for (var i = 0; i < readyCallbacks.length; i++) {
          readyCallbacks[i]();
        }
        readyCallbacks = []; // Reset
      }
      self.trigger('addedItem', child);

      return true;
    };

    /**
     * Removes the list item at the given index.
     *
     * @public
     * @param {Number} index
     */
    self.removeItem = function (index) {
      // Remove child field
      children[index].remove();
      children.splice(index, 1);

      if (parameters !== undefined) {
        // Clean up parameters
        parameters.splice(index, 1);
        if (!parameters.length) {
          // Create new parameters for list
          parameters = undefined;
          setValue(field);
        }
      }
      self.trigger('removedItem', index);
    };

    /**
     * Removes all items.
     * This is useful if a widget wants to reset the list.
     *
     * @public
     */
    self.removeAllItems = function () {
      if (parameters === undefined) {
        return;
      }

      // Remove child fields
      for (var i = 0; i < children.length; i++) {
        children[i].remove();
      }
      children = [];

      // Clean up parameters
      parameters = undefined;
      setValue(field);
    };

    /**
     * Change the order of the items in the list.
     * Be aware that this may change the index of other existing items.
     *
     * @public
     * @param {Number} currentIndex
     * @param {Number} newIndex
     */
    self.moveItem = function (currentIndex, newIndex) {
      // Update child fields
      var child = children.splice(currentIndex, 1);
      children.splice(newIndex, 0, child[0]);

      // Update parameters
      if (parameters) {
        var params = parameters.splice(currentIndex, 1);
        parameters.splice(newIndex, 0, params[0]);
      }
    };

    /**
     * Allows ancestors and widgets to do stuff with our children.
     *
     * @public
     * @param {Function} task
     */
    self.forEachChild = function (task) {
      for (var i = 0; i < children.length; i++) {
        task(children[i], i);
      }
    };

    /**
     * Collect callback to run when the editor is ready. If this item isn't
     * ready yet, jusy pass them on to the parent item.
     *
     * @public
     * @param {Function} ready
     */
    self.ready = function (ready) {
      if (passReadyCallbacks) {
        parent.ready(ready);
      }
      else {
        readyCallbacks.push(ready);
      }
    };

    /**
     * Make sure that this field and all child fields are valid.
     *
     * @public
     * @returns {Boolean}
     */
    self.validate = function () {
      var self = this;
      var valid = true;

      // Remove old error messages
      self.clearErrors();

      // Make sure child fields are valid
      for (var i = 0; i < children.length; i++) {
        if (children[i].validate() === false) {
          valid = false;
        }
      }

      // Validate our self
      if (field.max !== undefined && field.max > 0 &&
          children !== undefined && children.length > field.max) {
        // Invalid, more parameters than max allowed.
        valid = false;
        self.setError(H5PEditor.t('core', 'listExceedsMax', {':max': field.max}));
      }
      if (field.min !== undefined && field.min > 0 &&
          (children === undefined || children.length < field.min)) {
        // Invalid, less parameters than min allowed.
        valid = false;
        self.setError(H5PEditor.t('core', 'listBelowMin', {':min': field.min}));
      }

      return valid;
    };

    self.getImportance = function () {
      if (field.importance !== undefined) {
        return H5PEditor.createImportance(field.importance);
      }
      else if (field.field.importance !== undefined) {
        return H5PEditor.createImportance(field.field.importance);
      }
      else {
        return '';
      }
    };

    /**
     * Creates a copy of the current valid value. A copy is created to avoid
     * mistakes like directly editing the parameter values, which will cause
     * inconsistencies between the parameters and the editor widgets.
     *
     * @public
     * @returns {Array}
     */
    self.getValue = function () {
      return (parameters === undefined ? parameters : $.extend(true, [], parameters));
    };

    /**
     * Get a copy of the field semantics used by this list to create rows.
     * @return {Object}
     */
    self.getField = function () {
      return $.extend(true, {}, field.field);
    };

    // Start the party!
    init();
  }

  // Extends the semantics structure
  List.prototype = Object.create(H5PEditor.SemanticStructure.prototype);
  List.prototype.constructor = List;

  return List;
})(H5P.jQuery);

// Register widget
H5PEditor.widgets.list = H5PEditor.List;
