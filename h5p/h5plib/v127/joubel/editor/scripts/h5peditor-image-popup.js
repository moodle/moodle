/* global ns Cropper */
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
    var self = this;
    EventDispatcher.call(this);
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
    var setCropperDimensions = function () {
      // Set max dimensions
      var dims = ImageEditingPopup.staticDimensions;
      maxWidth = background.offsetWidth - dims.backgroundPaddingWidth;

      // Only use 65% of window height
      var maxScreenHeight = window.innerHeight * dims.maxScreenHeightPercentage;

      // Calculate editor max height
      var editorHeight = background.offsetHeight - dims.backgroundPaddingHeight - dims.popupHeaderHeight;

      // Use smallest of screen height and editor height,
      // we don't want to overflow editor or screen
      maxHeight = maxScreenHeight < editorHeight ? maxScreenHeight : editorHeight;
      maxHeight = Math.min(maxHeight, maxWidth); // prevent maxHeight from getting too big in long editors like h5p column
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
    var loadScripts = function (callback) {
      loadScript(H5PEditor.basePath + 'libs/cropper.js', function () {
        scriptsLoaded = true;
        if (callback) {
          callback();
        }
      });
    };

    /**
     * Grab canvas data and pass data to listeners.
     */
    var saveImage = () => {
      var convertData = function () {
        const finished = function (blob) {
          self.trigger('savedImage', blob);
        };
        if (self.cropper.mirror.toBlob) {
          // Export canvas as blob to save processing time and bandwidth
          self.cropper.mirror.toBlob(finished, self.mime);
        }
        else {
          // Blob export not supported by canvas, export as dataURL and export
          // to blob before uploading (saves processing resources on server)
          finished(dataURLtoBlob(this.cropper.mirror.toDataURL({
            format: self.mime.split('/')[1]
          })));
        }
      };
      convertData();
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

      // Only use 65% of window height
      var maxScreenHeight = window.innerHeight * 0.65;

      // Calculate editor max height
      var dims = ImageEditingPopup.staticDimensions;
      var backgroundHeight = H5P.$body.get(0).offsetHeight - dims.backgroundPaddingHeight;
      var popupHeightNoImage = dims.darkroomToolbarHeight + dims.popupHeaderHeight;
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
     * Resize cropper canvas, selector and mask.
     */
    this.resizeCropper = () => {
      setCropperDimensions();
      this.cropper.canvas.width = maxWidth - 2; // leave out 2px for container css border
      this.cropper.canvas.height = maxHeight;
      this.cropper.loadImage();
      this.cropper.loadMirror();
      this.cropper.toggleSection('tools');
      this.cropper.toggleSelector(false);
    }

    /**
     * Create image editing tool from image.
     */
    const createCropper = (image) => {
      if (this.cropper) {
        this.cropper.options.canvas.image = image;
        this.cropper.reset();
        return;
      }
      this.cropper = new Cropper({
        uniqueId,
        container: editingContainer,
        canvas: {
          width: maxWidth,
          height: maxHeight,
          background: '#2f323a',
          image
        },
        selector: {
          min: {
            width: 50,
            height: 50
          },
          mask: true
        },
        labels: {
          rotateLeft: H5P.t('rotateLeft'),
          rotateRight: H5P.t('rotateRight'),
          cropImage: H5P.t('cropImage'),
          confirmCrop: H5P.t('confirmCrop'),
          cancelCrop: H5P.t('cancelCrop')
        }
      });
      const classes = ['cropper-h5p-tooltip'];
      H5P.Tooltip(this.cropper.buttons.rotateLeft, { text: H5P.t('rotateLeft'), classes });
      H5P.Tooltip(this.cropper.buttons.rotateRight, { text: H5P.t('rotateRight'), classes });
      H5P.Tooltip(this.cropper.buttons.crop, { text: H5P.t('cropImage'), classes });

      // set before & after rotation events
      const beforeRotation = () => {
        this.cropper.sections.tools.classList.add('hidden');
        this.rotationTimer = setTimeout(() => {
          this.cropper.sections.tools.classList.add('wait');
          this.cropper.container.style.cursor = 'wait';
          this.cropper.masks.left.style.display = 'block';
          this.cropper.masks.left.style.width = '100%';
          this.cropper.masks.left.style.height = '100%';
        }, 1000);
      }
      const afterRotation = () => {
        clearTimeout(this.rotationTimer);
        this.cropper.container.style.cursor = 'auto';
        this.cropper.sections.tools.classList.remove('hidden', 'wait');
        this.cropper.masks.left.style.display = 'none';
      }
      const oldRotate = this.cropper.rotate;
      this.cropper.rotate = (rotation) => {
        beforeRotation();
        oldRotate(rotation, afterRotation);
      }
    };

    /**
     * Set new image in editing tool
     *
     * @param {string} imgSrc Source of new image
     */
    this.setImage = function (imgSrc, callback) {
      H5P.setSource(editingImage, imgSrc, H5PEditor.contentId);
      editingImage.onload = () => {
        createCropper(editingImage);
        editingImage.onload = null;
        imageLoading.classList.add('hidden');
        if (callback) {
          callback();
        }
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
     * @param {Event} [event] Event object (button) for positioning the popup
     */
    this.show = function (offset, imageSrc, event) {
      const openImageEditor = () => {
        H5P.$body.get(0).classList.add('h5p-editor-image-popup');
        background.classList.remove('hidden');
        self.trigger('initialized');
      }
      const alignPopup = () => {
        if (event) {
          let top = event.target.getBoundingClientRect().top + window.scrollY;
          if (window.innerHeight - top < popup.offsetHeight) {
            top = window.innerHeight - popup.offsetHeight - 58; // 48px background padding + 10px so that the popup does not touch the bottom
          }
          popup.style.top = top + 'px';
        }
      }
      const imageLoaded = () => {
        if (offset) {
          self.adjustPopupOffset(offset);
          openImageEditor();
          self.resizeCropper();
          window.addEventListener('resize', this.resizeCropper);
        }
        alignPopup();
      }
      H5P.$body.get(0).appendChild(background);
      background.classList.remove('hidden');
      setCropperDimensions();
      background.classList.add('hidden');
      if (imageSrc) {
        // Load image editing scripts dynamically
        if (!scriptsLoaded) {
          loadScripts(() => self.setImage(imageSrc, imageLoaded));
        }
        else {
          self.setImage(imageSrc, imageLoaded);
        }
      }
      else {
        openImageEditor();
        alignPopup();
      }
      isShowing = true;
    };

    /**
     * Hide popup
     */
    this.hide = () => {
      isShowing = false;
      H5P.$body.get(0).classList.remove('h5p-editor-image-popup');
      background.classList.add('hidden');
      H5P.$body.get(0).removeChild(background);
      window.removeEventListener('resize', this.resizeCropper);
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
      self.cropper.toggleSelector(false);
    });
    createButton('saveLabel', 'h5p-editing-image-save-button h5p-done', function () {
      if (self.cropper.selector.style.display !== 'none') {
        self.cropper.crop(() => {
          self.cropper.toggleSelector(false);
          saveImage();
          self.hide();
        });
      }
      else {
        saveImage();
        self.hide();
      }
    });
  }

  ImageEditingPopup.prototype = Object.create(EventDispatcher.prototype);
  ImageEditingPopup.prototype.constructor = ImageEditingPopup;

  ImageEditingPopup.staticDimensions = {
    backgroundPaddingWidth: 32,
    backgroundPaddingHeight: 96,
    maxScreenHeightPercentage: 0.65,
    popupHeaderHeight: 60
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

