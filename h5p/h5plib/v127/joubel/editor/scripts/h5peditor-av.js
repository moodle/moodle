/* global ns */
/**
 * Audio/Video module.
 * Makes it possible to add audio or video through file uploads and urls.
 *
 */
H5PEditor.widgets.video = H5PEditor.widgets.audio = H5PEditor.AV = (function ($) {

  /**
   * Constructor.
   *
   * @param {mixed} parent
   * @param {object} field
   * @param {mixed} params
   * @param {function} setValue
   * @returns {_L3.C}
   */
  function C(parent, field, params, setValue) {
    var self = this;

    // Initialize inheritance
    H5PEditor.FileUploader.call(self, field);

    this.parent = parent;
    this.field = field;
    this.params = params;
    this.setValue = setValue;
    this.changes = [];

    if (params !== undefined && params[0] !== undefined) {
      this.setCopyright(params[0].copyright);
    }

    // When uploading starts
    self.on('upload', function () {
      // Insert throbber
      self.$uploading = $('<div class="h5peditor-uploading h5p-throbber">' + H5PEditor.t('core', 'uploading') + '</div>').insertAfter(self.$add.hide());

      // Clear old error messages
      self.$errors.html('');

      // Close dialog
      self.closeDialog();
    });

    // Monitor upload progress
    self.on('uploadProgress', function (e) {
      self.$uploading.html(H5PEditor.t('core', 'uploading') + ' ' + Math.round(e.data * 100) + ' %');
    });

    // Handle upload complete
    self.on('uploadComplete', function (event) {
      var result = event.data;

      // Clear out add dialog
      this.$addDialog.find('.h5p-file-url').val('');

      try {
        if (result.error) {
          throw result.error;
        }

        // Set params if none is set
        if (self.params === undefined) {
          self.params = [];
          self.setValue(self.field, self.params);
        }

        // Add a new file/source
        var file = {
          path: result.data.path,
          mime: result.data.mime,
          copyright: self.copyright
        };
        var index = (self.updateIndex !== undefined ? self.updateIndex : self.params.length);
        self.params[index] = file;
        self.addFile(index);

        // Trigger change callbacks (old event system)
        for (var i = 0; i < self.changes.length; i++) {
          self.changes[i](file);
        }
      }
      catch (error) {
        // Display errors
        self.$errors.append(H5PEditor.createError(error));
      }

      if (self.$uploading !== undefined && self.$uploading.length !== 0) {
        // Hide throbber and show add button
        self.$uploading.remove();
        self.$add.show();
      }
    });
  }

  C.prototype = Object.create(ns.FileUploader.prototype);
  C.prototype.constructor = C;

  /**
   * Append widget to given wrapper.
   *
   * @param {jQuery} $wrapper
   */
  C.prototype.appendTo = function ($wrapper) {
    var self = this;
    const id = ns.getNextFieldId(this.field);

    var imageHtml =
      '<ul class="file list-unstyled"></ul>' +
      (self.field.widgetExtensions ? C.createTabbedAdd(self.field.type, self.field.widgetExtensions, id, self.field.description !== undefined) : C.createAdd(self.field.type, id, self.field.description !== undefined))

    if (!this.field.disableCopyright) {
      imageHtml += '<a class="h5p-copyright-button" href="#">' + H5PEditor.t('core', 'editCopyright') + '</a>';
    }

    imageHtml += '<div class="h5p-editor-dialog">' +
      '<a href="#" class="h5p-close" title="' + H5PEditor.t('core', 'close') + '"></a>' +
      '</div>';

    var html = H5PEditor.createFieldMarkup(this.field, imageHtml, id);
    var $container = $(html).appendTo($wrapper);

    this.$files = $container.children('.file');
    this.$add = $container.children('.h5p-add-file').click(function () {
      self.$addDialog.addClass('h5p-open');
    });

    // Tabs that are hard-coded into this widget. Any other tab must be an extension.
    const TABS = {
      UPLOAD: 0,
      INPUT: 1
    };

    // The current active tab
    let activeTab = TABS.UPLOAD;

    /**
     * @param {number} tab
     * @return {boolean}
     */
    const isExtension = function (tab) {
      return tab > TABS.INPUT; // Always last tab
    };

    /**
     * Toggle the currently active tab.
     */
    const toggleTab = function () {
      // Pause the last active tab
      if (isExtension(activeTab)) {
        tabInstances[activeTab].pause();
      }

      // Update tab
      this.parentElement.querySelector('.selected').classList.remove('selected');
      this.classList.add('selected');

      // Update tab panel
      const el = document.getElementById(this.getAttribute('aria-controls'));
      el.parentElement.querySelector('.av-tabpanel:not([hidden])').setAttribute('hidden', '');
      el.removeAttribute('hidden');

      // Set active tab index
      for (let i = 0; i < el.parentElement.children.length; i++) {
        if (el.parentElement.children[i] === el) {
          activeTab = i - 1; // Compensate for .av-tablist in the same wrapper
          break;
        }
      }

      // Toggle insert button disabled
      if (activeTab === TABS.UPLOAD) {
        self.$insertButton[0].disabled = true;
      }
      else if (activeTab === TABS.INPUT) {
        self.$insertButton[0].disabled = false;
      }
      else {
        self.$insertButton[0].disabled = !tabInstances[activeTab].hasMedia();
      }
    }

    /**
     * Switch focus between the buttons in the tablist
     */
    const moveFocus = function (el) {
      if (el) {
        this.setAttribute('tabindex', '-1');
        el.setAttribute('tabindex', '0');
        el.focus();
      }
    }

    // Register event listeners to tab DOM elements
    $container.find('.av-tab').click(toggleTab).keydown(function (e) {
      if (e.which === 13 || e.which === 32) { // Enter or Space
        toggleTab.call(this, e);
        e.preventDefault();
      }
      else if (e.which === 37 || e.which === 38) { // Left or Up
        moveFocus.call(this, this.previousSibling);
        e.preventDefault();
      }
      else if (e.which === 39 || e.which === 40) { // Right or Down
        moveFocus.call(this, this.nextSibling);
        e.preventDefault();
      }
    });

    this.$addDialog = this.$add.next().children().first();

    // Prepare to add the extra tab instances
    const tabInstances = [null, null]; // Add nulls for hard-coded tabs
    self.tabInstances = tabInstances;

    if (self.field.widgetExtensions) {

      /**
       * @param {string} type Constructor name scoped inside this widget
       * @param {number} index
       */
      const createTabInstance = function (type, index) {
        const tabInstance = new H5PEditor.AV[type]();
        tabInstance.appendTo(self.$addDialog[0].children[0].children[index + 1]); // Compensate for .av-tablist in the same wrapper
        tabInstance.on('hasMedia', function (e) {
          if (index === activeTab) {
            self.$insertButton[0].disabled = !e.data;
          }
        });
        tabInstances.push(tabInstance);
      }

      // Append extra tabs
      for (let i = 0; i < self.field.widgetExtensions.length; i++) {
        if (H5PEditor.AV[self.field.widgetExtensions[i]]) {
          createTabInstance(self.field.widgetExtensions[i], i + 2); // Compensate for the number of hard-coded tabs
        }
      }
    }

    var $url = this.$url = this.$addDialog.find('.h5p-file-url');
    this.$addDialog.find('.h5p-cancel').click(function () {
      self.updateIndex = undefined;
      self.closeDialog();
    });

    this.$addDialog.find('.h5p-file-drop-upload')
      .addClass('has-advanced-upload')
      .on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
      })
      .on('dragover dragenter', function (e) {
        $(this).addClass('over');
        e.originalEvent.dataTransfer.dropEffect = 'copy';
      })
      .on('dragleave', function () {
        $(this).removeClass('over');
      })
      .on('drop', function (e) {
        self.uploadFiles(e.originalEvent.dataTransfer.files);
      })
      .click(function () {
        self.openFileSelector();
      });

    this.$insertButton = this.$addDialog.find('.h5p-insert').click(function () {
      if (isExtension(activeTab)) {
        const media = tabInstances[activeTab].getMedia();
        if (media) {
          self.upload(media.data, media.name);
        }
      }
      else {
        const url = $url.val().trim();
        if (url) {
          self.useUrl(url);
        }
      }

      self.closeDialog();
    });

    this.$errors = $container.children('.h5p-errors');

    if (this.params !== undefined) {
      for (var i = 0; i < this.params.length; i++) {
        this.addFile(i);
      }
    }
    else {
      $container.find('.h5p-copyright-button').addClass('hidden');
    }

    var $dialog = $container.find('.h5p-editor-dialog');
    $container.find('.h5p-copyright-button').add($dialog.find('.h5p-close')).click(function () {
      $dialog.toggleClass('h5p-open');
      return false;
    });

    ns.File.addCopyright(self, $dialog, function (field, value) {
      self.setCopyright(value);
    });

  };

  /**
   * Add file icon with actions.
   *
   * @param {Number} index
   */
  C.prototype.addFile = function (index) {
    var that = this;
    var fileHtml;
    var file = this.params[index];
    var rowInputId = 'h5p-av-' + C.getNextId();
    var defaultQualityName = H5PEditor.t('core', 'videoQualityDefaultLabel', { ':index': index + 1 });
    var qualityName = (file.metadata && file.metadata.qualityName) ? file.metadata.qualityName : defaultQualityName;

    // Check if source is provider (Vimeo, YouTube, Panopto)
    const isProvider = file.path && C.findProvider(file.path);

    // Only allow single source if YouTube
    if (isProvider) {
      // Remove all other files except this one
      that.$files.children().each(function (i) {
        if (i !== that.updateIndex) {
          that.removeFileWithElement($(this));
        }
      });
      // Remove old element if updating
      that.$files.children().each(function () {
        $(this).remove();
      });
      // This is now the first and only file
      index = 0;
    }
    this.$add.toggleClass('hidden', isProvider);

    // If updating remove and recreate element
    if (that.updateIndex !== undefined) {
      var $oldFile = this.$files.children(':eq(' + index + ')');
      $oldFile.remove();
      this.updateIndex = undefined;
    }

    // Create file with customizable quality if enabled and not youtube
    if (this.field.enableCustomQualityLabel === true && !isProvider) {
      fileHtml = '<li class="h5p-av-row">' +
        '<div class="h5p-thumbnail">' +
          '<div class="h5p-type" title="' + file.mime + '">' + file.mime.split('/')[1] + '</div>' +
            '<div role="button" tabindex="0" class="h5p-remove" title="' + H5PEditor.t('core', 'removeFile') + '">' +
          '</div>' +
        '</div>' +
        '<div class="h5p-video-quality">' +
          '<div class="h5p-video-quality-title">' + H5PEditor.t('core', 'videoQuality') + '</div>' +
          '<label class="h5peditor-field-description" for="' + rowInputId + '">' + H5PEditor.t('core', 'videoQualityDescription') + '</label>' +
          '<input id="' + rowInputId + '" class="h5peditor-text" type="text" maxlength="60" value="' + qualityName + '">' +
        '</div>' +
      '</li>';
    }
    else {
      fileHtml = '<li class="h5p-av-cell">' +
        '<div class="h5p-thumbnail">' +
          '<div class="h5p-type" title="' + file.mime + '">' + file.mime.split('/')[1] + '</div>' +
          '<div role="button" tabindex="0" class="h5p-remove" title="' + H5PEditor.t('core', 'removeFile') + '">' +
        '</div>' +
      '</li>';
    }

    // Insert file element in appropriate order
    var $file = $(fileHtml);
    if (index >= that.$files.children().length) {
      $file.appendTo(that.$files);
    }
    else {
      $file.insertBefore(that.$files.children().eq(index));
    }

    this.$add.parent().find('.h5p-copyright-button').removeClass('hidden');

    // Handle thumbnail click
    $file
      .children('.h5p-thumbnail')
      .click(function () {
        if (!that.$add.is(':visible')) {
          return; // Do not allow editing of file while uploading
        }
        that.$addDialog.addClass('h5p-open').find('.h5p-file-url').val(that.params[index].path);
        that.updateIndex = index;
      });

    // Handle remove button click
    $file
      .find('.h5p-remove')
      .click(function () {
        if (that.$add.is(':visible')) {
          confirmRemovalDialog.show($file.offset().top);
        }

        return false;
      });

    // on input update
    $file
      .find('input')
      .change(function () {
        file.metadata = { qualityName: $(this).val() };
      });

    // Create remove file dialog
    var confirmRemovalDialog = new H5P.ConfirmationDialog({
      headerText: H5PEditor.t('core', 'removeFile'),
      dialogText: H5PEditor.t('core', 'confirmRemoval', {':type': 'file'})
    }).appendTo(document.body);

    // Remove file on confirmation
    confirmRemovalDialog.on('confirmed', function () {
      that.removeFileWithElement($file);
      if (that.$files.children().length === 0) {
        that.$add.parent().find('.h5p-copyright-button').addClass('hidden');
      }
    });
  };

  /**
   * Remove file at index
   *
   * @param {number} $file File element
   */
  C.prototype.removeFileWithElement = function ($file) {
    var index = $file.index();

    // Remove from params.
    if (this.params.length === 1) {
      delete this.params;
      this.setValue(this.field);
    }
    else {
      this.params.splice(index, 1);
    }

    $file.remove();
    this.$add.removeClass('hidden');

    // Notify change listeners
    for (var i = 0; i < this.changes.length; i++) {
      this.changes[i]();
    }
  };

  C.prototype.useUrl = function (url) {
    if (this.params === undefined) {
      this.params = [];
      this.setValue(this.field, this.params);
    }

    var mime;
    var aspectRatio;
    var i;
    var matches = url.match(/\.(webm|mp4|ogv|m4a|mp3|ogg|oga|wav)/i);
    if (matches !== null) {
      mime = matches[matches.length - 1];
    }
    else {
      // Try to find a provider
      const provider = C.findProvider(url);
      if (provider) {
        mime = provider.name;
        aspectRatio = provider.aspectRatio;
      }
    }

    var file = {
      path: url,
      mime: this.field.type + '/' + (mime ? mime : 'unknown'),
      copyright: this.copyright,
      aspectRatio: aspectRatio ? aspectRatio : undefined,
    };
    var index = (this.updateIndex !== undefined ? this.updateIndex : this.params.length);
    this.params[index] = file;
    this.addFile(index);

    for (i = 0; i < this.changes.length; i++) {
      this.changes[i](file);
    }
  };

  /**
   * Validate the field/widget.
   *
   * @returns {Boolean}
   */
  C.prototype.validate = function () {
    return true;
  };

  /**
   * Remove this field/widget.
   */
  C.prototype.remove = function () {
    this.$errors.parent().remove();
  };

  /**
   * Sync copyright between all video files.
   *
   * @returns {undefined}
   */
  C.prototype.setCopyright = function (value) {
    this.copyright = value;
    if (this.params !== undefined) {
      for (var i = 0; i < this.params.length; i++) {
        this.params[i].copyright = value;
      }
    }
  };

  /**
   * Collect functions to execute once the tree is complete.
   *
   * @param {function} ready
   * @returns {undefined}
   */
  C.prototype.ready = function (ready) {
    if (this.passReadies) {
      this.parent.ready(ready);
    }
    else {
      ready();
    }
  };

  /**
   * Close the add media dialog
   */
  C.prototype.closeDialog = function () {
    this.$addDialog.removeClass('h5p-open');

    // Reset URL input
    this.$url.val('');

    // Reset all of the tabs
    for (let i = 0; i < this.tabInstances.length; i++) {
      if (this.tabInstances[i]) {
        this.tabInstances[i].reset();
      }
    }
  };

  /**
   * Create the HTML for the dialog itself.
   *
   * @param {string} content HTML
   * @param {boolean} disableInsert
   * @param {string} id
   * @param {boolean} hasDescription
   * @returns {string} HTML
   */
  C.createInsertDialog = function (content, disableInsert, id, hasDescription) {
    return '<div role="button" tabindex="0" id="' + id + '"' + (hasDescription ? ' aria-describedby="' + ns.getDescriptionId(id) + '"' : '') + ' class="h5p-add-file" title="' + H5PEditor.t('core', 'addFile') + '"></div>' +
      '<div class="h5p-dialog-anchor"><div class="h5p-add-dialog">' +
        '<div class="h5p-add-dialog-table">' + content + '</div>' +
        '<div class="h5p-buttons">' +
          '<button class="h5peditor-button-textual h5p-insert"' + (disableInsert ? ' disabled' : '') + '>' + H5PEditor.t('core', 'insert') + '</button>' +
          '<button class="h5peditor-button-textual h5p-cancel">' + H5PEditor.t('core', 'cancel') + '</button>' +
        '</div>' +
      '</div></div>';
  };

  /**
   * Creates the HTML needed for the given tab.
   *
   * @param {string} tab Tab Identifier
   * @param {string} type 'video' or 'audio'
   * @returns {string} HTML
   */
  C.createTabContent = function (tab, type) {
    const isAudio = (type === 'audio');

    switch (tab) {
      case 'BasicFileUpload':
        const id = 'av-upload-' + C.getNextId();
        return '<h3 id="' + id + '">' + H5PEditor.t('core', isAudio ? 'uploadAudioTitle' : 'uploadVideoTitle') + '</h3>' +
          '<div class="h5p-file-drop-upload" tabindex="0" role="button" aria-labelledby="' + id + '">' +
            '<div class="h5p-file-drop-upload-inner ' + type + '"></div>' +
          '</div>';

      case 'InputLinkURL':
        return '<h3>' + H5PEditor.t('core', isAudio ? 'enterAudioTitle' : 'enterVideoTitle') + '</h3>' +
          '<div class="h5p-file-url-wrapper ' + type + '">' +
            '<input type="text" placeholder="' + H5PEditor.t('core', isAudio ? 'enterAudioUrl' : 'enterVideoUrl') + '" class="h5p-file-url h5peditor-text"/>' +
          '</div>' +
          (isAudio ? '' : '<div class="h5p-errors"></div><div class="h5peditor-field-description">' + H5PEditor.t('core', 'addVideoDescription') + '</div>');

      default:
        return '';
    }
  };

  /**
   * Creates the HTML for the tabbed insert media dialog. Only used when there
   * are extra tabs.
   *
   * @param {string} type 'video' or 'audio'
   * @param {Array} extraTabs
   * @returns {string} HTML
   */
  C.createTabbedAdd = function (type, extraTabs, id, hasDescription) {
    let i;

    const tabs = [
      'BasicFileUpload',
      'InputLinkURL'
    ];
    for (i = 0; i < extraTabs.length; i++) {
      tabs.push(extraTabs[i]);
    }

    let tabsHTML = '';
    let tabpanelsHTML = '';

    for (i = 0; i < tabs.length; i++) {
      const tab = tabs[i];
      const tabId = C.getNextId();
      const tabindex = (i === 0 ? 0 : -1)
      const selected = (i === 0 ? 'true' : 'false');
      const title = (i > 1 ? H5PEditor.t('H5PEditor.' + tab, 'title') : H5PEditor.t('core', 'tabTitle' + tab));

      tabsHTML += '<div class="av-tab' + (i === 0 ? ' selected' : '') + '" tabindex="' + tabindex + '" role="tab" aria-selected="' + selected + '" aria-controls="av-tabpanel-' + tabId + '" id="av-tab-' + tabId + '">' + title + '</div>';
      tabpanelsHTML += '<div class="av-tabpanel" tabindex="-1" role="tabpanel" id="av-tabpanel-' + tabId + '" aria-labelledby="av-tab-' + tabId + '"' + (i === 0 ? '' : ' hidden=""') + '>' + C.createTabContent(tab, type) + '</div>';
    }

    return C.createInsertDialog(
      '<div class="av-tablist" role="tablist" aria-label="' + H5PEditor.t('core', 'avTablistLabel') + '">' + tabsHTML + '</div>' + tabpanelsHTML,
      true, id, hasDescription
    );
  };

  /**
   * Creates the HTML for the basic 'Upload or URL' dialog.
   *
   * @param {string} type 'video' or 'audio'
   * @param {string} id
   * @param {boolean} hasDescription
   * @returns {string} HTML
   */
  C.createAdd = function (type, id, hasDescription) {
    return C.createInsertDialog(
      '<div class="h5p-dialog-box">' +
        C.createTabContent('BasicFileUpload', type) +
      '</div>' +
      '<div class="h5p-or-vertical">' +
        '<div class="h5p-or-vertical-line"></div>' +
        '<div class="h5p-or-vertical-word-wrapper">' +
          '<div class="h5p-or-vertical-word">' + H5PEditor.t('core', 'or') + '</div>' +
        '</div>' +
      '</div>' +
      '<div class="h5p-dialog-box">' +
          C.createTabContent('InputLinkURL', type) +
      '</div>',
      false, id, hasDescription
    );
  };

  /**
   * Providers incase mime type is unknown.
   * @public
   */
  C.providers = [
    {
      name: 'YouTube',
      regexp: /(?:https?:\/\/)?(?:www\.)?(?:(?:youtube.com\/(?:attribution_link\?(?:\S+))?(?:v\/|embed\/|watch\/|(?:user\/(?:\S+)\/)?watch(?:\S+)v\=))|(?:youtu.be\/|y2u.be\/))([A-Za-z0-9_-]{11})/i,
      aspectRatio: '16:9',
    },
    {
      name: 'Panopto',
      regexp: /^[^\/]+:\/\/([^\/]*panopto\.[^\/]+)\/Panopto\/.+\?id=(.+)$/i,
      aspectRatio: '16:9',
    },
    {
      name: 'Vimeo',
      regexp: /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/,
      aspectRatio: '16:9',
    }
  ];

  /**
   * Find & return an external provider based on the URL
   *
   * @param {string} url
   * @returns {Object}
   */
  C.findProvider = function (url) {
    for (i = 0; i < C.providers.length; i++) {
      if (C.providers[i].regexp.test(url)) {
        return C.providers[i];
      }
    }
  };

  // Avoid ID attribute collisions
  let idCounter = 0;

  /**
   * Grab the next available ID to avoid collisions on the page.
   * @public
   */
  C.getNextId = function () {
    return idCounter++;
  };

  return C;
})(H5P.jQuery);
