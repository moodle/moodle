/**
 * Utility that makes it possible to hide fields when a checkbox is unchecked
 */
(function ($) {
  function setupHiding() {
    var $toggler = $(this);

    // Getting the field which should be hidden:
    var $subject = $($toggler.data('h5p-visibility-subject-selector'));

    var toggle = function () {
      $subject.toggle($toggler.is(':checked'));
    };

    $toggler.change(toggle);
    toggle();
  }

  function setupRevealing() {
    var $button = $(this);

    // Getting the field which should have the value:
    var $input = $('#' + $button.data('control'));

    if (!$input.data('value')) {
      $button.remove();
      return;
    }

    // Setup button action
    var revealed = false;
    var text = $button.html();
    $button.click(function () {
      if (revealed) {
        $input.val('');
        $button.html(text);
        revealed = false;
      }
      else {
        $input.val($input.data('value'));
        $button.html($button.data('hide'));
        revealed = true;
      }
    });
  }

  $(document).ready(function () {
    // Get the checkboxes making other fields being hidden:
    $('.h5p-visibility-toggler').each(setupHiding);

    // Get the buttons making other fields have hidden values:
    $('.h5p-reveal-value').each(setupRevealing);
  });
})(H5P.jQuery);
