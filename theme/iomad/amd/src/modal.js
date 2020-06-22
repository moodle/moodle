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
  var NAME = 'modal';
  var VERSION = '4.3.1';
  var DATA_KEY = 'bs.modal';
  var EVENT_KEY = ".".concat(DATA_KEY);
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = _jquery2.default.fn[NAME];
  var ESCAPE_KEYCODE = 27; // KeyboardEvent.which value for Escape (Esc) key

  var Default = {
    backdrop: true,
    keyboard: true,
    focus: true,
    show: true
  };
  var DefaultType = {
    backdrop: '(boolean|string)',
    keyboard: 'boolean',
    focus: 'boolean',
    show: 'boolean'
  };
  var Event = {
    HIDE: "hide".concat(EVENT_KEY),
    HIDDEN: "hidden".concat(EVENT_KEY),
    SHOW: "show".concat(EVENT_KEY),
    SHOWN: "shown".concat(EVENT_KEY),
    FOCUSIN: "focusin".concat(EVENT_KEY),
    RESIZE: "resize".concat(EVENT_KEY),
    CLICK_DISMISS: "click.dismiss".concat(EVENT_KEY),
    KEYDOWN_DISMISS: "keydown.dismiss".concat(EVENT_KEY),
    MOUSEUP_DISMISS: "mouseup.dismiss".concat(EVENT_KEY),
    MOUSEDOWN_DISMISS: "mousedown.dismiss".concat(EVENT_KEY),
    CLICK_DATA_API: "click".concat(EVENT_KEY).concat(DATA_API_KEY)
  };
  var ClassName = {
    SCROLLABLE: 'modal-dialog-scrollable',
    SCROLLBAR_MEASURER: 'modal-scrollbar-measure',
    BACKDROP: 'modal-backdrop',
    OPEN: 'modal-open',
    FADE: 'fade',
    SHOW: 'show'
  };
  var Selector = {
    DIALOG: '.modal-dialog',
    MODAL_BODY: '.modal-body',
    DATA_TOGGLE: '[data-toggle="modal"]',
    DATA_DISMISS: '[data-dismiss="modal"]',
    FIXED_CONTENT: '.fixed-top, .fixed-bottom, .is-fixed, .sticky-top',
    STICKY_CONTENT: '.sticky-top'
    /**
     * ------------------------------------------------------------------------
     * Class Definition
     * ------------------------------------------------------------------------
     */

  };

  var Modal = function () {
    function Modal(element, config) {
      _classCallCheck(this, Modal);

      this._config = this._getConfig(config);
      this._element = element;
      this._dialog = element.querySelector(Selector.DIALOG);
      this._backdrop = null;
      this._isShown = false;
      this._isBodyOverflowing = false;
      this._ignoreBackdropClick = false;
      this._isTransitioning = false;
      this._scrollbarWidth = 0;
    } // Getters


    _createClass(Modal, [{
      key: "toggle",
      value: function toggle(relatedTarget) {
        return this._isShown ? this.hide() : this.show(relatedTarget);
      }
    }, {
      key: "show",
      value: function show(relatedTarget) {
        var _this = this;

        if (this._isShown || this._isTransitioning) {
          return;
        }

        if ((0, _jquery2.default)(this._element).hasClass(ClassName.FADE)) {
          this._isTransitioning = true;
        }

        var showEvent = _jquery2.default.Event(Event.SHOW, {
          relatedTarget: relatedTarget
        });

        (0, _jquery2.default)(this._element).trigger(showEvent);

        if (this._isShown || showEvent.isDefaultPrevented()) {
          return;
        }

        this._isShown = true;

        this._checkScrollbar();

        this._setScrollbar();

        this._adjustDialog();

        this._setEscapeEvent();

        this._setResizeEvent();

        (0, _jquery2.default)(this._element).on(Event.CLICK_DISMISS, Selector.DATA_DISMISS, function (event) {
          return _this.hide(event);
        });
        (0, _jquery2.default)(this._dialog).on(Event.MOUSEDOWN_DISMISS, function () {
          (0, _jquery2.default)(_this._element).one(Event.MOUSEUP_DISMISS, function (event) {
            if ((0, _jquery2.default)(event.target).is(_this._element)) {
              _this._ignoreBackdropClick = true;
            }
          });
        });

        this._showBackdrop(function () {
          return _this._showElement(relatedTarget);
        });
      }
    }, {
      key: "hide",
      value: function hide(event) {
        var _this2 = this;

        if (event) {
          event.preventDefault();
        }

        if (!this._isShown || this._isTransitioning) {
          return;
        }

        var hideEvent = _jquery2.default.Event(Event.HIDE);

        (0, _jquery2.default)(this._element).trigger(hideEvent);

        if (!this._isShown || hideEvent.isDefaultPrevented()) {
          return;
        }

        this._isShown = false;
        var transition = (0, _jquery2.default)(this._element).hasClass(ClassName.FADE);

        if (transition) {
          this._isTransitioning = true;
        }

        this._setEscapeEvent();

        this._setResizeEvent();

        (0, _jquery2.default)(document).off(Event.FOCUSIN);
        (0, _jquery2.default)(this._element).removeClass(ClassName.SHOW);
        (0, _jquery2.default)(this._element).off(Event.CLICK_DISMISS);
        (0, _jquery2.default)(this._dialog).off(Event.MOUSEDOWN_DISMISS);

        if (transition) {
          var transitionDuration = _util2.default.getTransitionDurationFromElement(this._element);

          (0, _jquery2.default)(this._element).one(_util2.default.TRANSITION_END, function (event) {
            return _this2._hideModal(event);
          }).emulateTransitionEnd(transitionDuration);
        } else {
          this._hideModal();
        }
      }
    }, {
      key: "dispose",
      value: function dispose() {
        [window, this._element, this._dialog].forEach(function (htmlElement) {
          return (0, _jquery2.default)(htmlElement).off(EVENT_KEY);
        });
        /**
         * `document` has 2 events `Event.FOCUSIN` and `Event.CLICK_DATA_API`
         * Do not move `document` in `htmlElements` array
         * It will remove `Event.CLICK_DATA_API` event that should remain
         */

        (0, _jquery2.default)(document).off(Event.FOCUSIN);

        _jquery2.default.removeData(this._element, DATA_KEY);

        this._config = null;
        this._element = null;
        this._dialog = null;
        this._backdrop = null;
        this._isShown = null;
        this._isBodyOverflowing = null;
        this._ignoreBackdropClick = null;
        this._isTransitioning = null;
        this._scrollbarWidth = null;
      }
    }, {
      key: "handleUpdate",
      value: function handleUpdate() {
        this._adjustDialog();
      }
    }, {
      key: "_getConfig",
      value: function _getConfig(config) {
        config = _objectSpread({}, Default, config);

        _util2.default.typeCheckConfig(NAME, config, DefaultType);

        return config;
      }
    }, {
      key: "_showElement",
      value: function _showElement(relatedTarget) {
        var _this3 = this;

        var transition = (0, _jquery2.default)(this._element).hasClass(ClassName.FADE);

        if (!this._element.parentNode || this._element.parentNode.nodeType !== Node.ELEMENT_NODE) {
          // Don't move modal's DOM position
          document.body.appendChild(this._element);
        }

        this._element.style.display = 'block';

        this._element.removeAttribute('aria-hidden');

        this._element.setAttribute('aria-modal', true);

        if ((0, _jquery2.default)(this._dialog).hasClass(ClassName.SCROLLABLE)) {
          this._dialog.querySelector(Selector.MODAL_BODY).scrollTop = 0;
        } else {
          this._element.scrollTop = 0;
        }

        if (transition) {
          _util2.default.reflow(this._element);
        }

        (0, _jquery2.default)(this._element).addClass(ClassName.SHOW);

        if (this._config.focus) {
          this._enforceFocus();
        }

        var shownEvent = _jquery2.default.Event(Event.SHOWN, {
          relatedTarget: relatedTarget
        });

        var transitionComplete = function transitionComplete() {
          if (_this3._config.focus) {
            _this3._element.focus();
          }

          _this3._isTransitioning = false;
          (0, _jquery2.default)(_this3._element).trigger(shownEvent);
        };

        if (transition) {
          var transitionDuration = _util2.default.getTransitionDurationFromElement(this._dialog);

          (0, _jquery2.default)(this._dialog).one(_util2.default.TRANSITION_END, transitionComplete).emulateTransitionEnd(transitionDuration);
        } else {
          transitionComplete();
        }
      }
    }, {
      key: "_enforceFocus",
      value: function _enforceFocus() {
        var _this4 = this;

        (0, _jquery2.default)(document).off(Event.FOCUSIN) // Guard against infinite focus loop
        .on(Event.FOCUSIN, function (event) {
          if (document !== event.target && _this4._element !== event.target && (0, _jquery2.default)(_this4._element).has(event.target).length === 0) {
            _this4._element.focus();
          }
        });
      }
    }, {
      key: "_setEscapeEvent",
      value: function _setEscapeEvent() {
        var _this5 = this;

        if (this._isShown && this._config.keyboard) {
          (0, _jquery2.default)(this._element).on(Event.KEYDOWN_DISMISS, function (event) {
            if (event.which === ESCAPE_KEYCODE) {
              event.preventDefault();

              _this5.hide();
            }
          });
        } else if (!this._isShown) {
          (0, _jquery2.default)(this._element).off(Event.KEYDOWN_DISMISS);
        }
      }
    }, {
      key: "_setResizeEvent",
      value: function _setResizeEvent() {
        var _this6 = this;

        if (this._isShown) {
          (0, _jquery2.default)(window).on(Event.RESIZE, function (event) {
            return _this6.handleUpdate(event);
          });
        } else {
          (0, _jquery2.default)(window).off(Event.RESIZE);
        }
      }
    }, {
      key: "_hideModal",
      value: function _hideModal() {
        var _this7 = this;

        this._element.style.display = 'none';

        this._element.setAttribute('aria-hidden', true);

        this._element.removeAttribute('aria-modal');

        this._isTransitioning = false;

        this._showBackdrop(function () {
          (0, _jquery2.default)(document.body).removeClass(ClassName.OPEN);

          _this7._resetAdjustments();

          _this7._resetScrollbar();

          (0, _jquery2.default)(_this7._element).trigger(Event.HIDDEN);
        });
      }
    }, {
      key: "_removeBackdrop",
      value: function _removeBackdrop() {
        if (this._backdrop) {
          (0, _jquery2.default)(this._backdrop).remove();
          this._backdrop = null;
        }
      }
    }, {
      key: "_showBackdrop",
      value: function _showBackdrop(callback) {
        var _this8 = this;

        var animate = (0, _jquery2.default)(this._element).hasClass(ClassName.FADE) ? ClassName.FADE : '';

        if (this._isShown && this._config.backdrop) {
          this._backdrop = document.createElement('div');
          this._backdrop.className = ClassName.BACKDROP;

          if (animate) {
            this._backdrop.classList.add(animate);
          }

          (0, _jquery2.default)(this._backdrop).appendTo(document.body);
          (0, _jquery2.default)(this._element).on(Event.CLICK_DISMISS, function (event) {
            if (_this8._ignoreBackdropClick) {
              _this8._ignoreBackdropClick = false;
              return;
            }

            if (event.target !== event.currentTarget) {
              return;
            }

            if (_this8._config.backdrop === 'static') {
              _this8._element.focus();
            } else {
              _this8.hide();
            }
          });

          if (animate) {
            _util2.default.reflow(this._backdrop);
          }

          (0, _jquery2.default)(this._backdrop).addClass(ClassName.SHOW);

          if (!callback) {
            return;
          }

          if (!animate) {
            callback();
            return;
          }

          var backdropTransitionDuration = _util2.default.getTransitionDurationFromElement(this._backdrop);

          (0, _jquery2.default)(this._backdrop).one(_util2.default.TRANSITION_END, callback).emulateTransitionEnd(backdropTransitionDuration);
        } else if (!this._isShown && this._backdrop) {
          (0, _jquery2.default)(this._backdrop).removeClass(ClassName.SHOW);

          var callbackRemove = function callbackRemove() {
            _this8._removeBackdrop();

            if (callback) {
              callback();
            }
          };

          if ((0, _jquery2.default)(this._element).hasClass(ClassName.FADE)) {
            var _backdropTransitionDuration = _util2.default.getTransitionDurationFromElement(this._backdrop);

            (0, _jquery2.default)(this._backdrop).one(_util2.default.TRANSITION_END, callbackRemove).emulateTransitionEnd(_backdropTransitionDuration);
          } else {
            callbackRemove();
          }
        } else if (callback) {
          callback();
        }
      }
    }, {
      key: "_adjustDialog",
      value: function _adjustDialog() {
        var isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;

        if (!this._isBodyOverflowing && isModalOverflowing) {
          this._element.style.paddingLeft = "".concat(this._scrollbarWidth, "px");
        }

        if (this._isBodyOverflowing && !isModalOverflowing) {
          this._element.style.paddingRight = "".concat(this._scrollbarWidth, "px");
        }
      }
    }, {
      key: "_resetAdjustments",
      value: function _resetAdjustments() {
        this._element.style.paddingLeft = '';
        this._element.style.paddingRight = '';
      }
    }, {
      key: "_checkScrollbar",
      value: function _checkScrollbar() {
        var rect = document.body.getBoundingClientRect();
        this._isBodyOverflowing = rect.left + rect.right < window.innerWidth;
        this._scrollbarWidth = this._getScrollbarWidth();
      }
    }, {
      key: "_setScrollbar",
      value: function _setScrollbar() {
        var _this9 = this;

        if (this._isBodyOverflowing) {
          // Note: DOMNode.style.paddingRight returns the actual value or '' if not set
          //   while $(DOMNode).css('padding-right') returns the calculated value or 0 if not set
          var fixedContent = [].slice.call(document.querySelectorAll(Selector.FIXED_CONTENT));
          var stickyContent = [].slice.call(document.querySelectorAll(Selector.STICKY_CONTENT)); // Adjust fixed content padding

          (0, _jquery2.default)(fixedContent).each(function (index, element) {
            var actualPadding = element.style.paddingRight;
            var calculatedPadding = (0, _jquery2.default)(element).css('padding-right');
            (0, _jquery2.default)(element).data('padding-right', actualPadding).css('padding-right', "".concat(parseFloat(calculatedPadding) + _this9._scrollbarWidth, "px"));
          }); // Adjust sticky content margin

          (0, _jquery2.default)(stickyContent).each(function (index, element) {
            var actualMargin = element.style.marginRight;
            var calculatedMargin = (0, _jquery2.default)(element).css('margin-right');
            (0, _jquery2.default)(element).data('margin-right', actualMargin).css('margin-right', "".concat(parseFloat(calculatedMargin) - _this9._scrollbarWidth, "px"));
          }); // Adjust body padding

          var actualPadding = document.body.style.paddingRight;
          var calculatedPadding = (0, _jquery2.default)(document.body).css('padding-right');
          (0, _jquery2.default)(document.body).data('padding-right', actualPadding).css('padding-right', "".concat(parseFloat(calculatedPadding) + this._scrollbarWidth, "px"));
        }

        (0, _jquery2.default)(document.body).addClass(ClassName.OPEN);
      }
    }, {
      key: "_resetScrollbar",
      value: function _resetScrollbar() {
        // Restore fixed content padding
        var fixedContent = [].slice.call(document.querySelectorAll(Selector.FIXED_CONTENT));
        (0, _jquery2.default)(fixedContent).each(function (index, element) {
          var padding = (0, _jquery2.default)(element).data('padding-right');
          (0, _jquery2.default)(element).removeData('padding-right');
          element.style.paddingRight = padding ? padding : '';
        }); // Restore sticky content

        var elements = [].slice.call(document.querySelectorAll("".concat(Selector.STICKY_CONTENT)));
        (0, _jquery2.default)(elements).each(function (index, element) {
          var margin = (0, _jquery2.default)(element).data('margin-right');

          if (typeof margin !== 'undefined') {
            (0, _jquery2.default)(element).css('margin-right', margin).removeData('margin-right');
          }
        }); // Restore body padding

        var padding = (0, _jquery2.default)(document.body).data('padding-right');
        (0, _jquery2.default)(document.body).removeData('padding-right');
        document.body.style.paddingRight = padding ? padding : '';
      }
    }, {
      key: "_getScrollbarWidth",
      value: function _getScrollbarWidth() {
        // thx d.walsh
        var scrollDiv = document.createElement('div');
        scrollDiv.className = ClassName.SCROLLBAR_MEASURER;
        document.body.appendChild(scrollDiv);
        var scrollbarWidth = scrollDiv.getBoundingClientRect().width - scrollDiv.clientWidth;
        document.body.removeChild(scrollDiv);
        return scrollbarWidth;
      }
    }], [{
      key: "_jQueryInterface",
      value: function _jQueryInterface(config, relatedTarget) {
        return this.each(function () {
          var data = (0, _jquery2.default)(this).data(DATA_KEY);

          var _config = _objectSpread({}, Default, (0, _jquery2.default)(this).data(), _typeof(config) === 'object' && config ? config : {});

          if (!data) {
            data = new Modal(this, _config);
            (0, _jquery2.default)(this).data(DATA_KEY, data);
          }

          if (typeof config === 'string') {
            if (typeof data[config] === 'undefined') {
              throw new TypeError("No method named \"".concat(config, "\""));
            }

            data[config](relatedTarget);
          } else if (_config.show) {
            data.show(relatedTarget);
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

    return Modal;
  }();

  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */
  (0, _jquery2.default)(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
    var _this10 = this;

    var target;

    var selector = _util2.default.getSelectorFromElement(this);

    if (selector) {
      target = document.querySelector(selector);
    }

    var config = (0, _jquery2.default)(target).data(DATA_KEY) ? 'toggle' : _objectSpread({}, (0, _jquery2.default)(target).data(), (0, _jquery2.default)(this).data());

    if (this.tagName === 'A' || this.tagName === 'AREA') {
      event.preventDefault();
    }

    var $target = (0, _jquery2.default)(target).one(Event.SHOW, function (showEvent) {
      if (showEvent.isDefaultPrevented()) {
        // Only register focus restorer if modal will actually get shown
        return;
      }

      $target.one(Event.HIDDEN, function () {
        if ((0, _jquery2.default)(_this10).is(':visible')) {
          _this10.focus();
        }
      });
    });

    Modal._jQueryInterface.call((0, _jquery2.default)(target), config, this);
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  _jquery2.default.fn[NAME] = Modal._jQueryInterface;
  _jquery2.default.fn[NAME].Constructor = Modal;

  _jquery2.default.fn[NAME].noConflict = function () {
    _jquery2.default.fn[NAME] = JQUERY_NO_CONFLICT;
    return Modal._jQueryInterface;
  };

  exports.default = Modal;
});