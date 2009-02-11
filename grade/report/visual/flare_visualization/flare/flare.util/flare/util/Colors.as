package flare.util
{	
	/**
	 * Utility methods for working with colors.
	 */
	public class Colors
	{
		/**
		 * Constructor, throws an error if called, as this is an abstract class.
		 */
		public function Colors() {
			throw new Error("This is an abstract class.");
		}
				
		/**
		 * Returns the alpha component of a color value
		 * @param c the color value
		 * @return the alpha component
		 */
		public static function a(c:uint):uint
		{
			return (c >> 24) & 0xFF;
		}
		
		/**
		 * Returns the red component of a color value
		 * @param c the color value
		 * @return the red component
		 */
		public static function r(c:uint):uint
		{
			return (c >> 16) & 0xFF;
		}
		
		/**
		 * Returns the green component of a color value
		 * @param c the color value
		 * @return the green component
		 */
		public static function g(c:uint):uint
		{
			return (c >> 8) & 0xFF;
		}
		
		/**
		 * Returns the blue component of a color value
		 * @param c the color value
		 * @return the blue component
		 */
		public static function b(c:uint):uint
		{
			return (c & 0xFF);
		}
		
		/**
		 * Returns a color value with the given red, green, blue, and alpha
		 * components
		 * @param r the red component (0-255)
		 * @param g the green component (0-255)
		 * @param b the blue component (0-255)
		 * @param a the alpha component (0-255, 255 by default)
		 * @return the color value
		 * 
		 */
		public static function rgba(r:uint, g:uint, b:uint, a:uint=255):uint
		{
			return ((a & 0xFF) << 24) | ((r & 0xFF) << 16) |
				   ((g & 0xFF) <<  8) |  (b & 0xFF);
		}
		
		/**
		 * Returns a color value by updating the alpha component of another
		 * color value.
		 * @param c a color value
		 * @param a the desired alpha component (0-255)
		 * @return a color value with adjusted alpha component
		 */
		public static function setAlpha(c:uint, a:uint):uint
		{
			return ((a & 0xFF) << 24) | (c & 0x00FFFFFF);
		}
		
		/**
		 * Returns the RGB color value for a color specified in HSV (hue,
		 * saturation, value) color space.
		 * @param h the hue, a value between 0 and 1
		 * @param s the saturation, a value between 0 and 1
		 * @param v the value (brighntess), a value between 0 and 1
		 * @param a the (optional) alpha value, an integer between 0 and 255
		 *  (255 is the default)
		 * @return the corresponding RGB color value
		 */
		public static function hsv(h:Number, s:Number, v:Number, a:uint=255):uint
		{
			var r:uint=0, g:uint=0, b:uint=0;
            if (s == 0) {
                r = g = b = uint(v * 255 + .5);
            } else {
            	var i:Number = (h - Math.floor(h)) * 6.0;
                var f:Number = i - Math.floor(i);
                var p:Number = v * (1 - s);
                var q:Number = v * (1 - s * f);
                var t:Number = v * (1 - (s * (1 - f)));
                switch (int(i))
                {
                    case 0:
                        r = uint(v * 255 + .5);
                        g = uint(t * 255 + .5);
                        b = uint(p * 255 + .5);
                        break;
                    case 1:
                        r = uint(q * 255 + .5);
                        g = uint(v * 255 + .5);
                        b = uint(p * 255 + .5);
                        break;
                    case 2:
                        r = uint(p * 255 + .5);
                        g = uint(v * 255 + .5);
                        b = uint(t * 255 + .5);
                        break;
                    case 3:
                        r = uint(p * 255 + .5);
                        g = uint(q * 255 + .5);
                        b = uint(v * 255 + .5);
                        break;
                    case 4:
                        r = uint(t * 255 + .5);
                        g = uint(p * 255 + .5);
                        b = uint(v * 255 + .5);
                        break;
                    case 5:
                        r = uint(v * 255 + .5);
                        g = uint(p * 255 + .5);
                        b = uint(q * 255 + .5);
                        break;
                }
            }
            return rgba(r, g, b, a);
		}
		
		 /**
	     * Interpolate between two color values by the given mixing proportion.
	     * A mixing fraction of 0 will result in c1, a value of 1.0 will result
	     * in c2, and value of 0.5 will result in the color mid-way between the
	     * two in RGB color space.
	     * @param c1 the starting color
	     * @param c2 the target color
	     * @param f a fraction between 0 and 1 controlling the interpolation
	     * @return the interpolated color
	     */
		public static function interpolate(c1:uint, c2:uint, f:Number):uint
		{
			var t:uint;
			return rgba(
				(t=r(c1)) + f*(r(c2)-t),
				(t=g(c1)) + f*(g(c2)-t),
				(t=b(c1)) + f*(b(c2)-t),
				(t=a(c1)) + f*(a(c2)-t)
			);
		}
    
	    /**
	     * Get a darker shade of an input color.
	     * @param c a color value
	     * @return a darkened color value
	     */
	    public static function darker(c:uint, s:Number=1):uint
	    {
	    	s = Math.pow(0.7, s);
	        return rgba(Math.max(0, int(s*r(c))),
	                    Math.max(0, int(s*g(c))),
	                    Math.max(0, int(s*b(c))),
	                    a(c));
	    }
	
	    /**
	     * Get a brighter shade of an input color.
	     * @param c a color value
	     * @return a brighter color value
	     */
	    public static function brighter(c:uint, s:Number=1):uint
	    {
	    	var cr:uint, cg:uint, cb:uint, i:uint;
	    	s = Math.pow(0.7, s);
	    	
	        cr = r(c), cg = g(c), cb = b(c);
	        i = 30;
	        if (cr == 0 && cg == 0 && cb == 0) {
	           return rgba(i, i, i, a(c));
	        }
	        if ( cr > 0 && cr < i ) cr = i;
	        if ( cg > 0 && cg < i ) cg = i;
	        if ( cb > 0 && cb < i ) cb = i;
	
	        return rgba(Math.min(255, (int)(cr/s)),
	                    Math.min(255, (int)(cg/s)),
	                    Math.min(255, (int)(cb/s)),
	                    a(c));
	    }
	    
	    /**
	     * Get a desaturated shade of an input color.
	     * @param c a color value
	     * @return a desaturated color value
	     */
	    public static function desaturate(c:uint):uint
	    {
	        var a:uint = c & 0xff000000;
	        var cr:Number = Number(r(c));
	        var cg:Number = Number(g(c));
	        var cb:Number = Number(b(c));
	
	        cr *= 0.2125; // red band weight
	        cg *= 0.7154; // green band weight
	        cb *= 0.0721; // blue band weight
	
	        var gray:uint = uint(Math.min(int(cr+cg+cb),0xff)) & 0xff;
	        return a | (gray << 16) | (gray << 8) | gray;
	    }
	    
	    /**
	     * A color transform matrix that desaturates colors to corresponding
	     * grayscale values. Can be used with the
	     * <code>flash.filters.ColorMatrixFilter</code> class.
	     */
	    public static function get desaturationMatrix():Array {
	    	return [0.2125, 0.7154, 0.0721, 0, 0,
	    			0.2125, 0.7154, 0.0721, 0, 0,
	    			0.2125, 0.7154, 0.0721, 0, 0,
	    			     0,      0,      0, 1, 0];
	    }
	    
	} // end of class Colors
}