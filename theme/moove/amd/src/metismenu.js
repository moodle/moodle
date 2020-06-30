// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Metis menu definition.
 *
 * @package    theme_moove
 * @copyright  2020 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['exports', 'jquery'], function(exports, $) {
    'use strict';

    function _interopDefault (ex) { return (ex && (typeof ex === 'object') && 'default' in ex) ? ex['default'] : ex; }

    var $ = _interopDefault(require('jquery'));

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

    function _objectSpread(target) {
        for (var i = 1; i < arguments.length; i++) {
            var source = arguments[i] !== null ? arguments[i] : {};
            var ownKeys = Object.keys(source);

            if (typeof Object.getOwnPropertySymbols === 'function') {
                ownKeys = ownKeys.concat(
                    Object.getOwnPropertySymbols(source)
                    .filter(function(sym) {// eslint-disable-line no-loop-func
                        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
                }));
            }

            ownKeys.forEach(function (key) {// eslint-disable-line no-loop-func
                _defineProperty(target, key, source[key]);
            });
        }

        return target;
    }

    var Util = function($$$1) {// eslint-disable-line wrap-iife
        var TRANSITION_END = 'transitionend';
        var Util = {
            TRANSITION_END: 'mmTransitionEnd',
            triggerTransitionEnd: function triggerTransitionEnd(element) {
                $$$1(element).trigger(TRANSITION_END);
            },
            supportsTransitionEnd: function supportsTransitionEnd() {
                return Boolean(TRANSITION_END);
            }
        };

        function getSpecialTransitionEndEvent() {
            return {
                bindType: TRANSITION_END,
                delegateType: TRANSITION_END,
                handle: function handle(event) {
                    if ($$$1(event.target).is(this)) {
                        return event.handleObj.handler.apply(this, arguments);
                    }

                    return undefined;
                }
            };
        }

        function transitionEndEmulator(duration) {
            var _this = this;

            var called = false;
            $$$1(this).one(Util.TRANSITION_END, function () {
                called = true;
            });
            setTimeout(function () {
                if (!called) {
                    Util.triggerTransitionEnd(_this);
                }
            }, duration);
            return this;
        }

        function setTransitionEndSupport() {
            $$$1.fn.mmEmulateTransitionEnd = transitionEndEmulator;

            $$$1.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
        }

        setTransitionEndSupport();

        return Util;
    }($);

    var MetisMenu = function($$$1) {// eslint-disable-line wrap-iife
        var NAME = 'metisMenu';
        var DATA_KEY = 'metisMenu';
        var EVENT_KEY = "." + DATA_KEY;
        var DATA_API_KEY = '.data-api';
        var JQUERY_NO_CONFLICT = $$$1.fn[NAME];
        var TRANSITION_DURATION = 350;
        var Default = {
                toggle: true,
                preventDefault: true,
                activeClass: 'active',
                collapseClass: 'collapse',
                collapseInClass: 'in',
                collapsingClass: 'collapsing',
                triggerElement: 'a',
                parentTrigger: 'li',
                subMenu: 'ul'
        };
        var Event = {
            SHOW: "show" + EVENT_KEY,
            SHOWN: "shown" + EVENT_KEY,
            HIDE: "hide" + EVENT_KEY,
            HIDDEN: "hidden" + EVENT_KEY,
            CLICK_DATA_API: "click" + EVENT_KEY + DATA_API_KEY
    };
    var MetisMenu = function() {// eslint-disable-line wrap-iife
        function MetisMenu(element, config) {
            this.element = element;
            this.config = _objectSpread({}, Default, config);
            this.transitioning = null;
            this.init();
        }

        var _proto = MetisMenu.prototype;

        _proto.init = function init() {
            var self = this;
            var conf = this.config;
            $$$1(this.element).find(conf.parentTrigger + "." + conf.activeClass)
                .has(conf.subMenu)
                .children(conf.subMenu)
                .addClass(conf.collapseClass + " " + conf.collapseInClass);
            $$$1(this.element).find(conf.parentTrigger)
                .not("." + conf.activeClass)
                .has(conf.subMenu)
                .children(conf.subMenu)
                .addClass(conf.collapseClass);
            $$$1(this.element).find(conf.parentTrigger)
                .has(conf.subMenu)
                .children(conf.triggerElement)
                .on(Event.CLICK_DATA_API, function (e) {
                    var eTar = $$$1(this);
                    var paRent = eTar.parent(conf.parentTrigger);
                    var sibLings = paRent.siblings(conf.parentTrigger).children(conf.triggerElement);
                    var List = paRent.children(conf.subMenu);

                    if (conf.preventDefault) {
                        e.preventDefault();
                    }

                    if (eTar.attr('aria-disabled') === 'true') {
                        return;
                    }

                    if (paRent.hasClass(conf.activeClass)) {
                        eTar.attr('aria-expanded', false);
                        self.hide(List);
                    } else {
                        self.show(List);
                        eTar.attr('aria-expanded', true);

                        if (conf.toggle) {
                            sibLings.attr('aria-expanded', false);
                        }
                    }

                    if (conf.onTransitionStart) {
                        conf.onTransitionStart(e);
                    }
            });
        };

        _proto.show = function show(element) {
            var _this = this;

            if (this.transitioning || $$$1(element).hasClass(this.config.collapsingClass)) {
                return;
            }

            var elem = $$$1(element);
            var startEvent = $$$1.Event(Event.SHOW);
            elem.trigger(startEvent);

            if (startEvent.isDefaultPrevented()) {
                return;
            }

            elem.parent(this.config.parentTrigger).addClass(this.config.activeClass);

            if (this.config.toggle) {
                this.hide(elem.parent(this.config.parentTrigger)
                    .siblings()
                    .children(this.config.subMenu + "." + this.config.collapseInClass));
            }

            elem.removeClass(this.config.collapseClass).addClass(this.config.collapsingClass).height(0);
            this.setTransitioning(true);

            var complete = function complete() {
                if (!_this.config || !_this.element) {
                    return;
                }

                elem.removeClass(_this.config.collapsingClass)
                    .addClass(_this.config.collapseClass + " " + _this.config.collapseInClass)
                    .height("");

                _this.setTransitioning(false);

                elem.trigger(Event.SHOWN);
            };

            elem.height(element[0].scrollHeight).one(Util.TRANSITION_END, complete).mmEmulateTransitionEnd(TRANSITION_DURATION);
        };

        _proto.hide = function hide(element) {
            var _this2 = this;

            if (this.transitioning || !$$$1(element).hasClass(this.config.collapseInClass)) {
                return;
            }

            var elem = $$$1(element);
            var startEvent = $$$1.Event(Event.HIDE);
            elem.trigger(startEvent);

            if (startEvent.isDefaultPrevented()) {
                return;
            }

            elem.parent(this.config.parentTrigger).removeClass(this.config.activeClass);

            elem.addClass(this.config.collapsingClass)
                .removeClass(this.config.collapseClass)
                .removeClass(this.config.collapseInClass);

            this.setTransitioning(true);

            var complete = function complete() {
                if (!_this2.config || !_this2.element) {
                    return;
                }

                if (_this2.transitioning && _this2.config.onTransitionEnd) {
                    _this2.config.onTransitionEnd();
                }

                _this2.setTransitioning(false);

                elem.trigger(Event.HIDDEN);
                elem.removeClass(_this2.config.collapsingClass).addClass(_this2.config.collapseClass);
            };

            if (elem.height() === 0 || elem.css('display') === 'none') {
                complete();
            } else {
                elem.height(0).one(Util.TRANSITION_END, complete).mmEmulateTransitionEnd(TRANSITION_DURATION);
            }
        };

        _proto.setTransitioning = function setTransitioning(isTransitioning) {
            this.transitioning = isTransitioning;
        };

        _proto.dispose = function dispose() {
            $$$1.removeData(this.element, DATA_KEY);
            $$$1(this.element).find(this.config.parentTrigger)
                .has(this.config.subMenu)
                .children(this.config.triggerElement)
                .off("click");

            this.transitioning = null;
            this.config = null;
            this.element = null;
        };

        MetisMenu.jQueryInterface = function jQueryInterface(config) {
            return this.each(function () {
                var $this = $$$1(this);
                var data = $this.data(DATA_KEY);

                var conf = _objectSpread({}, Default, $this.data(), typeof config === 'object' && config ? config : {});

                    if (!data && /dispose/.test(config)) {
                        this.dispose();
                    }

                    if (!data) {
                        data = new MetisMenu(this, conf);
                        $this.data(DATA_KEY, data);
                    }

                    if (typeof config === 'string') {
                        if (data[config] === undefined) {
                            throw new Error("No method named \"" + config + "\"");
                        }

                        data[config]();
                    }
            });
        };

        return MetisMenu;
    }();

    $$$1.fn[NAME] = MetisMenu.jQueryInterface;

    $$$1.fn[NAME].Constructor = MetisMenu;

    $$$1.fn[NAME].noConflict = function () {
        $$$1.fn[NAME] = JQUERY_NO_CONFLICT;

        return MetisMenu.jQueryInterface;
    };

    return MetisMenu;

}($);

    exports.default = MetisMenu;
});