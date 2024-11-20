<?php

declare(strict_types=1);

namespace SAML2\Compat;

use \Psr\Log\LoggerInterface;

abstract class AbstractContainer
{
    /**
     * Get a PSR-3 compatible logger.
     * @return \Psr\Log\LoggerInterface
     */
    abstract public function getLogger() : LoggerInterface;


    /**
     * Generate a random identifier for identifying SAML2 documents.
     * @return string
     */
    abstract public function generateId() : string;


    /**
     * Log an incoming message to the debug log.
     *
     * Type can be either:
     * - **in** XML received from third party
     * - **out** XML that will be sent to third party
     * - **encrypt** XML that is about to be encrypted
     * - **decrypt** XML that was just decrypted
     *
     * @param \DOMElement|string $message
     * @param string $type
     * @return void
     */
    abstract public function debugMessage($message, string $type) : void;



    /**
     * Trigger the user to perform a GET to the given URL with the given data.
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    abstract public function redirect(string $url, array $data = []) : void;


    /**
     * Trigger the user to perform a POST to the given URL with the given data.
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    abstract public function postRedirect(string $url, array $data = []) : void;


    /**
     * This function retrieves the path to a directory where temporary files can be saved.
     *
     * @throws \Exception If the temporary directory cannot be created or it exists and does not belong
     * to the current user.
     * @return string Path to a temporary directory, without a trailing directory separator.
     */
    abstract public function getTempDir() : string;


    /**
     * Atomically write a file.
     *
     * This is a helper function for writing data atomically to a file. It does this by writing the file data to a
     * temporary file, then renaming it to the required file name.
     *
     * @param string $filename The path to the file we want to write to.
     * @param string $data The data we should write to the file.
     * @param int $mode The permissions to apply to the file. Defaults to 0600.
     * @return void
     */
    abstract public function writeFile(string $filename, string $data, int $mode = null) : void;
}
