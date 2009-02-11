package flare.vis.scale
{
	/**
	 * Constants defining known scale types, such as linear, log, and
	 * time scales.
	 */
	public class ScaleType
	{
		/** Constant indicating an unknown scale. */
		public static const UNKNOWN:String = "unknown";
		/** Constant indicating a categorical scale. */
		public static const CATEGORIES:String = "categories";
		/** Constant indicating an ordinal scale. */
		public static const ORDINAL:String = "ordinal";
		/** Constant indicating a linear numeric scale. */
		public static const LINEAR:String = "linear";
		/** Constant indicating a root-transformed numeric scale. */
		public static const ROOT:String = "root";
		/** Constant indicating a log-transformed numeric scale. */
		public static const LOG:String = "log";
		/** Constant indicating a quantile scale. */
		public static const QUANTILE:String = "quantile";
		/** Constant indicating a date/time scale. */
		public static const TIME:String = "time";
		
		/**
		 * Constructor, throws an error if called, as this is an abstract class.
		 */
		public function ScaleType() {
			throw new Error("This is an abstract class.");
		}
		
	} // end of class ScaleType
}