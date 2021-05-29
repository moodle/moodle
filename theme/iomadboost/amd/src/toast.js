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

  function _typeof(obj) {
    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
      _typeof = function _typeof(obj) {
        return typeof obj;
      };
    } else {
      _typeof = function _typeof(obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
      };
    }

    return _typeof(obj);
  }

  function _objectSpread(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};
      var ownKeys = Object.keys(source);

      if (typeof Object.getOwnPropertySymbols === 'function') {
        ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
          return Object.getOwnPropertyDescriptor(source, sym).enumerable;
        }));
      }

      ownKeys.forEach(function (key) {
        _defineProperty(target, key, source[key]);
      });
    }

    return target;
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
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
  var NAME = 'toast';
  var VERSION = '4.3.1';
  var DATA_KEY = 'bs.toast';
  var EVENT_KEY = ".".concat(DATA_KEY);
  var JQUERY_NO_CONFLICT = _jquery2.default.fn[NAME];
  var Event = {
    CLICK_DISMISS: "click.dismiss".concat(EVENT_KEY),
    HIDE: "hide".concat(EVENT_KEY),
    HIDDEN: "hidden".concat(EVENT_KEY),
    SHOW: "show".concat(EVENT_KEY),
    SHOWN: "shown".concat(EVENT_KEY)
  };
  var ClassName = {
    FADE: 'fade',
    HIDE: 'hide',
    SHOW: 'show',
    SHOWING: 'showing'
  };
  var DefaultType = {
    animation: 'boolean',
    autohide: 'boolean',
    delay: 'number'
  };
  var Default = {
    animation: true,
    autohide: true,
    delay: 500
  };
  var Selector = {
    DATA_DISMISS: '[data-dismiss="toast"]'
    /**
     * ------------------------------------------------------------------------
     * Class Definition
     * ------------------------------------------------------------------------
     */

  };

  var Toast = function () {
    function Toast(element, config) {
      _classCallCheck(this, Toast);

      this._element = element;
      this._config = this._getConfig(config);
      this._timeout = null;

      this._setListeners();
    } // Getters


    _createClass(Toast, [{
      key: "show",
      value: function show() {
        var _this = this;

        (0, _jquery2.default)(this._element).trigger(Event.SHOW);

        if (this._config.animation) {
          this._element.classList.add(ClassName.FADE);
        }

        var complete = function complete() {
          _this._element.classList.remove(ClassName.SHOWING);

          _this._element.classList.add(ClassName.SHOW);

          (0, _jquery2.default)(_this._element).trigger(Event.SHOWN);

          if (_this._config.autohide) {
            _this.hide();
          }
        };

        this._element.classList.remove(ClassName.HIDE);

        this._element.classList.add(ClassName.SHOWING);

        if (this._config.animation) {
          var transitionDuration = _util2.default.getTransitionDurationFromElement(this._element);

          (0, _jquery2.default)(this._element).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
        } else {
          complete();
        }
      }
    }, {
      key: "hide",
      value: function hide(withoutTimeout) {
        var _this2 = this;

        if (!this._element.classList.contains(ClassName.SHOW)) {
          return;
        }

        (0, _jquery2.default)(this._element).trigger(Event.HIDE);

        if (withoutTimeout) {
          this._close();
        } else {
          this._timeout = setTimeout(function () {
            _this2._close();
          }, this._config.delay);
        }
      }
    }, {
      key: "dispose",
      value: function dispose() {
        clearTimeout(this._timeout);
        this._timeout = null;

        if (this._element.classList.contains(ClassName.SHOW)) {
          this._element.classList.remove(ClassName.SHOW);
        }

        (0, _jquery2.default)(this._element).off(Event.CLICK_DISMISS);

        _jquery2.default.removeData(this._element, DATA_KEY);

        this._element = null;
        this._config = null;
      }
    }, {
      key: "_getConfig",
      value: function _getConfig(config) {
        config = _objectSpread({}, Default, (0, _jquery2.default)(this._element).data(), _typeof(config) === 'object' && config ? config : {});

        _util2.default.typeCheckConfig(NAME, config, this.constructor.DefaultType);

        return config;
      }
    }, {
      key: "_setListeners",
      value: function _setListeners() {
        var _this3 = this;

        (0, _jquery2.default)(this._element).on(Event.CLICK_DISMISS, Selector.DATA_DISMISS, function () {
          return _this3.hide(true);
        });
      }
    }, {
      key: "_close",
      value: function _close() {
        var _this4 = this;

        var complete = function complete() {
          _this4._element.classList.add(ClassName.HIDE);

          (0, _jquery2.default)(_this4._element).trigger(Event.HIDDEN);
        };

        this._element.classList.remove(ClassName.SHOW);

        if (this._config.animation) {
          var transitionDuration = _util2.default.getTransitionDurationFromElement(this._element);

          (0, _jquery2.default)(this._element).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
        } else {
          complete();
        }
      }
    }], [{
      key: "_jQueryInterface",
      value: function _jQueryInterface(config) {
        return this.each(function () {
          var $element = (0, _jquery2.default)(this);
          var data = $element.data(DATA_KEY);

          var _config = _typeof(config) === 'object' && config;

          if (!data) {
            data = new Toast(this, _config);
            $element.data(DATA_KEY, data);
          }

          if (typeof config === 'string') {
            if (typeof data[config] === 'undefined') {
              throw new TypeError("No method named \"".concat(config, "\""));
            }

            data[config](this);
          }
        });
      }
    }, {
      key: "VERSION",
      get: function get() {
        return VERSION;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default;
      }
    }]);

    return Toast;
  }();

  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */
  _jquery2.default.fn[NAME] = Toast._jQueryInterface;
  _jquery2.default.fn[NAME].Constructor = Toast;

  _jquery2.default.fn[NAME].noConflict = function () {
    _jquery2.default.fn[NAME] = JQUERY_NO_CONFLICT;
    return Toast._jQueryInterface;
  };

  exports.default = Toast;
});