package flare.vis.operator.encoder
{
	import flare.animate.Transitioner;
	import flare.vis.data.DataSprite;
	import flare.vis.operator.Operator;

	/**
	 * A property encoder simply sets a group of properties to static
	 * values for all data sprites. An input object determines which
	 * properties to set and what their values are.
	 * 
	 * For example, a PropertyEncoder created with this call:
	 * <code>new PropertyEncoder({size:1, lineColor:0xff0000ff{);</code>
	 * will set the size to 1 and the line color to blue for all
	 * data sprites processed by the encoder.
	 */
	public class PropertyEncoder extends Operator
	{
		/** Flag indicating which data group (NODES, EDGES, or ALL) should
		 *  be processed by this encoder. */
		protected var _which:int;
		/** Boolean function indicating which items to process. */
		protected var _filter:Function;
		/** Flag indicating if property values should be set immediately. */
		protected var _ignoreTrans:Boolean;
		/** The properties to set on each invocation. */
		protected var _values:Object;
		/** A transitioner for collecting value updates. */
		protected var _t:Transitioner;
		
		/** Flag indicating which data group (NODES, EDGES, or ALL) should
		 *  be processed by this encoder. */
		public function get which():int { return _which; }
		public function set which(w:int):void { _which = w; }
		
		/** Boolean function indicating which items to process. Only items
		 *  for which this function return true will be considered by the
		 *  Encoder. If the function is null, all items will be considered. */
		public function get filter():Function { return _filter; }
		public function set filter(f:Function):void { _filter = f; }
		
		public function get ignoreTransitioner():Boolean { return _ignoreTrans; }
		public function set ignoreTransitioner(b:Boolean):void { _ignoreTrans = b; }
		
		/** The properties to set on each invocation. */
		public function get values():Object { return _values; }
		public function set values(o:Object):void { _values = o; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new PropertyEncoder
		 * @param values The properties to set on each invocation. The input
		 *  should be an object with a set of name/value pairs.
		 * @param which Flag indicating which data group (NODES, EDGES, or ALL)
		 *  should be processed by this encoder.
		 * @param filter a Boolean-valued function that takes a DataSprite as
		 *  input and returns true if the sprite should be processed
		 * @param ignoreTransitioner Flag indicating if values should be set
		 *  immediately rather than being processed by any transitioners
		 */		
		public function PropertyEncoder(values:Object=null, which:int=1,
			filter:Function=null, ignoreTransitioner:Boolean=false)
		{
			_values = values==null ? {} : values;
			_which = which;
			_filter = filter;
			_ignoreTrans = ignoreTransitioner;
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			t = (t==null || _ignoreTrans ? Transitioner.DEFAULT : t);
			if (_values == null) return;
			
			visualization.data.visit(function(d:DataSprite):void {
				for (var p:String in _values)
					t.setValue(d, p, _values[p]);
			}, _which, _filter);
		}
		
	} // end of class PropertyEncoder
}