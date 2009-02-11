package flare.vis.axis
{
	import flare.animate.Transitioner;
	import flare.display.RectSprite;
	import flare.display.TextSprite;
	import flare.vis.Visualization;
	
	import flash.geom.Rectangle;
	
	/**
	 * Axes class representing 2D Cartesian (X-Y) axes.
	 */
	public class CartesianAxes extends Axes
	{
		// -- Properties ------------------------------------------------------
				
		private var _xaxis:Axis;
		private var _yaxis:Axis;
		private var _xline:AxisGridLine;
		private var _yline:AxisGridLine;
		private var _showXLine:Boolean;
		private var _showYLine:Boolean;
		private var _border:RectSprite;
		private var _xrev:Boolean = false;
		private var _yrev:Boolean = false;
		
		/** The x-axis. */
		public function get xAxis():Axis { return _xaxis; }
		/** The y-axis. */
		public function get yAxis():Axis { return _yaxis; }
		/** Grid line for the origin along the x-axis. */
		public function get xLine():AxisGridLine { return _xline; }
		/** Grid line for the origin along the y-axis. */
		public function get yLine():AxisGridLine { return _yline; }
		
		/** Determines if the x-axis should be in reverse order. */
		public function get xReverse():Boolean { return _xrev; }
		public function set xReverse(b:Boolean):void { _xrev = b; }
		
		/** Determines if the y-axis should be in reverse order. */
		public function get yReverse():Boolean { return _yrev; }
		public function set yReverse(b:Boolean):void { _yrev = b; }
		
		/** The x-coordinate of the axes' origin point. */
		public function get originX():Number { return _xaxis.originX; }
		/** The y-coordinate of the axes' origin point. */
		public function get originY():Number { return _yaxis.originY; }
		
		/** Flag indicating if the x-origin line should be shown. */
		public function get showXLine():Boolean { return _showXLine; }
		public function set showXLine(b:Boolean):void { _showXLine = b; }
		
		/** Flag indicating if the y-origin line should be shown. */
		public function get showYLine():Boolean { return _showYLine; }
		public function set showYLine(b:Boolean):void { _showYLine = b; }
		
		/** Flag indicating if a border for the axes should be shown. */
		public function get showBorder():Boolean { return _border.visible; }
		public function set showBorder(b:Boolean):void { _border.visible = b; }
		
		/** The axes border color. */
		public function get borderColor():uint { return _border.lineColor; }
		public function set borderColor(c:uint):void { _border.lineColor = c; }
		
		/** The line width of the axes border. */
		public function get borderWidth():Number { return _border.lineWidth; }
		public function set borderWidth(w:Number):void { _border.lineWidth = w; }
		
				
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Creates new CartesianAxes.
		 * @param vis the visualization the axes correspond to.
		 */
		public function CartesianAxes(vis:Visualization=null) {
			_vis = vis;
			
			addChild(_xaxis = new Axis());
			addChild(_yaxis = new Axis());
			addChild(_xline = new AxisGridLine());
			addChild(_yline = new AxisGridLine());
			addChild(_border = new RectSprite());
			
			// set names
			_xaxis.name = "_xaxis";
			_yaxis.name = "_yaxis";
			_xline.name = "_xline";
			_yline.name = "_yline";
			_border.name = "_border";
			
			// set label anchors
			_xaxis.horizontalAnchor = TextSprite.CENTER;
			_xaxis.verticalAnchor   = TextSprite.TOP;
			_yaxis.horizontalAnchor = TextSprite.RIGHT;
			_yaxis.verticalAnchor   = TextSprite.MIDDLE;
            
            // set default label offsets
            _xaxis.labelOffsetX =  0; _xaxis.labelOffsetY = 8;
            _yaxis.labelOffsetX = -8; _yaxis.labelOffsetY = 0;

            // set default gridline offsets
            _xaxis.lineOffsetX =  0; _xaxis.lineOffsetY = 5;
            _yaxis.lineOffsetX = -5; _yaxis.lineOffsetY = 0;

            // set default gridline colors
            _xaxis.lineColor = 0xd8d8d8;
            _yaxis.lineColor = 0xd8d8d8;

            // set default line settings
            _xline.lineColor = 0xcccccc;
            _yline.lineColor = 0xcccccc;
            
            // set up border
            _border.lineColor = 0xffd8d8d8;
            _border.fillColor = 0x00ffffff;
		}
		
		/** @inheritDoc */
		public override function update(trans:Transitioner=null):Transitioner
        {
        	var t:Transitioner = (trans!=null ? trans : Transitioner.DEFAULT);
        	var o:Object;
        	var b:Rectangle = layoutBounds.clone();
        	
        	// set x-axis position
        	if (_xrev) {
        		_xaxis.x1 = b.right; _xaxis.y1 = b.bottom;
        		_xaxis.x2 = b.left;  _xaxis.y2 = b.bottom;
        	} else {
        		_xaxis.x1 = b.left;  _xaxis.y1 = b.bottom;
        		_xaxis.x2 = b.right; _xaxis.y2 = b.bottom;
        	}

			// set y-axis position
			if (_yrev) {
				_yaxis.x1 = b.left;  _yaxis.y1 = b.top;
        		_yaxis.x2 = b.left;  _yaxis.y2 = b.bottom;
   			} else {
   				_yaxis.x1 = b.left;  _yaxis.y1 = b.bottom;
        		_yaxis.x2 = b.left;  _yaxis.y2 = b.top;
   			}
        	
        	// gridline bias
        	_xaxis.lineBiasX = 0; _xaxis.lineBiasY = -b.height;
        	_yaxis.lineBiasX = b.width; _yaxis.lineBiasY = 0;

			// update axes
			_xaxis.update(t);
			_yaxis.update(t);

			// update x-axis origin line
			var yx:Number = _yaxis.offsetX(0);
			var yy:Number = _yaxis.offsetY(0);
			var ys:Boolean = _showXLine && yx >= 0 && yx <= b.width;
			o = t.$(_xline);
			o.x1 = _xaxis.x1 + yx;
			o.y1 = _xaxis.y1 + yy;
			o.x2 = _xaxis.x2 + yx;
			o.y2 = _xaxis.y2 + yy;
			o.alpha = ys ? 1 : 0;
			
			// update y-axis origin line
			var xx:Number = _xaxis.offsetX(0);
			var xy:Number = _xaxis.offsetY(0);
			var xs:Boolean = _showYLine && xy >= 0 && xy <= b.height;
			o = t.$(_yline);
			o.x1 = _yaxis.x1 + xx;
			o.y1 = _yaxis.y1 + xy;
			o.x2 = _yaxis.x2 + xx;
			o.y2 = _yaxis.y2 + xy;
			o.alpha = xs ? 1 : 0;

			// update axis border
			o = t.$(_border);
			o.x = b.x;
			o.y = b.y;
			o.w = b.width;
			o.h = b.height;
			
			// set the gridline clipping region
			b.width += 1; b.height += 1;
			_xaxis.gridLines.scrollRect = b;
			_yaxis.gridLines.scrollRect = b;

            return trans;
        }
		
	} // end of class CartesianAxes
}