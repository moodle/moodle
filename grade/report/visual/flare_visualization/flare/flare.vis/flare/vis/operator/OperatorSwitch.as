package flare.vis.operator
{
	import flare.animate.Transitioner;
	
	/**
	 * An OperatorSwitch maintains a list of sub-operators but only runs
	 * one at a time, allowing different operator chains to be executed at
	 * different times. Operators can be added to an OperatorSwitch
	 * using the <code>add</code> method. Once added, operators can be
	 * retrieved and set using their index in the list, either with array
	 * notation (<code>[]</code>) or with the <code>getOperatorAt</code> and
	 * <code>setOperatorAt</code> methods.
	 * 
	 * <p>The current sub-operator to run is determined by
	 * the <tt>index</tt> property. This index can be set manually or can
	 * be automatically determined upon each invocation by assigning a
	 * custom function to the <tt>indexFunction</tt> property.</p>
	 */
	public class OperatorSwitch extends OperatorList
	{
		private var _cur:int = -1;
		
		/** The currently active index of the switch. Only the operator at this
		 *  index is run when the <code>operate</code> method is called. */
		public function get index():int { return _cur; }
		public function set index(i:int):void { _cur = i; }
		
		/**
		 * A function that determines the current index value of this
		 * OperatorSwitch. This can be used to have the operator automatically
		 * adjust which sub-operators to run. If this property is non-null,
		 * the function will be invoked each time this OperatorSwitch is run
		 * and the index property will be set with the resulting value,
		 * overriding any previous index setting.
		 * The index function should accept zero arguments and return an
		 * integer that is a legal index value for this switch. If the
		 * returned value is not a legal index value (i.e., it is not an
		 * integer or is out of bounds) then no sub-operators will be
		 * run.
		 */
		public var indexFunction:Function = null;
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new OperatorSwitch.
		 * @param ops an ordered set of operators to include in the switch.
		 */
		public function OperatorSwitch(...ops) {
			for each (var op:IOperator in ops) {
				add(op);
			}
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			t = (t!=null ? t : Transitioner.DEFAULT);
			if (indexFunction != null) {
				_cur = indexFunction();
			}
			if (_cur >= 0 && _cur < _list.length)
				_list[_cur].operate(t);
		}
		
	} // end of class OperatorSwitch
}