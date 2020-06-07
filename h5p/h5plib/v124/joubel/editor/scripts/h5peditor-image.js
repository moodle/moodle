var H5PEditor = window.H5PEditor = window.H5PEditor || {};
var ns = H5PEditor;

/**
 * Adds an image upload field with image editing tool to the form.
 *
 * @param {Object} parent Parent widget of this widget
 * @param {Object} field Semantic fields
 * @param {Object} params Existing image parameters
 * @param {function} setValue Function for updating parameters
 * @returns {ns.widgets.image}
 */
ns.widgets.image = function (parent, field, params, setValue) {
  var self = this;

  // Initialize inheritance
  ns.File.call(self, parent, field, params, setValue);

  this.parent = parent;
  this.field = field;
  this.params = params;
  this.setValue = setValue;
  this.library = parent.library + '/' + field.name;
  this.id = ns.getNextFieldId(field);

  if (params !== undefined) {
    this.copyright = params.copyright;
  }

  // Keep track of editing image
  this.isEditing = false;

  // Keep track of type of image that is being uploaded
  this.isOriginalImage = false;

  this.changes = [];
  this.passReadies = true;
  parent.ready(function () {
    self.passReadies = false;
  });

  this.confirmationDialog = new H5P.ConfirmationDialog({
    headerText: H5PEditor.t('core', 'removeImage'),
    bodyText: H5PEditor.t('core', 'confirmImageRemoval')
  });

  this.confirmationDialog.on('confirmed', function () {
    self.removeImage();
  });

  // When uploading starts
  self.on('upload', function () {
    // Hide edit image button
    self.$editImage.addClass('hidden');
  });

  // When a new file has been uploaded
  self.on('fileUploaded', function (event) {
    // Uploaded new original image
    if (self.isOriginalImage) {
      delete self.params.originalImage;
      self.editImagePopup.mime = self.params.mime
    }

    // Store width and height
    self.params.width = event.data.width;
    self.params.height = event.data.height;

    // Show edit image button
    self.$editImage.removeClass('hidden');
    self.isEditing = false;
  });
};

ns.widgets.image.prototype = Object.create(ns.File.prototype);
ns.widgets.image.prototype.constructor = ns.widgets.image;

/**
 * Append field to the given wrapper.
 *
 * @param {jQuery} $wrapper
 */
ns.widgets.image.prototype.appendTo = function ($wrapper) {
  var self = this;

  var htmlString = '<div class="file"></div>' +
    '<div class="h5p-editor-image-buttons">' +
      '<button class="h5peditor-button-textual h5p-editing-image-button">' + ns.t('core', 'editImage') + '</button>';

  if (!this.field.disableCopyright) {
    htmlString += '<button class="h5peditor-button-textual h5p-copyright-button">' + ns.t('core', 'editCopyright') + '</button>';
  }

  htmlString += '</div>' +
    '<div class="h5p-editor-dialog">' +
      '<a href="#" class="h5p-close" title="' + ns.t('core', 'close') + '"></a>' +
    '</div>';

  var html = ns.createFieldMarkup(this.field, htmlString, this.id);

  var $container = ns.$(html).appendTo($wrapper);
  this.$item = $container;
  this.$editImage = $container.find('.h5p-editing-image-button');
  this.$copyrightButton = $container.find('.h5p-copyright-button');
  this.$file = $container.find('.file');
  this.$errors = $container.find('.h5p-errors');
  this.addFile();

  var $dialog = $container.find('.h5p-editor-dialog');
  $container.find('.h5p-copyright-button').add($dialog.find('.h5p-close')).click(function () {
    $dialog.toggleClass('h5p-open');
    return false;
  });

  var editImagePopup = self.editImagePopup = new H5PEditor.ImageEditingPopup(this.field.ratio);
  editImagePopup.on('savedImage', function (e) {

    // Not editing any longer
    self.isEditing = false;

    // No longer an original image
    self.isOriginalImage = false;

    // Set current source as original image, if no original image
    if (!self.params.originalImage) {
      self.params.originalImage = {
        path: self.params.path,
        mime: self.params.mime,
        height: self.params.height,
        width: self.params.width
      };
    }

    const filenameparts = self.params.path.match(/([^\/]+)\.([^#]+)/);

    // Upload new image
    self.upload(e.data, filenameparts[1] + '-edit.' + filenameparts[2]);
  });

  editImagePopup.on('resetImage', function () {
    editImagePopup.setImage(self.params.originalImage ? self.params.originalImage : self.params);
  });

  editImagePopup.on('canceled', function () {
    self.isEditing = false;
  });

  editImagePopup.on('initialized', function () {
    // Remove throbber from image
    self.$editImage.removeClass('loading');
  });

  $container.find('.h5p-editing-image-button').click(function () {
    if (self.params && self.params.path) {
      var imageSrc;
      if (!self.isEditing) {
        imageSrc = self.params;
        self.isEditing = true;
      }
      self.$editImage.toggleClass('loading');

      // Add throbber to image
      editImagePopup.show(ns.$(this).offset(), imageSrc);
    }
  });

  ns.File.addCopyright(self, $dialog, function (field, value) {
    if (self.params !== undefined) {
      self.params.copyright = value;
    }
    self.copyright = value;
  });

};


/**
 * Sync copyright.
 */
ns.widgets.image.prototype.setCopyright = function (value) {
  this.copyright = this.params.copyright = value;
};


/**
 * Creates thumbnail HTML and actions.
 *
 * @returns {boolean} True if file was added, false if file was removed
 */
ns.widgets.image.prototype.addFile = function () {
  var that = this;

  if (this.params === undefined) {

    // No image look
    let html = '<a href="#" id="' + this.id + '" class="add"';
    if (this.field.description !== undefined) {
      html += ' aria-describedby="' + ns.getDescriptionId(this.id) + '"';
    }
    html += ' title="' + ns.t('core', 'addFile') + '">' +
      '<div class="h5peditor-field-file-upload-text">' + ns.t('core', 'add') + '</div>' +
    '</a>'

    this.$file
      .html(html)
      .children('.add')
      .click(function () {
        that.isOriginalImage = true;
        that.openFileSelector();
        return false;
      });

    // Remove edit image button
    this.$editImage.addClass('hidden');
    this.$copyrightButton.addClass('hidden');
    this.isEditing = false;

    return false;
  }

  var source = H5P.getPath(this.params.path, H5PEditor.contentId);
  var altText = (this.field.label === undefined ? '' : this.field.label);
  var fileHtmlString = '<a href="#" id="' + this.id + '" title="' + ns.t('core', 'changeFile') + '" class="thumbnail"';
  if (this.field.description !== undefined) {
    fileHtmlString += ' aria-describedby="' + ns.getDescriptionId(this.id) + '"';
  }
  fileHtmlString += '><img alt="' + altText + '"/></a>' +
    '<a href="#" class="remove" title="' + ns.t('core', 'removeFile') + '"></a>';

  this.$file.html(fileHtmlString)
    .children(':eq(0)')
    .click(function () {
      that.isOriginalImage = true;
      that.openFileSelector();
      return false;
    })
    .children('img')
    .attr('src', source)
    .end()
    .next()
    .click(function () {
      that.confirmRemovalDialog.show(that.$file.offset().top);
      return false;
    });

  var $img = this.$file.find('img');
  $img.one('load', function () {
    // Make editor resize
    $img.addClass('loaded');
  });

  // Uploading original image
  that.$editImage.removeClass('hidden');
  that.$copyrightButton.removeClass('hidden');

  // Notify listeners that image was changed to params
  that.trigger('changedImage', this.params);

  return true;
};

/**
 * Remove image
 */
ns.widgets.image.prototype.removeImage = function () {

  // Notify listeners that we removed image with params
  this.trigger('removedImage', this.params);

  delete this.params;
  this.setValue(this.field);
  this.addFile();

  for (var i = 0; i < this.changes.length; i++) {
    this.changes[i]();
  }
};

/**
 * Validate this item
 */
ns.widgets.image.prototype.validate = function () {
  return true;
};

/**
 * Remove this item.
 */
ns.widgets.image.prototype.remove = function () {
  // TODO: Check what happens when removed during upload.
  this.$file.parent().remove();
};

/**
 * Collect functions to execute once the tree is complete.
 *
 * @param {function} ready
 */
ns.widgets.image.prototype.ready = function (ready) {
  if (this.passReadies) {
    this.parent.ready(ready);
  }
  else {
    ready();
  }
};
