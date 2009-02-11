package flare.vis.data.render
{
	import flare.vis.data.DataSprite;
	import flash.display.Graphics;
	import flare.vis.util.graphics.Shapes;
	import flare.vis.util.graphics.GraphicsUtil;
	import flare.vis.palette.ShapePalette;

	/**
	 * Renderer that draws shapes. The ShapeRender uses a ShapePalette instance
	 * as needed to look up shape drawing routines based on a DataSprite's
	 * shape property.
	 * @see flare.vis.palette.ShapePalette
	 */
	public class ShapeRenderer implements IRenderer
	{
		private static var _instance:ShapeRenderer = new ShapeRenderer();
		/** Static ShapeRenderer instance. */
		public static function get instance():ShapeRenderer { return _instance; }
		
		/** The default size value for drawn shapes. This value is multiplied
		 *  by a DataSprite's size property to determine the final size. */
		public var defaultSize:Number = 6;
		/** The ShapePalette used by this renderer to draw shapes. */
		public var shapePalette:ShapePalette = ShapePalette.defaultPalette();
		
		/** @inheritDoc */
		public function render(d:DataSprite):void
		{
			var lineAlpha:Number = d.lineAlpha;
			var fillAlpha:Number = d.fillAlpha;
			var size:Number = d.size * defaultSize;
			
			var g:Graphics = d.graphics;
			g.clear();
			if (fillAlpha > 0) g.beginFill(d.fillColor, fillAlpha);
			if (lineAlpha > 0) g.lineStyle(d.lineWidth, d.lineColor, lineAlpha);
			
			switch (d.shape) {
				case Shapes.BLOCK:
					g.drawRect(d.u-d.x, d.v-d.y, d.w, d.h);
					break;
				case Shapes.POLYGON:
					if (d.points!=null) GraphicsUtil.drawPolygon(g, d.points);
					break;
				case Shapes.POLYBLOB:
					if (d.points!=null) GraphicsUtil.drawPolygon(g, d.points);
					break;
				case Shapes.VERTICAL_BAR:
					g.drawRect(-size/2, -d.h, size, d.h); 
					break;
				case Shapes.HORIZONTAL_BAR:
					g.drawRect(-d.w, -size/2, d.w, size);
					break;
				case Shapes.WEDGE:
					GraphicsUtil.drawWedge(g, -d.x, -d.y, 
								  d.h, d.v, d.u, d.u+d.w);
					break;
				default:
					shapePalette.getShape(d.shape)(g, size);
			}
			
			if (fillAlpha > 0) g.endFill();
		}
		
	} // end of class ShapeRenderer
}