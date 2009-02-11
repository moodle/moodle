package flare.vis.scale
{
	import flare.util.Stats;
	
	/**
	 * Utility class for generating Scale instances.
	 */
	public class Scales
	{
		/**
		 * Constructor, throws an error if called, as this is an abstract class.
		 */
		public function Scales() {
			throw new Error("This is an abstract class.");
		}
		
		/**
		 * Creates a new scale instance based on the input parameters.
		 * @param stats a <code>Stats</code> object describing a data variable
		 * @param scaleType the type of scale to create (LINEAR by default).
		 *  Should be one of ORDINAL, LINEAR, ROOT, LOG, QUANTILE, or TIME.
		 * @param rest additional arguments dependent on the type of scale. For
		 *  example, this might be the number of quantiles for a quantile scale
		 *  or the number base for a logarithmic scale.
		 */
		public static function scale(stats:Stats,
			scaleType:String=null, ...rest):Scale
		{
			var arg1:Number, arg2:Number;
			if (!scaleType) scaleType = ScaleType.LINEAR;
			
			switch (stats.dataType) {
				case Stats.NUMBER:
					switch (scaleType) {
						case ScaleType.LINEAR:
						case ScaleType.UNKNOWN:
							arg1 = rest.length > 0 ? rest[0] : 10;
							return linear(stats, arg1);
						case ScaleType.ROOT:
							arg1 = rest.length > 0 ? rest[0] : 2;
							arg2 = rest.length > 1 ? rest[1] : 10;
							return root(stats, arg1, arg2);
						case ScaleType.LOG:
							arg1 = rest.length > 0 ? rest[0] : 10;
							return log(stats, arg1);
						case ScaleType.QUANTILE:
							arg1 = rest.length > 0 ? rest[0] : 5;
							return quantile(stats, int(arg1));
						default: return ordinal(stats);
					}
				case Stats.DATE:
					switch (scaleType) {
						case ScaleType.UNKNOWN:
						case ScaleType.LINEAR:
						case ScaleType.TIME:
							return time(stats);
						default: return ordinal(stats);
					}
				default:
					return ordinal(stats);
			}
		}
		
		/**
		 * Creates a new linear scale according to the input statistics.
		 * @param stats a <code>Stats</code> object describing a data variable
		 * @param base the number base to use
		 * @return a new linear scale
		 */
		public static function linear(stats:Stats, base:Number=10):LinearScale
		{
			if (stats.dataType != Stats.NUMBER)
				throw new Error("The data are not numeric!");
			return new LinearScale(stats.minimum, stats.maximum, base);
		}
		
		/**
		 * Creates a new root-transformed scale according to the input
		 * statistics.
		 * @param stats a <code>Stats</code> object describing a data variable
		 * @param the exponent of the root transform (2 for square root, 3 for
		 *  cubic root, etc)
		 * @param base the number base to use
		 * @return a new root-transformed scale
		 */
		public static function root(stats:Stats, pow:Number=2, base:Number=10):RootScale
		{
			if (stats.dataType != Stats.NUMBER)
				throw new Error("The data are not numeric!");
			return new RootScale(stats.minimum, stats.maximum, base, false, pow);
		}
		
		/**
		 * Creates a new loagrithmic scale according to the input statistics.
		 * @param stats a <code>Stats</code> object describing a data variable
		 * @param base the logarithm base to use
		 * @return a new logarithmic scale
		 */
		public static function log(stats:Stats, base:Number=10):LogScale
		{
			if (stats.dataType != Stats.NUMBER)
				throw new Error("The data are not numeric!");
			return new LogScale(stats.minimum, stats.maximum, base);
		}
		
		/**
		 * Creates a new quantile scale according to the input statistics.
		 * @param stats a <code>Stats</code> object describing a data variable
		 * @param n the number of desired quantiles (5 by default)
		 * @return a new quantile scale
		 */
		public static function quantile(stats:Stats, quantiles:int=5):QuantileScale
		{
			if (stats.dataType != Stats.NUMBER)
				throw new Error("The data are not numeric!");
			return new QuantileScale(quantiles, stats.values, true);
		}
		
		/**
		 * Creates a new date/time scale according to the input statistics.
		 * @param stats a <code>Stats</code> object describing a data variable
		 * @return a new date/time scale
		 */
		public static function time(stats:Stats):TimeScale
		{
			if (stats.dataType != Stats.DATE)
				throw new Error("The data are not date-times!");
			return new TimeScale(stats.minDate, stats.maxDate);
		}
		
		/**
		 * Creates a new ordinal scale according to the input statistics.
		 * @param stats a <code>Stats</code> object describing a data variable
		 * @return a new ordinal scale
		 */
		public static function ordinal(stats:Stats):OrdinalScale
		{
			return new OrdinalScale(stats.distinctValues, false, false);
		}
		
	} // end of class Scales
}