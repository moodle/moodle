/**
 * videojs-ogvjs
 * @version 1.0.0
 * @copyright 2023 Huong Nguyen <huongnv13@gmail.com>
 * @license MIT
 */
define(['media_videojs/video-lazy', './local/ogv/ogv'], (function (videojs, ogv) { 'use strict';

	// We can access public classes either as ogv.OGVPlayer or just OGVPlayer.
	// But ogv.OGVPlayer will make the lint tools happier.
	const OGVCompat = ogv.OGVCompat;
	const OGVLoader = ogv.OGVLoader;
	const OGVPlayer = ogv.OGVPlayer;
	const Tech = videojs.getComponent('Tech');

	const androidOS = 'Android';
	const iPhoneOS = 'iPhoneOS';
	const iPadOS = 'iPadOS';
	const otherOS = 'Other';

	/**
	 * Object.defineProperty but "lazy", which means that the value is only set after
	 * it retrieved the first time, rather than being set right away.
	 *
	 * @param {Object} obj the object to set the property on.
	 * @param {string} key the key for the property to set.
	 * @param {Function} getValue the function used to get the value when it is needed.
	 * @param {boolean} setter whether a setter should be allowed or not.
	 */
	const defineLazyProperty = (obj, key, getValue, setter = true) => {
		const set = (value) => {
			Object.defineProperty(obj, key, {value, enumerable: true, writable: true});
		};

		const options = {
			configurable: true,
			enumerable: true,
			get() {
				const value = getValue();

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
	const getDeviceOS = () => {
		/* global navigator */
		const ua = navigator.userAgent;

		if (/android/i.test(ua)) {
			return androidOS;
		} else if (/iPad|iPhone|iPod/.test(ua)) {
			return iPhoneOS;
		} else if ((navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)) {
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
	class OgvJS extends Tech {

		/**
		 * Create an instance of this Tech.
		 *
		 * @param {Object} [options] The key/value store of player options.
		 * @param {Component~ReadyCallback} ready Callback function to call when the `OgvJS` Tech is ready.
		 */
		constructor(options, ready) {
			super(options, ready);

			this.el_.src = options.source.src;
			OgvJS.setIfAvailable(this.el_, 'autoplay', options.autoplay);
			OgvJS.setIfAvailable(this.el_, 'loop', options.loop);
			OgvJS.setIfAvailable(this.el_, 'poster', options.poster);
			OgvJS.setIfAvailable(this.el_, 'preload', options.preload);

			this.on('loadedmetadata', () => {
				if (getDeviceOS() === iPhoneOS) {
					// iPhoneOS add some inline styles to the canvas, we need to remove it.
					const canvas = this.el_.getElementsByTagName('canvas')[0];

					canvas.style.removeProperty('width');
					canvas.style.removeProperty('margin');
				}
			});

			this.triggerReady();
		}

		/**
		 * Create the 'OgvJS' Tech's DOM element.
		 *
		 * @return {Element} The element that gets created.
		 */
		createEl() {
			const options = this.options_;

			if (options.base) {
				OGVLoader.base = options.base;
			} else {
				throw new Error('Please specify the base for the ogv.js library');
			}

			const el = new OGVPlayer(options);

			el.className += ' vjs-tech';
			options.tag = el;

			return el;
		}

		/**
		 * Start playback
		 *
		 * @method play
		 */
		play() {
			this.el_.play();
		}

		/**
		 * Get the current playback speed.
		 *
		 * @return {number}
		 * @method playbackRate
		 */
		playbackRate() {
			return this.el_.playbackRate || 1;
		}

		/**
		 * Set the playback speed.
		 *
		 * @param {number} val Speed for the player to play.
		 * @method setPlaybackRate
		 */
		setPlaybackRate(val) {
			if (this.el_.hasOwnProperty('playbackRate')) {
				this.el_.playbackRate = val;
			}
		}

		/**
		 * Returns a TimeRanges object that represents the ranges of the media resource that the user agent has played.
		 *
		 * @return {TimeRangeObject} the range of points on the media timeline that has been reached through normal playback
		 */
		played() {
			return this.el_.played;
		}

		/**
		 * Pause playback
		 *
		 * @method pause
		 */
		pause() {
			this.el_.pause();
		}

		/**
		 * Is the player paused or not.
		 *
		 * @return {boolean}
		 * @method paused
		 */
		paused() {
			return this.el_.paused;
		}

		/**
		 * Get current playing time.
		 *
		 * @return {number}
		 * @method currentTime
		 */
		currentTime() {
			return this.el_.currentTime;
		}

		/**
		 * Set current playing time.
		 *
		 * @param {number} seconds Current time of audio/video.
		 * @method setCurrentTime
		 */
		setCurrentTime(seconds) {
			try {
				this.el_.currentTime = seconds;
			} catch (e) {
				videojs.log(e, 'Media is not ready. (Video.JS)');
			}
		}

		/**
		 * Get media's duration.
		 *
		 * @return {number}
		 * @method duration
		 */
		duration() {
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
		buffered() {
			return this.el_.buffered;
		}

		/**
		 * Get current volume level.
		 *
		 * @return {number}
		 * @method volume
		 */
		volume() {
			return this.el_.hasOwnProperty('volume') ? this.el_.volume : 1;
		}

		/**
		 * Set current playing volume level.
		 *
		 * @param {number} percentAsDecimal Volume percent as a decimal.
		 * @method setVolume
		 */
		setVolume(percentAsDecimal) {
			// Apple does not allow iOS and iPadOS devices to set the volume on UI.
			if (getDeviceOS() !== iPhoneOS && getDeviceOS() !== iPadOS && this.el_.hasOwnProperty('volume')) {
				this.el_.volume = percentAsDecimal;
			}
		}

		/**
		 * Is the player muted or not.
		 *
		 * @return {boolean}
		 * @method muted
		 */
		muted() {
			return this.el_.muted;
		}

		/**
		 * Mute the player.
		 *
		 * @param {boolean} muted True to mute the player.
		 */
		setMuted(muted) {
			this.el_.muted = !!muted;
		}

		/**
		 * Is the player muted by default or not.
		 *
		 * @return {boolean}
		 * @method defaultMuted
		 */
		defaultMuted() {
			return this.el_.defaultMuted || false;
		}

		/**
		 * Get the player width.
		 *
		 * @return {number}
		 * @method width
		 */
		width() {
			return this.el_.offsetWidth;
		}

		/**
		 * Get the player height.
		 *
		 * @return {number}
		 * @method height
		 */
		height() {
			return this.el_.offsetHeight;
		}

		/**
		 * Get the video width.
		 *
		 * @return {number}
		 * @method videoWidth
		 */
		videoWidth() {
			return this.el_.videoWidth;
		}

		/**
		 * Get the video height.
		 *
		 * @return {number}
		 * @method videoHeight
		 */
		videoHeight() {
			return this.el_.videoHeight;
		}

		/**
		 * Get/set media source.
		 *
		 * @param {Object=} src Source object
		 * @return {Object}
		 * @method src
		 */
		src(src) {
			if (typeof src === 'undefined') {
				return this.el_.src;
			}
			this.el_.src = src;
		}

		/**
		 * Load the media into the player.
		 *
		 * @method load
		 */
		load() {
			this.el_.load();
		}

		/**
		 * Get current media source.
		 *
		 * @return {Object}
		 * @method currentSrc
		 */
		currentSrc() {
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
		poster() {
			return this.el_.poster;
		}

		/**
		 * Set media poster URL.
		 *
		 * @param {string} url the poster image's url.
		 * @method
		 */
		setPoster(url) {
			this.el_.poster = url;
		}

		/**
		 * Is the media preloaded or not.
		 *
		 * @return {string}
		 * @method preload
		 */
		preload() {
			return this.el_.preload || 'none';
		}

		/**
		 * Set the media preload method.
		 *
		 * @param {string} val Value for preload attribute.
		 * @method setPreload
		 */
		setPreload(val) {
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
		autoplay() {
			return this.el_.autoplay || false;
		}

		/**
		 * Set media autoplay method.
		 *
		 * @param {boolean} val Value for autoplay attribute.
		 * @method setAutoplay
		 */
		setAutoplay(val) {
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
		controls() {
			return this.el_.controls || false;
		}

		/**
		 * Set the media controls method.
		 *
		 * @param {boolean} val Value for controls attribute.
		 * @method setControls
		 */
		setControls(val) {
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
		loop() {
			return this.el_.loop || false;
		}

		/**
		 * Set the media loop method.
		 *
		 * @param {boolean} val Value for loop attribute.
		 * @method setLoop
		 */
		setLoop(val) {
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
		seekable() {
			return this.el_.seekable;
		}

		/**
		 * Is player in the "seeking" state or not.
		 *
		 * @return {boolean}
		 * @method seeking
		 */
		seeking() {
			return this.el_.seeking;
		}

		/**
		 * Is the media ended or not.
		 *
		 * @return {boolean}
		 * @method ended
		 */
		ended() {
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
		networkState() {
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
		readyState() {
			return this.el_.readyState;
		}

		/**
		 * Does the player support native fullscreen mode or not. (Mobile devices)
		 *
		 * @return {boolean}
		 */
		supportsFullScreen() {
			// iOS devices have some problem with HTML5 fullscreen api so we need to fallback to fullWindow mode.
			return false;
		}

		/**
		 * Get media player error.
		 *
		 * @return {string}
		 * @method error
		 */
		error() {
			return this.el_.error;
		}

	}

	/**
	 * List of available events of the media player.
	 *
	 * @private
	 * @type {Array}
	 */
	OgvJS.Events = [
		'loadstart',
		'suspend',
		'abort',
		'error',
		'emptied',
		'stalled',
		'loadedmetadata',
		'loadeddata',
		'canplay',
		'canplaythrough',
		'playing',
		'waiting',
		'seeking',
		'seeked',
		'ended',
		'durationchange',
		'timeupdate',
		'progress',
		'play',
		'pause',
		'ratechange',
		'resize',
		'volumechange'
	];

	/**
	 * Set the value for the player is it has that property.
	 *
	 * @param {Element} el
	 * @param {string} name
	 * @param value
	 */
	OgvJS.setIfAvailable = (el, name, value) => {
		if (el.hasOwnProperty(name)) {
			el[name] = value;
		}
	};

	/**
	 * Check if browser/device is supported by Ogv.JS.
	 *
	 * @return {boolean}
	 */
	OgvJS.isSupported = () => {
		return OGVCompat.supported('OGVPlayer');
	};

	/**
	 * Check if the tech can support the given type.
	 *
	 * @param {string} type The mimetype to check
	 * @return {string} 'probably', 'maybe', or '' (empty string)
	 */
	OgvJS.canPlayType = (type) => {
		return (type.indexOf('/ogg') !== -1 || type.indexOf('/webm')) ? 'maybe' : '';
	};

	/**
	 * Check if the tech can support the given source
	 *
	 * @param srcObj The source object
	 * @return {string} The options passed to the tech
	 */
	OgvJS.canPlaySource = (srcObj) => {
		return OgvJS.canPlayType(srcObj.type);
	};

	/**
	 * Check if the volume can be changed in this browser/device.
	 * Volume cannot be changed in a lot of mobile devices.
	 * Specifically, it can't be changed on iOS and iPadOS.
	 *
	 * @return {boolean} True if volume can be controlled.
	 */
	OgvJS.canControlVolume = () => {
		if (getDeviceOS() === iPhoneOS || getDeviceOS() === iPadOS) {
			return false;
		}
		const p = new OGVPlayer();

		return p.hasOwnProperty('volume');
	};

	/**
	 * Check if the volume can be muted in this browser/device.
	 *
	 * @return {boolean} True if volume can be muted.
	 */
	OgvJS.canMuteVolume = () => {
		return true;
	};

	/**
	 * Check if the playback rate can be changed in this browser/device.
	 *
	 * @return {boolean} True if playback rate can be controlled.
	 */
	OgvJS.canControlPlaybackRate = () => {
		return true;
	};

	/**
	 * Check to see if native 'TextTracks' are supported by this browser/device.
	 *
	 * @return {boolean} True if native 'TextTracks' are supported.
	 */
	OgvJS.supportsNativeTextTracks = () => {
		return false;
	};

	/**
	 * Check if the fullscreen resize is supported by this browser/device.
	 *
	 * @return {boolean} True if the fullscreen resize is supported.
	 */
	OgvJS.supportsFullscreenResize = () => {
		return true;
	};

	/**
	 * Check if the progress events is supported by this browser/device.
	 *
	 * @return {boolean} True if the progress events is supported.
	 */
	OgvJS.supportsProgressEvents = () => {
		return true;
	};

	/**
	 * Check if the time update events is supported by this browser/device.
	 *
	 * @return {boolean} True if the time update events is supported.
	 */
	OgvJS.supportsTimeupdateEvents = () => {
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
	[
		['featuresVolumeControl', 'canControlVolume'],
		['featuresMuteControl', 'canMuteVolume'],
		['featuresPlaybackRate', 'canControlPlaybackRate'],
		['featuresNativeTextTracks', 'supportsNativeTextTracks'],
		['featuresFullscreenResize', 'supportsFullscreenResize'],
		['featuresProgressEvents', 'supportsProgressEvents'],
		['featuresTimeupdateEvents', 'supportsTimeupdateEvents']
	].forEach(([key, fn]) => {
		defineLazyProperty(OgvJS.prototype, key, () => OgvJS[fn](), true);
	});

	Tech.registerTech('OgvJS', OgvJS);

	return OgvJS;

}));
