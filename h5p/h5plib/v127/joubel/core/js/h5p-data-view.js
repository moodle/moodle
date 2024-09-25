/* global H5PUtils */
var H5PDataView = (function ($) {

  /**
   * Initialize a new H5P data view.
   *
   * @class
   * @param {Object} container
   *   Element to clear out and append to.
   * @param {String} source
   *   URL to get data from. Data format: {num: 123, rows:[[1,2,3],[2,4,6]]}
   * @param {Array} headers
   *   List with column headers. Can be strings or objects with options like
   *   "text" and "sortable". E.g.
   *   [{text: 'Col 1', sortable: true}, 'Col 2', 'Col 3']
   * @param {Object} l10n
   *   Localization / translations. e.g.
   *   {
   *     loading: 'Loading data.',
   *     ajaxFailed: 'Failed to load data.',
   *     noData: "There's no data available that matches your criteria.",
   *     currentPage: 'Page $current of $total',
   *     nextPage: 'Next page',
   *     previousPage: 'Previous page',
   *     search: 'Search'
   *   }
   * @param {Object} classes
   *   Custom html classes to use on elements.
   *   e.g. {tableClass: 'fixed'}.
   * @param {Array} filters
   *   Make it possible to filter/search in the given column.
   *   e.g. [null, true, null, null] will make it possible to do a text
   *   search in column 2.
   * @param {Function} loaded
   *   Callback for when data has been loaded.
   * @param {Object} order
   */
  function H5PDataView(container, source, headers, l10n, classes, filters, loaded, order) {
    var self = this;

    self.$container = $(container).addClass('h5p-data-view').html('');

    self.source = source;
    self.headers = headers;
    self.l10n = l10n;
    self.classes = (classes === undefined ? {} : classes);
    self.filters = (filters === undefined ? [] : filters);
    self.loaded = loaded;
    self.order = order;

    self.limit = 20;
    self.offset = 0;
    self.filterOn = [];
    self.facets = {};

    // Index of column with author name; could be made more general by passing database column names and checking for position
    self.columnIdAuthor = 2;

    // Future option: Create more general solution for filter presets
    if (H5PIntegration.user && parseInt(H5PIntegration.user.canToggleViewOthersH5PContents) === 1) {
      self.updateTable([]);
      self.filterByFacet(self.columnIdAuthor, H5PIntegration.user.id, H5PIntegration.user.name || '');
    }
    else {
      self.loadData();
    }
  }

  /**
   * Load data from source URL.
   */
  H5PDataView.prototype.loadData = function () {
    var self = this;

    // Throbb
    self.setMessage(H5PUtils.throbber(self.l10n.loading));

    // Create URL
    var url = self.source;
    url += (url.indexOf('?') === -1 ? '?' : '&') + 'offset=' + self.offset + '&limit=' + self.limit;

    // Add sorting
    if (self.order !== undefined) {
      url += '&sortBy=' + self.order.by + '&sortDir=' + self.order.dir;
    }

    // Add filters
    var filtering;
    for (var i = 0; i < self.filterOn.length; i++) {
      if (self.filterOn[i] === undefined) {
        continue;
      }

      filtering = true;
      url += '&filters[' + i + ']=' + encodeURIComponent(self.filterOn[i]);
    }

    // Add facets
    for (var col in self.facets) {
      if (!self.facets.hasOwnProperty(col)) {
        continue;
      }

      url += '&facets[' + col + ']=' + self.facets[col].id;
    }

    // Fire ajax request
    $.ajax({
      dataType: 'json',
      cache: true,
      url: url
    }).fail(function () {
      // Error handling
      self.setMessage($('<p/>', {text: self.l10n.ajaxFailed}));
    }).done(function (data) {
      if (!data.rows.length) {
        self.setMessage($('<p/>', {text: filtering ? self.l10n.noData : self.l10n.empty}));
      }
      else {
        // Update table data
        self.updateTable(data.rows);
      }

      // Update pagination widget
      self.updatePagination(data.num);

      if (self.loaded !== undefined) {
        self.loaded();
      }
    });
  };

  /**
   * Display the given message to the user.
   *
   * @param {jQuery} $message wrapper with message
   */
  H5PDataView.prototype.setMessage = function ($message) {
    var self = this;

    if (self.table === undefined) {
      self.$container.html('').append($message);
    }
    else {
      self.table.setBody($message);
    }
  };

  /**
   * Update table data.
   *
   * @param {Array} rows
   */
  H5PDataView.prototype.updateTable = function (rows) {
    var self = this;

    if (self.table === undefined) {
      // Clear out container
      self.$container.html('');

      // Add filters
      self.addFilters();

      // Add toggler for others' content
      if (H5PIntegration.user && parseInt(H5PIntegration.user.canToggleViewOthersH5PContents) > 0) {
        // canToggleViewOthersH5PContents = 1 is setting for only showing current user's contents
        self.addOthersContentToggler(parseInt(H5PIntegration.user.canToggleViewOthersH5PContents) === 1);
      }

      // Add facets
      self.$facets = $('<div/>', {
        'class': 'h5p-facet-wrapper',
        appendTo: self.$container
      });

      // Create new table
      self.table = new H5PUtils.Table(self.classes, self.headers);
      self.table.setHeaders(self.headers, function (order) {
        // Sorting column or direction has changed.
        self.order = order;
        self.loadData();
      }, self.order);
      self.table.appendTo(self.$container);
    }

    // Process cell data before updating table
    for (var i = 0; i < self.headers.length; i++) {
      if (self.headers[i].facet === true) {
        // Process rows for col, expect object or array
        for (var j = 0; j < rows.length; j++) {
          rows[j][i] = self.createFacets(rows[j][i], i);
        }
      }
    }

    // Add/update rows
    var $tbody = self.table.setRows(rows);

    // Add event handlers for facets
    $('.h5p-facet', $tbody).click(function () {
      var $facet = $(this);
      self.filterByFacet($facet.data('col'), $facet.data('id'), $facet.text());
    }).keypress(function (event) {
      if (event.which === 32) {
        var $facet = $(this);
        self.filterByFacet($facet.data('col'), $facet.data('id'), $facet.text());
      }
    });
  };

  /**
   * Create button for adding facet to filter.
   *
   * @param (object|Array) input
   * @param number col ID of column
   */
  H5PDataView.prototype.createFacets = function (input, col) {
    var facets = '';

    if (input instanceof Array) {
      // Facet can be filtered on multiple values at the same time
      for (var i = 0; i < input.length; i++) {
        if (facets !== '') {
          facets += ', ';
        }
        facets += '<span class="h5p-facet" role="button" tabindex="0" data-id="' + input[i].id + '" data-col="' + col + '">' + input[i].title + '</span>';
      }
    }
    else {
      // Single value facet filtering
      facets += '<span class="h5p-facet" role="button" tabindex="0" data-id="' + input.id + '" data-col="' + col + '">' + input.title + '</span>';
    }

    return facets === '' ? '—' : facets;
  };

  /**
   * Adds a filter based on the given facet.
   *
   * @param number col ID of column we're filtering
   * @param number id ID to filter on
   * @param string text Human readable label for the filter
   */
  H5PDataView.prototype.filterByFacet = function (col, id, text) {
    var self = this;

    if (self.facets[col] !== undefined) {
      if (self.facets[col].id === id) {
        return; // Don't use the same filter again
      }

      // Remove current filter for this col
      self.facets[col].$tag.remove();
    }

    // Add to UI
    self.facets[col] = {
      id: id,
      '$tag': $('<span/>', {
        'class': 'h5p-facet-tag',
        text: text,
        appendTo: self.$facets,
      })
    };
    /**
     * Callback for removing filter.
     *
     * @private
     */
    var remove = function () {
      // Uncheck toggler for others' H5P contents
      if ( self.$othersContentToggler && self.facets.hasOwnProperty( self.columnIdAuthor ) ) {
        self.$othersContentToggler.prop('checked', false );
      }

      self.facets[col].$tag.remove();
      delete self.facets[col];
      self.loadData();
    };

    // Remove button
    $('<span/>', {
      role: 'button',
      tabindex: 0,
      appendTo: self.facets[col].$tag,
      text: self.l10n.remove,
      title: self.l10n.remove,
      on: {
        click: remove,
        keypress: function (event) {
          if (event.which === 32) {
            remove();
          }
        }
      }
    });

    // Load data with new filter
    self.loadData();
  };

  /**
   * Update pagination widget.
   *
   * @param {Number} num size of data collection
   */
  H5PDataView.prototype.updatePagination = function (num) {
    var self = this;

    if (self.pagination === undefined) {
      if (self.table === undefined) {
        // No table, no pagination
        return;
      }

      // Create new widget
      var $pagerContainer = $('<div/>', {'class': 'h5p-pagination'});
      self.pagination = new H5PUtils.Pagination(num, self.limit, function (offset) {
        // Handle page changes in pagination widget
        self.offset = offset;
        self.loadData();
      }, self.l10n);

      self.pagination.appendTo($pagerContainer);
      self.table.setFoot($pagerContainer);
    }
    else {
      // Update existing widget
      self.pagination.update(num, self.limit);
    }
  };

  /**
   * Add filters.
   */
  H5PDataView.prototype.addFilters = function () {
    var self = this;

    for (var i = 0; i < self.filters.length; i++) {
      if (self.filters[i] === true) {
        // Add text input filter for col i
        self.addTextFilter(i);
      }
    }
  };

  /**
   * Add text filter for given col num.
   *
   * @param {Number} col
   */
  H5PDataView.prototype.addTextFilter = function (col) {
    var self = this;

    /**
     * Find input value and filter on it.
     * @private
     */
    var search = function () {
      var filterOn = $input.val().replace(/^\s+|\s+$/g, '');
      if (filterOn === '') {
        filterOn = undefined;
      }
      if (filterOn !== self.filterOn[col]) {
        self.filterOn[col] = filterOn;
        self.loadData();
      }
    };

    // Add text field for filtering
    var typing;
    var $input = $('<input/>', {
      type: 'text',
      placeholder: self.l10n.search,
      on: {
        'blur': function () {
          clearTimeout(typing);
          search();
        },
        'keyup': function (event) {
          if (event.keyCode === 13) {
            clearTimeout(typing);
            search();
            return false;
          }
          else {
            clearTimeout(typing);
            typing = setTimeout(function () {
              search();
            }, 500);
          }
        }
      }
    }).appendTo(self.$container);
  };

  /**
   * Add toggle for others' H5P content.
   * @param {boolean} [checked=false] Initial check setting.
   */
  H5PDataView.prototype.addOthersContentToggler = function (checked) {
    var self = this;

    checked = (typeof checked === 'undefined') ? false : checked;

    // Checkbox
    this.$othersContentToggler = $('<input/>', {
      type: 'checkbox',
      'class': 'h5p-others-contents-toggler',
      'id': 'h5p-others-contents-toggler',
      'checked': checked,
      'click': function () {
        if ( this.checked ) {
          // Add filter on current user
          self.filterByFacet( self.columnIdAuthor, H5PIntegration.user.id, H5PIntegration.user.name );
        }
        else {
          // Remove facet indicator and reload full data view
          if ( self.facets.hasOwnProperty( self.columnIdAuthor ) && self.facets[self.columnIdAuthor].$tag ) {
            self.facets[self.columnIdAuthor].$tag.remove();
          }
          delete self.facets[self.columnIdAuthor];
          self.loadData();
        }
      }
    });

    // Label
    var $label = $('<label>', {
      'class': 'h5p-others-contents-toggler-label',
      'text': this.l10n.showOwnContentOnly,
      'for': 'h5p-others-contents-toggler'
    }).prepend(this.$othersContentToggler);

    $('<div>', {
      'class': 'h5p-others-contents-toggler-wrapper'
    }).append($label)
      .appendTo(this.$container);
  };

  return H5PDataView;
})(H5P.jQuery);
