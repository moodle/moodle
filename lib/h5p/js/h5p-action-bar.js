/**
 * @class
 * @augments H5P.EventDispatcher
 * @param {Object} displayOptions
 * @param {boolean} displayOptions.export Triggers the display of the 'Download' button
 * @param {boolean} displayOptions.copyright Triggers the display of the 'Copyright' button
 * @param {boolean} displayOptions.embed Triggers the display of the 'Embed' button
 * @param {boolean} displayOptions.icon Triggers the display of the 'H5P icon' link
 */
H5P.ActionBar = (function ($, EventDispatcher) {
  "use strict";

  function ActionBar(displayOptions) {
    EventDispatcher.call(this);

    /** @alias H5P.ActionBar# */
    var self = this;

    var hasActions = false;

    // Create action bar
    var $actions = H5P.jQuery('<ul class="h5p-actions"></ul>');

    /**
     * Helper for creating action bar buttons.
     *
     * @private
     * @param {string} type
     * @param {string} customClass Instead of type class
     */
    var addActionButton = function (type, customClass) {
      /**
       * Handles selection of action
       */
      var handler = function () {
        self.trigger(type);
      };
      H5P.jQuery('<li/>', {
        'class': 'h5p-button h5p-noselect h5p-' + (customClass ? customClass : type),
        role: 'button',
        tabindex: 0,
        title: H5P.t(type + 'Description'),
        html: H5P.t(type),
        on: {
          click: handler,
          keypress: function (e) {
            if (e.which === 32) {
              handler();
              e.preventDefault(); // (since return false will block other inputs)
            }
          }
        },
        appendTo: $actions
      });

      hasActions = true;
    };

    // Register action bar buttons
    if (displayOptions.export || displayOptions.copy) {
      // Add export button
      addActionButton('reuse', 'export');
    }
    if (displayOptions.copyright) {
      addActionButton('copyrights');
    }
    if (displayOptions.embed) {
      addActionButton('embed');
    }
    if (displayOptions.icon) {
      // Add about H5P button icon
      H5P.jQuery('<li><a class="h5p-link" href="http://h5p.org" target="_blank" title="' + H5P.t('h5pDescription') + '"></a></li>').appendTo($actions);
      hasActions = true;
    }

    /**
     * Returns a reference to the dom element
     *
     * @return {H5P.jQuery}
     */
    self.getDOMElement = function () {
      return $actions;
    };

    /**
     * Does the actionbar contain actions?
     *
     * @return {Boolean}
     */
    self.hasActions = function () {
      return hasActions;
    };
  }

  ActionBar.prototype = Object.create(EventDispatcher.prototype);
  ActionBar.prototype.constructor = ActionBar;

  return ActionBar;

})(H5P.jQuery, H5P.EventDispatcher);
