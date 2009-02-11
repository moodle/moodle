package flare.animate
{
	import flare.util.Property;
	
	import flash.display.DisplayObject;
	import flash.utils.Dictionary;
	
	/**
	 * Parallel transition with convenience methods for adding new
	 * object tweens, helping to incrementally construct a group of transitions.
	 * A Transitioner will automatically generate tweens for any number of
	 * items and properties, simplifying the task of creating animated
	 * transitions. 
	 * 
	 * <p>For example, the following code creates a 1 second
	 * animation for two items. The first item is translated to the point
	 * (50,50) and the second item is scaled along the x dimension to twice the
	 * normal size.</p>
	 * <pre>
	 * var item1:Sprite, item2:Sprite; // assume these are two drawn sprites
	 * var t:Transitioner = new Transitioner(1); // create 1-second transition
	 * t.$(item1).x = 50;
	 * t.$(item1).y = 50;
	 * t.$(item2).scaleX = 2;
	 * t.play();
	 * </pre>
	 *
	 * <p>In the code above, the <code>$</code> method takes an item (this
	 * can be any ActionScript object, but is often a <code>DisplayObject</code>
	 * instance) and returns an <code>Object</code> which stores the names of
	 * the properties to animate and their target values. Behind the scenes,
	 * the <code>Transitioner</code> automatically creates <code>Tween</code>
	 * objects as needed.</p>
	 * 
	 * <p>The object returned by the <code>$</code> method is a proxy object
	 * that passes the values to underlying tweens as needed. This same proxy
	 * object is reused across calls to the <code>$</code> method so do
	 * <strong>not</strong> attempt to use multiple return values from the
	 * <code>$</code> method simultaneously. <em>The following example shows
	 * what you should not do!</em></p>
	 * <pre>
	 * var o1:Object = t.$(item1);
	 * var o2:Object = t.$(item2); // o2==o1, now configured for item2
	 * o1.x = 5; // actually sets the value 5 to item2, NOT item1
	 * </pre>
	 * 
	 * <p>
	 * A transitioner can also be set to "immediate" mode, either by setting
	 * the <code>immediate</code> property to true, or by passing in
	 * <code>NaN</code> as the duration value to the constructor. When in
	 * immediate mode, a transitioner will <strong>NOT</strong> generate
	 * <code>Tween</code> instances to animate the properties. Instead, the
	 * transitioner will set the values of the target objects immediately.
	 * For example, when in immediate mode, the <code>$</code> operator is
	 * equivalent to directly setting the property:
	 * <code>t.$(item1).x = 50</code> has exactly the same result at
	 * <code>t.x = 50</code>. The static property
	 * <code>Transitioner.DEFAULT</code> provides a default instance of an
	 * immediate-mode transitioner.
	 * </p>
	 * 
	 * <p>
	 * With these features, transitioners provide a highly flexible way to
	 * update values in your application. You can write layout and other
	 * methods once, using a transitioner to update all the property values.
	 * When animation is desired, a standard transitioner can be passed in
	 * to your routines. When immediate updates are desired, you can reuse
	 * the same code, but just pass in a transitioner in immediate mode
	 * instead. Whether or not value updates are animated or immediate then
	 * becomes easy to control.
	 * </p>
	 * 
	 * <p>
	 * Transitioners also provide optimizations to improve animation
	 * performance. However, they are not enabled by default, as the
	 * optimizations make some assumptions about how the transitioner will
	 * be used. See the <code>optimize</code> property and
	 * <code>dispose</code> method for more information.
	 * </p>
	 */
	public class Transitioner extends Parallel
	{
		/** The default, immediate-mode transitioner instance. */
		public static const DEFAULT:Transitioner = new Transitioner(NaN);
		
		/**
		 * Gets a transitioner instance depending upon the input value.
		 * @param t input determining the transitioner instance to return. If
		 *  the input is a transitioner, it is simply returned. If the input is
		 *  a number, a new Transitioner with duration set to the input value
		 *  is returned. If the input is null,
		 *  <code>Transitioner.DEFAULT</code> is returned.
		 * @return a Transitioner instance determined by the input
		 */		
		public static function instance(t:*):Transitioner {
			if (t is Number) {
				return new Transitioner(t as Number);
			} else if (t == null) {
				return Transitioner.DEFAULT;
			} else {
				return t as Transitioner;
			}
		}
		
		// --------------------------------------------------------------------
		
		private var _immediate:Boolean;
		private var _lookup:/*Object->Tween*/Dictionary = new Dictionary();
		
		private var _proxy:ValueProxy;
		private var _optimize:Boolean = false;
		private var _subdur:Number;
		
		/** @private */
		public override function get duration():Number {
			return _trans.length==0 ? _subdur : super.duration;
		}
		
		/** Immediate mode flag, used to bypass tween generation and perform
		 *  immediate updates of target object values. */
		public function get immediate():Boolean { return _immediate; }
		public function set immediate(b:Boolean):void {
			_immediate = b;
			if (!immediate && _proxy == null) _proxy = new ValueProxy(this);
		}
		
		/** 
		 * Flag indicating if aggressive optimization should be applied.
		 * This can significantly decrease processing time when large numbers
		 * of elements are involved. However, the optimization process makes a
		 * few assumptions about how the transitioner will be used. If these
		 * assumptions are not met, the animations may exhibit unexpected
		 * behaviors.
		 * 
		 * <p>The assumptions made for optimized transitioners are:
		 * <ol>
		 * <li>The property values of tweened objects will not change between
		 *     the time their target values are set and the transition is
		 *     played. This allows the transitioner to avoid creating tweens
		 *     when properties have the same starting and ending values, and
		 *     immediately set values for DisplayObjects that are not visible.
		 *     However, this means the starting value must stay the same. In
		 *     particular, this means that optimized transitioners are often
		 *     inappropriate for use within a <code>Sequence</code>.</li>
		 * <li>The transitioner will only be played once, then discarded.
		 *     This allows the transitioner to automatically recycle all 
		 *     generated <code>Tween</code> and <code>Interpolator</code>
		 *     instances, reducing initialization time across transitioners by
		 *     reusing objects.</li>
		 * </ol>
		 * </p>
		 */
		public function get optimize():Boolean { return _optimize; }
		public function set optimize(b:Boolean):void { _optimize = b; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new Transitioner with specified duration.
		 * @param duration the length of the transition. If this value is NaN,
		 *  the transitioner will be in immediate mode, in which all changes
		 *  are immediately applied and no tweens are generated.
		 * @param easing the easing function to use for this transition. If
		 *  null, the function Easing.none will be used.
		 * @param optimize boolean flag indicating if the transitioner should
		 *  attempt to optimize tween construction. See the documentation
		 *  for the <code>optimize</code> property for mode details.
		 */
		public function Transitioner(duration:Number=1, easing:Function=null,
									 optimize:Boolean=false)
		{
			super.easing = easing==null ? DEFAULT_EASING : easing;
			_subdur = duration;
			_optimize = optimize;
			_immediate = isNaN(duration);
			if (!_immediate) _proxy = new ValueProxy(this);
		}
		
		/**
		 * Returns the Tween for the given object, creating a new tween if no
		 * tween is yet associated with the object. This method returns null if
		 * the transitioner is in immediate mode.
		 * @param o the target object
		 * @return a tween for the input target object, or null if this
		 *  transitioner is in immediate mode.
		 */
		public function _(o:Object):Tween
		{
			if (_immediate) return null;
			
			var tw:Tween = _lookup[o];
			if (tw == null) {
				add(tw = getTween(o, _subdur));
				tw.easing = Easing.none;
				_lookup[o] = tw;
			}
			return tw;
		}
		
		/**
		 * Returns the values object of the Tween for the given object. If no
		 * tween is associated with the object, a new one is created.
		 * 
		 * If the transitioner is in immediate mode, then no Tween will be
		 * created. Instead, the input object will be returned. This allows
		 * value updates to be set immediately, rather than animated by a
		 * tween.
		 * @param o the target object
		 * @return the <code>values</code> object for the target object's
		 *  tween, or the target object itself if this transitioner is in
		 *  immediate mode.
		 */
		public function $(o:Object):Object
		{
			return _immediate ? o : _proxy.init(o);
		}
		
		/**
		 * Sets property values for a target object. This method has the same
		 * effect as setting a property on the object returned by the
		 * <code>$</code> method.
		 * 
		 * <p>If the transitioner is in immediate mode, the property name will
		 * be parsed and the value set at the end of the property chain. If
		 * the transitioner is not in immediate mode, the property name and
		 * values will simply be added to a Tween. If no Tween is associated
		 * with the input object, a new one will be created.</p>
		 * 
		 * @param o the target object
		 * @param name the property name string
		 * @param value the property value to set
		 */
		public function setValue(o:Object, name:String, value:*):void
		{
			if (_immediate) {
				// set the object property
				Property.$(name).setValue(o, value);
			} else if (optimize && getValue(o, name) == value) {
				// do nothing, optimize the call away...
			} else if (optimize && o is DisplayObject && !o.visible) {
				Property.$(name).setValue(o, value);
			} else {
				// add to a tween
				_(o).values[name] = value;
			}
		}
		
		/**
		 * Retrieves property values for a target object. This method has the
		 * same effect as accessing a property using the object returned by the
		 * <code>$</code> method.
		 * 
		 * <p>If the transitioner is in immediate mode, the property name will
		 * be parsed and the value retrieved diretly from the target object. If
		 * the transitioner is not in immediate mode, this method will first
		 * try to lookup the value in the tween <code>values</code> for the
		 * target object. If this does not succeed, the property value will be
		 * retrieved directly from the target object itself as in the immediate
		 * mode case.</p>
		 * 
		 * @param o the target object
		 * @param name the property name string
		 * @return the property value for the target object, either from the
		 *  target object's tween values, or, failing that, the object itself.
		 */
		public function getValue(o:Object, name:String):*
		{
			if (!_immediate) {
				var tw:Tween = _lookup[o];
				if (tw != null && tw.values[name] != undefined) {
					return tw.values[name];
				}
			}
			return Property.$(name).getValue(o);
		}
		
		/**
		 * Sets the delay of the tween for the given object. If the
		 * transitioner is in immediate mode, this method has no effect.
		 * @param o the object to set the delay for
		 * @param delay the delay, in seconds
		 */		
		public function setDelay(o:Object, delay:Number):void
		{
			if (!_immediate) {
				_(o).delay = delay;
			}
		}
				
		/**
		 * Gets the delay of the tween for the given object. If the
		 * transitioner is in immediate mode or no tween has been created for
		 * the input object, this method returns zero.
		 * @param o the object to get the delay for
		 * @return the delay of the tween, or zero if there is no tween
		 */
		public function getDelay(o:Object):Number
		{
			if (_immediate) return 0;
			var tw:Tween = _lookup[o];
			return tw==null ? 0 : tw.delay;
		}
		
		/**
		 * Sets the removal status of a display object with this transition.
		 * If true, the display object will be removed from its parent in the
		 * display list when the transition completes. If this transitioner is
		 * in immediate mode, any removals are performed immediately.
		 * @param dobj a display object
		 * @param b true to remove the display object from the display list
		 *  at the end of the transition (the default). If false, the removal
		 *  status will be updated so that the display object will not be
		 *  removed (not applicable in immediate mode).
		 */		
		public function removeChild(dobj:DisplayObject, b:Boolean=true):void
		{
			if (_immediate && b) {
				if (dobj.parent) dobj.parent.removeChild(dobj);
			} else if (!_immediate) {
				_(dobj).remove = b;
			}
		}
		
		/**
		 * Indicates if a display object is scheduled to be removed from the
		 * display list when this transition completes. This method always
		 * returns false if this transitioner is in immediate mode.
		 * @param dobj a display object
		 * @return true if the display object will be removed from the display
		 *  list at the end of this transition, false otherwise. This method
		 *  always returns false if the transitioner is in immediate mode.
		 */
		public function willRemove(dobj:DisplayObject):Boolean
		{
			if (_immediate) return false;
			var tw:Tween = _lookup[dobj];
			return (tw != null && tw.remove);
		}
		
		/** @inheritDoc */
		protected override function end():void
		{
			super.end();
			if (_optimize) dispose();
		}
		
		/**
		 * Disposes of the internal state of this transitioner.
		 * Contained tweens and their interpolators will be collected and
		 * recycled for future reuse, improving initialization times for
		 * subsequent transitioners. This method is automatically called at the
		 * end of the transition if the <code>optimize</code> flag is true.
		 * Otherwise, this method can be invoked manually when a transitioner
		 * is no longer needed.
		 */
		public override function dispose():void
		{
			while (_trans.length > 0) {
				var t:Transition = _trans.pop();
				t.dispose();
				if (t is Tween) reclaimTween(t as Tween);
			}
		}
		
		// --------------------------------------------------------------------
		
		private static var _maxPoolSize:int = 10000;
		private static var _tweenPool:Array = [];
		private static var _count:int = 0;
		
		private static function getTween(o:Object, duration:Number):Tween
		{
			var tw:Tween;
			if (_tweenPool.length > 0) {
				tw = _tweenPool.pop();
				tw.target = o;
				tw.duration = duration;
			} else {
				tw = new Tween(o, duration);
			}
			return tw;
		}
		
		private static function reclaimTween(tw:Tween):void
		{
			if (_tweenPool.length < _maxPoolSize) {
				_tweenPool.push(tw);
			}
		}
		
	} // end of class Transitioner
}

import flash.utils.Proxy;
import flash.utils.flash_proxy;
import flare.animate.Transitioner;
import flare.util.Property;

/**
 * Helper class that gets/sets values for a Transitioner.
 * This layer of indirection allows us to perform "behind-the-scenes"
 * handling while maintaining simple property assignment syntax.
 */
dynamic class ValueProxy extends Proxy
{
	private var _trans:Transitioner;
	private var _object:Object;
    
    public function ValueProxy(trans:Transitioner) {
    	_trans = trans;
    }

	public function init(obj:Object):Object
	{
		_object = obj;
		return this;
	}

	override flash_proxy function callProperty(methodName:*, ... args):* {
		return null;
	}

    override flash_proxy function getProperty(name:*):* {
    	return _trans.getValue(_object, name);
    }

    override flash_proxy function setProperty(name:*, value:*):void {
        _trans.setValue(_object, name, value);
    }
    
} // end of class ValueProxy