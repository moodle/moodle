<?php

namespace Box\Spout\Writer\XLSX\Manager;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Helper\Escaper;

/**
 * Class SharedStringsManager
 * This class provides functions to write shared strings
 */
class SharedStringsManager
{
    const SHARED_STRINGS_FILE_NAME = 'sharedStrings.xml';

    const SHARED_STRINGS_XML_FILE_FIRST_PART_HEADER = <<<'EOD'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
EOD;

    /**
     * This number must be really big so that the no generated file will have more strings than that.
     * If the strings number goes above, characters will be overwritten in an unwanted way and will corrupt the file.
     */
    const DEFAULT_STRINGS_COUNT_PART = 'count="9999999999999" uniqueCount="9999999999999"';

    /** @var resource Pointer to the sharedStrings.xml file */
    protected $sharedStringsFilePointer;

    /** @var int Number of shared strings already written */
    protected $numSharedStrings = 0;

    /** @var Escaper\XLSX Strings escaper */
    protected $stringsEscaper;

    /**
     * @param string $xlFolder Path to the "xl" folder
     * @param Escaper\XLSX $stringsEscaper Strings escaper
     */
    public function __construct($xlFolder, $stringsEscaper)
    {
        $sharedStringsFilePath = $xlFolder . '/' . self::SHARED_STRINGS_FILE_NAME;
        $this->sharedStringsFilePointer = \fopen($sharedStringsFilePath, 'w');

        $this->throwIfSharedStringsFilePointerIsNotAvailable();

        // the headers is split into different parts so that we can fseek and put in the correct count and uniqueCount later
        $header = self::SHARED_STRINGS_XML_FILE_FIRST_PART_HEADER . ' ' . self::DEFAULT_STRINGS_COUNT_PART . '>';
        \fwrite($this->sharedStringsFilePointer, $header);

        $this->stringsEscaper = $stringsEscaper;
    }

    /**
     * Checks if the book has been created. Throws an exception if not created yet.
     *
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     * @return void
     */
    protected function throwIfSharedStringsFilePointerIsNotAvailable()
    {
        if (!$this->sharedStringsFilePointer) {
            throw new IOException('Unable to open shared strings file for writing.');
        }
    }

    /**
     * Writes the given string into the sharedStrings.xml file.
     * Starting and ending whitespaces are preserved.
     *
     * @param string $string
     * @return int ID of the written shared string
     */
    public function writeString($string)
    {
        \fwrite($this->sharedStringsFilePointer, '<si><t xml:space="preserve">' . $this->stringsEscaper->escape($string) . '</t></si>');
        $this->numSharedStrings++;

        // Shared string ID is zero-based
        return ($this->numSharedStrings - 1);
    }

    /**
     * Finishes writing the data in the sharedStrings.xml file and closes the file.
     *
     * @return void
     */
    public function close()
    {
        if (!\is_resource($this->sharedStringsFilePointer)) {
            return;
        }

        \fwrite($this->sharedStringsFilePointer, '</sst>');

        // Replace the default strings count with the actual number of shared strings in the file header
        $firstPartHeaderLength = \strlen(self::SHARED_STRINGS_XML_FILE_FIRST_PART_HEADER);
        $defaultStringsCountPartLength = \strlen(self::DEFAULT_STRINGS_COUNT_PART);

        // Adding 1 to take into account the space between the last xml attribute and "count"
        \fseek($this->sharedStringsFilePointer, $firstPartHeaderLength + 1);
        \fwrite($this->sharedStringsFilePointer, \sprintf("%-{$defaultStringsCountPartLength}s", 'count="' . $this->numSharedStrings . '" uniqueCount="' . $this->numSharedStrings . '"'));

        \fclose($this->sharedStringsFilePointer);
    }
}
