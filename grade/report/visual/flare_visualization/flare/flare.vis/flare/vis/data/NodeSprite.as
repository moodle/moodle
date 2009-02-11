package flare.vis.data
{
	import flare.util.Arrays;
	
	/**
	 * Visually represents a data element, such as a data tuple or graph node.
	 * By default, NodeSprites are drawn using a <codeShapeRenderer<code>.
	 * NodeSprites are typically managed by a <code>Data</code> object.
	 * 
	 * <p>NodeSprites can separately maintain adjacency lists for both a
	 * general graph structure (managing lists for inlinks and outlinks) and a
	 * tree structure (managing a list for child links and a parent pointer).
	 * The graph and tree lists are maintained completely separately to
	 * maximize flexibility. While the the tree lists are often used to record
	 * a spanning tree of the general network structure, they can also be used
	 * to represent a hierarchy completely distinct from a co-existing graph
	 * structure. Take this into account when iterating over the edges incident
	 * on this node.</p>
	 */
	public class NodeSprite extends DataSprite
	{
		/** Flag indicating inlinks, edges that point to this node. */
		public static const IN_LINKS:uint    = 1;
		/** Flag indicating outlinks, edges that point away from node. */
		public static const OUT_LINKS:uint   = 2;
		/** Flag indicating both inlinks and outlinks. */
		public static const GRAPH_LINKS:uint = 3;  // IN_LINKS | OUT_LINKS
		/** Flag indicating child links in a tree structure. */
		public static const CHILD_LINKS:uint = 4;
		/** Flag indicating the link to a parent in a tree structure. */
		public static const PARENT_LINK:uint = 8;
		/** Flag indicating both child and parent links. */
		public static const TREE_LINKS:uint  = 12; // CHILD_LINKS | PARENT_LINK
		/** Flag indicating all links, including graph and tree links. */
		public static const ALL_LINKS:uint   = 15; // GRAPH_LINKS | TREE_LINKS
		/** Flag indicating that a traversal should be performed in reverse. */
		public static const REVERSE:uint     = 16;
		
		// -- Properties ------------------------------------------------------
		
		private var _parentEdge:EdgeSprite;
		private var _idx:int = -1; // node index in parent's array
		private var _childEdges:/*EdgeSprite*/Array;
		private var _inEdges:/*EdgeSprite*/Array;
		private var _outEdges:/*EdgeSprite*/Array;
		private var _expanded:Boolean = true;
		
		/** Flag indicating if this node is currently expanded. This flag can
		 *  be used by layout routines to expand/collapse connections. */
		public function get expanded():Boolean { return _expanded; }
		public function set expanded(b:Boolean):void { _expanded = b; }
		
		/** The edge connecting this node to its parent in a tree structure. */
		public function get parentEdge():EdgeSprite { return _parentEdge; }
		public function set parentEdge(e:EdgeSprite):void { _parentEdge = e; }
		
		/** The index of this node in its tree parent's child links list. */
		public function get parentIndex():int { return _idx; }
		public function set parentIndex(i:int):void { _idx = i; }

		// -- Node Degree Properties ------------------------------------------

		/** The number of child links. */
		public function get childDegree():uint { return _childEdges==null ? 0 : _childEdges.length; }
		/** The number of inlinks and outlinks. */
		public function get degree():uint { return inDegree + outDegree; }
		/** The number of inlinks. */
		public function get inDegree():uint { return _inEdges==null ? 0 : _inEdges.length; }
		/** The number of outlinks. */
		public function get outDegree():uint { return _outEdges==null ? 0 : _outEdges.length; }

		/** The depth of this node in the tree structure. A value of zero
		 *  indicates that this is a root node or that there is no tree. */
		public function get depth():uint {
			for (var d:uint=0, p:NodeSprite=parentNode; p!=null; p=p.parentNode, d++);
			return d;
		}

		// -- Node Access Properties ---------------------------

		/** The parent of this node in the tree structure. */
		public function get parentNode():NodeSprite
		{
			return _parentEdge == null ? null : _parentEdge.other(this);
		}
		
		/** The first child of this node in the tree structure. */
		public function get firstChildNode():NodeSprite
		{
			return childDegree > 0 ? _childEdges[0].other(this) : null;
		}
		
		/** The last child of this node in the tree structure. */
		public function get lastChildNode():NodeSprite
		{
			var len:uint = childDegree;
			return len > 0 ? _childEdges[len-1].other(this) : null;
		}
		
		/** The next sibling of this node in the tree structure. */
		public function get nextNode():NodeSprite
		{
			var p:NodeSprite = parentNode, i:int = _idx+1;
			if (p == null || i >= p.childDegree) return null;
			return parentNode.getChildNode(i);
		}
		
		/** The previous sibling of this node in the tree structure. */
		public function get prevNode():NodeSprite
		{
			var p:NodeSprite = parentNode, i:int = _idx-1;
			if (p == null || i < 0) return null;
			return parentNode.getChildNode(i);
		}
		
		// -- Position Overrides -------------------------------

		/** @inheritDoc */
		public override function set x(v:Number):void
		{
			if (x!=v) dirtyEdges();
			super.x = v;
		}
		/** @inheritDoc */
		public override function set y(v:Number):void
		{
			if (y!=v) dirtyEdges();
			super.y = v;
		}
		/** @inheritDoc */
		public override function set radius(r:Number):void
		{
			if (_radius!=r) dirtyEdges();
			super.radius = r;
		}
		/** @inheritDoc */
		public override function set angle(a:Number):void
		{
			if (_angle!=a) dirtyEdges();
			super.angle = a;
		}
		
		// -- Methods ---------------------------------------------------------

		/** Mark all incident edges as dirty. */
		private function dirtyEdges():void
		{
			var e:EdgeSprite;
			if (_parentEdge) _parentEdge.dirty();
			if (_childEdges) for each (e in _childEdges) { e.dirty(); }
			if (_outEdges)   for each (e in _outEdges)   { e.dirty(); }
			if (_inEdges)    for each (e in _inEdges)    { e.dirty(); }
		}
		
		// -- Test Methods -------------------------------------
		
		/**
		 * Indicates if the input node is connected to this node by an edge.
		 * @param n the node to check for connection
		 * @param opt flag indicating which links to check
		 * @return true if connected, false otherwise
		 */		
		public function isConnected(n:NodeSprite, opt:uint=ALL_LINKS):Boolean
		{
			return visitNodes(
				function(d:NodeSprite):Boolean { return n==d; },
				opt);
		}

		// -- Accessor Methods ---------------------------------
		
		/**
		 * Gets the child edge at the specified position
		 * @param i the position of the child edge
		 * @return the child edge
		 */		
		public function getChildEdge(i:uint):EdgeSprite
		{
			return _childEdges[i];
		}
		
		/**
		 * Gets the child node at the specified position
		 * @param i the position of the child node
		 * @return the child node
		 */
		public function getChildNode(i:uint):NodeSprite
		{
			return _childEdges[i].other(this);
		}
		
		/**
		 * Gets the inlink edge at the specified position
		 * @param i the position of the inlink edge
		 * @return the inlink edge
		 */
		public function getInEdge(i:uint):EdgeSprite
		{
			return _inEdges[i];
		}
		
		/**
		 * Gets the inlink node at the specified position
		 * @param i the position of the inlink node
		 * @return the inlink node
		 */
		public function getInNode(i:uint):NodeSprite
		{
			return _inEdges[i].source;
		}
		
		/**
		 * Gets the outlink edge at the specified position
		 * @param i the position of the outlink edge
		 * @return the outlink edge
		 */
		public function getOutEdge(i:uint):EdgeSprite
		{
			return _outEdges[i];
		}
		
		/**
		 * Gets the outlink node at the specified position
		 * @param i the position of the outlink node
		 * @return the outlink node
		 */
		public function getOutNode(i:uint):NodeSprite
		{
			return _outEdges[i].target;
		}
		
		// -- Mutator Methods ----------------------------------
		
		/**
		 * Adds a child edge to this node.
		 * @param e the edge to add to the child links list
		 * @return the index of the added edge in the list
		 */
		public function addChildEdge(e:EdgeSprite):uint
		{
			if (_childEdges == null) _childEdges = new Array();
			_childEdges.push(e);
			return _childEdges.length - 1;
		}
		
		/**
		 * Adds an inlink edge to this node.
		 * @param e the edge to add to the inlinks list
		 * @return the index of the added edge in the list
		 */
		public function addInEdge(e:EdgeSprite):uint
		{
			if (_inEdges == null) _inEdges = new Array();
			_inEdges.push(e);
			return _inEdges.length - 1;
		}
		
		/**
		 * Adds an outlink edge to this node.
		 * @param e the edge to add to the outlinks list
		 * @return the index of the added edge in the list
		 */
		public function addOutEdge(e:EdgeSprite):uint
		{
			if (_outEdges == null) _outEdges = new Array();
			_outEdges.push(e);
			return _outEdges.length - 1;
		}
		
		/**
		 * Removes all edges incident on this node. Note that this method
		 * does not update the edges themselves or the other nodes.
		 */
		public function removeAllEdges():void
		{
			removeEdges(ALL_LINKS);
		}
		
		/**
		 * Removes all edges of the indicated edge type. Note that this method
		 * does not update the edges themselves or the other nodes.
		 * @param type the type of edges to remove. For example, IN_LINKS,
		 *  OUT_LINKS, TREE_LINKS, etc.
		 */
		public function removeEdges(type:int):void
		{
			var e:EdgeSprite;
			if (type & PARENT_LINK && _parentEdge) {
				_parentEdge = null;
			}
			if (type & CHILD_LINKS && _childEdges) {
				while (_childEdges.length > 0) { e=_childEdges.pop(); }
			}
			if (type & OUT_LINKS && _outEdges) {
				while (_outEdges.length > 0) { e=_outEdges.pop(); }
			}
			if (type & IN_LINKS && _inEdges) {
				while (_inEdges.length > 0) { e=_inEdges.pop(); }	
			}
		}
		
		/**
		 * Removes an edge from the child links list. Note that this method
		 * does not update the edge itself or the other node.
		 * @param e the edge to remove
		 */
		public function removeChildEdge(e:EdgeSprite):void
		{
			Arrays.remove(_childEdges, e);
		}
		
		/**
		 * Removes an edge from the inlinks list. Note that this method
		 * does not update the edge itself or the other node.
		 * @param e the edge to remove
		 */
		public function removeInEdge(e:EdgeSprite):void
		{
			Arrays.remove(_inEdges, e);
		}
		
		/**
		 * Removes an edge from the outlinks list. Note that this method
		 * does not update the edge itself or the other node.
		 * @param e the edge to remove
		 */
		public function removeOutEdge(e:EdgeSprite):void
		{
			Arrays.remove(_outEdges, e);
		}
		
		// -- Visitor Methods --------------------------------------------------
		
		/**
		 * Visits this node's edges, invoking a function on each visited edge.
		 * @param f the function to invoke on the edges. If the function
		 *  returns true, the visitation is ended with an early exit.
		 * @param opt flag indicating which sets of edges should be visited
		 * @return true if the visitation was interrupted with an early exit
		 */
		public function visitEdges(f:Function, opt:uint=ALL_LINKS):Boolean
		{
			var rev:Boolean = (opt & REVERSE) > 0;
			if (opt & IN_LINKS && _inEdges != null) { 
				if (visitEdgeHelper(f, _inEdges, rev)) return true;
			}
			if (opt & OUT_LINKS && _outEdges != null) {
				if (visitEdgeHelper(f, _outEdges, rev)) return true;
			}
			if (opt & CHILD_LINKS && _childEdges != null) {
				if (visitEdgeHelper(f, _childEdges, rev)) return true;
			}
			if (opt & PARENT_LINK && _parentEdge != null) {
				if (f(_parentEdge)) return true;
			}
			return false;
		}
		
		private function visitEdgeHelper(f:Function, a:Array, r:Boolean):Boolean
		{
			var i:uint, v:*;
			if (r) {
				for (i=a.length; --i>=0;) {
					if (f(a[i]) as Boolean) return true;
				}
			} else {
				for (i=0; i<a.length; ++i) {
					if (f(a[i]) as Boolean) return true;
				}
			}
			return false;
		}
		
		/**
		 * Visits the nodes connected to this node by edges, invoking a
		 * function on each visited node.
		 * @param f the function to invoke on the nodes. If the function
		 *  returns true, the visitation is ended with an early exit.
		 * @param opt flag indicating which sets of edges should be traversed
		 * @return true if the visitation was interrupted with an early exit
		 */
		public function visitNodes(f:Function, opt:uint=ALL_LINKS):Boolean
		{
			var rev:Boolean = (opt & REVERSE) > 0;
			if (opt & IN_LINKS && _inEdges != null) {
				if (visitNodeHelper(f, _inEdges, rev)) return true;
			}
			if (opt & OUT_LINKS && _outEdges != null) {
				if (visitNodeHelper(f, _outEdges, rev)) return true;
			}
			if (opt & CHILD_LINKS && _childEdges != null) {
				if (visitNodeHelper(f, _childEdges, rev)) return true;
			}
			if (opt & PARENT_LINK && _parentEdge != null) {
				if (f(_parentEdge.other(this))) return true;
			}
			return false;
		}
		
		private function visitNodeHelper(f:Function, a:Array, r:Boolean):Boolean
		{
			var i:uint;
			if (r) {
				for (i=a.length; --i>=0;)
					if (f(a[i].other(this)) as Boolean) return true;
			} else {
				for (i=0; i<a.length; ++i)
					if (f(a[i].other(this)) as Boolean) return true;
			}
			return false;
		}
		
		/**
		 * Visits the subtree rooted at this node using a depth first search,
		 * invoking the input function on each visited node.
		 * @param f the function to invoke on the nodes. If the function
		 *  returns true, the visitation is ended with an early exit.
		 * @param preorder if true, nodes are visited in a pre-order traversal;
		 *  if false, they are visited in a post-order traversal
		 * @return true if the visitation was interrupted with an early exit
		 */
		public function visitTreeDepthFirst(f:Function, preorder:Boolean=false):Boolean
		{
			if (preorder && (f(this) as Boolean)) return true;
			for (var i:uint = 0; i<childDegree; ++i) {
				if (getChildNode(i).visitTreeDepthFirst(f)) return true;
			}
			if (!preorder && (f(this) as Boolean)) return false;
			return false;
		}
		
		/**
		 * Visits the subtree rooted at this node using a breadth first search,
		 * invoking the input function on each visited node.
		 * @param f the function to invoke on the nodes. If the function
		 *  returns true, the visitation is ended with an early exit.
		 * @return true if the visitation was interrupted with an early exit
		 */
		public function visitTreeBreadthFirst(f:Function):Boolean
		{
			var q:Array = new Array(), x:NodeSprite;
			
			q.push(this);
			while (q.length > 0) {
				if (f(x=q.shift()) as Boolean) return true;
				for (var i:uint = 0; i<x.childDegree; ++i)
					q.push(x.getChildNode(i));
			}
			return false;
		}
		
	} // end of class NodeSprite
}