package flare.query
{
	import flare.util.Property;
	
	/**
	 * Expression operator that retrieves a variable value from an object
	 * property. Uses a <code>flare.util.Property</code> instance to access
	 * the variable value.
	 * @see flare.util.Property
	 */
	public class Variable extends Expression
	{
		private var _prop:Property;
		
		/** The name of the variable property. */
		public function get name():String { return _prop.name; }
		public function set name(f:String):void {
			_prop = Property.$(f);
		}
		
		/**
		 * Creates a new Variable operator.
		 * @param name the name of the variable property
		 */
		public function Variable(name:String) {
			this.name = name;
		}
		
		/**
		 * @inheritDoc
		 */
		public override function clone():Expression
		{
			return new Variable(_prop.name);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function eval(o:Object=null):Object
		{
			return _prop.getValue(o);
		}
		
		/**
		 * @inheritDoc
		 */
		public override function toString():String
		{
			return "`"+_prop.name+"`";
		}
		
	} // end of class Variable
}