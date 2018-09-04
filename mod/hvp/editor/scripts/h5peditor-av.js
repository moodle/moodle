var H5PEditor = H5PEditor || {};

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
      self.$addDialog.removeClass('h5p-open');
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

    var imageHtml =
      '<ul class="file list-unstyled"></ul>' +
      C.createAdd(self.field.type) +
      '<a class="h5p-copyright-button" href="#">' + H5PEditor.t('core', 'editCopyright') + '</a>' +
      '<div class="h5p-editor-dialog">' +
        '<a href="#" class="h5p-close" title="' + H5PEditor.t('core', 'close') + '"></a>' +
      '</div>';

    var html = H5PEditor.createFieldMarkup(this.field, imageHtml);

    var $container = $(html).appendTo($wrapper);
    this.$files = $container.children('.file');
    this.$add = $container.children('.h5p-add-file').click(function () {
      self.$addDialog.addClass('h5p-open');
    });

    this.$addDialog = this.$add.next();
    var $url = this.$addDialog.find('.h5p-file-url');
    this.$addDialog.find('.h5p-cancel').click(function () {
      self.updateIndex = undefined;
      $url.val('');
      self.$addDialog.removeClass('h5p-open');
    });

    this.$addDialog.find('.h5p-file-drop-upload')
      .addClass('has-advanced-upload')
      .on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
      })
      .on('dragover dragenter', function(e) {
        $(this).addClass('over');
        e.originalEvent.dataTransfer.dropEffect = 'copy';
      })
      .on('dragleave', function(e) {
        $(this).removeClass('over');
      })
      .on('drop', function(e) {
        self.uploadFiles(e.originalEvent.dataTransfer.files);
      })
      .click(function () {
        self.openFileSelector();
      });

    this.$addDialog.find('.h5p-insert').click(function () {
      self.useUrl($url.val().trim());
      self.$addDialog.removeClass('h5p-open');
      $url.val('');
    });

    this.$errors = $container.children('.h5p-errors');

    if (this.params !== undefined) {
      for (var i = 0; i < this.params.length; i++) {
        this.addFile(i);
      }
    } else {
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
    var rowInputId = 'h5p-av-' + index;
    var defaultQualityName = H5PEditor.t('core', 'videoQualityDefaultLabel', { ':index': index + 1 });
    var qualityName = (file.metadata && file.metadata.qualityName) ? file.metadata.qualityName : defaultQualityName;

    // Check if source is YouTube
    var youtubeRegex = C.providers.filter(function (provider) {
      return provider.name === 'YouTube';
    })[0].regexp;
    var isYoutube = file.path && file.path.match(youtubeRegex);

    // Only allow single source if YouTube
    if (isYoutube) {
      // Remove all other files except this one
      that.$files.children().each(function (i) {
        if (i !== that.updateIndex) {
          that.removeFileWithElement($(this));
        }
      });
      // Remove old element if updating
      that.$files.children().each(function () {
        $(this).remove();
      })
      // This is now the first and only file
      index = 0;
    }
    this.$add.toggleClass('hidden', !!isYoutube);

    // If updating remove and recreate element
    if (that.updateIndex !== undefined) {
      var $oldFile = this.$files.children(':eq(' + index + ')');
      $oldFile.remove();
      this.updateIndex = undefined;
    }

    // Create file with customizable quality if enabled and not youtube
    if (this.field.enableCustomQualityLabel === true && !isYoutube) {
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
    var $file = $(fileHtml)
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
      .change(function() {
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
  }

  C.prototype.useUrl = function (url) {
    if (this.params === undefined) {
      this.params = [];
      this.setValue(this.field, this.params);
    }

    var mime;
    var matches = url.match(/\.(webm|mp4|ogv|m4a|mp3|ogg|oga|wav)/i);
    if (matches !== null) {
      mime = matches[matches.length - 1];
    }
    else {
      // Try to find a provider
      for (var i = 0; i < C.providers.length; i++) {
        if (C.providers[i].regexp.test(url)) {
          mime = C.providers[i].name;
          break;
        }
      }
    }

    var file = {
      path: url,
      mime: this.field.type + '/' + (mime ? mime : 'unknown'),
      copyright: this.copyright
    };
    var index = (this.updateIndex !== undefined ? this.updateIndex : this.params.length);
    this.params[index] = file;
    this.addFile(index);

    for (var i = 0; i < this.changes.length; i++) {
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
    // TODO: Check what happens when removed during upload.
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
   * HTML for add button.
   *
   * @param {string} type 'video' or 'audio'
   * @returns {string} HTML
   */
  C.createAdd = function (type) {
    var inputPlaceholder = H5PEditor.t('core', type === 'audio' ? 'enterAudioUrl' : 'enterVideoUrl');
    var inputTitle = H5PEditor.t('core', type === 'audio' ? 'enterAudioTitle' : 'enterVideoTitle');
    var uploadTitle = H5PEditor.t('core', type === 'audio' ? 'uploadAudioTitle' : 'uploadVideoTitle')
    var description = (type === 'audio' ? '' : '<div class="h5p-errors"></div><div class="h5peditor-field-description">' + H5PEditor.t('core', 'addVideoDescription') + '</div>');

    return '<div role="button" tabindex="0" class="h5p-add-file" title="' + H5PEditor.t('core', 'addFile') + '"></div>' +
      '<div class="h5p-add-dialog">' +
        '<div class="h5p-add-dialog-table">' +
          '<div class="h5p-dialog-box">' +
            '<h3>' + uploadTitle + '</h3>' +
            '<div class="h5p-file-drop-upload">' +
              '<div class="h5p-file-drop-upload-inner"/>' +
            '</div>' +
          '</div>' +
          '<div class="h5p-or-vertical">' +
            '<div class="h5p-or-vertical-line"></div>' +
            '<div class="h5p-or-vertical-word-wrapper">' +
              '<div class="h5p-or-vertical-word">' + H5PEditor.t('core', 'or') + '</div>' +
            '</div>' +
          '</div>' +
          '<div class="h5p-dialog-box">' +
            '<h3>' + inputTitle + '</h3>' +
            '<div class="h5p-file-url-wrapper">' +
              '<input type="text" placeholder="' + inputPlaceholder + '" class="h5p-file-url h5peditor-text"/>' +
            '</div>' +
            description +
          '</div>' +
        '</div>' +
        '<div class="h5p-buttons">' +
          '<button class="h5peditor-button-textual h5p-insert">' + H5PEditor.t('core', 'insert') + '</button>' +
          '<button class="h5peditor-button-textual h5p-cancel">' + H5PEditor.t('core', 'cancel') + '</button>' +
        '</div>' +
      '</div>';
  };

  /**
   * Providers incase mime type is unknown.
   * @public
   */
  C.providers = [{
    name: 'YouTube',
    regexp: /(?:https?:\/\/)?(?:www\.)?(?:(?:youtube.com\/(?:attribution_link\?(?:\S+))?(?:v\/|embed\/|watch\/|(?:user\/(?:\S+)\/)?watch(?:\S+)v\=))|(?:youtu.be\/|y2u.be\/))([A-Za-z0-9_-]{11})/i
  }];

  return C;
})(H5P.jQuery);
