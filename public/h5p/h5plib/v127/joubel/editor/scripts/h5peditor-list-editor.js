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

    /**
     * Add group collapse functionality to list editor if items are groups.
     */
    const addGroupCollapseFunctionality = () => {
      if (
        list.field?.field?.type === 'group' &&
        (list.getValue() ?? []).length
      ) {
        self.addGroupCollapseListener();
        self.addCollapseButtons();
      }
    };

    /**
     * Find closest parent list.
     * @param {object} library H5PEditor field instance.
     * @returns {object|boolean} Closest parent list or false if none found.
     */
    const findClosestParentList = (library) => {
      const parent = library?.parent;
      if (!parent) {
        return false;
      }

      if (!parent.field?.type) {
        return false;
      }

      if (parent.field.type === 'list') {
        return parent;
      }

      return findClosestParentList(parent);
    };

    /**
     * Set width of collapse button.
     *
     * The width of the button should not change when the label is changed,
     * so the button is rendered offsite with both labels and the longest one
     * is used to determine the button width.
     */
    const setcollapseButtonMainWidth = () => {
      if (!this.collapseButtonMain) {
        return; // Button not available
      }

      // The width should not need to be computed more than once
      if (this.fixedMainButtonWidth) {
        this.collapseButtonMain.style.width = `${this.fixedMainButtonWidth}px`;
        return;
      }

      const offsiteH5PEditorDOM = document.createElement('div');
      offsiteH5PEditorDOM.classList.add('h5peditor', 'offsite');

      const offsiteH5PEditorFlexWrapper = document.createElement('div');
      offsiteH5PEditorFlexWrapper.classList.add('h5p-editor-flex-wrapper');
      offsiteH5PEditorDOM.append(offsiteH5PEditorFlexWrapper);

      const offsiteButton1 = document.createElement('button');
      offsiteButton1.classList.add(
        'h5peditor-button',
        'h5peditor-button-textual',
        'h5peditor-button-collapse',
        'collapsed'
      );
      offsiteH5PEditorFlexWrapper.append(offsiteButton1);

      const offsiteIcon1 = document.createElement('span');
      offsiteIcon1.classList.add('icon');
      offsiteButton1.append(offsiteIcon1);

      const offsiteLabel1 = document.createElement('div');
      offsiteLabel1.classList.add('label');
      offsiteLabel1.innerText = H5PEditor.t('core', 'expandAllContent');
      offsiteButton1.append(offsiteLabel1);

      const offsiteButton2 = document.createElement('button');
      offsiteButton2.classList.add(
        'h5peditor-button',
        'h5peditor-button-textual',
        'h5peditor-button-collapse'
      );
      offsiteH5PEditorFlexWrapper.append(offsiteButton2);

      const offsiteIcon2 = document.createElement('span');
      offsiteIcon2.classList.add('icon');
      offsiteButton2.append(offsiteIcon2);

      const offsiteLabel2 = document.createElement('div');
      offsiteLabel2.classList.add('label');
      offsiteLabel2.innerText = H5PEditor.t('core', 'collapseAllContent');
      offsiteButton2.append(offsiteLabel2);

      document.body.append(offsiteH5PEditorDOM);

      // FontFaceSet API is used to ensure font of icon is loaded
      document.fonts.ready.then(() => {
        const width1 = offsiteButton1.getBoundingClientRect().width;
        const width2 = offsiteButton2.getBoundingClientRect().width;

        this.fixedMainButtonWidth = Math.ceil(Math.max(width1, width2));

        this.collapseButtonMain.style.width = `${this.fixedMainButtonWidth}px`;

        offsiteH5PEditorDOM?.remove();
      });
    };

    /**
     * Determine whether list should get a collapse button.
     *
     * List should get a collapse button if it's the topmost list only - or if
     * its on the 2nd leven and the parent list has a VerticalTabs widget.
     * @returns {boolean} True if list should get a collapse button. Else false.
     */
    shouldListGetCollapseButtonMain = (list) => {
      const closestParentList = findClosestParentList(list);
      if (!closestParentList) {
        return true; // Is topmost list
      }

      /*
       * Note: Currently, the only widget that changes the list editor
       * appearance to not make the collapse button suitable is the
       * VerticalTabs widget. In the future, this might change as other list
       * widgets get developed so the following exception may not suffice then.
       * There's no good way to determine this automatically, however.
       */
      return (
        // Second level list, but VerticalTabs widget
        !findClosestParentList(closestParentList) &&
          H5PEditor.VerticalTabs &&
          closestParentList.widget instanceof H5PEditor.VerticalTabs
      );
    };

    /**
     * Determine whether widget has expand/collapse capabilities.
     * @returns {boolean} True if widget has collapse capabilities. Else false.
     */
    self.hasCollapseCapabilities = () => {
      return (
        this.container?.parentNode.firstChild.querySelector(
          '.h5p-editor-flex-wrapper .h5peditor-button-collapse'
        ) instanceof HTMLElement ||
        this.container?.parentNode.firstChild.querySelector(
          '.h5peditor-label-button'
        ) instanceof HTMLElement
      );
    };

    /**
     * Toggle collapse button main label visibility.
     * @param {boolean} visible True to show label. False to hide.
     */
    self.toggleCollapseButtonMainLabel = (visible) => {
      if (typeof visible !== 'boolean') {
        return;
      }

      if (!visible) {
        this.collapseButtonMain.style.width = '';
      }
      else {
        setcollapseButtonMainWidth();
      }

      this.collapseButtonMain.classList.toggle('no-label', !visible);
    }

    /**
     * Resize handler.
     */
    self.handleResize = () => {
      /*
       * When the two buttons for collapsing/expanding groups are in the same
       * container and the horizontal space does not suffice, first the main
       * button should loose its label. If there's still not enough space, the
       * list button label will wrap.
       * Can't be done in CSS alone, unfortunately, because the main button
       * needs a fixed width.
       */
      const wrapperRect = this.collapseButtonsWrapper.getBoundingClientRect();
      if (wrapperRect.width === 0) {
        return; // Not visible
      }

      this.collapseButtonsGap = this.collapseButtonsGap ?? parseFloat(
        window.getComputedStyle(this.collapseButtonsWrapper).gap ?? 0
      );

      const listButtonRect = this.collapseButtonList.getBoundingClientRect();

      const hasSpaceForBothButtons =
        wrapperRect.width - listButtonRect.width - this.collapseButtonsGap >=
        this.fixedMainButtonWidth;

      this.toggleCollapseButtonMainLabel(hasSpaceForBothButtons);
    };
    self.handleResize = self.handleResize.bind(self);

    /**
     * Set toggle button collapsed state.
     * @param {boolean} shouldBeCollapsed True for collapsed state.
     */
    self.setButtonsCollapsed = (shouldBeCollapsed) => {
      if (typeof shouldBeCollapsed !== 'boolean') {
        return; // Invalid type
      }

      const ariaActionText = shouldBeCollapsed ?
        H5PEditor.t('core', 'expandAllContent') :
        H5PEditor.t('core', 'collapseAllContent');

      if (this.collapseButtonList) {
        this.collapseButtonList.classList.toggle(
          'collapsed', shouldBeCollapsed
        );

        this.collapseButtonList.setAttribute(
          'aria-label',
          `${this.collapseButtonList.innerText}. ${ariaActionText}`
        );
      }

      if (this.collapseButtonMain) {
        this.collapseButtonMain.classList.toggle(
          'collapsed', shouldBeCollapsed
        );

        this.collapseButtonMainLabel.innerText = ariaActionText;
      }
    };

    /**
     * Add group collapse listener.
     */
    self.addGroupCollapseListener = () => {
      if (this.hasCollapseCapabilities()) {
        return; // Don't add extra listener
      }

      list.on('groupCollapsedStateChanged', (event) => {
        this.setButtonsCollapsed(event.data.allGroupsCollapsed);
      });

      /*
       * Note: This is a workaround. It determines the element to focus
       * by finding the first contained error message and then choosing the
       * first element with the `.error` class that is commonly used by H5P
       * editor widgets. This may fail if an editor widget does not put the
       * `.error` class on the element however. If no such element is found,
       * the error message will at least be scrolled into view.
       * Ideally, every widget would have a method to return fields that do
       * not validate, but that would require to change every widget and should
       * be documented in the H5P core API.
       */
      list.on('cannotCollapseAll', () => {
        const errorMessageDOM =
          [... this.container.querySelectorAll('.h5p-errors')]
            .filter((error) => error.innerHTML.length > 0)
            .shift();

        if (!errorMessageDOM) {
          return;
        }

        let errorDOM;
        let parentNode = errorMessageDOM.parentNode;

        while (!errorDOM && parentNode) {
          errorDOM = parentNode.querySelector('.error');
          parentNode = parentNode.parentNode;
        }

        if (errorDOM) {
          errorDOM?.focus();
        }
        else {
          errorMessageDOM.scrollIntoView();
        }
      });
    };

    /**
     * Add toggle buttons for collapsing/expanding groups to container.
     *
     * There's a main button for the topmost list with groups and a button that
     * replaces the original list title for all other lists.
     */
    self.addCollapseButtons = () => {
      if (this.hasCollapseCapabilities()) {
        return; // Don't add extra buttons
      }

      /*
       * Adding the same flex-wrapper approach that's used for the content title
       * label and the metadata button, so the "collapse/expand" button can be
       * aligned as required.
       */
      this.collapseButtonsWrapper = document.createElement('div');
      this.collapseButtonsWrapper.classList.add(
        'h5p-editor-flex-wrapper', 'has-button-collapse'
      );

      /*
       * Move original label offsite, because it is used as a <label> for screen
       * readers and display list title collapse button instead.
       */
      this.originalLabel =
        this.container.parentNode?.querySelector('.h5peditor-label');
      this.originalLabel.classList.add('offsite');

      this.collapseButtonList = document.createElement('button');
      this.collapseButtonList.classList.add('h5peditor-label-button');

      const icon = document.createElement('div');
      icon.classList.add('icon');
      this.collapseButtonList.append(icon);

      const label = document.createElement('div');
      label.classList.add('label', 'h5peditor-required');
      label.innerText = this.originalLabel.innerText;
      this.collapseButtonList.append(label);

      this.collapseButtonList.setAttribute(
        'aria-label',
        `${this.collapseButtonList.innerText}. ${H5PEditor.t('core', 'collapseAllContent')}`
      );

      this.collapseButtonList.addEventListener('click', () => {
        list.toggleItemCollapsed();
      });

      /*
       * If label is directly before the list editor container, put it next to
       * the button. Otherwise, e. g. when there are list widgets, use button
       * alone on top of those and leave the "label" where it was.
       */
      const bothsButtonsInSameContainer =
        self.container.previousSibling === this.originalLabel;

      if (bothsButtonsInSameContainer) {
        this.collapseButtonsWrapper.classList.add('has-label');
        this.collapseButtonsWrapper.append(this.collapseButtonList);
      }
      else {
        self.container.previousSibling.parentNode?.insertBefore(
          this.collapseButtonList, self.container.previousSibling
        );
      }

      if (shouldListGetCollapseButtonMain(list)) {
        this.collapseButtonMain = document.createElement('button');
        this.collapseButtonMain.classList.add(
          'h5peditor-button',
          'h5peditor-button-textual',
          'h5peditor-button-collapse'
        );

        // Icon fixed left aligned
        const icon = document.createElement('div');
        icon.classList.add('icon');
        this.collapseButtonMain.append(icon);

        // Label centered in remaining space
        this.collapseButtonMainLabel = document.createElement('div');
        this.collapseButtonMainLabel.classList.add('label');
        this.collapseButtonMain.append(this.collapseButtonMainLabel);

        this.collapseButtonMainLabel.innerText =
          H5PEditor.t('core', 'collapseAllContent');

        // Longest label should fit inside button
        setcollapseButtonMainWidth();

        this.collapseButtonMain.addEventListener('click', () => {
          list.toggleItemCollapsed();
        });

        this.collapseButtonsWrapper.append(this.collapseButtonMain);

        if (bothsButtonsInSameContainer) {
          // We may need to hide the main button's label
          H5P.$window.get(0).addEventListener('resize', self.handleResize);
        }
      }

      self.container.parentNode?.prepend(this.collapseButtonsWrapper);
    };

    // Create add button
    var $button = ns.createButton(
      list.getImportance(),
      H5PEditor.t('core', 'addEntity', { ':entity': entity }),
      () => {
        list.addItem();

        if (!this.hasCollapseCapabilities()) {
          addGroupCollapseFunctionality();
        }
      },
      true
    );

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
      if (
        $next.length && y + $item.height() >
          $next.offset().top + ($next.height() / 2)
      ) {
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
        dialogText: H5PEditor.t('core', 'confirmRemoval', { ':type': entity })
      }).appendTo(document.body);

      // Remove list item on confirmation
      confirmRemovalDialog.on('confirmed', confirm);
      confirmRemovalDialog.show(buttonOffset.top);
    };

    // Use the default confirmation handler by default
    let confirmHandler = self.defaultConfirmHandler;

    /**
     * Set custom confirmation handler callback (instead of the default dialog)
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

          if (! (
            event.pageX > mouseDownAt.x + 5 ||
            event.pageX < mouseDownAt.x - 5 ||
            event.pageY > mouseDownAt.y + 5 ||
            event.pageY < mouseDownAt.y - 5
          )) {
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
          .attr('unselectable', 'off')[0]
          .onselectstart = H5P.$body[0].ondragstart = null;

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
          .attr('unselectable', 'on')[0]
          .onselectstart = H5P.$body[0].ondragstart = () => {
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

      H5PEditor.createButton(
        'order-up', H5PEditor.t('core', 'orderItemUp'), moveItemUp
      ).appendTo($orderGroup);
      H5PEditor.createButton(
        'order-down', H5PEditor.t('core', 'orderItemDown'), moveItemDown
      ).appendTo($orderGroup);

      H5PEditor.createButton(
        'remove', H5PEditor.t('core', 'removeItem'), function () {
          confirmHandler(item, $item.index(), $(this).offset(), function () {
            list.removeItem($item.index());
            $item.remove();

            if (!(list.getValue() ?? []).length) {
              self.removeCollapseButtons();
            }
          });
        }
      ).appendTo($listActions);

      // Append new field item to content wrapper
      if (item instanceof H5PEditor.Group) {
        // Append to item
        item.appendTo($item);
        $item.addClass('listgroup');
        $titleBar.addClass(list.getImportance());

        // Move label
        $item
          .children('.field').children('.title')
          .appendTo($titleBar).addClass('h5peditor-label');

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
          const $label =
            $content.children('.field').find('.h5peditor-label:first');

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
        /*
         * Good UX: automatically expand groups if not explicitly disabled by
         * semantics
         */
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
      self.container = $container[0];

      addGroupCollapseFunctionality();

      $list.appendTo($container);
      $button.appendTo($container);
    };

    /**
     * Remove this widget from the editor DOM.
     *
     * @public
     */
    self.remove = function () {
      this.removeCollapseButtons();
      $list.remove();
      $button.remove();
    };

    /**
     * Remove collapse buttons from container.
     */
    self.removeCollapseButtons = () => {
      this.originalLabel?.classList.remove('offsite');
      this.collapseButtonList?.remove();
      this.collapseButtonMain?.remove();
      this.collapseButtonsWrapper?.remove();
      H5P.$window.get(0).removeEventListener('resize', self.handleResize);
    };
  }

  return ListEditor;
})(H5P.jQuery);
