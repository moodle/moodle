// node_modules/bootstrap/js/src/base-component.js
import Data from "./dom/data.js";
import EventHandler from "./dom/event-handler.js";
import Config from "./util/config.js";
import { executeAfterTransition, getElement } from "./util/index.js";
var VERSION = "5.3.8";
var BaseComponent = class extends Config {
  constructor(element, config) {
    super();
    element = getElement(element);
    if (!element) {
      return;
    }
    this._element = element;
    this._config = this._getConfig(config);
    Data.set(this._element, this.constructor.DATA_KEY, this);
  }
  // Public
  dispose() {
    Data.remove(this._element, this.constructor.DATA_KEY);
    EventHandler.off(this._element, this.constructor.EVENT_KEY);
    for (const propertyName of Object.getOwnPropertyNames(this)) {
      this[propertyName] = null;
    }
  }
  // Private
  _queueCallback(callback, element, isAnimated = true) {
    executeAfterTransition(callback, element, isAnimated);
  }
  _getConfig(config) {
    config = this._mergeConfigObj(config, this._element);
    config = this._configAfterMerge(config);
    this._typeCheckConfig(config);
    return config;
  }
  // Static
  static getInstance(element) {
    return Data.get(getElement(element), this.DATA_KEY);
  }
  static getOrCreateInstance(element, config = {}) {
    return this.getInstance(element) || new this(element, typeof config === "object" ? config : null);
  }
  static get VERSION() {
    return VERSION;
  }
  static get DATA_KEY() {
    return `bs.${this.NAME}`;
  }
  static get EVENT_KEY() {
    return `.${this.DATA_KEY}`;
  }
  static eventName(name) {
    return `${name}${this.EVENT_KEY}`;
  }
};
var base_component_default = BaseComponent;

// node_modules/bootstrap/js/src/alert.js
import EventHandler2 from "./dom/event-handler.js";
import { enableDismissTrigger } from "./util/component-functions.js";
import { defineJQueryPlugin } from "./util/index.js";
var NAME = "alert";
var DATA_KEY = "bs.alert";
var EVENT_KEY = `.${DATA_KEY}`;
var EVENT_CLOSE = `close${EVENT_KEY}`;
var EVENT_CLOSED = `closed${EVENT_KEY}`;
var CLASS_NAME_FADE = "fade";
var CLASS_NAME_SHOW = "show";
var Alert = class _Alert extends base_component_default {
  // Getters
  static get NAME() {
    return NAME;
  }
  // Public
  close() {
    const closeEvent = EventHandler2.trigger(this._element, EVENT_CLOSE);
    if (closeEvent.defaultPrevented) {
      return;
    }
    this._element.classList.remove(CLASS_NAME_SHOW);
    const isAnimated = this._element.classList.contains(CLASS_NAME_FADE);
    this._queueCallback(() => this._destroyElement(), this._element, isAnimated);
  }
  // Private
  _destroyElement() {
    this._element.remove();
    EventHandler2.trigger(this._element, EVENT_CLOSED);
    this.dispose();
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Alert.getOrCreateInstance(this);
      if (typeof config !== "string") {
        return;
      }
      if (data[config] === void 0 || config.startsWith("_") || config === "constructor") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config](this);
    });
  }
};
enableDismissTrigger(Alert, "close");
defineJQueryPlugin(Alert);
var alert_default = Alert;

// node_modules/bootstrap/js/src/button.js
import EventHandler3 from "./dom/event-handler.js";
import { defineJQueryPlugin as defineJQueryPlugin2 } from "./util/index.js";
var NAME2 = "button";
var DATA_KEY2 = "bs.button";
var EVENT_KEY2 = `.${DATA_KEY2}`;
var DATA_API_KEY = ".data-api";
var CLASS_NAME_ACTIVE = "active";
var SELECTOR_DATA_TOGGLE = '[data-bs-toggle="button"]';
var EVENT_CLICK_DATA_API = `click${EVENT_KEY2}${DATA_API_KEY}`;
var Button = class _Button extends base_component_default {
  // Getters
  static get NAME() {
    return NAME2;
  }
  // Public
  toggle() {
    this._element.setAttribute("aria-pressed", this._element.classList.toggle(CLASS_NAME_ACTIVE));
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Button.getOrCreateInstance(this);
      if (config === "toggle") {
        data[config]();
      }
    });
  }
};
EventHandler3.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, (event) => {
  event.preventDefault();
  const button = event.target.closest(SELECTOR_DATA_TOGGLE);
  const data = Button.getOrCreateInstance(button);
  data.toggle();
});
defineJQueryPlugin2(Button);
var button_default = Button;

// node_modules/bootstrap/js/src/carousel.js
import EventHandler4 from "./dom/event-handler.js";
import Manipulator from "./dom/manipulator.js";
import SelectorEngine from "./dom/selector-engine.js";
import {
  defineJQueryPlugin as defineJQueryPlugin3,
  getNextActiveElement,
  isRTL,
  isVisible,
  reflow,
  triggerTransitionEnd
} from "./util/index.js";
import Swipe from "./util/swipe.js";
var NAME3 = "carousel";
var DATA_KEY3 = "bs.carousel";
var EVENT_KEY3 = `.${DATA_KEY3}`;
var DATA_API_KEY2 = ".data-api";
var ARROW_LEFT_KEY = "ArrowLeft";
var ARROW_RIGHT_KEY = "ArrowRight";
var TOUCHEVENT_COMPAT_WAIT = 500;
var ORDER_NEXT = "next";
var ORDER_PREV = "prev";
var DIRECTION_LEFT = "left";
var DIRECTION_RIGHT = "right";
var EVENT_SLIDE = `slide${EVENT_KEY3}`;
var EVENT_SLID = `slid${EVENT_KEY3}`;
var EVENT_KEYDOWN = `keydown${EVENT_KEY3}`;
var EVENT_MOUSEENTER = `mouseenter${EVENT_KEY3}`;
var EVENT_MOUSELEAVE = `mouseleave${EVENT_KEY3}`;
var EVENT_DRAG_START = `dragstart${EVENT_KEY3}`;
var EVENT_LOAD_DATA_API = `load${EVENT_KEY3}${DATA_API_KEY2}`;
var EVENT_CLICK_DATA_API2 = `click${EVENT_KEY3}${DATA_API_KEY2}`;
var CLASS_NAME_CAROUSEL = "carousel";
var CLASS_NAME_ACTIVE2 = "active";
var CLASS_NAME_SLIDE = "slide";
var CLASS_NAME_END = "carousel-item-end";
var CLASS_NAME_START = "carousel-item-start";
var CLASS_NAME_NEXT = "carousel-item-next";
var CLASS_NAME_PREV = "carousel-item-prev";
var SELECTOR_ACTIVE = ".active";
var SELECTOR_ITEM = ".carousel-item";
var SELECTOR_ACTIVE_ITEM = SELECTOR_ACTIVE + SELECTOR_ITEM;
var SELECTOR_ITEM_IMG = ".carousel-item img";
var SELECTOR_INDICATORS = ".carousel-indicators";
var SELECTOR_DATA_SLIDE = "[data-bs-slide], [data-bs-slide-to]";
var SELECTOR_DATA_RIDE = '[data-bs-ride="carousel"]';
var KEY_TO_DIRECTION = {
  [ARROW_LEFT_KEY]: DIRECTION_RIGHT,
  [ARROW_RIGHT_KEY]: DIRECTION_LEFT
};
var Default = {
  interval: 5e3,
  keyboard: true,
  pause: "hover",
  ride: false,
  touch: true,
  wrap: true
};
var DefaultType = {
  interval: "(number|boolean)",
  // TODO:v6 remove boolean support
  keyboard: "boolean",
  pause: "(string|boolean)",
  ride: "(boolean|string)",
  touch: "boolean",
  wrap: "boolean"
};
var Carousel = class _Carousel extends base_component_default {
  constructor(element, config) {
    super(element, config);
    this._interval = null;
    this._activeElement = null;
    this._isSliding = false;
    this.touchTimeout = null;
    this._swipeHelper = null;
    this._indicatorsElement = SelectorEngine.findOne(SELECTOR_INDICATORS, this._element);
    this._addEventListeners();
    if (this._config.ride === CLASS_NAME_CAROUSEL) {
      this.cycle();
    }
  }
  // Getters
  static get Default() {
    return Default;
  }
  static get DefaultType() {
    return DefaultType;
  }
  static get NAME() {
    return NAME3;
  }
  // Public
  next() {
    this._slide(ORDER_NEXT);
  }
  nextWhenVisible() {
    if (!document.hidden && isVisible(this._element)) {
      this.next();
    }
  }
  prev() {
    this._slide(ORDER_PREV);
  }
  pause() {
    if (this._isSliding) {
      triggerTransitionEnd(this._element);
    }
    this._clearInterval();
  }
  cycle() {
    this._clearInterval();
    this._updateInterval();
    this._interval = setInterval(() => this.nextWhenVisible(), this._config.interval);
  }
  _maybeEnableCycle() {
    if (!this._config.ride) {
      return;
    }
    if (this._isSliding) {
      EventHandler4.one(this._element, EVENT_SLID, () => this.cycle());
      return;
    }
    this.cycle();
  }
  to(index) {
    const items = this._getItems();
    if (index > items.length - 1 || index < 0) {
      return;
    }
    if (this._isSliding) {
      EventHandler4.one(this._element, EVENT_SLID, () => this.to(index));
      return;
    }
    const activeIndex = this._getItemIndex(this._getActive());
    if (activeIndex === index) {
      return;
    }
    const order = index > activeIndex ? ORDER_NEXT : ORDER_PREV;
    this._slide(order, items[index]);
  }
  dispose() {
    if (this._swipeHelper) {
      this._swipeHelper.dispose();
    }
    super.dispose();
  }
  // Private
  _configAfterMerge(config) {
    config.defaultInterval = config.interval;
    return config;
  }
  _addEventListeners() {
    if (this._config.keyboard) {
      EventHandler4.on(this._element, EVENT_KEYDOWN, (event) => this._keydown(event));
    }
    if (this._config.pause === "hover") {
      EventHandler4.on(this._element, EVENT_MOUSEENTER, () => this.pause());
      EventHandler4.on(this._element, EVENT_MOUSELEAVE, () => this._maybeEnableCycle());
    }
    if (this._config.touch && Swipe.isSupported()) {
      this._addTouchEventListeners();
    }
  }
  _addTouchEventListeners() {
    for (const img of SelectorEngine.find(SELECTOR_ITEM_IMG, this._element)) {
      EventHandler4.on(img, EVENT_DRAG_START, (event) => event.preventDefault());
    }
    const endCallBack = () => {
      if (this._config.pause !== "hover") {
        return;
      }
      this.pause();
      if (this.touchTimeout) {
        clearTimeout(this.touchTimeout);
      }
      this.touchTimeout = setTimeout(() => this._maybeEnableCycle(), TOUCHEVENT_COMPAT_WAIT + this._config.interval);
    };
    const swipeConfig = {
      leftCallback: () => this._slide(this._directionToOrder(DIRECTION_LEFT)),
      rightCallback: () => this._slide(this._directionToOrder(DIRECTION_RIGHT)),
      endCallback: endCallBack
    };
    this._swipeHelper = new Swipe(this._element, swipeConfig);
  }
  _keydown(event) {
    if (/input|textarea/i.test(event.target.tagName)) {
      return;
    }
    const direction = KEY_TO_DIRECTION[event.key];
    if (direction) {
      event.preventDefault();
      this._slide(this._directionToOrder(direction));
    }
  }
  _getItemIndex(element) {
    return this._getItems().indexOf(element);
  }
  _setActiveIndicatorElement(index) {
    if (!this._indicatorsElement) {
      return;
    }
    const activeIndicator = SelectorEngine.findOne(SELECTOR_ACTIVE, this._indicatorsElement);
    activeIndicator.classList.remove(CLASS_NAME_ACTIVE2);
    activeIndicator.removeAttribute("aria-current");
    const newActiveIndicator = SelectorEngine.findOne(`[data-bs-slide-to="${index}"]`, this._indicatorsElement);
    if (newActiveIndicator) {
      newActiveIndicator.classList.add(CLASS_NAME_ACTIVE2);
      newActiveIndicator.setAttribute("aria-current", "true");
    }
  }
  _updateInterval() {
    const element = this._activeElement || this._getActive();
    if (!element) {
      return;
    }
    const elementInterval = Number.parseInt(element.getAttribute("data-bs-interval"), 10);
    this._config.interval = elementInterval || this._config.defaultInterval;
  }
  _slide(order, element = null) {
    if (this._isSliding) {
      return;
    }
    const activeElement = this._getActive();
    const isNext = order === ORDER_NEXT;
    const nextElement = element || getNextActiveElement(this._getItems(), activeElement, isNext, this._config.wrap);
    if (nextElement === activeElement) {
      return;
    }
    const nextElementIndex = this._getItemIndex(nextElement);
    const triggerEvent = (eventName) => {
      return EventHandler4.trigger(this._element, eventName, {
        relatedTarget: nextElement,
        direction: this._orderToDirection(order),
        from: this._getItemIndex(activeElement),
        to: nextElementIndex
      });
    };
    const slideEvent = triggerEvent(EVENT_SLIDE);
    if (slideEvent.defaultPrevented) {
      return;
    }
    if (!activeElement || !nextElement) {
      return;
    }
    const isCycling = Boolean(this._interval);
    this.pause();
    this._isSliding = true;
    this._setActiveIndicatorElement(nextElementIndex);
    this._activeElement = nextElement;
    const directionalClassName = isNext ? CLASS_NAME_START : CLASS_NAME_END;
    const orderClassName = isNext ? CLASS_NAME_NEXT : CLASS_NAME_PREV;
    nextElement.classList.add(orderClassName);
    reflow(nextElement);
    activeElement.classList.add(directionalClassName);
    nextElement.classList.add(directionalClassName);
    const completeCallBack = () => {
      nextElement.classList.remove(directionalClassName, orderClassName);
      nextElement.classList.add(CLASS_NAME_ACTIVE2);
      activeElement.classList.remove(CLASS_NAME_ACTIVE2, orderClassName, directionalClassName);
      this._isSliding = false;
      triggerEvent(EVENT_SLID);
    };
    this._queueCallback(completeCallBack, activeElement, this._isAnimated());
    if (isCycling) {
      this.cycle();
    }
  }
  _isAnimated() {
    return this._element.classList.contains(CLASS_NAME_SLIDE);
  }
  _getActive() {
    return SelectorEngine.findOne(SELECTOR_ACTIVE_ITEM, this._element);
  }
  _getItems() {
    return SelectorEngine.find(SELECTOR_ITEM, this._element);
  }
  _clearInterval() {
    if (this._interval) {
      clearInterval(this._interval);
      this._interval = null;
    }
  }
  _directionToOrder(direction) {
    if (isRTL()) {
      return direction === DIRECTION_LEFT ? ORDER_PREV : ORDER_NEXT;
    }
    return direction === DIRECTION_LEFT ? ORDER_NEXT : ORDER_PREV;
  }
  _orderToDirection(order) {
    if (isRTL()) {
      return order === ORDER_PREV ? DIRECTION_LEFT : DIRECTION_RIGHT;
    }
    return order === ORDER_PREV ? DIRECTION_RIGHT : DIRECTION_LEFT;
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Carousel.getOrCreateInstance(this, config);
      if (typeof config === "number") {
        data.to(config);
        return;
      }
      if (typeof config === "string") {
        if (data[config] === void 0 || config.startsWith("_") || config === "constructor") {
          throw new TypeError(`No method named "${config}"`);
        }
        data[config]();
      }
    });
  }
};
EventHandler4.on(document, EVENT_CLICK_DATA_API2, SELECTOR_DATA_SLIDE, function(event) {
  const target = SelectorEngine.getElementFromSelector(this);
  if (!target || !target.classList.contains(CLASS_NAME_CAROUSEL)) {
    return;
  }
  event.preventDefault();
  const carousel = Carousel.getOrCreateInstance(target);
  const slideIndex = this.getAttribute("data-bs-slide-to");
  if (slideIndex) {
    carousel.to(slideIndex);
    carousel._maybeEnableCycle();
    return;
  }
  if (Manipulator.getDataAttribute(this, "slide") === "next") {
    carousel.next();
    carousel._maybeEnableCycle();
    return;
  }
  carousel.prev();
  carousel._maybeEnableCycle();
});
EventHandler4.on(window, EVENT_LOAD_DATA_API, () => {
  const carousels = SelectorEngine.find(SELECTOR_DATA_RIDE);
  for (const carousel of carousels) {
    Carousel.getOrCreateInstance(carousel);
  }
});
defineJQueryPlugin3(Carousel);
var carousel_default = Carousel;

// node_modules/bootstrap/js/src/collapse.js
import EventHandler5 from "./dom/event-handler.js";
import SelectorEngine2 from "./dom/selector-engine.js";
import {
  defineJQueryPlugin as defineJQueryPlugin4,
  getElement as getElement2,
  reflow as reflow2
} from "./util/index.js";
var NAME4 = "collapse";
var DATA_KEY4 = "bs.collapse";
var EVENT_KEY4 = `.${DATA_KEY4}`;
var DATA_API_KEY3 = ".data-api";
var EVENT_SHOW = `show${EVENT_KEY4}`;
var EVENT_SHOWN = `shown${EVENT_KEY4}`;
var EVENT_HIDE = `hide${EVENT_KEY4}`;
var EVENT_HIDDEN = `hidden${EVENT_KEY4}`;
var EVENT_CLICK_DATA_API3 = `click${EVENT_KEY4}${DATA_API_KEY3}`;
var CLASS_NAME_SHOW2 = "show";
var CLASS_NAME_COLLAPSE = "collapse";
var CLASS_NAME_COLLAPSING = "collapsing";
var CLASS_NAME_COLLAPSED = "collapsed";
var CLASS_NAME_DEEPER_CHILDREN = `:scope .${CLASS_NAME_COLLAPSE} .${CLASS_NAME_COLLAPSE}`;
var CLASS_NAME_HORIZONTAL = "collapse-horizontal";
var WIDTH = "width";
var HEIGHT = "height";
var SELECTOR_ACTIVES = ".collapse.show, .collapse.collapsing";
var SELECTOR_DATA_TOGGLE2 = '[data-bs-toggle="collapse"]';
var Default2 = {
  parent: null,
  toggle: true
};
var DefaultType2 = {
  parent: "(null|element)",
  toggle: "boolean"
};
var Collapse = class _Collapse extends base_component_default {
  constructor(element, config) {
    super(element, config);
    this._isTransitioning = false;
    this._triggerArray = [];
    const toggleList = SelectorEngine2.find(SELECTOR_DATA_TOGGLE2);
    for (const elem of toggleList) {
      const selector = SelectorEngine2.getSelectorFromElement(elem);
      const filterElement = SelectorEngine2.find(selector).filter((foundElement) => foundElement === this._element);
      if (selector !== null && filterElement.length) {
        this._triggerArray.push(elem);
      }
    }
    this._initializeChildren();
    if (!this._config.parent) {
      this._addAriaAndCollapsedClass(this._triggerArray, this._isShown());
    }
    if (this._config.toggle) {
      this.toggle();
    }
  }
  // Getters
  static get Default() {
    return Default2;
  }
  static get DefaultType() {
    return DefaultType2;
  }
  static get NAME() {
    return NAME4;
  }
  // Public
  toggle() {
    if (this._isShown()) {
      this.hide();
    } else {
      this.show();
    }
  }
  show() {
    if (this._isTransitioning || this._isShown()) {
      return;
    }
    let activeChildren = [];
    if (this._config.parent) {
      activeChildren = this._getFirstLevelChildren(SELECTOR_ACTIVES).filter((element) => element !== this._element).map((element) => _Collapse.getOrCreateInstance(element, { toggle: false }));
    }
    if (activeChildren.length && activeChildren[0]._isTransitioning) {
      return;
    }
    const startEvent = EventHandler5.trigger(this._element, EVENT_SHOW);
    if (startEvent.defaultPrevented) {
      return;
    }
    for (const activeInstance of activeChildren) {
      activeInstance.hide();
    }
    const dimension = this._getDimension();
    this._element.classList.remove(CLASS_NAME_COLLAPSE);
    this._element.classList.add(CLASS_NAME_COLLAPSING);
    this._element.style[dimension] = 0;
    this._addAriaAndCollapsedClass(this._triggerArray, true);
    this._isTransitioning = true;
    const complete = () => {
      this._isTransitioning = false;
      this._element.classList.remove(CLASS_NAME_COLLAPSING);
      this._element.classList.add(CLASS_NAME_COLLAPSE, CLASS_NAME_SHOW2);
      this._element.style[dimension] = "";
      EventHandler5.trigger(this._element, EVENT_SHOWN);
    };
    const capitalizedDimension = dimension[0].toUpperCase() + dimension.slice(1);
    const scrollSize = `scroll${capitalizedDimension}`;
    this._queueCallback(complete, this._element, true);
    this._element.style[dimension] = `${this._element[scrollSize]}px`;
  }
  hide() {
    if (this._isTransitioning || !this._isShown()) {
      return;
    }
    const startEvent = EventHandler5.trigger(this._element, EVENT_HIDE);
    if (startEvent.defaultPrevented) {
      return;
    }
    const dimension = this._getDimension();
    this._element.style[dimension] = `${this._element.getBoundingClientRect()[dimension]}px`;
    reflow2(this._element);
    this._element.classList.add(CLASS_NAME_COLLAPSING);
    this._element.classList.remove(CLASS_NAME_COLLAPSE, CLASS_NAME_SHOW2);
    for (const trigger of this._triggerArray) {
      const element = SelectorEngine2.getElementFromSelector(trigger);
      if (element && !this._isShown(element)) {
        this._addAriaAndCollapsedClass([trigger], false);
      }
    }
    this._isTransitioning = true;
    const complete = () => {
      this._isTransitioning = false;
      this._element.classList.remove(CLASS_NAME_COLLAPSING);
      this._element.classList.add(CLASS_NAME_COLLAPSE);
      EventHandler5.trigger(this._element, EVENT_HIDDEN);
    };
    this._element.style[dimension] = "";
    this._queueCallback(complete, this._element, true);
  }
  // Private
  _isShown(element = this._element) {
    return element.classList.contains(CLASS_NAME_SHOW2);
  }
  _configAfterMerge(config) {
    config.toggle = Boolean(config.toggle);
    config.parent = getElement2(config.parent);
    return config;
  }
  _getDimension() {
    return this._element.classList.contains(CLASS_NAME_HORIZONTAL) ? WIDTH : HEIGHT;
  }
  _initializeChildren() {
    if (!this._config.parent) {
      return;
    }
    const children = this._getFirstLevelChildren(SELECTOR_DATA_TOGGLE2);
    for (const element of children) {
      const selected = SelectorEngine2.getElementFromSelector(element);
      if (selected) {
        this._addAriaAndCollapsedClass([element], this._isShown(selected));
      }
    }
  }
  _getFirstLevelChildren(selector) {
    const children = SelectorEngine2.find(CLASS_NAME_DEEPER_CHILDREN, this._config.parent);
    return SelectorEngine2.find(selector, this._config.parent).filter((element) => !children.includes(element));
  }
  _addAriaAndCollapsedClass(triggerArray, isOpen) {
    if (!triggerArray.length) {
      return;
    }
    for (const element of triggerArray) {
      element.classList.toggle(CLASS_NAME_COLLAPSED, !isOpen);
      element.setAttribute("aria-expanded", isOpen);
    }
  }
  // Static
  static jQueryInterface(config) {
    const _config = {};
    if (typeof config === "string" && /show|hide/.test(config)) {
      _config.toggle = false;
    }
    return this.each(function() {
      const data = _Collapse.getOrCreateInstance(this, _config);
      if (typeof config === "string") {
        if (typeof data[config] === "undefined") {
          throw new TypeError(`No method named "${config}"`);
        }
        data[config]();
      }
    });
  }
};
EventHandler5.on(document, EVENT_CLICK_DATA_API3, SELECTOR_DATA_TOGGLE2, function(event) {
  if (event.target.tagName === "A" || event.delegateTarget && event.delegateTarget.tagName === "A") {
    event.preventDefault();
  }
  for (const element of SelectorEngine2.getMultipleElementsFromSelector(this)) {
    Collapse.getOrCreateInstance(element, { toggle: false }).toggle();
  }
});
defineJQueryPlugin4(Collapse);
var collapse_default = Collapse;

// node_modules/bootstrap/js/src/dropdown.js
import * as Popper from "@popperjs/core";
import EventHandler6 from "./dom/event-handler.js";
import Manipulator2 from "./dom/manipulator.js";
import SelectorEngine3 from "./dom/selector-engine.js";
import {
  defineJQueryPlugin as defineJQueryPlugin5,
  execute,
  getElement as getElement3,
  getNextActiveElement as getNextActiveElement2,
  isDisabled,
  isElement,
  isRTL as isRTL2,
  isVisible as isVisible2,
  noop
} from "./util/index.js";
var NAME5 = "dropdown";
var DATA_KEY5 = "bs.dropdown";
var EVENT_KEY5 = `.${DATA_KEY5}`;
var DATA_API_KEY4 = ".data-api";
var ESCAPE_KEY = "Escape";
var TAB_KEY = "Tab";
var ARROW_UP_KEY = "ArrowUp";
var ARROW_DOWN_KEY = "ArrowDown";
var RIGHT_MOUSE_BUTTON = 2;
var EVENT_HIDE2 = `hide${EVENT_KEY5}`;
var EVENT_HIDDEN2 = `hidden${EVENT_KEY5}`;
var EVENT_SHOW2 = `show${EVENT_KEY5}`;
var EVENT_SHOWN2 = `shown${EVENT_KEY5}`;
var EVENT_CLICK_DATA_API4 = `click${EVENT_KEY5}${DATA_API_KEY4}`;
var EVENT_KEYDOWN_DATA_API = `keydown${EVENT_KEY5}${DATA_API_KEY4}`;
var EVENT_KEYUP_DATA_API = `keyup${EVENT_KEY5}${DATA_API_KEY4}`;
var CLASS_NAME_SHOW3 = "show";
var CLASS_NAME_DROPUP = "dropup";
var CLASS_NAME_DROPEND = "dropend";
var CLASS_NAME_DROPSTART = "dropstart";
var CLASS_NAME_DROPUP_CENTER = "dropup-center";
var CLASS_NAME_DROPDOWN_CENTER = "dropdown-center";
var SELECTOR_DATA_TOGGLE3 = '[data-bs-toggle="dropdown"]:not(.disabled):not(:disabled)';
var SELECTOR_DATA_TOGGLE_SHOWN = `${SELECTOR_DATA_TOGGLE3}.${CLASS_NAME_SHOW3}`;
var SELECTOR_MENU = ".dropdown-menu";
var SELECTOR_NAVBAR = ".navbar";
var SELECTOR_NAVBAR_NAV = ".navbar-nav";
var SELECTOR_VISIBLE_ITEMS = ".dropdown-menu .dropdown-item:not(.disabled):not(:disabled)";
var PLACEMENT_TOP = isRTL2() ? "top-end" : "top-start";
var PLACEMENT_TOPEND = isRTL2() ? "top-start" : "top-end";
var PLACEMENT_BOTTOM = isRTL2() ? "bottom-end" : "bottom-start";
var PLACEMENT_BOTTOMEND = isRTL2() ? "bottom-start" : "bottom-end";
var PLACEMENT_RIGHT = isRTL2() ? "left-start" : "right-start";
var PLACEMENT_LEFT = isRTL2() ? "right-start" : "left-start";
var PLACEMENT_TOPCENTER = "top";
var PLACEMENT_BOTTOMCENTER = "bottom";
var Default3 = {
  autoClose: true,
  boundary: "clippingParents",
  display: "dynamic",
  offset: [0, 2],
  popperConfig: null,
  reference: "toggle"
};
var DefaultType3 = {
  autoClose: "(boolean|string)",
  boundary: "(string|element)",
  display: "string",
  offset: "(array|string|function)",
  popperConfig: "(null|object|function)",
  reference: "(string|element|object)"
};
var Dropdown = class _Dropdown extends base_component_default {
  constructor(element, config) {
    super(element, config);
    this._popper = null;
    this._parent = this._element.parentNode;
    this._menu = SelectorEngine3.next(this._element, SELECTOR_MENU)[0] || SelectorEngine3.prev(this._element, SELECTOR_MENU)[0] || SelectorEngine3.findOne(SELECTOR_MENU, this._parent);
    this._inNavbar = this._detectNavbar();
  }
  // Getters
  static get Default() {
    return Default3;
  }
  static get DefaultType() {
    return DefaultType3;
  }
  static get NAME() {
    return NAME5;
  }
  // Public
  toggle() {
    return this._isShown() ? this.hide() : this.show();
  }
  show() {
    if (isDisabled(this._element) || this._isShown()) {
      return;
    }
    const relatedTarget = {
      relatedTarget: this._element
    };
    const showEvent = EventHandler6.trigger(this._element, EVENT_SHOW2, relatedTarget);
    if (showEvent.defaultPrevented) {
      return;
    }
    this._createPopper();
    if ("ontouchstart" in document.documentElement && !this._parent.closest(SELECTOR_NAVBAR_NAV)) {
      for (const element of [].concat(...document.body.children)) {
        EventHandler6.on(element, "mouseover", noop);
      }
    }
    this._element.focus();
    this._element.setAttribute("aria-expanded", true);
    this._menu.classList.add(CLASS_NAME_SHOW3);
    this._element.classList.add(CLASS_NAME_SHOW3);
    EventHandler6.trigger(this._element, EVENT_SHOWN2, relatedTarget);
  }
  hide() {
    if (isDisabled(this._element) || !this._isShown()) {
      return;
    }
    const relatedTarget = {
      relatedTarget: this._element
    };
    this._completeHide(relatedTarget);
  }
  dispose() {
    if (this._popper) {
      this._popper.destroy();
    }
    super.dispose();
  }
  update() {
    this._inNavbar = this._detectNavbar();
    if (this._popper) {
      this._popper.update();
    }
  }
  // Private
  _completeHide(relatedTarget) {
    const hideEvent = EventHandler6.trigger(this._element, EVENT_HIDE2, relatedTarget);
    if (hideEvent.defaultPrevented) {
      return;
    }
    if ("ontouchstart" in document.documentElement) {
      for (const element of [].concat(...document.body.children)) {
        EventHandler6.off(element, "mouseover", noop);
      }
    }
    if (this._popper) {
      this._popper.destroy();
    }
    this._menu.classList.remove(CLASS_NAME_SHOW3);
    this._element.classList.remove(CLASS_NAME_SHOW3);
    this._element.setAttribute("aria-expanded", "false");
    Manipulator2.removeDataAttribute(this._menu, "popper");
    EventHandler6.trigger(this._element, EVENT_HIDDEN2, relatedTarget);
  }
  _getConfig(config) {
    config = super._getConfig(config);
    if (typeof config.reference === "object" && !isElement(config.reference) && typeof config.reference.getBoundingClientRect !== "function") {
      throw new TypeError(`${NAME5.toUpperCase()}: Option "reference" provided type "object" without a required "getBoundingClientRect" method.`);
    }
    return config;
  }
  _createPopper() {
    if (typeof Popper === "undefined") {
      throw new TypeError("Bootstrap's dropdowns require Popper (https://popper.js.org/docs/v2/)");
    }
    let referenceElement = this._element;
    if (this._config.reference === "parent") {
      referenceElement = this._parent;
    } else if (isElement(this._config.reference)) {
      referenceElement = getElement3(this._config.reference);
    } else if (typeof this._config.reference === "object") {
      referenceElement = this._config.reference;
    }
    const popperConfig = this._getPopperConfig();
    this._popper = Popper.createPopper(referenceElement, this._menu, popperConfig);
  }
  _isShown() {
    return this._menu.classList.contains(CLASS_NAME_SHOW3);
  }
  _getPlacement() {
    const parentDropdown = this._parent;
    if (parentDropdown.classList.contains(CLASS_NAME_DROPEND)) {
      return PLACEMENT_RIGHT;
    }
    if (parentDropdown.classList.contains(CLASS_NAME_DROPSTART)) {
      return PLACEMENT_LEFT;
    }
    if (parentDropdown.classList.contains(CLASS_NAME_DROPUP_CENTER)) {
      return PLACEMENT_TOPCENTER;
    }
    if (parentDropdown.classList.contains(CLASS_NAME_DROPDOWN_CENTER)) {
      return PLACEMENT_BOTTOMCENTER;
    }
    const isEnd = getComputedStyle(this._menu).getPropertyValue("--bs-position").trim() === "end";
    if (parentDropdown.classList.contains(CLASS_NAME_DROPUP)) {
      return isEnd ? PLACEMENT_TOPEND : PLACEMENT_TOP;
    }
    return isEnd ? PLACEMENT_BOTTOMEND : PLACEMENT_BOTTOM;
  }
  _detectNavbar() {
    return this._element.closest(SELECTOR_NAVBAR) !== null;
  }
  _getOffset() {
    const { offset } = this._config;
    if (typeof offset === "string") {
      return offset.split(",").map((value) => Number.parseInt(value, 10));
    }
    if (typeof offset === "function") {
      return (popperData) => offset(popperData, this._element);
    }
    return offset;
  }
  _getPopperConfig() {
    const defaultBsPopperConfig = {
      placement: this._getPlacement(),
      modifiers: [
        {
          name: "preventOverflow",
          options: {
            boundary: this._config.boundary
          }
        },
        {
          name: "offset",
          options: {
            offset: this._getOffset()
          }
        }
      ]
    };
    if (this._inNavbar || this._config.display === "static") {
      Manipulator2.setDataAttribute(this._menu, "popper", "static");
      defaultBsPopperConfig.modifiers = [{
        name: "applyStyles",
        enabled: false
      }];
    }
    return {
      ...defaultBsPopperConfig,
      ...execute(this._config.popperConfig, [void 0, defaultBsPopperConfig])
    };
  }
  _selectMenuItem({ key, target }) {
    const items = SelectorEngine3.find(SELECTOR_VISIBLE_ITEMS, this._menu).filter((element) => isVisible2(element));
    if (!items.length) {
      return;
    }
    getNextActiveElement2(items, target, key === ARROW_DOWN_KEY, !items.includes(target)).focus();
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Dropdown.getOrCreateInstance(this, config);
      if (typeof config !== "string") {
        return;
      }
      if (typeof data[config] === "undefined") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config]();
    });
  }
  static clearMenus(event) {
    if (event.button === RIGHT_MOUSE_BUTTON || event.type === "keyup" && event.key !== TAB_KEY) {
      return;
    }
    const openToggles = SelectorEngine3.find(SELECTOR_DATA_TOGGLE_SHOWN);
    for (const toggle of openToggles) {
      const context = _Dropdown.getInstance(toggle);
      if (!context || context._config.autoClose === false) {
        continue;
      }
      const composedPath = event.composedPath();
      const isMenuTarget = composedPath.includes(context._menu);
      if (composedPath.includes(context._element) || context._config.autoClose === "inside" && !isMenuTarget || context._config.autoClose === "outside" && isMenuTarget) {
        continue;
      }
      if (context._menu.contains(event.target) && (event.type === "keyup" && event.key === TAB_KEY || /input|select|option|textarea|form/i.test(event.target.tagName))) {
        continue;
      }
      const relatedTarget = { relatedTarget: context._element };
      if (event.type === "click") {
        relatedTarget.clickEvent = event;
      }
      context._completeHide(relatedTarget);
    }
  }
  static dataApiKeydownHandler(event) {
    const isInput = /input|textarea/i.test(event.target.tagName);
    const isEscapeEvent = event.key === ESCAPE_KEY;
    const isUpOrDownEvent = [ARROW_UP_KEY, ARROW_DOWN_KEY].includes(event.key);
    if (!isUpOrDownEvent && !isEscapeEvent) {
      return;
    }
    if (isInput && !isEscapeEvent) {
      return;
    }
    event.preventDefault();
    const getToggleButton = this.matches(SELECTOR_DATA_TOGGLE3) ? this : SelectorEngine3.prev(this, SELECTOR_DATA_TOGGLE3)[0] || SelectorEngine3.next(this, SELECTOR_DATA_TOGGLE3)[0] || SelectorEngine3.findOne(SELECTOR_DATA_TOGGLE3, event.delegateTarget.parentNode);
    const instance = _Dropdown.getOrCreateInstance(getToggleButton);
    if (isUpOrDownEvent) {
      event.stopPropagation();
      instance.show();
      instance._selectMenuItem(event);
      return;
    }
    if (instance._isShown()) {
      event.stopPropagation();
      instance.hide();
      getToggleButton.focus();
    }
  }
};
EventHandler6.on(document, EVENT_KEYDOWN_DATA_API, SELECTOR_DATA_TOGGLE3, Dropdown.dataApiKeydownHandler);
EventHandler6.on(document, EVENT_KEYDOWN_DATA_API, SELECTOR_MENU, Dropdown.dataApiKeydownHandler);
EventHandler6.on(document, EVENT_CLICK_DATA_API4, Dropdown.clearMenus);
EventHandler6.on(document, EVENT_KEYUP_DATA_API, Dropdown.clearMenus);
EventHandler6.on(document, EVENT_CLICK_DATA_API4, SELECTOR_DATA_TOGGLE3, function(event) {
  event.preventDefault();
  Dropdown.getOrCreateInstance(this).toggle();
});
defineJQueryPlugin5(Dropdown);
var dropdown_default = Dropdown;

// node_modules/bootstrap/js/src/modal.js
import EventHandler7 from "./dom/event-handler.js";
import SelectorEngine4 from "./dom/selector-engine.js";
import Backdrop from "./util/backdrop.js";
import { enableDismissTrigger as enableDismissTrigger2 } from "./util/component-functions.js";
import FocusTrap from "./util/focustrap.js";
import {
  defineJQueryPlugin as defineJQueryPlugin6,
  isRTL as isRTL3,
  isVisible as isVisible3,
  reflow as reflow3
} from "./util/index.js";
import ScrollBarHelper from "./util/scrollbar.js";
var NAME6 = "modal";
var DATA_KEY6 = "bs.modal";
var EVENT_KEY6 = `.${DATA_KEY6}`;
var DATA_API_KEY5 = ".data-api";
var ESCAPE_KEY2 = "Escape";
var EVENT_HIDE3 = `hide${EVENT_KEY6}`;
var EVENT_HIDE_PREVENTED = `hidePrevented${EVENT_KEY6}`;
var EVENT_HIDDEN3 = `hidden${EVENT_KEY6}`;
var EVENT_SHOW3 = `show${EVENT_KEY6}`;
var EVENT_SHOWN3 = `shown${EVENT_KEY6}`;
var EVENT_RESIZE = `resize${EVENT_KEY6}`;
var EVENT_CLICK_DISMISS = `click.dismiss${EVENT_KEY6}`;
var EVENT_MOUSEDOWN_DISMISS = `mousedown.dismiss${EVENT_KEY6}`;
var EVENT_KEYDOWN_DISMISS = `keydown.dismiss${EVENT_KEY6}`;
var EVENT_CLICK_DATA_API5 = `click${EVENT_KEY6}${DATA_API_KEY5}`;
var CLASS_NAME_OPEN = "modal-open";
var CLASS_NAME_FADE2 = "fade";
var CLASS_NAME_SHOW4 = "show";
var CLASS_NAME_STATIC = "modal-static";
var OPEN_SELECTOR = ".modal.show";
var SELECTOR_DIALOG = ".modal-dialog";
var SELECTOR_MODAL_BODY = ".modal-body";
var SELECTOR_DATA_TOGGLE4 = '[data-bs-toggle="modal"]';
var Default4 = {
  backdrop: true,
  focus: true,
  keyboard: true
};
var DefaultType4 = {
  backdrop: "(boolean|string)",
  focus: "boolean",
  keyboard: "boolean"
};
var Modal = class _Modal extends base_component_default {
  constructor(element, config) {
    super(element, config);
    this._dialog = SelectorEngine4.findOne(SELECTOR_DIALOG, this._element);
    this._backdrop = this._initializeBackDrop();
    this._focustrap = this._initializeFocusTrap();
    this._isShown = false;
    this._isTransitioning = false;
    this._scrollBar = new ScrollBarHelper();
    this._addEventListeners();
  }
  // Getters
  static get Default() {
    return Default4;
  }
  static get DefaultType() {
    return DefaultType4;
  }
  static get NAME() {
    return NAME6;
  }
  // Public
  toggle(relatedTarget) {
    return this._isShown ? this.hide() : this.show(relatedTarget);
  }
  show(relatedTarget) {
    if (this._isShown || this._isTransitioning) {
      return;
    }
    const showEvent = EventHandler7.trigger(this._element, EVENT_SHOW3, {
      relatedTarget
    });
    if (showEvent.defaultPrevented) {
      return;
    }
    this._isShown = true;
    this._isTransitioning = true;
    this._scrollBar.hide();
    document.body.classList.add(CLASS_NAME_OPEN);
    this._adjustDialog();
    this._backdrop.show(() => this._showElement(relatedTarget));
  }
  hide() {
    if (!this._isShown || this._isTransitioning) {
      return;
    }
    const hideEvent = EventHandler7.trigger(this._element, EVENT_HIDE3);
    if (hideEvent.defaultPrevented) {
      return;
    }
    this._isShown = false;
    this._isTransitioning = true;
    this._focustrap.deactivate();
    this._element.classList.remove(CLASS_NAME_SHOW4);
    this._queueCallback(() => this._hideModal(), this._element, this._isAnimated());
  }
  dispose() {
    EventHandler7.off(window, EVENT_KEY6);
    EventHandler7.off(this._dialog, EVENT_KEY6);
    this._backdrop.dispose();
    this._focustrap.deactivate();
    super.dispose();
  }
  handleUpdate() {
    this._adjustDialog();
  }
  // Private
  _initializeBackDrop() {
    return new Backdrop({
      isVisible: Boolean(this._config.backdrop),
      // 'static' option will be translated to true, and booleans will keep their value,
      isAnimated: this._isAnimated()
    });
  }
  _initializeFocusTrap() {
    return new FocusTrap({
      trapElement: this._element
    });
  }
  _showElement(relatedTarget) {
    if (!document.body.contains(this._element)) {
      document.body.append(this._element);
    }
    this._element.style.display = "block";
    this._element.removeAttribute("aria-hidden");
    this._element.setAttribute("aria-modal", true);
    this._element.setAttribute("role", "dialog");
    this._element.scrollTop = 0;
    const modalBody = SelectorEngine4.findOne(SELECTOR_MODAL_BODY, this._dialog);
    if (modalBody) {
      modalBody.scrollTop = 0;
    }
    reflow3(this._element);
    this._element.classList.add(CLASS_NAME_SHOW4);
    const transitionComplete = () => {
      if (this._config.focus) {
        this._focustrap.activate();
      }
      this._isTransitioning = false;
      EventHandler7.trigger(this._element, EVENT_SHOWN3, {
        relatedTarget
      });
    };
    this._queueCallback(transitionComplete, this._dialog, this._isAnimated());
  }
  _addEventListeners() {
    EventHandler7.on(this._element, EVENT_KEYDOWN_DISMISS, (event) => {
      if (event.key !== ESCAPE_KEY2) {
        return;
      }
      if (this._config.keyboard) {
        this.hide();
        return;
      }
      this._triggerBackdropTransition();
    });
    EventHandler7.on(window, EVENT_RESIZE, () => {
      if (this._isShown && !this._isTransitioning) {
        this._adjustDialog();
      }
    });
    EventHandler7.on(this._element, EVENT_MOUSEDOWN_DISMISS, (event) => {
      EventHandler7.one(this._element, EVENT_CLICK_DISMISS, (event2) => {
        if (this._element !== event.target || this._element !== event2.target) {
          return;
        }
        if (this._config.backdrop === "static") {
          this._triggerBackdropTransition();
          return;
        }
        if (this._config.backdrop) {
          this.hide();
        }
      });
    });
  }
  _hideModal() {
    this._element.style.display = "none";
    this._element.setAttribute("aria-hidden", true);
    this._element.removeAttribute("aria-modal");
    this._element.removeAttribute("role");
    this._isTransitioning = false;
    this._backdrop.hide(() => {
      document.body.classList.remove(CLASS_NAME_OPEN);
      this._resetAdjustments();
      this._scrollBar.reset();
      EventHandler7.trigger(this._element, EVENT_HIDDEN3);
    });
  }
  _isAnimated() {
    return this._element.classList.contains(CLASS_NAME_FADE2);
  }
  _triggerBackdropTransition() {
    const hideEvent = EventHandler7.trigger(this._element, EVENT_HIDE_PREVENTED);
    if (hideEvent.defaultPrevented) {
      return;
    }
    const isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;
    const initialOverflowY = this._element.style.overflowY;
    if (initialOverflowY === "hidden" || this._element.classList.contains(CLASS_NAME_STATIC)) {
      return;
    }
    if (!isModalOverflowing) {
      this._element.style.overflowY = "hidden";
    }
    this._element.classList.add(CLASS_NAME_STATIC);
    this._queueCallback(() => {
      this._element.classList.remove(CLASS_NAME_STATIC);
      this._queueCallback(() => {
        this._element.style.overflowY = initialOverflowY;
      }, this._dialog);
    }, this._dialog);
    this._element.focus();
  }
  /**
   * The following methods are used to handle overflowing modals
   */
  _adjustDialog() {
    const isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;
    const scrollbarWidth = this._scrollBar.getWidth();
    const isBodyOverflowing = scrollbarWidth > 0;
    if (isBodyOverflowing && !isModalOverflowing) {
      const property = isRTL3() ? "paddingLeft" : "paddingRight";
      this._element.style[property] = `${scrollbarWidth}px`;
    }
    if (!isBodyOverflowing && isModalOverflowing) {
      const property = isRTL3() ? "paddingRight" : "paddingLeft";
      this._element.style[property] = `${scrollbarWidth}px`;
    }
  }
  _resetAdjustments() {
    this._element.style.paddingLeft = "";
    this._element.style.paddingRight = "";
  }
  // Static
  static jQueryInterface(config, relatedTarget) {
    return this.each(function() {
      const data = _Modal.getOrCreateInstance(this, config);
      if (typeof config !== "string") {
        return;
      }
      if (typeof data[config] === "undefined") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config](relatedTarget);
    });
  }
};
EventHandler7.on(document, EVENT_CLICK_DATA_API5, SELECTOR_DATA_TOGGLE4, function(event) {
  const target = SelectorEngine4.getElementFromSelector(this);
  if (["A", "AREA"].includes(this.tagName)) {
    event.preventDefault();
  }
  EventHandler7.one(target, EVENT_SHOW3, (showEvent) => {
    if (showEvent.defaultPrevented) {
      return;
    }
    EventHandler7.one(target, EVENT_HIDDEN3, () => {
      if (isVisible3(this)) {
        this.focus();
      }
    });
  });
  const alreadyOpen = SelectorEngine4.findOne(OPEN_SELECTOR);
  if (alreadyOpen) {
    Modal.getInstance(alreadyOpen).hide();
  }
  const data = Modal.getOrCreateInstance(target);
  data.toggle(this);
});
enableDismissTrigger2(Modal);
defineJQueryPlugin6(Modal);
var modal_default = Modal;

// node_modules/bootstrap/js/src/offcanvas.js
import EventHandler8 from "./dom/event-handler.js";
import SelectorEngine5 from "./dom/selector-engine.js";
import Backdrop2 from "./util/backdrop.js";
import { enableDismissTrigger as enableDismissTrigger3 } from "./util/component-functions.js";
import FocusTrap2 from "./util/focustrap.js";
import {
  defineJQueryPlugin as defineJQueryPlugin7,
  isDisabled as isDisabled2,
  isVisible as isVisible4
} from "./util/index.js";
import ScrollBarHelper2 from "./util/scrollbar.js";
var NAME7 = "offcanvas";
var DATA_KEY7 = "bs.offcanvas";
var EVENT_KEY7 = `.${DATA_KEY7}`;
var DATA_API_KEY6 = ".data-api";
var EVENT_LOAD_DATA_API2 = `load${EVENT_KEY7}${DATA_API_KEY6}`;
var ESCAPE_KEY3 = "Escape";
var CLASS_NAME_SHOW5 = "show";
var CLASS_NAME_SHOWING = "showing";
var CLASS_NAME_HIDING = "hiding";
var CLASS_NAME_BACKDROP = "offcanvas-backdrop";
var OPEN_SELECTOR2 = ".offcanvas.show";
var EVENT_SHOW4 = `show${EVENT_KEY7}`;
var EVENT_SHOWN4 = `shown${EVENT_KEY7}`;
var EVENT_HIDE4 = `hide${EVENT_KEY7}`;
var EVENT_HIDE_PREVENTED2 = `hidePrevented${EVENT_KEY7}`;
var EVENT_HIDDEN4 = `hidden${EVENT_KEY7}`;
var EVENT_RESIZE2 = `resize${EVENT_KEY7}`;
var EVENT_CLICK_DATA_API6 = `click${EVENT_KEY7}${DATA_API_KEY6}`;
var EVENT_KEYDOWN_DISMISS2 = `keydown.dismiss${EVENT_KEY7}`;
var SELECTOR_DATA_TOGGLE5 = '[data-bs-toggle="offcanvas"]';
var Default5 = {
  backdrop: true,
  keyboard: true,
  scroll: false
};
var DefaultType5 = {
  backdrop: "(boolean|string)",
  keyboard: "boolean",
  scroll: "boolean"
};
var Offcanvas = class _Offcanvas extends base_component_default {
  constructor(element, config) {
    super(element, config);
    this._isShown = false;
    this._backdrop = this._initializeBackDrop();
    this._focustrap = this._initializeFocusTrap();
    this._addEventListeners();
  }
  // Getters
  static get Default() {
    return Default5;
  }
  static get DefaultType() {
    return DefaultType5;
  }
  static get NAME() {
    return NAME7;
  }
  // Public
  toggle(relatedTarget) {
    return this._isShown ? this.hide() : this.show(relatedTarget);
  }
  show(relatedTarget) {
    if (this._isShown) {
      return;
    }
    const showEvent = EventHandler8.trigger(this._element, EVENT_SHOW4, { relatedTarget });
    if (showEvent.defaultPrevented) {
      return;
    }
    this._isShown = true;
    this._backdrop.show();
    if (!this._config.scroll) {
      new ScrollBarHelper2().hide();
    }
    this._element.setAttribute("aria-modal", true);
    this._element.setAttribute("role", "dialog");
    this._element.classList.add(CLASS_NAME_SHOWING);
    const completeCallBack = () => {
      if (!this._config.scroll || this._config.backdrop) {
        this._focustrap.activate();
      }
      this._element.classList.add(CLASS_NAME_SHOW5);
      this._element.classList.remove(CLASS_NAME_SHOWING);
      EventHandler8.trigger(this._element, EVENT_SHOWN4, { relatedTarget });
    };
    this._queueCallback(completeCallBack, this._element, true);
  }
  hide() {
    if (!this._isShown) {
      return;
    }
    const hideEvent = EventHandler8.trigger(this._element, EVENT_HIDE4);
    if (hideEvent.defaultPrevented) {
      return;
    }
    this._focustrap.deactivate();
    this._element.blur();
    this._isShown = false;
    this._element.classList.add(CLASS_NAME_HIDING);
    this._backdrop.hide();
    const completeCallback = () => {
      this._element.classList.remove(CLASS_NAME_SHOW5, CLASS_NAME_HIDING);
      this._element.removeAttribute("aria-modal");
      this._element.removeAttribute("role");
      if (!this._config.scroll) {
        new ScrollBarHelper2().reset();
      }
      EventHandler8.trigger(this._element, EVENT_HIDDEN4);
    };
    this._queueCallback(completeCallback, this._element, true);
  }
  dispose() {
    this._backdrop.dispose();
    this._focustrap.deactivate();
    super.dispose();
  }
  // Private
  _initializeBackDrop() {
    const clickCallback = () => {
      if (this._config.backdrop === "static") {
        EventHandler8.trigger(this._element, EVENT_HIDE_PREVENTED2);
        return;
      }
      this.hide();
    };
    const isVisible6 = Boolean(this._config.backdrop);
    return new Backdrop2({
      className: CLASS_NAME_BACKDROP,
      isVisible: isVisible6,
      isAnimated: true,
      rootElement: this._element.parentNode,
      clickCallback: isVisible6 ? clickCallback : null
    });
  }
  _initializeFocusTrap() {
    return new FocusTrap2({
      trapElement: this._element
    });
  }
  _addEventListeners() {
    EventHandler8.on(this._element, EVENT_KEYDOWN_DISMISS2, (event) => {
      if (event.key !== ESCAPE_KEY3) {
        return;
      }
      if (this._config.keyboard) {
        this.hide();
        return;
      }
      EventHandler8.trigger(this._element, EVENT_HIDE_PREVENTED2);
    });
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Offcanvas.getOrCreateInstance(this, config);
      if (typeof config !== "string") {
        return;
      }
      if (data[config] === void 0 || config.startsWith("_") || config === "constructor") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config](this);
    });
  }
};
EventHandler8.on(document, EVENT_CLICK_DATA_API6, SELECTOR_DATA_TOGGLE5, function(event) {
  const target = SelectorEngine5.getElementFromSelector(this);
  if (["A", "AREA"].includes(this.tagName)) {
    event.preventDefault();
  }
  if (isDisabled2(this)) {
    return;
  }
  EventHandler8.one(target, EVENT_HIDDEN4, () => {
    if (isVisible4(this)) {
      this.focus();
    }
  });
  const alreadyOpen = SelectorEngine5.findOne(OPEN_SELECTOR2);
  if (alreadyOpen && alreadyOpen !== target) {
    Offcanvas.getInstance(alreadyOpen).hide();
  }
  const data = Offcanvas.getOrCreateInstance(target);
  data.toggle(this);
});
EventHandler8.on(window, EVENT_LOAD_DATA_API2, () => {
  for (const selector of SelectorEngine5.find(OPEN_SELECTOR2)) {
    Offcanvas.getOrCreateInstance(selector).show();
  }
});
EventHandler8.on(window, EVENT_RESIZE2, () => {
  for (const element of SelectorEngine5.find("[aria-modal][class*=show][class*=offcanvas-]")) {
    if (getComputedStyle(element).position !== "fixed") {
      Offcanvas.getOrCreateInstance(element).hide();
    }
  }
});
enableDismissTrigger3(Offcanvas);
defineJQueryPlugin7(Offcanvas);
var offcanvas_default = Offcanvas;

// node_modules/bootstrap/js/src/tooltip.js
import * as Popper2 from "@popperjs/core";
import EventHandler9 from "./dom/event-handler.js";
import Manipulator3 from "./dom/manipulator.js";
import {
  defineJQueryPlugin as defineJQueryPlugin8,
  execute as execute2,
  findShadowRoot,
  getElement as getElement4,
  getUID,
  isRTL as isRTL4,
  noop as noop2
} from "./util/index.js";
import { DefaultAllowlist } from "./util/sanitizer.js";
import TemplateFactory from "./util/template-factory.js";
var NAME8 = "tooltip";
var DISALLOWED_ATTRIBUTES = /* @__PURE__ */ new Set(["sanitize", "allowList", "sanitizeFn"]);
var CLASS_NAME_FADE3 = "fade";
var CLASS_NAME_MODAL = "modal";
var CLASS_NAME_SHOW6 = "show";
var SELECTOR_TOOLTIP_INNER = ".tooltip-inner";
var SELECTOR_MODAL = `.${CLASS_NAME_MODAL}`;
var EVENT_MODAL_HIDE = "hide.bs.modal";
var TRIGGER_HOVER = "hover";
var TRIGGER_FOCUS = "focus";
var TRIGGER_CLICK = "click";
var TRIGGER_MANUAL = "manual";
var EVENT_HIDE5 = "hide";
var EVENT_HIDDEN5 = "hidden";
var EVENT_SHOW5 = "show";
var EVENT_SHOWN5 = "shown";
var EVENT_INSERTED = "inserted";
var EVENT_CLICK = "click";
var EVENT_FOCUSIN = "focusin";
var EVENT_FOCUSOUT = "focusout";
var EVENT_MOUSEENTER2 = "mouseenter";
var EVENT_MOUSELEAVE2 = "mouseleave";
var AttachmentMap = {
  AUTO: "auto",
  TOP: "top",
  RIGHT: isRTL4() ? "left" : "right",
  BOTTOM: "bottom",
  LEFT: isRTL4() ? "right" : "left"
};
var Default6 = {
  allowList: DefaultAllowlist,
  animation: true,
  boundary: "clippingParents",
  container: false,
  customClass: "",
  delay: 0,
  fallbackPlacements: ["top", "right", "bottom", "left"],
  html: false,
  offset: [0, 6],
  placement: "top",
  popperConfig: null,
  sanitize: true,
  sanitizeFn: null,
  selector: false,
  template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
  title: "",
  trigger: "hover focus"
};
var DefaultType6 = {
  allowList: "object",
  animation: "boolean",
  boundary: "(string|element)",
  container: "(string|element|boolean)",
  customClass: "(string|function)",
  delay: "(number|object)",
  fallbackPlacements: "array",
  html: "boolean",
  offset: "(array|string|function)",
  placement: "(string|function)",
  popperConfig: "(null|object|function)",
  sanitize: "boolean",
  sanitizeFn: "(null|function)",
  selector: "(string|boolean)",
  template: "string",
  title: "(string|element|function)",
  trigger: "string"
};
var Tooltip = class _Tooltip extends base_component_default {
  constructor(element, config) {
    if (typeof Popper2 === "undefined") {
      throw new TypeError("Bootstrap's tooltips require Popper (https://popper.js.org/docs/v2/)");
    }
    super(element, config);
    this._isEnabled = true;
    this._timeout = 0;
    this._isHovered = null;
    this._activeTrigger = {};
    this._popper = null;
    this._templateFactory = null;
    this._newContent = null;
    this.tip = null;
    this._setListeners();
    if (!this._config.selector) {
      this._fixTitle();
    }
  }
  // Getters
  static get Default() {
    return Default6;
  }
  static get DefaultType() {
    return DefaultType6;
  }
  static get NAME() {
    return NAME8;
  }
  // Public
  enable() {
    this._isEnabled = true;
  }
  disable() {
    this._isEnabled = false;
  }
  toggleEnabled() {
    this._isEnabled = !this._isEnabled;
  }
  toggle() {
    if (!this._isEnabled) {
      return;
    }
    if (this._isShown()) {
      this._leave();
      return;
    }
    this._enter();
  }
  dispose() {
    clearTimeout(this._timeout);
    EventHandler9.off(this._element.closest(SELECTOR_MODAL), EVENT_MODAL_HIDE, this._hideModalHandler);
    if (this._element.getAttribute("data-bs-original-title")) {
      this._element.setAttribute("title", this._element.getAttribute("data-bs-original-title"));
    }
    this._disposePopper();
    super.dispose();
  }
  show() {
    if (this._element.style.display === "none") {
      throw new Error("Please use show on visible elements");
    }
    if (!(this._isWithContent() && this._isEnabled)) {
      return;
    }
    const showEvent = EventHandler9.trigger(this._element, this.constructor.eventName(EVENT_SHOW5));
    const shadowRoot = findShadowRoot(this._element);
    const isInTheDom = (shadowRoot || this._element.ownerDocument.documentElement).contains(this._element);
    if (showEvent.defaultPrevented || !isInTheDom) {
      return;
    }
    this._disposePopper();
    const tip = this._getTipElement();
    this._element.setAttribute("aria-describedby", tip.getAttribute("id"));
    const { container } = this._config;
    if (!this._element.ownerDocument.documentElement.contains(this.tip)) {
      container.append(tip);
      EventHandler9.trigger(this._element, this.constructor.eventName(EVENT_INSERTED));
    }
    this._popper = this._createPopper(tip);
    tip.classList.add(CLASS_NAME_SHOW6);
    if ("ontouchstart" in document.documentElement) {
      for (const element of [].concat(...document.body.children)) {
        EventHandler9.on(element, "mouseover", noop2);
      }
    }
    const complete = () => {
      EventHandler9.trigger(this._element, this.constructor.eventName(EVENT_SHOWN5));
      if (this._isHovered === false) {
        this._leave();
      }
      this._isHovered = false;
    };
    this._queueCallback(complete, this.tip, this._isAnimated());
  }
  hide() {
    if (!this._isShown()) {
      return;
    }
    const hideEvent = EventHandler9.trigger(this._element, this.constructor.eventName(EVENT_HIDE5));
    if (hideEvent.defaultPrevented) {
      return;
    }
    const tip = this._getTipElement();
    tip.classList.remove(CLASS_NAME_SHOW6);
    if ("ontouchstart" in document.documentElement) {
      for (const element of [].concat(...document.body.children)) {
        EventHandler9.off(element, "mouseover", noop2);
      }
    }
    this._activeTrigger[TRIGGER_CLICK] = false;
    this._activeTrigger[TRIGGER_FOCUS] = false;
    this._activeTrigger[TRIGGER_HOVER] = false;
    this._isHovered = null;
    const complete = () => {
      if (this._isWithActiveTrigger()) {
        return;
      }
      if (!this._isHovered) {
        this._disposePopper();
      }
      this._element.removeAttribute("aria-describedby");
      EventHandler9.trigger(this._element, this.constructor.eventName(EVENT_HIDDEN5));
    };
    this._queueCallback(complete, this.tip, this._isAnimated());
  }
  update() {
    if (this._popper) {
      this._popper.update();
    }
  }
  // Protected
  _isWithContent() {
    return Boolean(this._getTitle());
  }
  _getTipElement() {
    if (!this.tip) {
      this.tip = this._createTipElement(this._newContent || this._getContentForTemplate());
    }
    return this.tip;
  }
  _createTipElement(content) {
    const tip = this._getTemplateFactory(content).toHtml();
    if (!tip) {
      return null;
    }
    tip.classList.remove(CLASS_NAME_FADE3, CLASS_NAME_SHOW6);
    tip.classList.add(`bs-${this.constructor.NAME}-auto`);
    const tipId = getUID(this.constructor.NAME).toString();
    tip.setAttribute("id", tipId);
    if (this._isAnimated()) {
      tip.classList.add(CLASS_NAME_FADE3);
    }
    return tip;
  }
  setContent(content) {
    this._newContent = content;
    if (this._isShown()) {
      this._disposePopper();
      this.show();
    }
  }
  _getTemplateFactory(content) {
    if (this._templateFactory) {
      this._templateFactory.changeContent(content);
    } else {
      this._templateFactory = new TemplateFactory({
        ...this._config,
        // the `content` var has to be after `this._config`
        // to override config.content in case of popover
        content,
        extraClass: this._resolvePossibleFunction(this._config.customClass)
      });
    }
    return this._templateFactory;
  }
  _getContentForTemplate() {
    return {
      [SELECTOR_TOOLTIP_INNER]: this._getTitle()
    };
  }
  _getTitle() {
    return this._resolvePossibleFunction(this._config.title) || this._element.getAttribute("data-bs-original-title");
  }
  // Private
  _initializeOnDelegatedTarget(event) {
    return this.constructor.getOrCreateInstance(event.delegateTarget, this._getDelegateConfig());
  }
  _isAnimated() {
    return this._config.animation || this.tip && this.tip.classList.contains(CLASS_NAME_FADE3);
  }
  _isShown() {
    return this.tip && this.tip.classList.contains(CLASS_NAME_SHOW6);
  }
  _createPopper(tip) {
    const placement = execute2(this._config.placement, [this, tip, this._element]);
    const attachment = AttachmentMap[placement.toUpperCase()];
    return Popper2.createPopper(this._element, tip, this._getPopperConfig(attachment));
  }
  _getOffset() {
    const { offset } = this._config;
    if (typeof offset === "string") {
      return offset.split(",").map((value) => Number.parseInt(value, 10));
    }
    if (typeof offset === "function") {
      return (popperData) => offset(popperData, this._element);
    }
    return offset;
  }
  _resolvePossibleFunction(arg) {
    return execute2(arg, [this._element, this._element]);
  }
  _getPopperConfig(attachment) {
    const defaultBsPopperConfig = {
      placement: attachment,
      modifiers: [
        {
          name: "flip",
          options: {
            fallbackPlacements: this._config.fallbackPlacements
          }
        },
        {
          name: "offset",
          options: {
            offset: this._getOffset()
          }
        },
        {
          name: "preventOverflow",
          options: {
            boundary: this._config.boundary
          }
        },
        {
          name: "arrow",
          options: {
            element: `.${this.constructor.NAME}-arrow`
          }
        },
        {
          name: "preSetPlacement",
          enabled: true,
          phase: "beforeMain",
          fn: (data) => {
            this._getTipElement().setAttribute("data-popper-placement", data.state.placement);
          }
        }
      ]
    };
    return {
      ...defaultBsPopperConfig,
      ...execute2(this._config.popperConfig, [void 0, defaultBsPopperConfig])
    };
  }
  _setListeners() {
    const triggers = this._config.trigger.split(" ");
    for (const trigger of triggers) {
      if (trigger === "click") {
        EventHandler9.on(this._element, this.constructor.eventName(EVENT_CLICK), this._config.selector, (event) => {
          const context = this._initializeOnDelegatedTarget(event);
          context._activeTrigger[TRIGGER_CLICK] = !(context._isShown() && context._activeTrigger[TRIGGER_CLICK]);
          context.toggle();
        });
      } else if (trigger !== TRIGGER_MANUAL) {
        const eventIn = trigger === TRIGGER_HOVER ? this.constructor.eventName(EVENT_MOUSEENTER2) : this.constructor.eventName(EVENT_FOCUSIN);
        const eventOut = trigger === TRIGGER_HOVER ? this.constructor.eventName(EVENT_MOUSELEAVE2) : this.constructor.eventName(EVENT_FOCUSOUT);
        EventHandler9.on(this._element, eventIn, this._config.selector, (event) => {
          const context = this._initializeOnDelegatedTarget(event);
          context._activeTrigger[event.type === "focusin" ? TRIGGER_FOCUS : TRIGGER_HOVER] = true;
          context._enter();
        });
        EventHandler9.on(this._element, eventOut, this._config.selector, (event) => {
          const context = this._initializeOnDelegatedTarget(event);
          context._activeTrigger[event.type === "focusout" ? TRIGGER_FOCUS : TRIGGER_HOVER] = context._element.contains(event.relatedTarget);
          context._leave();
        });
      }
    }
    this._hideModalHandler = () => {
      if (this._element) {
        this.hide();
      }
    };
    EventHandler9.on(this._element.closest(SELECTOR_MODAL), EVENT_MODAL_HIDE, this._hideModalHandler);
  }
  _fixTitle() {
    const title = this._element.getAttribute("title");
    if (!title) {
      return;
    }
    if (!this._element.getAttribute("aria-label") && !this._element.textContent.trim()) {
      this._element.setAttribute("aria-label", title);
    }
    this._element.setAttribute("data-bs-original-title", title);
    this._element.removeAttribute("title");
  }
  _enter() {
    if (this._isShown() || this._isHovered) {
      this._isHovered = true;
      return;
    }
    this._isHovered = true;
    this._setTimeout(() => {
      if (this._isHovered) {
        this.show();
      }
    }, this._config.delay.show);
  }
  _leave() {
    if (this._isWithActiveTrigger()) {
      return;
    }
    this._isHovered = false;
    this._setTimeout(() => {
      if (!this._isHovered) {
        this.hide();
      }
    }, this._config.delay.hide);
  }
  _setTimeout(handler, timeout) {
    clearTimeout(this._timeout);
    this._timeout = setTimeout(handler, timeout);
  }
  _isWithActiveTrigger() {
    return Object.values(this._activeTrigger).includes(true);
  }
  _getConfig(config) {
    const dataAttributes = Manipulator3.getDataAttributes(this._element);
    for (const dataAttribute of Object.keys(dataAttributes)) {
      if (DISALLOWED_ATTRIBUTES.has(dataAttribute)) {
        delete dataAttributes[dataAttribute];
      }
    }
    config = {
      ...dataAttributes,
      ...typeof config === "object" && config ? config : {}
    };
    config = this._mergeConfigObj(config);
    config = this._configAfterMerge(config);
    this._typeCheckConfig(config);
    return config;
  }
  _configAfterMerge(config) {
    config.container = config.container === false ? document.body : getElement4(config.container);
    if (typeof config.delay === "number") {
      config.delay = {
        show: config.delay,
        hide: config.delay
      };
    }
    if (typeof config.title === "number") {
      config.title = config.title.toString();
    }
    if (typeof config.content === "number") {
      config.content = config.content.toString();
    }
    return config;
  }
  _getDelegateConfig() {
    const config = {};
    for (const [key, value] of Object.entries(this._config)) {
      if (this.constructor.Default[key] !== value) {
        config[key] = value;
      }
    }
    config.selector = false;
    config.trigger = "manual";
    return config;
  }
  _disposePopper() {
    if (this._popper) {
      this._popper.destroy();
      this._popper = null;
    }
    if (this.tip) {
      this.tip.remove();
      this.tip = null;
    }
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Tooltip.getOrCreateInstance(this, config);
      if (typeof config !== "string") {
        return;
      }
      if (typeof data[config] === "undefined") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config]();
    });
  }
};
defineJQueryPlugin8(Tooltip);
var tooltip_default = Tooltip;

// node_modules/bootstrap/js/src/popover.js
import { defineJQueryPlugin as defineJQueryPlugin9 } from "./util/index.js";
var NAME9 = "popover";
var SELECTOR_TITLE = ".popover-header";
var SELECTOR_CONTENT = ".popover-body";
var Default7 = {
  ...tooltip_default.Default,
  content: "",
  offset: [0, 8],
  placement: "right",
  template: '<div class="popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
  trigger: "click"
};
var DefaultType7 = {
  ...tooltip_default.DefaultType,
  content: "(null|string|element|function)"
};
var Popover = class _Popover extends tooltip_default {
  // Getters
  static get Default() {
    return Default7;
  }
  static get DefaultType() {
    return DefaultType7;
  }
  static get NAME() {
    return NAME9;
  }
  // Overrides
  _isWithContent() {
    return this._getTitle() || this._getContent();
  }
  // Private
  _getContentForTemplate() {
    return {
      [SELECTOR_TITLE]: this._getTitle(),
      [SELECTOR_CONTENT]: this._getContent()
    };
  }
  _getContent() {
    return this._resolvePossibleFunction(this._config.content);
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Popover.getOrCreateInstance(this, config);
      if (typeof config !== "string") {
        return;
      }
      if (typeof data[config] === "undefined") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config]();
    });
  }
};
defineJQueryPlugin9(Popover);
var popover_default = Popover;

// node_modules/bootstrap/js/src/scrollspy.js
import EventHandler10 from "./dom/event-handler.js";
import SelectorEngine6 from "./dom/selector-engine.js";
import {
  defineJQueryPlugin as defineJQueryPlugin10,
  getElement as getElement5,
  isDisabled as isDisabled3,
  isVisible as isVisible5
} from "./util/index.js";
var NAME10 = "scrollspy";
var DATA_KEY8 = "bs.scrollspy";
var EVENT_KEY8 = `.${DATA_KEY8}`;
var DATA_API_KEY7 = ".data-api";
var EVENT_ACTIVATE = `activate${EVENT_KEY8}`;
var EVENT_CLICK2 = `click${EVENT_KEY8}`;
var EVENT_LOAD_DATA_API3 = `load${EVENT_KEY8}${DATA_API_KEY7}`;
var CLASS_NAME_DROPDOWN_ITEM = "dropdown-item";
var CLASS_NAME_ACTIVE3 = "active";
var SELECTOR_DATA_SPY = '[data-bs-spy="scroll"]';
var SELECTOR_TARGET_LINKS = "[href]";
var SELECTOR_NAV_LIST_GROUP = ".nav, .list-group";
var SELECTOR_NAV_LINKS = ".nav-link";
var SELECTOR_NAV_ITEMS = ".nav-item";
var SELECTOR_LIST_ITEMS = ".list-group-item";
var SELECTOR_LINK_ITEMS = `${SELECTOR_NAV_LINKS}, ${SELECTOR_NAV_ITEMS} > ${SELECTOR_NAV_LINKS}, ${SELECTOR_LIST_ITEMS}`;
var SELECTOR_DROPDOWN = ".dropdown";
var SELECTOR_DROPDOWN_TOGGLE = ".dropdown-toggle";
var Default8 = {
  offset: null,
  // TODO: v6 @deprecated, keep it for backwards compatibility reasons
  rootMargin: "0px 0px -25%",
  smoothScroll: false,
  target: null,
  threshold: [0.1, 0.5, 1]
};
var DefaultType8 = {
  offset: "(number|null)",
  // TODO v6 @deprecated, keep it for backwards compatibility reasons
  rootMargin: "string",
  smoothScroll: "boolean",
  target: "element",
  threshold: "array"
};
var ScrollSpy = class _ScrollSpy extends base_component_default {
  constructor(element, config) {
    super(element, config);
    this._targetLinks = /* @__PURE__ */ new Map();
    this._observableSections = /* @__PURE__ */ new Map();
    this._rootElement = getComputedStyle(this._element).overflowY === "visible" ? null : this._element;
    this._activeTarget = null;
    this._observer = null;
    this._previousScrollData = {
      visibleEntryTop: 0,
      parentScrollTop: 0
    };
    this.refresh();
  }
  // Getters
  static get Default() {
    return Default8;
  }
  static get DefaultType() {
    return DefaultType8;
  }
  static get NAME() {
    return NAME10;
  }
  // Public
  refresh() {
    this._initializeTargetsAndObservables();
    this._maybeEnableSmoothScroll();
    if (this._observer) {
      this._observer.disconnect();
    } else {
      this._observer = this._getNewObserver();
    }
    for (const section of this._observableSections.values()) {
      this._observer.observe(section);
    }
  }
  dispose() {
    this._observer.disconnect();
    super.dispose();
  }
  // Private
  _configAfterMerge(config) {
    config.target = getElement5(config.target) || document.body;
    config.rootMargin = config.offset ? `${config.offset}px 0px -30%` : config.rootMargin;
    if (typeof config.threshold === "string") {
      config.threshold = config.threshold.split(",").map((value) => Number.parseFloat(value));
    }
    return config;
  }
  _maybeEnableSmoothScroll() {
    if (!this._config.smoothScroll) {
      return;
    }
    EventHandler10.off(this._config.target, EVENT_CLICK2);
    EventHandler10.on(this._config.target, EVENT_CLICK2, SELECTOR_TARGET_LINKS, (event) => {
      const observableSection = this._observableSections.get(event.target.hash);
      if (observableSection) {
        event.preventDefault();
        const root = this._rootElement || window;
        const height = observableSection.offsetTop - this._element.offsetTop;
        if (root.scrollTo) {
          root.scrollTo({ top: height, behavior: "smooth" });
          return;
        }
        root.scrollTop = height;
      }
    });
  }
  _getNewObserver() {
    const options = {
      root: this._rootElement,
      threshold: this._config.threshold,
      rootMargin: this._config.rootMargin
    };
    return new IntersectionObserver((entries) => this._observerCallback(entries), options);
  }
  // The logic of selection
  _observerCallback(entries) {
    const targetElement = (entry) => this._targetLinks.get(`#${entry.target.id}`);
    const activate = (entry) => {
      this._previousScrollData.visibleEntryTop = entry.target.offsetTop;
      this._process(targetElement(entry));
    };
    const parentScrollTop = (this._rootElement || document.documentElement).scrollTop;
    const userScrollsDown = parentScrollTop >= this._previousScrollData.parentScrollTop;
    this._previousScrollData.parentScrollTop = parentScrollTop;
    for (const entry of entries) {
      if (!entry.isIntersecting) {
        this._activeTarget = null;
        this._clearActiveClass(targetElement(entry));
        continue;
      }
      const entryIsLowerThanPrevious = entry.target.offsetTop >= this._previousScrollData.visibleEntryTop;
      if (userScrollsDown && entryIsLowerThanPrevious) {
        activate(entry);
        if (!parentScrollTop) {
          return;
        }
        continue;
      }
      if (!userScrollsDown && !entryIsLowerThanPrevious) {
        activate(entry);
      }
    }
  }
  _initializeTargetsAndObservables() {
    this._targetLinks = /* @__PURE__ */ new Map();
    this._observableSections = /* @__PURE__ */ new Map();
    const targetLinks = SelectorEngine6.find(SELECTOR_TARGET_LINKS, this._config.target);
    for (const anchor of targetLinks) {
      if (!anchor.hash || isDisabled3(anchor)) {
        continue;
      }
      const observableSection = SelectorEngine6.findOne(decodeURI(anchor.hash), this._element);
      if (isVisible5(observableSection)) {
        this._targetLinks.set(decodeURI(anchor.hash), anchor);
        this._observableSections.set(anchor.hash, observableSection);
      }
    }
  }
  _process(target) {
    if (this._activeTarget === target) {
      return;
    }
    this._clearActiveClass(this._config.target);
    this._activeTarget = target;
    target.classList.add(CLASS_NAME_ACTIVE3);
    this._activateParents(target);
    EventHandler10.trigger(this._element, EVENT_ACTIVATE, { relatedTarget: target });
  }
  _activateParents(target) {
    if (target.classList.contains(CLASS_NAME_DROPDOWN_ITEM)) {
      SelectorEngine6.findOne(SELECTOR_DROPDOWN_TOGGLE, target.closest(SELECTOR_DROPDOWN)).classList.add(CLASS_NAME_ACTIVE3);
      return;
    }
    for (const listGroup of SelectorEngine6.parents(target, SELECTOR_NAV_LIST_GROUP)) {
      for (const item of SelectorEngine6.prev(listGroup, SELECTOR_LINK_ITEMS)) {
        item.classList.add(CLASS_NAME_ACTIVE3);
      }
    }
  }
  _clearActiveClass(parent) {
    parent.classList.remove(CLASS_NAME_ACTIVE3);
    const activeNodes = SelectorEngine6.find(`${SELECTOR_TARGET_LINKS}.${CLASS_NAME_ACTIVE3}`, parent);
    for (const node of activeNodes) {
      node.classList.remove(CLASS_NAME_ACTIVE3);
    }
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _ScrollSpy.getOrCreateInstance(this, config);
      if (typeof config !== "string") {
        return;
      }
      if (data[config] === void 0 || config.startsWith("_") || config === "constructor") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config]();
    });
  }
};
EventHandler10.on(window, EVENT_LOAD_DATA_API3, () => {
  for (const spy of SelectorEngine6.find(SELECTOR_DATA_SPY)) {
    ScrollSpy.getOrCreateInstance(spy);
  }
});
defineJQueryPlugin10(ScrollSpy);
var scrollspy_default = ScrollSpy;

// node_modules/bootstrap/js/src/tab.js
import EventHandler11 from "./dom/event-handler.js";
import SelectorEngine7 from "./dom/selector-engine.js";
import { defineJQueryPlugin as defineJQueryPlugin11, getNextActiveElement as getNextActiveElement3, isDisabled as isDisabled4 } from "./util/index.js";
var NAME11 = "tab";
var DATA_KEY9 = "bs.tab";
var EVENT_KEY9 = `.${DATA_KEY9}`;
var EVENT_HIDE6 = `hide${EVENT_KEY9}`;
var EVENT_HIDDEN6 = `hidden${EVENT_KEY9}`;
var EVENT_SHOW6 = `show${EVENT_KEY9}`;
var EVENT_SHOWN6 = `shown${EVENT_KEY9}`;
var EVENT_CLICK_DATA_API7 = `click${EVENT_KEY9}`;
var EVENT_KEYDOWN2 = `keydown${EVENT_KEY9}`;
var EVENT_LOAD_DATA_API4 = `load${EVENT_KEY9}`;
var ARROW_LEFT_KEY2 = "ArrowLeft";
var ARROW_RIGHT_KEY2 = "ArrowRight";
var ARROW_UP_KEY2 = "ArrowUp";
var ARROW_DOWN_KEY2 = "ArrowDown";
var HOME_KEY = "Home";
var END_KEY = "End";
var CLASS_NAME_ACTIVE4 = "active";
var CLASS_NAME_FADE4 = "fade";
var CLASS_NAME_SHOW7 = "show";
var CLASS_DROPDOWN = "dropdown";
var SELECTOR_DROPDOWN_TOGGLE2 = ".dropdown-toggle";
var SELECTOR_DROPDOWN_MENU = ".dropdown-menu";
var NOT_SELECTOR_DROPDOWN_TOGGLE = `:not(${SELECTOR_DROPDOWN_TOGGLE2})`;
var SELECTOR_TAB_PANEL = '.list-group, .nav, [role="tablist"]';
var SELECTOR_OUTER = ".nav-item, .list-group-item";
var SELECTOR_INNER = `.nav-link${NOT_SELECTOR_DROPDOWN_TOGGLE}, .list-group-item${NOT_SELECTOR_DROPDOWN_TOGGLE}, [role="tab"]${NOT_SELECTOR_DROPDOWN_TOGGLE}`;
var SELECTOR_DATA_TOGGLE6 = '[data-bs-toggle="tab"], [data-bs-toggle="pill"], [data-bs-toggle="list"]';
var SELECTOR_INNER_ELEM = `${SELECTOR_INNER}, ${SELECTOR_DATA_TOGGLE6}`;
var SELECTOR_DATA_TOGGLE_ACTIVE = `.${CLASS_NAME_ACTIVE4}[data-bs-toggle="tab"], .${CLASS_NAME_ACTIVE4}[data-bs-toggle="pill"], .${CLASS_NAME_ACTIVE4}[data-bs-toggle="list"]`;
var Tab = class _Tab extends base_component_default {
  constructor(element) {
    super(element);
    this._parent = this._element.closest(SELECTOR_TAB_PANEL);
    if (!this._parent) {
      return;
    }
    this._setInitialAttributes(this._parent, this._getChildren());
    EventHandler11.on(this._element, EVENT_KEYDOWN2, (event) => this._keydown(event));
  }
  // Getters
  static get NAME() {
    return NAME11;
  }
  // Public
  show() {
    const innerElem = this._element;
    if (this._elemIsActive(innerElem)) {
      return;
    }
    const active = this._getActiveElem();
    const hideEvent = active ? EventHandler11.trigger(active, EVENT_HIDE6, { relatedTarget: innerElem }) : null;
    const showEvent = EventHandler11.trigger(innerElem, EVENT_SHOW6, { relatedTarget: active });
    if (showEvent.defaultPrevented || hideEvent && hideEvent.defaultPrevented) {
      return;
    }
    this._deactivate(active, innerElem);
    this._activate(innerElem, active);
  }
  // Private
  _activate(element, relatedElem) {
    if (!element) {
      return;
    }
    element.classList.add(CLASS_NAME_ACTIVE4);
    this._activate(SelectorEngine7.getElementFromSelector(element));
    const complete = () => {
      if (element.getAttribute("role") !== "tab") {
        element.classList.add(CLASS_NAME_SHOW7);
        return;
      }
      element.removeAttribute("tabindex");
      element.setAttribute("aria-selected", true);
      this._toggleDropDown(element, true);
      EventHandler11.trigger(element, EVENT_SHOWN6, {
        relatedTarget: relatedElem
      });
    };
    this._queueCallback(complete, element, element.classList.contains(CLASS_NAME_FADE4));
  }
  _deactivate(element, relatedElem) {
    if (!element) {
      return;
    }
    element.classList.remove(CLASS_NAME_ACTIVE4);
    element.blur();
    this._deactivate(SelectorEngine7.getElementFromSelector(element));
    const complete = () => {
      if (element.getAttribute("role") !== "tab") {
        element.classList.remove(CLASS_NAME_SHOW7);
        return;
      }
      element.setAttribute("aria-selected", false);
      element.setAttribute("tabindex", "-1");
      this._toggleDropDown(element, false);
      EventHandler11.trigger(element, EVENT_HIDDEN6, { relatedTarget: relatedElem });
    };
    this._queueCallback(complete, element, element.classList.contains(CLASS_NAME_FADE4));
  }
  _keydown(event) {
    if (![ARROW_LEFT_KEY2, ARROW_RIGHT_KEY2, ARROW_UP_KEY2, ARROW_DOWN_KEY2, HOME_KEY, END_KEY].includes(event.key)) {
      return;
    }
    event.stopPropagation();
    event.preventDefault();
    const children = this._getChildren().filter((element) => !isDisabled4(element));
    let nextActiveElement;
    if ([HOME_KEY, END_KEY].includes(event.key)) {
      nextActiveElement = children[event.key === HOME_KEY ? 0 : children.length - 1];
    } else {
      const isNext = [ARROW_RIGHT_KEY2, ARROW_DOWN_KEY2].includes(event.key);
      nextActiveElement = getNextActiveElement3(children, event.target, isNext, true);
    }
    if (nextActiveElement) {
      nextActiveElement.focus({ preventScroll: true });
      _Tab.getOrCreateInstance(nextActiveElement).show();
    }
  }
  _getChildren() {
    return SelectorEngine7.find(SELECTOR_INNER_ELEM, this._parent);
  }
  _getActiveElem() {
    return this._getChildren().find((child) => this._elemIsActive(child)) || null;
  }
  _setInitialAttributes(parent, children) {
    this._setAttributeIfNotExists(parent, "role", "tablist");
    for (const child of children) {
      this._setInitialAttributesOnChild(child);
    }
  }
  _setInitialAttributesOnChild(child) {
    child = this._getInnerElement(child);
    const isActive = this._elemIsActive(child);
    const outerElem = this._getOuterElement(child);
    child.setAttribute("aria-selected", isActive);
    if (outerElem !== child) {
      this._setAttributeIfNotExists(outerElem, "role", "presentation");
    }
    if (!isActive) {
      child.setAttribute("tabindex", "-1");
    }
    this._setAttributeIfNotExists(child, "role", "tab");
    this._setInitialAttributesOnTargetPanel(child);
  }
  _setInitialAttributesOnTargetPanel(child) {
    const target = SelectorEngine7.getElementFromSelector(child);
    if (!target) {
      return;
    }
    this._setAttributeIfNotExists(target, "role", "tabpanel");
    if (child.id) {
      this._setAttributeIfNotExists(target, "aria-labelledby", `${child.id}`);
    }
  }
  _toggleDropDown(element, open) {
    const outerElem = this._getOuterElement(element);
    if (!outerElem.classList.contains(CLASS_DROPDOWN)) {
      return;
    }
    const toggle = (selector, className) => {
      const element2 = SelectorEngine7.findOne(selector, outerElem);
      if (element2) {
        element2.classList.toggle(className, open);
      }
    };
    toggle(SELECTOR_DROPDOWN_TOGGLE2, CLASS_NAME_ACTIVE4);
    toggle(SELECTOR_DROPDOWN_MENU, CLASS_NAME_SHOW7);
    outerElem.setAttribute("aria-expanded", open);
  }
  _setAttributeIfNotExists(element, attribute, value) {
    if (!element.hasAttribute(attribute)) {
      element.setAttribute(attribute, value);
    }
  }
  _elemIsActive(elem) {
    return elem.classList.contains(CLASS_NAME_ACTIVE4);
  }
  // Try to get the inner element (usually the .nav-link)
  _getInnerElement(elem) {
    return elem.matches(SELECTOR_INNER_ELEM) ? elem : SelectorEngine7.findOne(SELECTOR_INNER_ELEM, elem);
  }
  // Try to get the outer element (usually the .nav-item)
  _getOuterElement(elem) {
    return elem.closest(SELECTOR_OUTER) || elem;
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Tab.getOrCreateInstance(this);
      if (typeof config !== "string") {
        return;
      }
      if (data[config] === void 0 || config.startsWith("_") || config === "constructor") {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config]();
    });
  }
};
EventHandler11.on(document, EVENT_CLICK_DATA_API7, SELECTOR_DATA_TOGGLE6, function(event) {
  if (["A", "AREA"].includes(this.tagName)) {
    event.preventDefault();
  }
  if (isDisabled4(this)) {
    return;
  }
  Tab.getOrCreateInstance(this).show();
});
EventHandler11.on(window, EVENT_LOAD_DATA_API4, () => {
  for (const element of SelectorEngine7.find(SELECTOR_DATA_TOGGLE_ACTIVE)) {
    Tab.getOrCreateInstance(element);
  }
});
defineJQueryPlugin11(Tab);
var tab_default = Tab;

// node_modules/bootstrap/js/src/toast.js
import EventHandler12 from "./dom/event-handler.js";
import { enableDismissTrigger as enableDismissTrigger4 } from "./util/component-functions.js";
import { defineJQueryPlugin as defineJQueryPlugin12, reflow as reflow4 } from "./util/index.js";
var NAME12 = "toast";
var DATA_KEY10 = "bs.toast";
var EVENT_KEY10 = `.${DATA_KEY10}`;
var EVENT_MOUSEOVER = `mouseover${EVENT_KEY10}`;
var EVENT_MOUSEOUT = `mouseout${EVENT_KEY10}`;
var EVENT_FOCUSIN2 = `focusin${EVENT_KEY10}`;
var EVENT_FOCUSOUT2 = `focusout${EVENT_KEY10}`;
var EVENT_HIDE7 = `hide${EVENT_KEY10}`;
var EVENT_HIDDEN7 = `hidden${EVENT_KEY10}`;
var EVENT_SHOW7 = `show${EVENT_KEY10}`;
var EVENT_SHOWN7 = `shown${EVENT_KEY10}`;
var CLASS_NAME_FADE5 = "fade";
var CLASS_NAME_HIDE = "hide";
var CLASS_NAME_SHOW8 = "show";
var CLASS_NAME_SHOWING2 = "showing";
var DefaultType9 = {
  animation: "boolean",
  autohide: "boolean",
  delay: "number"
};
var Default9 = {
  animation: true,
  autohide: true,
  delay: 5e3
};
var Toast = class _Toast extends base_component_default {
  constructor(element, config) {
    super(element, config);
    this._timeout = null;
    this._hasMouseInteraction = false;
    this._hasKeyboardInteraction = false;
    this._setListeners();
  }
  // Getters
  static get Default() {
    return Default9;
  }
  static get DefaultType() {
    return DefaultType9;
  }
  static get NAME() {
    return NAME12;
  }
  // Public
  show() {
    const showEvent = EventHandler12.trigger(this._element, EVENT_SHOW7);
    if (showEvent.defaultPrevented) {
      return;
    }
    this._clearTimeout();
    if (this._config.animation) {
      this._element.classList.add(CLASS_NAME_FADE5);
    }
    const complete = () => {
      this._element.classList.remove(CLASS_NAME_SHOWING2);
      EventHandler12.trigger(this._element, EVENT_SHOWN7);
      this._maybeScheduleHide();
    };
    this._element.classList.remove(CLASS_NAME_HIDE);
    reflow4(this._element);
    this._element.classList.add(CLASS_NAME_SHOW8, CLASS_NAME_SHOWING2);
    this._queueCallback(complete, this._element, this._config.animation);
  }
  hide() {
    if (!this.isShown()) {
      return;
    }
    const hideEvent = EventHandler12.trigger(this._element, EVENT_HIDE7);
    if (hideEvent.defaultPrevented) {
      return;
    }
    const complete = () => {
      this._element.classList.add(CLASS_NAME_HIDE);
      this._element.classList.remove(CLASS_NAME_SHOWING2, CLASS_NAME_SHOW8);
      EventHandler12.trigger(this._element, EVENT_HIDDEN7);
    };
    this._element.classList.add(CLASS_NAME_SHOWING2);
    this._queueCallback(complete, this._element, this._config.animation);
  }
  dispose() {
    this._clearTimeout();
    if (this.isShown()) {
      this._element.classList.remove(CLASS_NAME_SHOW8);
    }
    super.dispose();
  }
  isShown() {
    return this._element.classList.contains(CLASS_NAME_SHOW8);
  }
  // Private
  _maybeScheduleHide() {
    if (!this._config.autohide) {
      return;
    }
    if (this._hasMouseInteraction || this._hasKeyboardInteraction) {
      return;
    }
    this._timeout = setTimeout(() => {
      this.hide();
    }, this._config.delay);
  }
  _onInteraction(event, isInteracting) {
    switch (event.type) {
      case "mouseover":
      case "mouseout": {
        this._hasMouseInteraction = isInteracting;
        break;
      }
      case "focusin":
      case "focusout": {
        this._hasKeyboardInteraction = isInteracting;
        break;
      }
      default: {
        break;
      }
    }
    if (isInteracting) {
      this._clearTimeout();
      return;
    }
    const nextElement = event.relatedTarget;
    if (this._element === nextElement || this._element.contains(nextElement)) {
      return;
    }
    this._maybeScheduleHide();
  }
  _setListeners() {
    EventHandler12.on(this._element, EVENT_MOUSEOVER, (event) => this._onInteraction(event, true));
    EventHandler12.on(this._element, EVENT_MOUSEOUT, (event) => this._onInteraction(event, false));
    EventHandler12.on(this._element, EVENT_FOCUSIN2, (event) => this._onInteraction(event, true));
    EventHandler12.on(this._element, EVENT_FOCUSOUT2, (event) => this._onInteraction(event, false));
  }
  _clearTimeout() {
    clearTimeout(this._timeout);
    this._timeout = null;
  }
  // Static
  static jQueryInterface(config) {
    return this.each(function() {
      const data = _Toast.getOrCreateInstance(this, config);
      if (typeof config === "string") {
        if (typeof data[config] === "undefined") {
          throw new TypeError(`No method named "${config}"`);
        }
        data[config](this);
      }
    });
  }
};
enableDismissTrigger4(Toast);
defineJQueryPlugin12(Toast);
var toast_default = Toast;
export {
  alert_default as Alert,
  button_default as Button,
  carousel_default as Carousel,
  collapse_default as Collapse,
  dropdown_default as Dropdown,
  modal_default as Modal,
  offcanvas_default as Offcanvas,
  popover_default as Popover,
  scrollspy_default as ScrollSpy,
  tab_default as Tab,
  toast_default as Toast,
  tooltip_default as Tooltip
};
//# sourceMappingURL=bootstrap.dev.js.map
