var H5PEditor = H5PEditor || {};
var ns = H5PEditor;

/**
 * Construct a library selector.
 *
 * @param {Array} libraries
 * @param {String} defaultLibrary
 * @param {Object} defaultParams
 * @returns {ns.LibrarySelector}
 */
ns.LibrarySelector = function (libraries, defaultLibrary, defaultParams) {
  var that = this;

  H5P.EventDispatcher.call(this);

  try {
    this.defaultParams = JSON.parse(defaultParams);
    if (!(this.defaultParams instanceof Object)) {
      throw true;
    }
  }
  catch (event) {
    // Content parameters are broken. Reset. (This allows for broken content to be reused without deleting it)
    this.defaultParams = {};
  }

  this.defaultLibrary = this.currentLibrary = defaultLibrary;
  this.defaultLibraryParameterized = defaultLibrary ? defaultLibrary.replace('.', '-').toLowerCase() : undefined;

  //Add tutorial and example link:
  this.$tutorialUrl = ns.$('<a class="h5p-tutorial-url" target="_blank">' + ns.t('core', 'tutorial') + '</a>').hide();
  this.$exampleUrl = ns.$('<a class="h5p-example-url" target="_blank">' + ns.t('core', 'example') + '</a>').hide();

  // Create confirm dialog
  var changeLibraryDialog = new H5P.ConfirmationDialog({
    headerText: H5PEditor.t('core', 'changeLibrary'),
    dialogText: H5PEditor.t('core', 'confirmChangeLibrary')
  }).appendTo(document.body);

  if (H5PIntegration.hubIsEnabled) {
    this.selector = new ns.SelectorHub(libraries, defaultLibrary, changeLibraryDialog);
  }
  else {
    this.selector = new ns.SelectorLegacy(libraries, defaultLibrary, changeLibraryDialog);
  }

  this.$selector = ns.$(this.selector.getElement());

  /**
   * @private
   * @param {object} library
   */
  var librarySelectHandler = function (library) {
    that.currentLibrary = library.uberName;
    that.loadSemantics(library.uberName, that.selector.getParams());

    that.$tutorialUrl.attr('href', library.tutorialUrl ? library.tutorialUrl : '#').toggle(!!library.tutorialUrl);
    that.$exampleUrl.attr('href', library.exampleUrl ? library.exampleUrl : '#').toggle(!!library.exampleUrl);
  };

  /**
   * Event handler for loading a new library editor
   * @private
   */
  var loadLibrary = function () {
    that.trigger('editorload', that.selector.currentLibrary);
    that.selector.getSelectedLibrary(librarySelectHandler);
  };

  // Change library on confirmation
  changeLibraryDialog.on('confirmed', loadLibrary);

  // Revert selector on cancel
  changeLibraryDialog.on('canceled', function () {
    that.selector.resetSelection(that.currentLibrary, that.defaultParams);
  });

  // First time a library is selected in the editor
  this.selector.on('selected', loadLibrary);

  this.selector.on('resize', function () {
    that.trigger('resize');
  });

  this.on('select', loadLibrary);
};

// Extends the event dispatcher
ns.LibrarySelector.prototype = Object.create(H5P.EventDispatcher.prototype);
ns.LibrarySelector.prototype.constructor = ns.LibrarySelector;

/**
 * Sets the current library
 *
 * @param {string} library
 */
ns.LibrarySelector.prototype.setLibrary = function (library) {
  this.trigger('select');
};

/**
 * Append the selector html to the given container.
 *
 * @param {jQuery} $element
 * @returns {undefined}
 */
ns.LibrarySelector.prototype.appendTo = function ($element) {
  this.$parent = $element;

  this.$selector.appendTo($element);
  this.$tutorialUrl.appendTo($element);
  this.$exampleUrl.appendTo($element);
};

/**
 * Display loading message and load library semantics.
 *
 * @param {String} library
 * @param {Object} params Pass in params to semantics
 * @returns {unresolved}
 */
ns.LibrarySelector.prototype.loadSemantics = function (library, params) {
  var that = this;

  if (this.form !== undefined) {
    // Remove old form.
    this.form.remove();
  }

  if (library === '-') {
    // No library chosen.
    this.$parent.attr('class', 'h5peditor');
    return;
  }
  this.$parent.attr('class', 'h5peditor ' + library.split(' ')[0].toLowerCase().replace('.', '-') + '-editor');

  // Display loading message
  var $loading = ns.$('<div class="h5peditor-loading h5p-throbber">' + ns.t('core', 'loading') + '</div>').appendTo(this.$parent);

  this.$selector.attr('disabled', true);

  ns.resetLoadedLibraries();
  ns.loadLibrary(library, function (semantics) {
    if (!semantics) {
      that.form = ns.$('<div/>', {
        'class': 'h5p-errors',
        text: H5PEditor.t('core', 'noSemantics'),
        insertAfter: $loading
      });
    }
    else {
      var overrideParams = {};
      if (params) {
        overrideParams = params;
        that.defaultParams = overrideParams;
      }
      else if (library === that.defaultLibrary || library === that.defaultLibraryParameterized) {
        overrideParams = that.defaultParams;
      }

      that.form = new ns.Form();
      that.form.replace($loading);
      that.form.currentLibrary = library;
      that.form.processSemantics(semantics, overrideParams);
    }

    that.$selector.attr('disabled', false);
    $loading.remove();
    that.trigger('editorloaded', library);
  });
};

/**
 * Returns currently selected library
 *
 * @returns {string} Currently selected library
 */
ns.LibrarySelector.prototype.getCurrentLibrary = function () {
  return this.currentLibrary;
};

/**
 * Return params needed to start library.
 */
ns.LibrarySelector.prototype.getParams = function () {
  if (this.form === undefined) {
    return;
  }

  // Only return if all fields has validated.
  var valid = true;

  if (this.form.children !== undefined) {
    for (var i = 0; i < this.form.children.length; i++) {
      if (this.form.children[i].validate() === false) {
        valid = false;
      }
    }
  }

  //return valid ? this.form.params : false;
  return this.form.params; // TODO: Switch to the line above when we are able to tell the user where the validation fails
};
