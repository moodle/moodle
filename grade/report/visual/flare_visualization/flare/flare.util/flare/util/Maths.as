package flare.util
{
	/**
	 * Utility methods for mathematics not found in the <code>Math</code> class.
	 */
	public class Maths
	{
		/**
		 * Constructor, throws an error if called, as this is an abstract class.
		 */
		public function Maths() {
			throw new ArgumentError("This is an abstract class");
		}
		
		// -- Operators -------------------------------------------------------
		
		/**
		 * Returns the logarithm with a given base value.
		 * @param x the number for which to compute the logarithm
		 * @param base the base of the logarithm
		 * @return the logarithm value
		 */
		public static function log(x:Number, base:Number):Number
		{
			return Math.log(x) / Math.log(base);
		}
		
		/**
		 * Rounds an input value down according to its logarithm. The method
		 *  takes the floor of the logarithm of the value and then uses the
		 *  resulting value as an exponent for the base value. For example:
		 * <code>
		 * trace(Maths.logFloor(13,10)); // prints "10"
		 * trace(Maths.logFloor(113,10)); // prints "100"
		 * </code>
		 * @param x the number for which to compute the logarithm floor
		 * @param base the base of the logarithm
		 * @return the rounded-by-logarithm value
		 */
		public static function logFloor(x:Number, base:Number):Number {
			if (x > 0) {
				return Math.pow(base, Math.floor(Maths.log(x, base)));
			} else {
				return -Math.pow(base, -Math.floor(-Maths.log(-x, base)));
			}
		}
		
		/**
		 * Rounds an input value up according to its logarithm. The method
		 *  takes the ceiling of the logarithm of the value and then uses the
		 *  resulting value as an exponent for the base value. For example:
		 * <code>
		 * trace(Maths.logCeil(13,10)); // prints "100"
		 * trace(Maths.logCeil(113,10)); // prints "1000"
		 * </code>
		 * @param x the number for which to compute the logarithm ceiling
		 * @param base the base of the logarithm
		 * @return the rounded-by-logarithm value
		 */
		public static function logCeil(x:Number, base:Number):Number {
			if (x > 0) {
				return Math.pow(base, Math.ceil(Maths.log(x, base)));
			} else {
				return -Math.pow(base, -Math.ceil(-Maths.log(-x, base)));
			}
		}
		
		/**
		 * Computes a zero-symmetric logarithm. Computes the logarithm of the
		 * absolute value of the input, and determines the sign of the output
		 * according to the sign of the input value.
		 * @param x the number for which to compute the logarithm
		 * @param b the base of the logarithm
		 * @return the symmetric log value.
		 */
		public static function symLog(x:Number, b:Number) : Number
        {
            return x == 0 ? 0 : x > 0 ? log(x, b) : -log(-x, b);
        }
        
        /**
		 * Computes a zero-symmetric logarithm, with adjustment to values
		 * between zero and the logarithm base. This adjustment introduces
		 * distortion for values less than the base number, but enables
		 * simultaneous plotting of log-transformed data involving both
		 * positive and negative numbers.
		 * @param x the number for which to compute the logarithm
		 * @param b the base of the logarithm
		 * @return the adjusted, symmetric log value.
		 */
        public static function adjLog(x:Number, b:Number) : Number
        {
        	var neg:Boolean = (x < 0);
        	if (neg) x = -x;
        	if (x < b) x += (b-x)/b;
        	return neg ? -log(x,b) : log(x,b);
        }
        
        /**
         * Computes a zero-symmetric square root. Computes the square root of
         * the absolute value of the input, and determines the sign of the
         * output according to the sign of the input value.
		 * @param x the number for which to compute the square root
		 * @return the symmetric square root value.
		 */
		public static function symSqrt(x:Number) : Number
		{
			return (x > 0 ? Math.sqrt(x) : -Math.sqrt(-x));
		}
		
		/**
         * Computes a zero-symmetric Nth root. Computes the root of
         * the absolute value of the input, and determines the sign of the
         * output according to the sign of the input value.
		 * @param x the number for which to compute the square root
		 * @param p the root value (2 for square root, 3 for cubic root, etc)
		 * @return the symmetric Nth root value.
		 */
		public static function symRoot(x:Number, p:Number) : Number
		{
			return (x > 0 ? Math.pow(x, 1/p) : -Math.pow(-x, 1/p));
		}
		
		// -- Statistics ------------------------------------------------------
		
		/**
         * Computes the n-quantile boundaries for a set of values.
         * @param n the number of quantiles
         * @param values the values to break into quantiles
         * @param sort indicates if the values array needs to be sorted. If
         *  true, the array will be sorted prior to determining quantile
         *  boundaries. If false, the array must be in ascending sort order.
         * @return an n-length array of quantile boundaries
         */
        public static function quantile(n:uint, values:Array, sort:Boolean):Array
        {
        	var len:uint = values.length-1;
			var qtls:Array = new Array(n);
			
			if (sort) {
            	values = Arrays.copy(values);
            	values.sort(Array.NUMERIC);
   			}
   			
            for (var i:uint=1; i <= n; ++i)
            {
                qtls[i-1] = values[uint((len*i)/n)];
            }            
            return qtls;
        }
		
		
		// -- Forward Interpolation Routines ----------------------------------
		
		/**
		 * Computes a linear interpolation between two values.
		 * @param f the interpolation fraction (typically between 0 and 1)
		 * @param min the minimum value (corresponds to f==0)
		 * @param max the maximum value (corresponds to f==1)
		 * @return the interpolated value
		 */
		public static function linearInterp(f:Number, min:Number, max:Number):Number
		{
			return min + f * (max - min);
		}
		
		/**
		 * Computes a logarithmic interpolation between two values. Uses a
		 * zero-symmetric logarithm calculation (see <code>symLog</code>).
		 * @param f the interpolation fraction (typically between 0 and 1)
		 * @param min the minimum value (corresponds to f==0)
		 * @param max the maximum value (corresponds to f==1)
		 * @param b the base of the logarithm
		 * @return the interpolated value
		 */
		public static function logInterp(f:Number, min:Number, max:Number, b:Number):Number
		{
			min = symLog(min, b); max = symLog(max, b);
			f = min + f * (max - min);
			return f<0 ? -Math.pow(b, -f) : Math.pow(b, f);
		}
		
		/**
		 * Computes a logarithmic interpolation between two values. Uses an
		 * adjusted zero-symmetric logarithm calculation (see <code>adjLog</code>).
		 * @param f the interpolation fraction (typically between 0 and 1)
		 * @param min the minimum value (corresponds to f==0)
		 * @param max the maximum value (corresponds to f==1)
		 * @param b the base of the logarithm
		 * @return the interpolated value
		 */
		public static function adjLogInterp(f:Number, min:Number, max:Number, b:Number):Number
		{
			min = adjLog(min, b); max = adjLog(max, b);
			f = min + f * (max - min);
			var neg:Boolean = f < 0;
			f = neg ? Math.pow(b, -f) : Math.pow(b, f);
			f = b*(f-1) / (b-1);
			return neg ? -f : f;
		}
		
		/**
		 * Computes a square root interpolation between two values. Uses a
		 * zero-symmetric root calculation (see <code>symSqrt</code>).
		 * @param f the interpolation fraction (typically between 0 and 1)
		 * @param min the minimum value (corresponds to f==0)
		 * @param max the maximum value (corresponds to f==1)
		 * @return the interpolated value
		 */
		public static function sqrtInterp(f:Number, min:Number, max:Number):Number
		{
			min = symSqrt(min); max = symSqrt(max);
			f = min + f * (max - min);
			return f<0 ? -(f*f) : f*f;
		}
		
		/**
		 * Computes an Nth-root interpolation between two values. Uses a
		 * zero-symmetric root calculation (see <code>symRoot</code>).
		 * @param f the interpolation fraction (typically between 0 and 1)
		 * @param min the minimum value (corresponds to f==0)
		 * @param max the maximum value (corresponds to f==1)
		 * @param p the root value (2 for square root, 3 for cubic root, etc)
		 * @return the interpolated value
		 */
		public static function rootInterp(f:Number, min:Number, max:Number, p:Number):Number
		{
			min = symRoot(min, p); max = symRoot(max, p);
			f = min + f*(max - min);
			var neg:Boolean = f < 0;
			return neg ? -Math.pow(-f, p) : Math.pow(f, p);
		}
		
		/**
		 * Computes an interpolated value in a quantile scale.
		 * @param f the interpolation fraction (typically between 0 and 1)
		 * @param quantiles an array of quantile boundaries
		 * @return the interpolated value
		 */
		public static function quantileInterp(f:Number, quantiles:Array):Number
		{
			return quantiles[int(Math.round(f*(quantiles.length-1)))];
		}
		
		
		// -- Inverse Interpolation Routines ----------------------------------
		
		/**
		 * Computes an inverse linear interpolation, returning an interpolation
		 * fraction. Returns 0.5 if the min and max values are the same.
		 * @param x the interpolated value
		 * @param min the minimum value (corresponds to f==0)
		 * @param min the maximum value (corresponds to f==1)
		 * @return the inferred interpolation fraction
		 */
        public static function invLinearInterp(x:Number, min:Number, max:Number):Number
        {
            var denom:Number = (max - min);
            return (denom == 0 ? 0.5 : (x - min) / denom);
        }
        
        /**
		 * Computes an inverse logarithmic interpolation, returning an
		 * interpolation fraction. Uses a zero-symmetric logarithm.
		 * Returns 0.5 if the min and max values are the same.
		 * @param x the interpolated value
		 * @param min the minimum value (corresponds to f==0)
		 * @param min the maximum value (corresponds to f==1)
		 * @param b the base of the logarithm
		 * @return the inferred interpolation fraction
		 */
        public static function invLogInterp(x:Number, min:Number, max:Number, b:Number):Number
        {
            min = symLog(min, b);
            var denom:Number = symLog(max, b) - min;
            return (denom == 0 ? 0.5 : (symLog(x, b) - min) / denom);
        }
        
        /**
		 * Computes an inverse logarithmic interpolation, returning an
		 * interpolation fraction. Uses an adjusted zero-symmetric logarithm.
		 * Returns 0.5 if the min and max values are the same.
		 * @param x the interpolated value
		 * @param min the minimum value (corresponds to f==0)
		 * @param min the maximum value (corresponds to f==1)
		 * @param b the base of the logarithm
		 * @return the inferred interpolation fraction
		 */
        public static function invAdjLogInterp(x:Number, min:Number, max:Number, b:Number):Number
        {
            min = adjLog(min, b);
            var denom:Number = adjLog(max, b) - min;
            return (denom == 0 ? 0.5 : (adjLog(x, b) - min) / denom);
        }
        
        /**
		 * Computes an inverse square root interpolation, returning an
		 * interpolation fraction. Uses a zero-symmetric square root.
		 * Returns 0.5 if the min and max values are the same.
		 * @param x the interpolated value
		 * @param min the minimum value (corresponds to f==0)
		 * @param min the maximum value (corresponds to f==1)
		 * @return the inferred interpolation fraction
		 */
        public static function invSqrtInterp(x:Number, min:Number, max:Number):Number
        {
            min = symSqrt(min);
            var denom:Number = symSqrt(max) - min;
            return (denom == 0 ? 0.5 : (symSqrt(x) - min) / denom);
        }
        
        /**
		 * Computes an inverse Nth-root interpolation, returning an
		 * interpolation fraction. Uses a zero-symmetric root.
		 * Returns 0.5 if the min and max values are the same.
		 * @param x the interpolated value
		 * @param min the minimum value (corresponds to f==0)
		 * @param min the maximum value (corresponds to f==1)
		 * @param p the root value (2 for square root, 3 for cubic root, etc)
		 * @return the inferred interpolation fraction
		 */
        public static function invRootInterp(x:Number, min:Number, max:Number, p:Number):Number
        {
            min = symRoot(min,p);
            var denom:Number = symRoot(max,p) - min;
            return (denom == 0 ? 0.5 : (symRoot(x,p) - min) / denom);
        }

		/**
		 * Computes an inverse quantile scale interpolation, returning an
		 * interpolation fraction.
		 * @param x the interpolated value
		 * @param quantiles an array of quantile boundaries
		 * @return the inferred interpolation fraction
		 */
		public static function invQuantileInterp(x:Number, quantiles:/*Number*/Array):Number
        {
            var a:uint = 0, b:uint = quantiles.length;
            var i:uint = b / 2;
            while (a < b)
            {   // binary search over the boundaries
                if (quantiles[i] == x)
                    break;
                else if (quantiles[i] < x)
                    a = i+1;
                else
                    b = i;
                i = a + ((b - a) >> 1);
            }
            return Number(i) / (quantiles.length-1);
        }
        
        // -- Perlin Noise ----------------------------------------------------
        
        /**
         * Computes Perlin noise for a given (x, y, z) point.
         * @param x the x parameter
         * @param y the y parameter (default 0)
         * @param z the z parameter (default 0)
         * @return Perlin noise for the input co-ordinates
         */
        public static function noise(x:Number, y:Number=0, z:Number=0):Number
   		{
   			var X:int,Y:int,Z:int,A:int,AA:int,AB:int,B:int,BA:int,BB:int;
   			var u:Number,v:Number,w:Number,a:Number,b:Number,c:Number,d:Number;
   			var xx:Number, yy:Number, zz:Number;
   			
   			X = int(x); Y = int(y); Z = int(z);
   			x -= X;     y -= Y;     z -= Z;
   			X &= 255;   Y &= 255;   Z &= 255;
   			xx = x-1;   yy = y-1;   zz = z-1;
   			
   			u = x * x * x * (x * (x*6 - 15) + 10);
   			v = y * y * y * (y * (y*6 - 15) + 10);
   			w = z * z * z * (z * (z*6 - 15) + 10);
   			
   			A  = (_p[X  ]+Y);
   			AA = (_p[A  ]+Z);
   			AB = (_p[A+1]+Z);
   			B  = (_p[X+1]+Y);
   			BA = (_p[B  ]+Z);
   			BB = (_p[B+1]+Z);
   			
   			// interpolate
   			a =       grad(_p[AA  ], x , y , z );
   			b =       grad(_p[AB  ], x , yy, z );
   			c =       grad(_p[AA+1], x , y , zz);
   			d =       grad(_p[AB+1], x , yy, zz);
   			a += u * (grad(_p[BA  ], xx, y , z ) - a);
   			b += u * (grad(_p[BB  ], xx, yy, z ) - b);
   			c += u * (grad(_p[BA+1], xx, y , zz) - c);
   			d += u * (grad(_p[BB+1], xx, yy, zz) - d);
   			a += v * (b - a);
   			c += v * (d - c);
   			return a + w * (c - a);
   		}
   		
		private static function grad(h:int, x:Number, y:Number, z:Number):Number
		{
			h &= 15;
			var u:Number = h<8 ? x : y;
			var v:Number = h<4 ? y : h==2||h==14 ? x : z;
			return ((h&1) == 0 ? u : -u) + ((h&2) == 0 ? v : -v);
		}
		
		private static const _p:Array = [151,160,137,91,90,15,131,13,201,95,96,
		53,194,233,7,225,140,36,103,30,69,142,8,99,37,240,21, 10,23,190, 6,148,
		247,120,234,75, 0,26,197,62,94,252,219,203,117,35, 11, 32,57,177,33,88,
		237,149,56,87,174, 20,125,136,171,168, 68,175,74,165, 71,134,139,48,27,
		166,77,146,158,231,83,111,229,122, 60,211,133,230,220,105,92, 41,55,46,
		245,40,244,102,143,54, 65,25,63,161, 1,216,80,73,209,76,132,187,208,89,
		18,169,200,196,135,130,116,188,159,86,164,100,109,198,173,186, 3,64,52,
		217,226,250,124,123, 5,202,38,147,118,126,255,82,85,212,207,206,59,227,
		47,16,58,17,182,189,28,42,223,183,170,213,119,248,152, 2,44,154,163,70,
		221,153,101,155,167, 43,172, 9,129,22,39,253, 19,98,108,110,79,113,224,
		232,178,185, 112,104,218,246,97,228,251,34,242,193,238,210,144, 12,191,
		179,162,241,81,51,145,235,249,14,239,107,49,192,214,31,181,199,106,157,
		184,84,204,176,115,121,50,45,127, 4,150,254,138,236,205,93,222,114, 67,
		29,24,72,243,141,128,195,78,66,215,61,156,180,// now we repeat the list
		151,160,137,91,90,15,131, 13,201,95,96,53,194,233, 7,225,140,36,103,30,
		69,142,8,99,37,240,21, 10,23,190, 6,148,247,120,234,75, 0,26,197,62,94,
		252,219,203,117,35,11,32,57,177,33,88,237,149,56,87,174,20,125,136,171,
		168, 68,175,74,165,71,134,139,48, 27,166,77,146,158,231,83,111,229,122,
		60,211,133,230,220,105,92,41,55,46,245,40,244,102,143,54, 65,25,63,161,
		1,216, 80,73,209,76,132,187,208, 89,18,169,200,196,135,130,116,188,159,
		86,164,100,109,198,173,186, 3,64,52,217,226,250,124,123, 5,202, 38,147,
		118,126,255,82,85,212,207,206,59,227,47,16,58,17,182,189,28,42,223,183,
		170,213,119,248,152, 2,44,154,163, 70,221,153,101,155,167,43,172,9,129,
		22,39,253, 19,98,108,110,79,113,224,232,178,185,112,104,218,246,97,228,
	   	251,34,242,193,238,210,144,12,191,179,162,241,81,51,145,235,249,14,239,
	   	107,49,192,214, 31,181,199,106,157,184,84,204,176,115,121,50,45,127, 4,
	   	150,254,138,236,205,93,222,114,67,29, 24,72,243,141,128,195, 78,66,215,
	   	61,156,180];

	} // end of class Maths
}