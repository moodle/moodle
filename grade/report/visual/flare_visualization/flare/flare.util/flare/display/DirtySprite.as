package flare.display
{
	import flash.display.DisplayObject;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.geom.Rectangle;
	
	/**
	 * A Sprite that redraws itself as needed when marked as "dirty".
	 * This allows multiple changes to be made to the sprite between frames
	 * without triggering a potentially costly redraw for each property update.
	 * Instead, the <code>dirty()</code> method should be called whenever a
	 * change is made to the Sprite that would normally require a redraw. This
	 * class will ensure that the Sprite is redrawn only once before the next
	 * frame is rendered.
	 * 
	 * <p>Subclasses should place drawing code within the <code>render()</code>
	 * method. For all properties used by the <code>render</code> method to help
	 * draw this sprite, the corresponding "setter" method should call the
	 * <code>dirty()</code> method to mark the sprite as dirty and thereby
	 * trigger a redraw for the next frame.</p>
	 * 
	 * <p>Internally, the DirtySprite class maintains a static list of all
	 * "dirty" sprites, and redraws each sprite in the list when a
	 * <code>Event.RENDER</code> event is issued. Typically, this process is
	 * performed automatically. In a few cases, erratic behavior has been
	 * observed due to a Flash Player bug that results in <code>RENDER</code>
	 * events not being properly issued. As a fallback, the static
	 * <code>renderDirty()</code> method can be invoked to manually force
	 * each dirty sprite to be redrawn.</p>
	 */
	public class DirtySprite extends Sprite implements IRenderable
	{
		private static var __stage:Stage;
		private static var __installed:Boolean = false;
		private static var __dirtyList:Array = [];
		
		/**
		 * Installs the frame render listener on the stage.
		 */	
		private static function install(stage:Stage):void
		{
			__stage = stage;
			__stage.addEventListener(Event.RENDER, renderDirty);
			__installed = true;
		}
		
		/**
		 * Frame render callback that renders all sprites on the dirty list.
		 * Typically, this method is automatically triggered by stage
		 * RENDER events. It can also be manually invoked to redraw all
		 * dirty DirtySprites.
		 * @param evt the event that triggered the rendering (can be null)
		 */
		public static function renderDirty(evt:Event=null):void
		{
			while (__dirtyList.length > 0) {
				var ds:DirtySprite = DirtySprite(__dirtyList.pop());
				var db:Boolean = (ds._dirty == DIRTY);
				ds._dirty = CLEAN;
				if (db) ds.render();
			}

			// We need to remove and then re-add the listeners
			// to work around Flash Player bugs (#139381?). Ugh.
			// TODO: it seems this is not a complete solution, as in
			// rare cases RENDER events are still omitted.
			if (__stage != null) {
				__stage.removeEventListener(Event.RENDER, renderDirty);
				__installed = false;
			}
		}
		
		// --------------------------------------------------------------------
		
		private static const CLEAN:int = 0; // no changes
		private static const DIRTY:int = 1; // re-rendering needed
		private static const VISIT:int = 2; // was re-rendered, but on list
		
		/** @private */
		protected var _dirty:int = DIRTY; // dirty at birth
		
		/**
		 * Creates a new DirtySprite. Registers this Sprite to receive
		 * added-to-stage events.
		 */
		public function DirtySprite() {
			this.addEventListener(Event.ADDED_TO_STAGE, onAddToStage,
								  false, 0, true); // use weak reference
		}
		
		/**
		 * Makes sure that "dirtying" changes made to this Sprite
		 * while it is off the display list still result in a
		 * re-rendering if the Sprite is ever added to the list.
		 */
		private function onAddToStage(evt:Event):void
		{
			if (_dirty) {
				if (!__installed) install(stage);
				__dirtyList.push(this);
				stage.invalidate();
			}
		}

		/**
		 * Marks this sprite as "dirty" and in need of re-rendering.
		 * The next time that (a) a new frame is rendered, and
		 * (b) this Sprite is on the display list, the render method
		 * will automatically be called.
		 */
		public final function dirty():void
		{
			if (_dirty == DIRTY) return;
			
			if (stage && !_dirty) {	
				if (!__installed) install(stage);
				__dirtyList.push(this);
				stage.invalidate();
			}
			_dirty = DIRTY;
		}
		
		/**
		 * If dirty, this sprite is re-rendered before returning the width.
		 */
		public override function get width():Number
		{
			if (_dirty == DIRTY) { _dirty = VISIT; render(); }
			return super.width;
		}
		
		/**
		 * If dirty, this sprite is re-rendered before returning the height.
		 */
		public override function get height():Number
		{
			if (_dirty == DIRTY) { _dirty = VISIT; render(); }
			return super.height;
		}
		
		/**
		 * If dirty, this sprite is re-rendered before returning the rect.
		 */
		public override function getRect(targetCoordinateSpace:DisplayObject):Rectangle
		{
			if (_dirty == DIRTY) { _dirty = VISIT; render(); }
			return super.getRect(targetCoordinateSpace);
		}
		
		/**
		 * If dirty, this sprite is re-rendered returning the bounds.
		 */
		public override function getBounds(targetCoordinateSpace:DisplayObject):Rectangle
		{
			if (_dirty == DIRTY) { _dirty = VISIT; render(); }
			return super.getBounds(targetCoordinateSpace);
		}
		
		/**
		 * If dirty, either sprite is re-rendered before hit-testing.
		 */
		public override function hitTestObject(obj:DisplayObject):Boolean
		{
			if (_dirty == DIRTY) { _dirty = VISIT; render(); }
			var ds:DirtySprite = obj as DirtySprite;
			if (ds && ds._dirty == DIRTY) { ds._dirty = VISIT; ds.render(); }
			return super.hitTestObject(obj);
		}
		
		/**
		 * If dirty, this sprite is re-rendered before hit-testing.
		 */
		public override function hitTestPoint(x:Number, y:Number, shapeFlag:Boolean=false):Boolean
		{
			if (_dirty == DIRTY) { _dirty = VISIT; render(); }
			return super.hitTestPoint(x, y, shapeFlag);
		}
		
		/**
		 * Draw this sprite's graphical content. Subclasses should
		 * override this method with custom drawing code.
		 */
		public function render():void
		{
			// for sub-classes to override...
		}
		
		/** @inheritDoc */
		public override function toString():String
		{
			var s:String = super.toString();
			return name==null ? s : s + " \""+name+"\"";
		}
		
	} // end of class DirtySprite
}