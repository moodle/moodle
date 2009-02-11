package flare.query
{
	/**
	 * Aggregate (group-by) operator for computing the average of a set of
	 * values.
	 */
	public class Average extends AggregateExpression
	{
		protected var _sum:Number;
		protected var _count:Number;
		
		/**
		 * Creates a new Average operator
		 * @param input the sub-expression of which to compute the average
		 */
		public function Average(input:*) {
			super(input);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function reset():void
		{
			_sum = 0;
			_count = 0;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			return _sum / _count;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function aggregate(value:Object):void
		{
			var x:Number = Number(_expr.eval(value));
			if (!isNaN(x)) {
				_sum += x;
				_count += 1;
			}
		}
		
	} // end of class Average
}