/* global ns */
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

  this.libraries = libraries;

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
  this.$tutorialUrl = ns.$(
    '<a class="h5p-tutorial-url" target="_blank">' + 
      '<span class="h5p-tutorial-url-label">' + 
        ns.t('core', 'tutorial') + 
      '</span>' +
    '</a>'
  ).hide();
  this.$exampleUrl = ns.$(
    '<a class="h5p-example-url" target="_blank">' + 
      '<span class="h5p-example-url-label">' + 
        ns.t('core', 'example') + 
      '</span>' +
    '</a>'
  ).hide();

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
    that.loadSemantics(library.uberName, that.selector.getParams(), that.selector.getMetadata());

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

  /**
   * Confirm replace if there is content selected
   *
   * @param {number} top Offset
   * @param {function} next Next callback
   */
  this.confirmPasteError = function (message, top, next) {
    // Confirm changing library
    var confirmReplace = new H5P.ConfirmationDialog({
      headerText: H5PEditor.t('core', 'pasteError'),
      dialogText: message,
      cancelText: ' ',
      confirmText: H5PEditor.t('core', 'ok')
    }).appendTo(document.body);
    confirmReplace.on('confirmed', next);
    confirmReplace.show(top);
  };

  // Change library on confirmation
  changeLibraryDialog.on('confirmed', loadLibrary);

  // Revert selector on cancel
  changeLibraryDialog.on('canceled', function () {
    that.selector.resetSelection(that.currentLibrary, that.defaultParams, that.form.metadata, true);
  });

  // First time a library is selected in the editor
  this.selector.on('selected', loadLibrary);

  this.selector.on('resize', function () {
    that.trigger('resize');
  });

  this.on('select', loadLibrary);
  H5P.externalDispatcher.on('datainclipboard', this.updateCopyPasteButtons.bind(this));
  this.selector.on('paste', this.pasteContent.bind(this));
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
  var self = this;
  this.$parent = $element;

  this.$selector.appendTo($element);
  this.$tutorialUrl.appendTo($element);
  this.$exampleUrl.appendTo($element);

  if (window.localStorage) {
    var $buttons = ns.$(ns.createCopyPasteButtons()).appendTo($element);

    // Hide copy paste until library is selected:
    $buttons.addClass('hidden');
    self.on('editorloaded', function () {
      $buttons.removeClass('hidden');
    });

    this.$copyButton = $buttons.find('.h5peditor-copy-button').click(function () {
      H5P.clipboardify({
        library: self.getCurrentLibrary(),
        params: self.getParams(),
        metadata: self.getMetadata()
      });
      ns.attachToastTo(
        self.$copyButton.get(0),
        H5PEditor.t('core', 'copiedToClipboard'), {
          position: {
            horizontal: 'center',
            vertical: 'above',
            noOverflowX: true
          }
        }
      );
    });
    this.$pasteButton = $buttons.find('.h5peditor-paste-button')
      .click(self.pasteContent.bind(this));

    self.updateCopyPasteButtons();
  }
};

/**
 * Update state of copy and paste buttons dependent on what is currently in
 * the clipboard
 */
ns.LibrarySelector.prototype.updateCopyPasteButtons = function () {
  if (!window.localStorage) {
    return;
  }

  // Check if content type is supported here
  const pasteCheck = ns.canPastePlus(H5P.getClipboard(), this.libraries);
  const canPaste = pasteCheck.canPaste;

  this.$copyButton
    .prop('disabled', false)
    .toggleClass('disabled', false);

  this.$pasteButton
    .text(ns.t('core', 'pasteAndReplaceButton'))
    .attr('title', canPaste ? ns.t('core', 'pasteAndReplaceFromClipboard') : pasteCheck.description)
    .toggleClass('disabled', !canPaste)
    .prop('disabled', !canPaste);

  this.selector.setCanPaste && this.selector.setCanPaste(canPaste, !canPaste ? pasteCheck.description : undefined);
};

/**
 * Sets the current library
 *
 * @param {string} library
 */
ns.LibrarySelector.prototype.pasteContent = function () {
  var self = this;
  var clipboard = H5P.getClipboard();

  ns.confirmReplace(self.getCurrentLibrary(), self.$parent.offset().top, function () {
    self.selector.resetSelection(clipboard.generic.library, clipboard.generic.params, clipboard.generic.metadata, false);
    self.setLibrary();
  });
};

/**
 * Display loading message and load library semantics.
 *
 * @param {String} library
 * @param {Object} params Pass in params to semantics
 * @returns {unresolved}
 */
ns.LibrarySelector.prototype.loadSemantics = function (library, params, metadata) {
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

      if (!metadata) {
        metadata = overrideParams.metadata;
      }
      const defaultLanguage = metadata && metadata.defaultLanguage
        ? metadata.defaultLanguage
        : null;
      that.form = new ns.Form(
        library,
        ns.libraryCache[library].languages,
        defaultLanguage
      );
      that.form.replace($loading);
      that.form.currentLibrary = library;
      that.form.processSemantics(semantics, overrideParams, metadata);
      that.updateCopyPasteButtons();
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
  //var valid = true;

  if (this.form.metadataForm.children !== undefined) {
    for (var i = 0; i < this.form.metadataForm.children.length; i++) {
      if (this.form.metadataForm.children[i].validate() === false) {
        //valid = false;
      }
    }
  }

  if (this.form.children !== undefined) {
    for (var i = 0; i < this.form.children.length; i++) {
      if (this.form.children[i].validate() === false) {
        //valid = false;
      }
    }
  }

  //return valid ? this.form.params : false;
  return this.form.params; // TODO: Switch to the line above when we are able to tell the user where the validation fails
};

/**
 * Get the metadata of the main form.
 *
 * @return {object} Metadata object.
 */
ns.LibrarySelector.prototype.getMetadata = function () {
  if (this.form === undefined) {
    return;
  }

  return this.form.metadata;
};

/**
 *
 * @param content
 * @param library
 * @returns {H5PEditor.Presave} Result after processing library and content
 */
ns.LibrarySelector.prototype.presave = function (content, library) {
  return (new ns.Presave).process(library, content);
};
