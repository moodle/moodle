package flare.vis.axis
{
	import flash.display.Sprite;
	import flare.animate.Transitioner;
	import flare.vis.Visualization;
	import flash.geom.Rectangle;
	
	/**
	 * Base class for representing metric data axes.
	 */
	public class Axes extends Sprite
	{
		/** The visualization the axes correspond to. */
		protected var _vis:Visualization;
		/** The layout bounds of the axes. */
		protected var _bounds:Rectangle;
		
		/** The visualization the axes correspond to. */
		public function get visualization():Visualization { return _vis; }
		public function set visualization(v:Visualization):void { _vis = v; }

		/** The layout bounds of the axes. If this value is not directly set,
		 *  the layout bounds of the visualization are provided. */
		public function get layoutBounds():Rectangle {
			if (_bounds != null) return _bounds;
			if (_vis != null) return _vis.bounds;
			return null;
		}
		public function set layoutBounds(b:Rectangle):void { _bounds = b; }

		/**
		 * Update these axes, performing filtering and layout as needed.
		 * @param trans a Transitioner for collecting value updates
		 * @return the input transitioner
		 */		
		public function update(trans:Transitioner=null):Transitioner
		{
			return trans;
		}
		
	} // end of class Axes
}