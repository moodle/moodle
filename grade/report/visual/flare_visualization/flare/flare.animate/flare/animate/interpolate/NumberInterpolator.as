package flare.animate.interpolate
{
	/**
	 * Interpolator for <code>Number</code> and <code>int</code> values.
	 */
	public class NumberInterpolator extends Interpolator
	{
		private var _start:Number;
		private var _end:Number;
		
		/**
		 * Creates a new NumberInterpolator.
		 * @param target the object whose property is being interpolated
		 * @param property the property to interpolate
		 * @param start the starting number to interpolate from
		 * @param end the target number to interpolate to
		 */
		public function NumberInterpolator(target:Object, property:String,
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
			_start = Number(start);
			_end = Number(end);
			if (isNaN(_start)) _start = _end;
			_end = _end - _start;
		}
		
		/**
		 * Calculate and set an interpolated property value.
		 * @param f the interpolation fraction (typically between 0 and 1)
		 */
		public override function interpolate(f:Number) : void
		{
			_prop.setValue(_target, _start + f*_end);
		}
		
	} // end of class NumberInterpolator
}