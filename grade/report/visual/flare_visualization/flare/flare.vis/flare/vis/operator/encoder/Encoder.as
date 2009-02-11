package flare.vis.operator.encoder
{
	import flare.vis.operator.Operator;
	import flare.animate.Transitioner;
	import flare.vis.data.Data;
	import flare.vis.data.DataSprite;
	import flare.util.Property;
	import flare.vis.scale.Scale;
	import flare.vis.palette.Palette;

	/**
	 * Base class for Operators that perform encoding of visual variables such
	 * as color, shape, and size. All Encoders share a similar structure:
	 * A source property (e.g., a data field) is mapped to a target property
	 * (e.g., a visual variable) using a <tt>Scale</tt> instance to map
	 * between values and a <tt>Palette</tt> instance to map scaled output
	 * into visual variables such as color, shape, and size.
	 */
	public class Encoder extends Operator
	{
		/** Flag indicating which data group (NODES, EDGES, or ALL) should
		 *  be processed by this encoder. */
		protected var _which:int;
		/** Boolean function indicating which items to process. */
		protected var _filter:Function;
		/** The source property. */
		protected var _source:Property;
		/** The target property. */
		protected var _target:String;
		/** A transitioner for collecting value updates. */
		protected var _t:Transitioner;
		
		/** The scale used by the encoder. */
		protected var _scale:Scale;
		/** The scale type parameter. */
		protected var _scaleType:String;
		/** A parameter for the scale instance. */
		protected var _scaleParam:Number;
		/** Flag indicating if this encoder should initialize the scale. */
		protected var _initScale:Boolean = true;
		
		/** Flag indicating which data group (NODES, EDGES, or ALL) should
		 *  be processed by this encoder. */
		public function get which():int { return _which; }
		public function set which(w:int):void { _which = w; }
		
		/** Boolean function indicating which items to process. Only items
		 *  for which this function return true will be considered by the
		 *  Encoder. If the function is null, all items will be considered. */
		public function get filter():Function { return _filter; }
		public function set filter(f:Function):void { _filter = f; }
		
		/** The source property. */
		public function get source():String { return _source.name; }
		public function set source(f:String):void {
			_source = Property.$(f); setup();
		}
		
		/** The target property. */
		public function get target():String { return _target; }
		public function set target(f:String):void { _target = f; }
		
		/** The scale type parameter.
		 *  @see flare.vis.scale.Scales */
		public function get scaleType():String { return _scaleType; }
		public function set scaleType(st:String):void { _scaleType = st; setup(); }
		
		/** A parameter for the scale instance. Used as input to the
		 *  <code>flare.vis.scale.Scales.scale method. */
		public function get scaleParam():Number { return _scaleParam; }
		public function set scaleParam(p:Number):void { _scaleParam = p; setup(); }

		/** The scale used by the encoder. */
		public function get scale():Scale { return _scale; }
		public function set scale(s:Scale):void {
			_scale = s; _initScale = (_scale==null); setup();
		}
		
		/** The palette used to map scale values to visual values. */
		public function get palette():Palette { return null; }
		public function set palette(p:Palette):void { }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new Encoder.
		 * @param source the source property
		 * @param target the target property
		 * @param which flag indicating which group of visual object to process
		 */		
		public function Encoder(source:String=null, target:String=null,
							which:int=1/*Data.NODES*/, filter:Function=null)
		{
			_source = source==null ? null : Property.$(source);
			_target = target;
			_which = which;
			_filter = filter;
		}
		
		/** @inheritDoc */
		public override function setup():void
		{
			if (visualization==null || !_initScale) return;
			var data:Data = visualization.data;
			_scale = data.scale(_source.name, _which, _scaleType, _scaleParam);
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = (t!=null ? t : Transitioner.DEFAULT);
			
			if (visualization == null) return;
			visualization.data.visit(function(d:DataSprite):void {
				_t.$(d)[_target] = encode(_source.getValue(d));
			}, _which, _filter);
			
			_t = null;
		}
		
		/**
		 * Computes an encoding for the input value.
		 * @param val a data value to encode
		 * @return the encoded visual value
		 */
		protected function encode(val:Object):*
		{
			// sub-classes can override this
			return null;
		}
		
	} // end of class Encoder
}