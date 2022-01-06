/* global H5PAdminIntegration H5PUtils */
var H5PLibraryDetails = H5PLibraryDetails || {};

(function ($) {

  H5PLibraryDetails.PAGER_SIZE = 20;
  /**
   * Initializing
   */
  H5PLibraryDetails.init = function () {
    H5PLibraryDetails.$adminContainer = H5P.jQuery(H5PAdminIntegration.containerSelector);
    H5PLibraryDetails.library = H5PAdminIntegration.libraryInfo;

    // currentContent holds the current list if data (relevant for filtering)
    H5PLibraryDetails.currentContent = H5PLibraryDetails.library.content;

    // The current page index (for pager)
    H5PLibraryDetails.currentPage = 0;

    // The current filter
    H5PLibraryDetails.currentFilter = '';

    // We cache the filtered results, so we don't have to do unneccessary searches
    H5PLibraryDetails.filterCache = [];

    // Append library info
    H5PLibraryDetails.$adminContainer.append(H5PLibraryDetails.createLibraryInfo());

    // Append node list
    H5PLibraryDetails.$adminContainer.append(H5PLibraryDetails.createContentElement());
  };

  /**
   * Create the library details view
   */
  H5PLibraryDetails.createLibraryInfo = function () {
    var $libraryInfo = $('<div class="h5p-library-info"></div>');

    $.each(H5PLibraryDetails.library.info, function (title, value) {
      $libraryInfo.append(H5PUtils.createLabeledField(title, value));
    });

    return $libraryInfo;
  };

  /**
   * Create the content list with searching and paging
   */
  H5PLibraryDetails.createContentElement = function () {
    if (H5PLibraryDetails.library.notCached !== undefined) {
      return H5PUtils.getRebuildCache(H5PLibraryDetails.library.notCached);
    }

    if (H5PLibraryDetails.currentContent === undefined) {
      H5PLibraryDetails.$content = $('<div class="h5p-content empty">' + H5PLibraryDetails.library.translations.noContent + '</div>');
    }
    else {
      H5PLibraryDetails.$content = $('<div class="h5p-content"><h3>' + H5PLibraryDetails.library.translations.contentHeader + '</h3></div>');
      H5PLibraryDetails.createSearchElement();
      H5PLibraryDetails.createPageSizeSelector();
      H5PLibraryDetails.createContentTable();
      H5PLibraryDetails.createPagerElement();
      return H5PLibraryDetails.$content;
    }
  };

  /**
   * Creates the content list
   */
  H5PLibraryDetails.createContentTable = function () {
    // Remove it if it exists:
    if (H5PLibraryDetails.$contentTable) {
      H5PLibraryDetails.$contentTable.remove();
    }

    H5PLibraryDetails.$contentTable = H5PUtils.createTable();

    var i = (H5PLibraryDetails.currentPage*H5PLibraryDetails.PAGER_SIZE);
    var lastIndex = (i+H5PLibraryDetails.PAGER_SIZE);

    if (lastIndex > H5PLibraryDetails.currentContent.length) {
      lastIndex = H5PLibraryDetails.currentContent.length;
    }
    for (; i<lastIndex; i++) {
      var content = H5PLibraryDetails.currentContent[i];
      H5PLibraryDetails.$contentTable.append(H5PUtils.createTableRow(['<a href="' + content.url + '">' + content.title + '</a>']));
    }

    // Appends it to the browser DOM
    H5PLibraryDetails.$contentTable.insertAfter(H5PLibraryDetails.$search);
  };

  /**
   * Creates the pager element on the bottom of the list
   */
  H5PLibraryDetails.createPagerElement = function () {
    H5PLibraryDetails.$previous = $('<button type="button" class="previous h5p-admin"><</button>');
    H5PLibraryDetails.$next = $('<button type="button" class="next h5p-admin">></button>');

    H5PLibraryDetails.$previous.on('click', function () {
      if (H5PLibraryDetails.$previous.hasClass('disabled')) {
        return;
      }

      H5PLibraryDetails.currentPage--;
      H5PLibraryDetails.updatePager();
      H5PLibraryDetails.createContentTable();
    });

    H5PLibraryDetails.$next.on('click', function () {
      if (H5PLibraryDetails.$next.hasClass('disabled')) {
        return;
      }

      H5PLibraryDetails.currentPage++;
      H5PLibraryDetails.updatePager();
      H5PLibraryDetails.createContentTable();
    });

    // This is the Page x of y widget:
    H5PLibraryDetails.$pagerInfo = $('<span class="pager-info"></span>');

    H5PLibraryDetails.$pager = $('<div class="h5p-content-pager"></div>').append(H5PLibraryDetails.$previous, H5PLibraryDetails.$pagerInfo, H5PLibraryDetails.$next);
    H5PLibraryDetails.$content.append(H5PLibraryDetails.$pager);

    H5PLibraryDetails.$pagerInfo.on('click', function () {
      var width = H5PLibraryDetails.$pagerInfo.innerWidth();
      H5PLibraryDetails.$pagerInfo.hide();

      // User has updated the pageNumber
      var pageNumerUpdated = function () {
        var newPageNum = $gotoInput.val()-1;
        var intRegex = /^\d+$/;

        $goto.remove();
        H5PLibraryDetails.$pagerInfo.css({display: 'inline-block'});

        // Check if input value is valid, and that it has actually changed
        if (!(intRegex.test(newPageNum) && newPageNum >= 0 && newPageNum < H5PLibraryDetails.getNumPages() && newPageNum != H5PLibraryDetails.currentPage)) {
          return;
        }

        H5PLibraryDetails.currentPage = newPageNum;
        H5PLibraryDetails.updatePager();
        H5PLibraryDetails.createContentTable();
      };

      // We create an input box where the user may type in the page number
      // he wants to be displayed.
      // Reson for doing this is when user has ten-thousands of elements in list,
      // this is the easiest way of getting to a specified page
      var $gotoInput = $('<input/>', {
        type: 'number',
        min : 1,
        max: H5PLibraryDetails.getNumPages(),
        on: {
          // Listen to blur, and the enter-key:
          'blur': pageNumerUpdated,
          'keyup': function (event) {
            if (event.keyCode === 13) {
              pageNumerUpdated();
            }
          }
        }
      }).css({width: width});
      var $goto = $('<span/>', {
        'class': 'h5p-pager-goto'
      }).css({width: width}).append($gotoInput).insertAfter(H5PLibraryDetails.$pagerInfo);

      $gotoInput.focus();
    });

    H5PLibraryDetails.updatePager();
  };

  /**
   * Calculates number of pages
   */
  H5PLibraryDetails.getNumPages = function () {
    return Math.ceil(H5PLibraryDetails.currentContent.length / H5PLibraryDetails.PAGER_SIZE);
  };

  /**
   * Update the pager text, and enables/disables the next and previous buttons as needed
   */
  H5PLibraryDetails.updatePager = function () {
    H5PLibraryDetails.$pagerInfo.css({display: 'inline-block'});

    if (H5PLibraryDetails.getNumPages() > 0) {
      var message = H5PUtils.translateReplace(H5PLibraryDetails.library.translations.pageXOfY, {
        '$x': (H5PLibraryDetails.currentPage+1),
        '$y': H5PLibraryDetails.getNumPages()
      });
      H5PLibraryDetails.$pagerInfo.html(message);
    }
    else {
      H5PLibraryDetails.$pagerInfo.html('');
    }

    H5PLibraryDetails.$previous.toggleClass('disabled', H5PLibraryDetails.currentPage <= 0);
    H5PLibraryDetails.$next.toggleClass('disabled', H5PLibraryDetails.currentContent.length < (H5PLibraryDetails.currentPage+1)*H5PLibraryDetails.PAGER_SIZE);
  };

  /**
   * Creates the search element
   */
  H5PLibraryDetails.createSearchElement = function () {

    H5PLibraryDetails.$search = $('<div class="h5p-content-search"><input placeholder="' + H5PLibraryDetails.library.translations.filterPlaceholder + '" type="search"></div>');

    var performSeach = function () {
      var searchString = $('.h5p-content-search > input').val();

      // If search string same as previous, just do nothing
      if (H5PLibraryDetails.currentFilter === searchString) {
        return;
      }

      if (searchString.trim().length === 0) {
        // If empty search, use the complete list
        H5PLibraryDetails.currentContent = H5PLibraryDetails.library.content;
      }
      else if (H5PLibraryDetails.filterCache[searchString]) {
        // If search is cached, no need to filter
        H5PLibraryDetails.currentContent = H5PLibraryDetails.filterCache[searchString];
      }
      else {
        var listToFilter = H5PLibraryDetails.library.content;

        // Check if we can filter the already filtered results (for performance)
        if (searchString.length > 1 && H5PLibraryDetails.currentFilter === searchString.substr(0, H5PLibraryDetails.currentFilter.length)) {
          listToFilter = H5PLibraryDetails.currentContent;
        }
        H5PLibraryDetails.currentContent = $.grep(listToFilter, function (content) {
          return content.title && content.title.match(new RegExp(searchString, 'i'));
        });
      }

      H5PLibraryDetails.currentFilter = searchString;
      // Cache the current result
      H5PLibraryDetails.filterCache[searchString] = H5PLibraryDetails.currentContent;
      H5PLibraryDetails.currentPage = 0;
      H5PLibraryDetails.createContentTable();

      // Display search results:
      if (H5PLibraryDetails.$searchResults) {
        H5PLibraryDetails.$searchResults.remove();
      }
      if (searchString.trim().length > 0) {
        H5PLibraryDetails.$searchResults = $('<span class="h5p-admin-search-results">' + H5PLibraryDetails.currentContent.length + ' hits on ' + H5PLibraryDetails.currentFilter + '</span>');
        H5PLibraryDetails.$search.append(H5PLibraryDetails.$searchResults);
      }
      H5PLibraryDetails.updatePager();
    };

    var inputTimer;
    $('input', H5PLibraryDetails.$search).on('change keypress paste input', function () {
      // Here we start the filtering
      // We wait at least 500 ms after last input to perform search
      if (inputTimer) {
        clearTimeout(inputTimer);
      }

      inputTimer = setTimeout( function () {
        performSeach();
      }, 500);
    });

    H5PLibraryDetails.$content.append(H5PLibraryDetails.$search);
  };

  /**
   * Creates the page size selector
   */
  H5PLibraryDetails.createPageSizeSelector = function () {
    H5PLibraryDetails.$search.append('<div class="h5p-admin-pager-size-selector">' + H5PLibraryDetails.library.translations.pageSizeSelectorLabel + ':<span data-page-size="10">10</span><span class="selected" data-page-size="20">20</span><span data-page-size="50">50</span><span data-page-size="100">100</span><span data-page-size="200">200</span></div>');

    // Listen to clicks on the page size selector:
    $('.h5p-admin-pager-size-selector > span', H5PLibraryDetails.$search).on('click', function () {
      H5PLibraryDetails.PAGER_SIZE = $(this).data('page-size');
      $('.h5p-admin-pager-size-selector > span', H5PLibraryDetails.$search).removeClass('selected');
      $(this).addClass('selected');
      H5PLibraryDetails.currentPage = 0;
      H5PLibraryDetails.createContentTable();
      H5PLibraryDetails.updatePager();
    });
  };

  // Initialize me:
  $(document).ready(function () {
    if (!H5PLibraryDetails.initialized) {
      H5PLibraryDetails.initialized = true;
      H5PLibraryDetails.init();
    }
  });

})(H5P.jQuery);
