<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Stats\Output;

use SimpleSAML\Configuration;
use SimpleSAML\Error;

/**
 * Statistics logger that writes to a set of log files
 *
 * @package SimpleSAMLphp
 */
class File extends \SimpleSAML\Stats\Output
{
    /**
     * The log directory.
     * @var string
     */
    private $logDir;

    /**
     * The file handle for the current file.
     * @var resource|null
     */
    private $file = null;

    /**
     * The current file date.
     * @var string|null
     */
    private $fileDate = null;


    /**
     * Initialize the output.
     *
     * @param \SimpleSAML\Configuration $config  The configuration for this output.
     */
    public function __construct(Configuration $config)
    {
        $logDir = $config->getPathValue('directory');
        if ($logDir === null) {
            throw new \Exception('Missing "directory" option for core:File');
        }
        if (!is_dir($logDir)) {
            throw new \Exception('Could not find log directory: ' . var_export($logDir, true));
        }
        $this->logDir = $logDir;
    }


    /**
     * Open a log file.
     *
     * @param string $date  The date for the log file.
     * @return void
     */
    private function openLog(string $date): void
    {
        if ($this->file !== null && $this->file !== false) {
            fclose($this->file);
            $this->file = null;
        }

        $fileName = $this->logDir . '/' . $date . '.log';
        $fh = @fopen($fileName, 'a');
        if ($fh === false) {
            throw new Error\Exception('Error opening log file: ' . var_export($fileName, true));
        }

        // Disable output buffering
        stream_set_write_buffer($fh, 0);

        $this->file = $fh;
        $this->fileDate = $date;
    }


    /**
     * Write a stats event.
     *
     * @param array $data  The event.
     * @return void
     */
    public function emit(array $data)
    {
        assert(isset($data['time']));

        $time = $data['time'];
        $milliseconds = intval((($time - intval($time)) * 1000));

        $timestamp = gmdate('Y-m-d\TH:i:s', intval($time)) . sprintf('.%03dZ', $milliseconds);

        $outDate = substr($timestamp, 0, 10); // The date-part of the timstamp

        if ($outDate !== $this->fileDate) {
            $this->openLog($outDate);
        }

        $line = $timestamp . ' ' . json_encode($data) . "\n";
        /** @psalm-suppress PossiblyNullArgument */
        fwrite($this->file, $line);
    }
}
