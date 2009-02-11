package flare.query
{
	/**
	 * Aggregate (group-by) operator for computing the minimum of a set of
	 * values.
	 */
	public class Minimum extends AggregateExpression
	{
		private var _value:Object = null;
		
		/**
		 * Creates a new Minimum operator
		 * @param input the sub-expression of which to compute the minimum
		 */
		public function Minimum(input:*) {
			super(input);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			return _value;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function reset():void
		{
			_value = null;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function aggregate(value:Object):void
		{
			value = _expr.eval(value);
			if (_value == null || value < _value) {
				_value = value;
			}
		}
		
	} // end of class Minimum
}