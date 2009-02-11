package flare.vis.operator.layout
{
	import flare.vis.data.NodeSprite;
	import flare.animate.Transitioner;
	import flash.geom.Point;
	import flare.util.Arrays;
	import flare.vis.data.EdgeSprite;
	
	/**
	 * Layout that places tree nodes in an indented outline layout.
	 */
	public class IndentedTreeLayout extends Layout
	{		
		private var _bspace:Number = 5;  // the spacing between sibling nodes
    	private var _dspace:Number = 50; // the spacing between depth levels
    	private var _depths:Array = new Array(20); // TODO make sure array regrows as needed
    	private var _maxDepth:int = 0;
    	private var _ax:Number, _ay:Number; // for holding anchor co-ordinates
		private var _t:Transitioner; // temp variable for transitioner access
		
		/** The spacing to use between depth levels (the amount of indent). */
		public function get depthSpacing():Number { return _dspace; }
		public function set depthSpacing(s:Number):void { _dspace = s; }
		
		/** The spacing to use between rows in the layout. */
		public function get breadthSpacing():Number { return _bspace; }
		public function set breadthSpacing(s:Number):void { _bspace = s; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new IndentedTreeLayout.
		 * @param depthSpace the amount of indent between depth levels
		 * @param breadthSpace the amount of spacing between rows
		 */		
		public function IndentedTreeLayout(depthSpace:Number=50,
										   breadthSpace:Number=5)
		{
			_bspace = breadthSpace;
			_dspace = depthSpace;
		}
		
		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = t!=null ? t : Transitioner.DEFAULT; // set transitioner
			
        	Arrays.fill(_depths, 0);
        	_maxDepth = 0;
        
        	var a:Point = layoutAnchor;
        	_ax = a.x; _ay = a.y;
        
        	var root:NodeSprite = layoutRoot as NodeSprite;
        	if (root == null) return; // TODO: throw exception?
        	
        	layoutNode(root,0,0,true);      

        	_t = null; // clear transitioner reference
    	}
    	
    	
    	private function layoutNode(node:NodeSprite, height:Number, indent:uint, visible:Boolean):Number
    	{
    		var x:Number = _ax + indent * _dspace;
    		var y:Number = _ay + height;
    		var o:Object = _t.$(node);
    		
    		// update node
    		o.x = x;
    		o.y = y;
    		o.alpha = visible ? 1.0 : 0.0;
    		
    		// update edge
    		if (node.parentEdge != null) 
    		{
    			var e:EdgeSprite = node.parentEdge;
    			var p:NodeSprite = node.parentNode;
    			o = _t.$(e); 
    			o.alpha = visible ? 1.0 : 0.0;
    			if (e.points == null) {
					e.points = [(p.x+node.x)/2, (p.y+node.y)/2];
    			}
    			o.points = [_t.getValue(p,"x"), y];
    		}
    		
    		if (visible) { height += node.height + _bspace; }
    		if (!node.expanded) { visible = false; }
    		
    		if (node.childDegree > 0) // is not a leaf node
    		{    			
    			var c:NodeSprite = node.firstChildNode;   			
    			for (; c != null; c = c.nextNode) {
    				height = layoutNode(c, height, indent+1, visible);
    			}
    		}
    		return height;
    	}
    	
	} // end of class IndentedTreeLayout
}