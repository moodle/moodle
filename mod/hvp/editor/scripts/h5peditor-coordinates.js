/* global ns */
/**
 * Creates a coordinates picker for the form.
 *
 * @param {mixed} parent
 * @param {object} field
 * @param {mixed} params
 * @param {function} setValue
 * @returns {ns.Coordinates}
 */
ns.Coordinates = function (parent, field, params, setValue) {
  var that = this;

  this.parent = parent;
  this.field = H5P.cloneObject(field, true); // TODO: Cloning is a quick fix, make sure this field doesn't change semantics!

  // Find image field to get max size from.
  // TODO: Use followField?
  this.findImageField('max', function (field) {
    if (field instanceof ns.File) {
      if (field.params !== undefined) {
        that.setMax(field.params.width, field.params.height);
      }

      field.changes.push(function (file) {
        if (file === undefined) {
          return;
        }
        // TODO: This callback should be removed when this item is removed.
        that.setMax(file.params.width, file.params.height);
      });
    }
    else if (field instanceof ns.Dimensions) {
      if (field.params !== undefined) {
        that.setMax(field.params.width, field.params.height);
      }

      field.changes.push(function (width, height) {
        // TODO: This callback should be removed when this item is removed.
        that.setMax(width, height);
      });
    }
  });

  this.params = params;
  this.setValue = setValue;
};

/**
 * Set max coordinates.
 *
 * @param {string} x
 * @param {string} y
 * @returns {undefined}
 */
ns.Coordinates.prototype.setMax = function (x, y) {
  this.field.max = {
    x: parseInt(x),
    y: parseInt(y)
  };
  if (this.params !== undefined) {
    this.$errors.html('');
    this.validate();
  }
};

/**
 * Find the image field for the given property and then run the callback.
 *
 * @param {string} property
 * @param {function} callback
 * @returns {unresolved}
 */
ns.Coordinates.prototype.findImageField = function (property, callback) {
  var that = this;
  var str = 'string';

  if (typeof this.field[property] !== str) {
    return;
  }

  // Find field when tree is ready.
  this.parent.ready(function () {
    if (typeof that.field[property] !== str) {
      if (that.field[property] !== undefined) {
        callback(that.field[property]);
      }
      return; // We've already found this field before.
    }
    var path = that.field[property];

    that.field[property] = ns.findField(that.field[property], that.parent);
    if (!that.field[property]) {
      throw ns.t('core', 'unknownFieldPath', {':path': path});
    }
    if (that.field[property].field.type !== 'image' && that.field[property].field.widget !== 'dimensions') {
      throw ns.t('core', 'notImageOrDimensionsField', {':path': path});
    }

    callback(that.field[property]);
  });
};

/**
 * Append the field to the wrapper.
 *
 * @param {jQuery} $wrapper
 * @returns {undefined}
 */
ns.Coordinates.prototype.appendTo = function ($wrapper) {
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
    }
  }).click(function () {
    return false;
  }).click(function () {
    return false;
  });
};

/**
 * Create HTML for the coordinates picker.
 */
ns.Coordinates.prototype.createHtml = function () {
  const id = ns.getNextFieldId(this.field);
  const descriptionId = (this.field.description !== undefined ? ns.getDescriptionId(id) : undefined)

  var input =
    ns.createText(this.params !== undefined ? this.params.x : undefined, 15, 'X', id, descriptionId) +
    ' , ' +
    ns.createText(this.params !== undefined ? this.params.y : undefined, 15, 'Y', undefined, descriptionId);

  return ns.createFieldMarkup(this.field, input, id);
};

/**
 * Validate the current values.
 */
ns.Coordinates.prototype.validate = function () {
  var that = this;
  var coordinates = {};

  this.$inputs.each(function (i) {
    var $input = ns.$(this);
    var value = H5P.trim($input.val());
    var property = i ? 'y' : 'x';

    if (that.field.optional && !value.length) {
      return true;
    }

    if ((that.field.optional === undefined || !that.field.optional) && !value.length) {
      that.$errors.append(ns.createError(ns.t('core', 'requiredProperty', {':property': property})));
      return false;
    }

    if (value.length && !value.match(new RegExp('^[0-9]+$'))) {
      that.$errors.append(ns.createError(ns.t('core', 'onlyNumbers', {':property': property})));
      return false;
    }

    value = parseInt(value);
    if (that.field.max !== undefined && value > that.field.max[property]) {
      that.$errors.append(ns.createError(ns.t('core', 'exceedsMax', {':property': property, ':max': that.field.max[property]})));
      return false;
    }

    coordinates[property] = value;
  });

  return ns.checkErrors(this.$errors, this.$inputs, coordinates);
};

/**
 * Remove this item.
 */
ns.Coordinates.prototype.remove = function () {
  this.$item.remove();
};

// Tell the editor what widget we are.
ns.widgets.coordinates = ns.Coordinates;
