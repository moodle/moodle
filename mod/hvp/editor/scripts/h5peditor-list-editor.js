/* global ns */
H5PEditor.ListEditor = (function ($) {

  /**
   * Draws the list.
   *
   * @class
   * @param {List} list
   */
  function ListEditor(list) {
    var self = this;

    var entity = list.getEntity();
    // Create list html
    var $list = $('<ul/>', {
      id: list.getId(),
      'aria-describedby': list.getDescriptionId(),
      'class': 'h5p-ul'
    });

    // Create add button
    var $button = ns.createButton(list.getImportance(), H5PEditor.t('core', 'addEntity', {':entity': entity}), function () {
      list.addItem();
    }, true);

    // Used when dragging items around
    var adjustX, adjustY, marginTop, formOffset;

    /**
     * @private
     * @param {jQuery} $item
     * @param {jQuery} $placeholder
     * @param {Number} x
     * @param {Number} y
     */
    var moveItem = function ($item, $placeholder, x, y) {
      var currentIndex;

      // Adjust so the mouse is placed on top of the icon.
      x = x - adjustX;
      y = y - adjustY;
      $item.css({
        top: y - marginTop - formOffset.top,
        left: x - formOffset.left
      });

      // Try to move up.
      var $prev = $item.prev().prev();
      if ($prev.length && y < $prev.offset().top + ($prev.height() / 2)) {
        $prev.insertAfter($item);

        currentIndex = $item.index();
        list.moveItem(currentIndex, currentIndex - 1);

        return;
      }

      // Try to move down.
      var $next = $item.next();
      if ($next.length && y + $item.height() > $next.offset().top + ($next.height() / 2)) {
        $next.insertBefore($placeholder);

        currentIndex = $item.index() - 2;
        list.moveItem(currentIndex, currentIndex + 1);
      }
    };

    /**
     * Default confirm handler.
     *
     * @param {Object} item Content parameters
     * @param {number} id Index of element being removed
     * @param {Object} buttonOffset Delete button offset, useful for positioning dialog
     * @param {function} confirm Run to confirm delete
     */
    self.defaultConfirmHandler = function (item, id, buttonOffset, confirm) {
      // Create default confirmation dialog for removing list item
      const confirmRemovalDialog = new H5P.ConfirmationDialog({
        dialogText: H5PEditor.t('core', 'confirmRemoval', {':type': entity})
      }).appendTo(document.body);

      // Remove list item on confirmation
      confirmRemovalDialog.on('confirmed', confirm);
      confirmRemovalDialog.show(buttonOffset.top);
    };

    // Use the default confirmation handler by default
    let confirmHandler = self.defaultConfirmHandler;

    /**
     * Set a custom confirmation handler callback (instead of the default dialog)
     *
     * @public
     * @param {function} confirmHandler
     */
    self.setConfirmHandler = function (handler) {
      confirmHandler = handler;
    };

    /**
     * Adds UI items to the widget.
     *
     * @public
     * @param {Object} item
     */
    self.addItem = function (item) {
      var $placeholder, mouseDownAt;
      var $item = $('<li/>', {
        'class' : 'h5p-li',
      });

      /**
       * Mouse move callback
       *
       * @private
       * @param {Object} event
       */
      var move = function (event) {
        if (mouseDownAt) {
          // Have not started moving yet

          if (! (event.pageX > mouseDownAt.x + 5 || event.pageX < mouseDownAt.x - 5 ||
                 event.pageY > mouseDownAt.y + 5 || event.pageY < mouseDownAt.y - 5) ) {
            return; // Not ready to start moving
          }

          // Prevent wysiwyg becoming unresponsive
          H5PEditor.Html.removeWysiwyg();

          // Prepare to start moving
          mouseDownAt = null;

          var offset = $item.offset();
          adjustX = event.pageX - offset.left;
          adjustY = event.pageY - offset.top;
          marginTop = parseInt($item.css('marginTop'));
          formOffset = $list.offsetParent().offset();
          // TODO: Couldn't formOffset and margin be added?

          var width = $item.width();
          var height = $item.height();

          $item.addClass('moving').css({
            width: width,
            height: height
          });
          $placeholder = $('<li/>', {
            'class': 'placeholder h5p-li',
            css: {
              width: width,
              height: height
            }
          }).insertBefore($item);
        }

        moveItem($item, $placeholder, event.pageX, event.pageY);
      };

      /**
       * Mouse button release callback
       *
       * @private
       */
      var up = function () {

        // Stop listening for mouse move events
        H5P.$window
          .unbind('mousemove', move)
          .unbind('mouseup', up);

        // Enable text select again
        H5P.$body
          .css({
            '-moz-user-select': '',
            '-webkit-user-select': '',
            'user-select': '',
            '-ms-user-select': ''
          })
          .attr('unselectable', 'off')[0].onselectstart = H5P.$body[0].ondragstart = null;

        if (!mouseDownAt) {
          // Not your regular click, we have been moving
          $item.removeClass('moving').css({
            width: 'auto',
            height: 'auto'
          });
          $placeholder.remove();

          if (item instanceof H5PEditor.Group) {
            // Avoid groups expand/collapse toggling
            item.preventToggle = true;
          }
        }
      };

      /**
       * Mouse button down callback
       *
       * @private
       */
      var down = function (event) {
        if (event.which !== 1) {
          return; // Only allow left mouse button
        }

        mouseDownAt = {
          x: event.pageX,
          y: event.pageY
        };

        // Start listening for mouse move events
        H5P.$window
          .mousemove(move)
          .mouseup(up);

        // Prevent text select
        H5P.$body
          .css({
            '-moz-user-select': 'none',
            '-webkit-user-select': 'none',
            'user-select': 'none',
            '-ms-user-select': 'none'
          })
          .attr('unselectable', 'on')[0].onselectstart = H5P.$body[0].ondragstart = function () {
            return false;
          };
      };

      /**
       * Order current list item up
       *
       * @private
       */
      var moveItemUp = function () {
        var $prev = $item.prev();
        if (!$prev.length) {
          return; // Cannot move item further up
        }

        // Prevent wysiwyg becoming unresponsive
        H5PEditor.Html.removeWysiwyg();

        var currentIndex = $item.index();
        $prev.insertAfter($item);
        list.moveItem(currentIndex, currentIndex - 1);
      };

      /**
       * Order current ist item down
       *
       * @private
       */
      var moveItemDown = function () {
        var $next = $item.next();
        if (!$next.length) {
          return; // Cannot move item further down
        }

        // Prevent wysiwyg becoming unresponsive
        H5PEditor.Html.removeWysiwyg();

        var currentIndex = $item.index();
        $next.insertBefore($item);
        list.moveItem(currentIndex, currentIndex + 1);
      };

      // List item title bar
      var $titleBar = $('<div/>', {
        'class': 'list-item-title-bar',
        appendTo: $item
      });

      // Container for list actions
      var $listActions = $('<div/>', {
        class: 'list-actions',
        appendTo: $titleBar
      });

      // Append order button
      var $orderGroup = $('<div/>', {
        class : 'order-group',
        appendTo: $listActions
      });

      H5PEditor.createButton('order-up', H5PEditor.t('core', 'orderItemUp'), moveItemUp).appendTo($orderGroup);
      H5PEditor.createButton('order-down', H5PEditor.t('core', 'orderItemDown'), moveItemDown).appendTo($orderGroup);

      H5PEditor.createButton('remove', H5PEditor.t('core', 'removeItem'), function () {
        confirmHandler(item, $item.index(), $(this).offset(), function () {
          list.removeItem($item.index());
          $item.remove();
        });
      }).appendTo($listActions);

      // Append new field item to content wrapper
      if (item instanceof H5PEditor.Group) {
        // Append to item
        item.appendTo($item);
        $item.addClass('listgroup');
        $titleBar.addClass(list.getImportance());

        // Move label
        $item.children('.field').children('.title').appendTo($titleBar).addClass('h5peditor-label');

        // Handle expand and collapse
        item.on('expanded', function () {
          $item.addClass('expanded').removeClass('collapsed');
        });
        item.on('collapsed', function () {
          $item.removeClass('expanded').addClass('collapsed');
        });
      }
      else {
        // Append content wrapper
        var $content = $('<div/>', {
          'class' : 'content'
        }).appendTo($item);

        // Add importance to items not in groups
        $titleBar.addClass(list.getImportance());

        // Append field
        item.appendTo($content);

        if (item.field.label !== 0) {
          // Try to find and move the label to the title bar
          const $label = $content.children('.field').find('.h5peditor-label:first');

          if ($label.length !== 0) {
            $titleBar.append($('<label/>', {
              'class': 'h5peditor-label',
              'for': $label.parent().attr('for'),
              html: $label.html()
            }));

            $label.hide();
          }
        }
      }

      // Append item to list
      $item.appendTo($list);

      if (item instanceof H5PEditor.Group && item.field.expanded !== false) {
        // Good UX: automatically expand groups if not explicitly disabled by semantics
        item.expand();
      }

      $titleBar.children('.h5peditor-label').mousedown(down);
    };

    /**
     * Determine if child is a text field
     *
     * @param {Object} child
     * @returns {boolean} True if child is a text field
     */
    self.isTextField = function (child) {
      var widget = ns.getWidgetName(child.field);
      return widget === 'html' || widget === 'text';
    };

    /**
     * Puts this widget at the end of the given container.
     *
     * @public
     * @param {jQuery} $container
     */
    self.appendTo = function ($container) {
      $list.appendTo($container);
      $button.appendTo($container);
    };

    /**
     * Remove this widget from the editor DOM.
     *
     * @public
     */
    self.remove = function () {
      $list.remove();
      $button.remove();
    };
  }

  return ListEditor;
})(H5P.jQuery);
