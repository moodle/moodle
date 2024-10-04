<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfReader;

use setasign\Fpdi\FpdiException;
use setasign\Fpdi\GraphicsState;
use setasign\Fpdi\Math\Vector;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use setasign\Fpdi\PdfParser\Type\PdfDictionary;
use setasign\Fpdi\PdfParser\Type\PdfHexString;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfName;
use setasign\Fpdi\PdfParser\Type\PdfNull;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfStream;
use setasign\Fpdi\PdfParser\Type\PdfString;
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
        if ($this->pageDictionary === null) {
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
        if ($rotation === null) {
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

    /**
     * Get information of all external links on this page.
     *
     * All coordinates are normalized in view to rotation and translation of the boundary-box, so that their
     * origin is lower-left.
     *
     * The URI is the binary value of the PDF string object. It can be in PdfDocEncoding or in UTF-16BE encoding.
     *
     * @return array
     */
    public function getExternalLinks($box = PageBoundaries::CROP_BOX)
    {
        try {
            $dict = $this->getPageDictionary();
            $annotations = PdfType::resolve(PdfDictionary::get($dict, 'Annots'), $this->parser);
        } catch (FpdiException $e) {
            return [];
        }

        if (!$annotations instanceof PdfArray) {
            return [];
        }

        $links = [];

        foreach ($annotations->value as $entry) {
            try {
                $annotation = PdfType::resolve($entry, $this->parser);

                $value = PdfType::resolve(PdfDictionary::get($annotation, 'Subtype'), $this->parser);
                if (!$value instanceof PdfName || $value->value !== 'Link') {
                    continue;
                }

                $dest = PdfType::resolve(PdfDictionary::get($annotation, 'Dest'), $this->parser);
                if (!$dest instanceof PdfNull) {
                    continue;
                }

                $action = PdfType::resolve(PdfDictionary::get($annotation, 'A'), $this->parser);
                if (!$action instanceof PdfDictionary) {
                    continue;
                }

                $actionType = PdfType::resolve(PdfDictionary::get($action, 'S'), $this->parser);
                if (!$actionType instanceof PdfName || $actionType->value !== 'URI') {
                    continue;
                }

                $uri = PdfType::resolve(PdfDictionary::get($action, 'URI'), $this->parser);
                if ($uri instanceof PdfString) {
                    $uriValue = PdfString::unescape($uri->value);
                } elseif ($uri instanceof PdfHexString) {
                    $uriValue = \hex2bin($uri->value);
                } else {
                    continue;
                }

                $rect = PdfType::resolve(PdfDictionary::get($annotation, 'Rect'), $this->parser);
                if (!$rect instanceof PdfArray || count($rect->value) !== 4) {
                    continue;
                }

                $rect = Rectangle::byPdfArray($rect, $this->parser);
                if ($rect->getWidth() === 0 || $rect->getHeight() === 0) {
                    continue;
                }

                $bbox = $this->getBoundary($box);
                $rotation = $this->getRotation();

                $gs = new GraphicsState();
                $gs->translate(-$bbox->getLlx(), -$bbox->getLly());
                $gs->rotate($bbox->getLlx(), $bbox->getLly(), -$rotation);

                switch ($rotation) {
                    case 90:
                        $gs->translate(-$bbox->getWidth(), 0);
                        break;
                    case 180:
                        $gs->translate(-$bbox->getWidth(), -$bbox->getHeight());
                        break;
                    case 270:
                        $gs->translate(0, -$bbox->getHeight());
                        break;
                }

                $normalizedRect = Rectangle::byVectors(
                    $gs->toUserSpace(new Vector($rect->getLlx(), $rect->getLly())),
                    $gs->toUserSpace(new Vector($rect->getUrx(), $rect->getUry()))
                );

                $quadPoints = PdfType::resolve(PdfDictionary::get($annotation, 'QuadPoints'), $this->parser);
                $normalizedQuadPoints = [];
                if ($quadPoints instanceof PdfArray) {
                    $quadPointsCount = count($quadPoints->value);
                    if ($quadPointsCount % 8 === 0) {
                        for ($i = 0; ($i + 1) < $quadPointsCount; $i += 2) {
                            $x = PdfNumeric::ensure(PdfType::resolve($quadPoints->value[$i], $this->parser));
                            $y = PdfNumeric::ensure(PdfType::resolve($quadPoints->value[$i + 1], $this->parser));

                            $v = $gs->toUserSpace(new Vector($x->value, $y->value));
                            $normalizedQuadPoints[] = $v->getX();
                            $normalizedQuadPoints[] = $v->getY();
                        }
                    }
                }

                // we remove unsupported/unneeded values here
                unset(
                    $annotation->value['P'],
                    $annotation->value['NM'],
                    $annotation->value['AP'],
                    $annotation->value['AS'],
                    $annotation->value['Type'],
                    $annotation->value['Subtype'],
                    $annotation->value['Rect'],
                    $annotation->value['A'],
                    $annotation->value['QuadPoints'],
                    $annotation->value['Rotate'],
                    $annotation->value['M'],
                    $annotation->value['StructParent'],
                    $annotation->value['OC']
                );

                // ...and flatten the PDF object to eliminate any indirect references.
                // Indirect references are a problem when writing the output in FPDF
                // because FPDF uses pre-calculated object numbers while FPDI creates
                // them at runtime.
                $annotation = PdfType::flatten($annotation, $this->parser);

                $links[] = [
                    'rect' => $normalizedRect,
                    'quadPoints' => $normalizedQuadPoints,
                    'uri' => $uriValue,
                    'pdfObject' => $annotation
                ];
            } catch (FpdiException $e) {
                continue;
            }
        }

        return $links;
    }
}
