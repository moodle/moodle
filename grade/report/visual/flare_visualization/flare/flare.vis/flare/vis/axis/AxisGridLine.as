package flare.vis.axis
{
	import flare.display.LineSprite;

	/**
	 * Axis grid line in an axis display.
	 */
	public class AxisGridLine extends LineSprite
	{
		private var _ordinal:int;
		private var _value:Object;

		/** The ordinal index of this grid line in the list of grid lines. */
		public function get ordinal():int { return _ordinal; }
		public function set ordinal(ord:int):void { _ordinal = ord; }
		
		/** The data value represented by this axis grid line. */
		public function get value():Object { return _value; }
		public function set value(value:Object):void { _value = value; }
		
	} // end of class AxisGridLine
}