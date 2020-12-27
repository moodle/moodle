/* global H5PAdminIntegration H5PUtils */
var H5PLibraryList = H5PLibraryList || {};

(function ($) {

  /**
   * Initializing
   */
  H5PLibraryList.init = function () {
    var $adminContainer = H5P.jQuery(H5PAdminIntegration.containerSelector).html('');

    var libraryList = H5PAdminIntegration.libraryList;
    if (libraryList.notCached) {
      $adminContainer.append(H5PUtils.getRebuildCache(libraryList.notCached));
    }

    // Create library list
    $adminContainer.append(H5PLibraryList.createLibraryList(H5PAdminIntegration.libraryList));
  };

  /**
   * Create the library list
   *
   * @param {object} libraries List of libraries and headers
   */
  H5PLibraryList.createLibraryList = function (libraries) {
    var t = H5PAdminIntegration.l10n;
    if (libraries.listData === undefined || libraries.listData.length === 0) {
      return $('<div>' + t.NA + '</div>');
    }

    // Create table
    var $table = H5PUtils.createTable(libraries.listHeaders);
    $table.addClass('libraries');

    // Add libraries
    $.each (libraries.listData, function (index, library) {
      var $libraryRow = H5PUtils.createTableRow([
        library.title,
        '<input class="h5p-admin-restricted" type="checkbox"/>',
        {
          text: library.numContent,
          class: 'h5p-admin-center'
        },
        {
          text: library.numContentDependencies,
          class: 'h5p-admin-center'
        },
        {
          text: library.numLibraryDependencies,
          class: 'h5p-admin-center'
        },
        '<div class="h5p-admin-buttons-wrapper">' +
            '<button class="h5p-admin-upgrade-library"></button>' +
            (library.detailsUrl ? '<button class="h5p-admin-view-library" title="' + t.viewLibrary + '"></button>' : '') +
            (library.deleteUrl ? '<button class="h5p-admin-delete-library"></button>' : '') +
            '</div>'
      ]);

      H5PLibraryList.addRestricted($('.h5p-admin-restricted', $libraryRow), library.restrictedUrl, library.restricted);

      var hasContent = !(library.numContent === '' || library.numContent === 0);
      if (library.upgradeUrl === null) {
        $('.h5p-admin-upgrade-library', $libraryRow).remove();
      }
      else if (library.upgradeUrl === false || !hasContent) {
        $('.h5p-admin-upgrade-library', $libraryRow).attr('disabled', true);
      }
      else {
        $('.h5p-admin-upgrade-library', $libraryRow).attr('title', t.upgradeLibrary).click(function () {
          window.location.href = library.upgradeUrl;
        });
      }

      // Open details view when clicked
      $('.h5p-admin-view-library', $libraryRow).on('click', function () {
        window.location.href = library.detailsUrl;
      });

      var $deleteButton = $('.h5p-admin-delete-library', $libraryRow);
      if (libraries.notCached !== undefined ||
          hasContent ||
          (library.numContentDependencies !== '' &&
           library.numContentDependencies !== 0) ||
          (library.numLibraryDependencies !== '' &&
           library.numLibraryDependencies !== 0)) {
        // Disabled delete if content.
        $deleteButton.attr('disabled', true);
      }
      else {
        // Go to delete page om click.
        $deleteButton.attr('title', t.deleteLibrary).on('click', function () {
          window.location.href = library.deleteUrl;
        });
      }

      $table.append($libraryRow);
    });

    return $table;
  };

  H5PLibraryList.addRestricted = function ($checkbox, url, selected) {
    if (selected === null) {
      $checkbox.remove();
    }
    else {
      $checkbox.change(function () {
        $checkbox.attr('disabled', true);

        $.ajax({
          dataType: 'json',
          url: url,
          cache: false
        }).fail(function () {
          $checkbox.attr('disabled', false);

          // Reset
          $checkbox.attr('checked', !$checkbox.is(':checked'));
        }).done(function (result) {
          url = result.url;
          $checkbox.attr('disabled', false);
        });
      });

      if (selected) {
        $checkbox.attr('checked', true);
      }
    }
  };

  // Initialize me:
  $(document).ready(function () {
    if (!H5PLibraryList.initialized) {
      H5PLibraryList.initialized = true;
      H5PLibraryList.init();
    }
  });

})(H5P.jQuery);
