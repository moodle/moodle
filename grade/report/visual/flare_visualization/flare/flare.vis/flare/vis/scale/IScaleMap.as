package flare.vis.scale
{
	/**
	 * Interface used by classes which support mapping between
	 * spatial (x,y) coordinates and values in a data scale. For example,
	 * both an axis or legend range should provide this functionality.
	 */
	public interface IScaleMap
	{
		/**
		 * Returns the x-coordinate corresponding to the lower end of the scale.
		 * @return the x-coordinate for the minimum value
		 */
		function get x1():Number;
		
		/**
		 * Returns the y-coordinate corresponding to the lower end of the scale.
		 * @return the y-coordinate for the minimum value
		 */
		function get y1():Number;
		
		/**
		 * Returns the x-coordinate corresponding to the upper end of the scale.
		 * @return the x-coordinate for the maximum value
		 */
		function get x2():Number;
		
		/**
		 * Returns the y-coordinate corresponding to the upper end of the scale.
		 * @return the y-coordinate for the maximum value
		 */
		function get y2():Number;
		
		/**
		 * Returns the scale value corresponding to a given coordinate.
		 * @param x the x-coordinate
		 * @param y the y-coordinate
		 * @param stayInBounds if true, x,y values outside the current layout
		 * bounds will be snapped to the bounds. If false, the value lookup
		 * will attempt to extrapolate beyond the scale bounds. This value
		 * is true be default.
		 * @return the scale value corresponding to the given coordinate.
		 */		
		function value(x:Number, y:Number, stayInBounds:Boolean=true):Object;
        
        /**
         * Returns the x-coordinate corresponding to the given scale value
         * @param val the scale value to lookup
         * @return the x-coordinate at which that scale value is placed
         */        
        function X(val:Object):Number;
        
        /**
         * Returns the y-coordinate corresponding to the given scale value
         * @param val the scale value to lookup
         * @return the y-coordinate at which that scale value is placed
         */
        function Y(val:Object):Number;
        
	} // end of interface IScaleMap
}