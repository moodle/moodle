package flare.vis.operator.layout
{
	import flare.animate.Transitioner;
	import flare.util.Arrays;
	import flare.vis.data.NodeSprite;
	
	import flash.geom.Rectangle;
	
	/**
	 * Layout that places tree nodes in a radial layout, laying out subsequent
	 * depth levels of a tree on circles of progressively increasing radius. 
	 * This layout can be used for both node-link diagrams, where nodes are
	 * connected by edges, and for radial space-filling ("starburst") diagrams.
	 * To generate space-filling layouts, nodes should have their shape
	 * property set to <code>Shapes.WEDGE</code> and the layout instance should
	 * have the <code>useNodeSize<code> property set to false.
	 * 
	 * <p>The algorithm used is an adaptation of a technique by Ka-Ping Yee,
	 * Danyel Fisher, Rachna Dhamija, and Marti Hearst, published in the paper
	 * <a href="http://citeseer.ist.psu.edu/448292.html">Animated Exploration of
	 * Dynamic Graphs with Radial Layout</a>, InfoVis 2001. This algorithm computes
	 * a radial layout which factors in possible variation in sizes, and maintains
	 * both orientation and ordering constraints to facilitate smooth and
	 * understandable transitions between layout configurations.
	 * </p>
	 */
	public class RadialTreeLayout extends Layout
	{
		// -- Properties ------------------------------------------------------
		
		/** Property name for storing parameters for this layout. */
		public static const PARAMS:String = "radialTreeLayoutParams";
		/** The default radius increment between depth levels. */
		public static const DEFAULT_RADIUS:int = 50;
	
	    private var _maxDepth:int = 0;
	    private var _radiusInc:Number = DEFAULT_RADIUS;
	    private var _theta1:Number = Math.PI/2;
	    private var _theta2:Number = Math.PI/2 + 2*Math.PI;
	    private var _sortAngles:Boolean = true;
	    private var _setTheta:Boolean = false;
	    private var _autoScale:Boolean = true;
	    private var _useNodeSize:Boolean = true;
	    private var _prevRoot:NodeSprite = null;

		private var _t:Transitioner;

		/** The radius increment between depth levels. */
		public function get radiusIncrement():Number { return _radiusInc; }
		public function set radiusIncrement(r:Number):void { _radiusInc = r; }
		
		/** Flag determining if nodes should be sorted by angles to help
		 *  maintain ordering across different spanning-tree configurations.
		 *  This sorting is important for understandable transitions when using
		 *  a radial layout of a general graph. However, it is unnecessary for
		 *  tree structures and increases the running time of layout. */
		public function get sortAngles():Boolean { return _sortAngles; }
		public function set sortAngles(b:Boolean):void { _sortAngles = b; }
		
		/** Flag indicating if the layout should automatically be scaled to
		 *  fit within the layout bounds. */
		public function get autoScale():Boolean { return _autoScale; }
		public function set autoScale(b:Boolean):void { _autoScale = b; }
		
		/** The initial angle for the radial layout. */
		public function get startAngle():Number { return _theta1; }
		public function set startAngle(a:Number):void {
			_theta2 += a - _theta1;
			_theta1 = a;
			_setTheta = true;
		}
		
		/** The total angular width the layout should use (2*pi by default).*/
		public function get angularWidth():Number { return _theta2 - _theta1; }
		public function set angularWidth(w:Number):void {
			_theta2 = _theta1 + w;
			_setTheta = true;
		}

		/** Flag indicating if node's <code>size</code> property should be
		 *  used to determine layout spacing. If a space-filling radial
		 *  layout is desired, this value must be false for the layout
		 *  to be accurate. */
		public function get useNodeSize():Boolean { return _useNodeSize; }
		public function set useNodeSize(b:Boolean):void {
			_useNodeSize = b;
		}

		// -- Methods ---------------------------------------------------------

		/**
		 * Creates a new RadialTreeLayout.
		 * @param radius the radius increment between depth levels
		 * @param sortAngles flag indicating if nodes should be sorted by angle
		 *  to maintain node ordering across spanning-tree configurations
		 * @param autoScale flag indicating if the layout should automatically
		 *  be scaled to fit within the layout bounds
		 */		
		public function RadialTreeLayout(radius:Number=DEFAULT_RADIUS,
			sortAngles:Boolean=true, autoScale:Boolean=true)
		{
			_radiusInc = radius;
			_sortAngles = sortAngles;
			_autoScale = autoScale;
		}

		/** @inheritDoc */
		public override function operate(t:Transitioner=null):void
		{
			_t = (t!=null ? t : Transitioner.DEFAULT);
			
			var n:NodeSprite = layoutRoot as NodeSprite;
			if (n == null) { _t = null; return; }
			var np:Params = params(n);
			
			// calc relative widths and maximum tree depth
        	// performs one pass over the tree
        	_maxDepth = 0;
        	calcAngularWidth(n, 0);
			
			if (_autoScale) setScale(layoutBounds);
			if (!_setTheta) calcAngularBounds(n);
			
			// perform the layout
	        if (_maxDepth > 0) {
	        	layout(n, _radiusInc, _theta1, _theta2);
	        } else if (n.childDegree > 0) {
	        	n.visitTreeDepthFirst(function(n:NodeSprite):void {
            		_t.$(n).radius = 0;
            		_t.$(n).alpha = 0;
            		_t.$(n).mouseEnabled = false;
            		if (n.parentEdge != null) {
            			_t.$(n.parentEdge).alpha = 0;
            		}
            	});
	        }
	        
	        // update properties of the root node
	        np.angle = _theta2 - _theta1;
	        update(n, 0, 0, np.angle, true);			
			updateEdgePoints();
			_t = null;
		}
		
		private function setScale(bounds:Rectangle):void
		{
	        var r:Number = Math.min(bounds.width, bounds.height)/2.0;
	        if (_maxDepth > 0) _radiusInc = r / (_maxDepth+1);
	    }
		
	    /**
	     * Calculates the angular bounds of the layout, attempting to
	     * preserve the angular orientation of the display across transitions.
	     */
	    private function calcAngularBounds(r:NodeSprite):void
	    {
	        if (_prevRoot == null || r == _prevRoot)
	        {
	            _prevRoot = r; return;
	        }
	        
	        // try to find previous parent of root
	        var p:NodeSprite = _prevRoot, pp:NodeSprite;
	        while (true) {
	        	pp = p.parentNode;
	            if (pp == r) {
	                break;
	            } else if (pp == null) {
	                _prevRoot = r;
	                return;
	            }
	            p = pp;
	        }
	
	        // compute offset due to children's angular width
	        var dt:Number = 0;
	        
	        for each (var n:NodeSprite in sortedChildren(r)) {
	        	if (n == p) break;
	        	dt += params(n).width;
	        }
	        
	        var rw:Number = params(r).width;
	        var pw:Number = params(p).width;
	        dt = -2*Math.PI * (dt+pw/2)/rw;
	
	        // set angular bounds
	        _theta1 = dt + Math.atan2(p.y-r.y, p.x-r.x);
	        _theta2 = _theta1 + 2*Math.PI;
	        _prevRoot = r;     
	    }
		
		/**
	     * Computes relative measures of the angular widths of each
	     * expanded subtree. Node diameters are taken into account
	     * to improve space allocation for variable-sized nodes.
	     * 
	     * This method also updates the base angle value for nodes 
	     * to ensure proper ordering of nodes.
	     */
	    private function calcAngularWidth(n:NodeSprite, d:int):Number
	    {
	        if (d > _maxDepth) _maxDepth = d;       
	        var aw:Number = 0, diameter:Number = 0;
	        if (_useNodeSize && d > 0) {
	        	//diameter = 1;
	        	diameter = n.expanded && n.childDegree > 0 ? 0 : _t.$(n).size;
	        } else if (d > 0) {
	        	var w:Number = n.width, h:Number = n.height;
	        	diameter = Math.sqrt(w*w+h*h)/d;
	        	if (isNaN(diameter)) diameter = 0;
	        }

	        if (n.expanded && n.childDegree > 0) {
	        	for (var c:NodeSprite=n.firstChildNode; c!=null; c=c.nextNode)
	        	{
	        		aw += calcAngularWidth(c, d+1);
	        	}
	        	aw = Math.max(diameter, aw);
	        } else {
	        	aw = diameter;
	        }
			params(n).width = aw;
	        return aw;
	    }
		
		private static function normalize(angle:Number):Number
		{
	        while (angle > 2*Math.PI)
	            angle -= 2*Math.PI;
	        while (angle < 0)
	            angle += 2*Math.PI;
	        return angle;
	    }
	    
	    private static function minDist(a1:Number, a2:Number):Number
	    {
	    	var d1:Number = a2 - a1;
	    	var d2:Number = Math.abs(d1 - 2*Math.PI);
	    	var d3:Number = Math.abs(d1 + 2*Math.PI);
			var dd:Number = Math.min(d1, d2, d3);
	    	
	    	if (dd == d1) {
	    		return a2;
	    	} else if (dd == d2) {
	    		return a2 - 2*Math.PI;
	    	} else {
	    		return a2 + 2*Math.PI;
	    	}
	    }

		private function sortedChildren(n:NodeSprite):Array
		{
			var cc:int = n.childDegree;
			if (cc == 0) return Arrays.EMPTY;
			var angles:Array = new Array(cc);
	        
	        if (_sortAngles) {
	        	// update base angle for node ordering			
				var base:Number = -_theta1;
				var p:NodeSprite = n.parentNode;
	        	if (p != null) base = normalize(Math.atan2(p.y-n.y, n.x-p.x));
	        	
	        	// collect the angles
	        	var c:NodeSprite = n.firstChildNode;
		        for (var i:uint=0; i<cc; ++i, c=c.nextNode) {
		        	angles[i] = normalize(-base + Math.atan2(c.y-n.y,n.x-c.x));
		        }
		        // get array of indices, sorted by angle
		        angles = angles.sort(Array.NUMERIC | Array.RETURNINDEXEDARRAY);
		        // switch in the actual nodes and return
		        for (i=0; i<cc; ++i) {
		        	angles[i] = n.getChildNode(angles[i]);
		        }
		    } else {
		    	for (i=0; i<cc; ++i) {
		        	angles[i] = n.getChildNode(i);
		        }
		    }
	        
	        return angles;
	    }
		
		/**
	     * Compute the layout.
	     * @param n the root of the current subtree under consideration
	     * @param r the radius, current distance from the center
	     * @param theta1 the start (in radians) of this subtree's angular region
	     * @param theta2 the end (in radians) of this subtree's angular region
	     */
	    private function layout(n:NodeSprite, r:Number, theta1:Number, theta2:Number):void
	    {
	    	var dtheta:Number = (theta2-theta1);
	    	var dtheta2:Number = dtheta / 2.0;
	    	var width:Number = params(n).width;
	    	var cfrac:Number, nfrac:Number = 0;
	        
	        for each (var c:NodeSprite in sortedChildren(n)) {
	        	var cp:Params = params(c);
	            cfrac = cp.width / width;
	            if (c.expanded && c.childDegree > 0)
	            {
	                layout(c, r+_radiusInc, theta1 + nfrac*dtheta, 
	                                        theta1 + (nfrac+cfrac)*dtheta);
	            }
	            else if (c.childDegree > 0)
	            {
	            	var cr:Number = r + _radiusInc;
	            	var ca:Number = theta1 + nfrac*dtheta + cfrac*dtheta2;
	            	
	            	c.visitTreeDepthFirst(function(n:NodeSprite):void {
	            		update(n, cr, minDist(n.angle, ca), 0, false);
	            	});
	            }
	            
	            var a:Number = minDist(c.angle, theta1 + nfrac*dtheta + cfrac*dtheta2);
	            cp.angle = cfrac * dtheta;
	            update(c, r, a, cp.angle, true);
	            nfrac += cfrac;
	        }
	    }
		
		private function update(n:NodeSprite, r:Number, a:Number,
								aw:Number, v:Boolean) : void
		{
			var o:Object = _t.$(n), alpha:Number = v ? 1 : 0;
			o.radius = r;
			o.angle = a;
			o.h = r + _radiusInc/2;
			o.v = r - _radiusInc/2;
			o.w = aw;
			o.u = a - aw/2;
			o.alpha = alpha;
			o.mouseEnabled = v;
			if (n.parentEdge != null)
				_t.$(n.parentEdge).alpha = alpha;
		}
				
		private function params(n:NodeSprite):Params
		{
			var p:Params = n.props[PARAMS];
			if (p == null) {
				p = new Params();
				n.props[PARAMS] = p;
			}
			return p;
		}
		
	} // end of class RadialTreeLayout
}

class Params {
	public var width:Number = 0;
	public var angle:Number = 0;
}