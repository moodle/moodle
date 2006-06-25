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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Calculate query weights and build query scorers.
 *
 * A Weight is constructed by a query Query->createWeight().
 * The sumOfSquaredWeights() method is then called on the top-level
 * query to compute the query normalization factor Similarity->queryNorm(float).
 * This factor is then passed to normalize(float).  At this point the weighting
 * is complete.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Search_Lucene_Search_Weight
{
    /**
     * The weight for this query.
     *
     * @return float
     */
    abstract public function getValue();

    /**
     * The sum of squared weights of contained query clauses.
     *
     * @return float
     */
    abstract public function sumOfSquaredWeights();

    /**
     * Assigns the query normalization factor to this.
     *
     * @param $norm
     */
    abstract public function normalize($norm);
}

