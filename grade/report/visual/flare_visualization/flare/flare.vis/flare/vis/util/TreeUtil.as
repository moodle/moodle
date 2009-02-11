package flare.vis.util
{
	import flare.vis.data.Data;
	import flare.vis.data.EdgeSprite;
	import flare.vis.data.NodeSprite;
	import flare.vis.data.Tree;
	import flare.vis.util.heap.FibonacciHeap;
	import flare.vis.util.heap.HeapNode;
	
	/**
	 * Utility class for manipulating tree structures, including spanning tree
	 * generation.
	 */
	public class TreeUtil
	{
		// -- Breadth First Spanning Tree -------------------------------------
		
		/**
		 * Creates a spanning tree over the input data using
		 * breadth-first-search from a root node.
		 * @param n the root node of the tree (must be in the input data)
		 * @param data the data over which to create the spanning tree
		 * @return the breadth-first-search spanning tree
		 */
		public static function breadthFirstTree(n:NodeSprite, data:Data):Tree
		{
			if (n==null) return new Tree();
			
			var t:Tree = new Tree(); t.root = n;
			var q:Array = [n], nn:NodeSprite;
			while (q.length > 0) {
				n = q.shift();
				n.visitEdges(function(e:EdgeSprite):void {
					nn = e.other(n);
					if (t.nodes.contains(nn)) return;
					t.addChildEdge(e);
					q.push(nn);
				}, NodeSprite.GRAPH_LINKS);
			}
			return t;
		}
		
		
		// -- Depth First Spanning Tree ---------------------------------------
		
		/**
		 * Creates a spanning tree over the input data using
		 * depth-first-search from a root node.
		 * @param n the root node of the tree (must be in the input data)
		 * @param data the data over which to create the spanning tree
		 * @return the depth-first-search spanning tree
		 */
		public static function depthFirstTree(n:NodeSprite, d:Data):Tree
		{
			if (n==null) return new Tree();
			
			var t:Tree = new Tree(); t.root = n;
			depthFirstHelper(n, t);
			return t;
		}
		
		private static function depthFirstHelper(n:NodeSprite, t:Tree):void
		{
			n.visitEdges(function(e:EdgeSprite):void {
				var nn:NodeSprite = e.other(n);
				if (t.nodes.contains(nn)) return;
				t.addChildEdge(e);
				if (nn.degree > 1) depthFirstHelper(nn, t);
			}, NodeSprite.GRAPH_LINKS);
		}
		
		
		// -- Minimum Spanning Tree -------------------------------------------
		
		/**
		 * Given an edge-weighting function, returns a corresponding
		 * minimum spanning tree builder function.
		 * @param w an edge-weighting function that returns edge weight values
		 *  for <code>EdgeSprite</code> input arguments
		 * @return method closure that wraps the
		 *  <code>minimumSpanningTree</code> method and calls it using the
		 *  provided edge-weighting function
		 */
		public static function mstBuilder(w:Function):Function
		{
			return function(n:NodeSprite, d:Data):Tree {
				return minimumSpanningTree(n, d, w);
			}
		}
		
		/**
		 * Creates a minimum spanning tree over the input data using
		 * Prim's algorithm.
		 * @param n the root node of the tree (must be in the input data)
		 * @param data the data over which to create the spanning tree
		 * @param w an edge weighting function that returns numeric values
		 *  for input <code>EdgeSprite</code> values. These edge weights are
		 *  used to determine the minimum spanning tree.
		 * @return the minimum spanning tree
		 */
		public static function minimumSpanningTree(n:NodeSprite, d:Data, w:Function):Tree
		{
			if (n==null) return new Tree();
			
			var t:Tree = new Tree(); t.root = n;
			var hn:HeapNode, weight:Number, e:EdgeSprite;
			
			// initialize the heap
			var heap:FibonacciHeap = new FibonacciHeap();
			d.nodes.visit(function(nn:NodeSprite):void {
				nn.props.heapNode = heap.insert(nn, Number.POSITIVE_INFINITY);
			});
			heap.decreaseKey(n.props.heapNode, 0);
			
			// collect spanning tree edges (Prim's algorithm)
			while (!heap.empty) {
				hn = heap.removeMin();
				n = hn.data as NodeSprite;
				// add saved tree edge to spanning tree
				e = n.props.treeEdge as EdgeSprite;
				if (e != null) t.addChildEdge(e);
				
				n.visitEdges(function(e:EdgeSprite):void {
					var nn:NodeSprite = e.other(n);
					var hnn:HeapNode = nn.props.heapNode;
					if (hnn.inHeap && (weight=w(e)) < hnn.key) {
						nn.props.treeEdge = e; // set tree edge
						heap.decreaseKey(hnn, weight);
					}
				}, NodeSprite.GRAPH_LINKS);
			}
			
			// clean-up and return
			d.nodes.visit(function(nn:NodeSprite):void {
				delete nn.props.treeEdge;
				delete nn.props.heapNode;
			});
			return t;
		}
				
	} // end of class TreeUtil
}