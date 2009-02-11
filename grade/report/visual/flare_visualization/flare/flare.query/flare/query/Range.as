package flare.query
{	
	/**
	 * Expression operator that tests if a value is within a given range.
	 * Implemented as an <code>And</code> of <code>Comparison</code>
	 * expressions.
	 */
	public class Range extends And
	{
		/** Sub-expression for the minimum value of the range. */
		public function get min():Expression { return _children[0].left; }
		public function set min(e:*):void {
			_children[0].left = Expression.expr(e);
		}
		
		/** Sub-expression for the maximum value of the range. */
		public function get max():Expression { return _children[1].right; }
		public function set max(e:*):void {
			_children[1].right = Expression.expr(e);
		}
		
		/** Sub-expression for the value to test for range inclusion. */
		public function get val():Expression { return _children[0].right; }
		public function set val(e:*):void {
			var expr:Expression = Expression.expr(e);
			_children[0].right = expr;
			_children[1].left = expr;
		}
		
		/**
		 * Create a new Range operator.
		 * @param min sub-expression for the minimum value of the range
		 * @param max sub-expression for the maximum value of the range
		 * @param val sub-expression for the value to test for range inclusion
		 */
		public function Range(min:*, max:*, val:*)
		{
			addChild(new Comparison(Comparison.LTEQ, min, val));
			addChild(new Comparison(Comparison.LTEQ, val, max));
		}
		
		/**
		 * @inheritDoc
		 */
		public override function clone():Expression
		{
			return new Range(min.clone(), max.clone(), val.clone());
		}
		
	} // end of class RangePredicate
}