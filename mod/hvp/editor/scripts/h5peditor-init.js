//var H5PEditor = (H5PEditor || {});
(function ($, ns) {
  H5PEditor.init = function ($form, $type, $upload, $create, $editor, $library, $params) {
    H5PEditor.$ = H5P.jQuery;
    H5PEditor.basePath = H5PIntegration.editor.libraryUrl;
    H5PEditor.fileIcon = H5PIntegration.editor.fileIcon;
    H5PEditor.ajaxPath = H5PIntegration.editor.ajaxPath;
    H5PEditor.filesPath = H5PIntegration.editor.filesPath;
    H5PEditor.apiVersion = H5PIntegration.editor.apiVersion;

    // Semantics describing what copyright information can be stored for media.
    H5PEditor.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;

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

    $form.submit(function () {
      if (h5peditor !== undefined) {
        var params = h5peditor.getParams();
        if (params !== undefined) {
          $library.val(h5peditor.getLibrary());
          $params.val(JSON.stringify(params));
        }
      }
    });
  };

  H5PEditor.getAjaxUrl = function (action, parameters) {
    var url = H5PIntegration.editor.ajaxPath + action;

    if (parameters !== undefined) {
      for (var property in parameters) {
        if (parameters.hasOwnProperty(property)) {
          url += '&' + property + '=' + parameters[property];
        }
      }
    }

    return url;
  };
})(H5P.jQuery, H5PEditor);
