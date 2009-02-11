package flare.vis.axis
{
	import flare.display.TextSprite;
	
	/**
	 * Axis label in an axis display.
	 */
	public class AxisLabel extends TextSprite
	{
		private var _ordinal:int;
		private var _value:Object;

		/** The ordinal index of this axis label in the list of labels. */
		public function get ordinal():int { return _ordinal; }
		public function set ordinal(ord:int):void { _ordinal = ord; }
		
		/** The data value represented by this axis label. */
		public function get value():Object { return _value; }
		public function set value(value:Object):void { _value = value; }
		
	} // end of class AxisLabel
}