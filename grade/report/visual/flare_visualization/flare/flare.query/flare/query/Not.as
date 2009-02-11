package flare.query
{	
	/**
	 * Expression operator that returns the logical "not" of a sub-expression.
	 */
	public class Not extends Expression
	{
		private var _clause:Expression;
		
		/** The sub-expression clause to negate. */
		public function get clause():Expression { return _clause; }
		public function set clause(e:*):void { _clause = Expression.expr(e); }
		
		/**
		 * @inheritDoc
		 */
		public override function get numChildren():int { return 1; }
		
		/**
		 * Creates a new Not operator.
		 * @param clause the sub-expression clause to negate
		 */
		public function Not(clause:*) {
			_clause = Expression.expr(clause);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function clone():Expression
		{
			return new Not(_clause.clone());
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			return predicate(o);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function predicate(o:Object):Boolean
		{
			return !_clause.eval(o);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function getChildAt(idx:int):Expression
		{
			return (idx==0 ? _clause : null);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function setChildAt(idx:int, expr:Expression):Boolean
		{
			if (idx == 0) {
				_clause = expr;
				return true;
			}
			return false;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function toString():String
		{
			return "NOT " + _clause.toString();
		}
		
	} // end of class Not
}