/* global H5PEditor */
H5PEditor.FullscreenBar = (function ($) {
  function FullscreenBar ($mainForm, library) {

    const title = H5PEditor.libraryCache[library] ? H5PEditor.libraryCache[library].title : library;
    const iconId = library.split(' ')[0].split('.')[1].toLowerCase();

    let isInFullscreen = false;
    let exitSemiFullscreen;

    $mainForm.addClass('h5peditor-form-manager');

    // Add fullscreen bar
    const $bar = ns.$('<div/>', {
      'class': 'h5peditor-form-manager-head'
    })

    const $breadcrumb = ns.$('<div/>', {
      'class': 'h5peditor-form-manager-breadcrumb',
      appendTo: $bar
    });

    const $title = ns.$('<div/>', {
      'class': 'h5peditor-form-manager-title ' + iconId,
      text: title,
      appendTo: $breadcrumb
    });

    const fullscreenButton = createButton('fullscreen', '', function () {
      if (isInFullscreen) {
        // Trigger semi-fullscreen exit
        exitSemiFullscreen();
      }
      else {
        // Trigger semi-fullscreen enter
        exitSemiFullscreen = H5PEditor.semiFullscreen($mainForm, function () {
          fullscreenButton.setAttribute('aria-label', H5PEditor.t('core', 'exitFullscreenButtonLabel'));
          isInFullscreen = true;
        }, function () {
          fullscreenButton.setAttribute('aria-label', H5PEditor.t('core', 'enterFullscreenButtonLabel'))
          isInFullscreen = false;
        });
      }
    }, H5PEditor.t('core', 'enterFullscreenButtonLabel'));

    // Create 'Proceed to save' button
    const proceedButton = createButton('proceed', H5PEditor.t('core', 'proceedButtonLabel'), function () {
      exitSemiFullscreen();
    });

    $bar.append(proceedButton);
    $bar.append(fullscreenButton);
    $mainForm.prepend($bar);
  }

  /**
   * Helper for creating buttons.
   *
   * @private
   * @param {string} id
   * @param {string} text
   * @param {function} clickHandler
   * @param {string} ariaLabel
   * @return {Element}
   */
  const createButton = function (id, text, clickHandler, ariaLabel) {
    if (ariaLabel === undefined) {
      ariaLabel = text;
    }

    const button = document.createElement('button');
    button.setAttribute('type', 'button');
    button.classList.add('h5peditor-form-manager-button');
    button.classList.add('h5peditor-form-manager-' + id);
    button.setAttribute('aria-label', ariaLabel);
    button.addEventListener('click', clickHandler);

    // Create special inner filler to avoid focus from pointer devices.
    const content = document.createElement('span');
    content.classList.add('h5peditor-form-manager-button-inner');
    content.innerText = text
    content.tabIndex = -1;
    button.appendChild(content);

    return button;
  };

  return FullscreenBar;
}(ns.jQuery));
