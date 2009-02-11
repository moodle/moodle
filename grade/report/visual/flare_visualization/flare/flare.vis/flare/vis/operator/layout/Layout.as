package flare.vis.operator.layout
{
	import flare.vis.operator.Operator;
	import flash.geom.Point;
	import flare.vis.data.DataSprite;
	import flash.geom.Rectangle;
	import flare.vis.data.Tree;
	import flare.vis.data.EdgeSprite;
	import flare.animate.Transitioner;
	import flare.vis.axis.Axes;
	import flare.vis.axis.CartesianAxes;
	import flare.vis.Visualization;
	import flare.vis.data.Data;
	import flare.vis.data.NodeSprite;

	/**
	 * Base class for all operators that perform spatial layout. Provides
	 * methods for retrieving the desired layout bounds, providing a layout
	 * anchor point, and returning the layout root (for tree layouts in
	 * particular). This class also provides convenience methods for
	 * manipulating the visibility of axes and performing common updates
	 * to edge control points in graph/tree visualizations.
	 */
	public class Layout extends Operator
	{
		// -- Properties ------------------------------------------------------
		
		private var _bounds:Rectangle = null;
		private var _anchor:Point = new Point(0,0);
		private var _root:DataSprite = null;
		
		/** The layout bounds for the layout. If this value is not explicitly
		 *  set, the bounds for the visualization is returned. */
		public function get layoutBounds():Rectangle {
			if (_bounds != null) return _bounds;
			if (visualization != null) return visualization.bounds;
			return null;
		}
		public function set layoutBounds(b:Rectangle):void { _bounds = b; }
		
		/** The layout anchor, used by some layout instances to place an
		 *  initial item or determine a focal point. */
		public function get layoutAnchor():Point { return _anchor; }
		public function set layoutAnchor(p:Point):void { _anchor = p; }
		
		/** The layout root, the root node for tree layouts. */
		public function get layoutRoot():DataSprite {
			if (_root != null) return _root;
			if (visualization != null) {
				return visualization.data.tree.root;
			}
			return null;
		}
		public function set layoutRoot(r:DataSprite):void { _root = r; }
		
		
		// -- Axis Helpers ----------------------------------------------------
		
		/**
		 * Reveals the axes.
		 * @param t a transitioner to collect value updates
		 * @return the input transitioner
		 */
		public function showAxes(t:Transitioner=null):Transitioner
		{
			var axes:Axes = visualization.axes;
			if (axes == null || axes.visible) return t;
			
			if (t==null || t.immediate) {
				axes.alpha = 1;
				axes.visible = true;
			} else {
				t.$(axes).alpha = 1;
				t.$(axes).visible = true;
			}
			return t;
		}
		
		/**
		 * Hides the axes.
		 * @param t a transitioner to collect value updates
		 * @return the input transitioner
		 */
		public function hideAxes(t:Transitioner=null):Transitioner
		{
			var axes:Axes = visualization.axes;
			if (axes == null || !axes.visible) return t;
			
			if (t==null || t.immediate) {
				axes.alpha = 0;
				axes.visible = false;
			} else {
				t.$(axes).alpha = 0;
				t.$(axes).visible = false;
			}
			return t;
		}
		
		/**
		 * Returns the visualization's axes as a CartesianAxes instance.
		 * Creates/modifies existing axes as needed to ensure the
		 * presence of CartesianAxes.
		 */
		protected function get xyAxes():CartesianAxes
		{
			var vis:Visualization = visualization;
			if (vis == null) return null;
			
			if (vis.xyAxes == null) {
				vis.axes = new CartesianAxes();
			}
			return vis.xyAxes;
		}
		
		// -- Edge Helpers ----------------------------------------------------
		
		/**
		 * Updates all edges to be straight lines. Useful for undoing the
		 * results of layouts that route edges using edge control points.
		 * @param t a transitioner to collect value updates
		 */
		public function updateEdgePoints(t:Transitioner=null):void
		{
			if (t==null || t.immediate) {
				clearEdgePoints();
			} else {
				var clear:Boolean = false;
				
				// set end points to mid-points
				visualization.data.edges.visit(function(e:EdgeSprite):void {
					if (e.points == null) return;
					
					var src:NodeSprite = e.source;
					var trg:NodeSprite = e.target;
					clear = true;
					
					// get target end points
					var x1:Number = t.$(src).x, y1:Number = t.$(src).y;
					var x2:Number = t.$(trg).x, y2:Number = t.$(trg).y;
					
					// create new control points
					var i:uint, len:uint = e.points.length, f:Number;
					var cp:Array = new Array(len);
					
					for (i=0; i<len; i+=2) {
						f = (i+2)/(len+2);
						cp[i]   = x1 + f * (x2 - x1);
						cp[i+1] = y1 + f * (y2 - y1);
					}
					t.$(e).points = cp;
				});
				// after transition, clear out control points
				if (clear) t.onEnd = clearEdgePoints;
			}
		}
		
		/**
		 * Strips all EdgeSprites in a visualization of any control points.
		 */
		public function clearEdgePoints():void
		{
			visualization.data.edges.visit(clearPoints);
		}
		
		/**
		 * Removes any control points from a DataSprite instance.
		 * @param d a DataSprite
		 */
		public function clearPoints(d:DataSprite):void
		{
			d.points = null;
		}
		
	} // end of class Layout
}