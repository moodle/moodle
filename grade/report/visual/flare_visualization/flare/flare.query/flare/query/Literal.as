package flare.query
{	
	/**
	 * Expression instance for a literal value.
	 */
	public class Literal extends Expression
	{
		/** The boolean true literal. */
		public static const TRUE:Literal = new Literal(true);
		/** The boolean false literal. */
		public static const FALSE:Literal = new Literal(false);
		
		private var _value:Object = null;
		
		/** The literal value of this expression. */
		public function get value():Object { return _value; }
		
		/**
		 * Creates a new Literal instance.
		 * @param val the literal value
		 */
		public function Literal(val:Object=null) {
			_value = val;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function clone():Expression
		{
			return new Literal(_value);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function predicate(o:Object):Boolean
		{
			return Boolean(_value);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			return _value;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function toString():String
		{
			return String(_value);
		}
		
	} // end of class Literal
}