H5PEditor.SemanticStructure = (function ($) {

  /**
   * The base of the semantic structure system.
   * All semantic structure class types will inherit this class.
   *
   * @class
   * @param {Object} field
   * @param {Object} defaultWidget
   */
  function SemanticStructure(field, defaultWidget) {
    var self = this;

    // Initialize event inheritance
    H5P.EventDispatcher.call(self);

    /**
     * Determine this fields label. Used in error messages.
     * @public
     */
    self.label = (field.label === undefined ? field.name : field.label);

    // Support old editor libraries
    self.field = {};

    const id = H5PEditor.getNextFieldId(field);
    const descriptionId = (field.description !== undefined ? H5PEditor.getDescriptionId(id) : undefined)

    /**
     * Global instance variables.
     * @private
     */
    var $widgetSelect, $wrapper, $inner, $errors, $helpText, widgets;

    /**
     * Initialize. Wrapped to avoid leaking variables
     * @private
     */
    var init = function () {
      // Create field wrapper
      $wrapper = $('<div/>', {
        'class': 'field ' + field.type + ' ' + H5PEditor.createImportance(field.importance)
      });

      /* We want to be in control of the label, description and errors
      containers to give the editor some structure. Also we do not provide
      direct access to the field object to avoid cluttering semantics.json with
      non-semantic properties and options. Getters and setters will be
      created for what is needed. */

      // Create field label
      if (field.label !== 0) {
        // Add label
        createLabel(self.label, field.optional, id).appendTo($wrapper);
      }

      // Create description
      var $description;
      if (field.description !== undefined) {
        $description = $('<div/>', {
          'id': descriptionId,
          'class': 'h5peditor-field-description',
          text: field.description,
          appendTo: $wrapper
        });
        $description.html($description.html().replace('\n', '<br/>'));
      }

      widgets = getValidWidgets();
      if (widgets.length > 1) {
        // Create widget select box
        $widgetSelect = $('<ul/>', {
          'class': 'h5peditor-widget-select',
          title: H5PEditor.t('core', 'editMode'),
          appendTo: $wrapper
        });
        for (var i = 0; i < widgets.length; i++) {
          addWidgetOption(widgets[i], i === 0);
        }

        // Allow custom styling when selector is present
        $wrapper.addClass('h5peditor-widgets');
      }

      // Create inner wrapper
      $inner = $('<div/>', {
        'class': 'h5peditor-widget-wrapper' + (widgets.length > 1 ? ' content' : ' '),
        appendTo: $wrapper
      });

      // Create errors container
      $errors = $('<div/>', {
        'class': 'h5p-errors'
      });

      // Create help text
      $helpText = $('<div/>', {
        'class': 'h5p-help-text'
      });
    };

    /**
     * Add widget select option.
     *
     * @private
     */
    var addWidgetOption = function (widget, active) {
      var $option = $('<li/>', {
        'class': 'h5peditor-widget-option' + (active ? ' ' + CLASS_WIDGET_ACTIVE : ''),
        text: widget.label,
        role: 'button',
        tabIndex: 1,
        on: {
          click: function () {
            // Update UI
            $widgetSelect.children('.' + CLASS_WIDGET_ACTIVE).removeClass(CLASS_WIDGET_ACTIVE);
            $option.addClass(CLASS_WIDGET_ACTIVE);

            // Change Widget
            changeWidget(widget.name);
          }
        }
      }).appendTo($widgetSelect);
    };

    /**
     * Get a list of widgets that are valid and loaded.
     *
     * @private
     * @throws {TypeError} widgets must be an array
     * @returns {Array} List of valid widgets
     */
    var getValidWidgets = function () {
      if (field.widgets === undefined) {
        // No widgets specified use default
        return [defaultWidget];
      }
      if (!(field.widgets instanceof Array)) {
        throw TypeError('widgets must be an array');
      }

      // Check if specified widgets are valid
      var validWidgets = [];
      for (var i = 0; i < field.widgets.length; i++) {
        var widget = field.widgets[i];
        if (getWidget(widget.name)) {
          validWidgets.push(widget);
        }
      }

      if (!validWidgets.length) {
        // There are no valid widgets, add default
        validWidgets.push(self.default);
      }

      return validWidgets;
    };

    /**
     * Finds the widget class with the given name.
     *
     * @private
     * @param {String} name
     * @returns {Class}
     */
    var getWidget = function (name) {
      return H5PEditor[name];
    };

    /**
     * Change the UI widget.
     *
     * @private
     * @param {String} name
     */
    var changeWidget = function (name) {
      if (self.widget !== undefined) {
        // Validate our fields first to makes sure all "stored" from their widgets
        self.validate();

        // Remove old widgets
        self.widget.remove();
      }

      // TODO: Improve error handling?
      var widget = getWidget(name);
      self.widget = new widget(self);
      self.trigger('changeWidget');
      self.widget.appendTo($inner);

      // Add errors container and description.
      $errors.appendTo($inner);

      if (self.widget.helpText !== undefined) {
        $helpText.html(self.widget.helpText).appendTo($inner);
      }
      else {
        $helpText.detach();
      }
    };

    /**
     * Appends the field widget to the given container.
     *
     * @public
     * @param {jQuery} $container
     */
    self.appendTo = function ($container) {
      // Use first widget by default
      changeWidget(widgets[0].name);

      $wrapper.appendTo($container);
    };

    /**
     * Remove this field and widget.
     *
     * @public
     */
    self.remove = function () {
      self.widget.remove();
      $wrapper.remove();
    };

    /**
     * Remove this field and widget.
     *
     * @public
     * @param {String} message
     */
    self.setError = function (message) {
      $errors.append(H5PEditor.createError(message));
    };

    /**
     * Clear error messages.
     *
     * @public
     */
    self.clearErrors = function () {
      $errors.html('');
    };

    /**
     * Get the name of this field.
     *
     * @public
     * @returns {String} Name of the current field
     */
    self.getName = function () {
      return field.name;
    };

    /**
     * Get the input id the label points to.
     *
     * @returns {String} Name of the current field
     */
    self.getId = function () {
      return id;
    };

    /**
     * Get the description id to point to.
     *
     * @returns {String} Name of the current field
     */
    self.getDescriptionId = function () {
      return descriptionId;
    };

    // Must be last
    init();
  }

  // Extends the event dispatcher
  SemanticStructure.prototype = Object.create(H5P.EventDispatcher.prototype);
  SemanticStructure.prototype.constructor = SemanticStructure;

  /**
   * Create generic editor label.
   *
   * @private
   * @param {String} text
   * @returns {jQuery}
   */
  var createLabel = function (text, optional, id) {
    return $('<label/>', {
      'for': id,
      'class': 'h5peditor-label' + (optional ? '' : ' h5peditor-required'),
      text: text
    });
  };



  /**
   * @constant
   */
  var CLASS_WIDGET_ACTIVE = 'h5peditor-widget-active';

  return SemanticStructure;
})(H5P.jQuery);
