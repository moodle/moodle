package flare.query
{
	/**
	 * Aggregate (group-by) operator for computing the variance or
	 * standard deviation of a set of values.
	 */
	public class Variance extends AggregateExpression
	{
		/** Flag indicating the population variance or deviation. */
		public static const POPULATION:int = 0;
		/** Flag indicating the sample variance or deviation. */
		public static const SAMPLE:int     = 2;
		/** Flag indicating the variance should be computed. */
		public static const VARIANCE:int   = 0;
		/** Flag indicating the standard deviation should be computed. */
		public static const DEVIATION:int  = 1;
		
		private var _type:int;
		private var _sum:Number;
		private var _accum:Number;
		private var _count:Number;
		
		/**
		 * Creates a new Variance operator. By default, the population variance
		 * is computed. Use the type flags to change this. For example, the type
		 * argument <code>Variance.SAMPLE | Variance.DEVIATION</code> results in
		 * the sample standard deviation being computed.
		 * @param input the sub-expression of which to compute variance
		 * @param type the type of variance or deviation to compute
		 */
		public function Variance(input:*, type:int=0) {
			super(input);
			_type = type;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function reset():void
		{
			_sum = 0;
			_accum = 0;
			_count = 0;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			var n:Number = _count - (_type & SAMPLE ? 1 : 0);
			var v:Number = _sum / n;
			v = v*v + _accum / n;
			return (_type & DEVIATION ? Math.sqrt(v) : v);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function aggregate(value:Object):void
		{
			var x:Number = Number(_expr.eval(value));
			if (!isNaN(x)) {
				_sum += x;
				_accum += x*x;
				_count += 1;
			}
		}
		
	} // end of class Variance
}