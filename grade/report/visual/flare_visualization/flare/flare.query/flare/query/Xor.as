package flare.query
{
	/**
	 * Expression operator that computes the exclusive or ("xor") of
	 * sub-expression clauses.
	 */
	public class Xor extends CompositeExpression
	{
		/**
		 * Creates a new Xor operator.
		 * @param clauses the sub-expression clauses
		 */
		public function Xor(...clauses) {
			super(clauses);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function clone():Expression
		{
			return cloneHelper(new Xor());
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
			if (_children.length == 0) return false;
			
			var b:Boolean = _children[0].predicate(o);
			for (var i:int=1; i<_children.length; ++i) {
				b = (b != Expression(_children[i]).predicate(o));
			}
			return b;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function toString():String
		{
			return _children.length==0 ? "FALSE" : super.getString("XOR");
		}
		
	} // end of class Xor
}