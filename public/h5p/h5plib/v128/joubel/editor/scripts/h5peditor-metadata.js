H5PEditor.MetadataForm = (function (EventDispatcher, $, metadataSemantics) {

  /**
   * Create a metadata form popup that attaches to other fields
   *
   * @class
   * @param {Object} parent
   * @param {Object} params
   * @param {$} $container
   * @param {boolean} [hasExtraTitleField=true]
   * @param {boolean} [populateTitleField=false]
   */
  function MetadataForm(parent, params, $container, hasExtraTitleField, populateTitleField) {
    var self = this;

    // Initialize event inheritance
    EventDispatcher.call(self);

    // We're a parent, so we must handle readies callbacks
    self.passReadies = true;
    // (but in a special way since we process multiple semantics chunks)

    // Set current author as default in semantics
    const currentUserName = (H5PIntegration.user && H5PIntegration.user.name) ? H5PIntegration.user.name : undefined;
    if (currentUserName) {
      // Set current user as default for "changed by":
      findField('changes').field.fields[1].default = currentUserName;
      findField('authors').field.fields[0].default = currentUserName;
    }

    /**
     * Open the popup
     * @param {Object} event
     */
    const openPopup = function (event) {
      var offset = event.clientY - 150;

      $('html,body').css('height', '100%');
      $('.h5peditor').append($overlay);
      $wrapper.css('margin-top', (offset > 20 ? offset : 20) + 'px');

      // Focus title field
      titleField.$input.focus();
    };

    /**
     * Close the popup
     */
    const closePopup = function () {
      $('html,body').css('height', '');
      $overlay.detach();
    };

    /**
     * @private
     */
    const handleSaveButtonClick = function () {
      // If license selected, and there's no authors, add the current one
      if (params.license !== 'U' && params.authors.length === 0) {
        metadataAuthorWidget.addDefaultAuthor(currentUserName, 'Author');
      }

      closePopup();
    };

    /**
     * @private
     */
    const setupTitleField = function () {
      // Select title field text on click
      titleField.$input.click(function () {
        if (this.selectionStart === 0 && this.selectionEnd === this.value.length) {
          return;
        }
        this.select();
        this.setSelectionRange(0, this.value.length); // Safari mobile fix
      });

      // Set the default title
      if (!params.title && populateTitleField) {
        titleField.$input.val(H5PEditor.LibraryListCache.getDefaultTitle(parent.currentLibrary)).change();
      }

      titleField.$input.change(function () {
        self.trigger('titlechange');
      });
    };

    /**
     * @private
     */
    const setupLicenseField = function () {
      // Locate license and version selectors
      var licenseField = H5PEditor.findField('license', self);
      var versionField = H5PEditor.findField('licenseVersion', self);
      versionField.field.optional = true; // Avoid any error messages

      // Listen for changes to license
      licenseField.changes.push(function (value) {
        // Find versions for selected value
        function getNestedOptions(options) {
          var flattenedOptions = [];
          options.forEach(function (option) {
            if (option.type === 'optgroup') {
              flattenedOptions = flattenedOptions.concat(getNestedOptions(option.options));
            }
            else {
              flattenedOptions.push(option);
            }
          });
          return flattenedOptions;
        }

        var nestedOptions = getNestedOptions(licenseField.field.options);
        var option = find(nestedOptions, 'value', value);
        var versions = (option) ? option.versions : undefined;

        versionField.$select.prop('disabled', versions === undefined);
        if (versions === undefined) {
          // If no versions add default
          versions = [{
            value: '-',
            label: '-'
          }];
        }

        // Find default selected version
        var selected = (params.license === value && params ? params.licenseVersion : versions[0].value);

        // Update versions selector
        versionField.$select.html(H5PEditor.Select.createOptionsHtml(versions, selected)).change();
      });

      // Trigger update straight away
      licenseField.changes[licenseField.changes.length - 1](params.license);
    };

    /**
     * Toggle visibility of accessibility title field
     */
    const toggleA11yTitle = function () {
      const a11yTitleField = H5PEditor.findField('a11yTitle', self);
      const wrapper = a11yTitleField.$item[0];
      self.isA11yExpanded = !self.isA11yExpanded;
      if (self.isA11yExpanded) {
        wrapper.classList.remove('hidden');
      }
      wrapper.classList[self.isA11yExpanded ? 'remove' : 'add']('hide');
      self.a11yTitleText.innerHTML = self.isA11yExpanded
        ? t('a11yTitleHideLabel') : t('a11yTitleShowLabel');
      wrapper.setAttribute('aria-hidden', !self.isA11yExpanded);
    }

    /**
     * Setup functionality of accessibility title field and button
     */
    const setupA11yTitleField = function () {
      const titleField = H5PEditor.findField('title', self);
      const a11yTitleField = H5PEditor.findField('a11yTitle', self);
      const label = titleField.$item[0].querySelector('label.h5peditor-label-wrapper');
      const toggleA11yTitleButton = document.createElement('button');
      const a11yTitleText = document.createElement('span');
      a11yTitleText.classList.add('h5p-a11y-title-text');
      a11yTitleText.innerHTML = t('a11yTitleShowLabel');
      self.a11yTitleText = a11yTitleText;

      toggleA11yTitleButton.classList.add('a11y-title-toggle');
      toggleA11yTitleButton.appendChild(a11yTitleText);

      a11yTitleField.$item[0].classList.add('hide');
      a11yTitleField.$item[0].setAttribute('aria-hidden', true);
      a11yTitleField.$item[0].addEventListener('transitionend', function () {
        // Hide when transition is done
        if (!self.isA11yExpanded) {
          a11yTitleField.$item[0].classList.add('hidden');
        }
      });

      toggleA11yTitleButton.addEventListener('click', toggleA11yTitle);
      self.togglea11yTitleButton = toggleA11yTitleButton;
      self.isA11yExpanded = false;

      label.appendChild(toggleA11yTitleButton);
    }

    /**
     * @private
     */
    const setupSourceField = function () {
      // Make sure the source field is empty or starts with a protocol
      const sourceField = H5PEditor.findField('source', self);
      sourceField.$item.on('change', function () {
        const sourceInput = $(this).find('input.h5peditor-text');
        if (sourceInput.val().trim() !== '' &&
          sourceInput.val().indexOf('https://') !== 0 &&
          sourceInput.val().indexOf('http://') !== 0
        ) {
          sourceInput.val('http://' + sourceInput.val()).trigger('change');
        }
      });
    };

    const $overlay = $('<div>', {
      'class': 'overlay h5p-metadata-popup-overlay'
    });

    const $wrapper = $(
      '<div class="h5p-metadata-wrapper">' +
        '<div class="h5p-metadata-header">' +
          '<div class="h5p-title-container">' +
            '<h2>' + t('metadataSharingAndLicensingInfo') + '</h2>' +
            '<p>' + t('fillInTheFieldsBelow') + '</p>' +
          '</div>' +
          '<div class="metadata-button-wrapper">' +
            '<button href="#" class="h5p-metadata-button h5p-save">' + t('saveMetadata') + '</button>' +
          '</div>' +
        '</div>' +
      '</div>');

    // Handle click on save button
    $wrapper.find('.h5p-save').click(handleSaveButtonClick);

    const $fieldsWrapper = $('<div/>', {
      'class': 'h5p-metadata-fields-wrapper',
      appendTo: $wrapper
    });

    $wrapper.appendTo($overlay);

    const $button = $(
      '<div role="button" tabindex="0" class="h5p-metadata-button-wrapper">' +
        '<div class="h5p-metadata-button-tip"></div>' +
        '<div class="h5p-metadata-toggler">' + t('metadata') + '</div>' +
      '</div>')
      .click(openPopup)
      .keydown(function (event) {
        if (event.which == 13 || event.which == 32) {
          openPopup(event);
        }
      });

    /**
     * Handle ready callbacks from children
     * @param {function} callback
     */
    self.ready = function (callback) {
      if (parent.passReadies) {
        parent.ready(callback); // Pass to parent, run when all editor fields are ready
      }
      else {
        readies.push(callback); // Run by processSemanticsChunk when all fields are done
      }
    };

    /**
     * @param {$} $container
     */
    self.appendTo = function ($container) {
      $wrapper.appendTo($container);
    };

    /**
     * @param {$} $element
     */
    self.appendButtonTo = function ($item) {
      $button.appendTo($item.children('.h5peditor-label-wrapper').wrap('<div class="h5p-editor-flex-wrapper"/>').parent());
    };

    /**
     * @return {Object} The extra title field instance
     */
    self.getExtraTitleField = function () {
      return hasExtraTitleField ? extraTitle : undefined;
    };

    // Prepare semantics
    const semantics = [];
    if (hasExtraTitleField) {
      semantics.push(getExtraTitleFieldSemantics());
    }
    semantics.push(findField('title'));
    semantics.push(findField('a11yTitle'));
    semantics.push(findField('license'));
    semantics.push(findField('licenseVersion'));
    semantics.push(findField('yearFrom'));
    semantics.push(findField('yearTo'));
    semantics.push(findField('source'));

    // Collect readies callbacks
    const readies = [];

    // Generate the form
    H5PEditor.processSemanticsChunk(semantics, params, $fieldsWrapper, self);

    // Keep track of children between generating
    let children = self.children;

    // Extra processing of fields
    const titleField = H5PEditor.findField('title', self);
    setupTitleField();
    setupLicenseField();
    setupSourceField();
    setupA11yTitleField();

    // Append the metadata author list widget (Not the same type of widgets as the rest of editor fields)
    const metadataAuthorWidget = H5PEditor.metadataAuthorWidget(findField('authors').field.fields, params, $fieldsWrapper, self);
    children = children.concat(self.children);

    // TODO: Ideally these widgets should behave the same way as the reset of
    // the editor widgets and be created through a single call to processSemanticsChunk().

    // Append the License Extras field
    H5PEditor.processSemanticsChunk([findField('licenseExtras')], params, $fieldsWrapper, self);
    children = children.concat(self.children);

    // Append the metadata changelog widget (Not the same type of widgets as the rest of editor fields)
    H5PEditor.metadataChangelogWidget([findField('changes').field], params, $fieldsWrapper, self);
    children = children.concat(self.children);

    // Append the Additional information group
    var additionals = new H5PEditor.widgets.group(self, {
      name: 'additionals',
      label: 'Additional information', // TODO: l10n ?
      fields: [
        findField('authorComments')
      ]
    }, params.authorComments, function (field, value) {
      params.authorComments = value;
    });
    additionals.appendTo($fieldsWrapper);

    // Add the final child
    self.children = children.concat([additionals]);

    let extraTitle;
    if (hasExtraTitleField) {
      // Append to correct place in DOM
      extraTitle = H5PEditor.findField('extraTitle', self);
      extraTitle.$item.appendTo($container);
      self.appendButtonTo(extraTitle.$item);

      linkFields(titleField, extraTitle);
    }

    if (!parent.passReadies) {
      // Run readies callbacks
      for (let i = 0; i < readies.length; i++) {
        readies[i]();
      }
    }
  }

  // Extends the event dispatcher
  MetadataForm.prototype = Object.create(EventDispatcher.prototype);
  MetadataForm.prototype.constructor = MetadataForm;

  MetadataForm.createLegacyForm = function (params, $container) {
    const legacyForm = {
      passReadies: false,
      getExtraTitleField: function () {
        return H5PEditor.findField('title', legacyForm);
      }
    };

    // Generate the form
    const field = getExtraTitleFieldSemantics();
    field.name = 'title';
    H5PEditor.processSemanticsChunk([field], params, $container, legacyForm);

    return legacyForm;
  };

  /**
   * @return {Object}
   */
  const getExtraTitleFieldSemantics = function () {
    const extraTitle = JSON.parse(JSON.stringify(findField('title'))); // Clone
    extraTitle.name = 'extraTitle'; // Change name to avoid conflicts
    extraTitle.description = t('usedForSearchingReportsAndCopyrightInformation');
    delete extraTitle.placeholder;
    return extraTitle;
  };

  /**
   * @param {string} key
   * @return {string}
   */
  const t = function (key) {
    return H5PEditor.t('core', key);
  };

  /**
   * Find metdata semantics field.
   * @param {string} name
   * @return {Object}
   */
  const findField = function (name) {
    for (let i = 0; i < metadataSemantics.length; i++) {
      if (metadataSemantics[i].name === name) {
        return metadataSemantics[i];
      }
    }
  };

  /**
   * Help find object in list with the given property value.
   *
   * @param {Object[]} list of objects to search through
   * @param {string} property to look for
   * @param {string} value to match property value against
   * @return {Object}
   */
  const find = function (list, property, value) {
    var properties = property.split('.');

    for (var i = 0; i < list.length; i++) {
      var objProp = list[i];

      for (var j = 0; j < properties.length; j++) {
        objProp = objProp[properties[j]];
      }

      if (objProp === value) {
        return list[i];
      }
    }
  };

  /**
   * Automatically sync all the given fields when one value changes.
   * Note: Currently only supports H5PEditor.Text field widgets.
   *
   * @private
   * @param {...*} var_args
   */
  const linkFields = function (var_args) {
    const fields = arguments;

    let preventLoop;

    /**
     * Change event handler for all fields
     * @private
     * @param {*} value
     */
    const updateAllFields = function (value) {
      if (preventLoop || value === undefined) {
        return;
      }

      // Do not run updates for this update
      preventLoop = true;

      // Apply value to all fields
      for (let i = 0; i < fields.length; i++) {
        fields[i].$input.val(value).change();
      }

      // Done
      preventLoop = false;
    };

    // Add change event listeners
    for (let i = 0; i < fields.length; i++) {
      fields[i].change(updateAllFields);
    }

    // Use initial value from first field
    if (fields[0].value !== undefined) {
      const escaper = document.createElement('div');
      escaper.innerHTML = fields[0].value;
      updateAllFields(escaper.innerText);
    }
  };

  return MetadataForm;
})(H5P.EventDispatcher, H5P.jQuery, H5PEditor.metadataSemantics);
