package flare.vis.controls
{
	import flare.vis.Visualization;
	import flare.vis.operator.layout.Layout;
	
	import flash.display.InteractiveObject;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	
	/**
	 * An interactive control that updates a layout's anchor point in response
	 * to mouse movement. This control is often used to dynamically update a
	 * focus+context distortion.
	 */
	public class AnchorControl extends Control
	{
		private var _layout:Layout;
		
		public function get layout():Layout { return _layout; }
		public function set layout(l:Layout):void { _layout = l; }
		
		/** Update function called when the layout anchor changes. */
		public var update:Function = function():void {
			Visualization(_object).update();
		}
		
		/**
		 * Creates a new AnchorControl
		 */
		public function AnchorControl(vis:Visualization=null, layout:Layout=null)
		{
			_layout = layout;
			attach(vis);
		}
		
		/** @inheritDoc */
		public override function attach(obj:InteractiveObject):void
		{
			super.attach(obj);
			if (obj != null) {
				obj.addEventListener(Event.ENTER_FRAME, updateMouse);
			}
		}
		
		/** @inheritDoc */
		public override function detach():InteractiveObject
		{
			if (_object != null) {
				_object.removeEventListener(Event.ENTER_FRAME, updateMouse);
			}
			return super.detach();
		}
		
		/**
		 * Causes the layout anchor to be updated according to the current
		 * mouse position.
		 * @param evt an optional mouse event
		 */
		public function updateMouse(evt:Event=null):void
		{
			// get current anchor, run update if changed
			var p1:Point = _layout.layoutAnchor;
			_layout.layoutAnchor = new Point(_object.mouseX, _object.mouseY);
			// distortion might snap the anchor to the layout bounds
			// so we need to re-retrieve the point to ensure accuracy
			var p2:Point = _layout.layoutAnchor;
			if (p1.x != p2.x || p1.y != p2.y) update();
		}
		
	} // end of class AnchorControl
}