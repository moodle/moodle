(function ($, ns) {
  H5PEditor.init = function ($form, $type, $upload, $create, $editor, $library, $params, $maxScore, $title, cancelSubmitCallback) {
    H5PEditor.$ = H5P.jQuery;
    H5PEditor.basePath = H5PIntegration.editor.libraryUrl;
    H5PEditor.fileIcon = H5PIntegration.editor.fileIcon;
    H5PEditor.ajaxPath = H5PIntegration.editor.ajaxPath;
    H5PEditor.filesPath = H5PIntegration.editor.filesPath;
    H5PEditor.apiVersion = H5PIntegration.editor.apiVersion;
    H5PEditor.contentLanguage = H5PIntegration.editor.language;

    // Semantics describing what copyright information can be stored for media.
    H5PEditor.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;
    H5PEditor.metadataSemantics = H5PIntegration.editor.metadataSemantics;

    // Required styles and scripts for the editor
    H5PEditor.assets = H5PIntegration.editor.assets;

    // Required for assets
    H5PEditor.baseUrl = '';

    if (H5PIntegration.editor.nodeVersionId !== undefined) {
      H5PEditor.contentId = H5PIntegration.editor.nodeVersionId;
    }

    var h5peditor;
    $create.hide();
    var library = $library.val();

    $type.change(function () {
      if ($type.filter(':checked').val() === 'upload') {
        $create.hide();
        $upload.show();
      }
      else {
        $upload.hide();
        if (h5peditor === undefined) {
          h5peditor = new ns.Editor(library, $params.val(), $editor[0]);
        }
        $create.show();
      }
    });

    if ($type.filter(':checked').val() === 'upload') {
      $type.change();
    }
    else {
      $type.filter('input[value="create"]').attr('checked', true).change();
    }

    // Duplicate the submit button input because it is not posted when calling $form.submit()
    const $submitters = $form.find('input[type="submit"]');
    let isCanceling = false;
    $submitters.click(function () {
      // Create hidden input and give it the value
      const name = $(this).prop('name');
      const value = $(this).prop('value');
      $('<input type="hidden" name="' + name + '" value="' + value + '" />').appendTo($form);

      // Allow caller to cancel validation and submission of form on button click
      if (cancelSubmitCallback) {
        isCanceling = cancelSubmitCallback($(this));
      }
    });

    let formIsUpdated = false;
    $form.submit(function (event) {
      if ($type.length && $type.filter(':checked').val() === 'upload') {
        return; // Old file upload
      }

      if (isCanceling) {
        return;
      }

      if (h5peditor !== undefined && !formIsUpdated) {

        // Get content from editor
        h5peditor.getContent(function (content) {

          // Set the title field to the metadata title if the field exists
          $title.val(content.title);

          // Set main library
          $library.val(content.library);

          // Set params
          $params.val(content.params);

          // Submit form data
          formIsUpdated = true;
          $form.submit();
        });

        // Stop default submit
        event.preventDefault();
      }
    });
  };

  H5PEditor.getAjaxUrl = function (action, parameters) {
    var url = H5PIntegration.editor.ajaxPath + action;

    if (parameters !== undefined) {
      var separator = url.indexOf('?') === -1 ? '?' : '&';
      for (var property in parameters) {
        if (parameters.hasOwnProperty(property)) {
          url += separator + property + '=' + parameters[property];
          separator = '&';
        }
      }
    }

    return url;
  };
})(H5P.jQuery, H5PEditor);
