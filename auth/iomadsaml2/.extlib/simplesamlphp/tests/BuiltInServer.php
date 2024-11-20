<?php

declare(strict_types=1);

namespace SimpleSAML\Test;

use SimpleSAML\Utils\System;

/**
 * An extremely simple class to start and stop PHP's built-in server, with the possibility to specify the document
 * root and the "router" file to run for every request.
 *
 * @author Jaime Pérez Crespo <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */

class BuiltInServer
{
    /**
     * The PID of the running server.
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * The address (host:port) where the server is listening for connections after being started.
     *
     * @var string
     */
    protected $address = 'example.org';

    /**
     * The name of a "router" file to run for every request performed to this server.
     *
     * @var string
     */
    protected $router = '';

    /**
     * The document root of the server.
     *
     * @var string
     */
    protected $docroot;


    /**
     * BuiltInServer constructor.
     *
     * @param string|null $router The name of a "router" file to run first for every request performed to this server.
     * @param string|null $docroot The document root to use when starting the server.
     *
     * @see http://php.net/manual/en/features.commandline.webserver.php
     */
    public function __construct($router = null, $docroot = null)
    {
        if (!is_null($router)) {
            $this->setRouter($router);
        }

        if (!is_null($docroot)) {
            $this->docroot = $docroot;
        } else {
            $this->docroot = dirname(dirname(__FILE__)) . '/www/';
        }

        // Rationalize docroot
        $this->docroot = str_replace('\\', '/', $this->docroot);
        $this->docroot = rtrim($this->docroot, '/');
    }


    /**
     * Start the built-in server in a random port.
     *
     * This method will wait up to 5 seconds for the server to start. When it returns an address, it is guaranteed that
     * the server has started and is listening for connections. If it returns the default value on the other hand,
     * there will be no guarantee that the server started properly.
     *
     * @return string The address where the server is listening for connections, or false if the server failed to start
     * for some reason.
     *
     * @todo This method should be resilient to clashes in the randomly-picked port number.
     */
    public function start()
    {
        $port = mt_rand(1025, 65535);
        $this->address = 'localhost:' . $port;

        if (System::getOS() === System::WINDOWS) {
            $command = sprintf(
                'powershell $proc = start-process php -ArgumentList (\'-S %s\', \'-t %s\', \'%s\') '
                    . '-Passthru; Write-output $proc.Id;',
                $this->address,
                $this->docroot,
                $this->router
            );
        } else {
            $command = sprintf(
                'php -S %s -t %s %s >> /dev/null 2>&1 & echo $!',
                $this->address,
                $this->docroot,
                $this->router
            );
        }

        // execute the command and store the process ID
        $output = [];
        exec($command, $output);
        $this->pid = intval($output[0]);

        // wait until it's listening for connections to avoid race conditions
        $start = microtime(true);
        do {
            $sock = @fsockopen('localhost', $port, $errno, $errstr, 10);
            if ($sock === false) {
                // set a 5 secs timeout waiting for the server to start
                if (microtime(true) > $start + 5) {
                    $this->pid = 0; // signal failure
                    break;
                }
            }
        } while ($sock === false);

        if ($sock !== false) {
            fclose($sock);
        }

        return $this->address;
    }


    /**
     * Stop the built-in server.
     * @return void
     */
    public function stop()
    {
        if ($this->pid === 0) {
            return;
        } elseif (System::getOS() === System::WINDOWS) {
            exec('taskkill /PID ' . $this->pid);
        } else {
            exec('kill ' . $this->pid);
        }
        $this->pid = 0;
    }


    /**
     * Get the PID of the running server.
     *
     * @return int The PID of the server, or 0 if the server was not started.
     */
    public function getPid()
    {
        return $this->pid;
    }


    /**
     * Get the name of the "router" file.
     *
     * @return string The name of the "router" file.
     */
    public function getRouter()
    {
        return $this->router;
    }


    /**
     * Set the "router" file.
     *
     * @param string $router The name of a "router" file to use when starting the server.
     * @return void
     */
    public function setRouter($router)
    {
        $file = dirname(dirname(__FILE__)) . '/tests/routers/' . $router . '.php';
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('Unknown router "' . $router . '".');
        }
        $this->router = $file;
    }


    /**
     * This function performs an HTTP GET request to the built-in server.
     *
     * @param string $query The query to perform.
     * @param array $parameters An array (can be empty) with parameters for the requested URI.
     * @param array $curlopts An array (can be empty) with options for cURL.
     *
     * @return array The response obtained from the built-in server.
     */
    public function get($query, $parameters, $curlopts = [])
    {
        $ch = curl_init();
        $url = 'http://' . $this->address . $query;
        $url .= (!empty($parameters)) ? '?' . http_build_query($parameters) : '';
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1,
        ]);
        curl_setopt_array($ch, $curlopts);

        /** @psalm-var array|false $resp  RETURNTRANSFER was set to true */
        $resp = curl_exec($ch);

        if ($resp === false) {
            throw new \Exception("Unable to contact: " . $url);
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        list($header, $body) = explode("\r\n\r\n", $resp, 2);
        $raw_headers = explode("\r\n", $header);
        array_shift($raw_headers);
        $headers = [];
        foreach ($raw_headers as $header) {
            list($name, $value) = explode(':', $header, 2);
            $headers[trim($name)] = trim($value);
        }
        curl_close($ch);
        return [
            'code' => $code,
            'headers' => $headers,
            'body' => $body,
        ];
    }
}
