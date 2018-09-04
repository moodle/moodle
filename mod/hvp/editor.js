(function ($) {
  function getRow ($el) {
    return $el.closest('.fitem');
  }

  function init () {
    var $editor = $('.h5p-editor');
    var $fileField = $('input[name="h5pfile"]');

    if (H5PIntegration.hubIsEnabled) {
      // TODO: This can easily break in new themes. Improve robustness of this
      // by not including h5paction in form, when it should not be used.
      $('input[name="h5paction"]').parents('.fitem').last().hide();
    }

    H5PEditor.init(
      $('#mform1'),
      $('input[name="h5paction"]'),
      getRow($fileField),
      getRow($editor),
      $editor,
      $('input[name="h5plibrary"]'),
      $('input[name="h5pparams"]')
    );
  }

  $(document).ready(init);
})(H5P.jQuery);
