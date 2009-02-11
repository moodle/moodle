package flare.vis.data
{
	import flare.animate.Transitioner;
	import flare.util.Arrays;
	import flare.util.Property;
	import flare.util.Sort;
	import flare.util.Stats;
	import flare.vis.scale.Scale;
	import flare.vis.scale.ScaleType;
	import flare.vis.scale.Scales;
	
	import flash.utils.flash_proxy;
	import flash.utils.Dictionary;
	import flash.utils.Proxy;

	/**
	 * A list of nodes or edges maintained by a Data instance. Items contained
	 * in this list can be accessed using array notation (<code>[]</code>),
	 * iterated over using the <code>for each</code> construct, or can be
	 * processed by passing a visitor function to the <code>visit</code>
	 * method.
	 */
	public class DataList extends Proxy
	{
		/** Hashed set of items in the data list. */
		private var _map:Dictionary = new Dictionary();
		/** Array of items in the data set. */
		private var _list:Array = [];
		/** Default property values to be applied to new items. */
		private var _defs:Object = null;
		/** Cache of Stats objects for item properties. */
		private var _stats:Object = {};
		/** The underlying array storing the list. */
		internal function get list():Array { return _list; }
		
		/** Internal count of visitors traversing the current list.*/
		private var _visiting:int = 0;
		private var _sort:Sort;
		
		/** The number of items contained in this list. */
		public function get size():int { return _list.length; }
		
		/** A standing sort criteria for items in the list. */
		public function get sort():Sort { return _sort; }
		public function set sort(s:*):void {
			_sort = s==null ? s : (s is Sort ? Sort(s) : new Sort(s));
			if (_sort != null) _sort.sort(_list);
		}
		
		
		// -- Basic Operations: Contains, Add, Remove, Clear ------------------
		
		/**
		 * Indicates if the given object is contained in this list.
		 * @param o the object to check for containment
		 * @return true if the list contains the object, false otherwise.
		 */
		public function contains(o:Object):Boolean
		{
			return (_map[o] != undefined);
		}
		
		/**
		 * Internal method for adding an object to the list. This method should
		 * be used by the Data class only.
		 * @param o the object to add
		 * @return the added object
		 * @private
		 */
		internal function add(o:Object):Object
		{
			_map[o] = _list.length;
			_stats = {};
			if (_sort != null) {
				var idx:int = Arrays.binarySearch(_list, o, null,
				                                  _sort.comparator);
				_list.splice(-(idx+1), 0, o);
			} else {
				_list.push(o);
			}
			return o;
		}
		
		/**
		 * Internal method for removing an object from the list. This method
		 * should be used by the Data class only.
		 * @param o the object to remove
		 * @return true if the object was found and removed, false otherwise
		 * @private
		 */
		internal function remove(o:Object):Boolean
		{
			if (_map[o] == undefined) return false;
			if (_visiting > 0) {
				// if called from a visitor, use a copy-on-write strategy
				_list = Arrays.copy(_list);
				_visiting = 0; // reset the visitor count
			}
			Arrays.remove(_list, o);
			delete _map[o];
			_stats = {};	
			return true;
		}
		
		/**
		 * Internal method for removing an object from the list. This method
		 * should be used by the Data class only.
		 * @param idx the index of the object to remove
		 * @return the removed object
		 * @private
		 */
		internal function removeAt(idx:int):Object
		{
			var o:Object = Arrays.removeAt(_list, idx);
			if (o != null) {
				delete _map[o];
				_stats = {};
			}
			return o;
		}
		
		/**
		 * Internal method for removing all objects from ths list.
		 * @private
		 */
		internal function clear():void
		{
			_map = new Dictionary();
			_list = [];
			_stats = {};
		}
		
		/**
		 * Returns an array of data objects for each item in this data list.
		 * Data objects are retrieved from the "data" property for each item.
		 * @return an array of data objects for items in this data list
		 */
		public function toDataArray():Array
		{
			var a:Array = new Array(_list.length);
			for (var i:int=0; i<a.length; ++i) {
				a[i] = _list[i].data;
			}
			return a;
		}

		// -- Sort ------------------------------------------------------------
		
		/**
		 * Sort DataSprites according to their properties. This method performs
		 * a one-time sorting. To establish a consistent sort order robust over
		 * the addition of new items, use the <code>sort</code> property.
		 * @param a the sort arguments.
		 * 	If a String is provided, the data will be sorted in ascending order
		 *   according to the data field named by the string.
		 *  If an Array is provided, the data will be sorted according to the
		 *   fields in the array. In addition, field names can optionally
		 *   be followed by a boolean value. If true, the data is sorted in
		 *   ascending order (the default). If false, the data is sorted in
		 *   descending order.
		 */
		public function sortBy(a:*):void
		{
			var args:Array;
			if (a is String) args = [a];
			else if (a is Array)  args = a;
			else throw new ArgumentError("Illegal input: "+a);
			
			var f:Function = Sort.sorter(args);
			_list.sort(f);
		}

		// -- Visitation ------------------------------------------------------
		
		/**
		 * Iterates over the contents of the list, invoking a visitor function
		 * on each element of the list. If the visitor function returns a
		 * Boolean true value, the iteration will stop with an early exit.
		 * @param visitor the visitor function to be invoked on each item
		 * @param reverse optional flag indicating if the list should be
		 *  visited in reverse order
		 * @param filter an optional boolean-valued function indicating which
		 *  items should be visited
		 * @return true if the visitation was interrupted with an early exit
		 */		
		public function visit(visitor:Function, reverse:Boolean=false,
			filter:Function=null):Boolean
		{
			_visiting++; // mark a visit in process
			var a:Array = _list; // use our own reference to the list
			var i:uint, b:Boolean = false;
			
			if (reverse && filter==null) {
				for (i=a.length; --i>=0;)
					if (visitor(a[i]) as Boolean) {
						b = true; break;
					}
			}
			else if (reverse) {
				for (i=a.length; --i>=0;)
					if (filter(a[i]) && (visitor(a[i]) as Boolean)) {
						b = true; break;
					}
			}
			else if (filter==null) {
				for (i=0; i<a.length; ++i)
					if (visitor(a[i]) as Boolean) {
						b = true; break;
					}
			}
			else {
				for (i=0; i<a.length; ++i)
					if (filter(a[i]) && (visitor(a[i]) as Boolean)) {
						b = true; break;
					}
			}
			_visiting = Math.max(0, --_visiting); // unmark a visit in process
			return b;
		}
		
		// -- Default Values --------------------------------------------------
		
		/**
		 * Sets a default property value for newly created items.
		 * @param name the name of the property
		 * @param value the value of the property
		 */
		public function setDefault(name:String, value:*):void
		{
			if (_defs == null) _defs = {};
			_defs[name] = value;
		}
		
		/**
		 * Removes a default value for newly created items.
		 * @param name the name of the property
		 */
		public function removeDefault(name:String):void
		{
			if (_defs != null) delete _defs[name];
		}
		
		/**
		 * Sets default values for newly created items.
		 * @param values the default properties to set
		 */
		public function setDefaults(values:Object):void
		{
			if (_defs == null) _defs = {};
			for (var name:String in values)
				_defs[name] = values[name];
		}
		
		/**
		 * Clears all default value settings for this list.
		 */
		public function clearDefaults():void
		{
			_defs = null;
		}
		
		/**
		 * Applies the default values to an object.
		 * @param o the object on which to set the default values
		 * @param vals the set of default property values
		 */
		public function applyDefaults(o:Object):void
		{
			if (_defs == null) return;
			for (var name:String in _defs) {
				Property.$(name).setValue(o, _defs[name]);
			}
		}
		
		// -- Set Values ------------------------------------------------------
		
		/**
		 * Sets a property value on all items in the list.
		 * @param name the name of the property
		 * @param value the value of the property
		 * @param t a transitioner or time span for updating object values. If
		 *  the input is a transitioner, it will be used to store the updated
		 *  values. If the input is a number, a new Transitioner with duration
		 *  set to the input value will be used. The input is null by default,
		 *  in which case object values are updated immediately.
		 * @return the transitioner used to update the values
		 */
		public function setProperty(name:String, value:*, t:*=null):Transitioner
		{
			var trans:Transitioner = Transitioner.instance(t);
			for (var i:uint=0; i<_list.length; ++i)
				trans.setValue(_list[i], name, value);
			return trans;
		}
		
		/**
		 * Sets property values on all sprites in a given group.
		 * @param vals an object containing the properties and values to set.
		 * @param t a transitioner or time span for updating object values. If
		 *  the input is a transitioner, it will be used to store the updated
		 *  values. If the input is a number, a new Transitioner with duration
		 *  set to the input value will be used. The input is null by default,
		 *  in which case object values are updated immediately.
		 * @return the transitioner used to update the values
		 */
		public function setProperties(vals:Object, t:*=null):Transitioner
		{
			var trans:Transitioner = Transitioner.instance(t);
			for (var name:String in vals) {
				for (var i:uint=0; i<_list.length; ++i)
					trans.setValue(_list[i], name, vals[name]);
			}
			return trans;
		}
		
		// -- Statistics ------------------------------------------------------
				
		/**
		 * Computes and caches statistics for a data field. The resulting
		 * <code>Stats</code> object is cached, so that later access does not
		 * require any re-calculation. The cache of statistics objects may be
		 * cleared, however, if changes to the data set are made.
		 * @param field the property name
		 * @return a <code>Stats</code> object with the computed statistics
		 */
		public function stats(field:String):Stats
		{
			// TODO: allow custom comparators?
			
			// check cache for stats
			if (_stats[field] != undefined) {
				return _stats[field] as Stats;
			} else {
				return _stats[field] = new Stats(_list, field);
			}
		}
		
		
		/**
		 * Clears any cached stats for the given field. 
		 * @param field the data field to clear the stats for.
		 */
		public function clearStats(field:String):void
		{
			delete _stats[field];
		}
		
		// -- Scales ----------------------------------------------------------
		
		/**
		 * Create a new Scale instance for the given data field.
		 * @param field the data property name to compute the scale for
		 * @param which the data group (either NODES or EDGES) in which to look
		 * @param scaleType the type of scale instance to generate
		 * @return a Scale instance for the given data field
		 * @see flare.vis.scale.Scales
		 */
		public function scale(field:String,
			scaleType:String=ScaleType.LINEAR, ...rest):Scale
		{
			var scale:Scale = Scales.scale(stats(field), scaleType);
			// TODO: lookup formatting info (?)
			return scale;
		}
		
		// -- Proxy Methods ---------------------------------------------------
		
		/** @private */
		flash_proxy override function getProperty(name:*):*
		{
        	return _list[name];
    	}
    	
    	/** @private */
    	flash_proxy override function setProperty(name:*, value:*):void
    	{
    		this.setProperty(name, value);
    	}
		
		/** @private */
		flash_proxy override function nextNameIndex(idx:int):int
		{
			return (idx < _list.length ? idx + 1 : 0);
		}

		/** @private */
		flash_proxy override function nextName(idx:int):String
		{
			return String(idx-1);
		}
		
		/** @private */
		flash_proxy override function nextValue(idx:int):*
		{
			return _list[idx-1];
		}
		
	} // end of class DataList
}