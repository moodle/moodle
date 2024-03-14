<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\Type;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\Ascii85;
use setasign\Fpdi\PdfParser\Filter\AsciiHex;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\Filter\Flate;
use setasign\Fpdi\PdfParser\Filter\Lzw;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\FpdiPdfParser\PdfParser\Filter\Predictor;

/**
 * Class representing a PDF stream object
 */
class PdfStream extends PdfType
{
    /**
     * Parses a stream from a stream reader.
     *
     * @param PdfDictionary $dictionary
     * @param StreamReader $reader
     * @param PdfParser $parser Optional to keep backwards compatibility
     * @return self
     * @throws PdfTypeException
     */
    public static function parse(PdfDictionary $dictionary, StreamReader $reader, PdfParser $parser = null)
    {
        $v = new self();
        $v->value = $dictionary;
        $v->reader = $reader;
        $v->parser = $parser;

        $offset = $reader->getOffset();

        // Find the first "newline"
        while (($firstByte = $reader->getByte($offset)) !== false) {
            $offset++;
            if ($firstByte === "\n" || $firstByte === "\r") {
                break;
            }
        }

        if ($firstByte === false) {
            throw new PdfTypeException(
                'Unable to parse stream data. No newline after the stream keyword found.',
                PdfTypeException::NO_NEWLINE_AFTER_STREAM_KEYWORD
            );
        }

        $sndByte = $reader->getByte($offset);
        if ($sndByte === "\n" && $firstByte !== "\n") {
            $offset++;
        }

        $reader->setOffset($offset);
        // let's only save the byte-offset and read the stream only when needed
        $v->stream = $reader->getPosition() + $reader->getOffset();

        return $v;
    }

    /**
     * Helper method to create an instance.
     *
     * @param PdfDictionary $dictionary
     * @param string $stream
     * @return self
     */
    public static function create(PdfDictionary $dictionary, $stream)
    {
        $v = new self();
        $v->value = $dictionary;
        $v->stream = (string) $stream;

        return $v;
    }

    /**
     * Ensures that the passed value is a PdfStream instance.
     *
     * @param mixed $stream
     * @return self
     * @throws PdfTypeException
     */
    public static function ensure($stream)
    {
        return PdfType::ensureType(self::class, $stream, 'Stream value expected.');
    }

    /**
     * The stream or its byte-offset position.
     *
     * @var int|string
     */
    protected $stream;

    /**
     * The stream reader instance.
     *
     * @var StreamReader|null
     */
    protected $reader;

    /**
     * The PDF parser instance.
     *
     * @var PdfParser
     */
    protected $parser;

    /**
     * Get the stream data.
     *
     * @param bool $cache Whether cache the stream data or not.
     * @return bool|string
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @throws PdfParserException
     */
    public function getStream($cache = false)
    {
        if (\is_int($this->stream)) {
            $length = PdfDictionary::get($this->value, 'Length');
            if ($this->parser !== null) {
                $length = PdfType::resolve($length, $this->parser);
            }

            if (!($length instanceof PdfNumeric) || $length->value === 0) {
                $this->reader->reset($this->stream, 100000);
                $buffer = $this->extractStream();
            } else {
                $this->reader->reset($this->stream, $length->value);
                $buffer = $this->reader->getBuffer(false);
                if ($this->parser !== null) {
                    $this->reader->reset($this->stream + strlen($buffer));
                    $this->parser->getTokenizer()->clearStack();
                    $token = $this->parser->readValue();
                    if ($token === false || !($token instanceof PdfToken) || $token->value !== 'endstream') {
                        $this->reader->reset($this->stream, 100000);
                        $buffer = $this->extractStream();
                        $this->reader->reset($this->stream + strlen($buffer));
                    }
                }
            }

            if ($cache === false) {
                return $buffer;
            }

            $this->stream = $buffer;
            $this->reader = null;
        }

        return $this->stream;
    }

    /**
     * Extract the stream "manually".
     *
     * @return string
     * @throws PdfTypeException
     */
    protected function extractStream()
    {
        while (true) {
            $buffer = $this->reader->getBuffer(false);
            $length = \strpos($buffer, 'endstream');
            if ($length === false) {
                if (!$this->reader->increaseLength(100000)) {
                    throw new PdfTypeException('Cannot extract stream.');
                }
                continue;
            }
            break;
        }

        $buffer = \substr($buffer, 0, $length);
        $lastByte = \substr($buffer, -1);

        /* Check for EOL marker =
         *   CARRIAGE RETURN (\r) and a LINE FEED (\n) or just a LINE FEED (\n},
         *   and not by a CARRIAGE RETURN (\r) alone
         */
        if ($lastByte === "\n") {
            $buffer = \substr($buffer, 0, -1);

            $lastByte = \substr($buffer, -1);
            if ($lastByte === "\r") {
                $buffer = \substr($buffer, 0, -1);
            }
        }

        // There are streams in the wild, which have only white signs in them but need to be parsed manually due
        // to a problem encountered before (e.g. Length === 0). We should set them to empty streams to avoid problems
        // in further processing (e.g. applying of filters).
        if (trim($buffer) === '') {
            $buffer = '';
        }

        return $buffer;
    }

    /**
     * Get all filters defined for this stream.
     *
     * @return PdfType[]
     * @throws PdfTypeException
     */
    public function getFilters()
    {
        $filters = PdfDictionary::get($this->value, 'Filter');
        if ($filters instanceof PdfNull) {
            return [];
        }

        if ($filters instanceof PdfArray) {
            $filters = $filters->value;
        } else {
            $filters = [$filters];
        }

        return $filters;
    }

    /**
     * Get the unfiltered stream data.
     *
     * @return string
     * @throws FilterException
     * @throws PdfParserException
     */
    public function getUnfilteredStream()
    {
        $stream = $this->getStream();
        $filters = $this->getFilters();
        if ($filters === []) {
            return $stream;
        }

        $decodeParams = PdfDictionary::get($this->value, 'DecodeParms');
        if ($decodeParams instanceof PdfArray) {
            $decodeParams = $decodeParams->value;
        } else {
            $decodeParams = [$decodeParams];
        }

        foreach ($filters as $key => $filter) {
            if (!($filter instanceof PdfName)) {
                continue;
            }

            $decodeParam = null;
            if (isset($decodeParams[$key])) {
                $decodeParam = ($decodeParams[$key] instanceof PdfDictionary ? $decodeParams[$key] : null);
            }

            switch ($filter->value) {
                case 'FlateDecode':
                case 'Fl':
                case 'LZWDecode':
                case 'LZW':
                    if (\strpos($filter->value, 'LZW') === 0) {
                        $filterObject = new Lzw();
                    } else {
                        $filterObject = new Flate();
                    }

                    $stream = $filterObject->decode($stream);

                    if ($decodeParam instanceof PdfDictionary) {
                        $predictor = PdfDictionary::get($decodeParam, 'Predictor', PdfNumeric::create(1));
                        if ($predictor->value !== 1) {
                            if (!\class_exists(Predictor::class)) {
                                throw new PdfParserException(
                                    'This PDF document makes use of features which are only implemented in the ' .
                                    'commercial "FPDI PDF-Parser" add-on (see https://www.setasign.com/fpdi-pdf-' .
                                    'parser).',
                                    PdfParserException::IMPLEMENTED_IN_FPDI_PDF_PARSER
                                );
                            }

                            $colors = PdfDictionary::get($decodeParam, 'Colors', PdfNumeric::create(1));
                            $bitsPerComponent = PdfDictionary::get(
                                $decodeParam,
                                'BitsPerComponent',
                                PdfNumeric::create(8)
                            );

                            $columns = PdfDictionary::get($decodeParam, 'Columns', PdfNumeric::create(1));

                            $filterObject = new Predictor(
                                $predictor->value,
                                $colors->value,
                                $bitsPerComponent->value,
                                $columns->value
                            );

                            $stream = $filterObject->decode($stream);
                        }
                    }

                    break;
                case 'ASCII85Decode':
                case 'A85':
                    $filterObject = new Ascii85();
                    $stream = $filterObject->decode($stream);
                    break;

                case 'ASCIIHexDecode':
                case 'AHx':
                    $filterObject = new AsciiHex();
                    $stream = $filterObject->decode($stream);
                    break;

                case 'Crypt':
                    if (!$decodeParam instanceof PdfDictionary) {
                        break;
                    }
                    // Filter is "Identity"
                    $name = PdfDictionary::get($decodeParam, 'Name');
                    if (!$name instanceof PdfName || $name->value !== 'Identity') {
                        break;
                    }

                    throw new FilterException(
                        'Support for Crypt filters other than "Identity" is not implemented.',
                        FilterException::UNSUPPORTED_FILTER
                    );

                default:
                    throw new FilterException(
                        \sprintf('Unsupported filter "%s".', $filter->value),
                        FilterException::UNSUPPORTED_FILTER
                    );
            }
        }

        return $stream;
    }
}
