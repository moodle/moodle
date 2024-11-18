<?php

declare(strict_types=1);

namespace SAML2\Configuration;

use SAML2\Exception\InvalidArgumentException;
use SAML2\Exception\RuntimeException;

/**
 * Configuration of a private key.
 */
class PrivateKey extends ArrayAdapter
{
    const NAME_NEW     = 'new';
    const NAME_DEFAULT = 'default';

    /**
     * @var string
     */
    private $filePathOrContents;

    /**
     * @var string|null
     */
    private $passphrase;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isFile;

    /**
     * Constructor for PrivateKey.
     *
     * @param string $filePathOrContents
     * @param string $name
     * @param string $passphrase
     * @param bool $isFile
     */
    public function __construct(string $filePathOrContents, string $name, string $passphrase = '', bool $isFile = true)
    {
        $this->filePathOrContents = $filePathOrContents;
        $this->passphrase = $passphrase;
        $this->name = $name;
        $this->isFile = $isFile;
    }


    /**
     * @return string
     */
    public function getFilePath() : string
    {
        if (!$this->isFile()) {
            throw new RuntimeException('No path provided.');
        }

        return $this->filePathOrContents;
    }


    /**
     * @return bool
     */
    public function hasPassPhrase() : bool
    {
        return $this->passphrase !== null;
    }


    /**
     * @return string|null
     */
    public function getPassPhrase() : ?string
    {
        return $this->passphrase;
    }


    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContents() : string
    {
        if ($this->isFile()) {
            throw new RuntimeException('No contents provided');
        }

        return $this->filePathOrContents;
    }

    /**
     * @return bool
     */
    public function isFile() : bool
    {
        return $this->isFile;
    }
}
