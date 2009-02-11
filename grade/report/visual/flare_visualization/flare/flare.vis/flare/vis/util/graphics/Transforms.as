package flare.vis.util.graphics
{
	import flare.animate.Transitioner;
	import flash.display.DisplayObject;
	import flash.geom.Point;
	import flash.geom.Matrix;
	
	/**
	 * Utility class providing methods for perfoming camera transformas. These
	 * methods update the transformation matrix of a display object to simulate
	 * pan, zoom, and rotate camera movements. When used on a container, these
	 * operations will correspondingly pan, zoom, and rotate the contents of
	 * the container.
	 */
	public class Transforms
	{
		private static var _point:Point = new Point();
		
		/**
		 * Constructor, throws an error if called, as this is an abstract class.
		 */
		public function Transforms()
		{
			throw new Error("This is an abstract class.");
		}
		
		/**
		 * Performs a pan (translation) on an input matrix.
		 * The result is a transformation matrix including the translation.
		 * @param mat an input transformation matrix
		 * @param dx the change in x position
		 * @param dy the change in y position
		 * @return the resulting, panned transformation matrix
		 */
		public static function panMatrixBy(mat:Matrix, dx:Number, dy:Number):Matrix
		{
			mat.translate(dx, dy);
			return mat;
		}
		
		/**
		 * Performs a zoom about a specific point on an input matrix.
		 * The result is a transformation matrix including the zoom.
		 * @param mat an input transformation matrix
		 * @param scale a scale factor specifying the amount to zoom. A value
		 *  of 2 will zoom in such that objects are twice as large. A value of
		 *  0.5 will zoom out such that objects are half the size.
		 * @param p the point about which to zoom in or out
		 * @return the resulting, zoomed transformation matrix
		 */
		public static function zoomMatrixBy(mat:Matrix, scale:Number, p:Point):Matrix
		{
			mat.translate(-p.x, -p.y);
			mat.scale(scale, scale);
			mat.translate(p.x, p.y);
			return mat;
		}
		
		/**
		 * Performs a rotation around a specific point on an input matrix.
		 * The result is a transformation matrix including the rotation.
		 * @param mat an input transformation matrix
		 * @param angle the rotation angle, in degrees
		 * @param p the point about which to zoom in or out
		 * @return the resulting, rotated transformation matrix
		 */
		public static function rotateMatrixBy(mat:Matrix, angle:Number, p:Point):Matrix
		{
			mat.translate(-p.x, -p.y);
			mat.rotate(angle * Math.PI/180);
			mat.translate(p.x, p.y);
			return mat;
		}
		
		/**
		 * Pan the "camera" by the specified amount.
		 * @param obj the display object to treat as the camera
		 * @param dx the change in x position, in the parent's coordinate space
		 * @param dy the change in y position, in the parent's coordinate space
		 * @param t an optional transitioner for animating the pan
		 */
		public static function panBy(obj:DisplayObject, dx:Number, dy:Number,
			t:Transitioner=null):void
		{
			var mat:Matrix = panMatrixBy(obj.transform.matrix, dx, dy);
			if (t==null) obj.transform.matrix = mat;
			else t.$(obj)["transform.matrix"] = mat;
		}
		
		/**
		 * Zoom the "camera" by the specified scale factor.
		 * @param obj the display object to treat as the camera
		 * @param scale a scale factor specifying the amount to zoom. A value
		 *  of 2 will zoom in such that objects are twice as large. A value of
		 *  0.5 will zoom out such that objects are half the size.
		 * @param xp the x-coordinate around which to zoom, in stage
		 *  coordinates. If this value is <code>NaN</code>, 0 will be used.
		 * @param yp the y-coordinate around which to zoom, in stage
		 *  coordinates. If this value is <code>NaN</code>, 0 will be used.
		 * @param t an optional transitioner for animating the zoom
		 */		
		public static function zoomBy(obj:DisplayObject, scale:Number,
			xp:Number=NaN, yp:Number=NaN, t:Transitioner=null):void
		{
			var p:Point = getLocalPoint(obj, xp, yp);
			var mat:Matrix = zoomMatrixBy(obj.transform.matrix, scale, p);
			if (t==null) obj.transform.matrix = mat;
			else t.$(obj)["transform.matrix"] = mat;
		}

		/**
		 * Rotate the "camera" by the specified amount.
		 * @param obj the display object to treat as the camera
		 * @param angle the amount to rotate, in degrees
		 * @param xp the x-coordinate around which to rotate, in stage
		 *  coordinates. If this value is <code>NaN</code>, 0 will be used.
		 * @param yp the x-coordinate around which to rotate, in stage
		 *  coordinates. If this value is <code>NaN</code>, 0 will be used.
		 * @param t an optional transitioner for animating the rotation
		 */
		public static function rotateBy(obj:DisplayObject, angle:Number,
			xp:Number=NaN, yp:Number=NaN, t:Transitioner=null):void
		{
			var p:Point = getLocalPoint(obj, xp, yp);
			var mat:Matrix = rotateMatrixBy(obj.transform.matrix, angle, p);
			if (t==null) obj.transform.matrix = mat;
			else t.$(obj)["transform.matrix"] = mat;
		}
		
		/**
		 * Helper routine that maps points from stage coordinates to this
		 * camera's parent's coordinate space. If either input value is NaN,
		 * a value of zero is assumed.
		 */
		private static function getLocalPoint(obj:DisplayObject, xp:Number, yp:Number):Point
		{
			var xn:Boolean = isNaN(xp);
			var yn:Boolean = isNaN(yp);
			var p:Point = _point;
			
			if (!(xn && yn)) {
				p.x = xp;
				p.y = yp;
				p = obj.parent.globalToLocal(p);
			}
			if (xn) p.x = 0;
			if (yn) p.y = 0;
			return p;
		}
		
	} // end of class Transforms
}