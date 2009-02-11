package flare.vis.data
{
	import flash.events.Event;
	import flare.vis.data.render.EdgeRenderer;
	
	/**
	 * Visually represents a connection between two data elements. Examples
	 * include an edge in a graph structure or a line between points in a line
	 * chart. EdgeSprites maintain <code>source</code> and <code>target</code>
	 * properties for accessing the NodeSprites connected by this edge. By
	 * default, EdgeSprites are drawn using an <code>EdgeRenderer</code>.
	 * EdgeSprites are typically managed by a <code>Data</code> object.
	 */
	public class EdgeSprite extends DataSprite
	{
		// -- Properties ------------------------------------------------------

		private var _source:NodeSprite;
		private var _target:NodeSprite;
		private var _directed:Boolean = false;
		
		private var _x1:Number;
		private var _y1:Number;
		private var _x2:Number;
		private var _y2:Number;
		
		/** The x-coordinate for the first end point of this edge. */
		public function get x1():Number { return _x1; }
		public function set x1(x:Number):void { _x1 = x; }
		/** The y-coordinate for the first end point of this edge. */
		public function get y1():Number { return _y1; }
		public function set y1(y:Number):void { _y1 = y; }
		/** The x-coordinate for the second end point of this edge. */
		public function get x2():Number { return _x2; }
		public function set x2(x:Number):void { _x2 = x; }
		/** The y-coordinate for the second end point of this edge. */
		public function get y2():Number { return _y2; }
		public function set y2(y:Number):void { _y2 = y; }
		
		/** The first, or source, node upon which this edge is incident. */
		public function get source():NodeSprite { return _source; }
		public function set source(n:NodeSprite):void { _source = n; }
		
		/** The second, or target, node upon which this edge is incident. */
		public function get target():NodeSprite { return _target; }
		public function set target(n:NodeSprite):void { _target = n; }
		
		/** Flag indicating if this edge is directed (true) or undirected
		 *  (false). */
		public function get directed():Boolean { return _directed; }
		public function set directed(b:Boolean):void { _directed = b; }
		
		
		// -- Methods ---------------------------------------------------------
		
		/**
		 * Creates a new EdgeSprite.
		 * @param source the source node
		 * @param target the target node
		 * @param directed true for a directed edge, false for undirected
		 */		
		public function EdgeSprite(source:NodeSprite=null,
			target:NodeSprite=null, directed:Boolean=false)
		{
			_source = source;
			_target = target;
			_directed = directed;
			_lineColor = 0xffcccccc;
			_renderer = EdgeRenderer.instance;
			render();
		}
		
		/**
		 * Given a node upon which this edge is incident, return the other
		 * node connected by this edge.
		 * @param n a node upon which this edge is incident
		 * @return the other node
		 */		
		public function other(n:NodeSprite):NodeSprite
		{
			if (n == _source) return _target;
			if (n == _target) return _source;
			else return null;	
		}
		
		/**
		 * Clears the edge, removing references to the edge's nodes.
		 */		
		public function clear():void
		{
			_source = null;
			_target = null;
		}
		
		/** @inheritDoc */
		public override function render():void
		{
			if (_source != null) {
				_x1 = _source.x;
				_y1 = _source.y;
			}
			if (_target != null) {
				_x2 = _target.x;
				_y2 = _target.y;
			}
			super.render();
		}
		
	} // end of class EdgeSprite
}