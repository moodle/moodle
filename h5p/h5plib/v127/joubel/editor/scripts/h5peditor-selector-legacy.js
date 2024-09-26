/* global ns */
/**
 * @class
 * @alias H5PEditor.SelectorLegacy
 */
ns.SelectorLegacy = function (libraries, selectedLibrary, changeLibraryDialog) {
  var self = this;

  H5P.EventDispatcher.call(this);

  var defaultLibraryParameterized = selectedLibrary ? selectedLibrary.replace('.', '-').toLowerCase() : undefined;
  this.currentLibrary = selectedLibrary;

  var options = '<option value="-">-</option>';
  for (var i = 0; i < libraries.length; i++) {
    var library = libraries[i];
    var libraryName = ns.libraryToString(library);

    // Never deny editing existing content
    // For new content deny old or restricted libs.
    if (selectedLibrary === libraryName ||
      ((library.restricted === undefined || !library.restricted) &&
      library.isOld !== true
      )
    ) {
      options += '<option value="' + libraryName + '"';
      if (libraryName === selectedLibrary || library.name === defaultLibraryParameterized) {
        options += ' selected="selected"';
      }
      if (library.tutorialUrl !== undefined) {
        options += ' data-tutorial-url="' + library.tutorialUrl + '"';
      }
      if (library.exampleUrl !== undefined) {
        options += ' data-example-url="' + library.exampleUrl + '"';
      }
      options += '>' + library.title + (library.isOld===true ? ' (deprecated)' : '') + '</option>';
    }
  }

  this.$selector = ns.$('' +
    '<select name="h5peditor-library" title="' + ns.t('core', 'selectLibrary') + '"' + '>' +
      options +
    '</select>'
  ).change(function () {
    // Use timeout to avoid bug in Chrome >44, when confirm is used inside change event.
    // Ref. https://code.google.com/p/chromium/issues/detail?id=525629
    setTimeout(function () {
      if (!self.currentLibrary) {
        self.currentLibrary = self.$selector.val();
        self.trigger('selected');
        return;
      }

      self.currentLibrary = self.$selector.val();
      changeLibraryDialog.show(self.$selector.offset().top);
    }, 0);
  });
};

/**
 * Reset selector to provided library
 *
 * @param {string} library
 * @param {Object} params
 * @param {Object} metadata
 */
ns.SelectorLegacy.prototype.resetSelection = function (library, params, metadata) {
  this.$selector.val(library);
  this.currentParams = params;
  this.currentMetadata = metadata;
  this.currentLibrary = library;
};

/**
 * Get currently selected library.
 *
 * @returns {string}
 */
ns.SelectorLegacy.prototype.getSelectedLibrary = function (next) {
  var that = this;
  var $option = this.$selector.find(':selected');
  next({
    uberName: that.currentLibrary,
    tutorialUrl: $option.data('tutorial-url'),
    exampleUrl: $option.data('example-url')
  });
};

/**
 * Load new params into legacy selector
 *
 * @returns {undefined}
 */
ns.SelectorLegacy.prototype.getParams = function () {
  return this.currentParams;
};

/**
 * Load new metadata into legacy selector
 *
 * @returns {undefined}
 */
ns.SelectorLegacy.prototype.getMetadata = function () {
  return this.currentMetadata;
};

/**
 * Returns the html element for the hub
 *
 * @return {HTMLElement}
 */
ns.SelectorLegacy.prototype.getElement = function () {
  return this.$selector.get(0);
};
