package flare.vis.operator.layout
{
	import flare.animate.Transitioner;
	import flash.geom.Rectangle;
	import flare.vis.data.NodeSprite;
	import flare.util.Maths;
	import flare.util.Arrays;
	import flash.display.Sprite;
	import flare.vis.scale.LinearScale;
	import flare.vis.Visualization;
	import flare.vis.axis.CartesianAxes;
	import flare.vis.axis.Axis;
	import flare.vis.scale.QuantitativeScale;
	import flare.vis.scale.Scale;
	import flare.vis.scale.Scales;
	import flare.util.Stats;
	
	/**
	 * Layout that consecutively places items on top of each other. The layout
	 * currently assumes that each column value is available as separate
	 * properties of individual DataSprites.
	 */
	public class StackedAreaLayout extends Layout
	{
		// -- Properties ------------------------------------------------------
		
		private var _columns:Array;
    	private var _baseline:Array;
    	private var _peaks:Array;
    	private var _poly:Array;
		
		private var _orient:String = Orientation.BOTTOM_TO_TOP;
		private var _horiz:Boolean = false;
		private var _top:Boolean = false;
		private var _initAxes:Boolean = true;
		
		private var _normalize:Boolean = false;
		private var _padding:Number = 0.05;
		private var _threshold:Number = 1.0;
		private var _t:Transitioner;
		
		private var _scale:QuantitativeScale = new LinearScale(0,0);
		
		/** Flag indicating if the visualization should be normalized. */		
		public function get normalize():Boolean { return _normalize; }
		public function set normalize(b:Boolean):void { _normalize = b; }
		
		/** Flag indicating the padding (as a percentage of the view)
		 *  that should be reserved within the visualization. */
		public function get padding():Number { return _padding; }
		public function set padding(p:Number):void { _padding = p; }
		
		/** Threshold value that at least one column value must surpass for
		 *  a stack to remain visible. */
		public function get threshold():Number { return _threshold; }
		public function set threshold(t:Number):void { _threshold = t; }
		
		/** The orientation of the layout. */
		public function get orientation():String { return _orient; }
		public function set orientation(o:String):void {
			_orient = o;
			_horiz = Orientation.isHorizontal(_orient);
        	_top   = (_orient == Orientation.TOP_TO_BOTTOM ||
        			  _orient == Orientation.LEFT_TO_RIGHT);
        	initializeAxes();
		}
		
		/** The scale used to layout the stacked values. */
		public function get scale():QuantitativeScale { return _scale; }
		public function set scale(s:QuantitativeScale):void {
			_scale = s; _scale.dataMin = 0;
		}
		
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Creates a new StackedAreaLayout.
		 * @param cols an ordered array of properties for the column values
		 */		
		public function StackedAreaLayout(cols:Array) {
			_columns = Arrays.copy(cols);
			_baseline = new Array(cols.length);
			_peaks = new Array(cols.length);
			_poly = new Array(cols.length);
		}
		
		/** @inheritDoc */
		public override function setup():void
		{
			initializeAxes();
		}
		
		/**
		 * Initializes the axes prior to layout.
		 */
		protected function initializeAxes():void
		{
			if (!_initAxes || visualization==null) return;
			
			var axes:CartesianAxes = super.xyAxes;
			var axis1:Axis = _horiz ? axes.xAxis : axes.yAxis;
			var axis2:Axis = _horiz ? axes.yAxis : axes.xAxis;
			
			axis1.axisScale = _scale;
			axis2.showLines = false;
			axis2.axisScale = Scales.scale(new Stats(_columns));
			axis2.axisScale.flush = true;
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = (t!=null ? t : Transitioner.DEFAULT);

	        // get the orientation specifics sorted out
	        var bounds:Rectangle = layoutBounds;
	        var hgt:Number, wth:Number;
	        var xbias:int, ybias:int, mult:int, len:int;
	        hgt = (_horiz ? bounds.width : bounds.height);
	        wth = (_horiz ? -bounds.height : bounds.width);
	        xbias = (_horiz ? 1 : 0);
	        ybias = (_horiz ? 0 : 1);
	        mult = _top ? 1 : -1;
	        len = _columns.length;

	        // perform first walk to compute max values
	        var maxValue:Number = peaks();
	        var minX:Number = _horiz ? bounds.bottom : bounds.left;
	        var minY:Number = _horiz ? (_top ? bounds.left : bounds.right)
	                                 : (_top ? bounds.top : bounds.bottom);
	        Arrays.fill(_baseline, minY);
	        _scale.dataMax = maxValue;
	        
	        // initialize current polygon
	        var axes:CartesianAxes = super.xyAxes;
	        var scale:Scale = (_horiz ? axes.yAxis : axes.xAxis).axisScale;
	        var xx:Number;
	        for (var j:uint=0; j<len; ++j) {
				xx = minX + wth * scale.interpolate(_columns[j]);
	            _poly[2*(len+j)+xbias] = xx;
	            _poly[2*(len+j)+ybias] = minY;
	            _poly[2*(len-1-j)+xbias] = xx;
	            _poly[2*(len-1-j)+ybias] = minY;
	        }
	        
	        // perform second walk to compute polygon layout
	        visualization.data.nodes.visit(function(d:NodeSprite):void
	        {
	        	var obj:Object = t.$(d);
	        	var height:Number = 0, i:uint;
	        	var visible:Boolean = d.visible && d.alpha>0;
	        	var filtered:Boolean = !obj.visible;
	        	
	        	// set full polygon to current baseline
	        	for (i=0; i<len; ++i) {
	            	_poly[2*(len-1-i)+ybias] = _baseline[i];
	            }
	            // if not visible, flatten on current baseline
	        	if (!visible || filtered) {
	        		if (!visible || _t.immediate) d.points = Arrays.copy(_poly, d.points);
	        		else obj.points = Arrays.copy(_poly, d.props.poly);
	        		return;
	        	}
	        	
	        	// if visible, compute the new heights
	            for (i=0; i<len; ++i ) {
	                var base:int = 2*(len+i);
	                var value:Number = d.data[_columns[i]];
	                _baseline[i] += mult * hgt * Maths.invLinearInterp(value,0,_peaks[i]);
	                _poly[base+ybias] = _baseline[i];
	                height = Math.max(height, Math.abs(
	                	_poly[2*(len-1-i)+ybias] - _poly[base+ybias]));
	            }
	            
	            // if size is beneath threshold, then hide
	            if ( height < _threshold ) {
	            	obj.visible = false;
	            }
	            
	            // update data sprite layout
	            if (d.points == null)
	            	d.points = getPolygon(d, bounds);
	            if (d.props.poly == null)
	            	d.props.poly = Arrays.copy(_poly);
	            obj.x = 0;
	            obj.y = 0;
	            obj.points = Arrays.copy(_poly, 
	            	_t.immediate ? d.points : d.props.poly);
	        });
			
			_t = null;
		}
		
		private function peaks():Number
		{
			var sum:Number = 0;
	        
	        // first, compute max value of the current data
	        Arrays.fill(_peaks, 0);
	        visualization.data.nodes.visit(function(d:NodeSprite):void {
	        	if (!d.visible || d.alpha <= 0 || !_t.$(d).visible)
	        		return;
	        	
	        	for (var i:uint=0; i<_columns.length; ++i) {
	        		var val:Number = d.data[_columns[i]];
	        		_peaks[i] += val;
	        		sum += val;
	        	}
	        });
	        var max:Number = Arrays.max(_peaks);
	        
	        // update peaks array as needed
	        // adjust peaks to include padding space
	        if (!_normalize) {
	        	Arrays.fill(_peaks, max);
	            for (var i:uint=0; i<_peaks.length; ++i) {
	                _peaks[i] += _padding * _peaks[i];
	            }
	            max += _padding*max;
	        }
	        
	        // return max range value
	        if (_normalize) max = 1.0;
	        if (isNaN(max)) max = 0;
	        return max;
		}
		
		private function getPolygon(d:Sprite, b:Rectangle, poly:Array=null):Array
		{
			// get oriented
			var len:uint = _columns.length;
			var inc:Number = _horiz ? (b.left-b.right) : (b.bottom-b.top);
			inc /= len-1;
			var min:Number = _horiz ? (_top ? b.right : b.left)
									: (_top ? b.top : b.bottom);
			var bias:int = _horiz ? 1 : 0;
			
			// create polygon, populate default values
			if (poly==null) poly = new Array(4*len);
			Arrays.fill(poly, min);
			for (var i:uint=0, x:Number=min; i<len; ++i, x = i*inc+min) {
				poly[2*(len+1)  +bias] = x;
				poly[2*(len-1-i)+bias] = x;
			}
			return poly;
		}
		
	} // end of class StackedAreaLayout
}