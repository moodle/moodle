package flare.query
{
	/**
	 * Expression operator for text matching operations. Performs prefix,
	 * suffix, containment, and regular expression matching.
	 */
	public class Match extends BinaryExpression
	{
		/** Indicates a prefix matching test. */
	    public static const PREFIX:int = 0;
	    /** Indicates a suffix matching test. */
	    public static const SUFFIX:int = 1;
	    /** Indicates a string containment test. */
	    public static const WITHIN:int = 2;
	    /** Indicates a regular expression matching test. */
	    public static const REGEXP:int = 3;
		
		/** Returns a string representation of the string matching operator. */
		public override function get operatorString():String
		{
        	switch (_op) {
	        	case PREFIX: return 'STARTS-WITH';
	        	case SUFFIX: return 'ENDS-WITH';
	        	case WITHIN: return 'CONTAINS';
	        	case REGEXP: return 'REGEXP';
	        	default: return '?';
	        }
		}
		
		/**
	     * Create a new Match expression.
	     * @param operation the operation to perform
	     * @param left the left sub-expression
	     * @param right the right sub-expression
	     */
	    public function Match(op:int, left:*, right:*)
	    {
	        super(op, PREFIX, REGEXP, left, right);
	    }
		
		/**
		 * @inheritDoc
		 */
		public override function clone():Expression
		{
			return new Match(_op, _left.clone(), _right.clone());
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			var s:String = String(_left.eval(o));
			var p:String = String(_right.eval(o));
			
	        // compute return value
	        switch (_op) {
	        	case PREFIX: return StringUtil.startsWith(s, p);
	        	case SUFFIX: return StringUtil.endsWith(s, p);
	        	case WITHIN: return s.indexOf(p) >= 0;
	        	case REGEXP: return parseRegExp(p).test(s);
	        }
	        throw new Error("Unknown operation type: "+_op);
		}
		
		private var cachedRegExp:RegExp;
		private var cachedPattern:String;
		
		private function parseRegExp(p:String):RegExp
		{
			if (p == cachedPattern) return cachedRegExp;
			
			cachedPattern = p;
			var tok:Array = p.split("/");
			cachedRegExp = new RegExp(tok[1], tok[2]);
			return cachedRegExp;
		}
		
		// -- Static constructors ---------------------------------------------
		
		/**
		 * Creates a new Match operator for matching string prefix.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Match operator
		 */
		public static function StartsWith(left:*, right:*):Match
		{
			return new Match(PREFIX, left, right);
		}
		
		/**
		 * Creates a new Match operator for matching a string suffix.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Match operator
		 */
		public static function EndsWith(left:*, right:*):Match
		{
			return new Match(SUFFIX, left, right);
		}
		
		/**
		 * Creates a new Match operator for matching string containment.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Match operator
		 */
		public static function Contains(left:*, right:*):Match
		{
			return new Match(WITHIN, left, right);
		}
		
		/**
		 * Creates a new Match operator for matching a regular expression.
		 * @param left the left-hand input expression
		 * @param right the right-hand input expression
		 * @return the new Match operator
		 */
		public static function RegEx(left:*, right:*):Match
		{
			return new Match(REGEXP, left, right);
		}
		
	} // end of class Match
}