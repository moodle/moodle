var H5PUtils = H5PUtils || {};

(function ($) {
  /**
   * Generic function for creating a table including the headers
   *
   * @param {array} headers List of headers
   */
  H5PUtils.createTable = function (headers) {
    var $table = $('<table class="h5p-admin-table' + (H5PAdminIntegration.extraTableClasses !== undefined ? ' ' + H5PAdminIntegration.extraTableClasses : '') + '"></table>');

    if(headers) {
      var $thead = $('<thead></thead>');
      var $tr = $('<tr></tr>');

      $.each(headers, function (index, value) {
        if (!(value instanceof Object)) {
          value = {
            html: value
          };
        }

        $('<th/>', value).appendTo($tr);
      });

      $table.append($thead.append($tr));
    }

    return $table;
  };

  /**
   * Generic function for creating a table row
   *
   * @param {array} rows Value list. Object name is used as class name in <TD>
   */
  H5PUtils.createTableRow = function (rows) {
    var $tr = $('<tr></tr>');

    $.each(rows, function (index, value) {
      if (!(value instanceof Object)) {
        value = {
          html: value
        };
      }

        $('<td/>', value).appendTo($tr);
    });

    return $tr;
  };

  /**
   * Generic function for creating a field containing label and value
   *
   * @param {string} label The label displayed in front of the value
   * @param {string} value The value
   */
  H5PUtils.createLabeledField = function (label, value) {
    var $field = $('<div class="h5p-labeled-field"></div>');

    $field.append('<div class="h5p-label">' + label + '</div>');
    $field.append('<div class="h5p-value">' + value + '</div>');

    return $field;
  };

  /**
   * Replaces placeholder fields in translation strings
   *
   * @param {string} template The translation template string in the following format: "$name is a $sex"
   * @param {array} replacors An js object with key and values. Eg: {'$name': 'Frode', '$sex': 'male'}
   */
  H5PUtils.translateReplace = function (template, replacors) {
    $.each(replacors, function (key, value) {
      template = template.replace(new RegExp('\\'+key, 'g'), value);
    });
    return template;
  };

  /**
   * Get throbber with given text.
   *
   * @param {String} text
   * @returns {$}
   */
  H5PUtils.throbber = function (text) {
    return $('<div/>', {
      class: 'h5p-throbber',
      text: text
    });
  };

  /**
   * Makes it possbile to rebuild all content caches from admin UI.
   * @param {Object} notCached
   * @returns {$}
   */
  H5PUtils.getRebuildCache = function (notCached) {
    var $container = $('<div class="h5p-admin-rebuild-cache"><p class="message">' + notCached.message + '</p><p class="progress">' + notCached.progress + '</p></div>');
    var $button = $('<button>' + notCached.button + '</button>').appendTo($container).click(function () {
      var $spinner = $('<div/>', {class: 'h5p-spinner'}).replaceAll($button);
      var parts = ['|', '/', '-', '\\'];
      var current = 0;
      var spinning = setInterval(function () {
        $spinner.text(parts[current]);
        current++;
        if (current === parts.length) current = 0;
      }, 100);

      var $counter = $container.find('.progress');
      var build = function () {
        $.post(notCached.url, function (left) {
          if (left === '0') {
            clearInterval(spinning);
            $container.remove();
            location.reload();
          }
          else {
            var counter = $counter.text().split(' ');
            counter[0] = left;
            $counter.text(counter.join(' '));
            build();
          }
        });
      };
      build();
    });

    return $container;
  };

  /**
   * Generic table class with useful helpers.
   *
   * @class
   * @param {Object} classes
   *   Custom html classes to use on elements.
   *   e.g. {tableClass: 'fixed'}.
   */
  H5PUtils.Table = function (classes) {
    var numCols;
    var sortByCol;
    var $sortCol;
    var sortCol;
    var sortDir;

    // Create basic table
    var tableOptions = {};
    if (classes.table !== undefined) {
      tableOptions['class'] = classes.table;
    }
    var $table = $('<table/>', tableOptions);
    var $thead = $('<thead/>').appendTo($table);
    var $tfoot = $('<tfoot/>').appendTo($table);
    var $tbody = $('<tbody/>').appendTo($table);

    /**
     * Add columns to given table row.
     *
     * @private
     * @param {jQuery} $tr Table row
     * @param {(String|Object)} col Column properties
     * @param {Number} id Used to seperate the columns
     */
    var addCol = function ($tr, col, id) {
      var options = {
        on: {}
      };

      if (!(col instanceof Object)) {
        options.text = col;
      }
      else {
        if (col.text !== undefined) {
          options.text = col.text;
        }
        if (col.class !== undefined) {
          options.class = col.class;
        }

        if (sortByCol !== undefined && col.sortable === true) {
          // Make sortable
          options.role = 'button';
          options.tabIndex = 0;

          // This is the first sortable column, use as default sort
          if (sortCol === undefined) {
            sortCol = id;
            sortDir = 0;
          }

          // This is the sort column
          if (sortCol === id) {
            options['class'] = 'h5p-sort';
            if (sortDir === 1) {
              options['class'] += ' h5p-reverse';
            }
          }

          options.on.click = function () {
            sort($th, id);
          };
          options.on.keypress = function (event) {
            if ((event.charCode || event.keyCode) === 32) { // Space
              sort($th, id);
            }
          };
        }
      }

      // Append
      var $th = $('<th>', options).appendTo($tr);
      if (sortCol === id) {
        $sortCol = $th; // Default sort column
      }
    };

    /**
     * Updates the UI when a column header has been clicked.
     * Triggers sorting callback.
     *
     * @private
     * @param {jQuery} $th Table header
     * @param {Number} id Used to seperate the columns
     */
    var sort = function ($th, id) {
      if (id === sortCol) {
        // Change sorting direction
        if (sortDir === 0) {
          sortDir = 1;
          $th.addClass('h5p-reverse');
        }
        else {
          sortDir = 0;
          $th.removeClass('h5p-reverse');
        }
      }
      else {
        // Change sorting column
        $sortCol.removeClass('h5p-sort').removeClass('h5p-reverse');
        $sortCol = $th.addClass('h5p-sort');
        sortCol = id;
        sortDir = 0;
      }

      sortByCol({
        by: sortCol,
        dir: sortDir
      });
    };

    /**
     * Set table headers.
     *
     * @public
     * @param {Array} cols
     *   Table header data. Can be strings or objects with options like
     *   "text" and "sortable". E.g.
     *   [{text: 'Col 1', sortable: true}, 'Col 2', 'Col 3']
     * @param {Function} sort Callback which is runned when sorting changes
     * @param {Object} [order]
     */
    this.setHeaders = function (cols, sort, order) {
      numCols = cols.length;
      sortByCol = sort;

      if (order) {
        sortCol = order.by;
        sortDir = order.dir;
      }

      // Create new head
      var $newThead = $('<thead/>');
      var $tr = $('<tr/>').appendTo($newThead);
      for (var i = 0; i < cols.length; i++) {
        addCol($tr, cols[i], i);
      }

      // Update DOM
      $thead.replaceWith($newThead);
      $thead = $newThead;
    };

    /**
     * Set table rows.
     *
     * @public
     * @param {Array} rows Table rows with cols: [[1,'hello',3],[2,'asd',6]]
     */
    this.setRows = function (rows) {
      var $newTbody = $('<tbody/>');

      for (var i = 0; i < rows.length; i++) {
        var $tr = $('<tr/>').appendTo($newTbody);

        for (var j = 0; j < rows[i].length; j++) {
          $('<td>', {
            html: rows[i][j]
          }).appendTo($tr);
        }
      }

      $tbody.replaceWith($newTbody);
      $tbody = $newTbody;

      return $tbody;
    };

    /**
     * Set custom table body content. This can be a message or a throbber.
     * Will cover all table columns.
     *
     * @public
     * @param {jQuery} $content Custom content
     */
    this.setBody = function ($content) {
      var $newTbody = $('<tbody/>');
      var $tr = $('<tr/>').appendTo($newTbody);
      $('<td>', {
        colspan: numCols
      }).append($content).appendTo($tr);
      $tbody.replaceWith($newTbody);
      $tbody = $newTbody;
    };

    /**
     * Set custom table foot content. This can be a pagination widget.
     * Will cover all table columns.
     *
     * @public
     * @param {jQuery} $content Custom content
     */
    this.setFoot = function ($content) {
      var $newTfoot = $('<tfoot/>');
      var $tr = $('<tr/>').appendTo($newTfoot);
      $('<td>', {
        colspan: numCols
      }).append($content).appendTo($tr);
      $tfoot.replaceWith($newTfoot);
    };


    /**
     * Appends the table to the given container.
     *
     * @public
     * @param {jQuery} $container
     */
    this.appendTo = function ($container) {
      $table.appendTo($container);
    };
  };

  /**
   * Generic pagination class. Creates a useful pagination widget.
   *
   * @class
   * @param {Number} num Total number of items to pagiate.
   * @param {Number} limit Number of items to dispaly per page.
   * @param {Function} goneTo
   *   Callback which is fired when the user wants to go to another page.
   * @param {Object} l10n
   *   Localization / translations. e.g.
   *   {
   *     currentPage: 'Page $current of $total',
   *     nextPage: 'Next page',
   *     previousPage: 'Previous page'
   *   }
   */
  H5PUtils.Pagination = function (num, limit, goneTo, l10n) {
    var current = 0;
    var pages = Math.ceil(num / limit);

    // Create components

    // Previous button
    var $left = $('<button/>', {
      html: '&lt;',
      'class': 'button',
      title: l10n.previousPage
    }).click(function () {
      goTo(current - 1);
    });

    // Current page text
    var $text = $('<span/>').click(function () {
      $input.width($text.width()).show().val(current + 1).focus();
      $text.hide();
    });

    // Jump to page input
    var $input = $('<input/>', {
      type: 'number',
      min : 1,
      max: pages,
      on: {
        'blur': function () {
          gotInput();
        },
        'keyup': function (event) {
          if (event.keyCode === 13) {
            gotInput();
            return false;
          }
        }
      }
    }).hide();

    // Next button
    var $right = $('<button/>', {
      html: '&gt;',
      'class': 'button',
      title: l10n.nextPage
    }).click(function () {
      goTo(current + 1);
    });

    /**
     * Check what page the user has typed in and jump to it.
     *
     * @private
     */
    var gotInput = function () {
      var page = parseInt($input.hide().val());
      if (!isNaN(page)) {
        goTo(page - 1);
      }
      $text.show();
    };

    /**
     * Update UI elements.
     *
     * @private
     */
    var updateUI = function () {
      var next = current + 1;

      // Disable or enable buttons
      $left.attr('disabled', current === 0);
      $right.attr('disabled', next === pages);

      // Update counter
      $text.html(l10n.currentPage.replace('$current', next).replace('$total', pages));
    };

    /**
     * Try to go to the requested page.
     *
     * @private
     * @param {Number} page
     */
    var goTo = function (page) {
      if (page === current || page < 0 || page >= pages) {
        return; // Invalid page number
      }
      current = page;

      updateUI();

      // Fire callback
      goneTo(page * limit);
    };

    /**
     * Update number of items and limit.
     *
     * @public
     * @param {Number} newNum Total number of items to pagiate.
     * @param {Number} newLimit Number of items to dispaly per page.
     */
    this.update = function (newNum, newLimit) {
      if (newNum !== num || newLimit !== limit) {
        // Update num and limit
        num = newNum;
        limit = newLimit;
        pages = Math.ceil(num / limit);
        $input.attr('max', pages);

        if (current >= pages) {
          // Content is gone, move to last page.
          goTo(pages - 1);
          return;
        }

        updateUI();
      }
    };

    /**
     * Append the pagination widget to the given container.
     *
     * @public
     * @param {jQuery} $container
     */
    this.appendTo = function ($container) {
      $left.add($text).add($input).add($right).appendTo($container);
    };

    // Update UI
    updateUI();
  };

})(H5P.jQuery);
