package flare.vis.util
{
	import flare.vis.axis.AxisGridLine;
	import flare.vis.axis.AxisLabel;
	import flare.vis.data.DataSprite;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.NodeSprite;
	
	/**
	 * Utility class providing a default set of filtering functions. Each
	 * filtering function returns a boolean value indicating if the input
	 * argument meets the filtering criterion.
	 */
	public class Filters
	{
		/**
		 * Constructor, throws an error if called, as this is an abstract class.
		 */
		public function Filters()
		{
			throw new Error("This is an abstract class.");
		}
		
		// -- Static Filters --------------------------------------------------
		
		/**
		 * Returns true if the input argument is a <code>DataSprite</code>,
		 * false otherwise.
		 * @param x the input value
		 * @return true if the input is a <code>DataSprite</code>
		 */
		public static function isDataSprite(x:Object):Boolean
		{
			return x is DataSprite;
		}
		
		/**
		 * Returns true if the input argument is a <code>NodeSprite</code>,
		 * false otherwise.
		 * @param x the input value
		 * @return true if the input is a <code>NodeSprite</code>
		 */
		public static function isNodeSprite(x:Object):Boolean
		{
			return x is NodeSprite;
		}
		
		/**
		 * Returns true if the input argument is an <code>EdgeSprite</code>,
		 * false otherwise.
		 * @param x the input value
		 * @return true if the input is an <code>EdgeSprite</code>
		 */
		public static function isEdgeSprite(x:Object):Boolean
		{
			return x is EdgeSprite;
		}
		
		/**
		 * Returns true if the input argument is an <code>AxisLabel</code>,
		 * false otherwise.
		 * @param x the input value
		 * @return true if the input is a <code>AxisLabel</code>
		 */
		public static function isAxisLabel(x:Object):Boolean
		{
			return x is AxisLabel;
		}
		
		/**
		 * Returns true if the input argument is an <code>AxisGridLine</code>,
		 * false otherwise.
		 * @param x the input value
		 * @return true if the input is an <code>AxisGridLine</code>
		 */
		public static function isAxisGridLine(x:Object):Boolean
		{
			return x is AxisGridLine;
		}
		
	} // end of class Filters
}