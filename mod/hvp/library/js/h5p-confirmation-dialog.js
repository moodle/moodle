/*global H5P*/
H5P.ConfirmationDialog = (function (EventDispatcher) {
  "use strict";

  /**
   * Create a confirmation dialog
   *
   * @param [options] Options for confirmation dialog
   * @param [options.instance] Instance that uses confirmation dialog
   * @param [options.headerText] Header text
   * @param [options.dialogText] Dialog text
   * @param [options.cancelText] Cancel dialog button text
   * @param [options.confirmText] Confirm dialog button text
   * @constructor
   */
  function ConfirmationDialog(options) {
    EventDispatcher.call(this);
    var self = this;

    // Make sure confirmation dialogs have unique id
    H5P.ConfirmationDialog.uniqueId += 1;
    var uniqueId = H5P.ConfirmationDialog.uniqueId;

    // Default options
    options = options || {};
    options.headerText = options.headerText || H5P.t('confirmDialogHeader');
    options.dialogText = options.dialogText || H5P.t('confirmDialogBody');
    options.cancelText = options.cancelText || H5P.t('cancelLabel');
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
     * @param {Event} e
     */
    function dialogCanceled(e) {
      self.hide();
      self.trigger('canceled');
      e.preventDefault();
    }

    /**
     * Flow focus to element
     * @param {HTMLElement} element Next element to be focused
     * @param {Event} e Original tab event
     */
    function flowTo(element, e) {
      element.focus();
      e.preventDefault();
    }

    // Offset of exit button
    var exitButtonOffset = 2 * 16;
    var shadowOffset = 8;

    // Determine if we are too large for our container and must resize
    var resizeIFrame = false;

    // Create background
    var popupBackground = document.createElement('div');
    popupBackground.classList
      .add('h5p-confirmation-dialog-background', 'hidden', 'hiding');

    // Create outer popup
    var popup = document.createElement('div');
    popup.classList.add('h5p-confirmation-dialog-popup', 'hidden');
    popup.setAttribute('role', 'dialog');
    popup.setAttribute('aria-labelledby', 'h5p-confirmation-dialog-dialog-text-' + uniqueId);
    popupBackground.appendChild(popup);
    popup.addEventListener('keydown', function (e) {
      if (e.which === 27) {// Esc key
        // Exit dialog
        dialogCanceled(e);
      }
    });

    // Popup header
    var header = document.createElement('div');
    header.classList.add('h5p-confirmation-dialog-header');
    popup.appendChild(header);

    // Header text
    var headerText = document.createElement('div');
    headerText.classList.add('h5p-confirmation-dialog-header-text');
    headerText.innerHTML = options.headerText;
    header.appendChild(headerText);

    // Popup body
    var body = document.createElement('div');
    body.classList.add('h5p-confirmation-dialog-body');
    popup.appendChild(body);

    // Popup text
    var text = document.createElement('div');
    text.classList.add('h5p-confirmation-dialog-text');
    text.innerHTML = options.dialogText;
    text.id = 'h5p-confirmation-dialog-dialog-text-' + uniqueId;
    body.appendChild(text);

    // Popup buttons
    var buttons = document.createElement('div');
    buttons.classList.add('h5p-confirmation-dialog-buttons');
    body.appendChild(buttons);

    // Cancel button
    var cancelButton = document.createElement('button');
    cancelButton.classList.add('h5p-core-cancel-button');
    cancelButton.textContent = options.cancelText;

    // Confirm button
    var confirmButton = document.createElement('button');
    confirmButton.classList.add('h5p-core-button');
    confirmButton.classList.add('h5p-confirmation-dialog-confirm-button');
    confirmButton.textContent = options.confirmText;

    // Exit button
    var exitButton = document.createElement('button');
    exitButton.classList.add('h5p-confirmation-dialog-exit');
    exitButton.setAttribute('aria-hidden', 'true');
    exitButton.tabIndex = -1;
    exitButton.title = options.cancelText;

    // Cancel handler
    cancelButton.addEventListener('click', dialogCanceled);
    cancelButton.addEventListener('keydown', function (e) {
      if (e.which === 32) { // Space
        dialogCanceled(e);
      }
      else if (e.which === 9 && e.shiftKey) { // Shift-tab
        flowTo(confirmButton, e);
      }
    });
    buttons.appendChild(cancelButton);

    // Confirm handler
    confirmButton.addEventListener('click', dialogConfirmed);
    confirmButton.addEventListener('keydown', function (e) {
      if (e.which === 32) { // Space
        dialogConfirmed(e);
      }
      else if (e.which === 9 && !e.shiftKey) { // Tab
        flowTo(cancelButton, e);
      }
    });
    buttons.appendChild(confirmButton);

    // Exit handler
    exitButton.addEventListener('click', dialogCanceled);
    exitButton.addEventListener('keydown', function (e) {
      if (e.which === 32) { // Space
        dialogCanceled(e);
      }
    });
    popup.appendChild(exitButton);

    // Wrapper element
    var wrapperElement;

    // Focus capturing
    var focusPredator;

    // Maintains hidden state of elements
    var wrapperSiblingsHidden = [];
    var popupSiblingsHidden = [];

    // Element with focus before dialog
    var previouslyFocused;

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
     * Capture the focus element, send it to confirmation button
     * @param {Event} e Original focus event
     */
    var captureFocus = function (e) {
      if (!popupBackground.contains(e.target)) {
        e.preventDefault();
        confirmButton.focus();
      }
    };

    /**
     * Hide siblings of element from assistive technology
     *
     * @param {HTMLElement} element
     * @returns {Array} The previous hidden state of all siblings
     */
    var hideSiblings = function (element) {
      var hiddenSiblings = [];
      var siblings = element.parentNode.children;
      var i;
      for (i = 0; i < siblings.length; i += 1) {
        // Preserve hidden state
        hiddenSiblings[i] = siblings[i].getAttribute('aria-hidden') ?
          true : false;

        if (siblings[i] !== element) {
          siblings[i].setAttribute('aria-hidden', true);
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
    var restoreSiblings = function (element, hiddenSiblings) {
      var siblings = element.parentNode.children;
      var i;
      for (i = 0; i < siblings.length; i += 1) {
        if (siblings[i] !== element && !hiddenSiblings[i]) {
          siblings[i].removeAttribute('aria-hidden');
        }
      }
    };

    /**
     * Start capturing focus of parent and send it to dialog
     */
    var startCapturingFocus = function () {
      focusPredator = wrapperElement.parentNode || wrapperElement;
      focusPredator.addEventListener('focus', captureFocus, true);
    };

    /**
     * Clean up event listener for capturing focus
     */
    var stopCapturingFocus = function () {
      focusPredator.removeAttribute('aria-hidden');
      focusPredator.removeEventListener('focus', captureFocus, true);
    };

    /**
     * Hide siblings in underlay from assistive technologies
     */
    var disableUnderlay = function () {
      wrapperSiblingsHidden = hideSiblings(wrapperElement);
      popupSiblingsHidden = hideSiblings(popupBackground);
    };

    /**
     * Restore state of underlay for assistive technologies
     */
    var restoreUnderlay = function () {
      restoreSiblings(wrapperElement, wrapperSiblingsHidden);
      restoreSiblings(popupBackground, popupSiblingsHidden);
    };

    /**
     * Fit popup to container. Makes sure it doesn't overflow.
     * @params {number} [offsetTop] Offset of popup
     */
    var fitToContainer = function (offsetTop) {
      var popupOffsetTop = parseInt(popup.style.top, 10);
      if (offsetTop) {
        popupOffsetTop = offsetTop;
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
      popup.style.top = popupOffsetTop + 'px';
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
      startCapturingFocus();
      disableUnderlay();
      popupBackground.classList.remove('hidden');
      fitToContainer(offsetTop);
      setTimeout(function () {
        popup.classList.remove('hidden');
        popupBackground.classList.remove('hiding');

        setTimeout(function () {
          // Focus confirm button
          confirmButton.focus();

          // Resize iFrame if necessary
          if (resizeIFrame && options.instance) {
            var minHeight = parseInt(popup.offsetHeight, 10) +
              exitButtonOffset + (2 * shadowOffset);
            self.setViewPortMinimumHeight(minHeight);
            options.instance.trigger('resize');
            resizeIFrame = false;
          }
        }, 100);
      }, 0);

      return this;
    };

    /**
     * Hide confirmation dialog
     * @returns {H5P.ConfirmationDialog}
     */
    this.hide = function () {
      popupBackground.classList.add('hiding');
      popup.classList.add('hidden');

      // Restore focus
      stopCapturingFocus();
      previouslyFocused.focus();
      restoreUnderlay();
      setTimeout(function () {
        popupBackground.classList.add('hidden');
        wrapperElement.removeChild(popupBackground);
        self.setViewPortMinimumHeight(null);
      }, 100);

      return this;
    };

    /**
     * Sets the minimum height of the view port
     *
     * @param {number|null} minHeight
     */
    this.setViewPortMinimumHeight = function(minHeight) {
      var container = document.querySelector('.h5p-container') || document.body;
      container.style.minHeight = (typeof minHeight === 'number') ? (minHeight + 'px') : minHeight;
    };
  }

  ConfirmationDialog.prototype = Object.create(EventDispatcher.prototype);
  ConfirmationDialog.prototype.constructor = ConfirmationDialog;

  return ConfirmationDialog;

}(H5P.EventDispatcher));

H5P.ConfirmationDialog.uniqueId = -1;
