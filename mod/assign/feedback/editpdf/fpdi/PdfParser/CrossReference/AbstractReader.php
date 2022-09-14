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
use setasign\Fpdi\PdfParser\Type\PdfDictionary;
use setasign\Fpdi\PdfParser\Type\PdfToken;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

/**
 * Abstract class for cross-reference reader classes.
 */
abstract class AbstractReader
{
    /**
     * @var PdfParser
     */
    protected $parser;

    /**
     * @var PdfDictionary
     */
    protected $trailer;

    /**
     * AbstractReader constructor.
     *
     * @param PdfParser $parser
     * @throws CrossReferenceException
     * @throws PdfTypeException
     */
    public function __construct(PdfParser $parser)
    {
        $this->parser = $parser;
        $this->readTrailer();
    }

    /**
     * Get the trailer dictionary.
     *
     * @return PdfDictionary
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    /**
     * Read the trailer dictionary.
     *
     * @throws CrossReferenceException
     * @throws PdfTypeException
     */
    protected function readTrailer()
    {
        try {
            $trailerKeyword = $this->parser->readValue(null, PdfToken::class);
            if ($trailerKeyword->value !== 'trailer') {
                throw new CrossReferenceException(
                    \sprintf(
                        'Unexpected end of cross reference. "trailer"-keyword expected, got: %s.',
                        $trailerKeyword->value
                    ),
                    CrossReferenceException::UNEXPECTED_END
                );
            }
        } catch (PdfTypeException $e) {
            throw new CrossReferenceException(
                'Unexpected end of cross reference. "trailer"-keyword expected, got an invalid object type.',
                CrossReferenceException::UNEXPECTED_END,
                $e
            );
        }

        try {
            $trailer = $this->parser->readValue(null, PdfDictionary::class);
        } catch (PdfTypeException $e) {
            throw new CrossReferenceException(
                'Unexpected end of cross reference. Trailer not found.',
                CrossReferenceException::UNEXPECTED_END,
                $e
            );
        }

        $this->trailer = $trailer;
    }
}
