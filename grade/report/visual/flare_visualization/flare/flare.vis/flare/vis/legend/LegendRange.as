package flare.vis.legend
{
	import flash.display.Sprite;
	import flare.vis.scale.Scale;
	import flare.vis.palette.ColorPalette;
	import flare.display.DirtySprite;
	import flash.display.Graphics;
	import flare.util.Colors;
	import flash.display.GradientType;
	import flash.geom.Matrix;
	import flash.display.Shape;
	import flare.display.RectSprite;
	import flare.display.TextSprite;
	import flash.text.TextFormat;
	import flare.vis.scale.IScaleMap;
	import flash.geom.Rectangle;
	import flare.vis.data.Data;
	import flare.util.Stats;
	import flare.util.Maths;

	/**
	 * A range in a continuous legend, consisting of a continuous
	 * visual scale and value labels.
	 */
	public class LegendRange extends RectSprite implements IScaleMap
	{
		private var _dataField:String;
		private var _scale:Scale;
		private var _stats:Stats;
		private var _palette:ColorPalette;
		private var _matrix:Matrix = new Matrix();
		private var _margin:Number = 5;
		
		private var _labels:Sprite;
		private var _fmt:TextFormat;
		
		private var _range:Shape;
		private var _rh:Number = 20;
		private var _borderColor:uint = 0xcccccc;
		
		/** The data field described by this legend range. */
		public function get dataField():String { return _dataField; }
		
		/** Sprite containing the range's labels. */
		public function get labels():Sprite { return _labels; }
		
		/** Stats object describing the data range. */
		public function get stats():Stats { return _stats; }
		public function set stats(s:Stats):void { _stats = s; }
		
		/** TextFormat (font, size, style) of legend range labels. */
		public function get labelTextFormat():TextFormat { return _fmt; }
		public function set labelTextFormat(fmt:TextFormat):void {
			_fmt = fmt; dirty();
		}
		
		/** Margin value for padding within the legend item. */
		public function get margin():Number { return _margin; }
		public function set margin(m:Number):void {
			_margin = m; dirty();
		}
		
		/** The color of the legend range border. */
		public function get borderColor():uint { return _borderColor; }
		public function set borderColor(c:uint):void { _borderColor = c; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new LegendRange.
		 * @param dataField the data field described by this range
		 * @param palette the color palette for the data field
		 * @param scale the Scale instance mapping the data field to a visual
		 *  variable
		 */
		public function LegendRange(dataField:String, palette:ColorPalette, scale:Scale)
		{
			_dataField = dataField;
			_palette = palette;
			_scale = scale;
			addChild(_range = new Shape());
			addChild(_labels = new Sprite());
			_range.cacheAsBitmap = true;
		}
		
		// --------------------------------------------------------------------
		// Lookup
		
		/** @inheritDoc */
		public function get x1():Number { return _margin; }
		/** @inheritDoc */
		public function get x2():Number { return _w - _margin; }
		/** @inheritDoc */
		public function get y1():Number { return _margin; }
		/** @inheritDoc */
		public function get y2():Number { return _margin + _rh; }
		
		private var _bounds:Rectangle = new Rectangle();
				
		/**
		 * Bounds for the visual range portion of this legend range.
		 * @return the bounds of the range display
		 */
		public function get bounds():Rectangle {
			_bounds.x = x1;
			_bounds.y = y1;
			_bounds.width = x2 - x1;
			_bounds.height = y2 - y1;
			return _bounds;
		}
		
		/** @inheritDoc */
		public function value(x:Number, y:Number, stayInBounds:Boolean=true):Object
        {
        	var f:Number = (x-_margin) / (_w - 2*_margin);
        	// correct bounds
        	if (stayInBounds) {
        		if (f < 0) f = 0;
        		if (f > 1) f = 1;
        	}
        	// lookup and return value
        	return _scale.lookup(f);
        }
        
        /** @inheritDoc */
        public function X(val:Object):Number
        {
        	return x1 + _scale.interpolate(val) * (x2 - x1);	
        }
        
        /** @inheritDoc */
        public function Y(val:Object):Number
        {
        	return y1;
        }
		
		// --------------------------------------------------------------------
		// Layout and Render
		
		/**
		 * Update the labels shown by this legend range.
		 */
		public function updateLabels():void
		{
			var pts:Array = _palette==null ? [0,1] : _palette.keyframes;
			var n:int = pts.length;
			
			// filter for the needed number of labels
			for (var i:int=_labels.numChildren; i<n; ++i) {
				_labels.addChild(new TextSprite());
			}
			for (i=_labels.numChildren; --i>=n;) {
				_labels.removeChildAt(i);
			}
			// update and layout the labels
			for (i=0; i<n; ++i) {
				var ts:TextSprite = TextSprite(_labels.getChildAt(i));
				var val:Object = _scale.lookup(pts[i]);
				// set format
				if (_fmt != null) ts.setTextFormat(_fmt);
				// set text
				ts.text = _scale.label(val);
				// set alignement
				if (i==0) {
					ts.horizontalAnchor = TextSprite.LEFT;
				} else if (i==n-1) {
					ts.horizontalAnchor = TextSprite.RIGHT;
				} else {
					ts.horizontalAnchor = TextSprite.CENTER;
				}
				// set position
				ts.x = X(val);
				ts.y = y2;
				ts.render();
			}
			// adjust visibility based on overlap
			// TODO
		}
		
		/** @inheritDoc */
		public override function render():void
		{
			updateLabels();
			
			var w:Number = _w - 2*_margin;
			var h:Number = 20;
			_range.x = _margin;
			_range.y = _margin;
			this.h = 2*margin + h + _labels.height;
			
			_range.graphics.clear();
			
			if (_palette != null) {
				drawPalette(w, h);
			} else if (_stats != null) {
				drawHistogram(w, h);
			}
			
			_range.graphics.lineStyle(0, _borderColor);
			_range.graphics.drawRect(0, 0, w, h);
		}
		
		/**
		 * Draws a histogram of data values in the range dispay.
		 * @param w the width of the range display
		 * @param h the height of the range display
		 */
		protected function drawHistogram(w:Number, h:Number):void
		{
			var values:Array = _stats.values;
			var iw:int = int(w/2);
			var i:int, pw:int = w / iw, f:Number;
			
			var counts:Array = new Array(iw);
			for (i=0; i<counts.length; ++i) counts[i] = 0;
			
			for (i=0; i<values.length; ++i) {
				f = _scale.interpolate(values[i]);
				var idx:int = int(Math.round(f*(iw-1)));
				counts[idx]++;
			}
			
			var max:Number = 0;
			for (i=0; i<counts.length; ++i) {
				if (counts[i] > max) max = counts[i];
			}
			max =  h / (1.1*max);
			
			var g:Graphics = _range.graphics;
			var v:Number, x:Number;
			for (i=0; i<iw; ++i) {
				g.beginFill(0xcccccc, 1);
				x = (i/iw) * w;
				v = counts[i] * max;
				g.drawRect(x,h,pw,-v);
				g.endFill();
			}
		}
		
		/**
		 * Draws a continuous color range in the range display.
		 * @param w the width of the range display
		 * @param h the height of the range display
		 */
		protected function drawPalette(w:Number, h:Number):void
		{
			// build gradient paint parameters
			var N:int = _palette.keyframes.length;
			var colors:Array = new Array(N);
			var alphas:Array = new Array(N);
			var ratios:Array = new Array(N);
			for (var i:int=0; i<N; ++i) {
				var c:uint = _palette.getColor(_palette.keyframes[i]);
				colors[i] = 0x00ffffff & c;
				alphas[i] = Colors.a(c) / 255;
				ratios[i] = int(255 * _palette.keyframes[i]);
			}
			_matrix.createGradientBox(w, h);
			
			// paint the color palette
			var g:Graphics = _range.graphics;
			g.beginGradientFill(GradientType.LINEAR,
				colors, alphas, ratios, _matrix);
			g.drawRect(0, 0, w, h);
			g.endFill();
		}
		
	} // end of class LegendRange
}