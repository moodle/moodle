/* global ns */
/**
 * Create a group of fields.
 *
 * @param {mixed} parent
 * @param {object} field
 * @param {mixed} params
 * @param {function} setValue
 * @returns {ns.Group}
 */
ns.Group = function (parent, field, params, setValue) {
  // Support for events
  H5P.EventDispatcher.call(this);

  if (field.label === undefined) {
    field.label = field.name;
  }
  else if (field.label === 0) {
    field.label = '';
  }

  this.parent = parent;
  this.passReadies = true;
  this.params = params;
  this.setValue = setValue;
  this.library = parent.library + '/' + field.name;
  this.expandedState = false;

  if (field.deprecated !== undefined && field.deprecated) {
    this.field = H5P.cloneObject(field, true);
    var empties = 0;
    for (var i = 0; i < this.field.fields.length; i++) {
      var f = this.field.fields[i];
      if (params !== undefined && params[f.name] === '') {
        delete params[f.name];
      }
      if (params === undefined || params[f.name] === undefined) {
        f.widget = 'none';
        empties++;
      }
    }
    if (i === empties) {
      this.field.fields = [];
    }
  }
  else {
    this.field = field;
  }

  if (this.field.optional === true) {
    // If this field is optional, make sure child fields are as well
    for (var j = 0; j < this.field.fields.length; j++) {
      this.field.fields[j].optional = true;
    }
  }
};

// Extends the event dispatcher
ns.Group.prototype = Object.create(H5P.EventDispatcher.prototype);
ns.Group.prototype.constructor = ns.Group;

/**
 * Append group to its wrapper.
 *
 * @param {jQuery} $wrapper
 * @returns {undefined}
 */
ns.Group.prototype.appendTo = function ($wrapper) {
  var that = this;

  if (this.field.fields.length === 0) {
    // No fields or all are deprecated
    this.setValue(this.field);
    return;
  }

  // Add fieldset wrapper for group
  this.$group = ns.$('<fieldset/>', {
    'class': 'field group ' + H5PEditor.createImportance(this.field.importance) + ' field-name-' + this.field.name,
    appendTo: $wrapper
  });

  // Add title expand/collapse button
  this.$title = ns.$('<div/>', {
    'class': 'title',
    'aria-expanded': 'false',
    title: ns.t('core', 'expandCollapse'),
    role: 'button',
    tabIndex: 0,
    on: {
      click: function () {
        that.toggle();
      },
      keypress: function (event) {
        if ((event.charCode || event.keyCode) === 32) {
          that.toggle();
          event.preventDefault();
        }
      }
    },
    appendTo: this.$group
  });

  // Add content container
  this.$content = ns.$('<div/>', {
    'class': 'content',
    appendTo: this.$group
  });

  if (this.hasSingleChild() && !this.isSubContent()) {
    this.$content.addClass('h5peditor-single');
    this.children = [];
    var field = this.field.fields[0];
    var widget = field.widget === undefined ? field.type : field.widget;
    this.children[0] = new ns.widgets[widget](this, field, this.params, function (field, value) {
      that.setValue(that.field, value);
    });
    this.children[0].appendTo(this.$content);
  }
  else {
    if (this.params === undefined) {
      this.params = {};
      this.setValue(this.field, this.params);
    }

    this.params = this.initSubContent(this.params);

    ns.processSemanticsChunk(this.field.fields, this.params, this.$content, this);
  }

  // Set summary
  this.findSummary();

  // Check if group should be expanded.
  // Default is to be collapsed unless explicity defined in semantics by optional attribute expanded
  if (this.field.expanded === true) {
    this.expand();
  }
};

/**
 * Return whether this group is Sub Content
 *
 * @private
 * @return {boolean}
 */
ns.Group.prototype.hasSingleChild = function () {
  return this.field.fields.length === 1;
};

/**
 * Add generated 'subContentId' attribute, if group is "sub content (library-like embedded structure)"
 *
 * @param {object} params
 *
 * @private
 * @return {object}
 */
ns.Group.prototype.initSubContent = function (params) {
  // If group contains library-like sub content that needs UUIDs
  if (this.isSubContent()) {
    params['subContentId'] = params['subContentId'] || H5P.createUUID();
  }

  return params;
};

/**
 * Return whether this group is Sub Content
 *
 * @private
 * @return {boolean}
 */
ns.Group.prototype.isSubContent = function () {
  return this.field.isSubContent === true;
};

/**
 * Toggle expand/collapse for the given group.
 */
ns.Group.prototype.toggle = function () {
  if (this.preventToggle) {
    this.preventToggle = false;
    return;
  }

  if (this.isExpanded()) {
    this.collapse();
  }
  else {
    this.expand();
  }
};

/**
 * Expand the given group.
 */
ns.Group.prototype.expand = function () {
  this.$title.attr('aria-expanded', 'true');
  // Set timeout is necessary because aria-expanded status is not announced
  // when the :before element changes content because Firefox
  // re-creates the accessible element..
  // @see https://github.com/nvaccess/nvda/issues/8341
  // Should be fixeed by Firefox 70 (https://bugzilla.mozilla.org/show_bug.cgi?id=686400)
  setTimeout(function () {
    this.expandedState = true;
    this.trigger('expanded');
    this.$group.addClass('expanded');
  }.bind(this), 100);
};

/**
 * Collapse the given group (if valid).
 *
 * @returns {boolean} True, if the group is valid. False, otherwise.
 */
ns.Group.prototype.collapse = function () {
  // Do not collapse before valid!
  var valid = true;
  for (var i = 0; i < this.children.length; i++) {
    if (this.children[i].validate() === false) {
      valid = false;
    }
  }
  if (valid) {
    this.$title.attr('aria-expanded', 'false');
    // Set timeout is necessary because aria-expanded status is not announced
    // when the :before element changes content because Firefox
    // re-creates the accessible element..
    // @see https://github.com/nvaccess/nvda/issues/8341
    // Should be fixeed by Firefox 70 (https://bugzilla.mozilla.org/show_bug.cgi?id=686400)
    setTimeout(function () {
      this.expandedState = false;
      this.trigger('collapsed');
      this.$group.removeClass('expanded');
    }.bind(this), 100);
  }

  return valid;
};

/**
 * Determine if the group is expanded.
 * @returns {boolean} True, if the group is expanded. False, otherwise.
 */
ns.Group.prototype.isExpanded = function () {
  return this.expandedState;
};

/**
 * Find summary to display in group header.
 */
ns.Group.prototype.findSummary = function () {
  var that = this;
  var summary;
  for (var j = 0; j < this.children.length; j++) {
    var child = this.children[j];
    if (child.field === undefined) {
      continue;
    }
    var params = (that.hasSingleChild() && !that.isSubContent()) ? this.params : this.params[child.field.name];
    var widget = ns.getWidgetName(child.field);

    if (widget === 'text' || widget === 'html') {
      if (params !== undefined && params !== '') {
        summary = params.replace(/(<([^>]+)>)/ig, "");
      }

      child.$input.change(function () {
        var params = (that.hasSingleChild() && !that.isSubContent()) ? that.params : that.params[child.field.name];
        if (params !== undefined && params !== '') {
          that.setSummary(params.replace(/(<([^>]+)>)/ig, ""));
        }
      });
      break;
    }
    else if (widget === 'library') {
      let lastLib;
      if (child.params !== undefined) {
        summary = child.$select.children(':selected').text();
        if (child.params.metadata && child.params.metadata.title) {
          // The given title usually makes more sense than the type name
          summary = child.params.metadata.title + (!child.libraries || (child.libraries.length > 1 && child.params.metadata.title.indexOf(summary) === -1) ? ' (' +  summary + ')' : '');
        }
        else if (!child.params.library) {
          // Nothing selected
          summary = that.field.label;
        }
      }
      const setSummary = function () {
        if (child.params && child.params.metadata && child.params.metadata.title) {
          // The given title usually makes more sense than the type name
          that.setSummary(child.params.metadata.title + (child.libraries.length > 1 && child.params.metadata.title.indexOf(lastLib.title) === -1 ? ' (' +  lastLib.title + ')' : ''));
        }
        else {
          that.setSummary(lastLib ? lastLib.title : that.field.label);
        }
      };
      if (child.metadataForm) {
        child.metadataForm.on('titlechange', setSummary);
      }
      child.change(function (library) {
        lastLib = library;
        setSummary();

        if (child.metadataForm) {
          // Update summary when metadata title changes
          child.metadataForm.off('titlechange', setSummary);
          child.metadataForm.on('titlechange', setSummary);
        }
      });
      break;
    }
  }
  this.setSummary(summary);
};

/**
 * Set the given group summary.
 *
 * @param {string} summary
 * @returns {undefined}
 */
ns.Group.prototype.setSummary = function (summary) {
  var summaryText;

  // Parse html
  var summaryTextNode = ns.$.parseHTML(summary);

  if (summaryTextNode !== null && summaryTextNode.length) {
    summaryText = summaryTextNode[0].nodeValue;
  }

  // Make it possible for parent to monitor summary changes
  this.trigger('summary', summaryText);

  if (summaryText !== undefined) {
    summaryText = (summaryText.length > 48 ? summaryText.substr(0, 45) + '...' : summaryText);
  }
  else {
    summaryText = this.field.label;
  }

  this.$title.text(summaryText);
};

/**
 * Validate all children.
 */
ns.Group.prototype.validate = function () {
  var valid = true;

  if (this.children !== undefined) {
    for (var i = 0; i < this.children.length; i++) {
      if (this.children[i].validate() === false) {
        valid = false;
      }
    }
  }

  return valid;
};

/**
 * Allows ancestors and widgets to do stuff with our children.
 *
 * @public
 * @param {Function} task
 */
ns.Group.prototype.forEachChild = function (task) {
  for (var i = 0; i < this.children.length; i++) {
    task(this.children[i], i);
  }
};

/**
 * Collect functions to execute once the tree is complete.
 *
 * @param {function} ready
 * @returns {undefined}
 */
ns.Group.prototype.ready = function (ready) {
  this.parent.ready(ready);
};

/**
 * Remove this item.
 */
ns.Group.prototype.remove = function () {
  if (this.$group !== undefined) {
    ns.removeChildren(this.children);
    this.$group.remove();
  }
};

/**
 * Get a copy of the fields semantics used by this group.
 * @return {Array}
 */
ns.Group.prototype.getFields = function () {
  return H5PEditor.$.extend(true, [], this.field.fields);
};

/**
 * When someone from the outside wants to set a value.
 *
 * @param {Object} value
 */
ns.Group.prototype.forceValue = function (value) {
  for (let i = 0; i < this.children.length; i++) {
    this.children[i].forceValue(value[this.children[i].field.name]);
  }
};

// Tell the editor what widget we are.
ns.widgets.group = ns.Group;
