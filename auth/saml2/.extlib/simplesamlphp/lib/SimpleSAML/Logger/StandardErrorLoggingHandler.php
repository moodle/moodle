<?php

declare(strict_types=1);

namespace SimpleSAML\Logger;

use SimpleSAML\Configuration;

/**
 * A logging handler that outputs all messages to standard error.
 *
 * @author Jaime Perez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */
class StandardErrorLoggingHandler extends FileLoggingHandler
{
    /**
     * StandardError constructor.
     *
     * It runs the parent constructor and sets the log file to be the standard error descriptor.
     *
     * @param \SimpleSAML\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        // Remove any non-printable characters before storing
        $this->processname = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $config->getString('logging.processname', 'SimpleSAMLphp'));
        $this->logFile = 'php://stderr';
    }
}
