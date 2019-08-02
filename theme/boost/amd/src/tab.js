"use strict";

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
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */
  var NAME = 'tab';
  var VERSION = '4.3.1';
  var DATA_KEY = 'bs.tab';
  var EVENT_KEY = ".".concat(DATA_KEY);
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = _jquery2.default.fn[NAME];
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

        if (this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE && (0, _jquery2.default)(this._element).hasClass(ClassName.ACTIVE) || (0, _jquery2.default)(this._element).hasClass(ClassName.DISABLED)) {
          return;
        }

        var target;
        var previous;
        var listElement = (0, _jquery2.default)(this._element).closest(Selector.NAV_LIST_GROUP)[0];

        var selector = _util2.default.getSelectorFromElement(this._element);

        if (listElement) {
          var itemSelector = listElement.nodeName === 'UL' || listElement.nodeName === 'OL' ? Selector.ACTIVE_UL : Selector.ACTIVE;
          previous = _jquery2.default.makeArray((0, _jquery2.default)(listElement).find(itemSelector));
          previous = previous[previous.length - 1];
        }

        var hideEvent = _jquery2.default.Event(Event.HIDE, {
          relatedTarget: this._element
        });

        var showEvent = _jquery2.default.Event(Event.SHOW, {
          relatedTarget: previous
        });

        if (previous) {
          (0, _jquery2.default)(previous).trigger(hideEvent);
        }

        (0, _jquery2.default)(this._element).trigger(showEvent);

        if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) {
          return;
        }

        if (selector) {
          target = document.querySelector(selector);
        }

        this._activate(this._element, listElement);

        var complete = function complete() {
          var hiddenEvent = _jquery2.default.Event(Event.HIDDEN, {
            relatedTarget: _this._element
          });

          var shownEvent = _jquery2.default.Event(Event.SHOWN, {
            relatedTarget: previous
          });

          (0, _jquery2.default)(previous).trigger(hiddenEvent);
          (0, _jquery2.default)(_this._element).trigger(shownEvent);
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
        _jquery2.default.removeData(this._element, DATA_KEY);

        this._element = null;
      }
    }, {
      key: "_activate",
      value: function _activate(element, container, callback) {
        var _this2 = this;

        var activeElements = container && (container.nodeName === 'UL' || container.nodeName === 'OL') ? (0, _jquery2.default)(container).find(Selector.ACTIVE_UL) : (0, _jquery2.default)(container).children(Selector.ACTIVE);
        var active = activeElements[0];
        var isTransitioning = callback && active && (0, _jquery2.default)(active).hasClass(ClassName.FADE);

        var complete = function complete() {
          return _this2._transitionComplete(element, active, callback);
        };

        if (active && isTransitioning) {
          var transitionDuration = _util2.default.getTransitionDurationFromElement(active);

          (0, _jquery2.default)(active).removeClass(ClassName.SHOW).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
        } else {
          complete();
        }
      }
    }, {
      key: "_transitionComplete",
      value: function _transitionComplete(element, active, callback) {
        if (active) {
          (0, _jquery2.default)(active).removeClass(ClassName.ACTIVE);
          var dropdownChild = (0, _jquery2.default)(active.parentNode).find(Selector.DROPDOWN_ACTIVE_CHILD)[0];

          if (dropdownChild) {
            (0, _jquery2.default)(dropdownChild).removeClass(ClassName.ACTIVE);
          }

          if (active.getAttribute('role') === 'tab') {
            active.setAttribute('aria-selected', false);
          }
        }

        (0, _jquery2.default)(element).addClass(ClassName.ACTIVE);

        if (element.getAttribute('role') === 'tab') {
          element.setAttribute('aria-selected', true);
        }

        _util2.default.reflow(element);

        if (element.classList.contains(ClassName.FADE)) {
          element.classList.add(ClassName.SHOW);
        }

        if (element.parentNode && (0, _jquery2.default)(element.parentNode).hasClass(ClassName.DROPDOWN_MENU)) {
          var dropdownElement = (0, _jquery2.default)(element).closest(Selector.DROPDOWN)[0];

          if (dropdownElement) {
            var dropdownToggleList = [].slice.call(dropdownElement.querySelectorAll(Selector.DROPDOWN_TOGGLE));
            (0, _jquery2.default)(dropdownToggleList).addClass(ClassName.ACTIVE);
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
          var $this = (0, _jquery2.default)(this);
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
  (0, _jquery2.default)(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
    event.preventDefault();

    Tab._jQueryInterface.call((0, _jquery2.default)(this), 'show');
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  _jquery2.default.fn[NAME] = Tab._jQueryInterface;
  _jquery2.default.fn[NAME].Constructor = Tab;

  _jquery2.default.fn[NAME].noConflict = function () {
    _jquery2.default.fn[NAME] = JQUERY_NO_CONFLICT;
    return Tab._jQueryInterface;
  };

  exports.default = Tab;
});