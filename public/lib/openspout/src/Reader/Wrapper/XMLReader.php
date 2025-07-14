<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Wrapper;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\XMLProcessingException;
use ZipArchive;

/**
 * @internal
 */
final class XMLReader extends \XMLReader
{
    use XMLInternalErrorsHelper;

    public const ZIP_WRAPPER = 'zip://';

    /**
     * Opens the XML Reader to read a file located inside a ZIP file.
     *
     * @param string $zipFilePath       Path to the ZIP file
     * @param string $fileInsideZipPath Relative or absolute path of the file inside the zip
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function openFileInZip(string $zipFilePath, string $fileInsideZipPath): bool
    {
        $wasOpenSuccessful = false;
        $realPathURI = $this->getRealPathURIForFileInZip($zipFilePath, $fileInsideZipPath);

        // We need to check first that the file we are trying to read really exist because:
        //  - PHP emits a warning when trying to open a file that does not exist.
        if ($this->fileExistsWithinZip($realPathURI)) {
            $wasOpenSuccessful = $this->open($realPathURI, null, LIBXML_NONET);
        }

        return $wasOpenSuccessful;
    }

    /**
     * Returns the real path for the given path components.
     * This is useful to avoid issues on some Windows setup.
     *
     * @param string $zipFilePath       Path to the ZIP file
     * @param string $fileInsideZipPath Relative or absolute path of the file inside the zip
     *
     * @return string The real path URI
     */
    public function getRealPathURIForFileInZip(string $zipFilePath, string $fileInsideZipPath): string
    {
        // The file path should not start with a '/', otherwise it won't be found
        $fileInsideZipPathWithoutLeadingSlash = ltrim($fileInsideZipPath, '/');

        $realpath = realpath($zipFilePath);
        if (false === $realpath) {
            throw new IOException("Could not open {$zipFilePath} for reading! File does not exist.");
        }

        return self::ZIP_WRAPPER.$realpath.'#'.$fileInsideZipPathWithoutLeadingSlash;
    }

    /**
     * Move to next node in document.
     *
     * @see \XMLReader::read
     *
     * @throws XMLProcessingException If an error/warning occurred
     */
    public function read(): bool
    {
        $this->useXMLInternalErrors();

        $wasReadSuccessful = parent::read();

        $this->resetXMLInternalErrorsSettingAndThrowIfXMLErrorOccured();

        return $wasReadSuccessful;
    }

    /**
     * Read until the element with the given name is found, or the end of the file.
     *
     * @param string $nodeName Name of the node to find
     *
     * @return bool TRUE on success or FALSE on failure
     *
     * @throws XMLProcessingException If an error/warning occurred
     */
    public function readUntilNodeFound(string $nodeName): bool
    {
        do {
            $wasReadSuccessful = $this->read();
            $isNotPositionedOnStartingNode = !$this->isPositionedOnStartingNode($nodeName);
        } while ($wasReadSuccessful && $isNotPositionedOnStartingNode);

        return $wasReadSuccessful;
    }

    /**
     * Move cursor to next node skipping all subtrees.
     *
     * @see \XMLReader::next
     *
     * @param null|string $localName The name of the next node to move to
     *
     * @throws XMLProcessingException If an error/warning occurred
     */
    public function next($localName = null): bool
    {
        $this->useXMLInternalErrors();

        $wasNextSuccessful = parent::next($localName);

        $this->resetXMLInternalErrorsSettingAndThrowIfXMLErrorOccured();

        return $wasNextSuccessful;
    }

    /**
     * @return bool Whether the XML Reader is currently positioned on the starting node with given name
     */
    public function isPositionedOnStartingNode(string $nodeName): bool
    {
        return $this->isPositionedOnNode($nodeName, self::ELEMENT);
    }

    /**
     * @return bool Whether the XML Reader is currently positioned on the ending node with given name
     */
    public function isPositionedOnEndingNode(string $nodeName): bool
    {
        return $this->isPositionedOnNode($nodeName, self::END_ELEMENT);
    }

    /**
     * @return string The name of the current node, un-prefixed
     */
    public function getCurrentNodeName(): string
    {
        return $this->localName;
    }

    /**
     * Returns whether the file at the given location exists.
     *
     * @param string $zipStreamURI URI of a zip stream, e.g. "zip://file.zip#path/inside.xml"
     *
     * @return bool TRUE if the file exists, FALSE otherwise
     */
    private function fileExistsWithinZip(string $zipStreamURI): bool
    {
        $doesFileExists = false;

        $pattern = '/zip:\/\/([^#]+)#(.*)/';
        if (1 === preg_match($pattern, $zipStreamURI, $matches)) {
            $zipFilePath = $matches[1];
            $innerFilePath = $matches[2];

            $zip = new ZipArchive();
            if (true === $zip->open($zipFilePath)) {
                $doesFileExists = (false !== $zip->locateName($innerFilePath));
                $zip->close();
            }
        }

        return $doesFileExists;
    }

    /**
     * @return bool Whether the XML Reader is currently positioned on the node with given name and type
     */
    private function isPositionedOnNode(string $nodeName, int $nodeType): bool
    {
        /**
         * In some cases, the node has a prefix (for instance, "<sheet>" can also be "<x:sheet>").
         * So if the given node name does not have a prefix, we need to look at the unprefixed name ("localName").
         *
         * @see https://github.com/box/spout/issues/233
         */
        $hasPrefix = str_contains($nodeName, ':');
        $currentNodeName = ($hasPrefix) ? $this->name : $this->localName;

        return $this->nodeType === $nodeType && $currentNodeName === $nodeName;
    }
}
