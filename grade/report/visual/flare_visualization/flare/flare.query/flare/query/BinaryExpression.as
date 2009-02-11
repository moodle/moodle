package flare.query
{
	/**
	 * Base class for binary expression operators.
	 */
	public class BinaryExpression extends Expression
	{
		/** Code indicating the operation perfomed by this instance. */
		protected var _op:int;
		/** The left-hand-side sub-expression. */
		protected var _left:Expression;
		/** The right-hand-side sub-expression. */
		protected var _right:Expression;
		
		/** Code indicating the operation performed by this instance. */
		public function get operator():int { return _op; }
		public function set operator(op:int):void { _op = op; }
		/** String representation of the operation performed by this
		 *  instance. */
		public function get operatorString():String { return null; }
		
		/** The left-hand-side sub-expression. */
		public function get left():Expression { return _left; }
		public function set left(l:*):void {
			_left = Expression.expr(l);
		}
		
		/** The right-hand-side sub-expression. */
		public function get right():Expression { return _right; }
		public function set right(r:*):void {
			_right = Expression.expr(r);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function get numChildren():int {
			return 2;
		}
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new BinaryExpression.
		 * @param op the operation code
		 * @param minOp the minimum legal operation code
		 * @param maxOp the maximum legal operation code
		 * @param left the left-hand-side sub-expression
		 * @param right the right-hand-side sub-expression
		 */
		public function BinaryExpression(op:int, minOp:int, maxOp:int,
            left:*, right:*)
	    {
	        // operation check
	        if (op < minOp || op > maxOp) {
	            throw new ArgumentError("Unknown operation type: " + op);
	        }
	        // null check
	        if (left == null || right == null) {
	            throw new ArgumentError("Expressions must be non-null.");
	        }
	        _op = op;
	        this.left = left;
	        this.right = right;
	    }
	    
	    /**
		 * @inheritDoc
		 */
	    public override function getChildAt(idx:int):Expression
	    {
	    	switch (idx) {
	    		case 0: return _left;
	    		case 1: return _right;
	    		default: return null;
	    	}
	    }
	    
	    /**
		 * @inheritDoc
		 */
	    public override function setChildAt(idx:int, expr:Expression):Boolean
	    {
	    	switch (idx) {
	    		case 0: _left = expr;  return true;
	    		case 1: _right = expr; return true;
	    		default: return false;
	    	}
	    }
	    
	    /**
		 * @inheritDoc
		 */
	    public override function toString():String
	    {
	        return '(' + _left.toString() +' '
	                   +  operatorString  +' '
	        		   + _right.toString()+')';
	    }

	} // end of class BinaryExpression
}