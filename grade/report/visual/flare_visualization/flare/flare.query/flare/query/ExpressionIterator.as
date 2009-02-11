package flare.query
{	
	/**
	 * The ExpressionIterator simplifies the process of traversing an
	 * expression tree.
	 */
	public class ExpressionIterator
	{
		private var _estack:Array;
		private var _istack:Array;
		private var _root:Expression;
		private var _cur:Expression;
		private var _idx:int;
		
		/** The expression being traversed. */
		public function get expression():Expression { return _root; }
		public function set expression(expr:Expression):void {
			_root = expr; reset();
		}
		/** The parent expression of the iterator's current position. */
		public function get parent():Expression { return _estack[_estack.length-1]; }
		/** The expression at this iterator's current position. */
		public function get current():Expression { return _cur; }
		/** The depth of this iterator's current position in the
		 *  expression tree. */
		public function get depth():int { return _estack.length; }
		/** An array of expressions from the root expression down to this
		 *  iterator's current position. */
		public function get path():Array {
			var a:Array = new Array(_estack.length);
			for (var i:int=0; i<a.length; ++i) { a[i] = _estack[i]; }
			a.push(_cur);
			return a;
		}
		
		/**
		 * Creates a new ExpressionIterator.
		 * @param expr the expression to iterate over
		 */
		public function ExpressionIterator(expr:Expression=null) {
			expression = expr;
		}
		
		/**
		 * Resets this iterator to the root of the expression tree.
		 */
		public function reset():void
		{
			_estack = new Array();
			_istack = new Array();
			_cur = _root;
			_idx = 0;
		}
		
		/**
		 * Moves the iterator one level up the expression tree.
		 * @return the new current expression, or null if the iterator
		 *  could not move any further up the tree
		 */
		public function up():Expression
		{
			if (_cur != _root) {
				_cur = _estack.pop();
				_idx = _istack.pop();
				return _cur;
			} else {
				return null;
			}
		}
		
		/**
		 * Moves the iterator one level down the expression tree. By default,
		 * the iterator moves to the left-most child of the previous position.
		 * @param idx the index of the child expression this iterator should
		 *  move down to
		 * @return the new current expression, or null if the iterator
		 *  could not move any further down the tree
		 */
		public function down(idx:int=0):Expression
		{
			if (idx >= 0 && _cur.numChildren > idx) {
				_estack.push(_cur);
				_istack.push(_idx);
				_idx = idx;
				_cur = _cur.getChildAt(_idx);
				return _cur;
			} else {
				return null;
			}
		}
		
		/**
		 * Moves the iterator to the next sibling expression in the expression
		 * tree.
		 * @return the new current expression, or null if the current position
		 *  has no next sibling.
		 */
		public function next():Expression
		{
			if (_estack.length > 0) {
				var p:Expression = _estack[_estack.length-1];
				if (p.numChildren > _idx+1) {
					_idx = _idx + 1;
					_cur = p.getChildAt(_idx);
					return _cur;
				}
			}
			return null;
		}
		
		/**
		 * Moves the iterator to the previous sibling expression in the
		 * expression tree.
		 * @return the new current expression, or null if the current position
		 *  has no previous sibling.
		 */
		public function prev():Expression
		{
			if (_estack.length > 0) {
				var p:Expression = _estack[_estack.length-1];
				if (_idx > 0) {
					_idx = _idx - 1;
					_cur = p.getChildAt(_idx);
					return _cur;
				}
			}
			return null;
		}
		
	} // end of class ExpressionIterator
}