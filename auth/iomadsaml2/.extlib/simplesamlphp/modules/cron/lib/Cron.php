<?php

declare(strict_types=1);

namespace SimpleSAML\Module\cron;

use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Module;

/**
 * Handles interactions with SSP's cron system/hooks.
 */
class Cron
{
    /**
     * The configuration for the Cron module
     * @var \SimpleSAML\Configuration
     */
    private $cronconfig;


    /*
     * @param \SimpleSAML\Configuration $cronconfig The cron configuration to use. If not specified defaults
     * to `config/module_cron.php`
     */
    public function __construct(Configuration $cronconfig = null)
    {
        if ($cronconfig == null) {
            $cronconfig = Configuration::getConfig('module_cron.php');
        }
        $this->cronconfig = $cronconfig;
    }

    /**
     * Invoke the cron hook for the given tag
     * @param string $tag The tag to use. Must be valid in the cronConfig
     * @return array the tag, and summary information from the run.
     * @throws \Exception If an invalid tag specified
     */
    public function runTag($tag)
    {
        if (!$this->isValidTag($tag)) {
            throw new \Exception("Invalid cron tag '$tag''");
        }

        $summary = [];
        $croninfo = [
            'summary' => &$summary,
            'tag' => $tag,
        ];

        Module::callHooks('cron', $croninfo);

        foreach ($summary as $s) {
            Logger::debug('Cron - Summary: ' . $s);
        }

        return $croninfo;
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function isValidTag($tag)
    {
        if (!is_null($this->cronconfig->getValue('allowed_tags'))) {
            return in_array($tag, $this->cronconfig->getArray('allowed_tags'), true);
        }
        return true;
    }
}
