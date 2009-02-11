package flare.vis.axis
{
	import flare.animate.Transitioner;
	import flare.display.TextSprite;
	import flare.util.Arrays;
	import flare.util.Stats;
	import flare.util.Strings;
	import flare.vis.scale.IScaleMap;
	import flare.vis.scale.LinearScale;
	import flare.vis.scale.LogScale;
	import flare.vis.scale.OrdinalScale;
	import flare.vis.scale.QuantitativeScale;
	import flare.vis.scale.Scale;
	
	import flash.display.DisplayObject;
	import flash.display.Sprite;
	import flash.geom.Point;
	import flash.text.TextFormat;
	import flash.utils.Dictionary;

	/**
	 * A linear, metric data axis, consisting of axis labels and gridlines.
	 */
	public class Axis extends Sprite implements IScaleMap
	{
		// children indices
		private static const LABELS:uint = 1;
        private static const GRIDLINES:uint = 0;
		
		// axis scale
		private var _axisScale:Scale;
		private var _prevScale:Scale;
		// axis settings
		private var _xa:Number, _ya:Number;    // start of the axis
		private var _xb:Number, _yb:Number;    // end of the axis
		private var _xgb:Number, _ygb:Number;  // gridline bias
		private var _xgo:Number, _ygo:Number;  // gridline offset
		private var _xlo:Number, _ylo:Number;  // label offset
		private var _showGrid:Boolean = true;
		private var _showLabels:Boolean = true;
		// gridline settings
		private var _lineColor:uint = 0xd8d8d8;
		private var _lineWidth:Number = 0;
		// label settings
		private var _fixOverlap:Boolean = true;
		private var _numLabels:int = -1;//10;
		private var _anchorH:int = TextSprite.LEFT;
		private var _anchorV:int = TextSprite.TOP;
		private var _labelAngle:Number = 0;
		private var _labelColor:uint = 0;
		private var _labelFormat:String = null;
		private var _labelTextMode:int = TextSprite.BITMAP;
		private var _labelTextFormat:TextFormat = new TextFormat("Arial",12,0);
		// temporary variables
		private var _point:Point = new Point();
		
		// -- Properties ------------------------------------------------------
		
		/** Sprite containing the axis labels. */
		public function get labels():Sprite { return this.getChildAt(LABELS) as Sprite; }
		/** Sprite containing the axis grid lines. */
		public function get gridLines():Sprite { return this.getChildAt(GRIDLINES) as Sprite; }
		
		/** The Scale used to map values to this axis. */
		public function get axisScale():Scale { return _axisScale; }
		public function set axisScale(s:Scale):void { _axisScale = s; }
		
		/** @inheritDoc */
		public function get x1():Number { return _xa; }
		public function set x1(x:Number):void { _xa = x; }
		
		/** @inheritDoc */
		public function get y1():Number { return _ya; }
		public function set y1(y:Number):void { _ya = y; }
		
		/** @inheritDoc */
		public function get x2():Number { return _xb; }
		public function set x2(x:Number):void { _xb = x; }
		
		/** @inheritDoc */
		public function get y2():Number { return _yb; }
		public function set y2(y:Number):void { _yb = y; }

		/** Flag indicating if axis labels should be shown. */
		public function get showLabels():Boolean { return _showLabels; }
		public function set showLabels(b:Boolean):void { _showLabels = b; }
		
		/** Flag indicating if labels should be removed in case of overlap. */
		public function get fixLabelOverlap():Boolean { return _fixOverlap; }
		public function set fixLabelOverlap(b:Boolean):void { _fixOverlap = b; }
		
		/** Flag indicating if axis grid lines should be shown. */
		public function get showLines():Boolean { return _showGrid; }
		public function set showLines(b:Boolean):void { _showGrid = b; }
		
		/** X-dimension length of axis gridlines. */
		public function get lineBiasX():Number { return _xgb; }
		public function set lineBiasX(x:Number):void { _xgb = x; }
		
		/** Y-dimension length of axis gridlines. */
		public function get lineBiasY():Number { return _ygb; }
		public function set lineBiasY(y:Number):void { _ygb = y; }
		
		/** X-dimension offset value for axis gridlines. */
		public function get lineOffsetX():Number { return _xgo; }
		public function set lineOffsetX(x:Number):void { _xgo = x; }
		
		/** Y-dimension offset value for axis gridlines. */
		public function get lineOffsetY():Number { return _ygo; }
		public function set lineOffsetY(y:Number):void { _ygo = y; }
		
		/** X-dimension offset value for axis labels. */
		public function get labelOffsetX():Number { return _xlo; }
		public function set labelOffsetX(x:Number):void { _xlo = x; }
		
		/** Y-dimension offset value for axis labels. */
		public function get labelOffsetY():Number { return _ylo; }
		public function set labelOffsetY(y:Number):void { _ylo = y; }
		
		/** The line color of axis grid lines. */
		public function get lineColor():uint { return _lineColor; }
		public function set lineColor(c:uint):void { _lineColor = c; updateGridLines(); }
		
		/** The line width of axis grid lines. */
		public function get lineWidth():Number { return _lineWidth; }
		public function set lineWidth(w:Number):void { _lineWidth = w; updateGridLines(); }
		
		/** The color of axis label text. */
		public function get labelColor():uint { return _labelColor; }
		public function set labelColor(c:uint):void { _labelColor = c; updateLabels(); }
		
		/** The angle (orientation) of axis label text. */
		public function get labelAngle():Number { return _labelAngle; }
		public function set labelAngle(a:Number):void { _labelAngle = a; updateLabels(); }
		
		/** TextFormat (font, size, style) for axis label text. */
		public function get labelTextFormat():TextFormat { return _labelTextFormat; }
		public function set labelTextFormat(f:TextFormat):void { _labelTextFormat = f; updateLabels(); }
		
		/** The text rendering mode to use for label TextSprites.
		 *  @see flare.display.TextSprite. */
		public function get labelTextMode():int { return _labelTextMode; }
		public function set labelTextMode(m:int):void { _labelTextMode = m; updateLabels(); }
		
		/** String formatting pattern used for axis labels, overwrites any
		 *  formatting pattern used by the <code>axisScale</code>. If null,
		 *  the foramtting pattern for the <code>axisScale</code> is used. */
		public function get labelFormat():String {
			return _labelFormat==null ? null 
					: _labelFormat.substring(3, _labelFormat.length-1);
		}
		public function set labelFormat(fmt:String):void {
			_labelFormat = "{0:"+fmt+"}"; updateLabels();
		}
		
		/** The horizontal anchor point for axis labels.
		 *  @see flare.display.TextSprite. */
		public function get horizontalAnchor():int { return _anchorH; }
		public function set horizontalAnchor(a:int):void { _anchorH = a; updateLabels(); }
		
		/** The vertical anchor point for axis labels.
		 *  @see flare.display.TextSprite. */
		public function get verticalAnchor():int { return _anchorV; }
		public function set verticalAnchor(a:int):void { _anchorV = a; updateLabels(); }		
		
		/** The x-coordinate of the axis origin. */
		public function get originX():Number {
			return (_axisScale is QuantitativeScale ? X(0) : _xa);
		}
		/** The y-coordinate of the axis origin. */
		public function get originY():Number {
			return (_axisScale is QuantitativeScale ? Y(0) : _ya);
		}
		
		// -- Initialization --------------------------------------------------
		
		/**
		 * Creates a new Axis.
		 * @param axisScale the axis scale to use. If null, a linear scale
		 *  is assumed.
		 */
		public function Axis(axisScale:Scale=null)
        {
        	if (axisScale == null)
        		axisScale = new LinearScale();
            _axisScale = axisScale;
            _prevScale = axisScale;
            initializeChildren();
        }

		/**
		 * Initializes the child container sprites for labels and grid lines.
		 */
        protected function initializeChildren():void
        {
            addChild(new Sprite()); // add gridlines
            addChild(new Sprite()); // add labels
        }
		
		// -- Updates ---------------------------------------------------------
		
		/**
		 * Updates this axis, performing filtering and layout as needed.
		 * @param trans a Transitioner for collecting value updates
		 * @return the input transitioner.
		 */
		public function update(trans:Transitioner):Transitioner
        {
        	var t:Transitioner = (trans!=null ? trans : Transitioner.DEFAULT);
            filter(t);
            layout(t);
            updateLabels(); // TODO run through transitioner
            return trans;
        }
		
		// -- Lookups ---------------------------------------------------------
		
		/**
		 * Returns the horizontal offset along the axis for the input value.
		 * @param value an input data value
		 * @return the horizontal offset along the axis corresponding to the
		 *  input value. This is the x-position minus <code>x1</code>.
		 */
		public function offsetX(value:Object):Number
        {
        	return _axisScale.interpolate(value) * (_xb - _xa);
        }
        
        /**
		 * Returns the vertical offset along the axis for the input value.
		 * @param value an input data value
		 * @return the vertical offset along the axis corresponding to the
		 *  input value. This is the y-position minus <code>y1</code>.
		 */
        public function offsetY(value:Object):Number
        {
        	return _axisScale.interpolate(value) * (_yb - _ya);
        }

		/** @inheritDoc */
		public function X(value:Object):Number
        {
        	return _xa + offsetX(value);
        }
        
        /** @inheritDoc */
        public function Y(value:Object):Number
        {
        	return _ya + offsetY(value);
        }
        
        /** @inheritDoc */
        public function value(x:Number, y:Number, stayInBounds:Boolean=true):Object
        {
        	// project the input point onto the axis line
        	// (P-A).(B-A) / |B-A|^2 == fractional projection onto axis line
        	var dx:Number = (_xb-_xa);
        	var dy:Number = (_yb-_ya);
        	var f:Number = ((x-_xa)*dx + (y-_ya)*dy) / (dx*dx + dy*dy);
        	// correct bounds, if desired
        	if (stayInBounds) {
        		if (f < 0) f = 0;
        		if (f > 1) f = 1;
        	}
        	// lookup and return value
        	return axisScale.lookup(f);
        }
		
		/**
		 * Clears the previous axis scale used, if cached.
		 */
		public function clearPreviousScale():void
		{
			_prevScale = _axisScale;
		}
		
		// -- Filter ----------------------------------------------------------
		
		/**
		 * Performs filtering, determining which axis labels and grid lines
		 * should be visible.
		 * @param trans a Transitioner for collecting value updates.
		 */
		protected function filter(trans:Transitioner) : void
		{
			var ordinal:uint = 0, i:uint, idx:int = -1, val:Object;
			var label:AxisLabel = null;
			var gline:AxisGridLine = null;
			var nl:uint = labels.numChildren;
			var ng:uint = gridLines.numChildren;
			
			var keepLabels:Array = new Array(nl);
			var keepLines:Array = new Array(ng);
			// TODO: generalize label number determination
			var numLabels:int = _axisScale is OrdinalScale ? -1 : 10;
			var values:Array = _axisScale.values(numLabels);
			
			if (_showLabels) { // process labels
				for (i=0, ordinal=0; i<values.length; ++i) {
					val = values[i];
					if ((idx = findLabel(val, nl)) < 0) {
						label = createLabel(val);
					} else {
						label = labels.getChildAt(idx) as AxisLabel;
						keepLabels[idx] = true;
					}
					label.ordinal = ordinal++;
				}
			}
			if (_showGrid) { // process gridlines
				for (i=0, ordinal=0; i<values.length; ++i) {
					val = values[i];
					if ((idx = findGridLine(val, ng)) < 0) {
						gline = createGridLine(val);
					} else {
						gline = gridLines.getChildAt(idx) as AxisGridLine;
						keepLines[idx] = true;
					}
					gline.ordinal = ordinal++;
				}
			}
			markRemovals(trans, keepLabels, labels);
			markRemovals(trans, keepLines, gridLines);
		}
		
		/**
		 * Marks all items slated for removal from this axis.
		 * @param trans a Transitioner for collecting value updates.
		 * @param keep a Boolean array indicating which items to keep
		 * @param con a container Sprite whose contents should be marked
		 *  for removal
		 */
		protected function markRemovals(trans:Transitioner, keep:Array, con:Sprite) : void
		{
			for (var i:uint = keep.length; --i >= 0; ) {
				if (!keep[i]) trans.removeChild(con.getChildAt(i));
			}
		}
		
		// -- Layout ----------------------------------------------------------
		
		/**
		 * Performs layout, setting the position of labels and grid lines.
		 * @param trans a Transitioner for collecting value updates.
		 */
		protected function layout(trans:Transitioner) : void
		{
			var i:uint, label:AxisLabel, gline:AxisGridLine, p:Point;
			var _lab:Sprite = this.labels;
			var _gls:Sprite = this.gridLines;
			var o:Object;
			
			// layout labels
			for (i=0; i<_lab.numChildren; ++i) {
				label = _lab.getChildAt(i) as AxisLabel;
				p = positionLabel(label, _axisScale);
				
				o = trans.$(label);
				o.x = p.x;
				o.y = p.y;
				o.alpha = trans.willRemove(label) ? 0 : 1;
			}
			// fix label overlap
			if (_fixOverlap) fixOverlap(trans);
			// layout gridlines
			for (i=0; i<_gls.numChildren; ++i) {
				gline = _gls.getChildAt(i) as AxisGridLine;
				p = positionGridLine(gline, _axisScale);
				
				o = trans.$(gline);
				o.x1 = p.x;
				o.y1 = p.y;
				o.x2 = p.x + _xgb - _xgo;
				o.y2 = p.y + _ygb - _ygo;
				o.alpha = trans.willRemove(gline) ? 0 : 1;
			}
			// update previous scale
			_prevScale = _axisScale.clone(); // clone as IScale
		}
		
		// -- Label Overlap ---------------------------------------------------
		
		/**
		 * Eliminates overlap between labels along an axis. 
		 * @param trans a transitioner, potentially storing label positions
		 */		
		protected function fixOverlap(trans:Transitioner):void
		{
			var labs:Array = [], d:DisplayObject, i:int;
			// collect and sort labels
			for (i=0; i<labels.numChildren; ++i) {
				var s:AxisLabel = AxisLabel(labels.getChildAt(i));
				if (!trans.willRemove(s)) labs.push(s);
			}
			if (labs.length == 0) return;
			labs.sortOn("ordinal", Array.NUMERIC);
			
			// stores the labels to remove
			var rem:Dictionary = new Dictionary();
			
			if (_axisScale is LogScale) {
				fixLogOverlap(labs, rem, trans, LogScale(_axisScale));
			}
			
			// maintain min and max if we get down to two
			i = labs.length;
			var min:Object = labs[0];
			var max:Object = labs[i-1];
			var mid:Object = (i&1) ? labs[(i>>1)] : null;

			// fix overlap with an iterative optimization
			// remove every other label with each iteration
			while (hasOverlap(labs, trans)) {
				// reduce labels
				i = labs.length;
				if (mid && i>3 && i<8) { // use min, med, max if we can
					for each (d in labs) rem[d] = d;
					if (rem[min]) delete rem[min];
					if (rem[max]) delete rem[max];
					if (rem[mid]) delete rem[mid];
					labs = [min, mid, max];
				}
				else if (i < 4) { // use min and max if we're down to two
					if (rem[min]) delete rem[min];
					if (rem[max]) delete rem[max];
					for each (d in labs) {
						if (d != min && d != max) rem[d] = d;
					}
					break;
				}
				else { // else remove every odd element
					i = i - (i&1 ? 2 : 1);
					for (; i>0; i-=2) {
						rem[labs[i]] = labs[i];
						labs.splice(i, 1); // remove from array
					}
				}
			}
			
			// remove the deleted labels
			for each (d in rem) {
				trans.$(d).alpha = 0;
				trans.removeChild(d, true);
			}
		}
		
		private static function fixLogOverlap(labs:Array, rem:Dictionary,
			trans:Transitioner, scale:LogScale):void
		{
				var base:int = int(scale.base), i:int, j:int, zidx:int;
				if (!hasOverlap(labs, trans)) return;
				
				// find zero
				zidx = Arrays.binarySearch(labs, 0, "value");
				var neg:Boolean = scale.dataMin < 0;
				var pos:Boolean = scale.dataMax > 0;
				
				// if includes negative, traverse backwards from zero/end
				if (neg) {
					i = (zidx<0 ? labs.length : zidx) - (pos ? 1 : 2);
					for (j=pos?1:2; i>=0; ++j, --i) {
						if (j == base) {
							j = 1;
						} else {
							rem[labs[i]] = labs[i];
							labs.splice(i, 1); --zidx;
						}
					}
				}
				// if includes positive, traverse forwards from zero/start
				if (pos) {
					i = (zidx<0 ? 0 : zidx+1) + (neg ? 0 : 1);
					for (j=neg?1:2; i<labs.length; ++j) {
						if (j == base) {
							j = 1; ++i;
						} else {
							rem[labs[i]] = labs[i];
							labs.splice(i, 1);
						}
					}
				}
		}
		
		private static function hasOverlap(labs:Array, trans:Transitioner):Boolean
		{
			var d:DisplayObject = labs[0], e:DisplayObject;
			for (var i:int=1; i<labs.length; ++i) {
				if (overlaps(trans, d, (e=labs[i]))) return true;
				d = e;
			}
			return false;
		}
		
		/**
		 * Indicates if two display objects overlap, sensitive to any target
		 * values stored in a transitioner.
		 * @param trans a Transitioner, potentially with target values
		 * @param l1 a display object
		 * @param l2 a display object
		 * @return true if the objects overlap (considering values in the
		 *  transitioner, if appropriate), false otherwise
		 */
		private static function overlaps(trans:Transitioner,
			l1:DisplayObject, l2:DisplayObject):Boolean
		{
			if (trans.immediate) return l1.hitTestObject(l2);
			// get original co-ordinates
			var xa:Number = l1.x, ya:Number = l1.y;
			var xb:Number = l2.x, yb:Number = l2.y;
			var o:Object;
			// set to target co-ordinates
			o = trans.$(l1); l1.x = o.x; l1.y = o.y;
			o = trans.$(l2); l2.x = o.x; l2.y = o.y;
			// compute overlap
			var b:Boolean = l1.hitTestObject(l2);
			// reset to original coordinates
			l1.x = xa; l1.y = ya; l2.x = xb; l2.y = yb;
			return b;
		}
		
		// -- Axis Label Helpers ----------------------------------------------
		
		/**
		 * Creates a new axis label.
		 * @param val the value to create the label for
		 * @return an AxisLabel
		 */		
		protected function createLabel(val:Object) : AxisLabel
		{
			var label:AxisLabel = new AxisLabel();
			label.alpha = 0;
			label.value = val;
			var p:Point = positionLabel(label, _prevScale);
			label.x = p.x;
			label.y = p.y;
			updateLabel(label);
			labels.addChild(label);
			return label;
		}
		
		/**
		 * Computes the position of an axis label.
		 * @param label the axis label to layout
		 * @param scale the scale used to map values to the axis
		 * @return a Point with x,y coordinates for the axis label
		 */
		protected function positionLabel(label:AxisLabel, scale:Scale) : Point
		{
			var f:Number = scale.interpolate(label.value);
			_point.x = _xlo + _xa + f*(_xb-_xa);
			_point.y = _ylo + _ya + f*(_yb-_ya);
			return _point;
		}
		
		/**
		 * Updates an axis label's settings
		 * @param label the label to update
		 */		
		protected function updateLabel(label:AxisLabel) : void
		{
			label.setTextFormat(_labelTextFormat);
			label.horizontalAnchor = _anchorH;
			label.verticalAnchor = _anchorV;
			label.rotation = (180/Math.PI) * _labelAngle;
			label.textMode = _labelTextMode;
			label.text = _labelFormat==null ? axisScale.label(label.value)
					   : Strings.format(_labelFormat, label.value);
		}
		
		/**
		 * Updates all axis labels.
		 */		
		protected function updateLabels() : void
		{
			var _labels:Sprite = this.labels;
			for (var i:uint = 0; i<_labels.numChildren; ++i) {
				updateLabel(_labels.getChildAt(i) as AxisLabel);
			}
		}
		
		/**
		 * Returns the index of a label in the label's container sprite for a
		 * given data value.
		 * @param val the data value to find
		 * @param len the number of labels to check
		 * @return the index of a label with matching value, or -1 if no label
		 *  was found
		 */		
		protected function findLabel(val:Object, len:uint) : int
		{
			var _labels:Sprite = this.labels;
			for (var i:uint = 0; i < len; ++i) {
				// TODO: make this robust to repeated values
				if (Stats.equal((_labels.getChildAt(i) as AxisLabel).value, val)) {
					return i;
				}
			}
			return -1;
		}
		
		// -- Axis GridLine Helpers -------------------------------------------
		
		/**
		 * Creates a new axis grid line.
		 * @param val the value to create the grid lines for
		 * @return an AxisGridLine
		 */	
		protected function createGridLine(val:Object) : AxisGridLine
		{
			var gline:AxisGridLine = new AxisGridLine();
			gline.alpha = 0;
			gline.value = val;
			var p:Point = positionGridLine(gline, _prevScale);
			gline.x1 = p.x;
			gline.y1 = p.y;
			gline.x2 = gline.x1 + _xgb;
			gline.y2 = gline.y1 + _ygb;	
			updateGridLine(gline);
			gridLines.addChild(gline);
			return gline;
		}
		
		/**
		 * Computes the position of an axis grid line.
		 * @param gline the axis grid line to layout
		 * @param scale the scale used to map values to the axis
		 * @return a Point with x,y coordinates for the axis grid line
		 */
		protected function positionGridLine(gline:AxisGridLine, scale:Scale) : Point
		{
			var f:Number = scale.interpolate(gline.value);
			_point.x = _xgo + _xa + f*(_xb - _xa);
			_point.y = _ygo + _ya + f*(_yb - _ya);
			return _point;
		}
		
		/**
		 * Updates an axis grid line's settings
		 * @param gline the grid line to update
		 */
		protected function updateGridLine(gline:AxisGridLine) : void
		{
			gline.lineColor = _lineColor;
			gline.lineWidth = _lineWidth;
		}
		
		/**
		 * Updates all grid lines.
		 */
		protected function updateGridLines() : void
		{
			var _glines:Sprite = this.gridLines;
			for (var i:uint = 0; i<_glines.numChildren; ++i) {
				updateGridLine(_glines.getChildAt(i) as AxisGridLine);
			}
		}
		
		/**
		 * Returns the index of a grid lines in the line's container sprite
		 * for a given data value.
		 * @param val the data value to find
		 * @param len the number of grid lines to check
		 * @return the index of a grid line with matching value, or -1 if no
		 *  grid line was found
		 */	
		protected function findGridLine(val:Object, len:uint) : int
		{
			var _glines:Sprite = this.gridLines;
			for (var i:uint = 0; i<len; ++i) {
				// TODO: make this robust to repeated values
				if (Stats.equal((_glines.getChildAt(i) as AxisGridLine).value, val)) {
					return i;
				}
			}
			return -1;
		}
		
	} // end of class Axis
}