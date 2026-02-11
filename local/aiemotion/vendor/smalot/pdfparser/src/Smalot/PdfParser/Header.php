<?php

/**
 * @file
 *          This file is part of the PdfParser library.
 *
 * @author  Sébastien MALOT <sebastien@malot.fr>
 *
 * @date    2017-01-03
 *
 * @license LGPLv3
 *
 * @url     <https://github.com/smalot/pdfparser>
 *
 *  PdfParser is a pdf library written in PHP, extraction oriented.
 *  Copyright (C) 2017 - Sébastien MALOT <sebastien@malot.fr>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with this program.
 *  If not, see <http://www.pdfparser.org/sites/default/LICENSE.txt>.
 */

namespace Smalot\PdfParser;

use Smalot\PdfParser\Element\ElementArray;
use Smalot\PdfParser\Element\ElementMissing;
use Smalot\PdfParser\Element\ElementStruct;
use Smalot\PdfParser\Element\ElementXRef;

/**
 * Class Header
 */
class Header
{
    /**
     * @var Document|null
     */
    protected $document;

    /**
     * @var Element[]
     */
    protected $elements;

    /**
     * @param Element[] $elements list of elements
     * @param Document  $document document
     */
    public function __construct(array $elements = [], ?Document $document = null)
    {
        $this->elements = $elements;
        $this->document = $document;
    }

    public function init()
    {
        foreach ($this->elements as $element) {
            if ($element instanceof Element) {
                $element->init();
            }
        }
    }

    /**
     * Returns all elements.
     */
    public function getElements()
    {
        foreach ($this->elements as $name => $element) {
            $this->resolveXRef($name);
        }

        return $this->elements;
    }

    /**
     * Used only for debug.
     */
    public function getElementTypes(): array
    {
        $types = [];

        foreach ($this->elements as $key => $element) {
            $types[$key] = \get_class($element);
        }

        return $types;
    }

    public function getDetails(bool $deep = true): array
    {
        $values = [];
        $elements = $this->getElements();

        foreach ($elements as $key => $element) {
            if ($element instanceof self && $deep) {
                $values[$key] = $element->getDetails($deep);
            } elseif ($element instanceof PDFObject && $deep) {
                $values[$key] = $element->getDetails(false);
            } elseif ($element instanceof ElementArray) {
                if ($deep) {
                    $values[$key] = $element->getDetails();
                }
            } elseif ($element instanceof Element) {
                $values[$key] = (string) $element;
            }
        }

        return $values;
    }

    /**
     * Indicate if an element name is available in header.
     *
     * @param string $name the name of the element
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->elements);
    }

    /**
     * @return Element|PDFObject
     */
    public function get(string $name)
    {
        if (\array_key_exists($name, $this->elements) && $element = $this->resolveXRef($name)) {
            return $element;
        }

        return new ElementMissing();
    }

    /**
     * Resolve XRef to object.
     *
     * @return Element|PDFObject
     *
     * @throws \Exception
     */
    protected function resolveXRef(string $name)
    {
        if (($obj = $this->elements[$name]) instanceof ElementXRef && null !== $this->document) {
            /** @var ElementXRef $obj */
            $object = $this->document->getObjectById($obj->getId());

            if (null === $object) {
                return new ElementMissing();
            }

            // Update elements list for future calls.
            $this->elements[$name] = $object;
        }

        return $this->elements[$name];
    }

    /**
     * @param string   $content  The content to parse
     * @param Document $document The document
     * @param int      $position The new position of the cursor after parsing
     */
    public static function parse(string $content, Document $document, int &$position = 0): self
    {
        /* @var Header $header */
        if ('<<' == substr(trim($content), 0, 2)) {
            $header = ElementStruct::parse($content, $document, $position);
        } else {
            $elements = ElementArray::parse($content, $document, $position);
            $header = new self([], $document);

            if ($elements) {
                $header = new self($elements->getRawContent(), null);
            }
        }

        if ($header) {
            return $header;
        }

        // Build an empty header.
        return new self([], $document);
    }
}
