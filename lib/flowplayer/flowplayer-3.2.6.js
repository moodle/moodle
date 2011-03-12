/**
 * flowplayer.js 3.2.6. The Flowplayer API
 *
 * Copyright 2009 Flowplayer Oy
 *
 * This file is part of Flowplayer.
 *
 * Flowplayer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Flowplayer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Flowplayer.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Date: 2010-08-25 12:48:46 +0000 (Wed, 25 Aug 2010)
 * Revision: 575
 */
(function() {

/*
	FEATURES
	--------
	- $f() and flowplayer() functions
	- handling multiple instances
	- Flowplayer programming API
	- Flowplayer event model
	- player loading / unloading
	- jQuery support
*/


/*jslint glovar: true, browser: true */
/*global flowplayer, $f */

// {{{ private utility methods

	function log(args) {
		console.log("$f.fireEvent", [].slice.call(args));
	}


	// thanks: http://keithdevens.com/weblog/archive/2007/Jun/07/javascript.clone
	function clone(obj) {
		if (!obj || typeof obj != 'object') { return obj; }
		var temp = new obj.constructor();
		for (var key in obj) {
			if (obj.hasOwnProperty(key)) {
				temp[key] = clone(obj[key]);
			}
		}
		return temp;
	}

	// stripped from jQuery, thanks John Resig
	function each(obj, fn) {
		if (!obj) { return; }

		var name, i = 0, length = obj.length;

		// object
		if (length === undefined) {
			for (name in obj) {
				if (fn.call(obj[name], name, obj[name]) === false) { break; }
			}

		// array
		} else {
			for (var value = obj[0];
				i < length && fn.call( value, i, value ) !== false; value = obj[++i]) {
			}
		}

		return obj;
	}


	// convenience
	function el(id) {
		return document.getElementById(id);
	}


	// used extensively. a very simple implementation.
	function extend(to, from, skipFuncs) {
		if (typeof from != 'object') { return to; }

		if (to && from) {
			each(from, function(name, value) {
				if (!skipFuncs || typeof value != 'function') {
					to[name] = value;
				}
			});
		}

		return to;
	}

	// var arr = select("elem.className");
	function select(query) {
		var index = query.indexOf(".");
		if (index != -1) {
			var tag = query.slice(0, index) || "*";
			var klass = query.slice(index + 1, query.length);
			var els = [];
			each(document.getElementsByTagName(tag), function() {
				if (this.className && this.className.indexOf(klass) != -1) {
					els.push(this);
				}
			});
			return els;
		}
	}

	// fix event inconsistencies across browsers
	function stopEvent(e) {
		e = e || window.event;

		if (e.preventDefault) {
			e.stopPropagation();
			e.preventDefault();

		} else {
			e.returnValue = false;
			e.cancelBubble = true;
		}
		return false;
	}

	// push an event listener into existing array of listeners
	function bind(to, evt, fn) {
		to[evt] = to[evt] || [];
		to[evt].push(fn);
	}


	// generates an unique id
   function makeId() {
      return "_" + ("" + Math.random()).slice(2, 10);
   }

//}}}


// {{{ Clip

	var Clip = function(json, index, player) {

		// private variables
		var self = this,
			 cuepoints = {},
			 listeners = {};

		self.index = index;

		// instance variables
		if (typeof json == 'string') {
			json = {url:json};
		}

		extend(this, json, true);

		// event handling
		each(("Begin*,Start,Pause*,Resume*,Seek*,Stop*,Finish*,LastSecond,Update,BufferFull,BufferEmpty,BufferStop").split(","),
			function() {

			var evt = "on" + this;

			// before event
			if (evt.indexOf("*") != -1) {
				evt = evt.slice(0, evt.length -1);
				var before = "onBefore" + evt.slice(2);

				self[before] = function(fn) {
					bind(listeners, before, fn);
					return self;
				};
			}

			self[evt] = function(fn) {
				bind(listeners, evt, fn);
				return self;
			};


			// set common clip event listeners to player level
			if (index == -1) {
				if (self[before]) {
					player[before] = self[before];
				}
				if (self[evt])  {
					player[evt] = self[evt];
				}
			}

		});

		extend(this, {

			onCuepoint: function(points, fn) {

				// embedded cuepoints
				if (arguments.length == 1) {
					cuepoints.embedded = [null, points];
					return self;
				}

				if (typeof points == 'number') {
					points = [points];
				}

				var fnId = makeId();
				cuepoints[fnId] = [points, fn];

				if (player.isLoaded()) {
					player._api().fp_addCuepoints(points, index, fnId);
				}

				return self;
			},

			update: function(json) {
				extend(self, json);

				if (player.isLoaded()) {
					player._api().fp_updateClip(json, index);
				}
				var conf = player.getConfig();
				var clip = (index == -1) ? conf.clip : conf.playlist[index];
				extend(clip, json, true);
			},


			// internal event for performing clip tasks. should be made private someday
			_fireEvent: function(evt, arg1, arg2, target) {
				if (evt == 'onLoad') {
					each(cuepoints, function(key, val) {
						if (val[0]) {
							player._api().fp_addCuepoints(val[0], index, key);
						}
					});
					return false;
				}

				// target clip we are working against
				target = target || self;

				if (evt == 'onCuepoint') {
					var fn = cuepoints[arg1];
					if (fn) {
						return fn[1].call(player, target, arg2);
					}
				}

				// 1. clip properties, 2-3. metadata, 4. updates, 5. resumes from nested clip
				if (arg1 && "onBeforeBegin,onMetaData,onStart,onUpdate,onResume".indexOf(evt) != -1) {
					// update clip properties
					extend(target, arg1);

					if (arg1.metaData) {
						if (!target.duration) {
							target.duration = arg1.metaData.duration;
						} else {
							target.fullDuration = arg1.metaData.duration;
						}
					}
				}


				var ret = true;
				each(listeners[evt], function() {
					ret = this.call(player, target, arg1, arg2);
				});
				return ret;
			}

		});


		// get cuepoints from config
		if (json.onCuepoint) {
			var arg = json.onCuepoint;
			self.onCuepoint.apply(self, typeof arg == 'function' ? [arg] : arg);
			delete json.onCuepoint;
		}

		// get other events
		each(json, function(key, val) {

			if (typeof val == 'function') {
				bind(listeners, key, val);
				delete json[key];
			}

		});


		// setup common clip event callbacks for Player object too (shortcuts)
		if (index == -1) {
			player.onCuepoint = this.onCuepoint;
		}

	};

//}}}


// {{{ Plugin

	var Plugin = function(name, json, player, fn) {

		var self = this,
			 listeners = {},
			 hasMethods = false;

		if (fn) {
			extend(listeners, fn);
		}

		// custom callback functions in configuration
		each(json, function(key, val) {
			if (typeof val == 'function') {
				listeners[key] = val;
				delete json[key];
			}
		});

		// core plugin methods
		extend(this, {

			// speed and fn are optional
			animate: function(props, speed, fn) {
				if (!props) {
					return self;
				}

				if (typeof speed == 'function') {
					fn = speed;
					speed = 500;
				}

				if (typeof props == 'string') {
					var key = props;
					props = {};
					props[key] = speed;
					speed = 500;
				}

				if (fn) {
					var fnId = makeId();
					listeners[fnId] = fn;
				}

				if (speed === undefined) { speed = 500; }
				json = player._api().fp_animate(name, props, speed, fnId);
				return self;
			},

			css: function(props, val) {
				if (val !== undefined) {
					var css = {};
					css[props] = val;
					props = css;
				}
				json = player._api().fp_css(name, props);
				extend(self, json);
				return self;
			},

			show: function() {
				this.display = 'block';
				player._api().fp_showPlugin(name);
				return self;
			},

			hide: function() {
				this.display = 'none';
				player._api().fp_hidePlugin(name);
				return self;
			},

			// toggle between visible / hidden state
			toggle: function() {
				this.display = player._api().fp_togglePlugin(name);
				return self;
			},

			fadeTo: function(o, speed, fn) {

				if (typeof speed == 'function') {
					fn = speed;
					speed = 500;
				}

				if (fn) {
					var fnId = makeId();
					listeners[fnId] = fn;
				}
				this.display = player._api().fp_fadeTo(name, o, speed, fnId);
				this.opacity = o;
				return self;
			},

			fadeIn: function(speed, fn) {
				return self.fadeTo(1, speed, fn);
			},

			fadeOut: function(speed, fn) {
				return self.fadeTo(0, speed, fn);
			},

			getName: function() {
				return name;
			},

			getPlayer: function() {
				return player;
			},

			// internal method. should be made private some day
         _fireEvent: function(evt, arg, arg2) {

            // update plugins properties & methods
            if (evt == 'onUpdate') {
               var json = player._api().fp_getPlugin(name);
					if (!json) { return;	}

               extend(self, json);
               delete self.methods;

               if (!hasMethods) {
                  each(json.methods, function() {
                     var method = "" + this;

                     self[method] = function() {
                        var a = [].slice.call(arguments);
                        var ret = player._api().fp_invoke(name, method, a);
                        return ret === 'undefined' || ret === undefined ? self : ret;
                     };
                  });
                  hasMethods = true;
               }
            }

            // plugin callbacks
            var fn = listeners[evt];

			if (fn) {
				var ret = fn.apply(self, arg);

				// "one-shot" callback
				if (evt.slice(0, 1) == "_") {
					delete listeners[evt];
				}

				return ret;
            }

            return self;
         }

		});

	};


//}}}


function Player(wrapper, params, conf) {

	// private variables (+ arguments)
	var self = this,
		api = null,
		isUnloading = false,
		html,
		commonClip,
		playlist = [],
		plugins = {},
		listeners = {},
		playerId,
		apiId,

		// n'th player on the page
		playerIndex,

		// active clip's index number
		activeIndex,

		swfHeight,
		wrapperHeight;


// {{{ public methods

	extend(self, {

		id: function() {
			return playerId;
		},

		isLoaded: function() {
			return (api !== null && api.fp_play !== undefined && !isUnloading);
		},

		getParent: function() {
			return wrapper;
		},

		hide: function(all) {
			if (all) { wrapper.style.height = "0px"; }
			if (self.isLoaded()) { api.style.height = "0px"; }
			return self;
		},

		show: function() {
			wrapper.style.height = wrapperHeight + "px";
			if (self.isLoaded()) { api.style.height = swfHeight + "px"; }
			return self;
		},

		isHidden: function() {
			return self.isLoaded() && parseInt(api.style.height, 10) === 0;
		},

		load: function(fn) {
			if (!self.isLoaded() && self._fireEvent("onBeforeLoad") !== false) {
				var onPlayersUnloaded = function() {
					html = wrapper.innerHTML;

					// do not use splash as alternate content for flashembed
					if (html && !flashembed.isSupported(params.version)) {
						wrapper.innerHTML = "";
					}

					// onLoad listener given as argument
					if (fn) {
						fn.cached = true;
						bind(listeners, "onLoad", fn);
					}

					// install Flash object inside given container
					flashembed(wrapper, params, {config: conf});
				};

				// unload all instances
				var unloadedPlayersNb = 0;
				each(players, function()  {
					this.unload(function(wasUnloaded) {
						if ( ++unloadedPlayersNb == players.length ) {
							onPlayersUnloaded();
						}
					});
				});
			}

			return self;
		},

		unload: function(fn) {


			// if we are fullscreen on safari, we can't unload as it would crash the PluginHost, sorry
			if (this.isFullscreen() && /WebKit/i.test(navigator.userAgent)) {
				if ( fn ) { fn(false); }
				return self;
			}


			// unload only if in splash state
			if (html.replace(/\s/g,'') !== '') {

				if (self._fireEvent("onBeforeUnload") === false) {
					if ( fn ) { fn(false); }
					return self;
				}

				isUnloading = true;
				// try closing
				try {
					if (api) {
						api.fp_close();

						// fire unload only when API is present
						self._fireEvent("onUnload");
					}
				} catch (error) {}

				var clean = function() {
					api = null;
					wrapper.innerHTML = html;
					isUnloading = false;

					if ( fn ) { fn(true); }
				};

				setTimeout(clean, 50);
			}
			else if ( fn ) { fn(false); }

			return self;

		},

		getClip: function(index) {
			if (index === undefined) {
				index = activeIndex;
			}
			return playlist[index];
		},


		getCommonClip: function() {
			return commonClip;
		},

		getPlaylist: function() {
			return playlist;
		},

      getPlugin: function(name) {
         var plugin = plugins[name];

			// create plugin if nessessary
         if (!plugin && self.isLoaded()) {
				var json = self._api().fp_getPlugin(name);
				if (json) {
					plugin = new Plugin(name, json, self);
					plugins[name] = plugin;
				}
         }
         return plugin;
      },

		getScreen: function() {
			return self.getPlugin("screen");
		},

		getControls: function() {
			return self.getPlugin("controls")._fireEvent("onUpdate");
		},

		// 3.2
		getLogo: function() {
			try {
				return self.getPlugin("logo")._fireEvent("onUpdate");
			} catch (ignored) {}
		},

		// 3.2
		getPlay: function() {
			return self.getPlugin("play")._fireEvent("onUpdate");
		},


		getConfig: function(copy) {
			return copy ? clone(conf) : conf;
		},

		getFlashParams: function() {
			return params;
		},

		loadPlugin: function(name, url, props, fn) {

			// properties not supplied
			if (typeof props == 'function') {
				fn = props;
				props = {};
			}

			// if fn not given, make a fake id so that plugin's onUpdate get's fired
			var fnId = fn ? makeId() : "_";
			self._api().fp_loadPlugin(name, url, props, fnId);

			// create new plugin
			var arg = {};
			arg[fnId] = fn;
			var p = new Plugin(name, null, self, arg);
			plugins[name] = p;
			return p;
		},


		getState: function() {
			return self.isLoaded() ? api.fp_getState() : -1;
		},

		// "lazy" play
		play: function(clip, instream) {

			var p = function() {
				if (clip !== undefined) {
					self._api().fp_play(clip, instream);
				} else {
					self._api().fp_play();
				}
			};

			if (self.isLoaded()) {
				p();
			} else if ( isUnloading ) {
				setTimeout(function() {
					self.play(clip, instream);
				}, 50);

			} else {
				self.load(function() {
					p();
				});
			}

			return self;
		},

		getVersion: function() {
			var js = "flowplayer.js 3.2.6";
			if (self.isLoaded()) {
				var ver = api.fp_getVersion();
				ver.push(js);
				return ver;
			}
			return js;
		},

		_api: function() {
			if (!self.isLoaded()) {
				throw "Flowplayer " +self.id()+ " not loaded when calling an API method";
			}
			return api;
		},

		setClip: function(clip) {
			self.setPlaylist([clip]);
			return self;
		},

		getIndex: function() {
			return playerIndex;
		},

		_swfHeight: function() {
			return api.clientHeight;
		}

	});


	// event handlers
	each(("Click*,Load*,Unload*,Keypress*,Volume*,Mute*,Unmute*,PlaylistReplace,ClipAdd,Fullscreen*,FullscreenExit,Error,MouseOver,MouseOut").split(","),
		function() {
			var name = "on" + this;

			// before event
			if (name.indexOf("*") != -1) {
				name = name.slice(0, name.length -1);
				var name2 = "onBefore" + name.slice(2);
				self[name2] = function(fn) {
					bind(listeners, name2, fn);
					return self;
				};
			}

			// normal event
			self[name] = function(fn) {
				bind(listeners, name, fn);
				return self;
			};
		}
	);


	// core API methods
	each(("pause,resume,mute,unmute,stop,toggle,seek,getStatus,getVolume,setVolume,getTime,isPaused,isPlaying,startBuffering,stopBuffering,isFullscreen,toggleFullscreen,reset,close,setPlaylist,addClip,playFeed,setKeyboardShortcutsEnabled,isKeyboardShortcutsEnabled").split(","),
		function() {
			var name = this;

			self[name] = function(a1, a2) {
				if (!self.isLoaded()) { return self; }
				var ret = null;

				// two arguments
				if (a1 !== undefined && a2 !== undefined) {
					ret = api["fp_" + name](a1, a2);

				} else {
					ret = (a1 === undefined) ? api["fp_" + name]() : api["fp_" + name](a1);

				}

				return ret === 'undefined' || ret === undefined ? self : ret;
			};
		}
	);

//}}}


// {{{ public method: _fireEvent

	self._fireEvent = function(a) {

		if (typeof a == 'string') { a = [a]; }

		var evt = a[0], arg0 = a[1], arg1 = a[2], arg2 = a[3], i = 0;
		if (conf.debug) { log(a); }

		// internal onLoad
		if (!self.isLoaded() && evt == 'onLoad' && arg0 == 'player') {

			api = api || el(apiId);
			swfHeight = self._swfHeight();

			each(playlist, function() {
				this._fireEvent("onLoad");
			});

			each(plugins, function(name, p) {
				p._fireEvent("onUpdate");
			});

			commonClip._fireEvent("onLoad");
		}

		// other onLoad events are skipped
		if (evt == 'onLoad' && arg0 != 'player') { return; }


		// "normalize" error handling
		if (evt == 'onError') {
			if (typeof arg0 == 'string' || (typeof arg0 == 'number' && typeof arg1 == 'number'))  {
				arg0 = arg1;
				arg1 = arg2;
			}
		}


      if (evt == 'onContextMenu') {
         each(conf.contextMenu[arg0], function(key, fn)  {
            fn.call(self);
         });
         return;
      }

		if (evt == 'onPluginEvent' || evt == 'onBeforePluginEvent') {
			var name = arg0.name || arg0;
			var p = plugins[name];

			if (p) {
				p._fireEvent("onUpdate", arg0);
				return p._fireEvent(arg1, a.slice(3));
			}
			return;
		}

		// replace whole playlist
		if (evt == 'onPlaylistReplace') {
			playlist = [];
			var index = 0;
			each(arg0, function() {
				playlist.push(new Clip(this, index++, self));
			});
		}

		// insert new clip to the playlist. arg0 = clip, arg1 = index
		if (evt == 'onClipAdd') {

			// instream clip additions are ignored at this point
			if (arg0.isInStream) { return; }

			// add new clip into playlist
			arg0 = new Clip(arg0, arg1, self);
			playlist.splice(arg1, 0, arg0);

			// increment index variable for the rest of the clips on playlist
			for (i = arg1 + 1; i < playlist.length; i++) {
				playlist[i].index++;
			}
		}


		var ret = true;

		// clip event
		if (typeof arg0 == 'number' && arg0 < playlist.length) {

			activeIndex = arg0;
			var clip = playlist[arg0];

			if (clip) {
				ret = clip._fireEvent(evt, arg1, arg2);
			}

			if (!clip || ret !== false) {
				// clip argument is given for common clip, because it behaves as the target
				ret = commonClip._fireEvent(evt, arg1, arg2, clip);
			}
		}


		// trigger player event
		each(listeners[evt], function() {
			ret = this.call(self, arg0, arg1);

			// remove cached entry
			if (this.cached) {
				listeners[evt].splice(i, 1);
			}

			// break loop
			if (ret === false) { return false;	 }
			i++;

		});

		return ret;
	};

//}}}


// {{{ init

   function init() {
        wrapper.innerHTML = ''; // Moodle hack - we do not want splashscreens, unfortunately there is not switch to disable them
		// replace previous installation
		if ($f(wrapper)) {
			$f(wrapper).getParent().innerHTML = "";
			playerIndex = $f(wrapper).getIndex();
			players[playerIndex] = self;

		// register this player into global array of instances
		} else {
			players.push(self);
			playerIndex = players.length -1;
		}

		wrapperHeight = parseInt(wrapper.style.height, 10) || wrapper.clientHeight;

		// playerId
		playerId = wrapper.id || "fp" + makeId();
		apiId = params.id || playerId + "_api";
		params.id = apiId;
		conf.playerId = playerId;


		// plain url is given as config
		if (typeof conf == 'string') {
			conf = {clip:{url:conf}};
		}

		if (typeof conf.clip == 'string') {
			conf.clip = {url: conf.clip};
		}

		// common clip is always there
		conf.clip = conf.clip || {};


		// wrapper href as common clip's url
		if (wrapper.getAttribute("href", 2) && !conf.clip.url) {
			conf.clip.url = wrapper.getAttribute("href", 2);
		}

		commonClip = new Clip(conf.clip, -1, self);

		// playlist
		conf.playlist = conf.playlist || [conf.clip];

		var index = 0;

		each(conf.playlist, function() {

			var clip = this;

			/* sometimes clip is given as array. this is not accepted. */
			if (typeof clip == 'object' && clip.length) {
				clip = {url: "" + clip};
			}

			// populate common clip properties to each clip
			each(conf.clip, function(key, val) {
				if (val !== undefined && clip[key] === undefined && typeof val != 'function') {
					clip[key] = val;
				}
			});

			// modify playlist in configuration
			conf.playlist[index] = clip;

			// populate playlist array
			clip = new Clip(clip, index, self);
			playlist.push(clip);
			index++;
		});

		// event listeners
		each(conf, function(key, val) {
			if (typeof val == 'function') {

				// common clip event
				if (commonClip[key]) {
					commonClip[key](val);

				// player event
				} else {
					bind(listeners, key, val);
				}

				// no need to supply for the Flash component
				delete conf[key];
			}
		});


		// plugins
		each(conf.plugins, function(name, val) {
			if (val) {
				plugins[name] = new Plugin(name, val, self);
			}
		});


		// setup controlbar plugin if not explicitly defined
		if (!conf.plugins || conf.plugins.controls === undefined) {
			plugins.controls = new Plugin("controls", null, self);
		}

		// setup canvas as plugin
		plugins.canvas = new Plugin("canvas", null, self);

		html = wrapper.innerHTML;

		// click function
		function doClick(e) {

			// ipad/iPhone --> follow the link if plugin not installed
			var hasiPadSupport = self.hasiPadSupport && self.hasiPadSupport();
			if (/iPad|iPhone|iPod/i.test(navigator.userAgent) && !/.flv$/i.test(playlist[0].url) && ! hasiPadSupport ) {
				return true;
			}

			if (!self.isLoaded() && self._fireEvent("onBeforeClick") !== false) {
				self.load();
			}
			return stopEvent(e);
		}

		function installPlayer() {
			// defer loading upon click
			if (html.replace(/\s/g, '') !== '') {

				if (wrapper.addEventListener) {
					wrapper.addEventListener("click", doClick, false);

				} else if (wrapper.attachEvent) {
					wrapper.attachEvent("onclick", doClick);
				}

			// player is loaded upon page load
			} else {

				// prevent default action from wrapper. (fixes safari problems)
				if (wrapper.addEventListener) {
					wrapper.addEventListener("click", stopEvent, false);
				}
				// load player
				self.load();
			}
		}

		// now that the player is initialized, wait for the plugin chain to finish
		// before actually changing the dom
		setTimeout(installPlayer, 0);
	}

	// possibly defer initialization until DOM get's loaded
	if (typeof wrapper == 'string') {
		var node = el(wrapper);
		if (!node) { throw "Flowplayer cannot access element: " + wrapper; }
		wrapper = node;
		init();

	// we have a DOM element so page is already loaded
	} else {
		init();
	}


//}}}


}


// {{{ flowplayer() & statics

// container for player instances
var players = [];


// this object is returned when multiple player's are requested
function Iterator(arr) {

	this.length = arr.length;

	this.each = function(fn)  {
		each(arr, fn);
	};

	this.size = function() {
		return arr.length;
	};
}

// these two variables are the only global variables
window.flowplayer = window.$f = function() {
	var instance = null;
	var arg = arguments[0];

    // Moodle hack - we do not want the missing flash hints - we need the original links for accessibility and incompatible browsers
    if (!flashembed.isSupported([6, 65])) {
        return null;
    }
    // Moodle hack end

	// $f()
	if (!arguments.length) {
		each(players, function() {
			if (this.isLoaded())  {
				instance = this;
				return false;
			}
		});

		return instance || players[0];
	}

	if (arguments.length == 1) {

		// $f(index);
		if (typeof arg == 'number') {
			return players[arg];


		// $f(wrapper || 'containerId' || '*');
		} else {

			// $f("*");
			if (arg == '*') {
				return new Iterator(players);
			}

			// $f(wrapper || 'containerId');
			each(players, function() {
				if (this.id() == arg.id || this.id() == arg || this.getParent() == arg)  {
					instance = this;
					return false;
				}
			});

			return instance;
		}
	}

	// instance builder
	if (arguments.length > 1) {

		// flashembed parameters
		var params = arguments[1],
			 conf = (arguments.length == 3) ? arguments[2] : {};


		if (typeof params == 'string') {
			params = {src: params};
		}

		params = extend({
			bgcolor: "#000000",
			version: [9, 0],
			expressInstall: "http://static.flowplayer.org/swf/expressinstall.swf",
			cachebusting: false

		}, params);

		if (typeof arg == 'string') {

			// select arg by classname
			if (arg.indexOf(".") != -1) {
				var instances = [];

				each(select(arg), function() {
					instances.push(new Player(this, clone(params), clone(conf)));
				});

				return new Iterator(instances);

			// select node by id
			} else {
				var node = el(arg);
				return new Player(node !== null ? node : arg, params, conf);
			}


		// arg is a DOM element
		} else if (arg) {
			return new Player(arg, params, conf);
		}

	}

	return null;
};

extend(window.$f, {

	// called by Flash External Interface
	fireEvent: function() {
		var a = [].slice.call(arguments);
		var p = $f(a[0]);
		return p ? p._fireEvent(a.slice(1)) : null;
	},


	// create plugins by modifying Player's prototype
	addPlugin: function(name, fn) {
		Player.prototype[name] = fn;
		return $f;
	},

	// utility methods for plugin developers
	each: each,

	extend: extend
});


//}}}


//{{{ jQuery support

if (typeof jQuery == 'function') {

	jQuery.fn.flowplayer = function(params, conf) {

		// select instances
		if (!arguments.length || typeof arguments[0] == 'number') {
			var arr = [];
			this.each(function()  {
				var p = $f(this);
				if (p) {
					arr.push(p);
				}
			});
			return arguments.length ? arr[arguments[0]] : new Iterator(arr);
		}

		// create flowplayer instances
		return this.each(function() {
			$f(this, clone(params), conf ? clone(conf) : {});
		});

	};

}

//}}}


})();
/**
 * @license
 * jQuery Tools 3.2.6 / Flashembed - New wave Flash embedding
 *
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 *
 * http://flowplayer.org/tools/toolbox/flashembed.html
 *
 * Since : March 2008
 * Date  : @DATE
 */
(function() {

	var IE = document.all,
		 URL = 'http://www.adobe.com/go/getflashplayer',
		 JQUERY = typeof jQuery == 'function',
		 RE = /(\d+)[^\d]+(\d+)[^\d]*(\d*)/,
		 GLOBAL_OPTS = {
			// very common opts
			width: '100%',
			height: '100%',
			id: "_" + ("" + Math.random()).slice(9),

			// flashembed defaults
			allowfullscreen: true,
			allowscriptaccess: 'always',
			quality: 'high',

			// flashembed specific options
			version: [3, 0],
			onFail: null,
			expressInstall: null,
			w3c: false,
			cachebusting: false
	};

	// version 9 bugfix: (http://blog.deconcept.com/2006/07/28/swfobject-143-released/)
	if (window.attachEvent) {
		window.attachEvent("onbeforeunload", function() {
			__flash_unloadHandler = function() {};
			__flash_savedUnloadHandler = function() {};
		});
	}

	// simple extend
	function extend(to, from) {
		if (from) {
			for (var key in from) {
				if (from.hasOwnProperty(key)) {
					to[key] = from[key];
				}
			}
		}
		return to;
	}

	// used by asString method
	function map(arr, func) {
		var newArr = [];
		for (var i in arr) {
			if (arr.hasOwnProperty(i)) {
				newArr[i] = func(arr[i]);
			}
		}
		return newArr;
	}

	window.flashembed = function(root, opts, conf) {

		// root must be found / loaded
		if (typeof root == 'string') {
			root = document.getElementById(root.replace("#", ""));
		}

		// not found
		if (!root) { return; }

		if (typeof opts == 'string') {
			opts = {src: opts};
		}

		return new Flash(root, extend(extend({}, GLOBAL_OPTS), opts), conf);
	};

	// flashembed "static" API
	var f = extend(window.flashembed, {

		conf: GLOBAL_OPTS,

		getVersion: function()  {
			var fo, ver;

			try {
				ver = navigator.plugins["Shockwave Flash"].description.slice(16);
			} catch(e) {

				try  {
					fo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
					ver = fo && fo.GetVariable("$version");

				} catch(err) {
                try  {
                    fo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
                    ver = fo && fo.GetVariable("$version");
                } catch(err2) { }
				}
			}

			ver = RE.exec(ver);
			return ver ? [ver[1], ver[3]] : [0, 0];
		},

		asString: function(obj) {

			if (obj === null || obj === undefined) { return null; }
			var type = typeof obj;
			if (type == 'object' && obj.push) { type = 'array'; }

			switch (type){

				case 'string':
					obj = obj.replace(new RegExp('(["\\\\])', 'g'), '\\$1');

					// flash does not handle %- characters well. transforms "50%" to "50pct" (a dirty hack, I admit)
					obj = obj.replace(/^\s?(\d+\.?\d+)%/, "$1pct");
					return '"' +obj+ '"';

				case 'array':
					return '['+ map(obj, function(el) {
						return f.asString(el);
					}).join(',') +']';

				case 'function':
					return '"function()"';

				case 'object':
					var str = [];
					for (var prop in obj) {
						if (obj.hasOwnProperty(prop)) {
							str.push('"'+prop+'":'+ f.asString(obj[prop]));
						}
					}
					return '{'+str.join(',')+'}';
			}

			// replace ' --> "  and remove spaces
			return String(obj).replace(/\s/g, " ").replace(/\'/g, "\"");
		},

		getHTML: function(opts, conf) {

			opts = extend({}, opts);

			/******* OBJECT tag and it's attributes *******/
			var html = '<object width="' + opts.width +
				'" height="' + opts.height +
				'" id="' + opts.id +
				'" name="' + opts.id + '"';

			if (opts.cachebusting) {
				opts.src += ((opts.src.indexOf("?") != -1 ? "&" : "?") + Math.random());
			}

			if (opts.w3c || !IE) {
				html += ' data="' +opts.src+ '" type="application/x-shockwave-flash"';
			} else {
				html += ' classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
			}

			html += '>';

			/******* nested PARAM tags *******/
			if (opts.w3c || IE) {
				html += '<param name="movie" value="' +opts.src+ '" />';
			}

			// not allowed params
			opts.width = opts.height = opts.id = opts.w3c = opts.src = null;
			opts.onFail = opts.version = opts.expressInstall = null;

			for (var key in opts) {
				if (opts[key]) {
					html += '<param name="'+ key +'" value="'+ opts[key] +'" />';
				}
			}

			/******* FLASHVARS *******/
			var vars = "";

			if (conf) {
				for (var k in conf) {
					if (conf[k]) {
						var val = conf[k];
						vars += k +'='+ (/function|object/.test(typeof val) ? f.asString(val) : val) + '&';
					}
				}
				vars = vars.slice(0, -1);
				html += '<param name="flashvars" value=\'' + vars + '\' />';
			}

			html += "</object>";

			return html;
		},

		isSupported: function(ver) {
			return VERSION[0] > ver[0] || VERSION[0] == ver[0] && VERSION[1] >= ver[1];
		}

	});

	var VERSION = f.getVersion();

	function Flash(root, opts, conf) {

		// version is ok
		if (f.isSupported(opts.version)) {
			root.innerHTML = f.getHTML(opts, conf);

		// express install
		} else if (opts.expressInstall && f.isSupported([6, 65])) {
			root.innerHTML = f.getHTML(extend(opts, {src: opts.expressInstall}), {
				MMredirectURL: location.href,
				MMplayerType: 'PlugIn',
				MMdoctitle: document.title
			});

		} else {

			// fail #2.1 custom content inside container
			if (!root.innerHTML.replace(/\s/g, '')) {
				root.innerHTML =
					"<h2>Flash version " + opts.version + " or greater is required</h2>" +
					"<h3>" +
						(VERSION[0] > 0 ? "Your version is " + VERSION : "You have no flash plugin installed") +
					"</h3>" +

					(root.tagName == 'A' ? "<p>Click here to download latest version</p>" :
						"<p>Download latest version from <a href='" + URL + "'>here</a></p>");

				if (root.tagName == 'A') {
					root.onclick = function() {
						location.href = URL;
					};
				}
			}

			// onFail
			if (opts.onFail) {
				var ret = opts.onFail.call(this);
				if (typeof ret == 'string') { root.innerHTML = ret; }
			}
		}

		// http://flowplayer.org/forum/8/18186#post-18593
		if (IE) {
			window[opts.id] = document.getElementById(opts.id);
		}

		// API methods for callback
		extend(this, {

			getRoot: function() {
				return root;
			},

			getOptions: function() {
				return opts;
			},


			getConf: function() {
				return conf;
			},

			getApi: function() {
				return root.firstChild;
			}

		});
	}

	// setup jquery support
	if (JQUERY) {

		// tools version number
		jQuery.tools = jQuery.tools || {version: '3.2.6'};

		jQuery.tools.flashembed = {
			conf: GLOBAL_OPTS
		};

		jQuery.fn.flashembed = function(opts, conf) {
			return this.each(function() {
				jQuery(this).data("flashembed", flashembed(this, opts, conf));
			});
		};
	}

})();

