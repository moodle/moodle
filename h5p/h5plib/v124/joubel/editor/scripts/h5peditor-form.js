/* global ns */
/**
 * Construct a form from library semantics.
 */
ns.Form = function (library, startLanguages, defaultLanguage) {
  var self = this;

  this.params = {};
  this.passReadies = false;
  this.commonFields = {};

  this.$form = ns.$('' +
    '<div class="h5peditor-form">' +
      '<div class="tree"></div>' +
      '<div class="common collapsed hidden">' +
        '<div class="fields">' +
          '<p class="desc">' +
            ns.t('core', 'commonFieldsDescription') +
          '</p>' +
          '<div class="h5peditor-language-switcher">' +
            '<label class="language-label" for="h5peditor-language-switcher">' + ns.t('core', 'language') + ':</label>' +
            '<select id="h5peditor-language-switcher">' +
              '<option value="-">' + ns.t('core', 'noLanguagesSupported') + '</option>' +
            '</select>' +
          '</div>' +
          '<div class="h5peditor-language-notice">' +
            '<div class="first"></div>' +
            '<div class="last"></div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>'
  );
  this.$common = this.$form.find('.common > .fields');

  if (ns.FullscreenBar !== undefined) {
    // Exception from rules
    if (library.indexOf('H5P.CoursePresentation') === -1 &&
        library.indexOf('H5P.BranchingScenario') === -1 &&
        library.indexOf('H5P.InteractiveVideo') === -1) {
      ns.FullscreenBar(this.$form, library);
    }
  }

  // Add title expand/collapse button
  self.$commonButton = ns.$('<div/>', {
    'class': 'h5peditor-label',
    'aria-expanded': 'false',
    title: ns.t('core', 'expandCollapse'),
    role: 'button',
    tabIndex: 0,
    html: '<span class="icon"></span>' + ns.t('core', 'commonFields'),
    on: {
      click: function () {
        toggleCommonFields();
      },
      keypress: function (event) {
        if ((event.charCode || event.keyCode) === 32) {
          toggleCommonFields();
          event.preventDefault();
        }
      }
    },
    prependTo: this.$common.parent()
  });

  // Alternate background colors
  this.zebra = "odd";

  // Locate the language switcher DOM element
  const $switcher = this.$form.find('.h5peditor-language-switcher select');
  const $notice = this.$form.find('.h5peditor-language-notice');
  const loadedLibs = [];
  const languages = {};
  ns.defaultLanguage = ns.contentLanguage;
  if (defaultLanguage) {
    ns.defaultLanguage = defaultLanguage;
  }

  /**
   * Toggle common fields group visibility
   */
  const toggleCommonFields = function () {
    const expandedValue = self.$common.parent().hasClass('collapsed')
      ? 'true' : 'false';
    self.$commonButton.attr('aria-expanded', expandedValue);
    self.$common.parent().toggleClass('collapsed');
  };

  /**
   * Create options DOM elements
   *
   * @private
   * @return {string}
   */
  const createOptions = function () {
    let options = '';
    for (let code in languages) {
      let label = ns.supportedLanguages[code] ? ns.supportedLanguages[code] : code.toLocaleUpperCase();
      options += '<option value="' + code + '"' + (code === ns.defaultLanguage ? ' selected' : '') + '>' + label + '</option>';
    }
    return options;
  };

  /**
   * Figure out if all loaded libraries supports the chosen language code
   *
   * @private
   * @param {string} code
   * @return {boolean}
   */
  const isSupportedByAll = function (code) {
    return (languages[code].length === loadedLibs.length);
  };

  /**
   * This function does something different than the other functions.
   *
   * @private
   * @param {string} lang Global value not used to avoid it changing while loading
   */
  const updateCommonFields = function (lang) {
    const libs = languages[lang];
    for (let lib in ns.libraryCache) {

      // Update common fields
      if (ns.renderableCommonFields[lib] && ns.renderableCommonFields[lib].fields) {
        for (let j = 0; j < ns.renderableCommonFields[lib].fields.length; j++) {
          const field = ns.renderableCommonFields[lib].fields[j];

          // Determine translation to use
          const translation = ns.libraryCache[lib].translation[lang];

          if (field.instance === undefined || translation === undefined) {
            continue; // Skip
          }

          // Find the correct translation for the field
          const fieldTranslation = findFieldDefaultTranslation(field.field, ns.libraryCache[lib].semantics, translation);

          // Extract the default values from the translation
          const defaultValue = getDefaultValue(fieldTranslation, field.field);

          // Update the widget
          field.instance.forceValue(defaultValue);
        }
      }

      if (ns.libraryCache[lib].translation[lang] !== undefined) {
        // Update semantics, so that the next time something is inserted it will get the same language
        ns.updateCommonFieldsDefault(ns.libraryCache[lib].semantics, ns.libraryCache[lib].translation[lang]);
      }
    }
  };

  /**
   * Recursivly search for the field's translations
   *
   * @private
   * @param {Object} field The field we're looking for
   * @param {Array} semantics The fields tree to search amongst
   * @param {Array} translation The translation tree to search and return from
   * @return {Object} The translation if found
   */
  const findFieldDefaultTranslation = function (field, semantics, translation) {
    for (let i = 0; i < semantics.length; i++) {
      if (semantics[i] === field) {
        return translation[i];
      }
      if (semantics[i].fields !== undefined && semantics[i].fields.length &&
          translation[i].fields !== undefined && translation[i].fields.length) {
        const found1 = findFieldDefaultTranslation(field, semantics[i].fields, translation[i].fields);
        if (found1 !== undefined) {
          return found1;
        }
      }
      if (semantics[i].field !== undefined && translation[i].field !== undefined) {
        const found2 = findFieldDefaultTranslation(field, [semantics[i].field], [translation[i].field]);
        if (found2 !== undefined) {
          return found2;
        }
      }
    }
  };

  /**
   * Recursivly format a default value for a field.
   *
   * @private
   * @param {Object} translation The translation field to extract the default values from
   * @param {Object} field Needed for field naming
   * @return {Object} The default value
   */
  const getDefaultValue = function (translation, field) {
    if (translation.default !== undefined) {
      return translation.default;
    }
    if (translation.fields !== undefined && translation.fields.length) {
      if (translation.fields.length === 1) {
        return getDefaultValue(translation.fields[0], field.fields[0]);
      }
      const values = {};
      for (let i = 0; i < translation.fields.length; i++) {
        values[field.fields[i].name] = getDefaultValue(translation.fields[i], field.fields[i]);
      }
      return values;
    }
    if (translation.field !== undefined) {
      return getDefaultValue(translation.field, field.field);
    }
  };

  /**
   * Prepares and loads all the missing translations from the server.
   *
   * @param {string} lang Global value not used to avoid it changing while loading
   * @param {function} done Callback
   */
  const loadTranslations = function (lang, done) {
    // Figure out what we actually need to load
    const loadLibs = [];
    for (let li in ns.libraryCache) {
      if (ns.libraryCache[li] === 0 || ns.libraryCache[li].translation[lang] === undefined) {
        loadLibs.push(li);
      }
    }

    if (loadLibs.length) {
      ns.$.post(
        ns.getAjaxUrl('translations', { language: lang }),
        { libraries: loadLibs },
        function (res) {
          for (let lib in res.data) {
            ns.libraryCache[lib].translation[lang] = JSON.parse(res.data[lib]).semantics;
          }
          done();
        }
      );
    }
    else {
      done(); // Continue without loading anything
    }
  }

  /**
   * Add new languages for content type.
   *
   * @param {string} lib uberName
   * @param {Array} langs
   */
  self.addLanguages = function (lib, langs) {
    // Update language counters
    for (let i = 0; i < langs.length; i++) {
      const code = langs[i];
      if (languages[code] === undefined) {
        languages[code] = [lib];
      }
      else {
        languages[code].push(lib);
      }
    }
    loadedLibs.push(lib);

    // Update
    $switcher.html(createOptions());
  };

  /**
   * Remove languages for content type.
   *
   * @param {string} lib uberName
   * @param {Array} langs
   */
  self.removeLanguages = function (lib, langs) {
    // Update language counters
    for (let i = 0; i < langs.length; i++) {
      const code = langs[i];
      if (languages[code] !== undefined) {
        if (languages[code].length === 1) {
          delete languages[code];
        }
        else {
          languages[code].splice(languages[code].indexOf(lib), 1);
        }
      }
    }
    loadedLibs.splice(loadedLibs.indexOf(lib), 1);

    // Update
    $switcher.html(createOptions());
  };

  // Handle switching language and loading new translations
  $switcher.change(function (e) {
    // Create confirmation dialog
    const confirmDialog = new H5P.ConfirmationDialog({
      headerText: ns.t('core', 'changeLanguage', {':language': (ns.supportedLanguages[this.value] ? ns.supportedLanguages[this.value] : this.value.toLocaleUpperCase())}),
      dialogText: ns.t('core', 'thisWillPotentially'),
    }).appendTo(document.body);
    confirmDialog.on('confirmed', function () {
      const lang = ns.defaultLanguage = $switcher.val();
      const humanLang = (ns.supportedLanguages[lang] ? ns.supportedLanguages[lang] : lang.toLocaleUpperCase());

      // Update chosen default language for main content and sub-content
      self.metadata.defaultLanguage = lang;
      self.params = self.setSubContentDefaultLanguage(self.params, lang);

      // Figure out if all libraries were supported
      if (!isSupportedByAll(lang)) {
        // Show a warning message
        $notice.children('.first').html(ns.t('core', 'notAllTextsChanged', {':language': humanLang}));
        $notice.children('.last').html(ns.t('core', 'contributeTranslations', {':language': humanLang, ':url': 'https://h5p.org/contributing#translating'}));
        $notice.addClass('show');
      }
      else {
        // Hide a warning message
        $notice.removeClass('show');
      }

      $switcher.prop('disabled', 'disabled');
      loadTranslations(lang, function () {
        // Do the actualy update of the field values
        updateCommonFields(lang);
        $switcher.prop('disabled', false);
      });
    });
    confirmDialog.on('canceled', function () {
      $switcher.val(ns.defaultLanguage);
    });
    // Show
    confirmDialog.show($switcher.offset().top);
  });

  // Add initial langauges for content type
  self.addLanguages(library, startLanguages);
};

/**
 * Recursively traverse params and sets default language for each sub-content
 *
 * @param {Object|Array} params Parameters
 * @param {string} lang Default language that will be set
 *
 * @return {Object|Array} Parameters with default language set for sub-content
 */
ns.Form.prototype.setSubContentDefaultLanguage = function (params, lang) {
  if (!params) {
    return params;
  }

  const self = this;

  if (Array.isArray(params)) {
    for (let i; i < params.length; i++) { 
      params[i] = self.setSubContentDefaultLanguage(params[i], lang);
    }
  }
  else if (typeof params === 'object') {
    if (params.metadata) {
      params.metadata.defaultLanguage = lang;
    }

    for (let parameter in params) {
      if (!params.hasOwnProperty(parameter)) {
        continue;
      }
      params[parameter] = this.setSubContentDefaultLanguage(
        params[parameter],
        lang
      );
    }
  }

  return params;
};

/**
 * Replace the given element with our form.
 *
 * @param {jQuery} $element
 * @returns {undefined}
 */
ns.Form.prototype.replace = function ($element) {
  $element.replaceWith(this.$form);
  this.offset = this.$form.offset();
  // Prevent inputs and selects in an h5peditor form from submitting the main
  // framework form.
  this.$form.on('keydown', 'input,select', function (event) {
    if (event.keyCode === 13) {
      // Prevent enter key from submitting form.
      return false;
    }
  });
};

/**
 * Remove the current form.
 */
ns.Form.prototype.remove = function () {
  ns.removeChildren(this.metadataForm.children);
  ns.removeChildren(this.children);
  ns.renderableCommonFields = {}; // Reset all common fields
  this.$form.remove();
};

/**
 * Wrapper for processing the semantics.
 *
 * @param {Array} semantics
 * @param {Object} defaultParams
 * @returns {undefined}
 */
ns.Form.prototype.processSemantics = function (semantics, defaultParams, metadata) {
  this.metadata = (metadata ? metadata : defaultParams.metadata || {});

  // Set language initially used
  if (!this.metadata.defaultLanguage) {
    this.metadata.defaultLanguage = ns.defaultLanguage;
  }

  if (ns.enableMetadata(this.currentLibrary)) {
    this.metadataForm = new ns.MetadataForm(this, this.metadata, this.$form.children('.tree'), true);
  }
  else {
    this.metadataForm = H5PEditor.MetadataForm.createLegacyForm(this.metadata, this.$form.children('.tree'));

    // This fixes CSS overrides done by some old custom editors
    switch (this.currentLibrary.split(' ')[0]) {
      case 'H5P.InteractiveVideo':
      case 'H5P.DragQuestion':
      case 'H5P.ImageHotspotQuestion':
        this.metadataForm.getExtraTitleField().$item.css('padding', '20px 20px 0 20px');
        break;

      case 'H5P.CoursePresentation':
        this.metadataForm.getExtraTitleField().$item.css('padding-bottom', '1em');
        break;
    }
  }

  // Overriding this.params with {} will lead to old content not being editable for now
  this.params = (defaultParams.params ? defaultParams.params : defaultParams);

  // Create real children
  ns.processSemanticsChunk(semantics, this.params, this.$form.children('.tree'), this);
};

/**
 * Collect functions to execute once the tree is complete.
 *
 * @param {function} ready
 * @returns {undefined}
 */
ns.Form.prototype.ready = function (ready) {
  this.readies.push(ready);
};
