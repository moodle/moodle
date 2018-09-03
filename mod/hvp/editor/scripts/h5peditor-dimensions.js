var H5PEditor = H5PEditor || {};
var ns = H5PEditor;

/**
 * Adds a dimensions field to the form.
 *
 * TODO: Make it possible to lock width/height ratio.
 *
 * @param {mixed} parent
 * @param {object} field
 * @param {mixed} params
 * @param {function} setValue
 * @returns {ns.Dimensions}
 */
ns.Dimensions = function (parent, field, params, setValue) {
  var that = this;

  this.parent = parent;
  this.field = field;
  this.changes = [];

  // Find image field to get max size from.
  H5PEditor.followField(parent, field.max, function (file) {
    that.setMax(file);
  });

  // Find image field to get default size from.
  H5PEditor.followField(parent, field['default'], function (file, index) {
    // Make sure we don't set size if we have one in the default params.
    if (params.width === undefined) {
      that.setSize(file);
    }
  });

  this.params = params;
  this.setValue = setValue;

  // Remove default field from params to avoid saving it.
  if (this.params.field) {
    this.params.field = undefined;
  }
};

/**
 * Set max dimensions.
 *
 * @param {Object} file
 * @returns {unresolved}
 */
ns.Dimensions.prototype.setMax = function (file) {
  if (file === undefined) {
    return;
  }

  this.max = {
    width: parseInt(file.width),
    height: parseInt(file.height)
  };
};

/**
 * Set current dimensions.
 *
 * @param {string} width
 * @param {string} height
 * @returns {undefined}
 */
ns.Dimensions.prototype.setSize = function (file) {
  if (file === undefined) {
    return;
  }

  this.params = {
    width: parseInt(file.width),
    height: parseInt(file.height)
  };
  this.setValue(this.field, this.params);

  this.$inputs.filter(':eq(0)').val(file.width).next().val(file.height);

  for (var i = 0; i < this.changes.length; i++) {
    this.changes[i](file.width, file.height);
  }
};

/**
 * Append the field to the given wrapper.
 *
 * @param {jQuery} $wrapper
 * @returns {undefined}
 */
ns.Dimensions.prototype.appendTo = function ($wrapper) {
  var that = this;

  this.$item = ns.$(this.createHtml()).appendTo($wrapper);
  this.$inputs = this.$item.find('input');
  this.$errors = this.$item.children('.h5p-errors');

  this.$inputs.change(function () {
    // Validate
    var value = that.validate();

    if (value) {
      // Set param
      that.params = value;
      that.setValue(that.field, value);

      for (var i = 0; i < that.changes.length; i++) {
        that.changes[i](value.width, value.height);
      }
    }
  }).click(function () {
    return false;
  });
};

/**
 * Create HTML for the field.
 */
ns.Dimensions.prototype.createHtml = function () {
  var input = ns.createText(this.params !== undefined ? this.params.width : undefined, 15, ns.t('core', 'width')) + ' x ' + ns.createText(this.params !== undefined ? this.params.height : undefined, 15, ns.t('core', 'height'));
  return ns.createFieldMarkup(this.field, input);
};

/**
 * Validate the current text field.
 */
ns.Dimensions.prototype.validate = function () {
  var that = this;
  var size = {};

  this.$errors.html('');

  this.$inputs.each(function (i) {
    var $input = ns.$(this);
    var value = H5P.trim($input.val());
    var property = i ? 'height' : 'width';
    var propertyTranslated = ns.t('core', property);

    if ((that.field.optional === undefined || !that.field.optional) && !value.length) {
      that.$errors.append(ns.createError(ns.t('core', 'requiredProperty', {':property': propertyTranslated})));
      return false;
    }
    else if (!value.match(new RegExp('^[0-9]+$'))) {
      that.$errors.append(ns.createError(ns.t('core', 'onlyNumbers', {':property': propertyTranslated})));
      return false;
    }

    value = parseInt(value);
    if (that.max !== undefined && value > that.max[property]) {
      that.$errors.append(ns.createError(ns.t('core', 'exceedsMax', {':property': propertyTranslated, ':max': that.max[property]})));
      return false;
    }

    size[property] = value;
  });

  return ns.checkErrors(this.$errors, this.$inputs, size);
};

/**
 * Remove this item.
 */
ns.Dimensions.prototype.remove = function () {
  this.$item.remove();
};

// Tell the editor what widget we are.
ns.widgets.dimensions = ns.Dimensions;
