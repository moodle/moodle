define(["exports", "jquery", "./util"], function (exports, _jquery, _util) {
  "use strict";

  Object.defineProperty(exports, "__esModule", {
    value: true
  });

  var _jquery2 = _interopRequireDefault(_jquery);

  var _util2 = _interopRequireDefault(_util);

  function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
      default: obj
    };
  }

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  /**
   * --------------------------------------------------------------------------
   * Bootstrap (v4.0.0): tab.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * --------------------------------------------------------------------------
   */
  var Tab = function ($) {
    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */
    var NAME = 'tab';
    var VERSION = '4.0.0';
    var DATA_KEY = 'bs.tab';
    var EVENT_KEY = ".".concat(DATA_KEY);
    var DATA_API_KEY = '.data-api';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var TRANSITION_DURATION = 150;
    var Event = {
      HIDE: "hide".concat(EVENT_KEY),
      HIDDEN: "hidden".concat(EVENT_KEY),
      SHOW: "show".concat(EVENT_KEY),
      SHOWN: "shown".concat(EVENT_KEY),
      CLICK_DATA_API: "click".concat(EVENT_KEY).concat(DATA_API_KEY)
    };
    var ClassName = {
      DROPDOWN_MENU: 'dropdown-menu',
      ACTIVE: 'active',
      DISABLED: 'disabled',
      FADE: 'fade',
      SHOW: 'show'
    };
    var Selector = {
      DROPDOWN: '.dropdown',
      NAV_LIST_GROUP: '.nav, .list-group',
      ACTIVE: '.active',
      ACTIVE_UL: '> li > .active',
      DATA_TOGGLE: '[data-toggle="tab"], [data-toggle="pill"], [data-toggle="list"]',
      DROPDOWN_TOGGLE: '.dropdown-toggle',
      DROPDOWN_ACTIVE_CHILD: '> .dropdown-menu .active'
      /**
       * ------------------------------------------------------------------------
       * Class Definition
       * ------------------------------------------------------------------------
       */

    };

    var Tab = function () {
      function Tab(element) {
        _classCallCheck(this, Tab);

        this._element = element;
      } // Getters


      _createClass(Tab, [{
        key: "show",
        value: function show() {
          var _this = this;

          if (this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE && $(this._element).hasClass(ClassName.ACTIVE) || $(this._element).hasClass(ClassName.DISABLED)) {
            return;
          }

          var target;
          var previous;
          var listElement = $(this._element).closest(Selector.NAV_LIST_GROUP)[0];

          var selector = _util2.default.getSelectorFromElement(this._element);

          if (listElement) {
            var itemSelector = listElement.nodeName === 'UL' ? Selector.ACTIVE_UL : Selector.ACTIVE;
            previous = $.makeArray($(listElement).find(itemSelector));
            previous = previous[previous.length - 1];
          }

          var hideEvent = $.Event(Event.HIDE, {
            relatedTarget: this._element
          });
          var showEvent = $.Event(Event.SHOW, {
            relatedTarget: previous
          });

          if (previous) {
            $(previous).trigger(hideEvent);
          }

          $(this._element).trigger(showEvent);

          if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) {
            return;
          }

          if (selector) {
            target = $(selector)[0];
          }

          this._activate(this._element, listElement);

          var complete = function complete() {
            var hiddenEvent = $.Event(Event.HIDDEN, {
              relatedTarget: _this._element
            });
            var shownEvent = $.Event(Event.SHOWN, {
              relatedTarget: previous
            });
            $(previous).trigger(hiddenEvent);
            $(_this._element).trigger(shownEvent);
          };

          if (target) {
            this._activate(target, target.parentNode, complete);
          } else {
            complete();
          }
        }
      }, {
        key: "dispose",
        value: function dispose() {
          $.removeData(this._element, DATA_KEY);
          this._element = null;
        }
      }, {
        key: "_activate",
        value: function _activate(element, container, callback) {
          var _this2 = this;

          var activeElements;

          if (container.nodeName === 'UL') {
            activeElements = $(container).find(Selector.ACTIVE_UL);
          } else {
            activeElements = $(container).children(Selector.ACTIVE);
          }

          var active = activeElements[0];
          var isTransitioning = callback && _util2.default.supportsTransitionEnd() && active && $(active).hasClass(ClassName.FADE);

          var complete = function complete() {
            return _this2._transitionComplete(element, active, callback);
          };

          if (active && isTransitioning) {
            $(active).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
          } else {
            complete();
          }
        }
      }, {
        key: "_transitionComplete",
        value: function _transitionComplete(element, active, callback) {
          if (active) {
            $(active).removeClass("".concat(ClassName.SHOW, " ").concat(ClassName.ACTIVE));
            var dropdownChild = $(active.parentNode).find(Selector.DROPDOWN_ACTIVE_CHILD)[0];

            if (dropdownChild) {
              $(dropdownChild).removeClass(ClassName.ACTIVE);
            }

            if (active.getAttribute('role') === 'tab') {
              active.setAttribute('aria-selected', false);
            }
          }

          $(element).addClass(ClassName.ACTIVE);

          if (element.getAttribute('role') === 'tab') {
            element.setAttribute('aria-selected', true);
          }

          _util2.default.reflow(element);

          $(element).addClass(ClassName.SHOW);

          if (element.parentNode && $(element.parentNode).hasClass(ClassName.DROPDOWN_MENU)) {
            var dropdownElement = $(element).closest(Selector.DROPDOWN)[0];

            if (dropdownElement) {
              $(dropdownElement).find(Selector.DROPDOWN_TOGGLE).addClass(ClassName.ACTIVE);
            }

            element.setAttribute('aria-expanded', true);
          }

          if (callback) {
            callback();
          }
        }
      }], [{
        key: "_jQueryInterface",
        value: function _jQueryInterface(config) {
          return this.each(function () {
            var $this = $(this);
            var data = $this.data(DATA_KEY);

            if (!data) {
              data = new Tab(this);
              $this.data(DATA_KEY, data);
            }

            if (typeof config === 'string') {
              if (typeof data[config] === 'undefined') {
                throw new TypeError("No method named \"".concat(config, "\""));
              }

              data[config]();
            }
          });
        }
      }, {
        key: "VERSION",
        get: function get() {
          return VERSION;
        }
      }]);

      return Tab;
    }();

    /**
     * ------------------------------------------------------------------------
     * Data Api implementation
     * ------------------------------------------------------------------------
     */
    $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
      event.preventDefault();

      Tab._jQueryInterface.call($(this), 'show');
    });
    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */

    $.fn[NAME] = Tab._jQueryInterface;
    $.fn[NAME].Constructor = Tab;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return Tab._jQueryInterface;
    };

    return Tab;
  }(_jquery2.default);

  exports.default = Tab;
});