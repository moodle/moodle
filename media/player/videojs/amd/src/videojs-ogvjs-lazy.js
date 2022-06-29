/**
 * videojs-ogvjs
 * @version 0.1.2
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license MIT
 */
/*! @name videojs-ogvjs @version 0.1.2 @license MIT */
(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory(require('video.js'), require('OGVCompat'), require('OGVLoader'), require('OGVPlayer')) :
	typeof define === 'function' && define.amd ? define(['media_videojs/video-lazy', './local/ogv/ogv'], factory) :
	(global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.videojsOgvjs = factory(global.videojs, global.OGVCompat, global.OGVLoader, global.OGVPlayer));
}(this, (function (videojs, ogvBase) { 'use strict';

	function _interopDefaultLegacy (e) { return e && typeof e === 'object' && 'default' in e ? e : { 'default': e }; }

	var videojs__default = /*#__PURE__*/_interopDefaultLegacy(videojs);
	var OGVCompat__default = /*#__PURE__*/_interopDefaultLegacy(ogvBase.OGVCompat);
	var OGVLoader__default = /*#__PURE__*/_interopDefaultLegacy(ogvBase.OGVLoader);
	var OGVPlayer__default = /*#__PURE__*/_interopDefaultLegacy(ogvBase.OGVPlayer);

	function createCommonjsModule(fn, basedir, module) {
		return module = {
		  path: basedir,
		  exports: {},
		  require: function (path, base) {
	      return commonjsRequire(path, (base === undefined || base === null) ? module.path : base);
	    }
		}, fn(module, module.exports), module.exports;
	}

	function commonjsRequire () {
		throw new Error('Dynamic requires are not currently supported by @rollup/plugin-commonjs');
	}

	var setPrototypeOf = createCommonjsModule(function (module) {
	  function _setPrototypeOf(o, p) {
	    module.exports = _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
	      o.__proto__ = p;
	      return o;
	    };

	    module.exports["default"] = module.exports, module.exports.__esModule = true;
	    return _setPrototypeOf(o, p);
	  }

	  module.exports = _setPrototypeOf;
	  module.exports["default"] = module.exports, module.exports.__esModule = true;
	});

	var inheritsLoose = createCommonjsModule(function (module) {
	  function _inheritsLoose(subClass, superClass) {
	    subClass.prototype = Object.create(superClass.prototype);
	    subClass.prototype.constructor = subClass;
	    setPrototypeOf(subClass, superClass);
	  }

	  module.exports = _inheritsLoose;
	  module.exports["default"] = module.exports, module.exports.__esModule = true;
	});

	var Tech = videojs__default['default'].getComponent('Tech');
	var androidOS = 'Android';
	var iPhoneOS = 'iPhoneOS';
	var iPadOS = 'iPadOS';
	var otherOS = 'Other';
	/**
	 * Object.defineProperty but "lazy", which means that the value is only set after
	 * it retrieved the first time, rather than being set right away.
	 *
	 * @param {Object} obj the object to set the property on.
	 * @param {string} key the key for the property to set.
	 * @param {Function} getValue the function used to get the value when it is needed.
	 * @param {boolean} setter whether a setter should be allowed or not.
	 */

	var defineLazyProperty = function defineLazyProperty(obj, key, getValue, setter) {
	  if (setter === void 0) {
	    setter = true;
	  }

	  var set = function set(value) {
	    Object.defineProperty(obj, key, {
	      value: value,
	      enumerable: true,
	      writable: true
	    });
	  };

	  var options = {
	    configurable: true,
	    enumerable: true,
	    get: function get() {
	      var value = getValue();
	      set(value);
	      return value;
	    }
	  };

	  if (setter) {
	    options.set = set;
	  }

	  return Object.defineProperty(obj, key, options);
	};
	/**
	 * Get the device's OS.
	 *
	 * @return {string} Device's OS.
	 */


	var getDeviceOS = function getDeviceOS() {
	  /* global navigator */
	  var ua = navigator.userAgent;

	  if (/android/i.test(ua)) {
	    return androidOS;
	  } else if (/iPad|iPhone|iPod/.test(ua)) {
	    return iPhoneOS;
	  } else if (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) {
	    return iPadOS;
	  }

	  return otherOS;
	};
	/**
	 * OgvJS Media Controller - Wrapper for ogv.js Media API
	 *
	 * @mixes Tech~SourceHandlerAdditions
	 * @extends Tech
	 */


	var OgvJS = /*#__PURE__*/function (_Tech) {
	  inheritsLoose(OgvJS, _Tech);

	  /**
	   * Create an instance of this Tech.
	   *
	   * @param {Object} [options] The key/value store of player options.
	   * @param {Component~ReadyCallback} ready Callback function to call when the `OgvJS` Tech is ready.
	   */
	  function OgvJS(options, ready) {
	    var _this;

	    _this = _Tech.call(this, options, ready) || this;
	    _this.el_.src = options.source.src;
	    OgvJS.setIfAvailable(_this.el_, 'autoplay', options.autoplay);
	    OgvJS.setIfAvailable(_this.el_, 'loop', options.loop);
	    OgvJS.setIfAvailable(_this.el_, 'poster', options.poster);
	    OgvJS.setIfAvailable(_this.el_, 'preload', options.preload);

	    _this.on('loadedmetadata', function () {
	      if (getDeviceOS() === iPhoneOS) {
	        // iPhoneOS add some inline styles to the canvas, we need to remove it.
	        var canvas = this.el_.getElementsByTagName('canvas')[0];
	        canvas.style.removeProperty('width');
	        canvas.style.removeProperty('margin');
	      }

	      this.triggerReady();
	    });

	    return _this;
	  }
	  /**
	   * Create the 'OgvJS' Tech's DOM element.
	   *
	   * @return {Element} The element that gets created.
	   */


	  var _proto = OgvJS.prototype;

	  _proto.createEl = function createEl() {
	    var options = this.options_;

	    if (options.base) {
	      OGVLoader__default['default'].base = options.base;
	    } else {
	      throw new Error('Please specify the base for the ogv.js library');
	    }

	    var el = new OGVPlayer__default['default'](options);
	    el.className += ' vjs-tech';
	    options.tag = el;
	    return el;
	  }
	  /**
	   * Start playback
	   *
	   * @method play
	   */
	  ;

	  _proto.play = function play() {
	    this.el_.play();
	  }
	  /**
	   * Get the current playback speed.
	   *
	   * @return {number}
	   * @method playbackRate
	   */
	  ;

	  _proto.playbackRate = function playbackRate() {
	    return this.el_.playbackRate || 1;
	  }
	  /**
	   * Set the playback speed.
	   *
	   * @param {number} val Speed for the player to play.
	   * @method setPlaybackRate
	   */
	  ;

	  _proto.setPlaybackRate = function setPlaybackRate(val) {
	    if (this.el_.hasOwnProperty('playbackRate')) {
	      this.el_.playbackRate = val;
	    }
	  }
	  /**
	   * Returns a TimeRanges object that represents the ranges of the media resource that the user agent has played.
	   *
	   * @return {TimeRangeObject} the range of points on the media timeline that has been reached through normal playback
	   */
	  ;

	  _proto.played = function played() {
	    return this.el_.played;
	  }
	  /**
	   * Pause playback
	   *
	   * @method pause
	   */
	  ;

	  _proto.pause = function pause() {
	    this.el_.pause();
	  }
	  /**
	   * Is the player paused or not.
	   *
	   * @return {boolean}
	   * @method paused
	   */
	  ;

	  _proto.paused = function paused() {
	    return this.el_.paused;
	  }
	  /**
	   * Get current playing time.
	   *
	   * @return {number}
	   * @method currentTime
	   */
	  ;

	  _proto.currentTime = function currentTime() {
	    return this.el_.currentTime;
	  }
	  /**
	   * Set current playing time.
	   *
	   * @param {number} seconds Current time of audio/video.
	   * @method setCurrentTime
	   */
	  ;

	  _proto.setCurrentTime = function setCurrentTime(seconds) {
	    try {
	      this.el_.currentTime = seconds;
	    } catch (e) {
	      videojs__default['default'].log(e, 'Media is not ready. (Video.JS)');
	    }
	  }
	  /**
	   * Get media's duration.
	   *
	   * @return {number}
	   * @method duration
	   */
	  ;

	  _proto.duration = function duration() {
	    if (this.el_.duration && this.el_.duration !== Infinity) {
	      return this.el_.duration;
	    }

	    return 0;
	  }
	  /**
	   * Get a TimeRange object that represents the intersection
	   * of the time ranges for which the user agent has all
	   * relevant media.
	   *
	   * @return {TimeRangeObject}
	   * @method buffered
	   */
	  ;

	  _proto.buffered = function buffered() {
	    return this.el_.buffered;
	  }
	  /**
	   * Get current volume level.
	   *
	   * @return {number}
	   * @method volume
	   */
	  ;

	  _proto.volume = function volume() {
	    return this.el_.hasOwnProperty('volume') ? this.el_.volume : 1;
	  }
	  /**
	   * Set current playing volume level.
	   *
	   * @param {number} percentAsDecimal Volume percent as a decimal.
	   * @method setVolume
	   */
	  ;

	  _proto.setVolume = function setVolume(percentAsDecimal) {
	    if (getDeviceOS() !== iPhoneOS && this.el_.hasOwnProperty('volume')) {
	      this.el_.volume = percentAsDecimal;
	    }
	  }
	  /**
	   * Is the player muted or not.
	   *
	   * @return {boolean}
	   * @method muted
	   */
	  ;

	  _proto.muted = function muted() {
	    return this.el_.muted;
	  }
	  /**
	   * Mute the player.
	   *
	   * @param {boolean} muted True to mute the player.
	   */
	  ;

	  _proto.setMuted = function setMuted(muted) {
	    this.el_.muted = !!muted;
	  }
	  /**
	   * Is the player muted by default or not.
	   *
	   * @return {boolean}
	   * @method defaultMuted
	   */
	  ;

	  _proto.defaultMuted = function defaultMuted() {
	    return this.el_.defaultMuted || false;
	  }
	  /**
	   * Get the player width.
	   *
	   * @return {number}
	   * @method width
	   */
	  ;

	  _proto.width = function width() {
	    return this.el_.offsetWidth;
	  }
	  /**
	   * Get the player height.
	   *
	   * @return {number}
	   * @method height
	   */
	  ;

	  _proto.height = function height() {
	    return this.el_.offsetHeight;
	  }
	  /**
	   * Get the video width.
	   *
	   * @return {number}
	   * @method videoWidth
	   */
	  ;

	  _proto.videoWidth = function videoWidth() {
	    return this.el_.videoWidth;
	  }
	  /**
	   * Get the video height.
	   *
	   * @return {number}
	   * @method videoHeight
	   */
	  ;

	  _proto.videoHeight = function videoHeight() {
	    return this.el_.videoHeight;
	  }
	  /**
	   * Get/set media source.
	   *
	   * @param {Object=} src Source object
	   * @return {Object}
	   * @method src
	   */
	  ;

	  _proto.src = function src(_src) {
	    if (typeof _src === 'undefined') {
	      return this.el_.src;
	    }

	    this.el_.src = _src;
	  }
	  /**
	   * Load the media into the player.
	   *
	   * @method load
	   */
	  ;

	  _proto.load = function load() {
	    this.el_.load();
	  }
	  /**
	   * Get current media source.
	   *
	   * @return {Object}
	   * @method currentSrc
	   */
	  ;

	  _proto.currentSrc = function currentSrc() {
	    if (this.currentSource_) {
	      return this.currentSource_.src;
	    }

	    return this.el_.currentSrc;
	  }
	  /**
	   * Get media poster URL.
	   *
	   * @return {string}
	   * @method poster
	   */
	  ;

	  _proto.poster = function poster() {
	    return this.el_.poster;
	  }
	  /**
	   * Set media poster URL.
	   *
	   * @param {string} url the poster image's url.
	   * @method
	   */
	  ;

	  _proto.setPoster = function setPoster(url) {
	    this.el_.poster = url;
	  }
	  /**
	   * Is the media preloaded or not.
	   *
	   * @return {string}
	   * @method preload
	   */
	  ;

	  _proto.preload = function preload() {
	    return this.el_.preload || 'none';
	  }
	  /**
	   * Set the media preload method.
	   *
	   * @param {string} val Value for preload attribute.
	   * @method setPreload
	   */
	  ;

	  _proto.setPreload = function setPreload(val) {
	    if (this.el_.hasOwnProperty('preload')) {
	      this.el_.preload = val;
	    }
	  }
	  /**
	   * Is the media auto-played or not.
	   *
	   * @return {boolean}
	   * @method autoplay
	   */
	  ;

	  _proto.autoplay = function autoplay() {
	    return this.el_.autoplay || false;
	  }
	  /**
	   * Set media autoplay method.
	   *
	   * @param {boolean} val Value for autoplay attribute.
	   * @method setAutoplay
	   */
	  ;

	  _proto.setAutoplay = function setAutoplay(val) {
	    if (this.el_.hasOwnProperty('autoplay')) {
	      this.el_.autoplay = !!val;
	    }
	  }
	  /**
	   * Does the media has controls or not.
	   *
	   * @return {boolean}
	   * @method controls
	   */
	  ;

	  _proto.controls = function controls() {
	    return this.el_.controls || false;
	  }
	  /**
	   * Set the media controls method.
	   *
	   * @param {boolean} val Value for controls attribute.
	   * @method setControls
	   */
	  ;

	  _proto.setControls = function setControls(val) {
	    if (this.el_.hasOwnProperty('controls')) {
	      this.el_.controls = !!val;
	    }
	  }
	  /**
	   * Is the media looped or not.
	   *
	   * @return {boolean}
	   * @method loop
	   */
	  ;

	  _proto.loop = function loop() {
	    return this.el_.loop || false;
	  }
	  /**
	   * Set the media loop method.
	   *
	   * @param {boolean} val Value for loop attribute.
	   * @method setLoop
	   */
	  ;

	  _proto.setLoop = function setLoop(val) {
	    if (this.el_.hasOwnProperty('loop')) {
	      this.el_.loop = !!val;
	    }
	  }
	  /**
	   * Get a TimeRanges object that represents the
	   * ranges of the media resource to which it is possible
	   * for the user agent to seek.
	   *
	   * @return {TimeRangeObject}
	   * @method seekable
	   */
	  ;

	  _proto.seekable = function seekable() {
	    return this.el_.seekable;
	  }
	  /**
	   * Is player in the "seeking" state or not.
	   *
	   * @return {boolean}
	   * @method seeking
	   */
	  ;

	  _proto.seeking = function seeking() {
	    return this.el_.seeking;
	  }
	  /**
	   * Is the media ended or not.
	   *
	   * @return {boolean}
	   * @method ended
	   */
	  ;

	  _proto.ended = function ended() {
	    return this.el_.ended;
	  }
	  /**
	   * Get the current state of network activity
	   * NETWORK_EMPTY (numeric value 0)
	   * NETWORK_IDLE (numeric value 1)
	   * NETWORK_LOADING (numeric value 2)
	   * NETWORK_NO_SOURCE (numeric value 3)
	   *
	   * @return {number}
	   * @method networkState
	   */
	  ;

	  _proto.networkState = function networkState() {
	    return this.el_.networkState;
	  }
	  /**
	   * Get the current state of the player.
	   * HAVE_NOTHING (numeric value 0)
	   * HAVE_METADATA (numeric value 1)
	   * HAVE_CURRENT_DATA (numeric value 2)
	   * HAVE_FUTURE_DATA (numeric value 3)
	   * HAVE_ENOUGH_DATA (numeric value 4)
	   *
	   * @return {number}
	   * @method readyState
	   */
	  ;

	  _proto.readyState = function readyState() {
	    return this.el_.readyState;
	  }
	  /**
	   * Does the player support native fullscreen mode or not. (Mobile devices)
	   *
	   * @return {boolean}
	   */
	  ;

	  _proto.supportsFullScreen = function supportsFullScreen() {
	    // iOS devices have some problem with HTML5 fullscreen api so we need to fallback to fullWindow mode.
	    return false;
	  }
	  /**
	   * Get media player error.
	   *
	   * @return {string}
	   * @method error
	   */
	  ;

	  _proto.error = function error() {
	    return this.el_.error;
	  };

	  return OgvJS;
	}(Tech);
	/**
	 * List of available events of the media player.
	 *
	 * @private
	 * @type {Array}
	 */


	OgvJS.Events = ['loadstart', 'suspend', 'abort', 'error', 'emptied', 'stalled', 'loadedmetadata', 'loadeddata', 'canplay', 'canplaythrough', 'playing', 'waiting', 'seeking', 'seeked', 'ended', 'durationchange', 'timeupdate', 'progress', 'play', 'pause', 'ratechange', 'resize', 'volumechange'];
	/**
	 * Set the value for the player is it has that property.
	 *
	 * @param {Element} el
	 * @param {string} name
	 * @param value
	 */

	OgvJS.setIfAvailable = function (el, name, value) {
	  if (el.hasOwnProperty(name)) {
	    el[name] = value;
	  }
	};
	/**
	 * Check if browser/device is supported by Ogv.JS.
	 *
	 * @return {boolean}
	 */


	OgvJS.isSupported = function () {
	  return OGVCompat__default['default'].supported('OGVPlayer');
	};
	/**
	 * Check if the tech can support the given type.
	 *
	 * @param {string} type The mimetype to check
	 * @return {string} 'probably', 'maybe', or '' (empty string)
	 */


	OgvJS.canPlayType = function (type) {
	  return type.indexOf('/ogg') !== -1 || type.indexOf('/webm') ? 'maybe' : '';
	};
	/**
	 * Check if the tech can support the given source
	 *
	 * @param srcObj The source object
	 * @return {string} The options passed to the tech
	 */


	OgvJS.canPlaySource = function (srcObj) {
	  return OgvJS.canPlayType(srcObj.type);
	};
	/**
	 * Check if the volume can be changed in this browser/device.
	 * Volume cannot be changed in a lot of mobile devices.
	 * Specifically, it can't be changed from 1 on iOS.
	 *
	 * @return {boolean} True if volume can be controlled.
	 */


	OgvJS.canControlVolume = function () {
	  if (getDeviceOS() === iPhoneOS) {
	    return false;
	  }

	  var p = new OGVPlayer__default['default']();
	  return p.hasOwnProperty('volume');
	};
	/**
	 * Check if the volume can be muted in this browser/device.
	 *
	 * @return {boolean} True if volume can be muted.
	 */


	OgvJS.canMuteVolume = function () {
	  return true;
	};
	/**
	 * Check if the playback rate can be changed in this browser/device.
	 *
	 * @return {boolean} True if playback rate can be controlled.
	 */


	OgvJS.canControlPlaybackRate = function () {
	  return true;
	};
	/**
	 * Check to see if native 'TextTracks' are supported by this browser/device.
	 *
	 * @return {boolean} True if native 'TextTracks' are supported.
	 */


	OgvJS.supportsNativeTextTracks = function () {
	  return false;
	};
	/**
	 * Check if the fullscreen resize is supported by this browser/device.
	 *
	 * @return {boolean} True if the fullscreen resize is supported.
	 */


	OgvJS.supportsFullscreenResize = function () {
	  return true;
	};
	/**
	 * Check if the progress events is supported by this browser/device.
	 *
	 * @return {boolean} True if the progress events is supported.
	 */


	OgvJS.supportsProgressEvents = function () {
	  return true;
	};
	/**
	 * Check if the time update events is supported by this browser/device.
	 *
	 * @return {boolean} True if the time update events is supported.
	 */


	OgvJS.supportsTimeupdateEvents = function () {
	  return true;
	};
	/**
	 * Boolean indicating whether the 'OgvJS' tech supports volume control.
	 *
	 * @type {boolean}
	 * @default {@link OgvJS.canControlVolume}
	 */

	/**
	 * Boolean indicating whether the 'OgvJS' tech supports muting volume.
	 *
	 * @type {boolean}
	 * @default {@link OgvJS.canMuteVolume}
	 */

	/**
	 * Boolean indicating whether the 'OgvJS' tech supports changing the speed at which the media plays.
	 * Examples:
	 *   - Set player to play 2x (twice) as fast.
	 *   - Set player to play 0.5x (half) as fast.
	 *
	 * @type {boolean}
	 * @default {@link OgvJS.canControlPlaybackRate}
	 */

	/**
	 * Boolean indicating whether the 'OgvJS' tech currently supports native 'TextTracks'.
	 *
	 * @type {boolean}
	 * @default {@link OgvJS.supportsNativeTextTracks}
	 */

	/**
	 * Boolean indicating whether the 'OgvJS' tech currently supports fullscreen resize.
	 *
	 * @type {boolean}
	 * @default {@link OgvJS.supportsFullscreenResize}
	 */

	/**
	 * Boolean indicating whether the 'OgvJS' tech currently supports progress events.
	 *
	 * @type {boolean}
	 * @default {@link OgvJS.supportsProgressEvents}
	 */

	/**
	 * Boolean indicating whether the 'OgvJS' tech currently supports time update events.
	 *
	 * @type {boolean}
	 * @default {@link OgvJS.supportsTimeupdateEvents}
	 */


	[['featuresVolumeControl', 'canControlVolume'], ['featuresMuteControl', 'canMuteVolume'], ['featuresPlaybackRate', 'canControlPlaybackRate'], ['featuresNativeTextTracks', 'supportsNativeTextTracks'], ['featuresFullscreenResize', 'supportsFullscreenResize'], ['featuresProgressEvents', 'supportsProgressEvents'], ['featuresTimeupdateEvents', 'supportsTimeupdateEvents']].forEach(function (_ref) {
	  var key = _ref[0],
	      fn = _ref[1];
	  defineLazyProperty(OgvJS.prototype, key, function () {
	    OgvJS[fn]();
	  }, true);
	});
	Tech.registerTech('OgvJS', OgvJS);

	return OgvJS;

})));
