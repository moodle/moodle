package flare.animate.interpolate
{
	import flash.geom.Point;
	
	/**
	 * Interpolator for <code>flash.geom.Point</code> values.
	 */
	public class PointInterpolator extends Interpolator
	{
		private var _startX:Number, _startY:Number;
		private var _rangeX:Number, _rangeY:Number;
		private var _cur:Point;
		
		/**
		 * Creates a new PointInterpolator.
		 * @param target the object whose property is being interpolated
		 * @param property the property to interpolate
		 * @param start the starting point value to interpolate from
		 * @param end the target point value to interpolate to
		 */
		public function PointInterpolator(target:Object, property:String,
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
			var e:Point = Point(end), s:Point = Point(start);
			if (_cur == null || _cur == s || _cur == e)
				_cur = e.clone();
			
			_startX = s.x;
			_startY = s.y;
			_rangeX = e.x - _startX;
			_rangeY = e.y - _startY;
		}
		
		/**
		 * Calculate and set an interpolated property value.
		 * @param f the interpolation fraction (typically between 0 and 1)
		 */
		public override function interpolate(f:Number) : void
		{
			_cur.x = _startX + f*_rangeX;
			_cur.y = _startY + f*_rangeY;
			_prop.setValue(_target, _cur);
		}
		
	} // end of class PointInterpolator
}