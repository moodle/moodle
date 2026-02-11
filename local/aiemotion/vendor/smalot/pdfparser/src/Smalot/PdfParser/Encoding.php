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

use Smalot\PdfParser\Element\ElementNumeric;
use Smalot\PdfParser\Encoding\EncodingLocator;
use Smalot\PdfParser\Encoding\PostScriptGlyphs;
use Smalot\PdfParser\Exception\EncodingNotFoundException;

/**
 * Class Encoding
 */
class Encoding extends PDFObject
{
    /**
     * @var array
     */
    protected $encoding;

    /**
     * @var array
     */
    protected $differences;

    /**
     * @var array
     */
    protected $mapping;

    public function init()
    {
        $this->mapping = [];
        $this->differences = [];
        $this->encoding = [];

        if ($this->has('BaseEncoding')) {
            $this->encoding = EncodingLocator::getEncoding($this->getEncodingClass())->getTranslations();

            // Build table including differences.
            $differences = $this->get('Differences')->getContent();
            $code = 0;

            if (!\is_array($differences)) {
                return;
            }

            foreach ($differences as $difference) {
                /** @var ElementNumeric $difference */
                if ($difference instanceof ElementNumeric) {
                    $code = $difference->getContent();
                    continue;
                }

                // ElementName
                $this->differences[$code] = $difference;
                if (\is_object($difference)) {
                    $this->differences[$code] = $difference->getContent();
                }

                // For the next char.
                ++$code;
            }

            $this->mapping = $this->encoding;
            foreach ($this->differences as $code => $difference) {
                /* @var string $difference */
                $this->mapping[$code] = $difference;
            }
        }
    }

    public function getDetails(bool $deep = true): array
    {
        $details = [];

        $details['BaseEncoding'] = ($this->has('BaseEncoding') ? (string) $this->get('BaseEncoding') : 'Ansi');
        $details['Differences'] = ($this->has('Differences') ? (string) $this->get('Differences') : '');

        $details += parent::getDetails($deep);

        return $details;
    }

    public function translateChar($dec): ?int
    {
        if (isset($this->mapping[$dec])) {
            $dec = $this->mapping[$dec];
        }

        return PostScriptGlyphs::getCodePoint($dec);
    }

    /**
     * Returns encoding class name if available or empty string (only prior PHP 7.4).
     *
     * @throws \Exception On PHP 7.4+ an exception is thrown if encoding class doesn't exist.
     */
    public function __toString(): string
    {
        try {
            return $this->getEncodingClass();
        } catch (\Exception $e) {
            // prior to PHP 7.4 toString has to return an empty string.
            if (version_compare(\PHP_VERSION, '7.4.0', '<')) {
                return '';
            }
            throw $e;
        }
    }

    /**
     * @throws EncodingNotFoundException
     */
    protected function getEncodingClass(): string
    {
        // Load reference table charset.
        $baseEncoding = preg_replace('/[^A-Z0-9]/is', '', $this->get('BaseEncoding')->getContent());

        // Check for empty BaseEncoding field value
        if (!\is_string($baseEncoding) || 0 == \strlen($baseEncoding)) {
            $baseEncoding = 'StandardEncoding';
        }

        $className = '\\Smalot\\PdfParser\\Encoding\\'.$baseEncoding;

        if (!class_exists($className)) {
            throw new EncodingNotFoundException('Missing encoding data for: "'.$baseEncoding.'".');
        }

        return $className;
    }
}
