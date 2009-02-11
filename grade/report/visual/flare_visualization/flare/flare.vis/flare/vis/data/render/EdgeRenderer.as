package flare.vis.data.render
{
	import flare.vis.data.DataSprite;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.NodeSprite;
	import flare.vis.util.graphics.GraphicsUtil;
	import flare.vis.util.graphics.Shapes;
	
	import flash.display.Graphics;

	/**
	 * Renderer that draws edges as lines. The EdgeRenderer supports straight
	 * lines, poly lines, and curves as Bezier or cardinal splines. The type
	 * of edge drawn is determined from an EdgeSprite's <code>shape</code>
	 * property and control points found in the <code>points</code> property.
	 * The line rendering properties are set by the <code>setLineStyle</code>
	 * method, which can be overridden by subclasses. By default, the line
	 * rendering settings are determined using default property values set
	 * on this class (for example, the <code>scaleMode<code> and 
	 * <code>caps</code> properties).
	 */
	public class EdgeRenderer implements IRenderer
	{
		private static var _instance:EdgeRenderer = new EdgeRenderer();
		/** Static EdgeRenderer instance. */
		public static function get instance():EdgeRenderer { return _instance; }
		
		/** Pixel hinting flag for line rendering. */
		public var pixelHinting:Boolean = false;
		/** Scale mode for line rendering (normal by default). */
		public var scaleMode:String = "normal";
		/** The joint style for line rendering. */
		public var joints:String = null;
		/** The caps style for line rendering. */
		public var caps:String = null;
		/** The miter limit for line rendering. */
		public var miterLimit:int = 3;
		
		/** @inheritDoc */
		public function render(d:DataSprite):void
		{
			var e:EdgeSprite = d as EdgeSprite;
			if (e == null) { return; } // TODO: throw exception?
			var s:NodeSprite = e.source;
			var t:NodeSprite = e.target;
			var g:Graphics = e.graphics;
			
			if (s==null || t==null) { g.clear(); return; }
			
			var ctrls:Array = e.points as Array;
				
			g.clear(); // clear it out
			setLineStyle(e, g); // set the line style
			
			if (e.shape == Shapes.BEZIER && ctrls != null && ctrls.length > 1) {
				if (ctrls.length < 4)
				{
					g.moveTo(e.x1, e.y1);
					g.curveTo(ctrls[0], ctrls[1], e.x2, e.y2);
				}
				else
				{
					GraphicsUtil.drawCubic(g, e.x1, e.y1, ctrls[0], ctrls[1],
										   ctrls[2], ctrls[3], e.x2, e.y2);
				}
			} else if (e.shape == Shapes.CARDINAL) {
				GraphicsUtil.drawCardinal2(g, e.x1, e.y1, ctrls, e.x2, e.y2);
			} else {
				g.moveTo(e.x1, e.y1);
				if (ctrls != null) {
					for (var i:uint=0; i<ctrls.length; i+=2)
						g.lineTo(ctrls[i], ctrls[i+1]);
				}
				g.lineTo(e.x2, e.y2);
			}
		}
		
		/**
		 * Sets the line style for edge rendering.
		 * @param e the EdgeSprite to render
		 * @param g the Graphics context to draw with
		 */
		protected function setLineStyle(e:EdgeSprite, g:Graphics):void
		{
			var lineAlpha:Number = e.lineAlpha;
			if (lineAlpha == 0) return;
			
			g.lineStyle(e.lineWidth, e.lineColor, lineAlpha, 
				pixelHinting, scaleMode, caps, joints, miterLimit);
		}

	} // end of class EdgeRenderer
}