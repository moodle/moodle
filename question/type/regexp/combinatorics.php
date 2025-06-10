<?php
// @codingStandardsIgnoreStart
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Math_Combinatorics
 *
 * Math_Combinatorics provides the ability to find all combinations and
 * permutations given an set and a subset size.  Associative arrays are
 * preserved.
 *
 * PHP version 5
 *
 * @package    qtype_regexp
 * @author     David Sanders <shangxiao@php.net>
 * @copyright  David Sanders <shangxiao@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pyrus.sourceforge.net/Math_Combinatorics.html
 */

/**
 * Math_Combinatorics
 * @package    qtype_regexp
 * @copyright  David Sanders <shangxiao@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 */
class Math_Combinatorics
{
    /**
     * List of pointers that record the current combination.
     *
     * @var array
     */
    private $_pointers = array();

    /**
     * Find all combinations given a set and a subset size.
     *
     * @param  array $set          Parent set
     * @param  int   $subset_size  Subset size
     * @return array An array of combinations
     */
    public function combinations(array $set, $subset_size = null)
    {
        $set_size = count($set);

        if (is_null($subset_size)) {
            $subset_size = $set_size;
        }

        if ($subset_size >= $set_size) {
            return array($set);
        } else if ($subset_size == 1) {
            return array_chunk($set, 1);
        } else if ($subset_size == 0) {
            return array();
        }

        $combinations = array();
        $set_keys = array_keys($set);
        $this->_pointers = array_slice(array_keys($set_keys), 0, $subset_size);

        $combinations[] = $this->_getCombination($set);
        while ($this->_advancePointers($subset_size - 1, $set_size - 1)) {
            $combinations[] = $this->_getCombination($set);
        }

        return $combinations;
    }

    /**
     * Recursive function used to advance the list of 'pointers' that record the
     * current combination.
     *
     * @param  int $pointer_number The ID of the pointer that is being advanced
     * @param  int $limit          Pointer limit
     * @return bool True if a pointer was advanced, false otherwise
     */
    private function _advancePointers($pointer_number, $limit)
    {
        if ($pointer_number < 0) {
            return false;
        }

        if ($this->_pointers[$pointer_number] < $limit) {
            $this->_pointers[$pointer_number]++;
            return true;
        } else {
            if ($this->_advancePointers($pointer_number - 1, $limit - 1)) {
                $this->_pointers[$pointer_number] =
                    $this->_pointers[$pointer_number - 1] + 1;
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Get the current combination.
     *
     * @param  array $set The parent set
     * @return array The current combination
     */
    private function _getCombination($set)
    {
        $set_keys = array_keys($set);

        $combination = array();

        foreach ($this->_pointers as $pointer) {
            $combination[$set_keys[$pointer]] = $set[$set_keys[$pointer]];
        }

        return $combination;
    }

    /**
     * Find all permutations given a set and a subset size.
     *
     * @param  array $set          Parent set
     * @param  int   $subset_size  Subset size
     * @return array An array of permutations
     */
    public function permutations(array $set, $subset_size = null)
    {
        $combinations = $this->combinations($set, $subset_size);
        $permutations = array();

        foreach ($combinations as $combination) {
            $permutations = array_merge($permutations,
                                        $this->_findPermutations($combination));
        }

        return $permutations;
    }

    /**
     * Recursive function to find the permutations of the current combination.
     *
     * @param array $set Current combination set
     * @return array Permutations of the current combination
     */
    private function _findPermutations($set)
    {
        if (count($set) <= 1) {
            return array($set);
        }

        $permutations = array();

        list($key, $val) = $this->array_shift_assoc($set);
        $sub_permutations = $this->_findPermutations($set);

        foreach ($sub_permutations as $permutation) {
            $permutations[] = array_merge(array($key => $val), $permutation);
        }

        $set[$key] = $val;

        $start_key = $key;

        $key = $this->_firstKey($set);
        while ($key != $start_key) {

            list($key, $val) = $this->array_shift_assoc($set);
            $sub_permutations = $this->_findPermutations($set);

            foreach ($sub_permutations as $permutation) {
                $permutations[] = array_merge(array($key => $val), $permutation);
            }

            $set[$key] = $val;
            $key = $this->_firstKey($set);
        }

        return $permutations;
    }

    /**
     * Associative version of array_shift()
     *
     * @param  array $array Reference to the array to shift
     * @return array Array with 1st element as the shifted key and the 2nd
     *               element as the shifted value
     */
    public function array_shift_assoc(array &$array)
    {
        foreach ($array as $key => $val) {
            unset($array[$key]);
            break;
        }
        return array($key, $val);
    }

    /**
     * Get the first key of an associative array
     *
     * @param  array $array Array to find the first key
     * @return mixed The first key of the given array
     */
    private function _firstKey($array)
    {
        foreach ($array as $key => $val) {
            break;
        }
        return $key;
    }
}
// @codingStandardsIgnoreEnd