package flare.vis.controls
{
	import flash.display.InteractiveObject;
	
	import mx.core.IMXMLObject;
	
	public interface IControl extends IMXMLObject
	{
		/** The interactive object this control is attached to. */
		function get object():InteractiveObject;
		
		/**
		 * Attach this control to the given interactive object. This method
		 * will automatically detach if already attached to another object.
		 * @param obj the display object to attach to
		 */
		function attach(obj:InteractiveObject):void;
		
		/**
		 * Detach this control.
		 * @return the interactive object this control was attached to,
		 *  or null if this control was not attached.
		 */
		function detach():InteractiveObject;
		
	} // end of interface IControl
}