/* global H5PDisableHubData */

/**
 * Global data for disable hub functionality
 *
 * @typedef {object} H5PDisableHubData Data passed in from the backend
 *
 * @property {string} selector Selector for the disable hub check-button
 * @property {string} overlaySelector Selector for the element that the confirmation dialog will mask
 * @property {Array} errors Errors found with the current server setup
 *
 * @property {string} header Header of the confirmation dialog
 * @property {string} confirmationDialogMsg Body of the confirmation dialog
 * @property {string} cancelLabel Cancel label of the confirmation dialog
 * @property {string} confirmLabel Confirm button label of the confirmation dialog
 *
 */
/**
 * Utility that makes it possible to force the user to confirm that he really
 * wants to use the H5P hub without proper server settings.
 */
(function ($) {

  $(document).on('ready', function () {

    // No data found
    if (!H5PDisableHubData) {
      return;
    }

    // No errors found, no need for confirmation dialog
    if (!H5PDisableHubData.errors || !H5PDisableHubData.errors.length) {
      return;
    }

    H5PDisableHubData.selector = H5PDisableHubData.selector ||
      '.h5p-settings-disable-hub-checkbox';
    H5PDisableHubData.overlaySelector = H5PDisableHubData.overlaySelector ||
      '.h5p-settings-container';

    var dialogHtml = '<div>' +
      '<p>' + H5PDisableHubData.errors.join('</p><p>') + '</p>' +
      '<p>' + H5PDisableHubData.confirmationDialogMsg + '</p>';

    // Create confirmation dialog, make sure to include translations
    var confirmationDialog = new H5P.ConfirmationDialog({
      headerText: H5PDisableHubData.header,
      dialogText: dialogHtml,
      cancelText: H5PDisableHubData.cancelLabel,
      confirmText: H5PDisableHubData.confirmLabel
    }).appendTo($(H5PDisableHubData.overlaySelector).get(0));

    confirmationDialog.on('confirmed', function () {
      enableButton.get(0).checked = true;
    });

    confirmationDialog.on('canceled', function () {
      enableButton.get(0).checked = false;
    });

    var enableButton = $(H5PDisableHubData.selector);
    enableButton.change(function () {
      if ($(this).is(':checked')) {
        confirmationDialog.show(enableButton.offset().top);
      }
    });
  });
})(H5P.jQuery);
