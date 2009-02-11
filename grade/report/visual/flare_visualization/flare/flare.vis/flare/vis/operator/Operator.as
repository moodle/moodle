package flare.vis.operator
{
	import flare.animate.Transitioner;
	import flare.vis.Visualization;
	
	/**
	 * Operators performs processing tasks on the contents of a Visualization.
	 * These tasks include layout, and color, shape, and size encoding.
	 * Custom operators can be defined by subclassing this class.
	 */
	public class Operator implements IOperator
	{
		// -- Properties ------------------------------------------------------
		
		private var _vis:Visualization;
		
		/** The visualization processed by this operator. */
		public function get visualization():Visualization { return _vis; }
		public function set visualization(v:Visualization):void {
			_vis = v; setup();
		}
		
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Performs an operation over the contents of a visualization.
		 * @param t a Transitioner instance for collecting value updates.
		 */
		public function operate(t:Transitioner=null) : void {
			// for sub-classes to implement	
		}
		
		/**
		 * Setup method invoked whenever this operator's visualization
		 * property is set.
		 */
		public function setup():void
		{
			// for subclasses
		}
		
		// -- MXML ------------------------------------------------------------
		
		/** @private */
		public function initialized(document:Object, id:String):void
		{
			// do nothing
		}
		
	} // end of class Operator
}