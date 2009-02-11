package flare.display
{
	import flare.util.Colors;
	
	/**
	 * A Sprite representing a rectangle shape. Supports line and fill colors
	 * and rounded corners.
	 */
	public class RectSprite extends DirtySprite
	{
		/** @private */
		protected var _w:Number;
		/** @private */
		protected var _h:Number;
		/** @private */
		protected var _cw:Number = 0;
		/** @private */
		protected var _ch:Number = 0;
		/** @private */
		protected var _fillColor:uint = 0x00ffffff;
		/** @private */
		protected var _lineColor:uint = 0xffaaaaaa;
		/** @private */
		protected var _lineWidth:Number = 0;
		/** @private */
		protected var _pixelHinting:Boolean = true;
		
		/** The width of the rectangle. */
		public function get w():Number { return _w; }
		public function set w(v:Number):void { _w = v; dirty(); }
		
		/** The height of the rectangle. */
		public function get h():Number { return _h; }
		public function set h(v:Number):void { _h = v; dirty(); }
		
		/** The width of rounded corners. Zero indicates no rounding. */
		public function get cornerWidth():Number { return _cw; }
		public function set cornerWidth(v:Number):void { _cw = v; dirty(); }
		
		/** The height of rounded corners. Zero indicates no rounding. */
		public function get cornerHeight():Number { return _ch; }
		public function set cornerHeight(v:Number):void { _ch = v; dirty(); }
		
		/** Sets corner width and height simultaneously. */
		public function set cornerSize(v:Number):void { _cw = _ch = v; dirty(); }
		
		/** The fill color of the rectangle. */
		public function get fillColor():uint { return _fillColor; }
		public function set fillColor(c:uint):void { _fillColor = c; dirty(); }
		
		/** The line color of the rectangle outline. */
		public function get lineColor():uint { return _lineColor; }
		public function set lineColor(c:uint):void { _lineColor = c; dirty(); }
		
		/** The line width of the rectangle outline. */
		public function get lineWidth():Number { return _lineWidth; }
		public function set lineWidth(v:Number):void { _lineWidth = v; dirty(); }
		
		/** Flag indicating if pixel hinting should be used for the outline. */
		public function get linePixelHinting():Boolean { return _pixelHinting; }
		public function set linePixelHinting(b:Boolean):void {
			_pixelHinting = b; dirty();
		}
				
		/**
		 * Creates a new RectSprite.
		 * @param x the x-coordinate of the top-left corner of the rectangle
		 * @param y the y-coordinate of the top-left corder of the rectangle
		 * @param w the width of the rectangle
		 * @param h the height of the rectangle
		 * @param cw the width of rounded corners (zero for no rounding)
		 * @param ch the height of rounded corners (zero for no rounding)
		 */
		public function RectSprite(x:Number=0, y:Number=0, w:Number=0,
			h:Number=0, cw:Number=0, ch:Number=0)
		{
			this.x = x;
			this.y = y;
			this._w = w;
			this._h = h;
			this._cw = cw;
			this._ch = ch;
		}
		
		/** @inheritDoc */
		public override function render():void
		{
			graphics.clear();
			if (isNaN(_w) || isNaN(_h)) return;
			
			var la:Number = Colors.a(_lineColor) / 255;
			var fa:Number = Colors.a(_fillColor) / 255;
			var lc:uint = _lineColor & 0x00ffffff;
			var fc:uint = _fillColor & 0x00ffffff;

			if (la>0) graphics.lineStyle(_lineWidth, lc, la, _pixelHinting);
			graphics.beginFill(fc, fa);
			if (_cw > 0 || _ch > 0) {
				graphics.drawRoundRect(0, 0, _w, _h, _cw, _ch);
			} else {
				graphics.drawRect(0, 0, _w, _h);
			}
			graphics.endFill();
		}
		
	} // end of class RectSprite
}