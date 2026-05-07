/* eslint-disable no-param-reassign */
H5P.ConfirmationDialog = (function (EventDispatcher) {
  /**
   * Create a confirmation dialog
   *
   * @param [options] Options for confirmation dialog
   * @param [options.instance] Instance that uses confirmation dialog
   * @param [options.headerText] Header text
   * @param [options.dialogText] Dialog text
   * @param [options.cancelText] Cancel dialog button text
   * @param [options.confirmText] Confirm dialog button text
   * @param [options.hideCancel] Hide cancel button
   * @param [options.hideExit] Hide exit button
   * @param [options.skipRestoreFocus] Skip restoring focus when hiding the dialog
   * @param [options.classes] Extra classes for popup
   * @param [options.theme] Whether to use the new theme (true) or the old design (false)
   * @constructor
   */
  function ConfirmationDialog(options) {
    EventDispatcher.call(this);
    const self = this;

    // Make sure confirmation dialogs have unique id
    H5P.ConfirmationDialog.uniqueId += 1;
    const { uniqueId } = H5P.ConfirmationDialog;

    // Default options
    options = options || {};
    options.headerText = options.headerText || H5P.t('confirmDialogHeader');
    options.dialogText = options.dialogText || H5P.t('confirmDialogBody');
    options.cancelText = options.cancelText || H5P.t('cancelLabel');
    options.closeText = options.closeText || H5P.t('close');
    options.confirmText = options.confirmText || H5P.t('confirmLabel');

    /**
     * Handle confirming event
     * @param {Event} e
     */
    function dialogConfirmed(e) {
      self.hide();
      self.trigger('confirmed');
      e.preventDefault();
    }

    /**
     * Handle dialog canceled
     * @param {Event} e Event.
     * @param {object} [options] Options.
     * @param {boolean} [options.wasExplicitChoice] 
     * True if user chose cancel explicitly, otherwise (close, esc) false.
     */
    function dialogCanceled(e, options = {}) {
      self.hide();
      self.trigger('canceled', { wasExplicitChoice: options.wasExplicitChoice ?? false });
      e.preventDefault();
    }

    /**
     * Handle tabbing through buttons with focus trapping.
     * @param {KeyboardEvent} event Keyboard event.
     */
    function handleTabbing(event) {
      if (event.key !== 'Tab') {
        return;
      }

      event.preventDefault();

      const currentIndex = focusableButtons.indexOf(event.target);
      const offset = event.shiftKey ? -1 : 1;
      const nextIndex = (currentIndex + offset + focusableButtons.length) % focusableButtons.length;

      focusableButtons[nextIndex].focus();
    }

    // Offset of exit button
    const exitButtonOffset = 2 * 16;
    const shadowOffset = 8;

    // Determine if we are too large for our container and must resize
    let resizeIFrame = false;

    // Create background
    const popupBackground = document.createElement('div');
    popupBackground.classList
      .add('h5p-confirmation-dialog-background', 'hidden', 'hiding');

    if (options.theme) {
      popupBackground.classList.add('h5p-theme');
    }

    if (window.H5PEditor) {
      popupBackground.classList.add('h5peditor');

      if (H5PIntegration.theme?.density) {
        popupBackground.classList.add(`h5p-${H5PIntegration.theme.density}`);
      }
    }

    // Create outer popup
    const popup = document.createElement('div');
    popup.classList.add('h5p-confirmation-dialog-popup', 'hidden');
    if (options.classes) {
      options.classes.forEach((popupClass) => {
        popup.classList.add(popupClass);
      });
    }

    popup.setAttribute('role', 'alertdialog');
    popup.setAttribute('aria-modal', 'true');
    popup.setAttribute('aria-labelledby', `h5p-confirmation-dialog-header-text-${uniqueId}`);
    popup.setAttribute('aria-describedby', `h5p-confirmation-dialog-text-${uniqueId}`);
    popupBackground.appendChild(popup);
    popup.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { // Esc key
        // Exit dialog
        dialogCanceled(e);
      }
    });
    popup.addEventListener('keydown', (event) => {
      handleTabbing(event);
    });

    // Popup header
    const header = document.createElement('div');
    header.classList.add('h5p-confirmation-dialog-header');
    popup.appendChild(header);

    // Header text
    const headerText = document.createElement('div');
    headerText.classList.add('h5p-confirmation-dialog-header-text');
    headerText.id = `h5p-confirmation-dialog-dialog-header-text-${uniqueId}`;
    headerText.innerHTML = options.headerText;
    header.appendChild(headerText);

    // Popup body
    const body = document.createElement('div');
    body.classList.add('h5p-confirmation-dialog-body');
    popup.appendChild(body);

    // Popup text
    const text = document.createElement('div');
    text.classList.add('h5p-confirmation-dialog-text');
    text.innerHTML = options.dialogText;
    text.id = `h5p-confirmation-dialog-dialog-text-${uniqueId}`;
    body.appendChild(text);

    // Popup buttons
    const buttons = document.createElement('div');
    buttons.classList.add('h5p-confirmation-dialog-buttons');
    body.appendChild(buttons);

    // Cancel button
    if (!options.hideCancel) {
      const cancelButton = document.createElement('button');
      if (!options.theme) {
        cancelButton.classList.add('h5p-core-cancel-button');
      }
      else {
        cancelButton.classList.add('h5p-theme-button', 'h5p-theme-secondary-cta');
        cancelButton.classList.add('h5p-theme-cancel');
      }
      const cancelText = document.createElement('span');
      cancelText.textContent = options.cancelText;
      cancelButton.appendChild(cancelText);
      cancelButton.addEventListener('click', (event) => {
        dialogCanceled(event, { wasExplicitChoice: true });
      });
      buttons.appendChild(cancelButton);
    }
    else {
      // Center remaining buttons
      buttons.classList.add('center');
    }

    // Confirm button
    const confirmButton = document.createElement('button');
    if (!options.theme) {
      confirmButton.classList.add('h5p-core-button');
    }
    // confirmButton.classList.add('h5p-confirmation-dialog-confirm-button');
    confirmButton.setAttribute('aria-label', options.confirmText);

    if (options.theme) {
      confirmButton.classList.add('h5p-theme-button', 'h5p-theme-primary-cta');
      confirmButton.classList.add('h5p-theme-check');
    }

    confirmButton.addEventListener('click', dialogConfirmed);
    const confirmText = document.createElement('span');
    confirmText.textContent = options.confirmText;
    confirmButton.appendChild(confirmText);
    buttons.appendChild(confirmButton);

    let focusableButtons = [...buttons.childNodes];

    // Exit button
    if (!options.hideExit) {
      const exitButton = document.createElement('button');
      exitButton.classList.add('h5p-confirmation-dialog-exit');
      exitButton.setAttribute('aria-label', options.closeText);
      exitButton.addEventListener('click', dialogCanceled);
      if (options.theme) {
        header.appendChild(exitButton);
      }
      else {
        popup.appendChild(exitButton);
      }
      focusableButtons.push(exitButton);
    }

    // Wrapper element
    let wrapperElement;

    // Maintains hidden state of elements
    let wrapperSiblingsHidden = [];
    let popupSiblingsHidden = [];

    // Element with focus before dialog
    let previouslyFocused;

    /**
     * Set parent of confirmation dialog
     * @param {HTMLElement} wrapper
     * @returns {H5P.ConfirmationDialog}
     */
    this.appendTo = function (wrapper) {
      wrapperElement = wrapper;
      return this;
    };

    /**
     * Hide siblings of element from assistive technology
     *
     * @param {HTMLElement} element
     * @returns {Array} The previous hidden state of all siblings
     */
    const hideSiblings = function (element) {
      const hiddenSiblings = [];
      const siblings = element.parentNode.children;
      let i;
      for (i = 0; i < siblings.length; i += 1) {
        // Preserve hidden state
        hiddenSiblings[i] = !!siblings[i].getAttribute('aria-hidden');

        if (siblings[i] !== element) {
          if (siblings[i].getAttribute('aria-live')) {
            siblings[i].setAttribute('aria-busy', true);
          }
          else {
            siblings[i].setAttribute('aria-hidden', true);
          }
        }
      }
      return hiddenSiblings;
    };

    /**
     * Restores assistive technology state of element's siblings
     *
     * @param {HTMLElement} element
     * @param {Array} hiddenSiblings Hidden state of all siblings
     */
    const restoreSiblings = function (element, hiddenSiblings) {
      const siblings = element.parentNode.children;
      let i;
      for (i = 0; i < siblings.length; i += 1) {
        if (siblings[i] !== element && !hiddenSiblings[i]) {
          if (siblings[i].getAttribute('aria-live')) {
            siblings[i].setAttribute('aria-busy', false);
          }
          else {
            siblings[i].removeAttribute('aria-hidden');
          }
        }
      }
    };

    /**
     * Hide siblings in underlay from assistive technologies
     */
    const disableUnderlay = function () {
      wrapperSiblingsHidden = hideSiblings(wrapperElement);
      popupSiblingsHidden = hideSiblings(popupBackground);
    };

    /**
     * Restore state of underlay for assistive technologies
     */
    const restoreUnderlay = function () {
      restoreSiblings(wrapperElement, wrapperSiblingsHidden);
      restoreSiblings(popupBackground, popupSiblingsHidden);
    };

    /**
     * Fit popup to container. Makes sure it doesn't overflow.
     * @params {number} [offsetTop] Offset of popup
     */
    const fitToContainer = function (offsetTop) {
      let popupOffsetTop = parseInt(popup.style.top, 10);
      if (offsetTop !== undefined) {
        popupOffsetTop = offsetTop;
      }

      if (!popupOffsetTop) {
        popupOffsetTop = 0;
      }

      // Overflows height
      if (popupOffsetTop + popup.offsetHeight > wrapperElement.offsetHeight) {
        popupOffsetTop = wrapperElement.offsetHeight - popup.offsetHeight - shadowOffset;
      }

      if (popupOffsetTop - exitButtonOffset <= 0) {
        popupOffsetTop = exitButtonOffset + shadowOffset;

        // We are too big and must resize
        resizeIFrame = true;
      }
      popup.style.top = `${popupOffsetTop}px`;
    };

    /**
     * Show confirmation dialog
     * @params {number} offsetTop Offset top
     * @returns {H5P.ConfirmationDialog}
     */
    this.show = function (offsetTop) {
      // Capture focused item
      previouslyFocused = document.activeElement;
      wrapperElement.appendChild(popupBackground);
      popupBackground.classList.remove('hidden');
      fitToContainer(offsetTop);
      popup.classList.remove('hidden');
      popupBackground.addEventListener('transitionend', () => {
        buttons.firstChild.focus();
      }, { once: true });
      popupBackground.classList.remove('hiding');
      disableUnderlay();

      // Resize iFrame if necessary
      if (resizeIFrame && options.instance) {
        const minHeight = parseInt(popup.offsetHeight, 10)
          + exitButtonOffset + (2 * shadowOffset);
        self.setViewPortMinimumHeight(minHeight);
        options.instance.trigger('resize');
        resizeIFrame = false;
      }

      // Detect if the user prefers reduced motion, because in that case
      // we cannot rely on transitionend triggering and we need to manually
      // focus the buttons. It should also be checked for each show, since a
      // user may change this setting at any time.
      const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
      if (prefersReducedMotion.matches) {
        buttons.firstChild.focus();
      }

      return this;
    };

    /**
     * Hide confirmation dialog
     * @returns {H5P.ConfirmationDialog}
     */
    this.hide = function () {
      restoreUnderlay();
      popupBackground.classList.add('hiding');
      popup.classList.add('hidden');

      // Restore focus
      if (!options.skipRestoreFocus) {
        previouslyFocused.focus();
      }
      popupBackground.classList.add('hidden');
      wrapperElement.removeChild(popupBackground);
      self.setViewPortMinimumHeight(null);

      return this;
    };

    /**
     * Retrieve element
     *
     * @return {HTMLElement}
     */
    this.getElement = function () {
      return popup;
    };

    /**
     * Get previously focused element
     * @return {HTMLElement}
     */
    this.getPreviouslyFocused = function () {
      return previouslyFocused;
    };

    /**
     * Sets the minimum height of the view port
     *
     * @param {number|null} minHeight
     */
    this.setViewPortMinimumHeight = function (minHeight) {
      const container = document.querySelector('.h5p-container') || document.body;
      container.style.minHeight = (typeof minHeight === 'number') ? (`${minHeight}px`) : minHeight;
    };
  }

  ConfirmationDialog.prototype = Object.create(EventDispatcher.prototype);
  ConfirmationDialog.prototype.constructor = ConfirmationDialog;

  return ConfirmationDialog;
}(H5P.EventDispatcher));

H5P.ConfirmationDialog.uniqueId = -1;
