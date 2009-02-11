package flare.util
{
	import flare.vis.data.NodeSprite;
	import flare.vis.data.Data;
	import flare.vis.data.Tree;
	
	public class GraphUtil
	{
		public static function printTree(n:NodeSprite, d:int) : void
		{
			/*
			var s:String = "";
			for (var k:uint = 0; k < d; k++) {
				s += "  ";
			}
			trace(s+n.name+" ("+n.x+", "+n.y+", "+n.w+", "+n.h+")");
			*/
			trace(n.name+"\t"+n.u+"\t"+n.v+"\t"+n.w+"\t"+n.h);
			for (var i:uint = 0; i < n.childDegree; ++i) {
				printTree(n.getChildNode(i), d+1);
			}
		}
	
		// -- Graph Generators ------------------------------------------------
		
		/**
	     * Builds a completely unconnected (edge-free) graph with the given 
	     * number of nodes
	     * @param n the number of nodes
	     * @return a graph with n nodes and no edges
	     */
	    public static function nodes(n:uint):Data
	    {
	        var g:Data = new Data();
	        for (var i:uint=0; i < n; i++) {
	            var node:NodeSprite = g.addNode();
	            node.data.label = String(i);
	        }
	        return g;
	    }
	    
	    /**
	     * Builds a "star" graph with one central hub connected to the given
	     * number of satellite nodes.
	     * @param n the number of points of the star
	     * @return a "star" graph with n points, for a total of n+1 nodes
	     */
	    public static function star(n:uint):Data
	    {
	        var g:Data = new Data();
	        
	        var r:NodeSprite = g.addNode();
	        r.data.label = "0";
	        
	        for (var i:uint=1; i <= n; ++i) {
	            var nn:NodeSprite = g.addNode();
	            nn.data.label = String(i);
	            g.addEdgeFor(r, nn);
	        }
	        return g;
	    }
	    
	    /**
	     * Returns a clique of given size. A clique is a graph in which every node
	     * is a neighbor of every other node.
	     * @param n the number of nodes in the graph
	     * @return a clique of size n
	     */
	    public static function clique(n:uint):Data
	    {
	        var g:Data = new Data();
	        var i:uint, j:uint;
	        
	        var nodes:Array = new Array(n);
	        for (i=0; i < n; ++i) {
	            nodes[i] = g.addNode();
	            nodes[i].data.label = String(i);
	        }
	        for (i=0; i < n; ++i) {
	            for (j=i; j < n; ++j)
	                if (i != j)
	                    g.addEdgeFor(nodes[i], nodes[j]);
	        }
	        return g;
	    }
	    
	    /**
	     * Returns a graph structured as an m-by-n grid.
	     * @param m the number of rows of the grid
	     * @param n the number of columns of the grid
	     * @return an m-by-n grid structured graph
	     */
	    public static function grid(m:uint, n:uint):Data
	    {
	        var g:Data = new Data();
	        
	        var nodes:Array = new Array(m*n);
	        for (var i:uint=0; i < m*n; ++i) {
	            nodes[i] = g.addNode();
	            nodes[i].data.label = String(i);
	            
	            if (i >= n)
	                g.addEdgeFor(nodes[i-n], nodes[i]);
	            if (i % n != 0)
	                g.addEdgeFor(nodes[i-1], nodes[i]);
	        }
	        return g;
	    }
	    
	    public static function honeycomb(levels:uint):Data
	    {
	        var g:Data = new Data();
	        var layer1:Array = halfcomb(g, levels);
	        var layer2:Array = halfcomb(g, levels);
	        for (var i:uint=0; i<(levels<<1); ++i) {
	            var n1:NodeSprite = layer1[i];
	            var n2:NodeSprite = layer2[i];
	            g.addEdgeFor(n1, n2);
	        }
	        return g;
	    }
	    
	    private static function halfcomb(g:Data, levels:uint):Array
	    {
	    	var top:Array = new Array();
	    	var layer:Array = new Array();
	        var label:uint = 0, i:uint, j:uint;
	        
	        for (i=0; i<levels; ++i) {
	            var n:NodeSprite = g.addNode();
	            n.data.label = String(label++);
	            top.push(n);
	        }
	        for (i=0; i<levels; ++i) {
	            n = null;
	            for (j=0; j<top.length; ++j) {
	                var p:NodeSprite = top[j];
	                if (n == null) {
	                    n = g.addNode();
	                    n.data.label = String(label++);
	                    layer.push(n);
	                }
	                g.addEdgeFor(p, n);
	                n = g.addNode();
	                n.data.label = String(label++);
	                layer.push(n);
	                g.addEdgeFor(p, n);
	            }
	            if (i == levels-1) {
	                return layer;
	            }
	            top.splice(0, top.length);
	            for (j=0; j<layer.length; ++j) {
	                p = layer[j];
	                n = g.addNode();
	                n.data.label = String(label++);
	                top.push(n);
	                g.addEdgeFor(p, n);
	            }
	            layer.splice(0, layer.length);
	        }
	        // should never happen
	        return top;
	    }
	    
	    /**
	     * Returns a balanced tree of the requested breadth and depth.
	     * @param breadth the breadth of each level of the tree
	     * @param depth the depth of the tree
	     * @return a balanced tree
	     */
	    public static function balancedTree(breadth:uint, depth:uint):Tree
	    {
	    	var t:Tree = new Tree();
	        var r:NodeSprite = t.addRoot();
	        r.data.label = "0,0";
	        
	        if (depth > 0)
	            balancedHelper(t, r, breadth, depth-1);
	        return t;
	    }
	    
	    private static function balancedHelper(t:Tree, n:NodeSprite, 
	            breadth:uint, depth:uint):void
	    {
	        for (var i:uint=0; i<breadth; ++i) {
	            var c:NodeSprite = t.addChild(n);
	            c.data.label = i+","+c.depth;
	            if (depth > 0)
	                balancedHelper(t,c,breadth,depth-1);
	        }
	    }
	    
	    /**
	     * Returns a left deep binary tree
	     * @param depth the depth of the tree
	     * @return the generated tree
	     */
	    public static function leftDeepTree(depth:uint):Tree
	    {
	        var t:Tree = new Tree();
			var r:NodeSprite = t.addRoot();
	        r.data.label = "0,0";
	        
	        deepHelper(t, r, 2, depth, true);
	        return t;
	    }
	    
	    /**
	     * Returns a right deep binary tree
	     * @param depth the depth of the tree
	     * @return the generated Tree
	     */
	    public static function rightDeepTree(depth:uint):Tree
	    {
	        var t:Tree = new Tree();
			var r:NodeSprite = t.addRoot();
	        r.data.label = "0,0";
	        
	        deepHelper(t, r, 2, depth, false);
	        return t;
	    }
		
		/**
    	 * Create a diamond tree, with a given branching factor at
    	 * each level, and depth levels for the two main branches.
    	 * @param b the number of children of each branch node
    	 * @param d1 the length of the first (left) branch
    	 * @param d2 the length of the second (right) branch
    	 * @return the generated Tree
    	 */
		public static function diamondTree(b:int, d1:int, d2:int) : Tree
		{
			var tree:Tree = new Tree();
			var n:NodeSprite = tree.addRoot();
			var l:NodeSprite = tree.addChild(n);
			var r:NodeSprite = tree.addChild(n);
            
            deepHelper(tree, l, b, d1-2, true);
        	deepHelper(tree, r, b, d1-2, false);
        
			while (l.firstChildNode != null)
				l = l.firstChildNode;
			while (r.lastChildNode != null)
				r = r.lastChildNode;
        	
        	deepHelper(tree, l, b, d2-1, false);
        	deepHelper(tree, r, b, d2-1, true);
        
        	return tree;
  		}
    
		private static function deepHelper(t:Tree, n:NodeSprite,
			breadth:int, depth:int, left:Boolean) : void
		{
			var c:NodeSprite = t.addChild(n);
			if (left && depth > 0)
				deepHelper(t, c, breadth, depth-1, left);
			
			for (var i:uint = 1; i<breadth; ++i) {
				c = t.addChild(n);
			}
			
			if (!left && depth > 0)
				deepHelper(t, c, breadth, depth-1, left);
		}
	}
}