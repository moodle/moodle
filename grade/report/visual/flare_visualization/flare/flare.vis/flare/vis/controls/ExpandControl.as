package flare.vis.controls
{
	import flare.vis.Visualization;
	import flare.vis.data.NodeSprite;
	
	import flash.display.InteractiveObject;
	import flash.events.MouseEvent;

	/**
	 * Interactive control for expaning and collapsing graph or tree nodes
	 * by clicking them. This control will only work when applied to a
	 * Visualization instance.
	 */
	public class ExpandControl extends Control
	{
		private var _cur:NodeSprite;

		/** Boolean-valued filter function for determining which items
		 *  this control will attempt to expand or collapse. */
		public var filter:Function;
		
		/** Update function invoked after expanding or collapsing an item.
		 *  By default, invokes the <code>update</code> method on the
		 *  visualization with a 1-second transitioner. */
		public var update:Function = function():void {
			Visualization(_object).update(1).play();
		}
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new ExpandControl.
		 * @param vis the visualization to interact with
		 * @param filter a Boolean-valued filter function for determining which
		 *  item this control will expand or collapse
		 * @param update function invokde after expanding or collapsing an
		 *  item.
		 */		
		public function ExpandControl(vis:Visualization=null, filter:Function=null, 
									  update:Function=null)
		{
			attach(vis);
			this.filter = filter;
			if (update != null) this.update = update;
		}
		
		/** @inheritDoc */
		public override function attach(obj:InteractiveObject):void
		{
			if (obj==null) { detach(); return; }
			if (!(obj is Visualization)) {
				throw new Error("This control can only be attached to a Visualization");
			}
			super.attach(obj);
			obj.addEventListener(MouseEvent.MOUSE_DOWN, onMouseDown);
		}
		
		/** @inheritDoc */
		public override function detach():InteractiveObject
		{
			if (_object != null) {
				_object.removeEventListener(MouseEvent.MOUSE_DOWN, onMouseDown);
			}
			return super.detach();
		}
		
		private function onMouseDown(event:MouseEvent) : void {
			var s:NodeSprite = event.target as NodeSprite;
			if (s==null) return; // exit if not a NodeSprite
			
			if (filter==null || filter(s)) {
				_cur = s;
				_cur.stage.addEventListener(MouseEvent.MOUSE_MOVE, onDrag);
				_cur.stage.addEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			}
			event.stopPropagation();
		}
		
		private function onDrag(event:MouseEvent) : void {
			_cur.stage.removeEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			_cur.stage.removeEventListener(MouseEvent.MOUSE_MOVE, onDrag);
			_cur = null;
		}
		
		private function onMouseUp(event:MouseEvent) : void {
			_cur.stage.removeEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			_cur.stage.removeEventListener(MouseEvent.MOUSE_MOVE, onDrag);
			_cur.expanded = !_cur.expanded;
			_cur = null;	
			event.stopPropagation();
			
			update();
		}
		
	} // end of class ExpandControl
}