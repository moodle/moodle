package flare.vis.events
{
	import flash.events.Event;
	import flare.vis.data.Data;
	import flare.vis.data.DataSprite;
	import flare.vis.data.NodeSprite;
	import flare.vis.data.EdgeSprite;
	
	/**
	 * Event fired when a <code>Data</code> instance is modified.
	 */
	public class DataEvent extends Event
	{
		/** A data added event. */
		public static const DATA_ADDED:String   = "DATA_ADDED";
		/** A data removed event. */
		public static const DATA_REMOVED:String = "DATA_REMOVED";
		/** A data updated event. */
		public static const DATA_UPDATED:String = "DATA_UPDATED";
		
		private var _data:Data;
		private var _item:DataSprite;
		
		/** The Data instance that was modified. */
		public function get data():Data { return _data; }
		/** The DataSprite that was added/removed/updated. */
		public function get item():DataSprite { return _item; }
		/** The NodeSprite that was added/removed/updated. */
		public function get node():NodeSprite { return _item as NodeSprite; }
		/** The EdgeSprite that was added/removed/updated. */
		public function get edge():EdgeSprite { return _item as EdgeSprite; }
		
		/**
		 * Creates a new DataEvent.
		 * @param type the event type (DATA_ADDED, DATA_REMOVED, or
		 *  DATA_UPDATED)
		 * @param data the Data instance that was modified
		 * @param item the DataSprite that was added, removed, or updated
		 */		
		public function DataEvent(type:String, data:Data, item:DataSprite)
		{
			super(type);
			_data = data;
			_item = item;
		}
		
		/** @inheritDoc */
		public override function clone():Event
		{
			return new DataEvent(type, _data, _item);
		}
		
	} // end of class DataEvent
}