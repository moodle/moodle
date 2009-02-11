package flare.query
{
	/**
	 * Aggregate (group-by) operator for counting the number of distinct
	 * values in a set of values.
	 */
	public class Distinct extends AggregateExpression
	{
		private var _map:Object;
		private var _count:int;
		
		/**
		 * Creates a new Distinct operator
		 * @param input the sub-expression of which to compute the distinct
		 *  values
		 */
		public function Distinct(input:*) {
			super(input);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function reset():void
		{
			_map = {};
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
			value = _expr.eval(value);
			if (_map[value] == undefined) {
				_count++;
				_map[value] = 1;
			}
		}
		
	} // end of class Distinct
}