package flare.query
{
	/**
	 * Base class representing an aggregate (group-by) query operator.
	 */
	public class AggregateExpression extends Expression
	{
		/** The sub-expression to aggregate. */
		protected var _expr:Expression;
		
		/** The sub-expression to aggregate. */
		public function get input():Expression { return _expr; }
		public function set input(e:*):void {
			_expr = Expression.expr(e);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function get numChildren():int {
			return 1;
		}
		
		/**
		 * Creates a new AggregateExpression.
		 * @param input the sub-expression to aggregate.
		 */
		public function AggregateExpression(input:*) {
			this.input = input;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function getChildAt(idx:int):Expression
	    {
	    	return idx==0 ? _expr : null;
	    }
	    
	    /**
		 * @inheritDoc
		 */
	    public override function setChildAt(idx:int, expr:Expression):Boolean
	    {
	    	if (idx == 0) {
	    		_expr = expr;
	    		return true;
	    	}
	    	return false;
	    }
	    
	    // --------------------------------------------------------------------
	    
	    /**
	     * Resets the aggregation computation.
	     */ 
	    public function reset():void
		{
			// subclasses override this
		}
		
		/**
		 * Increments the aggregation computation to include the input value.
		 * @param value a value to include within the aggregation.
		 */
		public function aggregate(value:Object):void
		{
			// subclasses override this
		}
	    
	} // end of class AggregateExpression
}