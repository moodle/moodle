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
  var NAME = 'alert';
  var VERSION = '4.3.1';
  var DATA_KEY = 'bs.alert';
  var EVENT_KEY = ".".concat(DATA_KEY);
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = _jquery2.default.fn[NAME];
  var Selector = {
    DISMISS: '[data-dismiss="alert"]'
  };
  var Event = {
    CLOSE: "close".concat(EVENT_KEY),
    CLOSED: "closed".concat(EVENT_KEY),
    CLICK_DATA_API: "click".concat(EVENT_KEY).concat(DATA_API_KEY)
  };
  var ClassName = {
    ALERT: 'alert',
    FADE: 'fade',
    SHOW: 'show'
    /**
     * ------------------------------------------------------------------------
     * Class Definition
     * ------------------------------------------------------------------------
     */

  };

  var Alert = function () {
    function Alert(element) {
      _classCallCheck(this, Alert);

      this._element = element;
    } // Getters


    _createClass(Alert, [{
      key: "close",
      value: function close(element) {
        var rootElement = this._element;

        if (element) {
          rootElement = this._getRootElement(element);
        }

        var customEvent = this._triggerCloseEvent(rootElement);

        if (customEvent.isDefaultPrevented()) {
          return;
        }

        this._removeElement(rootElement);
      }
    }, {
      key: "dispose",
      value: function dispose() {
        _jquery2.default.removeData(this._element, DATA_KEY);

        this._element = null;
      }
    }, {
      key: "_getRootElement",
      value: function _getRootElement(element) {
        var selector = _util2.default.getSelectorFromElement(element);

        var parent = false;

        if (selector) {
          parent = document.querySelector(selector);
        }

        if (!parent) {
          parent = (0, _jquery2.default)(element).closest(".".concat(ClassName.ALERT))[0];
        }

        return parent;
      }
    }, {
      key: "_triggerCloseEvent",
      value: function _triggerCloseEvent(element) {
        var closeEvent = _jquery2.default.Event(Event.CLOSE);

        (0, _jquery2.default)(element).trigger(closeEvent);
        return closeEvent;
      }
    }, {
      key: "_removeElement",
      value: function _removeElement(element) {
        var _this = this;

        (0, _jquery2.default)(element).removeClass(ClassName.SHOW);

        if (!(0, _jquery2.default)(element).hasClass(ClassName.FADE)) {
          this._destroyElement(element);

          return;
        }

        var transitionDuration = _util2.default.getTransitionDurationFromElement(element);

        (0, _jquery2.default)(element).one(_util2.default.TRANSITION_END, function (event) {
          return _this._destroyElement(element, event);
        }).emulateTransitionEnd(transitionDuration);
      }
    }, {
      key: "_destroyElement",
      value: function _destroyElement(element) {
        (0, _jquery2.default)(element).detach().trigger(Event.CLOSED).remove();
      }
    }], [{
      key: "_jQueryInterface",
      value: function _jQueryInterface(config) {
        return this.each(function () {
          var $element = (0, _jquery2.default)(this);
          var data = $element.data(DATA_KEY);

          if (!data) {
            data = new Alert(this);
            $element.data(DATA_KEY, data);
          }

          if (config === 'close') {
            data[config](this);
          }
        });
      }
    }, {
      key: "_handleDismiss",
      value: function _handleDismiss(alertInstance) {
        return function (event) {
          if (event) {
            event.preventDefault();
          }

          alertInstance.close(this);
        };
      }
    }, {
      key: "VERSION",
      get: function get() {
        return VERSION;
      }
    }]);

    return Alert;
  }();

  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */
  (0, _jquery2.default)(document).on(Event.CLICK_DATA_API, Selector.DISMISS, Alert._handleDismiss(new Alert()));
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  _jquery2.default.fn[NAME] = Alert._jQueryInterface;
  _jquery2.default.fn[NAME].Constructor = Alert;

  _jquery2.default.fn[NAME].noConflict = function () {
    _jquery2.default.fn[NAME] = JQUERY_NO_CONFLICT;
    return Alert._jQueryInterface;
  };

  exports.default = Alert;
});