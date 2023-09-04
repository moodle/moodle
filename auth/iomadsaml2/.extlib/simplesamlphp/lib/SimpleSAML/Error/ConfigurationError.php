<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

/**
 * This exception represents a configuration error.
 *
 * @author Jaime Perez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */

class ConfigurationError extends Error
{
    /**
     * The reason for this exception.
     *
     * @var null|string
     */
    protected $reason;

    /**
     * The configuration file that caused this exception.
     *
     * @var null|string
     */
    protected $config_file;


    /**
     * ConfigurationError constructor.
     *
     * @param string|null $reason The reason for this exception.
     * @param string|null $file The configuration file that originated this error.
     * @param array|null $config The configuration array that led to this problem.
     */
    public function __construct($reason = null, $file = null, array $config = null)
    {
        $file_str = '';
        $reason_str = '.';
        $params = ['CONFIG'];
        if ($file !== null) {
            $params['%FILE%'] = $file;
            $basepath = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
            $file_str = '(' . str_replace($basepath, '', $file) . ') ';
        }
        if ($reason !== null) {
            $params['%REASON%'] = $reason;
            $reason_str = ': ' . $reason;
        }
        $this->reason = $reason;
        $this->config_file = $file;
        parent::__construct($params);
        $this->message = 'The configuration ' . $file_str . 'is invalid' . $reason_str;
    }


    /**
     * Get the reason for this exception.
     *
     * @return null|string The reason for this exception.
     */
    public function getReason()
    {
        return $this->reason;
    }


    /**
     * Get the configuration file that caused this exception.
     *
     * @return null|string The configuration file that caused this exception.
     */
    public function getConfFile()
    {
        return $this->config_file;
    }
}
