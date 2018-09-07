H5PEditor.FileUploader = (function ($, EventDispatcher) {
  var nextIframe;

  /**
   * File Upload API for H5P
   *
   * @class H5PEditor.FileUploader
   * @extends H5P.EventDispatcher
   * @param {Object} field Required for validating the uploaded file
   */
  function FileUploader(field) {
    var self = this;

    // Initialize event inheritance
    EventDispatcher.call(self);

    var isUploadingData;

    /**
     * Trigger uploading start.
     *
     * @private
     * @param {string} [data] Optional for uploading string data (URI)
     * @return {boolean} false if the iframe is unavailable and the caller should try again later
     */
    var upload = function (data, files) {
      if (!nextIframe.isReady()) {
        return false; // Iframe isn't loaded. The caller should try again later
      }
      isUploadingData = (data !== undefined && data !== null) || (files !== undefined);

      // Add event listeners
      nextIframe.on('upload', function (event) {
        self.trigger(event);
      });
      nextIframe.on('uploadComplete', function (event) {
        self.trigger(event);
      });

      // Update field
      nextIframe.setField(field, data, files);

      return true;
    };

    /**
     * Prepare an iframe and triggers the opening of the file selector
     * @return {boolean} false if the iframe is unavailable and the caller should try again later
     */
    self.openFileSelector = function () {
      return upload();
    };

    /**
     * Prepare an iframe and trigger upload of the given data.
     *
     * @param {string} data
     * @return {boolean} false if the iframe is unavailable and the caller should try again later
     */
    self.uploadData = function (data) {
      if (data === undefined) {
        throw('Missing data.');
      }
      return upload(data);
    };

    self.uploadFiles = function (files) {
      return upload(null, files);
    };

    /**
     * Makes it possible to check if it is data or a file being uploaded.
     *
     * @return {boolean}
     */
    self.isUploadingData = function () {
      return isUploadingData;
    };

    if (!nextIframe) {
      // We must always have an iframe available for the next upload
      nextIframe = new Iframe();
    }
  }

  // Extends the event dispatcher
  FileUploader.prototype = Object.create(EventDispatcher.prototype);
  FileUploader.prototype.constructor = FileUploader;

  /**
   * Iframe for file uploading. Only available for the FileUploader class.
   * Iframes are discarded after the upload is completed.
   *
   * @private
   * @class Iframe
   * @extends H5P.EventDispatcher
   */
  function Iframe() {
    var self = this;

    // Initialize event inheritance
    EventDispatcher.call(self);

    var ready = false;
    var $iframe, $form, $file, $data, $field;

    /**
     * @private
     */
    var upload = function () {
      // Iframe isn't really bound to a field until the upload starts
      ready = false;
      // Trigger upload event and submit upload form
      self.trigger('upload');
      $form.submit();

      // This iframe is used, we must add another for the next upload
      nextIframe = new Iframe();
    };

    /**
     * Create and insert iframe into the DOM.
     *
     * @private
     */
    var insertIframe = function () {
      $iframe = $('<iframe/>', {
        css: {
          position: 'absolute',
          width: '1px',
          height: '1px',
          top: '-1px',
          border: 0,
          overflow: 'hidden'
        },
        one: {
          load: function () {
            ready = true;
          }
        },
        appendTo: 'body'
      });
    };

    /**
     * Create and add upload form to the iframe.
     *
     * @private
     */
    var insertForm = function () {
      // Create upload form
      $form = $('<form/>', {
        method: 'post',
        enctype: 'multipart/form-data',
        action: H5PEditor.getAjaxUrl('files')
      });

      // Create input fields
      $file = $('<input/>', {
        type: 'file',
        name: 'file',
        on: {
          change: upload
        },
        appendTo: $form
      });
      $data = $('<input/>', {
        type: 'hidden',
        name: 'dataURI',
        appendTo: $form
      });
      $field = $('<input/>', {
        type: 'hidden',
        name: 'field',
        appendTo: $form
      });
      $('<input/>', {
        type: 'hidden',
        name: 'contentId',
        value: H5PEditor.contentId ? H5PEditor.contentId : 0,
        appendTo: $form
      });

      // Add form to iframe
      var $body = $iframe.contents().find('body');
      $form.appendTo($body);

      // Add event handler for processing results
      $iframe.on('load', processResponse);
    };

    /**
     * Handler for processing server response when upload form is submitted.
     *
     * @private
     */
    var processResponse = function () {
      // Upload complete, get response text
      var $body = $iframe.contents().find('body');
      var response = $body.text();

      // Clean up all our DOM elements
      $iframe.remove();

      // Try to parse repsonse
      if (response) {
        var result;
        var uploadComplete = {
          error: null,
          data: null
        };

        try {
          result = JSON.parse(response);
        }
        catch (err) {
          H5P.error(err);
          // Add error data to event object
          uploadComplete.error = H5PEditor.t('core', 'fileToLarge');
        }

        if (result !== undefined) {
          if (result.error !== undefined) {
            uploadComplete.error = result.error;
          }
          if (result.success === false) {
            uploadComplete.error = (result.message ? result.message : H5PEditor.t('core', 'unknownFileUploadError'));
          }
        }

        if (uploadComplete.error === null) {
          // No problems, add response data to event object
          uploadComplete.data = result;
        }

        // Allow the widget to process the result
        self.trigger('uploadComplete', uploadComplete);
      }
    };

    /**
     * Prepare the upload form for the given field.
     * Opens the file selector or if data is provided, submits the form
     * straight away.
     *
     * @param {Object} field
     * @param {string} [data] Optional URI
     */
    self.setField = function (field, data, files) {
      // Determine allowed file mimes
      var mimes;
      if (field.mimes) {
        mimes = field.mimes.join(',');
      }
      else if (field.type === 'image') {
        mimes = 'image/jpeg,image/png,image/gif';
      }
      else if (field.type === 'audio') {
        mimes = 'audio/mpeg,audio/x-wav,audio/ogg';
      }
      else if (field.type === 'video') {
        mimes = 'video/mp4,video/webm,video/ogg';
      }

      $file.attr('accept', mimes);

      // Set field
      $field.val(JSON.stringify(field));

      if (files !== undefined) {
        $file.prop('files', files);
      } else if (data !== undefined) {
        // Upload given data
        $data.val(data);
        upload();
      }
      else {
        // Trigger file selector
        $file.click();
      }
    };

    /**
     * Indicates if this iframe is ready to be used
     */
    self.isReady = function () {
      if (!ready) {
        return false;
      }

      if (!$form) {
        // Insert form if not present
        insertForm();
      }
      else {
        // If present clear any event handlers (was used by another field)
        self.off('upload');
        self.off('uploadComplete');
      }

      return true;
    };

    // Always insert iframe on construct
    insertIframe();
    // The iframe must be loaded before the click event that sets the field,
    // async clicking won't work for security reasons in the browser.
  }

  // Extends the event dispatcher
  Iframe.prototype = Object.create(EventDispatcher.prototype);
  Iframe.prototype.constructor = Iframe;

  return FileUploader;
})(H5P.jQuery, H5P.EventDispatcher);
