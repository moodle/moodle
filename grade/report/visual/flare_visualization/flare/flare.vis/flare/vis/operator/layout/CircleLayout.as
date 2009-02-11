package flare.vis.operator.layout
{
	import flare.animate.Transitioner;
	import flare.vis.data.Data;
	import flash.geom.Rectangle;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.NodeSprite;
	
	/**
	 * Layout that places items in a circle. The order in which items are
	 * placed can be determined either by the sort order of the data container
	 * or through a barycentric sorting technique for graph structures. The
	 * barycentric sort attempts to sort items based on their connectivity to
	 * other items; this often results in different graph clusters emerging
	 * along the final sort order.
	 */
	public class CircleLayout extends Layout
	{
		private var _barysort:Boolean = false;
		private var _weight:String = null; // TODO: update this to use a Property instance
		private var _edges:uint = NodeSprite.ALL_LINKS;
		private var _padding:Number = 0.05;
		private var _t:Transitioner;
		
		/** Flag indicating if barycentric sorting using the graph structure
		 *  should be performed. */
		public function get sortByEdges():Boolean { return _barysort; }
		public function set sortByEdges(b:Boolean):void { _barysort = b; }
		
		/**
		 * Creates a new CircleLayout.
		 * @param sortbyEdges Flag indicating if barycentric sorting using
		 *  the graph structure should be performed
		 */
		public function CircleLayout(sortByEdges:Boolean=false) {
			this.sortByEdges = sortByEdges;
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = (t!=null ? t : Transitioner.DEFAULT);
			
			var d:Data = visualization.data;
			var nn:uint = d.nodes.size, i:int = 0;
	        var items:Array = new Array(nn);
	        for (i=0; i<nn; ++i) items[i] = d.nodes[i];
	        
	        // sort by barycenter
	        if (_barysort && d.edges.size > 0) {
		         barysort(items);
			}
			
			// perform the layout
			var r:Rectangle = layoutBounds;
			var cx:Number = (r.x + r.width) / 2;
			var cy:Number = (r.y + r.height) / 2;
			var rx:Number = (0.5 - _padding) * r.width;
			var ry:Number = (0.5 - _padding) * r.height;

			for (i=0; i<items.length; i++) {
				var n:NodeSprite = items[i];
				var angle:Number = i*2*Math.PI / nn;
				_t.$(n).x = Math.cos(angle)*rx + cx;
				_t.$(n).y = Math.sin(angle)*ry + cy;
        	}
			
			updateEdgePoints(_t);
			_t = null;
		}
		
		/**
		 * Sort the items around the circle according to the
		 * barycenters of the individual nodes.
		 */
		private function barysort(items:Array):void
		{
			var niters:uint = 700, i:uint=0, k:uint;
			var inertia:Number = 0;
			var weight:Number;
			var unchanged:Boolean;
			
			// u --> index position
			// v --> barycenter
			for (i=0; i<items.length; ++i) {
				items[i].u = items[i].v = i;
			}
			
			for (i=0; i<niters; ++i) {
				inertia = (i / (niters-1));
				
	        	// sort by barycenters, update each position index
	        	items.sortOn("v", Array.NUMERIC);
	            for (unchanged=(i>0), k=0; k<items.length; ++k) {
	            	if (unchanged && items[k].u != k)
	            		unchanged = false;
	            	items[k].u = k;
	            }
	            if (unchanged) break; // if no difference, we're done
	            
	            // for each node, compute the new barycenter
	            for (k=0; k<items.length; ++k) {
	            	var n:NodeSprite = items[k];
	                weight = inertia;
	                n.v = weight * n.u;
	                
	                n.visitEdges(function(e:EdgeSprite):void
	                {
	                	// retrieve the edge weight
	                	var w:Number = _weight==null ? 1.0 : e.props[_weight];
	                	if (isNaN(w)) w = 1.0;
	                	w = Math.exp(w); // transform the weight
	                	
	                	// add weighted distance to barycenter
	                	n.v += w * e.other(n).u;
	                	weight += w;
	                });
	                
	                // normalize to get final barycenter value
	                n.v /= weight;
	            }
	        }
			items.sortOn("v", Array.NUMERIC);
		}
		
	} // end of class CircleLayout
}