<?php

namespace SimpleSAML\Module\statistics;

use SimpleSAML\Configuration;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class Ruleset
{
    /** \SimpleSAML\Configuration */
    private $statconfig;

    /** @var array */
    private $availrulenames;

    /** @var array */
    private $availrules;

    /** @var array */
    private $available;


    /**
     * Constructor
     *
     * @param \SimpleSAML\Configuration $statconfig
     */
    public function __construct(Configuration $statconfig)
    {
        $this->statconfig = $statconfig;
        $this->init();
    }


    /**
     * @return void
     */
    private function init()
    {
        $statdir = $this->statconfig->getValue('statdir');
        $statrules = $this->statconfig->getValue('statrules');
        $timeres = $this->statconfig->getValue('timeres');

        /*
         * Walk through file lists, and get available [rule][fileslot]...
         */
        if (!is_dir($statdir)) {
            throw new \Exception('Statisics output directory [' . $statdir . '] does not exist.');
        }
        $filelist = scandir($statdir);
        $this->available = [];
        foreach ($filelist as $file) {
            if (preg_match('/([a-z0-9_]+)-([a-z0-9_]+)-([0-9]+)\.stat/', $file, $matches)) {
                if (array_key_exists($matches[1], $statrules)) {
                    if (array_key_exists($matches[2], $timeres)) {
                        $this->available[$matches[1]][$matches[2]][] = $matches[3];
                    }
                }
            }
        }
        if (empty($this->available)) {
            throw new \Exception('No aggregated statistics files found in [' . $statdir . ']');
        }

        /**
         * Create array with information about available rules..
         */
        $this->availrules = array_keys($statrules);
        $available_rules = [];
        foreach ($this->availrules as $key) {
            $available_rules[$key] = ['name' => $statrules[$key]['name'], 'descr' => $statrules[$key]['descr']];
        }
        $this->availrulenames = $available_rules;
    }


    /**
     * @return array
     */
    public function availableRules()
    {
        return $this->availrules;
    }


    /**
     * @return array
     */
    public function availableRulesNames()
    {
        return $this->availrulenames;
    }


    /**
     * Resolve which rule is selected. Taking user preference and checks if it exists.
     *
     * @param string|null $preferRule
     * @return string|null
     */
    private function resolveSelectedRule($preferRule = null)
    {
        $rule = $this->statconfig->getString('default', $this->availrules[0]);
        if (!empty($preferRule)) {
            if (in_array($preferRule, $this->availrules, true)) {
                $rule = $preferRule;
            }
        }
        return $rule;
    }


    /**
     * @param string|null $preferRule
     * @return \SimpleSAML\Module\statistics\Statistics\Rulesets\BaseRule
     */
    public function getRule($preferRule = null)
    {
        $rule = $this->resolveSelectedRule($preferRule);
        $statrulesConfig = $this->statconfig->getConfigItem('statrules');
        $statruleConfig = $statrulesConfig->getConfigItem($rule);

        $presenterClass = \SimpleSAML\Module::resolveClass(
            $statruleConfig->getValue('presenter', 'statistics:BaseRule'),
            'Statistics\Rulesets'
        );

        /** @psalm-suppress InvalidStringClass */
        $statrule = new $presenterClass($this->statconfig, $statruleConfig, $rule, $this->available);

        /** @var \SimpleSAML\Module\statistics\Statistics\Rulesets\BaseRule $statrule */
        return $statrule;
    }
}
