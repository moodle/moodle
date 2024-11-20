<?php

declare(strict_types=1);

namespace SimpleSAML;

/**
 * Statistics handler class.
 *
 * This class is responsible for taking a statistics event and logging it.
 *
 * @package SimpleSAMLphp
 */

class Stats
{
    /**
     * Whether this class is initialized.
     *
     * @var boolean
     */
    private static $initialized = false;


    /**
     * The statistics output callbacks.
     *
     * @var array
     */
    private static $outputs = null;


    /**
     * Create an output from a configuration object.
     *
     * @param \SimpleSAML\Configuration $config The configuration object.
     *
     * @return mixed A new instance of the configured class.
     */
    private static function createOutput(Configuration $config)
    {
        $cls = $config->getString('class');
        $cls = Module::resolveClass($cls, 'Stats\Output', '\SimpleSAML\Stats\Output');

        $output = new $cls($config);
        return $output;
    }


    /**
     * Initialize the outputs.
     *
     * @return void
     */
    private static function initOutputs(): void
    {
        $config = Configuration::getInstance();
        $outputCfgs = $config->getConfigList('statistics.out');

        self::$outputs = [];
        foreach ($outputCfgs as $cfg) {
            self::$outputs[] = self::createOutput($cfg);
        }
    }


    /**
     * Notify about an event.
     *
     * @param string $event The event.
     * @param array  $data Event data. Optional.
     *
     * @return void|boolean False if output is not enabled, void otherwise.
     */
    public static function log($event, array $data = [])
    {
        assert(is_string($event));
        assert(!isset($data['op']));
        assert(!isset($data['time']));
        assert(!isset($data['_id']));

        if (!self::$initialized) {
            self::initOutputs();
            self::$initialized = true;
        }

        if (empty(self::$outputs)) {
            // not enabled
            return false;
        }

        $data['op'] = $event;
        $data['time'] = microtime(true);

        // the ID generation is designed to cluster IDs related in time close together
        $int_t = (int) $data['time'];
        $hd = openssl_random_pseudo_bytes(16);
        $data['_id'] = sprintf('%016x%s', $int_t, bin2hex($hd));

        foreach (self::$outputs as $out) {
            $out->emit($data);
        }
    }
}
