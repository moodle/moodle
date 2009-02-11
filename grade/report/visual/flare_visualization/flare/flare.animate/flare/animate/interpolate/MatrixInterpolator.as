package flare.animate.interpolate
{
	import flash.geom.Matrix;
	
	/**
	 * Interpolator for <code>flash.geom.Matrix</code> values.
	 */
	public class MatrixInterpolator extends Interpolator
	{
		private var _startA:Number, _startB:Number, _startC:Number;
		private var _startD:Number, _startX:Number, _startY:Number;
		private var _rangeA:Number, _rangeB:Number, _rangeC:Number;
		private var _rangeD:Number, _rangeX:Number, _rangeY:Number;
		private var _cur:Matrix;
		
		/**
		 * Creates a new MatrixInterpolator.
		 * @param target the object whose property is being interpolated
		 * @param property the property to interpolate
		 * @param start the starting matrix value to interpolate from
		 * @param end the target matrix value to interpolate to
		 */
		public function MatrixInterpolator(target:Object, property:String,
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
			var e:Matrix = Matrix(end), s:Matrix = Matrix(start);
			if (_cur == null || _cur == s || _cur == e)
				_cur = e.clone();
			
			_startA = s.a;
			_startB = s.b;
			_startC = s.c;
			_startD = s.d;
			_startX = s.tx;
			_startY = s.ty;
			_rangeA = e.a  - _startA;
			_rangeB = e.b  - _startB;
			_rangeC = e.c  - _startC;
			_rangeD = e.d  - _startD;
			_rangeX = e.tx - _startX;
			_rangeY = e.ty - _startY;
		}
		
		/**
		 * Calculate and set an interpolated property value.
		 * @param f the interpolation fraction (typically between 0 and 1)
		 */
		public override function interpolate(f:Number) : void
		{
			_cur.a  = _startA + f * _rangeA;
			_cur.b  = _startB + f * _rangeB;
			_cur.c  = _startC + f * _rangeC;
			_cur.d  = _startD + f * _rangeD;
			_cur.tx = _startX + f * _rangeX;
			_cur.ty = _startY + f * _rangeY;
			_prop.setValue(_target, _cur);
		}
		
	} // end of class MatrixInterpolator
}