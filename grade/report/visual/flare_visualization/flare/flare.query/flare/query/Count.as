package flare.query
{
	/**
	 * Aggregate (group-by) operator for counting the number of items in a set
	 * of values.
	 */
	public class Count extends AggregateExpression
	{
		private var _count:int;
		
		/**
		 * Creates a new Count operator
		 * @param input the sub-expression of which to count the value
		 */
		public function Count(input:*) {
			super(input);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function reset():void
		{
			_count = 0;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			return _count;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function aggregate(value:Object):void
		{
			if (_expr.eval(value) != null) {
				_count++;
			}
		}
		
	} // end of class Count
}