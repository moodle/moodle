"use strict";

define(["exports", "jquery", "core/popper", "./util"], function (exports, _jquery, _popper, _util) {
  "use strict";

  Object.defineProperty(exports, "__esModule", {
    value: true
  });

  var _jquery2 = _interopRequireDefault(_jquery);

  var _popper2 = _interopRequireDefault(_popper);

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
  var NAME = 'dropdown';
  var VERSION = '4.3.1';
  var DATA_KEY = 'bs.dropdown';
  var EVENT_KEY = ".".concat(DATA_KEY);
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = _jquery2.default.fn[NAME];
  var ESCAPE_KEYCODE = 27; // KeyboardEvent.which value for Escape (Esc) key

  var SPACE_KEYCODE = 32; // KeyboardEvent.which value for space key

  var TAB_KEYCODE = 9; // KeyboardEvent.which value for tab key

  var ARROW_UP_KEYCODE = 38; // KeyboardEvent.which value for up arrow key

  var ARROW_DOWN_KEYCODE = 40; // KeyboardEvent.which value for down arrow key

  var RIGHT_MOUSE_BUTTON_WHICH = 3; // MouseEvent.which value for the right button (assuming a right-handed mouse)

  var REGEXP_KEYDOWN = new RegExp("".concat(ARROW_UP_KEYCODE, "|").concat(ARROW_DOWN_KEYCODE, "|").concat(ESCAPE_KEYCODE));
  var Event = {
    HIDE: "hide".concat(EVENT_KEY),
    HIDDEN: "hidden".concat(EVENT_KEY),
    SHOW: "show".concat(EVENT_KEY),
    SHOWN: "shown".concat(EVENT_KEY),
    CLICK: "click".concat(EVENT_KEY),
    CLICK_DATA_API: "click".concat(EVENT_KEY).concat(DATA_API_KEY),
    KEYDOWN_DATA_API: "keydown".concat(EVENT_KEY).concat(DATA_API_KEY),
    KEYUP_DATA_API: "keyup".concat(EVENT_KEY).concat(DATA_API_KEY)
  };
  var ClassName = {
    DISABLED: 'disabled',
    SHOW: 'show',
    DROPUP: 'dropup',
    DROPRIGHT: 'dropright',
    DROPLEFT: 'dropleft',
    MENURIGHT: 'dropdown-menu-right',
    MENULEFT: 'dropdown-menu-left',
    POSITION_STATIC: 'position-static'
  };
  var Selector = {
    DATA_TOGGLE: '[data-toggle="dropdown"]',
    FORM_CHILD: '.dropdown form',
    MENU: '.dropdown-menu',
    NAVBAR_NAV: '.navbar-nav',
    VISIBLE_ITEMS: '.dropdown-menu .dropdown-item:not(.disabled):not(:disabled)'
  };
  var AttachmentMap = {
    TOP: 'top-start',
    TOPEND: 'top-end',
    BOTTOM: 'bottom-start',
    BOTTOMEND: 'bottom-end',
    RIGHT: 'right-start',
    RIGHTEND: 'right-end',
    LEFT: 'left-start',
    LEFTEND: 'left-end'
  };
  var Default = {
    offset: 0,
    flip: true,
    boundary: 'scrollParent',
    reference: 'toggle',
    display: 'dynamic'
  };
  var DefaultType = {
    offset: '(number|string|function)',
    flip: 'boolean',
    boundary: '(string|element)',
    reference: '(string|element)',
    display: 'string'
    /**
     * ------------------------------------------------------------------------
     * Class Definition
     * ------------------------------------------------------------------------
     */

  };

  var Dropdown = function () {
    function Dropdown(element, config) {
      _classCallCheck(this, Dropdown);

      this._element = element;
      this._popper = null;
      this._config = this._getConfig(config);
      this._menu = this._getMenuElement();
      this._inNavbar = this._detectNavbar();

      this._addEventListeners();
    } // Getters


    _createClass(Dropdown, [{
      key: "toggle",
      value: function toggle() {
        if (this._element.disabled || (0, _jquery2.default)(this._element).hasClass(ClassName.DISABLED)) {
          return;
        }

        var parent = Dropdown._getParentFromElement(this._element);

        var isActive = (0, _jquery2.default)(this._menu).hasClass(ClassName.SHOW);

        Dropdown._clearMenus();

        if (isActive) {
          return;
        }

        var relatedTarget = {
          relatedTarget: this._element
        };

        var showEvent = _jquery2.default.Event(Event.SHOW, relatedTarget);

        (0, _jquery2.default)(parent).trigger(showEvent);

        if (showEvent.isDefaultPrevented()) {
          return;
        } // Disable totally Popper.js for Dropdown in Navbar


        if (!this._inNavbar) {
          /**
           * Check for Popper dependency
           * Popper - https://popper.js.org
           */
          if (typeof _popper2.default === 'undefined') {
            throw new TypeError('Bootstrap\'s dropdowns require Popper.js (https://popper.js.org/)');
          }

          var referenceElement = this._element;

          if (this._config.reference === 'parent') {
            referenceElement = parent;
          } else if (_util2.default.isElement(this._config.reference)) {
            referenceElement = this._config.reference; // Check if it's jQuery element

            if (typeof this._config.reference.jquery !== 'undefined') {
              referenceElement = this._config.reference[0];
            }
          } // If boundary is not `scrollParent`, then set position to `static`
          // to allow the menu to "escape" the scroll parent's boundaries
          // https://github.com/twbs/bootstrap/issues/24251


          if (this._config.boundary !== 'scrollParent') {
            (0, _jquery2.default)(parent).addClass(ClassName.POSITION_STATIC);
          }

          this._popper = new _popper2.default(referenceElement, this._menu, this._getPopperConfig());
        } // If this is a touch-enabled device we add extra
        // empty mouseover listeners to the body's immediate children;
        // only needed because of broken event delegation on iOS
        // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html


        if ('ontouchstart' in document.documentElement && (0, _jquery2.default)(parent).closest(Selector.NAVBAR_NAV).length === 0) {
          (0, _jquery2.default)(document.body).children().on('mouseover', null, _jquery2.default.noop);
        }

        this._element.focus();

        this._element.setAttribute('aria-expanded', true);

        (0, _jquery2.default)(this._menu).toggleClass(ClassName.SHOW);
        (0, _jquery2.default)(parent).toggleClass(ClassName.SHOW).trigger(_jquery2.default.Event(Event.SHOWN, relatedTarget));
      }
    }, {
      key: "show",
      value: function show() {
        if (this._element.disabled || (0, _jquery2.default)(this._element).hasClass(ClassName.DISABLED) || (0, _jquery2.default)(this._menu).hasClass(ClassName.SHOW)) {
          return;
        }

        var relatedTarget = {
          relatedTarget: this._element
        };

        var showEvent = _jquery2.default.Event(Event.SHOW, relatedTarget);

        var parent = Dropdown._getParentFromElement(this._element);

        (0, _jquery2.default)(parent).trigger(showEvent);

        if (showEvent.isDefaultPrevented()) {
          return;
        }

        (0, _jquery2.default)(this._menu).toggleClass(ClassName.SHOW);
        (0, _jquery2.default)(parent).toggleClass(ClassName.SHOW).trigger(_jquery2.default.Event(Event.SHOWN, relatedTarget));
      }
    }, {
      key: "hide",
      value: function hide() {
        if (this._element.disabled || (0, _jquery2.default)(this._element).hasClass(ClassName.DISABLED) || !(0, _jquery2.default)(this._menu).hasClass(ClassName.SHOW)) {
          return;
        }

        var relatedTarget = {
          relatedTarget: this._element
        };

        var hideEvent = _jquery2.default.Event(Event.HIDE, relatedTarget);

        var parent = Dropdown._getParentFromElement(this._element);

        (0, _jquery2.default)(parent).trigger(hideEvent);

        if (hideEvent.isDefaultPrevented()) {
          return;
        }

        (0, _jquery2.default)(this._menu).toggleClass(ClassName.SHOW);
        (0, _jquery2.default)(parent).toggleClass(ClassName.SHOW).trigger(_jquery2.default.Event(Event.HIDDEN, relatedTarget));
      }
    }, {
      key: "dispose",
      value: function dispose() {
        _jquery2.default.removeData(this._element, DATA_KEY);

        (0, _jquery2.default)(this._element).off(EVENT_KEY);
        this._element = null;
        this._menu = null;

        if (this._popper !== null) {
          this._popper.destroy();

          this._popper = null;
        }
      }
    }, {
      key: "update",
      value: function update() {
        this._inNavbar = this._detectNavbar();

        if (this._popper !== null) {
          this._popper.scheduleUpdate();
        }
      }
    }, {
      key: "_addEventListeners",
      value: function _addEventListeners() {
        var _this = this;

        (0, _jquery2.default)(this._element).on(Event.CLICK, function (event) {
          event.preventDefault();
          event.stopPropagation();

          _this.toggle();
        });
      }
    }, {
      key: "_getConfig",
      value: function _getConfig(config) {
        config = _objectSpread({}, this.constructor.Default, (0, _jquery2.default)(this._element).data(), config);

        _util2.default.typeCheckConfig(NAME, config, this.constructor.DefaultType);

        return config;
      }
    }, {
      key: "_getMenuElement",
      value: function _getMenuElement() {
        if (!this._menu) {
          var parent = Dropdown._getParentFromElement(this._element);

          if (parent) {
            this._menu = parent.querySelector(Selector.MENU);
          }
        }

        return this._menu;
      }
    }, {
      key: "_getPlacement",
      value: function _getPlacement() {
        var $parentDropdown = (0, _jquery2.default)(this._element.parentNode);
        var placement = AttachmentMap.BOTTOM; // Handle dropup

        if ($parentDropdown.hasClass(ClassName.DROPUP)) {
          placement = AttachmentMap.TOP;

          if ((0, _jquery2.default)(this._menu).hasClass(ClassName.MENURIGHT)) {
            placement = AttachmentMap.TOPEND;
          }
        } else if ($parentDropdown.hasClass(ClassName.DROPRIGHT)) {
          placement = AttachmentMap.RIGHT;
        } else if ($parentDropdown.hasClass(ClassName.DROPLEFT)) {
          placement = AttachmentMap.LEFT;
        } else if ((0, _jquery2.default)(this._menu).hasClass(ClassName.MENURIGHT)) {
          placement = AttachmentMap.BOTTOMEND;
        }

        return placement;
      }
    }, {
      key: "_detectNavbar",
      value: function _detectNavbar() {
        return (0, _jquery2.default)(this._element).closest('.navbar').length > 0;
      }
    }, {
      key: "_getOffset",
      value: function _getOffset() {
        var _this2 = this;

        var offset = {};

        if (typeof this._config.offset === 'function') {
          offset.fn = function (data) {
            data.offsets = _objectSpread({}, data.offsets, _this2._config.offset(data.offsets, _this2._element) || {});
            return data;
          };
        } else {
          offset.offset = this._config.offset;
        }

        return offset;
      }
    }, {
      key: "_getPopperConfig",
      value: function _getPopperConfig() {
        var popperConfig = {
          placement: this._getPlacement(),
          modifiers: {
            offset: this._getOffset(),
            flip: {
              enabled: this._config.flip
            },
            preventOverflow: {
              boundariesElement: this._config.boundary
            }
          } // Disable Popper.js if we have a static display

        };

        if (this._config.display === 'static') {
          popperConfig.modifiers.applyStyle = {
            enabled: false
          };
        }

        return popperConfig;
      }
    }], [{
      key: "_jQueryInterface",
      value: function _jQueryInterface(config) {
        return this.each(function () {
          var data = (0, _jquery2.default)(this).data(DATA_KEY);

          var _config = _typeof(config) === 'object' ? config : null;

          if (!data) {
            data = new Dropdown(this, _config);
            (0, _jquery2.default)(this).data(DATA_KEY, data);
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
      key: "_clearMenus",
      value: function _clearMenus(event) {
        if (event && (event.which === RIGHT_MOUSE_BUTTON_WHICH || event.type === 'keyup' && event.which !== TAB_KEYCODE)) {
          return;
        }

        var toggles = [].slice.call(document.querySelectorAll(Selector.DATA_TOGGLE));

        for (var i = 0, len = toggles.length; i < len; i++) {
          var parent = Dropdown._getParentFromElement(toggles[i]);

          var context = (0, _jquery2.default)(toggles[i]).data(DATA_KEY);
          var relatedTarget = {
            relatedTarget: toggles[i]
          };

          if (event && event.type === 'click') {
            relatedTarget.clickEvent = event;
          }

          if (!context) {
            continue;
          }

          var dropdownMenu = context._menu;

          if (!(0, _jquery2.default)(parent).hasClass(ClassName.SHOW)) {
            continue;
          }

          if (event && (event.type === 'click' && /input|textarea/i.test(event.target.tagName) || event.type === 'keyup' && event.which === TAB_KEYCODE) && _jquery2.default.contains(parent, event.target)) {
            continue;
          }

          var hideEvent = _jquery2.default.Event(Event.HIDE, relatedTarget);

          (0, _jquery2.default)(parent).trigger(hideEvent);

          if (hideEvent.isDefaultPrevented()) {
            continue;
          } // If this is a touch-enabled device we remove the extra
          // empty mouseover listeners we added for iOS support


          if ('ontouchstart' in document.documentElement) {
            (0, _jquery2.default)(document.body).children().off('mouseover', null, _jquery2.default.noop);
          }

          toggles[i].setAttribute('aria-expanded', 'false');
          (0, _jquery2.default)(dropdownMenu).removeClass(ClassName.SHOW);
          (0, _jquery2.default)(parent).removeClass(ClassName.SHOW).trigger(_jquery2.default.Event(Event.HIDDEN, relatedTarget));
        }
      }
    }, {
      key: "_getParentFromElement",
      value: function _getParentFromElement(element) {
        var parent;

        var selector = _util2.default.getSelectorFromElement(element);

        if (selector) {
          parent = document.querySelector(selector);
        }

        return parent || element.parentNode;
      }
    }, {
      key: "_dataApiKeydownHandler",
      value: function _dataApiKeydownHandler(event) {
        // If not input/textarea:
        //  - And not a key in REGEXP_KEYDOWN => not a dropdown command
        // If input/textarea:
        //  - If space key => not a dropdown command
        //  - If key is other than escape
        //    - If key is not up or down => not a dropdown command
        //    - If trigger inside the menu => not a dropdown command
        if (/input|textarea/i.test(event.target.tagName) ? event.which === SPACE_KEYCODE || event.which !== ESCAPE_KEYCODE && (event.which !== ARROW_DOWN_KEYCODE && event.which !== ARROW_UP_KEYCODE || (0, _jquery2.default)(event.target).closest(Selector.MENU).length) : !REGEXP_KEYDOWN.test(event.which)) {
          return;
        }

        event.preventDefault();
        event.stopPropagation();

        if (this.disabled || (0, _jquery2.default)(this).hasClass(ClassName.DISABLED)) {
          return;
        }

        var parent = Dropdown._getParentFromElement(this);

        var isActive = (0, _jquery2.default)(parent).hasClass(ClassName.SHOW);

        if (!isActive || isActive && (event.which === ESCAPE_KEYCODE || event.which === SPACE_KEYCODE)) {
          if (event.which === ESCAPE_KEYCODE) {
            var toggle = parent.querySelector(Selector.DATA_TOGGLE);
            (0, _jquery2.default)(toggle).trigger('focus');
          }

          (0, _jquery2.default)(this).trigger('click');
          return;
        }

        var items = [].slice.call(parent.querySelectorAll(Selector.VISIBLE_ITEMS));

        if (items.length === 0) {
          return;
        }

        var index = items.indexOf(event.target);

        if (event.which === ARROW_UP_KEYCODE && index > 0) {
          // Up
          index--;
        }

        if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1) {
          // Down
          index++;
        }

        if (index < 0) {
          index = 0;
        }

        items[index].focus();
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
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType;
      }
    }]);

    return Dropdown;
  }();

  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */
  (0, _jquery2.default)(document).on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, Dropdown._dataApiKeydownHandler).on(Event.KEYDOWN_DATA_API, Selector.MENU, Dropdown._dataApiKeydownHandler).on("".concat(Event.CLICK_DATA_API, " ").concat(Event.KEYUP_DATA_API), Dropdown._clearMenus).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
    event.preventDefault();
    event.stopPropagation();

    Dropdown._jQueryInterface.call((0, _jquery2.default)(this), 'toggle');
  }).on(Event.CLICK_DATA_API, Selector.FORM_CHILD, function (e) {
    e.stopPropagation();
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  _jquery2.default.fn[NAME] = Dropdown._jQueryInterface;
  _jquery2.default.fn[NAME].Constructor = Dropdown;

  _jquery2.default.fn[NAME].noConflict = function () {
    _jquery2.default.fn[NAME] = JQUERY_NO_CONFLICT;
    return Dropdown._jQueryInterface;
  };

  exports.default = Dropdown;
});