package flare.vis.util.graphics
{
	import flash.display.Graphics;
	
	/**
	 * Utility class defining shape types and shape drawing routines. All shape
	 * drawing functions take two arguments: a <code>Graphics</code> context
	 * to draw with and a size parameter determining the radius of the shape
	 * (i.e., the height and width of the shape are twice the size parameter).
	 */
	public class Shapes
	{
		/** Constant indicating a straight line shape. */
		public static const LINE:uint = 0;
		/** Constant indicating a Bezier curve. */
		public static const BEZIER:uint = 1;
		/** Constant indicating a cardinal spline. */
		public static const CARDINAL:uint = 2;
		
		/** Constant indicating a rectangular block shape. */
		public static const BLOCK:int = -1;
		/** Constant indicating a polygon shape. */
		public static const POLYGON:int = -2;
		/** Constant indicating a "polyblob" shape, a polygon whose
		 *  edges are interpolated with a cardinal spline. */
		public static const POLYBLOB:int = -3;
		/** Constant indicating a vertical bar shape. */
		public static const VERTICAL_BAR:int = -4;
		/** Constant indicating a horizontal bar shape. */
		public static const HORIZONTAL_BAR:int = -5;
		/** Constant indicating a wedge shape. */
		public static const WEDGE:int = -6;
		
		/**
		 * Draws a circle shape.
		 * @param g the graphics context to draw with
		 * @param size the radius of the circle
		 */
		public static function circle(g:Graphics, size:Number):void
		{
			g.drawCircle(0, 0, size);
		}
		
		/**
		 * Draws a square shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the square. The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function square(g:Graphics, size:Number):void
		{
			g.drawRect(-size, -size, 2*size, 2*size);
		}
		
		/**
		 * Draws a cross shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the cross. The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function cross(g:Graphics, size:Number):void
		{
			g.moveTo(0, -size);
			g.lineTo(0, size);
			g.moveTo(-size, 0);
			g.lineTo(size, 0);
		}
		
		/**
		 * Draws an "x" shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the "x". The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function x(g:Graphics, size:Number):void
		{
			g.moveTo(-size, -size);
			g.lineTo(size, size);
			g.moveTo(size, -size);
			g.lineTo(-size, size);
		}
		
		/**
		 * Draws a diamond shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the diamond. The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function diamond(g:Graphics, size:Number):void
		{
			g.moveTo(0, size);
			g.lineTo(-size, 0);
			g.lineTo(0, -size);
			g.lineTo(size, 0);
			g.lineTo(0, size);	
		}
		
		/**
		 * Draws an upward-pointing triangle shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the triangle. The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function triangleUp(g:Graphics, size:Number):void
		{
			g.moveTo(-size, size);
			g.lineTo(size, size);
			g.lineTo(0, -size);
			g.lineTo(-size, size);
		}
		
		/**
		 * Draws a downward-pointing triangle shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the triangle. The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function triangleDown(g:Graphics, size:Number):void
		{
			g.moveTo(-size, -size);
			g.lineTo(size, -size);
			g.lineTo(0, size);
			g.lineTo(-size, -size);
		}
		
		/**
		 * Draws a right-pointing triangle shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the triangle. The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function triangleRight(g:Graphics, size:Number):void
		{
			g.moveTo(-size, -size);
			g.lineTo(size, 0);
			g.lineTo(-size, size);
			g.lineTo(-size, -size);
		}
		
		/**
		 * Draws a left-pointing triangle shape.
		 * @param g the graphics context to draw with
		 * @param size the (half-)size of the triangle. The height and width of
		 *  the shape will both be exactly twice the size parameter.
		 */
		public static function triangleLeft(g:Graphics, size:Number):void
		{
			g.moveTo(size, -size);
			g.lineTo(-size, 0);
			g.lineTo(size, size);
			g.lineTo(size, -size);
		}
		
	} // end of class Shapes
}