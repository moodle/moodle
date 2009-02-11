package flare.vis.scale
{
	import flare.util.Maths;
	import flare.util.Strings;
	
	/**
	 * Scale that performs a log transformation of the data. The base of the
	 * logarithm is determined by the <code>base</code> property.
	 */
	public class LogScale extends QuantitativeScale
	{
		private var _zero:Boolean = false;
		
		/**
		 * Creates a new LogScale.
		 * @param min the minimum data value
		 * @param max the maximum data value
		 * @param base the number base to use
		 * @param flush the flush flag for scale padding
		 * @param labelFormat the formatting pattern for value labels
		 */
		public function LogScale(min:Number=0, max:Number=0, base:Number=10,
			flush:Boolean=false, labelFormat:String=Strings.DEFAULT_NUMBER)
		{
			super(min, max, base, flush, labelFormat);
		}
		
		/** @inheritDoc */
		public override function clone():Scale {
			return new LogScale(_dmin, _dmax, _base, _flush, _format);
		}
		
		/** @inheritDoc */
		protected override function interp(val:Number):Number {
			if (_zero) {
				return Maths.invAdjLogInterp(val, _smin, _smax, _base);
			} else {
				return Maths.invLogInterp(val, _smin, _smax, _base);
			}
		}
		
		/** @inheritDoc */
		public override function lookup(f:Number):Object
		{
			if (_zero) {
				return Maths.adjLogInterp(f, _smin, _smax, _base);
			} else {
				return Maths.logInterp(f, _smin, _smax, _base);
			}
		}
		
		/** @inheritDoc */
		protected override function updateScale():void
		{
			_zero = (_dmin < 0 && _dmax > 0);
			if (!_flush) {
				_smin = Maths.logFloor(_dmin, _base);
				_smax = Maths.logCeil(_dmax, _base);
				
				if (_zero) {
					if (Math.abs(_dmin) < _base) _smin = Math.floor(_dmin);
					if (Math.abs(_dmax) < _base) _smax = Math.ceil(_dmax);	
				}
			} else {
				_smin = _dmin;
				_smax = _dmax;
			}	
		}
		
		private function log(x:Number):Number {
			if (_zero) {
				// distorts the scale to accomodate zero
				return Maths.adjLog(x, _base);
			} else {
				// uses a zero-symmetric logarithmic scale
				return Maths.symLog(x, _base);
			}
		}
		
		/** @inheritDoc */
		public override function values(num:int=-1):Array
		{
			var vals:Array = new Array();
			
			var beg:int = int(Math.round(log(_smin)));
			var end:int = int(Math.round(log(_smax)));
			
			if (beg == end && beg > 0 && Math.pow(10, beg) > _smin) {
            	--beg; // decrement to generate more values
   			}
   			
            var i:int, j:int, b:Number, v:Number = _zero?-1:1;
            for (i = beg; i <= end; ++i)
            {
	           	if (i==0 && v<=0) { vals.push(v); vals.push(0); }
	           	v = _zero && i<0 ? -Math.pow(_base,-i) : Math.pow(_base,i);
	           	b = _zero && i<0 ? Math.pow(_base,-i-1) : v;
	            	
	           	for (j = 1; j < _base; ++j, v += b) {
	           		if (v > _smax) return vals;
	           		vals.push(v);
	           	}
            }
            return vals;
        }
		
	} // end of class LogScale
}