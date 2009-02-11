package flare.vis.palette
{
	import flare.util.Colors;
	import flare.vis.scale.OrdinalScale;
	import flare.vis.scale.QuantitativeScale;
	import flare.vis.scale.Scale;
	
	/**
	 * Palette for color values, including utility methods for generating
	 * both categorical and ordinal color palettes.
	 */
	public class ColorPalette extends Palette
	{
		private var _keyframes:Array;
		
		/** Keyframes at which color values change in the palette. Useful
		 *  for configuring gradient paint fills. */
		public function get keyframes():Array { return _keyframes; }
		
		/**
		 * Creates a new ColorPalette.
		 * @param colors an array of colors defining the palette
		 * @param keyframes array of keyframes of color interpolations
		 */
		public function ColorPalette(colors:Array=null, keyframes:Array=null) {
			_values = colors;
			_keyframes = keyframes;
		}
		
		/**
		 * Retrieves the color corresponding to input interpolation fraction.
		 * @param f an interpolation fraction
		 * @return the color corresponding to the input fraction
		 */
		public function getColor(v:Number):uint
		{
			if (_values==null || _values.length==0)
				return 0;
			return _values[uint(Math.round(v*(_values.length-1)))];
		}
		
		/**
		 * Retrieves the color corresponding to the input array index.
		 * @param idx an integer index. The actual index value used is
		 *  the modulo of the input index by the length of the palette.
		 * @return the color in the palette at the given index
		 */
		public function getColorByIndex(idx:int):uint
		{
			if (_values == null || _values.length == 0 || idx < 0)
				return 0;
			else
				return _values[idx % _values.length];
		}
		
		// --------------------------------------------------------------------
		
		/**
		 * Returns a default color palette based on the input scale.
		 * @param scale the scale of values to map to colors
		 * @return a default color palette for the input scale
		 */
		public static function getDefaultPalette(scale:Scale):ColorPalette
		{
			/// TODO: more intelligent color palette selection?
			
			if (scale is OrdinalScale)
			{
				return category(OrdinalScale(scale).length);
			}
			else if (scale is QuantitativeScale)
			{
				var qs:QuantitativeScale = QuantitativeScale(scale);				
				if (qs.dataMin < 0 && qs.dataMax > 0)
					return diverging();
			}
			return ramp();
		}
		
		/** Default size of generated color palettes. */
		public static const DEFAULT_SIZE:int = 64;
		/** A set of 10 colors for encoding category values. */
		public static const CATEGORY_COLORS_10:/*uint*/Array = [
			0xFF1F77B4, 0xFFFF7F0E, 0xFF2CA02C, 0xFFD62728, 0xFF9467BD,
			0xFF8C564B, 0xFFE377C2, 0xFF7F7F7F, 0xFFBCBD22, 0xFF17BECF
		];
		/** A set of 20 colors for encoding category values. Includes
		 *  the colors of <code>CATEGORY_COLORS_10</code> plus lighter
		 *  shades of each. */
		public static const CATEGORY_COLORS_20:/*uint*/Array = [
			0xFF1F77B4, 0xFFAEC7E8, 0xFFFF7F0E, 0xFFFFBB78, 0xFF2CA02C,
			0xFF98DF8A, 0xFFD62728, 0xFFFF9896, 0xFF9467BD, 0xFFC5B0D5,
			0xFF8C564B, 0xFFC49C94, 0xFFE377C2, 0xFFF7B6D2, 0xFF7F7F7F,
			0xFFC7C7C7, 0xFFBCBD22, 0xFFDBDB8D, 0xFF17BECF, 0xFF9EDAE5
		];

		
		/**
		 * Generates a categorical color palette
		 * @param size the number of colors to include
		 * @param colors an array of category colors to use. If null, a
		 *  default category color palette will be used.
		 * @param alpha the alpha value for this palette's colors
		 * @return the categorical color palette
		 */
		public static function category(size:int=20, colors:Array=null,
										alpha:Number=1.0):ColorPalette
		{
			if (colors==null)
				colors = size<=10 ? CATEGORY_COLORS_10 : CATEGORY_COLORS_20;
			var a:uint = uint(255 * alpha) % 256;
			var cm:Array = new Array(size);
			for (var i:uint=0; i<size; ++i) {
				cm[i] = Colors.setAlpha(colors[i % colors.length], a);
			}
			return new ColorPalette(cm);
		}
		
		/**
	     * Generates a color palette that uses a "cool", blue-heavy color scheme.
	     * @param size the size of the color palette
	     * @return the color palette
	     */
	    public static function cool(size:int=DEFAULT_SIZE):ColorPalette
	    {
	    	return ramp(0xff00ffff, 0xffff00ff, size);
	    }
	
	    /**
	     * Generates a color palette that moves from black to red to yellow
	     * to white.
	     * @param size the size of the color palette
	     * @return the color palette
	     */
	    public static function hot(size:int=DEFAULT_SIZE):ColorPalette
	    {
	        var cm:Array = new Array(size), r:Number, g:Number, b:Number;
	        var n:int = int(2*size/8);
	        
	        for (var i:uint=0; i<size; i++) {
	            r = i<n ? (i+1)/n : 1;
	            g = i<n ? 0 : (i<2*n ? (i-n)/n : 1);
	            b = i<2*n ? 0 : (i-2*n)/(size-2*n);
	            cm[i] = Colors.rgba(255*r, 255*g, 255*b);
	        }
			var f:Number = 1/4;
	        return new ColorPalette(cm, [0, f, 2*f, 1]);
	    }
		
		/**
	     * Generates a color palette that "ramps" from one color to another.
	     * @param min the color corresponding to the minimum scale value
	     * @param max the color corresponding to the maximum scale value
	     * @param size the size of the color palette
	     * @return the color palette
	     */
		public static function ramp(min:uint=0xfff1eef6, max:uint=0xff045a8d,
			size:int=DEFAULT_SIZE):ColorPalette
		{
			var cm:Array = new Array(size);
			for (var i:uint=0; i<size; ++i) {
				cm[i] = Colors.interpolate(min, max, i/(size-1));
			}
			return new ColorPalette(cm, [0,1]);
		}
		
		/**
	     * Generates a color palette of color ramps diverging from a central
	     * value.
	     * @param min the color corresponding to the minimum scale value
	     * @param mid the color corresponding to the central scale value
	     * @param max the color corresponding to the maximum scale value
	     * @param f an interpolation fraction specifying the position of the
	     *  central value
	     * @param size the size of the color palette
	     * @return the color palette
	     */
		public static function diverging(min:uint=0xffd73027,
			mid:uint=0xffffffbf, max:uint=0xff1a9850,
			f:Number=0.5, size:int=DEFAULT_SIZE):ColorPalette
		{
			var cm:Array = new Array(size);
			var mp:int = int(f*size), i:uint, j:uint;
			for (i=0; i<mp; ++i) {
				cm[i] = Colors.interpolate(min, mid, i/mp);
			}
			mp = size - mp - 1;
			for (j=0; i<size; ++i, ++j) {
				cm[i] = Colors.interpolate(mid, max, j/mp);
			}
			return new ColorPalette(cm, [0,f,1]);
		}
		
	} // end of class ColorPalette
}