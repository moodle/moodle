/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 2.8.1
*/
/**
 * The Storage module manages client-side data storage.
 * @module Storage
 */

(function() {

	// internal shorthand
var Y = YAHOO,
	YU = Y.util,
	YL = Y.lang,
	_logOverwriteError;

if (! YU.Storage) {
	_logOverwriteError = function(fxName) {
		Y.log('Exception in YAHOO.util.Storage.?? - must be extended by a storage engine'.replace('??', fxName).replace('??', this.getName ? this.getName() : 'Unknown'), 'error');
	};

	/**
	 * The Storage class is an HTML 5 storage API clone, used to wrap individual storage implementations with a common API.
	 * @class Storage
	 * @namespace YAHOO.util
	 * @constructor
	 * @param location {String} Required. The storage location.
	 * @parm name {String} Required. The engine name.
	 * @param conf {Object} Required. A configuration object.
	 */
	YU.Storage = function(location, name, conf) {
		var that = this;
		Y.env._id_counter += 1;

		// protected variables
		that._cfg = YL.isObject(conf) ? conf : {};
		that._location = location;
		that._name = name;
		that.isReady = false;

		// public variables
		that.createEvent(that.CE_READY, {scope: that});
		that.createEvent(that.CE_CHANGE, {scope: that});
		
		that.subscribe(that.CE_READY, function() {
			that.isReady = true;
		});
	};

	YU.Storage.prototype = {

		/**
		 * The event name for when the storage item is ready.
		 * @property CE_READY
		 * @type {String}
		 * @public
		 */
		CE_READY: 'YUIStorageReady',

		/**
		 * The event name for when the storage item has changed.
		 * @property CE_CHANGE
		 * @type {String}
		 * @public
		 */
		CE_CHANGE: 'YUIStorageChange',

		/**
		 * The delimiter uesed between the data type and the data.
		 * @property DELIMITER
		 * @type {String}
		 * @public
		 */
		DELIMITER: '__',

		/**
		 * The configuration of the engine.
		 * @property _cfg
		 * @type {Object}
		 * @protected
		 */
		_cfg: '',

		/**
		 * The name of this engine.
		 * @property _name
		 * @type {String}
		 * @protected
		 */
		_name: '',

		/**
		 * The location for this instance.
		 * @property _location
		 * @type {String}
		 * @protected
		 */
		_location: '',

		/**
		 * The current length of the keys.
		 * @property length
		 * @type {Number}
		 * @public
		 */
		length: 0,

		/**
		 * This engine singleton has been initialized already.
		 * @property isReady
		 * @type {String}
		 * @protected
		 */
		isReady: false,

		/**
		 * Clears any existing key/value pairs.
		 * @method clear
		 * @public
		 */
		clear: function() {
			this._clear();
			this.length = 0;
		},

		/**
		 * Fetches the data stored and the provided key.
		 * @method getItem
		 * @param key {String} Required. The key used to reference this value (DOMString in HTML 5 spec).
		 * @return {String|NULL} The value stored at the provided key (DOMString in HTML 5 spec).
		 * @public
		 */
		getItem: function(key) {
			Y.log("Fetching item at  " + key);
			var item = this._getItem(key);
			return YL.isValue(item) ? this._getValue(item) : null; // required by HTML 5 spec
		},

		/**
		 * Fetches the storage object's name; should be overwritten by storage engine.
		 * @method getName
		 * @return {String} The name of the data storage object.
		 * @public
		 */
		getName: function() {return this._name;},

		/**
		 * Tests if the key has been set (not in HTML 5 spec); should be overwritten by storage engine.
		 * @method hasKey
		 * @param key {String} Required. The key to search for.
		 * @return {Boolean} True when key has been set.
		 * @public
		 */
		hasKey: function(key) {
			return YL.isString(key) && this._hasKey(key);
		},

		/**
		 * Retrieve the key stored at the provided index; should be overwritten by storage engine.
		 * @method key
		 * @param index {Number} Required. The index to retrieve (unsigned long in HTML 5 spec).
		 * @return {String} Required. The key at the provided index (DOMString in HTML 5 spec).
		 * @public
		 */
		key: function(index) {
			Y.log("Fetching key at " + index);

			if (YL.isNumber(index) && -1 < index && this.length > index) {
				var value = this._key(index);
				if (value) {return value;}
			}

			// this is thrown according to the HTML5 spec
			throw('INDEX_SIZE_ERR - Storage.setItem - The provided index (' + index + ') is not available');
		},

		/**
		 * Remove an item from the data storage.
		 * @method setItem
		 * @param key {String} Required. The key to remove (DOMString in HTML 5 spec).
		 * @public
		 */
		removeItem: function(key) {
			Y.log("removing " + key);
			
			if (this.hasKey(key)) {
                var oldValue = this._getItem(key);
                if (! oldValue) {oldValue = null;}
                this._removeItem(key);
				this.fireEvent(this.CE_CHANGE, new YU.StorageEvent(this, key, oldValue, null, YU.StorageEvent.TYPE_REMOVE_ITEM));
			}
			else {
				// HTML 5 spec says to do nothing
			}
		},

		/**
		 * Adds an item to the data storage.
		 * @method setItem
		 * @param key {String} Required. The key used to reference this value (DOMString in HTML 5 spec).
		 * @param data {Object} Required. The data to store at key (DOMString in HTML 5 spec).
		 * @public
		 * @throws QUOTA_EXCEEDED_ERROR
		 */
		setItem: function(key, data) {
			Y.log("SETTING " + data + " to " + key);
			
			if (YL.isString(key)) {
				var eventType = this.hasKey(key) ? YU.StorageEvent.TYPE_UPDATE_ITEM : YU.StorageEvent.TYPE_ADD_ITEM,
					oldValue = this._getItem(key);
				if (! oldValue) {oldValue = null;}

				if (this._setItem(key, this._createValue(data))) {
					this.fireEvent(this.CE_CHANGE, new YU.StorageEvent(this, key, oldValue, data, eventType));
				}
				else {
					// this is thrown according to the HTML5 spec
					throw('QUOTA_EXCEEDED_ERROR - Storage.setItem - The choosen storage method (' +
						  this.getName() + ') has exceeded capacity');
				}
			}
			else {
				// HTML 5 spec says to do nothing
			}
		},

		/**
		 * Implementation of the clear login; should be overwritten by storage engine.
		 * @method _clear
		 * @protected
		 */
		_clear: function() {
			_logOverwriteError('_clear');
			return '';
		},

		/**
		 * Converts the object into a string, with meta data (type), so it can be restored later.
		 * @method _createValue
		 * @param s {Object} Required. An object to store.
		 * @protected
		 */
		_createValue: function(s) {
			var type = (YL.isNull(s) || YL.isUndefined(s)) ? ('' + s) : typeof s;
			return 'string' === type ? s : type + this.DELIMITER + s;
		},

		/**
		 * Implementation of the getItem login; should be overwritten by storage engine.
		 * @method _getItem
		 * @param key {String} Required. The key used to reference this value.
		 * @return {String|NULL} The value stored at the provided key.
		 * @protected
		 */
		_getItem: function(key) {
			_logOverwriteError('_getItem');
			return '';
		},

		/**
		 * Converts the stored value into its appropriate type.
		 * @method _getValue
		 * @param s {String} Required. The stored value.
		 * @protected
		 */
		_getValue: function(s) {
			var a = s ? s.split(this.DELIMITER) : [];
			if (1 == a.length) {return s;}

			switch (a[0]) {
				case 'boolean': return 'true' === a[1];
				case 'number': return parseFloat(a[1]);
				case 'null': return null;
				default: return a[1];
			}
		},

		/**
		 * Implementation of the key logic; should be overwritten by storage engine.
		 * @method _key
		 * @param index {Number} Required. The index to retrieve (unsigned long in HTML 5 spec).
		 * @return {String|NULL} Required. The key at the provided index (DOMString in HTML 5 spec).
		 * @protected
		 */
		_key: function(index) {
			_logOverwriteError('_key');
			return '';
		},

		/*
		 * Implementation to fetch evaluate the existence of a key.
		 * @see YAHOO.util.Storage._hasKey
		 */
		_hasKey: function(key) {
			return null !== this._getItem(key);
		},

		/**
		 * Implementation of the removeItem login; should be overwritten by storage engine.
		 * @method _removeItem
		 * @param key {String} Required. The key to remove.
		 * @protected
		 */
		_removeItem: function(key) {
			_logOverwriteError('_removeItem');
			return '';
		},

		/**
		 * Implementation of the setItem login; should be overwritten by storage engine.
		 * @method _setItem
		 * @param key {String} Required. The key used to reference this value.
		 * @param data {Object} Required. The data to storage at key.
		 * @return {Boolean} True when successful, false when size QUOTA exceeded.
		 * @protected
		 */
		_setItem: function(key, data) {
			_logOverwriteError('_setItem');
			return '';
		}
	};

	YL.augmentProto(YU.Storage, YU.EventProvider);
}

}());
/**
 * The StorageManager class is a singleton that registers DataStorage objects and returns instances of those objects.
 * @class StorageManager
 * @namespace YAHOO.util
 * @static
 */
(function() {
	// internal shorthand
var Y = YAHOO.util,
	YL = YAHOO.lang,

	// private variables
	_locationEngineMap = {}, // cached engines
	_registeredEngineSet = [], // set of available engines
	_registeredEngineMap = {}, // map of available engines
	
	/**
	 * Fetches a storage constructor if it is available, otherwise returns NULL.
	 * @method _getClass
	 * @param klass {Function} Required. The storage constructor to test.
	 * @return {Function} An available storage constructor or NULL.
	 * @private
	 */
	_getClass = function(klass) {
		return (klass && klass.isAvailable()) ? klass : null;
	},

	/**
	 * Fetches the storage engine from the cache, or creates and caches it.
	 * @method _getStorageEngine
	 * @param location {String} Required. The location to store.
	 * @param klass {Function} Required. A pointer to the engineType Class.
	 * @param conf {Object} Optional. Additional configuration for the data source engine.
	 * @private
	 */
	_getStorageEngine = function(location, klass, conf) {
		var engine = _locationEngineMap[location + klass.ENGINE_NAME];

		if (! engine) {
			engine = new klass(location, conf);
			_locationEngineMap[location + klass.ENGINE_NAME] = engine;
		}

		return engine;
	},

	/**
	 * Ensures that the location is valid before returning it or a default value.
	 * @method _getValidLocation
	 * @param location {String} Required. The location to evaluate.
	 * @private
	 */
	_getValidLocation = function(location) {
		switch (location) {
			case Y.StorageManager.LOCATION_LOCAL:
			case Y.StorageManager.LOCATION_SESSION:
				return location;

			default: return Y.StorageManager.LOCATION_SESSION;
		}
	};

	// public namespace
	Y.StorageManager = {

        /**
         * The storage location - session; data cleared at the end of a user's session.
         * @property LOCATION_SESSION
         * @type {String}
         * @static
         */
		LOCATION_SESSION: 'sessionStorage',

        /**
         * The storage location - local; data cleared on demand.
         * @property LOCATION_LOCAL
         * @type {String}
         * @static
         */
		LOCATION_LOCAL: 'localStorage',

		/**
		 * Fetches the desired engine type or first available engine type.
		 * @method get
		 * @param engineType {String} Optional. The engine type, see engines.
		 * @param location {String} Optional. The storage location - LOCATION_SESSION & LOCATION_LOCAL; default is LOCAL.
		 * @param conf {Object} Optional. Additional configuration for the getting the storage engine.
		 * {
		 * 	engine: {Object} configuration parameters for the desired engine
		 * 	order: {Array} an array of storage engine names; the desired order to try engines}
		 * }
		 * @static
		 */
		get: function(engineType, location, conf) {
			var _cfg = YL.isObject(conf) ? conf : {},
				klass = _getClass(_registeredEngineMap[engineType]);

			if (! klass && ! _cfg.force) {
				var i, j;

				if (_cfg.order) {
					j = _cfg.order.length;

					for (i = 0; i < j && ! klass; i += 1) {
						klass = _getClass(_cfg.order[i]);
					}
				}

				if (! klass) {
					j = _registeredEngineSet.length;

					for (i = 0; i < j && ! klass; i += 1) {
						klass = _getClass(_registeredEngineSet[i]);
					}
				}
			}

			if (klass) {
				return _getStorageEngine(_getValidLocation(location), klass, _cfg.engine);
			}

			throw('YAHOO.util.StorageManager.get - No engine available, please include an engine before calling this function.');
		},

        /*
         * Estimates the size of the string using 1 byte for each alpha-numeric character and 3 for each non-alpha-numeric character.
         * @method getByteSize
         * @param s {String} Required. The string to evaulate.
         * @return {Number} The estimated string size.
         * @private
         */
        getByteSize: function(s) {
			return encodeURIComponent('' + s).length;
        },

		/**
		 * Registers a engineType Class with the StorageManager singleton; first in is the first out.
		 * @method register
		 * @param engineConstructor {Function} Required. The engine constructor function, see engines.
		 * @return {Boolean} When successfully registered.
		 * @static
		 */
		register: function(engineConstructor) {
			if (YL.isFunction(engineConstructor) && YL.isFunction(engineConstructor.isAvailable) && YL.isString(engineConstructor.ENGINE_NAME)) {
				_registeredEngineMap[engineConstructor.ENGINE_NAME] = engineConstructor;
				_registeredEngineSet.push(engineConstructor);
				return true;
			}

			return false;
		}
	};

	YAHOO.register("StorageManager", Y.SWFStore, {version: "2.8.1", build: "19"});
}());
(function() {

/**
 * The StorageEvent class manages the storage events by emulating the HTML 5 implementation.
 * @namespace YAHOO.util
 * @class StorageEvent
 * @constructor
 * @param storageArea {Object} Required. The Storage object that was affected.
 * @param key {String} Required. The key being changed; DOMString in HTML 5 spec.
 * @param oldValue {String} Required. The old value of the key being changed; DOMString in HTML 5 spec.
 * @param newValue {String} Required. The new value of the key being changed; DOMString in HTML 5 spec.
 * @param type {String} Required. The storage event type.
 */
YAHOO.util.StorageEvent = function(storageArea, key, oldValue, newValue, type) {
	this.key = key;
	this.oldValue = oldValue;
	this.newValue = newValue;
	this.url = window.location.href;
	this.window = window; // todo: think about the CAJA and innocent code
	this.storageArea = storageArea;
	this.type = type;
};

YAHOO.lang.augmentObject(YAHOO.util.StorageEvent, {
	TYPE_ADD_ITEM: 'addItem',
	TYPE_REMOVE_ITEM: 'removeItem',
	TYPE_UPDATE_ITEM: 'updateItem'
});

YAHOO.util.StorageEvent.prototype = {

    /**
     * The 'key' attribute represents the key being changed.
     * @property key
     * @type {String}
     * @static
     * @readonly
     */
    key: null,

    /**
     * The 'newValue' attribute represents the new value of the key being changed.
     * @property newValue
     * @type {String}
     * @static
     * @readonly
     */
    newValue: null,

    /**
     * The 'oldValue' attribute represents the old value of the key being changed.
     * @property oldValue
     * @type {String}
     * @static
     * @readonly
     */
    oldValue: null,

    /**
     * The 'source' attribute represents the WindowProxy object of the browsing context of the document whose key changed.
     * @property source
     * @type {Object}
     * @static
     * @readonly
     */
    source: null,

    /**
     * The 'storageArea' attribute represents the Storage object that was affected.
     * @property storageArea
     * @type {Object}
     * @static
     * @readonly
     */
    storageArea: null,

    /**
     * The 'type' attribute represents the Storage event type.
     * @property type
     * @type {Object}
     * @static
     * @readonly
     */
    type: null,

    /**
     * The 'url' attribute represents the address of the document whose key changed.
     * @property url
     * @type {String}
     * @static
     * @readonly
     */
    url: null
};
	
}());
(function() {
var Y = YAHOO.util,
	YL = YAHOO.lang;

	/**
	 * The StorageEngineKeyed class implements the interface necessary for managing keys.
	 * @namespace YAHOO.util
	 * @class StorageEngineKeyed
	 * @constructor
	 * @extend YAHOO.util.Storage
	 */
	Y.StorageEngineKeyed = function() {
		Y.StorageEngineKeyed.superclass.constructor.apply(this, arguments);
		this._keys = [];
		this._keyMap = {};
	};

	YL.extend(Y.StorageEngineKeyed, Y.Storage, {

		/**
		 * A collection of keys applicable to the current location. This should never be edited by the developer.
		 * @property _keys
		 * @type {Array}
		 * @protected
		 */
		_keys: null,

		/**
		 * A map of keys to their applicable position in keys array. This should never be edited by the developer.
		 * @property _keyMap
		 * @type {Object}
		 * @protected
		 */
		_keyMap: null,

		/**
		 * Adds the key to the set.
		 * @method _addKey
		 * @param key {String} Required. The key to evaluate.
		 * @protected
		 */
		_addKey: function(key) {
			this._keyMap[key] = this.length;
			this._keys.push(key);
			this.length = this._keys.length;
		},

		/**
		 * Evaluates if a key exists in the keys array; indexOf does not work in all flavors of IE.
		 * @method _indexOfKey
		 * @param key {String} Required. The key to evaluate.
		 * @protected
		 */
		_indexOfKey: function(key) {
			var i = this._keyMap[key];
			return undefined === i ? -1 : i;
		},

		/**
		 * Removes a key from the keys array.
		 * @method _removeKey
		 * @param key {String} Required. The key to remove.
		 * @protected
		 */
		_removeKey: function(key) {
			var j = this._indexOfKey(key),
				rest = this._keys.slice(j + 1);

			delete this._keyMap[key];

			for (var k in this._keyMap) {
				if (j < this._keyMap[k]) {
					this._keyMap[k] -= 1;
				}
			}
			
			this._keys.length = j;
			this._keys = this._keys.concat(rest);
			this.length = this._keys.length;
		}
	});
}());
/*
 * HTML limitations:
 *  - 5MB in FF and Safari, 10MB in IE 8
 *  - only FF 3.5 recovers session storage after a browser crash
 *
 * Thoughts:
 *  - how can we not use cookies to handle session
 */
(function() {
	// internal shorthand
var Y = YAHOO.util,
	YL = YAHOO.lang,

	/*
	 * Required for IE 8 to make synchronous.
	 */
	_beginTransaction = function(engine) {
		if (engine.begin) {engine.begin();}
	},

	/*
	 * Required for IE 8 to make synchronous.
	 */
	_commitTransaction = function(engine) {
		if (engine.commit) {engine.commit();}
	};

	/**
	 * The StorageEngineHTML5 class implements the HTML5 storage engine.
	 * @namespace YAHOO.util
	 * @class StorageEngineHTML5
	 * @constructor
	 * @extend YAHOO.util.Storage
	 * @param location {String} Required. The storage location.
	 * @param conf {Object} Required. A configuration object.
	 */
	Y.StorageEngineHTML5 = function(location, conf) {
		var _this = this;
		Y.StorageEngineHTML5.superclass.constructor.call(_this, location, Y.StorageEngineHTML5.ENGINE_NAME, conf);// not set, are cookies available
		_this._engine = window[location];
		_this.length = _this._engine.length;
		YL.later(250, _this, function() { // temporary solution so that CE_READY can be subscribed to after this object is created
			_this.fireEvent(_this.CE_READY);
		});
	};

	YAHOO.lang.extend(Y.StorageEngineHTML5, Y.Storage, {

		_engine: null,

		/*
		 * Implementation to clear the values from the storage engine.
		 * @see YAHOO.util.Storage._clear
		 */
		_clear: function() {
			var _this = this;
			if (_this._engine.clear) {
				_this._engine.clear();
			}
			// for FF 3, fixed in FF 3.5
			else {
				for (var i = _this.length, key; 0 <= i; i -= 1) {
					key = _this._key(i);
					_this._removeItem(key);
				}
			}
		},

		/*
		 * Implementation to fetch an item from the storage engine.
		 * @see YAHOO.util.Storage._getItem
		 */
		_getItem: function(key) {
			var o = this._engine.getItem(key);
			return YL.isObject(o) ? o.value : o; // for FF 3, fixed in FF 3.5
		},

		/*
		 * Implementation to fetch a key from the storage engine.
		 * @see YAHOO.util.Storage._key
		 */
		_key: function(index) {return this._engine.key(index);},

		/*
		 * Implementation to remove an item from the storage engine.
		 * @see YAHOO.util.Storage._removeItem
		 */
		_removeItem: function(key) {
			var _this = this;
			_beginTransaction(_this._engine);
			_this._engine.removeItem(key);
			_commitTransaction(_this._engine);
			_this.length = _this._engine.length;
		},

		/*
		 * Implementation to remove an item from the storage engine.
		 * @see YAHOO.util.Storage._setItem
		 */
		_setItem: function(key, value) {
			var _this = this;
			
			try {
				_beginTransaction(_this._engine);
				_this._engine.setItem(key, value);
				_commitTransaction(_this._engine);
				_this.length = _this._engine.length;
				return true;
			}
			catch (e) {
				return false;
			}
		}
	}, true);

	Y.StorageEngineHTML5.ENGINE_NAME = 'html5';
	Y.StorageEngineHTML5.isAvailable = function() {
		return window.localStorage;
	};
    Y.StorageManager.register(Y.StorageEngineHTML5);
}());
/*
 * Gears limitation:
 *  - SQLite limitations - http://www.sqlite.org/limits.html
 *  - DB Best Practices - http://code.google.com/apis/gears/gears_faq.html#bestPracticeDB
 * 	- the user must approve before gears can be used
 *  - each SQL query has a limited number of characters (9948 bytes), data will need to be spread across rows
 *  - no query should insert or update more than 9948 bytes of data in a single statement or GEARs will throw:
 *  	[Exception... "'Error: SQL statement is too long.' when calling method: [nsIDOMEventListener::handleEvent]" nsresult: "0x8057001c (NS_ERROR_XPC_JS_THREW_JS_OBJECT)" location: "<unknown>" data: no]
 *
 * Thoughts:
 *  - we may want to implement additional functions for the gears only implementation
 *  - how can we not use cookies to handle session location
 */
(function() {
	// internal shorthand
var Y = YAHOO.util,
	YL = YAHOO.lang,
	_SQL_STMT_LIMIT = 9948,
	_TABLE_NAME = 'YUIStorageEngine',

	// local variables
	_engine = null,

	eURI = encodeURIComponent,
	dURI = decodeURIComponent;

	/**
	 * The StorageEngineGears class implements the Google Gears storage engine.
	 * @namespace YAHOO.util
	 * @class StorageEngineGears
	 * @constructor
	 * @extend YAHOO.util.Storage
	 * @param location {String} Required. The storage location.
	 * @param conf {Object} Required. A configuration object.
	 */
	Y.StorageEngineGears = function(location, conf) {
		var _this = this;
		Y.StorageEngineGears.superclass.constructor.call(_this, location, Y.StorageEngineGears.ENGINE_NAME, conf);

		if (! _engine) {
			// create the database
			_engine = google.gears.factory.create(Y.StorageEngineGears.GEARS);
			_engine.open(window.location.host + '-' + Y.StorageEngineGears.DATABASE);
			_engine.execute('CREATE TABLE IF NOT EXISTS ' + _TABLE_NAME + ' (key TEXT, location TEXT, value TEXT)');
		}

		var isSessionStorage = Y.StorageManager.LOCATION_SESSION === _this._location,
			sessionKey = Y.Cookie.get('sessionKey' + Y.StorageEngineGears.ENGINE_NAME);

		if (! sessionKey) {
			_engine.execute('BEGIN');
			_engine.execute('DELETE FROM ' + _TABLE_NAME + ' WHERE location="' + eURI(Y.StorageManager.LOCATION_SESSION) + '"');
			_engine.execute('COMMIT');
		}

		var rs = _engine.execute('SELECT key FROM ' + _TABLE_NAME + ' WHERE location="' + eURI(_this._location) + '"'),
			keyMap = {};
	
		try {
			// iterate on the rows and map the keys
			while (rs.isValidRow()) {
				var fld = dURI(rs.field(0));

				if (! keyMap[fld]) {
					keyMap[fld] = true;
					_this._addKey(fld);
				}

				rs.next();
			}
		}
		finally {
			rs.close();
		}

		// this is session storage, ensure that the session key is set
		if (isSessionStorage) {
			Y.Cookie.set('sessionKey' + Y.StorageEngineGears.ENGINE_NAME, true);
		}

		_this.length = _this._keys.length;
		YL.later(250, _this, function() { // temporary solution so that CE_READY can be subscribed to after this object is created
			_this.fireEvent(_this.CE_READY);
		});
	};

	YL.extend(Y.StorageEngineGears, Y.StorageEngineKeyed, {

		/*
		 * Implementation to clear the values from the storage engine.
		 * @see YAHOO.util.Storage._clear
		 */
		_clear: function() {
			_engine.execute('BEGIN');
			_engine.execute('DELETE FROM ' + _TABLE_NAME + ' WHERE location="' + eURI(this._location) + '"');
			_engine.execute('COMMIT');
			this._keys = [];
			this.length = 0;
		},

		/*
		 * Implementation to fetch an item from the storage engine.
		 * @see YAHOO.util.Storage._getItem
		 */
		_getItem: function(key) {
			var rs = _engine.execute('SELECT value FROM ' + _TABLE_NAME + ' WHERE key="' + eURI(key) + '" AND location="' + eURI(this._location) + '"'),
				value = '';

			try {
				while (rs.isValidRow()) {
					var temp = rs.field(0);
					value += rs.field(0);
					rs.next();
				}
			}
			finally {
				rs.close();
			}

			return value ? dURI(value) : null;
		},

		/*
		 * Implementation to fetch a key from the storage engine.
		 * @see YAHOO.util.Storage.key
		 */
		_key: function(index) {return this._keys[index];},

		/*
		 * Implementation to remove an item from the storage engine.
		 * @see YAHOO.util.Storage._removeItem
		 */
		_removeItem: function(key) {
			YAHOO.log("removing " + key);
			_engine.execute('BEGIN');
			_engine.execute('DELETE FROM ' + _TABLE_NAME + ' WHERE key="' + eURI(key) + '" AND location="' + eURI(this._location) + '"');
			_engine.execute('COMMIT');
			this._removeKey(key);
		},

		/*
		 * Implementation to remove an item from the storage engine.
		 * @see YAHOO.util.Storage._setItem
		 */
		_setItem: function(key, data) {
			YAHOO.log("SETTING " + data + " to " + key);

			if (! this.hasKey(key)) {
				this._addKey(key);
			}

			var _key = eURI(key),
				_location = eURI(this._location),
				_value = eURI(data),
				_values = [],
				_len = _SQL_STMT_LIMIT - (_key + _location).length;

			// the length of the value exceeds the available space
			if (_len < _value.length) {
				for (var i = 0, j = _value.length; i < j; i += _len) {
					_values.push(_value.substr(i, _len));
				}
			}
			else {
				_values.push(_value);
			}

			// Google recommends using INSERT instead of update, because it is faster
			_engine.execute('BEGIN');
			_engine.execute('DELETE FROM ' + _TABLE_NAME + ' WHERE key="' + eURI(key) + '" AND location="' + eURI(this._location) + '"');
			for (var m = 0, n = _values.length; m < n; m += 1) {
				_engine.execute('INSERT INTO ' + _TABLE_NAME + ' VALUES ("' + _key + '", "' + _location + '", "' + _values[m] + '")');
			}
			_engine.execute('COMMIT');
			
			return true;
		}
	});

	// releases the engine when the page unloads
	Y.Event.on('unload', function() {
		if (_engine) {_engine.close();}
	});
	Y.StorageEngineGears.ENGINE_NAME = 'gears';
	Y.StorageEngineGears.GEARS = 'beta.database';
	Y.StorageEngineGears.DATABASE = 'yui.database';
	Y.StorageEngineGears.isAvailable = function() {
		if (window.google && window.google.gears) {
			try {
				// this will throw an exception if the user denies gears
				google.gears.factory.create(Y.StorageEngineGears.GEARS);
				return true;
			}
			catch (e) {
				// no need to do anything
			}
		}

		return false;
	};
    Y.StorageManager.register(Y.StorageEngineGears);
}());
/*
 * SWF limitation:
 *  - only 100,000 bytes of data may be stored this way
 *  - data is publicly available on user machine
 *
 * Thoughts:
 *  - data can be shared across browsers
 *  - how can we not use cookies to handle session location
 */
(function() {
    // internal shorthand
var Y = YAHOO.util,
    YL = YAHOO.lang,
    YD = Y.Dom,
    
    /*
     * The minimum width required to be able to display the settings panel within the SWF.
     */ 
    MINIMUM_WIDTH = 215,

    /*
     * The minimum height required to be able to display the settings panel within the SWF.
     */ 
    MINIMUM_HEIGHT = 138,

    // local variables
    _engine = null,

    /*
     * Creates a location bound key.
     */
    _getKey = function(that, key) {
        return that._location + that.DELIMITER + key;
    },

    /*
     * Initializes the engine, if it isn't already initialized.
     */
    _initEngine = function(cfg) {
        if (! _engine) {
            if (! YL.isString(cfg.swfURL)) {cfg.swfURL = Y.StorageEngineSWF.SWFURL;}
            if (! cfg.containerID) {
                var bd = document.getElementsByTagName('body')[0],
                    container = bd.appendChild(document.createElement('div'));
                cfg.containerID = YD.generateId(container);
            }

            if (! cfg.attributes) {cfg.attributes  = {};}
            if (! cfg.attributes.flashVars) {cfg.attributes.flashVars = {};}
            cfg.attributes.flashVars.useCompression = 'true';
            cfg.attributes.version = 9.115;
            _engine = new YAHOO.widget.SWF(cfg.containerID, cfg.swfURL, cfg.attributes);
        }
    };

    /**
     * The StorageEngineSWF class implements the SWF storage engine.
     * @namespace YAHOO.util
     * @class StorageEngineSWF
     * @uses YAHOO.widget.SWF
     * @constructor
     * @extend YAHOO.util.Storage
     * @param location {String} Required. The storage location.
     * @param conf {Object} Required. A configuration object.
     */
    Y.StorageEngineSWF = function(location, conf) {
        var _this = this;
        Y.StorageEngineSWF.superclass.constructor.call(_this, location, Y.StorageEngineSWF.ENGINE_NAME, conf);
        
        _initEngine(_this._cfg);

        // evaluates when the SWF is loaded
		_engine.unsubscribe('contentReady'); // prevents local and session content ready callbacks from firing, when switching between context
        _engine.addListener("contentReady", function() {
            _this._swf = _engine._swf;
            _engine.initialized = true;
			
			var isSessionStorage = Y.StorageManager.LOCATION_SESSION === _this._location,
				sessionKey = Y.Cookie.get('sessionKey' + Y.StorageEngineSWF.ENGINE_NAME);

            for (var i = _engine.callSWF("getLength", []) - 1; 0 <= i; i -= 1) {
                var key = _engine.callSWF("getNameAt", [i]),
                    isKeySessionStorage = -1 < key.indexOf(Y.StorageManager.LOCATION_SESSION + _this.DELIMITER);

                // this is session storage, but the session key is not set, so remove item
                if (isSessionStorage && ! sessionKey) {
                    _engine.callSWF("removeItem", [key]);
                }
                // the key matches the storage type, add to key collection
                else if (isSessionStorage === isKeySessionStorage) {
                    _this._addKey(key);
                }
            }

            // this is session storage, ensure that the session key is set
            if (isSessionStorage) {
                Y.Cookie.set('sessionKey' + Y.StorageEngineSWF.ENGINE_NAME, true);
            }

            _this.length = _this._keys.length;
            _this.fireEvent(_this.CE_READY);
        });
        
        // required for pages with both a session and local storage
        if (_engine.initialized) {_engine.fireEvent('contentReady');}
    };

    YL.extend(Y.StorageEngineSWF, Y.StorageEngineKeyed, {
        /**
         * The underlying SWF of the engine, exposed so developers can modify the adapter behavior.
         * @property _swf
         * @type {Object}
         * @protected
         */
        _swf: null,

        /*
         * Implementation to clear the values from the storage engine.
         * @see YAHOO.util.Storage._clear
         */
        _clear: function() {
            for (var i = this._keys.length - 1; 0 <= i; i -= 1) {
                var key = this._keys[i];
                _engine.callSWF("removeItem", [key]);
            }

            this._keys = [];
            this.length = 0;
        },

        /*
         * Implementation to fetch an item from the storage engine.
         * @see YAHOO.util.Storage._getItem
         */
        _getItem: function(key) {
            var _key = _getKey(this, key);
            return _engine.callSWF("getValueOf", [_key]);
        },

        /*
         * Implementation to fetch a key from the storage engine.
         * @see YAHOO.util.Storage.key
         */
        _key: function(index) {
            return (this._keys[index] || '').replace(/^.*?__/, '');
        },

        /*
         * Implementation to remove an item from the storage engine.
         * @see YAHOO.util.Storage._removeItem
         */
        _removeItem: function(key) {
            var _key = _getKey(this, key);
            _engine.callSWF("removeItem", [_key]);
            this._removeKey(_key);
        },

        /*
         * Implementation to remove an item from the storage engine.
         * @see YAHOO.util.Storage._setItem
         */
        _setItem: function(key, data) {
            var _key = _getKey(this, key), swfNode;

            // setting the value returns false if the value didn't change,
            // so I changed this to clear the key if it exists so that the
            // fork below works.
            if (_engine.callSWF("getValueOf", [_key])) {
                this._removeItem(key);
            }

            this._addKey(_key);

            if (_engine.callSWF("setItem", [_key, data])) {
                return true;
            } else {

                // @TODO we should not assume that a false return means that
                // the quota has been exceeded.  this dialog should only be
                // displayed if the quotaExceededError event fired.
                swfNode = YD.get(_engine._id);
                if (MINIMUM_WIDTH > YD.getStyle(swfNode, 'width').replace(/\D+/g, '')) {
                    YD.setStyle(swfNode, 'width', MINIMUM_WIDTH + 'px');
                }
                if (MINIMUM_HEIGHT > YD.getStyle(swfNode, 'height').replace(/\D+/g, '')) {
                    YD.setStyle(swfNode, 'height', MINIMUM_HEIGHT + 'px');
                }
                return _engine.callSWF("displaySettings", []);
            }
        }
    });

    Y.StorageEngineSWF.SWFURL = "swfstore.swf";
    Y.StorageEngineSWF.ENGINE_NAME = 'swf';
    Y.StorageEngineSWF.isAvailable = function() {
        return (6 <= YAHOO.env.ua.flash && YAHOO.widget.SWF);
    };
    Y.StorageManager.register(Y.StorageEngineSWF);
}());
YAHOO.register("storage", YAHOO.util.Storage, {version: "2.8.1", build: "19"});
