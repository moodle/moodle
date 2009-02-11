package flare.vis.scale
{
	import flare.util.Maths;
	import flare.util.Strings;
	
	/**
	 * Scale that statistically organizes data by quantiles into discrete bins.
	 * For example, the quantile scale can be used to create a discrete size
	 * encoding by statistically dividing the data into bins. Quantiles are
	 * computed using the <code>flare.util.Maths.quantile</code> method.
	 * 
	 * @see flare.util.Maths#quantile
	 */
	public class QuantileScale extends Scale
	{
		private var _quantiles:Array;
		
		/** @inheritDoc */
		public override function get flush():Boolean { return true; }
		public override function set flush(val:Boolean):void { /* nothing */ }
		
		/** @inheritDoc */
		public override function get min():Object { return _quantiles[0]; }
		
		/** @inheritDoc */
		public override function get max():Object { return _quantiles[_quantiles.length-1]; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new QuantileScale.
		 * @param n the number of quantiles desired
		 * @param values the data values to organized into quantiles
		 * @param sorted flag indicating if the input values array is
		 *  already pre-sorted
		 * @param labelFormat the formatting pattern for value labels
		 */
		public function QuantileScale(n:int, values:Array,
			sorted:Boolean=false, labelFormat:String=Strings.DEFAULT_NUMBER)
		{
			_quantiles = (n<0 ? values : Maths.quantile(n, values, !sorted));
			this.labelFormat = labelFormat;
		}
		
		/** @inheritDoc */
		public override function clone():Scale
		{
			return new QuantileScale(-1, _quantiles, false, _format);
		}
		
		/** @inheritDoc */
		public override function interpolate(value:Object):Number
		{
			return Maths.invQuantileInterp(Number(value), _quantiles);
		}
		
		/** @inheritDoc */
		public override function lookup(f:Number):Object
		{
			return Maths.quantileInterp(f, _quantiles);
		}
		
		/** @inheritDoc */
		public override function values(num:int=-1):/*Number*/Array
		{
			var a:Array = new Array();
			var stride:int = num<0 ? 1 : 
				int(Math.max(1, Math.floor(_quantiles.length/num)));
			for (var i:uint=0; i<_quantiles.length; i += stride) {
				a.push(_quantiles[i]);
			}
			return a;
		}
		
	} // end of class QuantileScale
}