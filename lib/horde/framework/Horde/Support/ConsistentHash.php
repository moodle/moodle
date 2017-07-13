<?php
/**
 * For a thorough description of consistent hashing, see
 * http://www.spiteful.com/2008/03/17/programmers-toolbox-part-3-consistent-hashing/,
 * and also the original paper:
 * http://www8.org/w8-papers/2a-webserver/caching/paper2.html
 *
 * Copyright 2007-2014 Horde LLC (http://www.horde.org/)
 *
 * @todo Ideas for future enhancement:
 *   - provide a callback when a point is moved on the circle, so that the
 *     calling code can take an action (say, transferring data).
 *
 * @category Horde
 * @package  Support
 * @license  http://www.horde.org/licenses/bsd
 */
class Horde_Support_ConsistentHash
{
    /**
     * Number of times to put each node into the hash circle per weight value.
     * @var integer
     */
    protected $_numberOfReplicas = 100;

    /**
     * Array representing our circle
     * @var array
     */
    protected $_circle = array();

    /**
     * Numeric indices into the circle by hash position
     * @var array
     */
    protected $_pointMap = array();

    /**
     * Number of points on the circle
     * @var integer
     */
    protected $_pointCount = 0;

    /**
     * Array of nodes.
     * @var array
     */
    protected $_nodes = array();

    /**
     * Number of nodes
     * @var integer
     */
    protected $_nodeCount = 0;

    /**
     * Create a new consistent hash, with initial $nodes at $numberOfReplicas
     *
     * @param array    $nodes             Initial array of nodes to add at $weight.
     * @param integer  $weight            The weight for the initial node list.
     * @param integer  $numberOfReplicas  The number of points on the circle to generate for each node.
     */
    public function __construct($nodes = array(), $weight = 1, $numberOfReplicas = 100)
    {
        $this->_numberOfReplicas = $numberOfReplicas;
        $this->addNodes($nodes, $weight);
    }

    /**
     * Get the primary node for $key.
     *
     * @param string $key  The key to look up.
     *
     * @param string  The primary node for $key.
     */
    public function get($key)
    {
        $nodes = $this->getNodes($key, 1);
        if (!$nodes) {
            throw new Exception('No nodes found');
        }
        return $nodes[0];
    }

    /**
     * Get an ordered list of nodes for $key.
     *
     * @param string   $key    The key to look up.
     * @param integer  $count  The number of nodes to look up.
     *
     * @return array  An ordered array of nodes.
     */
    public function getNodes($key, $count = 5)
    {
        // Degenerate cases
        if ($this->_nodeCount < $count) {
            throw new Exception('Not enough nodes (have ' . $this->_nodeCount . ', ' . $count . ' requested)');
        }
        if ($this->_nodeCount == 0) {
            return array();
        }

        // Simple case
        if ($this->_nodeCount == 1) {
            return array($this->_nodes[0]['n']);
        }

        $hash = $this->hash(serialize($key));

        // Find the first point on the circle greater than $hash by binary search.
        $low = 0;
        $high = $this->_pointCount - 1;
        $index = null;
        while (true) {
            $mid = (int)(($low + $high) / 2);
            if ($mid == $this->_pointCount) {
                $index = 0;
                break;
            }

            $midval = $this->_pointMap[$mid];
            $midval1 = ($mid == 0) ? 0 : $this->_pointMap[$mid - 1];
            if ($midval1 < $hash && $hash <= $midval) {
                $index = $mid;
                break;
            }

            if ($midval > $hash) {
                $high = $mid - 1;
            } else {
                $low = $mid + 1;
            }

            if ($low > $high) {
                $index = 0;
                break;
            }
        }

        $nodes = array();
        while (count($nodes) < $count) {
            $nodeIndex = $this->_pointMap[$index++ % $this->_pointCount];
            $nodes[$nodeIndex] = $this->_nodes[$this->_circle[$nodeIndex]]['n'];
        }
        return array_values($nodes);
    }

    /**
     * Add $node with weight $weight
     *
     * @param mixed $node
     */
    public function add($node, $weight = 1)
    {
        // Delegate to addNodes so that the circle is only regenerated once when
        // adding multiple nodes.
        $this->addNodes(array($node), $weight);
    }

    /**
     * Add multiple nodes to the hash with the same weight.
     *
     * @param array    $nodes   An array of nodes.
     * @param integer  $weight  The weight to add the nodes with.
     */
    public function addNodes($nodes, $weight = 1)
    {
        foreach ($nodes as $node) {
            $this->_nodes[] = array('n' => $node, 'w' => $weight);
            $this->_nodeCount++;

            $nodeIndex = $this->_nodeCount - 1;
            $nodeString = serialize($node);

            $numberOfReplicas = (int)($weight * $this->_numberOfReplicas);
            for ($i = 0; $i < $numberOfReplicas; $i++) {
                $this->_circle[$this->hash($nodeString . $i)] = $nodeIndex;
            }
        }

        $this->_updateCircle();
    }

    /**
     * Remove $node from the hash.
     *
     * @param mixed $node
     */
    public function remove($node)
    {
        $nodeIndex = null;
        $nodeString = serialize($node);

        // Search for the node in the node list
        foreach (array_keys($this->_nodes) as $i) {
            if ($this->_nodes[$i]['n'] === $node) {
                $nodeIndex = $i;
                break;
            }
        }

        if (is_null($nodeIndex)) {
            throw new InvalidArgumentException('Node was not in the hash');
        }

        // Remove all points from the circle
        $numberOfReplicas = (int)($this->_nodes[$nodeIndex]['w'] * $this->_numberOfReplicas);
        for ($i = 0; $i < $numberOfReplicas; $i++) {
            unset($this->_circle[$this->hash($nodeString . $i)]);
        }
        $this->_updateCircle();

        // Unset the node from the node list
        unset($this->_nodes[$nodeIndex]);
        $this->_nodeCount--;
    }

    /**
     * Expose the hash function for testing, probing, and extension.
     *
     * @param string $key
     *
     * @return string Hash value
     */
    public function hash($key)
    {
        return 'h' . substr(hash('md5', $key), 0, 8);
    }

    /**
     * Maintain the circle and arrays of points.
     */
    protected function _updateCircle()
    {
        // Sort the circle
        ksort($this->_circle);

        // Now that the hashes are sorted, generate numeric indices into the
        // circle.
        $this->_pointMap = array_keys($this->_circle);
        $this->_pointCount = count($this->_pointMap);
    }

}
