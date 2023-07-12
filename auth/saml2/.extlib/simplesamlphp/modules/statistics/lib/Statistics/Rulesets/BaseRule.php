<?php

namespace SimpleSAML\Module\statistics\Statistics\Rulesets;

use SimpleSAML\Configuration;
use SimpleSAML\Module\statistics\DateHandler;
use SimpleSAML\Module\statistics\DateHandlerMonth;
use SimpleSAML\Module\statistics\StatDataset;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class BaseRule
{
    /** @var \SimpleSAML\Configuration */
    protected $statconfig;

    /** @var \SimpleSAML\Configuration */
    protected $ruleconfig;

    /** @var string */
    protected $ruleid = '';

    /** @var array */
    protected $available = [];


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
        $this->statconfig = $statconfig;
        $this->ruleconfig = $ruleconfig;
        $this->ruleid = $ruleid;

        if (array_key_exists($ruleid, $available)) {
            $this->available = $available[$ruleid];
        }
    }


    /**
     * @return string
     */
    public function getRuleID()
    {
        return $this->ruleid;
    }


    /**
     * @return array
     */
    public function availableTimeRes()
    {
        $timeresConfigs = $this->statconfig->getValue('timeres');
        $available_times = [];
        foreach ($timeresConfigs as $tres => $tresconfig) {
            if (array_key_exists($tres, $this->available)) {
                $available_times[$tres] = $tresconfig['name'];
            }
        }
        return $available_times;
    }


    /**
     * @param string $timeres
     * @return array
     */
    public function availableFileSlots($timeres)
    {
        $timeresConfigs = $this->statconfig->getValue('timeres');
        $timeresConfig = $timeresConfigs[$timeres];

        if (isset($timeresConfig['customDateHandler']) && $timeresConfig['customDateHandler'] == 'month') {
            $datehandler = new DateHandlerMonth(0);
        } else {
            $datehandler = new DateHandler($this->statconfig->getValue('offset', 0));
        }

        /*
         * Get list of avaiable times in current file (rule)
         */
        $available_times = [];
        foreach ($this->available[$timeres] as $slot) {
            $available_times[$slot] = $datehandler->prettyHeader(
                $slot,
                $slot + 1,
                $timeresConfig['fileslot'],
                $timeresConfig['dateformat-period']
            );
        }
        return $available_times;
    }


    /**
     * @param string $preferTimeRes
     * @return string
     */
    protected function resolveTimeRes($preferTimeRes)
    {
        $timeresavailable = array_keys($this->available);
        $timeres = $timeresavailable[0];

        // Then check if the user have provided one that is valid
        if (in_array($preferTimeRes, $timeresavailable, true)) {
            $timeres = $preferTimeRes;
        }
        return $timeres;
    }


    /**
     * @param string $timeres
     * @param string $preferTime
     * @return int
     */
    protected function resolveFileSlot($timeres, $preferTime)
    {
        // Get which time (fileslot) to use.. First get a default, which is the most recent one.
        $fileslot = $this->available[$timeres][count($this->available[$timeres]) - 1];
        // Then check if the user have provided one.
        if (in_array($preferTime, $this->available[$timeres], true)) {
            $fileslot = $preferTime;
        }
        return $fileslot;
    }


    /**
     * @param string $timeres
     * @param string $preferTime
     * @return array
     */
    public function getTimeNavigation($timeres, $preferTime)
    {
        $fileslot = $this->resolveFileSlot($timeres, $preferTime);

        // Extract previous and next time slots...
        $available_times_prev = null;
        $available_times_next = null;

        $timeslots = array_values($this->available[$timeres]);
        sort($timeslots, SORT_NUMERIC);
        $timeslotindex = array_flip($timeslots);

        if ($timeslotindex[$fileslot] > 0) {
            $available_times_prev = $timeslots[$timeslotindex[$fileslot] - 1];
        }
        if ($timeslotindex[$fileslot] < (count($timeslotindex) - 1)) {
            $available_times_next = $timeslots[$timeslotindex[$fileslot] + 1];
        }
        return ['prev' => $available_times_prev, 'next' => $available_times_next];
    }


    /**
     * @param string $preferTimeRes
     * @param string $preferTime
     * @return \SimpleSAML\Module\statistics\StatDataset
     */
    public function getDataSet($preferTimeRes, $preferTime)
    {
        $timeres = $this->resolveTimeRes($preferTimeRes);
        $fileslot = $this->resolveFileSlot($timeres, $preferTime);
        $dataset = new StatDataset(
            $this->statconfig,
            $this->ruleconfig,
            $this->ruleid,
            $timeres,
            $fileslot
        );
        return $dataset;
    }
}
