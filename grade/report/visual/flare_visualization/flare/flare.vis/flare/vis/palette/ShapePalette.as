package flare.vis.palette
{
	import flare.vis.util.graphics.Shapes;
	
	/**
	 * Palette for shape values that maps integer indices to shape drawing
	 * functions.
	 * @see flare.vis.util.graphics.Shapes
	 */
	public class ShapePalette extends Palette
	{	
		/**
		 * Creates a new, empty ShapePalette.
		 */
		public function ShapePalette() {
			_values = new Array();
		}	
		
		/**
		 * Adds a shape function to this ShapePalette.
		 * @param shapeFunc a shape drawing function, following the format of
		 *  the <code>flare.vis.util.graphics.Shapes</code> class.
		 */
		public function addShape(shapeFunc:Function):void
		{
			_values.push(shapeFunc);
		}
		
		/**
		 * Gets the shape function at the given index into the palette.
		 * @param idx the index of the shape function
		 * @return the requested shape drawing function
		 */
		public function getShape(idx:uint):Function
		{
			return _values[idx % _values.length];
		}
		
		/**
		 * Sets the shape function at the given index into the palette.
		 * @param idx the index of the shape function
		 * @param shapeFunc the shape drawing function to set
		 */
		public function setShape(idx:uint, shapeFunc:Function):void
		{
			_values[idx] = shapeFunc;
		}
		
		/**
		 * Returns a default shape palette instance. The default palette
		 * consists of (in order): circle, square, cross, "x", diamond,
		 * down-triangle, up-triangle, left-triangle, and right-triangle
		 * shape drawing functions.
		 * @return the default shape palette
		 */
		public static function defaultPalette():ShapePalette
		{
			var p:ShapePalette = new ShapePalette();
			p.addShape(Shapes.circle);
			p.addShape(Shapes.square);
			p.addShape(Shapes.cross);
			p.addShape(Shapes.x);
			p.addShape(Shapes.diamond);
			p.addShape(Shapes.triangleDown);
			p.addShape(Shapes.triangleUp);
			p.addShape(Shapes.triangleLeft);
			p.addShape(Shapes.triangleRight);
			return p;
		}
		
	} // end of class ShapePalette
}