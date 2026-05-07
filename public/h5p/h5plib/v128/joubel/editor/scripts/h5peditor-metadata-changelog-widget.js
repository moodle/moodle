H5PEditor.metadataChangelogWidget = function (semantics, params, $wrapper, parent) {
  if (!params.changes) {
    params.changes = [];
  }

  var $ = H5PEditor.$;

  // State
  var state = {
    editing: false,
    newLog: false,
    currentLog: undefined
  };

  var widget = $('<div class="field h5p-metadata-changelog"></div>');
  H5PEditor.processSemanticsChunk(semantics, {}, widget, parent);

  // Get a reference to the fields:
  var changeField = H5PEditor.findField('change', parent);
  var dateField = H5PEditor.findField('date', changeField);
  var authorField = H5PEditor.findField('author', changeField);
  var logField = H5PEditor.findField('log', changeField);

  var $form = changeField.$content;
  var $formFields = dateField.$item.add(authorField.$item).add(logField.$item);

  // Add description
  var $description = $('<div>', {
    'class': 'h5peditor-field-description',
    'text': H5PEditor.t('core', 'changelogDescription')
  });
  $form.append($description);

  var $cancelButton = $('<button>', {
    'class': 'h5p-metadata-button h5p-cancel',
    type: 'button',
    text: H5PEditor.t('core', 'cancel'),
    click: function () {
      resetForm();
      state.editing = false;
      state.currentLog = undefined;
      render();
    }
  });

  var $createLogButton = $('<button>', {
    'class': 'h5p-metadata-button inverted h5p-log-change',
    type: 'button',
    text: H5PEditor.t('core', 'logThisChange'),
    click: function () {
      var entry = validateForm(false);

      if (!entry.date || !entry.author || !entry.log) {
        return;
      }

      if (state.currentLog !== undefined) {
        params.changes[state.currentLog] = entry;
      }
      else {
        params.changes.push(entry);
        state.newLog = true;
      }

      state.editing = false;
      resetForm();
      render();
      state.currentLog = undefined;
    }
  });

  var $addLogButton = $('<button>', {
    'class': 'h5p-metadata-button inverted h5p-add-author',
    type: 'button',
    text: H5PEditor.t('core', 'addNewChange'),
    click: function () {
      state.editing = true;
      state.newLog = false;
      resetForm();
      render();
    }
  });

  var $buttons = $('<div class="h5p-metadata-changelog-buttons"></div>');
  $buttons.append($cancelButton);
  $buttons.append($createLogButton);
  $buttons.append($addLogButton);
  $form.append($buttons);

  var $newLogMessage = $('<div', {
    'class': 'h5p-metadata-new-log-message',
    text: H5PEditor.t('core', 'newChangeHasBeenLogged'),
    appendTo: $form
  });

  var $logWrapper = $('<div>', {
    'class': "h5p-metadata-logged-changes",
    appendTo: $form
  });

  widget.appendTo($wrapper);
  render();

  function resetForm() {
    dateField.$input.val('');
    logField.$input.val('');
    validateForm(true);
  }

  function render() {
    $newLogMessage.toggle(state.newLog);
    $description.toggle(!state.editing);
    $addLogButton.toggle(!state.editing);
    $cancelButton.toggle(state.editing);
    $createLogButton.toggle(state.editing);
    $formFields.toggle(state.editing);

    if (state.editing) {
      populateForm();
      if (!dateField.$input.hasClass('datepicker')) {
        dateField.$input.addClass('datepicker');
        setupDatePicker(dateField.$input);
      }
    }
    else {
      renderLogWrapper();
    }

    $logWrapper.toggleClass('editing', state.editing);
  }

  function renderLogWrapper() {
    $logWrapper.empty();
    $logWrapper.append('<span class="h5peditor-label h5p-metadata-log-wrapper-title">'+ H5PEditor.t('core', 'loggedChanges')  + '</span>');

    if (params.changes.length == 0) {
      $logWrapper.append($('<div class="h5peditor-field-description">' + H5PEditor.t('core', 'noChangesHaveBeenLogged') + '</div>'));
    }
    else {
      var logList = $('<div class="h5p-metadata-log-wrapper"></div>');
      $logWrapper.append(logList);

      for (var i = 0; i < params.changes.length; i++) {
        var log = params.changes[i];

        var dateWrapper = $('<div>', {
          'class': 'h5p-metadata-log-date',
          html: H5PEditor.htmlspecialchars(log.date)
        });

        var logDescription = $('<div>', {
          'class': 'h5p-metadata-log-description',
          html: H5PEditor.htmlspecialchars(log.log)
        });

        var authorWrapper = $('<div>', {
          'class': 'h5p-metadata-log-author',
          html: 'by ' + H5PEditor.htmlspecialchars(log.author) // TODO - translate
        });

        var $descriptionWrapper = $('<div class="h5p-metadata-description-wrapper"></div>');
        $descriptionWrapper.append(logDescription);
        $descriptionWrapper.append(authorWrapper);

        var logButtons = $('<div class="h5p-metadata-log-buttons">' +
         '<button type="button" class="h5p-metadata-edit h5p-metadata-icon-button"></button>' +
         '<button type="button" class="h5p-metadata-delete h5p-metadata-icon-button"></button>' +
        '</div>');

        logButtons.find('.h5p-metadata-delete').click(function () {
          // Ask for confirmation
          if (confirm(H5PEditor.t('core', 'confirmDeleteChangeLog'))) {
            var wrapper = $(this).closest('.h5p-metadata-log');
            var index = $(wrapper).data('index');
            deleteLog(index);
          }
        });

        logButtons.find('.h5p-metadata-edit').click(function () {
          var wrapper = $(this).closest('.h5p-metadata-log');
          var index = $(wrapper).data('index');

          editLog(index);
        });

        var logContent = $('<div>', {
          'class': 'h5p-metadata-log',
          'data-index': i
        });
        logContent.append(dateWrapper);
        logContent.append($descriptionWrapper);
        logContent.append(logButtons);

        logList.prepend(logContent);
      }
    }
  }

  function editLog(index) {
    state.editing = true;
    state.currentLog = index;
    state.newLog = false;
    render();
  }

  function deleteLog(index) {
    params.changes.splice(index, 1);
    render();
  }

  function validateForm(optional) {
    dateField.field.optional = optional;
    authorField.field.optional = optional;
    logField.field.optional = optional;

    return {
      date: dateField.validate(),
      author: authorField.validate(),
      log: logField.validate()
    };
  }

  function populateForm() {
    if (state.currentLog !== undefined) {
      validateForm(true);

      var log = params.changes[state.currentLog];
      dateField.$input.val(log.date);

      // Unescape in case it comes from backend
      var unescaper = document.createElement('div');

      unescaper.innerHTML = H5PEditor.htmlspecialchars(log.author);
      authorField.$input.val(unescaper.textContent);

      unescaper.innerHTML = H5PEditor.htmlspecialchars(log.log);
      logField.$input.val(unescaper.textContent);
    }
  }

  /**
   * Setup the datepicker. Loads the script if not already loaded
   */
  function setupDatePicker($dateInput) {
    var initDateField = function () {
      $dateInput.Zebra_DatePicker({
        format: 'd-m-y G:i:s',
        onClose: function () {
          dateField.validate();
        }
      });
    };

    // Make sure datepicker.js is only loaded once
    $dateInput.Zebra_DatePicker ? initDateField() : loadDatePickerLib(initDateField);
  }

  /**
   * Load the datepicker JS lib
   *
   * @param {Function} callback
   */
  function loadDatePickerLib(callback) {
    $.ajax({
      url: H5PEditor.basePath + 'libs/zebra_datepicker.min.js',
      dataType: 'script',
      success: callback,
      error: function (r,e) {
        console.warn('error loading libraries: ', e);
      },
      async: true
    });
  }
};
