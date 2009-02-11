package flare.vis.legend
{
	import flare.animate.Transitioner;
	import flare.display.RectSprite;
	import flare.display.TextSprite;
	import flare.vis.Visualization;
	import flare.vis.operator.IOperator;
	import flare.vis.operator.encoder.ColorEncoder;
	import flare.vis.operator.encoder.Encoder;
	import flare.vis.operator.encoder.ShapeEncoder;
	import flare.vis.operator.encoder.SizeEncoder;
	import flare.vis.palette.ColorPalette;
	import flare.vis.palette.ShapePalette;
	import flare.vis.palette.SizePalette;
	import flare.vis.scale.OrdinalScale;
	import flare.vis.scale.Scale;
	
	import flash.display.Sprite;
	import flash.geom.Rectangle;
	import flash.text.TextFormat;
	
	/**
	 * A legend describing the visual encoding of a data property. The Legend
	 * class supports discrete legends that list individual items and
	 * continuous legends the convey a continuous range of values.
	 */
	public class Legend extends Sprite
	{
		/** Constant indicating a left-to-right orientation */
		public static const LEFT_TO_RIGHT:uint = 0;
		/** Constant indicating a right-to-left orientation */
		public static const RIGHT_TO_LEFT:uint = 1;
		/** Constant indicating a top-to-bottom orientation */
		public static const TOP_TO_BOTTOM:uint = 2;
		/** Constant indicating a bottom-to-top orientation */
		public static const BOTTOM_TO_TOP:uint = 3;

		/** The layout bounds for this legend instance. */
		protected var _bounds:Rectangle = new Rectangle(0, 0, 200, 500);
		/** The data field this legend describes. */
		protected var _dataField:String;

		/** Sprite defining the border of the legend. */
		protected var _border:RectSprite;
		/** Sprite containing the legend items. */
		protected var _items:Sprite;
		/** TextSprite containing the legend title.*/
		protected var _title:TextSprite;
		
		/** Scale instance used to define the legend mapping. */
		protected var _scale:Scale;
		/** Flag indicating if this legend is discrete or continuous. */
		protected var _discrete:Boolean = true;
		
		/** The default color to use for legend items. */
		protected var _defaultColor:uint = 0xffbbbbbb;
		/** The color palette used to encode values (may be null). */
		protected var _colors:ColorPalette;
		/** The shape palette used to encode values (may be null). */
		protected var _shapes:ShapePalette;
		/** The size palette used to encode values (may be null). */
		protected var _sizes:SizePalette;
		
		/** Flag indicating the desired orientation of this legend. */
		protected var _orient:int = TOP_TO_BOTTOM;
		/** Margin spacing value. */
		protected var _margin:Number = 2;
		
		/** Label formatting string for legend items. */
		protected var _labelFormat:String = null;
		/** TextFormat (font, size, style) of legend item labels. */
		protected var _labelTextFormat:TextFormat = new TextFormat("Arial",12,0);
	
		/** The calculated internal width of the legend. */
		protected var _iw:Number;
		/** The calculated internal height of the legend. */
		protected var _ih:Number;

		// -- Properties ------------------------------------------------------

		/** The data field this legend describes. */
		public function get dataField():String { return _dataField; }

		/** The layout bounds for this legend instance. */
		public function get layoutBounds():Rectangle { return _bounds; }
		public function set layoutBounds(b:Rectangle):void { _bounds = b; }
		
		/** Sprite defining the border of the legend. */
		public function get border():RectSprite { return _border; }
		/** Sprite containing the legend items. */
		public function get items():Sprite { return _items; }
		/** TextSprite containing the legend title.*/
		public function get title():TextSprite { return _title; }
		
		/** Flag indicating if this legend is discrete or continuous. */
		public function get discrete():Boolean { return _discrete; }
		
		/** Scale instance used to define the legend mapping. */
		public function get scale():Scale { return _scale; }
		public function set scale(s:Scale):void {
			_scale = s; 
			_discrete = s ? _scale is OrdinalScale : true;
		}
		/** The LegendRange for this legend, if it is continuous. This
		 *  value is null if the legend is discrete. */
		public function get range():LegendRange {
			return _discrete ? null : LegendRange(_items.getChildAt(0));
		}

		/** The default color to use for legend items. */		
		public function get defaultColor():uint { return _defaultColor; }
		public function set defaultColor(c:uint):void { _defaultColor = c; }

		/** The color palette used to encode values (may be null). */		
		public function get colorPalette():ColorPalette { return _colors; }
		public function set colorPalette(cp:ColorPalette):void { _colors = cp; }

		/** The shape palette used to encode values (may be null). */		
		public function get shapePalette():ShapePalette { return _shapes; }
		public function set shapePalette(sp:ShapePalette):void { _shapes = sp; }

		/** The size palette used to encode values (may be null). */		
		public function get sizePalette():SizePalette { return _sizes; }
		public function set sizePalette(sp:SizePalette):void { _sizes = sp; }
		
		/** Flag indicating the desired orientation of this legend. */
		public function get orientation():int { return _orient; }
		public function set orientation(o:int):void { _orient = o; }

		/** Margin spacing value. */		
		public function get margin():Number { return _margin; }
		public function set margin(m:Number):void { _margin = m; }

		/** TextFormat (font, size, style) of legend item labels. */		
		public function get labelTextFormat():TextFormat { return _labelTextFormat; }
		public function set labelTextFormat(f:TextFormat):void {
			_labelTextFormat = f; updateItems();
		}

		/** Label formatting string for legend items. */		
		public function get labelFormat():String {
			return _labelFormat==null ? null 
					: _labelFormat.substring(3, _labelFormat.length-1);
		}
		public function set labelFormat(fmt:String):void {
			_labelFormat = "{0:"+fmt+"}"; updateItems();
		}
		
		// -- Initialization --------------------------------------------------
		
		/**
		 * Creates a new Legend for the given data field.
		 * @param dataField the data field to describe with the legend
		 * @param vis the visualization corresponding to this legend
		 * @param scale the scale value used to map the data field to visual
		 *  variables
		 */
		public function Legend(dataField:String, scale:Scale=null,
			colors:ColorPalette=null, shapes:ShapePalette=null,
			sizes:SizePalette=null)
		{
			_dataField = dataField;
			this.scale = scale;
			addChild(_border = new RectSprite(0,0,0,0,13,13));
			addChild(_title = new TextSprite());
			addChild(_items = new Sprite());
			
			_colors = colors;
			_shapes = shapes;
			_sizes = sizes;
			
			_title.textField.defaultTextFormat =
				new TextFormat("Helvetica,Arial",12,null,true);
			
			update();
		}
		
		// -- Updates ---------------------------------------------------------
		
		/**
		 * Update the legend, recomputing filtering and layout of items.
		 * @param trans a transitioner for value updates
		 * @return the input transitioner
		 */
		public function update(trans:Transitioner=null) : Transitioner
		{
			var _t:Transitioner = trans!=null ? trans : Transitioner.DEFAULT;
			filter(_t);
			if (_discrete) updateItems(); // TEMP
			layout(_t);
			return trans;
		}
		
		// -- Filter ----------------------------------------------------------
		
		/**
		 * Performs filtering, determining the items contained in the legend.
		 * @param trans a transitioner for value updates
		 */
		protected function filter(trans:Transitioner) : void
		{
			// first, remove all items
			while (_items.numChildren > 0) {
				_items.removeChildAt(_items.numChildren-1);
			}
			
			var item:LegendItem;
			
			if (_discrete) {
				var vals:Array = _scale.values(1000000);
				for (var i:uint=0; i<vals.length; ++i) {
					var f:Number = _scale.interpolate(vals[i]);
					if (true) {
						item = new LegendItem(
							_scale.label(vals[i]),
							_colors==null ? _defaultColor : _colors.getColor(f),
							_shapes==null ? null : _shapes.getShape(i),
							_sizes==null ? 1 : _sizes.getSize(f)
						);
						item.value = vals[i];
						_items.addChild(item);
					}
				}
			} else {
				_items.addChild(new LegendRange(_dataField, _colors, _scale));
			}
		}
		
		// -- Layout ----------------------------------------------------------
		
		/**
		 * Performs layout, setting the position for all items in the legend.
		 * @param trans a transitioner for value updates
		 */
		protected function layout(trans:Transitioner) : void
		{
			var vert:Boolean = _orient==TOP_TO_BOTTOM || _orient==BOTTOM_TO_TOP;
			var o:Object;
			var b:Rectangle = layoutBounds;
			var x:Number = 0, y:Number = 0, th:Number = 0;
			
			// position the legend
			o = trans.$(this);
			o.x = b.left;
			o.y = b.top;
			
			// layout the title
			if (_title.text != null && _title.text.length > 0) {
				trans.$(_title).x = _margin;
				trans.$(_title).alpha = 1;
				y += (th = _title.height);
			} else {
				trans.$(_title).alpha = 0;
			}
			
			// layout item container
			o = trans.$(_items);
			o.x = x;
			o.y = y;
			
			// layout items
			if (_discrete) {
				layoutItemsDiscrete(trans);
			} else {
				layoutItemsContinuous(trans);
			}
			
			x = x + (vert ? b.width : Math.min(_iw, b.width));
			y = y + (vert ? Math.min(_ih, b.height) : _ih);
			
			// size the border
			o = trans.$(_border);
			o.w = x;
			o.h = y;
			if (trans.immediate) _border.render();
			
			// create clipping panel
			trans.$(items).scrollRect =
				new Rectangle(0, 0, 1+x, 1+y-th);
		}
		
		// -- Legend Items ----------------------------------------------------
		
		/**
		 * Layout helper for positioning discrete legend items.
		 * @param trans a transitioner for value updates
		 */
		protected function layoutItemsDiscrete(trans:Transitioner):void
		{
			var vert:Boolean = _orient==TOP_TO_BOTTOM || _orient==BOTTOM_TO_TOP;
			var x:Number = 0;
			var y:Number = 0;
			var item:LegendItem;
			var o:Object;
			var b:Rectangle = layoutBounds;
			
			_iw = _ih = 0;
			for (var i:uint=0; i<_items.numChildren; ++i) {
				// layout the item
				item = _items.getChildAt(i) as LegendItem;
				o = trans.$(item);
				o.x = x;
				o.y = y;
				o.w = vert ? b.width : item.innerWidth;
				
				// increment spacing
				if (vert) {
					y += item.innerHeight;
					_iw = Math.max(_iw, item.innerWidth);
				}
				else {
					x += item.innerWidth;
					_ih = Math.max(_ih, item.innerHeight);
				}
			}
			_iw = vert ? _iw : x;
			_ih = vert ? y : _ih;
		}
		
		/**
		 * Layout helper for positioning a continous legend range.
		 * @param trans a transitioner for value updates
		 */
		protected function layoutItemsContinuous(trans:Transitioner):void
		{
			var lr:LegendRange = _items.getChildAt(0) as LegendRange;
			_iw = lr.w = layoutBounds.width;
			lr.updateLabels();
			_ih = lr.height + lr.margin;
		}
		
		/**
		 * Updates an individual legend item. Currently only the text format
		 * is updated.
		 * @param item the legend item to update
		 */
		protected function updateItem(item:LegendItem) : void
		{
			item.label.setTextFormat(_labelTextFormat);
			/*
			label.text = _labelFormat==null ? label.value.toString()
					   : Strings.format(_labelFormat, label.value);
			*/
		}
		
		/**
		 * Updates all individual legend items.
		 */
		protected function updateItems() : void
		{
			for (var i:uint = 0; i<_items.numChildren; ++i) {
				updateItem(_items.getChildAt(i) as LegendItem);
			}
		}
		
		// -- Static Constructor ----------------------------------------------
		
		/**
		 * Generates a Legend from a Visualization instance by analyzing the
		 * visualization operator chain.
		 * @param field the data field for which to create the legend
		 * @param vis the visualization to analyze
		 * @return a generated legend, or null if the data field is not
		 *  visually encoded.
		 */
		public static function fromVis(field:String, vis:Visualization):Legend
		{
			var colors:ColorPalette;
			var sizes:SizePalette;
			var shapes:ShapePalette;
			var scale:Scale;
			
			for (var i:int=0; i<vis.operators.length; ++i) {
				var op:IOperator = vis.operators[i];
				if (op is ColorEncoder) {
					colors = ColorEncoder(op).colors;
					scale = Encoder(op).scale;
				} else if (op is SizeEncoder) {
					sizes = SizeEncoder(op).sizes;
					scale = Encoder(op).scale;
				} else if (op is ShapeEncoder) {
					shapes = ShapeEncoder(op).shapes;
					scale = Encoder(op).scale;
				}
			}
			
			if (scale != null) {
				return new Legend(field, scale, colors, shapes, sizes);
			} else {
				return null;
			}
		}
		
	} // end of class Legend
}