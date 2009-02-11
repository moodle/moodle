package flare.query
{
	/**
	 * Expression operator for arithmetic operations. Performs addition,
	 * subtraction, multiplication, or division of sub-expression values.
	 */
	public class Arithmetic extends BinaryExpression
	{
		/** Indicates an addition operation. */
	    public static const ADD:int = 0;
	    /** Indicates a subtraction operation. */
	    public static const SUB:int = 1;
	    /** Indicates a multiplication operation. */
	    public static const MUL:int = 2;
	    /** Indicates a division operation. */
	    public static const DIV:int = 3;
	    /** Indicates a modulo operation. */
	    public static const MOD:int = 4;
		
		/** Returns a string representation of the arithmetic operator. */
		public override function get operatorString():String
		{
        	switch (_op) {
	        	case ADD: return '+';
	        	case SUB: return '-';
	        	case MUL: return '*';
	        	case DIV: return '/';
	        	case MOD: return '%';
	        	default: return '?';
	        }
		}
		
		/**
	     * Create a new Arithmetic expression.
	     * @param operation the operation to perform
	     * @param left the left sub-expression
	     * @param right the right sub-expression
	     */
	    public function Arithmetic(op:int, left:*, right:*)
	    {
	        super(op, ADD, MOD, left, right);
	    }
		
		/**
		 * @inheritDoc
		 */
		public override function clone():Expression
		{
			return new Arithmetic(_op, _left.clone(), _right.clone());
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			var x:Number = Number(_left.eval(o));
			var y:Number = Number(_right.eval(o));
			
	        // compute return value
	        switch (_op) {
	        	case ADD: return x+y;
	        	case SUB: return x-y;
	        	case MUL: return x*y;
	        	case DIV: return x/y;
	        	case MOD: return x%y;
	        }
	        throw new Error("Unknown operation type: "+_op);
		}
		
		// -- Static constructors ---------------------------------------------
		
		/**
		 * Creates a new Arithmetic operator for adding two numbers.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Arithmetic operator
		 */
		public static function Add(left:*, right:*):Arithmetic
		{
			return new Arithmetic(ADD, left, right);
		}
		
		/**
		 * Creates a new Arithmetic operator for subtracting one number
		 *  from another.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Arithmetic operator
		 */
		public static function Subtract(left:*, right:*):Arithmetic
		{
			return new Arithmetic(SUB, left, right);
		}
		
		/**
		 * Creates a new Arithmetic operator for multiplying two numbers.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Arithmetic operator
		 */
		public static function Multiply(left:*, right:*):Arithmetic
		{
			return new Arithmetic(MUL, left, right);
		}
		
		/**
		 * Creates a new Arithmetic operator for dividing one number
		 *  by another.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Arithmetic operator
		 */
		public static function Divide(left:*, right:*):Arithmetic
		{
			return new Arithmetic(DIV, left, right);
		}
		
		/**
		 * Creates a new Arithmetic operator for computing the modulo
		 *  (remainder) of a number.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Arithmetic operator
		 */
		public static function Mod(left:*, right:*):Arithmetic
		{
			return new Arithmetic(MOD, left, right);
		}
		
	} // end of class Arithmetic
}