<?php

namespace SimpleSAML\Module\statistics;

use SimpleSAML\Configuration;
use SimpleSAML\Module;
use SimpleSAML\Utils\Arrays;
use SimpleSAML\XHTML\Template;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class StatDataset
{
    /** @var \SimpleSAML\Configuration */
    protected $statconfig;

    /** @var \SimpleSAML\Configuration */
    protected $ruleconfig;

    /** @var \SimpleSAML\Configuration */
    protected $timeresconfig;

    /** @var string */
    protected $ruleid;

    /** @var int */
    protected $fileslot;

    /** @var string */
    protected $timeres;

    /** @var string */
    protected $delimiter;

    /** @var array */
    protected $results;

    /** @var array */
    protected $summary;

    /** @var int */
    protected $max;

    /** @var \SimpleSAML\Module\statistics\DateHandler */
    protected $datehandlerFile;

    /** @var \SimpleSAML\Module\statistics\DateHandler */
    protected $datehandlerTick;


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
        $this->statconfig = $statconfig;
        $this->ruleconfig = $ruleconfig;

        $timeresconfigs = $statconfig->getConfigItem('timeres');
        $this->timeresconfig = $timeresconfigs->getConfigItem($timeres);

        $this->ruleid = $ruleid;
        $this->fileslot = $fileslot;
        $this->timeres = $timeres;

        $this->delimiter = '_';
        $this->max = 0;
        $this->results = [];
        $this->summary = [];

        $this->datehandlerTick = new DateHandler($this->statconfig->getValue('offset', 0));
        if ($this->timeresconfig->getValue('customDateHandler', 'default') === 'month') {
            $this->datehandlerFile = new DateHandlerMonth(0);
        } else {
            $this->datehandlerFile = $this->datehandlerTick;
        }

        $this->loadData();
    }


    /**
     * @return int
     */
    public function getFileSlot()
    {
        return $this->fileslot;
    }


    /**
     * @return string
     */
    public function getTimeRes()
    {
        return $this->timeres;
    }


    /**
     * @param string $delimiter
     * @return void
     */
    public function setDelimiter($delimiter = '_')
    {
        if (empty($delimiter)) {
            $delimiter = '_';
        }
        $this->delimiter = $delimiter;
    }


    /**
     * @return string|null
     */
    public function getDelimiter()
    {
        if ($this->delimiter === '_') {
            return null;
        }
        return $this->delimiter;
    }


    /**
     * @return void
     */
    public function calculateMax()
    {
        $maxvalue = 0;
        foreach ($this->results as $slot => &$res) {
            if (!array_key_exists($this->delimiter, $res)) {
                $res[$this->delimiter] = 0;
            }
            $maxvalue = max($res[$this->delimiter], $maxvalue);
        }
        $this->max = Graph\GoogleCharts::roof($maxvalue);
    }


    /**
     * @return array
     */
    public function getDebugData()
    {
        $debugdata = [];

        $slotsize = $this->timeresconfig->getValue('slot');
        $dateformat_intra = $this->timeresconfig->getValue('dateformat-intra');

        foreach ($this->results as $slot => &$res) {
            $debugdata[$slot] = [
                $this->datehandlerTick->prettyDateSlot($slot, $slotsize, $dateformat_intra),
                $res[$this->delimiter]
            ];
        }
        return $debugdata;
    }


    /**
     * @return void
     */
    public function aggregateSummary()
    {
        // aggregate summary table from dataset. To be used in the table view
        $this->summary = [];
        foreach ($this->results as $slot => $res) {
            foreach ($res as $key => $value) {
                if (array_key_exists($key, $this->summary)) {
                    $this->summary[$key] += $value;
                } else {
                    $this->summary[$key] = $value;
                }
            }
        }
        asort($this->summary);
        $this->summary = array_reverse($this->summary, true);
    }


    /**
     * @return array
     */
    public function getTopDelimiters()
    {
        // create a list of delimiter keys that has the highest total summary in this period
        $topdelimiters = [];
        $maxdelimiters = 4;
        $i = 0;
        foreach ($this->summary as $key => $value) {
            if ($key !== '_') {
                $topdelimiters[] = $key;
            }
            if ($i++ >= $maxdelimiters) {
                break;
            }
        }
        return $topdelimiters;
    }


    /**
     * @return array
     */
    public function availDelimiters()
    {
        $availDelimiters = [];
        foreach ($this->summary as $key => $value) {
            $availDelimiters[$key] = 1;
        }
        return array_keys($availDelimiters);
    }


    /**
     * @return array
     */
    public function getPieData()
    {
        $piedata = [];
        $sum = 0;
        $topdelimiters = $this->getTopDelimiters();

        foreach ($topdelimiters as $td) {
            $sum += $this->summary[$td];
            $piedata[] = number_format(100 * $this->summary[$td] / $this->summary['_'], 2);
        }
        $piedata[] = number_format(100 - 100 * ($sum / $this->summary['_']), 2);
        return $piedata;
    }


    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }


    /**
     * @return array
     */
    public function getSummary()
    {
        return $this->summary;
    }


    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }


    /**
     * @return array
     */
    public function getAxis()
    {
        $slotsize = $this->timeresconfig->getValue('slot');
        $dateformat_intra = $this->timeresconfig->getValue('dateformat-intra');
        $axislabelint = $this->timeresconfig->getValue('axislabelint');

        $axis = [];
        $axispos = [];
        $xentries = count($this->results);
        $lastslot = 0;
        $i = 0;

        foreach ($this->results as $slot => $res) {
            $slot = intval($slot);

            // check if there should be an axis here...
            if ($slot % $axislabelint == 0) {
                $axis[] = $this->datehandlerTick->prettyDateSlot($slot, $slotsize, $dateformat_intra);
                $axispos[] = (($i) / ($xentries - 1));
            }

            $lastslot = $slot;
            $i++;
        }

        $axis[] = $this->datehandlerTick->prettyDateSlot($lastslot + 1, $slotsize, $dateformat_intra);

        return ['axis' => $axis, 'axispos' => $axispos];
    }


    /**
     * Walk through dataset to get percent values from max into dataset[].
     * @return array
     */
    public function getPercentValues()
    {
        $i = 0;
        $dataset = [];
        foreach ($this->results as $slot => $res) {
            if (array_key_exists($this->delimiter, $res)) {
                if ($res[$this->delimiter] === null) {
                    $dataset[] = -1;
                } else {
                    $dataset[] = number_format(100 * $res[$this->delimiter] / $this->max, 2);
                }
            } else {
                $dataset[] = '0';
            }
            $i++;
        }

        return $dataset;
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function getDelimiterPresentation()
    {
        $config = Configuration::getInstance();
        $t = new Template($config, 'statistics:statistics.tpl.php');

        $availdelimiters = $this->availDelimiters();

        // create a delimiter presentation filter for this rule...
        if ($this->ruleconfig->hasValue('fieldPresentation')) {
            $fieldpresConfig = $this->ruleconfig->getConfigItem('fieldPresentation');
            $classname = Module::resolveClass(
                $fieldpresConfig->getValue('class'),
                'Statistics\FieldPresentation'
            );
            if (!class_exists($classname)) {
                throw new \Exception('Could not find field presentation plugin [' . $classname . ']: No class found');
            }
            $presentationHandler = new $classname($availdelimiters, $fieldpresConfig->getValue('config'), $t);

            return $presentationHandler->getPresentation();
        }

        return [];
    }


    /**
     * @return array
     */
    public function getDelimiterPresentationPie()
    {
        $topdelimiters = $this->getTopDelimiters();
        $delimiterPresentation = $this->getDelimiterPresentation();

        $pieaxis = [];
        foreach ($topdelimiters as $key) {
            $keyName = $key;
            if (array_key_exists($key, $delimiterPresentation)) {
                $keyName = $delimiterPresentation[$key];
            }
            $pieaxis[] = $keyName;
        }
        $pieaxis[] = 'Others';
        return $pieaxis;
    }


    /**
     * @return void
     */
    public function loadData()
    {
        $statdir = $this->statconfig->getValue('statdir');
        $resarray = [];
        $rules = Arrays::arrayize($this->ruleid);
        foreach ($rules as $rule) {
            // Get file and extract results.
            $resultFileName = $statdir . '/' . $rule . '-' . $this->timeres . '-' . $this->fileslot . '.stat';
            if (!file_exists($resultFileName)) {
                throw new \Exception('Aggregated statitics file [' . $resultFileName . '] not found.');
            }
            if (!is_readable($resultFileName)) {
                throw new \Exception('Could not read statitics file [' . $resultFileName . ']. Bad file permissions?');
            }
            $resultfile = file_get_contents($resultFileName);
            $newres = unserialize($resultfile);
            if (empty($newres)) {
                throw new \Exception('Aggregated statistics in file [' . $resultFileName . '] was empty.');
            }
            $resarray[] = $newres;
        }

        $combined = $resarray[0];
        $count = count($resarray);
        if ($count > 1) {
            for ($i = 1; $i < $count; $i++) {
                $combined = $this->combine($combined, $resarray[$i]);
            }
        }
        $this->results = $combined;
    }


    /**
     * @return array
     */
    public function combine(array $combined, array $resarray)
    {
        return array_merge($combined, $resarray);
    }
}
