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
   * Bootstrap (v4.0.0): tooltip.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * --------------------------------------------------------------------------
   */
  var Tooltip = function ($) {
    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */
    var NAME = 'tooltip';
    var VERSION = '4.0.0';
    var DATA_KEY = 'bs.tooltip';
    var EVENT_KEY = ".".concat(DATA_KEY);
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var TRANSITION_DURATION = 150;
    var CLASS_PREFIX = 'bs-tooltip';
    var BSCLS_PREFIX_REGEX = new RegExp("(^|\\s)".concat(CLASS_PREFIX, "\\S+"), 'g');
    var DefaultType = {
      animation: 'boolean',
      template: 'string',
      title: '(string|element|function)',
      trigger: 'string',
      delay: '(number|object)',
      html: 'boolean',
      selector: '(string|boolean)',
      placement: '(string|function)',
      offset: '(number|string)',
      container: '(string|element|boolean)',
      fallbackPlacement: '(string|array)',
      boundary: '(string|element)'
    };
    var AttachmentMap = {
      AUTO: 'auto',
      TOP: 'top',
      RIGHT: 'right',
      BOTTOM: 'bottom',
      LEFT: 'left'
    };
    var Default = {
      animation: true,
      template: '<div class="tooltip" role="tooltip">' + '<div class="arrow"></div>' + '<div class="tooltip-inner"></div></div>',
      trigger: 'hover focus',
      title: '',
      delay: 0,
      html: false,
      selector: false,
      placement: 'top',
      offset: 0,
      container: false,
      fallbackPlacement: 'flip',
      boundary: 'scrollParent'
    };
    var HoverState = {
      SHOW: 'show',
      OUT: 'out'
    };
    var Event = {
      HIDE: "hide".concat(EVENT_KEY),
      HIDDEN: "hidden".concat(EVENT_KEY),
      SHOW: "show".concat(EVENT_KEY),
      SHOWN: "shown".concat(EVENT_KEY),
      INSERTED: "inserted".concat(EVENT_KEY),
      CLICK: "click".concat(EVENT_KEY),
      FOCUSIN: "focusin".concat(EVENT_KEY),
      FOCUSOUT: "focusout".concat(EVENT_KEY),
      MOUSEENTER: "mouseenter".concat(EVENT_KEY),
      MOUSELEAVE: "mouseleave".concat(EVENT_KEY)
    };
    var ClassName = {
      FADE: 'fade',
      SHOW: 'show'
    };
    var Selector = {
      TOOLTIP: '.tooltip',
      TOOLTIP_INNER: '.tooltip-inner',
      ARROW: '.arrow'
    };
    var Trigger = {
      HOVER: 'hover',
      FOCUS: 'focus',
      CLICK: 'click',
      MANUAL: 'manual'
      /**
       * ------------------------------------------------------------------------
       * Class Definition
       * ------------------------------------------------------------------------
       */

    };

    var Tooltip = function () {
      function Tooltip(element, config) {
        _classCallCheck(this, Tooltip);

        /**
         * Check for Popper dependency
         * Popper - https://popper.js.org
         */
        if (typeof _popper2.default === 'undefined') {
          throw new TypeError('Bootstrap tooltips require Popper.js (https://popper.js.org)');
        } // private


        this._isEnabled = true;
        this._timeout = 0;
        this._hoverState = '';
        this._activeTrigger = {};
        this._popper = null; // Protected

        this.element = element;
        this.config = this._getConfig(config);
        this.tip = null;

        this._setListeners();
      } // Getters


      _createClass(Tooltip, [{
        key: "enable",
        value: function enable() {
          this._isEnabled = true;
        }
      }, {
        key: "disable",
        value: function disable() {
          this._isEnabled = false;
        }
      }, {
        key: "toggleEnabled",
        value: function toggleEnabled() {
          this._isEnabled = !this._isEnabled;
        }
      }, {
        key: "toggle",
        value: function toggle(event) {
          if (!this._isEnabled) {
            return;
          }

          if (event) {
            var dataKey = this.constructor.DATA_KEY;
            var context = $(event.currentTarget).data(dataKey);

            if (!context) {
              context = new this.constructor(event.currentTarget, this._getDelegateConfig());
              $(event.currentTarget).data(dataKey, context);
            }

            context._activeTrigger.click = !context._activeTrigger.click;

            if (context._isWithActiveTrigger()) {
              context._enter(null, context);
            } else {
              context._leave(null, context);
            }
          } else {
            if ($(this.getTipElement()).hasClass(ClassName.SHOW)) {
              this._leave(null, this);

              return;
            }

            this._enter(null, this);
          }
        }
      }, {
        key: "dispose",
        value: function dispose() {
          clearTimeout(this._timeout);
          $.removeData(this.element, this.constructor.DATA_KEY);
          $(this.element).off(this.constructor.EVENT_KEY);
          $(this.element).closest('.modal').off('hide.bs.modal');

          if (this.tip) {
            $(this.tip).remove();
          }

          this._isEnabled = null;
          this._timeout = null;
          this._hoverState = null;
          this._activeTrigger = null;

          if (this._popper !== null) {
            this._popper.destroy();
          }

          this._popper = null;
          this.element = null;
          this.config = null;
          this.tip = null;
        }
      }, {
        key: "show",
        value: function show() {
          var _this = this;

          if ($(this.element).css('display') === 'none') {
            throw new Error('Please use show on visible elements');
          }

          var showEvent = $.Event(this.constructor.Event.SHOW);

          if (this.isWithContent() && this._isEnabled) {
            $(this.element).trigger(showEvent);
            var isInTheDom = $.contains(this.element.ownerDocument.documentElement, this.element);

            if (showEvent.isDefaultPrevented() || !isInTheDom) {
              return;
            }

            var tip = this.getTipElement();

            var tipId = _util2.default.getUID(this.constructor.NAME);

            tip.setAttribute('id', tipId);
            this.element.setAttribute('aria-describedby', tipId);
            this.setContent();

            if (this.config.animation) {
              $(tip).addClass(ClassName.FADE);
            }

            var placement = typeof this.config.placement === 'function' ? this.config.placement.call(this, tip, this.element) : this.config.placement;

            var attachment = this._getAttachment(placement);

            this.addAttachmentClass(attachment);
            var container = this.config.container === false ? document.body : $(this.config.container);
            $(tip).data(this.constructor.DATA_KEY, this);

            if (!$.contains(this.element.ownerDocument.documentElement, this.tip)) {
              $(tip).appendTo(container);
            }

            $(this.element).trigger(this.constructor.Event.INSERTED);
            this._popper = new _popper2.default(this.element, tip, {
              placement: attachment,
              modifiers: {
                offset: {
                  offset: this.config.offset
                },
                flip: {
                  behavior: this.config.fallbackPlacement
                },
                arrow: {
                  element: Selector.ARROW
                },
                preventOverflow: {
                  boundariesElement: this.config.boundary
                }
              },
              onCreate: function onCreate(data) {
                if (data.originalPlacement !== data.placement) {
                  _this._handlePopperPlacementChange(data);
                }
              },
              onUpdate: function onUpdate(data) {
                _this._handlePopperPlacementChange(data);
              }
            });
            $(tip).addClass(ClassName.SHOW); // If this is a touch-enabled device we add extra
            // empty mouseover listeners to the body's immediate children;
            // only needed because of broken event delegation on iOS
            // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html

            if ('ontouchstart' in document.documentElement) {
              $('body').children().on('mouseover', null, $.noop);
            }

            var complete = function complete() {
              if (_this.config.animation) {
                _this._fixTransition();
              }

              var prevHoverState = _this._hoverState;
              _this._hoverState = null;
              $(_this.element).trigger(_this.constructor.Event.SHOWN);

              if (prevHoverState === HoverState.OUT) {
                _this._leave(null, _this);
              }
            };

            if (_util2.default.supportsTransitionEnd() && $(this.tip).hasClass(ClassName.FADE)) {
              $(this.tip).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(Tooltip._TRANSITION_DURATION);
            } else {
              complete();
            }
          }
        }
      }, {
        key: "hide",
        value: function hide(callback) {
          var _this2 = this;

          var tip = this.getTipElement();
          var hideEvent = $.Event(this.constructor.Event.HIDE);

          var complete = function complete() {
            if (_this2._hoverState !== HoverState.SHOW && tip.parentNode) {
              tip.parentNode.removeChild(tip);
            }

            _this2._cleanTipClass();

            _this2.element.removeAttribute('aria-describedby');

            $(_this2.element).trigger(_this2.constructor.Event.HIDDEN);

            if (_this2._popper !== null) {
              _this2._popper.destroy();
            }

            if (callback) {
              callback();
            }
          };

          $(this.element).trigger(hideEvent);

          if (hideEvent.isDefaultPrevented()) {
            return;
          }

          $(tip).removeClass(ClassName.SHOW); // If this is a touch-enabled device we remove the extra
          // empty mouseover listeners we added for iOS support

          if ('ontouchstart' in document.documentElement) {
            $('body').children().off('mouseover', null, $.noop);
          }

          this._activeTrigger[Trigger.CLICK] = false;
          this._activeTrigger[Trigger.FOCUS] = false;
          this._activeTrigger[Trigger.HOVER] = false;

          if (_util2.default.supportsTransitionEnd() && $(this.tip).hasClass(ClassName.FADE)) {
            $(tip).one(_util2.default.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
          } else {
            complete();
          }

          this._hoverState = '';
        }
      }, {
        key: "update",
        value: function update() {
          if (this._popper !== null) {
            this._popper.scheduleUpdate();
          }
        }
      }, {
        key: "isWithContent",
        value: function isWithContent() {
          return Boolean(this.getTitle());
        }
      }, {
        key: "addAttachmentClass",
        value: function addAttachmentClass(attachment) {
          $(this.getTipElement()).addClass("".concat(CLASS_PREFIX, "-").concat(attachment));
        }
      }, {
        key: "getTipElement",
        value: function getTipElement() {
          this.tip = this.tip || $(this.config.template)[0];
          return this.tip;
        }
      }, {
        key: "setContent",
        value: function setContent() {
          var $tip = $(this.getTipElement());
          this.setElementContent($tip.find(Selector.TOOLTIP_INNER), this.getTitle());
          $tip.removeClass("".concat(ClassName.FADE, " ").concat(ClassName.SHOW));
        }
      }, {
        key: "setElementContent",
        value: function setElementContent($element, content) {
          var html = this.config.html;

          if (_typeof(content) === 'object' && (content.nodeType || content.jquery)) {
            // Content is a DOM node or a jQuery
            if (html) {
              if (!$(content).parent().is($element)) {
                $element.empty().append(content);
              }
            } else {
              $element.text($(content).text());
            }
          } else {
            $element[html ? 'html' : 'text'](content);
          }
        }
      }, {
        key: "getTitle",
        value: function getTitle() {
          var title = this.element.getAttribute('data-original-title');

          if (!title) {
            title = typeof this.config.title === 'function' ? this.config.title.call(this.element) : this.config.title;
          }

          return title;
        }
      }, {
        key: "_getAttachment",
        value: function _getAttachment(placement) {
          return AttachmentMap[placement.toUpperCase()];
        }
      }, {
        key: "_setListeners",
        value: function _setListeners() {
          var _this3 = this;

          var triggers = this.config.trigger.split(' ');
          triggers.forEach(function (trigger) {
            if (trigger === 'click') {
              $(_this3.element).on(_this3.constructor.Event.CLICK, _this3.config.selector, function (event) {
                return _this3.toggle(event);
              });
            } else if (trigger !== Trigger.MANUAL) {
              var eventIn = trigger === Trigger.HOVER ? _this3.constructor.Event.MOUSEENTER : _this3.constructor.Event.FOCUSIN;
              var eventOut = trigger === Trigger.HOVER ? _this3.constructor.Event.MOUSELEAVE : _this3.constructor.Event.FOCUSOUT;
              $(_this3.element).on(eventIn, _this3.config.selector, function (event) {
                return _this3._enter(event);
              }).on(eventOut, _this3.config.selector, function (event) {
                return _this3._leave(event);
              });
            }

            $(_this3.element).closest('.modal').on('hide.bs.modal', function () {
              return _this3.hide();
            });
          });

          if (this.config.selector) {
            this.config = _extends({}, this.config, {
              trigger: 'manual',
              selector: ''
            });
          } else {
            this._fixTitle();
          }
        }
      }, {
        key: "_fixTitle",
        value: function _fixTitle() {
          var titleType = _typeof(this.element.getAttribute('data-original-title'));

          if (this.element.getAttribute('title') || titleType !== 'string') {
            this.element.setAttribute('data-original-title', this.element.getAttribute('title') || '');
            this.element.setAttribute('title', '');
          }
        }
      }, {
        key: "_enter",
        value: function _enter(event, context) {
          var dataKey = this.constructor.DATA_KEY;
          context = context || $(event.currentTarget).data(dataKey);

          if (!context) {
            context = new this.constructor(event.currentTarget, this._getDelegateConfig());
            $(event.currentTarget).data(dataKey, context);
          }

          if (event) {
            context._activeTrigger[event.type === 'focusin' ? Trigger.FOCUS : Trigger.HOVER] = true;
          }

          if ($(context.getTipElement()).hasClass(ClassName.SHOW) || context._hoverState === HoverState.SHOW) {
            context._hoverState = HoverState.SHOW;
            return;
          }

          clearTimeout(context._timeout);
          context._hoverState = HoverState.SHOW;

          if (!context.config.delay || !context.config.delay.show) {
            context.show();
            return;
          }

          context._timeout = setTimeout(function () {
            if (context._hoverState === HoverState.SHOW) {
              context.show();
            }
          }, context.config.delay.show);
        }
      }, {
        key: "_leave",
        value: function _leave(event, context) {
          var dataKey = this.constructor.DATA_KEY;
          context = context || $(event.currentTarget).data(dataKey);

          if (!context) {
            context = new this.constructor(event.currentTarget, this._getDelegateConfig());
            $(event.currentTarget).data(dataKey, context);
          }

          if (event) {
            context._activeTrigger[event.type === 'focusout' ? Trigger.FOCUS : Trigger.HOVER] = false;
          }

          if (context._isWithActiveTrigger()) {
            return;
          }

          clearTimeout(context._timeout);
          context._hoverState = HoverState.OUT;

          if (!context.config.delay || !context.config.delay.hide) {
            context.hide();
            return;
          }

          context._timeout = setTimeout(function () {
            if (context._hoverState === HoverState.OUT) {
              context.hide();
            }
          }, context.config.delay.hide);
        }
      }, {
        key: "_isWithActiveTrigger",
        value: function _isWithActiveTrigger() {
          for (var trigger in this._activeTrigger) {
            if (this._activeTrigger[trigger]) {
              return true;
            }
          }

          return false;
        }
      }, {
        key: "_getConfig",
        value: function _getConfig(config) {
          config = _extends({}, this.constructor.Default, $(this.element).data(), config);

          if (typeof config.delay === 'number') {
            config.delay = {
              show: config.delay,
              hide: config.delay
            };
          }

          if (typeof config.title === 'number') {
            config.title = config.title.toString();
          }

          if (typeof config.content === 'number') {
            config.content = config.content.toString();
          }

          _util2.default.typeCheckConfig(NAME, config, this.constructor.DefaultType);

          return config;
        }
      }, {
        key: "_getDelegateConfig",
        value: function _getDelegateConfig() {
          var config = {};

          if (this.config) {
            for (var key in this.config) {
              if (this.constructor.Default[key] !== this.config[key]) {
                config[key] = this.config[key];
              }
            }
          }

          return config;
        }
      }, {
        key: "_cleanTipClass",
        value: function _cleanTipClass() {
          var $tip = $(this.getTipElement());
          var tabClass = $tip.attr('class').match(BSCLS_PREFIX_REGEX);

          if (tabClass !== null && tabClass.length > 0) {
            $tip.removeClass(tabClass.join(''));
          }
        }
      }, {
        key: "_handlePopperPlacementChange",
        value: function _handlePopperPlacementChange(data) {
          this._cleanTipClass();

          this.addAttachmentClass(this._getAttachment(data.placement));
        }
      }, {
        key: "_fixTransition",
        value: function _fixTransition() {
          var tip = this.getTipElement();
          var initConfigAnimation = this.config.animation;

          if (tip.getAttribute('x-placement') !== null) {
            return;
          }

          $(tip).removeClass(ClassName.FADE);
          this.config.animation = false;
          this.hide();
          this.show();
          this.config.animation = initConfigAnimation;
        }
      }], [{
        key: "_jQueryInterface",
        value: function _jQueryInterface(config) {
          return this.each(function () {
            var data = $(this).data(DATA_KEY);

            var _config = _typeof(config) === 'object' && config;

            if (!data && /dispose|hide/.test(config)) {
              return;
            }

            if (!data) {
              data = new Tooltip(this, _config);
              $(this).data(DATA_KEY, data);
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
      }, {
        key: "NAME",
        get: function get() {
          return NAME;
        }
      }, {
        key: "DATA_KEY",
        get: function get() {
          return DATA_KEY;
        }
      }, {
        key: "Event",
        get: function get() {
          return Event;
        }
      }, {
        key: "EVENT_KEY",
        get: function get() {
          return EVENT_KEY;
        }
      }, {
        key: "DefaultType",
        get: function get() {
          return DefaultType;
        }
      }]);

      return Tooltip;
    }();

    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */
    $.fn[NAME] = Tooltip._jQueryInterface;
    $.fn[NAME].Constructor = Tooltip;

    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return Tooltip._jQueryInterface;
    };

    return Tooltip;
  }(_jquery2.default, _popper2.default);

  exports.default = Tooltip;
});