package flare.display
{
	/**
	 * A Sprite representing a line. Supports position, color, and width
	 * properties.
	 */
	public class LineSprite extends DirtySprite
	{
		private var _color:uint = 0xcccccc;
		private var _width:Number = 0;
		private var _x1:Number;
		private var _y1:Number;
		private var _x2:Number;
		private var _y2:Number;
		
		/** The x-coordinate for the first line endpoint. */
		public function get x1():Number  { return _x1; }
		public function set x1(x:Number):void { _x1 = x; dirty(); }
		
		/** The y-coordinate for the first line endpoint. */
		public function get y1():Number  { return _y1; }
		public function set y1(y:Number):void { _y1 = y; dirty(); }
		
		/** The x-coordinate for the second line endpoint. */
		public function get x2():Number  { return _x2; }
		public function set x2(x:Number):void { _x2 = x; dirty(); }

		/** The y-coordinate for the second line endpoint. */		
		public function get y2():Number  { return _y2; }
		public function set y2(y:Number):void { _y2 = y; dirty(); }
		
		/** The color of the line. */
		public function get lineColor():uint  { return _color; }
		public function set lineColor(c:uint):void { _color = c; dirty(); }
		
		/** The width of the line. A value of zero indicates a hairwidth line,
		 *  as determined by <code>Graphics.lineStyle</code> */
		public function get lineWidth():Number  { return _width; }
		public function set lineWidth(w:Number):void { _width = w; dirty(); }
		
		public override function render():void
		{
			graphics.clear();
			graphics.lineStyle(_width, _color, 1, true, "none");
			graphics.moveTo(_x1, _y1);
			graphics.lineTo(_x2, _y2);
		}
		
	} // end of class LineSprite
}