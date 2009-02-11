package flare.vis.scale
{
	import flare.util.Maths;
	import flare.util.Strings;
	
	/**
	 * Scale that performs a root transformation of the data. This could be a
	 * square root or any arbitrary power.
	 */
	public class RootScale extends QuantitativeScale
	{
		private var _pow:Number = 2;
		
		/** The power of the root transform. A value of 2 indicates a square
		 *  root, 3 a cubic root, etc. */
		public function get power():Number { return _pow; }
		public function set power(p:Number):void { _pow = p; }
		
		/**
		 * Creates a new RootScale.
		 * @param min the minimum data value
		 * @param max the maximum data value
		 * @param base the number base to use
		 * @param flush the flush flag for scale padding
		 * @param labelFormat the formatting pattern for value labels
		 */
		public function RootScale(min:Number=0, max:Number=0, base:Number=10,
								  flush:Boolean=false, pow:Number=2,
								  labelFormat:String=Strings.DEFAULT_NUMBER)
		{
			super(min, max, base, flush, labelFormat);
			_pow = pow;
		}
		
		/** @inheritDoc */
		public override function clone():Scale {
			return new RootScale(_dmin, _dmax, _base, _flush, _pow, _format);
		}
		
		/** @inheritDoc */
		protected override function interp(val:Number):Number {
			if (_pow==2) return Maths.invSqrtInterp(val, _smin, _smax);
			return Maths.invRootInterp(val, _smin, _smax, _pow);
		}
		
		/** @inheritDoc */
		public override function lookup(f:Number):Object {
			if (_pow==2) return Maths.sqrtInterp(f, _smin, _smax);
			return Maths.rootInterp(f, _smin, _smax, _pow);
		}
		
	} // end of class RootScale
}