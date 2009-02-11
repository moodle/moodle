package flare.vis.legend
{
	import flare.display.DirtySprite;
	import flare.display.RectSprite;
	import flash.display.Shape;
	import flare.display.TextSprite;
	import flash.display.Graphics;
	import flare.vis.util.graphics.Shapes;
	import flash.display.Sprite;

	/**
	 * An item in a discrete legend consisting of a label and
	 * an icon indicating color, shape, and/or size.
	 */
	public class LegendItem extends RectSprite
	{
		private var _value:Object;
		
		private var _icon:Shape;
		private var _iconLineWidth:Number = 2;
		private var _label:TextSprite;
		private var _text:String;
		
		private var _iconSize:Number = 12;
		private var _margin:Number = 5;
		
		private var _shape:Function;
		private var _color:uint;
		private var _isize:Number; // should be a value between 0 and 1
		
		private var _selected:Boolean = false;
		private var _backgroundColor:uint = 0xffffff;
		
		// -- Properties ------------------------------------------------------
		
		/** The data value represented by this legend item. */
		public function get value():Object { return _value; }
		public function set value(v:Object):void { _value = v; }
		
		/** Shape presenting this legend item's icon. */
		public function get icon():Shape { return _icon; }
		/** TextSprite presenting this legend item's label. */
		public function get label():TextSprite { return _label; }
		
		/** The label text. */
		public function get text():String { return _text; }
		public function set text(t:String):void {
			if (t != _text) { _text = t; _label.text = t; dirty(); }
		}
		
		/** Line width to use within the icon. */
		public function get iconLineWidth():Number { return _iconLineWidth; }
		public function set iconLineWidth(s:Number):void {
			if (s != _iconLineWidth) {
				_iconLineWidth = s; _h = innerHeight; dirty();
			}
		}
		
		/** Size parameter for icon drawing. */
		public function get iconSize():Number { return _iconSize; }
		public function set iconSize(s:Number):void {
			if (s != _iconSize) {
				_iconSize = s; _h = innerHeight; dirty();
			}
		}
		
		/** Margin value for padding within the legend item. */
		public function get margin():Number { return _margin; }
		public function set margin(m:Number):void {
			if (m != _margin) {
				_margin = m; _h = innerHeight; dirty();
			}
		}
		
		/** The inner width of this legend item. */
		public function get innerWidth():Number {
			return 2*_margin + _iconSize + 
				(_label.text.length>0 ? 2*_margin + _label.width : 0);
		}
		/** The inner height of this legend item. */
		public function get innerHeight():Number {
			return 2*_margin + _iconSize; // _label.height
		}
		
		/** Flag indicating if this legend item has been selected. */
		public function get selected():Boolean { return _selected; }
		public function set selected(b:Boolean):void { _selected = b; }
		
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Creates a new LegendItem.
		 * @param text the label text
		 * @param color the color of the label icon
		 * @param shape a shape drawing function for the label icon
		 * @param iconScale a size parameter for drawing the label icon
		 */
		public function LegendItem(text:String=null, color:uint=0xff000000,
								   shape:Function=null, iconScale:Number = 1)
		{
			addChild(_icon = new Shape());
			addChild(_label = new TextSprite(_text=text));
			
			// init background
			super(0,0,0, 2*_margin + _iconSize, 13, 13);
			lineColor = 0x00000000;
			fillColor = 0x00ffffff;
			
			// init label
			_label.verticalAnchor = TextSprite.MIDDLE;
			_label.mouseEnabled = false;
			
			// init icon
			_color = color;
			_shape = shape;
			_isize = iconScale;
		}
		
		/** @inheritDoc */
		public override function render():void
		{			
			// layout label
			_label.x = 2*_margin + _iconSize;
			_label.y = _margin + _iconSize / 2;
			// TODO compute text abbrev as needed
			
			// layout icon
			_icon.x = _margin + _iconSize/2;
			_icon.y = _margin + _iconSize/2;
			
			// render icon
			var size:Number = _iconSize * _isize/2;
			var g:Graphics = _icon.graphics;
			g.clear();
			if (_shape != null) {
				g.lineStyle(_iconLineWidth, _color, 1);
				_shape(g, size);
			} else {
				g.beginFill(_color);
				g.lineStyle(1, 0xcccccc);
				Shapes.square(g, size);
				g.endFill();
			}
			
			super.render();
		}
		
	} // end of class LegendItem
}