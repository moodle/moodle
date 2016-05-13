<?php

namespace Box\Spout\Reader\Wrapper;


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

    /**
     * Set the URI containing the XML to parse
     * @see \XMLReader::open
     *
     * @param string $URI URI pointing to the document
   	 * @param string|null|void $encoding The document encoding
   	 * @param int $options A bitmask of the LIBXML_* constants
     * @return bool TRUE on success or FALSE on failure
     */
    public function open($URI, $encoding = null, $options = 0)
    {
        $wasOpenSuccessful = false;
        $realPathURI = $this->convertURIToUseRealPath($URI);

        // HHVM does not check if file exists within zip file
        // @link https://github.com/facebook/hhvm/issues/5779
        if ($this->isRunningHHVM() && $this->isZipStream($realPathURI)) {
            if ($this->fileExistsWithinZip($realPathURI)) {
                $wasOpenSuccessful = parent::open($realPathURI, $encoding, $options|LIBXML_NONET);
            }
        } else {
            $wasOpenSuccessful = parent::open($realPathURI, $encoding, $options|LIBXML_NONET);
        }

        return $wasOpenSuccessful;
    }

    /**
     * Updates the given URI to use a real path.
     * This is to avoid issues on some Windows setup.
     *
     * @param string $URI URI
     * @return string The URI using a real path
     */
    protected function convertURIToUseRealPath($URI)
    {
        $realPathURI = $URI;

        if ($this->isZipStream($URI)) {
            if (preg_match('/zip:\/\/(.*)#(.*)/', $URI, $matches)) {
                $documentPath = $matches[1];
                $documentInsideZipPath = $matches[2];
                $realPathURI = 'zip://' . realpath($documentPath) . '#' . $documentInsideZipPath;
            }
        } else {
            $realPathURI = realpath($URI);
        }

        return $realPathURI;
    }

    /**
     * Returns whether the given URI is a zip stream.
     *
     * @param string $URI URI pointing to a document
     * @return bool TRUE if URI is a zip stream, FALSE otherwise
     */
    protected function isZipStream($URI)
    {
        return (strpos($URI, 'zip://') === 0);
    }

    /**
     * Returns whether the current environment is HHVM
     *
     * @return bool TRUE if running on HHVM, FALSE otherwise
     */
    protected function isRunningHHVM()
    {
        return defined('HHVM_VERSION');
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
        while (($wasReadSuccessful = $this->read()) && ($this->nodeType !== \XMLReader::ELEMENT || $this->name !== $nodeName)) {
            // do nothing
        }

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
        return ($this->nodeType === XMLReader::ELEMENT && $this->name === $nodeName);
    }

    /**
     * @param string $nodeName
     * @return bool Whether the XML Reader is currently positioned on the ending node with given name
     */
    public function isPositionedOnEndingNode($nodeName)
    {
        return ($this->nodeType === XMLReader::END_ELEMENT && $this->name === $nodeName);
    }
}
