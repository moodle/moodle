package flare.query
{
	/**
	 * Expression operator for an if statement that performs conditional
	 * execution.
	 */
	public class If extends Expression
	{
		private var _test:Expression;
    	private var _then:Expression;
    	private var _else:Expression;
	    
	    /** The conditional clause of the if statement. */
	    public function get test():Expression { return _test; }
	    public function set test(e:*):void {
	    	_test = Expression.expr(e);
	    }
	    
	    /** Sub-expression evaluated if the test condition is true. */
	    public function get then():Expression { return _then; }
	    public function set then(e:*):void {
	    	_then = Expression.expr(e);
	    }
	    
	    /** Sub-expression evaluated if the test condition is false. */
	    public function get els():Expression { return _else; }
	    public function set els(e:*):void {
	    	_else = Expression.expr(e);
	    }
	    
	    /**
		 * @inheritDoc
		 */
	    public override function get numChildren():int { return 3; }
	    
	    // --------------------------------------------------------------------
	    
	    /**
	     * Create a new IfExpression.
	     * @param test the test expression for the if statement
	     * @param thenExpr the expression to evaluate if the test predicate
	     * evaluates to true
	     * @param elseExpr the expression to evaluate if the test predicate
	     * evaluates to false
	     */
	    public function If(test:*, thenExpr:*, elseExpr:*)
	    {
	        this.test = test;
	        this.then = thenExpr;
	        this.els = elseExpr;
	    }
	    
	    /**
		 * @inheritDoc
		 */
	    public override function clone():Expression
		{
			return new If(_test.clone(), _then.clone(), _else.clone());
		}
	    
	    /**
		 * @inheritDoc
		 */
	    public override function eval(o:Object=null):Object
		{
			return (_test.predicate(o) ? _then : _else).eval(o);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function getChildAt(idx:int):Expression
		{
			switch (idx) {
				case 0: return _test;
				case 1: return _then;
				case 2: return _else;
				default: return null;
			}
		}
		
		/**
		 * @inheritDoc
		 */
		public override function setChildAt(idx:int, expr:Expression):Boolean
		{
			switch (idx) {
				case 0: _test = expr; return true;
				case 1: _then = expr; return true;
				case 2: _else = expr; return true;
				default: return false;
			}
		}
		
		/**
		 * @inheritDoc
		 */
	    public override function toString():String
	    {
	        return "IF " + _test.toString()
	            + " THEN " + _then.toString()
	            + " ELSE " + _else.toString();
	    }
		
	} // end of class If
}