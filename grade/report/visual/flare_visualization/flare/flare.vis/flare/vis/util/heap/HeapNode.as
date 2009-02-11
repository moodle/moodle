package flare.vis.util.heap
{
	/**
	 * Represents a node in a heap data structure.
	 * For use with the <code>FibonacciHeap</code> class.
	 * @see flare.vis.util.FibonacciHeap
	 */
	public class HeapNode
	{
		/** Arbitrary client data property to store with the node. */
		public var data:*;
		/** The parent node of this node. */
		public var parent:HeapNode;
		/** A child node of this node. */
		public var child:HeapNode;
		/** The right child node of this node. */
		public var right:HeapNode;
		/** The left child node of this node. */
		public var left:HeapNode;
		/** Boolean flag useful for marking this node. */
		public var mark:Boolean;
		/** Flag indicating if this node is currently in a heap. */
		public var inHeap:Boolean = true;
		/** Key value used for sorting the heap nodes. */
		public var key:Number;
		/** The degree of this heap node (number of child nodes). */
		public var degree:int;
	
		/**
		 * Creates a new HeapNode
		 * @param data arbitrary data to store with this node
		 * @param key the key value to sort on
		 */
		function HeapNode(data:*, key:Number)
		{
			this.data = data;
			this.key = key;
			right = this;
			left = this;
		}
	} // end of class HeapNode
}