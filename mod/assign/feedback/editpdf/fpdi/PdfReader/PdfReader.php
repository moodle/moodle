<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfReader;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use setasign\Fpdi\PdfParser\Type\PdfDictionary;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObjectReference;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfType;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

/**
 * A PDF reader class
 */
class PdfReader
{
    /**
     * @var PdfParser
     */
    protected $parser;

    /**
     * @var int
     */
    protected $pageCount;

    /**
     * Indirect objects of resolved pages.
     *
     * @var PdfIndirectObjectReference[]|PdfIndirectObject[]
     */
    protected $pages = [];

    /**
     * PdfReader constructor.
     *
     * @param PdfParser $parser
     */
    public function __construct(PdfParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * PdfReader destructor.
     */
    public function __destruct()
    {
        if ($this->parser !== null) {
            $this->parser->cleanUp();
        }
    }

    /**
     * Get the pdf parser instance.
     *
     * @return PdfParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Get the PDF version.
     *
     * @return string
     * @throws PdfParserException
     */
    public function getPdfVersion()
    {
        return \implode('.', $this->parser->getPdfVersion());
    }

    /**
     * Get the page count.
     *
     * @return int
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @throws PdfParserException
     */
    public function getPageCount()
    {
        if ($this->pageCount === null) {
            $catalog = $this->parser->getCatalog();

            $pages = PdfType::resolve(PdfDictionary::get($catalog, 'Pages'), $this->parser);
            $count = PdfType::resolve(PdfDictionary::get($pages, 'Count'), $this->parser);

            $this->pageCount = PdfNumeric::ensure($count)->value;
        }

        return $this->pageCount;
    }

    /**
     * Get a page instance.
     *
     * @param int $pageNumber
     * @return Page
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws \InvalidArgumentException
     */
    public function getPage($pageNumber)
    {
        if (!\is_numeric($pageNumber)) {
            throw new \InvalidArgumentException(
                'Page number needs to be a number.'
            );
        }

        if ($pageNumber < 1 || $pageNumber > $this->getPageCount()) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Page number "%s" out of available page range (1 - %s)',
                    $pageNumber,
                    $this->getPageCount()
                )
            );
        }

        $this->readPages();

        $page = $this->pages[$pageNumber - 1];

        if ($page instanceof PdfIndirectObjectReference) {
            $readPages = function ($kids) use (&$readPages) {
                $kids = PdfArray::ensure($kids);

                /** @noinspection LoopWhichDoesNotLoopInspection */
                foreach ($kids->value as $reference) {
                    $reference = PdfIndirectObjectReference::ensure($reference);
                    $object = $this->parser->getIndirectObject($reference->value);
                    $type = PdfDictionary::get($object->value, 'Type');

                    if ($type->value === 'Pages') {
                        return $readPages(PdfDictionary::get($object->value, 'Kids'));
                    }

                    return $object;
                }

                throw new PdfReaderException(
                    'Kids array cannot be empty.',
                    PdfReaderException::KIDS_EMPTY
                );
            };

            $page = $this->parser->getIndirectObject($page->value);
            $dict = PdfType::resolve($page, $this->parser);
            $type = PdfDictionary::get($dict, 'Type');

            if ($type->value === 'Pages') {
                $kids = PdfType::resolve(PdfDictionary::get($dict, 'Kids'), $this->parser);
                try {
                    $page = $this->pages[$pageNumber - 1] = $readPages($kids);
                } catch (PdfReaderException $e) {
                    if ($e->getCode() !== PdfReaderException::KIDS_EMPTY) {
                        throw $e;
                    }

                    // let's reset the pages array and read all page objects
                    $this->pages = [];
                    $this->readPages(true);
                    // @phpstan-ignore-next-line
                    $page = $this->pages[$pageNumber - 1];
                }
            } else {
                $this->pages[$pageNumber - 1] = $page;
            }
        }

        return new Page($page, $this->parser);
    }

    /**
     * Walk the page tree and resolve all indirect objects of all pages.
     *
     * @param bool $readAll
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     */
    protected function readPages($readAll = false)
    {
        if (\count($this->pages) > 0) {
            return;
        }

        $readPages = function ($kids, $count) use (&$readPages, $readAll) {
            $kids = PdfArray::ensure($kids);
            $isLeaf = ($count->value === \count($kids->value));

            foreach ($kids->value as $reference) {
                $reference = PdfIndirectObjectReference::ensure($reference);

                if (!$readAll && $isLeaf) {
                    $this->pages[] = $reference;
                    continue;
                }

                $object = $this->parser->getIndirectObject($reference->value);
                $type = PdfDictionary::get($object->value, 'Type');

                if ($type->value === 'Pages') {
                    $readPages(PdfDictionary::get($object->value, 'Kids'), PdfDictionary::get($object->value, 'Count'));
                } else {
                    $this->pages[] = $object;
                }
            }
        };

        $catalog = $this->parser->getCatalog();
        $pages = PdfType::resolve(PdfDictionary::get($catalog, 'Pages'), $this->parser);
        $count = PdfType::resolve(PdfDictionary::get($pages, 'Count'), $this->parser);
        $kids = PdfType::resolve(PdfDictionary::get($pages, 'Kids'), $this->parser);
        $readPages($kids, $count);
    }
}
