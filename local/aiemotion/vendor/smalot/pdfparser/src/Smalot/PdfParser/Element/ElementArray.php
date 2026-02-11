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

namespace Smalot\PdfParser\Element;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\Element;
use Smalot\PdfParser\Header;
use Smalot\PdfParser\PDFObject;

/**
 * Class ElementArray
 */
class ElementArray extends Element
{
    public function __construct($value, ?Document $document = null)
    {
        parent::__construct($value, $document);
    }

    public function getContent()
    {
        foreach ($this->value as $name => $element) {
            $this->resolveXRef($name);
        }

        return parent::getContent();
    }

    public function getRawContent(): array
    {
        return $this->value;
    }

    public function getDetails(bool $deep = true): array
    {
        $values = [];
        $elements = $this->getContent();

        foreach ($elements as $key => $element) {
            if ($element instanceof Header && $deep) {
                $values[$key] = $element->getDetails($deep);
            } elseif ($element instanceof PDFObject && $deep) {
                $values[$key] = $element->getDetails(false);
            } elseif ($element instanceof self) {
                if ($deep) {
                    $values[$key] = $element->getDetails();
                }
            } elseif ($element instanceof Element && !($element instanceof self)) {
                $values[$key] = $element->getContent();
            }
        }

        return $values;
    }

    public function __toString(): string
    {
        return implode(',', $this->value);
    }

    /**
     * @return Element|PDFObject
     */
    protected function resolveXRef(string $name)
    {
        if (($obj = $this->value[$name]) instanceof ElementXRef) {
            /** @var ElementXRef $obj */
            $obj = $this->document->getObjectById($obj->getId());
            $this->value[$name] = $obj;
        }

        return $this->value[$name];
    }

    /**
     * @todo: These methods return mixed and mismatched types throughout the hierarchy
     *
     * @return bool|ElementArray
     */
    public static function parse(string $content, ?Document $document = null, int &$offset = 0)
    {
        if (preg_match('/^\s*\[(?P<array>.*)/is', $content, $match)) {
            preg_match_all('/(.*?)(\[|\])/s', trim($content), $matches);

            $level = 0;
            $sub = '';
            foreach ($matches[0] as $part) {
                $sub .= $part;
                $level += (false !== strpos($part, '[') ? 1 : -1);
                if ($level <= 0) {
                    break;
                }
            }

            // Removes 1 level [ and ].
            $sub = substr(trim($sub), 1, -1);
            $sub_offset = 0;
            $values = Element::parse($sub, $document, $sub_offset, true);

            $offset += strpos($content, '[') + 1;
            // Find next ']' position
            $offset += \strlen($sub) + 1;

            return new self($values, $document);
        }

        return false;
    }
}
