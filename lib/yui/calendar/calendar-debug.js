/*
Copyright (c) 2006, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 0.12.2
*/
/**
* Config is a utility used within an Object to allow the implementer to maintain a list of local configuration properties and listen for changes to those properties dynamically using CustomEvent. The initial values are also maintained so that the configuration can be reset at any given point to its initial state.
* @namespace YAHOO.util
* @class Config
* @constructor
* @param {Object}	owner	The owner Object to which this Config Object belongs
*/
YAHOO.util.Config = function(owner) {
	if (owner) {
		this.init(owner);
	} else {
		YAHOO.log("No owner specified for Config object", "error");
	}
};

YAHOO.util.Config.prototype = {
	
	/**
	* Object reference to the owner of this Config Object
	* @property owner
	* @type Object
	*/
	owner : null,

	/**
	* Boolean flag that specifies whether a queue is currently being executed
	* @property queueInProgress
	* @type Boolean
	*/
	queueInProgress : false,


	/**
	* Validates that the value passed in is a Boolean.
	* @method checkBoolean
	* @param	{Object}	val	The value to validate
	* @return	{Boolean}	true, if the value is valid
	*/	
	checkBoolean: function(val) {
		if (typeof val == 'boolean') {
			return true;
		} else {
			return false;
		}
	},

	/**
	* Validates that the value passed in is a number.
	* @method checkNumber
	* @param	{Object}	val	The value to validate
	* @return	{Boolean}	true, if the value is valid
	*/
	checkNumber: function(val) {
		if (isNaN(val)) {
			return false;
		} else {
			return true;
		}
	}
};


/**
* Initializes the configuration Object and all of its local members.
* @method init
* @param {Object}	owner	The owner Object to which this Config Object belongs
*/
YAHOO.util.Config.prototype.init = function(owner) {

	this.owner = owner;

	/**
	* Object reference to the owner of this Config Object
	* @event configChangedEvent
	*/
	this.configChangedEvent = new YAHOO.util.CustomEvent("configChanged");
	this.queueInProgress = false;

	/* Private Members */

	/**
	* Maintains the local collection of configuration property objects and their specified values
	* @property config
	* @private
	* @type Object
	*/ 
	var config = {};

	/**
	* Maintains the local collection of configuration property objects as they were initially applied.
	* This object is used when resetting a property.
	* @property initialConfig
	* @private
	* @type Object
	*/ 
	var initialConfig = {};

	/**
	* Maintains the local, normalized CustomEvent queue
	* @property eventQueue
	* @private
	* @type Object
	*/ 
	var eventQueue = [];

	/**
	* Fires a configuration property event using the specified value. 
	* @method fireEvent
	* @private
	* @param {String}	key			The configuration property's name
	* @param {value}	Object		The value of the correct type for the property
	*/ 
	var fireEvent = function( key, value ) {
		YAHOO.log("Firing Config event: " + key + "=" + value, "info");
		
		key = key.toLowerCase();

		var property = config[key];

		if (typeof property != 'undefined' && property.event) {
			property.event.fire(value);
		}	
	};
	/* End Private Members */

	/**
	* Adds a property to the Config Object's private config hash.
	* @method addProperty
	* @param {String}	key	The configuration property's name
	* @param {Object}	propertyObject	The Object containing all of this property's arguments
	*/
	this.addProperty = function( key, propertyObject ) {
		key = key.toLowerCase();
		
		YAHOO.log("Added property: " + key, "info");

		config[key] = propertyObject;

		propertyObject.event = new YAHOO.util.CustomEvent(key);
		propertyObject.key = key;

		if (propertyObject.handler) {
			propertyObject.event.subscribe(propertyObject.handler, this.owner, true);
		}

		this.setProperty(key, propertyObject.value, true);
		
		if (! propertyObject.suppressEvent) {
			this.queueProperty(key, propertyObject.value);
		}
	};

	/**
	* Returns a key-value configuration map of the values currently set in the Config Object.
	* @method getConfig
	* @return {Object} The current config, represented in a key-value map
	*/
	this.getConfig = function() {
		var cfg = {};
			
		for (var prop in config) {
			var property = config[prop];
			if (typeof property != 'undefined' && property.event) {
				cfg[prop] = property.value;
			}
		}
		
		return cfg;
	};

	/**
	* Returns the value of specified property.
	* @method getProperty
	* @param {String} key	The name of the property
	* @return {Object}		The value of the specified property
	*/
	this.getProperty = function(key) {
		key = key.toLowerCase();

		var property = config[key];
		if (typeof property != 'undefined' && property.event) {
			return property.value;
		} else {
			return undefined;
		}
	};

	/**
	* Resets the specified property's value to its initial value.
	* @method resetProperty
	* @param {String} key	The name of the property
	* @return {Boolean} True is the property was reset, false if not
	*/
	this.resetProperty = function(key) {
		key = key.toLowerCase();

		var property = config[key];
		if (typeof property != 'undefined' && property.event) {
			if (initialConfig[key] && initialConfig[key] != 'undefined')	{
				this.setProperty(key, initialConfig[key]);
			}
			return true;
		} else {
			return false;
		}
	};

	/**
	* Sets the value of a property. If the silent property is passed as true, the property's event will not be fired.
	* @method setProperty
	* @param {String} key		The name of the property
	* @param {String} value		The value to set the property to
	* @param {Boolean} silent	Whether the value should be set silently, without firing the property event.
	* @return {Boolean}			True, if the set was successful, false if it failed.
	*/
	this.setProperty = function(key, value, silent) {
		key = key.toLowerCase();
		
		YAHOO.log("setProperty: " + key + "=" + value, "info");

		if (this.queueInProgress && ! silent) {
			this.queueProperty(key,value); // Currently running through a queue... 
			return true;
		} else {
			var property = config[key];
			if (typeof property != 'undefined' && property.event) {
				if (property.validator && ! property.validator(value)) { // validator
					return false;
				} else {
					property.value = value;
					if (! silent) {
						fireEvent(key, value);
						this.configChangedEvent.fire([key, value]);
					}
					return true;
				}
			} else {
				return false;
			}
		}
	};

	/**
	* Sets the value of a property and queues its event to execute. If the event is already scheduled to execute, it is
	* moved from its current position to the end of the queue.
	* @method queueProperty
	* @param {String} key	The name of the property
	* @param {String} value	The value to set the property to
	* @return {Boolean}		true, if the set was successful, false if it failed.
	*/	
	this.queueProperty = function(key, value) {
		key = key.toLowerCase();

		YAHOO.log("queueProperty: " + key + "=" + value, "info");

		var property = config[key];
							
		if (typeof property != 'undefined' && property.event) {
			if (typeof value != 'undefined' && property.validator && ! property.validator(value)) { // validator
				return false;
			} else {

				if (typeof value != 'undefined') {
					property.value = value;
				} else {
					value = property.value;
				}

				var foundDuplicate = false;

				for (var i=0;i<eventQueue.length;i++) {
					var queueItem = eventQueue[i];

					if (queueItem) {
						var queueItemKey = queueItem[0];
						var queueItemValue = queueItem[1];
						
						if (queueItemKey.toLowerCase() == key) {
							// found a dupe... push to end of queue, null current item, and break
							eventQueue[i] = null;
							eventQueue.push([key, (typeof value != 'undefined' ? value : queueItemValue)]);
							foundDuplicate = true;
							break;
						}
					}
				}
				
				if (! foundDuplicate && typeof value != 'undefined') { // this is a refire, or a new property in the queue
					eventQueue.push([key, value]);
				}
			}

			if (property.supercedes) {
				for (var s=0;s<property.supercedes.length;s++) {
					var supercedesCheck = property.supercedes[s];

					for (var q=0;q<eventQueue.length;q++) {
						var queueItemCheck = eventQueue[q];

						if (queueItemCheck) {
							var queueItemCheckKey = queueItemCheck[0];
							var queueItemCheckValue = queueItemCheck[1];
							
							if ( queueItemCheckKey.toLowerCase() == supercedesCheck.toLowerCase() ) {
								eventQueue.push([queueItemCheckKey, queueItemCheckValue]);
								eventQueue[q] = null;
								break;
							}
						}
					}
				}
			}
	
			YAHOO.log("Config event queue: " + this.outputEventQueue(), "info");

			return true;
		} else {
			return false;
		}
	};

	/**
	* Fires the event for a property using the property's current value.
	* @method refireEvent
	* @param {String} key	The name of the property
	*/
	this.refireEvent = function(key) {
		key = key.toLowerCase();

		var property = config[key];
		if (typeof property != 'undefined' && property.event && typeof property.value != 'undefined') {
			if (this.queueInProgress) {
				this.queueProperty(key);
			} else {
				fireEvent(key, property.value);
			}
		}
	};

	/**
	* Applies a key-value Object literal to the configuration, replacing any existing values, and queueing the property events.
	* Although the values will be set, fireQueue() must be called for their associated events to execute.
	* @method applyConfig
	* @param {Object}	userConfig	The configuration Object literal
	* @param {Boolean}	init		When set to true, the initialConfig will be set to the userConfig passed in, so that calling a reset will reset the properties to the passed values.
	*/
	this.applyConfig = function(userConfig, init) {
		if (init) {
			initialConfig = userConfig;
		}
		for (var prop in userConfig) {
			this.queueProperty(prop, userConfig[prop]);
		}
	};

	/**
	* Refires the events for all configuration properties using their current values.
	* @method refresh
	*/
	this.refresh = function() {
		for (var prop in config) {
			this.refireEvent(prop);
		}
	};

	/**
	* Fires the normalized list of queued property change events
	* @method fireQueue
	*/
	this.fireQueue = function() {
		this.queueInProgress = true;
		for (var i=0;i<eventQueue.length;i++) {
			var queueItem = eventQueue[i];
			if (queueItem) {
				var key = queueItem[0];
				var value = queueItem[1];
				
				var property = config[key];
				property.value = value;

				fireEvent(key,value);
			}
		}
		
		this.queueInProgress = false;
		eventQueue = [];
	};

	/**
	* Subscribes an external handler to the change event for any given property. 
	* @method subscribeToConfigEvent
	* @param {String}	key			The property name
	* @param {Function}	handler		The handler function to use subscribe to the property's event
	* @param {Object}	obj			The Object to use for scoping the event handler (see CustomEvent documentation)
	* @param {Boolean}	override	Optional. If true, will override "this" within the handler to map to the scope Object passed into the method.
	* @return {Boolean}				True, if the subscription was successful, otherwise false.
	*/	
	this.subscribeToConfigEvent = function(key, handler, obj, override) {
		key = key.toLowerCase();

		var property = config[key];
		if (typeof property != 'undefined' && property.event) {
			if (! YAHOO.util.Config.alreadySubscribed(property.event, handler, obj)) {
				property.event.subscribe(handler, obj, override);
			}
			return true;
		} else {
			return false;
		}
	};

	/**
	* Unsubscribes an external handler from the change event for any given property. 
	* @method unsubscribeFromConfigEvent
	* @param {String}	key			The property name
	* @param {Function}	handler		The handler function to use subscribe to the property's event
	* @param {Object}	obj			The Object to use for scoping the event handler (see CustomEvent documentation)
	* @return {Boolean}				True, if the unsubscription was successful, otherwise false.
	*/
	this.unsubscribeFromConfigEvent = function(key, handler, obj) {
		key = key.toLowerCase();

		var property = config[key];
		if (typeof property != 'undefined' && property.event) {
			return property.event.unsubscribe(handler, obj);
		} else {
			return false;
		}
	};

	/**
	* Returns a string representation of the Config object
	* @method toString
	* @return {String}	The Config object in string format.
	*/
	this.toString = function() {
		var output = "Config";
		if (this.owner) {
			output += " [" + this.owner.toString() + "]";
		}
		return output;
	};

	/**
	* Returns a string representation of the Config object's current CustomEvent queue
	* @method outputEventQueue
	* @return {String}	The string list of CustomEvents currently queued for execution
	*/
	this.outputEventQueue = function() {
		var output = "";
		for (var q=0;q<eventQueue.length;q++) {
			var queueItem = eventQueue[q];
			if (queueItem) {
				output += queueItem[0] + "=" + queueItem[1] + ", ";
			}
		}
		return output;
	};
};

/**
* Checks to determine if a particular function/Object pair are already subscribed to the specified CustomEvent
* @method YAHOO.util.Config.alreadySubscribed
* @static
* @param {YAHOO.util.CustomEvent} evt	The CustomEvent for which to check the subscriptions
* @param {Function}	fn	The function to look for in the subscribers list
* @param {Object}	obj	The execution scope Object for the subscription
* @return {Boolean}	true, if the function/Object pair is already subscribed to the CustomEvent passed in
*/
YAHOO.util.Config.alreadySubscribed = function(evt, fn, obj) {
	for (var e=0;e<evt.subscribers.length;e++) {
		var subsc = evt.subscribers[e];
		if (subsc && subsc.obj == obj && subsc.fn == fn) {
			return true;
		}
	}
	return false;
};

/**
* YAHOO.widget.DateMath is used for simple date manipulation. The class is a static utility
* used for adding, subtracting, and comparing dates.
* @namespace YAHOO.widget
* @class DateMath
*/
YAHOO.widget.DateMath = {
	/**
	* Constant field representing Day
	* @property DAY
	* @static
	* @final
	* @type String
	*/
	DAY : "D",

	/**
	* Constant field representing Week
	* @property WEEK
	* @static
	* @final
	* @type String
	*/
	WEEK : "W",

	/**
	* Constant field representing Year
	* @property YEAR
	* @static
	* @final
	* @type String
	*/
	YEAR : "Y",

	/**
	* Constant field representing Month
	* @property MONTH
	* @static
	* @final
	* @type String
	*/
	MONTH : "M",

	/**
	* Constant field representing one day, in milliseconds
	* @property ONE_DAY_MS
	* @static
	* @final
	* @type Number
	*/
	ONE_DAY_MS : 1000*60*60*24,

	/**
	* Adds the specified amount of time to the this instance.
	* @method add
	* @param {Date} date	The JavaScript Date object to perform addition on
	* @param {String} field	The field constant to be used for performing addition.
	* @param {Number} amount	The number of units (measured in the field constant) to add to the date.
	* @return {Date} The resulting Date object
	*/
	add : function(date, field, amount) {
		var d = new Date(date.getTime());
		switch (field) {
			case this.MONTH:
				var newMonth = date.getMonth() + amount;
				var years = 0;


				if (newMonth < 0) {
					while (newMonth < 0) {
						newMonth += 12;
						years -= 1;
					}
				} else if (newMonth > 11) {
					while (newMonth > 11) {
						newMonth -= 12;
						years += 1;
					}
				}
				
				d.setMonth(newMonth);
				d.setFullYear(date.getFullYear() + years);
				break;
			case this.DAY:
				d.setDate(date.getDate() + amount);
				break;
			case this.YEAR:
				d.setFullYear(date.getFullYear() + amount);
				break;
			case this.WEEK:
				d.setDate(date.getDate() + (amount * 7));
				break;
		}
		return d;
	},

	/**
	* Subtracts the specified amount of time from the this instance.
	* @method subtract
	* @param {Date} date	The JavaScript Date object to perform subtraction on
	* @param {Number} field	The this field constant to be used for performing subtraction.
	* @param {Number} amount	The number of units (measured in the field constant) to subtract from the date.
	* @return {Date} The resulting Date object
	*/
	subtract : function(date, field, amount) {
		return this.add(date, field, (amount*-1));
	},

	/**
	* Determines whether a given date is before another date on the calendar.
	* @method before
	* @param {Date} date		The Date object to compare with the compare argument
	* @param {Date} compareTo	The Date object to use for the comparison
	* @return {Boolean} true if the date occurs before the compared date; false if not.
	*/
	before : function(date, compareTo) {
		var ms = compareTo.getTime();
		if (date.getTime() < ms) {
			return true;
		} else {
			return false;
		}
	},

	/**
	* Determines whether a given date is after another date on the calendar.
	* @method after
	* @param {Date} date		The Date object to compare with the compare argument
	* @param {Date} compareTo	The Date object to use for the comparison
	* @return {Boolean} true if the date occurs after the compared date; false if not.
	*/
	after : function(date, compareTo) {
		var ms = compareTo.getTime();
		if (date.getTime() > ms) {
			return true;
		} else {
			return false;
		}
	},

	/**
	* Determines whether a given date is between two other dates on the calendar.
	* @method between
	* @param {Date} date		The date to check for
	* @param {Date} dateBegin	The start of the range
	* @param {Date} dateEnd		The end of the range
	* @return {Boolean} true if the date occurs between the compared dates; false if not.
	*/
	between : function(date, dateBegin, dateEnd) {
		if (this.after(date, dateBegin) && this.before(date, dateEnd)) {
			return true;
		} else {
			return false;
		}
	},
	
	/**
	* Retrieves a JavaScript Date object representing January 1 of any given year.
	* @method getJan1
	* @param {Number} calendarYear		The calendar year for which to retrieve January 1
	* @return {Date}	January 1 of the calendar year specified.
	*/
	getJan1 : function(calendarYear) {
		return new Date(calendarYear,0,1); 
	},

	/**
	* Calculates the number of days the specified date is from January 1 of the specified calendar year.
	* Passing January 1 to this function would return an offset value of zero.
	* @method getDayOffset
	* @param {Date}	date	The JavaScript date for which to find the offset
	* @param {Number} calendarYear	The calendar year to use for determining the offset
	* @return {Number}	The number of days since January 1 of the given year
	*/
	getDayOffset : function(date, calendarYear) {
		var beginYear = this.getJan1(calendarYear); // Find the start of the year. This will be in week 1.
		
		// Find the number of days the passed in date is away from the calendar year start
		var dayOffset = Math.ceil((date.getTime()-beginYear.getTime()) / this.ONE_DAY_MS);
		return dayOffset;
	},

	/**
	* Calculates the week number for the given date. This function assumes that week 1 is the
	* week in which January 1 appears, regardless of whether the week consists of a full 7 days.
	* The calendar year can be specified to help find what a the week number would be for a given
	* date if the date overlaps years. For instance, a week may be considered week 1 of 2005, or
	* week 53 of 2004. Specifying the optional calendarYear allows one to make this distinction
	* easily.
	* @method getWeekNumber
	* @param {Date}	date	The JavaScript date for which to find the week number
	* @param {Number} calendarYear	OPTIONAL - The calendar year to use for determining the week number. Default is
	*											the calendar year of parameter "date".
	* @param {Number} weekStartsOn	OPTIONAL - The integer (0-6) representing which day a week begins on. Default is 0 (for Sunday).
	* @return {Number}	The week number of the given date.
	*/
	getWeekNumber : function(date, calendarYear) {
		date = this.clearTime(date);
		var nearestThurs = new Date(date.getTime() + (4 * this.ONE_DAY_MS) - ((date.getDay()) * this.ONE_DAY_MS));

		var jan1 = new Date(nearestThurs.getFullYear(),0,1);
		var dayOfYear = ((nearestThurs.getTime() - jan1.getTime()) / this.ONE_DAY_MS) - 1;

		var weekNum = Math.ceil((dayOfYear)/ 7);
		return weekNum;
	},

	/**
	* Determines if a given week overlaps two different years.
	* @method isYearOverlapWeek
	* @param {Date}	weekBeginDate	The JavaScript Date representing the first day of the week.
	* @return {Boolean}	true if the date overlaps two different years.
	*/
	isYearOverlapWeek : function(weekBeginDate) {
		var overlaps = false;
		var nextWeek = this.add(weekBeginDate, this.DAY, 6);
		if (nextWeek.getFullYear() != weekBeginDate.getFullYear()) {
			overlaps = true;
		}
		return overlaps;
	},

	/**
	* Determines if a given week overlaps two different months.
	* @method isMonthOverlapWeek
	* @param {Date}	weekBeginDate	The JavaScript Date representing the first day of the week.
	* @return {Boolean}	true if the date overlaps two different months.
	*/
	isMonthOverlapWeek : function(weekBeginDate) {
		var overlaps = false;
		var nextWeek = this.add(weekBeginDate, this.DAY, 6);
		if (nextWeek.getMonth() != weekBeginDate.getMonth()) {
			overlaps = true;
		}
		return overlaps;
	},

	/**
	* Gets the first day of a month containing a given date.
	* @method findMonthStart
	* @param {Date}	date	The JavaScript Date used to calculate the month start
	* @return {Date}		The JavaScript Date representing the first day of the month
	*/
	findMonthStart : function(date) {
		var start = new Date(date.getFullYear(), date.getMonth(), 1);
		return start;
	},

	/**
	* Gets the last day of a month containing a given date.
	* @method findMonthEnd
	* @param {Date}	date	The JavaScript Date used to calculate the month end
	* @return {Date}		The JavaScript Date representing the last day of the month
	*/
	findMonthEnd : function(date) {
		var start = this.findMonthStart(date);
		var nextMonth = this.add(start, this.MONTH, 1);
		var end = this.subtract(nextMonth, this.DAY, 1);
		return end;
	},

	/**
	* Clears the time fields from a given date, effectively setting the time to 12 noon.
	* @method clearTime
	* @param {Date}	date	The JavaScript Date for which the time fields will be cleared
	* @return {Date}		The JavaScript Date cleared of all time fields
	*/
	clearTime : function(date) {
		date.setHours(12,0,0,0);
		return date;
	}
};

/**
* The Calendar component is a UI control that enables users to choose one or more dates from a graphical calendar presented in a one-month ("one-up") or two-month ("two-up") interface. Calendars are generated entirely via script and can be navigated without any page refreshes.
* @module    calendar
* @title     Calendar
* @namespace YAHOO.widget
* @requires  yahoo,dom,event
*/

/**
* Calendar is the base class for the Calendar widget. In its most basic
* implementation, it has the ability to render a calendar widget on the page
* that can be manipulated to select a single date, move back and forth between
* months and years.
* <p>To construct the placeholder for the calendar widget, the code is as
* follows:
*	<xmp>
*		<div id="cal1Container"></div>
*	</xmp>
* Note that the table can be replaced with any kind of element.
* </p>
* @namespace YAHOO.widget
* @class Calendar
* @constructor
* @param {String}	id			The id of the table element that will represent the calendar widget
* @param {String}	containerId	The id of the container div element that will wrap the calendar table
* @param {Object}	config		The configuration object containing the Calendar's arguments
*/
YAHOO.widget.Calendar = function(id, containerId, config) {
	this.init(id, containerId, config);
};

/**
* The path to be used for images loaded for the Calendar
* @property YAHOO.widget.Calendar.IMG_ROOT
* @static
* @type String
*/
YAHOO.widget.Calendar.IMG_ROOT = (window.location.href.toLowerCase().indexOf("https") === 0 ? "https://a248.e.akamai.net/sec.yimg.com/i/" : "http://us.i1.yimg.com/us.yimg.com/i/");

/**
* Type constant used for renderers to represent an individual date (M/D/Y)
* @property YAHOO.widget.Calendar.DATE
* @static
* @final
* @type String
*/
YAHOO.widget.Calendar.DATE = "D";

/**
* Type constant used for renderers to represent an individual date across any year (M/D)
* @property YAHOO.widget.Calendar.MONTH_DAY
* @static
* @final
* @type String
*/
YAHOO.widget.Calendar.MONTH_DAY = "MD";

/**
* Type constant used for renderers to represent a weekday
* @property YAHOO.widget.Calendar.WEEKDAY
* @static
* @final
* @type String
*/
YAHOO.widget.Calendar.WEEKDAY = "WD";

/**
* Type constant used for renderers to represent a range of individual dates (M/D/Y-M/D/Y)
* @property YAHOO.widget.Calendar.RANGE
* @static
* @final
* @type String
*/
YAHOO.widget.Calendar.RANGE = "R";

/**
* Type constant used for renderers to represent a month across any year
* @property YAHOO.widget.Calendar.MONTH
* @static
* @final
* @type String
*/
YAHOO.widget.Calendar.MONTH = "M";

/**
* Constant that represents the total number of date cells that are displayed in a given month
* @property YAHOO.widget.Calendar.DISPLAY_DAYS
* @static
* @final
* @type Number
*/
YAHOO.widget.Calendar.DISPLAY_DAYS = 42;

/**
* Constant used for halting the execution of the remainder of the render stack
* @property YAHOO.widget.Calendar.STOP_RENDER
* @static
* @final
* @type String
*/
YAHOO.widget.Calendar.STOP_RENDER = "S";

YAHOO.widget.Calendar.prototype = {

	/**
	* The configuration object used to set up the calendars various locale and style options.
	* @property Config
	* @private
	* @deprecated Configuration properties should be set by calling Calendar.cfg.setProperty.
	* @type Object
	*/
	Config : null,

	/**
	* The parent CalendarGroup, only to be set explicitly by the parent group
	* @property parent
	* @type CalendarGroup
	*/	
	parent : null,

	/**
	* The index of this item in the parent group
	* @property index
	* @type Number
	*/
	index : -1,

	/**
	* The collection of calendar table cells
	* @property cells
	* @type HTMLTableCellElement[]
	*/
	cells : null,
	
	/**
	* The collection of calendar cell dates that is parallel to the cells collection. The array contains dates field arrays in the format of [YYYY, M, D].
	* @property cellDates
	* @type Array[](Number[])
	*/
	cellDates : null,

	/**
	* The id that uniquely identifies this calendar. This id should match the id of the placeholder element on the page.
	* @property id
	* @type String
	*/
	id : null,

	/**
	* The DOM element reference that points to this calendar's container element. The calendar will be inserted into this element when the shell is rendered.
	* @property oDomContainer
	* @type HTMLElement
	*/
	oDomContainer : null,

	/**
	* A Date object representing today's date.
	* @property today
	* @type Date
	*/
	today : null,

	/**
	* The list of render functions, along with required parameters, used to render cells. 
	* @property renderStack
	* @type Array[]
	*/
	renderStack : null,

	/**
	* A copy of the initial render functions created before rendering.
	* @property _renderStack
	* @private
	* @type Array
	*/
	_renderStack : null,

	/**
	* A Date object representing the month/year that the calendar is initially set to
	* @property _pageDate
	* @private
	* @type Date
	*/
	_pageDate : null,

	/**
	* The private list of initially selected dates.
	* @property _selectedDates
	* @private
	* @type Array
	*/
	_selectedDates : null,

	/**
	* A map of DOM event handlers to attach to cells associated with specific CSS class names
	* @property domEventMap
	* @type Object
	*/
	domEventMap : null
};



/**
* Initializes the Calendar widget.
* @method init
* @param {String}	id			The id of the table element that will represent the calendar widget
* @param {String}	containerId	The id of the container div element that will wrap the calendar table
* @param {Object}	config		The configuration object containing the Calendar's arguments
*/
YAHOO.widget.Calendar.prototype.init = function(id, containerId, config) {
	this.logger = new YAHOO.widget.LogWriter("Calendar_Core " + id);
	
	this.initEvents();
	this.today = new Date();
	YAHOO.widget.DateMath.clearTime(this.today);

	this.id = id;
	this.oDomContainer = document.getElementById(containerId);
	if (! this.oDomContainer) {
		this.logger.log("No valid container present.", "error");
	}

	/**
	* The Config object used to hold the configuration variables for the Calendar
	* @property cfg
	* @type YAHOO.util.Config
	*/
	this.cfg = new YAHOO.util.Config(this);
	
	/**
	* The local object which contains the Calendar's options
	* @property Options
	* @type Object
	*/
	this.Options = {};

	/**
	* The local object which contains the Calendar's locale settings
	* @property Locale
	* @type Object
	*/
	this.Locale = {};

	this.initStyles();

	YAHOO.util.Dom.addClass(this.oDomContainer, this.Style.CSS_CONTAINER);	
	YAHOO.util.Dom.addClass(this.oDomContainer, this.Style.CSS_SINGLE);

	this.cellDates = [];
	this.cells = [];
	this.renderStack = [];
	this._renderStack = [];

	this.setupConfig();
	
	if (config) {
		this.cfg.applyConfig(config, true);
	}
	
	this.cfg.fireQueue();
};

/**
* Renders the built-in IFRAME shim for the IE6 and below
* @method configIframe
*/
YAHOO.widget.Calendar.prototype.configIframe = function(type, args, obj) {
	var useIframe = args[0];

	if (YAHOO.util.Dom.inDocument(this.oDomContainer)) {
		if (useIframe) {
			var pos = YAHOO.util.Dom.getStyle(this.oDomContainer, "position");

			if (this.browser == "ie" && (pos == "absolute" || pos == "relative")) {
				if (! YAHOO.util.Dom.inDocument(this.iframe)) {
					this.iframe = document.createElement("iframe");
					this.iframe.src = "javascript:false;";
					YAHOO.util.Dom.setStyle(this.iframe, "opacity", "0");
					this.oDomContainer.insertBefore(this.iframe, this.oDomContainer.firstChild);
				}
			}
		} else {
			if (this.iframe) {
				if (this.iframe.parentNode) {
					this.iframe.parentNode.removeChild(this.iframe);
				}
				this.iframe = null;
			}
		}
	}
};

/**
* Default handler for the "title" property
* @method configTitle
*/
YAHOO.widget.Calendar.prototype.configTitle = function(type, args, obj) {
	var title = args[0];
	var close = this.cfg.getProperty("close");
	
	var titleDiv;

	if (title && title !== "") {
		titleDiv = YAHOO.util.Dom.getElementsByClassName(YAHOO.widget.CalendarGroup.CSS_2UPTITLE, "div", this.oDomContainer)[0] || document.createElement("div");
		titleDiv.className = YAHOO.widget.CalendarGroup.CSS_2UPTITLE;
		titleDiv.innerHTML = title;
		this.oDomContainer.insertBefore(titleDiv, this.oDomContainer.firstChild);
		YAHOO.util.Dom.addClass(this.oDomContainer, "withtitle");
	} else {
		titleDiv = YAHOO.util.Dom.getElementsByClassName(YAHOO.widget.CalendarGroup.CSS_2UPTITLE, "div", this.oDomContainer)[0] || null;

		if (titleDiv) {
			YAHOO.util.Event.purgeElement(titleDiv);
			this.oDomContainer.removeChild(titleDiv);
		}
		if (! close) {
			YAHOO.util.Dom.removeClass(this.oDomContainer, "withtitle");
		}
	}
};

/**
* Default handler for the "close" property
* @method configClose
*/
YAHOO.widget.Calendar.prototype.configClose = function(type, args, obj) {
	var close = args[0];
	var title = this.cfg.getProperty("title");

	var linkClose;

	if (close === true) {
		linkClose = YAHOO.util.Dom.getElementsByClassName("link-close", "a", this.oDomContainer)[0] || document.createElement("a");
		linkClose.href = "javascript:void(null);";
		linkClose.className = "link-close";
		YAHOO.util.Event.addListener(linkClose, "click", this.hide, this, true);
		var imgClose = document.createElement("img");
		imgClose.src = YAHOO.widget.Calendar.IMG_ROOT + "us/my/bn/x_d.gif";
		imgClose.className = YAHOO.widget.CalendarGroup.CSS_2UPCLOSE;
		linkClose.appendChild(imgClose);
		this.oDomContainer.appendChild(linkClose);
		YAHOO.util.Dom.addClass(this.oDomContainer, "withtitle");
	} else {
		linkClose = YAHOO.util.Dom.getElementsByClassName("link-close", "a", this.oDomContainer)[0] || null;

		if (linkClose) {
			YAHOO.util.Event.purgeElement(linkClose);
			this.oDomContainer.removeChild(linkClose);
		}
		if (! title || title === "") {
			YAHOO.util.Dom.removeClass(this.oDomContainer, "withtitle");
		}
	}
};

/**
* Initializes Calendar's built-in CustomEvents
* @method initEvents
*/
YAHOO.widget.Calendar.prototype.initEvents = function() {

	/**
	* Fired before a selection is made
	* @event beforeSelectEvent
	*/
	this.beforeSelectEvent = new YAHOO.util.CustomEvent("beforeSelect"); 

	/**
	* Fired when a selection is made
	* @event selectEvent
	* @param {Array}	Array of Date field arrays in the format [YYYY, MM, DD].
	*/
	this.selectEvent = new YAHOO.util.CustomEvent("select");

	/**
	* Fired before a selection is made
	* @event beforeDeselectEvent
	*/
	this.beforeDeselectEvent = new YAHOO.util.CustomEvent("beforeDeselect");

	/**
	* Fired when a selection is made
	* @event deselectEvent
	* @param {Array}	Array of Date field arrays in the format [YYYY, MM, DD].
	*/
	this.deselectEvent = new YAHOO.util.CustomEvent("deselect");

	/**
	* Fired when the Calendar page is changed
	* @event changePageEvent
	*/
	this.changePageEvent = new YAHOO.util.CustomEvent("changePage");

	/**
	* Fired before the Calendar is rendered
	* @event beforeRenderEvent
	*/
	this.beforeRenderEvent = new YAHOO.util.CustomEvent("beforeRender");

	/**
	* Fired when the Calendar is rendered
	* @event renderEvent
	*/
	this.renderEvent = new YAHOO.util.CustomEvent("render");

	/**
	* Fired when the Calendar is reset
	* @event resetEvent
	*/
	this.resetEvent = new YAHOO.util.CustomEvent("reset");

	/**
	* Fired when the Calendar is cleared
	* @event clearEvent
	*/
	this.clearEvent = new YAHOO.util.CustomEvent("clear");

	this.beforeSelectEvent.subscribe(this.onBeforeSelect, this, true);
	this.selectEvent.subscribe(this.onSelect, this, true);
	this.beforeDeselectEvent.subscribe(this.onBeforeDeselect, this, true);
	this.deselectEvent.subscribe(this.onDeselect, this, true);
	this.changePageEvent.subscribe(this.onChangePage, this, true);
	this.renderEvent.subscribe(this.onRender, this, true);
	this.resetEvent.subscribe(this.onReset, this, true);
	this.clearEvent.subscribe(this.onClear, this, true);
};


/**
* The default event function that is attached to a date link within a calendar cell
* when the calendar is rendered.
* @method doSelectCell
* @param {DOMEvent} e	The event
* @param {Calendar} cal	A reference to the calendar passed by the Event utility
*/
YAHOO.widget.Calendar.prototype.doSelectCell = function(e, cal) {
	var target = YAHOO.util.Event.getTarget(e);

	var cell,index,d,date;

	while (target.tagName.toLowerCase() != "td" && ! YAHOO.util.Dom.hasClass(target, cal.Style.CSS_CELL_SELECTABLE)) {
		target = target.parentNode;
		if (target.tagName.toLowerCase() == "html") {
			return;
		}
	}
	
	cell = target;

	if (YAHOO.util.Dom.hasClass(cell, cal.Style.CSS_CELL_SELECTABLE)) {
		index = cell.id.split("cell")[1];
		d = cal.cellDates[index];
		date = new Date(d[0],d[1]-1,d[2]);
	
		var link;

		cal.logger.log("Selecting cell " + index + " via click", "info");

		if (cal.Options.MULTI_SELECT) {
			link = cell.getElementsByTagName("a")[0];
			if (link) {
				link.blur();
			}
			
			var cellDate = cal.cellDates[index];
			var cellDateIndex = cal._indexOfSelectedFieldArray(cellDate);
			
			if (cellDateIndex > -1) {	
				cal.deselectCell(index);
			} else {
				cal.selectCell(index);
			}	
			
		} else {
			link = cell.getElementsByTagName("a")[0];
			if (link) {
				link.blur();
			}
			cal.selectCell(index);
		}
	}
};

/**
* The event that is executed when the user hovers over a cell
* @method doCellMouseOver
* @param {DOMEvent} e	The event
* @param {Calendar} cal	A reference to the calendar passed by the Event utility
*/
YAHOO.widget.Calendar.prototype.doCellMouseOver = function(e, cal) {
	var target;
	if (e) {
		target = YAHOO.util.Event.getTarget(e);
	} else {
		target = this;
	}

	while (target.tagName.toLowerCase() != "td") {
		target = target.parentNode;
		if (target.tagName.toLowerCase() == "html") {
			return;
		}
	}

	if (YAHOO.util.Dom.hasClass(target, cal.Style.CSS_CELL_SELECTABLE)) {
		YAHOO.util.Dom.addClass(target, cal.Style.CSS_CELL_HOVER);
	}
};

/**
* The event that is executed when the user moves the mouse out of a cell
* @method doCellMouseOut
* @param {DOMEvent} e	The event
* @param {Calendar} cal	A reference to the calendar passed by the Event utility
*/
YAHOO.widget.Calendar.prototype.doCellMouseOut = function(e, cal) {
	var target;
	if (e) {
		target = YAHOO.util.Event.getTarget(e);
	} else {
		target = this;
	}

	while (target.tagName.toLowerCase() != "td") {
		target = target.parentNode;
		if (target.tagName.toLowerCase() == "html") {
			return;
		}
	}

	if (YAHOO.util.Dom.hasClass(target, cal.Style.CSS_CELL_SELECTABLE)) {
		YAHOO.util.Dom.removeClass(target, cal.Style.CSS_CELL_HOVER);
	}
};

YAHOO.widget.Calendar.prototype.setupConfig = function() {

	/**
	* The month/year representing the current visible Calendar date (mm/yyyy)
	* @config pagedate
	* @type String
	* @default today's date
	*/
	this.cfg.addProperty("pagedate", { value:new Date(), handler:this.configPageDate } );

	/**
	* The date or range of dates representing the current Calendar selection
	* @config selected
	* @type String
	* @default []
	*/
	this.cfg.addProperty("selected", { value:[], handler:this.configSelected } );

	/**
	* The title to display above the Calendar's month header
	* @config title
	* @type String
	* @default ""
	*/
	this.cfg.addProperty("title", { value:"", handler:this.configTitle } );

	/**
	* Whether or not a close button should be displayed for this Calendar
	* @config close
	* @type Boolean
	* @default false
	*/
	this.cfg.addProperty("close", { value:false, handler:this.configClose } );

	/**
	* Whether or not an iframe shim should be placed under the Calendar to prevent select boxes from bleeding through in Internet Explorer 6 and below.
	* @config iframe
	* @type Boolean
	* @default true
	*/
	this.cfg.addProperty("iframe", { value:true, handler:this.configIframe, validator:this.cfg.checkBoolean } );

	/**
	* The minimum selectable date in the current Calendar (mm/dd/yyyy)
	* @config mindate
	* @type String
	* @default null
	*/
	this.cfg.addProperty("mindate", { value:null, handler:this.configMinDate } );

	/**
	* The maximum selectable date in the current Calendar (mm/dd/yyyy)
	* @config maxdate
	* @type String
	* @default null
	*/
	this.cfg.addProperty("maxdate", { value:null, handler:this.configMaxDate } );


	// Options properties

	/**
	* True if the Calendar should allow multiple selections. False by default.
	* @config MULTI_SELECT
	* @type Boolean
	* @default false
	*/
	this.cfg.addProperty("MULTI_SELECT",	{ value:false, handler:this.configOptions, validator:this.cfg.checkBoolean } );

	/**
	* The weekday the week begins on. Default is 0 (Sunday).
	* @config START_WEEKDAY
	* @type number
	* @default 0
	*/
	this.cfg.addProperty("START_WEEKDAY",	{ value:0, handler:this.configOptions, validator:this.cfg.checkNumber  } );

	/**
	* True if the Calendar should show weekday labels. True by default.
	* @config SHOW_WEEKDAYS
	* @type Boolean
	* @default true
	*/
	this.cfg.addProperty("SHOW_WEEKDAYS",	{ value:true, handler:this.configOptions, validator:this.cfg.checkBoolean  } );

	/**
	* True if the Calendar should show week row headers. False by default.
	* @config SHOW_WEEK_HEADER
	* @type Boolean
	* @default false
	*/
	this.cfg.addProperty("SHOW_WEEK_HEADER",{ value:false, handler:this.configOptions, validator:this.cfg.checkBoolean } );

	/**
	* True if the Calendar should show week row footers. False by default.
	* @config SHOW_WEEK_FOOTER
	* @type Boolean
	* @default false
	*/	
	this.cfg.addProperty("SHOW_WEEK_FOOTER",{ value:false, handler:this.configOptions, validator:this.cfg.checkBoolean } );

	/**
	* True if the Calendar should suppress weeks that are not a part of the current month. False by default.
	* @config HIDE_BLANK_WEEKS
	* @type Boolean
	* @default false
	*/	
	this.cfg.addProperty("HIDE_BLANK_WEEKS",{ value:false, handler:this.configOptions, validator:this.cfg.checkBoolean } );

	/**
	* The image that should be used for the left navigation arrow.
	* @config NAV_ARROW_LEFT
	* @type String
	* @default YAHOO.widget.Calendar.IMG_ROOT + "us/tr/callt.gif"
	*/	
	this.cfg.addProperty("NAV_ARROW_LEFT",	{ value:YAHOO.widget.Calendar.IMG_ROOT + "us/tr/callt.gif", handler:this.configOptions } );

	/**
	* The image that should be used for the left navigation arrow.
	* @config NAV_ARROW_RIGHT
	* @type String
	* @default YAHOO.widget.Calendar.IMG_ROOT + "us/tr/calrt.gif"
	*/	
	this.cfg.addProperty("NAV_ARROW_RIGHT",	{ value:YAHOO.widget.Calendar.IMG_ROOT + "us/tr/calrt.gif", handler:this.configOptions } );

	// Locale properties

	/**
	* The short month labels for the current locale.
	* @config MONTHS_SHORT
	* @type String[]
	* @default ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
	*/
	this.cfg.addProperty("MONTHS_SHORT",	{ value:["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], handler:this.configLocale } );
	
	/**
	* The long month labels for the current locale.
	* @config MONTHS_LONG
	* @type String[]
	* @default ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	*/	
	this.cfg.addProperty("MONTHS_LONG",		{ value:["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], handler:this.configLocale } );
	
	/**
	* The 1-character weekday labels for the current locale.
	* @config WEEKDAYS_1CHAR
	* @type String[]
	* @default ["S", "M", "T", "W", "T", "F", "S"]
	*/	
	this.cfg.addProperty("WEEKDAYS_1CHAR",	{ value:["S", "M", "T", "W", "T", "F", "S"], handler:this.configLocale } );
	
	/**
	* The short weekday labels for the current locale.
	* @config WEEKDAYS_SHORT
	* @type String[]
	* @default ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"]
	*/	
	this.cfg.addProperty("WEEKDAYS_SHORT",	{ value:["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"], handler:this.configLocale } );
	
	/**
	* The medium weekday labels for the current locale.
	* @config WEEKDAYS_MEDIUM
	* @type String[]
	* @default ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
	*/	
	this.cfg.addProperty("WEEKDAYS_MEDIUM",	{ value:["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], handler:this.configLocale } );
	
	/**
	* The long weekday labels for the current locale.
	* @config WEEKDAYS_LONG
	* @type String[]
	* @default ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
	*/	
	this.cfg.addProperty("WEEKDAYS_LONG",	{ value:["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], handler:this.configLocale } );

	/**
	* Refreshes the locale values used to build the Calendar.
	* @method refreshLocale
	* @private
	*/
	var refreshLocale = function() {
		this.cfg.refireEvent("LOCALE_MONTHS");
		this.cfg.refireEvent("LOCALE_WEEKDAYS");
	};

	this.cfg.subscribeToConfigEvent("START_WEEKDAY", refreshLocale, this, true);
	this.cfg.subscribeToConfigEvent("MONTHS_SHORT", refreshLocale, this, true);
	this.cfg.subscribeToConfigEvent("MONTHS_LONG", refreshLocale, this, true);
	this.cfg.subscribeToConfigEvent("WEEKDAYS_1CHAR", refreshLocale, this, true);
	this.cfg.subscribeToConfigEvent("WEEKDAYS_SHORT", refreshLocale, this, true);
	this.cfg.subscribeToConfigEvent("WEEKDAYS_MEDIUM", refreshLocale, this, true);
	this.cfg.subscribeToConfigEvent("WEEKDAYS_LONG", refreshLocale, this, true);
	
	/**
	* The setting that determines which length of month labels should be used. Possible values are "short" and "long".
	* @config LOCALE_MONTHS
	* @type String
	* @default "long"
	*/	
	this.cfg.addProperty("LOCALE_MONTHS",	{ value:"long", handler:this.configLocaleValues } );
	
	/**
	* The setting that determines which length of weekday labels should be used. Possible values are "1char", "short", "medium", and "long".
	* @config LOCALE_WEEKDAYS
	* @type String
	* @default "short"
	*/	
	this.cfg.addProperty("LOCALE_WEEKDAYS",	{ value:"short", handler:this.configLocaleValues } );

	/**
	* The value used to delimit individual dates in a date string passed to various Calendar functions.
	* @config DATE_DELIMITER
	* @type String
	* @default ","
	*/	
	this.cfg.addProperty("DATE_DELIMITER",		{ value:",", handler:this.configLocale } );

	/**
	* The value used to delimit date fields in a date string passed to various Calendar functions.
	* @config DATE_FIELD_DELIMITER
	* @type String
	* @default "/"
	*/	
	this.cfg.addProperty("DATE_FIELD_DELIMITER",{ value:"/", handler:this.configLocale } );

	/**
	* The value used to delimit date ranges in a date string passed to various Calendar functions.
	* @config DATE_RANGE_DELIMITER
	* @type String
	* @default "-"
	*/
	this.cfg.addProperty("DATE_RANGE_DELIMITER",{ value:"-", handler:this.configLocale } );

	/**
	* The position of the month in a month/year date string
	* @config MY_MONTH_POSITION
	* @type Number
	* @default 1
	*/
	this.cfg.addProperty("MY_MONTH_POSITION",	{ value:1, handler:this.configLocale, validator:this.cfg.checkNumber } );

	/**
	* The position of the year in a month/year date string
	* @config MY_YEAR_POSITION
	* @type Number
	* @default 2
	*/
	this.cfg.addProperty("MY_YEAR_POSITION",	{ value:2, handler:this.configLocale, validator:this.cfg.checkNumber } );

	/**
	* The position of the month in a month/day date string
	* @config MD_MONTH_POSITION
	* @type Number
	* @default 1
	*/
	this.cfg.addProperty("MD_MONTH_POSITION",	{ value:1, handler:this.configLocale, validator:this.cfg.checkNumber } );

	/**
	* The position of the day in a month/year date string
	* @config MD_DAY_POSITION
	* @type Number
	* @default 2
	*/
	this.cfg.addProperty("MD_DAY_POSITION",		{ value:2, handler:this.configLocale, validator:this.cfg.checkNumber } );

	/**
	* The position of the month in a month/day/year date string
	* @config MDY_MONTH_POSITION
	* @type Number
	* @default 1
	*/
	this.cfg.addProperty("MDY_MONTH_POSITION",	{ value:1, handler:this.configLocale, validator:this.cfg.checkNumber } );

	/**
	* The position of the day in a month/day/year date string
	* @config MDY_DAY_POSITION
	* @type Number
	* @default 2
	*/
	this.cfg.addProperty("MDY_DAY_POSITION",	{ value:2, handler:this.configLocale, validator:this.cfg.checkNumber } );

	/**
	* The position of the year in a month/day/year date string
	* @config MDY_YEAR_POSITION
	* @type Number
	* @default 3
	*/
	this.cfg.addProperty("MDY_YEAR_POSITION",	{ value:3, handler:this.configLocale, validator:this.cfg.checkNumber } );
};

/**
* The default handler for the "pagedate" property
* @method configPageDate
*/
YAHOO.widget.Calendar.prototype.configPageDate = function(type, args, obj) {
	var val = args[0];
	var month, year, aMonthYear;

	if (val) {
		if (val instanceof Date) {
			val = YAHOO.widget.DateMath.findMonthStart(val);
			this.cfg.setProperty("pagedate", val, true);
			if (! this._pageDate) {
				this._pageDate = this.cfg.getProperty("pagedate");
			}
			return;
		} else {
			aMonthYear = val.split(this.cfg.getProperty("DATE_FIELD_DELIMITER"));
			month = parseInt(aMonthYear[this.cfg.getProperty("MY_MONTH_POSITION")-1], 10)-1;
			year = parseInt(aMonthYear[this.cfg.getProperty("MY_YEAR_POSITION")-1], 10);
		}
	} else {
		month = this.today.getMonth();
		year = this.today.getFullYear();
	}
	
	this.cfg.setProperty("pagedate", new Date(year, month, 1), true);
	
	this.logger.log("Set month/year to " + month + "/" + year, "info");

	if (! this._pageDate) {
		this._pageDate = this.cfg.getProperty("pagedate");
	}
};

/**
* The default handler for the "mindate" property
* @method configMinDate
*/
YAHOO.widget.Calendar.prototype.configMinDate = function(type, args, obj) {
	var val = args[0];
	if (typeof val == 'string') {
		val = this._parseDate(val);
		this.cfg.setProperty("mindate", new Date(val[0],(val[1]-1),val[2]));
	}
};

/**
* The default handler for the "maxdate" property
* @method configMaxDate
*/
YAHOO.widget.Calendar.prototype.configMaxDate = function(type, args, obj) {
	var val = args[0];
	if (typeof val == 'string') {
		val = this._parseDate(val);
		this.cfg.setProperty("maxdate", new Date(val[0],(val[1]-1),val[2]));
	}
};

/**
* The default handler for the "selected" property
* @method configSelected
*/
YAHOO.widget.Calendar.prototype.configSelected = function(type, args, obj) {
	var selected = args[0];
	
	if (selected) {
		if (typeof selected == 'string') {
			this.cfg.setProperty("selected", this._parseDates(selected), true);
		} 
	}
	if (! this._selectedDates) {
		this._selectedDates = this.cfg.getProperty("selected");
	}
};

/**
* The default handler for all configuration options properties
* @method configOptions
*/
YAHOO.widget.Calendar.prototype.configOptions = function(type, args, obj) {
	type = type.toUpperCase();
	var val = args[0];
	this.Options[type] = val;
};

/**
* The default handler for all configuration locale properties
* @method configLocale
*/
YAHOO.widget.Calendar.prototype.configLocale = function(type, args, obj) {
	type = type.toUpperCase();
	var val = args[0];
	this.Locale[type] = val;

	this.cfg.refireEvent("LOCALE_MONTHS");
	this.cfg.refireEvent("LOCALE_WEEKDAYS");

};

/**
* The default handler for all configuration locale field length properties
* @method configLocaleValues
*/
YAHOO.widget.Calendar.prototype.configLocaleValues = function(type, args, obj) {
	type = type.toUpperCase();
	var val = args[0];

	switch (type) {
		case "LOCALE_MONTHS":
			switch (val) {
				case "short":
					this.Locale.LOCALE_MONTHS = this.cfg.getProperty("MONTHS_SHORT").concat();
					break;
				case "long":
					this.Locale.LOCALE_MONTHS = this.cfg.getProperty("MONTHS_LONG").concat();
					break;
			}
			break;
		case "LOCALE_WEEKDAYS":
			switch (val) {
				case "1char":
					this.Locale.LOCALE_WEEKDAYS = this.cfg.getProperty("WEEKDAYS_1CHAR").concat();
					break;
				case "short":
					this.Locale.LOCALE_WEEKDAYS = this.cfg.getProperty("WEEKDAYS_SHORT").concat();
					break;
				case "medium":
					this.Locale.LOCALE_WEEKDAYS = this.cfg.getProperty("WEEKDAYS_MEDIUM").concat();
					break;
				case "long":
					this.Locale.LOCALE_WEEKDAYS = this.cfg.getProperty("WEEKDAYS_LONG").concat();
					break;
			}
			
			var START_WEEKDAY = this.cfg.getProperty("START_WEEKDAY");

			if (START_WEEKDAY > 0) {
				for (var w=0;w<START_WEEKDAY;++w) {
					this.Locale.LOCALE_WEEKDAYS.push(this.Locale.LOCALE_WEEKDAYS.shift());
				}
			}
			break;
	}
};

/**
* Defines the style constants for the Calendar
* @method initStyles
*/
YAHOO.widget.Calendar.prototype.initStyles = function() {

	/**
	* Collection of Style constants for the Calendar
	* @property Style
	*/
	this.Style = {
		/**
		* @property Style.CSS_ROW_HEADER
		*/
		CSS_ROW_HEADER: "calrowhead",
		/**
		* @property Style.CSS_ROW_FOOTER
		*/
		CSS_ROW_FOOTER: "calrowfoot",
		/**
		* @property Style.CSS_CELL
		*/
		CSS_CELL : "calcell",
		/**
		* @property Style.CSS_CELL_SELECTED
		*/
		CSS_CELL_SELECTED : "selected",
		/**
		* @property Style.CSS_CELL_SELECTABLE
		*/
		CSS_CELL_SELECTABLE : "selectable",
		/**
		* @property Style.CSS_CELL_RESTRICTED
		*/
		CSS_CELL_RESTRICTED : "restricted",
		/**
		* @property Style.CSS_CELL_TODAY
		*/
		CSS_CELL_TODAY : "today",
		/**
		* @property Style.CSS_CELL_OOM
		*/
		CSS_CELL_OOM : "oom",
		/**
		* @property Style.CSS_CELL_OOB
		*/
		CSS_CELL_OOB : "previous",
		/**
		* @property Style.CSS_HEADER
		*/
		CSS_HEADER : "calheader",
		/**
		* @property Style.CSS_HEADER_TEXT
		*/
		CSS_HEADER_TEXT : "calhead",
		/**
		* @property Style.CSS_WEEKDAY_CELL
		*/
		CSS_WEEKDAY_CELL : "calweekdaycell",
		/**
		* @property Style.CSS_WEEKDAY_ROW
		*/
		CSS_WEEKDAY_ROW : "calweekdayrow",
		/**
		* @property Style.CSS_FOOTER
		*/
		CSS_FOOTER : "calfoot",
		/**
		* @property Style.CSS_CALENDAR
		*/
		CSS_CALENDAR : "yui-calendar",
		/**
		* @property Style.CSS_SINGLE
		*/
		CSS_SINGLE : "single",
		/**
		* @property Style.CSS_CONTAINER
		*/
		CSS_CONTAINER : "yui-calcontainer",
		/**
		* @property Style.CSS_NAV_LEFT
		*/
		CSS_NAV_LEFT : "calnavleft",
		/**
		* @property Style.CSS_NAV_RIGHT
		*/
		CSS_NAV_RIGHT : "calnavright",
		/**
		* @property Style.CSS_CELL_TOP
		*/
		CSS_CELL_TOP : "calcelltop",
		/**
		* @property Style.CSS_CELL_LEFT
		*/
		CSS_CELL_LEFT : "calcellleft",
		/**
		* @property Style.CSS_CELL_RIGHT
		*/
		CSS_CELL_RIGHT : "calcellright",
		/**
		* @property Style.CSS_CELL_BOTTOM
		*/
		CSS_CELL_BOTTOM : "calcellbottom",
		/**
		* @property Style.CSS_CELL_HOVER
		*/
		CSS_CELL_HOVER : "calcellhover",
		/**
		* @property Style.CSS_CELL_HIGHLIGHT1
		*/
		CSS_CELL_HIGHLIGHT1 : "highlight1",
		/**
		* @property Style.CSS_CELL_HIGHLIGHT2
		*/
		CSS_CELL_HIGHLIGHT2 : "highlight2",
		/**
		* @property Style.CSS_CELL_HIGHLIGHT3
		*/
		CSS_CELL_HIGHLIGHT3 : "highlight3",
		/**
		* @property Style.CSS_CELL_HIGHLIGHT4
		*/
		CSS_CELL_HIGHLIGHT4 : "highlight4"
	};
};

/**
* Builds the date label that will be displayed in the calendar header or
* footer, depending on configuration.
* @method buildMonthLabel
* @return	{String}	The formatted calendar month label
*/
YAHOO.widget.Calendar.prototype.buildMonthLabel = function() {
	var text = this.Locale.LOCALE_MONTHS[this.cfg.getProperty("pagedate").getMonth()] + " " + this.cfg.getProperty("pagedate").getFullYear();
	return text;
};

/**
* Builds the date digit that will be displayed in calendar cells
* @method buildDayLabel
* @param {Date}	workingDate	The current working date
* @return	{String}	The formatted day label
*/
YAHOO.widget.Calendar.prototype.buildDayLabel = function(workingDate) {
	var day = workingDate.getDate();
	return day;
};

/**
* Renders the calendar header.
* @method renderHeader
* @param {Array}	html	The current working HTML array
* @return {Array} The current working HTML array
*/
YAHOO.widget.Calendar.prototype.renderHeader = function(html) {
	this.logger.log("Rendering header", "info");

	var colSpan = 7;
	
	if (this.cfg.getProperty("SHOW_WEEK_HEADER")) {
		colSpan += 1;
	}

	if (this.cfg.getProperty("SHOW_WEEK_FOOTER")) {
		colSpan += 1;
	}

	html[html.length] = "<thead>";
	html[html.length] =		"<tr>";
	html[html.length] =			'<th colspan="' + colSpan + '" class="' + this.Style.CSS_HEADER_TEXT + '">';
	html[html.length] =				'<div class="' + this.Style.CSS_HEADER + '">';

		var renderLeft, renderRight = false;

		if (this.parent) {
			if (this.index === 0) {
				renderLeft = true;
			}
			if (this.index == (this.parent.cfg.getProperty("pages") -1)) {
				renderRight = true;
			}
		} else {
			renderLeft = true;
			renderRight = true;
		}

		var cal = this.parent || this;

		if (renderLeft) {
			html[html.length] = '<a class="' + this.Style.CSS_NAV_LEFT + '" style="background-image:url(' + this.cfg.getProperty("NAV_ARROW_LEFT") + ')">&#160;</a>';
		}
		
		html[html.length] = this.buildMonthLabel();
		
		if (renderRight) {
			html[html.length] = '<a class="' + this.Style.CSS_NAV_RIGHT + '" style="background-image:url(' + this.cfg.getProperty("NAV_ARROW_RIGHT") + ')">&#160;</a>';
		}


	html[html.length] =				'</div>';
	html[html.length] =			'</th>';
	html[html.length] =		'</tr>';

	if (this.cfg.getProperty("SHOW_WEEKDAYS")) {
		html = this.buildWeekdays(html);
	}
	
	html[html.length] = '</thead>';

	return html;
};

/**
* Renders the Calendar's weekday headers.
* @method buildWeekdays
* @param {Array}	html	The current working HTML array
* @return {Array} The current working HTML array
*/
YAHOO.widget.Calendar.prototype.buildWeekdays = function(html) {

	html[html.length] = '<tr class="' + this.Style.CSS_WEEKDAY_ROW + '">';

	if (this.cfg.getProperty("SHOW_WEEK_HEADER")) {
		html[html.length] = '<th>&#160;</th>';
	}

	for(var i=0;i<this.Locale.LOCALE_WEEKDAYS.length;++i) {
		html[html.length] = '<th class="calweekdaycell">' + this.Locale.LOCALE_WEEKDAYS[i] + '</th>';
	}

	if (this.cfg.getProperty("SHOW_WEEK_FOOTER")) {
		html[html.length] = '<th>&#160;</th>';
	}

	html[html.length] = '</tr>';

	return html;
};

/**
* Renders the calendar body.
* @method renderBody
* @param {Date}	workingDate	The current working Date being used for the render process
* @param {Array}	html	The current working HTML array
* @return {Array} The current working HTML array
*/
YAHOO.widget.Calendar.prototype.renderBody = function(workingDate, html) {
	this.logger.log("Rendering body", "info");
	
	var startDay = this.cfg.getProperty("START_WEEKDAY");

	this.preMonthDays = workingDate.getDay();
	if (startDay > 0) {
		this.preMonthDays -= startDay;
	}
	if (this.preMonthDays < 0) {
		this.preMonthDays += 7;
	}
	
	this.monthDays = YAHOO.widget.DateMath.findMonthEnd(workingDate).getDate();
	this.postMonthDays = YAHOO.widget.Calendar.DISPLAY_DAYS-this.preMonthDays-this.monthDays;

	this.logger.log(this.preMonthDays + " preciding out-of-month days", "info");
	this.logger.log(this.monthDays + " month days", "info");
	this.logger.log(this.postMonthDays + " post-month days", "info");
	
	workingDate = YAHOO.widget.DateMath.subtract(workingDate, YAHOO.widget.DateMath.DAY, this.preMonthDays);
	this.logger.log("Calendar page starts on " + workingDate, "info");

	var useDate,weekNum,weekClass;
	useDate = this.cfg.getProperty("pagedate");

	html[html.length] = '<tbody class="m' + (useDate.getMonth()+1) + '">';
	
	var i = 0;

	var tempDiv = document.createElement("div");
	var cell = document.createElement("td");
	tempDiv.appendChild(cell);

	var jan1 = new Date(useDate.getFullYear(),0,1);

	var cal = this.parent || this;

	for (var r=0;r<6;r++) {

		weekNum = YAHOO.widget.DateMath.getWeekNumber(workingDate, useDate.getFullYear(), startDay);

		weekClass = "w" + weekNum;

		if (r !== 0 && this.isDateOOM(workingDate) && this.cfg.getProperty("HIDE_BLANK_WEEKS") === true) {
			break;
		} else {
					
			html[html.length] = '<tr class="' + weekClass + '">';
			
			if (this.cfg.getProperty("SHOW_WEEK_HEADER")) { html = this.renderRowHeader(weekNum, html); }
			
			for (var d=0;d<7;d++){ // Render actual days

				var cellRenderers = [];

				this.clearElement(cell);
				
				YAHOO.util.Dom.addClass(cell, "calcell");

				cell.id = this.id + "_cell" + i;
				this.logger.log("Rendering cell " + cell.id + " (" + workingDate.getFullYear() + "-" + (workingDate.getMonth()+1) + "-" + workingDate.getDate() + ")", "cellrender");

				cell.innerHTML = i;

				var renderer = null;
				
				if (workingDate.getFullYear()	== this.today.getFullYear() &&
					workingDate.getMonth()		== this.today.getMonth() &&
					workingDate.getDate()		== this.today.getDate()) {
					cellRenderers[cellRenderers.length]=cal.renderCellStyleToday;
				}
				
				this.cellDates[this.cellDates.length]=[workingDate.getFullYear(),workingDate.getMonth()+1,workingDate.getDate()]; // Add this date to cellDates
							
				if (this.isDateOOM(workingDate)) {
					cellRenderers[cellRenderers.length]=cal.renderCellNotThisMonth;
				} else {

					YAHOO.util.Dom.addClass(cell, "wd" + workingDate.getDay());
					YAHOO.util.Dom.addClass(cell, "d" + workingDate.getDate());
				
					for (var s=0;s<this.renderStack.length;++s) {

						var rArray = this.renderStack[s];
						var type = rArray[0];
						
						var month;
						var day;
						var year;
						
						switch (type) {
							case YAHOO.widget.Calendar.DATE:
								month = rArray[1][1];
								day = rArray[1][2];
								year = rArray[1][0];

								if (workingDate.getMonth()+1 == month && workingDate.getDate() == day && workingDate.getFullYear() == year) {
									renderer = rArray[2];
									this.renderStack.splice(s,1);
								}
								break;
							case YAHOO.widget.Calendar.MONTH_DAY:
								month = rArray[1][0];
								day = rArray[1][1];
								
								if (workingDate.getMonth()+1 == month && workingDate.getDate() == day) {
									renderer = rArray[2];
									this.renderStack.splice(s,1);
								}
								break;
							case YAHOO.widget.Calendar.RANGE:
								var date1 = rArray[1][0];
								var date2 = rArray[1][1];

								var d1month = date1[1];
								var d1day = date1[2];
								var d1year = date1[0];
								
								var d1 = new Date(d1year, d1month-1, d1day);

								var d2month = date2[1];
								var d2day = date2[2];
								var d2year = date2[0];

								var d2 = new Date(d2year, d2month-1, d2day);

								if (workingDate.getTime() >= d1.getTime() && workingDate.getTime() <= d2.getTime()) {
									renderer = rArray[2];

									if (workingDate.getTime()==d2.getTime()) { 
										this.renderStack.splice(s,1);
									}
								}
								break;
							case YAHOO.widget.Calendar.WEEKDAY:
								
								var weekday = rArray[1][0];
								if (workingDate.getDay()+1 == weekday) {
									renderer = rArray[2];
								}
								break;
							case YAHOO.widget.Calendar.MONTH:
								
								month = rArray[1][0];
								if (workingDate.getMonth()+1 == month) {
									renderer = rArray[2];
								}
								break;
						}
						
						if (renderer) {
							cellRenderers[cellRenderers.length]=renderer;
						}
					}

				}

				if (this._indexOfSelectedFieldArray([workingDate.getFullYear(),workingDate.getMonth()+1,workingDate.getDate()]) > -1) {
					cellRenderers[cellRenderers.length]=cal.renderCellStyleSelected; 
				}

				var mindate = this.cfg.getProperty("mindate");
				var maxdate = this.cfg.getProperty("maxdate");

				if (mindate) {
					mindate = YAHOO.widget.DateMath.clearTime(mindate);
				}
				if (maxdate) {
					maxdate = YAHOO.widget.DateMath.clearTime(maxdate);
				}

				if (
					(mindate && (workingDate.getTime() < mindate.getTime())) ||
					(maxdate && (workingDate.getTime() > maxdate.getTime()))
				) {
					cellRenderers[cellRenderers.length]=cal.renderOutOfBoundsDate;
				} else {
					cellRenderers[cellRenderers.length]=cal.styleCellDefault;
					cellRenderers[cellRenderers.length]=cal.renderCellDefault;	
				}

				
				
				for (var x=0;x<cellRenderers.length;++x) {
					var ren = cellRenderers[x];
					this.logger.log("renderer[" + x + "] for (" + workingDate.getFullYear() + "-" + (workingDate.getMonth()+1) + "-" + workingDate.getDate() + ")", "cellrender");

					if (ren.call((this.parent || this),workingDate,cell) == YAHOO.widget.Calendar.STOP_RENDER) {
						break;
					}
				}

				workingDate.setTime(workingDate.getTime() + YAHOO.widget.DateMath.ONE_DAY_MS);

				if (i >= 0 && i <= 6) {
					YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_TOP);
				}
				if ((i % 7) === 0) {
					YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_LEFT);
				}
				if (((i+1) % 7) === 0) {
					YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_RIGHT);
				}
				
				var postDays = this.postMonthDays; 
				if (postDays >= 7 && this.cfg.getProperty("HIDE_BLANK_WEEKS")) {
					var blankWeeks = Math.floor(postDays/7);
					for (var p=0;p<blankWeeks;++p) {
						postDays -= 7;
					}
				}
				
				if (i >= ((this.preMonthDays+postDays+this.monthDays)-7)) {
					YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_BOTTOM);
				}

				html[html.length] = tempDiv.innerHTML;
				
				i++;
			}

			if (this.cfg.getProperty("SHOW_WEEK_FOOTER")) { html = this.renderRowFooter(weekNum, html); }

			html[html.length] = '</tr>';
		}
	}

	html[html.length] = '</tbody>';

	return html;
};

/**
* Renders the calendar footer. In the default implementation, there is
* no footer.
* @method renderFooter
* @param {Array}	html	The current working HTML array
* @return {Array} The current working HTML array
*/
YAHOO.widget.Calendar.prototype.renderFooter = function(html) { return html; };

/**
* Renders the calendar after it has been configured. The render() method has a specific call chain that will execute
* when the method is called: renderHeader, renderBody, renderFooter.
* Refer to the documentation for those methods for information on 
* individual render tasks.
* @method render
*/
YAHOO.widget.Calendar.prototype.render = function() {
	this.beforeRenderEvent.fire();

	// Find starting day of the current month
	var workingDate = YAHOO.widget.DateMath.findMonthStart(this.cfg.getProperty("pagedate"));

	this.resetRenderers();
	this.cellDates.length = 0;
	
	YAHOO.util.Event.purgeElement(this.oDomContainer, true);
	
	var html = [];

	html[html.length] = '<table cellSpacing="0" class="' + this.Style.CSS_CALENDAR + ' y' + workingDate.getFullYear() + '" id="' + this.id + '">';
	html = this.renderHeader(html);
	html = this.renderBody(workingDate, html);
	html = this.renderFooter(html);
	html[html.length] = '</table>';

	this.oDomContainer.innerHTML = html.join("\n");
	
	this.applyListeners();
	this.cells = this.oDomContainer.getElementsByTagName("td");

	this.cfg.refireEvent("title");
	this.cfg.refireEvent("close");
	this.cfg.refireEvent("iframe");

	this.renderEvent.fire();
};

/**
* Applies the Calendar's DOM listeners to applicable elements.
* @method applyListeners
*/
YAHOO.widget.Calendar.prototype.applyListeners = function() {
	
	var root = this.oDomContainer;
	var cal = this.parent || this;

	var linkLeft, linkRight;
	
	linkLeft = YAHOO.util.Dom.getElementsByClassName(this.Style.CSS_NAV_LEFT, "a", root);
	linkRight = YAHOO.util.Dom.getElementsByClassName(this.Style.CSS_NAV_RIGHT, "a", root);

	if (linkLeft) {
		this.linkLeft = linkLeft[0];
		YAHOO.util.Event.addListener(this.linkLeft, "mousedown", cal.previousMonth, cal, true);
	}

	if (linkRight) {
		this.linkRight = linkRight[0];
		YAHOO.util.Event.addListener(this.linkRight, "mousedown", cal.nextMonth, cal, true);
	}

	if (this.domEventMap) {
		var el,elements;
		for (var cls in this.domEventMap) {	
			if (this.domEventMap.hasOwnProperty(cls)) {
				var items = this.domEventMap[cls];
				
				if (! (items instanceof Array)) {
					items = [items];
				}

				for (var i=0;i<items.length;i++)	{
					var item = items[i];
					elements = YAHOO.util.Dom.getElementsByClassName(cls, item.tag, this.oDomContainer);

					for (var c=0;c<elements.length;c++) {
						el = elements[c];
						 YAHOO.util.Event.addListener(el, item.event, item.handler, item.scope, item.correct );
					}
				}
			}
		}
	}

	YAHOO.util.Event.addListener(this.oDomContainer, "click", this.doSelectCell, this);
	YAHOO.util.Event.addListener(this.oDomContainer, "mouseover", this.doCellMouseOver, this);
	YAHOO.util.Event.addListener(this.oDomContainer, "mouseout", this.doCellMouseOut, this);
};

/**
* Retrieves the Date object for the specified Calendar cell
* @method getDateByCellId
* @param {String}	id	The id of the cell
* @return {Date} The Date object for the specified Calendar cell
*/
YAHOO.widget.Calendar.prototype.getDateByCellId = function(id) {
	var date = this.getDateFieldsByCellId(id);
	return new Date(date[0],date[1]-1,date[2]);
};

/**
* Retrieves the Date object for the specified Calendar cell
* @method getDateFieldsByCellId
* @param {String}	id	The id of the cell
* @return {Array}	The array of Date fields for the specified Calendar cell
*/
YAHOO.widget.Calendar.prototype.getDateFieldsByCellId = function(id) {
	id = id.toLowerCase().split("_cell")[1];
	id = parseInt(id, 10);
	return this.cellDates[id];
};
												  
// BEGIN BUILT-IN TABLE CELL RENDERERS

/**
* Renders a cell that falls before the minimum date or after the maximum date.
* widget class.
* @method renderOutOfBoundsDate
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
* @return {String} YAHOO.widget.Calendar.STOP_RENDER if rendering should stop with this style, null or nothing if rendering
*			should not be terminated
*/
YAHOO.widget.Calendar.prototype.renderOutOfBoundsDate = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_OOB);
	cell.innerHTML = workingDate.getDate();
	return YAHOO.widget.Calendar.STOP_RENDER;
};

/**
* Renders the row header for a week.
* @method renderRowHeader
* @param {Number}	weekNum	The week number of the current row
* @param {Array}	cell	The current working HTML array
*/
YAHOO.widget.Calendar.prototype.renderRowHeader = function(weekNum, html) {
	html[html.length] = '<th class="calrowhead">' + weekNum + '</th>';
	return html;
};

/**
* Renders the row footer for a week.
* @method renderRowFooter
* @param {Number}	weekNum	The week number of the current row
* @param {Array}	cell	The current working HTML array
*/
YAHOO.widget.Calendar.prototype.renderRowFooter = function(weekNum, html) {
	html[html.length] = '<th class="calrowfoot">' + weekNum + '</th>';
	return html;
};

/**
* Renders a single standard calendar cell in the calendar widget table.
* All logic for determining how a standard default cell will be rendered is 
* encapsulated in this method, and must be accounted for when extending the
* widget class.
* @method renderCellDefault
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
*/
YAHOO.widget.Calendar.prototype.renderCellDefault = function(workingDate, cell) {
	cell.innerHTML = '<a href="javascript:void(null);" >' + this.buildDayLabel(workingDate) + "</a>";
};

/**
* Styles a selectable cell.
* @method styleCellDefault
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
*/
YAHOO.widget.Calendar.prototype.styleCellDefault = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_SELECTABLE);
};


/**
* Renders a single standard calendar cell using the CSS hightlight1 style
* @method renderCellStyleHighlight1
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
*/
YAHOO.widget.Calendar.prototype.renderCellStyleHighlight1 = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_HIGHLIGHT1);
};

/**
* Renders a single standard calendar cell using the CSS hightlight2 style
* @method renderCellStyleHighlight2
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
*/
YAHOO.widget.Calendar.prototype.renderCellStyleHighlight2 = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_HIGHLIGHT2);
};

/**
* Renders a single standard calendar cell using the CSS hightlight3 style
* @method renderCellStyleHighlight3
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
*/
YAHOO.widget.Calendar.prototype.renderCellStyleHighlight3 = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_HIGHLIGHT3);
};

/**
* Renders a single standard calendar cell using the CSS hightlight4 style
* @method renderCellStyleHighlight4
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
*/
YAHOO.widget.Calendar.prototype.renderCellStyleHighlight4 = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_HIGHLIGHT4);
};

/**
* Applies the default style used for rendering today's date to the current calendar cell
* @method renderCellStyleToday
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
*/
YAHOO.widget.Calendar.prototype.renderCellStyleToday = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_TODAY);
};

/**
* Applies the default style used for rendering selected dates to the current calendar cell
* @method renderCellStyleSelected
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
* @return {String} YAHOO.widget.Calendar.STOP_RENDER if rendering should stop with this style, null or nothing if rendering
*			should not be terminated
*/
YAHOO.widget.Calendar.prototype.renderCellStyleSelected = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_SELECTED);
};

/**
* Applies the default style used for rendering dates that are not a part of the current
* month (preceding or trailing the cells for the current month)
* @method renderCellNotThisMonth
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
* @return {String} YAHOO.widget.Calendar.STOP_RENDER if rendering should stop with this style, null or nothing if rendering
*			should not be terminated
*/
YAHOO.widget.Calendar.prototype.renderCellNotThisMonth = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_OOM);
	cell.innerHTML=workingDate.getDate();
	return YAHOO.widget.Calendar.STOP_RENDER;
};

/**
* Renders the current calendar cell as a non-selectable "black-out" date using the default
* restricted style.
* @method renderBodyCellRestricted
* @param {Date}					workingDate		The current working Date object being used to generate the calendar
* @param {HTMLTableCellElement}	cell			The current working cell in the calendar
* @return {String} YAHOO.widget.Calendar.STOP_RENDER if rendering should stop with this style, null or nothing if rendering
*			should not be terminated
*/
YAHOO.widget.Calendar.prototype.renderBodyCellRestricted = function(workingDate, cell) {
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL);
	YAHOO.util.Dom.addClass(cell, this.Style.CSS_CELL_RESTRICTED);
	cell.innerHTML=workingDate.getDate();
	return YAHOO.widget.Calendar.STOP_RENDER;
};

// END BUILT-IN TABLE CELL RENDERERS

// BEGIN MONTH NAVIGATION METHODS

/**
* Adds the designated number of months to the current calendar month, and sets the current
* calendar page date to the new month.
* @method addMonths
* @param {Number}	count	The number of months to add to the current calendar
*/
YAHOO.widget.Calendar.prototype.addMonths = function(count) {
	this.cfg.setProperty("pagedate", YAHOO.widget.DateMath.add(this.cfg.getProperty("pagedate"), YAHOO.widget.DateMath.MONTH, count));
	this.resetRenderers();
	this.changePageEvent.fire();
};

/**
* Subtracts the designated number of months from the current calendar month, and sets the current
* calendar page date to the new month.
* @method subtractMonths
* @param {Number}	count	The number of months to subtract from the current calendar
*/
YAHOO.widget.Calendar.prototype.subtractMonths = function(count) {
	this.cfg.setProperty("pagedate", YAHOO.widget.DateMath.subtract(this.cfg.getProperty("pagedate"), YAHOO.widget.DateMath.MONTH, count));
	this.resetRenderers();
	this.changePageEvent.fire();
};

/**
* Adds the designated number of years to the current calendar, and sets the current
* calendar page date to the new month.
* @method addYears
* @param {Number}	count	The number of years to add to the current calendar
*/
YAHOO.widget.Calendar.prototype.addYears = function(count) {
	this.cfg.setProperty("pagedate", YAHOO.widget.DateMath.add(this.cfg.getProperty("pagedate"), YAHOO.widget.DateMath.YEAR, count));
	this.resetRenderers();
	this.changePageEvent.fire();
};

/**
* Subtcats the designated number of years from the current calendar, and sets the current
* calendar page date to the new month.
* @method subtractYears
* @param {Number}	count	The number of years to subtract from the current calendar
*/
YAHOO.widget.Calendar.prototype.subtractYears = function(count) {
	this.cfg.setProperty("pagedate", YAHOO.widget.DateMath.subtract(this.cfg.getProperty("pagedate"), YAHOO.widget.DateMath.YEAR, count));
	this.resetRenderers();
	this.changePageEvent.fire();
};

/**
* Navigates to the next month page in the calendar widget.
* @method nextMonth
*/
YAHOO.widget.Calendar.prototype.nextMonth = function() {
	this.addMonths(1);
};

/**
* Navigates to the previous month page in the calendar widget.
* @method previousMonth
*/
YAHOO.widget.Calendar.prototype.previousMonth = function() {
	this.subtractMonths(1);
};

/**
* Navigates to the next year in the currently selected month in the calendar widget.
* @method nextYear
*/
YAHOO.widget.Calendar.prototype.nextYear = function() {
	this.addYears(1);
};

/**
* Navigates to the previous year in the currently selected month in the calendar widget.
* @method previousYear
*/
YAHOO.widget.Calendar.prototype.previousYear = function() {
	this.subtractYears(1);
};

// END MONTH NAVIGATION METHODS

// BEGIN SELECTION METHODS

/**
* Resets the calendar widget to the originally selected month and year, and 
* sets the calendar to the initial selection(s).
* @method reset
*/
YAHOO.widget.Calendar.prototype.reset = function() {
	this.cfg.resetProperty("selected");
	this.cfg.resetProperty("pagedate");
	this.resetEvent.fire();
};

/**
* Clears the selected dates in the current calendar widget and sets the calendar
* to the current month and year.
* @method clear
*/
YAHOO.widget.Calendar.prototype.clear = function() {
	this.cfg.setProperty("selected", []);
	this.cfg.setProperty("pagedate", new Date(this.today.getTime()));
	this.clearEvent.fire();
};

/**
* Selects a date or a collection of dates on the current calendar. This method, by default,
* does not call the render method explicitly. Once selection has completed, render must be 
* called for the changes to be reflected visually.
* @method select
* @param	{String/Date/Date[]}	date	The date string of dates to select in the current calendar. Valid formats are
*								individual date(s) (12/24/2005,12/26/2005) or date range(s) (12/24/2005-1/1/2006).
*								Multiple comma-delimited dates can also be passed to this method (12/24/2005,12/11/2005-12/13/2005).
*								This method can also take a JavaScript Date object or an array of Date objects.
* @return	{Date[]}			Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.Calendar.prototype.select = function(date) {
	this.logger.log("Select: " + date, "info");

	this.beforeSelectEvent.fire();

	var selected = this.cfg.getProperty("selected");
	var aToBeSelected = this._toFieldArray(date);
	this.logger.log("Selection field array: " + aToBeSelected, "info");

	for (var a=0;a<aToBeSelected.length;++a) {
		var toSelect = aToBeSelected[a]; // For each date item in the list of dates we're trying to select
		if (this._indexOfSelectedFieldArray(toSelect) == -1) { // not already selected?
			selected[selected.length]=toSelect;
		}
	}
	
	if (this.parent) {
		this.parent.cfg.setProperty("selected", selected);
	} else {
		this.cfg.setProperty("selected", selected);
	}

	this.selectEvent.fire(aToBeSelected);
	
	return this.getSelectedDates();
};

/**
* Selects a date on the current calendar by referencing the index of the cell that should be selected.
* This method is used to easily select a single cell (usually with a mouse click) without having to do
* a full render. The selected style is applied to the cell directly.
* @method selectCell
* @param	{Number}	cellIndex	The index of the cell to select in the current calendar. 
* @return	{Date[]}	Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.Calendar.prototype.selectCell = function(cellIndex) {
	this.beforeSelectEvent.fire();

	var selected = this.cfg.getProperty("selected");

	var cell = this.cells[cellIndex];
	var cellDate = this.cellDates[cellIndex];

	var dCellDate = this._toDate(cellDate);
	this.logger.log("Select: " + dCellDate, "info");

	var selectDate = cellDate.concat();

	selected[selected.length] = selectDate;

	if (this.parent) {
		this.parent.cfg.setProperty("selected", selected);
	} else {
		this.cfg.setProperty("selected", selected);
	}

	this.renderCellStyleSelected(dCellDate,cell);

	this.selectEvent.fire([selectDate]);

	this.doCellMouseOut.call(cell, null, this);

	return this.getSelectedDates();
};

/**
* Deselects a date or a collection of dates on the current calendar. This method, by default,
* does not call the render method explicitly. Once deselection has completed, render must be 
* called for the changes to be reflected visually.
* @method deselect
* @param	{String/Date/Date[]}	date	The date string of dates to deselect in the current calendar. Valid formats are
*								individual date(s) (12/24/2005,12/26/2005) or date range(s) (12/24/2005-1/1/2006).
*								Multiple comma-delimited dates can also be passed to this method (12/24/2005,12/11/2005-12/13/2005).
*								This method can also take a JavaScript Date object or an array of Date objects.	
* @return	{Date[]}			Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.Calendar.prototype.deselect = function(date) {
	this.beforeDeselectEvent.fire();

	var selected = this.cfg.getProperty("selected");

	var aToBeSelected = this._toFieldArray(date);

	for (var a=0;a<aToBeSelected.length;++a) {
		var toSelect = aToBeSelected[a]; // For each date item in the list of dates we're trying to select
		var index = this._indexOfSelectedFieldArray(toSelect);
	
		if (index != -1) {	
			selected.splice(index,1);
		}
	}

	if (this.parent) {
		this.parent.cfg.setProperty("selected", selected);
	} else {
		this.cfg.setProperty("selected", selected);
	}

	this.deselectEvent.fire(aToBeSelected);
	
	return this.getSelectedDates();
};

/**
* Deselects a date on the current calendar by referencing the index of the cell that should be deselected.
* This method is used to easily deselect a single cell (usually with a mouse click) without having to do
* a full render. The selected style is removed from the cell directly.
* @method deselectCell
* @param	{Number}	cellIndex	The index of the cell to deselect in the current calendar. 
* @return	{Date[]}	Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.Calendar.prototype.deselectCell = function(i) {
	this.beforeDeselectEvent.fire();
	
	var selected = this.cfg.getProperty("selected");

	var cell = this.cells[i];
	var cellDate = this.cellDates[i];
	var cellDateIndex = this._indexOfSelectedFieldArray(cellDate);

	var dCellDate = this._toDate(cellDate);

	var selectDate = cellDate.concat();

	if (cellDateIndex > -1) {
		if (this.cfg.getProperty("pagedate").getMonth() == dCellDate.getMonth() &&
			this.cfg.getProperty("pagedate").getFullYear() == dCellDate.getFullYear()) {
			YAHOO.util.Dom.removeClass(cell, this.Style.CSS_CELL_SELECTED);
		}

		selected.splice(cellDateIndex, 1);
	}


	if (this.parent) {
		this.parent.cfg.setProperty("selected", selected);
	} else {
		this.cfg.setProperty("selected", selected);
	}
	
	this.deselectEvent.fire(selectDate);
	return this.getSelectedDates();
};

/**
* Deselects all dates on the current calendar.
* @method deselectAll
* @return {Date[]}		Array of JavaScript Date objects representing all individual dates that are currently selected.
*						Assuming that this function executes properly, the return value should be an empty array.
*						However, the empty array is returned for the sake of being able to check the selection status
*						of the calendar.
*/
YAHOO.widget.Calendar.prototype.deselectAll = function() {
	this.beforeDeselectEvent.fire();

	var selected = this.cfg.getProperty("selected");
	var count = selected.length;
	var sel = selected.concat();

	if (this.parent) {
		this.parent.cfg.setProperty("selected", []);
	} else {
		this.cfg.setProperty("selected", []);
	}
	
	if (count > 0) {
		this.deselectEvent.fire(sel);
	}

	return this.getSelectedDates();
};

// END SELECTION METHODS

// BEGIN TYPE CONVERSION METHODS

/**
* Converts a date (either a JavaScript Date object, or a date string) to the internal data structure
* used to represent dates: [[yyyy,mm,dd],[yyyy,mm,dd]].
* @method _toFieldArray
* @private
* @param	{String/Date/Date[]}	date	The date string of dates to deselect in the current calendar. Valid formats are
*								individual date(s) (12/24/2005,12/26/2005) or date range(s) (12/24/2005-1/1/2006).
*								Multiple comma-delimited dates can also be passed to this method (12/24/2005,12/11/2005-12/13/2005).
*								This method can also take a JavaScript Date object or an array of Date objects.	
* @return {Array[](Number[])}	Array of date field arrays
*/
YAHOO.widget.Calendar.prototype._toFieldArray = function(date) {
	var returnDate = [];

	if (date instanceof Date) {
		returnDate = [[date.getFullYear(), date.getMonth()+1, date.getDate()]];
	} else if (typeof date == 'string') {
		returnDate = this._parseDates(date);
	} else if (date instanceof Array) {
		for (var i=0;i<date.length;++i) {
			var d = date[i];
			returnDate[returnDate.length] = [d.getFullYear(),d.getMonth()+1,d.getDate()];
		}
	}
	
	return returnDate;
};

/**
* Converts a date field array [yyyy,mm,dd] to a JavaScript Date object.
* @method _toDate
* @private
* @param	{Number[]}		dateFieldArray	The date field array to convert to a JavaScript Date.
* @return	{Date}	JavaScript Date object representing the date field array
*/
YAHOO.widget.Calendar.prototype._toDate = function(dateFieldArray) {
	if (dateFieldArray instanceof Date) {
		return dateFieldArray;
	} else {
		return new Date(dateFieldArray[0],dateFieldArray[1]-1,dateFieldArray[2]);
	}
};

// END TYPE CONVERSION METHODS 

// BEGIN UTILITY METHODS

/**
* Converts a date field array [yyyy,mm,dd] to a JavaScript Date object.
* @method _fieldArraysAreEqual
* @private
* @param	{Number[]}	array1	The first date field array to compare
* @param	{Number[]}	array2	The first date field array to compare
* @return	{Boolean}	The boolean that represents the equality of the two arrays
*/
YAHOO.widget.Calendar.prototype._fieldArraysAreEqual = function(array1, array2) {
	var match = false;

	if (array1[0]==array2[0]&&array1[1]==array2[1]&&array1[2]==array2[2]) {
		match=true;	
	}

	return match;
};

/**
* Gets the index of a date field array [yyyy,mm,dd] in the current list of selected dates.
* @method	_indexOfSelectedFieldArray
* @private
* @param	{Number[]}		find	The date field array to search for
* @return	{Number}			The index of the date field array within the collection of selected dates.
*								-1 will be returned if the date is not found.
*/
YAHOO.widget.Calendar.prototype._indexOfSelectedFieldArray = function(find) {
	var selected = -1;
	var seldates = this.cfg.getProperty("selected");

	for (var s=0;s<seldates.length;++s) {
		var sArray = seldates[s];
		if (find[0]==sArray[0]&&find[1]==sArray[1]&&find[2]==sArray[2]) {
			selected = s;
			break;
		}
	}

	return selected;
};

/**
* Determines whether a given date is OOM (out of month).
* @method	isDateOOM
* @param	{Date}	date	The JavaScript Date object for which to check the OOM status
* @return	{Boolean}	true if the date is OOM
*/
YAHOO.widget.Calendar.prototype.isDateOOM = function(date) {
	var isOOM = false;
	if (date.getMonth() != this.cfg.getProperty("pagedate").getMonth()) {
		isOOM = true;
	}
	return isOOM;
};

// END UTILITY METHODS

// BEGIN EVENT HANDLERS

/**
* Event executed before a date is selected in the calendar widget.
* @deprecated Event handlers for this event should be susbcribed to beforeSelectEvent.
*/
YAHOO.widget.Calendar.prototype.onBeforeSelect = function() {
	if (this.cfg.getProperty("MULTI_SELECT") === false) {
		if (this.parent) {
			this.parent.callChildFunction("clearAllBodyCellStyles", this.Style.CSS_CELL_SELECTED);
			this.parent.deselectAll();
		} else {
			this.clearAllBodyCellStyles(this.Style.CSS_CELL_SELECTED);
			this.deselectAll();
		}
	}
};

/**
* Event executed when a date is selected in the calendar widget.
* @param	{Array}	selected	An array of date field arrays representing which date or dates were selected. Example: [ [2006,8,6],[2006,8,7],[2006,8,8] ]
* @deprecated Event handlers for this event should be susbcribed to selectEvent.
*/
YAHOO.widget.Calendar.prototype.onSelect = function(selected) { };

/**
* Event executed before a date is deselected in the calendar widget.
* @deprecated Event handlers for this event should be susbcribed to beforeDeselectEvent.
*/
YAHOO.widget.Calendar.prototype.onBeforeDeselect = function() { };

/**
* Event executed when a date is deselected in the calendar widget.
* @param	{Array}	selected	An array of date field arrays representing which date or dates were deselected. Example: [ [2006,8,6],[2006,8,7],[2006,8,8] ]
* @deprecated Event handlers for this event should be susbcribed to deselectEvent.
*/
YAHOO.widget.Calendar.prototype.onDeselect = function(deselected) { };

/**
* Event executed when the user navigates to a different calendar page.
* @deprecated Event handlers for this event should be susbcribed to changePageEvent.
*/
YAHOO.widget.Calendar.prototype.onChangePage = function() {
	this.render();
};

/**
* Event executed when the calendar widget is rendered.
* @deprecated Event handlers for this event should be susbcribed to renderEvent.
*/
YAHOO.widget.Calendar.prototype.onRender = function() { };

/**
* Event executed when the calendar widget is reset to its original state.
* @deprecated Event handlers for this event should be susbcribed to resetEvemt.
*/
YAHOO.widget.Calendar.prototype.onReset = function() { this.render(); };

/**
* Event executed when the calendar widget is completely cleared to the current month with no selections.
* @deprecated Event handlers for this event should be susbcribed to clearEvent.
*/
YAHOO.widget.Calendar.prototype.onClear = function() { this.render(); };

/**
* Validates the calendar widget. This method has no default implementation
* and must be extended by subclassing the widget.
* @return	Should return true if the widget validates, and false if
* it doesn't.
* @type Boolean
*/
YAHOO.widget.Calendar.prototype.validate = function() { return true; };

// END EVENT HANDLERS

// BEGIN DATE PARSE METHODS

/**
* Converts a date string to a date field array
* @private
* @param	{String}	sDate			Date string. Valid formats are mm/dd and mm/dd/yyyy.
* @return				A date field array representing the string passed to the method
* @type Array[](Number[])
*/
YAHOO.widget.Calendar.prototype._parseDate = function(sDate) {
	var aDate = sDate.split(this.Locale.DATE_FIELD_DELIMITER);
	var rArray;

	if (aDate.length == 2) {
		rArray = [aDate[this.Locale.MD_MONTH_POSITION-1],aDate[this.Locale.MD_DAY_POSITION-1]];
		rArray.type = YAHOO.widget.Calendar.MONTH_DAY;
	} else {
		rArray = [aDate[this.Locale.MDY_YEAR_POSITION-1],aDate[this.Locale.MDY_MONTH_POSITION-1],aDate[this.Locale.MDY_DAY_POSITION-1]];
		rArray.type = YAHOO.widget.Calendar.DATE;
	}

	for (var i=0;i<rArray.length;i++) {
		rArray[i] = parseInt(rArray[i], 10);
	}

	return rArray;
};

/**
* Converts a multi or single-date string to an array of date field arrays
* @private
* @param	{String}	sDates		Date string with one or more comma-delimited dates. Valid formats are mm/dd, mm/dd/yyyy, mm/dd/yyyy-mm/dd/yyyy
* @return							An array of date field arrays
* @type Array[](Number[])
*/
YAHOO.widget.Calendar.prototype._parseDates = function(sDates) {
	var aReturn = [];

	var aDates = sDates.split(this.Locale.DATE_DELIMITER);
	
	for (var d=0;d<aDates.length;++d) {
		var sDate = aDates[d];

		if (sDate.indexOf(this.Locale.DATE_RANGE_DELIMITER) != -1) {
			// This is a range
			var aRange = sDate.split(this.Locale.DATE_RANGE_DELIMITER);

			var dateStart = this._parseDate(aRange[0]);
			var dateEnd = this._parseDate(aRange[1]);

			var fullRange = this._parseRange(dateStart, dateEnd);
			aReturn = aReturn.concat(fullRange);
		} else {
			// This is not a range
			var aDate = this._parseDate(sDate);
			aReturn.push(aDate);
		}
	}
	return aReturn;
};

/**
* Converts a date range to the full list of included dates
* @private
* @param	{Number[]}	startDate	Date field array representing the first date in the range
* @param	{Number[]}	endDate		Date field array representing the last date in the range
* @return							An array of date field arrays
* @type Array[](Number[])
*/
YAHOO.widget.Calendar.prototype._parseRange = function(startDate, endDate) {
	var dStart   = new Date(startDate[0],startDate[1]-1,startDate[2]);
	var dCurrent = YAHOO.widget.DateMath.add(new Date(startDate[0],startDate[1]-1,startDate[2]),YAHOO.widget.DateMath.DAY,1);
	var dEnd     = new Date(endDate[0],  endDate[1]-1,  endDate[2]);

	var results = [];
	results.push(startDate);
	while (dCurrent.getTime() <= dEnd.getTime()) {
		results.push([dCurrent.getFullYear(),dCurrent.getMonth()+1,dCurrent.getDate()]);
		dCurrent = YAHOO.widget.DateMath.add(dCurrent,YAHOO.widget.DateMath.DAY,1);
	}
	return results;
};

// END DATE PARSE METHODS

// BEGIN RENDERER METHODS

/**
* Resets the render stack of the current calendar to its original pre-render value.
*/
YAHOO.widget.Calendar.prototype.resetRenderers = function() {
	this.renderStack = this._renderStack.concat();
};

/**
* Clears the inner HTML, CSS class and style information from the specified cell.
* @method clearElement
* @param	{HTMLTableCellElement}	The cell to clear
*/ 
YAHOO.widget.Calendar.prototype.clearElement = function(cell) {
	cell.innerHTML = "&#160;";
	cell.className="";
};

/**
* Adds a renderer to the render stack. The function reference passed to this method will be executed
* when a date cell matches the conditions specified in the date string for this renderer.
* @method addRenderer
* @param	{String}	sDates		A date string to associate with the specified renderer. Valid formats
*									include date (12/24/2005), month/day (12/24), and range (12/1/2004-1/1/2005)
* @param	{Function}	fnRender	The function executed to render cells that match the render rules for this renderer.
*/
YAHOO.widget.Calendar.prototype.addRenderer = function(sDates, fnRender) {
	var aDates = this._parseDates(sDates);
	for (var i=0;i<aDates.length;++i) {
		var aDate = aDates[i];
	
		if (aDate.length == 2) { // this is either a range or a month/day combo
			if (aDate[0] instanceof Array) { // this is a range
				this._addRenderer(YAHOO.widget.Calendar.RANGE,aDate,fnRender);
			} else { // this is a month/day combo
				this._addRenderer(YAHOO.widget.Calendar.MONTH_DAY,aDate,fnRender);
			}
		} else if (aDate.length == 3) {
			this._addRenderer(YAHOO.widget.Calendar.DATE,aDate,fnRender);
		}
	}
};

/**
* The private method used for adding cell renderers to the local render stack.
* This method is called by other methods that set the renderer type prior to the method call.
* @method _addRenderer
* @private
* @param	{String}	type		The type string that indicates the type of date renderer being added.
*									Values are YAHOO.widget.Calendar.DATE, YAHOO.widget.Calendar.MONTH_DAY, YAHOO.widget.Calendar.WEEKDAY,
*									YAHOO.widget.Calendar.RANGE, YAHOO.widget.Calendar.MONTH
* @param	{Array}		aDates		An array of dates used to construct the renderer. The format varies based
*									on the renderer type
* @param	{Function}	fnRender	The function executed to render cells that match the render rules for this renderer.
*/
YAHOO.widget.Calendar.prototype._addRenderer = function(type, aDates, fnRender) {
	var add = [type,aDates,fnRender];
	this.renderStack.unshift(add);	
	this._renderStack = this.renderStack.concat();
};

/**
* Adds a month to the render stack. The function reference passed to this method will be executed
* when a date cell matches the month passed to this method.
* @method addMonthRenderer
* @param	{Number}	month		The month (1-12) to associate with this renderer
* @param	{Function}	fnRender	The function executed to render cells that match the render rules for this renderer.
*/
YAHOO.widget.Calendar.prototype.addMonthRenderer = function(month, fnRender) {
	this._addRenderer(YAHOO.widget.Calendar.MONTH,[month],fnRender);
};

/**
* Adds a weekday to the render stack. The function reference passed to this method will be executed
* when a date cell matches the weekday passed to this method.
* @method addWeekdayRenderer
* @param	{Number}	weekday		The weekday (0-6) to associate with this renderer
* @param	{Function}	fnRender	The function executed to render cells that match the render rules for this renderer.
*/
YAHOO.widget.Calendar.prototype.addWeekdayRenderer = function(weekday, fnRender) {
	this._addRenderer(YAHOO.widget.Calendar.WEEKDAY,[weekday],fnRender);
};

// END RENDERER METHODS

// BEGIN CSS METHODS

/**
* Removes all styles from all body cells in the current calendar table.
* @method clearAllBodyCellStyles
* @param	{style}		The CSS class name to remove from all calendar body cells
*/
YAHOO.widget.Calendar.prototype.clearAllBodyCellStyles = function(style) {
	for (var c=0;c<this.cells.length;++c) {
		YAHOO.util.Dom.removeClass(this.cells[c],style);
	}
};

// END CSS METHODS

// BEGIN GETTER/SETTER METHODS
/**
* Sets the calendar's month explicitly
* @method setMonth
* @param {Number}	month		The numeric month, from 0 (January) to 11 (December)
*/
YAHOO.widget.Calendar.prototype.setMonth = function(month) {
	var current = this.cfg.getProperty("pagedate");
	current.setMonth(parseInt(month, 10));
	this.cfg.setProperty("pagedate", current);
};

/**
* Sets the calendar's year explicitly.
* @method setYear
* @param {Number}	year		The numeric 4-digit year
*/
YAHOO.widget.Calendar.prototype.setYear = function(year) {
	var current = this.cfg.getProperty("pagedate");
	current.setFullYear(parseInt(year, 10));
	this.cfg.setProperty("pagedate", current);
};

/**
* Gets the list of currently selected dates from the calendar.
* @method getSelectedDates
* @return {Date[]} An array of currently selected JavaScript Date objects.
*/
YAHOO.widget.Calendar.prototype.getSelectedDates = function() {
	var returnDates = [];
	var selected = this.cfg.getProperty("selected");

	for (var d=0;d<selected.length;++d) {
		var dateArray = selected[d];

		var date = new Date(dateArray[0],dateArray[1]-1,dateArray[2]);
		returnDates.push(date);
	}

	returnDates.sort( function(a,b) { return a-b; } );
	return returnDates;
};

/// END GETTER/SETTER METHODS ///

/**
* Hides the Calendar's outer container from view.
* @method hide
*/
YAHOO.widget.Calendar.prototype.hide = function() {
	this.oDomContainer.style.display = "none";
};

/**
* Shows the Calendar's outer container.
* @method show
*/
YAHOO.widget.Calendar.prototype.show = function() {
	this.oDomContainer.style.display = "block";
};

/**
* Returns a string representing the current browser.
* @property browser
* @type String
*/
YAHOO.widget.Calendar.prototype.browser = function() {
			var ua = navigator.userAgent.toLowerCase();
				  if (ua.indexOf('opera')!=-1) { // Opera (check first in case of spoof)
					 return 'opera';
				  } else if (ua.indexOf('msie 7')!=-1) { // IE7
					 return 'ie7';
				  } else if (ua.indexOf('msie') !=-1) { // IE
					 return 'ie';
				  } else if (ua.indexOf('safari')!=-1) { // Safari (check before Gecko because it includes "like Gecko")
					 return 'safari';
				  } else if (ua.indexOf('gecko') != -1) { // Gecko
					 return 'gecko';
				  } else {
					 return false;
				  }
			}();
/**
* Returns a string representation of the object.
* @method toString
* @return {String}	A string representation of the Calendar object.
*/
YAHOO.widget.Calendar.prototype.toString = function() {
	return "Calendar " + this.id;
};

/**
* @namespace YAHOO.widget
* @class Calendar_Core
* @extends YAHOO.widget.Calendar
* @deprecated The old Calendar_Core class is no longer necessary.
*/
YAHOO.widget.Calendar_Core = YAHOO.widget.Calendar;

YAHOO.widget.Cal_Core = YAHOO.widget.Calendar;

/**
* YAHOO.widget.CalendarGroup is a special container class for YAHOO.widget.Calendar. This class facilitates
* the ability to have multi-page calendar views that share a single dataset and are
* dependent on each other.
* 
* The calendar group instance will refer to each of its elements using a 0-based index.
* For example, to construct the placeholder for a calendar group widget with id "cal1" and
* containerId of "cal1Container", the markup would be as follows:
*	<xmp>
*		<div id="cal1Container_0"></div>
*		<div id="cal1Container_1"></div>
*	</xmp>
* The tables for the calendars ("cal1_0" and "cal1_1") will be inserted into those containers.
* @namespace YAHOO.widget
* @class CalendarGroup
* @constructor
* @param {String}	id			The id of the table element that will represent the calendar widget
* @param {String}	containerId	The id of the container div element that will wrap the calendar table
* @param {Object}	config		The configuration object containing the Calendar's arguments
*/
YAHOO.widget.CalendarGroup = function(id, containerId, config) {
	if (arguments.length > 0) {
		this.init(id, containerId, config);
	}
};

/**
* Initializes the calendar group. All subclasses must call this method in order for the
* group to be initialized properly.
* @method init
* @param {String}	id			The id of the table element that will represent the calendar widget
* @param {String}	containerId	The id of the container div element that will wrap the calendar table
* @param {Object}	config		The configuration object containing the Calendar's arguments
*/
YAHOO.widget.CalendarGroup.prototype.init = function(id, containerId, config) {
	this.logger = new YAHOO.widget.LogWriter("CalendarGroup " + id);

	this.initEvents();
	this.initStyles();

	/**
	* The collection of Calendar pages contained within the CalendarGroup
	* @property pages
	* @type YAHOO.widget.Calendar[]
	*/
	this.pages = [];
	
	/**
	* The unique id associated with the CalendarGroup
	* @property id
	* @type String
	*/
	this.id = id;

	/**
	* The unique id associated with the CalendarGroup container
	* @property containerId
	* @type String
	*/
	this.containerId = containerId;

	/**
	* The outer containing element for the CalendarGroup
	* @property oDomContainer
	* @type HTMLElement
	*/
	this.oDomContainer = document.getElementById(containerId);

	YAHOO.util.Dom.addClass(this.oDomContainer, YAHOO.widget.CalendarGroup.CSS_CONTAINER);
	YAHOO.util.Dom.addClass(this.oDomContainer, YAHOO.widget.CalendarGroup.CSS_MULTI_UP);

	/**
	* The Config object used to hold the configuration variables for the CalendarGroup
	* @property cfg
	* @type YAHOO.util.Config
	*/
	this.cfg = new YAHOO.util.Config(this);

	/**
	* The local object which contains the CalendarGroup's options
	* @property Options
	* @type Object
	*/
	this.Options = {};

	/**
	* The local object which contains the CalendarGroup's locale settings
	* @property Locale
	* @type Object
	*/
	this.Locale = {};

	this.setupConfig();

	if (config) {
		this.cfg.applyConfig(config, true);
	}

	this.cfg.fireQueue();

	// OPERA HACK FOR MISWRAPPED FLOATS
	if (this.browser == "opera"){
		var fixWidth = function() {
			var startW = this.oDomContainer.offsetWidth;
			var w = 0;
			for (var p=0;p<this.pages.length;++p) {
				var cal = this.pages[p];
				w += cal.oDomContainer.offsetWidth;
			}
			if (w > 0) {
				this.oDomContainer.style.width = w + "px";
			}
		};
		this.renderEvent.subscribe(fixWidth,this,true);
	}
	
	this.logger.log("Initialized " + pageCount + "-page CalendarGroup", "info");
};


YAHOO.widget.CalendarGroup.prototype.setupConfig = function() {
	/**
	* The number of pages to include in the CalendarGroup. This value can only be set once, in the CalendarGroup's constructor arguments.
	* @config pages
	* @type Number
	* @default 2
	*/
	this.cfg.addProperty("pages", { value:2, validator:this.cfg.checkNumber, handler:this.configPages } );

	/**
	* The month/year representing the current visible Calendar date (mm/yyyy)
	* @config pagedate
	* @type String
	* @default today's date
	*/
	this.cfg.addProperty("pagedate", { value:new Date(), handler:this.configPageDate } );

	/**
	* The date or range of dates representing the current Calendar selection
	* @config selected
	* @type String
	* @default []
	*/
	this.cfg.addProperty("selected", { value:[], handler:this.delegateConfig } );

	/**
	* The title to display above the CalendarGroup's month header
	* @config title
	* @type String
	* @default ""
	*/
	this.cfg.addProperty("title", { value:"", handler:this.configTitle } );

	/**
	* Whether or not a close button should be displayed for this CalendarGroup
	* @config close
	* @type Boolean
	* @default false
	*/
	this.cfg.addProperty("close", { value:false, handler:this.configClose } );

	/**
	* Whether or not an iframe shim should be placed under the Calendar to prevent select boxes from bleeding through in Internet Explorer 6 and below.
	* @config iframe
	* @type Boolean
	* @default true
	*/
	this.cfg.addProperty("iframe", { value:true, handler:this.delegateConfig, validator:this.cfg.checkBoolean } );

	/**
	* The minimum selectable date in the current Calendar (mm/dd/yyyy)
	* @config mindate
	* @type String
	* @default null
	*/
	this.cfg.addProperty("mindate", { value:null, handler:this.delegateConfig } );

	/**
	* The maximum selectable date in the current Calendar (mm/dd/yyyy)
	* @config maxdate
	* @type String
	* @default null
	*/	
	this.cfg.addProperty("maxdate", { value:null, handler:this.delegateConfig  } );

	// Options properties

	/**
	* True if the Calendar should allow multiple selections. False by default.
	* @config MULTI_SELECT
	* @type Boolean
	* @default false
	*/
	this.cfg.addProperty("MULTI_SELECT",	{ value:false, handler:this.delegateConfig, validator:this.cfg.checkBoolean } );

	/**
	* The weekday the week begins on. Default is 0 (Sunday).
	* @config START_WEEKDAY
	* @type number
	* @default 0
	*/	
	this.cfg.addProperty("START_WEEKDAY",	{ value:0, handler:this.delegateConfig, validator:this.cfg.checkNumber  } );
	
	/**
	* True if the Calendar should show weekday labels. True by default.
	* @config SHOW_WEEKDAYS
	* @type Boolean
	* @default true
	*/	
	this.cfg.addProperty("SHOW_WEEKDAYS",	{ value:true, handler:this.delegateConfig, validator:this.cfg.checkBoolean } );
	
	/**
	* True if the Calendar should show week row headers. False by default.
	* @config SHOW_WEEK_HEADER
	* @type Boolean
	* @default false
	*/	
	this.cfg.addProperty("SHOW_WEEK_HEADER",{ value:false, handler:this.delegateConfig, validator:this.cfg.checkBoolean } );
	
	/**
	* True if the Calendar should show week row footers. False by default.
	* @config SHOW_WEEK_FOOTER
	* @type Boolean
	* @default false
	*/
	this.cfg.addProperty("SHOW_WEEK_FOOTER",{ value:false, handler:this.delegateConfig, validator:this.cfg.checkBoolean } );
	
	/**
	* True if the Calendar should suppress weeks that are not a part of the current month. False by default.
	* @config HIDE_BLANK_WEEKS
	* @type Boolean
	* @default false
	*/		
	this.cfg.addProperty("HIDE_BLANK_WEEKS",{ value:false, handler:this.delegateConfig, validator:this.cfg.checkBoolean } );
	
	/**
	* The image that should be used for the left navigation arrow.
	* @config NAV_ARROW_LEFT
	* @type String
	* @default YAHOO.widget.Calendar.IMG_ROOT + "us/tr/callt.gif"
	*/		
	this.cfg.addProperty("NAV_ARROW_LEFT",	{ value:YAHOO.widget.Calendar.IMG_ROOT + "us/tr/callt.gif", handler:this.delegateConfig } );
	
	/**
	* The image that should be used for the left navigation arrow.
	* @config NAV_ARROW_RIGHT
	* @type String
	* @default YAHOO.widget.Calendar.IMG_ROOT + "us/tr/calrt.gif"
	*/		
	this.cfg.addProperty("NAV_ARROW_RIGHT",	{ value:YAHOO.widget.Calendar.IMG_ROOT + "us/tr/calrt.gif", handler:this.delegateConfig } );

	// Locale properties
	
	/**
	* The short month labels for the current locale.
	* @config MONTHS_SHORT
	* @type String[]
	* @default ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
	*/
	this.cfg.addProperty("MONTHS_SHORT",	{ value:["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], handler:this.delegateConfig } );
	
	/**
	* The long month labels for the current locale.
	* @config MONTHS_LONG
	* @type String[]
	* @default ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	*/		
	this.cfg.addProperty("MONTHS_LONG",		{ value:["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], handler:this.delegateConfig } );
	
	/**
	* The 1-character weekday labels for the current locale.
	* @config WEEKDAYS_1CHAR
	* @type String[]
	* @default ["S", "M", "T", "W", "T", "F", "S"]
	*/		
	this.cfg.addProperty("WEEKDAYS_1CHAR",	{ value:["S", "M", "T", "W", "T", "F", "S"], handler:this.delegateConfig } );
	
	/**
	* The short weekday labels for the current locale.
	* @config WEEKDAYS_SHORT
	* @type String[]
	* @default ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"]
	*/		
	this.cfg.addProperty("WEEKDAYS_SHORT",	{ value:["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"], handler:this.delegateConfig } );
	
	/**
	* The medium weekday labels for the current locale.
	* @config WEEKDAYS_MEDIUM
	* @type String[]
	* @default ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
	*/		
	this.cfg.addProperty("WEEKDAYS_MEDIUM",	{ value:["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], handler:this.delegateConfig } );
	
	/**
	* The long weekday labels for the current locale.
	* @config WEEKDAYS_LONG
	* @type String[]
	* @default ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
	*/		
	this.cfg.addProperty("WEEKDAYS_LONG",	{ value:["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], handler:this.delegateConfig } );

	/**
	* The setting that determines which length of month labels should be used. Possible values are "short" and "long".
	* @config LOCALE_MONTHS
	* @type String
	* @default "long"
	*/
	this.cfg.addProperty("LOCALE_MONTHS",	{ value:"long", handler:this.delegateConfig } );

	/**
	* The setting that determines which length of weekday labels should be used. Possible values are "1char", "short", "medium", and "long".
	* @config LOCALE_WEEKDAYS
	* @type String
	* @default "short"
	*/	
	this.cfg.addProperty("LOCALE_WEEKDAYS",	{ value:"short", handler:this.delegateConfig } );

	/**
	* The value used to delimit individual dates in a date string passed to various Calendar functions.
	* @config DATE_DELIMITER
	* @type String
	* @default ","
	*/
	this.cfg.addProperty("DATE_DELIMITER",		{ value:",", handler:this.delegateConfig } );

	/**
	* The value used to delimit date fields in a date string passed to various Calendar functions.
	* @config DATE_FIELD_DELIMITER
	* @type String
	* @default "/"
	*/	
	this.cfg.addProperty("DATE_FIELD_DELIMITER",{ value:"/", handler:this.delegateConfig } );

	/**
	* The value used to delimit date ranges in a date string passed to various Calendar functions.
	* @config DATE_RANGE_DELIMITER
	* @type String
	* @default "-"
	*/
	this.cfg.addProperty("DATE_RANGE_DELIMITER",{ value:"-", handler:this.delegateConfig } );

	/**
	* The position of the month in a month/year date string
	* @config MY_MONTH_POSITION
	* @type Number
	* @default 1
	*/
	this.cfg.addProperty("MY_MONTH_POSITION",	{ value:1, handler:this.delegateConfig, validator:this.cfg.checkNumber } );
	
	/**
	* The position of the year in a month/year date string
	* @config MY_YEAR_POSITION
	* @type Number
	* @default 2
	*/	
	this.cfg.addProperty("MY_YEAR_POSITION",	{ value:2, handler:this.delegateConfig, validator:this.cfg.checkNumber } );
	
	/**
	* The position of the month in a month/day date string
	* @config MD_MONTH_POSITION
	* @type Number
	* @default 1
	*/	
	this.cfg.addProperty("MD_MONTH_POSITION",	{ value:1, handler:this.delegateConfig, validator:this.cfg.checkNumber } );
	
	/**
	* The position of the day in a month/year date string
	* @config MD_DAY_POSITION
	* @type Number
	* @default 2
	*/	
	this.cfg.addProperty("MD_DAY_POSITION",		{ value:2, handler:this.delegateConfig, validator:this.cfg.checkNumber } );
	
	/**
	* The position of the month in a month/day/year date string
	* @config MDY_MONTH_POSITION
	* @type Number
	* @default 1
	*/	
	this.cfg.addProperty("MDY_MONTH_POSITION",	{ value:1, handler:this.delegateConfig, validator:this.cfg.checkNumber } );
	
	/**
	* The position of the day in a month/day/year date string
	* @config MDY_DAY_POSITION
	* @type Number
	* @default 2
	*/	
	this.cfg.addProperty("MDY_DAY_POSITION",	{ value:2, handler:this.delegateConfig, validator:this.cfg.checkNumber } );
	
	/**
	* The position of the year in a month/day/year date string
	* @config MDY_YEAR_POSITION
	* @type Number
	* @default 3
	*/	
	this.cfg.addProperty("MDY_YEAR_POSITION",	{ value:3, handler:this.delegateConfig, validator:this.cfg.checkNumber } );

};

/**
* Initializes CalendarGroup's built-in CustomEvents
* @method initEvents
*/
YAHOO.widget.CalendarGroup.prototype.initEvents = function() {
	var me = this;

	/**
	* Proxy subscriber to subscribe to the CalendarGroup's child Calendars' CustomEvents
	* @method sub
	* @private
	* @param {Function} fn	The function to subscribe to this CustomEvent
	* @param {Object}	obj	The CustomEvent's scope object
	* @param {Boolean}	bOverride	Whether or not to apply scope correction
	*/
	var sub = function(fn, obj, bOverride) {
		for (var p=0;p<me.pages.length;++p) {
			var cal = me.pages[p];
			cal[this.type + "Event"].subscribe(fn, obj, bOverride);
		}
	};

	/**
	* Proxy unsubscriber to unsubscribe from the CalendarGroup's child Calendars' CustomEvents
	* @method unsub
	* @private
	* @param {Function} fn	The function to subscribe to this CustomEvent
	* @param {Object}	obj	The CustomEvent's scope object
	*/
	var unsub = function(fn, obj) {
		for (var p=0;p<me.pages.length;++p) {
			var cal = me.pages[p];
			cal[this.type + "Event"].unsubscribe(fn, obj);
		}
	};

	/**
	* Fired before a selection is made
	* @event beforeSelectEvent
	*/
	this.beforeSelectEvent = new YAHOO.util.CustomEvent("beforeSelect");
	this.beforeSelectEvent.subscribe = sub; this.beforeSelectEvent.unsubscribe = unsub;

	/**
	* Fired when a selection is made
	* @event selectEvent
	* @param {Array}	Array of Date field arrays in the format [YYYY, MM, DD].
	*/
	this.selectEvent = new YAHOO.util.CustomEvent("select"); 
	this.selectEvent.subscribe = sub; this.selectEvent.unsubscribe = unsub;

	/**
	* Fired before a selection is made
	* @event beforeDeselectEvent
	*/
	this.beforeDeselectEvent = new YAHOO.util.CustomEvent("beforeDeselect"); 
	this.beforeDeselectEvent.subscribe = sub; this.beforeDeselectEvent.unsubscribe = unsub;

	/**
	* Fired when a selection is made
	* @event deselectEvent
	* @param {Array}	Array of Date field arrays in the format [YYYY, MM, DD].
	*/
	this.deselectEvent = new YAHOO.util.CustomEvent("deselect"); 
	this.deselectEvent.subscribe = sub; this.deselectEvent.unsubscribe = unsub;
	
	/**
	* Fired when the Calendar page is changed
	* @event changePageEvent
	*/
	this.changePageEvent = new YAHOO.util.CustomEvent("changePage"); 
	this.changePageEvent.subscribe = sub; this.changePageEvent.unsubscribe = unsub;

	/**
	* Fired before the Calendar is rendered
	* @event beforeRenderEvent
	*/
	this.beforeRenderEvent = new YAHOO.util.CustomEvent("beforeRender");
	this.beforeRenderEvent.subscribe = sub; this.beforeRenderEvent.unsubscribe = unsub;

	/**
	* Fired when the Calendar is rendered
	* @event renderEvent
	*/
	this.renderEvent = new YAHOO.util.CustomEvent("render");
	this.renderEvent.subscribe = sub; this.renderEvent.unsubscribe = unsub;

	/**
	* Fired when the Calendar is reset
	* @event resetEvent
	*/
	this.resetEvent = new YAHOO.util.CustomEvent("reset"); 
	this.resetEvent.subscribe = sub; this.resetEvent.unsubscribe = unsub;

	/**
	* Fired when the Calendar is cleared
	* @event clearEvent
	*/
	this.clearEvent = new YAHOO.util.CustomEvent("clear");
	this.clearEvent.subscribe = sub; this.clearEvent.unsubscribe = unsub;

};

/**
* The default Config handler for the "pages" property
* @method configPages
* @param {String} type	The CustomEvent type (usually the property name)
* @param {Object[]}	args	The CustomEvent arguments. For configuration handlers, args[0] will equal the newly applied value for the property.
* @param {Object} obj	The scope object. For configuration handlers, this will usually equal the owner.
*/
YAHOO.widget.CalendarGroup.prototype.configPages = function(type, args, obj) {
	var pageCount = args[0];

	for (var p=0;p<pageCount;++p) {
		var calId = this.id + "_" + p;
		var calContainerId = this.containerId + "_" + p;

		var childConfig = this.cfg.getConfig();
		childConfig.close = false;
		childConfig.title = false;

		var cal = this.constructChild(calId, calContainerId, childConfig);
		var caldate = cal.cfg.getProperty("pagedate");
		caldate.setMonth(caldate.getMonth()+p);
		cal.cfg.setProperty("pagedate", caldate);
		
		YAHOO.util.Dom.removeClass(cal.oDomContainer, this.Style.CSS_SINGLE);
		YAHOO.util.Dom.addClass(cal.oDomContainer, "groupcal");
		
		if (p===0) {
			YAHOO.util.Dom.addClass(cal.oDomContainer, "first");
		}

		if (p==(pageCount-1)) {
			YAHOO.util.Dom.addClass(cal.oDomContainer, "last");
		}
		
		cal.parent = this;
		cal.index = p; 

		this.pages[this.pages.length] = cal;
	}
};

/**
* The default Config handler for the "pagedate" property
* @method configPageDate
* @param {String} type	The CustomEvent type (usually the property name)
* @param {Object[]}	args	The CustomEvent arguments. For configuration handlers, args[0] will equal the newly applied value for the property.
* @param {Object} obj	The scope object. For configuration handlers, this will usually equal the owner.
*/
YAHOO.widget.CalendarGroup.prototype.configPageDate = function(type, args, obj) {
	var val = args[0];

	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.cfg.setProperty("pagedate", val);
		var calDate = cal.cfg.getProperty("pagedate");
		calDate.setMonth(calDate.getMonth()+p);
	}
};

/**
* Delegates a configuration property to the CustomEvents associated with the CalendarGroup's children
* @method delegateConfig
* @param {String} type	The CustomEvent type (usually the property name)
* @param {Object[]}	args	The CustomEvent arguments. For configuration handlers, args[0] will equal the newly applied value for the property.
* @param {Object} obj	The scope object. For configuration handlers, this will usually equal the owner.
*/
YAHOO.widget.CalendarGroup.prototype.delegateConfig = function(type, args, obj) {
	var val = args[0];
	var cal;

	for (var p=0;p<this.pages.length;p++) {
		cal = this.pages[p];
		cal.cfg.setProperty(type, val);
	}
};


/**
* Adds a function to all child Calendars within this CalendarGroup.
* @method setChildFunction
* @param {String}		fnName		The name of the function
* @param {Function}		fn			The function to apply to each Calendar page object
*/
YAHOO.widget.CalendarGroup.prototype.setChildFunction = function(fnName, fn) {
	var pageCount = this.cfg.getProperty("pages");

	for (var p=0;p<pageCount;++p) {
		this.pages[p][fnName] = fn;
	}
};

/**
* Calls a function within all child Calendars within this CalendarGroup.
* @method callChildFunction
* @param {String}		fnName		The name of the function
* @param {Array}		args		The arguments to pass to the function
*/
YAHOO.widget.CalendarGroup.prototype.callChildFunction = function(fnName, args) {
	var pageCount = this.cfg.getProperty("pages");

	for (var p=0;p<pageCount;++p) {
		var page = this.pages[p];
		if (page[fnName]) {
			var fn = page[fnName];
			fn.call(page, args);
		}
	}	
};

/**
* Constructs a child calendar. This method can be overridden if a subclassed version of the default
* calendar is to be used.
* @method constructChild
* @param {String}	id			The id of the table element that will represent the calendar widget
* @param {String}	containerId	The id of the container div element that will wrap the calendar table
* @param {Object}	config		The configuration object containing the Calendar's arguments
* @return {YAHOO.widget.Calendar}	The YAHOO.widget.Calendar instance that is constructed
*/
YAHOO.widget.CalendarGroup.prototype.constructChild = function(id,containerId,config) {
	var container = document.getElementById(containerId);
	if (! container) {
		container = document.createElement("div");
		container.id = containerId;
		this.oDomContainer.appendChild(container);
	}
	return new YAHOO.widget.Calendar(id,containerId,config);
};


/**
* Sets the calendar group's month explicitly. This month will be set into the first
* page of the multi-page calendar, and all other months will be iterated appropriately.
* @method setMonth
* @param {Number}	month		The numeric month, from 0 (January) to 11 (December)
*/
YAHOO.widget.CalendarGroup.prototype.setMonth = function(month) {
	month = parseInt(month, 10);

	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.setMonth(month+p);
	}
};

/**
* Sets the calendar group's year explicitly. This year will be set into the first
* page of the multi-page calendar, and all other months will be iterated appropriately.
* @method setYear
* @param {Number}	year		The numeric 4-digit year
*/
YAHOO.widget.CalendarGroup.prototype.setYear = function(year) {
	year = parseInt(year, 10);
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		var pageDate = cal.cfg.getProperty("pageDate");

		if ((pageDate.getMonth()+1) == 1 && p>0) {
			year+=1;
		}
		cal.setYear(year);
	}
};
/**
* Calls the render function of all child calendars within the group.
* @method render
*/
YAHOO.widget.CalendarGroup.prototype.render = function() {
	this.renderHeader();
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.render();
	}
	this.renderFooter();
};

/**
* Selects a date or a collection of dates on the current calendar. This method, by default,
* does not call the render method explicitly. Once selection has completed, render must be 
* called for the changes to be reflected visually.
* @method select
* @param	{String/Date/Date[]}	date	The date string of dates to select in the current calendar. Valid formats are
*								individual date(s) (12/24/2005,12/26/2005) or date range(s) (12/24/2005-1/1/2006).
*								Multiple comma-delimited dates can also be passed to this method (12/24/2005,12/11/2005-12/13/2005).
*								This method can also take a JavaScript Date object or an array of Date objects.
* @return	{Date[]}			Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.CalendarGroup.prototype.select = function(date) {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.select(date);
	}
	return this.getSelectedDates();
};

/**
* Selects a date on the current calendar by referencing the index of the cell that should be selected.
* This method is used to easily select a single cell (usually with a mouse click) without having to do
* a full render. The selected style is applied to the cell directly.
* @method selectCell
* @param	{Number}	cellIndex	The index of the cell to select in the current calendar. 
* @return	{Date[]}	Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.CalendarGroup.prototype.selectCell = function(cellIndex) {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.selectCell(cellIndex);
	}
	return this.getSelectedDates();
};

/**
* Deselects a date or a collection of dates on the current calendar. This method, by default,
* does not call the render method explicitly. Once deselection has completed, render must be 
* called for the changes to be reflected visually.
* @method deselect
* @param	{String/Date/Date[]}	date	The date string of dates to deselect in the current calendar. Valid formats are
*								individual date(s) (12/24/2005,12/26/2005) or date range(s) (12/24/2005-1/1/2006).
*								Multiple comma-delimited dates can also be passed to this method (12/24/2005,12/11/2005-12/13/2005).
*								This method can also take a JavaScript Date object or an array of Date objects.	
* @return	{Date[]}			Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.CalendarGroup.prototype.deselect = function(date) {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.deselect(date);
	}
	return this.getSelectedDates();
};

/**
* Deselects all dates on the current calendar.
* @method deselectAll
* @return {Date[]}		Array of JavaScript Date objects representing all individual dates that are currently selected.
*						Assuming that this function executes properly, the return value should be an empty array.
*						However, the empty array is returned for the sake of being able to check the selection status
*						of the calendar.
*/
YAHOO.widget.CalendarGroup.prototype.deselectAll = function() {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.deselectAll();
	}
	return this.getSelectedDates();
};

/**
* Deselects a date on the current calendar by referencing the index of the cell that should be deselected.
* This method is used to easily deselect a single cell (usually with a mouse click) without having to do
* a full render. The selected style is removed from the cell directly.
* @method deselectCell
* @param	{Number}	cellIndex	The index of the cell to deselect in the current calendar. 
* @return	{Date[]}	Array of JavaScript Date objects representing all individual dates that are currently selected.
*/
YAHOO.widget.CalendarGroup.prototype.deselectCell = function(cellIndex) {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.deselectCell(cellIndex);
	}
	return this.getSelectedDates();
};

/**
* Resets the calendar widget to the originally selected month and year, and 
* sets the calendar to the initial selection(s).
* @method reset
*/
YAHOO.widget.CalendarGroup.prototype.reset = function() {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.reset();
	}
};

/**
* Clears the selected dates in the current calendar widget and sets the calendar
* to the current month and year.
* @method clear
*/
YAHOO.widget.CalendarGroup.prototype.clear = function() {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.clear();
	}
};

/**
* Navigates to the next month page in the calendar widget.
* @method nextMonth
*/
YAHOO.widget.CalendarGroup.prototype.nextMonth = function() {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.nextMonth();
	}
};

/**
* Navigates to the previous month page in the calendar widget.
* @method previousMonth
*/
YAHOO.widget.CalendarGroup.prototype.previousMonth = function() {
	for (var p=this.pages.length-1;p>=0;--p) {
		var cal = this.pages[p];
		cal.previousMonth();
	}
};

/**
* Navigates to the next year in the currently selected month in the calendar widget.
* @method nextYear
*/
YAHOO.widget.CalendarGroup.prototype.nextYear = function() {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.nextYear();
	}
};

/**
* Navigates to the previous year in the currently selected month in the calendar widget.
* @method previousYear
*/
YAHOO.widget.CalendarGroup.prototype.previousYear = function() {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.previousYear();
	}
};


/**
* Gets the list of currently selected dates from the calendar.
* @return			An array of currently selected JavaScript Date objects.
* @type Date[]
*/
YAHOO.widget.CalendarGroup.prototype.getSelectedDates = function() { 
	var returnDates = [];
	var selected = this.cfg.getProperty("selected");

	for (var d=0;d<selected.length;++d) {
		var dateArray = selected[d];

		var date = new Date(dateArray[0],dateArray[1]-1,dateArray[2]);
		returnDates.push(date);
	}

	returnDates.sort( function(a,b) { return a-b; } );
	return returnDates;
};

/**
* Adds a renderer to the render stack. The function reference passed to this method will be executed
* when a date cell matches the conditions specified in the date string for this renderer.
* @method addRenderer
* @param	{String}	sDates		A date string to associate with the specified renderer. Valid formats
*									include date (12/24/2005), month/day (12/24), and range (12/1/2004-1/1/2005)
* @param	{Function}	fnRender	The function executed to render cells that match the render rules for this renderer.
*/
YAHOO.widget.CalendarGroup.prototype.addRenderer = function(sDates, fnRender) {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.addRenderer(sDates, fnRender);
	}
};

/**
* Adds a month to the render stack. The function reference passed to this method will be executed
* when a date cell matches the month passed to this method.
* @method addMonthRenderer
* @param	{Number}	month		The month (1-12) to associate with this renderer
* @param	{Function}	fnRender	The function executed to render cells that match the render rules for this renderer.
*/
YAHOO.widget.CalendarGroup.prototype.addMonthRenderer = function(month, fnRender) {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.addMonthRenderer(month, fnRender);
	}
};

/**
* Adds a weekday to the render stack. The function reference passed to this method will be executed
* when a date cell matches the weekday passed to this method.
* @method addWeekdayRenderer
* @param	{Number}	weekday		The weekday (0-6) to associate with this renderer
* @param	{Function}	fnRender	The function executed to render cells that match the render rules for this renderer.
*/
YAHOO.widget.CalendarGroup.prototype.addWeekdayRenderer = function(weekday, fnRender) {
	for (var p=0;p<this.pages.length;++p) {
		var cal = this.pages[p];
		cal.addWeekdayRenderer(weekday, fnRender);
	}
};

/**
* Renders the header for the CalendarGroup.
* @method renderHeader
*/
YAHOO.widget.CalendarGroup.prototype.renderHeader = function() {};

/**
* Renders a footer for the 2-up calendar container. By default, this method is
* unimplemented.
* @method renderFooter
*/
YAHOO.widget.CalendarGroup.prototype.renderFooter = function() {};

/**
* Adds the designated number of months to the current calendar month, and sets the current
* calendar page date to the new month.
* @method addMonths
* @param {Number}	count	The number of months to add to the current calendar
*/
YAHOO.widget.CalendarGroup.prototype.addMonths = function(count) {
	this.callChildFunction("addMonths", count);
};


/**
* Subtracts the designated number of months from the current calendar month, and sets the current
* calendar page date to the new month.
* @method subtractMonths
* @param {Number}	count	The number of months to subtract from the current calendar
*/
YAHOO.widget.CalendarGroup.prototype.subtractMonths = function(count) {
	this.callChildFunction("subtractMonths", count);
};

/**
* Adds the designated number of years to the current calendar, and sets the current
* calendar page date to the new month.
* @method addYears
* @param {Number}	count	The number of years to add to the current calendar
*/
YAHOO.widget.CalendarGroup.prototype.addYears = function(count) {
	this.callChildFunction("addYears", count);
};

/**
* Subtcats the designated number of years from the current calendar, and sets the current
* calendar page date to the new month.
* @method subtractYears
* @param {Number}	count	The number of years to subtract from the current calendar
*/
YAHOO.widget.CalendarGroup.prototype.subtractYears = function(count) {
	this.callChildFunction("subtractYears", count);
};

/**
* CSS class representing the container for the calendar
* @property YAHOO.widget.CalendarGroup.CSS_CONTAINER
* @static
* @final
* @type String
*/
YAHOO.widget.CalendarGroup.CSS_CONTAINER = "yui-calcontainer";

/**
* CSS class representing the container for the calendar
* @property YAHOO.widget.CalendarGroup.CSS_MULTI_UP
* @static
* @final
* @type String
*/
YAHOO.widget.CalendarGroup.CSS_MULTI_UP = "multi";

/**
* CSS class representing the title for the 2-up calendar
* @property YAHOO.widget.CalendarGroup.CSS_2UPTITLE
* @static
* @final
* @type String
*/
YAHOO.widget.CalendarGroup.CSS_2UPTITLE = "title";

/**
* CSS class representing the close icon for the 2-up calendar
* @property YAHOO.widget.CalendarGroup.CSS_2UPCLOSE
* @static
* @final
* @type String
*/
YAHOO.widget.CalendarGroup.CSS_2UPCLOSE = "close-icon";

YAHOO.augment(YAHOO.widget.CalendarGroup, YAHOO.widget.Calendar, "buildDayLabel",
																 "buildMonthLabel",
																 "renderOutOfBoundsDate",
																 "renderRowHeader",
																 "renderRowFooter",
																 "renderCellDefault",
																 "styleCellDefault",
																 "renderCellStyleHighlight1",
																 "renderCellStyleHighlight2",
																 "renderCellStyleHighlight3",
																 "renderCellStyleHighlight4",
																 "renderCellStyleToday",
																 "renderCellStyleSelected",
																 "renderCellNotThisMonth",
																 "renderBodyCellRestricted",
																 "initStyles",
																 "configTitle",
																 "configClose",
																 "hide",
																 "show",
																 "browser");

/**
* Returns a string representation of the object.
* @method toString
* @return {String}	A string representation of the CalendarGroup object.
*/
YAHOO.widget.CalendarGroup.prototype.toString = function() {
	return "CalendarGroup " + this.id;
};

YAHOO.widget.CalGrp = YAHOO.widget.CalendarGroup;

/**
* @class YAHOO.widget.Calendar2up
* @extends YAHOO.widget.CalendarGroup
* @deprecated The old Calendar2up class is no longer necessary, since CalendarGroup renders in a 2up view by default.
*/
YAHOO.widget.Calendar2up = function(id, containerId, config) {
	this.init(id, containerId, config);
};

YAHOO.extend(YAHOO.widget.Calendar2up, YAHOO.widget.CalendarGroup);

/**
* @deprecated The old Calendar2up class is no longer necessary, since CalendarGroup renders in a 2up view by default.
*/
YAHOO.widget.Cal2up = YAHOO.widget.Calendar2up;