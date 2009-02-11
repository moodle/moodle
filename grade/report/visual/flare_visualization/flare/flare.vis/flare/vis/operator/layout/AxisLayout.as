package flare.vis.operator.layout
{
	import flare.animate.Transitioner;
	import flare.util.Property;
	import flare.vis.axis.Axis;
	import flare.vis.axis.CartesianAxes;
	import flare.vis.data.Data;
	import flare.vis.data.DataSprite;
	import flare.vis.scale.LinearScale;
	import flare.vis.scale.ScaleType;
	import flare.vis.scale.Scales;
	
	/**
	 * Layout that places items according to data properties along the X and Y
	 * axes. The AxisLayout can also compute stacked layouts, in which elements
	 * that share the same data values along an axis can be consecutively
	 * stacked on top of each other.
	 */
	public class AxisLayout extends Layout
	{
		public static const ALWAYS:int = 2;
		public static const SETUP:int = 1;
		public static const NEVER:int = 0;
		
		protected var _initAxes:int = SETUP;
		protected var _xStacks:Boolean = false;
		protected var _yStacks:Boolean = false;		
		protected var _xField:Property;
		protected var _yField:Property;
		protected var _t:Transitioner;
		
		/** The scale type parameter for the x-axis. */
		protected var _xScaleType:String = ScaleType.LINEAR;
		/** A parameter for the scale instance for the x-axis. */
		protected var _xScaleParam:Number = 10;
		/** The scale type parameter for the y-axis. */
		protected var _yScaleType:String = ScaleType.LINEAR;
		/** A parameter for the scale instance for the y-axis. */
		protected var _yScaleParam:Number = 10;
		
		// ------------------------------------------------
		
		/** The x-axis source property. */
		public function get xField():String {
			return _xField==null ? null : _xField.name;
		}
		public function set xField(f:String):void {
			_xField = Property.$(f); initializeAxes();
		}
		
		/** The y-axis source property. */
		public function get yField():String {
			return _yField==null ? null : _yField.name;
		}
		public function set yField(f:String):void {
			_yField = Property.$(f); initializeAxes();
		}
		
		/** Flag indicating if values should be stacked according to their
		 *  x-axis values. */
		public function get xStacked():Boolean { return _xStacks; }
		public function set xStacked(b:Boolean):void { _xStacks = b; }

		/** Flag indicating if values should be stacked according to their
		 *  y-axis values. */
		public function get yStacked():Boolean { return _yStacks; }
		public function set yStacked(b:Boolean):void { _yStacks = b; }
		
		/** The scale type parameter for the x-axis.
		 *  @see flare.vis.scale.Scales */
		public function get xScaleType():String { return _xScaleType; }
		public function set xScaleType(st:String):void { _xScaleType = st; setup(); }
		
		/** A parameter for the scale instance for the x-axis. Used as input
		 *  to the <code>flare.vis.scale.Scales.scale method. */
		public function get xScaleParam():Number { return _xScaleParam; }
		public function set xScaleParam(p:Number):void { _xScaleParam = p; setup(); }
		
		/** The scale type parameter for the y-axis.
		 *  @see flare.vis.scale.Scales */
		public function get yScaleType():String { return _yScaleType; }
		public function set yScaleType(st:String):void { _yScaleType = st; setup(); }
		
		/** A parameter for the scale instance for the y-axis. Used as input
		 *  to the <code>flare.vis.scale.Scales.scale method. */
		public function get yScaleParam():Number { return _yScaleParam; }
		public function set yScaleParam(p:Number):void { _yScaleParam = p; setup(); }
		
		/** The policy for when axes should be initialized by this layout.
		 *  One of NEVER, SETUP (to initialize only on setup), and ALWAYS. */
		public function get initAxes():int { return _initAxes; }
		public function set initAxes(policy:int):void { _initAxes = policy; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new AxisLayout
		 * @param xAxisField the x-axis source property
		 * @param yAxisField the y-axis source property
		 * @param xStacked indicates if values should be stacked according to
		 *  their x-axis values
		 * @param yStacked indicates if values should be stacked according to
		 *  their y-axis values
		 */		
		public function AxisLayout(xAxisField:String=null, yAxisField:String=null,
								   xStacked:Boolean=false, yStacked:Boolean=false)
		{
			_xField = Property.$(xAxisField);
			_yField = Property.$(yAxisField);
			_xStacks = xStacked;
			_yStacks = yStacked;
		}
		
		/** @inheritDoc */
		public override function setup():void
		{
			initializeAxes();
		}
		
		/**
		 * Initializes the axes prior to layout.
		 */
		public function initializeAxes():void
		{
			if (_initAxes==NEVER || visualization==null) return;
			
			// set axes
			var axes:CartesianAxes = super.xyAxes, axis:Axis;
			var data:Data = visualization.data;

			axes.xAxis.axisScale = data.scale(
				_xField.name, Data.NODES, _xScaleType, _xScaleParam);
			axes.yAxis.axisScale = data.scale(
				_yField.name, Data.NODES, _yScaleType, _yScaleParam);
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = (t != null ? t : Transitioner.DEFAULT);
			if (_initAxes==ALWAYS) initializeAxes();
			if (_xStacks || _yStacks) { rescale(); }
						
			var axes:CartesianAxes = super.xyAxes;
			var x0:Number = axes.originX;
			var y0:Number = axes.originY;

			var xmap:Object = _xStacks ? new Object() : null;
			var ymap:Object = _yStacks ? new Object() : null;
			
			visualization.data.nodes.visit(function(d:DataSprite):void {
				var dx:Object, dy:Object, x:Number, y:Number, s:Number, z:Number;
				var o:Object = _t.$(d);
				dx = _xField.getValue(d); dy = _yField.getValue(d);
				
				if (_xField != null) {
					x = axes.xAxis.X(dx);
					if (_xStacks) {
						z = x - x0;
						s = z + (isNaN(s=xmap[dy]) ? 0 : s);
						o.x = x0 + s;
						o.w = z;
						xmap[dy] = s;
					} else {
						o.x = x;
						o.w = x - x0;
					}
				}
				if (_yField != null) {
					y = axes.yAxis.Y(dy);
					if (_yStacks) {
						z = y - y0;
						s = z + (isNaN(s=ymap[dx]) ? 0 : s);
						o.y = y0 + s;
						o.h = z;
						ymap[dx] = s;
					} else {
						o.y = y;
						o.h = y - y0;
					}
				}
			});
			
			_t = null;
		}
		
		private function rescale():void {
			var xmap:Object = _xStacks ? new Object() : null;
			var ymap:Object = _yStacks ? new Object() : null;
			var xmax:Number = 0;
			var ymax:Number = 0;
			
			visualization.data.nodes.visit(function(d:DataSprite):void {
				var x:Object = _xField.getValue(d);
				var y:Object = _yField.getValue(d);
				var v:Number;
				
				if (_xStacks) {
					v = isNaN(xmap[y]) ? 0 : xmap[y];
					xmap[y] = v = (Number(x) + v);
					if (v > xmax) xmax = v;
				}
				if (_yStacks) {
					v = isNaN(ymap[x]) ? 0 : ymap[x];
					ymap[x] = v = (Number(y) + v);
					if (v > ymax) ymax = v;
				}
			});
			
			var axes:CartesianAxes = visualization.xyAxes;
			if (_xStacks) axes.xAxis.axisScale = new LinearScale(0, xmax);
			if (_yStacks) axes.yAxis.axisScale = new LinearScale(0, ymax);
		}
		
	} // end of class AxisLayout
}