package flare.vis.util.graphics
{
	import flash.display.Graphics;
	import flash.geom.Point;
	
	/**
	 * Utility class providing graphics drawing routines.
	 */
	public class GraphicsUtil
	{
		// temp variables
		private static var _p1:Point = new Point();
		private static var _p2:Point = new Point();
		private static var _p3:Point = new Point();
		private static var _p4:Point = new Point();
		private static var _c1:Point = new Point();
		private static var _c2:Point = new Point();
		private static var _c3:Point = new Point();
		
		/**
		 * Draws an arc (a segment of a circle's circumference)
		 * @param g the graphics context to draw with
		 * @param x the center x-coordinate of the arc
		 * @param y the center y-coorindate of the arc
		 * @param radius the radius of the arc
		 * @param a0 the starting angle of the arc (in radians)
		 * @param a1 the ending angle of the arc (in radians)
		 */
		public static function drawArc(g:Graphics, x:Number, y:Number, 
									radius:Number, a0:Number, a1:Number) : void
		{
			var slices:Number = (Math.abs(a1-a0) * radius) / 4;
			var a:Number, cx:Number = x, cy:Number = y;
			
			for (var i:uint = 0; i <= slices; ++i) {
				a = a0 + i*(a1-a0)/slices;
				x = cx + radius * Math.cos(a);
				y = cy + -radius * Math.sin(a);
				if (i==0) {
					g.moveTo(x, y);
				} else {
					g.lineTo(x,y);
				}
			}
		}
		
		/**
		 * Draws a wedge defined by an angular range and inner and outer radii.
		 * An inner radius of zero results in a pie-slice shape.
		 * @param g the graphics context to draw with
		 * @param x the center x-coordinate of the wedge
		 * @param y the center y-coorindate of the wedge
		 * @param outer the outer radius of the wedge
		 * @param inner the inner radius of the wedge
		 * @param a0 the starting angle of the wedge (in radians)
		 * @param a1 the ending angle of the wedge (in radians)
		 */
		public static function drawWedge(g:Graphics, x:Number, y:Number, 
			outer:Number, inner:Number, a0:Number, a1:Number) : void
		{
			var a:Number = Math.abs(a1-a0);
			var slices:int = Math.max(4, int(a * outer / 6));
			var cx:Number = x, cy:Number = y, x0:Number, y0:Number;
			var circle:Boolean = (a >= 2*Math.PI);

			
			if (slices <= 0) return;
		
			// pick starting point
			if (inner <= 0 && !circle) {
				g.moveTo(cx, cy);
			} else {
				x0 = cx + outer * Math.cos(a0);
				y0 = cy + -outer * Math.sin(a0);
				g.moveTo(x0, y0);
			}
			
			// draw outer arc
			for (var i:uint = 0; i <= slices; ++i) {
				a = a0 + i*(a1-a0)/slices;
				x = cx + outer * Math.cos(a);
				y = cy + -outer * Math.sin(a);
				g.lineTo(x,y);
			}

			if (circle) {
				// return to starting point
				g.lineTo(x0, y0);
			} else if (inner > 0) {
				// draw inner arc
				for (i = slices+1; --i >= 0;) {
					a = a0 + i*(a1-a0)/slices;
					x = cx + inner * Math.cos(a);
					y = cy + -inner * Math.sin(a);
					g.lineTo(x,y);
				}
				g.lineTo(x0, y0);
			} else {
				// return to center
				g.lineTo(cx, cy);
			}
		}
		
		/**
		 * Draws a polygon shape.
		 * @param g the graphics context to draw with
		 * @param a a flat array of x, y values defining the polygon
		 */
		public static function drawPolygon(g:Graphics, a:Array) : void
		{
			g.moveTo(a[0], a[1]);
			for (var i:uint=2; i<a.length; i+=2) {
				g.lineTo(a[i], a[i+1]);
			}
			g.lineTo(a[0], a[1]);
		}
		
		/**
		 * Draws a cubic Bezier curve.
		 * @param g the graphics context to draw with
		 * @param ax x-coordinate of the starting point
		 * @param ay y-coordinate of the starting point
		 * @param bx x-coordinate of the first control point
		 * @param by y-coordinate of the first control point
		 * @param cx x-coordinate of the second control point
		 * @param cy y-coordinate of the second control point
		 * @param dx x-coordinate of the ending point
		 * @param dy y-coordinate of the ending point
		 * @param move if true (the default), the graphics context will be
		 *  moved to the starting point before drawing starts. If false,
		 *  no move command will be issued; this is useful when connecting
		 *  multiple curves to define a filled region.
		 */
		public static function drawCubic(g:Graphics, ax:Number, ay:Number,
			bx:Number, by:Number, cx:Number, cy:Number, dx:Number, dy:Number,
			move:Boolean=true) : void
		{			
			var subdiv:Number, u:Number, xx:Number, yy:Number
			_p1.x = ax; _p1.y = ay;
			_p2.x = bx; _p2.y = by;
			_p3.x = cx; _p3.y = cy;
			_p4.x = dx; _p4.y = dy;
			
			// determine number of line segments
			subdiv = Math.sqrt((xx=(bx-ax))*xx + (yy=(by-ay))*yy) +
					 Math.sqrt((xx=(cx-bx))*xx + (yy=(cy-by))*yy) +
					 Math.sqrt((xx=(dx-cx))*xx + (yy=(dy-cy))*yy);
			subdiv = Math.floor(subdiv/4);
			
			Geometry.cubicCoeff(_p1, _p2, _p3, _p4, _c1, _c2, _c3);
			
			if (move) g.moveTo(ax, ay);
			for (var i:uint=0; i<=subdiv; ++i) {
				u = i/subdiv;
				Geometry.cubic(u, _p1, _c1, _c2, _c3, _p2);
				g.lineTo(_p2.x, _p2.y);
			}
		}
		
		/**
		 * Draws a cardinal spline composed of piecewise connected cubic
		 * Bezier curves. Curve control points are inferred so as to ensure
		 * C1 continuity (continuous derivative).
		 * @param g the graphics context to draw with
		 * @param p an array defining a polygon or polyline to render with a
		 *  cardinal spline
		 * @param s a tension parameter determining the spline's "tightness"
		 * @param closed indicates if the cardinal spline should be a closed
		 *  shapes. False by default.
		 */
		public static function drawCardinal(g:Graphics, p:Array, s:Number=0.15, closed:Boolean=false) : void
		{
			// compute the size of the path
	        var len:uint = p.length;
	        
	        if (len < 2)
	            throw new Error("Cardinal splines require at least 3 points");
	        
	        var dx1:Number, dy1:Number, dx2:Number, dy2:Number;
	        g.moveTo(p[0], p[1]);
	        
	        // compute first control points
	        if (closed) {
	            dx1 = p[2]-p[len-2];
	            dy1 = p[3]-p[len-1];
	        } else {
	            dx1 = p[4]-p[0]
	            dy1 = p[5]-p[1];
	        }

	        // iterate through control points
	        var i:uint = 0;
	        for (i=2; i<len-2; i+=2) {
	            dx1 = dx2; dy1 = dy2;
	            dx2 = p[i+2] - p[i-2];
	            dy2 = p[i+3] - p[i-1];
	            
	            drawCubic(g, p[i-2],    p[i-1],
						     p[i-2]+s*dx1, p[i-1]+s*dy1,
	                         p[i]  -s*dx2, p[i+1]-s*dy2,
	                         p[i],         p[i+1]);
	        }
	        
	        // finish spline
	        if (closed) {
	            dx1 = dx2; dy1 = dy2;
	            dx2 = p[0] - p[i-2];
	            dy2 = p[1] - p[i-1];
	            drawCubic(g, p[i-2], p[i-1], p[i-2]+s*dx1, p[i-1]+s*dy1,
	            			 p[i]-s*dx2, p[i+1]-s*dy2, p[i], p[i+1], false);
	            
	            dx1 = dx2; dy1 = dy2;
	            dx2 = p[2] - p[len-2];
	            dy2 = p[3] - p[len-1];
	            drawCubic(g, p[len-2], p[len-1], p[len-2]+s*dx1, p[len-1]+s*dy1,
	            	p[0]-s*dx2, p[1]-s*dy2, p[0], p[1], false);
	        } else {
	        	drawCubic(g, p[i-2], p[i-1], p[i-2]+s*dx1, p[i-1]+s*dy1,
	        		p[i]-s*dx2, p[i+1]-s*dy2, p[i], p[i+1], false);
	        }
		}
		
		/**
		 * Draws a cardinal spline composed of piecewise connected cubic
		 * Bezier curves. Curve control points are inferred so as to ensure
		 * C1 continuity (continuous derivative).
		 * @param g the graphics context to draw with
		 * @param ax the x-coordinate of the starting point
		 * @param ay the y-coordinate of the starting point
		 * @param p an array defining a polygon or polyline to render with a
		 *  cardinal spline, excluding starting and ending points
		 * @param bx the x-coordinate of the ending point
		 * @param by the y-coordinate of the ending point
		 * @param s a tension parameter determining the spline's "tightness"
		 * @param closed indicates if the cardinal spline should be a closed
		 *  shapes. False by default.
		 */
		public static function drawCardinal2(g:Graphics, ax:Number, ay:Number,
			pts:Array, bx:Number, by:Number, s:Number=0.15, closed:Boolean=false) : void
		{
			// compute the size of the path
	        var len:uint = pts.length;
	        
	        if (len < 2)
	            throw new Error("Cardinal splines require at least 3 points");
	        
	        var dx1:Number, dy1:Number, dx2:Number, dy2:Number;
	        g.moveTo(ax, ay);
	        
	        // compute first control points
	        if (closed) {
	            dx1 = pts[0]-bx;
	            dy1 = pts[1]-by;
	        } else {
	            dx1 = pts[2]-ax;
	            dy1 = pts[3]-ay;
	        }
	        
	        // draw first curve
	        dx2 = pts[2] - ax;
	        dy2 = pts[3] - ay;
	        drawCubic(g, ax, ay, ax+s*dx1, ay+s*dy1, pts[0]-s*dx2, pts[1]-s*dy2, pts[0], pts[1], false);

	        // iterate through control points
	        var i:uint = 0;
	        for ( i=2; i<len-2; i+=2 ) {
	            dx1 = dx2; dy1 = dy2;
	            dx2 = pts[i+2]-pts[i-2];
	            dy2 = pts[i+3]-pts[i-1];
	            
	            drawCubic(g, pts[i-2],    pts[i-1],
						  pts[i-2]+s*dx1, pts[i-1]+s*dy1,
	                      pts[i]  -s*dx2, pts[i+1]-s*dy2,
	                      pts[i],         pts[i+1]);
	        }
	        
	        // draw second-to-last curve
	        dx1 = dx2; dy1 = dy2;
	        dx2 = bx - pts[len-4];
	        dy2 = by - pts[len-3];
	        drawCubic(g, pts[len-4], pts[len-3], pts[len-4]+s*dx1, pts[len-3]+s*dy1,
	        			 pts[len-2]-s*dx2, pts[len-1]-s*dy2, pts[len-2], pts[len-1], false);
	        
	        // finish spline
	        if (closed) {
	            dx1 = dx2; dy1 = dy2;
	            dx2 = ax - pts[len-2];
	            dy2 = ay - pts[len-1];
	            drawCubic(g, pts[len-2], pts[len-1], pts[len-2]+s*dx1, pts[len-1]+s*dy1,
	            			 bx-s*dx2, by-s*dy2, bx, by, false);
	            
	            dx1 = dx2; dy1 = dy2;
	            dx2 = pts[0]-bx;
	            dy2 = pts[1]-by;
	            drawCubic(g, bx, by, bx+s*dx1, by+s*dy1, ax-s*dx2, ay-s*dy2, ax, ay, false);
	        } else {
	        	drawCubic(g, pts[len-2], pts[len-1], pts[len-2]+s*dx1, pts[len-1]+s*dy1,
	        				 bx-s*dx2, by-s*dy2, bx, by, false);
	        }
		}
		
	} // end of class GraphicsUtil
}