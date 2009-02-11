package flare.vis.operator.layout
{
	import flare.animate.Transitioner;
	import flare.util.Property;
	import flare.vis.data.Data;
	import flare.vis.data.DataSprite;
	
	import flash.geom.Rectangle;
	
	/**
	 * Layout that places wedges for pie and donut charts.
	 */
	public class PieLayout extends Layout
	{
		private var _field:Property;
		private var _radius:Number = NaN;
		private var _width:Number = -1;
		private var _a0:Number = Math.PI/2;
		private var _cw:Boolean = true;
		private var _t:Transitioner;
		
		/** The source property determining wedge size. */
		public function get source():String { return _field.name; }
		public function set source(f:String):void { _field = Property.$(f); }
		
		/** The radius of the pie/donut chart. If this value is not a number
		 *  (NaN) the radius will be determined from the layout bounds. */
		public function get radius():Number { return _radius; }
		public function set radius(r:Number):void { _radius = r; }
		
		/** The width of wedges, negative for a full pie slice. */
		public function get width():Number { return _width; }
		public function set width(w:Number):void { _width = w; }
		
		/** Flag for clockwise (true) or counter-clockwise (false) layout. */
		public function get clockwise():Boolean { return _cw; }
		public function set clockwise(cw:Boolean):void { _cw = cw; }
		
		/** The initial angle for the pie layout. */
		public function get startAngle():Number { return _a0; }
		public function set startAngle(a:Number):void { _a0 = a; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new PieLayout
		 * @param field the source data field for determining wedge size
		 * @param width the radial width of wedges, negative for full slices
		 */		
		public function PieLayout(field:String=null, width:Number=-1) {
			_field = (field==null) ? null : new Property(field);
			_width = width;
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = (t!=null ? t : Transitioner.DEFAULT);
			hideAxes(_t);
			
			var b:Rectangle = layoutBounds;
			var cx:Number = (b.left + b.right) / 2;
			var cy:Number = (b.top + b.bottom) / 2;
			var r:Number = isNaN(_radius) ? Math.min(b.width, b.height)/2 : _radius;
			var a:Number = _a0, aw:Number;
			var sum:Number = visualization.data.nodes.stats(_field.name).sum;
			
			var o:Object = _t.$(visualization.marks);
			o.x = cx; o.y = cy;
			
			visualization.data.visit(function(d:DataSprite):void {
				var aw:Number = (_cw?-1:1) * 2*Math.PI * (_field.getValue(d)/sum);
				var rh:Number = _width * r;
				var o:Object = _t.$(d);
				
				// replaced a/r with x/y zeroes due to Flash rendering errors
				//o.angle = a + aw/2;    // angular mid-point
				//o.radius = (r - rh)/2; // radial mid-point
				o.x = 0;
				o.y = 0;
				
				o.u = a;   // starting angle
				o.w  = aw; // angular width
				o.h = r;   // outer radius
				o.v = rh;  // inner radius

				a += aw;
			}, Data.NODES);
			
			_t = null;
		}
		
	} // end of class PieLayout
}