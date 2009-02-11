package flare.physics
{
	/**
	 * Force simulating an N-Body force of charged particles with pairwise
	 * interaction, such as gravity or electrical charge. This class uses a
	 * quad-tree structure to aggregate charge values and optimize computation.
	 * The force function is a standard inverse-square law (though in this case
	 * approximated due to optimization): <code>F = G * m1 * m2 / d^2</code>,
	 * where G is a constant (e.g., gravitational constant), m1 and m2 are the
	 * masses (charge) of the particles, and d is the distance between them.
	 * 
	 * <p>The algorithm used is that of J. Barnes and P. Hut, in their research
	 * paper <i>A Hierarchical  O(n log n) force calculation algorithm</i>, Nature, 
	 * v.324, December 1986. For more details on the algorithm, see one of
	 * the following links:
	 * <ul>
	 *   <li><a href="http://www.cs.berkeley.edu/~demmel/cs267/lecture26/lecture26.html">James Demmel's UC Berkeley lecture notes</a>
	 *   <li><a href="http://www.physics.gmu.edu/~large/lr_forces/desc/bh/bhdesc.html">Description of the Barnes-Hut algorithm</a>
	 *   <li><a href="http://www.ifa.hawaii.edu/~barnes/treecode/treeguide.html">Joshua Barnes' implementation</a>
	 * </ul></p>
	 */
	public class NBodyForce implements IForce
	{
		private var _g:Number;     // gravitational constant
		private var _t:Number;     // barnes-hut theta
		private var _max:Number;   // max effective distance
		private var _min:Number;   // min effective distance
		private var _eps:Number;   // epsilon for determining 'same' location
		
		private var _x1:Number, _y1:Number, _x2:Number, _y2:Number;
		private var _root:QuadTreeNode;
		
		/** The gravitational constant to use. 
		 *  Negative values produce a repulsive force. */
		public function get gravitation():Number { return _g; }
		public function set gravitation(g:Number):void { _g = g; }
		
		/** The maximum distance over which forces are exerted. 
		 *  Any greater distances will be ignored. */
		public function get maxDistance():Number { return _max; }
		public function set maxDistance(d:Number):void { _max = d; }
		
		/** The minumum effective distance over which forces are exerted.
		 * 	Any lesser distances will be treated as the minimum. */
		public function get minDistance():Number { return _min; }
		public function set minDistance(d:Number):void { _min = d; }
		
		// --------------------------------------------------------------------
		
		/**
		 * Creates a new NBodyForce with given parameters.
		 * @param g the gravitational constant to use.
		 *  Negative values produce a repulsive force.
		 * @param maxd a maximum distance over which the force should operate.
		 *  Particles separated by more than this distance will not interact.
		 * @param mind the minimum distance over which the force should operate.
		 *  Particles closer than this distance will interact as if they were
		 *  the minimum distance apart. This helps avoid extreme forces.
		 *  Helpful when particles are very close together.
		 * @param eps an epsilon values for determining a minimum distance
		 *  between particles
		 * @param t the theta parameter for the Barnes-Hut approximation.
		 *  Determines the level of approximation (default value if 0.9).
		 */
		public function NBodyForce(g:Number=-1, max:Number=200, min:Number=2,
								   eps:Number=0.01, t:Number=0.9)
		{
			_g = g;
			_max = max;
			_min = min;
			_eps = eps;
			_t = t;
			_root = QuadTreeNode.node();
		}

		/**
		 * Applies this force to a simulation.
		 * @param sim the Simulation to apply the force to
		 */
		public function apply(sim:Simulation):void
		{
			if (_g == 0) return;
			
			// clear the quadtree
			clear(_root); _root = QuadTreeNode.node();
			
			// get the tree bounds
			bounds(sim);
        
        	// populate the tree
        	for (var i:uint = 0; i<sim.particles.length; ++i) {
        		insert(sim.particles[i], _root, _x1, _y1, _x2, _y2);
        	}	
        	
        	// traverse tree to compute mass
        	accumulate(_root);
        	
        	// calculate forces on each particle
        	for (i=0; i<sim.particles.length; ++i) {
        		forces(sim.particles[i], _root, _x1, _y1, _x2, _y2);
        	}
		}
		
		private function accumulate(n:QuadTreeNode):void {
			var xc:Number = 0, yc:Number = 0;
			n.mass = 0;
			
			// accumulate childrens' mass
			var recurse:Function = function(c:QuadTreeNode):void {
				if (c == null) return;
				accumulate(c);
				n.mass += c.mass;
				xc += c.mass * c.cx;
				yc += c.mass * c.cy;
			}
			if (n.hasChildren) {
				recurse(n.c1); recurse(n.c2); recurse(n.c3); recurse(n.c4);
			}
			
			// accumulate own mass
			if (n.p != null) {
				n.mass += n.p.mass;
				xc += n.p.mass * n.p.x;
				yc += n.p.mass * n.p.y;
			}
			n.cx = xc / n.mass;
			n.cy = yc / n.mass;
		}
		
		private function forces(p:Particle, n:QuadTreeNode,
			x1:Number, y1:Number, x2:Number, y2:Number):void
		{
			var f:Number = 0;
			var dx:Number = n.cx - p.x;
			var dy:Number = n.cy - p.y;
			var dd:Number = Math.sqrt(dx*dx + dy*dy);
			var max:Boolean = _max > 0 && dd > _max;
			if (dd==0) { // add direction when needed
				dx = _eps * (0.5-Math.random());
				dy = _eps * (0.5-Math.random());
			}
			
			// the Barnes-Hut approximation criteria is if the ratio of the
        	// size of the quadtree box to the distance between the point and
        	// the box's center of mass is beneath some threshold theta.
        	if ( (!n.hasChildren && n.p != p) || ((x2-x1)/dd < _t) )
        	{
            	if ( max ) return;
            	// either only 1 particle or we meet criteria
            	// for Barnes-Hut approximation, so calc force
            	dd = dd<_min ? _min : dd;
            	f = _g * p.mass * n.mass / (dd*dd*dd)
            	p.fx += f*dx; p.fy += f*dy;
        	}
        	else if ( n.hasChildren )
        	{
            	// recurse for more accurate calculation
            	var sx:Number = (x1+x2)/2
            	var sy:Number = (y1+y2)/2;
            	
            	if (n.c1) forces(p, n.c1, x1, y1, sx, sy);
				if (n.c2) forces(p, n.c2, sx, y1, x2, sy);
				if (n.c3) forces(p, n.c3, x1, sy, sx, y2);
				if (n.c4) forces(p, n.c4, sx, sy, x2, y2);

            	if ( max ) return;
            	if ( n.p != null && n.p != p ) {
            		dd = dd<_min ? _min : dd;
                	f = _g * p.mass * n.p.mass / (dd*dd*dd);
                	p.fx += f*dx; p.fy += f*dy;
            	}
			}
		}
				
		// -- Helpers ---------------------------------------------------------
		
		private function insert(p:Particle, n:QuadTreeNode,
			x1:Number, y1:Number, x2:Number, y2:Number):void
		{
			// try to insert particle p at node n in the quadtree
        	// by construction, each leaf will contain either 1 or 0 particles
        	if ( n.hasChildren ) {
            	// n contains more than 1 particle
            	insertHelper(p,n,x1,y1,x2,y2);
        	} else if ( n.p != null ) {
            	// n contains 1 particle
            	if ( isSameLocation(n.p, p) ) {
            		// recurse
                	insertHelper(p,n,x1,y1,x2,y2);
            	} else {
            		// divide
            		var v:Particle = n.p; n.p = null;
                	insertHelper(v,n,x1,y1,x2,y2);
                	insertHelper(p,n,x1,y1,x2,y2);
            	}
        	} else { 
            	// n is empty, add p as leaf
            	n.p = p;
        	}
		}
		
		private function insertHelper(p:Particle, n:QuadTreeNode, 
			x1:Number, y1:Number, x2:Number, y2:Number):void
    	{
    		// determine split
			var sx:Number = (x1+x2)/2;
			var sy:Number = (y1+y2)/2;
			var c:uint = (p.x >= sx ? 1 : 0) + (p.y >= sy ? 2 : 0);
			
			// update bounds
			if (c==1 || c==3) x1 = sx; else x2 = sx;
			if (c>1) y1 = sy; else y2 = sy;
			
			// update children
			var cn:QuadTreeNode;
			if (c == 0) {
				if (n.c1==null) n.c1 = QuadTreeNode.node();
				cn = n.c1;
			} else if (c == 1) {
				if (n.c2==null) n.c2 = QuadTreeNode.node();
				cn = n.c2;
			} else if (c == 2) {
				if (n.c3==null) n.c3 = QuadTreeNode.node();
				cn = n.c3;
			} else {
				if (n.c4==null) n.c4 = QuadTreeNode.node();
				cn = n.c4;
			}
			n.hasChildren = true;
			insert(p,cn,x1,y1,x2,y2);
    	}
		
		private function clear(n:QuadTreeNode):void
		{
			if (n.c1 != null) clear(n.c1);
			if (n.c2 != null) clear(n.c2);
			if (n.c3 != null) clear(n.c3);
			if (n.c4 != null) clear(n.c4);
			QuadTreeNode.reclaim(n);
		}
		
		private function bounds(sim:Simulation):void
		{
			var p:Particle, dx:Number, dy:Number;
			_x1 = _y1 = Number.MAX_VALUE;
			_x2 = _y2 = Number.MIN_VALUE;

			// get bounding box
			for (var i:uint = 0; i<sim.particles.length; ++i) {
				p = sim.particles[i] as Particle;
				if (p.x < _x1) _x1 = p.x;
				if (p.y < _y1) _y1 = p.y;
				if (p.x > _x2) _x2 = p.x;
				if (p.y > _y2) _y2 = p.y;
			}
			
			// square the box
			dx = _x2 - _x1;
			dy = _y2 - _y1;
			if (dx > dy) {
				_y2 = _y1 + dx;
			} else {
				_x2 = _x1 + dy;
			}
		}
		
		private function isSameLocation(p1:Particle, p2:Particle):Boolean {
        	return (Math.abs(p1.x - p2.x) < _eps && 
        			Math.abs(p1.y - p2.y) < _eps);
    	}
		
	} // end of class NBodyForce
}

// -- Helper QuadTreeNode class -----------------------------------------------

import flare.physics.Particle;

class QuadTreeNode
{
	public var mass:Number = 0;
	public var cx:Number = 0;
	public var cy:Number = 0;
	public var p:Particle = null;
	public var c1:QuadTreeNode = null;
	public var c2:QuadTreeNode = null;
	public var c3:QuadTreeNode = null;
	public var c4:QuadTreeNode = null;
	public var hasChildren:Boolean = false;
	
	// -- Factory ---------------------------------------------------------
	
	private static var _nodes:Array = new Array();
	
	public static function node():QuadTreeNode {
		var n:QuadTreeNode;
		if (_nodes.length > 0) {
			n = QuadTreeNode(_nodes.pop());
		} else {
			n = new QuadTreeNode();
		}
		return n;
	}
	
	public static function reclaim(n:QuadTreeNode):void {
		n.mass = n.cx = n.cy = 0;
		n.p = null;
		n.hasChildren = false;
		n.c1 = n.c2 = n.c3 = n.c4 = null;
		_nodes.push(n);
	}
}