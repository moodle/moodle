package flare.vis.palette
{
	/**
	 * Palette for size values represeneted as scale factors. The SizePalette
	 * class distinguishes between 1D and 2D scale factors, with a square
	 * root being applied to 2D scale factors to ensure that area scales
	 * linearly with the size value.
	 */
	public class SizePalette extends Palette
	{
		private var _minSize:Number = 1;
		private var _range:Number = 6;
		private var _is2D:Boolean = true;
		
		/** The minimum scale factor in this size palette. */
		public function get minimumSize():Number { return _minSize; }
		public function set minimumSize(s:Number):void {
			_range += s - _minSize; _minSize = s;
		}
		
		/** the maximum scale factor in this size palette. */
		public function get maximumSize():Number { return _minSize + _range; }
		public function set maximumSize(s:Number):void { _range = s - _minSize; }
		
		/** Flag indicating if this size palette is for 2D shapes. */
		public function get is2D():Boolean { return _is2D; }
		public function set is2D(b:Boolean):void { _is2D = b; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new SizePalette.
		 * @param minSize the minimum scale factor in the palette
		 * @param maxSize the maximum scale factor in the palette
		 * @param is2D flag indicating if the size values are for a 2D shape,
		 *  true by default
		 */		
		public function SizePalette(minSize:Number=1, maxSize:Number=6, is2D:Boolean=true)
		{
			_minSize = minSize;
			_range = maxSize - minSize;
			_is2D = is2D;
		}
		
		/** @inheritDoc */
		public override function getValue(f:Number):Object
		{
			return getSize(f);
		}
		
		/**
		 * Retrieves the size value corresponding to the input interpolation
		 * fraction. If the <code>is2D</code> flag is true, the square root
		 * of the size value is returned.
		 * @param f an interpolation fraction
		 * @return the size value corresponding to the input fraction
		 */
		public function getSize(v:Number):Number
		{
			var s:Number;
			if (_values == null) {
				s = _minSize + v * _range;
			} else {
				s = _values[uint(Math.round(v*(_values.length-1)))];
			}
			return _is2D ? Math.sqrt(s) : s;
		}
		
	} // end of class SizePalette
}