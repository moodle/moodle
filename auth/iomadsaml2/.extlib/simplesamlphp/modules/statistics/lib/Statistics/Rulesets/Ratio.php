<?php

namespace SimpleSAML\Module\statistics\Statistics\Rulesets;

use SimpleSAML\Configuration;
use SimpleSAML\Module\statistics\RatioDataset;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class Ratio extends BaseRule
{
    /** @var \SimpleSAML\Module\statistics\Statistics\Rulesets\BaseRule $refrule1 */
    protected $refrule1;

    /** @var \SimpleSAML\Module\statistics\Statistics\Rulesets\BaseRule $refrule2 */
    protected $refrule2;


    /**
     * Constructor
     *
     * @param \SimpleSAML\Configuration $statconfig
     * @param \SimpleSAML\Configuration $ruleconfig
     * @param string $ruleid
     * @param array $available
     */
    public function __construct(Configuration $statconfig, Configuration $ruleconfig, $ruleid, array $available)
    {
        parent::__construct($statconfig, $ruleconfig, $ruleid, $available);

        $refNames = $this->ruleconfig->getArray('ref');

        $statrulesConfig = $this->statconfig->getConfigItem('statrules');

        $statruleConfig1 = $statrulesConfig->getConfigItem($refNames[0]);
        $statruleConfig2 = $statrulesConfig->getConfigItem($refNames[1]);

        $this->refrule1 = new BaseRule($this->statconfig, $statruleConfig1, $refNames[0], $available);
        $this->refrule2 = new BaseRule($this->statconfig, $statruleConfig2, $refNames[1], $available);
    }


    /**
     * @return array
     */
    public function availableTimeRes()
    {
        return $this->refrule1->availableTimeRes();
    }


    /**
     * @param string $timeres
     * @return array
     */
    public function availableFileSlots($timeres)
    {
        return $this->refrule1->availableFileSlots($timeres);
    }


    /**
     * @param string $preferTimeRes
     * @return string
     */
    protected function resolveTimeRes($preferTimeRes)
    {
        return $this->refrule1->resolveTimeRes($preferTimeRes);
    }


    /**
     * @param string $timeres
     * @param string $preferTime
     * @return int
     */
    protected function resolveFileSlot($timeres, $preferTime)
    {
        return $this->refrule1->resolveFileSlot($timeres, $preferTime);
    }


    /**
     * @param string $timeres
     * @param string $preferTime
     * @return array
     */
    public function getTimeNavigation($timeres, $preferTime)
    {
        return $this->refrule1->getTimeNavigation($timeres, $preferTime);
    }


    /**
     * @param string $preferTimeRes
     * @param string $preferTime
     * @return \SimpleSAML\Module\statistics\RatioDataset
     */
    public function getDataSet($preferTimeRes, $preferTime)
    {
        $timeres = $this->resolveTimeRes($preferTimeRes);
        $fileslot = $this->resolveFileSlot($timeres, $preferTime);

        $refNames = $this->ruleconfig->getArray('ref');

        $dataset = new RatioDataset(
            $this->statconfig,
            $this->ruleconfig,
            $refNames,
            $timeres,
            $fileslot
        );
        return $dataset;
    }
}
