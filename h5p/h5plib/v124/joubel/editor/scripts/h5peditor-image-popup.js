/* global ns Darkroom */
H5PEditor.ImageEditingPopup = (function ($, EventDispatcher) {
  var instanceCounter = 0;
  var scriptsLoaded = false;

  /**
   * Popup for editing images
   *
   * @param {number} [ratio] Ratio that cropping must keep
   * @constructor
   */
  function ImageEditingPopup(ratio) {
    EventDispatcher.call(this);
    var self = this;
    var uniqueId = instanceCounter;
    var isShowing = false;
    var isReset = false;
    var topOffset = 0;
    var maxWidth;
    var maxHeight;

    // Create elements
    var background = document.createElement('div');
    background.className = 'h5p-editing-image-popup-background hidden';

    var popup = document.createElement('div');
    popup.className = 'h5p-editing-image-popup';
    background.appendChild(popup);

    var header = document.createElement('div');
    header.className = 'h5p-editing-image-header';
    popup.appendChild(header);

    var headerTitle = document.createElement('div');
    headerTitle.className = 'h5p-editing-image-header-title';
    headerTitle.textContent = H5PEditor.t('core', 'editImage');
    header.appendChild(headerTitle);

    var headerButtons = document.createElement('div');
    headerButtons.className = 'h5p-editing-image-header-buttons';
    header.appendChild(headerButtons);

    var editingContainer = document.createElement('div');
    editingContainer.className = 'h5p-editing-image-editing-container';
    popup.appendChild(editingContainer);

    var imageLoading = document.createElement('div');
    imageLoading.className = 'h5p-editing-image-loading';
    imageLoading.textContent = ns.t('core', 'loadingImageEditor');
    popup.appendChild(imageLoading);

    // Create editing image
    var editingImage = new Image();
    editingImage.className = 'h5p-editing-image hidden';
    editingImage.id = 'h5p-editing-image-' + uniqueId;
    editingContainer.appendChild(editingImage);

    // Close popup on background click
    background.addEventListener('click', function () {
      this.hide();
    }.bind(this));

    // Prevent closing popup
    popup.addEventListener('click', function (e) {
      e.stopPropagation();
    });

    // Make sure each ImageEditingPopup instance has a unique ID
    instanceCounter += 1;

    /**
     * Create header button
     *
     * @param {string} coreString Must be specified in core translations
     * @param {string} className Unique button identifier that will be added to classname
     * @param {function} clickEvent OnClick function
     */
    var createButton = function (coreString, className, clickEvent) {
      var button = document.createElement('button');
      button.textContent = ns.t('core', coreString);
      button.className = className;
      button.addEventListener('click', clickEvent);
      headerButtons.appendChild(button);
    };

    /**
     * Set max width and height for image editing tool
     */
    var setDarkroomDimensions = function () {
      // Set max dimensions
      var dims = ImageEditingPopup.staticDimensions;
      maxWidth = background.offsetWidth - dims.backgroundPaddingWidth -
        dims.darkroomPadding;

      // Only use 65% of screen height
      var maxScreenHeight = screen.height * dims.maxScreenHeightPercentage;

      // Calculate editor max height
      var editorHeight = background.offsetHeight -
        dims.backgroundPaddingHeight - dims.popupHeaderHeight -
        dims.darkroomToolbarHeight - dims.darkroomPadding;

      // Use smallest of screen height and editor height,
      // we don't want to overflow editor or screen
      maxHeight = maxScreenHeight < editorHeight ? maxScreenHeight : editorHeight;
    };

    /**
     * Create image editing tool from image.
     */
    var createDarkroom = function () {
      window.requestAnimationFrame(function () {
        self.darkroom = new Darkroom('#h5p-editing-image-' + uniqueId, {
          initialize: function () {
            // Reset transformations
            this.transformations = [];

            H5P.$body.get(0).classList.add('h5p-editor-image-popup');
            background.classList.remove('hidden');
            imageLoading.classList.add('hidden');
            self.trigger('initialized');
          },
          maxWidth: maxWidth,
          maxHeight: maxHeight,
          plugins: {
            crop: {
              ratio: ratio || null
            },
            save : false
          }
        });
      });
    };

    /**
     * Load a script dynamically
     *
     * @param {string} path Path to script
     * @param {function} [callback]
     */
    var loadScript = function (path, callback) {
      $.ajax({
        url: path,
        dataType: 'script',
        success: function () {
          if (callback) {
            callback();
          }
        },
        async: true
      });
    };

    /**
     * Load scripts dynamically
     */
    var loadScripts = function () {
      loadScript(H5PEditor.basePath + 'libs/fabric.js', function () {
        loadScript(H5PEditor.basePath + 'libs/darkroom.js', function () {
          createDarkroom();
          scriptsLoaded = true;
        });
      });
    };

    /**
     * Grab canvas data and pass data to listeners.
     */
    var saveImage = function () {

      var isCropped = self.darkroom.plugins.crop.hasFocus();
      var canvas = self.darkroom.canvas.getElement();

      var convertData = function () {
        const finished = function (blob) {
          self.trigger('savedImage', blob);
          canvas.removeEventListener('crop:update', convertData, false);
        };

        if (self.darkroom.canvas.contextContainer.canvas.toBlob) {
          // Export canvas as blob to save processing time and bandwidth
          self.darkroom.canvas.contextContainer.canvas.toBlob(finished, self.mime);
        }
        else {
          // Blob export not supported by canvas, export as dataURL and export
          // to blob before uploading (saves processing resources on server)
          finished(dataURLtoBlob(self.darkroom.canvas.toDataURL({
            format: self.mime.split('/')[1]
          })));
        }
      };

      // Check if image has changed
      if (self.darkroom.transformations.length || isReset || isCropped) {

        if (isCropped) {
          //self.darkroom.plugins.crop.okButton.element.click();
          self.darkroom.plugins.crop.cropCurrentZone();

          canvas.addEventListener('crop:update', convertData, false);
        }
        else {
          convertData();
        }
      }

      isReset = false;
    };

    /**
     * Adjust popup offset.
     * Make sure it is centered on top of offset.
     *
     * @param {Object} [offset] Offset that popup should center on.
     * @param {number} [offset.top] Offset to top.
     */
    this.adjustPopupOffset = function (offset) {
      if (offset) {
        topOffset = offset.top;
      }

      // Only use 65% of screen height
      var maxScreenHeight = screen.height * 0.65;

      // Calculate editor max height
      var dims = ImageEditingPopup.staticDimensions;
      var backgroundHeight = H5P.$body.get(0).offsetHeight - dims.backgroundPaddingHeight;
      var popupHeightNoImage = dims.darkroomToolbarHeight + dims.popupHeaderHeight +
        dims.darkroomPadding;
      var editorHeight =  backgroundHeight - popupHeightNoImage;

      // Available editor height
      var availableHeight = maxScreenHeight < editorHeight ? maxScreenHeight : editorHeight;

      // Check if image is smaller than available height
      var actualImageHeight;
      if (editingImage.naturalHeight < availableHeight) {
        actualImageHeight = editingImage.naturalHeight;
      }
      else {
        actualImageHeight = availableHeight;

        // We must check ratio as well
        var imageRatio = editingImage.naturalHeight / editingImage.naturalWidth;
        var maxActualImageHeight = maxWidth * imageRatio;
        if (maxActualImageHeight < actualImageHeight) {
          actualImageHeight = maxActualImageHeight;
        }
      }

      var popupHeightWImage = actualImageHeight + popupHeightNoImage;
      var offsetCentered = topOffset - (popupHeightWImage / 2) -
        (dims.backgroundPaddingHeight / 2);

      // Min offset is 0
      offsetCentered = offsetCentered > 0 ? offsetCentered : 0;

      // Check that popup does not overflow editor
      if (popupHeightWImage + offsetCentered > backgroundHeight) {
        var newOffset = backgroundHeight - popupHeightWImage;
        offsetCentered = newOffset < 0 ? 0 : newOffset;
      }

      popup.style.top = offsetCentered + 'px';
    };

    /**
     * Set new image in editing tool
     *
     * @param {string} imgSrc Source of new image
     */
    this.setImage = function (imgSrc) {
      // Set new image
      var darkroom = popup.querySelector('.darkroom-container');
      if (darkroom) {
        darkroom.parentNode.removeChild(darkroom);
      }

      H5P.setSource(editingImage, imgSrc, H5PEditor.contentId);
      editingImage.onload = function () {
        createDarkroom();
        editingImage.onload = null;
      };
      imageLoading.classList.remove('hidden');
      editingImage.classList.add('hidden');
      editingContainer.appendChild(editingImage);
    };

    /**
     * Show popup
     *
     * @param {Object} [offset] Offset that popup should center on.
     * @param {string} [imageSrc] Source of image that will be edited
     */
    this.show = function (offset, imageSrc) {
      H5P.$body.get(0).appendChild(background);
      background.classList.remove('hidden');
      setDarkroomDimensions();
      background.classList.add('hidden');
      if (imageSrc) {
        // Load image editing scripts dynamically
        if (!scriptsLoaded) {
          H5P.setSource(editingImage, imageSrc, H5PEditor.contentId);
          loadScripts();
        }
        else {
          self.setImage(imageSrc);
        }

        if (offset) {
          var imageLoaded = function () {
            this.adjustPopupOffset(offset);
            editingImage.removeEventListener('load', imageLoaded);
          }.bind(this);

          editingImage.addEventListener('load', imageLoaded);
        }
      }
      else {
        H5P.$body.get(0).classList.add('h5p-editor-image-popup');
        background.classList.remove('hidden');
        self.trigger('initialized');
      }

      isShowing = true;
    };

    /**
     * Hide popup
     */
    this.hide = function () {
      isShowing = false;
      H5P.$body.get(0).classList.remove('h5p-editor-image-popup');
      background.classList.add('hidden');
      H5P.$body.get(0).removeChild(background);
    };

    /**
     * Toggle popup visibility
     */
    this.toggle = function () {
      if (isShowing) {
        this.hide();
      }
      else {
        this.show();
      }
    };

    // Create header buttons
    createButton('resetToOriginalLabel', 'h5p-editing-image-reset-button h5p-remove', function () {
      self.trigger('resetImage');
      isReset = true;
    });
    createButton('cancelLabel', 'h5p-editing-image-cancel-button', function () {
      self.trigger('canceled');
      self.hide();
    });
    createButton('saveLabel', 'h5p-editing-image-save-button h5p-done', function () {
      saveImage();
      self.hide();
    });
  }

  ImageEditingPopup.prototype = Object.create(EventDispatcher.prototype);
  ImageEditingPopup.prototype.constructor = ImageEditingPopup;

  ImageEditingPopup.staticDimensions = {
    backgroundPaddingWidth: 32,
    backgroundPaddingHeight: 96,
    darkroomPadding: 64,
    darkroomToolbarHeight: 40,
    maxScreenHeightPercentage: 0.65,
    popupHeaderHeight: 59
  };

  /**
   * Convert a data URL(base64) into blob.
   *
   * @param {string} dataURL
   * @return {Blob}
   */
  const dataURLtoBlob = function (dataURL) {
    const split = dataURL.split(',');

    // First part is the mime type
    const mime = split[0].match(/data:(.*);base64/i)[1];

    // Second part is the base64 data
    const bytes = atob(split[1]);

    // Convert string into char code array
    const bits = new Uint8Array(bytes.length);
    for (let i = 0; i < bytes.length; i++) {
      bits[i] = bytes.charCodeAt(i);
    }

    // Make the codes into a Blob, and we're done!
    return new Blob([bits], {
      type: mime
    });
  }

  return ImageEditingPopup;

}(H5P.jQuery, H5P.EventDispatcher));
