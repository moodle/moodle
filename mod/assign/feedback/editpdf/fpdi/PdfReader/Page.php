<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfReader;

use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use setasign\Fpdi\PdfParser\Type\PdfDictionary;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfNull;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfStream;
use setasign\Fpdi\PdfParser\Type\PdfType;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\DataStructure\Rectangle;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;

/**
 * Class representing a page of a PDF document
 */
class Page
{
    /**
     * @var PdfIndirectObject
     */
    protected $pageObject;

    /**
     * @var PdfDictionary
     */
    protected $pageDictionary;

    /**
     * @var PdfParser
     */
    protected $parser;

    /**
     * Inherited attributes
     *
     * @var null|array
     */
    protected $inheritedAttributes;

    /**
     * Page constructor.
     *
     * @param PdfIndirectObject $page
     * @param PdfParser $parser
     */
    public function __construct(PdfIndirectObject $page, PdfParser $parser)
    {
        $this->pageObject = $page;
        $this->parser = $parser;
    }

    /**
     * Get the indirect object of this page.
     *
     * @return PdfIndirectObject
     */
    public function getPageObject()
    {
        return $this->pageObject;
    }

    /**
     * Get the dictionary of this page.
     *
     * @return PdfDictionary
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws CrossReferenceException
     */
    public function getPageDictionary()
    {
        if (null === $this->pageDictionary) {
            $this->pageDictionary = PdfDictionary::ensure(PdfType::resolve($this->getPageObject(), $this->parser));
        }

        return $this->pageDictionary;
    }

    /**
     * Get a page attribute.
     *
     * @param string $name
     * @param bool $inherited
     * @return PdfType|null
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws CrossReferenceException
     */
    public function getAttribute($name, $inherited = true)
    {
        $dict = $this->getPageDictionary();

        if (isset($dict->value[$name])) {
            return $dict->value[$name];
        }

        $inheritedKeys = ['Resources', 'MediaBox', 'CropBox', 'Rotate'];
        if ($inherited && \in_array($name, $inheritedKeys, true)) {
            if ($this->inheritedAttributes === null) {
                $this->inheritedAttributes = [];
                $inheritedKeys = \array_filter($inheritedKeys, function ($key) use ($dict) {
                    return !isset($dict->value[$key]);
                });

                if (\count($inheritedKeys) > 0) {
                    $parentDict = PdfType::resolve(PdfDictionary::get($dict, 'Parent'), $this->parser);
                    while ($parentDict instanceof PdfDictionary) {
                        foreach ($inheritedKeys as $index => $key) {
                            if (isset($parentDict->value[$key])) {
                                $this->inheritedAttributes[$key] = $parentDict->value[$key];
                                unset($inheritedKeys[$index]);
                            }
                        }

                        /** @noinspection NotOptimalIfConditionsInspection */
                        if (isset($parentDict->value['Parent']) && \count($inheritedKeys) > 0) {
                            $parentDict = PdfType::resolve(PdfDictionary::get($parentDict, 'Parent'), $this->parser);
                        } else {
                            break;
                        }
                    }
                }
            }

            if (isset($this->inheritedAttributes[$name])) {
                return $this->inheritedAttributes[$name];
            }
        }

        return null;
    }

    /**
     * Get the rotation value.
     *
     * @return int
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws CrossReferenceException
     */
    public function getRotation()
    {
        $rotation = $this->getAttribute('Rotate');
        if (null === $rotation) {
            return 0;
        }

        $rotation = PdfNumeric::ensure(PdfType::resolve($rotation, $this->parser))->value % 360;

        if ($rotation < 0) {
            $rotation += 360;
        }

        return $rotation;
    }

    /**
     * Get a boundary of this page.
     *
     * @param string $box
     * @param bool $fallback
     * @return bool|Rectangle
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @see PageBoundaries
     */
    public function getBoundary($box = PageBoundaries::CROP_BOX, $fallback = true)
    {
        $value = $this->getAttribute($box);

        if ($value !== null) {
            return Rectangle::byPdfArray($value, $this->parser);
        }

        if ($fallback === false) {
            return false;
        }

        switch ($box) {
            case PageBoundaries::BLEED_BOX:
            case PageBoundaries::TRIM_BOX:
            case PageBoundaries::ART_BOX:
                return $this->getBoundary(PageBoundaries::CROP_BOX, true);
            case PageBoundaries::CROP_BOX:
                return $this->getBoundary(PageBoundaries::MEDIA_BOX, true);
        }

        return false;
    }

    /**
     * Get the width and height of this page.
     *
     * @param string $box
     * @param bool $fallback
     * @return array|bool
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws CrossReferenceException
     */
    public function getWidthAndHeight($box = PageBoundaries::CROP_BOX, $fallback = true)
    {
        $boundary = $this->getBoundary($box, $fallback);
        if ($boundary === false) {
            return false;
        }

        $rotation = $this->getRotation();
        $interchange = ($rotation / 90) % 2;

        return [
            $interchange ? $boundary->getHeight() : $boundary->getWidth(),
            $interchange ? $boundary->getWidth() : $boundary->getHeight()
        ];
    }

    /**
     * Get the raw content stream.
     *
     * @return string
     * @throws PdfReaderException
     * @throws PdfTypeException
     * @throws FilterException
     * @throws PdfParserException
     */
    public function getContentStream()
    {
        $dict = $this->getPageDictionary();
        $contents = PdfType::resolve(PdfDictionary::get($dict, 'Contents'), $this->parser);
        if ($contents instanceof PdfNull) {
            return '';
        }

        if ($contents instanceof PdfArray) {
            $result = [];
            foreach ($contents->value as $content) {
                $content = PdfType::resolve($content, $this->parser);
                if (!($content instanceof PdfStream)) {
                    continue;
                }
                $result[] = $content->getUnfilteredStream();
            }

            return \implode("\n", $result);
        }

        if ($contents instanceof PdfStream) {
            return $contents->getUnfilteredStream();
        }

        throw new PdfReaderException(
            'Array or stream expected.',
            PdfReaderException::UNEXPECTED_DATA_TYPE
        );
    }
}
