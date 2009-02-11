package flare.vis.controls
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.InteractiveObject;
	import flash.events.MouseEvent;

	/**
	 * Interactive control for responding to mouse hover events. The
	 * <code>onRollOver</code> and <code>onRollOut</code> function properties
	 * should be set with custom functions for performing actions when the
	 * mouse cursor hovers over a display object.
	 */
	public class HoverControl extends Control
	{
		/** Constant indicating that objects hovered over should not be moved
		 *  within their parent container changed. */
		public static const DONT_MOVE:int = 0;
		/** Constant indicating that objects hovered over should be moved to
		 *  the front of their parent container and kept there. */
		public static const MOVE_TO_FRONT:int = 1;
		/** Constant indicating that objects hovered over should be moved to
		 *  the front of their parent container and then returned to their
		 *  previous position when the mouse rolls out. */
		public static const MOVE_AND_RETURN:int = 2;
		
		private var _cur:DisplayObject;
		private var _idx:int;
		private var _filter:Function;
		private var _movePolicy:int;
		
		/** Function invoked when the mouse enters an item. */
		public var onRollOver:Function;
		/** Function invoked when the mouse leaves an item. */
		public var onRollOut:Function;
				
		/**
		 * Creates a new HoverControl.
		 * @param container the container object to monitor
		 * @param filter a Boolean-valued filter function indicating which
		 *  items should trigger hover processing
		 * @param movePolicy indicates which policy should be used for changing
		 *  the z-ordering of hovered items. One of DONT_MOVE (the default),
		 *  MOVE_TO_FRONT, or MOVE_AND_RETURN.
		 */
		public function HoverControl(container:InteractiveObject=null,
			filter:Function=null, movePolicy:int=DONT_MOVE)
		{
			attach(container);
			_filter = filter;
			_movePolicy = movePolicy;
		}
		
		/** @inheritDoc */
		public override function attach(obj:InteractiveObject):void
		{
			super.attach(obj);
			if (obj != null) {
				obj.addEventListener(MouseEvent.MOUSE_OVER, onMouseOver);
				obj.addEventListener(MouseEvent.MOUSE_OUT, onMouseOut);
			}
		}
		
		/** @inheritDoc */
		public override function detach():InteractiveObject
		{
			if (_object != null) {
				_object.removeEventListener(MouseEvent.MOUSE_OVER, onMouseOver);
				_object.removeEventListener(MouseEvent.MOUSE_OUT, onMouseOut);
			}
			return super.detach();
		}
		
		private function onMouseOver(evt:MouseEvent):void
		{
			var n:DisplayObject = evt.target as DisplayObject;
			if (n==null || (_filter!=null && !_filter(n))) return;
			
			_cur = n;
			
			if (_movePolicy != DONT_MOVE && n.parent != null) {
				var p:DisplayObjectContainer = n.parent;
				_idx = p.getChildIndex(n);
				p.setChildIndex(n, p.numChildren-1);
			}
			if (onRollOver != null) { onRollOver(_cur); }
		}
		
		private function onMouseOut(evt:MouseEvent):void
		{
			if (_cur == null) return;
			if (onRollOut != null) { onRollOut(_cur); }
			if (_movePolicy == MOVE_AND_RETURN) {
				_cur.parent.setChildIndex(_cur, _idx);
			}
			_cur = null;
		}
		
	} // end of class HoverControl
}