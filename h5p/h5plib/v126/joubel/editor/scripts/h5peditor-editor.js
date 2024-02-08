/* global ns */
window.ns = window.H5PEditor = window.H5PEditor || {};

/**
 * Construct the editor.
 *
 * @class H5PEditor.Editor
 * @param {string} library
 * @param {string} defaultParams
 * @param {Element} replace
 * @param {Function} iframeLoaded
 */
ns.Editor = function (library, defaultParams, replace, iframeLoaded) {
  var self = this;

  // Library may return "0", make sure this doesn't return true in checks
  library = library && library != 0 ? library : '';

  let parsedParams = {};
  try {
    parsedParams = JSON.parse(defaultParams);
  }
  catch (e) {
    // Ignore failed parses, this should be handled elsewhere
  }

  // Define iframe DOM Element through jQuery
  var $iframe = ns.$('<iframe/>', {
    'css': {
      display: 'block',
      width: '100%',
      height: '3em',
      border: 'none',
      zIndex: 101,
      top: 0,
      left: 0
    },
    'class': 'h5p-editor-iframe',
    'frameBorder': '0',
    'allowfullscreen': 'allowfullscreen',
    'allow': "fullscreen"
  });
  const metadata = parsedParams.metadata;
  let title = ''
  if (metadata) {
    if (metadata.a11yTitle) {
      title = metadata.a11yTitle;
    }
    else if (metadata.title) {
      title = metadata.title;
    }
  }
  $iframe.attr('title', title);


  // The DOM element is often used directly
  var iframe = $iframe.get(0);

  /**
   * Set the iframe content and start loading the necessary assets
   *
   * @private
   */
  var populateIframe = function () {
    if (!iframe.contentDocument) {
      return; // Not possible, iframe 'load' hasn't been triggered yet
    }
    const language = metadata && metadata.defaultLanguage
      ? metadata.defaultLanguage : ns.contentLanguage;
    iframe.contentDocument.open();
    iframe.contentDocument.write(
      '<!doctype html><html lang="' + language + '">' +
      '<head>' +
      ns.wrap('<link rel="stylesheet" href="', ns.assets.css, '">') +
      ns.wrap('<script src="', ns.assets.js, '"></script>') +
      '</head><body>' +
      '<div class="h5p-editor h5peditor">' + ns.t('core', 'loading') + '</div>' +
      '</body></html>');
    iframe.contentDocument.close();
    iframe.contentDocument.documentElement.style.overflow = 'hidden';
  };

  /**
   * Wrapper for binding iframe unload event to a callback for multiple
   * devices.
   *
   * @private
   * @param {jQuery} $window of iframe
   * @param {function} action callback on unload
   */
  var onUnload = function ($window, action) {
    $window.one('beforeunload unload', function () {
      $window.off('pagehide beforeunload unload');
      action();
    });
    $window.on('pagehide', action);
  };

  /**
   * Object for keeping the scrollHeight + clientHeight used when the previous resize occurred
   * This is used to skip handling resize when nothing actually is resized.
   */
  const previousHeight = {
    scroll: 0,
    client: 0
  };

  /**
   * Checks if iframe needs resizing, and then resize it.
   *
   * @public
   * @param {bool} force If true, force resizing
   */
  self.resize = function (force) {
    force = (force === undefined ? false : force);

    if (!iframe.contentDocument || !iframe.contentDocument.body || self.preventResize) {
      return; // Prevent crashing when iframe is unloaded
    }

    // Has height changed?
    const heightNotChanged =
      previousHeight.scroll === iframe.contentDocument.body.scrollHeight &&
      previousHeight.client === iframe.contentWindow.document.body.clientHeight;

    if (!force && (heightNotChanged || (
        iframe.clientHeight === iframe.contentDocument.body.scrollHeight &&
        Math.abs(iframe.contentDocument.body.scrollHeight - iframe.contentWindow.document.body.clientHeight) <= 1
    ))) {
      return; // Do not resize unless page and scrolling differs
      // Note: ScrollHeight may be 1px larger in some cases(Edge) where the actual height is a fraction.
    }

    // Save the current scrollHeight/clientHeight
    previousHeight.scroll = iframe.contentDocument.body.scrollHeight;
    previousHeight.client = iframe.contentWindow.document.body.clientHeight;

    // Retain parent size to avoid jumping/scrolling
    var parentHeight = iframe.parentElement.style.height;
    iframe.parentElement.style.height = iframe.parentElement.clientHeight + 'px';

    // Reset iframe height, in case content has shrinked.
    iframe.style.height = iframe.contentWindow.document.body.clientHeight + 'px';

    // Resize iframe so all content is visible. Use scrollHeight to make sure we get everything
    iframe.style.height = iframe.contentDocument.body.scrollHeight + 'px';

    // Free parent
    iframe.parentElement.style.height = parentHeight;
  };

  // Register loaded event handler for iframe
  var load = function () {
    if (!iframe.contentWindow.H5P) {
      // The iframe has probably been reloaded, losing its content
      setTimeout(function () {
        // Wait for next tick as a new 'load' can't be triggered recursivly
        populateIframe();
      }, 0);
      return;
    }

    // Trigger loaded callback. Could this have been an event?
    if (iframeLoaded) {
      iframeLoaded.call(this.contentWindow);
    }

    // Used for accessing resources inside iframe
    self.iframeWindow = this.contentWindow;

    var LibrarySelector = this.contentWindow.H5PEditor.LibrarySelector;
    var $ = this.contentWindow.H5P.jQuery;
    var $container = $('body > .h5p-editor');

    this.contentWindow.H5P.$body = $(this.contentDocument.body);

    /**
     * Trigger semi-fullscreen for $element.
     *
     * @param {jQuery} $element Element to put in semi-fullscreen
     * @param {function} before Callback that runs after entering
     *   semi-fullscreen
     * @param {function} done Callback that runs after exiting semi-fullscreen
     * @return {function} Exit trigger
     */
    this.contentWindow.H5PEditor.semiFullscreen = function ($element, after, done) {
      const exit = self.semiFullscreen($iframe, $element, done);
      after();
      return exit;
    };

    // Load libraries data
    $.ajax({
      url: this.contentWindow.H5PEditor.getAjaxUrl(H5PIntegration.hubIsEnabled ? 'content-type-cache' : 'libraries')
    }).fail(function () {
      $container.html('Error, unable to load libraries.');
    }).done(function (data) {
      if (data.success === false) {
        $container.html(data.message + ' (' + data.errorCode  + ')');
        return;
      }

      // Create library selector
      self.selector = new LibrarySelector(data, library, defaultParams);
      self.selector.appendTo($container.html(''));

      // Resize iframe when selector resizes
      self.selector.on('resize', self.resize.bind(self));

      /**
       * Event handler for exposing events
       *
       * @private
       * @param {H5P.Event} event
       */
      var relayEvent = function (event) {
        H5P.externalDispatcher.trigger(event);
      };
      self.selector.on('editorload', relayEvent);
      self.selector.on('editorloaded', relayEvent);

      // Set library if editing
      if (library) {
        self.selector.setLibrary(library);
      }
    });

    // Start resizing the iframe
    if (iframe.contentWindow.MutationObserver !== undefined) {
      // If supported look for changes to DOM elements. This saves resources.
      var running;
      var limitedResize = function () {
        if (!running) {
          running = setTimeout(function () {
            self.resize();
            running = null;
          }, 40); // 25 fps cap
        }
      };

      new iframe.contentWindow.MutationObserver(limitedResize).observe(iframe.contentWindow.document.body, {
        childList: true,
        attributes: true,
        characterData: true,
        subtree: true,
        attributeOldValue: false,
        characterDataOldValue: false
      });

      H5P.$window.resize(limitedResize);
      self.resize();
    }
    else {
      // Use an interval for resizing the iframe
      (function resizeInterval() {
        self.resize();
        setTimeout(resizeInterval, 40); // No more than 25 times per second
      })();
    }

    // Handle iframe being reloaded
    onUnload($(iframe.contentWindow), function () {
      if (self.formSubmitted) {
        return;
      }

      // Keep track of previous state
      library = self.getLibrary();
      defaultParams = JSON.stringify(self.getParams(true));
    });
  };

  // Insert iframe into DOM
  $iframe.replaceAll(replace);

  // Need to put this after the above replaceAll(), since that one makes Safari
  // 11 trigger a load event for the iframe
  $iframe.on('load', load);

  // Populate iframe with the H5P Editor
  // (should not really be done until 'load', but might be here in case the iframe is reloaded?)
  populateIframe();
};

/**
 * Find out which library is used/selected.
 *
 * @alias H5PEditor.Editor#getLibrary
 * @returns {string} Library name
 */
ns.Editor.prototype.getLibrary = function () {
  if (this.selector !== undefined) {
    return this.selector.getCurrentLibrary();
  }
  else if (this.selectedContentTypeId) {
    return this.selectedContentTypeId;
  }
  else {
    console.warn('no selector defined for "getLibrary"');
  }
};

/**
 * Get parameters needed to start library.
 *
 * @alias H5PEditor.Editor#getParams
 * @returns {Object} Library parameters
 */
ns.Editor.prototype.getParams = function (notFormSubmit) {
  if (!notFormSubmit) {
    this.formSubmitted = true;
  }
  if (this.selector !== undefined) {
    return {
      params: this.selector.getParams(),
      metadata: this.selector.getMetadata()
    };
  }
  else {
    console.warn('no selector defined for "getParams"');
  }
};

/**
 * Validate editor data and submit content using callback.
 *
 * @alias H5PEditor.Editor#getContent
 * @param {Function} submit Callback to submit the content data
 * @param {Function} [error] Callback on failure
 */
ns.Editor.prototype.getContent = function (submit, error) {
  const iframeEditor = this.iframeWindow.H5PEditor;

  if (!this.selector.form) {
    if (error) {
      error('content-not-selected');
    }
    return;
  }

  const content = {
    title: this.isMainTitleSet(),
    library: this.getLibrary(),
    params: this.getParams()
  };

  if (!content.title) {
    if (error) {
      error('missing-title');
    }
    return;
  }
  if (!content.library) {
    if (error) {
      error('missing-library');
    }
    return;
  }
  if (!content.params) {
    if (error) {
      error('missing-params');
    }
    return;
  }
  if (!content.params.params) {
    if (error) {
      error('missing-params-params');
    }
    return;
  }

  library = new iframeEditor.ContentType(content.library);
  const upgradeLibrary = iframeEditor.ContentType.getPossibleUpgrade(library, this.selector.libraries.libraries !== undefined ? this.selector.libraries.libraries : this.selector.libraries);
  if (upgradeLibrary) {
    // We need to run content upgrade before saving
    iframeEditor.upgradeContent(library, upgradeLibrary, content.params, function (err, result) {
      if (err) {
        if (error) {
          error(err);
        }
      }
      else {
        content.library = iframeEditor.ContentType.getNameVersionString(upgradeLibrary);
        content.params = result;
        submit(content);
      }
    })
  }
  else {
    // All OK, store the data
    content.params = JSON.stringify(content.params);
    submit(content);
  }
};

/**
 * Check if main title is set. If not, focus on it!
 *
 * @return {[type]}
 */
ns.Editor.prototype.isMainTitleSet = function () {
  var mainTitleField = this.selector.form.metadataForm.getExtraTitleField();

  // validate() actually doesn't return a boolean, but the trimmed value
  // We know title is a mandatory field, so that's what we are checking here
  var valid = mainTitleField.validate();
  if (!valid) {
    mainTitleField.$input.focus();
  }
  return valid;
};

/**
 *
 * @alias H5PEditor.Editor#presave
 * @param content
 * @return {H5PEditor.Presave}
 */
ns.Editor.prototype.getMaxScore = function (content) {
  try {
    var value = this.selector.presave(content, this.getLibrary());
    return value.maxScore;
  }
  catch (e) {
    // Deliberatly catching error
    return 0;
  }
};

/**
 * Trigger semi-fullscreen for $iframe and $element.
 *
 * @param {jQuery} $iframe
 * @param {jQuery} $element
 * @param {function} done Callback that runs after semi-fullscreen exit
 * @return {function} Exit trigger
 */
ns.Editor.prototype.semiFullscreen = function ($iframe, $element, done) {
  const self = this;

  // Add class for element to cover all of the page
  const $classes = $iframe.add($element).addClass('h5peditor-semi-fullscreen');
  // NOTE: Styling for this class is provided by Core

  // Prevent the resizing loop from messing with the iframe while
  // the semi-fullscreen is active.
  self.preventResize = true;

  // Prevent body overflow
  const bodyOverflowValue = document.body.style.getPropertyValue('overflow');
  const bodyOverflowPriority = document.body.style.getPropertyPriority('overflow');
  document.body.style.setProperty('overflow', 'hidden', 'important');

  // Reset the iframe's default CSS props
  $iframe.css({
    width: '',
    height: '',
    zIndex: '',
    top: '',
    left: ''
  });
  // NOTE: Style attribute has been used here since June 2014 since there are
  // no CSS files in H5PEditor loaded outside the iframe.

  // Hide all elements except the iframe and the fullscreen elements
  // This is to avoid tabbing and readspeakers accessing these while
  // the semi-fullscreen is active.
  const iframeWindow = $iframe[0].contentWindow;
  const restoreOutside = ns.hideAllButOne($iframe[0], iframeWindow);
  const restoreInside = ns.hideAllButOne($element[0], window);

  /**
   * Trigger semi-fullscreen exit on ESC key
   *
   * @private
   */
  const handleKeyup = function (e) {
    if (e.which === 27) {
      restore();
    }
  }
  iframeWindow.document.body.addEventListener('keyup', handleKeyup);

  /**
   * Exit/restore callback returned.
   *
   * @private
   */
  const restore = function () {
    // Remove our special class
    $classes.removeClass('h5peditor-semi-fullscreen');

    // Allow the resizing loop to adjust the iframe
    self.preventResize = false;

    // Restore body overflow
    document.body.style.setProperty('overflow', bodyOverflowValue, bodyOverflowPriority);

    // Restore the default style attribute properties
    $iframe.css({
      width: '100%',
      height: '3em',
      zIndex: 101,
      top: 0,
      left: 0
    });

    // Return all of the elements hidden back to their original state
    restoreOutside();
    restoreInside();

    iframeWindow.document.body.removeEventListener('keyup', handleKeyup);
    done(); // Callback for UI

    self.resize(true);
  }

  return restore;
};

/**
 * Will hide all siblings and ancestor siblings(uncles and aunts) of element.
 *
 * @param {Element} element
 * @param {Window} win Needed to get the correct computed style
 * @return {function} Restore trigger
 */
ns.hideAllButOne = function (element, win) {
  // Make it easy and quick to restore previous display values
  const restore = [];

  /**
   * Check if the given element is visible.
   *
   * @private
   * @param {Element} element
   */
  const isVisible = function (element) {
    if (element.offsetParent === null) {
      // Must check computed style to be sure in case of fixed element
      if (win.getComputedStyle(element).display !== 'none') {
        return true;
      }
    }
    else {
      return true;
    }
    return false;
  }

  /**
   * Recusive function going up the DOM tree.
   * Will hide all siblings of given element.
   *
   * @private
   * @param {Element} element
   */
  const recurse = function (element) {
    // Loop through siblings
    for (let i = 0; i < element.parentElement.children.length; i++) {
      let sibling = element.parentElement.children[i];
      if (sibling === element) {
        continue; // Skip where we came from
      }

      // Only hide if sibling is visible
      if (isVisible(sibling)) {
        // Make it simple to restore original value
        restore.push({
          element: sibling,
          display: sibling.style.getPropertyValue('display'),
          priority: sibling.style.getPropertyPriority('display')
        });
        sibling.style.setProperty('display', 'none', 'important');
      }
    }

    // Climb up the tree until we hit some body
    if (element.parentElement.tagName !== 'BODY') {
      recurse(element.parentElement);
    }
  }
  recurse(element); // Start

  /**
   * Restore callback returned.
   *
   * @private
   */
  return function () {
    for (let i = restore.length - 1; i > -1; i--) { // In opposite order
      restore[i].element.style.setProperty('display', restore[i].display, restore[i].priority);
    }
  };
}

/**
 * Editor translations index by library name or "core".
 *
 * @member {Object} H5PEditor.language
 */
ns.language = {};

/**
 * Translate text strings.
 *
 * @method H5PEditor.t
 * @param {string} library The library name(machineName), or "core".
 * @param {string} key Translation string identifier.
 * @param {Object} [vars] Placeholders and values to replace in the text.
 * @returns {string} Translated string, or a text if string translation is
 *   missing.
 */
ns.t = function (library, key, vars) {
  if (ns.language[library] === undefined) {
    return 'Missing translations for library ' + library;
  }

  var translation;
  if (library === 'core') {
    if (ns.language[library][key] === undefined) {
      return 'Missing translation for ' + key;
    }
    translation = ns.language[library][key];
  }
  else {
    if (ns.language[library].libraryStrings === undefined || ns.language[library].libraryStrings[key] === undefined) {
      return ns.t('core', 'missingTranslation', {':key': key});
    }
    translation = ns.language[library].libraryStrings[key];
  }

  // Replace placeholder with variables.
  for (var placeholder in vars) {
    if (vars[placeholder] === undefined) {
      continue;
    }
    translation = translation.replace(placeholder, vars[placeholder]);
  }

  return translation;
};

/**
 * Wraps multiple content between a prefix and a suffix.
 *
 * @method H5PEditor.wrap
 * @param {string} prefix Inserted before the content.
 * @param {Array} content List of content to be wrapped.
 * @param {string} suffix Inserted after the content.
 * @returns {string} All content put together with prefix and suffix.
 */
ns.wrap = function (prefix, content, suffix) {
  var result = '';
  for (var i = 0; i < content.length; i++) {
    result += prefix + content[i] + suffix;
  }
  return result;
};
