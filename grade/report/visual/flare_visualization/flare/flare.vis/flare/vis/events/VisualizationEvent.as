package flare.vis.events
{
	import flash.events.Event;
	import flare.animate.Transitioner;

	/**
	 * Event fired in response to visualization updates.
	 */
	public class VisualizationEvent extends Event
	{
		/** A visualization update event. */
		public static const UPDATE:String = "update";
		
		private var _trans:Transitioner;
		
		/** Transitioner used in the visualization update. */
		public function get transitioner():Transitioner { return _trans; }
		
		/**
		 * Creates a new VisualizationEvent.
		 * @param type the event type
		 * @param trans the Transitioner used in the visualization update
		 */		
		public function VisualizationEvent(type:String, trans:Transitioner=null)
		{
			super(type);
			_trans = trans==null ? Transitioner.DEFAULT : trans;
		}
		
	} // end of class VisualizationEvent
}