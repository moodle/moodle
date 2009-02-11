package flare.vis.util.graphics
{
	import flash.geom.Point;
	
	/**
	 * Utility class providing methods for computational geometry.
	 */
	public final class Geometry
	{		
        /**
         * Computes the co-efficients for a cubic Bezier curve.
         * @param a the starting point of the curve
         * @param b the first control point of the curve
         * @param c the second control point of the curve
         * @param d the ending point of the curve
         * @param c1 point in which to store the zero-order co-efficients
         * @param c2 point in which to store the first-order co-efficients
         * @param c3 point in which to store the second-order co-efficients
         */
        public static function cubicCoeff(a:Point, b:Point, c:Point, d:Point,
        								  c1:Point, c2:Point, c3:Point) : void
        {            
            c3.x = 3 * (b.x - a.x);
            c2.x = 3 * (c.x - b.x) - c3.x;
            c1.x = d.x - a.x - c3.x - c2.x;
            
            c3.y = 3 * (b.y - a.y);
            c2.y = 3 * (c.y - b.y) - c3.y;
            c1.y = d.y - a.y - c3.y - c2.y;
        }
        
        /**
         * Computes a point along a cubic Bezier curve.
         * @param u the interpolation fraction along the curve (between 0 and 1)
         * @param a the starting point of the curve
		 * @param c1 the zero-order Bezier co-efficients
         * @param c2 the first-order Bezier co-efficients
         * @param c3 the second-order Bezier co-efficients
         * @param p point in which to store the calculated point on the curve
         */
        public static function cubic(u:Number, a:Point, 
        							 c1:Point, c2:Point, c3:Point,
        						     p:Point) : Point
        {
            p.x = u*(c3.x + u*(c2.x + u*c1.x)) + a.x;
			p.y = u*(c3.y + u*(c2.y + u*c1.y)) + a.y;
            return p;
        }
        
        /**
         * Computes the co-efficients for a B-spline.
         * @param p the control points of the spline as an array of x,y values
         * @param a an array for storing the x-components of co-efficients
         * @param b an array for storing the y-components of co-efficients
         */
        public static function bsplineCoeff(p:Array, a:Array, b:Array):void
        {
        	a[0] = (-p[0] + 3 * p[2] - 3 * p[4] + p[6]) / 6.0;
        	a[1] = (3 * p[0] - 6 * p[2] + 3 * p[4]) / 6.0;
			a[2] = (-3 * p[0] + 3 * p[4]) / 6.0;
			a[3] = (p[0] + 4 * p[3] + p[4]) / 6.0;
			
			b[0] = (-p[1] + 3 * p[3] - 3 * p[5] + p[7]) / 6.0;
        	b[1] = (3 * p[1] - 6 * p[3] + 3 * p[5]) / 6.0;
			b[2] = (-3 * p[1] + 3 * p[5]) / 6.0;
			b[3] = (p[2] + 4 * p[3] + p[5]) / 6.0;
        }
        
        /**
         * Computes a point along a B-spline.
         * @param u the interpolation fraction along the curve (between 0 and 1)
         * @param a an array of x-components of the B-spline co-efficients
         * @param b an array of y-components of the B-spline co-efficients
         * @param s point in which to store the calculated point on the curve
         */
        public static function bspline(u:Number, a:Array, b:Array, s:Point) : Point
        {
			s.x = u*(a[2] + u*(a[1] + u*a[0])) + a[3];
			s.y = u*(b[2] + u*(b[1] + u*b[0])) + b[3];
			return s;
		}

        /**
         * Computes the convex hull for a set of points.
         * @param p the input points, as a flat array of x,y values
         * @param len the number of points to include in the hull
         * @return the convex hull, as a flat array of x,y values
         */
        public static function convexHull(p:Array, len:uint) : Array
        {
        	// check arguments
            if (len < 3)
            	throw new ArgumentError('Input must have at least 3 points');
            
            var pts:Array = new Array(len-1);
            var stack:Array = new Array(len);
			var i:uint, j:uint, i0:uint = 0;

            // find the starting ref point: leftmost point with the minimum y coord
            for (i = 0; i < len; ++i)
            {
                if (p[i].Y < p[i0].Y) { 
                	i0 = i;
                } else if (p[i].Y == p[i0].Y) {
                	i0 = (p[i].X < p[i0].X ? i : i0);
                }
            }

            // calculate polar angles from ref point and sort
            for (i = 0, j = 0; i < len; ++i) {
                if (i != i0) {
                	pts[j++] = {
                		angle: Math.atan2(p[i].Y-p[i0].Y, p[i].X-p[i0].X),
                		index: i
                	};
                }
            }
            pts.sortOn('angle');

            // toss out duplicated angles
            var angle:Number = pts[0].angle;
            var ti:uint = 0, tj:uint = pts[0].index;
            for (i = 1; i < len-1; i++) {
                j = pts[i].index;
                if (angle == pts[i].angle)
                {
                    // keep angle corresponding to point most distant from reference point
                    var d1:Number = Point.distance(p[i0], p[tj]);
                    var d2:Number = Point.distance(p[i0], p[j]);
                    
                    if (d1 >= d2) {
                        pts[i].index = -1;
                    } else {
                        pts[ti].index = -1;
                        angle = pts[i].angle;
                        ti = i;
                        tj = j;
                    }
                } else {
                    angle = pts[i].angle;
                    ti = i;
                    tj = j;
                }
            }

            // initialize the stack
            var sp:uint = 0;
            stack[sp++] = i0;
            var h:uint = 0;
            for (var k:uint = 0; k < 2; h++) {
                if (pts[h].index != -1) {
                    stack[sp++] = pts[h].index;
                    k++;
                }
            }

            // do graham's scan
            for (; h < len-1; h++)
            {
                if (pts[h].index == -1) continue; // skip tossed out points
                while (isNonLeft(i0, stack[sp-2], stack[sp-1], pts[h].index, p))
                    sp--;
                stack[sp++] = pts[h].index;
            }

            // construct the hull
            var hull:Array = new Array(sp);
            for (i = 0; i < sp; ++i) {
                hull[i] = p[stack[i]];
            }
            return hull;
        }

        private static function isNonLeft(i0:uint, i1:uint, i2:uint, i3:uint, p:Array) : Boolean
        {
            var l1:Number, l2:Number, l4:Number, l5:Number, l6:Number, a:Number, b:Number;

            a = p[i2].y - p[i1].y; b = p[i2].x - p[i1].y; l1 = Math.sqrt(a * a + b * b);
            a = p[i3].y - p[i2].y; b = p[i3].x - p[i2].y; l2 = Math.sqrt(a * a + b * b);
            a = p[i3].y - p[i0].y; b = p[i3].x - p[i0].y; l4 = Math.sqrt(a * a + b * b);
            a = p[i1].y - p[i0].y; b = p[i1].x - p[i0].y; l5 = Math.sqrt(a * a + b * b);
            a = p[i2].y - p[i0].y; b = p[i2].x - p[i0].y; l6 = Math.sqrt(a * a + b * b);
            
            a = Math.acos((l2*l2 + l6*l6 - l4*l4) / (2*l2*l6));
            b = Math.acos((l6*l6 + l1*l1 - l5*l5) / (2*l6*l1));

            return (Math.PI - a - b < 0);
        }
		
	} // end of class Geometry
}