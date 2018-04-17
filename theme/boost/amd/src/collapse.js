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

  function _extends() {
    _extends = Object.assign || function (target) {
      for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i];

        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }

      return target;
    };

    return _extends.apply(this, arguments);
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
   * Bootstrap (v4.0.0): collapse.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * --------------------------------------------------------------------------
   */
  var Collapse = function ($) {
    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */
    var NAME = 'collapse';
    var VERSION = '4.0.0';
    var DATA_KEY = 'bs.collapse';
    var EVENT_KEY = ".".concat(DATA_KEY);
    var DATA_API_KEY = '.data-api';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var TRANSITION_DURATION = 600;
    var Default = {
      toggle: true,
      parent: ''
    };
    var DefaultType = {
      toggle: 'boolean',
      parent: '(string|element)'
    };
    var Event = {
      SHOW: "show".concat(EVENT_KEY),
      SHOWN: "shown".concat(EVENT_KEY),
      HIDE: "hide".concat(EVENT_KEY),
      HIDDEN: "hidden".concat(EVENT_KEY),
      CLICK_DATA_API: "click".concat(EVENT_KEY).concat(DATA_API_KEY)
    };
    var ClassName = {
      SHOW: 'show',
      COLLAPSE: 'collapse',
      COLLAPSING: 'collapsing',
      COLLAPSED: 'collapsed'
    };
    var Dimension = {
      WIDTH: 'width',
      HEIGHT: 'height'
    };
    var Selector = {
      ACTIVES: '.show, .collapsing',
      DATA_TOGGLE: '[data-toggle="collapse"]'
      /**
       * ------------------------------------------------------------------------
       * Class Definition
       * ------------------------------------------------------------------------
       */

    };

    var Collapse = function () {
      function Collapse(element, config) {
        _classCallCheck(this, Collapse);

        this._isTransitioning = false;
        this._element = element;
        this._config = this._getConfig(config);
        this._triggerArray = $.makeArray($("[data-toggle=\"collapse\"][href=\"#".concat(element.id, "\"],") + "[data-toggle=\"collapse\"][data-target=\"#".concat(element.id, "\"]")));
        var tabToggles = $(Selector.DATA_TOGGLE);

        for (var i = 0; i < tabToggles.length; i++) {
          var elem = tabToggles[i];

          var selector = _util2.default.getSelectorFromElement(elem);

          if (selector !== null && $(selector).filter(element).length > 0) {
            this._selector = selector;

            this._triggerArray.push(elem);
          }
        }

        this._parent = this._config.parent ? this._getParent() : null;

        if (!this._config.parent) {
          this._addAriaAndCollapsedClass(this._element, this._triggerArray);
        }

        if (this._config.toggle) {
          this.toggle();
        }
      } // Getters


      _createClass(Collapse, [{
        key: "toggle",
        value: function toggle() {
          if ($(this._element).hasClass(ClassName.SHOW)) {
            this.hide();
          } else {
            this.show();
          }
        }
      }, {
        key: "show",
        value: function show() {
          var _this = this;

          if (this._isTransitioning || $(this._element).hasClass(ClassName.SHOW)) {
            return;
          }

          var actives;
          var activesData;

          if (this._parent) {
            actives = $.makeArray($(this._parent).find(Selector.ACTIVES).filter("[data-parent=\"".concat(this._config.parent, "\"]")));

            if (actives.length === 0) {
              actives = null;
            }
          }

          if (actives) {
            activesData = $(actives).not(this._selector).data(DATA_KEY);

            if (activesData && activesData._isTransitioning) {
              return;
            }
          }

          var startEvent = $.Event(Event.SHOW);
          $(this._element).trigger(startEvent);

          if (startEvent.isDefaultPrevented()) {
            return;
          }

          if (actives) {
            Collapse._jQueryInterface.call($(actives).not(this._selector), 'hide');

            if (!activesData) {
              $(actives).data(DATA_KEY, null);
            }
          }

          var dimension = this._getDimension();

          $(this._element).removeClass(ClassName.COLLAPSE).addClass(ClassName.COLLAPSING);
          this._element.style[dimension] = 0;

          if (this._triggerArray.length > 0) {
            $(this._triggerArray).removeClass(ClassName.COLLAPSED).attr('aria-expanded', true);
          }

          this.setTransitioning(true);

          var complete = function complete() {
            $(_this._element).removeClass(ClassName.COLLAPSING).addClass(ClassName.COLLAPSE).addClass(ClassName.SHOW);
            _this._element.style[dimension] = '';

            _this.setTransitioning(false);

            $(_this._element).trigger(Event.SHOWN);
          };

          if (!_util2.default.supportsTransitionEnd()) {
            complete();
            return;
          }

          var capitalizedDimension = dimension[0].toUpperCase() + dimension.slice(1);
          var scrollSize = "scroll".concat(capitalizedDimension);
          $(this._element).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
          this._element.style[dimension] = "".concat(this._element[scrollSize], "px");
        }
      }, {
        key: "hide",
        value: function hide() {
          var _this2 = this;

          if (this._isTransitioning || !$(this._element).hasClass(ClassName.SHOW)) {
            return;
          }

          var startEvent = $.Event(Event.HIDE);
          $(this._element).trigger(startEvent);

          if (startEvent.isDefaultPrevented()) {
            return;
          }

          var dimension = this._getDimension();

          this._element.style[dimension] = "".concat(this._element.getBoundingClientRect()[dimension], "px");

          _util2.default.reflow(this._element);

          $(this._element).addClass(ClassName.COLLAPSING).removeClass(ClassName.COLLAPSE).removeClass(ClassName.SHOW);

          if (this._triggerArray.length > 0) {
            for (var i = 0; i < this._triggerArray.length; i++) {
              var trigger = this._triggerArray[i];

              var selector = _util2.default.getSelectorFromElement(trigger);

              if (selector !== null) {
                var $elem = $(selector);

                if (!$elem.hasClass(ClassName.SHOW)) {
                  $(trigger).addClass(ClassName.COLLAPSED).attr('aria-expanded', false);
                }
              }
            }
          }

          this.setTransitioning(true);

          var complete = function complete() {
            _this2.setTransitioning(false);

            $(_this2._element).removeClass(ClassName.COLLAPSING).addClass(ClassName.COLLAPSE).trigger(Event.HIDDEN);
          };

          this._element.style[dimension] = '';

          if (!_util2.default.supportsTransitionEnd()) {
            complete();
            return;
          }

          $(this._element).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
        }
      }, {
        key: "setTransitioning",
        value: function setTransitioning(isTransitioning) {
          this._isTransitioning = isTransitioning;
        }
      }, {
        key: "dispose",
        value: function dispose() {
          $.removeData(this._element, DATA_KEY);
          this._config = null;
          this._parent = null;
          this._element = null;
          this._triggerArray = null;
          this._isTransitioning = null;
        }
      }, {
        key: "_getConfig",
        value: function _getConfig(config) {
          config = _extends({}, Default, config);
          config.toggle = Boolean(config.toggle); // Coerce string values

          _util2.default.typeCheckConfig(NAME, config, DefaultType);

          return config;
        }
      }, {
        key: "_getDimension",
        value: function _getDimension() {
          var hasWidth = $(this._element).hasClass(Dimension.WIDTH);
          return hasWidth ? Dimension.WIDTH : Dimension.HEIGHT;
        }
      }, {
        key: "_getParent",
        value: function _getParent() {
          var _this3 = this;

          var parent = null;

          if (_util2.default.isElement(this._config.parent)) {
            parent = this._config.parent; // It's a jQuery object

            if (typeof this._config.parent.jquery !== 'undefined') {
              parent = this._config.parent[0];
            }
          } else {
            parent = $(this._config.parent)[0];
          }

          var selector = "[data-toggle=\"collapse\"][data-parent=\"".concat(this._config.parent, "\"]");
          $(parent).find(selector).each(function (i, element) {
            _this3._addAriaAndCollapsedClass(Collapse._getTargetFromElement(element), [element]);
          });
          return parent;
        }
      }, {
        key: "_addAriaAndCollapsedClass",
        value: function _addAriaAndCollapsedClass(element, triggerArray) {
          if (element) {
            var isOpen = $(element).hasClass(ClassName.SHOW);

            if (triggerArray.length > 0) {
              $(triggerArray).toggleClass(ClassName.COLLAPSED, !isOpen).attr('aria-expanded', isOpen);
            }
          }
        }
      }], [{
        key: "_getTargetFromElement",
        value: function _getTargetFromElement(element) {
          var selector = _util2.default.getSelectorFromElement(element);

          return selector ? $(selector)[0] : null;
        }
      }, {
        key: "_jQueryInterface",
        value: function _jQueryInterface(config) {
          return this.each(function () {
            var $this = $(this);
            var data = $this.data(DATA_KEY);

            var _config = _extends({}, Default, $this.data(), _typeof(config) === 'object' && config);

            if (!data && _config.toggle && /show|hide/.test(config)) {
              _config.toggle = false;
            }

            if (!data) {
              data = new Collapse(this, _config);
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
      }, {
        key: "Default",
        get: function get() {
          return Default;
        }
      }]);

      return Collapse;
    }();

    /**
     * ------------------------------------------------------------------------
     * Data Api implementation
     * ------------------------------------------------------------------------
     */
    $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
      // preventDefault only for <a> elements (which change the URL) not inside the collapsible element
      if (event.currentTarget.tagName === 'A') {
        event.preventDefault();
      }

      var $trigger = $(this);

      var selector = _util2.default.getSelectorFromElement(this);

      $(selector).each(function () {
        var $target = $(this);
        var data = $target.data(DATA_KEY);
        var config = data ? 'toggle' : $trigger.data();

        Collapse._jQueryInterface.call($target, config);
      });
    });
    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */

    $.fn[NAME] = Collapse._jQueryInterface;
    $.fn[NAME].Constructor = Collapse;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return Collapse._jQueryInterface;
    };

    return Collapse;
  }(_jquery2.default);

  exports.default = Collapse;
});