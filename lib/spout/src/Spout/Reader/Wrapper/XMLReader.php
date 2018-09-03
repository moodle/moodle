<?php

namespace Box\Spout\Reader\Wrapper;
use DOMNode;


/**
 * Class XMLReader
 * Wrapper around the built-in XMLReader
 * @see \XMLReader
 *
 * @package Box\Spout\Reader\Wrapper
 */
class XMLReader extends \XMLReader
{
    use XMLInternalErrorsHelper;

    const ZIP_WRAPPER = 'zip://';

    /**
     * Opens the XML Reader to read a file located inside a ZIP file.
     *
     * @param string $zipFilePath Path to the ZIP file
     * @param string $fileInsideZipPath Relative or absolute path of the file inside the zip
     * @return bool TRUE on success or FALSE on failure
     */
    public function openFileInZip($zipFilePath, $fileInsideZipPath)
    {
        $wasOpenSuccessful = false;
        $realPathURI = $this->getRealPathURIForFileInZip($zipFilePath, $fileInsideZipPath);

        // We need to check first that the file we are trying to read really exist because:
        //  - PHP emits a warning when trying to open a file that does not exist.
        //  - HHVM does not check if file exists within zip file (@link https://github.com/facebook/hhvm/issues/5779)
        if ($this->fileExistsWithinZip($realPathURI)) {
            $wasOpenSuccessful = $this->open($realPathURI, null, LIBXML_NONET);
        }

        return $wasOpenSuccessful;
    }

    /**
     * Returns the real path for the given path components.
     * This is useful to avoid issues on some Windows setup.
     *
     * @param string $zipFilePath Path to the ZIP file
     * @param string $fileInsideZipPath Relative or absolute path of the file inside the zip
     * @return string The real path URI
     */
    public function getRealPathURIForFileInZip($zipFilePath, $fileInsideZipPath)
    {
        return (self::ZIP_WRAPPER . realpath($zipFilePath) . '#' . $fileInsideZipPath);
    }

    /**
     * Returns whether the file at the given location exists
     *
     * @param string $zipStreamURI URI of a zip stream, e.g. "zip://file.zip#path/inside.xml"
     * @return bool TRUE if the file exists, FALSE otherwise
     */
    protected function fileExistsWithinZip($zipStreamURI)
    {
        $doesFileExists = false;

        $pattern = '/zip:\/\/([^#]+)#(.*)/';
        if (preg_match($pattern, $zipStreamURI, $matches)) {
            $zipFilePath = $matches[1];
            $innerFilePath = $matches[2];

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath) === true) {
                $doesFileExists = ($zip->locateName($innerFilePath) !== false);
                $zip->close();
            }
        }

        return $doesFileExists;
    }

    /**
     * Move to next node in document
     * @see \XMLReader::read
     *
     * @return bool TRUE on success or FALSE on failure
     * @throws \Box\Spout\Reader\Exception\XMLProcessingException If an error/warning occurred
     */
    public function read()
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
     * @return bool TRUE on success or FALSE on failure
     * @throws \Box\Spout\Reader\Exception\XMLProcessingException If an error/warning occurred
     */
    public function readUntilNodeFound($nodeName)
    {
        do {
            $wasReadSuccessful = $this->read();
            $isNotPositionedOnStartingNode = !$this->isPositionedOnStartingNode($nodeName);
        } while ($wasReadSuccessful && $isNotPositionedOnStartingNode);

        return $wasReadSuccessful;
    }

    /**
     * Move cursor to next node skipping all subtrees
     * @see \XMLReader::next
     *
     * @param string|void $localName The name of the next node to move to
     * @return bool TRUE on success or FALSE on failure
     * @throws \Box\Spout\Reader\Exception\XMLProcessingException If an error/warning occurred
     */
    public function next($localName = null)
    {
        $this->useXMLInternalErrors();

        $wasNextSuccessful = parent::next($localName);

        $this->resetXMLInternalErrorsSettingAndThrowIfXMLErrorOccured();

        return $wasNextSuccessful;
    }

    /**
     * @param string $nodeName
     * @return bool Whether the XML Reader is currently positioned on the starting node with given name
     */
    public function isPositionedOnStartingNode($nodeName)
    {
        return $this->isPositionedOnNode($nodeName, XMLReader::ELEMENT);
    }

    /**
     * @param string $nodeName
     * @return bool Whether the XML Reader is currently positioned on the ending node with given name
     */
    public function isPositionedOnEndingNode($nodeName)
    {
        return $this->isPositionedOnNode($nodeName, XMLReader::END_ELEMENT);
    }

    /**
     * @param string $nodeName
     * @param int $nodeType
     * @return bool Whether the XML Reader is currently positioned on the node with given name and type
     */
    private function isPositionedOnNode($nodeName, $nodeType)
    {
        // In some cases, the node has a prefix (for instance, "<sheet>" can also be "<x:sheet>").
        // So if the given node name does not have a prefix, we need to look at the unprefixed name ("localName").
        // @see https://github.com/box/spout/issues/233
        $hasPrefix = (strpos($nodeName, ':') !== false);
        $currentNodeName = ($hasPrefix) ? $this->name : $this->localName;

        return ($this->nodeType === $nodeType && $currentNodeName === $nodeName);
    }

    /**
     * @return string The name of the current node, un-prefixed
     */
    public function getCurrentNodeName()
    {
        return $this->localName;
    }
}
