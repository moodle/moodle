package flare.vis.util.heap
{
	/**
	 * A Fibonacci heap data structure for maintaining a sorted priority queue.
	 * For more about this particular implementation see
	 * <a target="_top" href="http://en.wikipedia.org/wiki/Fibonacci_heap">
	 * Wikipedia's Fibonacci Heap article</a>.
	 */
	public class FibonacciHeap
	{
		private var _min:HeapNode;
		private var _size:int;

		/** True if the heap is empty, false otherwise. */
		public function get empty():Boolean
		{
			return _min == null;
		}
		
		/** The number of nodes contained in this heap. */
		public function get size():int
		{
			return _size;
		}
		
		/**
		 * Clears the heap, removing all nodes.
		 */
		public function clear():void
		{
			_min = null;
			_size = 0;
		}

		/**
		 * Decrease the key value for a heap node, changing its key and
		 * potentially re-configuring the heap structure
		 * @param x the heap node
		 * @param k the new key value for the node. If this value is greater
		 *  than the node's current key value an error will be thrown.
		 */		
		public function decreaseKey(x:HeapNode, k:Number):void
		{
			if (k > x.key)
            	throw new Error("Only lower key values allowed");

        	x.key = k;
        	var y:HeapNode = x.parent;

	        if ((y != null) && (x.key < y.key)) {
    	        cut(x, y);
        	    cascadingCut(y);
        	}

        	if (x.key < _min.key) {
            	_min = x;
        	}
		}

		/**
		 * Removes a node from the heap.
		 * @param x the heap node to remove
		 */
		public function remove(x:HeapNode):void
    	{
        	decreaseKey(x, Number.NEGATIVE_INFINITY);
        	removeMin();
    	}

		/**
		 * Inserts a new node into the heap.
		 * @param data the data to associate with the heap node
		 * @param key the key value used to sort the heap node
		 * @return the newly added heap node
		 */		
		public function insert(data:Object, key:Number):HeapNode
		{
			var n:HeapNode = new HeapNode(data, key);
			n.inHeap = true;

        	if (_min != null) {
            	n.left = _min;
            	n.right = _min.right;
            	_min.right = n;
            	n.right.left = n;

            	if (key < _min.key)
                	_min = n;
        	} else {
            	_min = n;
        	}
	        _size++;
	        return n;
		}

		/**
		 * Returns the heap node with the minimum key value.
		 * @return the heap node with the minimum key value
		 */
		public function min():HeapNode
		{
			return _min;
		}

		/**
		 * Removes and returns the heap node with the minimum key value.
		 * @return the heap node with the minimum key value
		 */
		public function removeMin():HeapNode
		{
			var z:HeapNode = _min;
			if (z == null) return z;
			
			var kids:int = z.degree;
			var x:HeapNode = z.child;
			var r:HeapNode;

            // for each child of z do...
            while (kids > 0) {
                r = x.right;

                // remove x from child list
                x.left.right = x.right;
                x.right.left = x.left;

                // add x to root list of heap
                x.left = _min;
                x.right = _min.right;
                _min.right = x;
                x.right.left = x;

                // set parent[x] to null
                x.parent = null;
                x = r;
                kids--;
            }

            // remove z from root list of heap
            z.left.right = z.right;
            z.right.left = z.left;

            if (z == z.right) {
                _min = null;
            } else {
                _min = z.right;
                consolidate();
            }

	        // decrement size of heap and return
	        _size--;
	  		z.inHeap = false;
	        return z;
		}

		/**
		 * Constructs the union of two fibonacci heaps.
		 * @param h1 the first heap
		 * @param h2 the second heap
		 * @return the union of the two heaps
		 */
		public static function union(h1:FibonacciHeap, h2:FibonacciHeap):FibonacciHeap
		{
			var h:FibonacciHeap = new FibonacciHeap();
					
	        if (h1 != null && h2 != null) {
	            h._min = h1._min;
	
	            if (h._min != null) {
	                if (h2._min != null) {
	                    h._min.right.left = h2._min.left;
	                    h2._min.left.right = h._min.right;
	                    h._min.right = h2._min;
	                    h2._min.left = h._min;
	                    if (h2._min.key < h1._min.key)
	                        h._min = h2._min;
	                }
	            } else {
	                h._min = h2._min;
	            }
	
	            h._size = h1._size + h2._size;
	        }
	
	        return h;
		}

	    private function cascadingCut(y:HeapNode):void
	    {
	        var z:HeapNode = y.parent;
	        if (z != null) {
	            if (!y.mark) {
	                y.mark = true;
	            } else {
	                cut(y, z);
	                cascadingCut(z);
	            }
	        }
	    }

	    private function consolidate():void
	    {
	    	var arraySize:int = _size + 1;
	    	var array:Array = new Array(arraySize);
	    	var i:uint;
	
	        // Initialize degree array
	        for (i=0; i<arraySize; i++)
	            array[i] = null;
	
	        // Find the number of root nodes.
	        var numRoots:int = 0, d:int;
	        var x:HeapNode = _min, y:HeapNode;
	        var next:HeapNode, temp:HeapNode;
	
	        if (x != null) {
	            numRoots++;
	            x = x.right;
	
	            while (x != _min) {
	                numRoots++;
	                x = x.right;
	            }
	        }
	
	        // For each node in root list do...
	        while (numRoots > 0) {
	            // Access this node's degree..
	            d = x.degree;
	            next = x.right;
	
	            // ..and see if there's another of the same degree.
	            while (array[d] != null) {
	                // There is, make one of the nodes a child of the other.
	                y = array[d];
	
	                // Do this based on the key value.
	                if (x.key > y.key) {
	                    temp = y;
	                    y = x;
	                    x = temp;
	                }
	
	                // FiboHeapNode y disappears from root list.
	                link(y, x);
	
	                // We've handled this degree, go to next one.
	                array[d] = null;
	                d++;
	            }
	
	            // Save this node for later when we might encounter another
	            // of the same degree.
	            array[d] = x;
	
	            // Move forward through list.
	            x = next;
	            numRoots--;
	        }
	
	        // Set min to null (effectively losing the root list) and
	        // reconstruct the root list from the array entries in array[].
	        _min = null;
	
	        for (i=0; i<arraySize; i++) {
	            if (array[i] != null) {
	                // We've got a live one, add it to root list.
	                if (_min != null) {
	                    // First remove node from root list.
	                    array[i].left.right = array[i].right;
	                    array[i].right.left = array[i].left;
	
	                    // Now add to root list, again.
	                    array[i].left = _min;
	                    array[i].right = _min.right;
	                    _min.right = array[i];
	                    array[i].right.left = array[i];
	
	                    // Check if this is a new min.
	                    if (array[i].key < _min.key) {
	                        _min = array[i];
	                    }
	                } else {
	                    _min = array[i];
	                }
	            }
	        }
	    }

	    private function cut(x:HeapNode, y:HeapNode):void
	    {
	        // remove x from childlist of y and decrement degree[y]
	        x.left.right = x.right;
	        x.right.left = x.left;
	        y.degree--;
	
	        // reset y.child if necessary
	        if (y.child == x) {
	            y.child = x.right;
	        }
	
	        if (y.degree == 0) {
	            y.child = null;
	        }
	
	        // add x to root list of heap
	        x.left = _min;
	        x.right = _min.right;
	        _min.right = x;
	        x.right.left = x;
	
	        // set parent[x] to nil
	        x.parent = null;
	
	        // set mark[x] to false
	        x.mark = false;
	    }

	    private function link(y:HeapNode, x:HeapNode):void
	    {
	        // remove y from root list of heap
	        y.left.right = y.right;
	        y.right.left = y.left;
	
	        // make y a child of x
	        y.parent = x;
	
	        if (x.child == null) {
	            x.child = y;
	            y.right = y;
	            y.left = y;
	        } else {
	            y.left = x.child;
	            y.right = x.child.right;
	            x.child.right = y;
	            y.right.left = y;
	        }
	
	        // increase degree[x]
	        x.degree++;
	
	        // set mark[y] false
	        y.mark = false;
	    }
		
	} // end of class FibonacciHeap
}