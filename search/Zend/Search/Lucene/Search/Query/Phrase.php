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
 * Zend_Search_Lucene_Search_Query
 */
require_once 'Zend/Search/Lucene/Search/Query.php';

/**
 * Zend_Search_Lucene_Search_Weight_MultiTerm
 */
require_once 'Zend/Search/Lucene/Search/Weight/Phrase.php';


/**
 * A Query that matches documents containing a particular sequence of terms.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_Query_Phrase extends Zend_Search_Lucene_Search_Query
{
    /**
     * Terms to find.
     * Array of Zend_Search_Lucene_Index_Term objects.
     *
     * @var array
     */
    private $_terms;

    /**
     * Term positions (relative positions of terms within the phrase).
     * Array of integers
     *
     * @var array
     */
    private $_offsets;

    /**
     * Sets the number of other words permitted between words in query phrase.
     * If zero, then this is an exact phrase search.  For larger values this works
     * like a WITHIN or NEAR operator.
     *
     * The slop is in fact an edit-distance, where the units correspond to
     * moves of terms in the query phrase out of position.  For example, to switch
     * the order of two words requires two moves (the first move places the words
     * atop one another), so to permit re-orderings of phrases, the slop must be
     * at least two.
     * More exact matches are scored higher than sloppier matches, thus search
     * results are sorted by exactness.
     *
     * The slop is zero by default, requiring exact matches.
     *
     * @var unknown_type
     */
    private $_slop;

    /**
     * Result vector.
     * Bitset or array of document IDs
     * (depending from Bitset extension availability).
     *
     * @var mixed
     */
    private $_resVector = null;

    /**
     * Terms positions vectors.
     * Array of Arrays:
     * term1Id => (docId => array( pos1, pos2, ... ), ...)
     * term2Id => (docId => array( pos1, pos2, ... ), ...)
     *
     * @var array
     */
    private $_termsPositions = array();

    /**
     * Class constructor.  Create a new prase query.
     *
     * @param string $field    Field to search.
     * @param array  $terms    Terms to search Array of strings.
     * @param array  $offsets  Relative term positions. Array of integers.
     * @throws Zend_Search_Lucene_Exception
     */
    public function __construct($terms = null, $offsets = null, $field = null)
    {
        $this->_slop = 0;

        if (is_array($terms)) {
            $this->_terms = array();
            foreach ($terms as $termId => $termText) {
                $this->_terms[$termId] = ($field !== null)? new Zend_Search_Lucene_Index_Term($termText, $field):
                                                            new Zend_Search_Lucene_Index_Term($termText);
            }
        } else if ($terms === null) {
            $this->_terms = array();
        } else {
            throw new Zend_Search_Lucene_Exception('terms argument must be array of strings or null');
        }

        if (is_array($offsets)) {
            if (count($this->_terms) != count($offsets)) {
                throw new Zend_Search_Lucene_Exception('terms and offsets arguments must have the same size.');
            }
            $this->_offsets = $offsets;
        } else if ($offsets === null) {
            $this->_offsets = array();
            foreach ($this->_terms as $termId => $term) {
                $position = count($this->_offsets);
                $this->_offsets[$termId] = $position;
            }
        } else {
            throw new Zend_Search_Lucene_Exception('offsets argument must be array of strings or null');
        }
    }

    /**
     * Set slop
     *
     * @param integer $slop
     */
    public function setSlop($slop)
    {
        $this->_slop = $slop;
    }


    /**
     * Get slop
     *
     * @return integer
     */
    public function getSlop()
    {
        return $this->_slop;
    }


    /**
     * Adds a term to the end of the query phrase.
     * The relative position of the term is specified explicitly or the one immediately
     * after the last term added.
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param integer $position
     */
    public function addTerm(Zend_Search_Lucene_Index_Term $term, $position = null) {
        if ((count($this->_terms) != 0)&&(end($this->_terms)->field != $term->field)) {
            throw new Zend_Search_Lucene_Exception('All phrase terms must be in the same field: ' .
                                                   $term->field . ':' . $term->text);
        }

        $this->_terms[] = $term;
        if ($position !== null) {
            $this->_offsets[] = $position;
        } else if (count($this->_offsets) != 0) {
            $this->_offsets[] = end($this->_offsets) + 1;
        } else {
            $this->_offsets[] = 0;
        }
    }


    /**
     * Returns query term
     *
     * @return array
     */
    public function getTerms()
    {
        return $this->_terms;
    }


    /**
     * Set weight for specified term
     *
     * @param integer $num
     * @param Zend_Search_Lucene_Search_Weight_Term $weight
     */
    public function setWeight($num, $weight)
    {
        $this->_weights[$num] = $weight;
    }


    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param Zend_Search_Lucene $reader
     * @return Zend_Search_Lucene_Search_Weight
     */
    protected function _createWeight($reader)
    {
        return new Zend_Search_Lucene_Search_Weight_Phrase($this, $reader);
    }


    /**
     * Calculate result vector
     *
     * @param Zend_Search_Lucene $reader
     */
    private function _calculateResult($reader)
    {
        if (extension_loaded('bitset')) {
            foreach( $this->_terms as $termId=>$term ) {
                if($this->_resVector === null) {
                    $this->_resVector = bitset_from_array($reader->termDocs($term));
                } else {
                    $this->_resVector = bitset_intersection(
                                $this->_resVector,
                                bitset_from_array($reader->termDocs($term)) );
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }
        } else {
            foreach( $this->_terms as $termId=>$term ) {
                if($this->_resVector === null) {
                    $this->_resVector = array_flip($reader->termDocs($term));
                } else {
                    $termDocs = array_flip($reader->termDocs($term));
                    foreach($this->_resVector as $key=>$value) {
                        if (!isset( $termDocs[$key] )) {
                            unset( $this->_resVector[$key] );
                        }
                    }
                }

                $this->_termsPositions[$termId] = $reader->termPositions($term);
            }
        }
    }


    /**
     * Score calculator for exact phrase queries (terms sequence is fixed)
     *
     * @param integer $docId
     * @return float
     */
    public function _exactPhraseFreq($docId)
    {
        $freq = 0;

        // Term Id with lowest cardinality
        $lowCardTermId = null;

        // Calculate $lowCardTermId
        foreach ($this->_terms as $termId => $term) {
            if ($lowCardTermId === null ||
                count($this->_termsPositions[$termId][$docId]) <
                count($this->_termsPositions[$lowCardTermId][$docId]) ) {
                    $lowCardTermId = $termId;
                }
        }

        // Walk through positions of the term with lowest cardinality
        foreach ($this->_termsPositions[$lowCardTermId][$docId] as $lowCardPos) {
            // We expect phrase to be found
            $freq++;

            // Walk through other terms
            foreach ($this->_terms as $termId => $term) {
                if ($termId != $lowCardTermId) {
                    $expectedPosition = $lowCardPos +
                                            ($this->_offsets[$termId] -
                                             $this->_offsets[$lowCardTermId]);

                    if (!in_array($expectedPosition, $this->_termsPositions[$termId][$docId])) {
                        $freq--;  // Phrase wasn't found.
                        break;
                    }
                }
            }
        }

        return $freq;
    }

    /**
     * Score calculator for sloppy phrase queries (terms sequence is fixed)
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function _sloppyPhraseFreq($docId, Zend_Search_Lucene $reader)
    {
        $freq = 0;

        $phraseQueue = array();
        $phraseQueue[0] = array(); // empty phrase
        $lastTerm = null;

        // Walk through the terms to create phrases.
        foreach ($this->_terms as $termId => $term) {
            $queueSize = count($phraseQueue);
            $firstPass = true;

            // Walk through the term positions.
            // Each term position produces a set of phrases.
            foreach ($this->_termsPositions[$termId][$docId] as $termPosition ) {
                if ($firstPass) {
                    for ($count = 0; $count < $queueSize; $count++) {
                        $phraseQueue[$count][$termId] = $termPosition;
                    }
                } else {
                    for ($count = 0; $count < $queueSize; $count++) {
                        if ($lastTerm !== null &&
                            abs( $termPosition - $phraseQueue[$count][$lastTerm] -
                                 ($this->_offsets[$termId] - $this->_offsets[$lastTerm])) > $this->_slop) {
                            continue;
                        }

                        $newPhraseId = count($phraseQueue);
                        $phraseQueue[$newPhraseId]          = $phraseQueue[$count];
                        $phraseQueue[$newPhraseId][$termId] = $termPosition;
                    }

                }

                $firstPass = false;
            }
            $lastTerm = $termId;
        }


        foreach ($phraseQueue as $phrasePos) {
            $minDistance = null;

            for ($shift = -$this->_slop; $shift <= $this->_slop; $shift++) {
                $distance = 0;
                $start = reset($phrasePos) - reset($this->_offsets) + $shift;

                foreach ($this->_terms as $termId => $term) {
                    $distance += abs($phrasePos[$termId] - $this->_offsets[$termId] - $start);

                    if($distance > $this->_slop) {
                        break;
                    }
                }

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                }
            }

            if ($minDistance <= $this->_slop) {
                $freq += $reader->getSimilarity()->sloppyFreq($minDistance);
            }
        }

        return $freq;
    }


    /**
     * Score specified document
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function score($docId, $reader)
    {
        // optimize zero-term case
        if (count($this->_terms) == 0) {
            return 0;
        }

        if($this->_resVector === null) {
            $this->_calculateResult($reader);
            $this->_initWeight($reader);
        }

        if ( (extension_loaded('bitset')) ?
                bitset_in($this->_resVector, $docId) :
                isset($this->_resVector[$docId])  ) {
            if ($this->_slop == 0) {
                $freq = $this->_exactPhraseFreq($docId);
            } else {
                $freq = $this->_sloppyPhraseFreq($docId, $reader);
            }

/*
            return $reader->getSimilarity()->tf($freq) *
                   $this->_weight->getValue() *
                   $reader->norm($docId, reset($this->_terms)->field);
*/
            if ($freq != 0) {
                $tf = $reader->getSimilarity()->tf($freq);
                $weight = $this->_weight->getValue();
                $norm = $reader->norm($docId, reset($this->_terms)->field);

                return $tf*$weight*$norm;
            }
        } else {
            return 0;
        }
    }
}

