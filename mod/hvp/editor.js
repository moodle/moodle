(function($) {

  /**
   * Get closest row of element
   *
   * @param {jQuery} $el
   * @returns {jQuery}
   */
  function getRow($el) {
    return $el.closest('.fitem');
  }

    /**
     * Initializes editor
     */
  function init() {
    var $editor = $('.h5p-editor');
    var $fileField = $('input[name="h5pfile"]');

    if (H5PIntegration.hubIsEnabled) {
      // TODO: This can easily break in new themes. Improve robustness of this
      // by not including h5paction in form, when it should not be used.
      $('input[name="h5paction"]').parents('.fitem').last().hide();
    }

    const mformId = H5PIntegration.editor && H5PIntegration.editor.formId !== null
      ? H5PIntegration.editor.formId
      : 'mform1';

    // Cancel validation and submission of form if clicking cancel button
    const cancelSubmitCallback = function ($button) {
      return $button.is('[name="cancel"]');
    };

    H5PEditor.init(
      $('#' + mformId),
      $('input[name="h5paction"]'),
      getRow($fileField),
      getRow($editor),
      $editor,
      $('input[name="h5plibrary"]'),
      $('input[name="h5pparams"]'),
      $('input[name="h5pmaxscore"]'),
      $('input[name="name"]'),
      cancelSubmitCallback
    );
  }

  $(document).ready(init);
})(H5P.jQuery);
