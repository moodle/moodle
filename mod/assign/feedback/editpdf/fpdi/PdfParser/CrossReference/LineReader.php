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
 * Class LineReader
 *
 * This reader class read all cross-reference entries in a single run.
 * It supports reading cross-references with e.g. invalid data (e.g. entries with a length < or > 20 bytes).
 */
class LineReader extends AbstractReader implements ReaderInterface
{
    /**
     * The object offsets.
     *
     * @var array
     */
    protected $offsets;

    /**
     * LineReader constructor.
     *
     * @param PdfParser $parser
     * @throws CrossReferenceException
     */
    public function __construct(PdfParser $parser)
    {
        $this->read($this->extract($parser->getStreamReader()));
        parent::__construct($parser);
    }

    /**
     * @inheritdoc
     */
    public function getOffsetFor($objectNumber)
    {
        if (isset($this->offsets[$objectNumber])) {
            return $this->offsets[$objectNumber][0];
        }

        return false;
    }

    /**
     * Get all found offsets.
     *
     * @return array
     */
    public function getOffsets()
    {
        return $this->offsets;
    }

    /**
     * Extracts the cross reference data from the stream reader.
     *
     * @param StreamReader $reader
     * @return string
     * @throws CrossReferenceException
     */
    protected function extract(StreamReader $reader)
    {
        $bytesPerCycle = 100;
        $reader->reset(null, $bytesPerCycle);

        $cycles = 0;
        do {
            // 6 = length of "trailer" - 1
            $pos = \max(($bytesPerCycle * $cycles) - 6, 0);
            $trailerPos = \strpos($reader->getBuffer(false), 'trailer', $pos);
            $cycles++;
        } while ($trailerPos === false && $reader->increaseLength($bytesPerCycle) !== false);

        if ($trailerPos === false) {
            throw new CrossReferenceException(
                'Unexpected end of cross reference. "trailer"-keyword not found.',
                CrossReferenceException::NO_TRAILER_FOUND
            );
        }

        $xrefContent = \substr($reader->getBuffer(false), 0, $trailerPos);
        $reader->reset($reader->getPosition() + $trailerPos);

        return $xrefContent;
    }

    /**
     * Read the cross-reference entries.
     *
     * @param string $xrefContent
     * @throws CrossReferenceException
     */
    protected function read($xrefContent)
    {
        // get eol markers in the first 100 bytes
        \preg_match_all("/(\r\n|\n|\r)/", \substr($xrefContent, 0, 100), $m);

        if (\count($m[0]) === 0) {
            throw new CrossReferenceException(
                'No data found in cross-reference.',
                CrossReferenceException::INVALID_DATA
            );
        }

        // count(array_count_values()) is faster then count(array_unique())
        // @see https://github.com/symfony/symfony/pull/23731
        // can be reverted for php7.2
        $differentLineEndings = \count(\array_count_values($m[0]));
        if ($differentLineEndings > 1) {
            $lines = \preg_split("/(\r\n|\n|\r)/", $xrefContent, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $lines = \explode($m[0][0], $xrefContent);
        }

        unset($differentLineEndings, $m);
        if (!\is_array($lines)) {
            $this->offsets = [];
            return;
        }

        $start = 0;
        $offsets = [];

        // trim all lines and remove empty lines
        $lines = \array_filter(\array_map('\trim', $lines));
        foreach ($lines as $line) {
            $pieces = \explode(' ', $line);

            switch (\count($pieces)) {
                case 2:
                    $start = (int) $pieces[0];
                    break;

                case 3:
                    switch ($pieces[2]) {
                        case 'n':
                            $offsets[$start] = [(int) $pieces[0], (int) $pieces[1]];
                            $start++;
                            break 2;
                        case 'f':
                            $start++;
                            break 2;
                    }
                    // fall through if pieces doesn't match

                default:
                    throw new CrossReferenceException(
                        \sprintf('Unexpected data in xref table (%s)', \implode(' ', $pieces)),
                        CrossReferenceException::INVALID_DATA
                    );
            }
        }

        $this->offsets = $offsets;
    }
}
