package flare.vis.controls
{
	import flash.display.InteractiveObject;

	/**
	 * Abstract class which interactive controls can inherit.
	 */
	public class Control implements IControl
	{
		/** @private */
		protected var _object:InteractiveObject;
		
		/**
		 * Creates a new Control
		 */
		public function Control() {
			// do nothing
		}
		
		/** @inheritDoc */
		public function get object():InteractiveObject
		{
			return _object;
		}
		
		/** @inheritDoc */
		public function attach(obj:InteractiveObject):void
		{
			_object = obj;
		}
		
		/** @inheritDoc */
		public function detach():InteractiveObject
		{
			var obj:InteractiveObject = _object;
			_object = null;	
			return obj;
		}
		
		// -- MXML ------------------------------------------------------------
		
		/** @private */
		public function initialized(document:Object, id:String):void
		{
			// do nothing
		}
		
	} // end of class Control
}