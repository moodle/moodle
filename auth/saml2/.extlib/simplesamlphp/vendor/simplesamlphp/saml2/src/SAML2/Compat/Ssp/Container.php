<?php

declare(strict_types=1);

namespace SAML2\Compat\Ssp;

use Psr\Log\LoggerInterface;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Utils\Random;
use SimpleSAML\Utils\System;
use SimpleSAML\Utils\XML;

use SAML2\Compat\AbstractContainer;

class Container extends AbstractContainer
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \SimpleSAML\Utils\HTTP
     */
    protected $utilsHttp;

    /**
     * @var \SimpleSAML\Utils\Random
     */
    protected $utilsRandom;

    /**
     * @var \SimpleSAML\Utils\System
     */
    protected $utilsSystem;

    /**
     * @var \SimpleSAML\Utils\XML
     */
    protected $utilsXml;

    /**
     * Create a new SimpleSAMLphp compatible container.
     */
    public function __construct()
    {
        $this->logger = new Logger();
        $this->utilsHttp = new HTTP();
        $this->utilsRandom = new Random();
        $this->utilsSystem = new System();
        $this->utilsXml = new XML();
    }


    /**
     * {@inheritdoc}
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger() : LoggerInterface
    {
        return $this->logger;
    }


    /**
     * {@inheritdoc}
     * @return string
     */
    public function generateId() : string
    {
        return $this->utilsRandom->generateID();
    }


    /**
     * {@inheritdoc}
     * @param mixed $message
     * @param string $type
     * @return void
     */
    public function debugMessage($message, string $type) : void
    {
        $this->utilsXml->debugSAMLMessage($message, $type);
    }


    /**
     * {@inheritdoc}
     * @param string $url
     * @param array $data
     * @return void
     */
    public function redirect(string $url, array $data = []) : void
    {
        $this->utilsHttp->redirectTrustedURL($url, $data);
    }


    /**
     * {@inheritdoc}
     * @param string $url
     * @param array $data
     * @return void
     */
    public function postRedirect(string $url, array $data = []) : void
    {
        $this->utilsHttp->submitPOSTData($url, $data);
    }


    /**
     * {@inheritdoc}
     * @return string
     */
    public function getTempDir() : string
    {
        return $this->utilsSystem->getTempDir();
    }


    /**
     * {@inheritdoc}
     * @param string $filename
     * @param string $date
     * @param int|null $mode
     * @return void
     */
    public function writeFile(string $filename, string $data, int $mode = null) : void
    {
        if ($mode === null) {
            $mode = 0600;
        }
        $this->utilsSystem->writeFile($filename, $data, $mode);
    }
}
