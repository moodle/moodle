<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\CrossReference;

use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\StreamReader;

/**
 * Class FixedReader
 *
 * This reader allows a very less overhead parsing of single entries of the cross-reference, because the main entries
 * are only read when needed and not in a single run.
 */
class FixedReader extends AbstractReader implements ReaderInterface
{
    /**
     * @var StreamReader
     */
    protected $reader;

    /**
     * Data of subsections.
     *
     * @var array
     */
    protected $subSections;

    /**
     * FixedReader constructor.
     *
     * @param PdfParser $parser
     * @throws CrossReferenceException
     */
    public function __construct(PdfParser $parser)
    {
        $this->reader = $parser->getStreamReader();
        $this->read();
        parent::__construct($parser);
    }

    /**
     * Get all subsection data.
     *
     * @return array
     */
    public function getSubSections()
    {
        return $this->subSections;
    }

    /**
     * @inheritdoc
     */
    public function getOffsetFor($objectNumber)
    {
        foreach ($this->subSections as $offset => list($startObject, $objectCount)) {
            /**
             * @var int $startObject
             * @var int $objectCount
             */
            if ($objectNumber >= $startObject && $objectNumber < ($startObject + $objectCount)) {
                $position = $offset + 20 * ($objectNumber - $startObject);
                $this->reader->ensure($position, 20);
                $line = $this->reader->readBytes(20);
                if ($line[17] === 'f') {
                    return false;
                }

                return (int) \substr($line, 0, 10);
            }
        }

        return false;
    }

    /**
     * Read the cross-reference.
     *
     * This reader will only read the subsections in this method. The offsets were resolved individually by this
     * information.
     *
     * @throws CrossReferenceException
     */
    protected function read()
    {
        $subSections = [];

        $startObject = $entryCount = $lastLineStart = null;
        $validityChecked = false;
        while (($line = $this->reader->readLine(20)) !== false) {
            if (\strpos($line, 'trailer') !== false) {
                $this->reader->reset($lastLineStart);
                break;
            }

            // jump over if line content doesn't match the expected string
            if (\sscanf($line, '%d %d', $startObject, $entryCount) !== 2) {
                continue;
            }

            $oldPosition = $this->reader->getPosition();
            $position = $oldPosition + $this->reader->getOffset();

            if (!$validityChecked && $entryCount > 0) {
                $nextLine = $this->reader->readBytes(21);
                /* Check the next line for maximum of 20 bytes and not longer
                 * By catching 21 bytes and trimming the length should be still 21.
                 */
                if (\strlen(\trim($nextLine)) !== 21) {
                    throw new CrossReferenceException(
                        'Cross-reference entries are larger than 20 bytes.',
                        CrossReferenceException::ENTRIES_TOO_LARGE
                    );
                }

                /* Check for less than 20 bytes: cut the line to 20 bytes and trim; have to result in exactly 18 bytes.
                 * If it would have less bytes the substring would get the first bytes of the next line which would
                 * evaluate to a 20 bytes long string after trimming.
                 */
                if (\strlen(\trim(\substr($nextLine, 0, 20))) !== 18) {
                    throw new CrossReferenceException(
                        'Cross-reference entries are less than 20 bytes.',
                        CrossReferenceException::ENTRIES_TOO_SHORT
                    );
                }

                $validityChecked = true;
            }

            $subSections[$position] = [$startObject, $entryCount];

            $lastLineStart = $position + $entryCount * 20;
            $this->reader->reset($lastLineStart);
        }

        // reset after the last correct parsed line
        $this->reader->reset($lastLineStart);

        if (\count($subSections) === 0) {
            throw new CrossReferenceException(
                'No entries found in cross-reference.',
                CrossReferenceException::NO_ENTRIES
            );
        }

        $this->subSections = $subSections;
    }

    /**
     * Fixes an invalid object number shift.
     *
     * This method can be used to repair documents with an invalid subsection header:
     *
     * <code>
     * xref
     * 1 7
     * 0000000000 65535 f
     * 0000000009 00000 n
     * 0000412075 00000 n
     * 0000412172 00000 n
     * 0000412359 00000 n
     * 0000412417 00000 n
     * 0000412468 00000 n
     * </code>
     *
     * It shall only be called on the first table.
     *
     * @return bool
     */
    public function fixFaultySubSectionShift()
    {
        $subSections = $this->getSubSections();
        if (\count($subSections) > 1) {
            return false;
        }

        $subSection = \current($subSections);
        if ($subSection[0] != 1) {
            return false;
        }

        if ($this->getOffsetFor(1) === false) {
            foreach ($subSections as $offset => list($startObject, $objectCount)) {
                $this->subSections[$offset] = [$startObject - 1, $objectCount];
            }
            return true;
        }

        return false;
    }
}
