package flare.animate.interpolate
{
	/**
	 * Interpolator for <code>Date</code> values.
	 */
	public class DateInterpolator extends Interpolator
	{
		private var _start:Number;
		private var _end:Number;
		private var _d:Date;
		
		/**
		 * Creates a new DateInterpolator.
		 * @param target the object whose property is being interpolated
		 * @param property the property to interpolate
		 * @param start the starting date value to interpolate from
		 * @param end the target date value to interpolate to
		 */
		public function DateInterpolator(target:Object, property:String,
		                                 start:Object, end:Object)
		{
			super(target, property, start, end);
		}
		
		/**
		 * Initializes this interpolator.
		 * @param start the starting value of the interpolation
		 * @param end the target value of the interpolation
		 */
		protected override function init(start:Object, end:Object) : void
		{
			_d = new Date();
			_start = (start as Date).time;
			_end = (end as Date).time - _start;
		}
		
		/**
		 * Calculate and set an interpolated property value.
		 * @param f the interpolation fraction (typically between 0 and 1)
		 */
		public override function interpolate(f:Number) : void
		{
			_d.time = _start + f * _end;
			_prop.setValue(_target, _d);
		}
		
	} // end of class DateInterpolator
}