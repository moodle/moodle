<?php

namespace SimpleSAML\Module\statistics;

use SimpleSAML\Configuration;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class RatioDataset extends StatDataset
{
    /**
     * Constructor
     *
     * @param \SimpleSAML\Configuration $statconfig
     * @param \SimpleSAML\Configuration $ruleconfig
     * @param string $ruleid
     * @param string $timeres
     * @param int $fileslot
     */
    public function __construct(Configuration $statconfig, Configuration $ruleconfig, $ruleid, $timeres, $fileslot)
    {
        parent::__construct($statconfig, $ruleconfig, $ruleid, $timeres, $fileslot);
    }


    /**
     * @return void
     */
    public function aggregateSummary()
    {
        /**
         * Aggregate summary table from dataset. To be used in the table view.
         */
        $this->summary = [];
        $noofvalues = [];
        foreach ($this->results as $slot => $res) {
            foreach ($res as $key => $value) {
                if (array_key_exists($key, $this->summary)) {
                    $this->summary[$key] += $value;
                    if ($value > 0) {
                        $noofvalues[$key]++;
                    }
                } else {
                    $this->summary[$key] = $value;
                    if ($value > 0) {
                        $noofvalues[$key] = 1;
                    } else {
                        $noofvalues[$key] = 0;
                    }
                }
            }
        }

        foreach ($this->summary as $key => $val) {
            $this->summary[$key] = $this->divide($this->summary[$key], $noofvalues[$key]);
        }

        asort($this->summary);
        $this->summary = array_reverse($this->summary, true);
    }


    /**
     * @param string $k
     * @param array $a
     * @return int
     */
    private function ag($k, array $a)
    {
        if (array_key_exists($k, $a)) {
            return $a[$k];
        }
        return 0;
    }


    /**
     * @param int $v1
     * @param int $v2
     * @return int|float
     */
    private function divide($v1, $v2)
    {
        if ($v2 == 0) {
            return 0;
        }
        return ($v1 / $v2);
    }


    /**
     * @param array $result1
     * @param array $result2
     * @return array
     */
    public function combine(array $result1, array $result2)
    {
        $combined = [];

        foreach ($result2 as $tick => $val) {
            $combined[$tick] = [];
            foreach ($val as $index => $num) {
                $combined[$tick][$index] = $this->divide(
                    $this->ag($index, $result1[$tick]),
                    $this->ag($index, $result2[$tick])
                );
            }
        }
        return $combined;
    }


    /**
     * @return array
     */
    public function getPieData()
    {
        return [];
    }
}
