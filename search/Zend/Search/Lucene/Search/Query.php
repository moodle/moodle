<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Search_Lucene_Document_Html */
require_once 'Zend/Search/Lucene/Document/Html.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Search_Lucene_Search_Query
{

    /**
     * query boost factor
     *
     * @var float
     */
    private $_boost = 1;

    /**
     * Query weight
     *
     * @var Zend_Search_Lucene_Search_Weight
     */
    protected $_weight = null;

    /**
     * Current highlight color
     *
     * @var integer
     */
    private $_currentColorIndex = 0;

    /**
     * List of colors for text highlighting
     *
     * @var array
     */
    private $_highlightColors = array('#66ffff', '#ff66ff', '#ffff66',
                                      '#ff8888', '#88ff88', '#8888ff',
                                      '#88dddd', '#dd88dd', '#dddd88',
                                      '#aaddff', '#aaffdd', '#ddaaff', '#ddffaa', '#ffaadd', '#ffddaa');


    /**
     * Gets the boost for this clause.  Documents matching
     * this clause will (in addition to the normal weightings) have their score
     * multiplied by boost.   The boost is 1.0 by default.
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->_boost;
    }

    /**
     * Sets the boost for this query clause to $boost.
     *
     * @param float $boost
     */
    public function setBoost($boost)
    {
        $this->_boost = $boost;
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param Zend_Search_Lucene_Interface $reader
     * @return float
     */
    abstract public function score($docId, Zend_Search_Lucene_Interface $reader);

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     */
    abstract public function matchedDocs();

    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * Query specific implementation
     *
     * @param Zend_Search_Lucene_Interface $reader
     */
    abstract public function execute(Zend_Search_Lucene_Interface $reader);

    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param Zend_Search_Lucene_Interface $reader
     * @return Zend_Search_Lucene_Search_Weight
     */
    abstract public function createWeight(Zend_Search_Lucene_Interface $reader);

    /**
     * Constructs an initializes a Weight for a _top-level_query_.
     *
     * @param Zend_Search_Lucene_Interface $reader
     */
    protected function _initWeight(Zend_Search_Lucene_Interface $reader)
    {
        // Check, that it's a top-level query and query weight is not initialized yet.
        if ($this->_weight !== null) {
            return $this->_weight;
        }

        $this->createWeight($reader);
        $sum = $this->_weight->sumOfSquaredWeights();
        $queryNorm = $reader->getSimilarity()->queryNorm($sum);
        $this->_weight->normalize($queryNorm);
    }

    /**
     * Re-write query into primitive queries in the context of specified index
     *
     * @param Zend_Search_Lucene_Interface $index
     * @return Zend_Search_Lucene_Search_Query
     */
    abstract public function rewrite(Zend_Search_Lucene_Interface $index);

    /**
     * Optimize query in the context of specified index
     *
     * @param Zend_Search_Lucene_Interface $index
     * @return Zend_Search_Lucene_Search_Query
     */
    abstract public function optimize(Zend_Search_Lucene_Interface $index);

    /**
     * Reset query, so it can be reused within other queries or
     * with other indeces
     */
    public function reset()
    {
        $this->_weight = null;
    }


    /**
     * Print a query
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Return query terms
     *
     * @return array
     */
    abstract public function getQueryTerms();

    /**
     * Get highlight color and shift to next
     *
     * @param integer &$colorIndex
     * @return string
     */
    protected function _getHighlightColor(&$colorIndex)
    {
        $color = $this->_highlightColors[$colorIndex++];

        $colorIndex %= count($this->_highlightColors);

        return $color;
    }

    /**
     * Highlight query terms
     *
     * @param integer &$colorIndex
     * @param Zend_Search_Lucene_Document_Html $doc
     */
    abstract public function highlightMatchesDOM(Zend_Search_Lucene_Document_Html $doc, &$colorIndex);

    /**
     * Highlight matches in $inputHTML
     *
     * @param string $inputHTML
     * @return string
     */
    public function highlightMatches($inputHTML)
    {
        $doc = Zend_Search_Lucene_Document_Html::loadHTML($inputHTML);

        $colorIndex = 0;
        $this->highlightMatchesDOM($doc, $colorIndex);

        return $doc->getHTML();
    }
}

