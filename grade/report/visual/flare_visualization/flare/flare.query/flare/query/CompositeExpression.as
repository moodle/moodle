package flare.query
{
	import flash.utils.ByteArray;
	
	/**
	 * Base class for expressions with an arbitrary number of sub-epxressions.
	 */
	public class CompositeExpression extends Expression
	{
		/** Array of sub-expressions. */
		protected var _children:Array;
		
		/**
		 * @inheritDoc
		 */
		public override function get numChildren():int {
			return _children.length;
		}
		
		/**
		 * Creates a new CompositeExpression.
		 * @param items either a single sub-expression or an array of
		 *  sub-expressions
		 */
		public function CompositeExpression(items:Object=null) {
			if (items is Array) {
				setChildren(items as Array);
			} else if (items is Expression) {
				_children = new Array();
				addChild(items as Expression);
			} else if (items == null) {
				_children = new Array();
			} else {
				throw new ArgumentError(
					"Input must be an expression or array of expressions");
			}
		}
		
		/**
		 * Helper routine that clones this composite's sub-expressions.
		 * @param ce the cloned composite expression
		 * @return the input expression
		 */
		protected function cloneHelper(ce:CompositeExpression):Expression
		{
			for (var i:int=0; i<_children.length; ++i) {
				ce.addChild(Expression(_children[i]).clone());
			}
			return ce;
		}
		
		/**
		 * Sets the sub-expressions of this composite
		 * @param array an array of sub-expressions
		 */
		public function setChildren(array:Array):void
		{
			_children = new Array();
			for each (var e:* in array) {
				_children.push(Expression.expr(e));
			}
		}
		
		/**
		 * Adds an additional sub-expression to this composite.
		 * @param expr the sub-expression to add.
		 */
		public function addChild(expr:Expression):void
		{
			_children.push(expr);
		}
		
		/**
		 * Removes a sub-expression from this composite.
		 * @param expr the sub-epxressions to remove
		 * @return true if the expression was found and removed, false
		 *  otherwise
		 */
		public function removeChild(expr:Expression):Boolean
		{
			var idx:int = _children.indexOf(expr);
			if (idx >= 0) {
				_children.splice(idx, 1);
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * @inheritDoc
		 */
		public override function getChildAt(idx:int):Expression
		{
			return _children[idx];
		}
		
		/**
		 * @inheritDoc
		 */
		public override function setChildAt(idx:int, expr:Expression):Boolean
		{
			if (idx>=0 && idx<_children.length) {
				_children[idx] = expr;
				return true;
			}
			return false;
		}
		
		/**
		 * Removes all sub-expressions from this composite.
		 */
		public function removeAllChildren():void
		{
			while (_children.length > 0) _children.pop();
		}
		
		/**
		 * Returns a string representation of this composite's sub-expressions.
		 * @param op a string describing the sub-class operator (null by
		 *  default). If non-null, the operator string will be interspersed
		 *  between sub-expression values in the output string.
		 * @return the requested string
		 */
		protected function getString(op:String=null):String
		{	        
	        var b:ByteArray = new ByteArray();
	        b.writeUTFBytes('(');
			for (var i:uint=0; i<_children.length; ++i) {
				if (i > 0) {
					if (op == null) {
						b.writeUTFBytes(', ');
					} else {
						b.writeUTFBytes(' ');
						b.writeUTFBytes(op);
						b.writeUTFBytes(' ');
					}
				}
				b.writeUTFBytes(_children[i].toString());
			}
			b.writeUTFBytes(')');	        

			b.position = 0;
	        return b.readUTFBytes(b.length);
		}
		
	} // end of class CompositeExpression
}